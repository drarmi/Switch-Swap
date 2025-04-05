<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/* Template Name:  Product management list */

if ( !is_user_logged_in() ) {
    wp_redirect(home_url());
    exit;
}

get_header('profile');
?>
<div>
    <section class="list-control">
        <?php get_template_part("src/template-parts/product-management/management-list-control"); ?>
    </section>

    <section class="list-result">
        <?php get_template_part("src/template-parts/product-management/management-list"); ?>
    </section>
</div>


<?php get_footer('profile');?>
