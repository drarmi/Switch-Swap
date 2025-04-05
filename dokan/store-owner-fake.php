<?php
use Omnis\src\inc\classes\dokan_enhancer\Dokan_Enhancer;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$store_user   = dokan()->vendor->get(get_query_var('author'));
$store_info   = $store_user->get_shop_info();
$map_location = $store_user->get_location();
$layout       = get_theme_mod('store_layout', 'left');
$vendor_id = $store_user->get_id();
$orders_count_data = dokan_count_orders( $vendor_id );
get_header('profile');

// -------------------------------------
// Подготовка некоторых данных вендора
// -------------------------------------

// 1) Количество опубликованных товаров
$published_products = $store_user->get_published_products();
$published_count    = count($published_products);

// 2) Рейтинг и кол-во отзывов (по API Dokan)
$rating_data  = $store_user->get_rating();
$average_rate = isset($rating_data['rating']) ? floatval($rating_data['rating']) : 0;
$reviews_num  = isset($rating_data['count']) ? intval($rating_data['count']) : 0;

// 3) Получим последние (по дате) 4 товара типа "auction"
$args_last_products  = array(
    'post_type' => 'product',
    'post_status' => 'publish',
    'author' => $store_user->get_id(),
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => 4,
    'tax_query' => array(
        array(
            'taxonomy' => 'product_type',
            'field' => 'slug',
            'terms' => array('auction'), // выбираем товары типа "auction"
        ),
    ),
);
$query_last_products = new WP_Query($args_last_products);

// 4) Получим последние 3 комментария (отзывы) на товары этого вендора
//    Считаем, что нужно выводить только "product" comments.
$args_reviews    = array(
    'post_type' => 'product',
    'post__in' => $published_products,  // отзывы только по товарам вендора
    'status' => 'approve',
    'number' => 3,                    // сколько отзывов показывать
    'orderby' => 'comment_date_gmt',
    'order' => 'DESC',
);
$vendor_comments = get_comments($args_reviews);
?>
<section class="profile-store__board">
    <div class="profile-store__figure">
        <img src="<?php echo esc_url($store_user->get_avatar()); ?>" width="88" height="88"
            alt="<?php echo esc_attr($store_user->get_shop_name()); ?>" class="profile-store__image">
        <button type="button" class="profile-store__image-edit">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M1.31128 14.1984C1.34932 13.8561 1.36833 13.685 1.42012 13.525C1.46606 13.3831 1.53098 13.2481 1.6131 13.1235C1.70566 12.9832 1.82742 12.8614 2.07094 12.6179L13.0031 1.68577C13.9174 0.77141 15.3999 0.771411 16.3143 1.68577C17.2286 2.60013 17.2286 4.0826 16.3142 4.99696L5.38213 15.9291C5.1386 16.1726 5.01684 16.2943 4.87648 16.3869C4.75194 16.469 4.61688 16.5339 4.47496 16.5799C4.315 16.6317 4.14385 16.6507 3.80157 16.6887L1 17L1.31128 14.1984Z"
                    stroke="#111111" stroke-width="1.5" stroke-linejoin="round" />
            </svg>
        </button>
    </div>
    <div class="profile-store__heading store__heading--verified">
        <h1 class="profile-store__name"><?php echo esc_attr($store_user->get_shop_name()); ?></h1>
        <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M9.81773 2.11194C11.0456 0.962687 12.9544 0.962687 14.1823 2.11194L15.2456 3.1071C15.5453 3.38759 15.9366 3.54969 16.3469 3.56326L17.8024 3.61142C19.4833 3.66704 20.833 5.01673 20.8886 6.69763L20.9367 8.15319C20.9503 8.5634 21.1124 8.95475 21.3929 9.25445L22.3881 10.3177C23.5373 11.5456 23.5373 13.4544 22.3881 14.6823L21.3929 15.7456C21.1124 16.0453 20.9503 16.4366 20.9367 16.8469L20.8886 18.3024C20.833 19.9833 19.4833 21.333 17.8024 21.3886L16.3468 21.4367C15.9366 21.4503 15.5453 21.6124 15.2456 21.8929L14.1823 22.8881C12.9544 24.0373 11.0456 24.0373 9.81773 22.8881L8.75445 21.8929C8.45475 21.6124 8.0634 21.4503 7.65319 21.4367L6.19763 21.3886C4.51673 21.333 3.16704 19.9833 3.11142 18.3024L3.06326 16.8469C3.04969 16.4366 2.88759 16.0453 2.6071 15.7456L1.61194 14.6823C0.462687 13.4544 0.462687 11.5456 1.61194 10.3177L2.6071 9.25445C2.88759 8.95475 3.04969 8.5634 3.06326 8.15319L3.11142 6.69763C3.16704 5.01673 4.51673 3.66704 6.19763 3.61142L7.65319 3.56326C8.0634 3.54969 8.45475 3.38759 8.75445 3.1071L9.81773 2.11194ZM16.6725 10.0126C16.9556 9.71019 16.94 9.23555 16.6376 8.9525C16.3352 8.66941 15.8606 8.68505 15.5775 8.98745L10.508 14.4026L8.4225 12.175C8.13945 11.8726 7.66481 11.8569 7.36245 12.14C7.06005 12.4231 7.04441 12.8977 7.3275 13.2001L9.96045 16.0126C10.1023 16.1641 10.3005 16.25 10.508 16.25C10.7155 16.25 10.9137 16.1641 11.0555 16.0126L16.6725 10.0126Z"
                fill="#111111" />
        </svg>
    </div>

    <?php 
    // По умолчанию статус – "מוביל/ה"
    $status_text = 'מוביל/ה';
    $data = Dokan_Enhancer::get_store_rating_data( $store_user ); 
    if ( $data !== false ) {
        if ( $data['reviews_count'] < 21 ) {
            $status_text = 'New Seller';
        } elseif ( $data['final_rating'] >= 4.5 ) {
            $status_text = 'Leader';
        }
    } 
    ?>
    <div class="profile-store__subheading">
        <p class="profile-store__status">מוביל/ה</p>
        <p class="profile-store__star-rating">
            <svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M6.66339 0.308882C6.8011 0.0298517 7.19899 0.0298517 7.3367 0.308882L9.11937 3.92097C9.17406 4.03177 9.27976 4.10857 9.40204 4.12634L13.3882 4.70556C13.6961 4.75031 13.8191 5.12872 13.5963 5.34592L10.7119 8.15753C10.6234 8.24378 10.583 8.36804 10.6039 8.48983L11.2848 12.4599C11.3374 12.7666 11.0155 13.0004 10.7401 12.8557L7.17475 10.9812C7.06538 10.9237 6.93472 10.9237 6.82535 10.9812L3.26001 12.8557C2.98459 13.0004 2.66269 12.7666 2.71529 12.4599L3.39621 8.48983C3.4171 8.36804 3.37672 8.24378 3.28824 8.15753L0.403822 5.34592C0.181003 5.12872 0.303958 4.75031 0.611886 4.70556L4.59806 4.12634C4.72033 4.10857 4.82604 4.03177 4.88073 3.92097L6.66339 0.308882Z"
                    fill="#111111" />
            </svg>
            <?php echo $data['final_rating']; ?>
        </p>
    </div>
    <?php if (!empty($store_info['vendor_biography'])): ?>
        <div class="profile-store__description">
            <?php
            echo "<p>";
            printf('%s', apply_filters('the_content', $store_info['vendor_biography']));
            echo "</p>";
            ?>
        </div>
    <?php endif; ?>
    <ul class="profile-store__benefits">
        <li class="profile-store__benefit">
            <strong><?php echo $published_count; ?></strong>
            פריטים
        </li>
        <li class="profile-store__benefit">
            <div class="prodile-store__rating">
                <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="path-1-outside-1_1367_27031" maskUnits="userSpaceOnUse" x="0.5" y="0.5" width="20"
                        height="20" fill="black">
                        <rect fill="white" x="0.5" y="0.5" width="20" height="20" />
                        <path
                            d="M18.5 10.5C18.5 14.9183 14.9183 18.5 10.5 18.5C6.08172 18.5 2.5 14.9183 2.5 10.5C2.5 6.08172 6.08172 2.5 10.5 2.5C14.9183 2.5 18.5 6.08172 18.5 10.5ZM6.02 10.5C6.02 12.9742 8.02576 14.98 10.5 14.98C12.9742 14.98 14.98 12.9742 14.98 10.5C14.98 8.02576 12.9742 6.02 10.5 6.02C8.02576 6.02 6.02 8.02576 6.02 10.5Z" />
                    </mask>
                    <path
                        d="M18.5 10.5C18.5 14.9183 14.9183 18.5 10.5 18.5C6.08172 18.5 2.5 14.9183 2.5 10.5C2.5 6.08172 6.08172 2.5 10.5 2.5C14.9183 2.5 18.5 6.08172 18.5 10.5ZM6.02 10.5C6.02 12.9742 8.02576 14.98 10.5 14.98C12.9742 14.98 14.98 12.9742 14.98 10.5C14.98 8.02576 12.9742 6.02 10.5 6.02C8.02576 6.02 6.02 8.02576 6.02 10.5Z"
                        fill="#C7A77F" fill-opacity="0.3" />
                    <path
                        d="M18.5 10.5C18.5 14.9183 14.9183 18.5 10.5 18.5C6.08172 18.5 2.5 14.9183 2.5 10.5C2.5 6.08172 6.08172 2.5 10.5 2.5C14.9183 2.5 18.5 6.08172 18.5 10.5ZM6.02 10.5C6.02 12.9742 8.02576 14.98 10.5 14.98C12.9742 14.98 14.98 12.9742 14.98 10.5C14.98 8.02576 12.9742 6.02 10.5 6.02C8.02576 6.02 6.02 8.02576 6.02 10.5Z"
                        stroke="#C7A77F" stroke-opacity="0.3" stroke-width="4"
                        mask="url(#path-1-outside-1_1367_27031)" />
                    <path
                        d="M10.5 4.26C10.5 3.28798 11.2973 2.48067 12.2459 2.69282C13.5516 2.98483 14.7729 3.60194 15.7905 4.49911C17.2518 5.7874 18.1928 7.56462 18.4369 9.49733C18.6811 11.4301 18.2116 13.3854 17.1166 14.9967C16.0217 16.6079 14.3764 17.7642 12.4895 18.2487C10.6026 18.7331 8.60383 18.5125 6.86807 17.6281C5.13232 16.7436 3.77892 15.2563 3.06179 13.445C2.34465 11.6337 2.31307 9.623 2.97295 7.79009C3.43251 6.51364 4.20542 5.38451 5.20893 4.4996C5.93798 3.85671 7.02559 4.18006 7.49386 5.03184C7.96213 5.88363 7.61084 6.9408 7.00321 7.69949C6.69718 8.0816 6.45339 8.51434 6.28485 8.98245C5.91532 10.0089 5.93301 11.1349 6.3346 12.1492C6.7362 13.1635 7.4941 13.9964 8.46612 14.4917C9.43814 14.987 10.5575 15.1106 11.6141 14.8393C12.6708 14.568 13.5921 13.9204 14.2053 13.0181C14.8185 12.1159 15.0814 11.0208 14.9447 9.93851C14.8079 8.85619 14.281 7.86094 13.4627 7.1395C13.0895 6.81048 12.6674 6.54872 12.2151 6.3613C11.3171 5.98919 10.5 5.23202 10.5 4.26Z"
                        fill="#C7A77F" />
                </svg>
                <strong>
                    <?php 
                    $finalRating = $data['final_rating']; 
                    $percentage = ($finalRating / 5) * 100;
                    echo $percentage . '%';
                    ?>
                </strong>
            </div>
            ממוצע דירוג
        </li>
        <li class="profile-store__benefit">
            <strong><?php echo $reviews_num; ?></strong>
            ביקורות
        </li>
    </ul>
