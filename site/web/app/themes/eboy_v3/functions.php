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
require_once('Microdot_Walker_Nav_Menu.php');

// Register Custom Navigation Walker (Soil)
require_once('cat_walker.php');

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


add_filter('site_option_active_sitewide_plugins', 'modify_sitewide_plugins');

function modify_sitewide_plugins($value) {
    global $current_blog;

     if ( is_page_template( 'template-eboy_v3.php' ) ) {
        unset($value['woocommerce/woocommerce.php']);
        unset($value['storefront-woocommerce-customiser/storefront-woocommerce-customiser.php']);
        unset($value['storefront-designer/storefront-designer.php']);
        unset($value['revslider/revslider.php']);
        unset($value['gravityforms-master/gravityforms.php']);
        unset($value['facetwp-select2/facetwp-select2.php']);
        unset($value['facetwp/index.php']);
        unset($value['pods/init.php']);
        unset($value['woocommerce-bookings/woocommerce-bookings.php']);
        unset($value['woocommerce-accommodation-bookings/woocommerce-accommodation-bookings.php']);
    }

    return $value;

    wp_dequeue_style('handle',get_theme_file_uri().'/js/my-script.js',array(), '1.0', true );

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
      wp_dequeue_style('woocommerce-smallscreen');
      wp_dequeue_style('woocommerce-layout');
      wp_dequeue_style('woocommerce-general');
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



add_action( 'wp_enqueue_scripts', 'CF7_cleanup' );
/**
** Contact Form 7 plugin: Cleanup scripts &amp; styles. **
**                                                      **
*/
   function CF7_cleanup() {
       global $current_blog;
   /** Dequeue enqueued scripts &amp; styles */

      wp_dequeue_script( 'select2.min.js' );
      wp_dequeue_style( 'frontend.css' );
   /** Only enqueue stuff on the used page ID or page name */
         if( $current_blog->blog_id == 6 || $current_blog->blog_id == 5 ) { //You can use a page name here eg: if ( is_page( 'Contact'))
         //You can use &amp;&amp; or || to add pages as shown
        //   wp_enqueue_script( 'contact-form-7' );
           wp_enqueue_style( 'frontend.css' );
        } // end if
   } // end function

   function get_intro_post() {
     $found_post = null;

 if ( $posts = get_posts( array(
     'name' => 'intro',
     'post_type' => 'post',
     'post_status' => 'publish',
     'posts_per_page' => 1
 ) ) ) $found_post = $posts[0];

 // Now, we can do something with $found_post
 if ( ! is_null( $found_post ) ){
     // do something with the post...
    //echo $content;
 }
   }
   add_action( 'my_custom_functions', 'get_intro_post' );

   remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
   // Remove count - Add facetwp
   remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
   add_action( 'woocommerce_before_shop_loop', 'woocommerce_facet', 20 );
   function woocommerce_facet() {
     echo wc_get_template( 'loop/facetwp.php' );
   }
   remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );


   remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
  //  add_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 15 );
    remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

    remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
  //  add_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 30 );

    remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
  //  add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash',  30 );

      remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
      remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
      remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
      remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
      remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
      remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
      function woocommerce_template_single_excerpt_meta() {

        echo wc_get_template( 'single-product/short-description-meta.php' );
      }
    add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt_meta', 20 );

    remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
    add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
    remove_action( 'woocommerce_shop_loop_item_title',  'woocommerce_template_loop_product_title', 10);

if ( ! function_exists( 'woocommerce_template_loop_product_thumbnail' ) ) {
    function woocommerce_template_loop_product_thumbnail() {
        echo woocommerce_get_product_thumbnail();
    }
}
if ( ! function_exists( 'woocommerce_get_product_thumbnail' ) ) {
    function woocommerce_get_product_thumbnail( $size = 'shop_catalog', $placeholder_width = 0, $placeholder_height = 0  ) {
        global $post, $woocommerce;
        $output = '<div class="grid-item-content">';

        if ( has_post_thumbnail() ) {
            $image_src_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(),'thumbnail' );
        //    $output .= get_the_post_thumbnail( $post->ID, $size );
            $output .= '<img width="100%" src="' . $image_src_thumbnail[0] . '">';

        }
        $output .= '</div>';
        return $output;
    }
}


          function woocommerce_template_single_content() {

            echo wc_get_template( 'single-product/content.php' );
          }
        add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_content', 30 );

        function tax_cat_active( $output, $args ) {

  if(is_single()){
    global $post;

    $terms = get_the_terms( $post->ID, $args['taxonomy'] );
    foreach( $terms as $term )
        if ( preg_match( '#cat-item-' . $term ->term_id . '#', $output ) )
            $output = str_replace('cat-item-'.$term ->term_id, 'cat-item-'.$term ->term_id . ' current-cat', $output);
  }

  return $output;
}
add_filter( 'wp_list_categories', 'tax_cat_active', 10, 2 );



function wpb_screenshots($atts, $content = NULL) {
extract(shortcode_atts(array(
"snap" => 'http://s.wordpress.com/mshots/v1/',
"url" => 'https://eboy.gr/coolcars/',
"alt" => 'screenshot',
"w" => '600', // width
"h" => '450' // height
), $atts));

$img = '<img alt="' . $alt . '" src="' . $snap . '' . urlencode($url) . '?w=' . $w . '&h=' . $h . '" />';

return $img;
}
add_shortcode("screen", "wpb_screenshots");
