<?php
namespace Omnis\src\inc\classes\auth;

class Auth {
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->setup_hooks();
            self::$instance->setup_ajax_handlers();
        }
        return self::$instance;
    }


    private function setup_hooks() {
        add_action('wp_enqueue_scripts', [$this, 'deenqueue_scripts'], 99);
        add_action('template_redirect', [$this, 'redirect_to_login']);
        add_action('init', [$this, 'customer_registration_form_handler']);
    }

    public function wp_ajax_action(string $action): void {
        add_action('wp_ajax_' . $action, [$this, $action]);
        add_action('wp_ajax_nopriv_' . $action, [$this, $action]);
    }

    public function setup_ajax_handlers() {
        $this->wp_ajax_action('set_guest'); 
    }

    public function set_guest() { 
        check_ajax_referer('registration_nonce', 'security');
    
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        if (!empty($_SESSION['guest_mode']) && $_SESSION['guest_mode']['expires'] < time()) {
            unset($_SESSION['guest_mode']);
        }
    
        if (empty($_SESSION['guest_mode'])) {
            $_SESSION['guest_mode'] = [
                'status' => true,
                'expires' => time() + 86400, 
            ];
        }
    
        session_write_close();
    
        wp_send_json_success(['message' => 'Guest mode activated']);
    }

    public function redirect_to_login() {
        if (is_user_logged_in()) {
            if (is_page('login') || is_page('lost-password') || is_page('registration') || is_page('customer-registration')) {
                wp_redirect(home_url());
                exit;
            }
        } else {
            if (!empty($_SESSION['guest_mode']) && $_SESSION['guest_mode']['expires'] < time()) {
                unset($_SESSION['guest_mode']);
            }

            $status = !empty($_SESSION['guest_mode']) ? $_SESSION['guest_mode']['status'] : false;

            if (!$status && !is_page('login') && !is_page('lost-password') && !is_page('restore-password') && !is_page('registration') && !is_page('customer-registration')) {
                wp_redirect(home_url('/login'));
                exit;
            }
        }
    }

    public function deenqueue_scripts() {
        if (is_page('login') || is_page('customer-registration') || is_page('reports-custom')) {
            wp_dequeue_style('storefront-style-parent'); 
            wp_dequeue_style('storefront-style');
            wp_deregister_style('storefront-style');
        }
    }

    public function customer_registration_form_handler() {
        if (isset($_POST['register'])) {
            // Перевірка nonce
            if (!isset($_POST['user_registration_nonce']) || !wp_verify_nonce($_POST['user_registration_nonce'], 'user_registration')) {
                wc_add_notice(__('Invalid nonce', 'swap'), 'error');
                return;
            }
    
            $username = sanitize_text_field($_POST['username']);
            $email = sanitize_email($_POST['email']);
            $dob = sanitize_text_field($_POST['dob']);
            $styles = isset($_POST['styles']) ? array_map('sanitize_text_field', $_POST['styles']) : [];
            $profile_picture = isset($_FILES['profile_photo']) ? $_FILES['profile_photo'] : null;
            $password = wp_generate_password();

            // Валідація
            if (empty($username) || empty($email)) {
                wc_add_notice(__('Please fill all required fields.', 'swap'), 'error');
                return;
            }

            if (!is_email($email)) {
                wc_add_notice(__('Invalid email address.', 'swap'), 'error');
                return;
            }

            if (empty($dob)) {
                wc_add_notice(__('Invalid birthday.', 'swap'), 'error');
                return;
            }

            if (email_exists($email)) {
                wc_add_notice(__('Email already exists.', 'swap'), 'error');
                return;
            }

            // Створення користувача через WordPress API
            $user_id = wp_insert_user([
                'user_login' => $email,
                'user_email' => $email,
                'first_name' => $username,
                'user_pass'  => $password,
                'role'       => 'customer'
            ]);

            if (is_wp_error($user_id)) {
                wc_add_notice($user_id->get_error_message(), 'error');
                return;
            }

            // Додаємо додаткові мета-поля
            update_user_meta($user_id, 'dob', $dob);
            update_user_meta($user_id, 'styles', $styles);

            // Обробка фото профілю
            if ($profile_picture && !empty($profile_picture['name'])) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';

                $upload_overrides = ['test_form' => false];
                $movefile = wp_handle_upload($profile_picture, $upload_overrides);

                if ($movefile && !isset($movefile['error'])) {
                    $attachment = [
                        'post_mime_type' => $movefile['type'],
                        'post_title'     => sanitize_file_name($movefile['file']),
                        'post_content'   => '',
                        'post_status'    => 'inherit'
                    ];

                    $attach_id = wp_insert_attachment($attachment, $movefile['file'], $user_id);
                    $attach_data = wp_generate_attachment_metadata($attach_id, $movefile['file']);
                    wp_update_attachment_metadata($attach_id, $attach_data);

                    update_user_meta($user_id, 'profile_picture', $attach_id);
                } else {
                    wc_add_notice(__('Failed to upload profile picture.', 'swap'), 'error');
                }
            }

            // Автоматичний логін після реєстрації
            $credentials = [
                'user_login'    => $email,
                'user_password' => $password,
                'remember'      => true
            ];
            $auth = wp_signon($credentials, is_ssl());

            if (is_wp_error($auth)) {
                wc_add_notice($auth->get_error_message(), 'error');
                return;
            }

            wc_add_notice(__('Registration successful! You are now logged in.', 'swap'), 'success');
            wp_redirect( home_url());
            exit;
        }
    }
}

Auth::get_instance();