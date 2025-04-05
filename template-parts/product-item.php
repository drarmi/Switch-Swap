<?php
$product = wc_get_product(get_the_ID());
$product_id = get_the_ID();
$attribute_data = get_product_variable_attribute($product_id);
$period_variation = $attribute_data["pa_product-type"];

$buy_now = $rent = [];

foreach ($period_variation as $variation) {
    $term_name = strtolower($variation["name"]);
    $taxonomy = strtolower($variation["taxonomy"]);
    if ($term_name === "buy") {
        $buy_now = get_product_variation_data_by_term($product_id, $term_name, $taxonomy);
    }
    if ($term_name === "rent") {
        $rent = get_product_variation_data_by_term($product_id, $term_name, $taxonomy);
    }
}
?>

<div class="swap-product">
    <a class="swap-product__media" href="<?php the_permalink(); ?>">
        <span class="swap-product__tag">חדש</span>
        
        <button class="swap-product__like swap-products__like--active <?php echo !empty($like_product) && in_array(get_the_ID(), $like_product) ? "active" : "" ?> like-favorites-js absolute" data-product-id="<?php echo esc_attr(get_the_ID()); ?>">
            <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.49413 3.27985C7.828 1.332 5.04963 0.808035 2.96208 2.59168C0.87454 4.37532 0.580644 7.35748 2.22 9.467C3.58302 11.2209 7.70798 14.9201 9.05992 16.1174C9.21117 16.2513 9.2868 16.3183 9.37502 16.3446C9.45201 16.3676 9.53625 16.3676 9.61325 16.3446C9.70146 16.3183 9.77709 16.2513 9.92834 16.1174C11.2803 14.9201 15.4052 11.2209 16.7683 9.467C18.4076 7.35748 18.1496 4.35656 16.0262 2.59168C13.9028 0.826798 11.1603 1.332 9.49413 3.27985Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
            </svg>
        </button>
        <?php echo woocommerce_get_product_thumbnail('medium'); ?>
    </a>

    <div class="swap-product__details">
        <h3 class="swap-product__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <div class="swap-product__desc"><?php the_excerpt(); ?></div>

        <?php if (!empty($rent)) : ?>
            <p class="swap-product__rent"><span>השכרה</span> <span>החל מ-</span> <span><?php echo esc_html($rent['price']); ?> ₪ </span></p>
        <?php endif; ?>

        <?php if (!empty($buy_now)) : ?>
            <p class="swap-product__amount">
                <span>קנייה מיידית</span>
                <del><?php echo esc_html($buy_now['price']); ?> ₪</del>
                <ins><?php echo esc_html($buy_now['sale_price']); ?> ₪</ins>
            </p>
        <?php endif; ?>

        <p class="swap-product__proposals-allowed">ניתן להגיש הצעות</p>
    </div>
</div>