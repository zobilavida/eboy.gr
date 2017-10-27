<section id="home">
  <!-- First Parallax Section -->
  <div class="jumbotron paral paralsec">
    <header class="banner">
      <div class="container-fluid">
        <div class="row">
          <div class="col-4 d-flex justify-content-end">
        <nav class="nav-primary">
          test
          <?php
              wp_nav_menu( array(
                  'menu'              => 'primary',
                  'theme_location'    => 'primary',
                  'depth'             => 2,
                  'container'         => 'div',
                  'container_class'   => 'nav justify-content-center top-menu mr-auto',
                  'container_id'      => '',
                  'menu_class'        => 'bar top-menu',
                  'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
                  'walker'            => new wp_bootstrap_navwalker())
              );
          ?>
        </nav>
          </div>
          <div class="col-4 d-flex justify-content-center">
            <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>

            </div>
            <div class="col-4 d-flex justify-content-start">
              test
            </div>
          </div>
      </div>
    </header>

    <h1><?php printf( esc_html__( '%s', 'sage' ), get_bloginfo ( 'description' ) ); ?></h1>
    <?
    $intro = get_page_by_path('intro');
  $content = $intro->post_content;
  echo $content
  ?>
  <div class="container-fluid car_slider">
    <div class="row">
      <div class="col-12">
  <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner" role="listbox">
        <?php query_posts('post_type=product&showposts=1'); ?>
              <?php if (have_posts()) : while (have_posts()) : the_post(); global $product;?>
                <div class="carousel-item active">
                  <div class="row">
                    <div class="col-2 d-flex justify-content-end">
                      test left

                    </div>
                    <div class="col-8 carinslider">
                      <div class="row">
                      <div class="col-2 d-flex justify-content-end">
                          <div class="row">
                    <div class="col-12">
                      <h3><?php echo $product->get_name(); ?></h3>
                      <div class="col-12 car_slider_seperator"></div>
                    <h5>from:</h5>
                         <?php echo $product->get_price_html(); ?>
                         <button type="button" class="btn btn-danger btn-lg btn-block">Reserve Now</button>
                    </div>


                    </div>
                      </div>
                        <div class="col-8">
                        <a href="<?php echo get_permalink() ?>" >
                  <?php the_post_thumbnail('', array('class' => 'd-block img-fluid mx-auto')); ?>
                  </a>
                  </div>
                  <div class="col-2 d-flex justify-content-end">
                    <?php echo $product->get_price_html(); ?>
                  </div>
                  </div>
                  <div class="col-2 d-flex justify-content-start">
                    test

                  </div>
                  </div>
                  </div>
                </div>
              <?php endwhile; endif; ?>
              <?php wp_reset_query(); ?>

              <?php query_posts('post_type=product&showposts=4&offset=1'); ?>
                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                      <div class="carousel-item">
                        <div class="row">
                          <div class="col-2 d-flex justify-content-end">
                            test left

                          </div>
                          <div class="col-8 carinslider">
                            <div class="row">
                            <div class="col-2 d-flex justify-content-end"> <?php
                            $terms = get_the_terms( $post->ID, 'product_cat' );
                            foreach ( $terms as $term ) {
                                $product_cat_id = $term->slug;
                                echo $product_cat_id;
                                break;
                            }
                            ?>
                            <h3><?php echo $product->get_name(); ?></h3>
                            </div>
                              <div class="col-8">
                              <a href="<?php echo get_permalink() ?>" >
                        <?php the_post_thumbnail('', array('class' => 'd-block img-fluid mx-auto')); ?>
                        </a>
                        </div>
                        <div class="col-2 d-flex justify-content-end">
                          <?php echo $product->get_price_html(); ?>
                        </div>
                        </div>
                        <div class="col-2 d-flex justify-content-start">
                          test

                        </div>
                        </div>
                        </div>
                      </div>
                    <?php endwhile; endif; ?>
                    <?php wp_reset_query(); ?>


    </div>
    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>
  </div>
    </div>
  </div>
    </div>

 </section>
