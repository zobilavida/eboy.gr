<?php
/*
Plugin Name: EboyWP - Conditional Logic
Description: Toggle facets based on certain conditions
Version: 1.2.2
Author: EboyWP, LLC
Author URI: https://eboywp.com/
GitHub URI: eboywp/eboywp-conditional-logic
*/

defined( 'ABSPATH' ) or exit;

class EboyWP_Conditional_Logic_Addon
{

    public $rules;
    public $facets = array();
    public $templates = array();


    function __construct() {

        define( 'EWPCL_VERSION', '1.2.2' );
        define( 'EWPCL_DIR', dirname( __FILE__ ) );
        define( 'EWPCL_URL', plugins_url( '', __FILE__ ) );
        define( 'EWPCL_BASENAME', plugin_basename( __FILE__ ) );

        add_action( 'init', array( $this, 'init' ), 12 );
    }


    function init() {
        if ( ! function_exists( 'EWP' ) ) {
            return;
        }

        $this->facets = EWP()->helper->get_facets();
        $this->templates = EWP()->helper->get_templates();

        // load settings
        $rulesets = get_option( 'fwpcl_rulesets' );
        $this->rulesets = empty( $rulesets ) ? array() : json_decode( $rulesets, true );

        // register assets
        wp_register_script( 'fwpcl-front', EWPCL_URL . '/assets/js/front.js', array( 'jquery' ), EWPCL_VERSION, true );
        wp_register_style( 'fwpcl-front', EWPCL_URL . '/assets/css/front.css', array(), EWPCL_VERSION );

        // ajax
        add_action( 'wp_ajax_fwpcl_import', array( $this, 'import' ) );
        add_action( 'wp_ajax_fwpcl_save', array( $this, 'save_rules' ) );

        // wp hooks
        add_action( 'wp_footer', array( $this, 'render_assets' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }


    function import() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $rulesets = stripslashes( $_POST['import_code'] );
        update_option( 'fwpcl_rulesets', $rulesets );
        _e( 'All done!', 'fwpcl' );
        exit;
    }


    function save_rules() {
        if ( current_user_can( 'manage_options' ) ) {
            $rulesets = stripslashes( $_POST['data'] );
            $json_test = json_decode( $rulesets, true );

            // check for valid JSON
            if ( is_array( $json_test ) ) {
                update_option( 'fwpcl_rulesets', $rulesets );
                _e( 'Rules saved', 'fwpcl' );
            }
            else {
                _e( 'Error: invalid JSON', 'fwpcl' );
            }
        }
        exit;
    }


    function admin_menu() {
        add_options_page( 'EboyWP Logic', 'EboyWP Logic', 'manage_options', 'fwpcl-admin', array( $this, 'settings_page' ) );
    }


    function enqueue_scripts( $hook ) {
        if ( 'settings_page_fwpcl-admin' == $hook ) {
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_style( 'media-views' );
        }
    }


    function settings_page() {
        include( dirname( __FILE__ ) . '/page-settings.php' );
    }


    function render_assets() {
        wp_enqueue_style( 'fwpcl-front' );
        wp_enqueue_script( 'fwpcl-front' );
        wp_localize_script( 'fwpcl-front', 'EWPCL', array( 'rulesets' => $this->rulesets ) );
    }
}


new EboyWP_Conditional_Logic_Addon();
