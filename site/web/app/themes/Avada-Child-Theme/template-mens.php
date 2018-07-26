<?php
/**
 * Template Name: mens Template
 */
?>

<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?><?php get_header(); ?>
<div class="d-flex flex-row">
  <div class="p-2">Flex item 1</div>
  <div class="p-2">Flex item 2</div>
  <div class="p-2">Flex item 3</div>
</div>
<div class="row related-products-preview-wrapper">
    <?php
        $args = array( 'post_type' => 'product', 'posts_per_page' => -1, 'product_cat' => 'mens', 'orderby' => 'name', 'order' => 'ASC' );

        $loop = new WP_Query( $args );

        while ( $loop->have_posts() ) : $loop->the_post(); global $product; $id = $product->get_id(); ?>



        <div class="col-3 slick-product">
          <a href="#" class="related-product-preview" data-project-id="<?php echo $id; ?>">
          <?php //the_title(); ?>
          <?php if (has_post_thumbnail( $loop->post->ID )) echo get_the_post_thumbnail($loop->post->ID, 'thumbnail'); else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" width="300px" height="300px" />'; ?>
          </a>
        </div>


    <?php endwhile; ?>
    <?php wp_reset_query(); ?>
</div><!--/.products-->
 <?php
get_footer();?>
