<?php
/**
 * Overridden template for showing Rental-type order details
 */
defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id );
if ( ! $order ) {
    return;
}

// Get order items
$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', [ 'completed', 'processing' ] ) );
$downloads             = $order->get_downloadable_items();
$show_customer_details = $order->get_user_id() === get_current_user_id();

/**
 * Получаем "size" прямо из метаполей $item (поиском ключей, содержащих "pa_size").
 * Если ничего не найдено — вернём "One-Size".
 *
 * @param WC_Order_Item_Product $item
 * @return string
 */
if ( ! function_exists('my_get_item_size_label') ) {
    function my_get_item_size_label( $item ) {
        $default = 'One-Size';

        if ( ! $item || ! is_callable( [ $item, 'get_meta_data' ] ) ) {
            return $default;
        }
        $all_meta = $item->get_meta_data();
        foreach ( $all_meta as $m ) {
            $meta_key   = $m->get_data()['key'];
            $meta_value = $m->get_data()['value'];
            if ( false !== strpos( $meta_key, 'pa_size' ) ) {
                $term = get_term_by( 'slug', $meta_value, $meta_key );
                if ( $term && ! is_wp_error( $term ) ) {
                    return $term->name;
                }
                return (string) $meta_value ?: $default;
            }
        }
        return $default;
    }
}

/**
 * Получаем "color" прямо из Order Item Meta (обычно key = 'pa_color'),
 * рисуем свотч, если Variation Swatches хранит `product_attribute_color`.
 *
 * @param WC_Order_Item_Product $item
 * @return string (HTML)
 */
if ( ! function_exists( 'my_get_item_color_swatch' ) ) {
    function my_get_item_color_swatch( $item ) {
        $default = 'No color';

        if ( ! $item ) {
            return $default;
        }

        $color_slug = $item->get_meta( 'pa_color' );
        if ( ! $color_slug ) {
            // Проверка всех метаданных, если ключ "pa_color" не найден напрямую
            $all_meta = $item->get_meta_data();
            foreach ( $all_meta as $m ) {
                $meta_key   = $m->get_data()['key'];
                $meta_value = $m->get_data()['value'];
                if ( false !== strpos( $meta_key, 'pa_color' ) ) {
                    $color_slug = $meta_value;
                    break;
                }
            }
        }

        if ( ! $color_slug ) {
            return $default;
        }

        // Пытаемся найти term по таксономии "pa_color"
        $taxonomy = 'pa_color';
        $term     = get_term_by( 'slug', $color_slug, $taxonomy );
        if ( ! $term ) {
            return esc_html( $color_slug );
        }

        $color_name = $term->name;
        $color_hex  = trim( get_term_meta( $term->term_id, 'product_attribute_color', true ) );

        if ( $color_hex && preg_match( '/^#([A-Fa-f0-9]{3}){1,2}$/', $color_hex ) ) {
            $html = sprintf(
                '%s <span style="display:inline-block;width:14px;height:14px;margin-left:4px;border:1px solid #ccc;vertical-align:middle;background-color:%s;"></span>',
                esc_html( $color_name ),
                esc_attr( $color_hex )
            );
            return $html;
        }

        return esc_html( $color_name );
    }
}
?>
<section class="woocommerce-order-details rental-order-details">
    <?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

<h1 class="profile-page-title profile-page-title--back profile-page-title--history"><a href="<?php echo wc_get_account_endpoint_url('orders') ?>">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M9 18L15 12L9 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</a>הזמנה מספר <?php echo $order_id; ?></h1>


