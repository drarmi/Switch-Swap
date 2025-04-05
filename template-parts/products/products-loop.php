<?php
$args = array(
    'post_type'      => 'product',
    'posts_per_page' => -1,
);

$query = new WP_Query($args); 
?>

<?php if ($query->have_posts()) : ?>
    <section class="swap-products">
        <?php while ($query->have_posts()) : $query->the_post(); ?>
            <?php get_template_part('template-parts/products/product-item'); ?>
        <?php endwhile; wp_reset_postdata(); ?>
    </section>
<?php else : ?>
    <p>No products found.</p>
<?php endif; ?>