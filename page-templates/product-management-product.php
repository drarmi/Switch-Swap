<?php



if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/* Template Name:  Product management product*/

if (!is_user_logged_in() || empty($_GET["management-productID"])) {
    wp_redirect(home_url());
    exit;
}

get_header('profile');

$productID = (int)sanitize_text_field($_GET["management-productID"]);

?>
<section class="top-title">
    <a href="<?php echo esc_url(home_url("management")); ?>">
        <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 13L7 7L1 1" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </a>

    <h1><?php echo esc_html(get_the_title($productID)) ?></h1>

    <div class="deleted-product-header deleted-product-modal-header-js">
        <svg width="18" height="4" viewBox="0 0 18 4" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 2C10 1.44771 9.55228 1 9 1C8.44772 1 8 1.44771 8 2C8 2.55228 8.44772 3 9 3C9.55228 3 10 2.55228 10 2Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M3 2C3 1.44772 2.55228 1 2 1C1.44772 1 1 1.44772 1 2C1 2.55228 1.44772 3 2 3C2.55228 3 3 2.55228 3 2Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M17 2C17 1.44771 16.5523 0.999999 16 0.999999C15.4477 0.999999 15 1.44771 15 2C15 2.55228 15.4477 3 16 3C16.5523 3 17 2.55228 17 2Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>

        <div class="deleted-product-modal-header">
            <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.3333 4.99996V4.33329C12.3333 3.39987 12.3333 2.93316 12.1517 2.57664C11.9919 2.26304 11.7369 2.00807 11.4233 1.84828C11.0668 1.66663 10.6001 1.66663 9.66667 1.66663H8.33333C7.39991 1.66663 6.9332 1.66663 6.57668 1.84828C6.26308 2.00807 6.00811 2.26304 5.84832 2.57664C5.66667 2.93316 5.66667 3.39987 5.66667 4.33329V4.99996M7.33333 9.58329V13.75M10.6667 9.58329V13.75M1.5 4.99996H16.5M14.8333 4.99996V14.3333C14.8333 15.7334 14.8333 16.4335 14.5608 16.9683C14.3212 17.4387 13.9387 17.8211 13.4683 18.0608C12.9335 18.3333 12.2335 18.3333 10.8333 18.3333H7.16667C5.76654 18.3333 5.06647 18.3333 4.53169 18.0608C4.06129 17.8211 3.67883 17.4387 3.43915 16.9683C3.16667 16.4335 3.16667 15.7334 3.16667 14.3333V4.99996" stroke="#E22400" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>

            <?php esc_html_e("מחיקת פריט", "swap"); ?>
        </div>
    </div>
</section>

<form id="management-product-main-form" action="" method="post">
    <section class="management-gallery">
        <?php get_template_part("src/template-parts/product-management/management-gallery"); ?>
    </section>

    <section class="management-drop-down">
        <?php get_template_part("src/template-parts/product-management/drop-down"); ?>
    </section>

    <section class="management-delivery">
        <?php get_template_part("src/template-parts/product-management/management-delivery"); ?>
    </section>

    <section class="management-price">
        <?php get_template_part("src/template-parts/product-management/management-price"); ?>
    </section>

    <section class="management-comments"
        <?php get_template_part("src/template-parts/product-management/product-comment", null); ?>
    </section>
    <?php get_template_part("src/template-parts/product-management/management-deleted-modal", null); ?>
    <button id="test-form-btn" type="submit"> test form </button>
</form>

<?php get_footer('profile'); ?>