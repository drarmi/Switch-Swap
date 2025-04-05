<?php


function storefront_child_enqueue_styles()
{
    wp_enqueue_style('storefront-style-parent', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('storefront-child-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'storefront_child_enqueue_styles', 15);


// Define theme directory path if not already defined.
if (! defined('THEME_DIR_PATH')) {
    define('THEME_DIR_PATH', untrailingslashit(get_template_directory()));
}

// Define theme directory path if not already defined.
if (! defined('FUNCTION_DIR_PATH')) {
    define('FUNCTION_DIR_PATH', untrailingslashit(__DIR__));
}

// Define theme directory URI if not already defined.
if (! defined('THEME_DIR_URI')) {
    define('THEME_DIR_URI', untrailingslashit(get_template_directory_uri()));
}

// Define theme version if not already defined.
if (! defined('THEME_VERSION')) {
    define('THEME_VERSION', gmdate('YmdHis'));
}

// Define nonce code if not already defined.
if (! defined('NONCE_CODE')) {
    define('NONCE_CODE', untrailingslashit(get_template_directory_uri()));
}

// Include Composer autoload file.

require_once __DIR__ . '/vendor/autoload.php';

// Include autoloader file for theme classes.
require_once(trailingslashit(get_theme_file_path()) . 'src/inc/class-autoloader.php');

// Import necessary class for theme setup.
use Omnis\src\inc\classes\setup\Omnis_Theme;

require_once __DIR__ . "/src/inc/classes/helpers/additional-functions.php";

// Get instance of the Omnis_Theme class to initiate theme setup.
Omnis_Theme::get_instance();


add_action('init', function() {
    if (defined('DOING_AJAX') && DOING_AJAX) {
        if (!headers_sent() && '' == session_id()) {
            session_start();
        }
        return;
    }

    if (defined('REST_REQUEST')) {
        return;
    }

    if (!headers_sent() && '' == session_id()) {
        session_start();
    }

    if (!defined('DOING_CRON')) {
        session_write_close();
    }
});





function get_dashboard_nav( $menus ) {
    $custom_menus = [
        'reports_data' => [
            'title' => esc_html__( 'Reports Data', 'swap' ),
            'icon'  => '<i class="fas fa-briefcase"></i>',
            'url'   => dokan_get_navigation_url( 'reports-data' ),
            'pos'   => 30,
            'submenu' => [
                'movements' => [
                    'title'      => esc_html__( 'Movements', 'swap' ),
                    'icon'       => '<i class="fas fa-university"></i>',
                    'url'        => dokan_get_navigation_url( 'reports-data/movements' ),
                    'pos'        => 10,
                    'permission' => 'dokan_view_store_settings_menu',
                ],
                'credit' => [
                     'title'      => esc_html__( 'Credit', 'swap' ),
                     'icon'       => '<i class="fas fa-university"></i>',
                     'url'        => dokan_get_navigation_url( 'reports-data/credit' ),
                     'pos'        => 20,
                     'permission' => 'dokan_view_store_settings_menu',
                ],
                'rating' => [
                    'title'      => esc_html__( 'Rating', 'swap' ),
                    'icon'       => '<i class="fas fa-university"></i>',
                    'url'        => dokan_get_navigation_url( 'reports-data/rating' ),
                    'pos'        => 20,
                    'permission' => 'dokan_view_store_settings_menu',
               ],
                'leads' => [
                    'title'      => esc_html__( 'Leads', 'swap' ),
                    'icon'       => '<i class="fas fa-university"></i>',
                    'url'        => dokan_get_navigation_url( 'reports-data/leads' ),
                    'pos'        => 20,
                    'permission' => 'dokan_view_store_settings_menu',
                ],
             ],
        ],
        'rental_history' => [
            'title' => esc_html__( 'Rental History', 'swap' ),
            'icon'  => '<i class="fas fa-briefcase"></i>',
            'url'   => dokan_get_navigation_url( 'rental-history' ),
            'pos'   => 30,
        ],
    ];

    return array_merge( $menus, $custom_menus );
}

add_filter( 'dokan_get_dashboard_nav', 'get_dashboard_nav' );


// Add query var for the custom menu
function add_reports_custom_query_var( $query_vars ) {
    $query_vars[] = 'reports-data';
    $query_vars[] = 'reports-data/movements';
    $query_vars[] = 'reports-data/credit';
    $query_vars[] = 'reports-data/rating';
    $query_vars[] = 'reports-data/leads';
    return $query_vars;
}
add_filter( 'dokan_query_var_filter', 'add_reports_custom_query_var' );




// Load the custom template based on subpage
add_action( 'dokan_load_custom_template', 'dokan_load_template' );
function dokan_load_template( $query_vars ) {
    if ( isset( $query_vars['reports-data'] ) ) {


        // Load the appropriate template based on the subpage
        if ( $query_vars['reports-data'] === 'movements' ) {
            include get_stylesheet_directory() . '/page-templates/dashboard/reports-data/reports-movements.php';
        } elseif ( $query_vars['reports-data'] === 'credit' ) {
            include get_stylesheet_directory() . '/page-templates/dashboard/reports-data/reports-credit.php';
        } elseif ( $query_vars['reports-data'] === 'rating' ) {
            include get_stylesheet_directory() . '/page-templates/dashboard/reports-data/reports-rating.php';
        } elseif ( $query_vars['reports-data'] === 'leads' ) {
            include get_stylesheet_directory() . '/page-templates/dashboard/reports-data/reports-leads.php';
        } else {
            include get_stylesheet_directory() . '/page-templates/dashboard/reports-data/reports.php';
        }

        exit; // Stop further processing
    }
}



// Add query var for the custom menu
function add_rental_custom_query_var( $query_vars ) {
    $query_vars[] = 'rental-history';
    return $query_vars;
}
add_filter( 'dokan_query_var_filter', 'add_rental_custom_query_var' );

// Load the custom template directly
add_action( 'dokan_load_custom_template', 'dokan_load_rental_template' );
function dokan_load_rental_template( $query_vars ) {
    if ( isset( $query_vars['rental-history'] ) ) {
        // Ensure no header/footer wrapping
        include get_stylesheet_directory() . '/page-templates/dashboard/rental-history/history.php';
        exit; // Stop further processing
    }
}





function add_order_query_var( $query_vars ) {
    $query_vars[] = 'order-id';
    return $query_vars;
}
add_filter( 'query_vars', 'add_order_query_var' );











add_action( 'dokan_settings_form_bottom', function( $current_user, $profile_info ) {
    $custom_field_value = isset( $profile_info['custom_store_description'] ) ? $profile_info['custom_store_description'] : '';
    ?>
    <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="custom_store_description"><?php esc_html_e( 'Custom Store Description', 'dokan' ); ?></label>
        <div class="dokan-w5">
            <textarea id="custom_store_description" name="custom_store_description" class="dokan-form-control"><?php echo esc_textarea( $custom_field_value ); ?></textarea>
        </div>
    </div>
    <?php
}, 10, 2 );

add_action( 'dokan_store_header_info_fields', function( $store_user, $store_info ) {
    if ( isset( $store_info['custom_store_description'] ) && ! empty( $store_info['custom_store_description'] ) ) {
        echo '<div class="dokan-store-custom-desc"><strong>' . esc_html__( 'About the Store:', 'dokan' ) . '</strong> ' . esc_html( $store_info['custom_store_description'] ) . '</div>';
    }
}, 10, 2 );


function custom_woocommerce_login_redirect( $redirect, $user ) {
    return home_url();
}
add_filter( 'woocommerce_login_redirect', 'custom_woocommerce_login_redirect', 10, 2 );

















function enqueue_filter_script() {
   // wp_enqueue_script('filter-script', get_template_directory_uri() . '/js/filter.js', array('jquery'), null, true);

    wp_localize_script('filter-script', 'ajax_object', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_filter_script');





add_action('wp_ajax_filter_products', 'filter_products');
add_action('wp_ajax_nopriv_filter_products', 'filter_products');

function filter_products() {
    $color = isset($_POST['color']) ? sanitize_text_field($_POST['color']) : '';
    $brand = isset($_POST['brand']) ? sanitize_text_field($_POST['brand']) : '';
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    $size = isset($_POST['size']) ? sanitize_text_field($_POST['size']) : '';

    $tax_query = array('relation' => 'AND');

    if (!empty($color)) {
        $tax_query[] = array('taxonomy' => 'color', 'field' => 'slug', 'terms' => $color);
    }

    if (!empty($brand)) {
        $tax_query[] = array('taxonomy' => 'product_brand', 'field' => 'slug', 'terms' => $brand);
    }

    if (!empty($category)) {
        $tax_query[] = array('taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $category);
    }

    if (!empty($size)) {
        $tax_query[] = array('taxonomy' => 'size', 'field' => 'slug', 'terms' => $size);
    }

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'tax_query' => $tax_query
    );

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            get_template_part('template-parts/product-item');
        endwhile;
    else :
        echo '<p>לא נמצאו מוצרים תואמים</p>';
    endif;
    wp_reset_postdata();

    echo ob_get_clean();
    die();
}




remove_action('woocommerce_account_navigation', 'woocommerce_account_navigation');

function remove_storefront_woocommerce_style_rtl() {
    wp_dequeue_style('storefront-style');
    wp_deregister_style('storefront-style');
}
add_action('wp_enqueue_scripts', 'remove_storefront_woocommerce_style_rtl', 99);



/**
 * Thumbnail Image Size.
 */	
add_filter( 'woocommerce_get_image_size_thumbnail', 'cristiano_wc_thumbnail_size' );
function cristiano_wc_thumbnail_size( $size ) {
    $size = array(
        'width' => 262,
        'height' => 346,
        'crop' => 1,
    );


	return $size;
}
