<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product;
$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$thumbnail_size    = apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' );
$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, $thumbnail_size );
$placeholder       = has_post_thumbnail() ? 'with-images' : 'without-images';
$wrapper_classes   = apply_filters( 'woocommerce_single_product_image_gallery_classes', array(
	'woocommerce-product-gallery',
	'woocommerce-product-gallery--' . $placeholder,
	'woocommerce-product-gallery--columns-' . absint( $columns ),
	'images',
) );
$next_arrow		= '<img class="ico" src=" ' .get_template_directory_uri() .'/dist/images/button_icon_2.svg">';

?>
<div class="container">
	<a class="carousel-control-prev" href="#postsCarousel" role="button" data-slide="prev">
		<img class="" src="<?= get_template_directory_uri(); ?>/dist/images/ico_previous_01.svg">
		<span class="sr-only">Previous</span>
	</a>
	<a class="carousel-control-next" href="#postsCarousel" role="button" data-slide="next">
		<img class="" src="<?= get_template_directory_uri(); ?>/dist/images/ico_next_01.svg">
		<span class="sr-only">Next</span>
	</a>


	<div class="row">
<div class="col-lg-8 col-sm-12 mx-auto">
	 <div class="carousel slide w-100 ml-auto mr-auto" data-ride="carousel" id="postsCarousel">
		 <div class="carousel-inner" role="listbox">
			<?php
			 $args = array( 'post_type' => 'attachment', 'numberposts' => 1, 'post_mime_type' => 'image', 'post_status' => 'inherit', 'post_parent' => $post->ID );
			 $attachments = get_posts($args);
			 if ($attachments) {
							 foreach ( $attachments as $attachment ) {
											 // Method #1: Allows you to define the image size
											 $src = wp_get_attachment_image_src( $attachment->ID, "large");
											 if ($src) {
												 echo '<div class="carousel-item active">';
												 //echo '<div class="col-md-12">';
												 echo '<img class="d-block w-100" src="' . $src[0] . '" alt="">';
												 //echo $src[0];
												 echo '</div>';
											 }
											 // Method #2: Would always return the "attached-image" size
											 //echo $attachment->guid;
							 }
			 } ?>
			 <?php
				$args = array( 'post_type' => 'attachment', 'offset' => 1, 'post_mime_type' => 'image', 'post_status' => 'inherit', 'post_parent' => $post->ID );
				$attachments = get_posts($args);
				if ($attachments) {
								foreach ( $attachments as $attachment ) {
												// Method #1: Allows you to define the image size
												$src = wp_get_attachment_image_src( $attachment->ID, "full-size");
												if ($src) {
													echo '<div class="carousel-item">';
												//  echo '<div class="col-md-12">';

													echo '<img class="d-block w-100" src="' . $src[0] . '" alt="">';
													//echo $src[0];
													echo '</div>';

												}
												// Method #2: Would always return the "attached-image" size
												//echo $attachment->guid;
								}
				}
				?>
		 </div>

	 </div>

	</div>
</div>
</div>
