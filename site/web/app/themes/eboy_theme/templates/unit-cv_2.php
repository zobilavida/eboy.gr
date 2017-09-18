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
<div class="content_body container-fluid no-gutters">
  <div class="row">
  <div class="col-lg-6 col-sm-12 no-gutters">
    <div class="container">
      <div class="row">
        <div class="col">
          <div class="card">
            <div class="card-block">

              <div id="carouselDocumentationIndicators" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#carouselDocumentationIndicators" data-slide-to="0" class="active"></li>
                  <li data-target="#carouselDocumentationIndicators" data-slide-to="1"></li>
                  <li data-target="#carouselDocumentationIndicators" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner in" role="listbox">


                </div>
                <a class="carousel-control-prev" href="#carouselDocumentationIndicators" role="button" data-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselDocumentationIndicators" role="button" data-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
            </div>
          </div>

        </div>
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
