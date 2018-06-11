<?php
/**
 * Booking product add to cart
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<noscript><?php _e( 'Your browser must support JavaScript in order to make a booking.', 'woocommerce-bookings' ); ?></noscript>

<form class="cart container" method="post" enctype='multipart/form-data'>


	<div id="wc-bookings-booking-form" class="wc-bookings-booking-form row test" style="">

		<?php // do_action( 'woocommerce_before_booking_form' ); ?>
			<div class="wc-bookings-booking-cost col-12" style=""></div>

		<?php $booking_form->output(); ?>



		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	</div>
	<div class="container" style="">
	<div class="row" style="">
		<div class="col-12" style="">

	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : $product->id ); ?>" class="wc-booking-product-id" />

	<button type="submit" class="btn btn-primary wc-bookings-booking-form-button single_add_to_cart_button button alt disabled" style="display:none"><?php echo $product->single_add_to_cart_text(); ?></button>

</div>
	</div>
	</div>
<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

</form>

<?php //do_action( 'woocommerce_after_add_to_cart_form' ); ?>
