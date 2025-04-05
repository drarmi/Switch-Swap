<?php
/**
 * My Orders (Overridden for Rental)
 *
 * Copy this file to yourtheme/woocommerce/myaccount/orders.php
 * to override the default WooCommerce template.
 *
 * @version 9.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders ); 
?>

<?php if ( $has_orders ) : ?>

<h1 class="profile-page-title no-before">
    <a href="<?php echo home_url(); ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 18L15 12L9 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>
    ההזמנות שלי
</h1>





<div class="rental-orders-grid">
    <?php foreach ( $customer_orders->orders as $customer_order ) :

        $order = wc_get_order( $customer_order );
        if ( ! $order ) {
            continue;
        }



        $item_count = $order->get_item_count() - $order->get_item_count_refunded();
        $order_status = wc_get_order_status_name( $order->get_status() );
    ?>
    <div class="order-history__overview rental-order-card order-status-<?php echo esc_attr( $order->get_status() ); ?>">

        <!-- HEADER: Номер заказа, дата, статус -->
        <div class="order-history__details">
            <time class="order-history__details-date" datetime="<?php echo esc_attr( $order->get_date_created() ? $order->get_date_created()->date( 'c' ) : '' ); ?>">
                <?php echo esc_html( $order->get_date_created() ? wc_format_datetime( $order->get_date_created() ) : '' ); ?>
            </time>
            <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="order-history__details-order">
                <?php /* translators: %s: order number */ ?>
                <?php echo esc_html( 'הזמנה מספר ' . $order->get_order_number()  ); ?>
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 13L1 7L7 1" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>

            <div class="order-total">
                <?php
                    $formatted_total = preg_replace('/[^0-9.]/', '', $order->get_formatted_order_total());
                    $currency_symbol = get_woocommerce_currency_symbol( $order->get_currency() );
                ?>
                <p class="order-history__details-price"><?php echo esc_html( number_format( (float) $formatted_total, 0 ) ); ?> <span><?php echo esc_html( $currency_symbol); ?></span> </p>
            </div>

            <?php //echo esc_html( $order_status ); ?>
        </div>



    </div>


        <!-- BODY: список товаров в заказе, история аренды -->
        <div class="rental-order-card__body">
            <div class="order-history__items">
                <?php
                $items = $order->get_items();
                foreach ( $items as $item_id => $item ) {
                    $product = $item->get_product();
                    if ( ! $product ) {
                        continue;
                    }

                    //echo '<pre>';
                    //    var_dump($product->attributes['pa_product-type']);
                    //echo '</pre>';

                    // Получаем данные для отображения
                    $product_name  = $product->get_name();
                    $product_image = $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'order-history__item-image' ) ); 
                    $item_meta     = $item->get_meta_data(); // если вдруг нужно что-то кастомное
                    // Пример получения атрибутов (например, "размер" - если задан в вариациях)
                    // $size = $product->get_attribute('pa_size'); // или как у вас называется атрибут
                    ?>
                    <div class="order-history__item">
                        <?php echo $product_image; ?>
                        <div class="rental-order-item__details">
                            <h2 class="order-history__item-title"><?php echo esc_html( $product_name ); ?></h2>
                            <!-- Для примера выведем размер, если есть -->
                            <div class="item-size"><?php // echo 'Size: ' . esc_html( $size ); ?></div>
                            <!-- Продавец (если хранится где-то) -->
                            <div class="item-seller"><?php // echo 'Продавец: ' . esc_html( $seller_name ); ?></div>
                            <!-- Дата аренды (условно берем дату заказа) -->
        
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>





    <?php endforeach; ?>
</div>

<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

<!-- Пагинация WooCommerce, если нужна -->
<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
    <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
        <?php if ( 1 !== $current_page ) : ?>
            <a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
        <?php endif; ?>

        <?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
            <a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php else : ?>

    <?php
    // Если заказов нет
    wc_print_notice(
        esc_html__( 'No order has been made yet.', 'woocommerce' ) . ' ' .
        '<a class="woocommerce-Button wc-forward button" href="' . esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ) . '">' .
        esc_html__( 'Browse products', 'woocommerce' ) . '</a>',
        'notice'
    );
    ?>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
