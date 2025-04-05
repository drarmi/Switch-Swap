<?php

namespace Omnis\src\inc\classes\pages;

class Product_Management
{
    private static $instance;

    public function __construct()
    {
        $this->setup_ajax_handlers();
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
        $this->wp_ajax_action('get_product_managementHTML');
    }

   
    public function wp_ajax_action($action)
    {
        add_action('wp_ajax_' . $action, [$this, $action]);
        add_action('wp_ajax_nopriv_' . $action, [$this, $action]);
    }

    public function get_product_managementHTML(){
        $nonce = sanitize_text_field($_POST['security']);
    
        if(!wp_verify_nonce($nonce, "registration_nonce") || empty($_POST['type']) || empty($_POST['page'])){
            wp_send_json_error(["message" => esc_html__("Invalid request", 'swap')]);
        }
        if (!is_user_logged_in()) {
            wp_send_json_error(["message" => esc_html__("User not logged in", 'swap')]);
        }
       
        $type = sanitize_text_field($_POST['type']);
        $page = intval($_POST['page']);
        $user_id = get_current_user_id();
        
        $productsPart = self::get_user_products_with_details_per_page($user_id, $page, 10, $type);
        
        if(empty($productsPart)){
            wp_send_json_success(["message" => esc_html__("There is no product", 'swap')]);
        }
        
        ob_start();
       
        foreach($productsPart as $productDetails){
            get_template_part("src/template-parts/product-management/product-list-card", null, ["productDetails" => $productDetails]);
        }
        $html = ob_get_clean();

        

        wp_send_json_success(["html" => $html]);
    }

