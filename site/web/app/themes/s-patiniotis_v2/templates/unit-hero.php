<section id="home" >
  <div class="jumbotron jumbotron-fluid">

  <div class="container">
  <div class="row">
    <div class="col-12">
      <div id="myCarousel" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner" role="listbox">
            <?php query_posts('category_name=featured&showposts=1'); ?>
                  <?php if (have_posts()) : while (have_posts()) : the_post();?>
                    <div class="carousel-item active">
                      <div class="row">

                        <div class="col-lg-6 col-12">
                          <h4> <?php  $terms = get_the_terms( $post->ID, 'category' );
                        foreach ( $terms as $term ) {
                          $product_cat_id = $term->slug;
                          echo $product_cat_id;
                          break;
                        } ?> </h4>
                        <h5><?php echo get_the_title( $post ); ?></h5>
                      <h5><?php echo get_the_content( $post ); ?></h5>
                      </div>

                      </div>

                    </div>
                  <?php endwhile; endif; ?>
                  <?php wp_reset_query(); ?>
                  <?php query_posts('category_name=featured&showposts=4&offset=1'); ?>
                        <?php if (have_posts()) : while (have_posts()) : the_post();?>
                          <div class="carousel-item">
                            <div class="row">

                              <div class="col-lg-6 col-12">
                                <h4> <?php  $terms = get_the_terms( $post->ID, 'category' );
                              foreach ( $terms as $term ) {
                                $product_cat_id = $term->slug;
                                echo $product_cat_id;
                                break;
                              } ?> </h4>
                              <h5><?php echo get_the_title( $post ); ?></h5>
                            <h5><?php echo get_the_content( $post ); ?></h5>
                            </div>

                            </div>

                          </div>
                        <?php endwhile; endif; ?>
                        <?php wp_reset_query(); ?>
        </div>
        <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="sr-only">Next</span>
        </a>
      </div>
    </div>
  </div>
  </div>
  	</div>







 </section>
