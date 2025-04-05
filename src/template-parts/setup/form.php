<nav class="setup">
    <div class="close_setup">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 6L6 18M6 6L18 18" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
    <div class="return_to go-to" data-go-to="setup-start" data-step="1">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 18L15 12L9 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
</nav>

<?php
$current_user = wp_get_current_user();

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['submit_vendor_form'] ) ) {
    $store_name        = sanitize_text_field( $_POST['store_name'] );
    $store_description = sanitize_textarea_field( $_POST['store_bio'] );
    $logo_id         = '';

    // Обробка завантаження зображення
    if ( ! empty( $_FILES['store-logo']['name'] ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $logo_id = media_handle_upload( 'store-logo', 0 );
        if ( is_wp_error( $logo_id ) ) {
            echo '<p>Помилка при завантаженні зображення: ' . esc_html( $logo_id->get_error_message() ) . '</p>';
            get_footer();
            exit;
        }
    }

    // Додати роль продавця
    if ( ! in_array( 'seller', (array) $current_user->roles ) ) {
        $current_user->set_role( 'seller' );
    }

    // Отримати/підготувати масив налаштувань магазину
    $store_settings = get_user_meta( $current_user->ID, 'dokan_profile_settings', true );
    if ( ! is_array( $store_settings ) ) {
        $store_settings = [];
    }

    $store_settings['store_name']        = $store_name;
    $store_settings['store_ppp']         = 10;
    $store_settings['show_email']        = 'no';

    if ( $logo_id ) {
        $store_settings['gravatar'] = $logo_id;
    }

    // Оновити мета-дані магазину
    update_user_meta( $current_user->ID, 'dokan_profile_settings', $store_settings );

    // (опційно) окремо зберігаємо store_bio
    update_user_meta( $current_user->ID, 'description', $store_description );

    // Перенаправлення
    wp_redirect( home_url() );
    exit;
}

?>

<form id="vendor_form" method="post" enctype="multipart/form-data">
    <div class="form-header">
        <h2 class="setup-title">
            <?php esc_html_e("פרטי החנות", "swap"); ?>
        </h2>
        <p class="setup-text">
            <?php esc_html_e("הזן את פרטי החנות החדשה שלך.", "swap"); ?>
        </p>
    </div>
    <section class="form-section" data-section="1">
        <div class="name_store">
            <span><?php esc_html_e("שם החנות", "swap") ?></span>
            <input 
                name="store_name" 
                type="text" 
                placeholder="<?php esc_attr_e("הזן/י את שם החנות", "swap") ?>"
                required
                data-parsley-minlength="2"
                data-parsley-trigger="focusout"
                data-parsley-minlength-message="חייב להיות לפחות 2 תווים"
                data-parsley-required-message="שדה זה הינו חובה"
            >
        </div>

        <div class="store-biography">
            <span><?php esc_html_e("ביוגרפיה", "swap") ?></span>
            <textarea 
                name="store_bio" 
                required
                placeholder="<?php esc_attr_e("הוספת תיאור קצר", "swap") ?>"
                data-parsley-minlength="30" 
                data-parsley-maxlength="128" 
                data-parsley-trigger="focusout"
                data-parsley-maxlength-message="לא יותר מ-128 תווים"
                data-parsley-minlength-message="חייב להיות לפחות 30 תווים"
                data-parsley-required-message="שדה זה הינו חובה"
            ></textarea>
            <div class="store-biography-count"><span class="details-count-current">0</span>/<span>128</span></div>
        </div>
    </section>

    <section class="logo-section" data-section="2">
        <div class="logo-border">
            <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M25.0001 10L25.0001 40M40.0001 25L10.0001 25" stroke="#A2845E" stroke-width="4.16667" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="store-name">
            123
        </div>
        <?php get_template_part("src/template-parts/setup/modal/add-logo"); ?>
    </section>


    <section class="logo-section-config" data-section="3">
        <div class="logo-border">
           <img src="" alt="">
           <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g filter="url(#filter0_d_1366_26478)">
                <rect x="2" y="2" width="36" height="36" rx="18" fill="white" shape-rendering="crispEdges"/>
                <path d="M12.3113 25.1984C12.3493 24.8561 12.3683 24.685 12.4201 24.525C12.4661 24.3831 12.531 24.2481 12.6131 24.1235C12.7057 23.9832 12.8274 23.8614 13.0709 23.6179L24.0031 12.6858C24.9174 11.7714 26.3999 11.7714 27.3143 12.6858C28.2286 13.6001 28.2286 15.0826 27.3142 15.997L16.3821 26.9291C16.1386 27.1726 16.0168 27.2943 15.8765 27.3869C15.7519 27.469 15.6169 27.5339 15.475 27.5799C15.315 27.6317 15.1439 27.6507 14.8016 27.6887L12 28L12.3113 25.1984Z" stroke="#111111" stroke-width="1.5" stroke-linejoin="round"/>
                </g>
                <defs>
                <filter id="filter0_d_1366_26478" x="0" y="0" width="40" height="40" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                <feOffset/>
                <feGaussianBlur stdDeviation="1"/>
                <feComposite in2="hardAlpha" operator="out"/>
                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/>
                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_1366_26478"/>
                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1366_26478" result="shape"/>
                </filter>
                </defs>
            </svg>
        </div>
        <div class="store-name">
            123
        </div>
        <?php get_template_part("src/template-parts/setup/modal/control-logo"); ?>
    </section>

    <input type="file" name="store-logo">

</form>
<div class="setup-btn form-section-js" data-go-to="2">
    <button class="black">
        <?php esc_html_e("התחלה", "swap"); ?>
    </button>

</div>