<?php
namespace Omnis\src\inc\classes\user;

class User_Notifications {
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->setup_ajax_handlers();
        }
        return self::$instance;
    }

    public function wp_ajax_action(string $action): void {
        add_action('wp_ajax_' . $action, [$this, $action]);
        add_action('wp_ajax_nopriv_' . $action, [$this, $action]);
    }

    public function setup_ajax_handlers() {
        $this->wp_ajax_action('notifications_switch');
    }

    public function notifications_switch() {
        if (!isset($_POST['notifications-form_nonce']) || 
            !wp_verify_nonce($_POST['notifications-form_nonce'], 'notifications-form_action')) {
            
            wp_send_json_error(['message' => "Not valid nonce"]);
        }
    
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => "Not valid user"]);
        }

        if(empty($_POST) || !is_array($_POST)){
            wp_send_json_error(['message' => "Not valid request"]);
        }

        if (!function_exists('get_field') || !function_exists('update_field')) {
            return;
        }
        $user_id = get_current_user_id();

        $existing_notifications = get_field('notifications-user', 'user_' . $user_id);
    
        if (!$existing_notifications) {
            $existing_notifications = [];
        }
    
        foreach($_POST as $key => $value){
            if($key != "notifications-form_nonce" && $key != "action" && $key != "_wp_http_referer"){
                $existing_notifications[$key] = (bool) $value;
            }
        }
        
        update_field('notifications-user', $existing_notifications, 'user_' . $user_id);

        wp_send_json_success(get_field('notifications-user', 'user_' . get_current_user_id()));
    }
}