</section>
<?php
/* Template Name: Profile Store */

get_header('profile');
?>


	<section class="profile-products-section section-block">
		<div class="section-heading">
			<h2 class="section-heading__title section-heading__title--arrow">פריטים אחרונים</h2>
		</div>		
		<div class="profile-products profile-products--swipe">
			<div class="profile-product">
				<div class="profile-product__media">
					<a class="profile-product__edit">
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M1.31128 14.1984C1.34932 13.8561 1.36833 13.685 1.42012 13.525C1.46606 13.3831 1.53098 13.2481 1.6131 13.1235C1.70566 12.9832 1.82742 12.8614 2.07094 12.6179L13.0031 1.68577C13.9174 0.77141 15.3999 0.771411 16.3143 1.68577C17.2286 2.60013 17.2286 4.0826 16.3142 4.99696L5.38213 15.9291C5.1386 16.1726 5.01684 16.2943 4.87648 16.3869C4.75194 16.469 4.61688 16.5339 4.47496 16.5799C4.315 16.6317 4.14385 16.6507 3.80157 16.6887L1 17L1.31128 14.1984Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
						</svg>
					</a>
					<a href="https://swap.madebyomnis.com/product/perfect-moment/">
						<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-3.webp' ?>" alt="product 1" class="profile-product__image">
					</a>
				</div>
				<div class="profile-product__details">
					<p class="profile-product__tag-rent">השכרה</p>
					<h3 class="profile-product__title">Perfect Moment</h3>
					<p class="profile-product__desc">להשכרה DL1961 דגם נבאדה עם הדפס מופשט</p>
					<p class="profile-product__rent"><span>השכרה</span><span>החל מ-</span><span>39 ₪</span></p>
					<p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
			<div class="profile-product">
				<div class="profile-product__media">
					<a class="profile-product__edit">
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M1.31128 14.1984C1.34932 13.8561 1.36833 13.685 1.42012 13.525C1.46606 13.3831 1.53098 13.2481 1.6131 13.1235C1.70566 12.9832 1.82742 12.8614 2.07094 12.6179L13.0031 1.68577C13.9174 0.77141 15.3999 0.771411 16.3143 1.68577C17.2286 2.60013 17.2286 4.0826 16.3142 4.99696L5.38213 15.9291C5.1386 16.1726 5.01684 16.2943 4.87648 16.3869C4.75194 16.469 4.61688 16.5339 4.47496 16.5799C4.315 16.6317 4.14385 16.6507 3.80157 16.6887L1 17L1.31128 14.1984Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
						</svg>
					</a>
					<a href="https://swap.madebyomnis.com/product/burberry/">
						<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-1.webp' ?>" alt="product 1" class="profile-product__image">
					</a>
					</div>
				<div class="profile-product__details">
					<p class="profile-product__tag-rent">השכרה</p>
					<h3 class="profile-product__title">Burberry</h3>
					<p class="profile-product__desc">כובע באקט בצבע בז׳ עם סגירת חוטי קשירה במידה אחת לשליחה מיידית</p>
					<p class="profile-product__rent"><span>השכרה</span> <span>החל מ-</span> <span>39 ₪ </span></p>
					<p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>			<div class="profile-product">
				<div class="profile-product__media">
					<a class="profile-product__edit">
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M1.31128 14.1984C1.34932 13.8561 1.36833 13.685 1.42012 13.525C1.46606 13.3831 1.53098 13.2481 1.6131 13.1235C1.70566 12.9832 1.82742 12.8614 2.07094 12.6179L13.0031 1.68577C13.9174 0.77141 15.3999 0.771411 16.3143 1.68577C17.2286 2.60013 17.2286 4.0826 16.3142 4.99696L5.38213 15.9291C5.1386 16.1726 5.01684 16.2943 4.87648 16.3869C4.75194 16.469 4.61688 16.5339 4.47496 16.5799C4.315 16.6317 4.14385 16.6507 3.80157 16.6887L1 17L1.31128 14.1984Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
						</svg>
					</a>
					<a href="https://swap.madebyomnis.com/product/moncler/">
						<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-9.webp' ?>" alt="product 1" class="profile-product__image">
					</a>
				</div>
				<div class="profile-product__details">
					<p class="profile-product__tag-rent">השכרה</p>
					<h3 class="profile-product__title">Moncler</h3>
					<p class="profile-product__desc">כובע באקט בצבע בז׳ עם סגירת חוטי קשירה במידה אחת לשליחה מיידית</p>
					<p class="profile-product__rent"><span>השכרה</span><span>החל מ-</span><span>39 ₪</span></p>
					<p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
			<div class="profile-product">
				<div class="profile-product__media">
					<a class="profile-product__edit">
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M1.31128 14.1984C1.34932 13.8561 1.36833 13.685 1.42012 13.525C1.46606 13.3831 1.53098 13.2481 1.6131 13.1235C1.70566 12.9832 1.82742 12.8614 2.07094 12.6179L13.0031 1.68577C13.9174 0.77141 15.3999 0.771411 16.3143 1.68577C17.2286 2.60013 17.2286 4.0826 16.3142 4.99696L5.38213 15.9291C5.1386 16.1726 5.01684 16.2943 4.87648 16.3869C4.75194 16.469 4.61688 16.5339 4.47496 16.5799C4.315 16.6317 4.14385 16.6507 3.80157 16.6887L1 17L1.31128 14.1984Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
						</svg>
					</a>
					<a href="https://swap.madebyomnis.com/product/moncler/">
						<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-4.webp' ?>" alt="product 1" class="profile-product__image">
					</a>
				</div>
				<div class="profile-product__details">
					<p class="profile-product__tag-rent">השכרה</p>
					<h3 class="profile-product__title">Perfect Moment</h3>
					<p class="profile-product__desc">להשכרה DL1961 דגם נבאדה עם הדפס מופשט</p>
					<p class="profile-product__rent"><span>השכרה</span><span>החל מ-</span><span>39 ₪</span></p>
					<p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
		</div>
	</section>

	<section class="profile-products-section section-block">
		<div class="section-heading">
			<h2 class="section-heading__title section-heading__title--arrow">הגשת הצעות</h2>
		</div>		
		<div class="profile-products profile-products--swipe">
			<div class="profile-product">
				<div class="profile-product__media">
					<a class="profile-product__edit">
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M1.31128 14.1984C1.34932 13.8561 1.36833 13.685 1.42012 13.525C1.46606 13.3831 1.53098 13.2481 1.6131 13.1235C1.70566 12.9832 1.82742 12.8614 2.07094 12.6179L13.0031 1.68577C13.9174 0.77141 15.3999 0.771411 16.3143 1.68577C17.2286 2.60013 17.2286 4.0826 16.3142 4.99696L5.38213 15.9291C5.1386 16.1726 5.01684 16.2943 4.87648 16.3869C4.75194 16.469 4.61688 16.5339 4.47496 16.5799C4.315 16.6317 4.14385 16.6507 3.80157 16.6887L1 17L1.31128 14.1984Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
						</svg>
					</a>
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-3.webp' ?>" alt="product 1" class="profile-product__image">
				</div>
				<div class="profile-product__details">
					<p class="profile-product__tag-rent">השכרה</p>
					<h3 class="profile-product__title">Perfect Moment</h3>
					<p class="profile-product__desc">להשכרה DL1961 דגם נבאדה עם הדפס מופשט</p>
					<p class="profile-product__rent"><span>השכרה</span><span>החל מ-</span><span>39 ₪</span></p>
					<p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
			<div class="profile-product">
				<div class="profile-product__media">
					<a class="profile-product__edit">
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M1.31128 14.1984C1.34932 13.8561 1.36833 13.685 1.42012 13.525C1.46606 13.3831 1.53098 13.2481 1.6131 13.1235C1.70566 12.9832 1.82742 12.8614 2.07094 12.6179L13.0031 1.68577C13.9174 0.77141 15.3999 0.771411 16.3143 1.68577C17.2286 2.60013 17.2286 4.0826 16.3142 4.99696L5.38213 15.9291C5.1386 16.1726 5.01684 16.2943 4.87648 16.3869C4.75194 16.469 4.61688 16.5339 4.47496 16.5799C4.315 16.6317 4.14385 16.6507 3.80157 16.6887L1 17L1.31128 14.1984Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
						</svg>
					</a>
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-5.webp' ?>" alt="product 1" class="profile-product__image">
				</div>
				<div class="profile-product__details">
					<p class="profile-product__tag-rent">השכרה</p>
					<h3 class="profile-product__title">Burberry</h3>
					<p class="profile-product__desc">כובע באקט בצבע בז׳ עם סגירת חוטי קשירה במידה אחת לשליחה מיידית</p>
					<p class="profile-product__rent"><span>השכרה</span> <span>החל מ-</span> <span>39 ₪ </span></p>
					<p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>			<div class="profile-product">
				<div class="profile-product__media">
					<a class="profile-product__edit">
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M1.31128 14.1984C1.34932 13.8561 1.36833 13.685 1.42012 13.525C1.46606 13.3831 1.53098 13.2481 1.6131 13.1235C1.70566 12.9832 1.82742 12.8614 2.07094 12.6179L13.0031 1.68577C13.9174 0.77141 15.3999 0.771411 16.3143 1.68577C17.2286 2.60013 17.2286 4.0826 16.3142 4.99696L5.38213 15.9291C5.1386 16.1726 5.01684 16.2943 4.87648 16.3869C4.75194 16.469 4.61688 16.5339 4.47496 16.5799C4.315 16.6317 4.14385 16.6507 3.80157 16.6887L1 17L1.31128 14.1984Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
						</svg>
					</a>
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-4.webp' ?>" alt="product 1" class="profile-product__image">
				</div>
				<div class="profile-product__details">
					<p class="profile-product__tag-rent">השכרה</p>
					<h3 class="profile-product__title">Moncler</h3>
					<p class="profile-product__desc">כובע באקט בצבע בז׳ עם סגירת חוטי קשירה במידה אחת לשליחה מיידית</p>
					<p class="profile-product__rent"><span>השכרה</span><span>החל מ-</span><span>39 ₪</span></p>
					<p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
			<div class="profile-product">
				<div class="profile-product__media">
					<a class="profile-product__edit">
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M1.31128 14.1984C1.34932 13.8561 1.36833 13.685 1.42012 13.525C1.46606 13.3831 1.53098 13.2481 1.6131 13.1235C1.70566 12.9832 1.82742 12.8614 2.07094 12.6179L13.0031 1.68577C13.9174 0.77141 15.3999 0.771411 16.3143 1.68577C17.2286 2.60013 17.2286 4.0826 16.3142 4.99696L5.38213 15.9291C5.1386 16.1726 5.01684 16.2943 4.87648 16.3869C4.75194 16.469 4.61688 16.5339 4.47496 16.5799C4.315 16.6317 4.14385 16.6507 3.80157 16.6887L1 17L1.31128 14.1984Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
						</svg>
					</a>
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-4.webp' ?>" alt="product 1" class="profile-product__image">
				</div>
				<div class="profile-product__details">
					<p class="profile-product__tag-rent">השכרה</p>
					<h3 class="profile-product__title">Perfect Moment</h3>
					<p class="profile-product__desc">להשכרה DL1961 דגם נבאדה עם הדפס מופשט</p>
					<p class="profile-product__rent"><span>השכרה</span><span>החל מ-</span><span>39 ₪</span></p>
					<p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
		</div>
	</section>
	<div class="section-heading" style="margin-top:40px">
		<h2 class="section-heading__title section-heading__title--arrow"><a href="<?php echo home_url('dashboard/reports-data/'); ?>">ביצועי החנות</a></h2>
	</div>
	<div class="swap-tabs profile-store-report-tabs">
    <div class="swap-tab-buttons diagram-switch">
        <button class="swap-tab-button active" data-tab="tab1">החודש הנוכחי</button>
        <button class="swap-tab-button" data-tab="tab2">חודש קודם</button>
    </div>
    <div class="swap-tab-content">
        <div class="swap-tab-panel active" id="tab1">

			<div class="all-charts">
				<div>
					<img class="reports-placeholder" src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/reports/gen-report-1.svg' ?>" alt="">
				</div>
				<div>
					<img class="reports-placeholder" src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/reports/gen-report-2.svg' ?>" alt="">
				</div>
				<div>
					<img class="reports-placeholder" src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/reports/gen-report-3.svg' ?>" alt="">
				</div>
				<div>
					<img class="reports-placeholder" src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/reports/gen-report-4.svg' ?>" alt="">
				</div>
			</div>

			
	<section class="dashboard__top-sales">
		<div class="dashboard__section-heading">
			<h2 class="dashboard__section-title">המוצרים הכי נמכרים</h2>
		</div>
		<div class="dashboard-products dashboard-products--swipe">


			<div class="dashboard-product">
				<a class="dashboard-product__media" href="#">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-5.webp' ?>" alt="product 1" class="dashboard-product__image">
				</a>
				<div class="profile-product__details">
					<p class="profile-product__dynamic">+7%
						<svg width="36" height="17" viewBox="0 0 36 17" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3" stroke="#C7A77F" stroke-opacity="0.2" stroke-width="6" stroke-linecap="round"/>
							<path d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3" stroke="#C7A77F" stroke-width="2" stroke-linecap="round"/>
						</svg>
					</p>
					<h3 class="profile-product__title">Jacquemus bucket hat</h3>
					<p class="profile-product__rent"><span>השכרה</span> <span>החל מ-</span> <span>39 ₪ </span></p>
					<p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>	
			
			<div class="dashboard-product">
				<a class="dashboard-product__media" href="#">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-4.webp' ?>" alt="product 1" class="dashboard-product__image">
				</a>
				<div class="profile-product__details">
					<p class="profile-product__dynamic">+10%
						<svg width="36" height="17" viewBox="0 0 36 17" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3" stroke="#C7A77F" stroke-opacity="0.2" stroke-width="6" stroke-linecap="round"/>
							<path d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3" stroke="#C7A77F" stroke-width="2" stroke-linecap="round"/>
						</svg>
					</p>
					<h3 class="profile-product__title">Jacquemus bucket hat</h3>
					<p class="profile-product__rent"><span>השכרה</span><span>החל מ-</span><span>39 ₪</span></p>
					<p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>

			<div class="dashboard-product">
				<a class="dashboard-product__media" href="#">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-5.webp' ?>" alt="product 1" class="dashboard-product__image">
				</a>
				<div class="profile-product__details">
					<p class="profile-product__dynamic">+23%
						<svg width="36" height="17" viewBox="0 0 36 17" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3" stroke="#C7A77F" stroke-opacity="0.2" stroke-width="6" stroke-linecap="round"/>
							<path d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3" stroke="#C7A77F" stroke-width="2" stroke-linecap="round"/>
						</svg>
					</p>
					<h3 class="profile-product__title">Jacquemus bucket hat</h3>
					<p class="profile-product__rent"><span>השכרה</span> <span>החל מ-</span> <span>39 ₪ </span></p>
					<p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
			<div class="dashboard-product">
				<a class="dashboard-product__media" href="#">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-4.webp' ?>" alt="product 1" class="dashboard-product__image">
				</a>
				<div class="profile-product__details">
					<p class="profile-product__dynamic">+17%
						<svg width="36" height="17" viewBox="0 0 36 17" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3" stroke="#C7A77F" stroke-opacity="0.2" stroke-width="6" stroke-linecap="round"/>
							<path d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3" stroke="#C7A77F" stroke-width="2" stroke-linecap="round"/>
						</svg>
					</p>
					<h3 class="profile-product__title">Jacquemus bucket hat</h3>
					<p class="profile-product__rent"><span>השכרה</span><span>החל מ-</span><span>39 ₪</span></p>
					<p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
		</div>
	</section>




	<section class="dashboard-returned-buyers">
		<div class="dashboard__section-heading">
			<h2 class="dashboard__section-title">לקוחות מובילים
				<span>עלייה/16<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M7.95631 5.89855V14.9033C7.95631 15.4556 8.40403 15.9033 8.95631 15.9033H9.04369C9.59597 15.9033 10.0437 15.4556 10.0437 14.9033V5.89855L13.8171 9.67199C14.2076 10.0625 14.8408 10.0625 15.2313 9.67199L15.2929 9.61043C15.6834 9.2199 15.6834 8.58674 15.2929 8.19621L9.70711 2.61043C9.31658 2.2199 8.68342 2.2199 8.29289 2.61043L2.70711 8.19621C2.31658 8.58674 2.31658 9.2199 2.70711 9.61043L2.76866 9.67198C3.15919 10.0625 3.79235 10.0625 4.18288 9.67199L7.95631 5.89855Z" fill="#C7A77F"/>
