<div id="media-modal" class="modal modal-step" data-step="2">
    <div class="modal-content">
        <div class="modal-content-top">
            <div class="close-upload-modal">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>

            <div class="select">
                <button id="open-gallery" class="btn"><?php esc_html_e("גָלֶרֵיָה", "swap") ?></button>
                <button id="open-camera" class="btn"><?php esc_html_e("מַצלֵמָה", "swap") ?></button>
            </div>

            <div>
                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 9.07449C2 8.72417 2 8.54901 2.01462 8.40148C2.1556 6.97853 3.28127 5.85287 4.70421 5.71188C4.85174 5.69727 5.03636 5.69727 5.40558 5.69727C5.54785 5.69727 5.61899 5.69727 5.67939 5.69361C6.45061 5.6469 7.12595 5.16014 7.41414 4.44326C7.43671 4.38712 7.45781 4.32384 7.5 4.19727C7.54219 4.07069 7.56329 4.00741 7.58586 3.95127C7.87405 3.23439 8.54939 2.74763 9.32061 2.70092C9.38101 2.69727 9.44772 2.69727 9.58114 2.69727H14.4189C14.5523 2.69727 14.619 2.69727 14.6794 2.70092C15.4506 2.74763 16.126 3.23439 16.4141 3.95127C16.4367 4.00741 16.4578 4.07069 16.5 4.19727C16.5422 4.32384 16.5633 4.38712 16.5859 4.44326C16.874 5.16014 17.5494 5.6469 18.3206 5.69361C18.381 5.69727 18.4521 5.69727 18.5944 5.69727C18.9636 5.69727 19.1483 5.69727 19.2958 5.71188C20.7187 5.85287 21.8444 6.97853 21.9854 8.40148C22 8.54901 22 8.72417 22 9.07449V16.8973C22 18.5774 22 19.4175 21.673 20.0592C21.3854 20.6237 20.9265 21.0827 20.362 21.3703C19.7202 21.6973 18.8802 21.6973 17.2 21.6973H6.8C5.11984 21.6973 4.27976 21.6973 3.63803 21.3703C3.07354 21.0827 2.6146 20.6237 2.32698 20.0592C2 19.4175 2 18.5774 2 16.8973V9.07449Z" stroke="#8F6B45" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M12 17.1973C14.2091 17.1973 16 15.4064 16 13.1973C16 10.9881 14.2091 9.19727 12 9.19727C9.79086 9.19727 8 10.9881 8 13.1973C8 15.4064 9.79086 17.1973 12 17.1973Z" stroke="#8F6B45" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>

        <h2><?php esc_html_e("העלאת פריט חדש", "swap") ?></h2>
        <p class="text-count"><?php esc_html_e("לחץ/י לבחירת תמונות להעלאה.", "swap") ?></p>
        <p class="count-count"><span class="num">1</span>/5</p>

        <div class="list">
            <?php

            $args = [
                'post_type' => 'attachment',
                'post_status' => 'any',
                'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png',
                'posts_per_page' => -1,
                'author' => get_current_user_id(),
                'orderby' => 'date',
                'order' => 'DESC',
            ];

            $query = new \WP_Query($args);

            if ($query->have_posts()) {
                echo '<div class="user-media-gallery">';
                while ($query->have_posts()) {
                    $query->the_post();
                    $image_url = wp_get_attachment_url(get_the_ID());
                    echo '<div><div class="num"></div><img data-id="' . get_the_ID() . '" src="' . esc_url($image_url) . '" alt="' . get_the_title() . '" /></div>';
                }
                echo '</div>';
            }
            wp_reset_postdata();

            ?>
            <div class="camera-wrap" style="display: none;">
                <video id="video" width="640" height="480" autoplay></video>
                <button id="captureBtn"></button>
                <button id="saveBtn" style="display: none;"><?php esc_html_e("לְהַצִיל", "swap"); ?></button>
                <canvas id="canvas" style="display: none;"></canvas>
                <img id="snapshot" src="" alt="Знімок" style="display: none;">
            </div>
        </div>
        <input type="file" id="select-images" multiple accept="image/*" class="hidden">
    </div>
</div>