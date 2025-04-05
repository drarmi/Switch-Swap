<?php
/**
 * Dokan Enhancer functionality.
 *
 * Этот класс объединяет различные расширения для Dokan,
 * такие как дополнительные поля для товаров, обработку AJAX‑запросов,
 * кастомизацию отзывов, статусов заказов и т.д.
 *
 * @package your-textdomain
 */
namespace Omnis\src\inc\classes\dokan_enhancer;

class Dokan_Enhancer {
	private static $instance;

	/**
	 * Конструктор класса: регистрирует все хуки.
	 */
	public function __construct() { 
		// Добавление кастомного поля "Item Condition" в форму нового товара.
		add_action( 'dokan_new_product_form', [ $this, 'add_item_condition_field' ], 10, 2 );
		add_action( 'dokan_process_product_meta', [ $this, 'save_item_condition_field' ], 10, 1 );

		// AJAX-обработчик для отправки отзыва о товаре.
		add_action( 'wp_ajax_dokan_submit_product_review', [ $this, 'my_dokan_submit_product_review' ] );
		add_action( 'wp_ajax_nopriv_dokan_submit_product_review', [ $this, 'my_dokan_submit_product_review' ] );

		// Расширение сохранения отзыва Dokan.
		add_action( 'dokan_store_review_saved', [ $this, 'my_extend_dokan_review' ], 10, 3 );
		// Функция создания комментария-отзыва для товара будет вызываться после сохранения отзыва,
		// но теперь внутри неё мы проверяем, существует ли уже отзыв для данного товара.
		add_action( 'dokan_store_review_saved', [ $this, 'my_create_product_review_comment' ], 15, 3 );

		// Фильтр для расширения вкладки отзывов продавца.
		add_filter( 'dokan_seller_tab_reviews_list', [ $this, 'my_extend_seller_tab_reviews_list' ], 20, 2 );

		// AJAX‑хук для загрузки формы редактирования отзыва.
		add_action( 'wp_ajax_child_edit_review_form', [ $this, 'child_dokan_edit_review_form' ] );

		// Блок опций аренды для товаров типа booking.
		add_action( 'dokan_product_edit_after_main', [ $this, 'add_rental_pricing_options_block' ], 10, 2 );
		add_action( 'dokan_process_product_meta', [ $this, 'save_rental_pricing_options' ], 10, 2 );

		// Фильтр для кастомизации рейтинга продавца.
		add_filter( 'dokan_seller_rating_value', [ $this, 'my_custom_dokan_seller_rating_value' ], 10, 2 );

		// Замена стандартной ссылки редактирования товара на dokan‑редактирование.
		add_filter( 'edit_post_link', [ $this, 'my_replace_edit_link_with_dokan' ], 10, 3 );

		// AJAX‑хук для размещения ставки в Simple Auctions.
		add_action( 'wc_ajax_my_place_bid', [ $this, 'my_custom_simple_auctions_bid_ajax' ] );
		add_action( 'wc_ajax_nopriv_my_place_bid', [ $this, 'my_custom_simple_auctions_bid_ajax' ] );

		// Регистрация и добавление кастомных статусов заказов.
		add_action( 'init', [ $this, 'register_custom_auction_order_statuses' ] );
		add_filter( 'wc_order_statuses', [ $this, 'add_custom_auction_order_statuses' ] );
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Выводит поле выбора состояния товара (Item Condition) в форме нового товара.
	 *
	 * @param mixed $post    Объект товара.
	 * @param int   $post_id ID товара.
	 */
	public function add_item_condition_field( $post, $post_id ) {
		// Получаем сохранённое значение состояния товара.
		$item_condition = get_post_meta( $post_id, '_item_condition', true );
		?>
		<div class="dokan-form-group item-condition">
			<label for="item_condition" class="form-label"><?php _e( 'Item Condition', 'storefront-child' ); ?></label>
			<select name="item_condition" id="item_condition" class="dokan-form-control">
				<option value="new" <?php selected( $item_condition, 'new' ); ?>><?php _e( 'New', 'storefront-child' ); ?></option>
				<option value="good" <?php selected( $item_condition, 'good' ); ?>><?php _e( 'In Good Shape', 'storefront-child' ); ?></option>
				<option value="used" <?php selected( $item_condition, 'used' ); ?>><?php _e( 'Used', 'storefront-child' ); ?></option>
			</select>
		</div>
		<?php
	}

	/**
	 * Сохраняет значение поля "Item Condition" при сохранении товара.
	 *
	 * @param int $post_id ID товара.
	 */
	public function save_item_condition_field( $post_id ) {
		if ( isset( $_POST['item_condition'] ) ) {
			update_post_meta( $post_id, '_item_condition', sanitize_text_field( $_POST['item_condition'] ) );
		}
	}

	/**
	 * AJAX‑обработчик отправки отзыва о товаре.
	 */
	public function my_dokan_submit_product_review() {
		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		$rating     = isset( $_POST['rating'] ) ? intval( $_POST['rating'] ) : 0;
		$user_id    = get_current_user_id();

		// Создаём комментарий (отзыв) типа "review". Обратите внимание, здесь статус - на модерации (0).
		$comment_id = wp_insert_comment(
			array(
				'comment_post_ID'      => $product_id,
				'comment_author'       => wp_get_current_user()->display_name,
				'comment_author_email' => wp_get_current_user()->user_email,
				'comment_content'      => '...', // Здесь можно взять содержимое из $_POST.
				'user_id'              => $user_id,
				'comment_approved'     => 0, // На модерации.
				'comment_type'         => 'review',
			)
		);

		if ( $comment_id ) {
			// Сохраняем рейтинг в метаполе комментария.
			update_comment_meta( $comment_id, 'rating', $rating );
			wp_send_json_success( 'OK' );
		} else {
			wp_send_json_error( 'Could not save review' );
		}
	}

	/**
	 * Дополняет сохранение отзыва Dokan дополнительными полями.
	 *
	 * @param int   $post_id  ID отзыва.
	 * @param array $postdata Данные, отправленные в форме.
	 * @param int   $rating   Рейтинг.
	 */
	public function my_extend_dokan_review( $post_id, $postdata, $rating ) {
		// 1. Сохраняем checklist.
		if ( ! empty( $postdata['checklist'] ) && is_array( $postdata['checklist'] ) ) {
			$clean = array_map( 'sanitize_text_field', $postdata['checklist'] );
			update_post_meta( $post_id, 'checklist', $clean );
		}
		if ( isset( $postdata['order_id'] ) && ! empty( $postdata['order_id'] ) ) {
			update_comment_meta( $post_id, 'orderid', $postdata['order_id'] );
		} 
		// 2. Сохраняем correctness.
		if ( isset( $postdata['correctness'] ) ) {
			$correctness = sanitize_text_field( $postdata['correctness'] );
			update_post_meta( $post_id, 'correctness', $correctness );
		}
		// 3. Сохраняем product_id.
		if ( isset( $postdata['product_id'] ) ) {
			update_post_meta( $post_id, 'product_id', absint( $postdata['product_id'] ) );
		}
		// 4. Сохраняем product_name и обновляем заголовок отзыва.
		if ( isset( $postdata['product_name'] ) && ! empty( $postdata['product_name'] ) ) {
			$product_name = sanitize_text_field( $postdata['product_name'] );
			$title        = sprintf( __( 'Review for %s', 'your-textdomain' ), $product_name );
			wp_update_post( array( 'ID' => $post_id, 'post_title' => $title ) );
			update_post_meta( $post_id, 'product_name', $product_name );
		}
		// 5. Обработка удаления изображений.
		if ( ! empty( $postdata['delete_images'] ) ) {
			$delete_ids      = array_map( 'absint', explode( ',', $postdata['delete_images'] ) );
			$existing_images = get_post_meta( $post_id, 'review_image_ids', true );
			if ( is_array( $existing_images ) ) {
				$remaining = array_diff( $existing_images, $delete_ids );
				update_post_meta( $post_id, 'review_image_ids', $remaining );
			}
		}
		// 6. Обработка загрузки новых изображений (до 5 файлов).
		if ( ! empty( $_FILES['review_image_file'] ) && ! empty( $_FILES['review_image_file']['name'][0] ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			$uploaded_image_ids = [];
			foreach ( $_FILES['review_image_file']['name'] as $key => $value ) {
				if ( $_FILES['review_image_file']['error'][ $key ] === 0 ) {
					$file = array(
						'name'     => $_FILES['review_image_file']['name'][ $key ],
						'type'     => $_FILES['review_image_file']['type'][ $key ],
						'tmp_name' => $_FILES['review_image_file']['tmp_name'][ $key ],
						'error'    => $_FILES['review_image_file']['error'][ $key ],
						'size'     => $_FILES['review_image_file']['size'][ $key ],
					);
					$uploaded = wp_handle_upload( $file, [ 'test_form' => false ] );
					if ( ! isset( $uploaded['error'] ) && isset( $uploaded['file'] ) ) {
						$file_name  = $uploaded['file'];
						$file_type  = $uploaded['type'];
						$attachment = [
							'post_mime_type' => $file_type,
							'post_title'     => sanitize_file_name( basename( $file_name ) ),
							'post_content'   => '',
							'post_status'    => 'inherit',
						];
						$attach_id   = wp_insert_attachment( $attachment, $file_name );
						require_once ABSPATH . 'wp-admin/includes/image.php';
						$attach_data = wp_generate_attachment_metadata( $attach_id, $file_name );
						wp_update_attachment_metadata( $attach_id, $attach_data );
						$uploaded_image_ids[] = $attach_id;
					}
				}
			}
			if ( ! empty( $uploaded_image_ids ) ) {
				$existing_images = get_post_meta( $post_id, 'review_image_ids', true );
				if ( ! is_array( $existing_images ) ) {
					$existing_images = [];
				}
				$merged = array_merge( $existing_images, $uploaded_image_ids );
				$merged = array_slice( $merged, 0, 5 );
				update_post_meta( $post_id, 'review_image_ids', $merged );
			}
		}
	}

	/**
	 * Создаёт комментарий-отзыв для товара на основе данных Dokan.
	 *
	 * @param int   $post_id  ID отзыва.
	 * @param array $postdata Данные из формы.
	 * @param int   $rating   Рейтинг.
	 */
	public function my_create_product_review_comment( $post_id, $postdata, $rating ) {
		// Если не передан product_id или он неверный, выходим.
		if ( empty( $postdata['product_id'] ) ) {
			return;
		}
		$product_id = absint( $postdata['product_id'] );
		if ( $product_id <= 0 ) {
			return;
		}
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return;
		}

		// Проверяем, существует ли уже комментарий-отзыв для этого товара от текущего пользователя.
		$existing = get_comments( array(
			'post_id'      => $product_id,
			'user_id'      => $user_id,
			'comment_type' => 'review',
			'number'       => 1,
		) );
		if ( ! empty( $existing ) ) {
			// Если такой комментарий уже есть, не создаём новый.
			return;
		}

		$user_info            = get_userdata( $user_id );
		$comment_author       = $user_info ? $user_info->display_name : 'Guest';
		$comment_author_email = $user_info ? $user_info->user_email : 'guest@example.com';
		$review_text          = isset( $postdata['dokan-review-details'] ) ? $postdata['dokan-review-details'] : '';

		$comment_data = array(
			'comment_post_ID'      => $product_id,
			'comment_author'       => $comment_author,
			'comment_author_email' => $comment_author_email,
			'comment_content'      => $review_text,
			'comment_type'         => 'review',
			'comment_approved'     => 1,
			'user_id'              => $user_id,
		);

		$comment_id = wp_insert_comment( $comment_data );

		if ( $comment_id ) {
			update_comment_meta( $comment_id, 'rating', (int) $rating );
			
			if ( ! empty( $postdata['correctness'] ) ) {
				$a = update_comment_meta( $comment_id, 'correctness', sanitize_text_field( $postdata['correctness'] ) );
			}
			if ( ! empty( $postdata['checklist'] ) && is_array( $postdata['checklist'] ) ) {
				$checklist_clean = array_map( 'sanitize_text_field', $postdata['checklist'] );
				update_comment_meta( $comment_id, 'checklist', $checklist_clean );
			}

			// Сохраняем order_id, если он передан в $postdata.
			if ( isset( $postdata['order_id'] ) && ! empty( $postdata['order_id'] ) ) {
				update_comment_meta( $comment_id, 'orderid', $postdata['order_id'] );
			}
		}
	}

	/**
	 * Расширяет HTML-вывод вкладки отзывов продавца.
	 *
	 * @param string $original_html Исходный HTML.
	 * @param int    $store_id      ID магазина.
	 * @return string Новый HTML.
	 */
	public function my_extend_seller_tab_reviews_list( $original_html, $store_id ) {
		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$args  = [
			'seller_id' => $store_id,
			'paged'     => $paged,
			'per_page'  => 20,
		];

		if ( ! class_exists( '\WeDevs\DokanPro\Modules\StoreReviews\Manager' ) ) {
			return $original_html;
		}

		$manager       = new \WeDevs\DokanPro\Modules\StoreReviews\Manager();
		$posts         = $manager->get_user_review( $args );
		$no_review_msg = apply_filters( 'dsr_no_review_found_msg', __( 'No Reviews found', 'dokan' ), $posts );
		ob_start();
		if ( ! $posts ) {
			echo '<span>' . esc_html( $no_review_msg ) . '</span>';
			return ob_get_clean();
		}

		$readable_correctness = [
			'proper'          => __( 'Proper (everything is fine)', 'your-textdomain' ),
			'invalid_dirty'   => __( 'The product is dirty', 'your-textdomain' ),
			'invalid_torn'    => __( 'The product is torn', 'your-textdomain' ),
			'invalid_damaged' => __( 'The product arrived damaged', 'your-textdomain' ),
			'invalid_lost'    => __( 'The product is stolen/lost', 'your-textdomain' ),
		];
		$readable_checklist = [
			'big_on_me'      => __( 'The product is big on me', 'your-textdomain' ),
			'small_on_me'    => __( 'The product is small on me', 'your-textdomain' ),
			'defective'      => __( 'The product is defective', 'your-textdomain' ),
			'not_as_picture' => __( 'The product is not as in the picture', 'your-textdomain' ),
			'arrived_late'   => __( 'Did not arrive on time', 'your-textdomain' ),
		];
		?>
		<ol class="commentlist" id="dokan-store-review-single">
			<?php
			foreach ( $posts as $review ) :
				$review_timestamp = dokan_current_datetime()
					->setTimezone( new \DateTimeZone( 'UTC' ) )
					->modify( $review->post_date_gmt )
					->getTimestamp();
				$review_date    = dokan_format_datetime( $review_timestamp );
				$user_info      = get_userdata( $review->post_author );
				$review_author_img = get_avatar( $user_info ? $user_info->user_email : '', 180 );
				$author_name    = $user_info ? ( $user_info->display_name ?: $user_info->user_nicename ) : __( 'Unknown', 'dokan' );
				$rating         = (int) get_post_meta( $review->ID, 'rating', true );

				$correctness = get_post_meta( $review->ID, 'correctness', true );
				if ( isset( $readable_correctness[ $correctness ] ) ) {
					$correctness = $readable_correctness[ $correctness ];
				}
				$checklist = get_post_meta( $review->ID, 'checklist', true );
				$permalink = '';
				?>
				<li itemtype="http://schema.org/Review" itemscope itemprop="reviews">
					<div class="review_comment_container">
						<div class="dokan-review-author-img">
							<?php echo $review_author_img; ?>
						</div>
						<div class="comment-text">
							<a href="<?php echo esc_url( $permalink ); ?>">
								<div class="dokan-rating">
									<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf( __( 'Rated %d out of 5', 'dokan' ), $rating ); ?>">
										<span style="width:<?php echo ( $rating / 5 ) * 100; ?>%">
											<strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'dokan' ); ?>
										</span>
									</div>
								</div>
							</a>
							<p>
								<strong itemprop="author"><?php echo esc_html( $author_name ); ?></strong>
								<em class="verified"></em> –
								<a href="<?php echo esc_url( $permalink ); ?>">
									<time datetime="<?php echo date( 'c', strtotime( $review_date ) ); ?>" itemprop="datePublished">
										<?php echo esc_html( $review_date ); ?>
									</time>
								</a>
							</p>
							<div class="description" itemprop="description">
								<h4><?php echo esc_html( $review->post_title ); ?></h4>
								<p><?php echo wp_kses_post( $review->post_content ); ?></p>

								<?php if ( $correctness ) : ?>
									<p><strong>Correctness:</strong> <?php echo esc_html( $correctness ); ?></p>
								<?php endif; ?>

								<?php if ( $checklist && is_array( $checklist ) ) : ?>
									<div class="order-item-checklist">
										<p><strong>Checklist:</strong></p>
										<ul>
											<?php
											foreach ( $checklist as $item ) {
												$readable = isset( $readable_checklist[ $item ] ) ? $readable_checklist[ $item ] : $item;
												echo '<li>' . esc_html( $readable ) . '</li>';
											}
											?>
										</ul>
									</div>
								<?php endif; ?>

								<?php
								$image_ids = get_post_meta( $review->ID, 'review_image_ids', true );
								if ( ! $image_ids ) {
									$img_id = get_post_meta( $review->ID, 'review_image_id', true );
									if ( $img_id ) {
										$image_ids = [ $img_id ];
									}
								}
								if ( ! empty( $image_ids ) && is_array( $image_ids ) ) : ?>
									<p><strong>Attached Images:</strong></p>
									<?php
									foreach ( $image_ids as $img_id ) :
										$img_url = wp_get_attachment_url( $img_id );
										if ( $img_url ) :
											?>
											<img src="<?php echo esc_url( $img_url ); ?>" alt="review image" style="max-width:150px; margin-right:5px;">
											<?php
										endif;
									endforeach;
									?>
								<?php endif; ?>
							</div>

							<?php if ( get_current_user_id() == $review->post_author ) : ?>
								<div class="dokan-review-wrapper" style="margin-bottom: 25px;">
									<button class="dokan-btn dokan-btn-sm dokan-btn-theme edit-review-btn"
										data-post_id="<?php echo esc_attr( $review->ID ); ?>"
										data-store_id="<?php echo esc_attr( $store_id ); ?>">
										<?php _e( ' Edit', 'dokan' ); ?>
									</button>
								</div>
								<div class="dokan-clearfix"></div>
							<?php endif; ?>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
		<?php
		return ob_get_clean();
	}

