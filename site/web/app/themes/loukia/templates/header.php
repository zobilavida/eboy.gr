<div id='slider'>Hello World!!</div>
<!--Indicators-->
<ol class="carousel-indicators">
    <li data-target="#video-carousel" data-slide-to="0" class="myCarousel-target active"></li>
    <li data-target="#video-carousel" data-slide-to="1" class="myCarousel-target"></li>
    <li data-target="#video-carousel" data-slide-to="2" class="myCarousel-target"></li>
</ol>
<!--/.Indicators-->
<header class="banner">
  <nav id="topNav" class="navbar  navbar-toggleable-sm navbar-inverse bg-inverse">
    <button class="hamburger hamburger--arrowturn" type="button">
      <span class="hamburger-box">
        <span class="hamburger-inner"></span>
      </span>Menu
    </button>
    <a class="navbar-brand mx-auto" href="#">
      <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
    </a>
    <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="#">About</a>
            </li>
        </ul>
    </div>
</nav>
</header>
