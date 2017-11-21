<?php
/**
 * coolcars includes
 *
 * The $coolcars_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/coolcars/pull/1042
 */
$coolcars_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/wrapper.php',   // Theme wrapper class
  'lib/customizer.php', // Theme customizer
  'lib/button.php', // Load Gravity Forms via AJAX
//  'lib/ajax.php' // Load Gravity Forms via AJAX
];

foreach ($coolcars_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'coolcars'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

//Remove <p> from body
// remove_filter ('the_content', 'wpautop');

// Register Custom Navigation Walker (Soil)
require_once('bs4navwalker.php');

//declare your new menu
register_nav_menus( array(
    'primary' => __( 'Primary Menu', 'coolcars' ),
    'pages' => __( 'In Menu', 'coolcars' ),
) );




// Base of our Custom Walker
class IBenic_Walker extends Walker_Nav_Menu {

	// Displays start of an element. E.g '<li> Item Name'
    // @see Walker::start_el()
    function start_el(&$output, $item, $depth=0, $args=array(), $id = 0) {
    	$object = $item->object;
    	$type = $item->type;
    	$title = $item->title;
    	$description = $item->description;
    	$permalink = $item->url;
      $output .= "<li class='nav-item'>";

      //Add SPAN if no Permalink
      if( $permalink && $permalink != '#' ) {
      	$output .= '<a class="nav-link" href="' . $permalink . '">';
      } else {
      	$output .= '<span>';
      }

      $output .= $title;
      if( $description != '' && $depth == 0 ) {
      	$output .= '<small class="description">' . $description . '</small>';
      }
      if( $permalink && $permalink != '#' ) {
      	$output .= '</a>';
      } else {
      	$output .= '</span>';
      }
    }
}

//add SVG to allowed file uploads
function add_svg_to_upload_mimes( $upload_mimes ) {
	$upload_mimes['svg'] = 'image/svg+xml';
	$upload_mimes['svgz'] = 'image/svg+xml';
	return $upload_mimes;
}
add_filter( 'upload_mimes', 'add_svg_to_upload_mimes', 10, 1 );
//enable logo uploading via the customize theme page

function themeslug_theme_customizer( $wp_customize ) {
    $wp_customize->add_section( 'themeslug_logo_section' , array(
    'title'       => __( 'Logo', 'themeslug' ),
    'priority'    => 30,
    'description' => 'Upload a logo to replace the default site name and description in the header',
) );
$wp_customize->add_setting( 'themeslug_logo' );
$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'themeslug_logo', array(
    'label'    => __( 'Logo', 'themeslug' ),
    'section'  => 'themeslug_logo_section',
    'settings' => 'themeslug_logo',
    'extensions' => array( 'jpg', 'jpeg', 'gif', 'png', 'svg' ),
) ) );
}
add_action('customize_register', 'themeslug_theme_customizer');

add_filter( 'woocommerce_add_cart_item_data', 'ps_empty_cart', 10,  3);

function ps_empty_cart( $cart_item_data, $product_id, $variation_id ) {

    global $woocommerce;
    $woocommerce->cart->empty_cart();

    // Do nothing with the data and return
    return $cart_item_data;
}

/**
 * Set a custom add to cart URL to redirect to
 * @return string
 */
function custom_add_to_cart_redirect() {
    return 'http://rentalspot.dev/checkout/';
}
add_filter( 'woocommerce_add_to_cart_redirect', 'custom_add_to_cart_redirect' );

//remove_action ('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5, 0) ;
//add_action ('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5, 0) ;
remove_action ('woocommerce_single_product_summary', 'woocommerce_template_single_price') ;
//add_action ('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 10, 0) ;
// Product meta
//remove_action ('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40) ;
//add_action ('woocommerce_before_single_product_summary', 'woocommerce_template_single_meta', 17) ;

//remove_action ('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10) ;

//remove_action ('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10) ;
//remove_action ('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5) ;

