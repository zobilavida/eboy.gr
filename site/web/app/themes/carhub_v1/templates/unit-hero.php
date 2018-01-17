<section id="home">
  <!-- First Parallax Section -->
  <div class="jumbotron paral paralsec">
    <h2><?php printf( esc_html__( '%s', 'sage' ), get_bloginfo ( 'description' ) ); ?></h2>
    <?
    $intro = get_page_by_path('intro');
  $content = $intro->post_content;
  echo '<h3>';
  echo $content;
  echo '</h3>';
  ?>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12 p-0">
  <div id="myCarousel" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner" role="listbox">
        <?php query_posts('post_type=product&showposts=1'); ?>
              <?php if (have_posts()) : while (have_posts()) : the_post(); global $product;?>
                <div class="carousel-item active">
                  <div class="row">
                    <div class="col-lg-3 col-12">


                      <div class="row h-50">
                        <div class="col-12 col-lg-6">
                        </div>
                        <div class="col-12 col-lg-6">
                          <div class="row">
                        <div class="col-12 ">
                        <h4> <?php  $terms = get_the_terms( $post->ID, 'product_cat' );
                    foreach ( $terms as $term ) {
                        $product_cat_id = $term->slug;
                        echo $product_cat_id;
                        break;
                    } ?> </h4>
                  </div>
                  <div class="col-12">
                    <h5><?php echo $product->get_name(); ?></h5>
                  </div>
                  <div class="col-12">
                    <div class="car_slider_seperator"></div>
                  </div>

                  <div class="col-6 col-lg-12">
                    <h4>From:</h4>
                  <h5><?php echo $product->get_price_html(); ?><span class="perday">/Day</span></h5>

                  </div>
                  <div class="col-6 col-lg-12">
                    <button type="button" class="btn btn-primary btn-lg btn-block openform" data-car-choise="<?php echo $product->get_name(); ?>" data-href="<?php echo $product->get_permalink(); ?>">Book Now!</button>
                  </div>
                    </div>
                      </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">

                        <a href="#" >
                  <?php the_post_thumbnail('', array('class' => 'd-block img-fluid mx-auto slider-image')); ?>
                  </a>
                  </div>
                  <div class="col-lg-3 col-12">
                    <div class="row attribute_row">
                    <div class="col-6 col-lg-12">
                    <?php do_action ( 'woocommerce_attribute_doors' );  ?>
                  </div>
                    <div class="col-6 col-lg-12">
                    <?php do_action ( 'woocommerce_attribute_passengers' );  ?>
                      </div>
                      <div class="col-6 col-lg-12">
                    <?php do_action ( 'woocommerce_attribute_luggage' );  ?>
                      </div>
                      <div class="col-6 col-lg-12">
                    <?php do_action ( 'woocommerce_attribute_air_conditioning' );  ?>
                      </div>
                      <div class="col-6 col-lg-12">
                    <?php do_action ( 'woocommerce_attribute_tansmission' );  ?>
                      </div>
                  </div>


                    </div>
                  </div>
                  <div class="container formbg">
                    <div class="row">
                      <div class="col-12">
<?php do_action( 'woocommerce_single_product_summary' ); ?>


                    </div>
                  </div>
                  </div>
                </div>

              <?php endwhile; endif; ?>
              <?php wp_reset_query(); ?>

              <?php query_posts('post_type=product&showposts=10&offset=1'); ?>
                    <?php if (have_posts()) : while (have_posts()) : the_post(); global $product;?>
                      <div class="carousel-item">
                        <div class="row">
                          <div class="col-lg-3 col-12">


                              <div class="row h-50">
                                <div class="col-12 col-lg-6">
                                </div>
                                <div class="col-12 col-lg-6">
                                  <div class="row">
                                <div class="col-12 ">
                                <h4> <?php  $terms = get_the_terms( $post->ID, 'product_cat' );
                            foreach ( $terms as $term ) {
                                $product_cat_id = $term->slug;
                                echo $product_cat_id;
                                break;
                            } ?> </h4>
                          </div>
                          <div class="col-12">
                            <h5><?php echo $product->get_name(); ?></h5>
                          </div>
                          <div class="col-12">
                            <div class="car_slider_seperator"></div>
                          </div>

                          <div class="col-6 col-lg-12">
                              <h4>From:</h4>
                              <h5><?php echo $product->get_price_html(); ?><span class="perday">/Day</span></h5>
                          </div>
                          <div class="col-6 col-lg-12">
                            <button type="button" class="btn btn-primary btn-lg btn-block openform" data-car-choise="<?php echo $product->get_name(); ?>">Book Now!</button>
                          </div>
                            </div>
                              </div>
                                </div>
                          </div>
                          <div class="col-lg-6 col-12">

                              <a href="#" >
                        <?php the_post_thumbnail('', array('class' => 'd-block img-fluid mx-auto slider-image')); ?>
                        </a>
                        </div>
                        <div class="col-lg-3 col-12">
                          <div class="row attribute_row">
                          <div class="col-6 col-lg-12">
                          <?php do_action ( 'woocommerce_attribute_doors' );  ?>
                        </div>
                          <div class="col-6 col-lg-12">
                          <?php do_action ( 'woocommerce_attribute_passengers' );  ?>
                            </div>
                            <div class="col-6 col-lg-12">
                          <?php do_action ( 'woocommerce_attribute_luggage' );  ?>
                            </div>
                            <div class="col-6 col-lg-12">
                          <?php do_action ( 'woocommerce_attribute_air_conditioning' );  ?>
                            </div>
                            <div class="col-6 col-lg-12">
                          <?php do_action ( 'woocommerce_attribute_tansmission' );  ?>
                            </div>
                        </div>


                          </div>
                        </div>
                        <div class="container">
                          <div class="row">
                            <div class="col-12">
                        <?php do_action( 'woocommerce_single_product_summary' ); ?>
                          </div>
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
