<section class="home-section bg-dark-30" id="home" data-background="<?= get_template_directory_uri(); ?>/assets/images/section-5.jpg">
  <div class="video-player" data-property="{videoURL:'https://www.youtube.com/watch?v=yOSL9L0efhQ', containment:'.home-section', startAt:0, mute:false, autoPlay:true, loop:true, opacity:0.15, showControls:false, showYTLogo:false, vol:25}"></div>

<div class="container h-100">
  <div class="video-controls-box">
    <div class="container">
          <div class="row">

              <?php
              // WP_Query arguments
              $args = array(
              	'post_type'              => array( 'services' ),
              	'post_status'            => array( 'published' ),
              );

              // The Query
              $query = new WP_Query( $args );

              // The Loop
              if ( $query->have_posts() ) {
              	while ( $query->have_posts() ) {
              		$query->the_post();
              		// do something
                  echo '<div class="col-4">';
                    echo '<div class="row">';
                    echo '<div class="col-3">';
                  if ( has_post_thumbnail() ) :
                    the_post_thumbnail();
                    echo '</div>';
                    echo '<div class="col-9">';
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
  </div>
  <div class="titan-caption">
    <div class="caption-content">

      <?php
$page = get_page_by_title( 'Home' );
$content = apply_filters('the_content', $page->post_content);
echo $content;
?>

    </div>
  </div>
  </div>
</section>
