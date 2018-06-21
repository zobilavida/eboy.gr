
	<header class="d-flex flex-wrap justify-content-between px-5">
  <div class="col-12 col-lg-6 p-0">
		<a class="brand" href="<?= esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
	</div>


		<?php
		wp_nav_menu(array(
		  'theme_location' => 'primary',
		  'walker' => new Microdot_Walker_Nav_Menu(),
		  'container' => false,
		  'items_wrap' => '<div class="col-12 col-lg-6 text-right p-0">%3$s</div>'
		));
		?>

</header>
