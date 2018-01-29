<nav class="navbar container navbar-expand-md navbar-light bg-faded fixed-top">
   <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#bs4navbar" aria-controls="bs4navbar" aria-expanded="false" aria-label="Toggle navigation">
     <span class="navbar-toggler-icon"></span>
   </button>
   <a class="navbar-brand" href="#">
     <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
   </a>
   <?php
   wp_nav_menu([
     'menu'            => 'top',
     'theme_location'  => 'top',
     'container'       => 'div',
     'container_id'    => 'bs4navbar',
     'container_class' => 'collapse navbar-collapse',
     'menu_id'         => false,
     'menu_class'      => 'navbar-nav mr-auto',
     'depth'           => 2,
     'fallback_cb'     => 'bs4navwalker::fallback',
     'walker'          => new bs4navwalker()
   ]);
   ?>
 </nav>
