<section class="module" id="europe">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-12 col-sm-offset-3">
        <h2 class="module-title font-alt"><?php
        $page = get_page_by_title( 'Ευρωπαϊκά Έργα' );

        $title = apply_filters('the_content', $page->post_title);
        $link = get_permalink( get_page_by_title( 'Ευρωπαϊκά Έργα' ) );
        echo $title;
        ?></h2>
        <div class="module-subtitle font-serif">      <?php
        $page = get_page_by_title( 'Ευρωπαϊκά Έργα' );
        $content = apply_filters('the_content', $page->post_excerpt);

        echo $content;
        ?></div>
      </div>
    </div>
    <div class="row ">
      <div id="myCarousel" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner" role="listbox">
            <?php query_posts('post_type=europians&showposts=1'); ?>
                  <?php if (have_posts()) : while (have_posts()) : the_post();?>
                    <div class="carousel-item active">
                      <div class="row">
                        <div class="col-lg-3 col-12">


                          <div class="row h-50">
                            <div class="col-12 col-lg-6">
                            </div>
                            <div class="col-12 col-lg-6">
                              <div class="row">
                            <div class="col-12 ">
                            <h4> </h4>
                      </div>
                      <div class="col-12">
                        <h5></h5>
                      </div>
                      <div class="col-12">
                        <div class="car_slider_seperator"></div>
                      </div>

                      <div class="col-6 col-lg-12">
                        <h4>From:</h4>
                      <h5><span class="perday">/Day</span></h5>

                      </div>
                      <div class="col-6 col-lg-12">
                        <button type="button" class="btn btn-primary btn-lg btn-block openform" data-car-choise="" data-href="">Book Now!</button>
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

                      </div>
                      <div class="container formbg">
                        <div class="row">
                          <div class="col-12">



                        </div>
                      </div>
                      </div>
                    </div>

                  <?php endwhile; endif; ?>
                  <?php wp_reset_query(); ?>

                  <?php query_posts('post_type=europians&showposts=10&offset=1'); ?>
                        <?php if (have_posts()) : while (have_posts()) : the_post();?>
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
        </section>
