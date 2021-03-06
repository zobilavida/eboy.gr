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
  'lib/ajax.php' // Load Gravity Forms via AJAX
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
require_once('Microdot_Walker_Nav_Menu.php');

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

remove_action ('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5, 0) ;
add_action ('woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 5, 0) ;
//remove_action ('woocommerce_single_product_summary', 'woocommerce_template_single_price') ;
//add_action ('woocommerce_before_single_product_summary', 'woocommerce_template_single_price', 10, 0) ;
// Product meta
remove_action ('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40) ;
add_action ('woocommerce_before_single_product_summary', 'woocommerce_template_single_meta', 17) ;

remove_action ('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10) ;

remove_action ('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10) ;
add_action ('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail_mono', 10) ;
	function woocommerce_template_loop_product_thumbnail_mono() {

      $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'thumbnail' );

       echo '<img class="img-fluid" src="' . $image_src_thumbnail[0] . '">';

  }

//remove_action ('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10) ;
//remove_action ('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5) ;


if ( ! function_exists( 'woocommerce_template_loop_product_title' ) ) {

	/**
	 * Show the product title in the product loop. By default this is an H2.
	 */
	function woocommerce_template_loop_product_title() {
    global $product;
    echo '<div class="card-body">';
    echo '<div class="row">';
    echo '<div class="col-7">';
    echo '<h2 class="card-title">' . get_the_title() . '</h2>';
    echo '</div>';
    echo '<div class="col-5">';
    echo wc_get_template( 'loop/price.php' );
    echo '</div>';

    echo '</div>';
    echo '</div>';
	}
}




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
     echo '<fieldset class="wc-bookings-fields second_step">';
     echo '<div class="form-group row">';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_before_your_first_name_field', 10, 0  );

 function add_intime_field() {
    echo '<div class="row">';
    echo '<div class="col col-lg-6">';
    echo '<label>Check-in</label>';
    echo '<input type="text" name="in-time" value="12:30" class="in_time"/>';
    echo '</div>';
 }
 add_action( 'woocommerce_after_calendar', 'add_intime_field', 11, 0  );

 function add_outtime_field() {
   echo '<div class="col col-lg-6">';
   echo '<label>Check-out</label>';
   echo '<input type="text" name="out-time" value="12:30" class="out_time"/>';
   echo '</div>';
      echo '</div>';
 }
 add_action( 'woocommerce_after_calendar', 'add_outtime_field', 12, 0  );

 function add_location_in_start_field() {
  //echo '<div class="row">';
  echo '<div class="col-lg-6">';
   echo '<div class="row form-group">';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_start_field', 13, 0  );

  function add_location_in_airport_field() {
    echo '<div class="col-lg-4 text-center custom-radio-airport">';
    echo '<input id="radio1" type="radio" name="pick-up" value="Airport" class="custom-radio" checked="">';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_airport_field', 14, 0  );

  function add_location_in_airport_text_field() {
    echo '<div class="reveal-if-active">
    <textarea name="pick-up-airport" class="input-text form-control require-if-active pick_up_airport" id="order_comments" placeholder="Flight Number ex. PH 4238" rows="4" cols="5"></textarea>
    </div>
    </div>';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_airport_text_field', 15, 0 );

  function add_location_in_port_field() {
    echo '<div class="col-lg-4 text-center custom-radio-port">';
    echo '<input id="radio2" type="radio" name="pick-up" value="Port" class="custom-radio">';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_port_field', 16, 0  );

  function add_location_in_port_text_field() {
    echo '<div class="reveal-if-active">
    <textarea name="pick-up-airport" class="input-text form-control require-if-active port-position" data-require-pair="#pick-up-hotel" id="order_comments" placeholder="Boat Name" rows="4" cols="5"></textarea>
    </div>
    </div>';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_port_text_field', 17, 0  );



  function add_location_in_custom_field() {
    echo '<div class="col-lg-4 text-center custom-radio-location">';
    echo '<input id="radio1" type="radio" name="pick-up" value="other_location" class="custom-radio">';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_custom_field', 18, 0  );

  function add_location_in_custom_text_field() {
    echo '<div class="reveal-if-active">
    <textarea name="pick-up" class="input-text form-control require-if-active location-position" data-require-pair="#pick-up-hotel" id="order_comments" placeholder="" rows="4" cols="5"></textarea>
    </div>
    </div>';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_custom_text_field', 19, 0  );



  function add_location_in_end_field() {
    echo '</div>';
    echo '</div>';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_in_end_field', 20, 0  );



    function add_location_out_start_field() {
      echo '<div class="col-lg-6">';
      echo '<div class="row form-group">';
    }
    add_action( 'woocommerce_before_add_to_cart_button', 'add_location_out_start_field', 21, 0  );

  function add_location_out_end_field() {
    echo '</div>';
    echo '</div>';
    //echo '</div>';
  }
  add_action( 'woocommerce_before_add_to_cart_button', 'add_location_out_end_field', 22, 0  );

 function add_your_first_name_field() {
   echo '<div class="col-lg-6">';
     //echo '<label>Name</label>';
     echo '<input name="your-first-name" type="text" class="form-control" id="inputName" placeholder="Your First Name" required>';
 echo '</div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_your_first_name_field', 23, 0  );

 function add_your_last_name_field() {
 echo '<div class="col-lg-6">';
 //echo '<div class="form-group">';
 //echo '<label>Last Name</label>';
     echo '<input type="text" name="your-last-name" placeholder="Last Name" value="" />';
 echo '</div>';
 //echo '</div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_your_last_name_field', 24, 0  );


 function add_your_email_field() {
 echo '<div class="col-lg-6">';
 //echo '<label>Email</label>';
     echo '<input type="email" name="your-email" placeholder="email" value="" />';
 echo '</div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_your_email_field', 25, 0  );

 function add_your_phone_field() {
 echo '<div class="col-lg-6">';
 //echo '<label>Phone</label>';

   echo '<input type="text" name="your-phone" placeholder="Phone" value="" />';
 echo '</div>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_your_phone_field', 26, 0  );





 function add_after_your_first_name_field() {
   echo '</div>';
     echo '</fieldset>';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_after_your_first_name_field', 30, 0  );




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





  /**
   * Optimize WooCommerce Scripts
   * Remove WooCommerce Generator tag, styles, and scripts from non WooCommerce pages.
   */
  add_action( 'wp_enqueue_scripts', 'child_manage_woocommerce_styles', 99 );
  function child_manage_woocommerce_styles() {
   //remove generator meta tag
   remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );

   //first check that woo exists to prevent fatal errors
   if ( function_exists( 'is_woocommerce' ) ) {
   //dequeue scripts and styles
   if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
   wp_dequeue_style( 'woocommerce_frontend_styles' );
   wp_dequeue_style( 'woocommerce_fancybox_styles' );
   wp_dequeue_style( 'woocommerce_chosen_styles' );
   wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
   wp_dequeue_script( 'wc_price_slider' );
   wp_dequeue_script( 'wc-single-product' );
   wp_dequeue_script( 'wc-add-to-cart' );
   wp_dequeue_script( 'wc-cart-fragments' );
   wp_dequeue_script( 'wc-checkout' );
   wp_dequeue_script( 'wc-add-to-cart-variation' );
   wp_dequeue_script( 'wc-single-product' );
   wp_dequeue_script( 'wc-cart' );
   wp_dequeue_script( 'wc-chosen' );
   wp_dequeue_script( 'woocommerce' );
   wp_dequeue_script( 'prettyPhoto' );
   wp_dequeue_script( 'prettyPhoto-init' );
   wp_dequeue_script( 'jquery-blockui' );
   wp_dequeue_script( 'jquery-placeholder' );
   wp_dequeue_script( 'fancybox' );
   wp_dequeue_script( 'jqueryui' );
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


function bbloomer_redirect_checkout_add_cart( $url ) {
   $url = get_permalink( get_option( 'woocommerce_checkout_page_id' ) );
   return $url;
}

add_filter( 'woocommerce_add_to_cart_redirect', 'bbloomer_redirect_checkout_add_cart' );

 //*Add custom redirection
add_action( 'template_redirect', 'wc_custom_redirect_after_purchase' );
function wc_custom_redirect_after_purchase() {
	global $wp;
  if ( is_checkout() && ! empty( $wp->query_vars['order-received'] ) ) {
		wp_redirect( 'https://eboy.gr/coolcars/thank-you-for-your-order/' );
		exit;
	}
}




//add_action( 'woocommerce_before_cart', 'bbloomer_print_cart_array' );
function bbloomer_print_cart_array() {
$cart = var_dump($GLOBALS);
print_r($cart);
}
