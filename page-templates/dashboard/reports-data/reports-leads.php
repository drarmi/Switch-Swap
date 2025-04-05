

<?php

get_header('profile');


add_action('wp_ajax_get_top_booked_products_ajax', 'handle_get_top_booked_products_ajax');
add_action('wp_ajax_nopriv_get_top_booked_products_ajax', 'handle_get_top_booked_products_ajax');

function handle_get_top_booked_products_ajax() {
	if (!isset($_POST['filter_type'])) {
		wp_send_json_error();
	}

	$filter_type = sanitize_text_field($_POST['filter_type']);

	$from = null;
	$to = null;
	$prevFrom = null;
	$prevTo = null;

	switch ($filter_type) {
		case 'month':
			$from = new DateTime('first day of this month');
			$to = new DateTime('now');
			break;
		case 'quarter':
			$currentDate = new DateTime();
			$currentMonth = (int)$currentDate->format('n');
			$currentYear = (int)$currentDate->format('Y');
			$quarter = ceil($currentMonth / 3);
			$startMonth = ($quarter - 1) * 3 + 1;
			$from = new DateTime("$currentYear-$startMonth-01");
			$to = new DateTime('now');
			break;
		case 'year':
			$from = new DateTime(date('Y-01-01'));
			$to = new DateTime('now');
			break;
		default:
			wp_send_json_error('Invalid filter type');
	}

	$filters = new \Omnis\src\inc\classes\dashboard\Dashboard_Filters($from, $to, null, null);
	$vendor = new \Omnis\src\inc\classes\dashboard\Dashboard_Vendor();
	$products = $vendor->getTopBookedProducts($filters);

	// Перетвори ID зображення на URL
	foreach ($products as &$product) {
		$product['product_image'] = wp_get_attachment_image_url($product['product_image'], 'medium');
	}

	wp_send_json_success($products);
} ?>




<div class="filter-buttons">
	<button class="filter-button swap-tab-button active" data-filter="month">חודשי</button>
	<button class="filter-button swap-tab-button" data-filter="quarter">רבעוני</button>
	<button class="filter-button swap-tab-button" data-filter="year">שנתי</button>
</div>

<div class="dashboard-products-container">
	<p>טוען...</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	const buttons = document.querySelectorAll('.filter-button');
	const container = document.querySelector('.dashboard-products-container');

	buttons.forEach(btn => {
		btn.addEventListener('click', function () {
			buttons.forEach(b => b.classList.remove('active'));
			this.classList.add('active');

			const filter = this.dataset.filter;

			container.innerHTML = '<p>טוען...</p>';

			fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: new URLSearchParams({
					action: 'get_top_booked_products_ajax',
					filter_type: filter
				})
			})
			.then(res => res.json())
			.then(data => {
				if (data.success && data.data.length > 0) {
					container.innerHTML = data.data.map(product => `
						<div class="dashboard-product">
							<img src="${product.product_image}" alt="${product.product_name}" />
							<div class="profile-product__details">
								<h3 class="profile-product__title">${product.product_name}</h3>
								<p class="profile-product__rent">השכרות: ${product.product_count}</p>
							</div>
						</div>
					`).join('');
				} else {
					container.innerHTML = '<p>אין נתונים להצגה</p>';
				}
			});
		});
	});
});
</script>




















<h1 class="profile-page-title">דוחות ונתונים</h1>
<ul class="dashboard-primary-nav">
    <li><a href="http://swap.loc/dashboard/reports-data">הצג הכל</a></li>
    <li><a href="http://swap.loc/dashboard/reports-data/movements/">תנועות</a></li>
    <li><a href="http://swap.loc/dashboard/reports-data/credit/">קרדיט</a></li>
    <li><a href="http://swap.loc/dashboard/reports-data/rating/">דירוג</a></li>
    <li><a href="http://swap.loc/dashboard/reports-data/leads/" class="active">המובילים</a></li>
</ul>