	/**
	 * AJAX‑обработчик загрузки формы редактирования отзыва из дочерней темы.
	 */
	public function child_dokan_edit_review_form() {
		if ( ! isset( $_POST['dokan-seller-rating-form-nonce'] ) ||
		     ! wp_verify_nonce( wp_unslash( $_POST['dokan-seller-rating-form-nonce'] ), 'dokan-seller-rating-form-action' ) ) {
			wp_send_json_error( 'Nonce verification failed.' . $_POST['dokan-seller-rating-form-nonce'] );
			wp_die();
		}

		$post_id  = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$store_id = isset( $_POST['store_id'] ) ? absint( $_POST['store_id'] ) : 0;

		error_log( '[ChildReview] child_edit_review_form invoked. post_id: ' . $post_id . ', store_id: ' . $store_id );

		$child_template = get_stylesheet_directory() . '/dokan/modules/store-reviews/templates/edit-review.php';
		if ( file_exists( $child_template ) ) {
			ob_start();
			include $child_template;
			$html = ob_get_clean();
			error_log( '[ChildReview] Child edit-review template loaded successfully. HTML length: ' . strlen( $html ) );
			wp_send_json_success( $html );
		} else {
			error_log( '[ChildReview] Child template not found at: ' . $child_template );
			wp_send_json_error( 'Child edit-review template not found.' );
		}
		wp_die();
	}

