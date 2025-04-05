<?php
namespace Omnis\src\inc\classes\dokan_enhancer;
class Authenticate_seller {
	/**
	 * Экземпляр класса.
	 *
	 * @var Authenticate_seller
	 */
	private static $instance;

	/**
	 * Конструктор. Регистрирует все хуки.
	 */
	public function __construct() {
		// Расширяем соц. поля Dokan: добавляем поле TikTok.
		add_filter( 'dokan_profile_social_fields', [ $this, 'add_tiktok_field' ] );

		// AJAX‑обработчик для получения TikTok-ссылки (страница одного вендора).
		add_action( 'wp_ajax_my_get_tiktok_link', [ $this, 'get_tiktok_link' ] );

		// AJAX‑обработчик для получения статуса верификации вендора.
		add_action( 'wp_ajax_my_get_vendor_verification_status', [ $this, 'get_vendor_verification_status' ] );

		// AJAX‑обработчик для обновления статуса верификации.
		add_action( 'wp_ajax_my_update_vendor_verification_status', [ $this, 'update_vendor_verification_status' ] );

		// Подключаем скрипты для админки.
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

		// Шорткод для формы запроса верификации.
		add_shortcode( 'authenticated_seller', [ $this, 'authenticated_seller_shortcode' ] );
	}

	/**
	 * Возвращает экземпляр класса (синглтон).
	 *
	 * @return Authenticate_seller
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Добавляет поле TikTok в список соц. полей Dokan.
	 *
	 * @param array $social_fields Исходный массив соц. полей.
	 * @return array Массив с добавленным полем TikTok.
	 */
	public function add_tiktok_field( $social_fields ) {
		$social_fields['tiktok'] = [
			'title' => __( 'TikTok', 'dokan' ),
			'icon'  => 'fab fa-tiktok',
		];
		return $social_fields;
	}

