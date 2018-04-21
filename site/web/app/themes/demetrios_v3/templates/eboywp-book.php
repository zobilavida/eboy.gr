<div class="eboywp-template container eboywp_t_radios">
  <div class="row">
    <div class="col-12 text-center select_city">
    Select a City to view available Stores
    </div>
  <div class="col-12 text-center p-3">
  Available Stores
  </div>
  </div>
  <?php while ( have_posts() ) : the_post(); ?>
  <?php
  $email_2 = get_field( "email_2" );
  $street_address = get_field( "street_address" );
  $phone = get_field( "phone" );
  $city = get_field( "city" );
  $country = get_field( "country" );
  $term_list = wp_get_post_terms($post->ID, 'store_cat', array("fields" => "all"));
  ?>

  <div class="row ">
  <div class="col-12 p-3">
  <div class="card">
  <h5 class="card-header"><?php the_title(); ?></h5>
    <div class="card-body">

      <h5 class="card-title"><input type="radio" name="store_name" id="store_name_id" value="<?php echo $email_2; ?>" ><?php echo $street_address; ?>, <?php echo $country; ?></h5>

       <footer class="blockquote-footer">
  <?php foreach($term_list as $term_single) {

  echo $term_single->name;
  echo ' - ';
  } ?>
  <span class="float-right"><a href="#" class="btn btn-primary">Get Directions</a></span>
  </footer>


    </div>
  </div>

  </div>
  </div>
  <?php endwhile; ?>
  </div>
