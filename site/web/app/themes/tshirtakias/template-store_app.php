<?php
/**
 * Template Name: Store App Template
 */
?>


<div class = "container-fluid">
   <div class = "row row-height">
     <div class = "col-lg-6 display-map left p-0">
       <a class="btn btn-primary project-preview" href="#" data-project-id="8" role="button">Link</a>
       <?php //do_action( 'custom_store_finder', 'store_finder' ); ?>
     </div>
     <div class = "col-lg-6 display-results right">
       Right<?php //do_action('tshirtakias', 'tshirtakias_front_product'); ?>
     </div>

     <?php get_template_part('templates/footer'); ?>
   </div>
 </div>
