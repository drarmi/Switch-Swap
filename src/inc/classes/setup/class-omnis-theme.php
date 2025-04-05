<?php

/**
 * Omnis Theme Setup
 *
 * @package omnis_base
 * @since   4.4.0
 */

namespace Omnis\src\inc\classes\setup;

use Omnis\src\inc\classes\helpers\Install_Required_Plugins;
use Omnis\src\inc\classes\auth\Auth;
use Omnis\src\inc\classes\user\User_Notifications;
use Omnis\src\inc\classes\auth\Password_Control;
use Omnis\src\inc\classes\pages\Home_Page;
use Omnis\src\inc\classes\pages\Product_Custom;
use Omnis\src\inc\classes\pages\Product_Management;
use Omnis\src\inc\classes\my_favorites\My_Favorites;
use Omnis\src\inc\classes\auth\Google_Login;
use Omnis\src\inc\classes\auth\Apple_Login;
use Omnis\src\inc\classes\components\Upload_Control;
use Omnis\src\inc\classes\dokan_enhancer\Dokan_Enhancer;
use Omnis\src\inc\classes\dokan_enhancer\Authenticate_seller;
use Omnis\src\inc\classes\search\Product_Search;
//use Omnis\src\inc\classes\registration\User_Registration;
//use Omnis\src\inc\classes\user\User;

// Импортируем глобальные функции WordPress без указания собственного пространства имён
use function register_nav_menus;
use function esc_html__;
use function add_action;
use function add_filter;
use function add_theme_support;
use function load_theme_textdomain;
use function wp_dequeue_style;
use function wp_deregister_style;
use function wp_enqueue_script;
use function wp_enqueue_style;
use function wp_localize_script;

/**
 *  This class handles the setup of the Omnis theme, including:
 *  - Bootstrapping required components
 *  - Enqueuing scripts and styles
 *  - Adding theme options using Advanced Custom Fields
 *  - Setting up language support
 *  - Registering custom post types and taxonomies
 *  - Localizing scripts for AJAX functionality
 *  - Deregistering unnecessary styles
 *  - Registering navigation menus
 *  - Setting up theme support features
 */
class Omnis_Theme
{

    /**
     * Holds the singleton instance of this class.
     *
     * @var Omnis_Theme
     */
    private static Omnis_Theme $instance;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Bootstrap required components.
        $this->add_action('init', array($this, 'bootstrap'));
        // Enqueue scripts and styles.
        $this->add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        $this->add_action('wp_enqueue_scripts', array($this, 'enqueue_style'), 100);
        // Add theme options using Advanced Custom Fields.
        $this->add_action('init', array($this, 'advanced_custom_fields_options'));
        // Set up language support.
        $this->add_action('init', array($this, 'languages'));
        // Register custom post types.
        $this->add_action('init', array($this, 'post_type'));
        // Register taxonomies.
        $this->add_action('init', array($this, 'taxonomy'));
        // Register navigation menus.
        $this->add_action('after_setup_theme', array($this, 'register_menus'));
        // Set up theme support features.
        $this->add_action('after_setup_theme', array($this, 'theme_setup'));
        // Deregister unnecessary styles.
        $this->add_action('wp_enqueue_scripts', array($this, 'dequeue_style'));
        // Localize scripts for AJAX functionality.
        $this->add_action('wp_enqueue_scripts', array($this, 'localize_script'));

        $this->add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Adds a WordPress action hook with the specified callback, priority, and accepted arguments.
     *
     * This method is a wrapper for the WordPress `add_action()` function, allowing
     * you to register a callback function to a specific action hook with an optional
     * priority and number of accepted arguments.
     *
     * @param string   $hook          The name of the action hook.
     * @param callable $callback      The callback function to be executed when the action is triggered.
     * @param int|null $priority      Optional. The priority at which the function should be executed. Default is 10.
     * @param int|null $accepted_args Optional. The number of arguments the callback accepts. Default is 1.
     *
     * @return void
     */
    public function add_action(string $hook, callable $callback, int $priority = null, int $accepted_args = null): void
    {
        add_action($hook, $callback, $priority, $accepted_args);
    }

