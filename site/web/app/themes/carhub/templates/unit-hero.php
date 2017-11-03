<section id="home">
  <!-- First Parallax Section -->
  <div class="jumbotron paral paralsec">
    <h1><?php printf( esc_html__( '%s', 'sage' ), get_bloginfo ( 'description' ) ); ?></h1>
    <?
    $intro = get_page_by_path('intro');
  $content = $intro->post_content;
  echo $content
  ?>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
  <div id="myCarousel" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner" role="listbox">
        <?php query_posts('post_type=product&showposts=1'); ?>
              <?php if (have_posts()) : while (have_posts()) : the_post(); global $product;?>
                <div class="carousel-item active">
                  <div class="row">
                    <div class="col-4 d-flex justify-content-end">
                      <div class="row">
                        <div class="col-6">
                        </div>
                        <div class="col-6">
                          <h6> <?php  $terms = get_the_terms( $post->ID, 'product_cat' );
                      foreach ( $terms as $term ) {
                          $product_cat_id = $term->slug;
                          echo $product_cat_id;
                          break;
                      } ?> </h6>
                      <h3><?php echo $product->get_name(); ?></h3>
                      <div class="car_slider_seperator"></div>
                      <h5>From:</h5>
                      <?php echo $product->get_price_html(); ?>
                      <button type="button" class="btn btn-primary btn-lg openform" data-href="<?php echo $product->get_name(); ?>">Reserve Now</button>
                      </div>
                      </div>
                    </div>
                    <div class="col-4">

                        <a href="<?php echo get_permalink() ?>" >
                  <?php the_post_thumbnail('', array('class' => 'd-block img-fluid mx-auto')); ?>
                  </a>
                  </div>
                  <div class="col-4">
                    <?php do_action ( 'woocommerce_attribute_doors' );  ?>
                    <?php do_action ( 'woocommerce_attribute_passengers' );  ?>
                    <?php do_action ( 'woocommerce_attribute_luggage' );  ?>
                    <?php do_action ( 'woocommerce_attribute_transmission' );  ?>
                    <?php do_action ( 'woocommerce_attribute_air_conditioning' );  ?>

                  </div>



                  </div>

                </div>
              <?php endwhile; endif; ?>
              <?php wp_reset_query(); ?>

              <?php query_posts('post_type=product&showposts=4&offset=1'); ?>
                    <?php if (have_posts()) : while (have_posts()) : the_post(); global $product;?>
                      <div class="carousel-item">
                        <div class="row">
                          <div class="col-4 d-flex justify-content-end">
                            <div class="row">
                              <div class="col-6">
                              </div>
                              <div class="col-6">
                                <h6> <?php  $terms = get_the_terms( $post->ID, 'product_cat' );
                            foreach ( $terms as $term ) {
                                $product_cat_id = $term->slug;
                                echo $product_cat_id;
                                break;
                            } ?> </h6>
                            <h3><?php echo $product->get_name(); ?></h3>
                            <div class="car_slider_seperator"></div>
                            <h5>From:</h5>
                            <?php echo $product->get_price_html(); ?>
                            <button type="button" class="btn btn-primary btn-lg openform" data-href="<?php echo $product->get_name(); ?>">Reserve Now</button>
                            </div>
                            </div>
                          </div>
                          <div class="col-4">

                              <a href="<?php echo get_permalink() ?>" >
                        <?php the_post_thumbnail('', array('class' => 'd-block img-fluid mx-auto')); ?>
                        </a>
                        </div>
                        <div class="col-4">
                          <?php do_action ( 'woocommerce_attribute_doors' );  ?>
                          <?php do_action ( 'woocommerce_attribute_passengers' );  ?>
                          <?php do_action ( 'woocommerce_attribute_luggage' );  ?>
                          <?php do_action ( 'woocommerce_attribute_transmission' );  ?>
                          <?php do_action ( 'woocommerce_attribute_air_conditioning' );  ?>

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

 </section>
