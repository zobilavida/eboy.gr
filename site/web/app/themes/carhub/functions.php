<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/wrapper.php',   // Theme wrapper class
  'lib/customizer.php' // Theme customizer
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

// Register Custom Navigation Walker (Soil)
require_once('wp_bootstrap_navwalker.php');
//declare your new menu
register_nav_menus( array(
    'primary' => __( 'Primary Menu', 'sage' ),
) );

// Add svg & swf support
function cc_mime_types( $mimes ){
    $mimes['svg'] = 'image/svg+xml';
    $mimes['swf']  = 'application/x-shockwave-flash';

    return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );



//enable logo uploading via the customize theme page

function themeslug_theme_customizer( $wp_customize ) {
    $wp_customize->add_section( 'themeslug_logo_section' , array(
    'title'       => __( 'Logo', 'themeslug' ),
    'priority'    => 30,
    'description' => 'Upload a logo to replace the default site name and description     in the header',
) );
$wp_customize->add_setting( 'themeslug_logo' );
$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize,     'themeslug_logo', array(
    'label'    => __( 'Logo', 'themeslug' ),
    'section'  => 'themeslug_logo_section',
    'settings' => 'themeslug_logo',
    'extensions' => array( 'jpg', 'jpeg', 'gif', 'png', 'svg' ),
) ) );
}
add_action('customize_register', 'themeslug_theme_customizer');



function show_attributes_doors() {
global $product;
$product_id = $product->get_id();
$attribute_slug = 'doors';
$array = wc_get_product_terms( $product_id , 'pa_' . $attribute_slug, array( 'fields' => 'names' ) );
$text = array_shift( $array );
echo '<div class="cars-slider_item-option car-option-' . $attribute_slug . '"><h5>Doors:' . $text . '</h5></div>';
}
add_action( 'woocommerce_attribute_doors', 'show_attributes_doors' );

function show_attributes_passengers() {
global $product;
$product_id = $product->get_id();
$attribute_slug = 'passengers';
$array = wc_get_product_terms( $product_id , 'pa_' . $attribute_slug, array( 'fields' => 'names' ) );
$text = array_shift( $array );
echo '<div class="cars-slider_item-option car-option-' . $attribute_slug . '"><h5>passengers:' . $text . '</h5></div>';
}
add_action( 'woocommerce_attribute_passengers', 'show_attributes_passengers' );

function show_attributes_luggage() {
global $product;
$product_id = $product->get_id();
$attribute_slug = 'luggage';
$array = wc_get_product_terms( $product_id , 'pa_' . $attribute_slug, array( 'fields' => 'names' ) );
$text = array_shift( $array );
echo '<div class="cars-slider_item-option car-option-' . $attribute_slug . '"><h5>luggage:' . $text . '</h5></div>';
}
add_action( 'woocommerce_attribute_luggage', 'show_attributes_luggage' );

function show_attributes_transmission() {
global $product;
$product_id = $product->get_id();
$attribute_slug = 'transmission';
$array = wc_get_product_terms( $product_id , 'pa_' . $attribute_slug, array( 'fields' => 'names' ) );
$text = array_shift( $array );
echo '<div class="cars-slider_item-option car-option-' . $attribute_slug . '"><h5>transmission:' . $text . '</h5></div>';
}
add_action( 'woocommerce_attribute_tansmission', 'show_attributes_transmission' );

function show_attributes_air_conditioning() {
global $product;
$product_id = $product->get_id();
$attribute_slug = 'air-conditioning';
$array = wc_get_product_terms( $product_id , 'pa_' . $attribute_slug, array( 'fields' => 'names' ) );
$text = array_shift( $array );
echo '<div class="cars-slider_item-option car-option-' . $attribute_slug . '"><h5>air condtitoning:' . $text . '</h5></div>';
}
add_action( 'woocommerce_attribute_air_conditioning', 'show_attributes_air_conditioning' );

add_filter( 'woocommerce_add_cart_item_data', 'ps_empty_cart', 10,  3);

function ps_empty_cart( $cart_item_data, $product_id, $variation_id ) {

    global $woocommerce;
    $woocommerce->cart->empty_cart();

    // Do nothing with the data and return
    return $cart_item_data;
}