	/**
	 * AJAX‑обработчик для получения TikTok-ссылки.
	 */
	public function get_tiktok_link() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( [ 'message' => __( 'No permission', 'add-tiktok-to-dokan' ) ], 403 );
		}
		$vendor_id = isset( $_POST['vendor_id'] ) ? absint( $_POST['vendor_id'] ) : 0;
		if ( ! $vendor_id ) {
			wp_send_json_error( [ 'message' => __( 'No vendor_id given', 'add-tiktok-to-dokan' ) ], 400 );
		}
		$store_info   = dokan_get_store_info( $vendor_id );
		$tiktok_link  = ! empty( $store_info['social']['tiktok'] ) ? esc_url( $store_info['social']['tiktok'] ) : '';
		wp_send_json_success( [ 'link' => $tiktok_link ] );
	}

	/**
	 * AJAX‑обработчик для получения статуса верификации вендора.
	 */
	public function get_vendor_verification_status() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( [ 'message' => __( 'No permission', 'add-tiktok-to-dokan' ) ] );
		}
		$vendor_id = isset( $_POST['vendor_id'] ) ? absint( $_POST['vendor_id'] ) : 0;
		if ( ! $vendor_id ) {
			wp_send_json_error( [ 'message' => __( 'No vendor_id given', 'add-tiktok-to-dokan' ) ] );
		}
		$profile_settings = dokan_get_store_info( $vendor_id );
		$status           = isset( $profile_settings['verification_status'] ) ? $profile_settings['verification_status'] : 'not requested';
		wp_send_json_success( [ 'status' => $status ] );
	}

	/**
	 * AJAX‑обработчик для обновления статуса верификации.
	 */
	public function update_vendor_verification_status() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( [ 'message' => __( 'No permission', 'add-tiktok-to-dokan' ) ] );
		}
		check_ajax_referer( 'vendor_authenticate_nonce', 'nonce' );
		$vendor_id  = isset( $_POST['vendor_id'] ) ? absint( $_POST['vendor_id'] ) : 0;
		$new_status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';
		if ( ! $vendor_id || empty( $new_status ) ) {
			wp_send_json_error( [ 'message' => __( 'Missing parameters', 'add-tiktok-to-dokan' ) ] );
		}
		$profile_settings = dokan_get_store_info( $vendor_id );
		if ( ! is_array( $profile_settings ) ) {
			wp_send_json_error( [ 'message' => __( 'Profile settings not found', 'add-tiktok-to-dokan' ) ] );
		}
		$profile_settings['verification_status'] = $new_status;
		update_user_meta( $vendor_id, 'dokan_profile_settings', $profile_settings );
		wp_send_json_success( [ 'status' => $new_status ] );
	}

	/**
	 * Подключает необходимые скрипты для админки.
	 */
	public function admin_enqueue_scripts() {
		// Скрипт для страницы одного вендора (TikTok-ссылка).
		wp_enqueue_script(
			'my-admin-tiktok',
			get_template_directory_uri() . '/js/my-admin-tiktok.js',
			[ 'jquery' ],
			'1.0',
			true
		);
		wp_localize_script( 'my-admin-tiktok', 'myAjax', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		] );

		// Скрипт для страницы списка вендоров, добавляющий колонку "Authenticate".
		wp_enqueue_script(
			'my-admin-vendors-authenticate',
			get_template_directory_uri() . '/js/admin-vendors-authenticate.js',
			[ 'jquery' ],
			'1.0',
			true
		);
		wp_localize_script( 'my-admin-vendors-authenticate', 'myAuthAjax', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'vendor_authenticate_nonce' ),
		] );
	}

	/**
	 * Шорткод для формы запроса верификации (Authenticated Seller).
	 *
	 * @return string HTML формы или сообщение об ошибке.
	 */
	public function authenticated_seller_shortcode() {
		if ( ! is_user_logged_in() ) {
			return __( 'You must be logged in to request verification.', 'add-tiktok-to-dokan' );
		}

		$current_user = wp_get_current_user();
		if ( ! dokan_is_user_seller( $current_user->ID ) ) {
			return __( 'Only vendors can request verification.', 'add-tiktok-to-dokan' );
		}

		$message = '';
		if ( isset( $_POST['authenticated_seller_nonce_field'] ) && wp_verify_nonce( $_POST['authenticated_seller_nonce_field'], 'authenticated_seller_nonce' ) ) {
			$instagram       = isset( $_POST['seller_instagram'] ) ? esc_url_raw( $_POST['seller_instagram'] ) : '';
			$tiktok          = isset( $_POST['seller_tiktok'] ) ? esc_url_raw( $_POST['seller_tiktok'] ) : '';
			$profile_settings = dokan_get_store_info( $current_user->ID );
			if ( ! isset( $profile_settings['social'] ) ) {
				$profile_settings['social'] = [];
			}
			$profile_settings['social']['instagram'] = $instagram;
			$profile_settings['social']['tiktok']    = $tiktok;

			// Устанавливаем флаг запроса верификации и статус "pending".
			$profile_settings['verification_request'] = true;
			$profile_settings['verification_status']  = 'pending';

			update_user_meta( $current_user->ID, 'dokan_profile_settings', $profile_settings );

			$message = '<div class="notice notice-success"><p>' . __( 'Your request for a verified account has been submitted.', 'add-tiktok-to-dokan' ) . '</p></div>';
		}

		$profile_settings = dokan_get_store_info( $current_user->ID );
		$instagram       = isset( $profile_settings['social']['instagram'] ) ? esc_url( $profile_settings['social']['instagram'] ) : '';
		$tiktok          = isset( $profile_settings['social']['tiktok'] ) ? esc_url( $profile_settings['social']['tiktok'] ) : '';

		ob_start();
		?>
		<div class="authenticated-seller-form">
			<?php echo $message; ?>
			<form method="post" id="authenticated-seller-form" class="dokan-form-horizontal">
				<?php wp_nonce_field( 'authenticated_seller_nonce', 'authenticated_seller_nonce_field' ); ?>
				<div class="dokan-form-group">
					<label class="dokan-control-label" for="seller_instagram"><?php _e( 'Instagram', 'add-tiktok-to-dokan' ); ?></label>
					<div class="dokan-w5">
						<input type="url" id="seller_instagram" name="seller_instagram" class="dokan-form-control" placeholder="http://" value="<?php echo esc_attr( $instagram ); ?>">
					</div>
				</div>
				<div class="dokan-form-group">
					<label class="dokan-control-label" for="seller_tiktok"><?php _e( 'TikTok', 'add-tiktok-to-dokan' ); ?></label>
					<div class="dokan-w5">
						<input type="url" id="seller_tiktok" name="seller_tiktok" class="dokan-form-control" placeholder="http://" value="<?php echo esc_attr( $tiktok ); ?>">
					</div>
				</div>
				<div class="dokan-form-group">
					<div class="dokan-w4">
						<input type="submit" name="request_verified" class="dokan-btn dokan-btn-theme" value="<?php esc_attr_e( 'Request for a verified account', 'add-tiktok-to-dokan' ); ?>">
					</div>
				</div>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}
}

Authenticate_seller::get_instance();
