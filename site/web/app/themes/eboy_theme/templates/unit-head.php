<nav class="navbar navbar-toggleable-md navbar-light bg-faded fixed-top">
  <div class="container">
  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="row">
    <div class="col-lg-3 col-md-6 col-12">
<div class="row">
      <div class="col-4">
        <img class="mx-auto d-block" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
      </div>
      <div class="col-8 no-gutters">
    <h1><?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?></h1>
<h2><?php printf( esc_html__( '%s', 'sage' ), get_bloginfo ( 'description' ) ); ?></h2>
</div>

</div>
</div>
<div class="col-7">
  <div class="collapse navbar-collapse" id="navbarNav">
    <?php
        wp_nav_menu( array(
            'menu'              => 'primary',
            'theme_location'    => 'primary',
            'depth'             => 2,
            'container'         => 'div',
            'container_class'   => 'nav justify-content-center top-menu',
            'container_id'      => '',
            'menu_class'        => 'bar top-menu',
            'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
            'walker'            => new wp_bootstrap_navwalker())
        );
    ?>
</div>
  </div>
  <div class="col-2">
  </div>
  </div>
  </div>
</nav>
