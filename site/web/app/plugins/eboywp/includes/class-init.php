<?php

class eboywp_Init
{

    function __construct() {
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }


    /**
     * Initialize classes and WP hooks
     */
    function init() {

        // i18n
        $this->load_textdomain();

        // is_plugin_active
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        // api 
        include( eboywp_DIR . '/includes/api/fetch.php' );
        include( eboywp_DIR . '/includes/api/refresh.php' );

        // update checks
        if ( is_admin() ) {
            include( eboywp_DIR . '/includes/class-updater.php' );
            include( eboywp_DIR . '/includes/libraries/github-updater.php' );
        }

        // core
        include( eboywp_DIR . '/includes/class-helper.php' );
        include( eboywp_DIR . '/includes/class-ajax.php' );
        include( eboywp_DIR . '/includes/class-renderer.php' );
        include( eboywp_DIR . '/includes/class-diff.php' );
        include( eboywp_DIR . '/includes/class-indexer.php' );
        include( eboywp_DIR . '/includes/class-display.php' );
        include( eboywp_DIR . '/includes/class-overrides.php' );
        include( eboywp_DIR . '/includes/class-settings-admin.php' );
        include( eboywp_DIR . '/includes/class-upgrade.php' );
        include( eboywp_DIR . '/includes/functions.php' );

        new eboywp_Upgrade();
        new eboywp_Overrides();
        new eboywp_API_Fetch();

        EWP()->helper       = new eboywp_Helper();
        EWP()->facet        = new eboywp_Renderer();
        EWP()->diff         = new eboywp_Diff();
        EWP()->indexer      = new eboywp_Indexer();
        EWP()->display      = new eboywp_Display();
        EWP()->ajax         = new eboywp_Ajax();

        // integrations
        include( eboywp_DIR . '/includes/integrations/searchwp/searchwp.php' );
        include( eboywp_DIR . '/includes/integrations/woocommerce/woocommerce.php' );
        include( eboywp_DIR . '/includes/integrations/edd/edd.php' );
        include( eboywp_DIR . '/includes/integrations/acf/acf.php' );

        // hooks
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
        add_filter( 'redirect_canonical', array( $this, 'redirect_canonical' ), 10, 2 );
        add_filter( 'plugin_action_links_eboywp/index.php', array( $this, 'plugin_action_links' ) );
    }


    /**
     * i18n support
     */
    function load_textdomain() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'EWP' );
        $mofile = WP_LANG_DIR . '/eboywp/EWP-' . $locale . '.mo';

        if ( file_exists( $mofile ) ) {
            load_textdomain( 'EWP', $mofile );
        }
        else {
            load_plugin_textdomain( 'EWP', false, dirname( eboywp_BASENAME ) . '/languages/' );
        }
    }


    /**
     * Register the eboywp settings page
     */
    function admin_menu() {
        add_options_page( 'eboywp', 'eboywp', 'manage_options', 'eboywp', array( $this, 'settings_page' ) );
    }


    /**
     * Enqueue jQuery
     */
    function front_scripts() {
        wp_enqueue_script( 'jquery' );
    }


    /**
     * Enqueue admin tooltips
     */
    function admin_scripts( $hook ) {
        if ( 'settings_page_eboywp' == $hook ) {
            wp_enqueue_style( 'media-views' );
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_script( 'jquery-powertip', eboywp_URL . '/assets/vendor/jquery-powertip/jquery.powertip.min.js', array( 'jquery' ), '1.2.0' );
        }
    }


    /**
     * Route to the correct edit screen
     */
    function settings_page() {
        include( eboywp_DIR . '/templates/page-settings.php' );
    }


    /**
     * Prevent WP from redirecting EWP pager to /page/X
     */
    function redirect_canonical( $redirect_url, $requested_url ) {
        if ( false !== strpos( $redirect_url, EWP()->helper->get_setting( 'prefix' ) . 'paged' ) ) {
            return false;
        }
        return $redirect_url;
    }


    /**
     * Add "Settings" link to plugin listing page
     */
    function plugin_action_links( $links ) {
        $settings_link = admin_url( 'options-general.php?page=eboywp' );
        $settings_link = '<a href=" ' . $settings_link . '">' . __( 'Settings', 'EWP' )  . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }


    /**
     * Notify users to install necessary integrations
     */
    function admin_notices() {
        if ( apply_filters( 'eboywp_dismiss_notices', false ) ) {
            return;
        }

        $reqs = array(
            'WPML' => array(
                'is_active' => defined( 'ICL_SITEPRESS_VERSION' ),
                'addon' => 'eboywp-wpml/eboywp-wpml.php',
                'slug' => 'wpml'
            ),
            'Polylang' => array(
                'is_active' => function_exists( 'pll_register_string' ),
                'addon' => 'eboywp-polylang/index.php',
                'slug' => 'polylang'
            ),
            'Relevanssi' => array(
                'is_active' => function_exists( 'relevanssi_search' ),
                'addon' => 'eboywp-relevanssi/eboywp-relevanssi.php',
                'slug' => 'relevanssi'
            )
        );

        $addon = __( 'integration add-on', 'EWP' );
        $message = __( 'To use eboywp with %s, please install the %s, then re-index.', 'EWP' );

        foreach ( $reqs as $req_name => $req ) {
            if ( $req['is_active'] && ! is_plugin_active( $req['addon'] ) ) {
                $link = sprintf( '<a href="https://eboywp.com/add-ons/%s/" target="_blank">%s</a>', $req['slug'], $addon );
                echo '<div class="error"><p>' . sprintf( $message, $req_name, $link ) . '</p></div>';
            }
        }
    }
}

$this->init = new eboywp_Init();
