  <div class="row facetwp_t_radios">

  <div class="col-12 text-center p-3">
  Available Stores
  </div>
  </div>
  <?php while ( have_posts() ) : the_post(); ?>
  <?php
  $email_2 = get_field( "email_2" );
  $street_address = get_field( "street_address" );
  $phone = get_field( "phone" );
  $phone_icon = '<img class="ico" src=" ' .get_template_directory_uri() .'/dist/images/phone.svg">';
  $directions_icon = '<img class="ico svg-convert" src=" ' .get_template_directory_uri() .'/dist/images/directions.svg">';
  $city = get_field( "city" );
  $country = get_field( "country" );
  $term_list = wp_get_post_terms($post->ID, 'store_cat', array("fields" => "all"));
  $location = get_field('location');
  ?>

  <div class="row ">
  <div class="col-12 p-3">
  <div class="card">
  <h5 class="card-header"><?php the_title(); ?></h5>
    <div class="card-body">

      <h6 class="card-title"><input type="radio" name="store_name" id="store_name_id" value="<?php echo $email_2; ?>" ><?php echo $street_address; ?>, <?php echo $country; ?></h6>

       <footer class="blockquote-footer">
  <?php foreach($term_list as $term_single) {

  echo $term_single->name;
  echo ' - ';
  } ?>
  <span class="float-right">
    <a class="btn btn-outline-primary btn-sm" href="tel:<?php echo $phone; ?>"><?php echo $phone_icon; ?> <?php echo $phone; ?></a>
 <a class="btn btn-primary btn-sm" href="https://www.google.com/maps?saddr=Current+Location&daddr=<?php  echo $location['lat'] . ',' . $location['lng']; ?>"><?php echo $directions_icon; ?> <?php _e('Get Directions','demetrios'); ?></a>
</span>
  </footer>


    </div>
  </div>

  </div>
  </div>
  <?php endwhile; ?>
