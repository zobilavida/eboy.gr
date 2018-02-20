<section class="module" id="alt-features">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-12 col-sm-offset-3">


          <?php
              $currentlang = get_bloginfo('language');
              if($currentlang=="el"):
          ?>

          <?php $page = get_page_by_title( 'Προφίλ' ); ?>
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

            <?php $page = get_page_by_title( 'Profile' ); ?>
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

      <div class="col-sm-6 col-md-3 col-lg-3">
        <?php $loop = new WP_Query( array( 'post_type' => 'profile', 'posts_per_page' => 2 ) ); ?>
        <?php

        while ( $loop->have_posts() ) : $loop->the_post(); ?>
        <div class="alt-features-item">
        <h3 class="alt-features-title font-alt"><?php the_title(); ?></h3>
        <div class="post-content"><?php the_excerpt(); ?> </div>
        <?php edit_post_link(); ?>
        </div>
        <?php endwhile; wp_reset_query();?>
      </div>
      <div class="col-md-6 col-lg-6 hidden-xs hidden-sm">
        <div class="alt-services-image align-center">
          <?php
  if ( has_post_thumbnail() ) {
  the_post_thumbnail();
  }  ?>

  <?php
      $currentlang = get_bloginfo('language');
      if($currentlang=="el"):
  ?>
  <button type="button" class="btn btn-lg btn-warning" >
    <div style="text-align:center;"><i class="fa fa-download"></i></div>
    <a href="http://knowl.gr/wp-content/uploads/2018/02/Knowl_Presentation_Jan2018.pdf" target="_blank">συντομη παρουσιαση της knowl</a>
  </button>
<?php elseif(get_locale() == 'en_GB'): ?>
  <button type="button" class="btn btn-lg btn-warning" >
    <div style="text-align:center;"><i class="fa fa-download"></i></div>
    <a href="http://knowl.gr/wp-content/uploads/2018/02/Knowl_Presentation_Jan2018.pdf" target="_blank">Presentation</a>
  </button>

  
  <?php endif; ?>
        </div>
      </div>
      <div class="col-sm-6 col-md-3 col-lg-3">
        <?php $loop = new WP_Query( array( 'post_type' => 'profile','offset' => '-2', 'posts_per_page' => 2 ) ); ?>
        <?php

        while ( $loop->have_posts() ) : $loop->the_post(); ?>
        <div class="alt-features-item">
        <h3 class="alt-features-title font-alt"><?php the_title(); ?></h3>
        <div class="post-content"><?php the_excerpt(); ?> </div>
        <?php edit_post_link(); ?>
        </div>
        <?php endwhile; wp_reset_query();?>
      </div>
  </div>
  </div>


</section>
