<li class="history-list__item">
                <div class="history-item__media">
                    <span class="rental-history__status rental-history__status--waiting">ממתין</span>
                    <?php if ( $is_booking ) : ?>
                        <button class="status-indicator <?php echo esc_attr( $status_class ); ?>">
                            <?php echo esc_html( $status_text ); ?>
                        </button>
                    <?php endif; ?>
                    <?php echo $product_image; ?>
                </div>
                <div class="history-item__details">
                    <h3 class="history-item__title"><?php echo esc_html( $product_name ); ?></h3>
                    <p class="history-item__atts">
                        <span class="history-item__color"><span class="history-item__color-preview" style="background-color:#F0ECE6"></span><?php echo wp_kses_post( $color_html ); ?></span>
                        <span class="history-item__size"><?php echo esc_html( $size_label ); ?></span>
                        <?php if ( $product_price ) : ?>
                            <span class="history-item__price"><?php echo wp_kses_post( $product_price ); ?></span>
                        <?php endif; ?>
                    </p>
                    <div class="history-item__store">
                        <a href="<?php echo dokan_get_store_url( $store_id ); ?>" class="history-item__store-link">
                            <?php echo wp_get_attachment_image( $store_image_id, 'thumbnail', false, array('class' => 'history-store__image') ); ?>
                            <?php echo esc_html( $store_name ); ?>
                        </a>
                    </div>

                    <div class="rental-date">
                        <p class="rental-date__details">
                            <span class="rental-date__status">
                                <svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.6667 14.6663L14.3333 11.9997M14.3333 11.9997L11.6667 9.33301M14.3333 11.9997H9M4.33333 2.66634H4.2C3.0799 2.66634 2.51984 2.66634 2.09202 2.88433C1.71569 3.07607 1.40973 3.38204 1.21799 3.75836C1 4.18618 1 4.74624 1 5.86634V6.66634M4.33333 2.66634H9.66667M4.33333 2.66634V1.33301M4.33333 2.66634V3.99967M9.66667 2.66634H9.8C10.9201 2.66634 11.4802 2.66634 11.908 2.88433C12.2843 3.07607 12.5903 3.38204 12.782 3.75836C13 4.18618 13 4.74624 13 5.86634V6.66634M9.66667 2.66634V1.33301M9.66667 2.66634V3.99967M1 6.66634V11.4663C1 12.5864 1 13.1465 1.21799 13.5743C1.40973 13.9506 1.71569 14.2566 2.09202 14.4484C2.51984 14.6663 3.0799 14.6663 4.2 14.6663H7.33333M1 6.66634H13M13 6.66634V7.66634" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                הגעה:
                            </span>
                            יום שלישי, 30 בדצמבר 2024
                        </p>
                        <p class="rental-date__details">
                            <span class="rental-date__status">
                                <svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.33333 2.66634H4.2C3.07989 2.66634 2.51984 2.66634 2.09202 2.88433C1.71569 3.07607 1.40973 3.38204 1.21799 3.75836C1 4.18618 1 4.74624 1 5.86634V6.66634M4.33333 2.66634H9.66667M4.33333 2.66634V1.33301M4.33333 2.66634V3.99967M9.66667 2.66634H9.8C10.9201 2.66634 11.4802 2.66634 11.908 2.88433C12.2843 3.07607 12.5903 3.38204 12.782 3.75836C13 4.18618 13 4.74624 13 5.86634V6.66634M9.66667 2.66634V1.33301M9.66667 2.66634V3.99967M1 6.66634V11.4663C1 12.5864 1 13.1465 1.21799 13.5743C1.40973 13.9506 1.71569 14.2566 2.09202 14.4484C2.51984 14.6663 3.07989 14.6663 4.2 14.6663H7.33333M1 6.66634H13M13 6.66634V7.66634M11.6667 9.33301L9 11.9997M9 11.9997L11.6667 14.6663M9 11.9997H14.3333" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                החזרה:
                            </span>
                            יום שני, 30 בינואר 2024 
                        </p>

                        <?php if ( $booking_start ) : ?>
                                <p>
                                    <?php esc_html_e( 'Start Date:', 'your-textdomain' ); ?>
                                    <?php echo esc_html( $booking_start ); ?>
                                </p>
                            <?php endif; ?>

                            <?php if ( $booking_end ) : ?>
                                <p>
                                    <?php esc_html_e( 'End Date:', 'your-textdomain' ); ?>
                                    <?php echo esc_html( $booking_end ); ?>
                                </p>
                        <?php endif; ?>


                    </div>

                    <div class="rental-actions">
                        <button type="button" 
                            class="button dokan-store-review-button rental-action__btn rental-action__btn--review"
                            data-item_id="<?php echo esc_attr( $item_id ); ?>"
                            data_store_id="<?php echo esc_attr( $store_id ); ?>">
                            ביקורת
                        </button>
                        <button class="rental-action__btn rental-action__btn--cancel">סיום השכרה</button>
                        <?php if ( $is_booking ) : ?>
                            <!-- Заменяем "End Rental" на встроенный get_cancel_url() (отменяет бронирование) -->
                            <a href="<?php echo esc_url( $booking->get_cancel_url() ); ?>" class="button cancel">
                                <?php esc_html_e( 'End Rental', 'your-textdomain' ); ?>
                            </a>
                        <?php endif; ?>
                    </div>

                </div>


                <div style="display: none">

                    <?php if ( $existing_review_id && intval($orderid_from_review) == $order_id) : ?>
                        <!-- Отзыв для этого заказа уже оставлен -->
                        <p style="color: green; font-weight: bold;">
                            <?php esc_html_e( 'You have already reviewed this product for this order.', 'your-textdomain' ); ?>
                        </p>
                        <p>
                            <?php esc_html_e( 'You can edit your review if needed.', 'your-textdomain' ); ?>
                        </p>
                        <div class="rental-actions">
                            <div class="dokan-review-wrapper" style="margin-bottom: 25px;">
                                <button type="button"
                                        class="dokan-btn dokan-btn-sm dokan-btn-theme edit-review-btn"
                                        data-post_id="<?php echo esc_attr( $existing_review_id ); ?>"
                                        data-store_id="<?php echo esc_attr( $store_id ); ?>"
                                        data-nonce="<?php echo esc_attr( wp_create_nonce('dokan-seller-rating-form-action') ); ?>">
                                    <?php esc_html_e( 'Edit Your Review', 'your-textdomain' ); ?>
                                </button>
                            </div>
                            <div class="dokan-clearfix"></div>
                        </div>
                    <?php else : ?>
                        <?php if('cancelled' != $booking_status) : ?>
                        <!-- Нет отзыва для этого заказа -->
                        <div class="order-item-checklist">
                            <p><strong><?php esc_html_e( 'Mark if the item is correct or incorrect:', 'your-textdomain' ); ?></strong></p>
                            <label>
                                <input type="radio" name="item_status_<?php echo esc_attr( $item_id ); ?>" value="proper" required>
                                <?php esc_html_e( 'Proper (everything is fine)', 'your-textdomain' ); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="item_status_<?php echo esc_attr( $item_id ); ?>" value="invalid_dirty" required>
                                <?php esc_html_e( 'The product is dirty', 'your-textdomain' ); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="item_status_<?php echo esc_attr( $item_id ); ?>" value="invalid_torn" required>
                                <?php esc_html_e( 'The product is torn', 'your-textdomain' ); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="item_status_<?php echo esc_attr( $item_id ); ?>" value="invalid_damaged" required>
                                <?php esc_html_e( 'The product arrived damaged', 'your-textdomain' ); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="item_status_<?php echo esc_attr( $item_id ); ?>" value="invalid_lost" required>
                                <?php esc_html_e( 'The product is stolen/lost', 'your-textdomain' ); ?>
                            </label>
                        </div>

                        <div class="order-item-review-options">
                            <label>
                                <input type="checkbox" name="leave_review_<?php echo esc_attr( $item_id ); ?>" value="1">
                                <?php esc_html_e( 'Leave a product review', 'your-textdomain' ); ?>
                            </label>
                            <div style="margin-left:20px;">
                                <p><?php esc_html_e( 'Check any that apply:', 'your-textdomain' ); ?></p>
                                <label>
                                    <input type="checkbox" name="review_big_<?php echo esc_attr( $item_id ); ?>" value="big_on_me">
                                    <?php esc_html_e( 'The product is big on me', 'your-textdomain' ); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="review_small_<?php echo esc_attr( $item_id ); ?>" value="small_on_me">
                                    <?php esc_html_e( 'The product is small on me', 'your-textdomain' ); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="review_defective_<?php echo esc_attr( $item_id ); ?>" value="defective">
                                    <?php esc_html_e( 'The product is defective', 'your-textdomain' ); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="review_not_as_pic_<?php echo esc_attr( $item_id ); ?>" value="not_as_picture">
                                    <?php esc_html_e( 'The product is not as in the picture', 'your-textdomain' ); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="review_late_<?php echo esc_attr( $item_id ); ?>" value="arrived_late">
                                    <?php esc_html_e( 'Did not arrive on time', 'your-textdomain' ); ?>
                                </label>
                            </div>
                        </div>

                        <div class="order-item-extra">
                            <p><?php esc_html_e( 'Your rating (optional):', 'your-textdomain' ); ?></p>
                            <select name="rating_<?php echo esc_attr( $item_id ); ?>">
                                <option value="0"><?php esc_html_e( 'No rating', 'your-textdomain' ); ?></option>
                                <option value="5">★★★★★</option>
                                <option value="4">★★★★</option>
                                <option value="3">★★★</option>
                                <option value="2">★★</option>
                                <option value="1">★</option>
                            </select>
    
                            <!-- Скрытые поля -->
                            <input type="hidden" name="product_id_<?php echo esc_attr( $item_id ); ?>" value="<?php echo esc_attr( $product->get_id() ); ?>">
                            <input type="hidden" name="product_name_<?php echo esc_attr( $item_id ); ?>" value="<?php echo esc_attr( $product_name ); ?>">
                            <input type="hidden" name="order_id_<?php echo esc_attr( $item_id ); ?>" value="<?php echo esc_attr( $order->get_id() ); ?>">
        

                            <p><?php esc_html_e( 'Add picture(s) (optional, up to 5):', 'your-textdomain' ); ?></p>
                            <input type="file"
                                name="review_image_<?php echo esc_attr( $item_id ); ?>[]"
                                accept="image/*"
                                multiple="multiple">

                            <p><?php esc_html_e( 'Comment (optional):', 'your-textdomain' ); ?></p>
                            <textarea name="review_text_<?php echo esc_attr( $item_id ); ?>" rows="3"></textarea>
                        </div>

                        <div class="rental-actions">
                            <button type="button"
                                    class="button dokan-store-review-button"
                                    style="background-color:#e2e8f0; color:#1f2937;"
                                    data-item_id="<?php echo esc_attr( $item_id ); ?>"
                                    data_store_id="<?php echo esc_attr( $store_id ); ?>">
                                <?php esc_html_e( 'Review Product', 'your-textdomain' ); ?>
                            </button>

                            <?php if ( $is_booking ) : ?>
                                <!-- Заменяем "End Rental" на встроенный get_cancel_url() (отменяет бронирование) -->
                                <a href="<?php echo esc_url( $booking->get_cancel_url() ); ?>" class="button cancel">
                                    <?php esc_html_e( 'End Rental', 'your-textdomain' ); ?>
                                </a>
                            <?php endif; ?>

                        </div>

                        
                        <?php endif; ?>
                    <?php endif; ?>

                </div>


            </li>