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
add_action( 'woocommerce_attribute_doors', 'show_attributes_doors' );

function show_attributes_passengers() {
global $product;
$product_id = $product->get_id();
$attribute_slug = 'passengers';
$array = wc_get_product_terms( $product_id , 'pa_' . $attribute_slug, array( 'fields' => 'names' ) );
$text = array_shift( $array );
echo '<div class="cars-slider_item-option car-option-' . $attribute_slug . '"><h6>passengers:<span class="attribute">' . $text . '</span></h6></div>';
}
add_action( 'woocommerce_attribute_passengers', 'show_attributes_passengers' );

function show_attributes_luggage() {
global $product;
$product_id = $product->get_id();
$attribute_slug = 'luggage';
$array = wc_get_product_terms( $product_id , 'pa_' . $attribute_slug, array( 'fields' => 'names' ) );
$text = array_shift( $array );
echo '<div class="cars-slider_item-option car-option-' . $attribute_slug . '"><h6>luggage:<span class="attribute">' . $text . '</span></h6></div>';
}
add_action( 'woocommerce_attribute_luggage', 'show_attributes_luggage' );

function show_attributes_transmission() {
global $product;
$product_id = $product->get_id();
$attribute_slug = 'transmission';
$array = wc_get_product_terms( $product_id , 'pa_' . $attribute_slug, array( 'fields' => 'names' ) );
$text = array_shift( $array );
echo '<div class="cars-slider_item-option car-option-' . $attribute_slug . '"><h6>transmission:<span class="attribute">' . $text . '</span></h6></div>';
}
add_action( 'woocommerce_attribute_tansmission', 'show_attributes_transmission' );

function show_attributes_air_conditioning() {
global $product;
$product_id = $product->get_id();
$attribute_slug = 'air-conditioning';
$array = wc_get_product_terms( $product_id , 'pa_' . $attribute_slug, array( 'fields' => 'names' ) );
$text = array_shift( $array );
echo '<div class="cars-slider_item-option car-option-' . $attribute_slug . '"><h6>air condtitoning:<span class="attribute">' . $text . '</span></h6></div>';
}
add_action( 'woocommerce_attribute_air_conditioning', 'show_attributes_air_conditioning' );

function show_attributes_drive_wheel() {
global $product;
$product_id = $product->get_id();
$attribute_slug = 'drive-wheel';
$array = wc_get_product_terms( $product_id , 'pa_' . $attribute_slug, array( 'fields' => 'names' ) );
$text = array_shift( $array );
echo '<div class="cars-slider_item-option car-option-' . $attribute_slug . '"><h6>Drive wheel:<span class="attribute">' . $text . '</span></h6></div>';
}
add_action( 'woocommerce_attribute_drive_wheel', 'show_attributes_drive_wheel' );



add_filter( 'woocommerce_add_cart_item_data', 'ps_empty_cart', 10,  3);

function ps_empty_cart( $cart_item_data, $product_id, $variation_id ) {

    global $woocommerce;
    $woocommerce->cart->empty_cart();

    // Do nothing with the data and return
    return $cart_item_data;
}

