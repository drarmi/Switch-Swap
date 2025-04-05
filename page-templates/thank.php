<?php
/**
 * Template Name: Template [Thank page]
 *
 * @package omnis_base
 * @since   4.4.0
 */

get_header();
$button = get_field( 'button' );
$background = get_field( 'background' );
?>
<div class="thank">
    <?php if ( $background ) : ?>
        <img class="background" src="<?php echo esc_url( $background['url'] ); ?>"
             alt="<?php echo esc_attr( $background['alt'] ); ?>"/>
    <?php endif; ?>
    <style>
        .thank {
            background: url(<?php the_field( 'background_mobile' ); ?>);
        }
    </style>
    <div class="container">
        <div class="content">

            <div class="icon">
                <svg width="70" height="70" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M61.1703 25.6747C60.9528 32.511 57.9356 39.6858 52.2684 45.353C41.6225 55.9989 25.6567 57.2934 16.6077 48.2444C10.1789 41.8156 8.97078 31.8956 12.6277 22.8723C7.70097 32.6775 8.81841 44.3028 16.2942 51.7785C25.8755 61.3598 42.2728 60.4968 52.9187 49.8509C59.7219 43.0477 62.53 33.8957 61.1703 25.6747Z"
                          fill="white"/>
                    <path d="M50.4776 16.0078L30.9154 35.57L22.0234 26.6781" stroke="white" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <h1 class="title">
                <?php the_field( 'title' ); ?>
            </h1>
            <div class="description">
                <?php the_field( 'description' ); ?>
            </div>
            <div class="button">
                <?php if ( $button ) : ?>
                    <a href="<?php echo esc_url( get_home_url() ); ?>"
                       target="<?php echo esc_attr( $button['target'] ); ?>">
                        <?php echo esc_html( $button['title'] ); ?>
                        <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.5 6L9.5 12L15.5 18" stroke="#030A11" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