	/**
	 * Выводит блок настроек цен для аренды (только для товаров типа booking).
	 *
	 * @param mixed $post    Объект товара.
	 * @param int   $post_id ID товара.
	 */
	public function add_rental_pricing_options_block( $post, $post_id ) {
		$product = wc_get_product( $post_id );

		if ( ! $product || $product->get_type() !== 'booking' ) {
			return;
		}

		$rental_pricing = get_post_meta( $post_id, '_rental_pricing', true ) ?: [
			[ 'days' => 3, 'rate' => '', 'discount' => '10' ],
			[ 'days' => 5, 'rate' => '', 'discount' => '15' ],
			[ 'days' => 7, 'rate' => '', 'discount' => '20' ],
		];
		?>
		<div class="rental_pricing dokan-edit-row dokan-clearfix">
			<div class="dokan-section-heading" data-togglehandler="rental_pricing">
				<h2><i class="fas fa-hand-holding-usd" aria-hidden="true"></i> <?php _e( 'Rental Pricing Options', 'storefront-child' ); ?></h2>
				<p><?php _e( 'Set pricing for rental days and optional discounts.', 'storefront-child' ); ?></p>
				<a href="#" class="dokan-section-toggle">
					<i class="fas fa-sort-down fa-flip-vertical" aria-hidden="true" style="margin-top: 9px;"></i>
				</a>
				<div class="dokan-clearfix"></div>
			</div>

			<div class="dokan-section-content">
				<table class="widefat dokan-booking-range-table">
					<thead>
						<tr>
							<th><?php _e( 'Days', 'storefront-child' ); ?></th>
							<th><?php _e( 'Daily Rate (e.g., 100)', 'storefront-child' ); ?></th>
							<th><?php _e( 'Discount (%)', 'storefront-child' ); ?></th>
							<th><?php _e( 'Actions', 'storefront-child' ); ?></th>
						</tr>
					</thead>
					<tbody id="rental_pricing_rows">
						<?php foreach ( $rental_pricing as $index => $pricing ) : ?>
							<tr>
								<td>
									<input type="number" name="rental_pricing[<?php echo $index; ?>][days]" class="dokan-form-control" value="<?php echo esc_attr( $pricing['days'] ); ?>" placeholder="<?php _e( 'Days', 'storefront-child' ); ?>" min="1">
								</td>
								<td>
									<input type="number" name="rental_pricing[<?php echo $index; ?>][rate]" class="dokan-form-control" value="<?php echo esc_attr( $pricing['rate'] ); ?>" placeholder="<?php _e( 'Rate', 'storefront-child' ); ?>" min="0" step="0.1">
								</td>
								<td>
									<input type="number" name="rental_pricing[<?php echo $index; ?>][discount]" class="dokan-form-control" value="<?php echo esc_attr( $pricing['discount'] ); ?>" placeholder="<?php _e( 'Discount', 'storefront-child' ); ?>" min="0" max="100">
								</td>
								<td>
									<a href="#" class="button remove_row dokan-btn dokan-btn-theme"><?php _e( 'Remove', 'storefront-child' ); ?></a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="4">
								<a href="#" class="button add_row dokan-btn dokan-btn-theme"><?php _e( 'Add Row', 'storefront-child' ); ?></a>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<?php
	}

