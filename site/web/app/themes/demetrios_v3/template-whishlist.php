<?php
/**
 * Template Name: Whishlist
 */
?>
<?php do_action( 'demetrios_butt_book', 'button_book' ); ?>
<?php do_action ('demetrios_pages_header', 'demetrios_pages_header_custom_1', 10, 10 ); ?>

    <div class="container p-0">
      <div class="row">
        <div class="col-12">
  <?php
     // TO SHOW THE PAGE CONTENTS
     while ( have_posts() ) : the_post(); ?> <!--Because the_content() works only inside a WP Loop -->
         <div class="entry-content-page">
             <?php the_content(); ?> <!-- Page Content -->
         </div><!-- .entry-content-page -->

     <?php
     endwhile; //resetting the page loop
     wp_reset_query(); //resetting the page query
     ?>
     </div>
   </div>
   </div>
