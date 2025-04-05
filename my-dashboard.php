<?php
/*
Template Name: My Dashboard
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Защита от прямого доступа
}

get_header();
 
?>
<?php
use Omnis\src\inc\classes\dokan_enhancer\Dokan_Enhancer;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$vendor_id = get_current_user_id();
$vendor = new \WeDevs\Dokan\Vendor\Vendor( $vendor_id );
$total_sales = $vendor->get_total_sales();
$store_user   = dokan()->vendor->get($vendor_id);
$store_info   = $store_user->get_shop_info();
$map_location = $store_user->get_location();
$layout       = get_theme_mod('store_layout', 'left');
$vendor_id = $store_user->get_id();
$orders_count_data = dokan_count_orders( $vendor_id ); 

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
<section class="top">
    <div class="icons">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 21C20 19.6044 20 18.9067 19.8278 18.3389C19.44 17.0605 18.4395 16.06 17.1611 15.6722C16.5933 15.5 15.8956 15.5 14.5 15.5H9.5C8.10444 15.5 7.40665 15.5 6.83886 15.6722C5.56045 16.06 4.56004 17.0605 4.17224 18.3389C4 18.9067 4 19.6044 4 21M16.5 7.5C16.5 9.98528 14.4853 12 12 12C9.51472 12 7.5 9.98528 7.5 7.5C7.5 5.01472 9.51472 3 12 3C14.4853 3 16.5 5.01472 16.5 7.5Z" stroke="#111111" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M11.5257 5.03591C9.61989 2.80783 6.44179 2.20848 4.05391 4.24874C1.66603 6.28899 1.32985 9.7002 3.20507 12.1132C4.76418 14.1195 9.4826 18.3508 11.029 19.7204C11.2021 19.8736 11.2886 19.9502 11.3895 19.9803C11.4775 20.0066 11.5739 20.0066 11.662 19.9803C11.7629 19.9502 11.8494 19.8736 12.0224 19.7204C13.5688 18.3508 18.2873 14.1195 19.8464 12.1132C21.7216 9.7002 21.4265 6.26753 18.9975 4.24874C16.5686 2.22994 13.4316 2.80783 11.5257 5.03591Z" stroke="#111111" stroke-width="2" stroke-linejoin="round"/>
        </svg>
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.35395 21C10.0591 21.6224 10.9853 22 11.9998 22C13.0142 22 13.9405 21.6224 14.6456 21M17.9998 8C17.9998 6.4087 17.3676 4.88258 16.2424 3.75736C15.1172 2.63214 13.5911 2 11.9998 2C10.4085 2 8.88235 2.63214 7.75713 3.75736C6.63192 4.88258 5.99977 6.4087 5.99977 8C5.99977 11.0902 5.22024 13.206 4.34944 14.6054C3.6149 15.7859 3.24763 16.3761 3.2611 16.5408C3.27601 16.7231 3.31463 16.7926 3.46155 16.9016C3.59423 17 4.19237 17 5.38863 17H18.6109C19.8072 17 20.4053 17 20.538 16.9016C20.6849 16.7926 20.7235 16.7231 20.7384 16.5408C20.7519 16.3761 20.3846 15.7859 19.6501 14.6054C18.7793 13.206 17.9998 11.0902 17.9998 8Z" stroke="#111111" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg> 
    </div>
    <div class="credit">
        <span>קרדיט</span>
        <span style="
        background: black;
        color: #fff;
        "><svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M8.66683 2.33333C8.66683 3.06971 7.02521 3.66667 5.00016 3.66667C2.97512 3.66667 1.3335 3.06971 1.3335 2.33333M8.66683 2.33333C8.66683 1.59695 7.02521 1 5.00016 1C2.97512 1 1.3335 1.59695 1.3335 2.33333M8.66683 2.33333V3.33333M1.3335 2.33333V10.3333C1.3335 11.0697 2.97512 11.6667 5.00016 11.6667M5.00016 6.33333C4.8878 6.33333 4.77662 6.3315 4.66683 6.3279C2.798 6.26666 1.3335 5.69552 1.3335 5M5.00016 9C2.97512 9 1.3335 8.40305 1.3335 7.66667M14.6668 6.66667C14.6668 7.40305 13.0252 8 11.0002 8C8.97512 8 7.3335 7.40305 7.3335 6.66667M14.6668 6.66667C14.6668 5.93029 13.0252 5.33333 11.0002 5.33333C8.97512 5.33333 7.3335 5.93029 7.3335 6.66667M14.6668 6.66667V11.6667C14.6668 12.403 13.0252 13 11.0002 13C8.97512 13 7.3335 12.403 7.3335 11.6667V6.66667M14.6668 9.16667C14.6668 9.90305 13.0252 10.5 11.0002 10.5C8.97512 10.5 7.3335 9.90305 7.3335 9.16667" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <?php echo $total_sales; ?></span>
    </div>
</section>
<section class="main">
    <h1>
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 18L15 12L9 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        דוחות ונתונים
    </h1>
    <ul class="menu-1">
        <li>הצג הכל</li>
        <li>תנועות</li>
        <li>קרדיט</li>
        <li>דירוג</li>
        <li>המובילים</li>
    </ul>
    <ul class="menu-2">
        <li>חודשי</li>
        <li>רבעוני</li>
        <li>שנתי</li>
        <li>טווח תאריכים</li>
    </ul>
</section>
<div class="swap-tabs profile-store-report-tabs">
    <div class="swap-tab-buttons diagram-switch"> 
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


<?php get_footer(); ?>
