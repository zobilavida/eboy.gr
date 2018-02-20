<section class="module" id="team">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-12 col-sm-offset-3">

        <?php
            $currentlang = get_bloginfo('language');
            if($currentlang=="el"):
        ?>

        <?php $page = get_page_by_title( 'Ποιοι είμαστε' ); ?>
        <?php $title = apply_filters('the_content', $page->post_title);
        echo '<h2 class="module-title font-alt">';
          echo $title;
          echo '</h2>';
         ?>


  <?php $excerpt = apply_filters('the_content', $page->post_excerpt);
  echo '<div class="module-subtitle font-serif">';
    echo $excerpt;
    echo '</div>';

  ?>
        <?php elseif(get_locale() == 'en_GB'): ?>

          <?php $page = get_page_by_title( 'Who we are' ); ?>
          <?php $title = apply_filters('the_content', $page->post_title);
          echo '<h2 class="module-title font-alt">';
            echo $title;
            echo '</h2>';
           ?>


    <?php $excerpt = apply_filters('the_content', $page->post_excerpt);
    echo '<div class="module-subtitle font-serif">';
      echo $excerpt;
      echo '</div>';

    ?>

        <?php endif; ?>

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