<path d="M16 10.3175L16 10.3175C16.781 9.53648 16.781 8.27016 16 7.48911L10.4142 1.90332C9.63316 1.12227 8.36683 1.12227 7.58579 1.90332L2 7.48911C1.21895 8.27016 1.21895 9.53649 2 10.3175L2.06156 10.3791C2.84261 11.1601 4.10894 11.1601 4.88998 10.3791L6.95631 8.31276V14.9033C6.95631 16.0079 7.85174 16.9033 8.95631 16.9033H9.04369C10.1483 16.9033 11.0437 16.0079 11.0437 14.9033V8.31276L13.11 10.3791C13.8911 11.1601 15.1574 11.1601 15.9384 10.3791C15.9384 10.3791 15.9384 10.3791 15.9384 10.3791L16 10.3175Z" stroke="#C69F7C" stroke-opacity="0.2" stroke-width="2"/>
<path d="M7.95631 5.89855V14.9033C7.95631 15.4556 8.40403 15.9033 8.95631 15.9033H9.04369C9.59597 15.9033 10.0437 15.4556 10.0437 14.9033V5.89855L13.8171 9.67199C14.2076 10.0625 14.8408 10.0625 15.2313 9.67199L15.2929 9.61043C15.6834 9.2199 15.6834 8.58674 15.2929 8.19621L9.70711 2.61043C9.31658 2.2199 8.68342 2.2199 8.29289 2.61043L2.70711 8.19621C2.31658 8.58674 2.31658 9.2199 2.70711 9.61043L2.76866 9.67198C3.15919 10.0625 3.79235 10.0625 4.18288 9.67199L7.95631 5.89855Z" fill="#C7A77F"/>
</svg></span>
</h2>
		</div>
		<div class="dashboard__buyer-profiles">
			<div class="dashboard__buyer-profile">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-1.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
				<h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
				<p class="dashboard__buyer-profile-orders">הזמנות 38</p>
				<p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
			</div>
			<div class="dashboard__buyer-profile">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/store-image.jpg' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
				<h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
				<p class="dashboard__buyer-profile-orders">הזמנות 38</p>
				<p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
			</div>
			<div class="dashboard__buyer-profile">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-4.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
				<h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
				<p class="dashboard__buyer-profile-orders">הזמנות 38</p>
				<p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
			</div>
			<div class="dashboard__buyer-profile">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-2.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
				<h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
				<p class="dashboard__buyer-profile-orders">הזמנות 38</p>
				<p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
			</div>

			<div class="dashboard__buyer-profile">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/store-image.jpg' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
				<h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
				<p class="dashboard__buyer-profile-orders">הזמנות 38</p>
				<p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
			</div>
			<div class="dashboard__buyer-profile">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-1.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
				<h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
				<p class="dashboard__buyer-profile-orders">הזמנות 38</p>
				<p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
			</div>
			<div class="dashboard__buyer-profile">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-2.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
				<h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
				<p class="dashboard__buyer-profile-orders">הזמנות 38</p>
				<p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
			</div>
			<div class="dashboard__buyer-profile">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-4.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
				<h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
				<p class="dashboard__buyer-profile-orders">הזמנות 38</p>
				<p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
			</div>
		</div>
	</section>


        </div>
        <div class="swap-tab-panel" id="tab2">
			<div class="all-charts">
				<div>
					<img class="reports-placeholder" src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/reports/gen-report-1-prev.svg' ?>" alt="">
				</div>
				<div>
					<img class="reports-placeholder" src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/reports/gen-report-2-prev.svg' ?>" alt="">
				</div>
				<div>
					<img class="reports-placeholder" src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/reports/gen-report-3-prev.svg' ?>" alt="">
				</div>
				<div>
					<img class="reports-placeholder" src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/reports/gen-report-4-prev.svg' ?>" alt="">
				</div>
			</div>

			
	<section class="dashboard__top-sales">
		<div class="dashboard__section-heading">
			<h2 class="dashboard__section-title">המוצרים הכי נמכרים</h2>
		</div>
		<div class="dashboard-products dashboard-products--swipe">
			<div class="dashboard-product">
				<a class="dashboard-product__media" href="#">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-4.webp' ?>" alt="product 1" class="dashboard-product__image">
				</a>
				<div class="profile-product__details">
					<p class="profile-product__dynamic">+17%
						<svg width="36" height="17" viewBox="0 0 36 17" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3" stroke="#C7A77F" stroke-opacity="0.2" stroke-width="6" stroke-linecap="round"/>
							<path d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3" stroke="#C7A77F" stroke-width="2" stroke-linecap="round"/>
						</svg>
					</p>
					<h3 class="profile-product__title">Jacquemus bucket hat</h3>
					<p class="profile-product__rent"><span>השכרה</span><span>החל מ-</span><span>39 ₪</span></p>
					<p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
			<div class="dashboard-product">
				<a class="dashboard-product__media" href="#">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-5.webp' ?>" alt="product 1" class="dashboard-product__image">
				</a>
				<div class="profile-product__details">
					<p class="profile-product__dynamic">+7%
						<svg width="36" height="17" viewBox="0 0 36 17" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3" stroke="#C7A77F" stroke-opacity="0.2" stroke-width="6" stroke-linecap="round"/>
							<path d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3" stroke="#C7A77F" stroke-width="2" stroke-linecap="round"/>
						</svg>
					</p>
					<h3 class="profile-product__title">Jacquemus bucket hat</h3>
					<p class="profile-product__rent"><span>השכרה</span> <span>החל מ-</span> <span>39 ₪ </span></p>
					<p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>			
			<div class="dashboard-product">
				<a class="dashboard-product__media" href="#">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-4.webp' ?>" alt="product 1" class="dashboard-product__image">
				</a>
				<div class="profile-product__details">
					<p class="profile-product__dynamic">+10%
						<svg width="36" height="17" viewBox="0 0 36 17" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3" stroke="#C7A77F" stroke-opacity="0.2" stroke-width="6" stroke-linecap="round"/>
							<path d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3" stroke="#C7A77F" stroke-width="2" stroke-linecap="round"/>
						</svg>
					</p>
					<h3 class="profile-product__title">Jacquemus bucket hat</h3>
					<p class="profile-product__rent"><span>השכרה</span><span>החל מ-</span><span>39 ₪</span></p>
					<p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
			<div class="dashboard-product">
				<a class="dashboard-product__media" href="#">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-5.webp' ?>" alt="product 1" class="dashboard-product__image">
				</a>
				<div class="profile-product__details">
					<p class="profile-product__dynamic">+23%
						<svg width="36" height="17" viewBox="0 0 36 17" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3" stroke="#C7A77F" stroke-opacity="0.2" stroke-width="6" stroke-linecap="round"/>
							<path d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3" stroke="#C7A77F" stroke-width="2" stroke-linecap="round"/>
						</svg>
					</p>
					<h3 class="profile-product__title">Jacquemus bucket hat</h3>
					<p class="profile-product__rent"><span>השכרה</span> <span>החל מ-</span> <span>39 ₪ </span></p>
					<p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
		</div>
	</section>




	<section class="dashboard-returned-buyers">
		<div class="dashboard__section-heading">
			<h2 class="dashboard__section-title">לקוחות מובילים
				<span>עלייה/9<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M7.95631 5.89855V14.9033C7.95631 15.4556 8.40403 15.9033 8.95631 15.9033H9.04369C9.59597 15.9033 10.0437 15.4556 10.0437 14.9033V5.89855L13.8171 9.67199C14.2076 10.0625 14.8408 10.0625 15.2313 9.67199L15.2929 9.61043C15.6834 9.2199 15.6834 8.58674 15.2929 8.19621L9.70711 2.61043C9.31658 2.2199 8.68342 2.2199 8.29289 2.61043L2.70711 8.19621C2.31658 8.58674 2.31658 9.2199 2.70711 9.61043L2.76866 9.67198C3.15919 10.0625 3.79235 10.0625 4.18288 9.67199L7.95631 5.89855Z" fill="#C7A77F"/>
