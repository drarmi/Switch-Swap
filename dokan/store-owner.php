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

<!-- ========================== 
     Блок "פריטים אחרונים"
     ========================== -->
<section class="profile-products-section section-block">
    <div class="section-heading">
        <h2 class="section-heading__title section-heading__title--arrow">פריטים אחרונים</h2>
    </div>
    <div class="profile-products profile-products--swipe">

        <?php if ($query_last_products->have_posts()): ?>
            <?php while ($query_last_products->have_posts()):
                $query_last_products->the_post();
                $product = wc_get_product(get_the_ID());

                // Проверим, “аренда” ли это (примерно):
                // (Это лишь пример, замените под свою логику)
                // $is_rent = ( 'booking' === $product->get_type() );
                // Или, может, $product->get_meta('_is_rent') === 'yes'
                $is_rent = false; // Заглушка, т.к. не знаю вашу логику
        
                // Цены
                $regular_price = $product->get_regular_price();
                $sale_price    = $product->get_sale_price();
                $price_html    = $product->get_price_html(); // готовая строка HTML
        
                // Картинка
                $thumbnail_url = get_the_post_thumbnail_url($product->get_id(), 'medium');
                if (!$thumbnail_url) {
                    $thumbnail_url = wc_placeholder_img_src();
                }
                ?>
                <div class="profile-product">
                    <div class="profile-product__media">
                        <a class="profile-product__edit">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M1.31128 14.1984C1.34932 13.8561 1.36833 13.685 1.42012 13.525C1.46606 13.3831 1.53098 13.2481 1.6131 13.1235C1.70566 12.9832 1.82742 12.8614 2.07094 12.6179L13.0031 1.68577C13.9174 0.77141 15.3999 0.771411 16.3143 1.68577C17.2286 2.60013 17.2286 4.0826 16.3142 4.99696L5.38213 15.9291C5.1386 16.1726 5.01684 16.2943 4.87648 16.3869C4.75194 16.469 4.61688 16.5339 4.47496 16.5799C4.315 16.6317 4.14385 16.6507 3.80157 16.6887L1 17L1.31128 14.1984Z"
                                    stroke="#111111" stroke-width="1.5" stroke-linejoin="round" />
                            </svg>
                        </a>
                        <!-- Картинка товара -->
                        <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php the_title_attribute(); ?>"
                            class="profile-product__image">
                    </div>
                    <div class="profile-product__details">
                        <?php if ($is_rent): ?>
                            <p class="profile-product__tag-rent">השכרה</p>
                        <?php endif; ?>

                        <h3 class="profile-product__title"><?php echo $product->get_name(); ?></h3>

                        <!-- Короткое описание, если нужно -->
                        <p class="profile-product__desc">
                            <?php
                            // Выводим "короткое описание" (Excerpt)
                            echo wp_trim_words($product->get_short_description(), 15);
                            ?>
                        </p>

                        <?php if ($is_rent): ?>
                            <p class="profile-product__rent">
                                <span>השכרה</span>
                                <span>החל מ-</span>
                                <span><?php echo wc_price($product->get_price()); ?></span>
                            </p>
                        <?php endif; ?>

                        <!-- Пример для "классической" цены (покупка) -->
                        <p class="profile-product__amount">
                            <span>קנייה מיידית</span>
                            <?php if ($sale_price && $sale_price < $regular_price): ?>
                                <del><?php echo wc_price($regular_price); ?></del>
                                <ins><?php echo wc_price($sale_price); ?></ins>
                            <?php else: ?>
                                <ins><?php echo wc_price($product->get_price()); ?></ins>
                            <?php endif; ?>
                        </p>

                        <!-- Пример, если у товара разрешены "предложения" -->
                        <?php
                        // Замените '_can_propose_price' на свой meta
                        // if ( $product->get_meta('_can_propose_price') === 'yes' ) :
                        ?>
                        <p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
                        <?php //endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php else: ?>
            <p>אין מוצרים אחרונים</p>
        <?php endif; ?>

    </div>
</section>


<!-- =========================================
     Ниже оставляем ваши блоки как есть:
     "הגשת הצעות", "ביצועי החנות", и т.д.
     ========================================= -->
