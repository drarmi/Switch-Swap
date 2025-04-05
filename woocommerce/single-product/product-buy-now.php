<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$id = $args["ID"];
$cart_url = wc_get_cart_url($id);
$checkout_url = wc_get_checkout_url($id);

$regular_price = get_post_meta($id, '_regular_price');
$sale_price = get_post_meta($id, '_sale_price');

?>
<div class="wrap-border">
    <div class="bay-now-wrap">
        <div class="section-header">
            <label for="buy-radio">
                <?php esc_html_e("לקנייה", ""); ?>
                <input name="card-radio" type="radio" id="buy-radio" value="buy-radio">
            </label>
            <?php if(!empty($sale_price[0])): ?>
                <div class="total sale_price">
                    <div class="regular_price">
                        <span><?php echo esc_html(!empty($regular_price[0]) ? $regular_price[0] : ""); ?></span>
                        <span>₪</span>
                    </div>
                    
                    <div class="sale_price">
                        <span><?php echo esc_html(!empty($sale_price[0]) ? $sale_price[0] : ""); ?></span>
                        <span>₪</span>
                    </div>
                </div>
            <?php else: ?>
                <div class="total">
                    <div class="regular_price">
                        <span><?php echo esc_html(!empty($regular_price[0]) ? $regular_price[0] : ""); ?></span>
                        <span>₪</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="section-body">
            <div class="total">
                <span><?php echo esc_html(empty($sale_price[0]) ? $regular_price[0] : $sale_price[0]); ?></span>
                <span>₪</span>
            </div>

            <div class="include_delivery">
                <?php esc_html_e("סה”כ לתשלום כולל משלוח.", ""); ?>
            </div>

            <div class="nav-bnt">
                <!-- Checkout Button -->
                <a type="submit" href="<?php echo esc_url(add_query_arg([
                                                'add-to-cart' => $id,
                                                'quantity' => 1,
                                            ], $checkout_url)); ?>" class="bay button-checkout">
                    <?php esc_html_e("לתשלום", ""); ?>
                </a>
                <!-- Add to Cart Button -->
                <a type="submit" href="<?php echo esc_url(add_query_arg([
                                                'add-to-cart' => $id,
                                                'quantity' => 1,
                                            ], $cart_url)); ?>" class="cart button-add-to-cart">
                    <?php esc_html_e("הוספה לסל", ""); ?>
                </a>
            </div>
        </div>
    </div>
</div>