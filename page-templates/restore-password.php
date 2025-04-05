<?php
/* Template Name: Restore-password */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('login');
?>

<main class="main-container lost-password-wrap">
    <div class="restore-pasword-form">
        <?php get_template_part("/src/template-parts/lost-password/restore-pasword-form", null, array()) ?>
    </div>
    <div class="restore-pasword-done" style="display: none;">
        <?php get_template_part("/src/template-parts/lost-password/restore-pasword-done", null, array()) ?>
    </div>
</main>

<?php
get_footer('login');
