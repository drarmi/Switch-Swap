<?php
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

redirectProductNotAvailable(get_the_ID());

use Omnis\src\inc\classes\pages\Product_Management;

get_header('swap'); ?>

<?php
/**
 * woocommerce_before_main_content hook.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 */
do_action('woocommerce_before_main_content');
?>

<?php while (have_posts()) :
	the_post();

	$product_id = get_the_ID();
	$author_id = get_post_field('post_author', $product_id);
	$user_all_comments_data = Product_Management::getUserCommentsData($author_id);
	$relatedProducts = Product_Management::getRelatedProducts($product_id);

	$args_comments = array(
		'post__in' => $relatedProducts,
		'status'   => 'approve',
	);
	
	$comments = get_comments($args_comments);

	$comment_count = $user_all_comments_data['total_reviews'] ?? 0;

	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'author'         => $author_id,
		'posts_per_page' => -1,
		'fields'         => 'ids',
	);

	$products_query = get_posts($args);
	$product_count = count($products_query);

	$user_info = get_userdata($author_id);

	$user_star_arr = [
		5 => $user_all_comments_data['rating_5'] ?? 0,
		4 => $user_all_comments_data['rating_4'] ?? 0,
		3 => $user_all_comments_data['rating_3'] ?? 0,
		2 => $user_all_comments_data['rating_2'] ?? 0,
		1 => $user_all_comments_data['rating_1'] ?? 0,
	];

	$user_star = !empty($user_all_comments_data['avg_rating']) && $user_all_comments_data['avg_rating'] > 0 ? round($user_all_comments_data['avg_rating'], 1) : 0;
	$rating_title = get_rating_title($user_star ?? null);
	$dokan_settings = dokan_get_store_info($author_id);

	$first_name = $user_info->first_name;
	$last_name = $user_info->last_name;
	$author_logo = get_user_meta($author_id, 'user_logo', true);

	if (!$author_logo) {
		$author_logo = get_avatar_url($author_id);
	}

	$product = wc_get_product($product_id) ?? null;


	$upsells = $product && $product->get_upsell_ids() ? $product->get_upsell_ids() : $products_query;
	$cross_sells = $product &&$product->get_cross_sell_ids() ? $product->get_cross_sell_ids() : $products_query;


	echo "<div class='product-custom-wrapper'>";
	get_template_part("woocommerce/single-product/product", "nav", ["ID" => $product_id]);
	get_template_part("woocommerce/single-product/product", "slider",  ["ID" => $product_id]);
	get_template_part("woocommerce/single-product/product", "title", ["first_name" => $first_name, "last_name" => $last_name, "author_logo" => $author_logo, "comment_count" => $comment_count, "product_count" => $product_count, "ID" => $product_id, "user_star" => $user_star, "rating_title" => $rating_title]);
	get_template_part("woocommerce/single-product/product", "buy", ["ID" => $product_id, "author_id" => $author_id,]);
	get_template_part("woocommerce/single-product/product", "about", ["ID" => $product_id]);
	get_template_part("woocommerce/single-product/product", "author", ["first_name" => $first_name, "last_name" => $last_name, "author_logo" => $author_logo, "product_count" => $product_count, "ID" => $product_id, "user_star" => $user_star, "rating_title" => $rating_title]);
	get_template_part("woocommerce/single-product/product", "more", ["query" => $products_query, "first_name" => $first_name]);
	get_template_part("woocommerce/single-product/product", "comment", ["ID" => $product_id]);
	get_template_part("woocommerce/single-product/product", "questions");
	get_template_part("woocommerce/single-product/product", "upsells", ["variation" => $upsells, "title" => __("מוצרים משלימים ללוק", "omnis_base"),]);
	get_template_part("woocommerce/single-product/product", "upsells", ["variation" => $cross_sells, "title" => __("מוצרים דומים נוספים ", "swap")]);
	echo "</div>";

endwhile;
?>

<?php
/**
 * woocommerce_after_main_content hook.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('woocommerce_after_main_content');
?>

<?php
/**
 * woocommerce_sidebar hook.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action('woocommerce_sidebar');
?>

<?php
get_footer('swap');
