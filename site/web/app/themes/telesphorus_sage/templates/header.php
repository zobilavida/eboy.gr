<header class="banner">
  <nav class="navbar navbar-expand-md navbar-dark  bg-white">
    <div id='slider'>Hello World!!</div>
    <button class="hamburger hamburger--collapse" type="button">
  <span class="hamburger-box">
    <span class="hamburger-inner"></span>
  </span>
</button>
<div class="container">
  <?php global $redux_demo; ?>
     <?php if ($redux_demo['opt-media']['url'] != ' ') { ?>
          <img src="<?php  echo '' . $redux_demo['opt-media']['url']; ?>" />

      <?php } else { ?>
          <span class="site-title">
            <a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a>
        </span>
           <h4 class="site-desc"><?php bloginfo('description'); ?></h4>
  <?php } ?>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
              <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Link</a>
            </li>
            <li class="nav-item">
              <a class="nav-link disabled" href="#">Disabled</a>
            </li>
          </ul>
          <form class="form-inline mt-2 mt-md-0">
            <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
          </form>
        </div>
      </nav>
</div>
</header>
