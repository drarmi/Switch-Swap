<?php

namespace Omnis\src\inc\classes\components;

class Upload_Control
{
    private static $instance;

    public function __construct()
    {
        $this->setup_ajax_handlers();
        $this->setup_hooks();
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
        $this->wp_ajax_action('uploadModal');
        $this->wp_ajax_action('saveImages');
        $this->wp_ajax_action('createNewProduct');
        $this->wp_ajax_action('handle_save_snapshot');
    }

    private function setup_hooks()
    {
        add_filter('storefront_handheld_footer_bar_links', [$this, 'custom_add_footer_bar_links']);
    }


    public function wp_ajax_action($action)
    {
        add_action('wp_ajax_' . $action, [$this, $action]);
        add_action('wp_ajax_nopriv_' . $action, [$this, $action]);
    }

    function custom_add_footer_bar_links($links)
    {

        if (!is_user_logged_in()) {
            return $links;
        }

        $upload_link = array(
            'priority' => 25,
            'callback' => [$this, 'custom_handheld_footer_bar_upload_link'],
        );

        $new_links = array();
        $inserted = false;

        foreach ($links as $key => $link) {

            if (!$inserted && isset($link['priority']) && $link['priority'] > 20) {
                $new_links['upload'] = $upload_link;
                $inserted = true;
            }
            $new_links[$key] = $link;
        }

        if (!$inserted) {
            $new_links['upload'] = $upload_link;
        }

        return $new_links;
    }


    public function custom_handheld_footer_bar_upload_link($key, $link)
    {

?>
        <a id="upload-product" href="#">
            <span>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5V19M5 12H19" stroke="#111111" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
        </a>
<?php
    }


    public function uploadModal()
    {
        $nonce = sanitize_text_field($_POST['security']);

        if (!wp_verify_nonce($nonce, "registration_nonce")) {
            wp_send_json_error(["message" => esc_html__("Invalid request ", 'swap')]);
        }

        ob_start();

        echo "<form class='main-upload-form' data-step='1'>";
        get_template_part("src/template-parts/upload/info", null);
        get_template_part("src/template-parts/upload/media", null);
        get_template_part("src/template-parts/upload/image-editor", null);
        get_template_part("src/template-parts/upload/nav-btn", null);
        echo "</form>";

        $data = ob_get_clean();

        wp_send_json_success($data);
    }

    public function createBayProduct()
    {
        if (empty($_POST['sale-option']) || $_POST['sale-option'] != "on" || empty($_POST['price-bay-only'])) {
            return;
        }

        $user_id = get_current_user_id();

        $product_data = [
            'post_title'   => sanitize_text_field($_POST['title']),
            'post_content' => sanitize_textarea_field($_POST['description']),
            'post_status'  => 'pending',
            'post_type'    => 'product',
            'post_author'  => $user_id,
            'comment_status' => 'open',
            'meta_input'     => [
                '_enable_reviews'   => 'yes',
                '_wc_review_count'  => 0,
                '_wc_average_rating' => 0,
                '_wc_review_enabled' => 'yes',
            ],
        ];

        $product_id = wp_insert_post($product_data);

        wp_set_object_terms($product_id, 'simple', 'product_type');

        $price = (int)sanitize_text_field($_POST['price-bay-only']);


        update_post_meta($product_id, '_regular_price', $price);
        update_post_meta($product_id, '_price', $price);

        if (!empty($_POST['sale-discount'])) {
            $discount = (int)sanitize_text_field($_POST['sale-discount']);
            $discountedPrice = $price * (1 - $discount / 100);
            update_post_meta($product_id, '_sale_price', $discountedPrice);
            update_post_meta($product_id, '_price', $discountedPrice);
        } else {
            update_post_meta($product_id, '_sale_price', '');
        }

        update_post_meta($product_id, '_stock_status', 'instock');
        update_post_meta($product_id, '_manage_stock', 'no');
        update_post_meta($product_id, '_virtual', 'no');
        update_post_meta($product_id, '_downloadable', 'no');
        update_post_meta($product_id, '_visibility', 'visible');

        return $product_id;
    }

