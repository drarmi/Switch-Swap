<?php
/**
 * Overridden My Bookings Template
 * Переопределяет wp-content/plugins/woocommerce-bookings/templates/myaccount/bookings.php
 *
 * Расположите этот файл в:
 *   wp-content/themes/storefront-child/woocommerce-bookings/myaccount/bookings.php
 */

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce Bookings здесь обычно передаёт:
 *   - $tables           (массив с группами бронирований)
 *   - $bookings_per_page
 *   - $page
 *   - и другие переменные для пагинации
 */

// Стили + верстка сделаны так же, как у вас в orders.php
?>

<?php do_action( 'woocommerce_before_account_bookings' ); ?>

<?php if ( ! empty( $tables ) ) : ?>

    <style>
    .rental-orders-grid {
        display: grid;
        grid-template-columns: 1fr 1fr; /* 2 колонки */
        gap: 20px;
        margin-bottom: 20px;
    }
    .rental-order-card {
        border: 1px solid #ccc;
        padding: 15px;
        background: #fff;
    }
    .rental-order-card__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .rental-order-card__body {
        margin-bottom: 10px;
    }
    .rental-order-items {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 10px;
    }
    .rental-order-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        border: 1px solid #eee;
        padding: 10px;
    }
    .rental-order-item__img img {
        max-width: 60px;
        height: auto;
        display: block;
    }
    .rental-order-item__details {
        flex: 1;
    }
    .rental-order-card__footer {
        text-align: right;
    }
    .order-total {
        font-weight: bold;
    }
    </style>

    <div class="rental-orders-grid">
    <?php
    // В $tables несколько "секций" бронирований, например «Текущие», «Прошедшие» и т.д.
    foreach ( $tables as $table ) :
        // $table['header'] – заголовок секции (например, "Upcoming Bookings")
        // $table['bookings'] – массив объектов WC_Booking
        ?>
        <!-- Если хотите выводить заголовок секции, раскомментируйте: -->
        <!-- <h2><?php // echo esc_html( $table['header'] ); ?></h2> -->

        <?php foreach ( $table['bookings'] as $booking ) : ?>
            <?php
            if ( ! $booking ) {
                continue;
            }

            // Получаем связанный заказ
            $order = $booking->get_order();
            if ( ! $order ) {
                // У бронирования может не быть заказа (создан вручную админом).
                // Пропускаем, или выводим что-то иное
                continue;
            }

            // Дата и статус заказа
            $order_status      = $order->get_status(); // например, "cancelled"
            $order_status_name = wc_get_order_status_name( $order_status ); // "Cancelled"
            $order_number      = '#' . $order->get_order_number();
            $order_url         = $order->get_view_order_url();

            // Время оформления заказа
            $order_date_obj = $order->get_date_created();
            $order_date     = $order_date_obj ? wc_format_datetime( $order_date_obj ) : '';

            // Кол-во товаров, общая сумма
            $item_count     = $order->get_item_count() - $order->get_item_count_refunded();
            $order_total    = $order->get_formatted_order_total();
            ?>
            <div class="rental-order-card <?php echo esc_attr( 'order-status-' . $order_status ); ?>">
                <!-- HEADER: Номер заказа, дата, статус -->
                <div class="rental-order-card__header">
                    <div>
                        <a href="<?php echo esc_url( $order_url ); ?>">
                            <?php echo esc_html( $order_number ); ?>
                        </a>
                    </div>
                    <div>
                        <?php if ( $order_date ) : ?>
                            <time datetime="<?php echo esc_attr( $order_date_obj->date( 'c' ) ); ?>">
                                <?php echo esc_html( $order_date ); ?>
                            </time>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php echo esc_html( $order_status_name ); ?>
                    </div>
                </div>

                <!-- BODY: список товаров в заказе -->
                <div class="rental-order-card__body">
                    <div class="rental-order-items">
                        <?php
                        $items = $order->get_items();
                        foreach ( $items as $item_id => $item_data ) {
                            $product = $item_data->get_product();
                            if ( ! $product ) {
                                continue;
                            }
                            $product_name  = $product->get_name();
                            $product_image = $product->get_image(); 
                            ?>
                            <div class="rental-order-item">
                                <div class="rental-order-item__img">
                                    <?php echo $product_image; ?>
                                </div>
                                <div class="rental-order-item__details">
                                    <div class="item-title"><strong><?php echo esc_html( $product_name ); ?></strong></div>
                                    <!-- Какие-то доп. поля, если нужно (size, seller) -->
                                    <div class="item-size"></div>
                                    <div class="item-seller"></div>
                                    <div class="item-rental-date">
                                        <?php
                                        echo __( 'Rental date:', 'your-textdomain' ) . ' ' . esc_html( $order_date );
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        } // end foreach items
                        ?>
                    </div>
                </div>

                <!-- FOOTER: общая стоимость, кнопки -->
                <div class="rental-order-card__footer">
                    <div class="order-total">
                        <?php
                        // Пример: "11.00 ₪ for 1 item" 
                        printf(
                            // translators: 1: formatted order total 2: total order items
                            _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ),
                            $order_total,
                            $item_count
                        );
                        ?>
                    </div>
                </div>
            </div>

        <?php endforeach; // $table['bookings'] ?>
    <?php endforeach; // $tables ?>
    </div>

    <?php 
    // Пример пагинации, если нужно:
    do_action( 'woocommerce_before_account_bookings_pagination' );
    ?>
    <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
        <?php if ( ! empty( $page ) && 1 !== $page ) : ?>
            <a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button"
               href="<?php echo esc_url( wc_get_endpoint_url( 'bookings', $page - 1 ) ); ?>">
               <?php esc_html_e( 'Previous', 'woocommerce-bookings' ); ?>
            </a>
        <?php endif; ?>

        <?php
        // Если нужно, можно добавить проверку "есть ли ещё бронирований" и вывод кнопки Next
        ?>
    </div>
    <?php do_action( 'woocommerce_after_account_bookings_pagination' ); ?>

<?php else : ?>
    <!-- Если нет бронирований -->
    <div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
        <a class="woocommerce-Button button" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
            <?php esc_html_e( 'Go Shop', 'woocommerce-bookings' ); ?>
        </a>
        <?php esc_html_e( 'No bookings available yet.', 'woocommerce-bookings' ); ?>
    </div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_bookings' ); ?>
