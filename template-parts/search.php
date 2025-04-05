<?php
/**
 * Template Name: Custom Search Page
 */

use Omnis\src\inc\classes\search\Product_Search;

get_header();

$user_id = get_current_user_id();
$search_history = get_user_meta( $user_id, 'search_history', true );
if ( ! is_array( $search_history ) ) {
    $search_history = [];
}

if ( ! empty( $search_history ) ) {
    // If the user already has search queries, display the results of the last query
    $last_search_term = $search_history[0];
    $section_title = get_field('recent_search_title') ? get_field('recent_search_title') : 'Recent Searches:';

    // Perform search on the server (10 products)
    $query_args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 10,
        's'              => $last_search_term,
    ];
    $query = new WP_Query( $query_args );
    $found_posts = $query->found_posts;
} else {
    // Retrieve trending tags from ACF (option field)
    $trending_tags = get_field('trending_tags', 'option');
    if ( ! is_array( $trending_tags ) || empty( $trending_tags ) ) {
        // Fallback array if the ACF field is not set
        $trending_tags = array('Black Dress', 'White Dress', 'Trendy', 'Contemporary Fashion');
    }
    $default_trending = $trending_tags[0];
    $section_title = get_field('default_trending_title') ? get_field('default_trending_title') : 'You Might Be Interested In:';

    $query_args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 10,
        's'              => $default_trending,
    ];
    $query = new WP_Query( $query_args );
    $found_posts = $query->found_posts;
}
?>

<div class="container search-page-wrapper" dir="rtl">

  <!-- Hidden nonce for AJAX search -->
  <input type="hidden" id="search-nonce" value="<?php echo esc_attr( wp_create_nonce('search_nonce') ); ?>">

  <!-- Search input field (with animated placeholder) -->
  <div class="search-input-wrapper">
    <input 
      type="text" 
      id="search-input" 
      placeholder="<?php echo get_field('search_placeholder') ? get_field('search_placeholder') : '...Start typing your search'; ?>" 
      autocomplete="off" 
    />
  </div>

  <?php if ( ! empty( $search_history ) ) : ?>
    <!-- User search history -->
    <div class="search-history"> 
      <div class="tags">
        <?php foreach ( $search_history as $query_item ) : ?>
          <button class="history-item <?php echo $search_history[0] == $query_item ? 'active' : ''; ?>">
            <?php echo esc_html( $query_item ); ?>
          </button>
        <?php endforeach; ?>
      </div>
      <div class="last-search">
          <p><?php echo get_field('recent_search_title') ? get_field('recent_search_title') : 'Recent Searches:'; ?> <?php echo esc_html( $search_history[0] ); ?></p>
      </div>
    </div>
  <?php else: ?>
    <!-- Trending section if search history is empty -->
    <div class="trending-search">
      <h3><?php echo get_field('trending_header') ? get_field('trending_header') : 'Trending:'; ?></h3>
      <div class="tags">
        <?php foreach ( $trending_tags as $tag ) : ?>
          <button class="history-item <?php echo $default_trending == $tag ? 'active' : ''; ?>">
            <?php echo esc_html( $tag ); ?>
          </button>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- Section title for results/suggestions -->
  <div class="section-title"><?php echo esc_html( $section_title ); ?></div>
  
  <!-- Block with search results summary (split into two spans) -->
  <div id="search-summary" class="search-summary" style="display: none;">
    <span class="result"><?php echo ! empty( $search_history ) ? esc_html( $search_history[0] ) : esc_html( $default_trending ); ?></span>
    <span class="count">
      <?php
        echo esc_html( $found_posts );
        echo $found_posts > 0 
          ? ( get_field('results_text') ? get_field('results_text') : 'results' ) 
          : ( get_field('no_results_text') ? get_field('no_results_text') : 'no results' );
      ?>
    </span>
  </div>
  
  <!-- Container for search results -->
  <div id="search-results" class="products">
    <?php if ( $query->have_posts() ) : ?>
      <div class="products-inner">
        <?php while ( $query->have_posts() ) : $query->the_post(); ?>
          <?php get_template_part('template-parts/products/product-item'); ?>
        <?php endwhile; ?>
      </div> 
    <?php endif; wp_reset_postdata(); ?>
  </div>
  
  <?php 
  $image_svg = get_field( 'image_svg_no_results');
  // Load the no-results template with a parameter for display status
  if ( isset( $query ) && $query->have_posts() ) {
    get_template_part('template-parts/no-results', null, array( 'status' => 'none','image' => $image_svg['url'] ));
  } else {
    get_template_part('template-parts/no-results', null, array( 'status' => 'block','image' => $image_svg['url'] ));
  }
  ?>
  
</div>

<?php get_footer(); ?>
