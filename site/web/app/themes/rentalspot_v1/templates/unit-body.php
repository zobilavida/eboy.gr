<section id="body">

  <div class="container-fluid">
      <div class="site-main row">
        <div class="container">
          <hr/>
          <div class="row filters">
            <div class="col-12 filters">
            <?php echo facetwp_display( 'sort' ); ?>
            </div>
          </div>
        </div>
      <div class="col-12 facetwp-template" >

        <div class="row grid">
          <div class="grid-sizer"></div>
            <div class="gutter-sizer"></div>

  <?php get_template_part('templates/unit', 'cars'); ?>

    </div>
    </div>

    </div>
  </div>
</section>
