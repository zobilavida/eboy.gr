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


// Woocommerce functions

add_action ('customize_register', 'themeslug_theme_customizer');


add_action( 'publish_post', 'itsg_create_sitemap' );
add_action( 'publish_page', 'itsg_create_sitemap' );
add_action( 'save_post_my_post_type', 'itsg_create_sitemap' );

function itsg_create_sitemap() {

    $postsForSitemap = get_posts(array(
        'numberposts' => -1,
        'orderby' => 'modified',
        'post_type'  => array( 'post', 'page', 'product' ),
        'order'    => 'DESC'
    ));

    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    foreach( $postsForSitemap as $post ) {
        setup_postdata( $post );

        $postdate = explode( " ", $post->post_modified );

        $sitemap .= '<url>'.
          '<loc>' . get_permalink( $post->ID ) . '</loc>' .
          '<lastmod>' . $postdate[0] . '</lastmod>' .
          '<changefreq>monthly</changefreq>' .
         '</url>';
      }

    $sitemap .= '</urlset>';

    $fp = fopen( ABSPATH . 'sitemap.xml', 'w' );

    fwrite( $fp, $sitemap );
    fclose( $fp );
}

add_filter('site_option_active_sitewide_plugins', 'modify_sitewide_plugins');

function modify_sitewide_plugins($value) {
    global $current_blog;

     if ( is_page_template( 'template-eboy_v3.php' ) ) {
      //  unset($value['woocommerce/woocommerce.php']);
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



remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

function woo_remove_product_tabs( $tabs ) {

    unset( $tabs['description'] );      	// Remove the description tab
    unset( $tabs['reviews'] ); 			// Remove the reviews tab
    unset( $tabs['additional_information'] );  	// Remove the additional information tab

    return $tabs;

}

// Inserts tabs under the main right product content
add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 60 );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);

add_action( 'woocommerce_product_title', 'woocommerce_template_single_title', 5 );

add_action( 'wpo_wcpdf_after_item_meta', 'wpo_wcpdf_show_product_full_description', 10, 3 );
function wpo_wcpdf_show_product_full_description ( $template_type, $item, $order ) {
    if (empty($item['product'])) return;
    if ( method_exists( $item['product'], 'get_description' ) ) {
        $_product = $item['product']->is_type( 'variation' ) ? wc_get_product( $item['product']->get_parent_id() ) : $item['product'];
        $description = $_product->get_description();
    } else { // WC 2.6 or older:
        $description = $item['product']->post->post_content;
    }

    printf('<div class="product-description">%s</div>', $description );
}

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text' );    // 2.1 +

function woo_custom_cart_button_text() { global $product;
        $price = $product->get_price_html();

        echo '<span class="align-bottom">';
        echo $price;
        return __('Buy', 'woocommerce' );
        echo '</span>';

}

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

               function woocommerce_single_product_sidebar_cat() {
                 $product_categories = get_terms('product_cat');
       foreach ($product_categories as $product_cat) {
       	echo $product_cat->name;
       }
     }
add_action ('woocommerce_single_product_sidebar', 'woocommerce_single_product_sidebar_cat');
//remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );

// Remove each style one by one
add_filter( 'woocommerce_enqueue_styles', 'jk_dequeue_styles' );
function jk_dequeue_styles( $enqueue_styles ) {
	unset( $enqueue_styles['woocommerce-general'] );	// Remove the gloss
	unset( $enqueue_styles['woocommerce-layout'] );		// Remove the layout
	unset( $enqueue_styles['woocommerce-smallscreen'] );	// Remove the smallscreen optimisation
	return $enqueue_styles;
}

// Or just remove them all in one line
add_filter( 'woocommerce_enqueue_styles', '__return_false' );



function eboy_woocommerce_current_tags_links() {
  $output = array();

// get an array of the WP_Term objects for a defined product ID
$terms = wp_get_post_terms( get_the_id(), 'product_tag' );

// Loop through each product tag for the current product
if( count($terms) > 0 ){
    foreach($terms as $term){
        $term_id = $term->term_id; // Product tag Id
        $term_name = $term->name; // Product tag Name
        $term_slug = $term->slug; // Product tag slug
        $term_link = get_term_link( $term, 'product_tag' ); // Product tag link

        // Set the product tag names in an array
        $output[] = $term_name;
    }
    // Set the array in a coma separated string of product tags for example
    $output = implode( ', ', $output );

    // Display the coma separated string of the product tags
    echo $output;
}
}
add_action ('eboy_woocommerce_current_tags', 'eboy_woocommerce_current_tags_links');


function eboy_woocommerce_current_tags_sketo() {
  $output = array();

// get an array of the WP_Term objects for a defined product ID
$terms = wp_get_post_terms( get_the_id(), 'product_cat' );

// Loop through each product tag for the current product
if( count($terms) > 0 ){
    foreach($terms as $term){
        $term_id = $term->term_id; // Product tag Id
        $term_name = $term->name; // Product tag Name
        $term_slug = $term->slug; // Product tag slug
        $term_link = get_term_link( $term, 'product_cat' ); // Product tag link

        // Set the product tag names in an array
        $output[] = $term_slug;
    }
    // Set the array in a coma separated string of product tags for example
    $output = implode( ' ', $output );

    // Display the coma separated string of the product tags
    echo $output;
}
}
add_action ('eboy_woocommerce_current_tags_thumb', 'eboy_woocommerce_current_tags_sketo');


function eboy_woocommerce_categories() { ?>
  <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
        <div class="filters btn-group mr-2 filter-button-group" role="group" aria-label="First group" id="box2">
          <ul id="menu2">
              <?php $filter_icon		= '<img class="ico svg-convert" src=" ' .get_template_directory_uri() .'/dist/images/ico_filter.svg">'; ?>

            <li class="active pl-0 filter_index" data-filter="*"><a href="javascript:;">All</a></li>
<?php
$tags = get_terms( 'product_cat', array(
  'smallest' => 1, // size of least used tag
  'largest'  => 2, // size of most used tag
  'unit'     => 'rem', // unit for sizing the tags
  'number'   => 45, // displays at most 45 tags
  'orderby'  => 'count', // order tags alphabetically
  'order'    => 'DESC', // order tags by ascending order
  'show_count'=> 0, // you can even make tags for custom taxonomies
  'hide_empty' => true
) );

if ( $tags ) :
    foreach ( $tags as $tag ) : ?>
      <li data-filter=".<?php echo esc_html( $tag->slug ); ?>">
        <a href="javascript:;" >
        <?php echo esc_html( $tag->name ); ?>
        </a>
      </li>
    <?php endforeach; ?>
<?php endif; ?>
</ul>
</div>
</div>
<?php }
add_action ('eboy_woocommerce_portfolio', 'eboy_woocommerce_categories', 10);

/**
 * Change number of related products output
 */
function woo_related_products_limit() {
  global $product;

	$args['posts_per_page'] = 6;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'jk_related_products_args' );
  function jk_related_products_args( $args ) {
	$args['posts_per_page'] = 6; // 4 related products
	$args['columns'] = 2; // arranged in 2 columns
	return $args;
}