	/**
	 * Сохраняет опции аренды при сохранении товара.
	 *
	 * @param int   $post_id  ID товара.
	 * @param array $postdata Данные из формы.
	 */
	public function save_rental_pricing_options( $post_id, $postdata = [] ) {
		if ( isset( $_POST['rental_pricing'] ) ) {
			$rental_pricing = array_map(
				function ( $pricing ) {
					return [
						'days'     => intval( $pricing['days'] ),
						'rate'     => floatval( $pricing['rate'] ),
						'discount' => floatval( $pricing['discount'] ),
					];
				},
				$_POST['rental_pricing']
			);
			update_post_meta( $post_id, '_rental_pricing', $rental_pricing );
		}
	}

	/**
	 * Кастомизирует значение рейтинга для продавца.
	 *
	 * @param array $rating_value Массив с рейтингом и количеством отзывов.
	 * @param int   $vendor_id    ID продавца.
	 * @return array Изменённый массив рейтинга.
	 */
	public function my_custom_dokan_seller_rating_value( $rating_value, $vendor_id ) {
		$average_rating = (float) $rating_value['rating'];
		$reviews_count  = (int) $rating_value['count']; 

		$repeats_percent = (float) get_user_meta( $vendor_id, '_my_repeats_percent', true );
		$base_rating     = $average_rating;
		$penalty_from_rep = 5 - ( $repeats_percent / 100 * 5 );
		$penalty_from_rep = max( 0, $penalty_from_rep );
		$final_rating     = 0.5 * $base_rating + 0.5 * $penalty_from_rep;

		$final_rating = max( 1.0, min( $final_rating, 5.0 ) );
		$final_rating = round( $final_rating, 1 );

		if ( $final_rating < 3.5 ) {
			$rating_value['rating'] = 0;
			update_user_meta( $vendor_id, '_my_real_rating', $final_rating );
		} else {
			$rating_value['rating'] = $final_rating;
		}

		return $rating_value;
	}

