
	<header class="d-flex flex-wrap justify-content-between px-5 py-3">
  <div class="col-12 col-lg-6 p-1 brand-container">
		<a class="brand" href="<?= esc_url(home_url('/')); ?>">
			  <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
		</a>
	</div>


		<?php
		wp_nav_menu(array(
		  'theme_location' => 'primary',
		  'walker' => new Microdot_Walker_Nav_Menu(),
		  'container' => false,
		  'items_wrap' => '<div class="col-12 col-lg-6 text-right p-0 menu-area">%3$s</div>'
		));
		?>

</header>
