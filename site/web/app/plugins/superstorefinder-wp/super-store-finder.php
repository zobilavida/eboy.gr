<?php
/*
Plugin Name: Super Store Finder Wordpress
Plugin URI: http://www.superstorefinder.net
Description: Super Store Finder Wordpress is a Wordpress Store Finder plugin with Google Maps API v3 that allows customers to locate your stores easily. Packed with Geo Location, Google Street View and Google Maps Direction your customers will never be lost again getting to your locations. The store finder will be able to list of nearby stores / outlets around your web visitors from nearest to the furthest distance away.
Version: 4.2.1
Author: Joe Iz
Author URI: http://www.superstorefinder.net
*/

$ssf_wp_version="4.2.1";
define('SSF_WP_VERSION', $ssf_wp_version);
$ssf_wp_db_version=4.0;
include_once("ssf-wp-define.php");
include_once(SSF_WP_INCLUDES_PATH."/copyfolderlibrary.php");

add_action('admin_menu', 'ssf_wp_add_options_page');
add_action('wp_head', 'ssf_wp_head_scripts');


include_once("ssf-wp-functions.php");


register_activation_hook( __FILE__, 'ssf_wp_install_tables');

add_action('the_content', 'ssf_wp_template');
	
if (preg_match("@$ssf_wp_dir@", $_SERVER['REQUEST_URI'])) {
	add_action("admin_print_scripts", 'ssf_wp_add_admin_javascript');
	add_action("admin_print_styles",'ssf_wp_add_admin_stylesheet');
}
load_plugin_textdomain(SSF_WP_TEXT_DOMAIN, "", "../uploads/ssf-wp-uploads/languages/");



function ssf_wp_plugin_prevent_upgrade($opt) {
	global $update_class;
	$plugin = plugin_basename(__FILE__);
	if ( $opt && isset($opt->response[$plugin]) ) {

		$update_class="update-message";

	}
	return $opt;
}

function ssf_wp_update_db_check() {
    global $ssf_wp_db_version;
    if (ssf_wp_data('ssf_wp_db_version') != $ssf_wp_db_version) {
        ssf_wp_install_tables();
    }
}
add_action('plugins_loaded', 'ssf_wp_update_db_check');

add_action('activated_plugin','ssf_save_error');
function ssf_save_error(){
    update_option('plugin_error',  ob_get_contents());
}
?>