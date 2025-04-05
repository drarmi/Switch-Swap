<?php
/**
 * Cookie Template
 *
 * @package omnis_base
 * @since 4.4.0
 */

// Define cookie consent details.
$cookie = array(
    'title' => __( 'We use cookies in the delivery of our services' ),
    'message' => __( 'To learn about the cookies we use and information about your preferences and opt-out choices, please <a href="#">click here</a>. By using our platform you agree to our use of cookies.' ),
    'accept_all' => __( 'Accept all' ),
    'accept_selection' => __( 'Accept selection' ),
    'reject_all' => __( 'Reject all' ),
);
?>
<div id="cookie-consent-box" class="cookie-consent__wrapper" style="display: none;">
    <div class="cookie-consent__container">
        <div class="cookie-consent__title">
            <?php echo esc_html( $cookie['title'] ); ?>
        </div>
        <div class="cookie-consent__message">
            <?php echo esc_html( $cookie['message'] ); ?>
        </div>
        <div class="cookie-consent__buttons">
            <button class="cookie-consent__btn cookie-consent__reject_all js-reject_all">
                <?php echo esc_html( $cookie['reject_all'] ); ?>
            </button>
            <button class="cookie-consent__btn cookie-consent__accept_selection js-accept_selection">
                <?php echo esc_html( $cookie['accept_selection'] ); ?>
            </button>
            <button class="cookie-consent__btn cookie-consent__accept_all btn-blue js-accept_all">
                <?php echo esc_html( $cookie['accept_all'] ); ?>
            </button>
        </div>
        <div class="cookie-consent__checkboxes">
            <?php
            // Define cookie categories.
            $categories = array( 'Necessary', 'Analytics', 'Preferences', 'Marketing' );
            foreach ( $categories as $category ) {
                $single_cat_id = 'checkbox_' . $category;
                ?>
                <div class="cookie-consent__checkboxes-line">
                    <input type="checkbox"
                           id="<?php echo esc_html( $single_cat_id ); ?>"
                           name="<?php echo esc_html( strtolower( $category ) ); ?>"
                    />
                    <label for="<?php echo esc_html( $single_cat_id ); ?>">
                        <?php echo esc_html( $category ); ?>
                    </label>
                </div>
            <?php } ?>
        </div>
        <button class="cookie-consent__close-cookie">
            <span class="sr-only"><?php echo esc_html( __( 'Close', 'omnis_base' ) ); ?></span>
        </button>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Retrieve cookie consent status from local storage
        const consent = localStorage.getItem('cookieConsent');

        const acceptAllButton = document.querySelector('.js-accept_all');
        const rejectAllButton = document.querySelector('.js-reject_all');
        const acceptSelectionButton = document.querySelector('.js-accept_selection');
        const closeCookieBtns = document.querySelectorAll('.cookie-consent__close-cookie');
        const cookieConsentBox = document.getElementById('cookie-consent-box');

        // Show the cookie consent box if no consent is found
        if (!consent && cookieConsentBox) {
            cookieConsentBox.style.display = 'flex';
        }

        // Function to save consent and hide the cookie consent box.
        const closeConsent = () => {
            localStorage.setItem('cookieConsent', 'true');
            if (cookieConsentBox) {
                cookieConsentBox.style.display = 'none';
            }
        };

        // Placeholder for Google Tag Manager (GTM) initialization.
        const initializeGTM = () => {
            // Add code for initializing Google Tag Manager here.
        }

        // Function to save consent, hide the box, and initialize GTM.
        const closeAndInitialize = () => {
            closeConsent();
            initializeGTM();
        };

        // Event listener for "Accept All" button if the element exists.
        acceptAllButton && acceptAllButton.addEventListener('click', closeAndInitialize);

        // Event listener for "Reject All" button if the element exists.
        rejectAllButton && rejectAllButton.addEventListener('click', closeConsent);

        // Event listener for "Accept Selection" button.
        acceptSelectionButton
        && acceptSelectionButton.addEventListener('click', (event) => {


            // Get all checkboxes.
            const checkboxes = document.querySelectorAll('.cookie-consent__checkboxes input[type="checkbox"]');

            // Check if any checkbox is selected.
            const isChecked = Array.from(checkboxes).some(checkbox => checkbox?.checked);

            if (!isChecked) {

                // Highlight and shake labels and button if no checkbox is selected.
                const target = event.target;
                if (target instanceof HTMLElement) {
                    target.classList.add('highlight', 'shake');
                }

                const labels = document.querySelectorAll('.cookie-consent__checkboxes label');

                labels && labels.forEach(label => {
                    label.classList.add('label-highlight', 'shake');
                });
                setTimeout(() => {
                    if (target instanceof HTMLElement) {
                        target.classList.remove('highlight', 'shake');
                    }

                    labels && labels.forEach(label => {
                        label.classList.remove('label-highlight', 'shake');
                    });
                }, 500);

                return;
            }

            // Initialize GTM if the Analytics checkbox is checked
            const analyticsCheckbox = document.getElementById('checkbox_Analytics');

            if (analyticsCheckbox instanceof HTMLInputElement && analyticsCheckbox.checked) {
                closeAndInitialize();
            } else {
                closeConsent();
            }

        });

        // Close buttons event listener
        closeCookieBtns && [...closeCookieBtns].forEach(button => {
            button.addEventListener('click', closeConsent);
        });


    });
</script>
