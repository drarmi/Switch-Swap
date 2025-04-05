<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$user_id = get_current_user_id();

if(!$user_id){
    return;
}

$productsAll = Omnis\src\inc\classes\pages\Product_Management::get_all_user_products($user_id);
$productsPart = Omnis\src\inc\classes\pages\Product_Management::get_user_products_with_details_per_page($user_id, 1, 10);
?>

<div class="product-count">
    <span class="count-js"><?php echo !empty($productsAll) ? count($productsAll) : "0" ?></span> <span><?php esc_html_e(" פריטים", "swap"); ?></span>
</div>

<div class="user-list-product">
    <?php if(!empty($productsPart)): ?>
        <?php foreach($productsPart as $productDetails): ?>
            <?php get_template_part("src/template-parts/product-management/product-list-card", null, ["productDetails" => $productDetails]) ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
