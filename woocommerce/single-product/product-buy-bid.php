<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$ID = $args["ID"];
$author_id = $args["author_id"];
$start_price = get_post_meta($ID, '_auction_start_price');
$current_bid = floatval(get_post_meta($ID, '_auction_current_bid', true));

$bid = $current_bid ? $current_bid : $start_price[0];

$bid_count = absint(get_post_meta($ID, '_auction_bid_count', true));
$previous_bidder = get_post_meta($ID, '_auction_current_bidder', true);
$bid_log = get_post_meta($ID, '_auction_log', true);
$history = get_post_meta($ID, '_auction_history', true);





?>
<div class="wrap-border">
    <div class="bay-contact-wrap">
        <div class="section-header">
            <label for="submit-offer">
                <?php esc_html_e("להגשת הצעה", "swap"); ?>
                <input name="card-radio" type="radio" id="submit-offer" value="submit-offer">
            </label>
            <div class="min-offer" data-min-offer="<?php echo esc_attr($bid) ?>">
                <?php esc_html_e("החל מ-", "swap") ?> <span class="min-offer-bid"><?php echo esc_html__($bid) ?> </span>₪
            </div>
        </div>

        <div class="section-body">
            <div class="info">
                <span>
                    <svg width="12" height="15" viewBox="0 0 12 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M5.8 7.5L3.23627 5.36356C2.85536 5.04613 2.6649 4.88742 2.52798 4.69286C2.40667 4.52048 2.31657 4.32812 2.26181 4.12457C2.2 3.89483 2.2 3.64691 2.2 3.15108V1.5M5.8 7.5L8.36373 5.36356C8.74464 5.04613 8.9351 4.88742 9.07202 4.69286C9.19333 4.52048 9.28343 4.32812 9.33819 4.12457C9.4 3.89483 9.4 3.64691 9.4 3.15108V1.5M5.8 7.5L3.23627 9.63644C2.85536 9.95387 2.6649 10.1126 2.52798 10.3071C2.40667 10.4795 2.31657 10.6719 2.26181 10.8754C2.2 11.1052 2.2 11.3531 2.2 11.8489V13.5M5.8 7.5L8.36373 9.63644C8.74464 9.95387 8.9351 10.1126 9.07202 10.3071C9.19333 10.4795 9.28343 10.6719 9.33819 10.8754C9.4 11.1052 9.4 11.3531 9.4 11.8489V13.5M1 1.5H10.6M1 13.5H10.6"
                            stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <span>
                    <?php esc_html_e("נעדכן אותך עד 48 שעות על קבלת/דחיית ההצעה.", "swap") ?>
                </span>
            </div>
            <form class="offer-form" action="">
                <label for="offer-form-input">
                <input id="offer-form-input" name="offer-form-input"
                    type="number"
                    placeholder="<?php esc_html_e("ההצעה שלך", "swap") ?>"
                    data-parsley-min="<?php echo esc_attr($bid + 1); ?>"
                    data-parsley-min-message="התעריף חייב להיות מעל המינימום"
                    data-parsley-errors-container="#error-container"
                    data-parsley-trigger="focusout change">
                </label>
                <input type="text" name="author_id" hidden value="<?php echo esc_attr($author_id); ?>">
                <input type="text" name="product_id" hidden value="<?php echo esc_attr($ID); ?>">
                <div id="error-container"></div>
                <div id="response-message"></div>

                <div class="nav-bnt">
                    <button type="submit" class="bay"><?php esc_html_e("הגשת הצעה", "swap") ?></button>
                </div>
            </form>
        </div>
    </div>
</div>