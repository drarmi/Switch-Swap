<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$hot = $args["hot"] ?? false;
$title = $args["title"] ?? "";
$img = $args["img"] ?? "";
$description = $args["description"] ?? "";
$postURL = $args["description"] ?? "";
$rent = $args["rent"] ?? null;
$bay_now = $args["bay_now"] ?? null;
$bay_was = $args["bay_was"] ?? null;
$offers = $args["offers"] ?? false;
$parent_id = $args["parent_id"] ?? null;
$views = get_field('views_count_total', "option") ?? [];
$user_logged = $args["user_logged"] ?? false;
$like_product = $args["like_product"] ?? [];
$variation_id = $args["variation_id"] ?? [];

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
<div class="similar-slide-wrap">
    <div class="similar-img">
        <?php if ($user_logged): ?>
            <div class="like like-favorites-js absolute nav-icon <?php echo !empty($like_product) && in_array($parent_id, $like_product) ? "active" : "" ?>"
                data-product-id="<?php echo esc_attr("$parent_id"); ?>">
                <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M9.99413 3.27985C8.328 1.332 5.54963 0.808035 3.46208 2.59168C1.37454 4.37532 1.08064 7.35748 2.72 9.467C4.08302 11.2209 8.20798 14.9201 9.55992 16.1174C9.71117 16.2513 9.7868 16.3183 9.87502 16.3446C9.95201 16.3676 10.0363 16.3676 10.1132 16.3446C10.2015 16.3183 10.2771 16.2513 10.4283 16.1174C11.7803 14.9201 15.9052 11.2209 17.2683 9.467C18.9076 7.35748 18.6496 4.35656 16.5262 2.59168C14.4028 0.826798 11.6603 1.332 9.99413 3.27985Z"
                        stroke="#111111" stroke-width="1.5" stroke-linejoin="round" />
                </svg>
            </div>
        <?php endif; ?>

        <?php if (!empty($views)): ?>
            <?php if (in_array($parent_id, $views)): ?>
                <div class="hot-wrap">
                    <?php esc_attr_e("פריט חם", "swap") ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <img src="<?php echo esc_url($img); ?>" alt="product image">
    </div>

    <a href="<?php echo esc_url($postURL) ?>" class="similar-bottom">
        <h4><?php echo esc_html($title); ?></h4>

        <div class="similar-content">
            <?php echo wp_kses_post($description); ?>
        </div>

        <?php if($rentID) : ?>
            <div class="similar-rent">
                <?php esc_html_e(" השכרה החל מ", "omnis_base"); ?> <?php echo $one_day ?? ""; ?> ₪
            </div>
        <?php endif; ?>

        <?php if($simpleID): ?>
            <div class="similar-bay">
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

        <div class="similar-offers">
            <?php if (!empty($bidID[0])): ?>
                <?php esc_html_e("ניתן להגיש הצעות", "omnis_base") ?>
            <?php endif; ?>
        </div>
    </a>

</div>