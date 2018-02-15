<section class="hero_2" id="home_2">



<div class="container-fluid">
      <div class="row justify-content-center d-lg-none home_2 p-5">

          <?php
          // WP_Query arguments
          $args = array(
            'post_type'              => array( 'services' ),
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
              echo '<div class="col-lg-4 py-5">';
                echo '<div class="row">';
                echo '<div class="col-md-3 text-center pb-4">';
              if ( has_post_thumbnail() ) :
                the_post_thumbnail();
                echo '</div>';
                echo '<div class="col-md-9">';
             endif;

                the_title( '<h3>', '</h3>' );
                $my_excerpt = get_the_excerpt();
                if ( '' != $my_excerpt ) {
                    // Some string manipulation performed
                }


                echo '<h4>';
                echo $my_excerpt; // Outputs the processed value to the page
                echo '</h4>';
                  echo '</div>';
                    echo '</div>';
                    echo '</div>';

                      }
              } else {
                // no posts found
              }

              // Restore original Post Data
              wp_reset_postdata();
                ?>
        </div>


</div>


</section>
