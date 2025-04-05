<?php
/**
 * 404-Page Template
 *
 * @package omnis_base
 * @since 4.4.0
 */

get_header();
?>

<section class="error-404 not-found">
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'omnis_base' ); ?></h1>
    </header><!-- .page-header -->
    <div class="page-content">
        <p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'omnis_base' ); ?></p>
        <?php get_search_form(); ?>
    </div><!-- .page-content -->
</section><!-- .error-404 -->


<?php get_footer(); ?>
