<header class="banner">
  <div class="container">
    <div class="d-flex flex-row justify-content-between align-items-center">
  <div class="p-0">

  <a class="brand" href="<?= esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>

</div>
  <div class="p-0"></div>
  <div class="p-0">
    <nav class="nav-primary">
        <?php
        if (has_nav_menu('primary_navigation')) :
          wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav p-3']);
        endif;
        ?>
      </nav>
</div>
</div>

  </div>
</header>
