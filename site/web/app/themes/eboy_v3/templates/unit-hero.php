  <section id="top" class="bg-white top mt-5">
    <div class="header-content container py-5">
        <div class="row">
            <div class="col-xl-8 col-lg-8 col-md-6 py-3">
              <div id="myCarousel" class="carousel" data-ride="carousel">
                <div class="carousel-inner" role="listbox">
                      <?php query_posts('post_type=post&showposts=1'); ?>
                          <?php if (have_posts()) : while (have_posts()) : the_post(); global $post;?>
                            <div class="carousel-item active">
                                      <span><?php echo the_title(); ?></span>
                                      <h3><?php echo the_content(); ?></h3>
                            </div>
                          <?php endwhile; endif; ?>
                          <?php wp_reset_query(); ?>
                          <?php query_posts('post_type=post&showposts=4&offset=1'); ?>
                              <?php if (have_posts()) : while (have_posts()) : the_post(); global $post;?>
                                <div class="carousel-item">
                                          <span><?php echo the_title(); ?></span>
                                          <h3><?php echo the_content(); ?></h3>
                                </div>
                              <?php endwhile; endif; ?>
                              <?php wp_reset_query(); ?>
                </div>
              </div>
            </div>
            <div class="col-xl-4 ml-xl-auto col-lg-4 ml-lg-auto col-md-5 ml-md-auto py-3">
                <blockquote class="blockquote">
                    <h5 class="text-right pt-3">Wow, yeaahh man<br>you <em>are</em> really<br>a hipster nerd</h5>
                    <br>
                    <h6 class="text-right"><a href="" class="text-muted link small">We made it easy</a></h6>
                </blockquote>
            </div>
        </div>
    </div>
</section>