<div class="swap-tabs">
    <div class="swap-tab-buttons diagram-switch">
        <button class="swap-tab-button active" data-tab="tab1">חודשי</button>
        <button class="swap-tab-button" data-tab="tab2">רבעוני</button>
        <button class="swap-tab-button" data-tab="tab3">שנתי</button>
    </div>
    <div class="swap-tab-content">
        <div class="swap-tab-panel active" id="tab1">

                    <section class="dashboard__top-sales">
                    <div class="dashboard__section-heading">
                        <h2 class="dashboard__section-title">המוצרים הכי נמכרים</h2>
                    </div>
                    <div class="dashboard-products dashboard-products--swipe">
                        <div class="dashboard-product">
                            <a class="dashboard-product__media" href="https://swap.madebyomnis.com/product/%d7%a9%d7%9e%d7%9c%d7%aa-%d7%a2%d7%a8%d7%91-%d7%96%d7%90%d7%a8%d7%94-1/">
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
                            <a class="dashboard-product__media" href="https://swap.madebyomnis.com/product/%d7%a9%d7%9e%d7%9c%d7%aa-%d7%a2%d7%a8%d7%91-%d7%96%d7%90%d7%a8%d7%94-1/">
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
                            <a class="dashboard-product__media" href="https://swap.madebyomnis.com/product/%d7%a9%d7%9e%d7%9c%d7%aa-%d7%a2%d7%a8%d7%91-%d7%96%d7%90%d7%a8%d7%94-1/">
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
                            <a class="dashboard-product__media" href="https://swap.madebyomnis.com/product/%d7%a9%d7%9e%d7%9c%d7%aa-%d7%a2%d7%a8%d7%91-%d7%96%d7%90%d7%a8%d7%94-1/">
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
                            <span>עלייה/16<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.95631 5.89855V14.9033C7.95631 15.4556 8.40403 15.9033 8.95631 15.9033H9.04369C9.59597 15.9033 10.0437 15.4556 10.0437 14.9033V5.89855L13.8171 9.67199C14.2076 10.0625 14.8408 10.0625 15.2313 9.67199L15.2929 9.61043C15.6834 9.2199 15.6834 8.58674 15.2929 8.19621L9.70711 2.61043C9.31658 2.2199 8.68342 2.2199 8.29289 2.61043L2.70711 8.19621C2.31658 8.58674 2.31658 9.2199 2.70711 9.61043L2.76866 9.67198C3.15919 10.0625 3.79235 10.0625 4.18288 9.67199L7.95631 5.89855Z" fill="#C7A77F"/>
                            <path d="M16 10.3175L16 10.3175C16.781 9.53648 16.781 8.27016 16 7.48911L10.4142 1.90332C9.63316 1.12227 8.36683 1.12227 7.58579 1.90332L2 7.48911C1.21895 8.27016 1.21895 9.53649 2 10.3175L2.06156 10.3791C2.84261 11.1601 4.10894 11.1601 4.88998 10.3791L6.95631 8.31276V14.9033C6.95631 16.0079 7.85174 16.9033 8.95631 16.9033H9.04369C10.1483 16.9033 11.0437 16.0079 11.0437 14.9033V8.31276L13.11 10.3791C13.8911 11.1601 15.1574 11.1601 15.9384 10.3791C15.9384 10.3791 15.9384 10.3791 15.9384 10.3791L16 10.3175Z" stroke="#C69F7C" stroke-opacity="0.2" stroke-width="2"/>
                            <path d="M7.95631 5.89855V14.9033C7.95631 15.4556 8.40403 15.9033 8.95631 15.9033H9.04369C9.59597 15.9033 10.0437 15.4556 10.0437 14.9033V5.89855L13.8171 9.67199C14.2076 10.0625 14.8408 10.0625 15.2313 9.67199L15.2929 9.61043C15.6834 9.2199 15.6834 8.58674 15.2929 8.19621L9.70711 2.61043C9.31658 2.2199 8.68342 2.2199 8.29289 2.61043L2.70711 8.19621C2.31658 8.58674 2.31658 9.2199 2.70711 9.61043L2.76866 9.67198C3.15919 10.0625 3.79235 10.0625 4.18288 9.67199L7.95631 5.89855Z" fill="#C7A77F"/>
                            </svg></span>
                        </h2>
                    </div>
                    <div class="dashboard__buyer-profiles">
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/store-image.jpg' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-1.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-2.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-4.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/store-image.jpg' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-1.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-2.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-4.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                    </div>
                </section>

        </div>
        <div class="swap-tab-panel" id="tab2">

        <section class="dashboard__top-sales">
                    <div class="dashboard__section-heading">
                        <h2 class="dashboard__section-title">המוצרים הכי נמכרים</h2>
                    </div>
                    <div class="dashboard-products dashboard-products--swipe">
                        <div class="dashboard-product">
                            <a class="dashboard-product__media" href="https://swap.madebyomnis.com/product/%d7%a9%d7%9e%d7%9c%d7%aa-%d7%a2%d7%a8%d7%91-%d7%96%d7%90%d7%a8%d7%94-1/">
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
                            <a class="dashboard-product__media" href="https://swap.madebyomnis.com/product/%d7%a9%d7%9e%d7%9c%d7%aa-%d7%a2%d7%a8%d7%91-%d7%96%d7%90%d7%a8%d7%94-1/">
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
                            <a class="dashboard-product__media" href="https://swap.madebyomnis.com/product/%d7%a9%d7%9e%d7%9c%d7%aa-%d7%a2%d7%a8%d7%91-%d7%96%d7%90%d7%a8%d7%94-1/">
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
                            <a class="dashboard-product__media" href="https://swap.madebyomnis.com/product/%d7%a9%d7%9e%d7%9c%d7%aa-%d7%a2%d7%a8%d7%91-%d7%96%d7%90%d7%a8%d7%94-1/">
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
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/store-image.jpg' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>

                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-2.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-4.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/store-image.jpg' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-1.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-2.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-4.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                    </div>
                </section>

        </div>
        <div class="swap-tab-panel" id="tab3">
        <section class="dashboard__top-sales">
                    <div class="dashboard__section-heading">
                        <h2 class="dashboard__section-title">המוצרים הכי נמכרים</h2>
                    </div>
                    <div class="dashboard-products dashboard-products--swipe">
                        <div class="dashboard-product">
                            <a class="dashboard-product__media" href="https://swap.madebyomnis.com/product/%d7%a9%d7%9e%d7%9c%d7%aa-%d7%a2%d7%a8%d7%91-%d7%96%d7%90%d7%a8%d7%94-1/">
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
                            <a class="dashboard-product__media" href="https://swap.madebyomnis.com/product/%d7%a9%d7%9e%d7%9c%d7%aa-%d7%a2%d7%a8%d7%91-%d7%96%d7%90%d7%a8%d7%94-1/">
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
                            <a class="dashboard-product__media" href="https://swap.madebyomnis.com/product/%d7%a9%d7%9e%d7%9c%d7%aa-%d7%a2%d7%a8%d7%91-%d7%96%d7%90%d7%a8%d7%94-1/">
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
                            <a class="dashboard-product__media" href="https://swap.madebyomnis.com/product/%d7%a9%d7%9e%d7%9c%d7%aa-%d7%a2%d7%a8%d7%91-%d7%96%d7%90%d7%a8%d7%94-1/">
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
                            <span>עלייה/16<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.95631 5.89855V14.9033C7.95631 15.4556 8.40403 15.9033 8.95631 15.9033H9.04369C9.59597 15.9033 10.0437 15.4556 10.0437 14.9033V5.89855L13.8171 9.67199C14.2076 10.0625 14.8408 10.0625 15.2313 9.67199L15.2929 9.61043C15.6834 9.2199 15.6834 8.58674 15.2929 8.19621L9.70711 2.61043C9.31658 2.2199 8.68342 2.2199 8.29289 2.61043L2.70711 8.19621C2.31658 8.58674 2.31658 9.2199 2.70711 9.61043L2.76866 9.67198C3.15919 10.0625 3.79235 10.0625 4.18288 9.67199L7.95631 5.89855Z" fill="#C7A77F"/>
                            <path d="M16 10.3175L16 10.3175C16.781 9.53648 16.781 8.27016 16 7.48911L10.4142 1.90332C9.63316 1.12227 8.36683 1.12227 7.58579 1.90332L2 7.48911C1.21895 8.27016 1.21895 9.53649 2 10.3175L2.06156 10.3791C2.84261 11.1601 4.10894 11.1601 4.88998 10.3791L6.95631 8.31276V14.9033C6.95631 16.0079 7.85174 16.9033 8.95631 16.9033H9.04369C10.1483 16.9033 11.0437 16.0079 11.0437 14.9033V8.31276L13.11 10.3791C13.8911 11.1601 15.1574 11.1601 15.9384 10.3791C15.9384 10.3791 15.9384 10.3791 15.9384 10.3791L16 10.3175Z" stroke="#C69F7C" stroke-opacity="0.2" stroke-width="2"/>
                            <path d="M7.95631 5.89855V14.9033C7.95631 15.4556 8.40403 15.9033 8.95631 15.9033H9.04369C9.59597 15.9033 10.0437 15.4556 10.0437 14.9033V5.89855L13.8171 9.67199C14.2076 10.0625 14.8408 10.0625 15.2313 9.67199L15.2929 9.61043C15.6834 9.2199 15.6834 8.58674 15.2929 8.19621L9.70711 2.61043C9.31658 2.2199 8.68342 2.2199 8.29289 2.61043L2.70711 8.19621C2.31658 8.58674 2.31658 9.2199 2.70711 9.61043L2.76866 9.67198C3.15919 10.0625 3.79235 10.0625 4.18288 9.67199L7.95631 5.89855Z" fill="#C7A77F"/>
                            </svg></span>
                        </h2>
                    </div>
                    <div class="dashboard__buyer-profiles">
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/store-image.jpg' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-1.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-2.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-4.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/store-image.jpg' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-1.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-2.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-4.webp' ?>" alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            <h3 class="dashboard__buyer-profile-name">שירה_95</h3>
                            <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                            <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                        </div>
                    </div>
                </section>
        </div>
        <div class="swap-tab-panel" id="tab4">

        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", () => {
    const tabButtons = document.querySelectorAll(".swap-tab-button");
    const tabPanels = document.querySelectorAll(".swap-tab-panel");

  tabButtons.forEach(button => {
    button.addEventListener("click", () => {
      // Remove active class from all buttons and panels
      tabButtons.forEach(btn => btn.classList.remove("active"));
      tabPanels.forEach(panel => panel.classList.remove("active"));

      // Add active class to the clicked button and corresponding panel
      button.classList.add("active");
      const tabId = button.getAttribute("data-tab");
      document.getElementById(tabId).classList.add("active");
    });
  });
});
</script>



















