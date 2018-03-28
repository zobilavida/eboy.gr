<?php
/*
Plugin Name: eboywp - Select2
Plugin URI: https://eboywp.com/
Description: Adds the Select2 facet type
Version: 1.2.1
Author: Matt Gibbs
Author URI: https://eboywp.com/
GitHub Plugin URI: https://github.com/mgibbs189/eboywp-select2
GitHub Branch: 1.2.1

Copyright 2014 Matt Gibbs

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

// exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * WordPress init hook
 */
add_action( 'init' , 'ewps2_init' );


/**
 * Intialize facet registration and any assets
 */
function ewps2_init() {
    add_filter( 'eboywp_facet_types', 'ewps2_facet_types' );

    wp_enqueue_script('select2',
        plugins_url( 'eboywp-select2' ) . '/select2/select2.min.js', array( 'jquery' ), '3.5.1' );

    wp_enqueue_style( 'select2',
        plugins_url( 'eboywp-select2' ) . '/select2/select2.css', array(), '3.5.1' );
}


/**
 * Register the facet type
 */
function ewps2_facet_types( $facet_types ) {
    include( dirname( __FILE__ ) . '/select2.php' );
    $facet_types['select2'] = new eboywp_Facet_Select2();
    return $facet_types;
}
