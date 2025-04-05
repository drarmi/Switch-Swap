<?php
/* Template Name: Home Page */

get_header('swap');
?>
	<nav class="home-nav">
		<?php
			wp_nav_menu( array(
				'theme_location'	=> 'secondary',
				'menu_class'		=> 'home-menu',
				'container'         => 'ul',
				'depth'				=> 1,
			) );
		?>
	</nav>
	
	<section class="banner section-block">
		<button class="banner__close-btn">
			<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M9 1L1 9M9 9L1 1" stroke="black" stroke-width="1.5" stroke-linecap="round"/>
			</svg>
		</button>
		<a href="#" class="banner__link-wrap">
			<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/banner/banner-1.webp' ?>" alt="Step 1">
		</a>
	</section>

	<section class="section-block how-it-works-section">
		<div class="section-heading section-heading--space">
			<h2 class="section-heading__title section-heading__title--small">איך זה עובד?</h2>
			<a href="#" class="section-heading__link">לפרטים נוספים</a>
		</div>
		<ul class="how-it-works">
			<li class="how-it-works__step">
				<div class="how-it-works__icon">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/how-it-works/icon-user.svg' ?>" alt="Step 1">
				</div>
				יצירת פרופיל
				<br>וניהול חנות
			</li>
			<li class="how-it-works__step">
				<div class="how-it-works__icon">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/how-it-works/icon-cart.svg' ?>" alt="Step 2">
				</div>
				קנייה והשכרה
				<br>של פריטים
			</li>
			<li class="how-it-works__step">
				<div class="how-it-works__icon">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/how-it-works/icon-transaction.svg' ?>" alt="Step 3">
				</div>
				עסקה בטוחה
				<br>ונוחה
			</li>
			<li class="how-it-works__step">
				<div class="how-it-works__icon">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/how-it-works/icon-clean.svg' ?>" alt="Step 4">
				</div>
				ניקוי יבש
				<br>והחזרת פריט
			</li>
		</ul>
	</section>

	<section class="banner section-block">
		<button class="banner__close-btn">
			<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M9 1L1 9M9 9L1 1" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
			</svg>
		</button>	
		<a href="#" class="banner__link-wrap">
			<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/banner/banner-2.webp' ?>" alt="Step 1">
		</a>	
	</section>

	<section class="section-block product-cats-section">
		<div class="section-heading">
			<h2 class="section-heading__title">קטגוריות</h2>
		</div>
		<div class="product-cats">
			<a class="product-cat" href="#">
				<div class="product-cat__icon-wrap">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/cats/cat-1.svg' ?>" alt="חופשה" class="product-cat__icon">
				</div>
				חופשה
			</a>
			<a class="product-cat" href="#">
				<div class="product-cat__icon-wrap">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/cats/cat-2.svg' ?>" alt="ערב" class="product-cat__icon">
				</div>
				ערב
			</a>
			<a class="product-cat" href="#">
				<div class="product-cat__icon-wrap">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/cats/cat-3.svg' ?>" alt="פסטיבל" class="product-cat__icon">
				</div>
				פסטיבל
			</a>
			<a class="product-cat" href="#">
				<div class="product-cat__icon-wrap">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/cats/cat-4.svg' ?>" alt="מסיבה" class="product-cat__icon">
				</div>
				מסיבה
			</a>
			<a class="product-cat" href="#">
				<div class="product-cat__icon-wrap">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/cats/cat-5.svg' ?>" alt="עבודה" class="product-cat__icon">
				</div>
				עבודה
			</a>
			<a class="product-cat" href="#">
				<div class="product-cat__icon-wrap">
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/cats/cat-6.svg' ?>" alt="חתונה" class="product-cat__icon">
				</div>
				חתונה
			</a>
		</div>
	</section>

	<!--section class="hot-sellers">
		<div class="section-heading">
			<h2 class="section-heading__title">המוכרים החמים</h2>
		</div>
		<ul class="sellers-list sellers-list--swipe">
			<li class="seller">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-1.webp' ?>" alt="product 1" class="product-cat__icon">
				<h2>Miss נויה</h2>
				<a class="seller__tag" href="#">מוביל/ה</a>
				<p class="stat">
					<span>87 פריטים</span>
					<span>512 ביקורות</span>
				</p>
			</li>
			<li class="seller">
				<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-2.webp' ?>" alt="product 2" class="product-cat__icon">
				<h2>Miss נויה</h2>
				<a class="seller__tag" href="#">מוביל/ה</a>
				<span class="single-star-rating">5</span>
				<p class="stat">
					<span>87 פריטים</span>
					<span>512 ביקורות</span>
				</p>
			</li>
		</ul>
	</section-->

	<section class="swap-products-section section-block">
		<div class="section-heading">
			<h2 class="section-heading__title">המוכרים החמים</h2>
		</div>		
		<div class="swap-products">
			<div class="swap-product">
				<a class="swap-product__media" href="#">
					<span class="swap-product__tag">חדש</span>
					<button class="swap-product__like">
						<svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M9.49413 3.27985C7.828 1.332 5.04963 0.808035 2.96208 2.59168C0.87454 4.37532 0.580644 7.35748 2.22 9.467C3.58302 11.2209 7.70798 14.9201 9.05992 16.1174C9.21117 16.2513 9.2868 16.3183 9.37502 16.3446C9.45201 16.3676 9.53625 16.3676 9.61325 16.3446C9.70146 16.3183 9.77709 16.2513 9.92834 16.1174C11.2803 14.9201 15.4052 11.2209 16.7683 9.467C18.4076 7.35748 18.1496 4.35656 16.0262 2.59168C13.9028 0.826798 11.1603 1.332 9.49413 3.27985Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
						</svg>
					</button>
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-4.webp' ?>" alt="product 1" class="swap-product__image">
				</a>
				<div class="swap-product__details">
					<h3 class="swap-product__title">Jacquemus bucket hat</h3>
					<p class="swap-product__desc">כובע באקט בצבע בז׳ עם סגירת חוטי קשירה במידה אחת לשליחה מיידית</p>
					<p class="swap-product__rent"><span>השכרה</span><span>החל מ-</span><span>39 ₪</span></p>
					<p class="swap-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="swap-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
			<div class="swap-product">
				<a class="swap-product__media" href="#">
					<span class="swap-product__tag">חדש</span>
					<button class="swap-product__like">
						<svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M9.49413 3.27985C7.828 1.332 5.04963 0.808035 2.96208 2.59168C0.87454 4.37532 0.580644 7.35748 2.22 9.467C3.58302 11.2209 7.70798 14.9201 9.05992 16.1174C9.21117 16.2513 9.2868 16.3183 9.37502 16.3446C9.45201 16.3676 9.53625 16.3676 9.61325 16.3446C9.70146 16.3183 9.77709 16.2513 9.92834 16.1174C11.2803 14.9201 15.4052 11.2209 16.7683 9.467C18.4076 7.35748 18.1496 4.35656 16.0262 2.59168C13.9028 0.826798 11.1603 1.332 9.49413 3.27985Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
						</svg>
					</button>
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-5.webp' ?>" alt="product 1" class="swap-product__image">
				</a>
				<div class="swap-product__details">
					<h3 class="swap-product__title">Jacquemus bucket hat</h3>
					<p class="swap-product__desc">כובע באקט בצבע בז׳ עם סגירת חוטי קשירה במידה אחת לשליחה מיידית</p>
					<p class="swap-product__rent"><span>השכרה</span> <span>החל מ-</span> <span>39 ₪ </span></p>
					<p class="swap-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="swap-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
		</div>
	</section>

	<section class="rent-by-budget-section section-block">
		<div class="section-heading">
			<h2 class="section-heading__title">השכרה לפי תקציב</h2>
		</div>
		<ul class="rent-by-budget__list">
			<li class="rent-by-budget__list-item"><sup>עד</sup>50<sub>₪</sub></li>
			<li class="rent-by-budget__list-item"><sup>עד</sup>100<sub>₪</sub></li>
			<li class="rent-by-budget__list-item"><sup>עד</sup>300<sub>₪</sub></li>
		</ul>
	</section>

	<section class="swap-products-section section-block">
		<div class="section-heading">
			<h2 class="section-heading__title">מוצרים חדשים</h2>
		</div>		
		<div class="swap-products">
			<div class="swap-product">
				<a class="swap-product__media" href="#">
					<span class="swap-product__tag">חדש</span>
					<button class="swap-product__like">
						<svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M9.49413 3.27985C7.828 1.332 5.04963 0.808035 2.96208 2.59168C0.87454 4.37532 0.580644 7.35748 2.22 9.467C3.58302 11.2209 7.70798 14.9201 9.05992 16.1174C9.21117 16.2513 9.2868 16.3183 9.37502 16.3446C9.45201 16.3676 9.53625 16.3676 9.61325 16.3446C9.70146 16.3183 9.77709 16.2513 9.92834 16.1174C11.2803 14.9201 15.4052 11.2209 16.7683 9.467C18.4076 7.35748 18.1496 4.35656 16.0262 2.59168C13.9028 0.826798 11.1603 1.332 9.49413 3.27985Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
						</svg>
					</button>
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-1.webp' ?>" alt="product 1" class="swap-product__image">
				</a>
				<div class="swap-product__details">
					<h3 class="swap-product__title">Jacquemus bucket hat</h3>
					<p class="swap-product__desc">כובע באקט בצבע בז׳ עם סגירת חוטי קשירה במידה אחת לשליחה מיידית</p>
					<p class="swap-product__rent"><span>השכרה</span><span>החל מ-</span><span>39 ₪</span></p>
					<p class="swap-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="swap-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
			<div class="swap-product">
				<a class="swap-product__media" href="#">
					<span class="swap-product__tag">חדש</span>
					<button class="swap-product__like">
						<svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M9.49413 3.27985C7.828 1.332 5.04963 0.808035 2.96208 2.59168C0.87454 4.37532 0.580644 7.35748 2.22 9.467C3.58302 11.2209 7.70798 14.9201 9.05992 16.1174C9.21117 16.2513 9.2868 16.3183 9.37502 16.3446C9.45201 16.3676 9.53625 16.3676 9.61325 16.3446C9.70146 16.3183 9.77709 16.2513 9.92834 16.1174C11.2803 14.9201 15.4052 11.2209 16.7683 9.467C18.4076 7.35748 18.1496 4.35656 16.0262 2.59168C13.9028 0.826798 11.1603 1.332 9.49413 3.27985Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
						</svg>
					</button>
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-2.webp' ?>" alt="product 1" class="swap-product__image">
				</a>
				<div class="swap-product__details">
					<h3 class="swap-product__title">Jacquemus bucket hat</h3>
					<p class="swap-product__desc">כובע באקט בצבע בז׳ עם סגירת חוטי קשירה במידה אחת לשליחה מיידית</p>
					<p class="swap-product__rent"><span>השכרה</span> <span>החל מ-</span> <span>39 ₪ </span></p>
					<p class="swap-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="swap-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
		</div>
	</section>

	<section class="swap-products-section section-block">
		<div class="section-heading section-heading--center">
			<h2 class="section-heading__title">מוצרים חדשים</h2>
		</div>		
		<div class="swap-products">
			<div class="swap-product">
				<a class="swap-product__media" href="#">
					<span class="swap-product__tag">חדש</span>
					<button class="swap-product__like swap-products__like--active">
						<svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M9.49413 3.27985C7.828 1.332 5.04963 0.808035 2.96208 2.59168C0.87454 4.37532 0.580644 7.35748 2.22 9.467C3.58302 11.2209 7.70798 14.9201 9.05992 16.1174C9.21117 16.2513 9.2868 16.3183 9.37502 16.3446C9.45201 16.3676 9.53625 16.3676 9.61325 16.3446C9.70146 16.3183 9.77709 16.2513 9.92834 16.1174C11.2803 14.9201 15.4052 11.2209 16.7683 9.467C18.4076 7.35748 18.1496 4.35656 16.0262 2.59168C13.9028 0.826798 11.1603 1.332 9.49413 3.27985Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
						</svg>
					</button>
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-3.webp' ?>" alt="product 1" class="swap-product__image">
				</a>
				<div class="swap-product__details">
					<h3 class="swap-product__title">Jacquemus bucket hat</h3>
					<p class="swap-product__desc">כובע באקט בצבע בז׳ עם סגירת חוטי קשירה במידה אחת לשליחה מיידית</p>
					<p class="swap-product__rent"><span>השכרה</span> <span>החל מ-</span> <span>39 ₪ </span></p>
					<p class="swap-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="swap-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
			<div class="swap-product">
				<a class="swap-product__media" href="#">
					<span class="swap-product__tag">חדש</span>
					<button class="swap-product__like">
						<svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M9.49413 3.27985C7.828 1.332 5.04963 0.808035 2.96208 2.59168C0.87454 4.37532 0.580644 7.35748 2.22 9.467C3.58302 11.2209 7.70798 14.9201 9.05992 16.1174C9.21117 16.2513 9.2868 16.3183 9.37502 16.3446C9.45201 16.3676 9.53625 16.3676 9.61325 16.3446C9.70146 16.3183 9.77709 16.2513 9.92834 16.1174C11.2803 14.9201 15.4052 11.2209 16.7683 9.467C18.4076 7.35748 18.1496 4.35656 16.0262 2.59168C13.9028 0.826798 11.1603 1.332 9.49413 3.27985Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
						</svg>
					</button>
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-4.webp' ?>" alt="product 1" class="swap-product__image">
				</a>
				<div class="swap-product__details">
					<h3 class="swap-product__title">Jacquemus bucket hat</h3>
					<p class="swap-product__desc">כובע באקט בצבע בז׳ עם סגירת חוטי קשירה במידה אחת לשליחה מיידית</p>
					<p class="swap-product__rent"><span>השכרה</span><span>החל מ-</span><span>39 ₪ </span></p>
					<p class="swap-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="swap-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
			<div class="swap-product">
				<a class="swap-product__media" href="#">
					<span class="swap-product__tag">חדש</span>
					<button class="swap-product__like">
						<svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M9.49413 3.27985C7.828 1.332 5.04963 0.808035 2.96208 2.59168C0.87454 4.37532 0.580644 7.35748 2.22 9.467C3.58302 11.2209 7.70798 14.9201 9.05992 16.1174C9.21117 16.2513 9.2868 16.3183 9.37502 16.3446C9.45201 16.3676 9.53625 16.3676 9.61325 16.3446C9.70146 16.3183 9.77709 16.2513 9.92834 16.1174C11.2803 14.9201 15.4052 11.2209 16.7683 9.467C18.4076 7.35748 18.1496 4.35656 16.0262 2.59168C13.9028 0.826798 11.1603 1.332 9.49413 3.27985Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
						</svg>
					</button>
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-1.webp' ?>" alt="product 1" class="swap-product__image">
				</a>
				<div class="swap-product__details">
					<h3 class="swap-product__title">Loewe תיק פייטיםs</h3>
					<p class="swap-product__desc">כובע באקט בצבע בז׳ עם סגירת חוטי קשירה במידה אחת לשליחה מיידית</p>
					<p class="swap-product__rent"><span>השכרה</span><span>החל מ-</span><span>39 ₪ </span></p>
					<p class="swap-product__amount"><span>קנייה מיידית</span><del>829 ₪</del><ins>620 ₪</ins></p>
					<p class="swap-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>
			<div class="swap-product">
				<a class="swap-product__media" href="#">
					<span class="swap-product__tag">חדש</span>
					<button class="swap-product__like">
						<svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M9.49413 3.27985C7.828 1.332 5.04963 0.808035 2.96208 2.59168C0.87454 4.37532 0.580644 7.35748 2.22 9.467C3.58302 11.2209 7.70798 14.9201 9.05992 16.1174C9.21117 16.2513 9.2868 16.3183 9.37502 16.3446C9.45201 16.3676 9.53625 16.3676 9.61325 16.3446C9.70146 16.3183 9.77709 16.2513 9.92834 16.1174C11.2803 14.9201 15.4052 11.2209 16.7683 9.467C18.4076 7.35748 18.1496 4.35656 16.0262 2.59168C13.9028 0.826798 11.1603 1.332 9.49413 3.27985Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
						</svg>
					</button>
					<img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-2.webp' ?>" alt="product 1" class="swap-product__image">
				</a>
				<div class="swap-product__details">
					<h3 class="swap-product__title">Jacquemus bucket hat</h3>
					<p class="swap-product__desc">כובע באקט בצבע בז׳ עם סגירת חוטי קשירה במידה אחת לשליחה מיידית</p>
					<p class="swap-product__rent"><span>השכרה</span> <span>החל מ-</span> <span>39 ₪ </span></p>
					<p class="swap-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
					<p class="swap-product__proposals-allowed">ניתן להגיש הצעות</p>
				</div>
			</div>

		</div>
	</section>

<?php
get_footer('swap');