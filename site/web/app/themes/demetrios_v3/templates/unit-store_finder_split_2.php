<div class = "container-fluid">
   <div class = "row row-height">
     <div class = "col-lg-6 display-map left p-0">
       <?php do_action( 'custom_store_finder', 'store_finder' ); ?>
     </div>
     <div class = "col-lg-6 display-results right">
       <?php do_action( 'custom_store_spilt_finder_2', 'store_finder_split_2' ); ?>
     </div>

     <?php get_template_part('templates/footer'); ?>
   </div>
 </div>