<path d="M16 10.3175L16 10.3175C16.781 9.53648 16.781 8.27016 16 7.48911L10.4142 1.90332C9.63316 1.12227 8.36683 1.12227 7.58579 1.90332L2 7.48911C1.21895 8.27016 1.21895 9.53649 2 10.3175L2.06156 10.3791C2.84261 11.1601 4.10894 11.1601 4.88998 10.3791L6.95631 8.31276V14.9033C6.95631 16.0079 7.85174 16.9033 8.95631 16.9033H9.04369C10.1483 16.9033 11.0437 16.0079 11.0437 14.9033V8.31276L13.11 10.3791C13.8911 11.1601 15.1574 11.1601 15.9384 10.3791C15.9384 10.3791 15.9384 10.3791 15.9384 10.3791L16 10.3175Z" stroke="#C69F7C" stroke-opacity="0.2" stroke-width="2"/>
<path d="M7.95631 5.89855V14.9033C7.95631 15.4556 8.40403 15.9033 8.95631 15.9033H9.04369C9.59597 15.9033 10.0437 15.4556 10.0437 14.9033V5.89855L13.8171 9.67199C14.2076 10.0625 14.8408 10.0625 15.2313 9.67199L15.2929 9.61043C15.6834 9.2199 15.6834 8.58674 15.2929 8.19621L9.70711 2.61043C9.31658 2.2199 8.68342 2.2199 8.29289 2.61043L2.70711 8.19621C2.31658 8.58674 2.31658 9.2199 2.70711 9.61043L2.76866 9.67198C3.15919 10.0625 3.79235 10.0625 4.18288 9.67199L7.95631 5.89855Z" fill="#C7A77F"/>
</svg></span>
</h2>
		</div>
		<div class="dashboard__buyer-profiles">
			<div class="dashboard__buyer-profile">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/store-image.jpg' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
				<h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
				<p class="dashboard__buyer-profile-orders">הזמנות 38</p>
				<p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
			</div>
			<div class="dashboard__buyer-profile">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-1.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
				<h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
				<p class="dashboard__buyer-profile-orders">הזמנות 38</p>
				<p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
			</div>
			<div class="dashboard__buyer-profile">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-2.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
				<h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
				<p class="dashboard__buyer-profile-orders">הזמנות 38</p>
				<p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
			</div>
			<div class="dashboard__buyer-profile">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-4.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
				<h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
				<p class="dashboard__buyer-profile-orders">הזמנות 38</p>
				<p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
			</div>
			<div class="dashboard__buyer-profile">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/store-image.jpg' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
				<h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
				<p class="dashboard__buyer-profile-orders">הזמנות 38</p>
				<p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
			</div>
			<div class="dashboard__buyer-profile">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-1.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
				<h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
				<p class="dashboard__buyer-profile-orders">הזמנות 38</p>
				<p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
			</div>
			<div class="dashboard__buyer-profile">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-2.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
				<h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
				<p class="dashboard__buyer-profile-orders">הזמנות 38</p>
				<p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
			</div>
			<div class="dashboard__buyer-profile">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-4.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
				<h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
				<p class="dashboard__buyer-profile-orders">הזמנות 38</p>
				<p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
			</div>
		</div>
	</section>

        </div>
    </div>
