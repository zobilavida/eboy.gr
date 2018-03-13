<?php
/**
 * WilokeAdminBar Class
 *
 * @category General
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
    exit;
}

class WilokeAdminBar
{
    public function __construct()
    {
        add_action( 'admin_bar_menu', array($this, 'add_menu_to_admin_bar'), 100 );
    }

    public function add_menu_to_admin_bar()
    {
        global $wp_admin_bar, $wiloke;

        do_action('wiloke_action_before_add_menu_to_admin_bar', $wp_admin_bar);

        $wp_admin_bar->add_menu(
            array(
                'id'    => 'wiloke-support-forum-url',
                'title' => esc_html__('Theme Support', 'listgo'),
                'href'  => $wiloke->aConfigs['general']['support_forum_url'],
                'meta'  => array(
                    'target' => '_blank'
                )
            ),
            $wiloke->aConfigs['general']['theme_options']
        );

        $wp_admin_bar->add_menu(
            array(
                'id'    => 'wiloke-ourproducts-url',
                'title' => esc_html__('Browse Our Products', 'listgo'),
                'href'  => $wiloke->aConfigs['general']['ourproducts'],
                'meta'  => array(
                    'target' => '_blank'
                )
            ),
            $wiloke->aConfigs['general']['theme_options']
        );

        do_action('wiloke_action_after_add_menu_to_admin_bar', $wp_admin_bar);
    }
}
