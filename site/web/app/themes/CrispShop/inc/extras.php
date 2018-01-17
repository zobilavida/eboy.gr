<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package crispshop
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function crispshop_theme_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	return $classes;
}
add_filter( 'body_class', 'crispshop_theme_body_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function crispshop_theme_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', bloginfo( 'pingback_url' ), '">';
	}
}

add_action( 'wp_head', 'crispshop_theme_pingback_header' );

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', 'crispshop_theme_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'crispshop_theme_wrapper_end', 10);

function crispshop_theme_wrapper_start() {
	echo '<div class="inner">';
}

function crispshop_theme_wrapper_end() {
	echo '</div>';
}

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

add_filter( 'woocommerce_enqueue_styles', 'crispshop_dequeue_styles' );

function crispshop_dequeue_styles( $enqueue_styles ) {
	unset( $enqueue_styles['woocommerce-general'] );
	unset( $enqueue_styles['woocommerce-smallscreen'] );
	return $enqueue_styles;
}

add_filter( 'woocommerce_product_tabs', 'crispshop_remove_product_tabs', 98 );

function crispshop_remove_product_tabs( $tabs ) {
    unset( $tabs['description'] );
    unset( $tabs['additional_information'] );

    return $tabs;
}

function crispshop_add_cart_ajax() {
	$prodID = $_POST['prodID'];

	WC()->cart->add_to_cart($prodID);

	$items = WC()->cart->get_cart();
	global $woocommerce;
	$item_count = $woocommerce->cart->cart_contents_count; ?>

	<span class="item-count"><?php echo $item_count; ?></span>

	<h4>Shopping Bag</h4>

	<?php foreach($items as $item => $values) { 
		$_product = $values['data']->post; ?>
		
		<div class="dropdown-cart-wrap">
			<div class="dropdown-cart-left">
				<?php echo get_the_post_thumbnail( $values['product_id'], 'thumbnail' ); ?>
			</div>

			<div class="dropdown-cart-right">
				<h5><?php echo $_product->post_title; ?></h5>
				<p><strong>Quantity:</strong> <?php echo $values['quantity']; ?></p>
				<?php global $woocommerce;
				$currency = get_woocommerce_currency_symbol();
				$price = get_post_meta( $values['product_id'], '_regular_price', true);
				$sale = get_post_meta( $values['product_id'], '_sale_price', true);
				?>
				 
				<?php if($sale) { ?>
					<p class="price"><strong>Price:</strong> <del><?php echo $currency; echo $price; ?></del> <?php echo $currency; echo $sale; ?></p>
				<?php } elseif($price) { ?>
					<p class="price"><strong>Price:</strong> <?php echo $currency; echo $price; ?></p>    
				<?php } ?>
			</div>

			<div class="clear"></div>
		</div>
	<?php } ?>

	<div class="dropdown-cart-wrap dropdown-cart-subtotal">
		<div class="dropdown-cart-left">
			<h6>Subtotal</h6>
		</div>

		<div class="dropdown-cart-right">
			<h6><?php echo WC()->cart->get_cart_total(); ?></h6>
		</div>

		<div class="clear"></div>
	</div>

	<?php $cart_url = $woocommerce->cart->get_cart_url();
	$checkout_url = $woocommerce->cart->get_checkout_url(); ?>

	<div class="dropdown-cart-wrap dropdown-cart-links">
		<div class="dropdown-cart-left dropdown-cart-link">
			<a href="<?php echo $cart_url; ?>">View Cart</a>
		</div>

		<div class="dropdown-cart-right dropdown-checkout-link">
			<a href="<?php echo $checkout_url; ?>">Checkout</a>
		</div>

		<div class="clear"></div>
	</div>

	<?php die();
}

add_action('wp_ajax_crispshop_add_cart', 'crispshop_add_cart_ajax');
add_action('wp_ajax_nopriv_crispshop_add_cart', 'crispshop_add_cart_ajax');

function crispshop_add_cart_single_ajax() {
	$product_id = $_POST['product_id'];
	$variation_id = $_POST['variation_id'];
	$quantity = $_POST['quantity'];

	if ($variation_id) {
		WC()->cart->add_to_cart( $product_id, $quantity, $variation_id );
	} else {
		WC()->cart->add_to_cart( $product_id, $quantity);
	}

	$items = WC()->cart->get_cart();
	global $woocommerce;
	$item_count = $woocommerce->cart->cart_contents_count; ?>

	<span class="item-count"><?php echo $item_count; ?></span>

	<h4>Shopping Bag</h4>

	<?php foreach($items as $item => $values) { 
		$_product = $values['data']->post; ?>
		
		<div class="dropdown-cart-wrap">
			<div class="dropdown-cart-left">
				<?php echo get_the_post_thumbnail( $values['product_id'], 'thumbnail' ); ?>
			</div>

			<div class="dropdown-cart-right">
				<h5><?php echo $_product->post_title; ?></h5>
				<p><strong>Quantity:</strong> <?php echo $values['quantity']; ?></p>
				<?php global $woocommerce;
				$currency = get_woocommerce_currency_symbol();
				$price = get_post_meta( $values['product_id'], '_regular_price', true);
				$sale = get_post_meta( $values['product_id'], '_sale_price', true);
				?>
				 
				<?php if($sale) { ?>
					<p class="price"><strong>Price:</strong> <del><?php echo $currency; echo $price; ?></del> <?php echo $currency; echo $sale; ?></p>
				<?php } elseif($price) { ?>
					<p class="price"><strong>Price:</strong> <?php echo $currency; echo $price; ?></p>    
				<?php } ?>
			</div>

			<div class="clear"></div>
		</div>
	<?php } ?>

	<div class="dropdown-cart-wrap dropdown-cart-subtotal">
		<div class="dropdown-cart-left">
			<h6>Subtotal</h6>
		</div>

		<div class="dropdown-cart-right">
			<h6><?php echo WC()->cart->get_cart_total(); ?></h6>
		</div>

		<div class="clear"></div>
	</div>

	<?php $cart_url = $woocommerce->cart->get_cart_url();
	$checkout_url = $woocommerce->cart->get_checkout_url(); ?>

	<div class="dropdown-cart-wrap dropdown-cart-links">
		<div class="dropdown-cart-left dropdown-cart-link">
			<a href="<?php echo $cart_url; ?>">View Cart</a>
		</div>

		<div class="dropdown-cart-right dropdown-checkout-link">
			<a href="<?php echo $checkout_url; ?>">Checkout</a>
		</div>

		<div class="clear"></div>
	</div>

	<?php die();
}

add_action('wp_ajax_crispshop_add_cart_single', 'crispshop_add_cart_single_ajax');
add_action('wp_ajax_nopriv_crispshop_add_cart_single', 'crispshop_add_cart_single_ajax');
