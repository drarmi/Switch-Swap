<?php
/* Template Name: Favorites */

get_header('profile');

use Omnis\src\inc\classes\my_favorites\My_Favorites;
$like_product = My_Favorites::get_user_like_product();

?>
<h1 class="profile-page-title like previousPage-js">מועדפים</h1>
<?php if (!empty($like_product)): ?>
    <ul class="favorite-products">
        <?php foreach ($like_product as $product_id): ?>
            <?php
            $product = wc_get_product($product_id);
            if (!$product) {
                break;
            }
            $vendor_id = get_post_field('post_author', $product_id);
            $vendor = get_userdata($vendor_id);
            $vendor_name = $vendor ? $vendor->display_name : '';
            $profile_settings = get_user_meta($vendor_id, 'dokan_profile_settings', true);
            $gravatar = !empty($profile_settings['gravatar']) ? esc_url($profile_settings['gravatar']) : get_avatar_url($vendor_id, ['size' => 100]);
            $title = $product->get_name();
            $description = $product->get_description();
            $thumbnail_url = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'medium')[0] ?? '';
            $add_to_cart_url = esc_url($product->add_to_cart_url());
            $in_stock = $product->is_in_stock();
            $product_url = $product ? get_permalink($product->get_id()) : '';

            ?>
            <li class="favorite-product <?php !$in_stock ? "out-of-stock" : "" ?>" data-product-id="<?php echo esc_attr($product_id); ?>">
                <?php if(!$in_stock): ?>
                    <div class="favorite-product__out-of-stock">
                        <?php esc_html_e("הפריט לא זמין") ?>
                    </div>
                <?php endif; ?>
                <img src="<?php echo esc_url($thumbnail_url); ?>" alt="Product Alt" class="favorite-product__image">
                <div class="favorite-product__details">
                    <a href="<?php echo esc_url($product_url) ?>" class="favorite-product__owner">
                        <?php if(!empty($gravatar)): ?>
                            <img src="<?php echo esc_url($gravatar); ?>" width="20" height="20" alt="" class="favorite-product__owner-image">
                        <?php endif; ?>
                        <h2 class="favorite-product__owner-name"><?php echo esc_html($vendor_name); ?></h2>
                    </a>
                    <h3 class="favorite-product__name"><?php echo esc_html($title); ?></h3>
                    <div class="favorite-product__description">
                        <?php echo wp_kses_post($description); ?>
                    </div>
                    <a href="<?php echo esc_url($add_to_cart_url) ?>" class="favorite-product__add-to-cart"><?php esc_html_e("הוספה לעגלה") ?></a>
                </div>
                <div>
                    <button type="button" class="icon-like--active like-favorites-js favorites-page-js" data-product-id="<?php echo esc_attr($product_id); ?>">
                        <svg width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.80638 3.20659C3.70651 2.30673 4.92719 1.80122 6.19998 1.80122C7.47276 1.80122 8.69344 2.30673 9.59357 3.20659L11 4.61179L12.4064 3.20659C12.8492 2.74815 13.3788 2.38247 13.9644 2.13091C14.5501 1.87934 15.1799 1.74693 15.8172 1.74139C16.4546 1.73585 17.0866 1.8573 17.6766 2.09865C18.2665 2.34 18.8024 2.69641 19.2531 3.1471C19.7038 3.59778 20.0602 4.13371 20.3015 4.72361C20.5429 5.31352 20.6643 5.94558 20.6588 6.58292C20.6532 7.22026 20.5208 7.85012 20.2693 8.43574C20.0177 9.02136 19.652 9.55101 19.1936 9.99379L11 18.1886L2.80638 9.99379C1.90651 9.09366 1.401 7.87298 1.401 6.60019C1.401 5.32741 1.90651 4.10673 2.80638 3.20659Z" fill="#111111" stroke="#111111" stroke-width="2" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php
get_footer('swap');
