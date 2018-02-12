<section class="module pb-0" id="works">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-12 col-sm-offset-3">
        <h2 class="module-title font-alt"><?php
        $page = get_page_by_title( 'Συνεργασίες' );

        $title = apply_filters('the_content', $page->post_title);
        $link = get_permalink( get_page_by_title( 'Συνεργασίες' ) );
        echo $title;
        ?></h2>
        <div class="module-subtitle font-serif"></div>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row">



    </div>
  </div>
  <ul class="works-grid works-grid-gut works-grid-5 works-hover-w" id="works-grid">

    <?php

    $query = new WP_Query( array( 'post_type' => 'portfolio', 'order' => 'ASC', 'posts_per_page' => 12,) );


    if ( $query->have_posts() ) : ?>
    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
      <?php $post_slug = get_post_field( 'post_name', get_post() ); ?>
    <li class="work-item <?php

    $post_slug = get_post_field( 'post_name', get_post() );

    echo $category->slug;


    ?>" data-href="<?php echo get_permalink( $post->ID ); ?>" data-rel="<?php echo $post->ID ; ?>" >
      <div class="work-image">
    <?php if ( has_post_thumbnail() ) {
      $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'full' );
      echo '<a href="'.get_permalink( $post->ID ).'" >';
       echo '<img width="100%" src="' . $image_src_thumbnail[0] . '" alt="Portfolio Item">';
       echo '</a>';
     }

    ?>
  </div>
     </li>
  <?php endwhile; wp_reset_postdata(); ?>
  <?php endif; ?>





  </ul>
</section>
