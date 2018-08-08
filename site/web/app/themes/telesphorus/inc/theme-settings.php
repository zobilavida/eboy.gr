<?php
/**
 * Check and setup theme's default settings
 *
 * @package telesphorus
 *
 */

if ( ! function_exists ( 'telesphorus_setup_theme_default_settings' ) ) {
	function telesphorus_setup_theme_default_settings() {

		// check if settings are set, if not set defaults.
		// Caution: DO NOT check existence using === always check with == .
		// Latest blog posts style.
		$telesphorus_posts_index_style = get_theme_mod( 'telesphorus_posts_index_style' );
		if ( '' == $telesphorus_posts_index_style ) {
			set_theme_mod( 'telesphorus_posts_index_style', 'default' );
		}

		// Sidebar position.
		$telesphorus_sidebar_position = get_theme_mod( 'telesphorus_sidebar_position' );
		if ( '' == $telesphorus_sidebar_position ) {
			set_theme_mod( 'telesphorus_sidebar_position', 'right' );
		}

		// Container width.
		$telesphorus_container_type = get_theme_mod( 'telesphorus_container_type' );
		if ( '' == $telesphorus_container_type ) {
			set_theme_mod( 'telesphorus_container_type', 'container' );
		}
	}
}