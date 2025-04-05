<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $wpdb;
$product_id = $args["ID"] ?? "";
$author_id = $args["author_id"] ?? "";
$custom_parent_id = get_post_meta($product_id, '_custom_parent_id', true);
$variation_id = get_variation_id($custom_parent_id);

$bidID = $variation_id["bidID"] ?? "";
$rentID = $variation_id["rentID"] ?? "";
$simpleID = $variation_id["simpleID"] ?? "";

?>
<div class="bay-wrap">
    <?php

    if (!empty($simpleID)) {
        get_template_part("woocommerce/single-product/product", "buy-now", ['ID' => $simpleID]);
    }
    
    if (!empty($rentID)) {
        get_template_part("woocommerce/single-product/product", "buy-rent", ["ID" => $rentID]);
    }


    if ($bidID) {
        get_template_part("woocommerce/single-product/product", "buy-bid", ["ID" => $bidID, "author_id" => $author_id]);
    }
    ?>
</div>