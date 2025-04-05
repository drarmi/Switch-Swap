<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Omnis\src\inc\classes\pages\Product_Management;

$product_id = (int)sanitize_text_field($_GET["management-productID"]);
$variations = Product_Management::get_variation_id($product_id);

$bidID = $variations["bidID"];
$rentID = $variations["rentID"];
$simpleID = $variations["simpleID"];

wp_get_object_terms($product_id, 'product_type');

$regular_price = get_post_meta($simpleID, '_regular_price', true);
$price = get_post_meta($simpleID, '_price', true);
$sale_price = get_post_meta($simpleID, '_sale_price', true);

if($sale_price){
    $discount_price = round(($regular_price - $sale_price), 2);
    $discount_percent_price = round((($discount_price / $price) * 100), 2);
}



$auction_start_price = get_post_meta($bidID, '_auction_start_price', true);
$auction_dates_from = get_post_meta($bidID, '_auction_dates_from', true);
$auction_dates_to = get_post_meta($bidID, '_auction_dates_to', true);
var_dump($auction_start_price);
var_dump($auction_dates_from);
var_dump($auction_dates_to);



if ($rentID) {
    $wc_booking_block_cost = get_post_meta($rentID, '_wc_booking_block_cost', true);
    $wc_booking_pricing = get_post_meta($rentID, '_wc_booking_pricing', true);

    $days_range_4 = "";
    $days_range_discount_4 = "";
    $days_range_8 = "";
    $days_range_discount_8 = "";


  if (!empty($wc_booking_pricing)){

    foreach ($wc_booking_pricing as $value){
        $discount = $value["cost"];
        $discount_percent = round((($discount / $wc_booking_block_cost) * 100), 2);
        $from = (int)$value["from"];

        if($from == 4) {
            $days_range_4 = $discount_percent;
            $days_range_discount_4 = $discount;
        } elseif($from == 8) {
            $days_range_8 = $discount_percent;
            $days_range_discount_8 = $discount;
        }
    }
  }
}


?>

<div class="section-title">
    <h4><?php esc_html_e("מחיר", "swap") ?></h4>
