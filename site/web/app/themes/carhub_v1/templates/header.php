<nav class="navbar fixed-top navbar-expand-sm">

    <div class="navbar-collapse " id="navbar8">
        <ul class="navbar-nav abs-center-x">
            <li class="nav-item text-center">
              <a class="" href="#">
                <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
                <h1><?php echo get_bloginfo( 'name' ); ?></h1>
              </a>
            </li>
        </ul>
      </div>
      <div class="secondary-cart">
				<?php $items = WC()->cart->get_cart();
				global $woocommerce;
				$item_count = $woocommerce->cart->cart_contents_count; ?>
				<a class="cart-totals" href="<?php echo wc_get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>">Cart (<span><?php echo $item_count; ?></span>)</a>

			</div>
</nav>
