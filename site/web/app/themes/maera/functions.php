<?php

/**
 * The Maera class autoloader.
 * Finds the path to a class that we're requiring and includes the file.
 */
function maera_autoload_classes( $class_name ) {

	if ( class_exists( $class_name ) ) {
		return;
	}

	$class_path = get_template_directory() . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-' . strtolower( str_replace( '_', '-', $class_name ) ) . '.php';
	if ( file_exists( $class_path ) ) {
		include $class_path;
	}

}
// Run the autoloader
spl_autoload_register( 'maera_autoload_classes' );

require_once( get_template_directory() . '/includes/template-hierarchy.php' );
require_once( get_template_directory() . '/includes/utils.php' );
require_once( get_template_directory() . '/includes/widgets.php' );
require_once( get_template_directory() . '/includes/class-maera-timber.php' );

/**
 * Dummy function to prevent fatal errors with the Tonesque library
 * Only used when Jetpack is not installed.
 */
if ( ! function_exists( 'jetpack_require_lib' ) && ! is_admin() ) {
	function jetpack_require_lib() {}
}

function Maera() {
	return Maera::get_instance();
}

// Global
$GLOBALS['maera'] = maera();
global $maera;


// Load our Maera_EDD class if EDD is installed
if ( class_exists( 'Easy_Digital_Downloads' ) ) {
	Maera_EDD::get_instance();
}



function maera_replace_title_if_front_page_is_posts( $title ) {
	if ( 'Archives' == $title ) {
		$title = 'Blog';
	}

	return $title;
}

add_filter( 'get_the_archive_title', 'maera_replace_title_if_front_page_is_posts' );