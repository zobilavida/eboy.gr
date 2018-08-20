<?php
// Register Custom Post Type collection
// Post Type Key: collection
function create_collection_cpt() {

	$labels = array(
		'name' => __( 'collections', 'Post Type General Name', 'sage' ),
		'singular_name' => __( 'collection', 'Post Type Singular Name', 'sage' ),
		'menu_name' => __( 'collections', 'sage' ),
		'name_admin_bar' => __( 'collection', 'sage' ),
		'archives' => __( 'collection Archives', 'sage' ),
		'attributes' => __( 'collection Attributes', 'sage' ),
		'parent_item_colon' => __( 'Parent collection:', 'sage' ),
		'all_items' => __( 'All collections', 'sage' ),
		'add_new_item' => __( 'Add New collection', 'sage' ),
		'add_new' => __( 'Add New', 'sage' ),
		'new_item' => __( 'New collection', 'sage' ),
		'edit_item' => __( 'Edit collection', 'sage' ),
		'update_item' => __( 'Update collection', 'sage' ),
		'view_item' => __( 'View collection', 'sage' ),
		'view_items' => __( 'View collections', 'sage' ),
		'search_items' => __( 'Search collection', 'sage' ),
		'not_found' => __( 'Not found', 'sage' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'sage' ),
		'featured_image' => __( 'Featured Image', 'sage' ),
		'set_featured_image' => __( 'Set featured image', 'sage' ),
		'remove_featured_image' => __( 'Remove featured image', 'sage' ),
		'use_featured_image' => __( 'Use as featured image', 'sage' ),
		'insert_into_item' => __( 'Insert into collection', 'sage' ),
		'uploaded_to_this_item' => __( 'Uploaded to this collection', 'sage' ),
		'items_list' => __( 'collections list', 'sage' ),
		'items_list_navigation' => __( 'collections list navigation', 'sage' ),
		'filter_items_list' => __( 'Filter collections list', 'sage' ),
	);
	$args = array(
		'label' => __( 'collection', 'sage' ),
		'description' => __( 'Collections', 'sage' ),
		'labels' => $labels,
		'menu_icon' => 'dashicons-book-alt',
		'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields', ),
		'taxonomies' => array(),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => true,
		'can_export' => true,
		'has_archive' => true,
		'hierarchical' => true,
		'exclude_from_search' => false,
		'show_in_rest' => true,
		'publicly_queryable' => true,
		'capability_type' => 'post',
	);
	register_post_type( 'collection', $args );

}
add_action( 'init', 'create_collection_cpt', 0 );