    public function createBidProduct()
    {
        if (empty($_POST['condition']) || empty($_POST['start-date-rent']) || empty($_POST['end-date-rent']) || empty($_POST['bids-option']) || $_POST['bids-option'] != "on" || empty($_POST['min-price-rent'])) {
            return;
        }

        $user_id = get_current_user_id();

        $product_data = [
            'post_title'   => sanitize_text_field($_POST['title']),
            'post_content' => sanitize_textarea_field($_POST['description']),
            'post_status'  => 'pending',
            'post_type'    => 'product',
            'post_author'  => $user_id,
            'comment_status' => 'open',
            'meta_input'     => [
                '_enable_reviews'   => 'yes',
                '_wc_review_count'  => 0,
                '_wc_average_rating' => 0,
                '_wc_review_enabled' => 'yes',
            ],
        ];

        $product_id = wp_insert_post($product_data);
        
        $min_price = (int) sanitize_text_field($_POST['min-price-rent']);

        $start_date = sanitize_text_field($_POST['start-date-rent']);
        $start_date = str_replace([".", "/", "-"], "-", $start_date);
        $start_date = \DateTime::createFromFormat('d-m-Y', $start_date);
        $start_date_formatted = $start_date ? $start_date->format('Y-m-d 00:00') : null;

        $end_date = sanitize_text_field($_POST['end-date-rent']);
        $end_date = str_replace([".", "/", "-"], "-", $end_date);
        $end_date = \DateTime::createFromFormat('d-m-Y', $end_date);
        $end_date_formatted = $end_date ? $end_date->format('Y-m-d 00:00') : null;

        $condition = sanitize_text_field($_POST['condition']) == 'new' ? "new" : "used";

        wp_set_object_terms($product_id, 'auction', 'product_type');

        update_post_meta($product_id, '_auction', 'yes');
        update_post_meta($product_id, '_auction_type', 'normal');
        update_post_meta($product_id, '_auction_bid_increment', 1);
        update_post_meta($product_id, '_auction_start_price', $min_price);
        update_post_meta($product_id, '_auction_dates_from', $start_date_formatted);
        update_post_meta($product_id, '_auction_dates_to', $end_date_formatted);
        update_post_meta($product_id, '_auction_item_condition', $condition);

        if (!empty($_POST['price-bay-now']) && empty($_POST['price-bay-only'])) {
            $price = (int) sanitize_text_field($_POST['price-bay-now']);
            update_post_meta($product_id, '_regular_price', $price);
            update_post_meta($product_id, '_price', $price);
            update_post_meta($product_id, '_auction_buy_now', 'yes');
            update_post_meta($product_id, '_auction_buy_now_price', $price);
        }

        return $product_id;
    }

