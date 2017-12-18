<nav class="navbar top-black fixed-top">

</nav>

<nav class="navbar navbar-expand-lg fixed-top second-nav">
				<div class="container-fluid">
          <div class="col-lg-4 col-24">

        </div>

			<?php
			wp_nav_menu(array(
			    'theme_location' => 'primary',
			    'walker' => new Microdot_Walker_Nav_Menu(),
			    'container' => false,
			    'items_wrap' => '<div class="col-lg-15 col-24 text-left menu-text">%3$s</div>'
			));
			?>
			<?php
					wp_nav_menu( array(
							'menu'              => 'shop',
							'theme_location'    => 'shop',
							'depth'             => 2,
							'container'         => 'div',
							'container_class'   => 'nav justify-content-center top-menu mr-auto',
							'container_id'      => '',
							'menu_class'        => 'bar top-menu',
							'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
							'walker'            => new wp_bootstrap_navwalker())
					);
			?>
      </div>
			</nav>
