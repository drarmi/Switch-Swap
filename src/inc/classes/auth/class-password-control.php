<?php

namespace Omnis\src\inc\classes\auth;

class Password_Control
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
        $this->wp_ajax_action('reset_password');
        $this->wp_ajax_action('custom_reset_password');
        $this->wp_ajax_action('dokan_register_vendor');
        $this->wp_ajax_action('custom_change_password_no_old');
    }

    private function setup_hooks() {}


    public function wp_ajax_action($action)
    {
        add_action('wp_ajax_' . $action, [$this, $action]);
        add_action('wp_ajax_nopriv_' . $action, [$this, $action]);
    }

    public function reset_password()
    {
        $security = sanitize_text_field($_POST['custom-lost-password-nonce']);
        $user_login = sanitize_text_field($_POST['user_login'] ?? "");

        if (!wp_verify_nonce($security, 'lost_password') || !$user_login) {
            wp_send_json_error('מסננים לא חוקיים או מסננים ריקים.');
        }

        $user = get_user_by('login', $user_login) ?: get_user_by('email', $user_login);

        if (!$user) {
            wp_send_json_error('לא נמצא משתמש עם שם המשתמש או האימייל הזה.');
        }

        $reset_key = get_password_reset_key($user);

        if (is_wp_error($reset_key)) {
            wp_send_json_error('שגיאה ביצירת מפתח איפוס סיסמה.');
        }

        $reset_url = add_query_arg(
            array(
                'key' => $reset_key,
                'login' => rawurlencode($user->user_login),
            ),
            home_url('/restore-password/')
        );

        $html = file_get_contents(FUNCTION_DIR_PATH . "/src/template-parts/lost-password/message.html");
        $html = str_replace("{{link_to}}", $reset_url, $html);

        $subject = 'Password Reset Request';
        $to = $user->user_email;
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $mail_sent = wp_mail($to, $subject, $html, $headers);

        if ($mail_sent) {
            wp_send_json_success($reset_url);
        } else {
            wp_send_json_error('There was an error sending the reset email. Please try again.');
        }
    }

    function custom_reset_password()
    {
        $security = sanitize_text_field($_POST['custom-lost-password-nonce']);

        if (!wp_verify_nonce($security, 'lost_password') || !isset($_POST['key'], $_POST['login'], $_POST['pwd'])) {
            wp_send_json_error('מסננים לא חוקיים או מסננים ריקים.');
        }

        $reset_key = sanitize_text_field($_POST['key']);
        $user_login = sanitize_text_field($_POST['login']);
        $new_password = sanitize_text_field($_POST['pwd']);


        $user = check_password_reset_key($reset_key, $user_login);
        if (is_wp_error($user)) {
            wp_send_json_error('מפתח או משתמש לא חוקיים.');
        }

        $update_user = wp_update_user(array(
            'ID' => $user->ID,
            'user_pass' => $new_password
        ));

        if (is_wp_error($update_user)) {
            wp_send_json_error('עדכון הסיסמה נכשל.');
        }

        wp_send_json_success();
    }

    function dokan_register_vendor() {
        check_ajax_referer('user_registration', 'security');
    
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $dob = sanitize_text_field($_POST['dob']);
        $password = $_POST['pas-registration'];
        $password_confirm = $_POST['pas-confirm-registration'];
        $styles = $_POST['styles'] ?? [];
        $profile_photo = $_FILES['profile_photo'] ?? null;
    
        if (!$username || !$email || !$password || !$password_confirm) {
            wp_send_json_error(['message' => 'Required fields are missing.']);
        }
    
        if ($password !== $password_confirm) {
            wp_send_json_error(['message' => 'Passwords do not match.']);
        }
    
        if (username_exists($username) || email_exists($email)) {
            wp_send_json_error(['message' => 'User already exists.']);
        }
    
        $user_id = wp_create_user($username, $password, $email);
    
        if (is_wp_error($user_id)) {
            wp_send_json_error(['message' => 'Error creating user.']);
        }
    
        wp_update_user([
            'ID' => $user_id,
            'role' => 'seller',
            'display_name' => $username,
        ]);
    
        $logoID = '';
    
        if ($profile_photo) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
    
            $uploaded = wp_handle_upload($profile_photo, ['test_form' => false]);
    
            if (!isset($uploaded['error'])) {
                $file = [
                    'name' => $profile_photo['name'],
                    'type' => $profile_photo['type'],
                    'tmp_name' => $uploaded['file'],
                    'error' => $profile_photo['error'],
                    'size' => $profile_photo['size'],
                ];
    
                $attachment_id = media_handle_sideload($file, 0);
    
                if (is_wp_error($attachment_id)) {
                    $logoID = "";
                }else{
                    $logoID = $attachment_id;
                }
                
            } else {
                wp_send_json_error(['message' => $uploaded['error']]);
            }
        }
    
        $store_user = dokan()->vendor->get($user_id);
    
        if (!$store_user) {
            wp_send_json_error(['message' => 'Error creating vendor user.']);
        }
    
        $store_user->set_store_name($username);
        $store_user->set_phone($phone);
    
        if ($logoID) {
            $store_user->set_gravatar_id($logoID);
        }
    
        $json_data = json_encode($styles);
    
        update_user_meta($store_user->get_id(), 'styles_fields_vendor', $json_data);
        update_user_meta($store_user->get_id(), 'dob_vendor', $dob);
    
        $store_user->save();


        $creds = [
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => true,
        ];
        
        $user = wp_signon($creds, false);
        

        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);
    
        wp_send_json_success([
            'message' => 'Vendor registered successfully!',
            'redirect_url' => home_url('/dashboard/'),
            'name' => $store_user->get_store_name(),
            'logo_url' => wp_get_attachment_url($logoID) // Отримуємо URL лого
        ]);
    }


    function custom_change_password_no_old() {
        check_ajax_referer('registration_nonce', 'security');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'User is not logged in']);
        }
    
        $user_id = get_current_user_id();
        $new_password = $_POST['pwd'] ?? '';
    
        if (empty($new_password) || strlen($new_password) < 8 || !preg_match('/^(?=.*[A-Z])(?=.*\d).*$/', $new_password)) {
            wp_send_json_error(['message' => 'Password must be at least 8 characters long, contain one uppercase letter, and one number']);
        }
    
        wp_set_password($new_password, $user_id);
    
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
    
        wp_send_json_success(['message' => 'Password successfully changed']);
    }
    
}

Password_Control::get_instance();

