<section class="module" id="team">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-6 col-sm-offset-3">
        <h2 class="module-title font-alt"><?php
        $page = get_page_by_title( 'Ποιοι είμαστε' );

        $title = apply_filters('the_content', $page->post_title);
        echo $title;
        ?></h2>
        <div class="module-subtitle font-serif">
          <?php
          $page = get_page_by_title( 'Ποιοι είμαστε' );

          $excerpt = apply_filters('the_content', $page->post_excerpt);
          echo $excerpt;
          ?>
        </div>
      </div>
    </div>


    <div class="row">
      <?php $loop = new WP_Query( array( 'post_type' => 'team', 'posts_per_page' => -1 ) ); ?>
      <?php
    //  $postNumber = 1;
      while ( $loop->have_posts() ) : $loop->the_post(); ?>
      <div class="mb-sm-20 wow fadeInUp col-sm-6 col-md-3" onclick="wow fadeInUp">
        <div class="team-item">
          <div class="team-image rounded-circle">
            <?php         if ( has_post_thumbnail() ) :
                      the_post_thumbnail();

                   endif; ?>
            <div class="team-detail">
              <h5 class="font-alt">Hi all</h5>
              <p class="font-serif">Lorem ipsum dolor sit amet, consectetur adipiscing elit lacus, a&amp;nbsp;iaculis diam.</p>
              <div class="team-social"><a href="#"><i class="fa fa-facebook"></i></a><a href="#"><i class="fa fa-twitter"></i></a><a href="#"><i class="fa fa-dribbble"></i></a><a href="#"><i class="fa fa-skype"></i></a></div>
            </div>

        </div>


      <div class="team-descr font-alt">
        <div class="team-name"><?php the_title(); ?></div>
        <div class="team-role"><?php the_excerpt(); ?> </div>
      </div>
        <?php edit_post_link(); ?>
      </div>
      </div>
      <?php endwhile; wp_reset_query();?>

    </div>
  </div>
</section>
