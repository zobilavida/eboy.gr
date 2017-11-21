<nav class="navbar navbar-toggleable-md navbar-light bg-faded fixed-top">
  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href="<?php echo get_home_url(); ?>">
    <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>

    <h1><?php printf( esc_html__( '%s', 'sage' ), get_bloginfo ( 'description' ) ); ?></h1>
</a>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">

    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <?php echo facetwp_display( 'facet', 'pickup' ); ?>
      </li>
      <li class="nav-item">
        <?php echo facetwp_display( 'facet', 'date' ); ?>
      </li>
      <li class="nav-item">
        <?php echo facetwp_display( 'facet', 'attributes' ); ?>
      </li>
      <li class="nav-item">
        <?php echo facetwp_display( 'facet', 'wheeldrive' ); ?>
      </li>
      <li class="nav-item">
        <?php echo facetwp_display( 'facet', 'car-top' ); ?>
      </li>
    </ul>

  </div>
  <?php echo do_shortcode( '[facetwp sort="true"]' ); ?>
</nav>
