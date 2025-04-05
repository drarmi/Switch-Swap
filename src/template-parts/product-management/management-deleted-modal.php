<?php
    if (! defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }

    $product_id = (int)sanitize_text_field($_GET["management-productID"]);
?>

<?php if($product_id): ?>
<div class="deleted-product-modal-wrapper">
    <div class="deleted-product-modal">
        <button class="confirm confirm-deleted-product-js">
            <?php esc_html_e("מחיקת הפריט", "swap") ?>
            <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.3333 4.99996V4.33329C12.3333 3.39987 12.3333 2.93316 12.1517 2.57664C11.9919 2.26304 11.7369 2.00807 11.4233 1.84828C11.0668 1.66663 10.6001 1.66663 9.66667 1.66663H8.33333C7.39991 1.66663 6.9332 1.66663 6.57668 1.84828C6.26308 2.00807 6.00811 2.26304 5.84832 2.57664C5.66667 2.93316 5.66667 3.39987 5.66667 4.33329V4.99996M7.33333 9.58329V13.75M10.6667 9.58329V13.75M1.5 4.99996H16.5M14.8333 4.99996V14.3333C14.8333 15.7334 14.8333 16.4335 14.5608 16.9683C14.3212 17.4387 13.9387 17.8211 13.4683 18.0608C12.9335 18.3333 12.2335 18.3333 10.8333 18.3333H7.16667C5.76654 18.3333 5.06647 18.3333 4.53169 18.0608C4.06129 17.8211 3.67883 17.4387 3.43915 16.9683C3.16667 16.4335 3.16667 15.7334 3.16667 14.3333V4.99996" stroke="#E22400" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <button class="black close close-sub-modal-deleted-product-js">
            <?php esc_html_e("ביטול", "swap") ?>
        </button>
    </div>

    <div class="deleted-product-modal-confirm">
        <div class="close-must-log-in-modal close-deleted-sub-modal-js">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18M6 6L18 18" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </div>

        <svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.99984 16.5L13.9998 20.5L21.9998 12.5M29.3332 16.5C29.3332 23.8638 23.3636 29.8333 15.9998 29.8333C8.63604 29.8333 2.6665 23.8638 2.6665 16.5C2.6665 9.13616 8.63604 3.16663 15.9998 3.16663C23.3636 3.16663 29.3332 9.13616 29.3332 16.5Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>

        <h2><?php esc_html_e("מחיקת הפריט", "swap") ?></h2>

        <img src="<?php echo esc_url(get_the_post_thumbnail_url($product_id, "small")) ?>" alt="<?php echo esc_attr(get_the_title($product_id)) ?>">

        <h4><?php echo esc_html(get_the_title($product_id)) ?></h4>

        <div class="nav-list">
            <div>
                <button class="black submit-deleted-product-js" data-id="<?php echo $product_id; ?>"><?php esc_html_e("אישור", "swap") ?></button>
            </div>

            <div>
                <button class="transparent close-deleted-sub-modal-js"><?php esc_html_e("ביטול", "swap") ?></button>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
    <?php echo esc_html__("No product ID provided", "swap"); ?>
<?php endif; ?>

