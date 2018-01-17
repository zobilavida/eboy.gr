<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<?php $crispshop_favicon = get_theme_mod('crispshop_favicon');
if ($crispshop_favicon) { ?>
	<link rel="shortcut icon" href="<?php echo $crispshop_favicon; ?>" />
<?php } else { ?>
	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png" />
<?php } ?>

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<nav id="mobile-navigation" class="main-navigation" role="navigation">
	<div class="mobile-nav-wrap">
		<a href="#" class="mobile-nav-close"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
		<a class="mobile-home-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
		<div class="categories-menu">
			<h5>Categories<span><i class="fa fa-angle-right" aria-hidden="true"></i></span></h5>
			<div class="categories-dropdown">
				<ul>
					<?php 
					$all_categories = get_categories( 'taxonomy=product_cat' );
					foreach ($all_categories as $cat) {
						if($cat->category_parent == 0) {
							$category_id = $cat->term_id;
							
							$args = array(
								'taxonomy' => 'product_cat',
								'parent' => $category_id
							);

							$sub_cats = get_categories( $args );
							if($sub_cats) { ?>
								<li class="has-sub"><a href="<?php echo get_term_link($cat->slug, 'product_cat'); ?>"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php echo $cat->name; ?></a>

								<ul>
									<?php foreach($sub_cats as $sub_category) {
										echo '<li><a href="'. get_term_link($sub_category->slug, 'product_cat') .'">'. $sub_category->name .'</a></li>';
									} ?>
								</ul>
							<?php } else { ?>
								<li><a href="<?php echo get_term_link($cat->slug, 'product_cat'); ?>"><?php echo $cat->name; ?></a>
							<?php }
						}
					} ?>
					</li>
				</ul>
			</div>
		</div>
		<?php wp_nav_menu( array( 'theme_location' => 'primary_menu', 'menu_id' => 'primary-menu' ) ); ?>
	</div>
</nav><!-- #site-navigation -->

<div id="page" class="site">
	<div class="mobile-overlay"></div>

	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'crispshop' ); ?></a>

	<?php $top_bar_display = get_theme_mod('crispshop_top_bar_display', '');
	$crispshop_phone_number = get_theme_mod('crispshop_phone_number', '');
	if (!$top_bar_display) { ?>
	<div id="top-bar">
		<div class="inner">
			<div class="top-left">
				<p><span>Call us toll free: </span><a href="tel:<?php echo $crispshop_phone_number; ?>"><span><?php echo $crispshop_phone_number; ?></span></a></p>
			</div>

			<?php if ( is_user_logged_in() ) { ?>
				<div class="top-right user-drop">
					<a class="top-account" href="<?php echo esc_url( home_url() ); ?>/my-account/">My Account</a>
					<div class="user-dropdown">
						<a href="<?php echo esc_url( home_url() ); ?>/my-account/">Dashboard</a>
						<a href="<?php echo esc_url( home_url() ); ?>/my-account/orders/">My Orders</a>
						<a href="<?php echo esc_url( home_url() ); ?>/my-account/downloads/">My Downloads</a>
						<a href="<?php echo esc_url( home_url() ); ?>/my-account/edit-address/">My Addresses</a>
						<a href="<?php echo esc_url( home_url() ); ?>/my-account/edit-account/">Edit Account</a>
						<a href="<?php echo esc_url( home_url() ); ?>/my-account/customer-logout/">Logout</a>
					</div>
				</div>
			<?php } else { ?>
				<div class="top-right">
					<a class="top-login" href="<?php echo esc_url( home_url() ); ?>/my-account/">Login/Register</a>
				</div>
			<?php } ?>
			
			<div class="clear"></div>
		</div>
	</div>
	<?php } ?>

	<header id="masthead" class="site-header" role="banner">
		<div class="inner">
			<div class="site-branding">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php $crispshop_logo = get_theme_mod('crispshop_logo');
					if ($crispshop_logo) { ?>
						<img src="<?php echo $crispshop_logo; ?>" alt="<?php bloginfo('name'); ?>" />
					<?php } else { ?>
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.png" alt="<?php bloginfo('name'); ?>" />
					<?php } ?>
				</a>
			</div><!-- .site-branding -->

			<div class="site-right">
				<div id="mobile-slide">
					<a href="#"><span class="icon-bar"><span class="icon-bars"></span></span></a>
				</div>
				<nav id="site-navigation" class="main-navigation" role="navigation">
					<?php wp_nav_menu( array( 'theme_location' => 'primary_menu', 'menu_id' => 'primary-menu' ) ); ?>
				</nav><!-- #site-navigation -->
			</div><!-- .site-branding -->

			<div class="clear"></div>
		</div>
	</header><!-- #masthead -->

	<div id="secondary-menu">
		<div class="inner">
			<div class="secondary-categories">
				<a href="#"><span></span>All Categories</a>
				<ul class="all-cats-menu">
					<?php 
					$all_categories = get_categories( 'taxonomy=product_cat' );
					foreach ($all_categories as $cat) {
						if($cat->category_parent == 0) {
							$category_id = $cat->term_id;
							
							$args = array(
								'taxonomy' => 'product_cat',
								'parent' => $category_id
							);

							$sub_cats = get_categories( $args );
							if($sub_cats) { ?>
								<li class="has-sub"><a href="<?php echo get_term_link($cat->slug, 'product_cat'); ?>"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span><?php echo $cat->name; ?></a>

								<ul>
									<?php foreach($sub_cats as $sub_category) {
										echo '<li><a href="'. get_term_link($sub_category->slug, 'product_cat') .'">'. $sub_category->name .'</a></li>';
									} ?>
								</ul>
							<?php } else { ?>
								<li><a href="<?php echo get_term_link($cat->slug, 'product_cat'); ?>"><?php echo $cat->name; ?></a>
							<?php }
						}
					} ?>
					</li>
				</ul>
			</div>

			<div class="secondary-search">
				<?php get_product_search_form(); ?>
			</div>

			<div class="secondary-cart">
				<?php $items = WC()->cart->get_cart();
				global $woocommerce;
				$item_count = $woocommerce->cart->cart_contents_count; ?>
				<a class="cart-totals" href="<?php echo wc_get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>">Cart (<span><?php echo $item_count; ?></span>)</a>
				<div class="cart-dropdown">
					<div class="cart-dropdown-inner">
						<?php if ($items) { ?>
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
						<?php } else { ?>
							<h4>Shopping Bag</h4>

							<div class="dropdown-cart-wrap">
								<p>Your cart is empty.</p>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>

			<div class="clear"></div>
		</div>
	</div>

	<div id="content" class="site-content">
