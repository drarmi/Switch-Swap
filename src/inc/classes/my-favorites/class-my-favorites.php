<?php

namespace Omnis\src\inc\classes\my_favorites;

class My_Favorites
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
        $this->wp_ajax_action('init_like');
    }

    public function wp_ajax_action($action)
    {
        add_action('wp_ajax_' . $action, [$this, $action]);
        add_action('wp_ajax_nopriv_' . $action, [$this, $action]);
    }

    public function init_like()
    {
        $nonce = sanitize_text_field($_POST['security']);
        $product_id = intval($_POST['ID']);
        $user_logged = is_user_logged_in();
    
        if (!wp_verify_nonce($nonce, "registration_nonce") || !$user_logged || !$product_id) {
            wp_send_json_error(["message" => esc_html__("Invalid request or user not logged in.", 'swap')]);
        }
    
        $user_id = get_current_user_id();
        $like_product = get_field("like_product", "user_" . $user_id);
        $like_product = is_array($like_product) ? $like_product : [];
        $is_like = false;
        $like_count = (int) get_field("like_count", $product_id);
        $like_count = max(0, $like_count);
    
        if (in_array($product_id, $like_product)) {
            $like_product = array_diff($like_product, [$product_id]);
            $is_like = false;
            $like_count = max(0, $like_count - 1);
        } else {
            $like_product[] = $product_id;
            $is_like = true;
            $like_count++; 
        }
    
        update_field("like_product", $like_product, "user_" . $user_id);
        update_field("like_count", $like_count, $product_id);
    
        wp_send_json_success(["active" => $is_like, "count" => $like_count]);
    }

    static function get_user_like_product(){
        $user_logged = is_user_logged_in();
        if ($user_logged) {
            $user_id = get_current_user_id();
            return get_field("like_product", "user_" . $user_id);
        }else{
            return [];
        }
    }
}


