<nav class="navbar navbar-inverse fixed-top bg-inverse">

  <div id="app" class="container">
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div  id="navbarNavDropdown" class="navbar-collapse collapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item icon_fb">

            </li>


        </ul>
        <ul class="navbar-nav">
            <li class="nav-item">
                <?php do_action( 'your_theme_header_top' ); ?>
            </li>
            <li class="nav-item icon_cart">
cart
            </li>
        </ul>
        </div>
      </div>
</nav>
    <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">

      <div id="app" class="container">
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div  id="navbarNavDropdown" class="navbar-collapse collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item icon_fb">

                </li>


            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <?php do_action( 'your_theme_header_top' ); ?>
                </li>
                <li class="nav-item icon_cart">
    cart
                </li>
            </ul>
            </div>
          </div>
    </nav>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-5">
    				<div class="container">
              <div class="row">

              <div class="col-12 col-lg-3 pt-3">
    					<a href="<?= esc_url(home_url('/')); ?>">
                <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
                <h1><?php printf( esc_html__( '%s', 'coolcars' ), get_bloginfo ( 'description' ) ); ?></h1>

              </a>
            </div>

    			<?php
    			wp_nav_menu(array(
    			    'theme_location' => 'primary',
    			    'walker' => new Microdot_Walker_Nav_Menu(),
    			    'container' => false,
    			    'items_wrap' => '<div class="col-12 col-lg-9 text-right pt-5">%3$s</div>'
    			));
    			?>



    </div>
          </div>
    			</nav>
