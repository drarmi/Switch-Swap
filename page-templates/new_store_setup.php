<?php
/* Template Name:  New Store Setup */

if ( !is_user_logged_in() ) {
    wp_redirect(home_url('/login/'));
    exit;
}

get_header('setup');
?>
<section class="setup-start modal-section" data-setup-section="setup-start">
    <?php get_template_part("src/template-parts/setup/start"); ?>
</section>

<section class="setup-form modal-section" data-setup-section="setup-form">
    <?php get_template_part("src/template-parts/setup/form"); ?>
</section>

<section class="setup-final modal-section" data-setup-section="setup-final">
    <?php get_template_part("src/template-parts/setup/final-store"); ?>
</section>

<?php get_footer('setup');?>
