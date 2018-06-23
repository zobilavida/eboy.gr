<section class="portfolio">
<div class="container-fluid p-0">
  <div class="row p-5">
    <div class="col-12 p-0">
      <?php do_action ('eboy_woocommerce_portfolio', 'eboy_woocommerce_categories', 10); ?>
</div>
</div>

    <!-- add extra container element for Masonry -->
    <div class="grid row facetwp-template">

      <?php

      $query = new WP_Query( array( 'post_type' => 'product', 'order' => 'DESC', 'posts_per_page' => -1, 'facetwp' => true,) );


      if ( $query->have_posts() ) : ?>
      <?php while ( $query->have_posts() ) : $query->the_post(); ?>
      <div class="product grid-item card col-6 col-md-4 col-lg-4 mb-3 <?php do_action( 'eboy_woocommerce_current_tags_thumb' ); ?>" data-href="<?php echo get_permalink( $post->ID ); ?>" >
        <div class="grid-item-content">
      <?php if ( has_post_thumbnail() ) {
        $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'large' );
        echo '<a href="'.get_permalink( $post->ID ).'" >';
         echo '<img width="100%" src="' . $image_src_thumbnail[0] . '" alt="eboy">';
         echo '</a>';
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
