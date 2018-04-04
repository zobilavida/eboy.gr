<?php
/*
Plugin Name: eboywp
Description: WordPress Filtering
Version: 1
Author: Giannis Sergentakis
Author URI: https://eboy.gr/

Copyright 2018 eboy

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

defined( 'ABSPATH' ) or exit;

class eboywp
{

    public $ajax;
    public $facet;
    public $helper;
    public $indexer;
    public $display;
    private static $instance;


    function __construct() {

        // php check
        if ( version_compare( phpversion(), '5.3', '<' ) ) {
            add_action( 'admin_notices', array( $this, 'upgrade_notice' ) );
            return;
        }

        // setup variables
        define( 'eboywp_VERSION', '3.1.5' );
        define( 'eboywp_DIR', dirname( __FILE__ ) );
        define( 'eboywp_URL', plugins_url( '', __FILE__ ) );
        define( 'eboywp_BASENAME', plugin_basename( __FILE__ ) );

        // get the gears turning
        include( eboywp_DIR . '/includes/class-init.php' );
    }


    /**
     * Singleton
     */
    public static function instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self;
        }
        return self::$instance;
    }


    /**
     * Require PHP 5.3+
     */
    function upgrade_notice() {
        $message = __( 'eboywp requires PHP %s or above. Please contact your host and request a PHP upgrade.', 'EWP' );
        echo '<div class="error"><p>' . sprintf( $message, '5.3' ) . '</p></div>';
    }
}


function EWP() {
    return eboywp::instance();
}


EWP();
