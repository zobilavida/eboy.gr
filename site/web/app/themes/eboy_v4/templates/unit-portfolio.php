<section class="portfolio">
<div class="container-fluid">


    <!-- add extra container element for Masonry -->
    <div class="grid ">

      <?php

      $query = new WP_Query( array( 'post_type' => 'portfolio', 'order' => 'DESC', 'posts_per_page' => -1,) );


      if ( $query->have_posts() ) : ?>
      <?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<div class="d-flex flex-row justify-content-between product grid-item py-4 px-0 w-100 <?php do_action( 'eboy_woocommerce_current_tags_thumb' ); ?>" data-href="<?php echo get_permalink( $post->ID ); ?>">
					<div class="p-2 grid-item-content">
            <div class="d-flex flex-row">
            <div class="p-0"><?php the_title( '<h1>', '</h1>' ); ?></div>
            <div class="px-3"><?php do_action ('eboy_portfolio', 'eboy_portfolio_demo'); ?></div>

          </div>


						<?php
the_excerpt();
?>
				</div>
					<div class="p-2">
						<?php if ( has_post_thumbnail() ) {
			        $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'large' );
			      //  echo '<a href="'.get_permalink( $post->ID ).'" >';
			         echo '<img width="100%" src="' . $image_src_thumbnail[0] . '" alt="eboy">';
			      //   echo '</a>';
			       }
			      ?>
					</div>
				</div>




<?php endwhile; wp_reset_postdata(); ?>
<?php endif; ?>


    </div>
  </div>


</div>
</section>