remove_action ('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5, 0) ;
remove_action ('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40) ;

function woocommerce_template_loop_product_open() {
    echo '<div class="card">';
}
add_action ('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_open', 15) ;

function woocommerce_template_loop_product_close() {
    echo '</div>';
}
add_action ('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_close', 15) ;

add_action ('woocommerce_shop_loop_item_image', 'woocommerce_loop_item_image_open', 10) ;
function woocommerce_loop_item_image_open() {
    echo '<img class="card-img-top" src=" ">';
}




/**
 * The following hook will add a input field right before "add to cart button"
 * will be used for getting Your first name
 */

 function add_before_your_first_name_field() {
     echo '<fieldset class="wc-bookings-fields second_step hidden_form">';
     echo '<div class="form-group row">';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_before_your_first_name_field', 10 );

 function add_intime_field() {
    echo '<div class="row">';
    echo '<div class="col col-lg-6">';
    echo '<label>Check-in</label>';
    echo '<input type="text" name="in-time" value="12:30" class="in_time"/>';
    echo '</div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_intime_field', 11 );

 function add_outtime_field() {
   echo '<div class="col col-lg-6">';
   echo '<label>Check-out</label>';
   echo '<input type="text" name="out-time" value="12:30" class="out_time"/>';
   echo '</div>';
      echo '</div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_outtime_field', 12 );

 function add_location_in_start_field() {
  //echo '<div class="row">';
  echo '<div class="col-lg-6">';
   echo '<div class="row form-group">';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_start_field', 13 );

  function add_location_in_airport_field() {
    echo '<div class="col-lg-4 text-center custom-radio-airport">';
    echo '<input id="radio1" type="radio" name="pick-up" value="Airport" class="custom-radio" checked="">';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_airport_field', 14 );

  function add_location_in_airport_text_field() {
    echo '<div class="reveal-if-active">
    <textarea name="pick-up-airport" class="input-text form-control require-if-active pick_up_airport" id="order_comments" placeholder="Flight Number ex. PH 4238" rows="4" cols="5"></textarea>
    </div>
    </div>';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_airport_text_field', 15 );

  function add_location_in_port_field() {
    echo '<div class="col-lg-4 text-center custom-radio-port">';
    echo '<input id="radio2" type="radio" name="pick-up" value="Port" class="custom-radio">';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_port_field', 16 );

  function add_location_in_port_text_field() {
    echo '<div class="reveal-if-active">
    <textarea name="pick-up-airport" class="input-text form-control require-if-active port-position" data-require-pair="#pick-up-hotel" id="order_comments" placeholder="Boat Name" rows="4" cols="5"></textarea>
    </div>
    </div>';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_port_text_field', 17 );



  function add_location_in_custom_field() {
    echo '<div class="col-lg-4 text-center custom-radio-location">';
    echo '<input id="radio1" type="radio" name="pick-up" value="other_location" class="custom-radio">';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_custom_field', 18 );

  function add_location_in_custom_text_field() {
    echo '<div class="reveal-if-active">
    <textarea name="pick-up" class="input-text form-control require-if-active location-position" data-require-pair="#pick-up-hotel" id="order_comments" placeholder="" rows="4" cols="5"></textarea>
    </div>
    </div>';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_custom_text_field', 19 );



  function add_location_in_end_field() {
    echo '</div>';
    echo '</div>';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_end_field', 20 );



    function add_location_out_start_field() {
      echo '<div class="col-lg-6">';
      echo '<div class="row form-group">';
    }
    add_action( 'woocommerce_before_add_to_cart_button', 'add_location_out_start_field', 21 );

  function add_location_out_end_field() {
    echo '</div>';
    echo '</div>';
    //echo '</div>';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_out_end_field', 22 );

 function add_your_first_name_field() {
   echo '<div class="col-lg-6">';
     //echo '<label>Name</label>';
     echo '<input name="your-first-name" type="text" class="form-control" id="inputName" placeholder="Your First Name" required>';
 echo '</div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_your_first_name_field', 23 );

 function add_your_last_name_field() {
 echo '<div class="col-lg-6">';
 //echo '<div class="form-group">';
 //echo '<label>Last Name</label>';
     echo '<input type="text" name="your-last-name" placeholder="Last Name" value="" />';
 echo '</div>';
 //echo '</div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_your_last_name_field', 24 );


 function add_your_email_field() {
 echo '<div class="col-lg-6">';
 //echo '<label>Email</label>';
     echo '<input type="email" name="your-email" placeholder="email" value="" />';
 echo '</div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_your_email_field', 25 );

 function add_your_phone_field() {
 echo '<div class="col-lg-6">';
 //echo '<label>Phone</label>';

   echo '<input type="text" name="your-phone" placeholder="Phone" value="" />';
 echo '</div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_your_phone_field', 26 );





 function add_after_your_first_name_field() {
   echo '</div>';
     echo '</fieldset>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_after_your_first_name_field', 30 );
