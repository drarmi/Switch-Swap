<?php 
        $store_user_id = get_query_var( 'author' ); // Get the store owner's user ID
        $current_user_id = get_current_user_id();
        $store_list_page_id = dokan_get_option( 'store_listing', 'dokan_pages' ); 




        if ( dokan_is_user_seller( $current_user_id ) ) {
            $store_info = dokan_get_store_info( $current_user_id );
            $store_url  = dokan_get_store_url( $current_user_id );
        
        } else {
            $store_url = home_url('store-creation-prompt');
        }



    ?>
    <footer class="swap-footer">
        <nav class="footer-nav">
            <a href="<?php echo home_url(); ?>">
                <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 16.0002H14M9.0177 1.76424L2.23539 7.03937C1.78202 7.39199 1.55534 7.5683 1.39203 7.7891C1.24737 7.98469 1.1396 8.20503 1.07403 8.4393C1 8.70376 1 8.99094 1 9.5653V16.8002C1 17.9203 1 18.4804 1.21799 18.9082C1.40973 19.2845 1.71569 19.5905 2.09202 19.7822C2.51984 20.0002 3.07989 20.0002 4.2 20.0002H15.8C16.9201 20.0002 17.4802 20.0002 17.908 19.7822C18.2843 19.5905 18.5903 19.2845 18.782 18.9082C19 18.4804 19 17.9203 19 16.8002V9.5653C19 8.99094 19 8.70376 18.926 8.4393C18.8604 8.20503 18.7526 7.98469 18.608 7.7891C18.4447 7.5683 18.218 7.39199 17.7646 7.03937L10.9823 1.76424C10.631 1.49099 10.4553 1.35436 10.2613 1.30184C10.0902 1.2555 9.9098 1.2555 9.73865 1.30184C9.54468 1.35436 9.36902 1.49099 9.0177 1.76424Z" stroke="#111111" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <a href="<?php echo home_url('search'); ?>">
                <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.5 19L16.0001 15.5M18.5 9.5C18.5 14.1944 14.6944 18 10 18C5.30558 18 1.5 14.1944 1.5 9.5C1.5 4.80558 5.30558 1 10 1C14.6944 1 18.5 4.80558 18.5 9.5Z" stroke="#111111" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <a href="#" id="upload-product">
                <svg width=" 16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 1V15M1 8H15" stroke="#111111" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <!-- <a href="<?php //echo ( $current_user_id === (int) $store_user_id ) ? '#' : 'store-creation-prompt'; ?>" id="<?php //echo ( $current_user_id === (int) $store_user_id ) ? 'upload-product' : 'upload-product_'; ?>">
                <svg width=" 16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 1V15M1 8H15" stroke="#111111" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a> -->
            <a href="<?php echo home_url('cart');  ?>">
                <svg width="21" height="22" viewBox="0 0 21 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.5004 8V5C14.5004 2.79086 12.7095 1 10.5004 1C8.29123 1 6.50037 2.79086 6.50037 5V8M2.09237 9.35196L1.49237 15.752C1.32178 17.5717 1.23648 18.4815 1.53842 19.1843C1.80367 19.8016 2.26849 20.3121 2.85839 20.6338C3.5299 21 4.44374 21 6.27142 21H14.7293C16.557 21 17.4708 21 18.1423 20.6338C18.7322 20.3121 19.1971 19.8016 19.4623 19.1843C19.7643 18.4815 19.679 17.5717 19.5084 15.752L18.9084 9.35197C18.7643 7.81535 18.6923 7.04704 18.3467 6.46616C18.0424 5.95458 17.5927 5.54511 17.055 5.28984C16.4444 5 15.6727 5 14.1293 5L6.87142 5C5.32806 5 4.55638 5 3.94579 5.28984C3.40803 5.54511 2.95838 5.95458 2.65403 6.46616C2.30846 7.04704 2.23643 7.81534 2.09237 9.35196Z" stroke="#111111" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

            </a>
            <a href="<?php echo $store_url; ?>">
                <svg width="26" height="22" viewBox="0 0 26 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.4286 19.2833C22.8754 19.2833 23.2357 18.9302 23.2357 18.4999V17.1448C23.2357 16.8239 23.0282 16.5296 22.7179 16.4133L22.4286 19.2833ZM22.4286 19.2833H3.57143C3.1246 19.2833 2.76429 18.9302 2.76429 18.4999V17.1448C2.76429 16.8239 2.97176 16.5296 3.28209 16.4133L22.4286 19.2833ZM23.3694 14.7646L23.3692 14.7646L13.9071 11.2257V9.29031C14.8692 9.08872 15.733 8.57516 16.3563 7.83297C16.9882 7.08043 17.3343 6.13887 17.3357 5.16692V5.16684C17.3357 2.84031 15.3894 0.95 13 0.95C10.6106 0.95 8.66429 2.84031 8.66429 5.16684C8.66429 5.40159 8.76021 5.62642 8.93048 5.79197C9.10071 5.95747 9.33129 6.05021 9.57143 6.05021C9.81157 6.05021 10.0422 5.95747 10.2124 5.79197C10.3826 5.62642 10.4786 5.40159 10.4786 5.16684C10.4786 3.81738 11.6086 2.71674 13 2.71674C14.3914 2.71674 15.5214 3.81738 15.5214 5.16684C15.5214 6.5163 14.3914 7.61694 13 7.61694C12.7599 7.61694 12.5293 7.70968 12.3591 7.87518C12.1888 8.04073 12.0929 8.26557 12.0929 8.50031V11.2266L2.63077 14.7646L2.63058 14.7646C2.13753 14.9513 1.71343 15.278 1.41361 15.7024C1.11378 16.1269 0.95217 16.6294 0.95 17.1446V17.1448V18.4999C0.95 19.9072 2.12744 21.05 3.57143 21.05H22.4286C23.8726 21.05 25.05 19.9072 25.05 18.4999L25.05 17.1448L25.05 17.1446C25.0478 16.6294 24.8862 16.1269 24.5864 15.7024C24.2866 15.278 23.8625 14.9513 23.3694 14.7646ZM13 12.7789L22.7179 16.4133H3.28212L13 12.7789Z" fill="#111111" stroke="#111111" stroke-width="0.1" />
                </svg>
            </a>
        </nav>
    </footer>
    <?php
    get_template_part("template-parts/modal-you-must-log-in", null);
    get_template_part("src/template-parts/upload-modal", null);
    ?>

    <?php wp_footer(); ?>
    </div>
    </body>

    </html>