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

  <!-- Carousel -->
<div id="promo-carousel" class="carousel slide" data-ride="carousel">



    <!-- Controls -->
  <div class="row ">
    <div class="col-12 text-center">
    <a class="left fawesome-control" href="#promo-carousel" role="button" data-slide="prev"><i class="fa fa-angle-left"></i></a>
    <a class="right fawesome-control" href="#promo-carousel" role="button" data-slide="next"><i class="fa fa-angle-right"></i></a>
    </div>
    </div>


  <!-- Wrapper for slides -->
  <div class="row">
  <div class="carousel-inner" role="listbox">

    <?php
    // Item size (set here the number of posts for each group)
    $i = 3;

    // Set the arguments for the query
    global $post;
    $args = array(
      'numberposts'   => -1, // -1 is for all
      'post_type'     => 'europians', // or 'post', 'page'
    //  'orderby'       => 'title', // or 'date', 'rand'
      'order' 	      => 'DESC', // or 'DESC'
    );

    // Get the posts
    $myposts = get_posts($args);

    // If there are posts
    if($myposts):

      // Groups the posts in groups of $i
      $chunks = array_chunk($myposts, $i);
      $html = '<div class="test"></div>';
      //echo = 'test';




      /*
       * Item
       * For each group (chunk) it generates an item
       */
      foreach($chunks as $chunk):
        // Sets as 'active' the first item
        ($chunk === reset($chunks)) ? $active = "active" : $active = "";
        $html .= '<div class="carousel-item '.$active.'"><div class="container"><div class="row">';

        /*
         * Posts inside the current Item
         * For each item it generates the posts HTML
         */
        foreach($chunk as $post):
          $html .= '<div class="col-lg-4 col-md-6 single-post">';
          $html .= '<h1 class="post-number">';
          $html .= get_field( "number" );
          $html .= '.</h1>';
          $html .= '<h3 class="post-title">';
          $html .= get_the_title($post->ID);
          $html .= '</h3>';
          $html .= '<div class="post-content">';
          $html .= get_the_excerpt($post->ID);
          $html .= '</div>';
          $html .= '</div>';
        endforeach;

        $html .= '</div></div></div>';

      endforeach;

      // Prints the HTML
      echo $html;

    endif;
    ?>
  </div>
  </div> <!-- carousel inner -->


</div> <!-- /carousel -->

</div>


          <div class="row">
      <?php $loop = new WP_Query( array( 'post_type' => 'europians', 'posts_per_page' => -1 ) ); ?>
      <?php
    //  $postNumber = 1;
      while ( $loop->have_posts() ) : $loop->the_post(); ?>
      <div class="col-lg-4 col-md-6 single-post">
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
        </section>
