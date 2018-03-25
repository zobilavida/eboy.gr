<?php

class eboywp_Integration_EDD
{

    function __construct() {
        add_filter( 'eboywp_facet_sources', array( $this, 'exclude_data_sources' ) );
        add_filter( 'edd_downloads_query', array( $this, 'edd_downloads_query' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ) );
    }


    /**
     * Trigger some EDD code on eboywp-loaded
     * @since 2.0.4
     */
    function front_scripts() {
        EWP()->display->assets['edd.js'] = eboywp_URL . '/includes/integrations/edd/edd.js';
    }


    /**
     * Intercept EDD's [downloads] shortcode
     * @since 2.0.4
     */
    function edd_downloads_query( $query ) {
        if ( ! empty( EWP()->facet->query_args ) && 'wp' == EWP()->facet->template['name'] ) {
            $query = array_merge( $query, EWP()->facet->query_args );
        }

        return $query;
    }


    /**
     * Exclude specific EDD custom fields
     * @since 2.4
     */
    function exclude_data_sources( $sources ) {
        $prefixes = array( '_edd_discount', '_edd_log', '_edd_payment' );
        foreach ( $sources['custom_fields']['choices'] as $key => $val ) {
            foreach ( $prefixes as $prefix ) {
                if ( 0 === strpos( $val, $prefix ) ) {
                    unset( $sources['custom_fields']['choices'][ $key ] );
                }
            }
        }

        return $sources;
    }
}


if ( is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) ) {
    new eboywp_Integration_EDD();
}
