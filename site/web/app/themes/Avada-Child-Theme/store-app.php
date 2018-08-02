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
	<div class="row filter">
		<div class="col-12 col-lg-6 text-right ">
			<div class="d-flex flex-row justify-content-between align-items-center">
  <div class="p-2"><?php echo facetwp_display( 'pager' ); ?></div>
  <div class="p-2"><?php echo facetwp_display( 'facet', 'stamps' ); ?></div>

</div>
		</div>
		<div class = "col-12 col-lg-1 middle-col">
</div>
<div class = "col-12 col-lg-5 ">
	</div>
	</div>
   <div class = "row row-height">
     <div class = "col-12 col-lg-6 left">

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
		   <div class="p-2 right-product">
				 <?php
				 $params = array(
				  'p' => '21057',
				  'post_type' => 'product'
				 );
				 $wc_query = new WP_Query($params);
				 ?>
				 <?php if ($wc_query->have_posts()) :  ?>
				 <?php while ($wc_query->have_posts()) : $wc_query->the_post();  ?>
				 <?php  wc_get_template_part( 'content', 'single-custom-product' );  ?>

				 <?php endwhile; ?>
				 <?php wp_reset_postdata();  ?>
				 <?php else:  ?>
				 <p><?php _e( 'No Product no' );  ?></p>
				 <?php endif; ?>
			 </div>

		 </div>
     </div>

		 <?php
		get_footer();?>
   </div>
 </div>
