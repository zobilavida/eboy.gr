<?php
/**
 * The template for displaying the booking form and calendar to customers.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce-bookings/booking-form/date-picker.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/bookings-templates/
 * @author  Automattic
 * @version 1.10.8
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wp_enqueue_script( 'wc-bookings-date-picker' );
extract( $field );

$month_before_day = strpos( __( 'F j, Y' ), 'F' ) < strpos( __( 'F j, Y' ), 'j' );
?>
<fieldset class="form-group row p-0 wc-bookings-date-picker wc-bookings-date-picker-<?php echo esc_attr( $product_type ); ?> <?php echo implode( ' ', $class ); ?>">
	<div class="container p-0">

<div class="row">
	<div class="col-6 p-0 input-group wc-bookings-date-picker-date-fields">


		<?php
		// woocommerce_bookings_mdy_format filter to choose between month/day/year and day/month/year format
		if ( $month_before_day && apply_filters( 'woocommerce_bookings_mdy_format', true ) ) :
		?>

		<div class="input-group addon w-100">
			<span class="input-group-addon label" id="basic-addon3"><i class="fa fa-2x fa-calendar-o">
<?php echo esc_html( apply_filters( 'woocommerce_bookings_date_picker_start_label', __( 'Start', 'woocommerce-bookings' ) ) ); ?></i></span>
			<input type="text" name="<?php echo $name; ?>_day" placeholder="<?php _e( 'dd', 'woocommerce-bookings' ); ?>" size="2" class="form-control booking_date_day" />
			<input type="text" name="<?php echo $name; ?>_month" placeholder="<?php _e( 'd MM', 'woocommerce-bookings' ); ?>" size="2" class="form-control booking_date_month" />
			<input type="text" value="<?php echo date( 'Y' ); ?>" name="<?php echo $name; ?>_year" placeholder="<?php _e( 'YYYY', 'woocommerce-bookings' ); ?>" size="4" class="form-control booking_date_year" />
		</div>
	 <div class="input-group addon w-25">
				<input type="text" name="in-time" value="12:30" class="out_time form-control "/>
	 </div>


		<?php else : ?>
		<label>
			<input type="text" name="<?php echo $name; ?>_day" placeholder="<?php _e( 'dd', 'woocommerce-bookings' ); ?>" size="2" class="booking_date_day" />
			<span><?php _e( 'Day', 'woocommerce-bookings' ); ?></span>
		</label> / <label>
			<input type="text" name="<?php echo $name; ?>_month" placeholder="<?php _e( 'MM', 'woocommerce-bookings' ); ?>" size="2" class="booking_date_month" />
			<span><?php _e( 'Month', 'woocommerce-bookings' ); ?></span>
		</label>
		<?php endif; ?>

				</div>
				<div class="col-6 p-0 input-group wc-bookings-date-picker-date-fields">
				<div class="input-group addon w-100">
					<span class="input-group-addon label" id="basic-addon3"><i class="fa fa-2x fa-map-o">Pick-up</i></span>

  				 <input type="text" name="pick-up" value="" placeholder="Flight number, Boat name or Hotel" class="out_time form-control "/>
				</div>
				</div>
	</div>
	<div class="row">
		<div class="col-6">
	<div class="picker 2" data-display="<?php echo $display; ?>" data-duration-unit="<?php echo esc_attr( $duration_unit ); ?>" data-availability="<?php echo esc_attr( json_encode( $availability_rules ) ); ?>" data-default-availability="<?php echo $default_availability ? 'true' : 'false'; ?>" data-fully-booked-days="<?php echo esc_attr( json_encode( $fully_booked_days ) ); ?>" data-unavailable-days="<?php echo esc_attr( json_encode( $unavailable_days ) ); ?>"data-partially-booked-days="<?php echo esc_attr( json_encode( $partially_booked_days ) ); ?>" data-buffer-days="<?php echo esc_attr( json_encode( $buffer_days ) ); ?>" data-restricted-days="<?php echo esc_attr( json_encode( $restricted_days ) ); ?>" data-min_date="<?php echo ! empty( $min_date_js ) ? $min_date_js : 0; ?>" data-max_date="<?php echo $max_date_js; ?>" data-default_date="<?php echo esc_attr( $default_date ); ?>" data-is_range_picker_enabled="<?php echo $is_range_picker_enabled ? 1 : 0; ?>"></div>
	</div>
		</div>
<div class="row">
	<?php if ( 'customer' == $duration_type && $is_range_picker_enabled ) : ?>
<div class="col-6 p-0 input-group wc-bookings-date-picker-date-fields">



			<?php if ( $month_before_day ) : ?>

				<div class="input-group addon w-100">
					<span class="input-group-addon label" id="basic-addon3"><i class="fa fa-2x fa-calendar-o">
		<?php echo esc_html( apply_filters( 'woocommerce_bookings_date_picker_end_label', __( 'End', 'woocommerce-bookings' ) ) ); ?></i></span>
					<input type="text" name="<?php echo $name; ?>_to_day" placeholder="<?php _e( 'dd', 'woocommerce-bookings' ); ?>" size="2" class="form-control booking_to_date_day" />
					<input type="text" name="<?php echo $name; ?>_to_month" placeholder="<?php _e( 'MM', 'woocommerce-bookings' ); ?>" size="2" class="form-control booking_to_date_month" />
						<input type="text" value="<?php echo date( 'Y' ); ?>" name="<?php echo $name; ?>_to_year" placeholder="<?php _e( 'YYYY', 'woocommerce-bookings' ); ?>" size="4" class="form-control booking_to_date_year" />
					</div>
				<div class="input-group addon w-25">
						 <input type="text" name="out-time" value="12:30" class="out_time form-control"/>
				</div>

			<?php else : ?>
			<label>
				<input type="text" name="<?php echo $name; ?>_to_day" placeholder="<?php _e( 'dd', 'woocommerce-bookings' ); ?>" size="2" class="booking_to_date_day" />
				<span><?php _e( 'Day', 'woocommerce-bookings' ); ?></span>
			</label> / <label>
				<input type="text" name="<?php echo $name; ?>_to_month" placeholder="<?php _e( 'MM', 'woocommerce-bookings' ); ?>" size="2" class="booking_to_date_month" />
				<span><?php _e( 'Month', 'woocommerce-bookings' ); ?></span>
			</label>
			<?php endif; ?>

					</div>
					<div class="col-6 p-0 input-group wc-bookings-date-picker-date-fields">
					<div class="input-group addon w-100">
						<span class="input-group-addon label" id="basic-addon3"><i class="fa fa-2x fa-map-o">Drop-off</i></span>

						 <input type="text" name="drop-off" value="" placeholder="Flight number, Boat name or Hotel" class="out_time form-control "/>
					</div>
					</div>
		</div>


	<?php endif; ?>


</div>


</div>
</fieldset>
