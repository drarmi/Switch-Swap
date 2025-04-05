<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$product_id = $args["ID"] ?? "";
$gallery_images = get_product_gallery_images_with_sizes($product_id);
?>

<div class="slide-wrap">
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <?php foreach ($gallery_images as $image): ?>
            <div class="swiper-slide">
                <img src="<?php echo esc_url($image["large"]); ?>" alt="image">
            </div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
    </div>
</div>