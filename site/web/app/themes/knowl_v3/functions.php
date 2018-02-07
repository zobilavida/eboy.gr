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
require_once('bs4navwalker.php');

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


add_post_type_support( 'page', 'excerpt' );
// Register Custom Post Type
function service() {

	$labels = array(
		'name'                  => _x( 'Services', 'Post Type General Name', 'knowl' ),
		'singular_name'         => _x( 'Service', 'Post Type Singular Name', 'knowl' ),
		'menu_name'             => __( 'Services', 'knowl' ),
		'name_admin_bar'        => __( 'Service', 'knowl' ),
		'archives'              => __( 'Item Archives', 'knowl' ),
		'attributes'            => __( 'Item Attributes', 'knowl' ),
		'parent_item_colon'     => __( 'Parent Item:', 'knowl' ),
		'all_items'             => __( 'All Items', 'knowl' ),
		'add_new_item'          => __( 'Add New Item', 'knowl' ),
		'add_new'               => __( 'Add New Service', 'knowl' ),
		'new_item'              => __( 'New Item', 'knowl' ),
		'edit_item'             => __( 'Edit Item', 'knowl' ),
		'update_item'           => __( 'Update Item', 'knowl' ),
		'view_item'             => __( 'View Item', 'knowl' ),
		'view_items'            => __( 'View Items', 'knowl' ),
		'search_items'          => __( 'Search Item', 'knowl' ),
		'not_found'             => __( 'Not found', 'knowl' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'knowl' ),
		'featured_image'        => __( 'Featured Image', 'knowl' ),
		'set_featured_image'    => __( 'Set featured image', 'knowl' ),
		'remove_featured_image' => __( 'Remove featured image', 'knowl' ),
		'use_featured_image'    => __( 'Use as featured image', 'knowl' ),
		'insert_into_item'      => __( 'Insert into item', 'knowl' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'knowl' ),
		'items_list'            => __( 'Items list', 'knowl' ),
		'items_list_navigation' => __( 'Items list navigation', 'knowl' ),
		'filter_items_list'     => __( 'Filter items list', 'knowl' ),
	);
	$rewrite = array(
		'slug'                  => 'services',
		'with_front'            => true,
		'pages'                 => true,
		'feeds'                 => true,
	);
	$args = array(
		'label'                 => __( 'Service', 'knowl' ),
		'description'           => __( 'Services Description', 'knowl' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields', 'excerpt' ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
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
		'rewrite'               => $rewrite,
		'capability_type'       => 'post',
		'show_in_rest'          => false,
	);
	register_post_type( 'services', $args );

}
add_action( 'init', 'service', 0 );

// Register Custom Post Type
function course() {

	$labels = array(
		'name'                  => _x( 'Courses', 'Post Type General Name', 'knowl' ),
		'singular_name'         => _x( 'Course', 'Post Type Singular Name', 'knowl' ),
		'menu_name'             => __( 'Courses', 'knowl' ),
		'name_admin_bar'        => __( 'Course', 'knowl' ),
		'archives'              => __( 'Item Archives', 'knowl' ),
		'attributes'            => __( 'Item Attributes', 'knowl' ),
		'parent_item_colon'     => __( 'Parent Item:', 'knowl' ),
		'all_items'             => __( 'All Items', 'knowl' ),
		'add_new_item'          => __( 'Add New Item', 'knowl' ),
		'add_new'               => __( 'Add New Course', 'knowl' ),
		'new_item'              => __( 'New Item', 'knowl' ),
		'edit_item'             => __( 'Edit Item', 'knowl' ),
		'update_item'           => __( 'Update Item', 'knowl' ),
		'view_item'             => __( 'View Item', 'knowl' ),
		'view_items'            => __( 'View Items', 'knowl' ),
		'search_items'          => __( 'Search Item', 'knowl' ),
		'not_found'             => __( 'Not found', 'knowl' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'knowl' ),
		'featured_image'        => __( 'Featured Image', 'knowl' ),
		'set_featured_image'    => __( 'Set featured image', 'knowl' ),
		'remove_featured_image' => __( 'Remove featured image', 'knowl' ),
		'use_featured_image'    => __( 'Use as featured image', 'knowl' ),
		'insert_into_item'      => __( 'Insert into item', 'knowl' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'knowl' ),
		'items_list'            => __( 'Items list', 'knowl' ),
		'items_list_navigation' => __( 'Items list navigation', 'knowl' ),
		'filter_items_list'     => __( 'Filter items list', 'knowl' ),
	);
	$rewrite = array(
		'slug'                  => 'courses',
		'with_front'            => true,
		'pages'                 => true,
		'feeds'                 => true,
	);
	$args = array(
		'label'                 => __( 'Course', 'knowl' ),
		'description'           => __( 'Courses Description', 'knowl' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields', 'excerpt' ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
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
		'rewrite'               => $rewrite,
		'capability_type'       => 'post',
		'show_in_rest'          => false,
	);
	register_post_type( 'Courses', $args );

}
add_action( 'init', 'course', 0 );



// Register Custom Post Type
function portfolio() {

	$labels = array(
		'name'                  => _x( 'portfolio', 'Post Type General Name', 'knowl' ),
		'singular_name'         => _x( 'Portfolio', 'Post Type Singular Name', 'knowl' ),
		'menu_name'             => __( 'portfolio', 'knowl' ),
		'name_admin_bar'        => __( 'Portfolio', 'knowl' ),
		'archives'              => __( 'Item Archives', 'knowl' ),
		'attributes'            => __( 'Item Attributes', 'knowl' ),
		'parent_item_colon'     => __( 'Parent Item:', 'knowl' ),
		'all_items'             => __( 'All Items', 'knowl' ),
		'add_new_item'          => __( 'Add New Item', 'knowl' ),
		'add_new'               => __( 'Add New Portfolio', 'knowl' ),
		'new_item'              => __( 'New Item', 'knowl' ),
		'edit_item'             => __( 'Edit Item', 'knowl' ),
		'update_item'           => __( 'Update Item', 'knowl' ),
		'view_item'             => __( 'View Item', 'knowl' ),
		'view_items'            => __( 'View Items', 'knowl' ),
		'search_items'          => __( 'Search Item', 'knowl' ),
		'not_found'             => __( 'Not found', 'knowl' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'knowl' ),
		'featured_image'        => __( 'Featured Image', 'knowl' ),
		'set_featured_image'    => __( 'Set featured image', 'knowl' ),
		'remove_featured_image' => __( 'Remove featured image', 'knowl' ),
		'use_featured_image'    => __( 'Use as featured image', 'knowl' ),
		'insert_into_item'      => __( 'Insert into item', 'knowl' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'knowl' ),
		'items_list'            => __( 'Items list', 'knowl' ),
		'items_list_navigation' => __( 'Items list navigation', 'knowl' ),
		'filter_items_list'     => __( 'Filter items list', 'knowl' ),
	);
	$rewrite = array(
		'slug'                  => 'portfolio',
		'with_front'            => true,
		'pages'                 => true,
		'feeds'                 => true,
	);
	$args = array(
		'label'                 => __( 'Portfolio', 'knowl' ),
		'description'           => __( 'portfolio Description', 'knowl' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields', 'excerpt' ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
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
		'rewrite'               => $rewrite,
		'capability_type'       => 'post',
		'show_in_rest'          => false,
	);
	register_post_type( 'portfolio', $args );

}
add_action( 'init', 'portfolio', 0 );


// Register Custom Post Type
function europe() {

	$labels = array(
		'name'                  => _x( 'europians', 'Post Type General Name', 'knowl' ),
		'singular_name'         => _x( 'europians', 'Post Type Singular Name', 'knowl' ),
		'menu_name'             => __( 'europians', 'knowl' ),
		'name_admin_bar'        => __( 'europians', 'knowl' ),
		'archives'              => __( 'Item Archives', 'knowl' ),
		'attributes'            => __( 'Item Attributes', 'knowl' ),
		'parent_item_colon'     => __( 'Parent Item:', 'knowl' ),
		'all_items'             => __( 'All Items', 'knowl' ),
		'add_new_item'          => __( 'Add New Item', 'knowl' ),
		'add_new'               => __( 'Add New europians', 'knowl' ),
		'new_item'              => __( 'New Item', 'knowl' ),
		'edit_item'             => __( 'Edit Item', 'knowl' ),
		'update_item'           => __( 'Update Item', 'knowl' ),
		'view_item'             => __( 'View Item', 'knowl' ),
		'view_items'            => __( 'View Items', 'knowl' ),
		'search_items'          => __( 'Search Item', 'knowl' ),
		'not_found'             => __( 'Not found', 'knowl' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'knowl' ),
		'featured_image'        => __( 'Featured Image', 'knowl' ),
		'set_featured_image'    => __( 'Set featured image', 'knowl' ),
		'remove_featured_image' => __( 'Remove featured image', 'knowl' ),
		'use_featured_image'    => __( 'Use as featured image', 'knowl' ),
		'insert_into_item'      => __( 'Insert into item', 'knowl' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'knowl' ),
		'items_list'            => __( 'Items list', 'knowl' ),
		'items_list_navigation' => __( 'Items list navigation', 'knowl' ),
		'filter_items_list'     => __( 'Filter items list', 'knowl' ),
	);
	$rewrite = array(
		'slug'                  => 'europians',
		'with_front'            => true,
		'pages'                 => true,
		'feeds'                 => true,
	);
	$args = array(
		'label'                 => __( 'europians', 'knowl' ),
		'description'           => __( 'europians Description', 'knowl' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields', 'excerpt' ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
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
		'rewrite'               => $rewrite,
		'capability_type'       => 'post',
		'show_in_rest'          => false,
	);
	register_post_type( 'europians', $args );

}
add_action( 'init', 'europe', 0 );


// Register Custom Post Type
function profile() {

	$labels = array(
		'name'                  => _x( 'Profile', 'Post Type General Name', 'knowl' ),
		'singular_name'         => _x( 'Profile', 'Post Type Singular Name', 'knowl' ),
		'menu_name'             => __( 'Profile', 'knowl' ),
		'name_admin_bar'        => __( 'Profile', 'knowl' ),
		'archives'              => __( 'Item Archives', 'knowl' ),
		'attributes'            => __( 'Item Attributes', 'knowl' ),
		'parent_item_colon'     => __( 'Parent Item:', 'knowl' ),
		'all_items'             => __( 'All Items', 'knowl' ),
		'add_new_item'          => __( 'Add New Item', 'knowl' ),
		'add_new'               => __( 'Add New Profile', 'knowl' ),
		'new_item'              => __( 'New Item', 'knowl' ),
		'edit_item'             => __( 'Edit Item', 'knowl' ),
		'update_item'           => __( 'Update Item', 'knowl' ),
		'view_item'             => __( 'View Item', 'knowl' ),
		'view_items'            => __( 'View Items', 'knowl' ),
		'search_items'          => __( 'Search Item', 'knowl' ),
		'not_found'             => __( 'Not found', 'knowl' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'knowl' ),
		'featured_image'        => __( 'Featured Image', 'knowl' ),
		'set_featured_image'    => __( 'Set featured image', 'knowl' ),
		'remove_featured_image' => __( 'Remove featured image', 'knowl' ),
		'use_featured_image'    => __( 'Use as featured image', 'knowl' ),
		'insert_into_item'      => __( 'Insert into item', 'knowl' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'knowl' ),
		'items_list'            => __( 'Items list', 'knowl' ),
		'items_list_navigation' => __( 'Items list navigation', 'knowl' ),
		'filter_items_list'     => __( 'Filter items list', 'knowl' ),
	);
	$rewrite = array(
		'slug'                  => 'Profile',
		'with_front'            => true,
		'pages'                 => true,
		'feeds'                 => true,
	);
	$args = array(
		'label'                 => __( 'Profile', 'knowl' ),
		'description'           => __( 'Profile Description', 'knowl' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields', 'excerpt' ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
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
		'rewrite'               => $rewrite,
		'capability_type'       => 'post',
		'show_in_rest'          => false,
	);
	register_post_type( 'Profile', $args );

}
add_action( 'init', 'profile', 0 );



// Register Custom Post Type
function team() {

	$labels = array(
		'name'                  => _x( 'Team', 'Post Type General Name', 'knowl' ),
		'singular_name'         => _x( 'Team', 'Post Type Singular Name', 'knowl' ),
		'menu_name'             => __( 'Team', 'knowl' ),
		'name_admin_bar'        => __( 'Team', 'knowl' ),
		'archives'              => __( 'Item Archives', 'knowl' ),
		'attributes'            => __( 'Item Attributes', 'knowl' ),
		'parent_item_colon'     => __( 'Parent Item:', 'knowl' ),
		'all_items'             => __( 'All Items', 'knowl' ),
		'add_new_item'          => __( 'Add New Item', 'knowl' ),
		'add_new'               => __( 'Add New Team', 'knowl' ),
		'new_item'              => __( 'New Item', 'knowl' ),
		'edit_item'             => __( 'Edit Item', 'knowl' ),
		'update_item'           => __( 'Update Item', 'knowl' ),
		'view_item'             => __( 'View Item', 'knowl' ),
		'view_items'            => __( 'View Items', 'knowl' ),
		'search_items'          => __( 'Search Item', 'knowl' ),
		'not_found'             => __( 'Not found', 'knowl' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'knowl' ),
		'featured_image'        => __( 'Featured Image', 'knowl' ),
		'set_featured_image'    => __( 'Set featured image', 'knowl' ),
		'remove_featured_image' => __( 'Remove featured image', 'knowl' ),
		'use_featured_image'    => __( 'Use as featured image', 'knowl' ),
		'insert_into_item'      => __( 'Insert into item', 'knowl' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'knowl' ),
		'items_list'            => __( 'Items list', 'knowl' ),
		'items_list_navigation' => __( 'Items list navigation', 'knowl' ),
		'filter_items_list'     => __( 'Filter items list', 'knowl' ),
	);
	$rewrite = array(
		'slug'                  => 'Team',
		'with_front'            => true,
		'pages'                 => true,
		'feeds'                 => true,
	);
	$args = array(
		'label'                 => __( 'Team', 'knowl' ),
		'description'           => __( 'Team Description', 'knowl' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields', 'excerpt' ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
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
		'rewrite'               => $rewrite,
		'capability_type'       => 'post',
		'show_in_rest'          => false,
	);
	register_post_type( 'Team', $args );

}
add_action( 'init', 'team', 0 );

// sequentially order posts / custom posts
function updateNumbers() {
/* numbering the published posts, starting with 1 for oldest;
/ creates and updates custom field 'incr_number';
/ to show in post (within the loop) use <?php echo get_post_meta($post->ID,'your_post_type',true); ?>
/ alchymyth 2010 */
global $wpdb;
$querystr = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'europians' ";
$pageposts = $wpdb->get_results($querystr, OBJECT);
$counts = 0 ;
if ($pageposts):
foreach ($pageposts as $post):
$counts++;
add_post_meta($post->ID, 'incr_number', $counts, true);
update_post_meta($post->ID, 'incr_number', $counts);
endforeach;
endif;
}

add_action ( 'publish_post', 'updateNumbers', 11 );
add_action ( 'deleted_post', 'updateNumbers' );
add_action ( 'edit_post', 'updateNumbers' );
