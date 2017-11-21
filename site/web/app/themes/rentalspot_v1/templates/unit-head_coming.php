<nav class="navbar navbar-shadow navbar-fixed-top">
<div class="row no-padding menu-box" >



  <div class="col-lg-2">
     <a class="brand" href="<?php echo get_home_url(); ?>">
<img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
  </a>
</div>
<div class="col-lg-2">
<?php echo facetwp_display( 'facet', 'pickup' ); ?>
</div>
<div class="col-lg-4 menu-box">
<?php echo facetwp_display( 'facet', 'date' ); ?>
</div>
<div class="col-lg-4">
<?php echo facetwp_display( 'facet', 'attributes' ); ?>
</div>


<div class="col-lg-8">
<?php echo facetwp_display( 'facet', 'price' ); ?>
</div>

</div> <!-- row -->

</nav>