    static function get_all_user_products(int $user_id): array {
        global $wpdb;
    
        $query = $wpdb->prepare("
            SELECT p.ID 
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
                AND pm.meta_key = '_custom_parent_id' 
                AND pm.meta_value != ''
            WHERE p.post_type = 'product'  
            AND p.post_status NOT IN ('trash')
            AND p.post_author = %d
        ", $user_id);
    
        $products = $wpdb->get_results($query, ARRAY_A);
    
        return $products ?: [];
    }

    static function get_variation_id(int $product_id): array{
        global $wpdb;

        $custom_parent_id = get_post_meta($product_id, '_custom_parent_id', true);

        $products = $wpdb->get_results($wpdb->prepare(
            "SELECT p.ID 
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
             WHERE p.post_type = 'product'
             AND pm.meta_key = '_custom_parent_id'
             AND pm.meta_value = %s",
            $custom_parent_id
        ));
        
        $bidID = "";
        $rentID = "";
        $simpleID = "";
        
        foreach ($products as $product) {
            $product_id = $product->ID;
            $terms = wp_get_object_terms($product_id, 'product_type', ['fields' => 'names']);
            
            if(!empty($terms) && $terms[0] == "auction") {
                $bidID = $product_id;
            } elseif(!empty($terms) && $terms[0] == "booking") {
                $rentID = $product_id;
            } elseif(!empty($terms) && $terms[0] == "simple") {
                $simpleID = $product_id;
            }
        }
    
        return [
            "bidID" => $bidID,
            "rentID" => $rentID,
            "simpleID" => $simpleID
        ];
    }

    static function get_user_products_with_details_per_page(int $user_id, int $page = 1, int $per_page = 10, string $product_type = ''): array {
        global $wpdb;
    
        $offset = ($page - 1) * $per_page;
        
        $product_type_condition = '';
        if ($product_type && $product_type != "all") {
            $product_type_condition = $wpdb->prepare("
                AND EXISTS (
                    SELECT 1
                    FROM {$wpdb->term_relationships} tr
                    INNER JOIN {$wpdb->terms} t ON tr.term_taxonomy_id = t.term_id
                    INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
                    WHERE tr.object_id = p.ID
                    AND tt.taxonomy = 'product_type'
                    AND t.slug = %s
                )
            ", $product_type);
        }
    
        $query = $wpdb->prepare("
            SELECT p.ID, p.post_title, p.post_content AS description, 
                pm.meta_value AS thumbnail_id
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
            WHERE p.post_type = 'product'  
            AND p.post_author = %d
            AND p.post_status NOT IN ('trash')
            $product_type_condition
            LIMIT %d OFFSET %d
        ", $user_id, $per_page, $offset);
        
        $products = $wpdb->get_results($query, ARRAY_A);
    
        foreach ($products as &$product) {
            $product['thumbnail'] = $product['thumbnail_id'] 
                ? wp_get_attachment_url($product['thumbnail_id']) 
                : null;
    
            $product['url'] = get_permalink($product['ID']);
    
            unset($product['thumbnail_id']);
        }
    
        return $products ?: [];
    }

    static function get_rent_date($productID){
        global $wpdb;
        $sortArr = [];
        $query = "
            SELECT pm.post_id, pm.meta_key, pm.meta_value 
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->postmeta} pm2 ON pm.post_id = pm2.post_id
            WHERE pm2.meta_key = '_booking_product_id' 
            AND pm2.meta_value = %d
            AND pm.meta_key IN ('_booking_end', '_booking_start')
        ";
        
        $booking_data = $wpdb->get_results($wpdb->prepare($query, $productID));
        
        if (!empty($booking_data)) {
            $bookings = [];
    
            foreach ($booking_data as $meta) {
                $bookings[$meta->post_id][$meta->meta_key] = $meta->meta_value;
            }
    
            foreach ($bookings as &$booking) {
                ksort($booking);
                $booking = array_values($booking);
            }
    
            foreach(array_values($bookings) as $value){
                if($value[0] > $value[1]){
                    $sortArr[] = [$value[1], $value[0]];
                }else{
                    $sortArr[] = [$value[0], $value[1]];
                }
                
            }
        }
        
        return $sortArr;
    }

    static function returnStatus($product_id) {
        $custom_parent_id = get_post_meta($product_id, '_custom_parent_id', true);
        $variation_ids = self::get_variation_id((int) $custom_parent_id);
        $status = "";
        $stock_status = "";
        
        foreach ($variation_ids as $id) {
            if (!$id) continue;
    
            $product = wc_get_product($id);
            if (!$product) continue;
            
            if (empty($status) || $status === "publish") {
                $status = $product->get_status();
            }
            
            if (empty($stock_status) || $stock_status === "instock") {
                $stock_status = $product->get_stock_status();
            }
        }
    
        $is_rent = false;
        if (!empty($variation_ids["rentID"])) {
            $rent_date = self::get_rent_date($variation_ids["rentID"]);
            $currentDate = new \DateTime();
    
            foreach ($rent_date as $date) {
                if (!isset($date[0], $date[1])) continue;
    
                $from = \DateTime::createFromFormat("YmdHis", $date[0]);
                $to = \DateTime::createFromFormat("YmdHis", $date[1]);
    
                if ($from && $to && $currentDate >= $from && $currentDate <= $to) {
                    $is_rent = true;
                    break;
                }
            }
        }
    
        return [
            "stock_status" => $stock_status ?: "outofstock",
            "status" => $status ?: "draft",
            "is_rent" => $is_rent
        ];
    }

    static function getProductStatusHTML($product_id) {
        $status = self::returnStatus($product_id);
        $product_author_id = get_post_field('post_author', $product_id);
        $current_user_id = get_current_user_id();
    
        if ($product_author_id != $current_user_id) {
            return "";
        }

        ob_start();
        ?>
        <div class="status-product">
            <?php if ($status["stock_status"] != "instock" || $status["stock_status"] == "draft"): ?>
                <span style="background: #858585"></span>
                <span><?php esc_html_e("נמכר", "swap"); ?></span>
            <?php elseif ($status["is_rent"]): ?>
                <span style="background: #E66702"></span>
                <span><?php esc_html_e("השכרה", "swap"); ?></span>
            <?php elseif ($status["status"] === "publish"): ?>
                <span style="background: #96D200"></span>
                <span><?php esc_html_e("זמין", "swap"); ?></span>
            <?php else: ?>
                <span style="background: #E2C018"></span>
                <span><?php esc_html_e("ממתין לפרסום", "swap"); ?></span>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    static function getUserCommentsData($user_id) {
        if (!is_numeric($user_id) || $user_id <= 0) {
            return new \WP_Error('invalid_user_id', esc_html__('Invalid user ID provided', 'swap'));
        }

        $cache_key = 'user_comments_data_' . $user_id;
        $cached_data = wp_cache_get($cache_key);
        
        if (false !== $cached_data) {
            return $cached_data;
        }

        try {
            $args = array(
                'user_id'    => $user_id,
                'status'     => 'approve',
                'post_type'  => 'product',
                'meta_key'   => 'rating',
            );
            
            $comments = get_comments($args);
            
            if (empty($comments)) {
                return [
                    'total_reviews' => 0,
                    'avg_rating'    => 0,
                    'rating_1'      => 0,
                    'rating_2'      => 0,
                    'rating_3'      => 0,
                    'rating_4'      => 0,
                    'rating_5'      => 0,
                ];
            }

            $ratings = array_fill(1, 5, 0);
            $total_rating = 0;
            $total_reviews = 0;

            foreach ($comments as $comment) {
                $rating = get_comment_meta($comment->comment_ID, 'rating', true);
                if ($rating >= 1 && $rating <= 5) {
                    $ratings[$rating]++;
                    $total_rating += $rating;
                    $total_reviews++;
                }
            }

            $data = [
                'total_reviews' => $total_reviews,
                'avg_rating'    => $total_reviews > 0 ? round($total_rating / $total_reviews, 2) : 0,
                'rating_1'      => $ratings[1],
                'rating_2'      => $ratings[2],
                'rating_3'      => $ratings[3],
                'rating_4'      => $ratings[4],
                'rating_5'      => $ratings[5],
            ];

            // Cache the results for 1 hour
            wp_cache_set($cache_key, $data, '', HOUR_IN_SECONDS);

            return $data;

        } catch (\Exception $e) {
            return new \WP_Error('data_error', esc_html__('Error processing review data', 'swap'));
        }
    }

    static function getRelatedProducts($product_id): array {
        if (!is_numeric($product_id) || $product_id <= 0) {
            return [];
        }

        $parent_id = get_post_meta($product_id, '_custom_parent_id', true);
        if (empty($parent_id)) {
            return [];
        }

        global $wpdb;
        
        $products = $wpdb->get_col($wpdb->prepare(
            "SELECT DISTINCT p.ID 
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE pm.meta_key = '_custom_parent_id' 
            AND pm.meta_value = %s
            AND p.post_type = 'product'
            AND p.post_status NOT IN ('trash')
            LIMIT 100",
            $parent_id
        ));
        
        $product_ids = !empty($products) ? array_map('absint', $products) : [];

        return $product_ids;
    }
}