    public function createRentProduct()
    {
        if (empty($_POST['condition']) || empty($_POST['renting-option']) || $_POST['renting-option'] != "on" || empty($_POST['renting_price'])) {
            return;
        }

        $user_id = get_current_user_id();

        $product_data = [
            'post_title'   => sanitize_text_field($_POST['title']),
            'post_content' => sanitize_textarea_field($_POST['description']),
            'post_status'  => 'pending',
            'post_type'    => 'product',
            'post_author'  => $user_id,
            'comment_status' => 'open',
            'meta_input'     => [
                '_enable_reviews'   => 'yes',
                '_wc_review_count'  => 0,
                '_wc_average_rating' => 0,
                '_wc_review_enabled' => 'yes',
            ],
        ];

        $product_id = wp_insert_post($product_data);

        wp_set_object_terms($product_id, 'booking', 'product_type');

        $price_per_day = (float) sanitize_text_field($_POST['renting_price']);
        $discount_day_percentage_4 = (float) sanitize_text_field($_POST['rent-discount-day-4']);
        $discounted_4 = $price_per_day * ($discount_day_percentage_4 / 100);
        $discount_day_percentage_8 = (float) sanitize_text_field($_POST['rent-discount-day-8']);
        $discounted_8 = $price_per_day * ($discount_day_percentage_8 / 100);

        update_post_meta($product_id, '_wc_booking_duration', 1);
        update_post_meta($product_id, '_wc_booking_duration_unit', 'day');
        update_post_meta($product_id, '_wc_booking_base_cost', '');
        update_post_meta($product_id, '_wc_booking_cost', 0);
        update_post_meta($product_id, '_wc_booking_block_cost', $price_per_day);
        update_post_meta($product_id, '_wc_booking_min_duration', 1);
        update_post_meta($product_id, '_wc_booking_max_duration', 30);
        update_post_meta($product_id, '_wc_booking_qty', 1);
        update_post_meta($product_id, '_wc_booking_max_date_unit', 'month');
        update_post_meta($product_id, '_wc_booking_max_date', 6);

        update_post_meta($product_id, '_wc_booking_cancel_limit_unit', 'day');
        update_post_meta($product_id, '_wc_booking_first_block_time', '');
        update_post_meta($product_id, '_wc_booking_buffer_period', 0);
        update_post_meta($product_id, '_wc_booking_check_availability_against', 'product');
        update_post_meta($product_id, '_wc_booking_default_date_availability', 'available');

        $cost_rules = [
            [
                'type'       => 'blocks',
                'from'       => 4,
                'to'         => 7,
                'modifier'   => 'minus',
                'cost'       => $discounted_4,
            ],
            [
                'type'       => 'blocks',
                'from'       => 8,
                'to'         => '',
                'modifier'   => 'minus',
                'cost'       => $discounted_8,
            ],
        ];
        update_post_meta($product_id, '_wc_booking_pricing', $cost_rules);

        return $product_id;
    }

