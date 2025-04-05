<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Omnis\src\inc\classes\pages\Product_Management;

$productDetails = $args["productDetails"];
$productID = $productDetails["ID"] ?? "";
$title = $productDetails["post_title"] ?? "";
$description = $productDetails["description"] ?? "";
$postURL  = $productDetails["url"] ?? "";
$img = $productDetails["thumbnail"] ?? "";
$variation_id = Product_Management::get_variation_id((int)$productID);


$bidID = $variation_id["bidID"] ?? "";
$rentID = $variation_id["rentID"] ?? "";
$simpleID = $variation_id["simpleID"] ?? "";

if($simpleID){
    $regular_price = get_post_meta($simpleID, '_regular_price');
    $sale_price = get_post_meta($simpleID, '_sale_price');
}
if($rentID){
    $one_day = get_post_meta($rentID, '_wc_booking_block_cost')[0] ?? "";
}
?>

<?php if(!$bidID && !$rentID && !$simpleID): ?>
    <?php echo ""; ?>
<?php else: ?>
<div class="small-product-list-card">
    <div class="card-img">
        <a href="<?php echo esc_url(home_url("management-product?management-productID=").$productID) ?>" class="edit-product">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.31128 14.1984C1.34932 13.8561 1.36833 13.685 1.42012 13.525C1.46606 13.3831 1.53098 13.2481 1.6131 13.1235C1.70566 12.9832 1.82742 12.8614 2.07094 12.6179L13.0031 1.68577C13.9174 0.77141 15.3999 0.771411 16.3143 1.68577C17.2286 2.60013 17.2286 4.0826 16.3142 4.99696L5.38213 15.9291C5.1386 16.1726 5.01684 16.2943 4.87648 16.3869C4.75194 16.469 4.61688 16.5339 4.47496 16.5799C4.315 16.6317 4.14385 16.6507 3.80157 16.6887L1 17L1.31128 14.1984Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
            </svg>
        </a>
        <?php if(!empty($img)): ?>
            <img src="<?php echo esc_url($img); ?>" alt="product image">
        <?php endif; ?>
    </div>

    <a href="<?php echo esc_url($postURL) ?>" class="card-bottom">
        <?php echo Product_Management::getProductStatusHTML($productID); ?>
        <h4><?php echo esc_html($title); ?></h4>

        <div class="card-content">
            <?php echo wp_kses_post($description); ?>
        </div>

        <?php if($rentID) : ?>
            <div class="card-rent">
                <?php esc_html_e(" השכרה החל מ", "omnis_base"); ?> <?php echo $one_day ?? ""; ?> ₪
            </div>
        <?php endif; ?>

        <?php if($simpleID): ?>
            <div class="card-bay">
                <?php esc_html_e("לקנייה מיידית", "omnis_base"); ?>

                <?php if ($regular_price): ?>
                    <div class="was <?php echo !empty($sale_price[0]) ? "sale" : ""; ?>">
                        <?php echo $regular_price[0] ?? ""; ?> ₪
                    </div>
                <?php endif; ?>

                <?php if ($sale_price): ?>
                    <div class="now">
                        <?php echo $sale_price[0] ?? ""; ?> ₪
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="card-offers">
            <?php if (!empty($bidID[0])): ?>
                <?php esc_html_e("ניתן להגיש הצעות", "omnis_base") ?>
            <?php endif; ?>
        </div>
    </a>
</div>
<?php endif; ?>
