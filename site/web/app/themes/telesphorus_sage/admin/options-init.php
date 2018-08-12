<?php

    /**
     * For full documentation, please visit: http://docs.reduxframework.com/
     * For a more extensive sample-config file, you may look at:
     * https://github.com/reduxframework/redux-framework/blob/master/sample/sample-config.php
     */

    if ( ! class_exists( 'Redux' ) ) {
        return;
    }

    // This is your option name where all the Redux data is stored.
    $opt_name = "telesphorus_options";

    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    $theme = wp_get_theme(); // For use with some settings. Not necessary.

    $args = array(
        'opt_name' => 'telesphorus_options',
        'dev_mode' => false,
        'display_name' => 'Telesphorus Options',
        'display_version' => '1.0.0',
        'page_title' => 'Telesphorus Options',
        'update_notice' => false,
        'intro_text' => 'Text to appear at the top of the options panel, below the title.',
        'footer_text' => 'Text to be displayed at the bottom of the options panel, in the footer area.',
        'menu_type' => 'menu',
        'menu_title' => 'Telesphorus Options',
        'allow_sub_menu' => TRUE,
        'page_parent_post_type' => 'your_post_type',
        'customizer' => TRUE,
        'default_show' => TRUE,
        'default_mark' => '*',
        'google_api_key' => 'AIzaSyDJOks_6nccno1S7OgNorLDcodK3ecZYuo',
        'hints' => array(
            'icon' => 'el el-bulb',
            'icon_position' => 'right',
            'icon_color' => '#eeee22',
            'icon_size' => 'normal',
            'tip_style' => array(
                'color' => 'light',
                'style' => 'bootstrap',
            ),
            'tip_position' => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect' => array(
                'show' => array(
                    'effect' => 'fade',
                    'duration' => '500',
                    'event' => 'mouseover',
                ),
                'hide' => array(
                    'effect' => 'fade',
                    'duration' => '500',
                    'event' => 'mouseleave unfocus',
                ),
            ),
        ),
        'output' => TRUE,
        'output_tag' => TRUE,
        'settings_api' => TRUE,
        'cdn_check_time' => '1440',
        'compiler' => TRUE,
        'page_permissions' => 'manage_options',
        'save_defaults' => TRUE,
        'show_import_export' => TRUE,
        'open_expanded' => TRUE,
        'database' => 'options',
        'transient_time' => '3600',
        'network_sites' => TRUE,
    );



    Redux::setArgs( $opt_name, $args );

    /*
     * ---> END ARGUMENTS
     */

    /*
     * ---> START HELP TABS
     */




    /*
     * <--- END HELP TABS
     */



         Redux::setSection( $opt_name, array(
             'title'  => __( 'Basic Field', 'redux-framework-demo' ),
             'id'     => 'basic',
             'desc'   => __( 'Basic field with no subsections.', 'redux-framework-demo' ),
             'icon'   => 'el el-home',
             'fields' => array(
                 array(
                     'id'       => 'opt-text',
                     'type'     => 'text',
                     'title'    => __( 'Example Text', 'redux-framework-demo' ),
                     'desc'     => __( 'Example description.', 'redux-framework-demo' ),
                     'subtitle' => __( 'Example subtitle.', 'redux-framework-demo' ),
                 )
             )
         ) );

         Redux::setSection( $opt_name, array(
             'title' => __( 'Basic Fields', 'redux-framework-demo' ),
             'id'    => 'basic',
             'desc'  => __( 'Basic fields as subsections.', 'redux-framework-demo' ),
             'icon'  => 'el el-home'
         ) );

         Redux::setSection( $opt_name, array(
             'title'      => __( 'Text', 'redux-framework-demo' ),
             'desc'       => __( 'For full documentation on this field, visit: ', 'redux-framework-demo' ) . '<a href="http://docs.reduxframework.com/core/fields/text/" target="_blank">http://docs.reduxframework.com/core/fields/text/</a>',
             'id'         => 'opt-text-subsection',
             'subsection' => true,
             'fields'     => array(
                 array(
                     'id'       => 'text-example',
                     'type'     => 'text',
                     'title'    => __( 'Text Field', 'redux-framework-demo' ),
                     'subtitle' => __( 'Subtitle', 'redux-framework-demo' ),
                     'desc'     => __( 'Field Description', 'redux-framework-demo' ),
                     'default'  => 'Default Text',
                 ),
             )
         ) );

         Redux::setSection( $opt_name, array(
             'title'      => __( 'Text Area', 'redux-framework-demo' ),
             'desc'       => __( 'For full documentation on this field, visit: ', 'redux-framework-demo' ) . '<a href="http://docs.reduxframework.com/core/fields/textarea/" target="_blank">http://docs.reduxframework.com/core/fields/textarea/</a>',
             'id'         => 'opt-textarea-subsection',
             'subsection' => true,
             'fields'     => array(
                 array(
                     'id'       => 'textarea-example',
                     'type'     => 'textarea',
                     'title'    => __( 'Text Area Field', 'redux-framework-demo' ),
                     'subtitle' => __( 'Subtitle', 'redux-framework-demo' ),
                     'desc'     => __( 'Field Description', 'redux-framework-demo' ),
                     'default'  => 'Default Text',
                 ),
             )
         ) );

         /*
          * <--- END SECTIONS
          */
