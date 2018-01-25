<header role="banner">
<div class="container-fluid ">
  <div class="row">
    <div class="col-3 my-auto">
      <a href="<?= esc_url(home_url('/')); ?>">
  <img id="logo-main" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/32877/logo-thing.png" width="200" alt="Logo Thing main logo">
</a>
      <p>
      <a class="d-lg-none" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample" onclick="myFunction(this)">
    <img src="<?= get_template_directory_uri(); ?>/assets/images/menu.svg" class="menu-icon" alt="Web developer"/>
</a>
</p>


</div>
    <div class="col-6">

</div>
    <div class="col-3">

</div>

</div>
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
