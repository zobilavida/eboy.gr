<nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-5">
				<div class="container-fluid">

          <div class="col-3">
					<a href="<?= esc_url(home_url('/')); ?>">
            <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
          </a>
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
