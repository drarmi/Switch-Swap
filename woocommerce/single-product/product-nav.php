<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Omnis\src\inc\classes\my_favorites\My_Favorites;

$current_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$user_logged = is_user_logged_in();
$product_id = $args["ID"] ?? "";
$prevPage = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
$like_product = My_Favorites::get_user_like_product();

?>
<div class="custom-product-nav">
    <div>
        <?php if ($user_logged): ?>
        <div class="like like-favorites-js nav-icon <?php echo !empty($like_product) && in_array($product_id, $like_product) ? "active" : "" ?>"
            data-product-id="<?php echo esc_attr("$product_id"); ?>">
            <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M9.99413 3.27985C8.328 1.332 5.54963 0.808035 3.46208 2.59168C1.37454 4.37532 1.08064 7.35748 2.72 9.467C4.08302 11.2209 8.20798 14.9201 9.55992 16.1174C9.71117 16.2513 9.7868 16.3183 9.87502 16.3446C9.95201 16.3676 10.0363 16.3676 10.1132 16.3446C10.2015 16.3183 10.2771 16.2513 10.4283 16.1174C11.7803 14.9201 15.9052 11.2209 17.2683 9.467C18.9076 7.35748 18.6496 4.35656 16.5262 2.59168C14.4028 0.826798 11.6603 1.332 9.99413 3.27985Z"
                    stroke="#111111" stroke-width="1.5" stroke-linejoin="round" />
            </svg>
        </div>
        <?php endif; ?>
        <div class="share-wrapper">
            <div class="share">
                <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6.15833 11.2583L11.85 14.575M11.8417 5.42496L6.15833 8.74163M16.5 4.16663C16.5 5.54734 15.3807 6.66663 14 6.66663C12.6193 6.66663 11.5 5.54734 11.5 4.16663C11.5 2.78591 12.6193 1.66663 14 1.66663C15.3807 1.66663 16.5 2.78591 16.5 4.16663ZM6.5 9.99996C6.5 11.3807 5.38071 12.5 4 12.5C2.61929 12.5 1.5 11.3807 1.5 9.99996C1.5 8.61925 2.61929 7.49996 4 7.49996C5.38071 7.49996 6.5 8.61925 6.5 9.99996ZM16.5 15.8333C16.5 17.214 15.3807 18.3333 14 18.3333C12.6193 18.3333 11.5 17.214 11.5 15.8333C11.5 14.4526 12.6193 13.3333 14 13.3333C15.3807 13.3333 16.5 14.4526 16.5 15.8333Z"
                        stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <ul class="social">
                <li>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($current_url); ?>"
                        target="_blank" title="Facebook">
                        <svg class="facebook" xmlns="http://www.w3.org/2000/svg" height="20" width="20"
                            viewBox="0 0 512 512">
                            <path fill="#111111"
                                d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z" />
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($current_url); ?>"
                        target="_blank" title="Twitter">
                        <svg class="twitter" xmlns="http://www.w3.org/2000/svg" height="20" width="20"
                            viewBox="0 0 512 512">
                            <path fill="#111111"
                                d="M459.4 151.7c.3 4.5 .3 9.1 .3 13.6 0 138.7-105.6 298.6-298.6 298.6-59.5 0-114.7-17.2-161.1-47.1 8.4 1 16.6 1.3 25.3 1.3 49.1 0 94.2-16.6 130.3-44.8-46.1-1-84.8-31.2-98.1-72.8 6.5 1 13 1.6 19.8 1.6 9.4 0 18.8-1.3 27.6-3.6-48.1-9.7-84.1-52-84.1-103v-1.3c14 7.8 30.2 12.7 47.4 13.3-28.3-18.8-46.8-51-46.8-87.4 0-19.5 5.2-37.4 14.3-53 51.7 63.7 129.3 105.3 216.4 109.8-1.6-7.8-2.6-15.9-2.6-24 0-57.8 46.8-104.9 104.9-104.9 30.2 0 57.5 12.7 76.7 33.1 23.7-4.5 46.5-13.3 66.6-25.3-7.8 24.4-24.4 44.8-46.1 57.8 21.1-2.3 41.6-8.1 60.4-16.2-14.3 20.8-32.2 39.3-52.6 54.3z" />
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($current_url); ?>"
                        target="_blank" title="LinkedIn">
                        <svg class="linkedin" xmlns="http://www.w3.org/2000/svg" height="20" width="17.5"
                            viewBox="0 0 448 512">
                            <path fill="#111111"
                                d="M100.3 448H7.4V148.9h92.9zM53.8 108.1C24.1 108.1 0 83.5 0 53.8a53.8 53.8 0 0 1 107.6 0c0 29.7-24.1 54.3-53.8 54.3zM447.9 448h-92.7V302.4c0-34.7-.7-79.2-48.3-79.2-48.3 0-55.7 37.7-55.7 76.7V448h-92.8V148.9h89.1v40.8h1.3c12.4-23.5 42.7-48.3 87.9-48.3 94 0 111.3 61.9 111.3 142.3V448z" />
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="nav-to">
        <a href="<?php echo esc_url($prevPage); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 18L15 12L9 6" stroke="black" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </a>
    </div>
</div>