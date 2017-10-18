<nav class="navbar navbar-light navbar-toggleable-sm bg-white justify-content-center fixed-top">


  <div class="container">


    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <a href="/" class="navbar-brand d-flex w-50">
      <div class="row">
        <div class="col-4">
      <img class="" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
    </div>
    <div class="col-8 no-gutters">
      <h1><?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?></h1>
  <h2><?php printf( esc_html__( '%s', 'sage' ), get_bloginfo ( 'description' ) ); ?></h2>
  </div>
</div>
    </a>
    <div class="navbar-collapse collapse" id="navbarNav">
      <?php
          wp_nav_menu( array(
              'menu'              => 'primary',
              'theme_location'    => 'primary',
              'depth'             => 2,
              'container'         => 'div',
              'container_class'   => 'nav justify-content-center top-menu mr-auto',
              'container_id'      => '',
              'menu_class'        => 'bar top-menu',
              'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
              'walker'            => new wp_bootstrap_navwalker())
          );
      ?>
      <!-- Button trigger modal -->
      <div id="feedback"><button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#feedback-modal">Feedback Modal Form</button></div>
  <img src="<?= get_template_directory_uri(); ?>/assets/images/ico_hand.svg" alt="Web developer"/>
  Hire Me!
</button>
    </div>
    <div class="collapse">
      test
    </div>
    </div>
</nav>
