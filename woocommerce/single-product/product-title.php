<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
use Omnis\src\inc\classes\pages\Product_Management;

$product_id = $args["ID"] ?? "";

$title = get_the_title($product_id);
$quality =  get_post_meta($product_id, '_quality', true) ?? "";
$short_description = get_the_excerpt($product_id);

$product_tags = wp_get_post_terms($product_id, 'product_tag', array('fields' => 'names'));
$hot = get_field('buy_count_total', "option") ?? [];

$termsColor = wp_get_object_terms($product_id, 'color', ['fields' => 'all']);
$termsSize = wp_get_object_terms($product_id, 'size', ['fields' => 'all']);

$size_chart = get_field( 'size_chart', 'option' );

$returnStatus = returnStatus($product_id);


if (!empty($termsColor) && !is_wp_error($termsColor)) {
    foreach ($termsColor as $term) {
        $color_title = $term->name;
        $color_hex = get_field("color", "term_" . $term->term_id);
    }
}

if (!empty($termsSize) && !is_wp_error($termsSize)) {
    foreach ($termsSize as $term) {
        $size = $term->name;
    }
}
?>

<div class="title-wrap">
    <?php echo Product_Management::getProductStatusHTML($product_id); ?>
    <div class="hot-wrap">
        <?php if (in_array($product_id, $hot)): ?>
            <div class="hot">
                <?php esc_attr_e("פריט חם", "swap") ?>
            </div>
        <?php endif; ?>
    </div>
    <h2><?php echo esc_html($title) ?></h2>
    <p><?php echo esc_html($short_description) ?></p>
    <?php if ($quality): ?>
        <div class="quality-wrap">
            <span class="quality">
                <?php echo esc_html($quality) ?>
            </span>
        </div>
    <?php endif; ?>
    
    <div class="size-info-wrap">
        <div class="size-info">
            <a href="<?php the_permalink( $size_chart ) ?>" target="_blank"><?php echo esc_attr_e("דואג/ת לגבי המידה?", "swap") ?></a>
            <span>
                <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M8.00016 4.83398V7.50065M8.00016 10.1673H8.00683M14.6668 7.50065C14.6668 11.1826 11.6821 14.1673 8.00016 14.1673C4.31826 14.1673 1.3335 11.1826 1.3335 7.50065C1.3335 3.81875 4.31826 0.833984 8.00016 0.833984C11.6821 0.833984 14.6668 3.81875 14.6668 7.50065Z"
                        stroke="#595959" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
        </div>
    </div>

    <div class="product-start-info">
        <?php if ($size): ?>
            <div class="size">
                <span class="text"><?php esc_attr_e("מידה", "swap") ?></span>
                <span class="size-box">
                    <?php echo esc_html($size ?? "") ?>
                </span>
            </div>
        <?php endif; ?>
        <?php if ($color_title && $color_hex): ?>
            <div class="color">
                <span class="text"><?php esc_attr_e("צבע", "swap") ?></span>
                <span>
                    <span class="color-pick"
                        style="display:inline-block; background-color: <?php echo esc_attr($color_hex ?? "") ?>;"></span>
                    <span class="color-title"><?php echo esc_html($color_title ?? "") ?></span>
                </span>
            </div>
        <?php endif; ?>
    </div>
</div>