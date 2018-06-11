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
echo '<div class="cars-slider_item-option car-option-' . $attribute_slug . '"><h6>Doors:<span class="attribute">' . $text . '</span></h6></div>';
}
add_action( 'woocommerce_attribute', 'show_attributes_doors', 10 );

function show_attributes_passengers() {
global $product;
$product_id = $product->get_id();
$attribute_slug = 'passengers';
$array = wc_get_product_terms( $product_id , 'pa_' . $attribute_slug, array( 'fields' => 'names' ) );
$text = array_shift( $array );
echo '<div class="cars-slider_item-option car-option-' . $attribute_slug . '"><h6>passengers:<span class="attribute">' . $text . '</span></h6></div>';
}
add_action( 'woocommerce_attribute', 'show_attributes_passengers', 20 );

function show_attributes_luggage() {
global $product;
$product_id = $product->get_id();
$attribute_slug = 'luggage';
$array = wc_get_product_terms( $product_id , 'pa_' . $attribute_slug, array( 'fields' => 'names' ) );
$text = array_shift( $array );
echo '<div class="cars-slider_item-option car-option-' . $attribute_slug . '"><h6>luggage:<span class="attribute">' . $text . '</span></h6></div>';
}
add_action( 'woocommerce_attribute', 'show_attributes_luggage', 30 );

function show_attributes_transmission() {
global $product;
$product_id = $product->get_id();
$attribute_slug = 'transmission';
$array = wc_get_product_terms( $product_id , 'pa_' . $attribute_slug, array( 'fields' => 'names' ) );
$text = array_shift( $array );
echo '<div class="cars-slider_item-option car-option-' . $attribute_slug . '"><h6>transmission:<span class="attribute">' . $text . '</span></h6></div>';
}
add_action( 'woocommerce_attribute', 'show_attributes_transmission', 40 );

function show_attributes_air_conditioning() {
global $product;
$product_id = $product->get_id();
$attribute_slug = 'air-conditioning';
$array = wc_get_product_terms( $product_id , 'pa_' . $attribute_slug, array( 'fields' => 'names' ) );
$text = array_shift( $array );
echo '<div class="cars-slider_item-option car-option-' . $attribute_slug . '"><h6>air condtitoning:<span class="attribute">' . $text . '</span></h6></div>';
}
add_action( 'woocommerce_attribute', 'show_attributes_air_conditioning', 50 );

function show_attributes_drive_wheel() {
global $product;
$product_id = $product->get_id();
$attribute_slug = 'drive-wheel';
$array = wc_get_product_terms( $product_id , 'pa_' . $attribute_slug, array( 'fields' => 'names' ) );
$text = array_shift( $array );
echo '<div class="cars-slider_item-option car-option-' . $attribute_slug . '"><h6>Drive wheel:<span class="attribute">' . $text . '</span></h6></div>';
}
add_action( 'woocommerce_attribute', 'show_attributes_drive_wheel', 60 );




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
 * Calendar wrapper
 */
 function woocommerce_before_booking_calendar_open() {
     echo '<div class="col-12 p-0">';
 }
 add_action( 'woocommerce_before_booking_calendar', 'woocommerce_before_booking_calendar_open', 10 );

 function woocommerce_after_booking_calendar_close() {
     echo '</div>';
 }
 add_action( 'woocommerce_after_booking_calendar', 'woocommerce_after_booking_calendar_close', 40 );

