<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


$post_id = $args["ID"] ?? "";
$first_name = $args["first_name"] ?? "";
$last_name = $args["last_name"] ?? "";
$author_logo = $args["author_logo"] ?? "";
$product_count = $args["product_count"] ?? "";
$user_star = $args["user_star"] ?? "";
$rating_title = $args["rating_title"] ?? "";
?>
<div class="author-wrap">
    <h3><?php esc_attr_e("על החנות", "omnis_base") ?></h3>
    <div class="author-section">
        <a href="#">
            <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15 18.5L9 12.5L15 6.5" stroke="#111111" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </a>
        <div class="author-info">
            <div class="author-logo">
                <img src="<?php echo esc_url($author_logo) ?>" alt="avatar">
            </div>
            <div>
                <div class="name"><?php echo esc_html($first_name); ?></div>
            </div>
        </div>
        <div class="rating">
            <div>
                <div class="num"><?php echo esc_html($user_star); ?></div>
                <div class="star">
                    <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M8 0.5L9.79611 6.02786H15.6085L10.9062 9.44427L12.7023 14.9721L8 11.5557L3.29772 14.9721L5.09383 9.44427L0.391548 6.02786H6.20389L8 0.5Z"
                            fill="#111111" />
                    </svg>
                </div>
            </div>
            <div class="rating-title">
                <?php echo esc_html($rating_title); ?>
            </div>
            <div>
                <span class="comment-count"><?php echo esc_html($product_count); ?></span>
                <span><?php esc_attr_e("ביקורות") ?></span>
            </div>
        </div>
    </div>
</div>