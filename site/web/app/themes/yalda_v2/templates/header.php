<header role="banner">
<div class="container-fluid ">

<div class="row d-flex justify-content-center">

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
