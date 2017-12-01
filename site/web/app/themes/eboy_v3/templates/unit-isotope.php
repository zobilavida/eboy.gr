<section class="pb-5">


  <div class="container-fluid">
    <!-- add extra container element for Masonry -->
    <div class="grid row facetwp-template">

      <?php

      $query = new WP_Query( array( 'post_type' => 'product', 'posts_per_page' => 4, 'facetwp' => true,) );


      if ( $query->have_posts() ) : ?>
      <?php while ( $query->have_posts() ) : $query->the_post(); ?>
      <div class="grid_item card col-6 col-md-4 col-lg-3 mb-3" data-href="<?php echo get_permalink( $post->ID ); ?>" data-rel="<?php echo $post->ID ; ?>" >
        <div class="grid-item-content">
      <?php if ( has_post_thumbnail() ) {
        $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'thumbnail' );
        echo '<a href="#myModal" data-toggle="modal">';
         echo '<img width="100%" src="' . $image_src_thumbnail[0] . '">';
         echo '</a>';
       }

      ?>
   </div>
      </div>

<?php endwhile; wp_reset_postdata(); ?>
<?php endif; ?>

<div class="modal fade modal-center" id="myModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vertically Centered Modal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Modal body text goes here.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    </div>
  </div>


</section>
