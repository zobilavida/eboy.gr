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
			    'items_wrap' => '<div class="col-lg-20 col-24 text-left menu-text">%3$s</div>'
			));
			?>

      </div>
			</nav>