</div>
<div class="prices-body">

    <?php if ($rentID): ?>
        <div class="row">
            <div class="price-option custom-radio"><?php esc_html_e("השכרה", "swap") ?></div>
            <div>
                <div class="price">
                    <span class="title"><?php esc_html_e("הזנת תעריף יומי", "swap") ?></span>
                    <div class="input-placeholder">
                        <span class="placeholder">₪</span>
                        <input class="row-main-price-js" value="<?php echo esc_attr($wc_booking_block_cost); ?>" type="number" name="renting_price" placeholder="100">
                    </div>
                </div>

                <div class="discounts-by-day">
                    <span class="title"><?php esc_html_e("הנחות לפי ימים", "swap") ?></span>
                    <div class="days row-discount-parent-js">
                        <span class="days-count"><?php esc_html_e("4 יום ומעלה", "swap") ?></span>
                        <div class="input-placeholder">
                            <span class="placeholder">%</span>
                            <input class="row-discount-input-js" type="number" value="<?php echo esc_attr($days_range_4); ?>" name="rent-discount-day-4" placeholder="10">
                        </div>
                        <span class="discount"><span class="discount-price row-discount-result-js"><?php echo esc_attr($days_range_discount_4); ?></span> ₪ ליום</span>
                    </div>
                    <div class="days row-discount-parent-js">
                        <span class="days-count"><?php esc_html_e("8 יום ומעלה", "swap") ?></span>
                        <div class="input-placeholder">
                            <span class="placeholder">%</span>
                            <input class="row-discount-input-js" type="number" value="<?php echo esc_attr($days_range_8); ?>" name="rent-discount-day-8" placeholder="15">
                        </div>
                        <span class="discount"><span class="discount-price row-discount-result-js"><?php echo esc_attr($days_range_discount_8); ?></span> ₪ ליום</span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($simpleID): ?>
        <div class="row">
            <div class="price-option custom-radio"><?php esc_html_e("מכירה", "swap") ?></div>
            <div class="price">
                <span class="title"><?php esc_html_e("מחיר לצרכן", "swap") ?></span>
                <div class="input-placeholder">
                    <span class="placeholder">₪</span>
                    <input class="row-main-price-js" value="<?php echo esc_attr($regular_price); ?>" type="number" name="price-bay-only" placeholder="100">
                </div>
            </div>

            <div class="discounts-by-day">
                <span class="title"><?php esc_html_e("אפשרות הנחה", "swap") ?></span>
                <div class="days row-discount-parent-js">
                    <span class="days-count"><?php echo esc_html_e("אחוז הנחה", "swap") ?></span>
                    <div class="input-placeholder">
                        <span class="placeholder">%</span>
                        <input class="row-discount-input-js" type="number" name="sale-discount" placeholder="10">
                    </div>
                    <span class="discount"><span class="discount-price row-discount-result-js"><?php echo esc_attr($discount_price); ?></span> ₪ ליום</span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($bidID): ?>
        <div class="row">
            <div class="price-option custom-radio"><?php esc_html_e("הגשת הצעות", "swap") ?></div>
            <div class="bids-prices">
                <div class="bid-price">
                    <label><?php esc_html_e("מחיר התחלתי", "swap") ?></label>
                    <div class="input-placeholder">
                        <span class='placeholder'>₪</span>
                        <input type="number" value="<?php echo esc_attr($auction_start_price); ?>" name="min-price-rent" placeholder="300">
                    </div>
                </div>
            </div>
            <div class="bids-dates">
                <div class="bid-date">
                    <label><?php esc_html_e("תחילת המכירה", "swap") ?></label>
                    <div class="input-placeholder">
                        <span class='placeholder'>
                            <svg width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.4981 5.71085H11.0872M3.156 1.50007V2.87377M3.156 2.87377L11.1245 2.87362M3.156 2.87377C1.83569 2.87377 0.765494 3.9625 0.765555 5.30565L0.765925 13.412C0.765986 14.755 1.83626 15.8438 3.15648 15.8438H11.125C12.4453 15.8438 13.5156 14.7549 13.5156 13.4117L13.5152 5.30542C13.5151 3.96236 12.4447 2.87362 11.1245 2.87362M11.1245 1.5V2.87362M5.54715 13.0065V8.14271L3.95344 9.35866M9.92983 13.0065V8.14271L8.33613 9.35866" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <input class="date-validation-parsley" type="text" name="start-date-rent" placeholder="DD/MM/YYYY"
                            pattern="(0[1-9]|[12][0-9]|3[01])(\/|-|.)(0[1-9]|1[0-2])(\/|-|.)(19|20)\d{2}"
                            data-parsley-pattern="(0[1-9]|[12][0-9]|3[01])(\/|-|.)(0[1-9]|1[0-2])(\/|-|.)(19|20)\d{2}"
                            data-parsley-trigger="focusout"
                            data-parsley-pattern-message="DD-MM-YYYY או DD/MM/YYYY או DD.MM.YYYY"
                            data-parsley-check-past-date="true"
                            data-parsley-check-past-date-message="תאריך לא יכול להיות עבר">
                    </div>
                </div>
                <div class="bid-date">
                    <label><?php esc_html_e("סיום המכירה", "swap") ?></label>
                    <div class="input-placeholder">
                        <span class='placeholder'>
                            <svg width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.4981 5.71085H11.0872M3.156 1.50007V2.87377M3.156 2.87377L11.1245 2.87362M3.156 2.87377C1.83569 2.87377 0.765494 3.9625 0.765555 5.30565L0.765925 13.412C0.765986 14.755 1.83626 15.8438 3.15648 15.8438H11.125C12.4453 15.8438 13.5156 14.7549 13.5156 13.4117L13.5152 5.30542C13.5151 3.96236 12.4447 2.87362 11.1245 2.87362M11.1245 1.5V2.87362M5.54715 13.0065V8.14271L3.95344 9.35866M9.92983 13.0065V8.14271L8.33613 9.35866" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <input class="date-validation-parsley" type="text" name="end-date-rent" placeholder="DD/MM/YYYY"
                            pattern="(0[1-9]|[12][0-9]|3[01])(\/|-|.)(0[1-9]|1[0-2])(\/|-|.)(19|20)\d{2}"
                            data-parsley-pattern="(0[1-9]|[12][0-9]|3[01])(\/|-|.)(0[1-9]|1[0-2])(\/|-|.)(19|20)\d{2}"
                            data-parsley-trigger="focusout"
                            data-parsley-pattern-message="DD-MM-YYYY או DD/MM/YYYY או DD.MM.YYYY"
                            data-parsley-check-past-date="true"
                            data-parsley-check-past-date-message="תאריך לא יכול להיות עבר">
                    </div>
                </div>
            </div>
            <div class="aditional-options">
                <div class="aditional-option bids-buy-now">
                    <label class="custom-radio">
                        <input type="checkbox" name="bids-buy-now">
                        <span></span>
                        <?php esc_html_e("הוספת אפשרות “קני עכשיו”", "swap"); ?>
                    </label>

                    <div class="limited-discount-rent-wrap aditional-options-wrap">
                        <div class="price">
                            <span class="title"><?php esc_html_e("מחיר לצרכן", "swap") ?></span>
                            <div class="input-placeholder">
                                <span class="placeholder">₪</span>
                                <input class="row-main-price-js" type="number" name="price-bay-now" placeholder="100">
                            </div>
                        </div>

                        <div class="discounts-by-day">
                            <span class="title"><?php esc_html_e("אפשרות הנחה", "swap") ?></span>
                            <div class="days row-discount-parent-js">
                                <span class="days-count"><?php echo esc_html_e("אחוז הנחה", "swap") ?></span>
                                <div class="input-placeholder">
                                    <span class="placeholder">%</span>
                                    <input class="row-discount-input-js" type="number" name="discount-bay-now" placeholder="10">
                                </div>
                                <span class="discount"><span class="discount-price row-discount-result-js">90</span> ₪ ליום</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="show-bids-condition">
                <button><?php esc_html_e("לתקנון ולתנאים", "swap"); ?></button>
            </div>
        </div>
    <?php endif; ?>

    <div class="item-condition">
        <span class="title">מצב הפריט</span>
        <div class="conditions-list">
            <label class="condition-btn">
                <input type="radio" name="condition" value="new">
                חדש
            </label>
            <label class="condition-btn">
                <input type="radio" name="condition" value="good">
                במצב טוב
            </label>
            <label class="condition-btn">
                <input type="radio" name="condition" value="used">
                משומש
            </label>
        </div>
    </div>
</div>