<section class="profile-products-section section-block">
    <div class="section-heading">
        <h2 class="section-heading__title section-heading__title--arrow">הגשת הצעות</h2>
    </div>
    <div class="profile-products profile-products--swipe">
        <!-- ... ВАШ СТАТИЧЕСКИЙ БЛОК ... -->
        <!-- Не меняю, т.к. не знаю вашей точной логики -->
        <?php // ... ?>
    </div>
</section>

<div class="section-heading" style="margin-top:40px">
    <h2 class="section-heading__title section-heading__title--arrow">ביצועי החנות</h2>
</div>
<div class="swap-tabs profile-store-report-tabs">
    <div class="swap-tab-buttons diagram-switch">
        <button class="swap-tab-button active" data-tab="tab1">החודש הנוכחי</button>
        <button class="swap-tab-button" data-tab="tab2">חודש קודם</button>
    </div>
    <?php
    // Получаем ID вендора
    $vendor_id = $store_user->get_id();

    // Получаем дату начала текущего месяца (например, "2025-02-01 00:00:00")
    $current_month_start = date('Y-m-01 00:00:00');

    // Параметры для получения заказов за текущий месяц
    $args = array(
        'limit' => -1, // получаем все заказы
        'status' => array('wc-completed'), // берем только завершённые заказы
        // Фильтр по мета-данным: предполагается, что заказ связан с вендором через meta-поле _dokan_vendor_id
        'meta_query' => array(
            array(
                'key' => '_dokan_vendor_id',
                'value' => $vendor_id,
                'compare' => '='
            )
        ),
        // Фильтр по дате создания заказа: выбираем заказы, созданные после начала текущего месяца
        'date_created' => '>' . $current_month_start,
    );

    // Получаем заказы согласно параметрам
    $orders = wc_get_orders($args);

    // Подсчитываем количество заказов (продаж) за текущий месяц
    $sales_count = count($orders);

    // Инициализируем переменные для заработка и арендованных товаров
    $total_earning = 0;
    $total_rentals = 0;

    // Проходим по всем заказам
    foreach ($orders as $order) {
        // Суммируем итоговую сумму заказа
        $total_earning += floatval($order->get_total());

        // Получаем товары (items) в заказе
        foreach ($order->get_items() as $item) {
            // Получаем продукт из элемента заказа
            $product = $item->get_product();
            if (!$product) {
                continue;
            }

            // Если тип продукта равен 'booking', считаем его как аренду
            if ('booking' === $product->get_type()) {
                // Увеличиваем счётчик арендованных товаров на количество единиц в этом элементе заказа
                $total_rentals += $item->get_quantity();
            }
        }
    }

    // Форматируем заработок согласно настройкам WooCommerce
    $formatted_earning = wc_price($total_earning);
    ?>

    <div class="swap-tab-content">
        <div class="swap-tab-panel active" id="tab1">

            <div class="all-charts">
                <div class="sales chart">
                    <span class="num"><span></span>עלייה/<i></i></span>
                    <div class="count"><?php echo $sales_count; ?></div>
                    <img class="reports-placeholder"
                        src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/reports/gen-report-1.svg' ?>"
                        alt="">
                </div>
                <div class="rentals chart">
                    <span class="num"><span></span>עלייה/<i></i></span>
                    <div class="count"><?php echo $total_rentals; ?></div>
                    <img class="reports-placeholder"
                        src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/reports/gen-report-2.svg' ?>"
                        alt="">
                </div>
                <div class="rating chart">
                    <span class="num"><span></span>/100%<i></i></span>
                    <div class="count"></div>
                    <div class="flex-wrapper">
                        <style>
                            .rating {
                                position: relative;
                            }

                            .flex-wrapper {
                                display: flex;
                                flex-flow: row nowrap;
                                position: absolute;
                                top: 77px;
                                left: 31px;
                                width: 69%;
                            }

                            .single-chart {
                                width: 100%;
                                justify-content: space-around;
                            }

                            .circle-bg {
                                fill: none;
                                stroke: #EEEEEE;
                                stroke-width: 4.4;
                            }

                            .circle {
                                fill: none;
                                stroke-width: 3.8;
                                stroke-linecap: round;
                                animation: progress 1s ease-out forwards;
                            }

                            @keyframes progress {
                                0% {
                                    stroke-dasharray: 0 100;
                                }
                            }

                            .percentage {
                                fill: #666;
                                font-family: sans-serif;
                                font-size: 0.5em;
                                text-anchor: middle;
                            }
                        </style>
                        <div class="rating-text"></div>
                        <div class="single-chart">
                            <div class="rating-number"><?php echo $data['final_rating']; ?></div>
                            <svg viewBox="0 0 36 36" class="circular-chart orange">
                                <defs>
                                    <linearGradient id="paint0_linear_1151_25027" x1="27" y1="102.878" x2="149.245"
                                        y2="102.878" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#8F6B45" />
                                        <stop offset="1" stop-color="#C7A77F" />
                                    </linearGradient>
                                </defs>
                                <path class="circle-bg" d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="circle" stroke-dasharray="30, 100" d="M18 2.0845
                            a 15.9155 15.9155 0 0 1 0 31.831
                            a 15.9155 15.9155 0 0 1 0 -31.831" stroke="url(#paint0_linear_1151_25027)" />
                            </svg>
                        </div>
                    </div>
                    <img class="reports-placeholder"
                        src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/reports/gen-report-4.svg' ?>" alt="">
                </div>
                <div class="credits chart">
                    <span class="num"><span></span><i></i></span>
                    <div class="count"><?php echo $total_earning; ?></div>
                    <img class="reports-placeholder"
                        src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/reports/gen-report-3.svg' ?>" alt="">
                </div> 
            </div>


            <?php
            // ----------------------------------------------------
            // 1. Собираем заказы вендора за ТЕКУЩИЙ месяц
            // ----------------------------------------------------

            // Дата начала текущего месяца (например, 2025-02-01 00:00:00)
            $start_of_current_month = date('Y-m-01 00:00:00');

            // Аргументы для заказов "текущего месяца"
            $args_current = array(
                'limit'      => -1,
                'status'     => 'wc-completed',
                'meta_query' => array(
                    array(
                        'key'     => '_dokan_vendor_id',
                        'value'   => $vendor_id, // ID текущего вендора
                        'compare' => '=',
                    ),
                ),
                'date_created' => '>' . $start_of_current_month, // заказы, созданные после начала текущего месяца
            );

            $orders_current = wc_get_orders( $args_current );

            // ----------------------------------------------------
            // 2. Подсчитываем проданное кол-во каждого товара
            // ----------------------------------------------------
            $products_sold_current = array(); // [product_id => qty]

            foreach ( $orders_current as $order ) {
                foreach ( $order->get_items() as $item ) {
                    $product_id = $item->get_product_id();
                    $qty        = $item->get_quantity();

                    if ( isset( $products_sold_current[ $product_id ] ) ) {
                        $products_sold_current[ $product_id ] += $qty;
                    } else {
                        $products_sold_current[ $product_id ] = $qty;
                    }
                }
            }

            // ----------------------------------------------------
            // 3. Получаем "топ 4" товаров вендора по total_sales
            //    (общие продажи за всё время), либо меняем логику.
            // ----------------------------------------------------
            $best_selling_args = array(
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'author'         => $vendor_id,       // товары конкретного вендора
                'meta_key'       => 'total_sales',    // сортируем по общим продажам (Woo хранит в total_sales)
                'orderby'        => 'meta_value_num',
                'order'          => 'DESC',
                'posts_per_page' => 4,
            );

            $best_selling_query = new WP_Query( $best_selling_args );
            ?>

            <section class="dashboard__top-sales">
                <div class="dashboard__section-heading">
                    <h2 class="dashboard__section-title">המוצרים הכי נמכרים</h2>
                </div>
                <div class="dashboard-products dashboard-products--swipe">

                    <?php if ( $best_selling_query->have_posts() ) : ?>
                        <?php while ( $best_selling_query->have_posts() ) : $best_selling_query->the_post();
                            $product = wc_get_product( get_the_ID() );
                            if ( ! $product ) {
                                continue;
                            }

                            // ID товара
                            $pid = $product->get_id();

                            // Количество продаж за текущий месяц (может быть 0, если не было в заказах)
                            $qty_current_month = isset( $products_sold_current[ $pid ] ) 
                                ? $products_sold_current[ $pid ] 
                                : 0;

                            // Если у вас есть логика «аренды»:
                            $is_rent      = false;
                            // Цены
                            $regular_price = $product->get_regular_price();
                            $sale_price    = $product->get_sale_price();
                            // Миниатюра
                            $thumbnail_url = get_the_post_thumbnail_url( $pid, 'medium' );
                            if ( ! $thumbnail_url ) {
                                $thumbnail_url = wc_placeholder_img_src();
                            }
                        ?>
                            <div class="dashboard-product">
                                <a class="dashboard-product__media" href="<?php the_permalink(); ?>">
                                    <img src="<?php echo esc_url( $thumbnail_url ); ?>"
                                        alt="<?php the_title_attribute(); ?>"
                                        class="dashboard-product__image">
                                </a>
                                <div class="profile-product__details">

                                    <!-- выводим "Продано в этом месяце: N" 
                                    <p class="profile-product__dynamic">
                                        <?php echo 'נמכר החודש: ' . $qty_current_month; ?>
                                    </p>-->

                                    <!-- Название товара -->
                                    <h3 class="profile-product__title"><?php echo esc_html( $product->get_name() ); ?></h3>

                                    <!-- Если аренда -->
                                    <?php if ( $is_rent ) : ?>
                                        <p class="profile-product__rent">
                                            <span>השכרה</span>
                                            <span>החל מ-</span>
                                            <span><?php echo wc_price( $product->get_price() ); ?></span>
                                        </p>
                                    <?php endif; ?>

                                    <!-- Цена (для покупки) -->
                                    <p class="profile-product__amount">
                                        <span>קנייה מיידית</span>
                                        <?php if ( $sale_price && $sale_price < $regular_price ) : ?>
                                            <del><?php echo wc_price( $regular_price ); ?></del>
                                            <ins><?php echo wc_price( $sale_price ); ?></ins>
                                        <?php else : ?>
                                            <ins><?php echo wc_price( $product->get_price() ); ?></ins>
                                        <?php endif; ?>
                                    </p>

                                    <!-- Пример для "предложить свою цену" -->
                                    <?php /*
                                    if ( $product->get_meta('_can_propose_price') === 'yes' ) {
                                        echo '<p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>';
                                    }
                                    */ ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        <?php wp_reset_postdata(); ?>
                    <?php else : ?>
                        <p>אין מוצרים נמכרים</p>
                    <?php endif; ?>

                </div>
            </section>





            <?php
            // 1) Собираем заказы для текущего вендора
            $args_top_customers = array(
                'limit'   => -1,
                'status'  => 'wc-completed',
                'meta_query' => array(
                    array(
                        'key'     => '_dokan_vendor_id',
                        'value'   => $vendor_id,
                        'compare' => '=',
                    ),
                ),
            );
            $orders_buyers = wc_get_orders( $args_top_customers );

            // 2) Готовим массив покупателей
            $customers_data = array(); 
            foreach ( $orders_buyers as $order ) {
                $order_date = $order->get_date_created();
                $timestamp  = $order_date ? $order_date->getTimestamp() : 0;

                $user_id = $order->get_user_id();
                if ( $user_id ) {
                    // Авторизованный пользователь
                    $user_info    = get_userdata( $user_id );
                    $display_name = $user_info ? $user_info->display_name : 'User #' . $user_id;
                    $key          = 'user_' . $user_id;

                    // Для последующего вывода аватара
                    $avatar_user_id = $user_id;
                    $avatar_email   = '';
                } else {
                    // Гость
                    $email      = $order->get_billing_email();
                    $first_name = $order->get_billing_first_name();
                    $last_name  = $order->get_billing_last_name();

                    $display_name = trim( $first_name . ' ' . $last_name );
                    if ( empty( $display_name ) ) {
                        $display_name = $email;
                    }
                    // Генерируем ключ, чтобы группировать заказы одного и того же e-mail
                    $key = 'guest_' . md5( $email );

                    // Гостевой покупатель
                    $avatar_user_id = 0;
                    $avatar_email   = $email;
                }

                if ( ! isset( $customers_data[ $key ] ) ) {
                    $customers_data[ $key ] = array(
                        'name'           => $display_name,
                        'orders_count'   => 0,
                        'last_timestamp' => 0,
                        // Сохраняем, чтобы позже вывести аватар
                        'user_id'        => $avatar_user_id,
                        'email'          => $avatar_email,
                    );
                }

                $customers_data[ $key ]['orders_count']++;
                if ( $timestamp > $customers_data[ $key ]['last_timestamp'] ) {
                    $customers_data[ $key ]['last_timestamp'] = $timestamp;
                }
            }

            // 3) Сортируем по orders_count (desc) + last_timestamp (desc)
            $customers_array = array_values( $customers_data );
            usort( $customers_array, function( $a, $b ) {
                if ( $a['orders_count'] !== $b['orders_count'] ) {
                    return $b['orders_count'] - $a['orders_count'];
                }
                return $b['last_timestamp'] - $a['last_timestamp'];
            });

            // 4) Берём первые 8
            $top_buyers = array_slice( $customers_array, 0, 8, true ); 
            $top_buyers_count = count( $top_buyers );


            ?>

            <section class="dashboard-returned-buyers">
                <div class="dashboard__section-heading">
                    <h2 class="dashboard__section-title">לקוחות מובילים
                        <span>עלייה/<?php echo $top_buyers_count; ?>
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.95631 5.89855V14.9033C7.95631 15.4556 8.40403 15.9033 8.95631 15.9033H9.04369C9.59597 15.9033 10.0437 15.4556 10.0437 14.9033V5.89855L13.8171 9.67199C14.2076 10.0625 14.8408 10.0625 15.2313 9.67199L15.2929 9.61043C15.6834 9.2199 15.6834 8.58674 15.2929 8.19621L9.70711 2.61043C9.31658 2.2199 8.68342 2.2199 8.29289 2.61043L2.70711 8.19621C2.31658 8.58674 2.31658 9.2199 2.70711 9.61043L2.76866 9.67198C3.15919 10.0625 3.79235 10.0625 4.18288 9.67199L7.95631 5.89855Z" fill="#C7A77F"></path>
                                <path d="M16 10.3175L16 10.3175C16.781 9.53648 16.781 8.27016 16 7.48911L10.4142 1.90332C9.63316 1.12227 8.36683 1.12227 7.58579 1.90332L2 7.48911C1.21895 8.27016 1.21895 9.53649 2 10.3175L2.06156 10.3791C2.84261 11.1601 4.10894 11.1601 4.88998 10.3791L6.95631 8.31276V14.9033C6.95631 16.0079 7.85174 16.9033 8.95631 16.9033H9.04369C10.1483 16.9033 11.0437 16.0079 11.0437 14.9033V8.31276L13.11 10.3791C13.8911 11.1601 15.1574 11.1601 15.9384 10.3791C15.9384 10.3791 15.9384 10.3791 15.9384 10.3791L16 10.3175Z" stroke="#C69F7C" stroke-opacity="0.2" stroke-width="2"></path>
                                <path d="M7.95631 5.89855V14.9033C7.95631 15.4556 8.40403 15.9033 8.95631 15.9033H9.04369C9.59597 15.9033 10.0437 15.4556 10.0437 14.9033V5.89855L13.8171 9.67199C14.2076 10.0625 14.8408 10.0625 15.2313 9.67199L15.2929 9.61043C15.6834 9.2199 15.6834 8.58674 15.2929 8.19621L9.70711 2.61043C9.31658 2.2199 8.68342 2.2199 8.29289 2.61043L2.70711 8.19621C2.31658 8.58674 2.31658 9.2199 2.70711 9.61043L2.76866 9.67198C3.15919 10.0625 3.79235 10.0625 4.18288 9.67199L7.95631 5.89855Z" fill="#C7A77F"></path>
                            </svg>
                        </span>
                    </h2>
                </div>
                <div class="dashboard__buyer-profiles">
                    <?php if ( ! empty( $top_buyers ) ) : ?>
                        <?php foreach ( $top_buyers as $cust ) : 
                            $orders_count   = $cust['orders_count'];
                            $last_timestamp = $cust['last_timestamp'];

                            // Для "3 ימים назад"
                            $diff_str = ( $last_timestamp )
                                ? human_time_diff( $last_timestamp, current_time('timestamp') )
                                : '—';

                            // Определяем URL аватара:
                            if ( $cust['user_id'] ) {
                                // Для авторизованного пользователя
                                $avatar_url = get_avatar_url( $cust['user_id'], array( 'size' => 44 ) );
                            } elseif ( ! empty( $cust['email'] ) ) {
                                // Для гостя по e-mail (Gravatar)
                                $avatar_url = get_avatar_url( $cust['email'], array( 'size' => 44 ) );
                            } else {
                                // Если нет данных — заглушка
                                $avatar_url = get_stylesheet_directory_uri() . '/assets/images/avatar-placeholder.png';
                            }
                        ?>
                        <div class="dashboard__buyer-profile">
                            <img src="<?php echo esc_url( $avatar_url ); ?>"
                                alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                            
                            <h3 class="dashboard__buyer-profile-name">
                                <?php echo esc_html( $cust['name'] ); ?>
                            </h3>

                            <p class="dashboard__buyer-profile-orders">
                                הזמנות <?php echo (int) $orders_count; ?>
                            </p>

                            <p class="dashboard__buyer-profile-buying-date">
                                לפני <?php echo esc_html( $diff_str ); ?>
                            </p>
                        </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>עדיין אין לקוחות מובילים</p>
                    <?php endif; ?>
                </div>
            </section>




        </div>
        <div class="swap-tab-panel" id="tab2">
            <div class="all-charts">
                <div>
                    <img class="reports-placeholder"
                        src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/reports/gen-report-1-prev.svg' ?>"
                        alt="">
                </div>
                <div>
                    <img class="reports-placeholder"
                        src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/reports/gen-report-2-prev.svg' ?>"
                        alt="">
                </div>
                <div>
                    <img class="reports-placeholder"
                        src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/reports/gen-report-3-prev.svg' ?>"
                        alt="">
                </div>
                <div>
                    <img class="reports-placeholder"
                        src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/reports/gen-report-4-prev.svg' ?>"
                        alt="">
                </div>
            </div>


            <section class="dashboard__top-sales">
                <div class="dashboard__section-heading">
                    <h2 class="dashboard__section-title">המוצרים הכי נמכרים</h2>
                </div>
                <div class="dashboard-products dashboard-products--swipe">
                    <div class="dashboard-product">
                        <a class="dashboard-product__media" href="#">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-4.webp' ?>"
                                alt="product 1" class="dashboard-product__image">
                        </a>
                        <div class="profile-product__details">
                            <p class="profile-product__dynamic">+17%
                                <svg width="36" height="17" viewBox="0 0 36 17" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3"
                                        stroke="#C7A77F" stroke-opacity="0.2" stroke-width="6" stroke-linecap="round" />
                                    <path
                                        d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3"
                                        stroke="#C7A77F" stroke-width="2" stroke-linecap="round" />
                                </svg>
                            </p>
                            <h3 class="profile-product__title">Jacquemus bucket hat</h3>
                            <p class="profile-product__rent"><span>השכרה</span><span>החל מ-</span><span>39 ₪</span></p>
                            <p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins>
                            </p>
                            <p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
                        </div>
                    </div>
                    <div class="dashboard-product">
                        <a class="dashboard-product__media" href="#">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-5.webp' ?>"
                                alt="product 1" class="dashboard-product__image">
                        </a>
                        <div class="profile-product__details">
                            <p class="profile-product__dynamic">+7%
                                <svg width="36" height="17" viewBox="0 0 36 17" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3"
                                        stroke="#C7A77F" stroke-opacity="0.2" stroke-width="6" stroke-linecap="round" />
                                    <path
                                        d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3"
                                        stroke="#C7A77F" stroke-width="2" stroke-linecap="round" />
                                </svg>
                            </p>
                            <h3 class="profile-product__title">Jacquemus bucket hat</h3>
                            <p class="profile-product__rent"><span>השכרה</span> <span>החל מ-</span> <span>39 ₪ </span>
                            </p>
                            <p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins>
                            </p>
                            <p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
                        </div>
                    </div>
                    <div class="dashboard-product">
                        <a class="dashboard-product__media" href="#">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-4.webp' ?>"
                                alt="product 1" class="dashboard-product__image">
                        </a>
                        <div class="profile-product__details">
                            <p class="profile-product__dynamic">+10%
                                <svg width="36" height="17" viewBox="0 0 36 17" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3"
                                        stroke="#C7A77F" stroke-opacity="0.2" stroke-width="6" stroke-linecap="round" />
                                    <path
                                        d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3"
                                        stroke="#C7A77F" stroke-width="2" stroke-linecap="round" />
                                </svg>
                            </p>
                            <h3 class="profile-product__title">Jacquemus bucket hat</h3>
                            <p class="profile-product__rent"><span>השכרה</span><span>החל מ-</span><span>39 ₪</span></p>
                            <p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins>
                            </p>
                            <p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
                        </div>
                    </div>
                    <div class="dashboard-product">
                        <a class="dashboard-product__media" href="#">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/products/product-5.webp' ?>"
                                alt="product 1" class="dashboard-product__image">
                        </a>
                        <div class="profile-product__details">
                            <p class="profile-product__dynamic">+23%
                                <svg width="36" height="17" viewBox="0 0 36 17" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3"
                                        stroke="#C7A77F" stroke-opacity="0.2" stroke-width="6" stroke-linecap="round" />
                                    <path
                                        d="M4.17236 13.9996C6.67236 8.49957 8.06443 7.93034 10.3674 12.4565C10.709 13.1279 13.0853 6.32932 13.6724 5.99969C14.1065 5.75593 15.6795 11.4336 15.9982 10.938C17.9897 7.84121 22.4359 1.97194 23.9131 6.61698C24.6255 8.85722 31.6136 5.28356 32.1784 3"
                                        stroke="#C7A77F" stroke-width="2" stroke-linecap="round" />
                                </svg>
                            </p>
                            <h3 class="profile-product__title">Jacquemus bucket hat</h3>
                            <p class="profile-product__rent"><span>השכרה</span> <span>החל מ-</span> <span>39 ₪ </span>
                            </p>
                            <p class="profile-product__amount"><span>קנייה מיידית</span><del>249 ₪</del><ins>180 ₪</ins>
                            </p>
                            <p class="profile-product__proposals-allowed">ניתן להגיש הצעות</p>
                        </div>
                    </div>
                </div>
            </section>




            <section class="dashboard-returned-buyers">
                <div class="dashboard__section-heading">
                    <h2 class="dashboard__section-title">לקוחות מובילים
                        <span>עלייה/9<svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.95631 5.89855V14.9033C7.95631 15.4556 8.40403 15.9033 8.95631 15.9033H9.04369C9.59597 15.9033 10.0437 15.4556 10.0437 14.9033V5.89855L13.8171 9.67199C14.2076 10.0625 14.8408 10.0625 15.2313 9.67199L15.2929 9.61043C15.6834 9.2199 15.6834 8.58674 15.2929 8.19621L9.70711 2.61043C9.31658 2.2199 8.68342 2.2199 8.29289 2.61043L2.70711 8.19621C2.31658 8.58674 2.31658 9.2199 2.70711 9.61043L2.76866 9.67198C3.15919 10.0625 3.79235 10.0625 4.18288 9.67199L7.95631 5.89855Z"
                                    fill="#C7A77F" />
                                <path
                                    d="M16 10.3175L16 10.3175C16.781 9.53648 16.781 8.27016 16 7.48911L10.4142 1.90332C9.63316 1.12227 8.36683 1.12227 7.58579 1.90332L2 7.48911C1.21895 8.27016 1.21895 9.53649 2 10.3175L2.06156 10.3791C2.84261 11.1601 4.10894 11.1601 4.88998 10.3791L6.95631 8.31276V14.9033C6.95631 16.0079 7.85174 16.9033 8.95631 16.9033H9.04369C10.1483 16.9033 11.0437 16.0079 11.0437 14.9033V8.31276L13.11 10.3791C13.8911 11.1601 15.1574 11.1601 15.9384 10.3791C15.9384 10.3791 15.9384 10.3791 15.9384 10.3791L16 10.3175Z"
                                    stroke="#C69F7C" stroke-opacity="0.2" stroke-width="2" />
                                <path
                                    d="M7.95631 5.89855V14.9033C7.95631 15.4556 8.40403 15.9033 8.95631 15.9033H9.04369C9.59597 15.9033 10.0437 15.4556 10.0437 14.9033V5.89855L13.8171 9.67199C14.2076 10.0625 14.8408 10.0625 15.2313 9.67199L15.2929 9.61043C15.6834 9.2199 15.6834 8.58674 15.2929 8.19621L9.70711 2.61043C9.31658 2.2199 8.68342 2.2199 8.29289 2.61043L2.70711 8.19621C2.31658 8.58674 2.31658 9.2199 2.70711 9.61043L2.76866 9.67198C3.15919 10.0625 3.79235 10.0625 4.18288 9.67199L7.95631 5.89855Z"
                                    fill="#C7A77F" />
                            </svg></span>
                    </h2>
                </div>
                <div class="dashboard__buyer-profiles">
                    <div class="dashboard__buyer-profile">
                        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/store-image.jpg' ?>"
                            alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                        <h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
                        <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                        <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                    </div>
                    <div class="dashboard__buyer-profile">
                        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-1.webp' ?>"
                            alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                        <h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
                        <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                        <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                    </div>
                    <div class="dashboard__buyer-profile">
                        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-2.webp' ?>"
                            alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                        <h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
                        <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                        <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                    </div>
                    <div class="dashboard__buyer-profile">
                        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-4.webp' ?>"
                            alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                        <h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
                        <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                        <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                    </div>
                    <div class="dashboard__buyer-profile">
                        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/store-image.jpg' ?>"
                            alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                        <h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
                        <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                        <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                    </div>
                    <div class="dashboard__buyer-profile">
                        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-1.webp' ?>"
                            alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                        <h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
                        <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                        <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                    </div>
                    <div class="dashboard__buyer-profile">
                        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-2.webp' ?>"
                            alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                        <h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
                        <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                        <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                    </div>
                    <div class="dashboard__buyer-profile">
                        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/dist/images/sections/profile/profile-image-4.webp' ?>"
                            alt="" width="44" height="44" class="dashboard__buyer-profile-image">
                        <h3 class="dashboard__buyer-profile-name">שירה_95🔥</h3>
                        <p class="dashboard__buyer-profile-orders">הזמנות 38</p>
                        <p class="dashboard__buyer-profile-buying-date">לפני 3 ימים</p>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

<!-- =========================================
     Блок отзывов (реальные)
     ========================================= -->
<section class="profile-product-comments">
    <div class="section-heading">
        <h2 class="section-heading__title section-heading__title--arrow">ביקורות ודירוגים</h2>
    </div>

    <div class="profile-product-comments-list">
        <?php if (!empty($vendor_comments)): ?>
            <?php foreach ($vendor_comments as $comment):
                // Получаем ID товара
                $product_id = $comment->comment_post_ID;
                $product    = wc_get_product($product_id);
                if (!$product) {
                    continue;
                }

                // Попробуем вытащить оценку (rating)
                $rating = intval(get_comment_meta($comment->comment_ID, 'rating', true));

                // Картинка товара (для мини-галереи)
                $thumb_url = get_the_post_thumbnail_url($product_id, 'thumbnail');
                if (!$thumb_url) {
                    $thumb_url = wc_placeholder_img_src();
                }
                ?>
                <div class="profile-product-comment">
                    <div class="profile-product-comment__header">
                        <p class="profile-product-comment__details">
                            <!-- Аватар автора отзыва -->
                            <?php echo get_avatar($comment->comment_author_email, 20, '', '', array(
                                'class' => 'dashboard__author-image'
                            )); ?>
                            <span class="profile-product-comment__author">
                                <?php echo esc_html($comment->comment_author); ?>
                            </span>
                            <span class="profile-product-comment__date">
                                <?php 
                                printf(
                                    __('%s days ago', 'textdomain'),
                                    human_time_diff(strtotime($comment->comment_date), current_time('timestamp'))
                                );
                                ?>
                            </span>
                        </p>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12Z"
                                stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M6 12C6 11.4477 5.55228 11 5 11C4.44772 11 4 11.4477 4 12C4 12.5523 4.44772 13 5 13C5.55228 13 6 12.5523 6 12Z"
                                stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M20 12C20 11.4477 19.5523 11 19 11C18.4477 11 18 11.4477 18 12C18 12.5523 18.4477 13 19 13C19.5523 13 20 12.5523 20 12Z"
                                stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>

                    <!-- Если есть понятие "размер" (S, M...), мы не знаем,
                         откуда его брать — оставим как пример -->
                    <p class="profile-product-comment__rent-size">
                        מידה שנשכרה:<span>S</span>
                    </p>

                    <!-- Текст отзыва -->
                    <p class="profile-product-comment__text">
                        <?php echo wp_kses_post($comment->comment_content); ?>
                    </p>

                    <!-- Звёздочки (если rating > 0) -->
                    <?php if ($rating > 0): ?>
                        <div style="margin:5px 0;">
                            <?php
                            // Пример: выводим 5 звёзд, закрашиваем $rating шт.
                            // Или используйте wc_get_rating_html($rating)
                            echo wc_get_rating_html($rating, 5);
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Пример маленькой "галереи" товара (одна картинка) -->
                    <div class="profile-product-comment__product-images">
                        <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>"
                            class="profile-product-comment__product-image">
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>עדיין אין ביקורות.</p>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer('swap');