remove_action ('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5, 0) ;
remove_action ('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40) ;


function woocommerce_template_loop_cart( $booking_id ) {
  $product_id = get_post_meta( $booking_id, '_booking_product_id', true );
  $product   = wc_get_product( $product_id );

  if ( ! is_a( $product, 'WC_Product_Accommodation_Booking' ) ) {
    return;
  }

    echo 'Test';
    global $woocommerce;
   $items = $woocommerce->cart->get_cart();

       foreach($items as $item => $values) {
           $_product =  wc_get_product( $values['data']->get_id());
           echo "<b>".$_product->get_title().'</b>  <br> Quantity: '.$values['quantity'].'<br>';
           $price = get_post_meta($values['product_id'] , '_price', true);
           $check_in  = WC_Product_Accommodation_Booking::get_check_times( 'in' );
           $start = get_post_meta( $booking_id, '_booking_start', true );
       		$end   = get_post_meta( $booking_id, '_booking_end', true );
           echo "  Price: ".$price."<br>";
           echo "  Check on: ".$check_in."<br>";
           echo "  Check out: ".$start."<br>";

       }
}
add_action ('woocommerce_cart_front', 'woocommerce_template_loop_cart', 10) ;



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
   echo '<div class="col-12 p-0">';
     echo '<fieldset class="second_step">';
     echo '<div class="container">';
     echo '<div class="row">';
 }
 add_action( 'woocommerce_before_add_to_cart_button', 'add_before_your_first_name_field', 8 );






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
   echo '<div class="input-group col-lg-4 py-3">';
   echo '<textarea name="pick-up" class="input-text form-control additional-info" data-require-pair="" id="order_comments" placeholder="Additional info" rows="5" cols="5"></textarea>';
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
 		if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
 			wp_dequeue_style( 'woocommerce_frontend_styles' );
 			wp_dequeue_style( 'woocommerce_fancybox_styles' );
 			wp_dequeue_style( 'woocommerce_chosen_styles' );
 			wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
    //  wp_dequeue_style( 'woocommerce-layout' );
    //  wp_dequeue_style( 'woocommerce-smallscreen' );
      //wp_dequeue_style('gforms_css');
      //wp_dequeue_script( 'datepicker' );
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
 			//wp_dequeue_script( 'jqueryui' );

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
	<script>
		jQuery(document).ready(function($) {
			var ajaxurl = "<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>"; // get the url we use to submit AJAX
			$( document.body).on('click', '.wc-bookings-date-picker-date-fields', function(e) { // i made this delegated, rather than a traditional click handler, so you could add additional buttons via AJAx, if you ever wanted to
				e.preventDefault(); // stop the click from doing normal button things
				var $this = $(this);
				if( $this.is(':disabled') ) { // don't do anything if the button is disabled (item is in cart)... this could be changed to toggle whether you are in cart or not
					return;
				}
				var id = $(this).data("product-id"); // get the product ID from the button
				var data = { // prep our AJAX request
					action     : 'my_custom_add_to_cart', // This is the AJAX function we define in PHP below
					product_id : id
				};
				$.post(ajaxurl, data, function(response) {
					if( response.success ) {
						// we added to cart so change the message, and make sure no one can add again
						$this.text("added to cart");
						$this.attr('disabled', 'disabled');
						// make woocommerce update cart counts in the menu widget
						$( document.body ).trigger( 'wc_fragment_refresh' );
					}
				}, 'json');
			})
		});
	</script>
	<?php
}
// here's where we define the ajax processing functions.. the part after wp_ajax, and wp_ajax_nopriv has to match the action we used in our javascript
add_action('wp_ajax_my_custom_add_to_cart', "my_custom_add_to_cart");
add_action('wp_ajax_nopriv_my_custom_add_to_cart', "my_custom_add_to_cart");

function my_custom_add_to_cart() {
	// just setting up data to return... we override this as we go
	$retval = array(
		'success' => false,
		'message' => ""
	);

	if( !function_exists( "WC" ) ) {
		// check if woocommerce is installed
		$retval['message'] = "woocommerce not installed";
	} elseif( empty( $_POST['product_id'] ) ) {
		// check product id was sent
		$retval['message'] = "no product id provided";
	} else {
		$product_id = $_POST['product_id'];
		// my_custom_cart_contains is defined below.. checks if the cart contains a product
		if( my_custom_cart_contains( $product_id ) ) {
			// make sure we can't add a product twice
			$retval['message'] = "product already in cart";
		} else {
			// we are good to add to cart
			$cart = WC()->cart;
			//add_to_cart returns an id, but we only need to make sure it doesn't return false... hence casting to a boolean
			$retval['success'] = (bool) $cart->add_to_cart( $product_id );
			if( !$retval['success'] ) {
				// the add to cart failed
				$retval['message'] = "product could not be added to cart";
			} else {
				// the add succeeded
				$retval['message'] = "product added to cart";
			}
		}
	}
	// we send the data back to javascript by outputting it as JSON, and exiting.
	echo json_encode( $retval );
	wp_die();
}

// check if cart contains product
function my_custom_cart_contains( $product_id ) {
	$cart = WC()->cart;
	$cart_items = $cart->get_cart();
	if( $cart_items ) {
		foreach( $cart_items as $item ) {
			$product = $item['data'];
			if( $product_id == $product->get_id() ) {
				return true;
			}
		}
	}
	return false;
}
// before add to cart, only allow 1 item in a cart
add_filter( 'woocommerce_add_to_cart_validation', 'woo_custom_add_to_cart_before' );

function woo_custom_add_to_cart_before( $cart_item_data ) {

    global $woocommerce;
    $woocommerce->cart->empty_cart();

    // Do nothing with the data and return
    return true;
}


/**
 * Add product to cart on page load
 */
 add_action( 'template_redirect', 'bbloomer_add_product_to_cart' );

 function bbloomer_add_product_to_cart() {

                 // select ID
                 $product_id = 851;

                 //check if product already in cart
         if ( WC()->cart->get_cart_contents_count() == 0 ) {

                         // if no products in cart, add it
             WC()->cart->add_to_cart( $product_id );

                 }
 }

 function single_post_insert() {
        $new_post = array(
        'post_title'    => $_POST['title'],
        'post_content'  => $_POST['content'],
        'post_status'   => 'publish',
        'post_type'     => 'post'
        );
        //insert the the post into database by passing $new_post to wp_insert_post
        //store our post ID in a variable $pid
        $pid = wp_insert_post($new_post);
    echo json_encode(array('flag'=>'1'));
  die;
}
add_action( 'wp_ajax_single_post', 'single_post_insert' );    // If called from admin panel
add_action( 'wp_ajax_nopriv_single_post', 'single_post_insert' );