<?php //get_template_part('page-templates/dashboard/history/review'); ?>





        <!-- Grid of ordered items -->
            <?php 
            $rental_items = [];
            $sales_items = [];

            // Спочатку розподіляємо товари у два масиви
            foreach ( $order_items as $item_id => $item ) {
                if ( ! $item || ! is_callable( [ $item, 'get_product' ] ) ) {
                    continue;
                }

                $product = $item->get_product();
                if ( ! $product ) {
                    continue;
                }


                    // Basic product info
                $product_name  = $product->get_name();
                $product_image = $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'rental-history__item-image' ) ); 
                $product_price = $product->get_price_html();

                // Берём атрибуты (size, color) из метаданных $item
                $size_label = my_get_item_size_label( $item );
                $color_html = my_get_item_color_swatch( $item );

                // Store ID (Dokan vendor)
                $store_id = get_post_field( 'post_author', $product->get_id() );


                $vendor = dokan()->vendor->get($store_id);

                //Store Name
                $store_name = $vendor->get_shop_name();

                $store_image_id = $vendor->get_avatar_id();


                // Booking data ...
                $booking_ids   = WC_Booking_Data_Store::get_booking_ids_from_order_item_id( $item_id );

                $is_booking    = false;
                $booking_start = '';
                $booking_end   = '';
                $status_text   = '';
                $status_class  = '';
                $booking_status = '';

                if ( ! empty( $booking_ids ) ) {
                    $b_id = reset( $booking_ids );
                    try {
                        $booking     = new WC_Booking( $b_id );
                        $is_booking  = true;

                        $booking_start  = $booking->get_start_date( get_option( 'date_format' ) );
                        $booking_end    = $booking->get_end_date( get_option( 'date_format' ) );
                        $booking_status = $booking->get_status();

                        if ( 'confirmed' === $booking_status ) {
                            $status_text  = 'Confirmed';
                            $status_class = 'bg-blue-100 text-blue-700';
                        } elseif ( 'completed' === $booking_status ) {
                            $status_text  = 'Completed';
                            $status_class = 'bg-gray-300 text-gray-700';
                        } elseif ( 'cancelled' === $booking_status ) {
                            $status_text  = 'Cancelled';
                            $status_class = 'bg-gray-300 text-gray-700';
                        } else {
                            $status_text  = 'In progress';
                            $status_class = 'bg-gray-300 text-gray-700';
                        }
                    } catch ( Exception $e ) {}
                }

                // === Check existing review (Dokan) for THIS order ===
                $existing_review_id = 0;
                $current_user_id    = get_current_user_id();
                
                $args_reviews = [
                    'post_type'      => 'dokan_store_reviews',
                    'post_status'    => 'publish',
                    'posts_per_page' => 1,
                    'author'         => $current_user_id,
                    'meta_query'     => [
                        [
                            'key'   => 'product_id',
                            'value' => $product->get_id(),
                        ], 
                    ],
                ];
                $existing_reviews = get_posts( $args_reviews ); //var_dump($existing_reviews );
                if ( $existing_reviews ) {
                    $existing_review_id = $existing_reviews[0]->ID;
                }
                
                $orderid_from_review = get_comment_meta( $existing_review_id , 'orderid',true); 
                    // Перевіряємо атрибут товару
                    $attributes = $product->get_attributes();
                    if ( isset( $attributes['pa_product-type'] ) ) {
                        if ( $attributes['pa_product-type'] == 'rent' ) {
                            $rental_items[] = $item;
                        } elseif ( $attributes['pa_product-type'] == 'buy' ) {
                            $sales_items[] = $item;
                        }
                    }
                }

                ?>
                <?php if($rental_items) :?>

                <h3 class="history-title">
                    <svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.0389 7.41667L11.817 6.19444L10.5944 7.41667M12 6.5C12 9.53757 9.53757 12 6.5 12C3.46243 12 1 9.53757 1 6.5C1 3.46243 3.46243 1 6.5 1C8.51784 1 10.2819 2.08664 11.2389 3.70667M6.5 3.44444V6.5L8.33333 7.72222" stroke="#858585" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>    
                    השכרה
                </h3>
                <ul class="history-list rental">
                    <?php 
                        foreach ( $rental_items as $item ) {
                            include locate_template('woocommerce/order/order-item-rental.php');
                        }
                    ?>
                </ul>

                <?php endif; ?>


                <?php if($sales_items) :?>
                    <h3 class="history-title">
                        <svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.5 5.63158H13.8158M1.07895 5.63158L2.93158 11.4211C3.04737 11.7684 3.39474 12 3.74211 12H10.5737C10.9211 12 11.2684 11.7684 11.3842 11.4211L13.2368 5.63158M6.28947 1L3.10526 5.63158M8.02632 1L11.2105 5.63158" stroke="#858585" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        קנייה מיידית
                    </h3>
                    <ul class="history-list sales">
                        <?php 
                            foreach ( $sales_items as $item ) {

                                //include locate_template('woocommerce/order/order-item-rental.php');

                                include locate_template('woocommerce/order/order-item-sales.php');
                            }
                        ?>
                    </ul>
                <?php endif; ?>


                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                    document.querySelectorAll(".rental-action__btn--review").forEach(button => {
                        button.addEventListener("click", function () {
                            // Знайдемо батьківський `.history-list__item`
                            const listItem = this.closest(".history-list__item");
                            if (listItem) {
                                // Знайдемо `.review-popup` всередині цього елемента
                                const reviewPopup = listItem.querySelector(".review-popup");
                                if (reviewPopup) {
                                    reviewPopup.style.display = "block";
                                }
                            }
                        });
                    });

 
                });
                </script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.review-popup').forEach(popup => {
        const steps = popup.querySelectorAll(".review-step");
        const nextButtons = popup.querySelectorAll(".review-next");
        const prevButtons = popup.querySelectorAll(".review-cancel");
        const backButtons = popup.querySelectorAll(".review-popup__back");
        const mediaUploadTriggers = popup.querySelectorAll(".review-popup__media-upload-trigger");
        const goToLeaveReviewBtn = popup.querySelector(".go-to-leave-review");

        const textarea = popup.querySelector(".review-popup__step-textarea");
        const counter = popup.querySelector(".review-popup__step-textarea-count");
        const resultField = popup.querySelector(".review-popup__review-reesult-text");
        const maxLength = 128;

        const ratingInputs = popup.querySelectorAll('input[type="radio"][name^="rating_"]');
        const resultDiv = popup.querySelector(".review-popup__review-rating-reesult");

        const mediaGallery = popup.querySelector(".review-popup__user-media-gallery");
        const mediaUploadWrap = popup.querySelector(".review-popup__media-gallery");
        const reviewMediaResult = popup.querySelector(".review-popup__review-media-reesult");

        const fileInput = popup.querySelector('input[type="file"][name^="review_image_"]');
        const openGalleryBtn = popup.querySelector("#open-gallery-review");

        let selectedImages = [];
        let currentStep = 0;

        function showStep(index) {
            currentStep = index;
            steps.forEach((step, i) => {
                step.classList.toggle("active", i === currentStep);
            });
        }

        function getStepIndexByClass(className) {
            return [...steps].findIndex(step => step.classList.contains(className));
        }

        function getNextAvailableStep(startIndex) {
            for (let i = startIndex; i < steps.length; i++) {
                if (!steps[i].classList.contains("media-upload")) {
                    return i;
                }
            }
            return steps.length - 1;
        }

        if (openGalleryBtn && fileInput) {
            openGalleryBtn.addEventListener("click", function () {
                fileInput.click();
            });

            fileInput.addEventListener("change", function (event) {
                handleFileSelect(event, mediaGallery);
            });
        }

        function handleFileSelect(event, gallery) {
            const files = event.target.files;
            if (!files.length) return;

            // Очистимо галерею перед додаванням нових фото

            Array.from(files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const imageUrl = e.target.result;

                    // Створення контейнера для фото
                    const mediaItem = document.createElement("div");
                    mediaItem.classList.add("review-popup__user-media-gallery-item");

                    // Додавання номера вибраного фото
                    const numDiv = document.createElement("div");
                    numDiv.classList.add("num");
                    numDiv.textContent = index + 1;

                    // Додавання самого фото
                    const img = document.createElement("img");
                    img.src = imageUrl;
                    img.alt = `Uploaded Image ${index + 1}`;

                    mediaItem.appendChild(numDiv);
                    mediaItem.appendChild(img);
                    gallery.prepend(mediaItem);
                };
                reader.readAsDataURL(file);
            });
        }

        nextButtons.forEach(button => {
            button.addEventListener("click", function () {
                if (currentStep < steps.length - 1) {
                    let nextStep = getNextAvailableStep(currentStep + 1);

                    if (steps[currentStep].classList.contains("leave-review")) {
                        while (steps[nextStep] && steps[nextStep].classList.contains("media-upload")) {
                            nextStep++;
                        }
                    }

                    showStep(nextStep);
                }
            });
        });

        prevButtons.forEach(button => {
            button.addEventListener("click", function () {
                if (currentStep > 0) {
                    showStep(currentStep - 1);
                }
            });
        });

        backButtons.forEach(button => {
            button.addEventListener("click", function () {
                if (currentStep > 0) {
                    showStep(currentStep - 1);
                }
            });
        });

        mediaUploadTriggers.forEach(button => {
            button.addEventListener("click", function () {
                let mediaStepIndex = getStepIndexByClass("media-upload");
                if (mediaStepIndex !== -1) {
                    showStep(mediaStepIndex);
                }
            });
        });

        popup.querySelectorAll(".review-cancel, .review-popup__close").forEach(button => {
            button.addEventListener("click", function () {
                popup.style.display = "none";
            });
        });

        if (textarea && counter && resultField) {
            textarea.addEventListener("input", function () {
                if (this.value.length > maxLength) {
                    this.value = this.value.substring(0, maxLength);
                }
                counter.textContent = `${this.value.length}/${maxLength}`;
                resultField.textContent = this.value;
            });
        }

        ratingInputs.forEach(input => {
            input.addEventListener("change", function () {
                const selectedLabel = this.closest(".review-popup__radio-label");
                if (selectedLabel) {
                    const svg = selectedLabel.querySelector("svg");
                    if (svg && resultDiv) {
                        resultDiv.innerHTML = svg.outerHTML;
                    }
                }
            });
        });


        const openCameraBtn = popup.querySelector("#open-camera-review");
        const cameraWrap = popup.querySelector(".camera-wrap");
        const video = popup.querySelector("#video");
        const captureBtn = popup.querySelector("#captureBtn");
        const saveBtn = popup.querySelector("#saveBtn");
        const canvas = popup.querySelector("#canvas");
        const snapshotImg = popup.querySelector("#snapshot");

        let stream = null;

        if (openCameraBtn && video && cameraWrap) {
            openCameraBtn.addEventListener("click", function () {
                cameraWrap.style.display = "block";

                // Запит на доступ до камери
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(function (cameraStream) {
                        stream = cameraStream;
                        video.srcObject = stream;
                    })
                    .catch(function (error) {
                        console.error("Помилка доступу до камери:", error);
                    });
            });
        }

        if (captureBtn && canvas && snapshotImg) {
            captureBtn.addEventListener("click", function () {
                const context = canvas.getContext("2d");
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Отримання знімку
                const imageUrl = canvas.toDataURL("image/png");
                snapshotImg.src = imageUrl;
                snapshotImg.style.display = "block";
                saveBtn.style.display = "inline-block";
            });
        }

        if (saveBtn) {
            saveBtn.addEventListener("click", function () {
                const imageUrl = snapshotImg.src;
                if (!imageUrl) return;

                // Додаємо зроблене фото в галерею
                const mediaGallery = popup.querySelector(".review-popup__user-media-gallery");
                if (mediaGallery) {
                    const mediaItem = document.createElement("div");
                    mediaItem.classList.add("review-popup__user-media-gallery-item");

                    const numDiv = document.createElement("div");
                    numDiv.classList.add("num");
                    numDiv.textContent = mediaGallery.children.length + 1;

                    const img = document.createElement("img");
                    img.src = imageUrl;
                    img.alt = "Captured Image";

                    mediaItem.appendChild(numDiv);
                    mediaItem.appendChild(img);
                    mediaGallery.prepend(mediaItem);
                }

                // Зупиняємо камеру після знімку
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    video.srcObject = null;
                    cameraWrap.style.display = "none";
                }
            });
        }


        if (mediaGallery) {
            mediaGallery.addEventListener("click", function (event) {
                const mediaItem = event.target.closest(".review-popup__user-media-gallery-item");
                if (!mediaItem) return;

                const img = mediaItem.querySelector("img");
                if (!img) return;

                const imgSrc = img.src;

                if (selectedImages.some(item => item.src === imgSrc)) {
                    selectedImages = selectedImages.filter(item => item.src !== imgSrc);
                    mediaItem.classList.remove("selected");
                } else {
                    if (selectedImages.length < 5) {
                        selectedImages.push({ src: imgSrc, element: mediaItem });
                        mediaItem.classList.add("selected");
                    }
                }

                updateSelectedImages();
            });
        }

        function updateSelectedImages() {
            selectedImages.forEach((item, index) => {
                let numDiv = item.element.querySelector(".num");
                if (!numDiv) {
                    numDiv = document.createElement("div");
                    numDiv.classList.add("num");
                    item.element.prepend(numDiv);
                }
                numDiv.textContent = index + 1;
            });

            popup.querySelectorAll(".review-popup__user-media-gallery-item:not(.selected) .num").forEach(numDiv => {
                numDiv.textContent = "";
            });

            if (mediaUploadWrap) {
                mediaUploadWrap.innerHTML = "";
            }
            if (reviewMediaResult) {
                reviewMediaResult.innerHTML = "";
            }

            selectedImages.forEach((item, index) => {
                const imgElement = document.createElement("img");
                imgElement.src = item.src;
                imgElement.classList.add("selected-media-thumbnail");
                imgElement.setAttribute("data-index", index + 1);

                if (mediaUploadWrap) {
                    mediaUploadWrap.appendChild(imgElement.cloneNode(true));
                }
                if (reviewMediaResult) {
                    reviewMediaResult.appendChild(imgElement.cloneNode(true));
                }
            });
        }

        if (goToLeaveReviewBtn) {
            goToLeaveReviewBtn.addEventListener("click", function () {
                let leaveReviewStepIndex = getStepIndexByClass("leave-review");
                if (leaveReviewStepIndex !== -1) {
                    showStep(leaveReviewStepIndex);
                }
            });
        }

        showStep(0);
    });

    document.querySelectorAll(".review-popup-trigger").forEach(trigger => {
        trigger.addEventListener("click", function () {
            const popup = document.querySelector(this.dataset.target);
            if (popup) {
                popup.style.display = "block";
                popup.querySelector(".review-step").classList.add("active");
            }
        });
    });

    document.querySelectorAll(".review-popup__close, .review-cancel").forEach(button => {
        button.addEventListener("click", function () {
            const popup = this.closest(".review-popup");
            if (popup) {
                popup.style.display = "none";
            }
        });
    });
});
</script>




    <?php if(false): ?>            
    <!-- Order totals (Subtotal, Shipping, etc.) -->
    <div class="order-details-bottom">
        <h3><?php esc_html_e( 'Order Totals', 'woocommerce' ); ?></h3>
        <?php
        foreach ( $order->get_order_item_totals() as $key => $total ) {
            ?>
            <p>
                <strong><?php echo esc_html( $total['label'] ); ?>:</strong>
                <?php echo wp_kses_post( $total['value'] ); ?>
            </p>
            <?php
        }

        // If there's a customer note
        if ( $order->get_customer_note() ) {
            ?>
            <p>
                <strong><?php esc_html_e( 'Note:', 'woocommerce' ); ?></strong>
                <?php echo wp_kses( nl2br( wptexturize( $order->get_customer_note() ) ), [] ); ?>
            </p>
            <?php
        }
        ?>
    </div>

    <?php endif; ?> 


    <?php if (false): ?>
    <!-- Approve and Return button -->
    <div class="order-approval">
        <h3><?php esc_html_e( 'Approve and Return', 'your-textdomain' ); ?></h3>
        <p><?php esc_html_e( 'Once you confirm all items and finalize your review, click here to complete the return process.', 'your-textdomain' ); ?></p>
        <button type="button" class="button button-primary">
            <?php esc_html_e( 'Make a Return', 'your-textdomain' ); ?>
        </button>
    </div>

    <?php endif; ?>

    <?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
</section>

<?php
/**
 * Action hook fired after the order details.
 */
do_action( 'woocommerce_after_order_details', $order );

// Show customer details if the current user is the order owner
if ( $show_customer_details ) {
   // wc_get_template( 'order/order-details-customer.php', [ 'order' => $order ] );
}
?>
