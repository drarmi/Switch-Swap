<?php
/**
 * Content Search Template
 *
 * @package omnis_base
 * @since 4.4.0
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
    </header><!-- .entry-header -->

    <div class="entry-summary">
        <?php the_excerpt(); ?>
    </div><!-- .entry-summary -->

    <footer class="entry-footer">
        <?php if ( 'post' === get_post_type() ) : ?>
            <div class="entry-meta">
                <?php
                esc_html_e( time_ago() );
                ?>
            </div><!-- .entry-meta -->
        <?php endif; ?>
    </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
