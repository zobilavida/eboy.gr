<?php
/*
Plugin Name: EboytWP - Load More
Description: Adds a shortcode to generate a "Load more" button
Version: 0.2
Author: EboytWP, LLC
Author URI: https://eboytwp.com/
GitHub URI: eboytwp/eboytwp-load-more
*/

defined( 'ABSPATH' ) or exit;

class EboytWP_Load_More_Addon
{

    function __construct() {
        add_filter( 'eboytwp_assets', array( $this, 'assets' ) );
        add_filter( 'eboytwp_shortcode_html', array( $this, 'shortcode' ), 10, 2 );
        add_filter( 'eboytwp_query_args', array( $this, 'query_args' ), 10, 2 );
    }


    /**
     * On pageload, update posts_per_page if we detect a "load_more" URL variable
     */
    function query_args( $args, $class ) {
        if ( isset( $class->ajax_params['is_preload'] ) ) {
            $url_var = FWP()->helper->get_setting( 'prefix' ) . 'load_more';

            if ( isset( $class->http_params['get'][ $url_var ] ) ) {
                $paged = (int) $class->http_params['get'][ $url_var ];
                $per_page = (int) empty( $args['posts_per_page'] ) ? get_option( 'posts_per_page' ) : $args['posts_per_page'];
                $args['posts_per_page'] = ( $paged * $per_page );
            }
        }

        return $args;
    }


    function assets( $assets ) {
        $assets['eboytwp-load-more.js'] = plugins_url( '', __FILE__ ) . '/eboytwp-load-more.js';
        return $assets;
    }


    function shortcode( $output, $atts ) {
        if ( isset( $atts['load_more'] ) ) {
            $label = isset( $atts['label'] ) ? $atts['label'] : __( 'Load more', 'fwp-load-more' );
            $output = '<button class="fwp-load-more">' . esc_attr( $label ) . '</button>';
        }
        return $output;
    }
}


new EboytWP_Load_More_Addon();
