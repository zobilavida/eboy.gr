<?php query_posts('post_type=product&showposts=1'); ?>
      <?php if (have_posts()) : while (have_posts()) : the_post(); global $product;?>

      <?php do_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart' ); ?>


                    <?php endwhile; endif; ?>
                    <?php wp_reset_query(); ?>
