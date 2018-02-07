<section class="module" id="alt-features">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-6 col-sm-offset-3">
        <h2 class="module-title font-alt"><?php
        $page = get_page_by_title( 'Προφίλ' );

        $title = apply_filters('the_content', $page->post_title);
        $link = get_permalink( get_page_by_title( 'Προφίλ' ) );
        echo $title;
        ?></h2>
        <div class="module-subtitle font-serif"><?php
        $page = get_page_by_title( 'Προφίλ' );

        $excerpt = apply_filters('the_content', $page->post_excerpt);
        echo $excerpt;
        ?></div>
      </div>
    </div>
    <div class="row">

      <div class="col-sm-6 col-md-3 col-lg-3">
        <?php $loop = new WP_Query( array( 'post_type' => 'profile', 'posts_per_page' => 4 ) ); ?>
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
        </div>
      </div>
      <div class="col-sm-6 col-md-3 col-lg-3">
        <?php $loop = new WP_Query( array( 'post_type' => 'profile','offset' => '-4', 'posts_per_page' => 4 ) ); ?>
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

      <div class="row ">
        <div class="col text-center">
      <button type="button" class="btn btn-lg btn-warning" >
        <div style="text-align:center;"><i class="fa fa-download"></i></div>
          σύντομη παρουσίαση της knowl
      </button>
      </div>
      </div>

</section>
