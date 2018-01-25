<header role="banner">
<div class="container-fluid ">

<div class="row d-flex justify-content-center">
  <a href="<?= esc_url(home_url('/')); ?>">
<img id="logo-main" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/32877/logo-thing.png" width="200" alt="Logo Thing main logo">
</a>
  <div class="collapse" id="collapseExample">
      <?php
          wp_nav_menu( array(
              'menu'              => 'primary',
              'theme_location'    => 'primary',
              'depth'             => 2,
              'container'         => 'div',
              'container_class'   => '',
              'container_id'      => '',
              'menu_class'        => '',
              'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
              'walker'            => new wp_bootstrap_navwalker())
          );
      ?>
  </div>
</div>
</div>


</header><!-- header role="banner" -->
