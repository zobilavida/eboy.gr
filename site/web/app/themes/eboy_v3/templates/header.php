<nav class="navbar navbar-expand-lg fixed-top navbar-custom pt-5 pb-0">
				<div class="container-fluid">
					<div class="row">
          <div class="col-3">
					<a href="<?= esc_url(home_url('/')); ?>">
            <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
          </a>
				</div>
				<div class="col-9">
					<?php //woocommerce_breadcrumb(); ?>
        </div>
				  </div>

			<?php
			wp_nav_menu(array(
			    'theme_location' => 'primary',
			    'walker' => new Microdot_Walker_Nav_Menu(),
			    'container' => false,
			    'items_wrap' => '<div class="col-9 text-right ">%3$s</div>'
			));
			?>




      </div>
			</nav>
