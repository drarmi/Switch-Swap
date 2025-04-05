<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Omnis\src\inc\classes\pages\Product_Management;


$product_id = (int)sanitize_text_field($_GET["management-productID"]);
$author_id = get_post_field('post_author', $product_id);

$user_all_comments_data = Product_Management::getUserCommentsData($author_id);
$relatedProducts = Product_Management::getRelatedProducts($product_id);
$args_comments = array(
    'post__in' => $relatedProducts,
    'status'   => 'approve',
);

$comments = get_comments($args_comments);
$comment_count = $user_all_comments_data['total_reviews'] ?? 0;

$user_star_arr = [
    5 => $user_all_comments_data['rating_5'] ?? 0,
    4 => $user_all_comments_data['rating_4'] ?? 0,
    3 => $user_all_comments_data['rating_3'] ?? 0,
    2 => $user_all_comments_data['rating_2'] ?? 0,
    1 => $user_all_comments_data['rating_1'] ?? 0,
];

$avg_rating = !empty($user_all_comments_data['avg_rating']) && $user_all_comments_data['avg_rating'] > 0 ? round($user_all_comments_data['avg_rating'], 1) : 0;

?>

<?php if (!empty($comments)): ?>
    <h3><?php esc_attr_e("ביקורות ודירוגים", "swap") ?></h3>
    <?php if ($avg_rating) : ?>
        <div class="top-star">
            <span> <?php echo esc_html($avg_rating); ?></span>
            <span>
                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.90205 1.19812C8.09412 0.809001 8.19015 0.614443 8.32053 0.552281C8.43396 0.498198 8.56574 0.498198 8.67918 0.552281C8.80955 0.614443 8.90558 0.809001 9.09766 1.19812L10.9199 4.88976C10.9766 5.00463 11.0049 5.06207 11.0464 5.10667C11.0831 5.14615 11.1271 5.17814 11.1759 5.20087C11.2311 5.22654 11.2945 5.2358 11.4213 5.25433L15.4973 5.85011C15.9266 5.91285 16.1412 5.94421 16.2405 6.04905C16.3269 6.14026 16.3675 6.2656 16.3511 6.39017C16.3322 6.53334 16.1768 6.68467 15.8661 6.98734L12.9177 9.85903C12.8258 9.94855 12.7798 9.99331 12.7502 10.0466C12.7239 10.0937 12.7071 10.1455 12.7006 10.1991C12.6933 10.2596 12.7041 10.3228 12.7258 10.4493L13.4215 14.5054C13.4948 14.9333 13.5315 15.1472 13.4626 15.2741C13.4026 15.3845 13.296 15.462 13.1724 15.4849C13.0304 15.5112 12.8383 15.4102 12.4541 15.2082L8.81015 13.2919C8.69662 13.2322 8.63985 13.2023 8.58004 13.1906C8.52709 13.1802 8.47262 13.1802 8.41966 13.1906C8.35986 13.2023 8.30309 13.2322 8.18955 13.2919L4.54561 15.2082C4.16143 15.4102 3.96935 15.5112 3.82731 15.4849C3.70374 15.462 3.59711 15.3845 3.53712 15.2741C3.46817 15.1472 3.50486 14.9333 3.57823 14.5054L4.27391 10.4493C4.2956 10.3228 4.30644 10.2596 4.2991 10.1991C4.29261 10.1455 4.27576 10.0937 4.24951 10.0466C4.21986 9.99331 4.1739 9.94855 4.08199 9.85903L1.13364 6.98734C0.82289 6.68467 0.667516 6.53334 0.648609 6.39017C0.632159 6.2656 0.672799 6.14026 0.759213 6.04905C0.858533 5.94421 1.07315 5.91285 1.50237 5.85011L5.57843 5.25433C5.70519 5.2358 5.76857 5.22654 5.82377 5.20087C5.87264 5.17814 5.91664 5.14615 5.95333 5.10667C5.99476 5.06207 6.02311 5.00463 6.07982 4.88976L7.90205 1.19812Z" fill="#111111" />
                </svg>
            </span>
        </div>

        <div class="comment-count-wrap">
            <span> <?php esc_html_e(" ביקורות שנכתבו") ?></span>
            <span> <?php echo esc_html($comment_count); ?></span>
            <span> <?php esc_html_e(" מבוסס על. ") ?></span>
        </div>

        <div class="rating-details">
            <?php foreach ($user_star_arr as $key => $value): ?>
                <div class="rating-details-row">
                    <div class="start-row">
                        <?php if ($key) {
                            for ($i = 0; $i < (int)$key; $i++) { ?>
                                <span>
                                    <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M8 0.5L9.79611 6.02786H15.6085L10.9062 9.44427L12.7023 14.9721L8 11.5557L3.29772 14.9721L5.09383 9.44427L0.391548 6.02786H6.20389L8 0.5Z"
                                            fill="#111111" />
                                    </svg>
                                </span>
                            <?php }

                            for ($i = $key; $i < 5; $i++) { ?>
                                <span>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M7.52181 2.30205C7.67547 1.99076 7.75229 1.83511 7.85659 1.78538C7.94734 1.74211 8.05277 1.74211 8.14351 1.78538C8.24781 1.83511 8.32464 1.99076 8.4783 2.30205L9.93608 5.25536C9.98144 5.34726 10.0041 5.39321 10.0373 5.42889C10.0666 5.46048 10.1018 5.48607 10.1409 5.50425C10.1851 5.52479 10.2358 5.5322 10.3372 5.54702L13.598 6.02364C13.9414 6.07383 14.1131 6.09893 14.1926 6.1828C14.2617 6.25577 14.2942 6.35604 14.281 6.45569C14.2659 6.57022 14.1416 6.69129 13.893 6.93343L11.5343 9.23078C11.4608 9.3024 11.424 9.33821 11.4003 9.38081C11.3793 9.41854 11.3658 9.45998 11.3607 9.50284C11.3548 9.55125 11.3635 9.60183 11.3808 9.703L11.9373 12.9479C11.996 13.2902 12.0254 13.4613 11.9702 13.5628C11.9222 13.6512 11.8369 13.7132 11.7381 13.7315C11.6245 13.7526 11.4708 13.6717 11.1634 13.5101L8.24829 11.9771C8.15746 11.9293 8.11205 11.9054 8.0642 11.896C8.02184 11.8877 7.97827 11.8877 7.9359 11.896C7.88806 11.9054 7.84264 11.9293 7.75181 11.9771L4.83666 13.5101C4.52932 13.6717 4.37565 13.7526 4.26202 13.7315C4.16316 13.7132 4.07786 13.6512 4.02987 13.5628C3.97471 13.4613 4.00406 13.2902 4.06276 12.9479L4.6193 9.703C4.63665 9.60183 4.64532 9.55125 4.63945 9.50284C4.63426 9.45998 4.62078 9.41854 4.59978 9.38081C4.57606 9.33821 4.53929 9.3024 4.46576 9.23078L2.10708 6.93342C1.85848 6.69129 1.73418 6.57022 1.71906 6.45569C1.7059 6.35604 1.73841 6.25577 1.80754 6.1828C1.887 6.09893 2.05869 6.07383 2.40207 6.02364L5.66291 5.54702C5.76432 5.5322 5.81503 5.52479 5.85919 5.50425C5.89828 5.48607 5.93348 5.46048 5.96283 5.42889C5.99598 5.39321 6.01866 5.34726 6.06402 5.25536L7.52181 2.30205Z"
                                            fill="#D9D9D9" />
                                    </svg>

                                </span>
                        <?php }
                        } ?>
                    </div>
                    <div class="line"></div>
                    <div><?php echo esc_html($value ?: 0); ?></div>
                </div>

            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php foreach ($comments as $key => $comment): ?>
        <div class="single-comment">
            <?php
            $comment_ID = $comment->comment_ID;
            $author_id = $comment->user_id;
            $user_info = get_userdata($author_id);
            if(!$user_info){
                continue;
            }
            $first_name = $user_info->first_name ?? "";
            $author_logo = get_user_meta($author_id, 'user_logo', true) ?? "";
            $img_set = get_field("comment_image", 'comment_' . $comment_ID) ?? [];
            $size = get_field("size", 'comment_' . $comment_ID) ?? "";
            $star = get_comment_meta($comment_ID, 'rating', true);

            if (!$author_logo) {
                $author_logo = get_avatar_url($author_id);
            }

            $args = [
                "logo" => $author_logo,
                "name" => $first_name,
                "date" => date('d/m/Y', strtotime($comment->comment_date)),
                "star" => $star,
                "size" => $size,
                "comment" => $comment->comment_content,
                "prod_img_set" => $img_set,
            ];
            get_template_part("woocommerce/single-product/product-comment-slide", null, $args);
            ?>

        </div>
    <?php endforeach; ?>
<?php endif; ?>