/**
 * The following hook will add a input field right before "add to cart button"
 * will be used for getting Your first name
 */
 function add_before_your_first_name_field() {
   echo '<div class="col-8 p-0">';
     echo '<fieldset class="second_step">';
     echo '<div class="container">';
     echo '<div class="row">';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_before_your_first_name_field', 8 );


  function pick_up_field() {
?>

  <div class="col-12">
    <div class="row ">
      <div class="col-8 pl-0">
        <div class="input-group addon">
          <span class="input-group-addon" id="basic-addon2"><i class="fa fa-map-marker"></i> Pick-up</span>
        <select class="form-control js-example-tags" style="width: 82%">
          <option selected="selected">Santorini Airport</option>
          <option>Santorini Port</option>

        </select>
        </div>
      </div>
      <div class="col-4">
        <div class="input-group addon">
          <span class="input-group-addon" id="basic-addon1"><i class="fa fa-clock-o"></i></span>
        <select class="form-control select-time" style="width: 82%">
<option value="12:00 am">12:00 am</option><option value="12:15 am">12:15 am</option><option value="12:30 am">12:30 am</option><option value="12:45 am">12:45 am</option><option value="1:00 am">1:00 am</option><option value="1:15 am">1:15 am</option><option value="1:30 am">1:30 am</option><option value="1:45 am">1:45 am</option><option value="2:00 am">2:00 am</option><option value="2:15 am">2:15 am</option><option value="2:30 am">2:30 am</option><option value="2:45 am">2:45 am</option><option value="3:00 am">3:00 am</option><option value="3:15 am">3:15 am</option><option value="3:30 am">3:30 am</option><option value="3:45 am">3:45 am</option><option value="4:00 am">4:00 am</option><option value="4:15 am">4:15 am</option><option value="4:30 am">4:30 am</option><option value="4:45 am">4:45 am</option><option value="5:00 am">5:00 am</option><option value="5:15 am">5:15 am</option><option value="5:30 am">5:30 am</option><option value="5:45 am">5:45 am</option><option value="6:00 am">6:00 am</option><option value="6:15 am">6:15 am</option><option value="6:30 am">6:30 am</option><option value="6:45 am">6:45 am</option><option value="7:00 am">7:00 am</option><option value="7:15 am">7:15 am</option><option value="7:30 am">7:30 am</option><option value="7:45 am">7:45 am</option><option value="8:00 am">8:00 am</option><option value="8:15 am">8:15 am</option><option value="8:30 am">8:30 am</option><option value="8:45 am">8:45 am</option><option value="9:00 am">9:00 am</option><option value="9:15 am">9:15 am</option><option value="9:30 am">9:30 am</option><option value="9:45 am">9:45 am</option><option value="10:00 am">10:00 am</option><option value="10:15 am">10:15 am</option><option value="10:30 am">10:30 am</option><option value="10:45 am">10:45 am</option><option value="11:00 am">11:00 am</option><option value="11:15 am">11:15 am</option><option value="11:30 am">11:30 am</option><option value="11:45 am">11:45 am</option><option value="12:00 pm" selected="selected">12:00 pm</option><option value="12:15 pm">12:15 pm</option><option value="12:30 pm">12:30 pm</option><option value="12:45 pm">12:45 pm</option><option value="1:00 pm">1:00 pm</option><option value="1:15 pm">1:15 pm</option><option value="1:30 pm">1:30 pm</option><option value="1:45 pm">1:45 pm</option><option value="2:00 pm">2:00 pm</option><option value="2:15 pm">2:15 pm</option><option value="2:30 pm">2:30 pm</option><option value="2:45 pm">2:45 pm</option><option value="3:00 pm">3:00 pm</option><option value="3:15 pm">3:15 pm</option><option value="3:30 pm">3:30 pm</option><option value="3:45 pm">3:45 pm</option><option value="4:00 pm">4:00 pm</option><option value="4:15 pm">4:15 pm</option><option value="4:30 pm">4:30 pm</option><option value="4:45 pm">4:45 pm</option><option value="5:00 pm">5:00 pm</option><option value="5:15 pm">5:15 pm</option><option value="5:30 pm">5:30 pm</option><option value="5:45 pm">5:45 pm</option><option value="6:00 pm">6:00 pm</option><option value="6:15 pm">6:15 pm</option><option value="6:30 pm">6:30 pm</option><option value="6:45 pm">6:45 pm</option><option value="7:00 pm">7:00 pm</option><option value="7:15 pm">7:15 pm</option><option value="7:30 pm">7:30 pm</option><option value="7:45 pm">7:45 pm</option><option value="8:00 pm">8:00 pm</option><option value="8:15 pm">8:15 pm</option><option value="8:30 pm">8:30 pm</option><option value="8:45 pm">8:45 pm</option><option value="9:00 pm">9:00 pm</option><option value="9:15 pm">9:15 pm</option><option value="9:30 pm">9:30 pm</option><option value="9:45 pm">9:45 pm</option><option value="10:00 pm">10:00 pm</option><option value="10:15 pm">10:15 pm</option><option value="10:30 pm">10:30 pm</option><option value="10:45 pm">10:45 pm</option><option value="11:00 pm">11:00 pm</option><option value="11:15 pm">11:15 pm</option><option value="11:30 pm">11:30 pm</option><option value="11:45 pm">11:45 pm</option>

        </select>
        </div>
      </div>
    </div>

    <div class="row ">
      <div class="col-8 pl-0">
        <div class="input-group addon">
          <span class="input-group-addon" id="basic-addon1"><i class="fa fa-map-marker"></i> Drop-off</span>
        <select class="form-control js-example-tags" style="width: 82%">
          <option selected="selected">Santorini Airport</option>
          <option>Santorini Port</option>

        </select>
        </div>
      </div>
      <div class="col-4">
        <div class="input-group addon">
          <span class="input-group-addon" id="basic-addon1"><i class="fa fa-clock-o"></i></span>
        <select class="form-control select-time" style="width: 82%">
<option value="12:00 am">12:00 am</option><option value="12:15 am">12:15 am</option><option value="12:30 am">12:30 am</option><option value="12:45 am">12:45 am</option><option value="1:00 am">1:00 am</option><option value="1:15 am">1:15 am</option><option value="1:30 am">1:30 am</option><option value="1:45 am">1:45 am</option><option value="2:00 am">2:00 am</option><option value="2:15 am">2:15 am</option><option value="2:30 am">2:30 am</option><option value="2:45 am">2:45 am</option><option value="3:00 am">3:00 am</option><option value="3:15 am">3:15 am</option><option value="3:30 am">3:30 am</option><option value="3:45 am">3:45 am</option><option value="4:00 am">4:00 am</option><option value="4:15 am">4:15 am</option><option value="4:30 am">4:30 am</option><option value="4:45 am">4:45 am</option><option value="5:00 am">5:00 am</option><option value="5:15 am">5:15 am</option><option value="5:30 am">5:30 am</option><option value="5:45 am">5:45 am</option><option value="6:00 am">6:00 am</option><option value="6:15 am">6:15 am</option><option value="6:30 am">6:30 am</option><option value="6:45 am">6:45 am</option><option value="7:00 am">7:00 am</option><option value="7:15 am">7:15 am</option><option value="7:30 am">7:30 am</option><option value="7:45 am">7:45 am</option><option value="8:00 am">8:00 am</option><option value="8:15 am">8:15 am</option><option value="8:30 am">8:30 am</option><option value="8:45 am">8:45 am</option><option value="9:00 am">9:00 am</option><option value="9:15 am">9:15 am</option><option value="9:30 am">9:30 am</option><option value="9:45 am">9:45 am</option><option value="10:00 am">10:00 am</option><option value="10:15 am">10:15 am</option><option value="10:30 am">10:30 am</option><option value="10:45 am">10:45 am</option><option value="11:00 am">11:00 am</option><option value="11:15 am">11:15 am</option><option value="11:30 am">11:30 am</option><option value="11:45 am">11:45 am</option><option value="12:00 pm" selected="selected">12:00 pm</option><option value="12:15 pm">12:15 pm</option><option value="12:30 pm">12:30 pm</option><option value="12:45 pm">12:45 pm</option><option value="1:00 pm">1:00 pm</option><option value="1:15 pm">1:15 pm</option><option value="1:30 pm">1:30 pm</option><option value="1:45 pm">1:45 pm</option><option value="2:00 pm">2:00 pm</option><option value="2:15 pm">2:15 pm</option><option value="2:30 pm">2:30 pm</option><option value="2:45 pm">2:45 pm</option><option value="3:00 pm">3:00 pm</option><option value="3:15 pm">3:15 pm</option><option value="3:30 pm">3:30 pm</option><option value="3:45 pm">3:45 pm</option><option value="4:00 pm">4:00 pm</option><option value="4:15 pm">4:15 pm</option><option value="4:30 pm">4:30 pm</option><option value="4:45 pm">4:45 pm</option><option value="5:00 pm">5:00 pm</option><option value="5:15 pm">5:15 pm</option><option value="5:30 pm">5:30 pm</option><option value="5:45 pm">5:45 pm</option><option value="6:00 pm">6:00 pm</option><option value="6:15 pm">6:15 pm</option><option value="6:30 pm">6:30 pm</option><option value="6:45 pm">6:45 pm</option><option value="7:00 pm">7:00 pm</option><option value="7:15 pm">7:15 pm</option><option value="7:30 pm">7:30 pm</option><option value="7:45 pm">7:45 pm</option><option value="8:00 pm">8:00 pm</option><option value="8:15 pm">8:15 pm</option><option value="8:30 pm">8:30 pm</option><option value="8:45 pm">8:45 pm</option><option value="9:00 pm">9:00 pm</option><option value="9:15 pm">9:15 pm</option><option value="9:30 pm">9:30 pm</option><option value="9:45 pm">9:45 pm</option><option value="10:00 pm">10:00 pm</option><option value="10:15 pm">10:15 pm</option><option value="10:30 pm">10:30 pm</option><option value="10:45 pm">10:45 pm</option><option value="11:00 pm">11:00 pm</option><option value="11:15 pm">11:15 pm</option><option value="11:30 pm">11:30 pm</option><option value="11:45 pm">11:45 pm</option>

        </select>
        </div>
      </div>
    </div>
    <hr/>
  </div>




<?  }
  add_action( 'woocommerce_before_add_to_cart_button', 'pick_up_field', 28 );


 function add_your_first_name_field() {
    echo '<div class="col-lg-8">';
    echo '<div class="row">';
   echo '<div class="input-group col-lg-6 p-0"><div class="input-group addon">';
     echo '<span class="input-group-addon" id="basic-addon1"><i class="fa fa-user-circle"></i></span>';
     echo '<input name="your-first-name" type="text" class="form-control" id="inputName" placeholder="Your First Name" required>';
 echo '</div></div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_your_first_name_field', 29 );

 function add_your_last_name_field() {
   echo '<div class="input-group col-lg-6 p-0"><div class="input-group addon">';
     echo '<span class="input-group-addon" id="basic-addon1"><i class="fa fa-user-circle-o"></i></span>';
     echo '<input type="text" class="form-control" name="your-last-name" placeholder="Last Name" value="" />';
     echo '</div></div>';
 //echo '</div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_your_last_name_field', 30 );

 function add_your_email_field() {
   echo '<div class="input-group col-lg-6 p-0"><div class="input-group addon">';
     echo '<span class="input-group-addon" id="basic-addon1"><i class="fa fa-envelope"></i></span>';
     echo '<input type="email" class="form-control" name="your-email" placeholder="email" value="" />';
     echo '</div></div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_your_email_field', 31 );

 function add_your_phone_field() {
   echo '<div class="input-group col-lg-6 p-0"><div class="input-group addon">';
     echo '<span class="input-group-addon" id="basic-addon1"><i class="fa fa-phone-square"></i></span>';
   echo '<input type="text" class="form-control" name="your-phone" placeholder="Phone" value="" />';
   echo '</div></div></div></div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_your_phone_field', 32 );

 function add_additional_info_field() {
   echo '<div class="input-group col-lg-4 ml-0 pr-0">';
   echo '<textarea name="pick-up" class="input-text form-control additional-info addon" data-require-pair="" id="order_comments" placeholder="Additional info" rows="5" cols="5"></textarea>';
   echo '</div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_additional_info_field', 33 );

 function add_after_your_first_name_field() {
   echo '</div>';
    echo '</div>';
     echo '</fieldset>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_after_your_first_name_field', 34 );

  function save_your_first_name_field( $cart_item_data, $product_id ) {
      if( isset( $_REQUEST['your-first-name'] ) ) {
          $cart_item_data[ 'your_first_name' ] = $_REQUEST['your-first-name'];
          /* below statement make sure every add to cart action as unique line item */
          $cart_item_data['unique_key'] = md5( microtime().rand() );
      }
      return $cart_item_data;
  }
  add_action( 'woocommerce_add_cart_item_data', 'save_your_first_name_field', 10, 2 );

  function save_your_last_name_field( $cart_item_data, $product_id ) {
      if( isset( $_REQUEST['your-last-name'] ) ) {
          $cart_item_data[ 'your_last_name' ] = $_REQUEST['your-last-name'];
          /* below statement make sure every add to cart action as unique line item */
          $cart_item_data['unique_key'] = md5( microtime().rand() );
      }
      return $cart_item_data;
  }
 add_action( 'woocommerce_add_cart_item_data', 'save_your_last_name_field', 10, 3 );


 function save_your_email_field( $cart_item_data, $product_id ) {
     if( isset( $_REQUEST['your-email'] ) ) {
         $cart_item_data[ 'your_email' ] = $_REQUEST['your-email'];
         /* below statement make sure every add to cart action as unique line item */
         $cart_item_data['unique_key'] = md5( microtime().rand() );
     }
     return $cart_item_data;
 }
 add_action( 'woocommerce_add_cart_item_data', 'save_your_email_field', 10, 5 );

 function save_your_phone_field( $cart_item_data, $product_id ) {
     if( isset( $_REQUEST['your-phone'] ) ) {
         $cart_item_data[ 'your_phone' ] = $_REQUEST['your-phone'];
         /* below statement make sure every add to cart action as unique line item */
         $cart_item_data['unique_key'] = md5( microtime().rand() );
     }
     return $cart_item_data;
 }
 add_action( 'woocommerce_add_cart_item_data', 'save_your_phone_field', 10, 6 );

 function save_intime_field( $cart_item_data, $product_id ) {
     if( isset( $_REQUEST['in-time'] ) ) {
         $cart_item_data[ 'in_time' ] = $_REQUEST['in-time'];
         /* below statement make sure every add to cart action as unique line item */
         $cart_item_data['unique_key'] = md5( microtime().rand() );
     }
     return $cart_item_data;
 }
 add_action( 'woocommerce_add_cart_item_data', 'save_intime_field', 10, 7 );

  function render_on_cart_and_checkout_your_first_name( $cart_data, $cart_item = null ) {
      $custom_items = array();
      /* Woo 2.4.2 updates */
      if( !empty( $cart_data ) ) {
          $custom_items = $cart_data;
      }
      if( isset( $cart_item['your_first_name'] ) ) {
          $custom_items[] = array( "name" => 'Your first name', "value" => $cart_item['your_first_name'] );
      }
      return $custom_items;
  }
  add_filter( 'woocommerce_get_item_data', 'render_on_cart_and_checkout_your_first_name', 10, 2 );

  function render_on_cart_and_checkout_your_last_name( $cart_data, $cart_item = null ) {
      $custom_items = array();
      /* Woo 2.4.2 updates */
      if( !empty( $cart_data ) ) {
          $custom_items = $cart_data;
      }
      if( isset( $cart_item['your_last_name'] ) ) {
          $custom_items[] = array( "name" => 'Your last name', "value" => $cart_item['your_last_name'] );
      }
      return $custom_items;
  }
 add_filter( 'woocommerce_get_item_data', 'render_on_cart_and_checkout_your_last_name', 10, 3 );

 function render_on_cart_and_checkout_your_email( $cart_data, $cart_item = null ) {
     $custom_items = array();
     /* Woo 2.4.2 updates */
     if( !empty( $cart_data ) ) {
         $custom_items = $cart_data;
     }
     if( isset( $cart_item['your_email'] ) ) {
         $custom_items[] = array( "name" => 'Your email', "value" => $cart_item['your_email'] );
     }
     return $custom_items;
 }

 add_filter( 'woocommerce_get_item_data', 'render_on_cart_and_checkout_your_email', 10, 4 );

 function render_on_cart_and_checkout_your_phone( $cart_data, $cart_item = null ) {
     $custom_items = array();
     /* Woo 2.4.2 updates */
     if( !empty( $cart_data ) ) {
         $custom_items = $cart_data;
     }
     if( isset( $cart_item['your_phone'] ) ) {
         $custom_items[] = array( "name" => 'Your Phone', "value" => $cart_item['your_phone'] );
     }
     return $custom_items;
 }

 add_filter( 'woocommerce_get_item_data', 'render_on_cart_and_checkout_your_phone', 10, 5 );


 function render_on_cart_and_checkout_in_time( $cart_data, $cart_item = null ) {
     $custom_items = array();
     /* Woo 2.4.2 updates */
     if( !empty( $cart_data ) ) {
         $custom_items = $cart_data;
     }
     if( isset( $cart_item['in_time'] ) ) {
         $custom_items[] = array( "name" => 'In time', "value" => $cart_item['in_time'] );
     }
     return $custom_items;
 }

 add_filter( 'woocommerce_get_item_data', 'render_on_cart_and_checkout_in_time', 10, 6 );

 add_action( 'wp_enqueue_scripts', 'child_manage_woocommerce_styles', 99 );

 function child_manage_woocommerce_styles() {
 	//remove generator meta tag
 	remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );

 	//first check that woo exists to prevent fatal errors
 	if ( function_exists( 'is_woocommerce' ) ) {
 		//dequeue scripts and styles
 		if ( ! is_cart() && ! is_checkout() ) {
 			wp_dequeue_style( 'woocommerce_frontend_styles' );
 			wp_dequeue_style( 'woocommerce_fancybox_styles' );
 			wp_dequeue_style( 'woocommerce_chosen_styles' );
 			wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
     wp_dequeue_style( 'woocommerce-layout' );
     wp_dequeue_style( 'woocommerce-smallscreen' );
    wp_dequeue_style('gforms_css');
    //  wp_dequeue_script( 'datepicker' );
 			wp_dequeue_script( 'wc_price_slider' );
 			wp_dequeue_script( 'wc-single-product' );
 	//		wp_dequeue_script( 'wc-add-to-cart' );
 		//	wp_dequeue_script( 'wc-cart-fragments' );
 			wp_dequeue_script( 'wc-checkout' );
 		//	wp_dequeue_script( 'wc-add-to-cart-variation' );
 			wp_dequeue_script( 'wc-single-product' );
 		//	wp_dequeue_script( 'wc-cart' );
 			wp_dequeue_script( 'wc-chosen' );
 			wp_dequeue_script( 'woocommerce' );
 			wp_dequeue_script( 'prettyPhoto' );
 			wp_dequeue_script( 'prettyPhoto-init' );
 			wp_dequeue_script( 'jquery-blockui' );
 			wp_dequeue_script( 'jquery-placeholder' );
 			wp_dequeue_script( 'fancybox' );
      	wp_dequeue_script( 'photoswipe' );
 	//		wp_dequeue_script( 'jqueryui' );

 		}
 	}

 }
 // Hook in specified cart item data
 add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

 function custom_override_checkout_fields( $fields  ) {

$stored_value = "something pulled from the DB";
   unset($fields['billing']['billing_address_1']);
   unset($fields['billing']['billing_address_2']);
   unset($fields['billing']['billing_postcode']);
   unset($fields['billing']['billing_state']);
   unset($fields['billing']['billing_company']);
   unset($fields['billing']['billing_address_2']);
   unset($fields['billing']['billing_country']);
   unset($fields['billing']['billing_city']);
 $fields['order']['order_comments']['placeholder'] = 'My new placeholder';

     return $fields;
 }
 add_filter('woocommerce_email_order_meta_keys', 'my_custom_order_meta_keys');

 function my_custom_order_meta_keys( $keys ) {
      $keys[] = 'Your Phone'; // This will look for a custom field called 'Tracking Code' and add it to emails
      return $keys;
 }

 //*Add custom redirection
add_action( 'template_redirect', 'wc_custom_redirect_after_purchase' );
function wc_custom_redirect_after_purchase() {
 global $wp;
  if ( is_checkout() && ! empty( $wp->query_vars['order-received'] ) ) {
   wp_redirect( 'https://eboy.gr/coolcars/thank-you-for-your-order/' );
   exit;
 }
}

add_action('wp_footer', 'my_custom_wc_button_script');
function my_custom_wc_button_script() {
	?>

	<?php
}
// here's where we define the ajax processing functions.. the part after wp_ajax, and wp_ajax_nopriv has to match the action we used in our javascript
add_action('wp_ajax_my_custom_add_to_cart', "my_custom_add_to_cart");
add_action('wp_ajax_nopriv_my_custom_add_to_cart', "my_custom_add_to_cart");


/**
 * When an item is added to the cart, remove other products
 */
function so_27030769_maybe_empty_cart( $valid, $product_id, $quantity ) {

    if( ! empty ( WC()->cart->get_cart() ) && $valid ){
        WC()->cart->empty_cart();
        wc_add_notice( 'Whoa hold up. You can only have 1 item in your cart', 'error' );
    }

    return $valid;

}
add_filter( 'woocommerce_add_to_cart_validation', 'so_27030769_maybe_empty_cart', 10, 3 );


// Skip the cart and redirect to check out url when clicking on Add to cart
add_filter ( 'add_to_cart_redirect', 'redirect_to_checkout' );
function redirect_to_checkout() {

	global $woocommerce;
	// Remove the default `Added to cart` message
	wc_clear_notices();
	return $woocommerce->cart->get_checkout_url();

}
// Global redirect to check out when hitting cart page
add_action( 'template_redirect', 'redirect_to_checkout_if_cart' );
function redirect_to_checkout_if_cart() {

	if ( !is_cart() ) return;
	global $woocommerce;
    // Redirect to check out url
	wp_redirect( $woocommerce->cart->get_checkout_url(), '301' );
	exit;

}
// Empty cart each time you click on add cart to avoid multiple element selected
add_action( 'woocommerce_add_cart_item_data', 'clear_cart', 0 );
function clear_cart () {
	global $woocommerce;
	$woocommerce->cart->empty_cart();
}
// Edit default add_to_cart button text
add_filter( 'add_to_cart_text', 'woo_custom_cart_button_text' );
add_filter( 'woocommerce_product_single_add_to_cart_text', 'custom_cart_button_text' );
function custom_cart_button_text() {
	return __( 'Buy', 'woocommerce' );
}
// Unset all options related to the cart
update_option( 'woocommerce_cart_redirect_after_add', 'no' );
update_option( 'woocommerce_enable_ajax_add_to_cart', 'no' );






function product_carousel() {
  global $product;
  $number = 1;
  ?>

  <div id="main-slider" class="carousel slide mt-4" data-ride="carousel" data-interval="false">

              <?php $args = array(
                 'posts_per_page' => 5,
                 'post_type' => 'product'
              );
              $slider = new WP_Query($args);
              if($slider->have_posts()):
              $count = $slider->found_posts;

              ?>
              <!--   <ol class="carousel-indicators">
                    <?php for($i = 0; $i < $count ;  $i++) { ?>
                           <li data-target="#main-slider" data-slide-to="<?php echo $i; ?>" class="<?php echo ($i == 0) ? 'active' : ''?>"></li>
                     <?php } ?>
                 </ol> .carousel-indicators-->

                 <div class="carousel-inner" role="listbox">
                    <?php $i = 0; while($slider->have_posts()): $slider->the_post();
                    $title = get_the_title();
                  //  $price = get_post_meta( get_the_ID(), '_regular_price', true);
                    ?>
                        <div class="carousel-item <?php echo ($i == 0) ? 'active' : ''?>">

                          <div class="container">
                            <div class="row">
                              <div class="col-12 p-0">

                              <div class="d-flex flex-wrap justify-content-between align-content-center">
                                <div class="col-12 col-lg-2 p-2">
                                  <div class="d-flex flex-row flex-wrap align-items-center">
                                    <div class="col-8 col-lg-12 px-2"><h2><?php echo $title; ?></h2></div>
                                  </hr>
                                    <div class="col-4 col-lg-12 px-2 mx-auto"><?php wc_get_template( 'loop/price.php' ); ?> </div>
                                    <div class="col-12 p-2">
                                      <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample<?php echo $i; ?>" role="button" aria-expanded="false" aria-controls="collapseExample">
                                        Book Now!
                                      </a></div>
                                  </div>

                                </div>
                                <div class="col-12 col-lg-8 p-2 cars" data-href="<?php echo get_permalink(); ?>">
                                  <?php the_post_thumbnail( 'slider', array(
                                                                          'class' => 'mx-auto d-block img-fluid',
                                                                          'alt' => get_the_title() ) ) ; ?>
                                </div>
                                <div class="col-12 col-lg-2 p-2">
                                  <?php do_action ( 'woocommerce_attribute' );  ?>
                                </div>
                                <div class="col-12 col-lg-8 p-2">

                                </div>
                                </div>
                              </div>
                              </div>
                              </div>


                                <div class="row">
                                  <div class="col-12 collapse" id="collapseExample<?php echo $i; ?>">
                              <?php echo wc_get_template_part( 'content', 'single-car' ); ?>
                              </div>
                              </div>



                        </div><!--.carousel-item-->
                     <?php $i++; endwhile; ?>
                 </div> <!--.carouse-inner-->


                 <a href="#main-slider" class="carousel-control-prev" data-slide="prev">
                     <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                     <span class="sr-only">Previous</span>
                 </a>
                 <a href="#main-slider" class="carousel-control-next" data-slide="next">
                     <span class="carousel-control-next-icon" aria-hidden="true"></span>
                     <span class="sr-only">Next</span>
                 </a>

              <?php endif;  wp_reset_postdata(); ?>
           </div>
<?
}
add_action('carhub_product_carousel', 'product_carousel', 10);

function product_form() {
  ?>

<?
}
add_action('carhub_product_carousel', 'product_form', 20);

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
