<?php
/**
 * Template Name: Store App
 * Template for store app.
 *
 * @package Avada
 * @subpackage Templates
 */

?>

<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?><?php get_header(); ?>
<div class = "container-fluid">
   <div class = "row row-height">
     <div class = "col-12 col-lg-5 left">
			 <div class="row">
				 <div class="col-12 text-right">
<?php echo facetwp_display( 'facet', 'stamps' ); ?>
				 </div>

			 </div>
         <div class ="row facetwp-template">

       <?php do_action( 'tshirtakias_stamps' ); ?>

       </div>
			 <input name="stamp_url" value="" class="stamp_url"/>

     </div>
     <div class = "col-12 col-lg-1 middle-col">

       <div class="row">
      <?php do_action('tshirtakias_product_category_images' ); ?>
      </div>

     </div>
     <div class = "col-12 col-lg-5 right">
			 <div class="d-flex flex-column">
		   <div class="p-2 right-product">Flex item 1</div>
		   <div class="right-category">Flex item 2</div>
		 </div>
     </div>


   </div>
 </div>
 <?php
get_footer();?>
