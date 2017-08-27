<section>

  <?php
   $number = 0;
   query_posts('post_type=attachment');
   if(have_posts()):
  ?>
  <div id="myCarousel" class="carousel slide">
    <ol class="carousel-indicators">
      <?php while(have_posts()): the_post(); ?>
      <li data-target="#myCarousel" data-slide-to="<?php echo $number++; ?>"></li>
      <?php endwhile; ?>
    </ol>

    <!-- Carousel items -->
    <div class="carousel-inner">
      <?php while(have_posts()): the_post(); ?>
      <div class="item">
        <?php the_post_thumbnail('large'); ?>
        <div class="carousel-caption">
          <h4><?php the_title(); ?></h4>
          <?php the_excerpt(); ?>
        </div>
      </div>
      <?php endwhile; ?>
    </div>

    <!-- Carousel nav -->
    <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
    <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
  </div>
  <?php endif; wp_reset_query(); ?>
    </section>
