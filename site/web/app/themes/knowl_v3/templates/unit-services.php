<section class="module" id="services">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-6 col-sm-offset-3">
        <h2 class="module-title font-alt"><?php
        $page = get_page_by_title( 'Εκπαιδεύσεις' );

        $title = apply_filters('the_content', $page->post_title);
        $link = get_permalink( get_page_by_title( 'Εκπαιδεύσεις' ) );

        echo $title;

        ?></h2>
        <div class="module-subtitle font-serif">      <?php
        $page = get_page_by_title( 'Εκπαιδεύσεις' );
        $content = apply_filters('the_content', $page->post_excerpt);

        echo $content;
        ?></div>
      </div>
    </div>
    <div class="row ">
    <?php
    // WP_Query arguments
    $args = array(
      'post_type'              => array( 'courses' ),
    //  'terms'                  => array( 'Εκπαιδεύσεις' ),
      'post_status'            => array( 'publish' ),
    );

    // The Query
    $query = new WP_Query( $args );

    // The Loop
    if ( $query->have_posts() ) {
      while ( $query->have_posts() ) {
        $query->the_post();
        // do something
        echo '<div class="col-md-4 col-sm-6 col-xs-12 services-box">';
the_title( '<span class="text-center"><h3>', '</h3></span>' );
        $my_excerpt = get_the_excerpt();
        if ( '' != $my_excerpt ) {
            // Some string manipulation performed
        }
        if ( has_post_thumbnail() ) :
          the_post_thumbnail();

                    echo '<h4>';
                    echo $my_excerpt; // Outputs the processed value to the page
                    echo '</h4>';
          echo '</div>';
      //  echo '<div class="col-9">';
       endif;




        //    echo '</div>';


                }
        } else {
          // no posts found
        }

        // Restore original Post Data
        wp_reset_postdata();
          ?>
          </div>
        </section>
