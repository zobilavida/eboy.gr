<?php
/*
Plugin Name: Wiloke Listgo Widgets
Author: wiloke
Plugin URI: https://wiloke.com
Author URI: http://themeforest.net/user/wiloke
Version: 1.1.7
Description: The bundle widgets for WordPress
Text Domain: wiloke
Domain Path: /languages/
*/

if ( !defined('ABSPATH') )
{
	die();
}

define('WILOKE_WIDGET_PREFIX', 'ListGo ');
define('WILOKE_WIDGET_VERSION', '1.1.7');

require plugin_dir_path(__FILE__) . 'app/Supports/Helpers.php';
$aWidgets = require plugin_dir_path(__FILE__) . 'configs/widget.php';
include plugin_dir_path(__FILE__) . 'class.wilokeWidgetOptions.php';
include plugin_dir_path(__FILE__) . 'app/Widgets/func.mailchimp.php';

add_action('delete_widget', 'wiloke_listgo_widget_delete_caching');
add_filter('widget_update_callback', 'wiloke_listgo_widget_update_callback', 10, 4);
function wiloke_listgo_widget_delete_caching($widgetID){
	if ( strpos($widgetID, 'wiloke') === false ){
		return false;
	}

	if ( Wiloke::$wilokePredis ){
		Wiloke::$wilokePredis->del(Wiloke::$prefix.$widgetID);
	}else{
		delete_transient(Wiloke::$prefix.$widgetID);
	}
}

function wiloke_listgo_widget_update_callback($instance, $new_instance, $old_instance, $that){
	if ( strpos($that->id_base, 'wiloke') === false ){
		return $instance;
	}

	if ( !empty($that->number) ){
		wiloke_listgo_widget_delete_caching($that->id_base.'-'.$that->number);
	}

	return $instance;
}

add_action('widgets_init', 'wiloke_listgo_widgets');
function wiloke_listgo_widgets(){
	global $aWidgets;
	foreach ( $aWidgets as $widget ){
		if ( file_exists(plugin_dir_path(__FILE__) . 'app/Widgets/' . $widget . '.php') ){
			include plugin_dir_path(__FILE__) . 'app/Widgets/' . $widget . '.php';
			register_widget($widget);
		}
	}
}

add_action('wp_enqueue_scripts', 'wiloke_listgo_widgets_frontend_scripts');
function wiloke_listgo_widgets_frontend_scripts(){
	wp_enqueue_script('wiloke-listgo-widgets', plugin_dir_url(__FILE__) . 'public/source/js/script.js', array('jquery'), null, true);
}

function wiloke_listgo_widgets_scripts($page)
{
	if ( !empty($page) && $page == 'widgets.php' )
	{
		wp_enqueue_script('wiloke_widgets', plugin_dir_url(__FILE__) . 'source/js/widgets.js', array('jquery'), '1.0', true);
		wp_enqueue_style('wiloke_widgets', plugin_dir_url(__FILE__) . 'source/css/style.css', array(), '1.0');
	}
}

add_action('admin_enqueue_scripts', 'wiloke_listgo_widgets_scripts');

function wiloke_listgo_widget_get_cache($atts){
	if ( Wiloke::$wilokePredis ){
		$aValue = Wiloke::$wilokePredis->get(Wiloke::$prefix.$atts['widget_id']);
	}else{
		$aValue = get_transient(Wiloke::$prefix.$atts['widget_id']);
	}

	return $aValue;
}

function wiloke_listgo_widget_set_cache($atts, $rawValue){
	global $wiloke;
	if ( !empty($wiloke->aThemeOptions['widget_caching']) && !empty($rawValue) && !is_wp_error($rawValue) ){
		$convertCachingTime = absint($wiloke->aThemeOptions['widget_caching'])*60*60;
		if ( Wiloke::$wilokePredis ){
			Wiloke::$wilokePredis->setEx(Wiloke::$prefix.$atts['widget_id'], $convertCachingTime, json_encode($rawValue));
		}else{
			set_transient(Wiloke::$prefix.$atts['widget_id'], json_encode($rawValue), $convertCachingTime);
		}
	}
}

add_action( 'plugins_loaded', 'wiloke_listgo_widgets_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function wiloke_listgo_widgets_load_textdomain() {
	load_plugin_textdomain( 'wiloke', false, basename(dirname(__FILE__)) . '/languages' );
}
