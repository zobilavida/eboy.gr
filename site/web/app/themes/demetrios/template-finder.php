<?php
/**
 * Template Name: Store Finder Template
 */
?>
<section class="store_finder">
<div class="container">
  <div class="row">
    <div class="col-6">
  <?php echo eboywp_display( 'facet', 'country' ); ?>
  </div>
  <div class="col-6">
<?php echo eboywp_display( 'facet', 'city' ); ?>
</div>
</div>
</div>
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
  <?php echo eboywp_display( 'facet', 'location' ); ?>
  </div>
</div>
</div>
<div class="container">
  <div class="row">
    <div class="col-12">
  <?php echo eboywp_display( 'template', 'stores' ); ?>
  </div>
</div>
</div>
</section>
