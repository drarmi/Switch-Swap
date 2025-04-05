<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$ID = $args["ID"];
$availability = get_post_meta($ID, '_wc_booking_default_date_availability')[0] ?? "";

if($availability != "available") {
    return;
}

$rent_date = get_rent_date($ID);

$checkout_url = wc_get_checkout_url();
$cart_url = wc_get_cart_url();
$max_duration = get_post_meta($ID, '_wc_booking_max_duration')[0] ?? "";
$max_period = get_post_meta($ID, '_wc_booking_max_date')[0] ?? "";
$discount_period = get_post_meta($ID, '_wc_booking_pricing');
$one_day = get_post_meta($ID, '_wc_booking_block_cost')[0] ?? "";
$days_range_4 = 0;
$days_range_8 = 0;
$days_range_discount_4 = 0;
$days_range_discount_8 = 0;
?>
<div class="wrap-border">
    <div class="bay-date-wrap">
        <div class="section-header">
            <label for="date-radio">
                <?php esc_html_e("להשכרה", "swap"); ?>
                <input name="card-radio" type="radio" id="date-radio" value="date-radio">
            </label>
            <div class="min-price">
                <?php esc_html_e(" החל מ ", "swap") ?> <?php echo esc_html($one_day) ?>
                <?php esc_html_e(" ₪ ליום", "swap") ?>
            </div>
        </div>
        <div class="section-body">
            <div>
                <span>
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M14.1333 8.5L12.8004 7.16667L11.4666 8.5M13 7.5C13 10.8137 10.3137 13.5 7 13.5C3.68629 13.5 1 10.8137 1 7.5C1 4.18629 3.68629 1.5 7 1.5C9.20128 1.5 11.1257 2.68542 12.1697 4.45273M7 4.16667V7.5L9 8.83333"
                            stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <span><?php esc_html_e("השכרה ממושכת יותר חסכונית יותר", "swap") ?></span>
            </div>
            <div class="grid-section">
                <div class="section-select"
                    data-product-id="<?php echo esc_attr($ID); ?>" data-day="<?php echo esc_attr(1); ?>"
                    data-price="<?php echo esc_attr($one_day); ?>">
                    <div class="day">
                        <?php esc_html_e("יום אחד", "swap") ?>
                    </div>
                    <div class="price">
                        <?php echo esc_html($one_day); ?>
                        ₪
                    </div>

                    <div class="saving">
                        <span class="gold">
                            <?php esc_attr_e("מחיר מלא", "swap") ?>
                        </span>
                    </div>
                </div>

                <?php if (!empty($discount_period[0])): ?>
                    <?php foreach ($discount_period[0] as $value) :
                        $discount = $value["cost"];
                        $discount_percent = round((($discount / $one_day) * 100), 2);
                        $from = (int)$value["from"];
                        if($from == 4) {
                            $days_range_4 = $discount_percent;
                            $days_range_discount_4 = $discount;
                        } elseif($from == 8) {
                            $days_range_8 = $discount_percent;
                            $days_range_discount_8 = $discount;
                        }

                        $discounted_price = $one_day - $discount;

                        $full_discount = round(($discount * $from), 2);
                        $full_discounted_price = round(($discounted_price * $from), 2)
                    ?>
                        <div class="section-select"
                            data-product-id="<?php echo esc_attr($ID); ?>"
                            data-day="<?php echo esc_attr($from); ?>"
                            data-price="<?php echo esc_attr($full_discounted_price); ?>"
                            discount_percent="<?php echo esc_attr($discount_percent); ?>">
                            <div class="day">
                                <?php echo esc_html($from); ?> <?php esc_html_e(" ימים", "swap") ?>
                            </div>
                            <div class="price">
                                <?php echo esc_html($full_discounted_price); ?>
                                ₪
                            </div>

                            <div class="saving">
                                <span class="saving-price">
                                    <?php echo esc_html($discounted_price); ?>
                                </span>
                                <span>
                                    <?php esc_html_e("₪ / ליום", "swap") ?>
                                </span>
                                <span class="gold">
                                    <?php esc_html_e(" חיסכון", "swap") ?>
                                    <?php echo esc_html($discount_percent) ?>%
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
               
                <div class="section-select range-none"
                    days_range
                    data-product-id="<?php echo esc_attr($ID); ?>"
                    days_range_4="<?php echo esc_attr($days_range_4) ?>"
                    days_range_8="<?php echo esc_attr($days_range_8) ?>"
                    data-price="<?php echo esc_attr($one_day); ?>">
                    <div class="day">
                        <?php esc_html_e("תאריכים
טווח"); ?>
                    </div>
                    <div class="price">
                        <?php esc_html_e("בחר/י טווח למחיר"); ?>
                    </div>

                    <div class="saving">
                        <span>
                            <?php esc_html_e("מה המחיר?", "swap") ?>
                        </span>
                        <span class="gold">
                            <?php esc_html_e("יחושב בבחירה!", "swap") ?>
                        </span>
                    </div>
                </div>
                <div class="section-select range-selected active"
                    style="display: none"
                    days_range
                    data-product-id="<?php echo esc_attr($ID); ?>"
                    days_range_4="<?php echo esc_attr($days_range_4) ?>"
                    days_range_8="<?php echo esc_attr($days_range_8) ?>"
                    data-price="<?php echo esc_attr($one_day); ?>">
                    <div class="day">
                    טווח: <span class="select-count">22</span> טווח
                    </div>
                    
                    <div class="price">
                        <span class="select-price">222</span>
                        ₪
                    </div>

                    <div class="saving">
                        <span class="saving-price">
                        <span class="select-count-discount-price">22</span>
                        </span>
                        <span>
                            <?php esc_html_e("₪ / ליום", "swap") ?>
                        </span>
                        <span class="gold">
                            <?php esc_html_e(" חיסכון", "swap") ?>
                            <span class="select-count-discount-percent">22</span>%
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <div class="calender-wrap" style="display: none;">
            <div class="calender-top-tex">
                <h3><?php esc_html_e("תאריכים", "swap") ?></h3>
                <p><?php esc_html_e("סמן/י כדי לבחור תאריך התחלה, רצוי 1-2 ימים לפני שאת/ה מתכנן ללבוש אותו.", "swap") ?></p>
            </div>
            <div class="calender-picker-wrap" data-product-id="<?php echo esc_attr($ID); ?>">
                <div class="calendar-picker-container" data-rent-date='<?php echo esc_attr(json_encode($rent_date, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>' data-max-month-period="<?php echo esc_attr($max_period) ?>"></div>
                <div class="calendar-picker-container-range" data-rent-date='<?php echo esc_attr(json_encode($rent_date, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>' data-max-month-period="<?php echo esc_attr($max_period) ?>" data-max-duration="<?php echo esc_attr($max_duration); ?>" days_discount_4="<?php echo esc_attr($days_range_discount_4) ?>" days_discount_8="<?php echo esc_attr($days_range_discount_8) ?>" days_range="<?php echo esc_attr($one_day) ?>"></div>
            </div>

            <div class="from-to">
                <div class="from">
                    <span>
                        <svg width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M14.8333 18.3332L18.1667 14.9998M18.1667 14.9998L14.8333 11.6665M18.1667 14.9998H11.5M5.66667 3.33317H5.5C4.09987 3.33317 3.3998 3.33317 2.86502 3.60565C2.39462 3.84534 2.01217 4.22779 1.77248 4.69819C1.5 5.23297 1.5 5.93304 1.5 7.33317V8.33317M5.66667 3.33317H12.3333M5.66667 3.33317V1.6665M5.66667 3.33317V4.99984M12.3333 3.33317H12.5C13.9001 3.33317 14.6002 3.33317 15.135 3.60565C15.6054 3.84534 15.9878 4.22779 16.2275 4.69819C16.5 5.23297 16.5 5.93304 16.5 7.33317V8.33317M12.3333 3.33317V1.6665M12.3333 3.33317V4.99984M1.5 8.33317V14.3332C1.5 15.7333 1.5 16.4334 1.77248 16.9681C2.01217 17.4386 2.39462 17.821 2.86502 18.0607C3.3998 18.3332 4.09987 18.3332 5.5 18.3332H9.41667M1.5 8.33317H16.5M16.5 8.33317V9.58317"
                                stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span><?php esc_html_e("הגעה עד:", "swap") ?></span>
                    <span class="from-date"><input id="from-datepicker" type="text" /></span>
                </div>
                <div class="from-range">
                    <span>
                        <svg width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M14.8333 18.3332L18.1667 14.9998M18.1667 14.9998L14.8333 11.6665M18.1667 14.9998H11.5M5.66667 3.33317H5.5C4.09987 3.33317 3.3998 3.33317 2.86502 3.60565C2.39462 3.84534 2.01217 4.22779 1.77248 4.69819C1.5 5.23297 1.5 5.93304 1.5 7.33317V8.33317M5.66667 3.33317H12.3333M5.66667 3.33317V1.6665M5.66667 3.33317V4.99984M12.3333 3.33317H12.5C13.9001 3.33317 14.6002 3.33317 15.135 3.60565C15.6054 3.84534 15.9878 4.22779 16.2275 4.69819C16.5 5.23297 16.5 5.93304 16.5 7.33317V8.33317M12.3333 3.33317V1.6665M12.3333 3.33317V4.99984M1.5 8.33317V14.3332C1.5 15.7333 1.5 16.4334 1.77248 16.9681C2.01217 17.4386 2.39462 17.821 2.86502 18.0607C3.3998 18.3332 4.09987 18.3332 5.5 18.3332H9.41667M1.5 8.33317H16.5M16.5 8.33317V9.58317"
                                stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span><?php esc_html_e("הגעה עד:", "swap") ?></span>
                    <span class="from-date"><input id="range-datepicker" type="text" /></span>
                </div>
                <div class="to">
                    <span>
                        <svg width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M14.8333 18.3332L18.1667 14.9998M18.1667 14.9998L14.8333 11.6665M18.1667 14.9998H11.5M5.66667 3.33317H5.5C4.09987 3.33317 3.3998 3.33317 2.86502 3.60565C2.39462 3.84534 2.01217 4.22779 1.77248 4.69819C1.5 5.23297 1.5 5.93304 1.5 7.33317V8.33317M5.66667 3.33317H12.3333M5.66667 3.33317V1.6665M5.66667 3.33317V4.99984M12.3333 3.33317H12.5C13.9001 3.33317 14.6002 3.33317 15.135 3.60565C15.6054 3.84534 15.9878 4.22779 16.2275 4.69819C16.5 5.23297 16.5 5.93304 16.5 7.33317V8.33317M12.3333 3.33317V1.6665M12.3333 3.33317V4.99984M1.5 8.33317V14.3332C1.5 15.7333 1.5 16.4334 1.77248 16.9681C2.01217 17.4386 2.39462 17.821 2.86502 18.0607C3.3998 18.3332 4.09987 18.3332 5.5 18.3332H9.41667M1.5 8.33317H16.5M16.5 8.33317V9.58317"
                                stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span><?php esc_html_e("החזרה עד:", "swap") ?></span>
                    <span class="to-date"><input type="text" id="to-datepicker" /></span>
                </div>
            </div>
            <div class="total">
                <span class="number">{{price}}</span>
                <span>₪</span>
            </div>
            <div class="delivery">
                <?php esc_html_e("סה”כ לתשלום כולל משלוח.") ?>
            </div>

            <div class="nav-bnt">
                <!-- Checkout Button -->
                <a type="submit" href="<?php echo esc_url(add_query_arg([
                                            'add-to-cart' => $data["product_id"] ?? "",
                                            'product_id' => $data["product_id"] ?? "",
                                            'variation_id' => $data["variation_id"] ?? "",
                                            'quantity' => 1,
                                        ], $checkout_url ?? "")); ?>" class="bay button-checkout make-reservation-js">
                    <?php esc_html_e("לתשלום", ""); ?>
                </a>
                <!-- Add to Cart Button -->
                <a type="submit" href="<?php echo esc_url(add_query_arg([
                                            'add-to-cart' => $data["product_id"] ?? "",
                                            'product_id' => $data["product_id"] ?? "",
                                            'variation_id' => $data["variation_id"] ?? "",
                                            'quantity' => 1,
                                        ], $cart_url ?? "")); ?>" class="cart button-add-to-cart make-reservation-js">
                    <?php esc_html_e("הוספה לסל", ""); ?>
                </a>
            </div>
        </div>



    </div>
</div>