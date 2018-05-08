<?php
/**
 * The Template for displaying wishlist.
 *
 * @version             1.6.1
 * @package           TInvWishlist\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="tinv-wishlist woocommerce tinv-wishlist-clear">
	<?php //do_action( 'tinvwl_before_wishlist', $wishlist ); ?>
	<?php if ( function_exists( 'wc_print_notices' ) ) {
		wc_print_notices();
	} ?>
	<form action="<?php echo esc_url( tinv_url_wishlist() ); ?>" method="post" autocomplete="off">


		<?php do_action( 'tinvwl_before_wishlist_table', $wishlist ); ?>
		<div class="tinvwl-table-manage-list 2">


			<?php do_action( 'tinvwl_wishlist_contents_before' ); ?>
			<div class="container p-5">
			  <div class="row grid">
			<?php
			foreach ( $products as $wl_product ) {
				$product = apply_filters( 'tinvwl_wishlist_item', $wl_product['data'] );
				unset( $wl_product['data'] );
				if ( $wl_product['quantity'] > 0 && apply_filters( 'tinvwl_wishlist_item_visible', true, $wl_product, $product ) ) {
					$product_url = apply_filters( 'tinvwl_wishlist_item_url', $product->get_permalink(), $wl_product, $product );
					do_action( 'tinvwl_wishlist_row_before', $wl_product, $product );
					?>
					<div class="card w-100 m-3 <?php echo esc_attr( apply_filters( 'tinvwl_wishlist_item_class', 'wishlist_item', $wl_product, $product ) ); ?>">

						<div class="d-flex flex-row">

											<div class="col-2 pl-0">
												<?php
															$thumbnail = apply_filters( 'tinvwl_wishlist_item_thumbnail', $product->get_image('image-fluid card-img-top'), $wl_product, $product );

															if ( ! $product->is_visible() ) {
																echo $thumbnail; // WPCS: xss ok.
															} else {
																printf( '<a href="%s">%s</a>', esc_url( $product_url ), $thumbnail ); // WPCS: xss ok.
															}
															?>
														</div>
											  <div class="col-10 p-3">
													<div class="d-flex flex-row">
													<div class="col-9">
														<?php
														if ( ! $product->is_visible() ) {
															echo apply_filters( 'tinvwl_wishlist_item_name', $product->get_title('<h1>', '</h1>'), $wl_product, $product ) . '&nbsp;'; // WPCS: xss ok.
														} else {
															echo apply_filters( 'tinvwl_wishlist_item_name', sprintf( '<h1 class="card-title"><a href="%s">%s</a></h1>', esc_url( $product_url ), $product->get_title() ), $wl_product, $product ); // WPCS: xss ok.
														}

														echo apply_filters( 'tinvwl_wishlist_item_meta_data', tinv_wishlist_get_item_data( $product, $wl_product ), $wl_product, $product ); // WPCS: xss ok.
														?>
														<?php
													//	echo '<p class="card-text">';
														echo apply_filters( 'woocommerce_short_description', $product->post->post_content );
													//	echo '</p>';
														?>
													</div>
													<div class="col-3 product-remove">
														<button type="submit" name="tinvwl-remove"
																		value="<?php echo esc_attr( $wl_product['ID'] ); ?>">X
														</button>
														<?php if ( isset( $wishlist_table_row['colm_price'] ) && $wishlist_table_row['colm_price'] ) { ?>
															<div class="product-price">
																<?php
																echo apply_filters( 'tinvwl_wishlist_item_price', $product->get_price_html(), $wl_product, $product ); // WPCS: xss ok.
																?>
															</div>
														<?php } ?>

														<?php if ( isset( $wishlist_table_row['colm_stock'] ) && $wishlist_table_row['colm_stock'] ) { ?>
															<div class="product-stock">
																<?php
																$availability = (array) $product->get_availability();
																if ( ! array_key_exists( 'availability', $availability ) ) {
																	$availability['availability'] = '';
																}
																if ( ! array_key_exists( 'class', $availability ) ) {
																	$availability['class'] = '';
																}
																$availability_html = empty( $availability['availability'] ) ? '<p class="stock ' . esc_attr( $availability['class'] ) . '"><span><i class="fa fa-check"></i></span><span class="tinvwl-txt">' . esc_html__( 'In stock', 'ti-woocommerce-wishlist' ) . '</span></p>' : '<p class="stock ' . esc_attr( $availability['class'] ) . '"><span><i class="fa fa-check"></i></span><span>' . esc_html( $availability['availability'] ) . '</span></p>';

																echo apply_filters( 'tinvwl_wishlist_item_status', $availability_html, $availability['availability'], $wl_product, $product ); // WPCS: xss ok.
																?>
															</div>
														<?php } ?>
														<?php if ( isset( $wishlist_table_row['add_to_cart'] ) && $wishlist_table_row['add_to_cart'] ) { ?>

														<?php } ?>
													</div>

												

													</div>
													<div class="col-12 p-3">
												 <?php if ( isset( $wishlist_table_row['colm_date'] ) && $wishlist_table_row['colm_date'] ) { ?>

														 <?php
														 echo apply_filters( 'tinvwl_wishlist_item_date', sprintf( // WPCS: xss ok.
															 '<time class="entry-date" datetime="%1$s">%2$s</time>', $wl_product['date'], mysql2date( get_option( 'date_format' ), $wl_product['date'] )
														 ), $wl_product, $product );
														 ?>

												 <?php } ?>
												 </div>
												</div>





						</div>





					</div>

					<?php
				//	do_action( 'tinvwl_wishlist_row_after', $wl_product, $product );
				} // End if().
			} // End foreach().
			?>
			</div>
					</div>
			<?php //do_action( 'tinvwl_wishlist_contents_after' ); ?>


			<div>
				<div colspan="100">
					<?php //do_action( 'tinvwl_after_wishlist_table', $wishlist ); ?>
					<?php wp_nonce_field( 'tinvwl_wishlist_owner', 'wishlist_nonce' ); ?>
				</div>
			</div>


	</form>
	<?php do_action( 'tinvwl_after_wishlist', $wishlist ); ?>
	<div class="tinv-lists-nav tinv-wishlist-clear">
		<?php do_action( 'tinvwl_pagenation_wishlist', $wishlist ); ?>
	</div>
</div>
