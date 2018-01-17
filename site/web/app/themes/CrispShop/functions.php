<?php
if ( ! function_exists( 'crispshop_theme_setup' ) ) :

function crispshop_theme_setup() {
	load_theme_textdomain( 'crispshop', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );

	add_theme_support( 'woocommerce' );

	register_nav_menus( array(
		'primary_menu' => esc_html__( 'Primary Menu', 'crispshop' ),
	) );

	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	$GLOBALS['content_width'] = apply_filters( 'crispshop_content_width', 1000 );
}
endif;

add_action( 'after_setup_theme', 'crispshop_theme_setup' );

require get_template_directory() . '/inc/theme-sidebars.php';

require get_template_directory() . '/inc/theme-scripts.php';

require get_template_directory() . '/inc/template-tags.php';

require get_template_directory() . '/inc/extras.php';

require get_template_directory() . '/inc/customizer.php';

require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'crispshop_required_plugins' );

function crispshop_required_plugins() {
    $plugins = array(
	    array(
	        'name' => 'Customize Image Gallery Control',
	        'slug' => 'wp-customize-image-gallery-control',
	        'source' => 'https://github.com/xwp/wp-customize-image-gallery-control/archive/master.zip',
	        'required' => true,
	        'force_activation' => false,
	    ),
	    array(
			'name' => 'MailPoet Newsletters',
			'slug' => 'wysija-newsletters',
			'required'  => true,
		),
		array(
			'name' => 'Contact Form 7',
			'slug' => 'contact-form-7',
			'required'  => true,
		),
		array(
			'name' => 'WooCommerce',
			'slug' => 'woocommerce',
			'required'  => true,
		),
	);
 
    tgmpa( $plugins );
}