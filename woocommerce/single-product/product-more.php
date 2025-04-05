<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$query = $args["query"] ?? [];
$first_name = $args["first_name"] ?? "";

?>
<?php if (!empty($query)) : ?>
    <div class="similar-wrap more">
        <h3><?php esc_html_e("מוצרים נוספים של ", "swap"); ?> <?php echo $first_name; ?> :</h3>

        <div class="swiper mySwipePerViewGap">
            <div class="swiper-wrapper">
                <?php
                $user_logged = is_user_logged_in();
                if ($user_logged) {
                    $user_id = get_current_user_id();
                    $like_product = get_field("like_product", "user_" . $user_id);
                }
                ?>
                <?php foreach ($query as $id) :  ?>
                    <?php
                    $custom_parent_id = get_post_meta($id, '_custom_parent_id', true);
                    $variation_id = get_variation_id($custom_parent_id);

                    $productUpsellsID = wc_get_product($id);
                    $parent_id = $productUpsellsID->is_type('variation') ? $productUpsellsID->get_parent_id() : $id;
                    $parent_product = wc_get_product($parent_id);
                    $postURL = get_permalink($parent_product);
                    $title = $parent_product->get_name();
                    $thumbnail_id = $parent_product->get_image_id();
                    $image_url = $thumbnail_id ? wp_get_attachment_image_src($thumbnail_id, 'large') : "";
                    $description = $parent_product->get_short_description();
                    $product_tags = wp_get_post_terms($parent_id, 'product_tag', array('fields' => 'names'));
                    ?>
                    <div class="swiper-slide">
                        <?php
                        $args = [
                            "hot" => in_array("hot", $product_tags),
                            "title" => $title ?? "",
                            "img" => $image_url ? reset($image_url) : "",
                            "description" => $description ?? "",
                            "parent_id" => $parent_id ?? 0,
                            "user_logged" => $user_logged ?? false,
                            "like_product" => $like_product ?? false,
                            "postURL" => $postURL ?? "",
                            "variation_id" => $variation_id ?? [],
                        ];
                        get_template_part("woocommerce/single-product/product-similar-slide", null, $args);
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>