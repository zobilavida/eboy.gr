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

// Include custom navwalker
require_once('bs4navwalker.php');

// Register WordPress nav menu
register_nav_menu('top', 'Top menu');

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


add_action ('customize_register', 'themeslug_theme_customizer');

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

add_action( 'publish_post', 'itsg_create_sitemap' );
add_action( 'publish_page', 'itsg_create_sitemap' );
add_action( 'save_post_my_post_type', 'itsg_create_sitemap' );


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


if ( ! function_exists('custom_stores_post_type') ) {

// Register Custom Post Type
function custom_stores_post_type() {

	$labels = array(
		'name'                  => _x( 'Stores', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Store', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Stores', 'text_domain' ),
		'name_admin_bar'        => __( 'Store', 'text_domain' ),
		'archives'              => __( 'Stores Archives', 'text_domain' ),
		'attributes'            => __( 'Stores Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Store:', 'text_domain' ),
		'all_items'             => __( 'All Stores', 'text_domain' ),
		'add_new_item'          => __( 'Add New Store', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Store', 'text_domain' ),
		'edit_item'             => __( 'Edit Store', 'text_domain' ),
		'update_item'           => __( 'Update Store', 'text_domain' ),
		'view_item'             => __( 'View Store', 'text_domain' ),
		'view_items'            => __( 'View Stores', 'text_domain' ),
		'search_items'          => __( 'Search Store', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into Store', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Store', 'text_domain' ),
		'items_list'            => __( 'Stores list', 'text_domain' ),
		'items_list_navigation' => __( 'Stores list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter Stores list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Store', 'text_domain' ),
		'description'           => __( 'Stores Directory Listing', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'taxonomies'            => array( 'store_category' ),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
	);
	register_post_type( 'stores', $args );

}
add_action( 'init', 'custom_stores_post_type', 0 );

}

// Register Custom Taxonomy
function custom_store_cats() {

	$labels = array(
		'name'                       => _x( 'Store Category', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Stores Category', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Stores Categories', 'text_domain' ),
		'all_items'                  => __( 'All Stores Categories', 'text_domain' ),
		'parent_item'                => __( 'Parent Stores Categories', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New Stores Categories Name', 'text_domain' ),
		'add_new_item'               => __( 'Add New Store Category', 'text_domain' ),
		'edit_item'                  => __( 'Edit Store Category', 'text_domain' ),
		'update_item'                => __( 'Update Store Category', 'text_domain' ),
		'view_item'                  => __( 'View Store Category', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate Stores Categories with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove Stores Categories', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular Stores Categories', 'text_domain' ),
		'search_items'               => __( 'Search Stores Categories', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
		'no_terms'                   => __( 'No Stores Categories', 'text_domain' ),
		'items_list'                 => __( 'Stores Categories list', 'text_domain' ),
		'items_list_navigation'      => __( 'Stores Categories list navigation', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'store_cat', array( 'stores' ), $args );

}
add_action( 'init', 'custom_store_cats', 0 );


// Admin Columns
function stores_columns($columns)
{
    $columns = array(
        'cb'         => '<input type="checkbox" />',
        'title'     => 'Store Name',
        'country'     => 'Country',
				'state'     => 'State',
				'city'     => 'City',
				'street'     => 'Street Address',
				'zip'     => 'Zip',
        'date'        =>    'Date',
    );
    return $columns;
}

function stores_columns_fields($column)
{
    global $post;

    if ($column == 'country') {
        echo get_field( "country", $post->ID );
    }
    else {
         echo '';
    }
		if ($column == 'state') {
        echo get_field( "state", $post->ID );
    }
    else {
         echo '';
    }
		if ($column == 'street') {
        echo get_field( "street_address", $post->ID );
    }
    else {
         echo '';
    }
		if ($column == 'zip') {
        echo get_field( "zip", $post->ID );
    }
    else {
         echo '';
    }
		if ($column == 'city') {
        echo get_field( "city", $post->ID );
    }
    else {
         echo '';
    }
}

add_action("manage_stores_posts_custom_column", "stores_columns_fields");
add_filter("manage_stores_posts_columns", "stores_columns");


// Google API Key
function my_acf_google_map_api( $api ){

	$api['key'] = 'AIzaSyAY55sLjGdZyuE5fX9gIH0NegqSeB24LEU';

	return $api;

}

add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');

function product_cats() {
  $args_t = array(
          'taxonomy'     => 'product_cat',
          //'include'      => array( 16, 15, 17 ),
          'orderby' => 'meta_value',
      );
      $thirds_categories = get_categories( $args_t );
      foreach ( $thirds_categories as $cat ) {
          if( $cat->category == 0 ) {
              $cat_class = mb_strtolower($cat->name);
              $image = wp_get_attachment_url( $thumbnail_id );
              $cat_thumb_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
              $cat_thumb_url = wp_get_attachment_thumb_url( $cat_thumb_id );
              $term_link = get_term_link( $cat, 'product_cat' );
              $thmb = wp_get_attachment_image_src($cat_thumb_id, 'large');

              ?>

              <div class="container">
                <div class="row">
                  <div class="col-6">
                      <a href="<?php echo $term_link; ?>">
                          <img src="<?php echo $thmb[0]; ?>" alt="<?php echo $cat->name; ?>" />
                      </a>
                    </div>
                    <div class="col-6">
                      <h4> <?php echo $cat->name; ?> </h4>
                      <p><?php echo $cat->description; ?> </p>
                      <button>View Products</button>
                      </div>
                      </div>
              </div>
         <?php }
      } wp_reset_query();
}

add_action("woocommerce_product_cats", "product_cats");


function page_content() {
  echo '<div class="container">';
  echo the_content();
  echo '</div>';
}

add_action("custom_functions", "page_content");
