<?php
    if (! defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }

    use Omnis\src\inc\classes\pages\Product_Management;

    $product_id = (int)sanitize_text_field($_GET["management-productID"]);

?>

<div class="section-title">
    <h4><?php esc_html_e("מעקב וסטטוס", "swap") ?></h4>
</div>
<div class="delivery-wrap">
    <div class="switcher-type">
        <ul>
            <li class="renting active"><?php esc_html_e("השכרה", "swap") ?></li>
            <li class="buying"><?php esc_html_e("קנייה מיידית", "swap") ?></li>
            <li class="bid"><?php esc_html_e("הגשת הצעות", "swap") ?></li>
        </ul>
    </div>
    <div class="status">
        <div>
            <?php esc_html_e("סטטוס:", "swap") ?>
        </div>
        <div class="status-value">
            <?php echo Product_Management::getProductStatusHTML($product_id); ?>
        </div>
    </div>

    <div class="renting">
        <div class="delivery-cod">
            <div>
                <?php esc_html_e("מספר מעקב", "swap") ?>
            </div>
            <div class="cod-value">
                23498572039485
            </div>
        </div>

        <?php
            get_template_part("src/template-parts/product-management/delivery-tracker-to-client", null, ["step" => 3, "columns" => 4]);
            //get_template_part("src/template-parts/product-management/delivery-tracker-to-seller", null, ["step" => 1, "columns" => 3]);
        ?>
    </div>

    <div class="buying">

    </div>

    <div class="bid">

    </div>  
    
</div>

