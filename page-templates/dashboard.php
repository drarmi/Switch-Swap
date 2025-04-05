<?php
/* Template Name: Dashboard */

//get_header('profile');
?>
<h1 class="profile-page-title">דוחות ונתונים</h1>
<ul class="dashboard-primary-nav">
    <li><a href="#" class="active">הצג הכל</a></li>
    <li><a href="#">תנועות</a></li>
    <li><a href="#">קרדיט</a></li>
    <li><a href="#">דירוג</a></li>
    <li><a href="#">המובילים</a></li>
</ul>
<ul class="dashboard-secondary-nav">
    <li><a href="#" class="active">חודשי</a></li>
    <li><a href="#">רבעוני</a></li>
    <li><a href="#">שנתי</a></li>
    <li><a href="#">טווח תאריכים</a></li>
</ul>

<section class="all-charts">
    <div>
        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/charts/placeholder-chart-1.png' ?>" alt="chart 1">
    </div>
    <div>
        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/charts/placeholder-chart-2.png' ?>" alt="chart 2">
    </div>
    <div>
        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/charts/placeholder-chart-3.png' ?>" alt="chart 3">
    </div>
    <div>
        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/charts/placeholder-chart-4.png' ?>" alt="chart 4">
    </div>
</section>

<section class="dashboard__best-sellers">
    <div class="dashboard__section-heading">
        <h2 class="dashboard__section-title">המוצרים הכי נמכרים</h2>
    </div>

    <div class="dashboard-products dashboard-products--swipe">
        <div class="dashboard-product">
            <a class="dashboard-product__media" href="#">
                <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-4.webp' ?>" alt="product 1" class="dashboard-product__image">
            </a>
            <div class="profile-product__details">
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
                <h3 class="profile-product__title">Jacquemus bucket hat</h3>
                <p class="profile-product__rent"><span>השכרה</span> <span>החל מ-</span> <span>39 ₪ </span></p>
                <p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
                <p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
            </div>
        </div>
    </div>

</section>

<section class="dashboard__top-customers">
    <div class="dashboard__section-heading">
        <h2 class="dashboard__section-title">לקוחות מובילים</h2>
    </div>


    <div class="dashboard-products dashboard-products--swipe">
        <div class="dashboard-product">
            <a class="dashboard-product__media" href="#">
                <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-4.webp' ?>" alt="product 1" class="dashboard-product__image">
            </a>
            <div class="profile-product__details">
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
                <h3 class="profile-product__title">Jacquemus bucket hat</h3>
                <p class="profile-product__rent"><span>השכרה</span> <span>החל מ-</span> <span>39 ₪ </span></p>
                <p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins></p>
                <p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
            </div>
        </div>
    </div>

</section>


