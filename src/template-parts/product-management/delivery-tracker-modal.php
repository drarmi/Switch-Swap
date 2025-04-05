<?php
    $title = $args["title"] ?? "";
    $type = $args["type"] ?? "";
?>

<div class="delivery-modal-wrapper">
    <div class="delivery-modal" data-type="<?php echo esc_attr($type); ?>">
        <div class="close-must-log-in-modal close-must-log-in-modal-js">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18M6 6L18 18" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </div>

        <svg width="30" height="29" viewBox="0 0 30 29" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14.9998 9.16602V14.4993M14.9998 19.8327H15.0132M28.3332 14.4993C28.3332 21.8631 22.3636 27.8327 14.9998 27.8327C7.63604 27.8327 1.6665 21.8631 1.6665 14.4993C1.6665 7.13555 7.63604 1.16602 14.9998 1.16602C22.3636 1.16602 28.3332 7.13555 28.3332 14.4993Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>

        <h2><?php echo esc_html($title); ?></h2>

        <div class="nav-list">
            <div>
                <a class="black" href="<?php echo home_url("login") ?>"><?php esc_html_e("יש לך חשבון?") ?></a>
            </div>

            <div>
                <a class="transparent" href="<?php echo home_url("customer-registration") ?>"><?php esc_html_e("יש לך חשבון?") ?></a>
            </div>
        </div>
    </div>
</div>