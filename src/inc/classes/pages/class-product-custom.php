<?php

namespace Omnis\src\inc\classes\pages;

class Product_Custom
{
    private static $instance;

    public function __construct()
    {
        $this->setup_ajax_handlers();
        $this->setup_hooks();
        $this->setup_cron_jobs();
    }

    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setup_ajax_handlers()
    {
        $this->wp_ajax_action('custom_offer');
        $this->wp_ajax_action('create_booking');
        
    }

    private function setup_hooks()
    {
        add_action('wp', [$this, 'update_acf_views_count_for_product']);
        add_action('woocommerce_cart_calculate_fees', [$this, 'apply_rent_discounts']);
        add_action('woocommerce_order_status_changed', [$this, 'update_acf_buy_count_on_order_complete'], 10, 3);
    }

    private function setup_cron_jobs()
    {
        register_activation_hook(__FILE__, [$this, 'schedule_cron_event']);

        register_deactivation_hook(__FILE__, [$this, 'unschedule_cron_event']);

        add_action('product_custom_cron_task', [$this, 'handle_cron_task']);
    }

    public function schedule_cron_event()
    {
        if (!wp_next_scheduled('product_custom_cron_task')) {
            wp_schedule_event(time(), 'hourly', 'product_custom_cron_task');
        }
    }

