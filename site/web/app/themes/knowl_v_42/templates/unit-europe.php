<section class="module" id="europe">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-12 col-sm-offset-3">



                          <?php
                              $currentlang = get_bloginfo('language');
                              if($currentlang=="el"):
                          ?>

                          <?php $page = get_page_by_title( 'Ευρωπαϊκά Έργα' ); ?>
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

                            <?php $page = get_page_by_title( 'European Projects' ); ?>
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



  <div class="row ">

  <!-- Carousel -->
<div id="promo-carousel" class="carousel slide col" data-ride="carousel">



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




          </div>
        </section>