	/**
	 * Получает данные рейтинга магазина.
	 *
	 * @param mixed $store Объект или ID магазина.
	 * @return array|false
	 */
	public static function get_store_rating_data( $store ) {
		// Если передан ID, получаем объект магазина
		if ( is_int( $store ) ) {
			$store = dokan()->vendor->get( $store );
		}
	
		// Если не удалось получить объект, возвращаем false
		if ( ! is_object( $store ) ) {
			return false;
		}
	
		$vendor_id     = $store->get_id();
		$rating_value  = $store->get_rating();
		$final_rating  = (float) $rating_value['rating'];
		$reviews_count = (int) $rating_value['count'];
	
		return array(
			'vendor_id'     => $vendor_id,
			'final_rating'  => $final_rating,
			'reviews_count' => $reviews_count,
		);
	}

	/**
	 * Выводит дополнительные теги продавца на странице магазина.
	 *
	 * @param mixed $store Объект или ID магазина.
	 */
	public static function output_dokan_store_custom_tags( $store ) {
		$data = self::get_store_rating_data( $store );
		if ( false === $data ) {
			return '';
		}
	
		$vendor_id     = $data['vendor_id'];
		$final_rating  = $data['final_rating'];
		$reviews_count = $data['reviews_count'];
	
		ob_start();
	
		// Если отзывов меньше 21, выводим тег "New Seller"
		if ( $reviews_count < 21 ) {
			echo '<span class="my-dokan-tag my-new-seller-tag">New Seller</span>';
			return ob_get_clean();
		}
	
		// Если рейтинг меньше 3.5 – ничего не выводим
		if ( $final_rating < 3.5 ) {
			return ob_get_clean();
		}
	
		// Если рейтинг 4.5 и выше, выводим тег "Leader" и проверяем дополнительные мета-поля
		if ( $final_rating >= 4.5 ) {
			echo '<span class="my-dokan-tag my-leader-tag">Leader</span>';
			$insta_blue  = get_user_meta( $vendor_id, '_my_insta_blue_check', true );
			$tiktok_blue = get_user_meta( $vendor_id, '_my_tiktok_blue_check', true );
			if ( 'yes' === $insta_blue || 'yes' === $tiktok_blue ) {
				echo ' <span class="my-dokan-tag my-blue-check">✔</span>';
			}
			return ob_get_clean();
		}
	
		// В остальных случаях выводим просто рейтинг
		echo '<span class="my-dokan-tag">Rating: ' . esc_html( $final_rating ) . '</span>';
	
		return ob_get_clean();
	}

