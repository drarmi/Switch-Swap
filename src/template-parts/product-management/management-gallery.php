<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$product_id = (int)sanitize_text_field($_GET["management-productID"]);
$product = wc_get_product($product_id);
$gallery_ids = $product->get_gallery_image_ids();
$thumbnail_id = get_post_thumbnail_id($product_id);
$thumbnail_url = wp_get_attachment_url($thumbnail_id);
$thumbnail_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
$thumbnail_title = get_the_title($thumbnail_id);
$gallery_ids = [$thumbnail_id, ...$gallery_ids];
?>
<div class="section-title">
    <h4><?php esc_html_e("תמונות", "swap") ?></h4>
</div>


<div class="selected-img-wrap">
    <div class="selected-img">
        <div class="img-wrap">
            <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($thumbnail_alt); ?>" alt="<?php echo esc_attr($thumbnail_title); ?>">
        </div>
        <div class="img-editor">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.31128 14.1984C1.34932 13.8561 1.36833 13.685 1.42012 13.525C1.46606 13.3831 1.53098 13.2481 1.6131 13.1235C1.70566 12.9832 1.82742 12.8614 2.07094 12.6179L13.0031 1.68577C13.9174 0.77141 15.3999 0.771411 16.3143 1.68577C17.2286 2.60013 17.2286 4.0826 16.3142 4.99696L5.38213 15.9291C5.1386 16.1726 5.01684 16.2943 4.87648 16.3869C4.75194 16.469 4.61688 16.5339 4.47496 16.5799C4.315 16.6317 4.14385 16.6507 3.80157 16.6887L1 17L1.31128 14.1984Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round" />
            </svg>
        </div>
    </div>
    <div class="pagination-list ui-sortable">
        <?php if ($gallery_ids): ?>
            <?php foreach ($gallery_ids as $key => $gallery_id) : ?>
                <?php
                $image_url = wp_get_attachment_url($gallery_id);
                $image_alt = get_post_meta($gallery_id, '_wp_attachment_image_alt', true);
                $image_title = get_the_title($gallery_id);
                ?>
                <div class="ui-sortable-handle">
                    <div class="num"><?php echo $key + 1 ?></div>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" alt="<?php echo esc_attr($image_title); ?>" data-id="<?php echo esc_attr($gallery_id); ?>">
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php if ($gallery_ids): ?>
    <?php foreach ($gallery_ids as $key => $gallery_id) : ?>
        <?php
        $image_url = wp_get_attachment_url($gallery_id);
        $image_alt = get_post_meta($gallery_id, '_wp_attachment_image_alt', true);
        $image_title = get_the_title($gallery_id);
        ?>
        <input class="selectedIMG" hidden type="text" name="<?php echo esc_attr("selectedIMG-" . ($key + 1)) ?>" src="<?php echo esc_url($image_url) ?>" count="<?php echo esc_attr($key + 1) ?>" data-id="<?php echo esc_attr($gallery_id) ?>" value="<?php echo esc_attr($gallery_id) ?>">
    <?php endforeach; ?>
<?php endif; ?>