function woocommerce_template_loop() {
    echo '<div class="row">';
  
    echo '<div class="col-lg-6">';
    echo wc_get_template( 'single-product/title.php' );
      echo '</div>';
      echo '<div class="col-lg-6">';
    echo get_template_part('templates/unit', 'cost');
echo '</div>';
    echo '</div>';
}
add_action ('woocommerce_before_add_to_cart_button', 'woocommerce_template_loop', 9) ;

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
    echo '<img class="card-img-top" src="http://success-at-work.com/wp-content/uploads/2015/04/free-stock-photos.gif">';
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

    echo '<div class="col-lg-6 col-sm-6 col-xs-6">';

    echo '<input type="text" name="in-time" value="12:30" class="in_time"/>';
    echo '</div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_intime_field', 11 );

 function add_outtime_field() {
   echo '<div class="col-lg-6 col-sm-6 col-xs-6">';

   echo '<input type="text" name="out-time" value="12:30" class="out_time"/>';
   echo '</div>';

 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_outtime_field', 12 );

 function add_location_in_start_field() {
  echo '<div class="col-lg-6 col-sm-6 col-xs-6">';
  echo '<div class="form-group locations row">';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_start_field', 13 );

  function add_location_in_airport_field() {
    echo '<div class="col-lg-4 col-sm-4 col-xs-4 text-center custom-radio-airport">';
    echo '<input id="radio1" type="radio" name="pick-up" value="Airport" class="custom-radio" checked="">';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_airport_field', 14 );

  function add_location_in_airport_text_field() {
    echo '<div class="reveal-if-active">
    <textarea name="your-airport" class="input-text form-control require-if-active pick_up_airport" id="pick-up-airport" placeholder="Flight Number ex. PH 4238"></textarea>
    </div>
    </div>';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_airport_text_field', 15 );

  function add_location_in_port_field() {
    echo '<div class="col-lg-4 col-sm-4 col-xs-4 text-center custom-radio-port">';
    echo '<input id="radio2" type="radio" name="pick-up" value="Port" class="custom-radio">';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_port_field', 16 );

  function add_location_in_port_text_field() {
    echo '<div class="reveal-if-active">
    <textarea name="your-port" class="input-text form-control require-if-active port-position" data-require-pair="#pick-up-hotel" id="your-port" placeholder="Boat Name"></textarea>
    </div>
    </div>';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_port_text_field', 17 );

  function add_location_in_custom_field() {
    echo '<div class="col-lg-4 col-sm-4 col-xs-4 text-center custom-radio-location">';
    echo '<input id="radio1" type="radio" name="pick-up" value="other_location" class="custom-radio">';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_custom_field', 18 );

  function add_location_in_custom_text_field() {
    echo '<div class="reveal-if-active">
    <textarea name="in-custom" class="input-text form-control require-if-active location-position" data-require-pair="#pick-up-hotel" id="in-custom" placeholder="" rows="4" cols="5"> - </textarea>
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
    echo '<div class="col-lg-6 col-sm-6 col-xs-6">';
    echo '<div class="form-group locations row">';
   }
   add_action( 'woocommerce_before_add_to_cart_button', 'add_location_out_start_field', 21 );

    function add_location_out_airport_field() {
      echo '<div class="col-lg-4 col-sm-4 col-xs-4 text-center custom-radio-airport-out">';
      echo '<input id="radio3" type="radio" name="drop-off" value="Airport" class="custom-radio" checked="">';
    }
    add_action( 'woocommerce_before_add_to_cart_button', 'add_location_out_airport_field', 22 );

    function add_location_out_airport_text_field() {
      echo '<div class="reveal-if-active-out">
      <textarea name="your-airport-out" class="input-text form-control require-if-active-out drop_off_airport" id="your-airport-out" placeholder="Flight Number ex. PH 4238"></textarea>
      </div>
      </div>';
    }
    add_action( 'woocommerce_before_add_to_cart_button', 'add_location_out_airport_text_field', 23 );

    function add_location_out_port_field() {
      echo '<div class="col-lg-4 col-sm-4 col-xs-4 text-center custom-radio-port">';
      echo '<input id="radio4" type="radio" name="drop-off" value="Port" class="custom-radio">';
    }
    add_action( 'woocommerce_before_add_to_cart_button', 'add_location_out_port_field', 24 );

    function add_location_out_port_text_field() {
      echo '<div class="reveal-if-active-out">
      <textarea name="your-port-out" class="input-text form-control require-if-active-out port-position" data-require-pair="#drop-off-hotel" id="your-port-out" placeholder="Boat Name"></textarea>
      </div>
      </div>';
    }
    add_action( 'woocommerce_before_add_to_cart_button', 'add_location_out_port_text_field', 25 );



    function add_location_out_custom_field() {
      echo '<div class="col-lg-4 col-sm-4 col-xs-4 text-center custom-radio-location">';
      echo '<input id="radio5" type="radio" name="drop-off" value="other_location" class="custom-radio">';
    }
    add_action( 'woocommerce_before_add_to_cart_button', 'add_location_out_custom_field', 26 );

    function add_location_out_custom_text_field() {
      echo '<div class="reveal-if-active-out">
      <textarea name="out-custom" class="input-text form-control require-if-active-out location-position" data-require-pair="#drop-off-hotel" id="out-custom" placeholder="" rows="4" cols="5"></textarea>
      </div>
      </div>';
    }
    add_action( 'woocommerce_before_add_to_cart_button', 'add_location_out_custom_text_field', 27 );



    function add_location_out_end_field() {
      echo '</div>';
      echo '</div>';
    }
    add_action( 'woocommerce_before_add_to_cart_button', 'add_location_out_end_field', 28 );




 function add_your_first_name_field() {
   echo '<div class="col-lg-6">';
     //echo '<label>Name</label>';
     echo '<input name="your-first-name" type="text" id="inputName" placeholder="First Name" required>';
 echo '</div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_your_first_name_field', 29 );

 function add_your_last_name_field() {
 echo '<div class="col-lg-6">';
 //echo '<div class="form-group">';
 //echo '<label>Last Name</label>';
     echo '<input type="text" name="your-last-name" placeholder="Last Name" value="" />';
 echo '</div>';
 //echo '</div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_your_last_name_field', 30 );


 function add_your_email_field() {
 echo '<div class="col-lg-6">';
 //echo '<label>Email</label>';
     echo '<input type="email" name="your-email" placeholder="email" value="" />';
 echo '</div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_your_email_field', 31 );

 function add_your_phone_field() {
 echo '<div class="col-lg-6">';
 //echo '<label>Phone</label>';

   echo '<input type="text" name="your-phone" placeholder="Phone" value="" />';
 echo '</div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_your_phone_field', 32 );


  function your_notes() {
  echo '<div class="col-12">';
  //echo '<label>Phone</label>';

    echo '<textarea name="your-notes" class="input-text " id="order_comments" placeholder="Order Notes" rows="2"></textarea>';
  echo '</div>';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'your_notes', 33 );


function add_after_your_first_name_field() {
   echo '</div>';
     echo '</fieldset>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_after_your_first_name_field', 34 );


 function save_intime_field( $cart_item_data, $product_id ) {
     if( isset( $_REQUEST['in-time'] ) ) {
         $cart_item_data[ 'in_time' ] = $_REQUEST['in-time'];
         /* below statement make sure every add to cart action as unique line item */
         $cart_item_data['unique_key'] = md5( microtime().rand() );
     }
     return $cart_item_data;
 }
 add_action( 'woocommerce_add_cart_item_data', 'save_intime_field', 10, 2 );

 function save_outtime_field( $cart_item_data, $product_id ) {
     if( isset( $_REQUEST['out-time'] ) ) {
         $cart_item_data[ 'out_time' ] = $_REQUEST['out-time'];
         /* below statement make sure every add to cart action as unique line item */
         $cart_item_data['unique_key'] = md5( microtime().rand() );
     }
     return $cart_item_data;
 }
 add_action( 'woocommerce_add_cart_item_data', 'save_outtime_field', 10, 3 );

 function save_your_first_name_field( $cart_item_data, $product_id ) {
     if( isset( $_REQUEST['your-first-name'] ) ) {
         $cart_item_data[ 'your_first_name' ] = $_REQUEST['your-first-name'];
         /* below statement make sure every add to cart action as unique line item */
         $cart_item_data['unique_key'] = md5( microtime().rand() );
     }
     return $cart_item_data;
 }
 add_action( 'woocommerce_add_cart_item_data', 'save_your_first_name_field', 10, 5 );

 function save_your_last_name_field( $cart_item_data, $product_id ) {
     if( isset( $_REQUEST['your-last-name'] ) ) {
         $cart_item_data[ 'your_last_name' ] = $_REQUEST['your-last-name'];
         /* below statement make sure every add to cart action as unique line item */
         $cart_item_data['unique_key'] = md5( microtime().rand() );
     }
     return $cart_item_data;
 }
add_action( 'woocommerce_add_cart_item_data', 'save_your_last_name_field', 10, 6 );


function save_your_email_field( $cart_item_data, $product_id ) {
    if( isset( $_REQUEST['your-email'] ) ) {
        $cart_item_data[ 'your_email' ] = $_REQUEST['your-email'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5( microtime().rand() );
    }
    return $cart_item_data;
}
add_action( 'woocommerce_add_cart_item_data', 'save_your_email_field', 10, 7 );

function save_your_phone_field( $cart_item_data, $product_id ) {
    if( isset( $_REQUEST['your-phone'] ) ) {
        $cart_item_data[ 'your_phone' ] = $_REQUEST['your-phone'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5( microtime().rand() );
    }
    return $cart_item_data;
}
add_action( 'woocommerce_add_cart_item_data', 'save_your_phone_field', 10, 8 );

function save_your_notes_field( $cart_item_data, $product_id ) {
    if( isset( $_REQUEST['your-notes'] ) ) {
        $cart_item_data[ 'your_notes' ] = $_REQUEST['your-notes'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5( microtime().rand() );
    }
    return $cart_item_data;
}
add_action( 'woocommerce_add_cart_item_data', 'save_your_notes_field', 10, 9 );

function save_your_airport_field( $cart_item_data, $product_id ) {
    if( isset( $_REQUEST['your-airport'] ) ) {
        $cart_item_data[ 'your_airport' ] = $_REQUEST['your-airport'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5( microtime().rand() );
    }
    return $cart_item_data;
}
add_action( 'woocommerce_add_cart_item_data', 'save_your_airport_field', 10, 10 );

function save_incustom_field( $cart_item_data, $product_id ) {
    if( isset( $_REQUEST['in-custom'] ) ) {
        $cart_item_data[ 'in_custom' ] = $_REQUEST['in-custom'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5( microtime().rand() );
    }
    return $cart_item_data;
}
add_action( 'woocommerce_add_cart_item_data', 'save_incustom_field', 10, 11 );

function save_your_port_field( $cart_item_data, $product_id ) {
    if( isset( $_REQUEST['your-port'] ) ) {
        $cart_item_data[ 'your_port' ] = $_REQUEST['your-port'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5( microtime().rand() );
    }
    return $cart_item_data;
}
add_action( 'woocommerce_add_cart_item_data', 'save_your_port_field', 10, 12 );


function save_your_outairport_field( $cart_item_data, $product_id ) {
    if( isset( $_REQUEST['your-airport-out'] ) ) {
        $cart_item_data[ 'your_airport_out' ] = $_REQUEST['your-airport-out'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5( microtime().rand() );
    }
    return $cart_item_data;
}
add_action( 'woocommerce_add_cart_item_data', 'save_your_outairport_field', 10, 13 );

function save_outcustom_field( $cart_item_data, $product_id ) {
    if( isset( $_REQUEST['out-custom'] ) ) {
        $cart_item_data[ 'out_custom' ] = $_REQUEST['out-custom'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5( microtime().rand() );
    }
    return $cart_item_data;
}
add_action( 'woocommerce_add_cart_item_data', 'save_incustom_field', 10, 14 );

function save_your_outport_field( $cart_item_data, $product_id ) {
    if( isset( $_REQUEST['your-port-out'] ) ) {
        $cart_item_data[ 'your_port_out' ] = $_REQUEST['your-port-out'];
        /* below statement make sure every add to cart action as unique line item */
        $cart_item_data['unique_key'] = md5( microtime().rand() );
    }
    return $cart_item_data;
}
add_action( 'woocommerce_add_cart_item_data', 'save_your_outport_field', 10, 15);






function render_on_cart_and_checkout_in_time( $cart_data, $cart_item = null ) {
    $custom_items = array();
    /* Woo 2.4.2 updates */
    if( !empty( $cart_data ) ) {
        $custom_items = $cart_data;
    }
    if( isset( $cart_item['in_time'] ) ) {
        $custom_items[] = array( "name" => 'Check in', "value" => $cart_item['in_time'] );
    }
    return $custom_items;
}

add_filter( 'woocommerce_get_item_data', 'render_on_cart_and_checkout_in_time', 10, 2 );

function render_in_airport_text( $cart_data, $cart_item = null ) {
    $custom_items = array();
    /* Woo 2.4.2 updates */
    if( !empty( $cart_data ) ) {
        $custom_items = $cart_data;
    }
    if( isset( $cart_item['your_airport'] ) ) {
        $custom_items[] = array( "name" => 'Pick up Airport', "value" => $cart_item['your_airport'] );
    }
    return $custom_items;
}

add_filter( 'woocommerce_get_item_data', 'render_in_airport_text', 10, 3 );

function render_in_port_text( $cart_data, $cart_item = null ) {
    $custom_items = array();
    /* Woo 2.4.2 updates */
    if( !empty( $cart_data ) ) {
        $custom_items = $cart_data;
    }
    if( isset( $cart_item['your_port'] ) ) {
        $custom_items[] = array( "name" => 'Pick up Port', "value" => $cart_item['your_port'] );
    }
    return $custom_items;
}

add_filter( 'woocommerce_get_item_data', 'render_in_port_text', 10, 4 );


function render_incustom_text( $cart_data, $cart_item = null ) {
    $custom_items = array();
    /* Woo 2.4.2 updates */
    if( !empty( $cart_data ) ) {
        $custom_items = $cart_data;
    }
    if( isset( $cart_item['in_custom'] ) ) {
        $custom_items[] = array( "name" => 'Pick up Custom', "value" => $cart_item['in_custom'] );
    }
    return $custom_items;
}

add_filter( 'woocommerce_get_item_data', 'render_incustom_text', 10, 5 );

function render_out_airport_text( $cart_data, $cart_item = null ) {
    $custom_items = array();
    /* Woo 2.4.2 updates */
    if( !empty( $cart_data ) ) {
        $custom_items = $cart_data;
    }
    if( isset( $cart_item['your_airport_out'] ) ) {
        $custom_items[] = array( "name" => 'Drop off Airport', "value" => $cart_item['your_airport_out'] );
    }
    return $custom_items;
}

add_filter( 'woocommerce_get_item_data', 'render_out_airport_text', 10, 6 );

function render_out_port_text( $cart_data, $cart_item = null ) {
    $custom_items = array();
    /* Woo 2.4.2 updates */
    if( !empty( $cart_data ) ) {
        $custom_items = $cart_data;
    }
    if( isset( $cart_item['your_port_out'] ) ) {
        $custom_items[] = array( "name" => 'Drop off Port', "value" => $cart_item['your_port_out'] );
    }
    return $custom_items;
}

add_filter( 'woocommerce_get_item_data', 'render_out_port_text', 10, 7 );


function render_outcustom_text( $cart_data, $cart_item = null ) {
    $custom_items = array();
    /* Woo 2.4.2 updates */
    if( !empty( $cart_data ) ) {
        $custom_items = $cart_data;
    }
    if( isset( $cart_item['out_custom'] ) ) {
        $custom_items[] = array( "name" => 'Pick up Custom', "value" => $cart_item['out_custom'] );
    }
    return $custom_items;
}

add_filter( 'woocommerce_get_item_data', 'render_outcustom_text', 10, 8 );


function render_on_cart_and_checkout_out_time( $cart_data, $cart_item = null ) {
    $custom_items = array();
    /* Woo 2.4.2 updates */
    if( !empty( $cart_data ) ) {
        $custom_items = $cart_data;
    }
    if( isset( $cart_item['out_time'] ) ) {
        $custom_items[] = array( "name" => 'Check out', "value" => $cart_item['out_time'] );
    }
    return $custom_items;
}

add_filter( 'woocommerce_get_item_data', 'render_on_cart_and_checkout_out_time', 10, 9 );


 function render_on_cart_and_checkout_your_first_name( $cart_data, $cart_item = null ) {
     $custom_items = array();
     /* Woo 2.4.2 updates */
     if( !empty( $cart_data ) ) {
         $custom_items = $cart_data;
     }
     if( isset( $cart_item['your_first_name'] ) ) {
         $custom_items[] = array( "name" => 'First name', "value" => $cart_item['your_first_name'] );
     }
     return $custom_items;
 }
 add_filter( 'woocommerce_get_item_data', 'render_on_cart_and_checkout_your_first_name', 10, 10 );

 function render_on_cart_and_checkout_your_last_name( $cart_data, $cart_item = null ) {
     $custom_items = array();
     /* Woo 2.4.2 updates */
     if( !empty( $cart_data ) ) {
         $custom_items = $cart_data;
     }
     if( isset( $cart_item['your_last_name'] ) ) {
         $custom_items[] = array( "name" => 'Last name', "value" => $cart_item['your_last_name'] );
     }
     return $custom_items;
 }
add_filter( 'woocommerce_get_item_data', 'render_on_cart_and_checkout_your_last_name', 10, 11 );

function render_on_cart_and_checkout_your_email( $cart_data, $cart_item = null ) {
    $custom_items = array();
    /* Woo 2.4.2 updates */
    if( !empty( $cart_data ) ) {
        $custom_items = $cart_data;
    }
    if( isset( $cart_item['your_email'] ) ) {
        $custom_items[] = array( "name" => 'email', "value" => $cart_item['your_email'] );
    }
    return $custom_items;
}

add_filter( 'woocommerce_get_item_data', 'render_on_cart_and_checkout_your_email', 10, 12 );

function render_on_cart_and_checkout_your_phone( $cart_data, $cart_item = null ) {
    $custom_items = array();
    /* Woo 2.4.2 updates */
    if( !empty( $cart_data ) ) {
        $custom_items = $cart_data;
    }
    if( isset( $cart_item['your_phone'] ) ) {
        $custom_items[] = array( "name" => 'Phone', "value" => $cart_item['your_phone'] );
    }
    return $custom_items;
}

add_filter( 'woocommerce_get_item_data', 'render_on_cart_and_checkout_your_phone', 10, 13 );


function render_on_cart_and_checkout_your_notes( $cart_data, $cart_item = null ) {
    $custom_items = array();
    /* Woo 2.4.2 updates */
    if( !empty( $cart_data ) ) {
        $custom_items = $cart_data;
    }
    if( isset( $cart_item['your_notes'] ) ) {
        $custom_items[] = array( "name" => 'Notes', "value" => $cart_item['your_notes'] );
    }
    return $custom_items;
}

add_filter( 'woocommerce_get_item_data', 'render_on_cart_and_checkout_your_notes', 10, 14 );





 if ( ! function_exists( 'woocommerce_template_single_name' ) ) {

 	/**
 	 * Output the product meta.
 	 *
 	 * @subpackage	Product
 	 */
 	function woocommerce_template_single_name() {
 		wc_get_template( 'single-product/name.php' );
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
		wp_redirect( 'https://coolcars.gr/thank-you-for-your-order/' );
		exit;
	}
}


// Add Specific Category ID Under Each Product Title
function show_cat_id() {
global $post;
$cats = get_the_terms( $post->ID, 'product_cat' );

if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {

// Loop through the product categories...

        foreach ( $cats as $term ) {

                        // If parent cat ID = 116 echo subcat name...
            if( $term->parent == 16 ) {
              echo '<div class="row">';
              echo '<div class="col-lg-1 col-1">';
              echo '<span class="location">';
              echo '</span>';
              echo '</div>';
              echo '<div class="col-lg-9 col-7w">';
              echo '<h4>';
              echo $term->name;
              echo '</h4>';
              echo '</div>';
              echo '</div>';
            }

        }

        }
}
add_action('woocommerce_single_product_meta','show_cat_id', 20);

// Add Specific Category ID Under Each Product Title
function show_cat_id_alt() {
global $post;
$cats = get_the_terms( $post->ID, 'product_cat' );

if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {

// Loop through the product categories...

        foreach ( $cats as $term ) {

                        // If parent cat ID = 116 echo subcat name...
            if( $term->parent == 16 ) {
              echo $term->name;
            }

        }

        }
}
add_action('woocommerce_single_product_alt','show_cat_id_alt', 10);

add_filter( 'facetwp_sort_options', 'fwp_add_price_sort', 10, 2 );
function fwp_add_price_sort( $options, $params ) {
    $options['price_desc'] = array(
        'label' => 'Price (Highest)',
        'query_args' => array(
            'orderby' => 'meta_value_num',
            'meta_key' => '_price',
            'order' => 'DESC',
        )
    );
    $options['price_asc'] = array(
        'label' => 'Price (Lowest)',
        'query_args' => array(
            'orderby' => 'meta_value_num',
            'meta_key' => '_price',
            'order' => 'ASC',
        )
    );
    return $options;
}
