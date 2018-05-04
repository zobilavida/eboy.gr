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

<div class="container pt-5">
<div class="row">
<div class="col-7">

	<?php do_action('demetrios_product_carousel', 'product_carousel'); ?>




</div>
<div class="col-5 px-4">

	<div class="d-flex flex-row ">

<div class="pr-2"><h1><?php echo esc_html( get_the_title() ); ?></h1></div>
<div class="p-2 align-self-center"><?php do_action('demetrios_current_product_category', 'woocommerce_category_description');  ?></div>
<div class="p-2">Flex item 3</div>

	</div>
	<p class="text-product"><?php the_content( ); ?></p>
	<?php do_action('demetrios_product_attributes', 'isa_woocommerce_all_pa'); ?>
	
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
