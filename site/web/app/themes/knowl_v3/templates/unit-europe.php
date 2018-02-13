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
      <div id="myCarousel" class="carousel slide row" data-ride="carousel">
        <div class="carousel-inner col-12" role="listbox">
          <div class="row">
      <?php $loop = new WP_Query( array( 'post_type' => 'europians', 'posts_per_page' => -1 ) ); ?>
      <?php
    //  $postNumber = 1;
      while ( $loop->have_posts() ) : $loop->the_post(); ?>
      <div class="col-4 single-post">
      <h1 class="post-number"><?php $value = get_field( "number" );

if( $value ) {

    echo $value;

} else {

    echo '';

}  ?>.</h1>
      <h3 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
      <div class="post-content"><?php the_excerpt(); ?> </div>
      <?php edit_post_link(); ?>
      </div>
      <?php endwhile; wp_reset_query();?>
        </div>
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
        </section>
