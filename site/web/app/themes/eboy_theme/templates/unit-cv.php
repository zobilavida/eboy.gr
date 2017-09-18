<section id="portfolio" >

  <div id="filters">
    <input type="checkbox" name="web" value=".web" id="web"><label for="web">web</label>
    <input type="checkbox" name="print" value=".print" id="print"><label for="print">blue</label>
  </div>

  <div class="container-fluid grid">

    <div class="grid-sizer"></div>
      <div class="gutter-sizer"></div>
  <?php

$query = new WP_Query( array( 'post_type' => 'portfolio', 'posts_per_page' => 100, 'facetwp' => true,) );


if ( $query->have_posts() ) : ?>
<?php while ( $query->have_posts() ) : $query->the_post();






?>
  <div class="grid_item card >" data-href="<?php echo get_permalink( $post->ID ); ?>" data-rel="<?php echo $post->ID ; ?>" >
    <div class="content_small">
<?php if ( has_post_thumbnail() ) {
    $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'thumbnail' );
     echo '<img width="100%" src="' . $image_src_thumbnail[0] . '">';

}
?>
</div>
<div class="content_body container-fluid">
  <div class="row">
  <div class="col-lg-6 col-sm-12">
    <div class="row">
        <div class="col-12">
    <a class="btn btn-outline-secondary prev" href="" title="go back"><i class="fa fa-lg fa-chevron-left"></i></a>
    <a class="btn btn-outline-secondary next" href="" title="more"><i class="fa fa-lg fa-chevron-right"></i></a>
    </div>
    </div>
     <div class="carousel slide" data-ride="carousel" id="postsCarousel">
       <div class="carousel-inner">



        <?php global $post;
         $args = array( 'post_type' => 'attachment', 'numberposts' => 1, 'post_mime_type' => 'image', 'post_status' => 'inherit', 'post_parent' => $post->ID );
         $attachments = get_posts($args);
         if ($attachments) {
                 foreach ( $attachments as $attachment ) {
                         // Method #1: Allows you to define the image size
                         $src = wp_get_attachment_image_src( $attachment->ID, "full-size");
                         if ($src) {
                           echo '<div class="carousel-item active">';
                           //echo '<div class="col-md-12">';
                           echo '<div class="card">';
                           echo '<div class="card-img-top card-img-top-250">';
                           echo '<img class="img-fluid" src="' . $src[0] . '" alt="">';
                           //echo $src[0];
                           echo '</div>';


                           echo '</div></div>';

                         }
                         // Method #2: Would always return the "attached-image" size
                         //echo $attachment->guid;
                 }
         } ?>
         <?php

          $args = array( 'post_type' => 'attachment', 'offset' => 1, 'post_mime_type' => 'image', 'post_status' => 'inherit', 'post_parent' => $post->ID );
          $attachments = get_posts($args);
          if ($attachments) {
                  foreach ( $attachments as $attachment ) {
                          // Method #1: Allows you to define the image size
                          $src = wp_get_attachment_image_src( $attachment->ID, "full-size");
                          if ($src) {
                            echo '<div class="carousel-item">';
                          //  echo '<div class="col-md-12">';
                            echo '<div class="card">';
                            echo '<div class="card-img-top card-img-top-250">';
                            echo '<img class="img-fluid" src="' . $src[0] . '" alt="">';
                            //echo $src[0];
                            echo '</div>';


                            echo '</div></div>';
                          }
                          // Method #2: Would always return the "attached-image" size
                          //echo $attachment->guid;
                  }
          }
          ?>
       </div>
     </div>

    </div>
    <div class="col-lg-6 col-sm-12 content_text">

      </div>

</div>
</div>


</div>


<?php endwhile; wp_reset_postdata(); ?>
<!-- show pagination here -->
<?php else : ?>
<!-- show 404 error here -->
<?php endif; ?>
</div>
</section>
