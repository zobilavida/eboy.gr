
 <div id="app" class="container">
     <nav class="navbar container-fluid navbar-expand-lg navbar-dark bg-faded navbar-custom fixed-top">
       <div class="container">
         <a class="navbar-brand" href="#">
                <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
         </a>
         <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
             <span class="navbar-toggler-icon"></span>
         </button>
         <div id="navbarNavDropdown" class="navbar-collapse collapse">
             <ul class="navbar-nav mr-auto">

             </ul>
             <?php
             wp_nav_menu([
               'menu'            => 'top',
               'theme_location'  => 'top',
               'container'       => '',
               'container_id'    => '',
               'container_class' => '',
               'menu_id'         => false,
               'menu_class'      => 'navbar-nav',
               'depth'           => 2,
               'fallback_cb'     => 'bs4navwalker::fallback',
               'walker'          => new bs4navwalker()
             ]);
             ?>
             <ul class="navbar-nav">
                 <li class="nav-item active">
                   <a class="nav-link" href="#"><div data-icon="f" class="icon"></div></a>
                 </li>
                 <li class="nav-item">
                     <a class="nav-link" href="#"><div data-icon="g" class="icon"></div></a>
                 </li>
                 <li class="nav-item">
                   <a class="nav-link" href="#"><div data-icon="h" class="icon"></div></a>
                 </li>

             </ul>
         </div>
         </div>
     </nav>
 </div>
