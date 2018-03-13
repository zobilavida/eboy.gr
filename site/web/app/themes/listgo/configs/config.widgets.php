<?php

return array(
	array(
		'name'          => esc_html__( 'Events Sidebar', 'listgo' ),
		'id'            => 'wiloke-events-sidebar',
		'description'   => esc_html__( 'Widgets in this area will be shown on the Events Page Template', 'listgo' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget_title">',
		'after_title'   => '</h4>',
	),
	array(
		'name'          => esc_html__( 'Listing Sidebar', 'listgo' ),
		'id'            => 'wiloke-listing-sidebar',
		'description'   => esc_html__( 'Widgets in this area will be shown on Listing Location page, Listing Category page and Listing Template page.', 'listgo' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget_title">',
		'after_title'   => '</h4>',
	),
	array(
		'name'          => esc_html__( 'Single Listing Sidebar', 'listgo' ),
		'id'            => 'wiloke-singular-listing-sidebar',
		'description'   => esc_html__( 'All widgets in this area will be shown only on the single listing page.', 'listgo' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget_title">',
		'after_title'   => '</h4>',
	),
    array(
        'name'          => esc_html__( 'Blog Sidebar', 'listgo' ),
        'id'            => 'wiloke-blog-sidebar',
        'description'   => esc_html__( 'Widgets in this area will be shown on Blog, Archive, Search and Single page.', 'listgo' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget_title">',
        'after_title'   => '</h4>',
    ),
	array(
		'name'          => esc_html__( 'WooCommerce Sidebar', 'listgo' ),
		'id'            => 'wiloke-woocommerce-sidebar',
		'description'   => esc_html__( 'Widgets in this area will be shown on whole WooCommerce pages.', 'listgo' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget_title">',
		'after_title'   => '</h4>',
	),
	array(
		'name'          => esc_html__( 'Footer 1', 'listgo' ),
		'id'            => 'wiloke-footer-1',
		'description'   => esc_html__( 'We recommend you to use only one widget item for this area', 'listgo' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget_title">',
		'after_title'   => '</h4>',
	),
	array(
		'name'          => esc_html__( 'Footer 2', 'listgo' ),
		'id'            => 'wiloke-footer-2',
		'description'   => esc_html__( 'We recommend you to use only one widget item for this area', 'listgo' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget_title">',
		'after_title'   => '</h4>',
	),
	array(
		'name'          => esc_html__( 'Footer 3', 'listgo' ),
		'id'            => 'wiloke-footer-3',
		'description'   => esc_html__( 'We recommend you to use only one widget item for this area', 'listgo' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget_title">',
		'after_title'   => '</h4>',
	)
);