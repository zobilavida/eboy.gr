<?php
/**
 * Template Name: Custom Template
 */
?>
<?php if (is_front_page()) { ?>
<?php do_action( 'demetrios_butt_book', 'button_book' ); ?>
<?php }
else { ?>
<?php do_action( 'demetrios_butt_book', 'button_book' ); ?>
    <div class="container-fluid back-grey500 top-page">
    <div class="row">
      <div class="col-12">
        <div class="container py-4">
          <div class="row align-items-center h-100">
            <div class="col-lg-6">
            <h1><?php echo esc_html( get_the_title() ); ?></h1>
           </div>
           <div class="col-lg-6 text-right">
             <?php if (function_exists('the_breadcrumb')) the_breadcrumb(); ?>
          </div>
      </div>
      </div>
      </div>
      </div>
      </div>
<?php
} ?>
    <div class="container-fluid">
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
