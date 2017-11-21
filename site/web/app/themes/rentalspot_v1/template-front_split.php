<?php
/**
 * Template Name: Front - Split
 */
?>
<div class="container-fluid">
    <div class="site-main row">
      <div class="col-lg-1 col-12 left">
        <div class="col-lg-12  col-12 menu-box">Found:<?php echo do_shortcode("[facetwp counts='true']"); ?>cars</div>
        <div class="col-lg-12  col-12"><?php echo facetwp_display( 'facet', 'type' ); ?></div>
      </div>

    <div class="col-lg-11 col-sm-10 col-10 facetwp-template right" >

      <div class="row grid">
        <div class="grid-sizer"></div>
          <div class="gutter-sizer"></div>

<?php get_template_part('templates/unit', 'cars'); ?>

  </div>
  </div>

  </div>
</div>
