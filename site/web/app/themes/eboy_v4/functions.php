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


// Add svg & swf support
function cc_mime_types( $mimes ){
    $mimes['svg'] = 'image/svg+xml';
    $mimes['swf']  = 'application/x-shockwave-flash';

    return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

// Register Custom Post Type
function custom_post_type() {

	$labels = array(
		'name'                  => _x( 'Portfolios', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Portfolio', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Portfolio', 'text_domain' ),
		'name_admin_bar'        => __( 'Portfolio', 'text_domain' ),
		'archives'              => __( 'Portfolio Archives', 'text_domain' ),
		'attributes'            => __( 'Portfolio Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Portfolio:', 'text_domain' ),
		'all_items'             => __( 'All Portfolios', 'text_domain' ),
		'add_new_item'          => __( 'Add Portfolio', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Portfolio', 'text_domain' ),
		'edit_item'             => __( 'Edit Portfolio', 'text_domain' ),
		'update_item'           => __( 'Update Portfolio', 'text_domain' ),
		'view_item'             => __( 'View Portfolio', 'text_domain' ),
		'view_items'            => __( 'View Portfolio', 'text_domain' ),
		'search_items'          => __( 'Search Portfolios', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Portfolio', 'text_domain' ),
		'description'           => __( 'Post Type Description', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes', 'post-formats' ),
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
		'capability_type'       => 'page',
	);
	register_post_type( 'Portfolio', $args );

}
add_action( 'init', 'custom_post_type', 0 );


function eboy_woocommerce_current_tags_links() {
  $output = array();

// get an array of the WP_Term objects for a defined product ID
$terms = wp_get_post_terms( get_the_id(), 'post_tag' );

// Loop through each product tag for the current product
if( count($terms) > 0 ){
    foreach($terms as $term){
        $term_id = $term->term_id; // Product tag Id
        $term_name = $term->name; // Product tag Name
        $term_slug = $term->slug; // Product tag slug
        $term_link = get_term_link( $term, 'post_tag' ); // Product tag link

        // Set the product tag names in an array
        $output[] = '<a href="'.$term_link.'">'.$term_name.'</a>';
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
$terms = wp_get_post_terms( get_the_id(), 'post_tag' );

// Loop through each product tag for the current product
if( count($terms) > 0 ){
    foreach($terms as $term){
        $term_id = $term->term_id; // Product tag Id
        $term_name = $term->name; // Product tag Name
        $term_slug = $term->slug; // Product tag slug
        $term_link = get_term_link( $term, 'post_tag' ); // Product tag link

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
