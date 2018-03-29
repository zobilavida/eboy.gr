<nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
  <div class="container">
    <a class="navbar-brand js-scroll-trigger" href="<?= esc_url(home_url('/')); ?>">
      <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
    </a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      Menu
      <i class="fa fa-bars"></i>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <?php
        wp_nav_menu([
          'menu'            => 'top',
          'theme_location'  => 'top',
          'container'       => 'div',
          'container_id'    => 'bs4navbar',
          'container_class' => 'collapse navbar-collapse',
          'menu_id'         => false,
          'menu_class'      => 'navbar-nav ml-auto',
          'depth'           => 2,
          'fallback_cb'     => 'bs4navwalker::fallback',
          'walker'          => new bs4navwalker()
        ]);
        ?>
    
    </div>
  </div>
</nav>
