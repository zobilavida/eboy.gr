<header role="banner">
<div class="container-fluid ">
  <div class="row">
    <div class="col-3 my-auto">

      <p>
      <div class="d-lg-none" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample" onclick="myFunction(this)">
  <div class="bar1"></div>
  <div class="bar2"></div>
  <div class="bar3"></div>
</div>
</p>


</div>
    <div class="col-6">
  <img id="logo-main" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/32877/logo-thing.png" width="200" alt="Logo Thing main logo">
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
