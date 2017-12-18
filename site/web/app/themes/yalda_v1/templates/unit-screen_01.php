<section class="screen_01">
<div class="container-fluid ">
<div class="row">
  <div class="col-3 leftside">

</div>
<div class="col-13">
  <div id="carouselExampleIndicators" class="carousel" data-ride="carousel">

  <div class="carousel-inner" role="listbox">
      <?php query_posts('post_type=product&showposts=1'); ?>
            <?php if (have_posts()) : while (have_posts()) : the_post(); global $product;?>
                <div class="carousel-item active">
                  <div class="container-fluid">
                    <div class="row">
                      <div class="col-6">
                        <?php the_post_thumbnail('', array('class' => 'd-block img-fluid mx-auto slider-image')); ?>


                      </div>
                      <div class="col-10">
<h5><?php echo $product->get_name(); ?></h5>

                      </div>
                    </div>
                  </div>
                </div>
              <?php endwhile; endif; ?>
              <?php wp_reset_query(); ?>

              <?php query_posts('post_type=product&showposts=10&offset=1'); ?>
                    <?php if (have_posts()) : while (have_posts()) : the_post(); global $product;?>
                        <div class="carousel-item">
                          <div class="container-fluid">
                            <div class="row">
                              <div class="col-6">
                                <?php the_post_thumbnail('', array('class' => 'd-block img-fluid mx-auto slider-image')); ?>


                              </div>
                              <div class="col-10">
        <h5><?php echo $product->get_name(); ?></h5>


                              </div>
                            </div>
                          </div>
                        </div>
                      <?php endwhile; endif; ?>
                      <?php wp_reset_query(); ?>
                      <?php $args = array(
                      'post_type'        => 'product',
                      'posts_per_page'   => 5,
                      'category'         => '',
                      );
                      $query = new WP_Query( $args );
                      if ( $query->have_posts() ) {
                      while ( $query->have_posts() ) {
                      $query->the_post();
                      echo the_post_thumbnail( 'medium' );
                      } // end while
                      } // end if
                      wp_reset_query();
                      ?>

    </div>

    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
    </div>
</div>

</div>
</div>
</section>
