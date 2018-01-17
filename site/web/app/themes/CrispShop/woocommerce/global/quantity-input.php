<?php
/**
 * Product quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
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
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="quantity">
	<button class="minus" type="button"></button>
	<input type="number" step="<?php echo esc_attr( $step ); ?>" min="<?php echo esc_attr( $min_value ); ?>" max="<?php echo esc_attr( $max_value ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'woocommerce' ) ?>" class="input-text qty text" size="4" pattern="<?php echo esc_attr( $pattern ); ?>" inputmode="<?php echo esc_attr( $inputmode ); ?>" />
	<button class="plus" type="button"></button>
</div>

<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery('.quantity').on('click', '.plus', function(e) {
        jQueryinput = jQuery(this).parent().find('input.qty');
        var val = parseInt(jQueryinput.val());
        jQueryinput.val( val+1 ).change();
    });

    jQuery('.quantity').on('click', '.minus', function(e) {
        jQueryinput = jQuery(this).parent().find('input.qty');
        var val = parseInt(jQueryinput.val());
        if (val !== 1) {
            jQueryinput.val( val-1 ).change();
        } 
    });
});
</script>
