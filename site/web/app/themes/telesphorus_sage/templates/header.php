<header class="banner" style="background-color:<?php echo get_theme_mod( 'color', '#FFFFFF' ); ?>">

  <button class="hamburger hamburger--collapse" type="button">
  <span class="hamburger-box">
  <span class="hamburger-inner"></span>
  </span>
  </button>
<div class="container">
  <div class="row">
    <div class="col-12 p-0">
  <div class="d-flex align-items-center justify-content-between">

<div class="p-2">
  <?php

      $logo_image = get_theme_mod( 'header_logo_setting', '' );
      if ( $logo_image ) : ?>
          <a class="navmenu-brand" href='<?php echo esc_url( home_url( '/' ) ); ?>' title='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>' rel='home'>
              <img src='<?php echo esc_url( $logo_image ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
          </a>
      <?php else : ?>
          <h1 class='site-title'><a href='<?php echo esc_url( home_url( '/' ) ); ?>' title='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>' rel='home'><?php bloginfo( 'name' ); ?></a></h1>
  <?php endif; ?>
</div>
  <div class="p-2 w-75">

           <?php
           wp_nav_menu([
             //'menu'            => 'top',
             'theme_location'  => 'primary_navigation',
             'container'       => '',
             'container_id'    => '',
             'container_class' => '',
             'menu_id'         => false,
             'menu_class'      => 'nav',
             'depth'           => 2,
             'fallback_cb'     => 'bs4navwalker::fallback',
             'walker'          => new bs4navwalker()
           ]);
           ?>
  </div>
  <div class="p-2">Flex item 3</div>
</div>
</div>
</div>
</div>

</header>
