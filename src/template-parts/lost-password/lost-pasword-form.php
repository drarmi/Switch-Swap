    <div class="lost-form">
        <h4 class="title"><?php esc_html_e('שכחת סיסמה?', 'omnis_base'); ?></h4>
        <p class="description">
            <?php esc_html_e('יש להזין את המייל שאיתו נרשמת אלינו, ונתחיל בהגדרת סיסמה חדשה.', 'omnis_base'); ?></p>
        <form id="custom-lost-password-form">
            <div class="auth-field">
                <input type="email" name="user_login" id="user_login" required aria-required="true" />
                <div class="input-info">
                    <?php esc_attr_e("מייל", "omnis_base"); ?>
                </div>
            </div>
            <?php wp_nonce_field('lost_password', 'custom-lost-password-nonce'); ?>
            <div id="response-message"></div>
            <div class="form-row">
                <button type="submit" class="button-grey" name="lost-password">
                    <?php esc_html_e('אפס סיסמה', "omnis_base"); ?>
                </button>
            </div>
        </form>
    </div>