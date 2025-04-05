<?php
namespace Omnis\src\inc\classes\registration;

use WP_Error;
use WeDevs\Dokan\Registration as Dokan_Registration;

class User_Registration {
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

    public function register_user($data) {
        // Process user data
        $first_name = sanitize_text_field($data['name']);
        $password = sanitize_text_field($data['password']);
        $email = sanitize_email($data['email']);
        $dob = sanitize_text_field($data['dob']);
        $gender = isset($data['gender']) ? sanitize_text_field($data['gender']) : '';
        $app_usage = isset($data['app_usage']) ? sanitize_text_field($data['app_usage']) : '';
        $styles = isset($data['styles']) ? array_map('sanitize_text_field', $data['styles']) : [];
        $role = sanitize_text_field($data['role']);
        $terms = isset($data['terms']) ? sanitize_text_field($data['terms']) : '';
        $profile_picture = isset($_FILES['profile_photo']) ? $_FILES['profile_photo'] : null;
        $shopname = isset($data['shopname']) ? sanitize_text_field($data['shopname']) : '';
        $shopurl = isset($data['shopurl']) ? sanitize_text_field($data['shopurl']) : '';

        // Check required fields and valid age
        if (empty($terms)) {
            return new WP_Error('missing_terms', __('You must agree to the Terms and Conditions.', 'omnis_base'));
        }

        if (empty($first_name) || empty($email) || empty($dob) || empty($password)) {
            return new WP_Error('missing_fields', __('Please fill in all required fields.', 'omnis_base'));
        }

        if ($role === 'seller' && (empty($shopname) || empty($shopurl))) {
            return new WP_Error('missing_fields', __('Please fill in all required fields for seller.', 'omnis_base'));
        }

        if (!$this->validate_age($dob)) {
            return new WP_Error('invalid_age', __('You must be at least 18 years old to register.', 'omnis_base'));
        }

        // Create user
        $user_id = wp_create_user($email, $password, $email);
        if (is_wp_error($user_id)) {
            return $user_id;
        }

        // Update user data
        wp_update_user([
            'ID' => $user_id,
            'first_name' => $first_name,
        ]);
        update_user_meta($user_id, 'gender', $gender);
        update_user_meta($user_id, 'app_usage', $app_usage);
        update_user_meta($user_id, 'styles', $styles);
        update_user_meta($user_id, 'dob', $dob);
        update_user_meta($user_id, 'role', $role);

        // Additional settings for 'seller' role
        if ($role === 'seller') {
            wp_update_user(['ID' => $user_id, 'role' => 'seller']);
            update_user_meta($user_id, 'dokan_store_name', $shopname);
            update_user_meta($user_id, 'dokan_store_url', $shopurl);
        }

        // Validate and upload profile picture
        if ($profile_picture && !empty($profile_picture['name'])) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            
            $file_type = wp_check_filetype($profile_picture['name']);
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($file_type['ext'], $allowed_types)) {
                return new WP_Error('invalid_photo', __('Profile photo must be an image file (jpg, jpeg, png, gif).', 'omnis_base'));
            }

            $uploaded_file = wp_handle_upload($profile_picture, ['test_form' => false]);
            if (isset($uploaded_file['url'])) {
                update_user_meta($user_id, 'profile_picture', esc_url($uploaded_file['url']));
            } else {
                return new WP_Error('photo_upload_failed', __('Failed to upload profile picture.', 'omnis_base'));
            }
        }

        // Authenticate the new user
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);

        // Redirect URL
        $redirect_url = $role === 'seller' ? site_url('/?page=dokan-seller-setup') : site_url('/my-account');
        return ['user_id' => $user_id, 'redirect_url' => $redirect_url];
    }

    private function validate_age( $dob ) {
        $birthdate = new \DateTime($dob);
        $today = new \DateTime('today');
        $age = $birthdate->diff($today)->y;

        return $age >= 18;
    }

    public function setup_ajax_handlers() {
        $this->wp_ajax_action('check_shop_url');
        $this->wp_ajax_action('register_user_ajax'); 
    }

    public function check_shop_url() { 
        check_ajax_referer('registration_nonce', 'security');
        
        $shop_url = isset($_POST['shopurl']) ? sanitize_text_field(wp_unslash($_POST['shopurl'])) : '';
        if (empty($shop_url)) {
            wp_send_json_error(['message' => 'Shop URL is required']);
        }
    
        // Check URL availability
        $args = [
            'role'    => 'seller',
            'meta_key' => 'dokan_store_url',
            'meta_value' => $shop_url,
            'number' => 1,
        ];
        $user_query = new \WP_User_Query($args);
        if (!empty($user_query->get_results())) {
            wp_send_json_error(['message' => 'Shop URL is already taken']);
        } else {
            wp_send_json_success(['message' => 'Shop URL is available']);
        }
    }

    public function register_user_ajax() {
        check_ajax_referer('registration_nonce', 'security');
        $data = [
            'name' => sanitize_text_field($_POST['name']),
            'password' => sanitize_text_field($_POST['password']),
            'email' => sanitize_email($_POST['email']),
            'dob' => sanitize_text_field($_POST['dob']),
            'gender' => isset($_POST['gender']) ? sanitize_text_field($_POST['gender']) : '',
            'role' => sanitize_text_field($_POST['role']),
            'app_usage' => isset($_POST['app_usage']) ? sanitize_text_field($_POST['app_usage']) : '',
            'styles' => isset($_POST['styles']) ? $_POST['styles'] : [],
            'terms' => isset($_POST['terms']) ? sanitize_text_field($_POST['terms']) : '',
            'shopname' => isset($_POST['shopname']) ? sanitize_text_field($_POST['shopname']) : '',
            'shopurl' => isset($_POST['shopurl']) ? sanitize_text_field($_POST['shopurl']) : '',
            'address' => isset($_POST['address']) ? wc_clean($_POST['address']) : null,
            'phone' => isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : null,
            'profile_photo' => $_FILES['profile_photo'] ?? null,
        ];
        $result = $this->register_user($data);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        } else {
            wp_send_json_success($result);
        }
    }
}

User_Registration::get_instance();