    /**
     * Adds a WordPress filter hook with the specified callback, priority, and accepted arguments.
     *
     * This method is a wrapper for the WordPress `add_filter()` function, allowing
     * you to register a callback function to a specific filter hook with an optional
     * priority and number of accepted arguments.
     *
     * @param string   $hook          The name of the filter hook.
     * @param callable $callback      The callback function to be executed when the filter is triggered.
     * @param int|null $priority      Optional. The priority at which the function should be executed. Default is 10.
     * @param int|null $accepted_args Optional. The number of arguments the callback accepts. Default is 1.
     *
     * @return void
     */
    public function add_filter(string $hook, callable $callback, int $priority = null, int $accepted_args = null): void
    {
        add_filter($hook, $callback, $priority, $accepted_args);
    }

    /**
     * Bootstrap required components
     */
    public static function bootstrap(): void
    {
        //Install_Required_Plugins::get_instance();
        Product_Search::get_instance();
        Home_Page::get_instance();
        Product_Custom::get_instance();
        Product_Management::get_instance();
        My_Favorites::get_instance();
        //User_Registration::get_instance();
        //User::get_instance();
        Auth::get_instance();
        User_Notifications::get_instance();
        Password_Control::get_instance();
        Google_Login::get_instance();
        Apple_Login::get_instance();
        Upload_Control::get_instance();
        Dokan_Enhancer::get_instance();
        Authenticate_seller::get_instance();
    }

    /**
     * Get an instance of the class
     */
    public static function get_instance(): Omnis_Theme
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Dequeue hooks
     */
    public function dequeue_hooks(): void
    {
        add_filter('big_image_size_threshold', '__return_false');
    }

