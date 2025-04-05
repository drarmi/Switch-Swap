<?php
/* Template Name: Create Store */

get_header();

if ( ! is_user_logged_in() ) {
    echo '<p>Будь ласка, <a href="' . esc_url( wp_login_url() ) . '">увійдіть</a>, щоб створити магазин.</p>';
    get_footer();
    exit;
}

$current_user = wp_get_current_user();

// Якщо вже продавець — перенаправити до кабінету
if ( in_array( 'seller', (array) $current_user->roles ) ) {
    echo '<p>Ви вже є продавцем. <a href="' . esc_url( dokan_get_navigation_url() ) . '">Перейти в кабінет продавця</a></p>';
    get_footer();
    exit;
}

// Обробка форми
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['submit_vendor_form'] ) ) {
    $store_name        = sanitize_text_field( $_POST['store_name'] );
    $store_description = sanitize_textarea_field( $_POST['store_bio'] );
    $logo_id         = '';

    // Обробка завантаження зображення
    if ( ! empty( $_FILES['store_logo']['name'] ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $logo_id = media_handle_upload( 'store_logo', 0 );
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
    wp_redirect( dokan_get_navigation_url() );
    exit;
}
?>

<div class="create-store-form" style="max-width: 600px; margin: 0 auto;">
    <h2>Створити магазин</h2>
    <form method="post" enctype="multipart/form-data">
        <p>
            <label for="store_name">Назва магазину</label><br>
            <input type="text" name="store_name" id="store_name" required style="width: 100%;">
        </p>
        <p>
            <label for="store_description">Опис магазину</label><br>
            <textarea name="store_bio" id="store_bio" rows="5" required style="width: 100%;"></textarea>
        </p>
        <p>
            <label for="store_logo">Банер магазину (зображення)</label><br>
            <input type="file" name="store_logo" id="store_logo" accept="image/*">
        </p>
        <p>
            <input type="submit" name="submit_vendor_form" value="Створити магазин">
        </p>
    </form>
</div>

<?php get_footer(); ?>