<?php
/* Template Name: Lost-password */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('login');
?>

<main class="main-container lost-password-wrap">
    <?php get_template_part("/src/template-parts/lost-password/lost-pasword-logo-bar", null, array()) ?>
    <div class="lost-pasword-form">
        <?php get_template_part("/src/template-parts/lost-password/lost-pasword-form", null, array()) ?>
    </div>
    <div class="lost-pasword-send" style="display: none;">
        <?php get_template_part("/src/template-parts/lost-password/lost-pasword-send", null, array()) ?>
    </div>
</main>

<?php
get_footer('login');
