<?php
if ( is_user_logged_in() ) {
    $user_id = get_current_user_id();

    // ✅ Обробка збереження даних
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['details-form_nonce']) && wp_verify_nonce($_POST['details-form_nonce'], 'details-form_action') ) {

        if (isset($_POST['first_name'])) {
            wp_update_user([
                'ID' => $user_id,
                'first_name' => sanitize_text_field($_POST['first_name']),
            ]);
        }

        if (isset($_POST['mail']) && is_email($_POST['mail'])) {
            wp_update_user([
                'ID' => $user_id,
                'user_email' => sanitize_email($_POST['mail']),
            ]);
        }

        if (isset($_POST['phone'])) {
            update_user_meta($user_id, 'billing_phone', sanitize_text_field($_POST['phone']));
        }

        if (!empty($_POST['birthday'])) {
            update_field('birthday', sanitize_text_field($_POST['birthday']), 'user_' . $user_id);
        }

        if (isset($_POST['store_name'])) {
            $dokan_settings = dokan_get_store_info($user_id);
            $dokan_settings['store_name'] = sanitize_text_field($_POST['store_name']);
            dokan_update_store_info($user_id, $dokan_settings);
        }

        if (isset($_POST['biography'])) {
            $dokan_settings = dokan_get_store_info($user_id);
            $dokan_settings['vendor_biography'] = sanitize_textarea_field($_POST['biography']);
            dokan_update_store_info($user_id, $dokan_settings);
        }

        if (!empty($_FILES['store-logo']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            $attachment_id = media_handle_upload('store-logo', 0);
            if (!is_wp_error($attachment_id)) {
                $dokan_settings = dokan_get_store_info($user_id);
                $dokan_settings['banner'] = $attachment_id;
                dokan_update_store_info($user_id, $dokan_settings);
            }
        }

        add_action('wp_footer', function () {
            echo '<script>alert("Дані збережено успішно!");</script>';
        });
    }

    if ( function_exists( 'dokan_is_user_seller' ) && dokan_is_user_seller( $user_id ) ) {
        // Є магазин — виводимо форму
        $user_info = get_userdata($user_id);
        $sex_seller = get_field("sex_seller", 'user_' . $user_id);

        $dokan_settings = dokan_get_store_info($user_id);
        if ( ! is_array($dokan_settings) ) {
            $dokan_settings = []; // fallback
        }

        $first_name = $user_info->first_name;
        $mail = $user_info->user_email;
        $author_logo = !empty($dokan_settings["banner"]) ? wp_get_attachment_url($dokan_settings["banner"]) : get_user_meta($user_id, 'user_logo', true);
        $store_name = $dokan_settings["store_name"] ?? '';
        $phone = !empty($dokan_settings["phone"]) ? $dokan_settings["phone"] : get_user_meta($user_id, 'billing_phone', true);
        $biography = !empty($dokan_settings["vendor_biography"]) ? $dokan_settings["vendor_biography"] : '';

        $birthday = get_field('birthday', 'user_' . $user_id);
        if (!$author_logo) {
            $author_logo = get_avatar_url($user_id);
        }

        if ($birthday) {
            $birthdayObj = DateTime::createFromFormat('Ymd', (string)$birthday);
            $birthdayShow = $birthdayObj->format('Y-m-d'); 
        }

        $currentDateObj = new DateTime();
        $currentDateFormatted = $currentDateObj->format('Y-m-d');
        $currentDateObjMinimum = $currentDateObj->modify('-100 years');
        $currentDateMinimumFormatted = $currentDateObjMinimum->format('Y-m-d');
        ?>

        <section class="personal_details modal-section">
            <a href="<?php echo home_url('profile-page'); ?>" class="profile-top-header to-main-js">
                <h2><?php esc_attr_e("פרטים אישיים", "swap") ?></h2>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 18L15 12L9 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>

            <div>
                <form method="POST" enctype="multipart/form-data" class="personal_details-form">
                    <div class="logo-wrap">
                        <img src="<?php echo esc_url($author_logo); ?>" alt="store-logo">
                        <input type="file" name="store-logo">
                        <div class="edit-logo">[...]</div>
                    </div>

                    <div class="details-section-title"><?php esc_html_e("חנות", "swap") ?></div>

                    <div class="input-wrap">
                        <div class="input-details-wrapper">
                            <input type="text" name="store_name" value="<?php echo esc_attr($store_name); ?>" required>
                            <span class="details-title"><?php esc_attr_e("שם חנות", "swap") ?></span>
                        </div>
                        <div class="textarea-details-wrapper">
                            <textarea class="textarea-details" name="biography" required><?php echo esc_html($biography); ?></textarea>
                            <span class="details-title"><?php esc_attr_e("ביוגרפיה", "swap") ?></span>
                        </div>
                    </div>

                    <div class="details-section-title"><?php esc_html_e("פרופיל", "swap") ?></div>

                    <div class="input-wrap">
                        <div class="input-details-wrapper">
                            <input type="text" name="first_name" value="<?php echo esc_attr($first_name); ?>" required>
                            <span class="details-title"><?php esc_attr_e("שם פרטי", "swap") ?></span>
                        </div>
                        <div class="input-details-wrapper">
                            <input type="email" name="mail" value="<?php echo esc_attr($mail); ?>" required>
                            <span class="details-title"><?php esc_attr_e("מייל", "swap") ?></span>
                        </div>
                        <div class="input-details-wrapper">
                            <input type="text" name="phone" value="<?php echo esc_attr($phone); ?>" required>
                            <span class="details-title"><?php esc_attr_e("טלפון", "swap") ?></span>
                        </div>
                        <div class="input-details-wrapper">
                            <input type="date" name="birthday" min="<?php echo esc_attr($currentDateMinimumFormatted) ?>" max="<?php echo esc_attr($currentDateFormatted) ?>" value="<?php echo esc_attr($birthdayShow ?? ""); ?>">
                            <span class="details-title"><?php esc_attr_e("תאריך לידה", "swap") ?></span>
                        </div>
                    </div>

                    <?php wp_nonce_field('details-form_action', 'details-form_nonce'); ?>

                    <button type="submit" class="authenticated-button-black">
                        <?php esc_attr_e("שמירה", "swap") ?>
                    </button>
                </form>
            </div>
        </section> 
        <?php
    } else {
        echo '<p style="padding: 20px; background: #fff3cd; color: #856404; border: 1px solid #ffeeba;">' . esc_html__('У вас немає магазину. Щоб редагувати профіль, спочатку створіть магазин.', 'swap') . '</p>';
    }
} else {
    echo '<p>' . esc_html__('Будь ласка, увійдіть, щоб переглянути цю сторінку.', 'swap') . '</p>';
}
?>