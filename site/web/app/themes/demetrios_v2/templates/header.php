<header class="banner">
  <nav class="navbar navbar-light navbar-expand-md navbar-transparent bg-transparent">

  					<div class="container">

  					<!-- Your site title as branding in the menu -->
  					<a href="<?= esc_url(home_url('/')); ?>" class="navbar-brand custom-logo-link" rel="home" itemprop="url"><img class="logo" src='<?php $image = get_field('home_logo');?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'></a><!-- end custom logo -->

  				<button class="navbar-toggler navbar-toggler-right toggleButton" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
  				</button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <?php
            wp_nav_menu([
              'menu'            => 'top',
              'theme_location'  => 'top',
              'container'       => 'div',
              'container_id'    => 'bs4navbar',
              'container_class' => 'collapse navbar-collapse',
              'menu_id'         => false,
              'menu_class'      => 'navbar-nav ml-auto w-100 justify-content-end',
              'depth'           => 2,
              'fallback_cb'     => 'bs4navwalker::fallback',
              'walker'          => new bs4navwalker()
            ]);
            ?>
                </div>
					</div><!-- .container -->

  		</nav>
</header>
