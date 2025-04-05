<?php

get_header(); ?>

	

			<?php
			while ( have_posts() ) :
				the_post();


				//do_action( 'storefront_page_before' );

				the_content();

				/**
				 * Functions hooked in to storefront_page_after action
				 *
				 * @hooked storefront_display_comments - 10
				 */
				//do_action( 'storefront_page_after' );

			endwhile; // End of the loop.
			?>



<?php
get_footer();
