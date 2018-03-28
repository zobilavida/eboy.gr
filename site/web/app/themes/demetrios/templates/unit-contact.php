<section id="contact">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 text-center">
        <h2 class="section-heading text-uppercase">Contact Us</h2>
        <h3 class="section-subheading text-muted">Lorem ipsum dolor sit amet consectetur.</h3>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12 p-0">

<div id="wrapper">
   <div id="google_map">
     <?php echo eboywp_display( 'facet', 'location' ); ?>
   </div>

   <div  class="container" id="over_map">

     <div class="row">
       <div class="col-lg-8">
         <div class="row">
           <div class="col-lg-4">
     <?php echo eboywp_display( 'facet', 'country' ); ?>
     </div>
     <div class="col-lg-4">
     <?php echo eboywp_display( 'facet', 'city' ); ?>
     </div>
     <div class="col-lg-4">
     <?php echo eboywp_display( 'facet', 'store_title' ); ?>
     </div>
     <div class="row">
    <div class="col-lg-12">
    <?php echo eboywp_display( 'facet', 'store_category' ); ?>
    </div>
    </div>
     </div>
      <div class="row">
     <div class="col-lg-12">
     <?php echo eboywp_display( 'facet', 'proximity' ); ?>
     </div>
     </div>

      </div>
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
      </div>
    </div>
  </div>
</section>
