<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php get_header(); ?>
<div class = "container-fluid">
   <div class = "row row-height">
     <div class = "col-lg-5 left">

         <div class ="row facetwp-template">

       <?php do_action( 'tshirtakias_stamps' ); ?>

       </div>
			 <input name="stamp_url" value="" class="stamp_url"/>

     </div>
     <div class = "col-2">

       <div class="d-flex flex-wrap">
      <?php do_action('tshirtakias_product_category_images' ); ?>
      </div>

     </div>
     <div class = "col-lg-5 right">
			 <div class="d-flex flex-column">
		   <div class="p-2 right-product">Flex item 1</div>
		   <div class="p-2 right-category">Flex item 2</div>
		 </div>
     </div>

		 <?php
	 	get_footer();?>
   </div>
 </div>
