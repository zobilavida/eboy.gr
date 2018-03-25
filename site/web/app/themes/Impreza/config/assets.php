<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Assets configuration (JS and CSS components)
 *
 * @filter us_config_assets
 */

return array(

	// Base Components
	'general' => array(
		'title' => us_translate_x( 'General', 'settings screen' ),
		'css' => '/css/base/general.css',
		'css_size' => 19,
		'hidden' => TRUE, // Make this component not visible in UI
	),
	'animation' => array(
		'title' => __( 'Animation', 'us' ),
		'css' => '/css/base/animation.css',
		'css_size' => 6,
		'group' => __( 'Base Components', 'us' ),
	),
	'carousel' =>  array(
		'title' => __( 'Carousel', 'us' ),
		'css' => '/css/base/carousel.css',
		'css_size' => 6,
	),
	'columns' => array(
		'title' => us_translate( 'Columns' ),
		'css' => '/css/base/columns.css',
		'css_size' => 10,
	),
	'comments' => array(
		'title' => us_translate( 'Comments' ),
		'css' => '/css/base/comments.css',
		'css_size' => 4,
	),
	'filters' =>  array(
		'title' => us_translate( 'Filter' ),
		'css' => '/css/base/filters.css',
		'css_size' => 2,
	),
	'forms' =>  array(
		'title' => __( 'Forms', 'us' ),
		'css' => '/css/base/forms.css',
		'css_size' => 7,
	),
	'header' =>  array(
		'title' => _x( 'Header', 'site top area', 'us' ),
		'css' => '/css/base/header.css',
		'css_size' => 11,
	),
	'pagination' =>  array(
		'title' => us_translate( 'Pagination' ),
		'css' => '/css/base/pagination.css',
		'css_size' => 6,
	),
	'preloader' =>  array(
		'title' => __( 'Preloader', 'us' ),
		'css' => '/css/base/preloader.css',
		'css_size' => 5,
	),
	'print' =>  array(
		'title' => __( 'Print styles', 'us' ),
		'css' => '/css/base/print.css',
		'css_size' => 3,
	),
	'popup' =>  array(
		'title' => __( 'Popups', 'us' ),
		'css' => '/css/base/popup.css',
		'css_size' => 8,
	),
	'titlebar' =>  array(
		'title' => __( 'Title Bar', 'us' ),
		'css' => '/css/base/titlebar.css',
		'css_size' => 3,
	),
	'font-awesome' =>  array(
		'title' => sprintf( __( '"%s" icons', 'us' ), 'Font Awesome' ),
		'css' => '/framework/css/font-awesome.css',
		'css_size' => 30,
	),
	'material-icons' =>  array(
		'title' => sprintf( __( '"%s" icons', 'us' ), 'Material' ),
		'css' => '/framework/css/material-icons.css',
		'css_size' => 1,
	),

	// Content Elements
	'actionbox' =>  array(
		'title' => __( 'ActionBox', 'us' ),
		'css' => '/css/elements/actionbox.css',
		'group' => __( 'Content Elements', 'us' ),
		'css_size' => 2,
	),
	'blog' => array(
		'title' => us_translate( 'Blog' ),
		'css' => '/css/elements/blog.css',
		'css_size' => 29,
	),
	'buttons' => array(
		'title' => __( 'Buttons', 'us' ),
		'css' => '/css/elements/buttons.css',
		'css_size' => 11,
	),
	'charts' => array(
		'title' => __( 'Charts', 'us' ),
		'css' => '/css/elements/charts.css',
		'css_size' => 1,
	),
	'contacts' => array(
		'title' => us_translate( 'Contact Info' ),
		'css' => '/css/elements/contacts.css',
		'css_size' => 2,
	),
	'counter' =>  array(
		'title' => __( 'Counter', 'us' ),
		'css' => '/css/elements/counter.css',
		'css_size' => 1,
	),
	'gmaps' => array(
		'title' => __( 'Google Maps', 'us' ),
		'css' => '/css/elements/gmaps.css',
		'css_size' => 1,
	),
	'gallery' => array(
		'title' => __( 'Image Gallery', 'us' ),
		'css' => '/css/elements/gallery.css',
		'css_size' => 2,
	),
	'slider' =>  array(
		'title' => __( 'Image Slider', 'us' ),
		'css' => '/css/elements/slider.css',
		'css_size' => 8,
	),
	'iconbox' => array(
		'title' => __( 'IconBox', 'us' ),
		'css' => '/css/elements/iconbox.css',
		'css_size' => 4,
	),
	'logos' => array(
		'title' => __( 'Logos Showcase', 'us' ),
		'css' => '/css/elements/logos.css',
		'css_size' => 2,
	),
	'menu' => array(
		'title' => us_translate( 'Menu' ),
		'css' => '/css/elements/menu.css',
		'css_size' => 17,
	),
	'message' =>  array(
		'title' => __( 'Message Box', 'us' ),
		'css' => '/css/elements/message.css',
		'css_size' => 2,
	),
	'person' => array(
		'title' => __( 'Person', 'us' ),
		'css' => '/css/elements/person.css',
		'css_size' => 7,
	),
	'portfolio' => array(
		'title' => __( 'Portfolio', 'us' ),
		'css' => '/css/elements/portfolio.css',
		'css_size' => 36,
	),
	'pricing' => array(
		'title' => __( 'Pricing Table', 'us' ),
		'css' => '/css/elements/pricing.css',
		'css_size' => 3,
	),
	'progbar' => array(
		'title' => __( 'Progress Bar', 'us' ),
		'css' => '/css/elements/progbar.css',
		'css_size' => 5,
	),
	'scroller' =>  array(
		'title' => __( 'Page Scroller', 'us' ),
		'css' => '/css/elements/scroller.css',
		'css_size' => 2,
	),
	'search' => array(
		'title' => us_translate( 'Search' ),
		'css' => '/css/elements/search.css',
		'css_size' => 7,
	),
	'separator' => array(
		'title' => __( 'Separator', 'us' ),
		'css' => '/css/elements/separator.css',
		'css_size' => 4,
	),
	'sharing' =>  array(
		'title' => __( 'Sharing Buttons', 'us' ),
		'css' => '/css/elements/sharing.css',
		'css_size' => 7,
	),
	'image' =>  array(
		'title' => __( 'Single Image', 'us' ),
		'css' => '/css/elements/image.css',
		'css_size' => 3,
	),
	'socials' =>  array(
		'title' => __( 'Social Links', 'us' ),
		'css' => '/css/elements/socials.css',
		'css_size' => 12,
	),
	'tabs' =>  array(
		'title' => us_translate( 'Tabs', 'js_composer' ) . ', ' . us_translate( 'Tour', 'js_composer' ) . ', ' . us_translate( 'Accordion', 'js_composer' ),
		'css' => '/css/elements/tabs.css',
		'css_size' => 16,
	),
	'testimonials' =>  array(
		'title' => __( 'Testimonials', 'us' ),
		'css' => '/css/elements/testimonials.css',
		'css_size' => 5,
	),
	'video' =>  array(
		'title' => us_translate( 'Video Player', 'js_composer' ),
		'css' => '/css/elements/video.css',
		'css_size' => 1,
	),

	// Plugins
	'bbpress' => array(
		'title' => 'bbPress',
		'css' => '/css/plugins/bbpress.css',
		'css_size' => 31,
		'separated' => TRUE,
		'apply_if' => class_exists( 'bbPress' ),
		'group' => us_translate( 'Plugins' ),
	),
	'gravityforms' =>  array(
		'title' => 'Gravity Forms',
		'css' => '/css/plugins/gravityforms.css',
		'css_size' => 30,
		'separated' => TRUE,
		'apply_if' => class_exists( 'GFForms' ),
	),
	'slider-revolution' =>  array(
		'title' => 'Slider Revolution',
		'css' => '/css/plugins/slider-revolution.css',
		'css_size' => 2,
		'apply_if' => class_exists( 'RevSliderFront' ),
	),
	'tablepress' =>  array(
		'title' => 'TablePress',
		'css' => '/css/plugins/tablepress.css',
		'css_size' => 3,
		'apply_if' => class_exists( 'TablePress' ),
	),
	'tribe-events' =>  array(
		'title' => 'The Events Calendar',
		'css' => '/css/plugins/tribe-events.css',
		'css_size' => 18,
		'separated' => TRUE,
		'apply_if' => class_exists( 'Tribe__Events__Main' ),
	),
	'ultimate-addons' =>  array(
		'title' => 'Ultimate Addons',
		'css' => '/css/plugins/ultimate-addons.css',
		'css_size' => 3,
		'apply_if' => class_exists( 'Ultimate_VC_Addons' ),
	),
	'woocommerce' =>  array(
		'title' => 'WooCommerce',
		'css' => '/css/plugins/woocommerce.css',
		'css_size' => 55,
		'separated' => TRUE,
		'apply_if' => class_exists( 'woocommerce' ),
	),
	'wpml' =>  array(
		'title' => 'WPML',
		'css' => '/css/plugins/wpml.css',
		'css_size' => 2,
		'apply_if' => class_exists( 'SitePress' ),
	),

);
