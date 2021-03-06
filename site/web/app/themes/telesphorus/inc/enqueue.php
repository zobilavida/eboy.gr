<?php
/**
 * telesphorus enqueue scripts
 *
 * @package telesphorus
 */

if ( ! function_exists( 'telesphorus_scripts' ) ) {
	/**
	 * Load theme's JavaScript sources.
	 */
	function telesphorus_scripts() {
		// Get the theme data.
		$the_theme = wp_get_theme();
		$theme_version = $the_theme->get( 'Version' );
		
		$css_version = $theme_version . '.' . filemtime(get_template_directory() . '/css/theme.min.css');
		wp_enqueue_style( 'telesphorus-styles', get_stylesheet_directory_uri() . '/css/theme.min.css', array(), $css_version );

		wp_enqueue_script( 'jquery');
		wp_enqueue_script( 'popper-scripts', get_template_directory_uri() . '/js/popper.min.js', array(), $theme_version, true);
		
		$js_version = $theme_version . '.' . filemtime(get_template_directory() . '/js/theme.min.js');
		wp_enqueue_script( 'telesphorus-scripts', get_template_directory_uri() . '/js/theme.min.js', array(), $js_version, true );
		wp_enqueue_script( 'telesphorus-scripts', get_template_directory_uri() . '/js/theme.min.js', array(), $the_theme->get( 'Version' ), true );
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
} // endif function_exists( 'telesphorus_scripts' ).

add_action( 'wp_enqueue_scripts', 'telesphorus_scripts' );