</div>




	<section class="profile-product-comments">
		<div class="section-heading">
			<h2 class="section-heading__title section-heading__title--arrow">ביקורות ודירוגים</h2>
		</div>
		<ol class="product-comments-list commentlist commentlist--swipe">
			<li class="product-comment">
				<div class="product-comment__header">
					<p class="product-comment__details">
						<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-4.webp' ?>" alt="" width="20" height="20" class="dashboard__author-image">
						<span class="product-comment__author">דניאלה</span>
						<span class="product-comment__date">שבוע שעבר</span>
					</p>
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12C4 12.5523 4.44772 13 5 13C5.55228 13 6 12.5523 6 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12C18 12.5523 18.4477 13 19 13C19.5523 13 20 12.5523 20 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</div>
				<p class="product-comment__rent-size">מידה שנשכרה:<span>S</span></p>
				<p class="product-comment__text">זו הייתה הפעם השנייה ששכרתי את השמלה הזו מנויה והיא הייתה שוב שלמות וכל התהליך היה כל כך חלק. שמלה מהממת! תודה לך!</p>
				<svg width="80" height="16" viewBox="0 0 80 16" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M7.52193 2.30205C7.67559 1.99076 7.75242 1.83511 7.85672 1.78538C7.94746 1.74211 8.05289 1.74211 8.14363 1.78538C8.24793 1.83511 8.32476 1.99076 8.47842 2.30205L9.9362 5.25536C9.98157 5.34726 10.0042 5.39321 10.0374 5.42889C10.0667 5.46048 10.1019 5.48607 10.141 5.50425C10.1852 5.52479 10.2359 5.5322 10.3373 5.54702L13.5982 6.02364C13.9415 6.07383 14.1132 6.09893 14.1927 6.1828C14.2618 6.25577 14.2943 6.35604 14.2812 6.45569C14.266 6.57022 14.1417 6.69129 13.8931 6.93343L11.5345 9.23078C11.4609 9.3024 11.4242 9.33821 11.4004 9.38081C11.3794 9.41854 11.366 9.45998 11.3608 9.50284C11.3549 9.55125 11.3636 9.60183 11.3809 9.703L11.9375 12.9479C11.9962 13.2902 12.0255 13.4613 11.9704 13.5628C11.9224 13.6512 11.8371 13.7132 11.7382 13.7315C11.6246 13.7526 11.4709 13.6717 11.1636 13.5101L8.24841 11.9771C8.15758 11.9293 8.11217 11.9054 8.06432 11.896C8.02196 11.8877 7.97839 11.8877 7.93602 11.896C7.88818 11.9054 7.84276 11.9293 7.75193 11.9771L4.83678 13.5101C4.52944 13.6717 4.37577 13.7526 4.26214 13.7315C4.16328 13.7132 4.07798 13.6512 4.02999 13.5628C3.97483 13.4613 4.00418 13.2902 4.06288 12.9479L4.61942 9.703C4.63677 9.60183 4.64545 9.55125 4.63958 9.50284C4.63438 9.45998 4.6209 9.41854 4.5999 9.38081C4.57618 9.33821 4.53941 9.3024 4.46589 9.23078L2.1072 6.93342C1.8586 6.69129 1.73431 6.57022 1.71918 6.45569C1.70602 6.35604 1.73853 6.25577 1.80766 6.1828C1.88712 6.09893 2.05881 6.07383 2.40219 6.02364L5.66304 5.54702C5.76445 5.5322 5.81515 5.52479 5.85931 5.50425C5.89841 5.48607 5.9336 5.46048 5.96295 5.42889C5.9961 5.39321 6.01878 5.34726 6.06415 5.25536L7.52193 2.30205Z" fill="#111111"/>
					<path d="M23.5219 2.30205C23.6756 1.99076 23.7524 1.83511 23.8567 1.78538C23.9475 1.74211 24.0529 1.74211 24.1436 1.78538C24.2479 1.83511 24.3248 1.99076 24.4784 2.30205L25.9362 5.25536C25.9816 5.34726 26.0042 5.39321 26.0374 5.42889C26.0667 5.46048 26.1019 5.48607 26.141 5.50425C26.1852 5.52479 26.2359 5.5322 26.3373 5.54702L29.5982 6.02364C29.9415 6.07383 30.1132 6.09893 30.1927 6.1828C30.2618 6.25577 30.2943 6.35604 30.2812 6.45569C30.266 6.57022 30.1417 6.69129 29.8931 6.93343L27.5345 9.23078C27.4609 9.3024 27.4242 9.33821 27.4004 9.38081C27.3794 9.41854 27.366 9.45998 27.3608 9.50284C27.3549 9.55125 27.3636 9.60183 27.3809 9.703L27.9375 12.9479C27.9962 13.2902 28.0255 13.4613 27.9704 13.5628C27.9224 13.6512 27.8371 13.7132 27.7382 13.7315C27.6246 13.7526 27.4709 13.6717 27.1636 13.5101L24.2484 11.9771C24.1576 11.9293 24.1122 11.9054 24.0643 11.896C24.022 11.8877 23.9784 11.8877 23.936 11.896C23.8882 11.9054 23.8428 11.9293 23.7519 11.9771L20.8368 13.5101C20.5294 13.6717 20.3758 13.7526 20.2621 13.7315C20.1633 13.7132 20.078 13.6512 20.03 13.5628C19.9748 13.4613 20.0042 13.2902 20.0629 12.9479L20.6194 9.703C20.6368 9.60183 20.6454 9.55125 20.6396 9.50284C20.6344 9.45998 20.6209 9.41854 20.5999 9.38081C20.5762 9.33821 20.5394 9.3024 20.4659 9.23078L18.1072 6.93342C17.8586 6.69129 17.7343 6.57022 17.7192 6.45569C17.706 6.35604 17.7385 6.25577 17.8077 6.1828C17.8871 6.09893 18.0588 6.07383 18.4022 6.02364L21.663 5.54702C21.7644 5.5322 21.8151 5.52479 21.8593 5.50425C21.8984 5.48607 21.9336 5.46048 21.963 5.42889C21.9961 5.39321 22.0188 5.34726 22.0641 5.25536L23.5219 2.30205Z" fill="#111111"/>
					<path d="M39.5219 2.30205C39.6756 1.99076 39.7524 1.83511 39.8567 1.78538C39.9475 1.74211 40.0529 1.74211 40.1436 1.78538C40.2479 1.83511 40.3248 1.99076 40.4784 2.30205L41.9362 5.25536C41.9816 5.34726 42.0042 5.39321 42.0374 5.42889C42.0667 5.46048 42.1019 5.48607 42.141 5.50425C42.1852 5.52479 42.2359 5.5322 42.3373 5.54702L45.5982 6.02364C45.9415 6.07383 46.1132 6.09893 46.1927 6.1828C46.2618 6.25577 46.2943 6.35604 46.2812 6.45569C46.266 6.57022 46.1417 6.69129 45.8931 6.93343L43.5345 9.23078C43.4609 9.3024 43.4242 9.33821 43.4004 9.38081C43.3794 9.41854 43.366 9.45998 43.3608 9.50284C43.3549 9.55125 43.3636 9.60183 43.3809 9.703L43.9375 12.9479C43.9962 13.2902 44.0255 13.4613 43.9704 13.5628C43.9224 13.6512 43.8371 13.7132 43.7382 13.7315C43.6246 13.7526 43.4709 13.6717 43.1636 13.5101L40.2484 11.9771C40.1576 11.9293 40.1122 11.9054 40.0643 11.896C40.022 11.8877 39.9784 11.8877 39.936 11.896C39.8882 11.9054 39.8428 11.9293 39.7519 11.9771L36.8368 13.5101C36.5294 13.6717 36.3758 13.7526 36.2621 13.7315C36.1633 13.7132 36.078 13.6512 36.03 13.5628C35.9748 13.4613 36.0042 13.2902 36.0629 12.9479L36.6194 9.703C36.6368 9.60183 36.6454 9.55125 36.6396 9.50284C36.6344 9.45998 36.6209 9.41854 36.5999 9.38081C36.5762 9.33821 36.5394 9.3024 36.4659 9.23078L34.1072 6.93342C33.8586 6.69129 33.7343 6.57022 33.7192 6.45569C33.706 6.35604 33.7385 6.25577 33.8077 6.1828C33.8871 6.09893 34.0588 6.07383 34.4022 6.02364L37.663 5.54702C37.7644 5.5322 37.8151 5.52479 37.8593 5.50425C37.8984 5.48607 37.9336 5.46048 37.963 5.42889C37.9961 5.39321 38.0188 5.34726 38.0641 5.25536L39.5219 2.30205Z" fill="#111111"/>
					<path d="M55.5219 2.30205C55.6756 1.99076 55.7524 1.83511 55.8567 1.78538C55.9475 1.74211 56.0529 1.74211 56.1436 1.78538C56.2479 1.83511 56.3248 1.99076 56.4784 2.30205L57.9362 5.25536C57.9816 5.34726 58.0042 5.39321 58.0374 5.42889C58.0667 5.46048 58.1019 5.48607 58.141 5.50425C58.1852 5.52479 58.2359 5.5322 58.3373 5.54702L61.5982 6.02364C61.9415 6.07383 62.1132 6.09893 62.1927 6.1828C62.2618 6.25577 62.2943 6.35604 62.2812 6.45569C62.266 6.57022 62.1417 6.69129 61.8931 6.93343L59.5345 9.23078C59.4609 9.3024 59.4242 9.33821 59.4004 9.38081C59.3794 9.41854 59.366 9.45998 59.3608 9.50284C59.3549 9.55125 59.3636 9.60183 59.3809 9.703L59.9375 12.9479C59.9962 13.2902 60.0255 13.4613 59.9704 13.5628C59.9224 13.6512 59.8371 13.7132 59.7382 13.7315C59.6246 13.7526 59.4709 13.6717 59.1636 13.5101L56.2484 11.9771C56.1576 11.9293 56.1122 11.9054 56.0643 11.896C56.022 11.8877 55.9784 11.8877 55.936 11.896C55.8882 11.9054 55.8428 11.9293 55.7519 11.9771L52.8368 13.5101C52.5294 13.6717 52.3758 13.7526 52.2621 13.7315C52.1633 13.7132 52.078 13.6512 52.03 13.5628C51.9748 13.4613 52.0042 13.2902 52.0629 12.9479L52.6194 9.703C52.6368 9.60183 52.6454 9.55125 52.6396 9.50284C52.6344 9.45998 52.6209 9.41854 52.5999 9.38081C52.5762 9.33821 52.5394 9.3024 52.4659 9.23078L50.1072 6.93342C49.8586 6.69129 49.7343 6.57022 49.7192 6.45569C49.706 6.35604 49.7385 6.25577 49.8077 6.1828C49.8871 6.09893 50.0588 6.07383 50.4022 6.02364L53.663 5.54702C53.7644 5.5322 53.8151 5.52479 53.8593 5.50425C53.8984 5.48607 53.9336 5.46048 53.963 5.42889C53.9961 5.39321 54.0188 5.34726 54.0641 5.25536L55.5219 2.30205Z" fill="#111111"/>
					<path d="M71.5219 2.30205C71.6756 1.99076 71.7524 1.83511 71.8567 1.78538C71.9475 1.74211 72.0529 1.74211 72.1436 1.78538C72.2479 1.83511 72.3248 1.99076 72.4784 2.30205L73.9362 5.25536C73.9816 5.34726 74.0042 5.39321 74.0374 5.42889C74.0667 5.46048 74.1019 5.48607 74.141 5.50425C74.1852 5.52479 74.2359 5.5322 74.3373 5.54702L77.5982 6.02364C77.9415 6.07383 78.1132 6.09893 78.1927 6.1828C78.2618 6.25577 78.2943 6.35604 78.2812 6.45569C78.266 6.57022 78.1417 6.69129 77.8931 6.93343L75.5345 9.23078C75.4609 9.3024 75.4242 9.33821 75.4004 9.38081C75.3794 9.41854 75.366 9.45998 75.3608 9.50284C75.3549 9.55125 75.3636 9.60183 75.3809 9.703L75.9375 12.9479C75.9962 13.2902 76.0255 13.4613 75.9704 13.5628C75.9224 13.6512 75.8371 13.7132 75.7382 13.7315C75.6246 13.7526 75.4709 13.6717 75.1636 13.5101L72.2484 11.9771C72.1576 11.9293 72.1122 11.9054 72.0643 11.896C72.022 11.8877 71.9784 11.8877 71.936 11.896C71.8882 11.9054 71.8428 11.9293 71.7519 11.9771L68.8368 13.5101C68.5294 13.6717 68.3758 13.7526 68.2621 13.7315C68.1633 13.7132 68.078 13.6512 68.03 13.5628C67.9748 13.4613 68.0042 13.2902 68.0629 12.9479L68.6194 9.703C68.6368 9.60183 68.6454 9.55125 68.6396 9.50284C68.6344 9.45998 68.6209 9.41854 68.5999 9.38081C68.5762 9.33821 68.5394 9.3024 68.4659 9.23078L66.1072 6.93342C65.8586 6.69129 65.7343 6.57022 65.7192 6.45569C65.706 6.35604 65.7385 6.25577 65.8077 6.1828C65.8871 6.09893 66.0588 6.07383 66.4022 6.02364L69.663 5.54702C69.7644 5.5322 69.8151 5.52479 69.8593 5.50425C69.8984 5.48607 69.9336 5.46048 69.963 5.42889C69.9961 5.39321 70.0188 5.34726 70.0641 5.25536L71.5219 2.30205Z" fill="#111111"/>
				</svg>
				<div class="product-comment__product-images">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-4.webp' ?>" alt="product 4" class="product-comment__product-image">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-4.webp' ?>" alt="product 4" class="product-comment__product-image">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-4.webp' ?>" alt="product 4" class="product-comment__product-image">
				</div>
			</li>
			<li class="product-comment">
				<div class="product-comment__header">
					<p class="product-comment__details">
						<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-4.webp' ?>" alt="" width="20" height="20" class="dashboard__author-image">
						<span class="product-comment__author">דניאלה</span>
						<span class="product-comment__date">שבוע שעבר</span>
					</p>
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12C4 12.5523 4.44772 13 5 13C5.55228 13 6 12.5523 6 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12C18 12.5523 18.4477 13 19 13C19.5523 13 20 12.5523 20 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</div>
				<p class="product-comment__rent-size">מידה שנשכרה:<span>S</span></p>
				<p class="product-comment__text">זו הייתה הפעם השנייה ששכרתי את השמלה הזו מנויה והיא הייתה שוב שלמות וכל התהליך היה כל כך חלק. שמלה מהממת! תודה לך!</p>
				<svg width="80" height="16" viewBox="0 0 80 16" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M7.52193 2.30205C7.67559 1.99076 7.75242 1.83511 7.85672 1.78538C7.94746 1.74211 8.05289 1.74211 8.14363 1.78538C8.24793 1.83511 8.32476 1.99076 8.47842 2.30205L9.9362 5.25536C9.98157 5.34726 10.0042 5.39321 10.0374 5.42889C10.0667 5.46048 10.1019 5.48607 10.141 5.50425C10.1852 5.52479 10.2359 5.5322 10.3373 5.54702L13.5982 6.02364C13.9415 6.07383 14.1132 6.09893 14.1927 6.1828C14.2618 6.25577 14.2943 6.35604 14.2812 6.45569C14.266 6.57022 14.1417 6.69129 13.8931 6.93343L11.5345 9.23078C11.4609 9.3024 11.4242 9.33821 11.4004 9.38081C11.3794 9.41854 11.366 9.45998 11.3608 9.50284C11.3549 9.55125 11.3636 9.60183 11.3809 9.703L11.9375 12.9479C11.9962 13.2902 12.0255 13.4613 11.9704 13.5628C11.9224 13.6512 11.8371 13.7132 11.7382 13.7315C11.6246 13.7526 11.4709 13.6717 11.1636 13.5101L8.24841 11.9771C8.15758 11.9293 8.11217 11.9054 8.06432 11.896C8.02196 11.8877 7.97839 11.8877 7.93602 11.896C7.88818 11.9054 7.84276 11.9293 7.75193 11.9771L4.83678 13.5101C4.52944 13.6717 4.37577 13.7526 4.26214 13.7315C4.16328 13.7132 4.07798 13.6512 4.02999 13.5628C3.97483 13.4613 4.00418 13.2902 4.06288 12.9479L4.61942 9.703C4.63677 9.60183 4.64545 9.55125 4.63958 9.50284C4.63438 9.45998 4.6209 9.41854 4.5999 9.38081C4.57618 9.33821 4.53941 9.3024 4.46589 9.23078L2.1072 6.93342C1.8586 6.69129 1.73431 6.57022 1.71918 6.45569C1.70602 6.35604 1.73853 6.25577 1.80766 6.1828C1.88712 6.09893 2.05881 6.07383 2.40219 6.02364L5.66304 5.54702C5.76445 5.5322 5.81515 5.52479 5.85931 5.50425C5.89841 5.48607 5.9336 5.46048 5.96295 5.42889C5.9961 5.39321 6.01878 5.34726 6.06415 5.25536L7.52193 2.30205Z" fill="#111111"/>
					<path d="M23.5219 2.30205C23.6756 1.99076 23.7524 1.83511 23.8567 1.78538C23.9475 1.74211 24.0529 1.74211 24.1436 1.78538C24.2479 1.83511 24.3248 1.99076 24.4784 2.30205L25.9362 5.25536C25.9816 5.34726 26.0042 5.39321 26.0374 5.42889C26.0667 5.46048 26.1019 5.48607 26.141 5.50425C26.1852 5.52479 26.2359 5.5322 26.3373 5.54702L29.5982 6.02364C29.9415 6.07383 30.1132 6.09893 30.1927 6.1828C30.2618 6.25577 30.2943 6.35604 30.2812 6.45569C30.266 6.57022 30.1417 6.69129 29.8931 6.93343L27.5345 9.23078C27.4609 9.3024 27.4242 9.33821 27.4004 9.38081C27.3794 9.41854 27.366 9.45998 27.3608 9.50284C27.3549 9.55125 27.3636 9.60183 27.3809 9.703L27.9375 12.9479C27.9962 13.2902 28.0255 13.4613 27.9704 13.5628C27.9224 13.6512 27.8371 13.7132 27.7382 13.7315C27.6246 13.7526 27.4709 13.6717 27.1636 13.5101L24.2484 11.9771C24.1576 11.9293 24.1122 11.9054 24.0643 11.896C24.022 11.8877 23.9784 11.8877 23.936 11.896C23.8882 11.9054 23.8428 11.9293 23.7519 11.9771L20.8368 13.5101C20.5294 13.6717 20.3758 13.7526 20.2621 13.7315C20.1633 13.7132 20.078 13.6512 20.03 13.5628C19.9748 13.4613 20.0042 13.2902 20.0629 12.9479L20.6194 9.703C20.6368 9.60183 20.6454 9.55125 20.6396 9.50284C20.6344 9.45998 20.6209 9.41854 20.5999 9.38081C20.5762 9.33821 20.5394 9.3024 20.4659 9.23078L18.1072 6.93342C17.8586 6.69129 17.7343 6.57022 17.7192 6.45569C17.706 6.35604 17.7385 6.25577 17.8077 6.1828C17.8871 6.09893 18.0588 6.07383 18.4022 6.02364L21.663 5.54702C21.7644 5.5322 21.8151 5.52479 21.8593 5.50425C21.8984 5.48607 21.9336 5.46048 21.963 5.42889C21.9961 5.39321 22.0188 5.34726 22.0641 5.25536L23.5219 2.30205Z" fill="#111111"/>
					<path d="M39.5219 2.30205C39.6756 1.99076 39.7524 1.83511 39.8567 1.78538C39.9475 1.74211 40.0529 1.74211 40.1436 1.78538C40.2479 1.83511 40.3248 1.99076 40.4784 2.30205L41.9362 5.25536C41.9816 5.34726 42.0042 5.39321 42.0374 5.42889C42.0667 5.46048 42.1019 5.48607 42.141 5.50425C42.1852 5.52479 42.2359 5.5322 42.3373 5.54702L45.5982 6.02364C45.9415 6.07383 46.1132 6.09893 46.1927 6.1828C46.2618 6.25577 46.2943 6.35604 46.2812 6.45569C46.266 6.57022 46.1417 6.69129 45.8931 6.93343L43.5345 9.23078C43.4609 9.3024 43.4242 9.33821 43.4004 9.38081C43.3794 9.41854 43.366 9.45998 43.3608 9.50284C43.3549 9.55125 43.3636 9.60183 43.3809 9.703L43.9375 12.9479C43.9962 13.2902 44.0255 13.4613 43.9704 13.5628C43.9224 13.6512 43.8371 13.7132 43.7382 13.7315C43.6246 13.7526 43.4709 13.6717 43.1636 13.5101L40.2484 11.9771C40.1576 11.9293 40.1122 11.9054 40.0643 11.896C40.022 11.8877 39.9784 11.8877 39.936 11.896C39.8882 11.9054 39.8428 11.9293 39.7519 11.9771L36.8368 13.5101C36.5294 13.6717 36.3758 13.7526 36.2621 13.7315C36.1633 13.7132 36.078 13.6512 36.03 13.5628C35.9748 13.4613 36.0042 13.2902 36.0629 12.9479L36.6194 9.703C36.6368 9.60183 36.6454 9.55125 36.6396 9.50284C36.6344 9.45998 36.6209 9.41854 36.5999 9.38081C36.5762 9.33821 36.5394 9.3024 36.4659 9.23078L34.1072 6.93342C33.8586 6.69129 33.7343 6.57022 33.7192 6.45569C33.706 6.35604 33.7385 6.25577 33.8077 6.1828C33.8871 6.09893 34.0588 6.07383 34.4022 6.02364L37.663 5.54702C37.7644 5.5322 37.8151 5.52479 37.8593 5.50425C37.8984 5.48607 37.9336 5.46048 37.963 5.42889C37.9961 5.39321 38.0188 5.34726 38.0641 5.25536L39.5219 2.30205Z" fill="#111111"/>
					<path d="M55.5219 2.30205C55.6756 1.99076 55.7524 1.83511 55.8567 1.78538C55.9475 1.74211 56.0529 1.74211 56.1436 1.78538C56.2479 1.83511 56.3248 1.99076 56.4784 2.30205L57.9362 5.25536C57.9816 5.34726 58.0042 5.39321 58.0374 5.42889C58.0667 5.46048 58.1019 5.48607 58.141 5.50425C58.1852 5.52479 58.2359 5.5322 58.3373 5.54702L61.5982 6.02364C61.9415 6.07383 62.1132 6.09893 62.1927 6.1828C62.2618 6.25577 62.2943 6.35604 62.2812 6.45569C62.266 6.57022 62.1417 6.69129 61.8931 6.93343L59.5345 9.23078C59.4609 9.3024 59.4242 9.33821 59.4004 9.38081C59.3794 9.41854 59.366 9.45998 59.3608 9.50284C59.3549 9.55125 59.3636 9.60183 59.3809 9.703L59.9375 12.9479C59.9962 13.2902 60.0255 13.4613 59.9704 13.5628C59.9224 13.6512 59.8371 13.7132 59.7382 13.7315C59.6246 13.7526 59.4709 13.6717 59.1636 13.5101L56.2484 11.9771C56.1576 11.9293 56.1122 11.9054 56.0643 11.896C56.022 11.8877 55.9784 11.8877 55.936 11.896C55.8882 11.9054 55.8428 11.9293 55.7519 11.9771L52.8368 13.5101C52.5294 13.6717 52.3758 13.7526 52.2621 13.7315C52.1633 13.7132 52.078 13.6512 52.03 13.5628C51.9748 13.4613 52.0042 13.2902 52.0629 12.9479L52.6194 9.703C52.6368 9.60183 52.6454 9.55125 52.6396 9.50284C52.6344 9.45998 52.6209 9.41854 52.5999 9.38081C52.5762 9.33821 52.5394 9.3024 52.4659 9.23078L50.1072 6.93342C49.8586 6.69129 49.7343 6.57022 49.7192 6.45569C49.706 6.35604 49.7385 6.25577 49.8077 6.1828C49.8871 6.09893 50.0588 6.07383 50.4022 6.02364L53.663 5.54702C53.7644 5.5322 53.8151 5.52479 53.8593 5.50425C53.8984 5.48607 53.9336 5.46048 53.963 5.42889C53.9961 5.39321 54.0188 5.34726 54.0641 5.25536L55.5219 2.30205Z" fill="#111111"/>
					<path d="M71.5219 2.30205C71.6756 1.99076 71.7524 1.83511 71.8567 1.78538C71.9475 1.74211 72.0529 1.74211 72.1436 1.78538C72.2479 1.83511 72.3248 1.99076 72.4784 2.30205L73.9362 5.25536C73.9816 5.34726 74.0042 5.39321 74.0374 5.42889C74.0667 5.46048 74.1019 5.48607 74.141 5.50425C74.1852 5.52479 74.2359 5.5322 74.3373 5.54702L77.5982 6.02364C77.9415 6.07383 78.1132 6.09893 78.1927 6.1828C78.2618 6.25577 78.2943 6.35604 78.2812 6.45569C78.266 6.57022 78.1417 6.69129 77.8931 6.93343L75.5345 9.23078C75.4609 9.3024 75.4242 9.33821 75.4004 9.38081C75.3794 9.41854 75.366 9.45998 75.3608 9.50284C75.3549 9.55125 75.3636 9.60183 75.3809 9.703L75.9375 12.9479C75.9962 13.2902 76.0255 13.4613 75.9704 13.5628C75.9224 13.6512 75.8371 13.7132 75.7382 13.7315C75.6246 13.7526 75.4709 13.6717 75.1636 13.5101L72.2484 11.9771C72.1576 11.9293 72.1122 11.9054 72.0643 11.896C72.022 11.8877 71.9784 11.8877 71.936 11.896C71.8882 11.9054 71.8428 11.9293 71.7519 11.9771L68.8368 13.5101C68.5294 13.6717 68.3758 13.7526 68.2621 13.7315C68.1633 13.7132 68.078 13.6512 68.03 13.5628C67.9748 13.4613 68.0042 13.2902 68.0629 12.9479L68.6194 9.703C68.6368 9.60183 68.6454 9.55125 68.6396 9.50284C68.6344 9.45998 68.6209 9.41854 68.5999 9.38081C68.5762 9.33821 68.5394 9.3024 68.4659 9.23078L66.1072 6.93342C65.8586 6.69129 65.7343 6.57022 65.7192 6.45569C65.706 6.35604 65.7385 6.25577 65.8077 6.1828C65.8871 6.09893 66.0588 6.07383 66.4022 6.02364L69.663 5.54702C69.7644 5.5322 69.8151 5.52479 69.8593 5.50425C69.8984 5.48607 69.9336 5.46048 69.963 5.42889C69.9961 5.39321 70.0188 5.34726 70.0641 5.25536L71.5219 2.30205Z" fill="#111111"/>
				</svg>
				<div class="product-comment__product-images">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-2.webp' ?>" alt="product 3" class="product-comment__product-image">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-2.webp' ?>" alt="product 3" class="product-comment__product-image">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-2.webp' ?>" alt="product 3" class="product-comment__product-image">
				</div>
			</li>
			<li class="product-comment">
				<div class="product-comment__header">
					<p class="product-comment__details">
						<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-4.webp' ?>" alt="" width="20" height="20" class="dashboard__author-image">
						<span class="profile-product-comment__author">דניאלה</span>
						<span class="profile-product-comment__date">שבוע שעבר</span>
					</p>
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12C4 12.5523 4.44772 13 5 13C5.55228 13 6 12.5523 6 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12C18 12.5523 18.4477 13 19 13C19.5523 13 20 12.5523 20 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</div>
				<p class="product-comment__rent-size">מידה שנשכרה:<span>S</span></p>
				<p class="product-comment__text">זו הייתה הפעם השנייה ששכרתי את השמלה הזו מנויה והיא הייתה שוב שלמות וכל התהליך היה כל כך חלק. שמלה מהממת! תודה לך!</p>
				<svg width="80" height="16" viewBox="0 0 80 16" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M7.52193 2.30205C7.67559 1.99076 7.75242 1.83511 7.85672 1.78538C7.94746 1.74211 8.05289 1.74211 8.14363 1.78538C8.24793 1.83511 8.32476 1.99076 8.47842 2.30205L9.9362 5.25536C9.98157 5.34726 10.0042 5.39321 10.0374 5.42889C10.0667 5.46048 10.1019 5.48607 10.141 5.50425C10.1852 5.52479 10.2359 5.5322 10.3373 5.54702L13.5982 6.02364C13.9415 6.07383 14.1132 6.09893 14.1927 6.1828C14.2618 6.25577 14.2943 6.35604 14.2812 6.45569C14.266 6.57022 14.1417 6.69129 13.8931 6.93343L11.5345 9.23078C11.4609 9.3024 11.4242 9.33821 11.4004 9.38081C11.3794 9.41854 11.366 9.45998 11.3608 9.50284C11.3549 9.55125 11.3636 9.60183 11.3809 9.703L11.9375 12.9479C11.9962 13.2902 12.0255 13.4613 11.9704 13.5628C11.9224 13.6512 11.8371 13.7132 11.7382 13.7315C11.6246 13.7526 11.4709 13.6717 11.1636 13.5101L8.24841 11.9771C8.15758 11.9293 8.11217 11.9054 8.06432 11.896C8.02196 11.8877 7.97839 11.8877 7.93602 11.896C7.88818 11.9054 7.84276 11.9293 7.75193 11.9771L4.83678 13.5101C4.52944 13.6717 4.37577 13.7526 4.26214 13.7315C4.16328 13.7132 4.07798 13.6512 4.02999 13.5628C3.97483 13.4613 4.00418 13.2902 4.06288 12.9479L4.61942 9.703C4.63677 9.60183 4.64545 9.55125 4.63958 9.50284C4.63438 9.45998 4.6209 9.41854 4.5999 9.38081C4.57618 9.33821 4.53941 9.3024 4.46589 9.23078L2.1072 6.93342C1.8586 6.69129 1.73431 6.57022 1.71918 6.45569C1.70602 6.35604 1.73853 6.25577 1.80766 6.1828C1.88712 6.09893 2.05881 6.07383 2.40219 6.02364L5.66304 5.54702C5.76445 5.5322 5.81515 5.52479 5.85931 5.50425C5.89841 5.48607 5.9336 5.46048 5.96295 5.42889C5.9961 5.39321 6.01878 5.34726 6.06415 5.25536L7.52193 2.30205Z" fill="#111111"/>
					<path d="M23.5219 2.30205C23.6756 1.99076 23.7524 1.83511 23.8567 1.78538C23.9475 1.74211 24.0529 1.74211 24.1436 1.78538C24.2479 1.83511 24.3248 1.99076 24.4784 2.30205L25.9362 5.25536C25.9816 5.34726 26.0042 5.39321 26.0374 5.42889C26.0667 5.46048 26.1019 5.48607 26.141 5.50425C26.1852 5.52479 26.2359 5.5322 26.3373 5.54702L29.5982 6.02364C29.9415 6.07383 30.1132 6.09893 30.1927 6.1828C30.2618 6.25577 30.2943 6.35604 30.2812 6.45569C30.266 6.57022 30.1417 6.69129 29.8931 6.93343L27.5345 9.23078C27.4609 9.3024 27.4242 9.33821 27.4004 9.38081C27.3794 9.41854 27.366 9.45998 27.3608 9.50284C27.3549 9.55125 27.3636 9.60183 27.3809 9.703L27.9375 12.9479C27.9962 13.2902 28.0255 13.4613 27.9704 13.5628C27.9224 13.6512 27.8371 13.7132 27.7382 13.7315C27.6246 13.7526 27.4709 13.6717 27.1636 13.5101L24.2484 11.9771C24.1576 11.9293 24.1122 11.9054 24.0643 11.896C24.022 11.8877 23.9784 11.8877 23.936 11.896C23.8882 11.9054 23.8428 11.9293 23.7519 11.9771L20.8368 13.5101C20.5294 13.6717 20.3758 13.7526 20.2621 13.7315C20.1633 13.7132 20.078 13.6512 20.03 13.5628C19.9748 13.4613 20.0042 13.2902 20.0629 12.9479L20.6194 9.703C20.6368 9.60183 20.6454 9.55125 20.6396 9.50284C20.6344 9.45998 20.6209 9.41854 20.5999 9.38081C20.5762 9.33821 20.5394 9.3024 20.4659 9.23078L18.1072 6.93342C17.8586 6.69129 17.7343 6.57022 17.7192 6.45569C17.706 6.35604 17.7385 6.25577 17.8077 6.1828C17.8871 6.09893 18.0588 6.07383 18.4022 6.02364L21.663 5.54702C21.7644 5.5322 21.8151 5.52479 21.8593 5.50425C21.8984 5.48607 21.9336 5.46048 21.963 5.42889C21.9961 5.39321 22.0188 5.34726 22.0641 5.25536L23.5219 2.30205Z" fill="#111111"/>
					<path d="M39.5219 2.30205C39.6756 1.99076 39.7524 1.83511 39.8567 1.78538C39.9475 1.74211 40.0529 1.74211 40.1436 1.78538C40.2479 1.83511 40.3248 1.99076 40.4784 2.30205L41.9362 5.25536C41.9816 5.34726 42.0042 5.39321 42.0374 5.42889C42.0667 5.46048 42.1019 5.48607 42.141 5.50425C42.1852 5.52479 42.2359 5.5322 42.3373 5.54702L45.5982 6.02364C45.9415 6.07383 46.1132 6.09893 46.1927 6.1828C46.2618 6.25577 46.2943 6.35604 46.2812 6.45569C46.266 6.57022 46.1417 6.69129 45.8931 6.93343L43.5345 9.23078C43.4609 9.3024 43.4242 9.33821 43.4004 9.38081C43.3794 9.41854 43.366 9.45998 43.3608 9.50284C43.3549 9.55125 43.3636 9.60183 43.3809 9.703L43.9375 12.9479C43.9962 13.2902 44.0255 13.4613 43.9704 13.5628C43.9224 13.6512 43.8371 13.7132 43.7382 13.7315C43.6246 13.7526 43.4709 13.6717 43.1636 13.5101L40.2484 11.9771C40.1576 11.9293 40.1122 11.9054 40.0643 11.896C40.022 11.8877 39.9784 11.8877 39.936 11.896C39.8882 11.9054 39.8428 11.9293 39.7519 11.9771L36.8368 13.5101C36.5294 13.6717 36.3758 13.7526 36.2621 13.7315C36.1633 13.7132 36.078 13.6512 36.03 13.5628C35.9748 13.4613 36.0042 13.2902 36.0629 12.9479L36.6194 9.703C36.6368 9.60183 36.6454 9.55125 36.6396 9.50284C36.6344 9.45998 36.6209 9.41854 36.5999 9.38081C36.5762 9.33821 36.5394 9.3024 36.4659 9.23078L34.1072 6.93342C33.8586 6.69129 33.7343 6.57022 33.7192 6.45569C33.706 6.35604 33.7385 6.25577 33.8077 6.1828C33.8871 6.09893 34.0588 6.07383 34.4022 6.02364L37.663 5.54702C37.7644 5.5322 37.8151 5.52479 37.8593 5.50425C37.8984 5.48607 37.9336 5.46048 37.963 5.42889C37.9961 5.39321 38.0188 5.34726 38.0641 5.25536L39.5219 2.30205Z" fill="#111111"/>
					<path d="M55.5219 2.30205C55.6756 1.99076 55.7524 1.83511 55.8567 1.78538C55.9475 1.74211 56.0529 1.74211 56.1436 1.78538C56.2479 1.83511 56.3248 1.99076 56.4784 2.30205L57.9362 5.25536C57.9816 5.34726 58.0042 5.39321 58.0374 5.42889C58.0667 5.46048 58.1019 5.48607 58.141 5.50425C58.1852 5.52479 58.2359 5.5322 58.3373 5.54702L61.5982 6.02364C61.9415 6.07383 62.1132 6.09893 62.1927 6.1828C62.2618 6.25577 62.2943 6.35604 62.2812 6.45569C62.266 6.57022 62.1417 6.69129 61.8931 6.93343L59.5345 9.23078C59.4609 9.3024 59.4242 9.33821 59.4004 9.38081C59.3794 9.41854 59.366 9.45998 59.3608 9.50284C59.3549 9.55125 59.3636 9.60183 59.3809 9.703L59.9375 12.9479C59.9962 13.2902 60.0255 13.4613 59.9704 13.5628C59.9224 13.6512 59.8371 13.7132 59.7382 13.7315C59.6246 13.7526 59.4709 13.6717 59.1636 13.5101L56.2484 11.9771C56.1576 11.9293 56.1122 11.9054 56.0643 11.896C56.022 11.8877 55.9784 11.8877 55.936 11.896C55.8882 11.9054 55.8428 11.9293 55.7519 11.9771L52.8368 13.5101C52.5294 13.6717 52.3758 13.7526 52.2621 13.7315C52.1633 13.7132 52.078 13.6512 52.03 13.5628C51.9748 13.4613 52.0042 13.2902 52.0629 12.9479L52.6194 9.703C52.6368 9.60183 52.6454 9.55125 52.6396 9.50284C52.6344 9.45998 52.6209 9.41854 52.5999 9.38081C52.5762 9.33821 52.5394 9.3024 52.4659 9.23078L50.1072 6.93342C49.8586 6.69129 49.7343 6.57022 49.7192 6.45569C49.706 6.35604 49.7385 6.25577 49.8077 6.1828C49.8871 6.09893 50.0588 6.07383 50.4022 6.02364L53.663 5.54702C53.7644 5.5322 53.8151 5.52479 53.8593 5.50425C53.8984 5.48607 53.9336 5.46048 53.963 5.42889C53.9961 5.39321 54.0188 5.34726 54.0641 5.25536L55.5219 2.30205Z" fill="#111111"/>
					<path d="M71.5219 2.30205C71.6756 1.99076 71.7524 1.83511 71.8567 1.78538C71.9475 1.74211 72.0529 1.74211 72.1436 1.78538C72.2479 1.83511 72.3248 1.99076 72.4784 2.30205L73.9362 5.25536C73.9816 5.34726 74.0042 5.39321 74.0374 5.42889C74.0667 5.46048 74.1019 5.48607 74.141 5.50425C74.1852 5.52479 74.2359 5.5322 74.3373 5.54702L77.5982 6.02364C77.9415 6.07383 78.1132 6.09893 78.1927 6.1828C78.2618 6.25577 78.2943 6.35604 78.2812 6.45569C78.266 6.57022 78.1417 6.69129 77.8931 6.93343L75.5345 9.23078C75.4609 9.3024 75.4242 9.33821 75.4004 9.38081C75.3794 9.41854 75.366 9.45998 75.3608 9.50284C75.3549 9.55125 75.3636 9.60183 75.3809 9.703L75.9375 12.9479C75.9962 13.2902 76.0255 13.4613 75.9704 13.5628C75.9224 13.6512 75.8371 13.7132 75.7382 13.7315C75.6246 13.7526 75.4709 13.6717 75.1636 13.5101L72.2484 11.9771C72.1576 11.9293 72.1122 11.9054 72.0643 11.896C72.022 11.8877 71.9784 11.8877 71.936 11.896C71.8882 11.9054 71.8428 11.9293 71.7519 11.9771L68.8368 13.5101C68.5294 13.6717 68.3758 13.7526 68.2621 13.7315C68.1633 13.7132 68.078 13.6512 68.03 13.5628C67.9748 13.4613 68.0042 13.2902 68.0629 12.9479L68.6194 9.703C68.6368 9.60183 68.6454 9.55125 68.6396 9.50284C68.6344 9.45998 68.6209 9.41854 68.5999 9.38081C68.5762 9.33821 68.5394 9.3024 68.4659 9.23078L66.1072 6.93342C65.8586 6.69129 65.7343 6.57022 65.7192 6.45569C65.706 6.35604 65.7385 6.25577 65.8077 6.1828C65.8871 6.09893 66.0588 6.07383 66.4022 6.02364L69.663 5.54702C69.7644 5.5322 69.8151 5.52479 69.8593 5.50425C69.8984 5.48607 69.9336 5.46048 69.963 5.42889C69.9961 5.39321 70.0188 5.34726 70.0641 5.25536L71.5219 2.30205Z" fill="#111111"/>
				</svg>
				<div class="product-comment__product-images">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-3.webp' ?>" alt="product 2" class="product-comment__product-image">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-3.webp' ?>" alt="product 2" class="product-comment__product-image">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-3.webp' ?>" alt="product 2" class="product-comment__product-image">
				</div>
			</li>

		</ol>
	</section>







	
<?php
get_footer('swap');