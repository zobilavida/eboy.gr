<div class="header bg-light">
    <div class="container">
        <div class="row">
            <div class="col-6">
                <?php dynamic_sidebar('sidebar-header-left'); ?>
            </div>
            <div class="col-6 text-right">
                <?php dynamic_sidebar('sidebar-header-right'); ?>
            </div>
        </div>
        <!--/row-->
    </div>
    <!--container-->
</div>
<nav class="navbar navbar-expand-sm sticky-top navbar-light bg-white">
    <div class="container">
        <a class="navbar-brand" href="#"><img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbar1">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbar1">

            <?php
            wp_nav_menu([
              'menu'            => 'top',
              'theme_location'  => 'top',
              'container'       => '',
              'container_id'    => '',
              'container_class' => '',
              'menu_id'         => false,
              'menu_class'      => 'navbar-nav ml-auto',
              'depth'           => 2,
              'fallback_cb'     => 'bs4navwalker::fallback',
              'walker'          => new bs4navwalker()
            ]);
            ?>
        </div>
    </div>
</nav>
