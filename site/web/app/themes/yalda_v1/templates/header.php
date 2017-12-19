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
		'theme_location'	=> 'shop',
		'container'			=> 'div',
		'container_class'	=> 'collapse navbar-collapse',
		'container_id'      => 'navbarCollapse',
		'menu_class'		=> 'nav navbar-nav',
		'fallback_cb'		=> '__return_false',
		'items_wrap'		=> '<ul id="%1$s" class="%2$s">%3$s</ul>',
		'depth'				=> 2,
		'walker'			=> new bootstrap_4_walker_nav_menu()
	) );
			?>
      </div>
			</nav>
