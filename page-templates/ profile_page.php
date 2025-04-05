<?php
/* Template Name:  Profile Page */

if ( !is_user_logged_in() ) {
    wp_redirect(home_url('/login/'));
    exit;
}

get_header('profile');
?>

<section class="main-nav-profile modal-section" data-profile="main-nav-profile">
    <?php get_template_part("src/template-parts/profile/main-nav"); ?>
</section>


<?php if(false): ?>
<section class="synchronization_social_networks modal-section" data-profile="synchronization_social_networks">
    <?php get_template_part("src/template-parts/profile/authenticated"); ?>
</section>

<section class="personal_details modal-section" data-profile="personal_details">
    <?php get_template_part("src/template-parts/profile/personal_details"); ?>
</section>

<section class="notification_management modal-section" data-profile="notification_management">
    <?php get_template_part("src/template-parts/profile/personal_notifications"); ?>
</section>

<?php endif; ?>

<?php get_footer('swap');?>

