<section id="intro">
  <div class="jumbotron bg-image">
  <div class="container">
    <h1><?php printf( esc_html__( '%s', 'sage' ), get_bloginfo ( 'title' ) ); ?></h1>
      <h2><?php printf( esc_html__( '%s', 'sage' ), get_bloginfo ( 'description' ) ); ?></h2>
  <div class="row mainform">
<div class="col-12 col-lg-4 location-field">
  <?php echo facetwp_display( 'facet', 'pickup' ); ?>
</div>
<div class="col-12 col-lg-5 availability-form">
  <?php echo facetwp_display( 'facet', 'date' ); ?>
  <!--   /range  <input class="range-cal" placeholder="Range" type="text"> -->
</div>
<div class="col-12 col-lg-2 lg-text-right">
 <?php echo facetwp_display( 'facet', 'manufacture' ); ?>
</div>
    </div>
      <div class="row no-gutters">
        <div class="col-12 col-lg-12 menu-box">
    <button onclick="FWP.refresh()" type="button" class="btn btn-outline-primary btn-round-lg btn-lg">
                  <span class="btn-label"><i class="fa fa-search"></i></span>
                  Search
                </button>
              </div>
              <div class="col-12 col-lg-12 menu-box">
                <?php echo facetwp_display( 'counts' ); ?>
                </div>

                </div>
    </div>
<!--   /container  -->
</div>
</section>
