<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php
	/**
	 * woocommerce_before_single_product hook.
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div class="container">
<div class="row">
<div class="col-7">

	<?php do_action('demetrios_product_carousel', 'product_carousel'); ?>




</div>
<div class="col-5">
	<?php echo esc_html( get_the_title() ); ?>
	<?php the_content( ); ?>
	<?php do_action('demetrios_product_attributes', 'isa_woocommerce_all_pa'); ?>
<?php //the_post_thumbnail_url( $size ); ?>
<?php // echo get_the_post_thumbnail_url( $post->ID, $image_size ); ?>
<?php
    global $product;

    $attachment_ids = $product->get_gallery_attachment_ids();

    foreach( $attachment_ids as $attachment_id ) {
        echo wp_get_attachment_image($attachment_id, 'shop_thumbnail');
    }
?>





</div>

</div>
	</div><!-- .summary -->

	<?php
		/**
		 * woocommerce_after_single_product_summary hook.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>




<?php do_action( 'woocommerce_after_single_product' ); ?>