	/**
	 * Заменяет стандартную ссылку редактирования товара на ссылку Dokan.
	 *
	 * @param string $link    Исходная ссылка.
	 * @param int    $post_id ID товара.
	 * @param string $text    Текст ссылки.
	 * @return string Новая ссылка.
	 */
	public function my_replace_edit_link_with_dokan( $link, $post_id, $text ) {
		if ( get_post_type( $post_id ) === 'product' ) {
			if ( function_exists( 'dokan_edit_product_url' ) ) {
				$dokan_url = dokan_edit_product_url( $post_id );
			} else {
				$dokan_url = '#';
			}

			$product_title = get_the_title( $post_id );

			$link = sprintf(
				'<span class="edit"><a href="%s">Edit <span class="screen-reader-text">%s</span></a></span>',
				esc_url( $dokan_url ),
				esc_html( $product_title )
			);
		}

		return $link;
	}

	/**
	 * AJAX‑обработчик размещения ставки в Simple Auctions.
	 */
	public function my_custom_simple_auctions_bid_ajax() {
		global $woocommerce_auctions;

		$product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
		$bid_value  = isset( $_POST['bid_value'] ) ? sanitize_text_field( $_POST['bid_value'] ) : '';
		$place_bid  = isset( $_POST['place_bid'] ) ? intval( $_POST['place_bid'] ) : 0;
		$user_id    = get_current_user_id();

		$_REQUEST['place-bid'] = $place_bid;
		$_REQUEST['bid_value'] = $bid_value;

		ob_start();
		$woocommerce_auctions->woocommerce_simple_auctions_place_bid();
		ob_end_clean();

		$errors   = wc_get_notices( 'error' );
		$messages = wc_get_notices( 'success' );

		if ( ! empty( $errors ) ) {
			wp_send_json_error(
				[
					'message' => wp_strip_all_tags( $errors[0]['notice'] ),
				]
			);
		} else {
			$success_text = ! empty( $messages ) ? wp_strip_all_tags( $messages[0]['notice'] ) : __( 'Your bid is successfully placed!', 'wc_simple_auctions' );
			wp_send_json_success(
				[
					'message'    => $success_text,
					'product_id' => $product_id,
					'bid_value'  => $bid_value,
				]
			);
		}
	}

