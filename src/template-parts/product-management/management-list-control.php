<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div>
    <div class="list-control">
        <div class="add-new-js add-new" id="upload-product">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8 1V15M1 8H15" stroke="#8F6B45" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1 class="management-page-title previousPage-js"><?php esc_html_e("פריטים אחרונים", "swap"); ?></h1>
    </div>

    <div class="sort-product-list-card">
        <ul>
            <li class="type-selection-js active" data-sort="all">
                <?php esc_html_e("הצג הכל", "swap"); ?>
            </li>
            <li class="type-selection-js" data-sort="booking">
                <?php esc_html_e("השכרה", "swap"); ?>
            </li>
            <li class="type-selection-js" data-sort="simple">
                <?php esc_html_e("קנייה מיידית", "swap"); ?>
            </li>
        </ul>
    </div>
</div>