<header class="banner">

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
    <a class="brand" href="<?= esc_url(home_url('/')); ?>">
        <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
    </a>
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