	/**
	 * Регистрирует кастомные статусы заказов для аукционов.
	 */
	public function register_custom_auction_order_statuses() {
		// Статус: Offer.
		register_post_status(
			'wc-offer',
			array(
				'label'                     => _x( 'Offer', 'Order status', 'your-textdomain' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Offer <span class="count">(%s)</span>', 'Offer <span class="count">(%s)</span>', 'your-textdomain' ),
			)
		);

		// Статус: Ordered.
		register_post_status(
			'wc-ordered',
			array(
				'label'                     => _x( 'Ordered', 'Order status', 'your-textdomain' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Ordered <span class="count">(%s)</span>', 'Ordered <span class="count">(%s)</span>', 'your-textdomain' ),
			)
		);

		// Статус: Shipped.
		register_post_status(
			'wc-shipped',
			array(
				'label'                     => _x( 'Shipped', 'Order status', 'your-textdomain' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Shipped <span class="count">(%s)</span>', 'Shipped <span class="count">(%s)</span>', 'your-textdomain' ),
			)
		);

		// Статус: Delivered.
		register_post_status(
			'wc-delivered',
			array(
				'label'                     => _x( 'Delivered', 'Order status', 'your-textdomain' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Delivered <span class="count">(%s)</span>', 'Delivered <span class="count">(%s)</span>', 'your-textdomain' ),
			)
		);
	}

	/**
	 * Добавляет кастомные статусы заказов в список WooCommerce.
	 *
	 * @param array $order_statuses Исходный массив статусов.
	 * @return array Новый массив статусов.
	 */
	public function add_custom_auction_order_statuses( $order_statuses ) {
		$new_statuses = array();

		foreach ( $order_statuses as $key => $status ) {
			$new_statuses[ $key ] = $status;
			if ( 'wc-on-hold' === $key ) {
				$new_statuses['wc-offer']     = __( 'Offer', 'your-textdomain' );
				$new_statuses['wc-ordered']   = __( 'Ordered', 'your-textdomain' );
				$new_statuses['wc-shipped']   = __( 'Shipped', 'your-textdomain' );
				$new_statuses['wc-delivered'] = __( 'Delivered', 'your-textdomain' );
			}
		}

		return $new_statuses;
	}
}

new Dokan_Enhancer();
