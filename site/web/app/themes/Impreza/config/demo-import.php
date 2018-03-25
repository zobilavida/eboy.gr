<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme's demo-import settings
 *
 * @filter us_config_demo-import
 */
return array(
	'main' => array(
		'title' => 'Main Demo',
		'preview_url' => 'http://impreza.us-themes.com/',
		'front_page' => 'Home',
		'content' => array(
			'pages',
			'posts',
			'portfolio_items',
			'testimonials',
			'products',
			'widgets',
		),
		'sliders' => array(
			'slider-home.zip',
		),
		'sidebars' => array(
			'shop_sidebar' => 'Shop Sidebar',
			'bbpress_sidebar' => 'BBPress Sidebar',
			'sidebar-8' => 'FAQ',
			'sidebar-9' => 'Login',
		),
	),
	'onepage' => array(
		'title' => 'One Page Demo',
		'preview_url' => 'http://impreza2.us-themes.com/',
		'front_page' => 'Home',
		'content' => array(
			'pages',
			'posts',
			'portfolio_items',
			'testimonials',
		),
	),
	'creative' => array(
		'title' => 'Creative Agency Demo',
		'preview_url' => 'http://impreza3.us-themes.com/',
		'front_page' => 'Home',
		'content' => array(
			'pages',
			'posts',
			'portfolio_items',
			'testimonials',
		),
		'sliders' => array(
			'slider-main.zip',
		),
	),
	'portfolio' => array(
		'title' => 'Portfolio Demo',
		'preview_url' => 'http://impreza4.us-themes.com/',
		'front_page' => 'Portfolio',
		'content' => array(
			'pages',
			'posts',
			'portfolio_items',
		),
		'sliders' => array(
			'slider-instagram.zip',
			'slider-portfolio-1.zip',
			'slider-portfolio-2.zip',
			'slider-portfolio-3.zip',
			'slider-portfolio-4.zip',
		),
	),
	'blog' => array(
		'title' => 'Blog Demo',
		'preview_url' => 'http://impreza5.us-themes.com/',
		'front_page' => 'Home',
		'content' => array(
			'pages',
			'posts',
			'widgets',
		),
		'sliders' => array(
			'slider-popular-posts.zip',
			'slider-posts-carousel.zip',
			'slider-recent_posts.zip',
		),
	),
	'restaurant' => array(
		'title' => 'Restaurant Demo',
		'preview_url' => 'http://impreza6.us-themes.com/',
		'front_page' => 'Home',
		'content' => array(
			'pages',
			'posts',
			'testimonials',
		),
		'sliders' => array(
			'slider-home.zip',
		),
	),
	'photography' => array(
		'title' => 'Photography Demo',
		'preview_url' => 'http://impreza7.us-themes.com/',
		'front_page' => 'Portrait Series',
		'content' => array(
			'pages',
			'portfolio_items',
		),
		'sliders' => array(
			'slider-portfolio-carousel.zip',
		),
	),
	'mobile-app' => array(
		'title' => 'Mobile App Demo',
		'preview_url' => 'http://impreza8.us-themes.com/',
		'front_page' => 'Variant 1',
		'content' => array(
			'pages',
			'testimonials',
		),
	),
	'mini-shop' => array(
		'title' => 'Minimalist Shop Demo',
		'preview_url' => 'http://impreza9.us-themes.com/',
		'front_page' => 'Shop 1',
		'content' => array(
			'pages',
			'products',
		),
		'sliders' => array(
			'slider-products-1.zip',
			'slider-products-2.zip',
		),
	),
);