    public function updateProductMeta($product_id)
    {
        $quality = [
            "new" => "חדש",
            "good" => "במצב טוב",
            "used" => "משומש",
        ];

        if (!empty($_POST['category-term'])) {
            wp_set_object_terms($product_id, (int) $_POST['category-term'], 'product_cat');
        }

        if (!empty($_POST['brands'])) {
            wp_set_object_terms($product_id, (int) $_POST['brands'], 'product_brand');
        }

        if (!empty($_POST['size-term'])) {
            wp_set_object_terms($product_id, (int) $_POST['size-term'], 'size');
        }

        if (!empty($_POST['color-term'])) {
            wp_set_object_terms($product_id, (int) $_POST['color-term'], 'color');
        }

        if (!empty($_POST['condition'])) {
            update_post_meta($product_id, '_quality', $quality[sanitize_text_field($_POST['condition'])]);
        }

        if (!empty($_POST['additional_comments'])) {
            update_post_meta($product_id, '_additional_comments', sanitize_text_field($_POST['additional_comments']));
        }

        if (!empty($_POST['selectedIMG-1'])) {
            set_post_thumbnail($product_id, (int) $_POST['selectedIMG-1']);
        }

        $gallery = [];
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'selectedIMG-') === 0 && $key !== 'selectedIMG-1') {
                $gallery[] = (int) $value;
            }
        }
        if (!empty($gallery)) {
            update_post_meta($product_id, '_product_image_gallery', implode(',', $gallery));
        }

        if (function_exists('dokan_get_seller_id_by_product')) {
            $vendor_id = dokan_get_current_user_id();
            wp_update_post([
                'ID'          => $product_id,
                'post_author' => $vendor_id,
            ]);
        }
    }

    public function createNewProduct()
    {
        $nonce = sanitize_text_field($_POST['security']);

        if (!wp_verify_nonce($nonce, "registration_nonce") || !is_user_logged_in()) {
            wp_send_json_error(["message" => esc_html__("Invalid request", 'swap')]);
        }

        $parent_product_ID = "";
        $product_link_bay = "";
        $idBay = $this->createBayProduct();
        $idBid = $this->createBidProduct();
        $idRent = $this->createRentProduct();

        if ($idBay) {
            $parent_product_ID = $idBay;
            update_post_meta($idBay, '_custom_parent_id',  $parent_product_ID);
            $this->updateProductMeta($idBay);
            $product_link_bay .= get_permalink($idBay);
        }

        if ($idBid) {
            $parent_product_ID = empty($parent_product_ID) ? $idBid : $parent_product_ID;
            update_post_meta($idBid, '_custom_parent_id',  $parent_product_ID);
            $this->updateProductMeta($idBid);
            $product_link_bay .= get_permalink($idBid);
        }

        if ($idRent) {
            $parent_product_ID = empty($parent_product_ID) ? $idRent : $parent_product_ID;
            update_post_meta($idRent, '_custom_parent_id',  $parent_product_ID);
            $this->updateProductMeta($idRent);
            $product_link_bay .= get_permalink($idRent);
        }

        wp_send_json_success([
            "message" => esc_html__("Product created successfully!", 'swap'),
            "parent_id" => $parent_product_ID
        ]);
    }

    public function saveImages()
    {
        $nonce = sanitize_text_field($_POST['security']);

        if (!wp_verify_nonce($nonce, "registration_nonce") || !is_user_logged_in()) {
            wp_send_json_error(["message" => esc_html__("Invalid request", 'swap')]);
        }

        $user_id = get_current_user_id();

        if (empty($_FILES['files'])) {
            wp_send_json_error(['message' => 'Файли не завантажені']);
        }

        $uploaded_files = $_FILES['files'];
        $uploaded_urls = [];

        foreach ($uploaded_files['name'] as $key => $filename) {
            $file = [
                'name' => $uploaded_files['name'][$key],
                'type' => $uploaded_files['type'][$key],
                'tmp_name' => $uploaded_files['tmp_name'][$key],
                'error' => $uploaded_files['error'][$key],
                'size' => $uploaded_files['size'][$key],
            ];

            $upload = wp_handle_upload($file, ['test_form' => false]);

            if (isset($upload['error'])) {
                wp_send_json_error(['message' => $upload['error']]);
            }


            $attachment_id = wp_insert_attachment([
                'guid' => $upload['url'],
                'post_mime_type' => $upload['type'],
                'post_title' => sanitize_file_name($filename),
                'post_content' => '',
                'post_status' => 'inherit',
            ], $upload['file']);


            require_once ABSPATH . 'wp-admin/includes/image.php';
            wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $upload['file']));

            update_post_meta($attachment_id, '_uploaded_by', $user_id);

            $uploaded_urls[] = ['url' => $upload['url'], 'id' => $attachment_id];
        }

        wp_send_json_success(['urls' => $uploaded_urls]);
    }

    function handle_save_snapshot()
    {
        $nonce = sanitize_text_field($_POST['security']);

        if (!wp_verify_nonce($nonce, "registration_nonce") || !is_user_logged_in()) {
            wp_send_json_error(["message" => esc_html__("Invalid request", 'swap')]);
        }

        $user_id = get_current_user_id();
        $image_data = $_POST['image'];


        $image_data = str_replace('data:image/png;base64,', '', $image_data);
        $image_data = base64_decode($image_data);

        $upload_dir = wp_upload_dir();
        $file_path = $upload_dir['path'] . '/snapshot_' . time() . '.png';

        file_put_contents($file_path, $image_data);

        $attachment = array(
            'guid' => $upload_dir['url'] . '/snapshot_' . time() . '.png',
            'post_mime_type' => 'image/png',
            'post_title' => 'Snapshot ' . time(),
            'post_content' => '',
            'post_status' => 'inherit',
        );

        $attachment_id = wp_insert_attachment($attachment, $file_path);

        require_once ABSPATH . 'wp-admin/includes/image.php';
        wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $file_path));

        update_post_meta($attachment_id, '_uploaded_by', $user_id);

        wp_send_json_success(['url' => $upload_dir['url'] . '/snapshot_' . time() . '.png', 'id' => $attachment_id]);
    }
}

Upload_Control::get_instance();
