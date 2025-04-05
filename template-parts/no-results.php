<?php 
// Retrieve the display status from the $args array, defaulting to 'none'
$status = isset( $args['status'] ) ? $args['status'] : 'none';
$image_svg = isset( $args['image'] ) ? $args['image'] : 'none';
// Retrieve the SVG image from ACF (stored in the Options page) 
?>
<p id="no-results" class="no-results" style="display: <?php echo esc_attr( $status ); ?>;">
    <?php if ( $image_svg ) : ?>
        <img src="<?php echo esc_url( $image_svg ); ?>" alt="<?php echo esc_attr( $image_svg ); ?>">
    <?php endif; ?>
</p>