    public function unschedule_cron_event()
    {
        $timestamp = wp_next_scheduled('product_custom_cron_task');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'product_custom_cron_task');
        }
    }

    public function handle_cron_task()
    {
        $args = [
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ];

        $products = get_posts($args);

        $product_views = [];
        $product_buy = [];

        foreach ($products as $product) {
            $buy_count = get_post_meta($product->ID, '_wc_total_sales', true);
            $views_count = (int)get_field('views_count', $product->ID) ?? 0;

            if ($views_count > 0) {
                $product_views[] = [
                    'id'          => $product->ID,
                    'views_count' => $views_count,
                ];
            }

            if ($buy_count > 0) {
                $product_buy[] = [
                    'id'        => $product->ID,
                    'buy_count' => $buy_count,
                ];
            }
        }

        usort($product_views, function ($a, $b) {
            return $b['views_count'] <=> $a['views_count'];
        });

        usort($product_buy, function ($a, $b) {
            return $b['buy_count'] <=> $a['buy_count'];
        });

        $top_views = array_slice($product_views, 0, 20);
        $top_views_IDs = array_column($top_views, 'id');

        $top_buy = array_slice($product_buy, 0, 20);
        $top_buy_IDs = array_column($top_buy, 'id');

        update_field('buy_count_total', $top_buy_IDs, "option");
        update_field('views_count_total', $top_views_IDs, "option");
    }

    public function wp_ajax_action($action)
    {
        add_action('wp_ajax_' . $action, [$this, $action]);
        add_action('wp_ajax_nopriv_' . $action, [$this, $action]);
    }

    public function custom_offer() {
        global $wpdb;
        
        $nonce = sanitize_text_field($_POST['security']);
    
        if (!wp_verify_nonce($nonce, "registration_nonce")) {
            wp_send_json_error(["message" => esc_html__("Invalid request", 'swap')]);
        }
        if (!is_user_logged_in()) {
            wp_send_json_error(["message" => esc_html__("User not logged in", 'swap')]);
        }
    
        $bid_amount = floatval($_POST['offer-form-input']);
        $user_id = get_current_user_id();
        $product_id = intval($_POST['product_id']);

        $custom_parent_id = get_post_meta($product_id, '_custom_parent_id', true);
        $variation_id = get_variation_id($custom_parent_id);

        foreach(array_values($variation_id) as $prodID){
            $product = wc_get_product($prodID);
            $status = $product->get_status();
            $stock_status = $product->get_stock_status();
        
            if($stock_status != "instock"){
                wp_send_json_error(["message" => esc_html__("The product is not active", 'swap')]);
            }
            if($status !== "publish"){
                wp_send_json_error(["message" => esc_html__("The product is not active", 'swap')]);
            }
        }

        $product = wc_get_product($product_id);
        if (!$product || !method_exists($product, 'is_type') || !$product->is_type('auction')) {
            wp_send_json_error(["message" => esc_html__("Invalid auction product", 'swap')]);
        }
    
        $current_bid = floatval(get_post_meta($product_id, '_auction_current_bid', true));
        $start_price = floatval(get_post_meta($product_id, '_auction_start_price', true));
        $bid_count = absint(get_post_meta($product_id, '_auction_bid_count', true));
        $previous_bidder = get_post_meta($product_id, '_auction_current_bidder', true);
    
        if ($bid_amount <= $current_bid) {
            wp_send_json_error(["message" => esc_html__("Your bid must be higher than the current bid", 'swap')]);
        }
    
        if ($current_bid == 0 && $bid_amount <= $start_price) {
            wp_send_json_error(["message" => esc_html__("Your bid must be higher than the starting price", 'swap')]);
        }
    
        update_post_meta($product_id, '_auction_current_bid', $bid_amount);
        update_post_meta($product_id, '_auction_current_bidder', $user_id);
        update_post_meta($product_id, '_auction_bid_count', $bid_count + 1);
    
        delete_post_meta($product_id, '_auction_current_bid_proxy');
    
        $wpdb->insert(
            $wpdb->prefix . 'simple_auction_log',
            [
                'auction_id' => $product_id,
                'userid'     => $user_id,
                'date'       => current_time('mysql'),
                'bid'        => $bid_amount,
                'proxy'      => 0 
            ],
            ['%d', '%d', '%s', '%f', '%d']
        );
    

        $auction_history = $product->auction_history();
    
        update_post_meta($product_id, '_auction_history', $auction_history);
    
        if (!empty($previous_bidder) && $previous_bidder != $user_id) {
            do_action('woocommerce_simple_auctions_outbid', [
                'product_id' => $product_id,
                'outbiddeduser_id' => $previous_bidder,
            ]);
        }
    
        wp_send_json_success(["message" => esc_html__("Your bid has been placed successfully", 'swap')]);
    }


    public function create_booking() {
        $nonce = sanitize_text_field($_POST['security']);
    
        if (!wp_verify_nonce($nonce, "registration_nonce")) {
            wp_send_json_error(["message" => esc_html__("Invalid request", 'swap')]);
        }
        if (!is_user_logged_in()) {
            wp_send_json_error(["message" => esc_html__("User not logged in", 'swap')]);
        }

        $product_id = intval($_POST['product_id']);
        $user_id = get_current_user_id();
        $start_date = sanitize_text_field($_POST['start_date']);
        $start_date_obj = new \DateTime($start_date);
        $start_date = $start_date_obj->format("Y-m-d");
        
        $end_date = sanitize_text_field($_POST['end_date']);
        $end_date_obj = new \DateTime($end_date);
        $end_date = $end_date_obj->format("Y-m-d");

        $booking = new \WC_Booking();
        $booking->set_product_id($product_id);
        $booking->set_customer_id($user_id);
        $booking->set_start($start_date);
        $booking->set_end($end_date);
        $booking->set_status('pending-confirmation');
        $booking->save();

        if ($booking->get_id()) {
            wp_send_json_success(["message" => esc_html__("Your booking has been placed successfully", 'swap'), 'booking_id' => $booking->get_id()]);

        } else {
            wp_send_json_error(['message' => 'booking error']);
        }
    }

    public function apply_rent_discounts()
    {
        $cart = WC()->cart;

        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            $product = $cart_item['data'];
            $quantity = $cart_item['quantity'];

            if ($product->is_type('variation')) {
                $variation_id = $product->get_id();
                $parent_id = wp_get_post_parent_id($variation_id);

                $discount_period = get_field("discount_for_the_period", $parent_id);
                $type = $product->get_attribute('pa_product-type');

                if ($type === 'Rent') {
                    $discount = 0;

                    foreach ($discount_period as $key => $value) {
                        if ($quantity >= $value["day"]) {
                            $discount = $product->get_price() * ($value["discount"] / 100) * $quantity;
                        }
                    }
                    if ($discount > 0) {
                        $cart->add_fee(__('Discount for Rent', 'your-text-domain'), -$discount, false);
                    }
                }
            }
        }
    }

    public function update_acf_views_count_for_product()
    {
        if (is_singular('product')) {
            $product_id = get_the_ID();
            $views_count = get_field('views_count', $product_id);

            if (!$views_count) {
                $views_count = 0;
            }

            $views_count++;

            update_field('views_count', $views_count, $product_id);
        }
    }

    function update_acf_buy_count_on_order_complete($order_id, $old_status, $new_status)
    {
        if ($new_status === 'completed') {
            $order = wc_get_order($order_id);
            if (!$order) {
                return;
            }

            foreach ($order->get_items() as $item) {
                $product_id = $item->get_product_id();

                $buy_count = get_field('buy_count', $product_id);

                if (!$buy_count) {
                    $buy_count = 0;
                }

                $buy_count++;

                update_field('buy_count', $buy_count, $product_id);
            }
        }
    }
}

Product_Custom::get_instance();
