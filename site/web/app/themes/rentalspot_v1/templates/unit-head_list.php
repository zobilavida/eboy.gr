<nav class="navbar navbar-toggleable-md navbar-light bg-faded fixed-top">
  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <a class="navbar-brand" href="<?php echo get_home_url(); ?>">
    <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
</a>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">

    <?php
      wp_nav_menu([
         'menu'            => 'primary',
         'theme_location'  => 'primary',
         'container'       => 'div',
         'container_id'    => 'exCollapsingNavbar2',
         'container_class' => 'collapse navbar-toggleable-sm',
         'menu_id'         => false,
         'menu_class'      => 'nav navbar-nav',
         'depth'           => 2,
         'fallback_cb'     => 'bs4navwalker::fallback',
         'walker'          => new bs4navwalker()
     ]);
    ?>

  </div>
</nav>
