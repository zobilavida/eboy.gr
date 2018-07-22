<?php
/**
 * Template Name: womens Template
 */
?>

<div class="d-flex flex-wrap related-products-preview">
    <?php
        $args = array( 'post_type' => 'product', 'posts_per_page' => -1, 'product_cat' => 'womens', 'orderby' => 'name', 'order' => 'ASC' );

        $loop = new WP_Query( $args );

        while ( $loop->have_posts() ) : $loop->the_post(); global $product; $id = $product->get_id(); ?>



        <a href="#" class="product-preview" data-project-id="<?php echo $id; ?>">
          <div class="p-2">
            <?php //the_title(); ?>
            <?php if (has_post_thumbnail( $loop->post->ID )) echo get_the_post_thumbnail($loop->post->ID, 'shop_catalog'); else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" width="300px" height="300px" />'; ?>
          </div>
        </a>



    <?php endwhile; ?>
    <?php wp_reset_query(); ?>
</div><!--/.products-->
