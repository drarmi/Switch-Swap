<?php
/**
 * Шаблон редактирования отзыва Dokan
 */
if ( ! is_user_logged_in() ) {
    wp_die( __('You must be logged in to edit your review. ', 'dokan') ); 
}

$post_id   = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
$seller_id = isset( $_POST['store_id'] ) ? absint( $_POST['store_id'] ) : 0;

$post = get_post( $post_id );
if ( ! $post || get_current_user_id() != $post->post_author ) {
    wp_die( __('You are not allowed to edit this review.', 'dokan') );
}

$rating    = get_post_meta( $post_id, 'rating', true );
$checklist = get_post_meta( $post_id, 'checklist', true ); // массив
$correctness = get_post_meta( $post_id, 'correctness', true );
$product_name = get_post_meta( $post_id, 'product_name', true );

$review_image_ids = get_post_meta( $post_id, 'review_image_ids', true );
if ( ! $review_image_ids && get_post_meta( $post_id, 'review_image_id', true ) ) {
    $review_image_ids = [ get_post_meta( $post_id, 'review_image_id', true) ];
}
?>
<div class="dokan-add-review-wrapper">
  <strong><?php printf( __('Hi, %s', 'dokan'), wp_get_current_user()->display_name ); ?></strong>
  <div class="dokan-seller-rating-intro-text">
    <?php printf( __('Update your review for %s', 'dokan'), esc_html($product_name) ); ?>
  </div>
  <form class="dokan-form-container" id="dokan-edit-review-form" enctype="multipart/form-data">
    <div class="dokan-form-group">
      <label for="dokan-review-title"><?php _e('Title:', 'dokan'); ?></label>
      <input type="text" name="dokan-review-title" id="dokan-review-title" value="<?php echo esc_attr($post->post_title); ?>" required>
    </div>
    <div class="dokan-form-group">
      <label for="dokan-review-details"><?php _e('Your Review:', 'dokan'); ?></label>
      <textarea name="dokan-review-details" id="dokan-review-details" rows="5" required><?php echo esc_textarea($post->post_content); ?></textarea>
    </div>
    <div class="dokan-form-group">
      <label for="dokan-review-rating"><?php _e('Rating:', 'dokan'); ?></label>
      <select name="rating" id="dokan-review-rating">
        <option value="0" <?php selected( $rating, 0 ); ?>><?php _e('No rating', 'dokan'); ?></option>
        <option value="5" <?php selected( $rating, 5 ); ?>>★★★★★</option>
        <option value="4" <?php selected( $rating, 4 ); ?>>★★★★</option>
        <option value="3" <?php selected( $rating, 3 ); ?>>★★★</option>
        <option value="2" <?php selected( $rating, 2 ); ?>>★★</option>
        <option value="1" <?php selected( $rating, 1 ); ?>>★</option>
      </select>
    </div>
    <!-- Дополнительные поля: чек-лист -->
    <div class="dokan-form-group">
      <label><?php _e('Select any options:', 'dokan'); ?></label><br>
      <?php
      $options = [
        'big_on_me'       => __('The product is big on me', 'dokan'),
        'small_on_me'     => __('The product is small on me', 'dokan'),
        'defective'       => __('The product is defective', 'dokan'),
        'not_as_picture'  => __('The product is not as in the picture', 'dokan'),
        'arrived_late'    => __('Did not arrive on time', 'dokan'),
      ];
      foreach ( $options as $key => $label ) {
        $checked = ( is_array($checklist) && in_array($key, $checklist) ) ? 'checked' : '';
        echo '<label><input type="checkbox" name="checklist[]" value="'. esc_attr($key) .'" '. $checked .' > '. esc_html($label) .'</label><br>';
      }
      ?>
    </div>
    <!-- Радио кнопки для корректности -->
    <div class="dokan-form-group">
      <label><?php _e('Product status:', 'dokan'); ?></label><br>
      <?php
      $status_options = [
        'proper'          => __('Proper (everything is fine)', 'dokan'),
        'invalid_dirty'   => __('The product is dirty', 'dokan'),
        'invalid_torn'    => __('The product is torn', 'dokan'),
        'invalid_damaged' => __('The product arrived damaged', 'dokan'),
        'invalid_lost'    => __('The product is stolen/lost', 'dokan'),
      ];
      foreach ( $status_options as $key => $label ) {
        $checked = ($correctness === $key) ? 'checked' : '';
        echo '<label><input type="radio" name="correctness" value="'. esc_attr($key) .'" '. $checked .' required> '. esc_html($label) .'</label><br>';
      }
      ?>
    </div>
    <!-- Изображения: показываем уже загруженные, с возможностью удалить, и поле для загрузки новых -->
    <div class="dokan-form-group">
      <label><?php _e('Attached Images:', 'dokan'); ?></label>
      <div class="dokan-review-images">
        <?php
        if ( ! empty( $review_image_ids ) && is_array( $review_image_ids ) ) {
          foreach ( $review_image_ids as $img_id ) {
            $img_url = wp_get_attachment_url( $img_id );
            if ( $img_url ) {
              ?>
              <div class="dokan-review-image-item" style="display:inline-block; position:relative; margin-right:5px;">
                <img src="<?php echo esc_url( $img_url ); ?>" alt="review image" style="max-width:150px;">
                <!-- Метка для удаления изображения -->
                <button type="button" class="delete-image-btn" data-img-id="<?php echo esc_attr( $img_id ); ?>" style="position:absolute; top:0; right:0; background:#fff; border:none; color:red; cursor:pointer;">&times;</button>
              </div>
              <?php
            }
          }
        }
        ?>
      </div>
      <!-- Скрытое поле для хранения ID изображений, которые будут удалены -->
      <input type="hidden" name="delete_images" id="delete_images" value="">
      <p><?php _e('Upload additional images (optional, up to 5 total):', 'dokan'); ?></p>
      <input type="file" name="review_image_file[]" accept="image/*" multiple="multiple">
    </div>
    <!-- Передаем необходимые данные -->
    <input type="hidden" name="store_id" value="<?php echo esc_attr($seller_id); ?>">
    <input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>">
    <?php wp_nonce_field( 'dokan-seller-rating-form-action', 'dokan-seller-rating-form-nonce' ); ?>
    <div class="dokan-form-group">
      <input type="submit" value="<?php _e('Submit', 'dokan'); ?>" class="dokan-btn dokan-btn-theme">
    </div>
  </form>
</div>
<script>
// Обработка удаления изображений: при клике на кнопку с классом .delete-image-btn
document.addEventListener('DOMContentLoaded', function() {
  const deleteBtns = document.querySelectorAll('.delete-image-btn');
  const deleteInput = document.getElementById('delete_images');
  deleteBtns.forEach((btn) => {
    btn.addEventListener('click', function() {
      const imgId = this.getAttribute('data-img-id');
      // Скрываем изображение в UI
      this.parentElement.style.opacity = '0.5';
      // Добавляем ID в скрытое поле (через запятую)
      let current = deleteInput.value;
      if ( current ) {
        current += ',' + imgId;
      } else {
        current = imgId;
      }
      deleteInput.value = current;
    });
  });
});
</script>