    /**
     * Add options using Advanced Custom Fields
     */
    public function advanced_custom_fields_options(): void
    {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page(
                array(
                    'page_title' => 'Theme Options',
                    'menu_title' => 'Theme Options',
                    'menu_slug'  => 'theme-options',
                    'capability' => 'edit_posts',
                    'redirect'   => true,
                )
            );

            // Add subpages.
            acf_add_options_sub_page(
                array(
                    'page_title'  => 'Header',
                    'menu_title'  => 'Header',
                    'parent_slug' => 'theme-options',
                )
            );

            acf_add_options_sub_page(
                array(
                    'page_title'  => 'Footer',
                    'menu_title'  => 'Footer',
                    'parent_slug' => 'theme-options',
                )
            );

            acf_add_options_sub_page(
                array(
                    'page_title'  => 'Popup Quick consultation',
                    'menu_title'  => 'Popup Quick consultation',
                    'parent_slug' => 'theme-options',
                )
            );

            acf_add_options_sub_page(
                array(
                    'page_title'  => '404 Page',
                    'menu_title'  => '404 Page',
                    'parent_slug' => 'theme-options',
                )
            );
            acf_add_options_sub_page(
                array(
                    'page_title'  => __('Information about the site'),
                    'menu_title'  => __('Information about the site'),
                    'parent_slug' => 'theme-options',
                )
            );
        }
    }

    /**
     * Set up language support.
     */
    public function languages(): void
    {
        load_theme_textdomain('omnis_base', get_template_directory() . '/languages');
    }

    /**
     * Register custom post types.
     */
    public function post_type(): void
    {
        // Implement custom post type registration here.
    }

    /**
     * Register taxonomies.
     */
    public function taxonomy(): void
    {
        $labelsBrands = array(
            'name'              => 'Brands',
            'singular_name'     => 'Brand',
            'search_items'      => 'Search Brands',
            'all_items'         => 'All Brands',
            'parent_item'       => 'Parent Brand',
            'parent_item_colon' => 'Parent Brand:',
            'edit_item'         => 'Edit Brand',
            'update_item'       => 'Update Brand',
            'add_new_item'      => 'Add New Brand',
            'new_item_name'     => 'New Brand Name',
            'menu_name'         => 'Brands',
        );

        $argsBrands = array(
            'hierarchical'      => true, // Set to true for checkbox-style selection
            'labels'            => $labelsBrands,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'brand'),
        );

        register_taxonomy('product_brand', array('product'), $argsBrands);
    }

    /**
     * Localize scripts for AJAX functionality.
     */
    public function localize_script(): void
    {
        $current_user_id = get_current_user_id();
        $user_data = get_userdata($current_user_id);

        if ($user_data && !empty($user_data->roles)) {
            $user_role = $user_data->roles[0];
        } else {
            $user_role = 'guest';
        }

        wp_localize_script(
            'frontend-js',
            'omnis_ajax_object',
            array(
                'ajaxurl'        => admin_url('admin-ajax.php'),
                'home_url'       => get_home_url(),
                'current_ID'     => get_the_ID(),
                'current_url'    => get_permalink(),
                'current_user_id' => get_current_user_id(),
                'nonce'          => wp_create_nonce('registration_nonce'),
                'nonce_dokan'           => wp_create_nonce( 'dokan-seller-rating-form-action' ),
                'current_page'   => get_query_var('paged') ? get_query_var('paged') : 1,
                'checkout_url' => wc_get_checkout_url(),
                'cart_url' => wc_get_cart_url(),
                'date_set' => get_field("selected_dates", get_the_ID()) ?? [],
                'discount_period' => get_field("discount_for_the_period", get_the_ID()) ?? [],
                'userLogin'       => is_user_logged_in(),
                'userRole' => $user_role,
            )
        );
    }

    /**
     * Deregister unnecessary styles.
     */
    public function dequeue_style(): void
    {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('toc-screen');
        wp_dequeue_style('wpm-main');
        wp_dequeue_style('global-styles-inline');
    }

    /**
     * Register navigation menus.
     */
    public function register_menus(): void
    {
        register_nav_menus(
            array(
                'header-menu' => esc_html__('Header Menu', 'text_domain'),
                'footer-menu' => esc_html__('Footer Menu', 'text_domain'),
            )
        );
    }

    /**
     * Set up theme support features.
     */
    public function theme_setup(): void
    {
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('align-wide');
        add_theme_support('responsive-embeds');
        add_theme_support('custom-logo');
    }

    /**
     * Enqueue styles.
     */
    public function enqueue_style(): void
    {
        // Enqueue vendor CSS.
        wp_enqueue_style('vendor-css', get_stylesheet_directory_uri() . '/assets/dist/vendor/vendor.css', array(), THEME_VERSION);
        // Enqueue main CSS.
        wp_enqueue_style('main-css', get_stylesheet_directory_uri() . '/assets/dist/css/main.css', array(), THEME_VERSION);
        // Deregister and dequeue unnecessary styles.
        wp_deregister_style('wp-block-library-rtl');
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('wc-blocks-style');
    }

    public function enqueue_admin_scripts(): void {
        wp_enqueue_script(
            'admin-vendors-authenticate',
            get_stylesheet_directory_uri() . '/src/js/admin/admin-vendors-authenticate.js',
            array('jquery'),
            THEME_VERSION,
            true
        );
        wp_localize_script('admin-vendors-authenticate', 'myAuthAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('vendor_authenticate_nonce'),
        ));
        wp_enqueue_script(
            'my-admin-tiktok',
            get_stylesheet_directory_uri() . '/src/js/admin/my-admin-tiktok.js',
            array('jquery'),
            THEME_VERSION,
            true
        );
        wp_localize_script('my-admin-tiktok', 'myAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
        ));
    }

    /**
     * Enqueue scripts
     */
    public function enqueue_scripts(): void
    {
        // Enqueue vendor JS.
        wp_enqueue_script('vendor-js', get_stylesheet_directory_uri() . '/assets/dist/vendor/vendor.js', array(), THEME_VERSION, true);
        // Enqueue main JS.
        wp_enqueue_script('main-js', get_stylesheet_directory_uri() . '/assets/dist/js/main.js', array('jquery'), THEME_VERSION, true);

        wp_enqueue_script('frontend-js', get_stylesheet_directory_uri() . '/assets/dist/js/frontend.js', array('jquery'), THEME_VERSION, true);
    }
}

// Инициализация класса
Omnis_Theme::get_instance();