<div class="control-element-wrap">
    <div class="control-element">
        <div class="make-photo">
            <span>
                <svg width="20" height="19" viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.66663 6.98037C1.66663 6.68844 1.66663 6.54247 1.67881 6.41952C1.79629 5.23374 2.73435 4.29568 3.92014 4.1782C4.04308 4.16602 4.19692 4.16602 4.50461 4.16602C4.62317 4.16602 4.68245 4.16602 4.73278 4.16297C5.37547 4.12404 5.93825 3.71841 6.17841 3.12101C6.19722 3.07423 6.2148 3.02149 6.24996 2.91602C6.28512 2.81054 6.3027 2.7578 6.32151 2.71102C6.56167 2.11362 7.12445 1.70799 7.76714 1.66906C7.81747 1.66602 7.87306 1.66602 7.98424 1.66602H12.0157C12.1269 1.66602 12.1825 1.66602 12.2328 1.66906C12.8755 1.70799 13.4383 2.11362 13.6784 2.71102C13.6972 2.7578 13.7148 2.81054 13.75 2.91602C13.7851 3.02149 13.8027 3.07423 13.8215 3.12101C14.0617 3.71841 14.6245 4.12404 15.2671 4.16297C15.3175 4.16602 15.3767 4.16602 15.4953 4.16602C15.803 4.16602 15.9568 4.16602 16.0798 4.1782C17.2656 4.29568 18.2036 5.23374 18.3211 6.41952C18.3333 6.54247 18.3333 6.68844 18.3333 6.98037V13.4993C18.3333 14.8995 18.3333 15.5995 18.0608 16.1343C17.8211 16.6047 17.4387 16.9872 16.9683 17.2269C16.4335 17.4993 15.7334 17.4993 14.3333 17.4993H5.66663C4.26649 17.4993 3.56643 17.4993 3.03165 17.2269C2.56124 16.9872 2.17879 16.6047 1.93911 16.1343C1.66663 15.5995 1.66663 14.8995 1.66663 13.4993V6.98037Z" stroke="#8F6B45" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M9.99996 13.7493C11.8409 13.7493 13.3333 12.257 13.3333 10.416C13.3333 8.57507 11.8409 7.08268 9.99996 7.08268C8.15901 7.08268 6.66663 8.57507 6.66663 10.416C6.66663 12.257 8.15901 13.7493 9.99996 13.7493Z" stroke="#8F6B45" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
            <span><?php echo esc_attr_e("צילום תמונה והעלאה", "swap"); ?></span>
        </div>
        <div class="get-photo">
            <span>
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.5 16.5H4.77614C4.2713 16.5 4.01887 16.5 3.90199 16.4002C3.80056 16.3135 3.74674 16.1836 3.75721 16.0506C3.76927 15.8974 3.94776 15.7189 4.30474 15.3619L11.3905 8.27614C11.7205 7.94613 11.8855 7.78112 12.0758 7.7193C12.2432 7.66492 12.4235 7.66492 12.5908 7.7193C12.7811 7.78112 12.9461 7.94613 13.2761 8.27614L16.5 11.5V12.5M12.5 16.5C13.9001 16.5 14.6002 16.5 15.135 16.2275C15.6054 15.9878 15.9878 15.6054 16.2275 15.135C16.5 14.6002 16.5 13.9001 16.5 12.5M12.5 16.5H5.5C4.09987 16.5 3.3998 16.5 2.86502 16.2275C2.39462 15.9878 2.01217 15.6054 1.77248 15.135C1.5 14.6002 1.5 13.9001 1.5 12.5V5.5C1.5 4.09987 1.5 3.3998 1.77248 2.86502C2.01217 2.39462 2.39462 2.01217 2.86502 1.77248C3.3998 1.5 4.09987 1.5 5.5 1.5H12.5C13.9001 1.5 14.6002 1.5 15.135 1.77248C15.6054 2.01217 15.9878 2.39462 16.2275 2.86502C16.5 3.3998 16.5 4.09987 16.5 5.5V12.5M7.75 6.08333C7.75 7.00381 7.00381 7.75 6.08333 7.75C5.16286 7.75 4.41667 7.00381 4.41667 6.08333C4.41667 5.16286 5.16286 4.41667 6.08333 4.41667C7.00381 4.41667 7.75 5.16286 7.75 6.08333Z" stroke="#8F6B45" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
            <span><?php echo esc_attr_e("העלאה מתוך גלריית התמונות", "swap"); ?></span>
        </div>
        <div class="delete-photo">
            <span>
                <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.3333 4.99935V4.33268C12.3333 3.39926 12.3333 2.93255 12.1517 2.57603C11.9919 2.26243 11.7369 2.00746 11.4233 1.84767C11.0668 1.66602 10.6001 1.66602 9.66667 1.66602H8.33333C7.39991 1.66602 6.9332 1.66602 6.57668 1.84767C6.26308 2.00746 6.00811 2.26243 5.84832 2.57603C5.66667 2.93255 5.66667 3.39926 5.66667 4.33268V4.99935M7.33333 9.58268V13.7493M10.6667 9.58268V13.7493M1.5 4.99935H16.5M14.8333 4.99935V14.3327C14.8333 15.7328 14.8333 16.4329 14.5608 16.9677C14.3212 17.4381 13.9387 17.8205 13.4683 18.0602C12.9335 18.3327 12.2335 18.3327 10.8333 18.3327H7.16667C5.76654 18.3327 5.06647 18.3327 4.53169 18.0602C4.06129 17.8205 3.67883 17.4381 3.43915 16.9677C3.16667 16.4329 3.16667 15.7328 3.16667 14.3327V4.99935" stroke="#E22400" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
            <span><?php echo esc_attr_e("מחיקת התמונה", "swap"); ?></span>
        </div>
        <div class="control-element-close"><?php echo esc_attr_e("ביטול", "swap"); ?></div>
    </div>
    <div class="camera-wrap-editor" style="display: none;">
        <video id="video-editor" width="640" height="480" autoplay></video>
        <button id="captureBtn-editor"></button>
        <button id="saveBtn-editor" style="display: none;"><?php esc_html_e("לְהַצִיל", "swap"); ?></button>
        <canvas id="canvas-editor" style="display: none;"></canvas>
        <img id="snapshot-editor" src="" alt="Знімок" style="display: none;">
    </div>
    <input type="file" id="select-images-one" accept="image/*" hidden name="new-product-images">
</div>