<?php
add_action( 'wp_enqueue_scripts', 'wilokeListGoChildEnqueueScripts', 90 );
function wilokeListGoChildEnqueueScripts() {
	wp_enqueue_style( 'listgo-child',get_stylesheet_directory_uri() . '/style.css');
	wp_enqueue_script('listgo-child', get_stylesheet_directory_uri() . '/script_22.js', array('jquery'), null, true);
}



// Skip the cart and redirect to check out url when clicking on Add to cart
add_filter ( 'add_to_cart_redirect', 'redirect_to_checkout' );
function redirect_to_checkout() {

	global $woocommerce;
	// Remove the default `Added to cart` message
	wc_clear_notices();
	return $woocommerce->cart->get_checkout_url();

}
// Global redirect to check out when hitting cart page
add_action( 'template_redirect', 'redirect_to_checkout_if_cart' );
function redirect_to_checkout_if_cart() {

	if ( !is_cart() ) return;
	global $woocommerce;
    // Redirect to check out url
	wp_redirect( $woocommerce->cart->get_checkout_url(), '301' );
	exit;

}
// Empty cart each time you click on add cart to avoid multiple element selected
add_action( 'woocommerce_add_cart_item_data', 'clear_cart', 0 );
function clear_cart () {
	global $woocommerce;
	$woocommerce->cart->empty_cart();
}
// Edit default add_to_cart button text
add_filter( 'add_to_cart_text', 'woo_custom_cart_button_text' );
add_filter( 'woocommerce_product_single_add_to_cart_text', 'custom_cart_button_text' );
function custom_cart_button_text() {
	return __( 'Buy', 'woocommerce' );
}
// Unset all options related to the cart
update_option( 'woocommerce_cart_redirect_after_add', 'no' );
update_option( 'woocommerce_enable_ajax_add_to_cart', 'no' );
