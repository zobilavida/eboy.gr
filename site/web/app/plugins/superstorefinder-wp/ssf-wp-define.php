<?php
$ssf_wp_siteurl=get_option('siteurl'); $ssf_wp_blog_charset=get_option('blog_charset'); $ssf_wp_admin_email=get_option('admin_email');
$ssf_wp_site_name=get_option('blogname');
$ssf_wp_dir=dirname(plugin_basename(__FILE__)); 
$ssf_wp_pub_dir=$ssf_wp_dir."/ssf-wp-pub";
$ssf_wp_inc_dir=$ssf_wp_dir."/ssf-wp-inc";
$ssf_wp_admin_dir=$ssf_wp_dir."/ssf-wp-admin";
$ssf_wp_base=plugins_url('', __FILE__); 
$ssf_wp_path=substr(plugin_dir_path(__FILE__), 0, -1); 
$ssf_wp_uploads=wp_upload_dir();

if ( is_ssl() ) {
$ssf_wp_uploads = str_replace( 'http://', 'https://', $ssf_wp_uploads );
}

$ssf_wp_uploads_base=$ssf_wp_uploads['baseurl']."/ssf-wp-uploads";
$ssf_wp_upload_base=$ssf_wp_uploads_base; 
$ssf_wp_uploads_path=$ssf_wp_uploads['basedir']."/ssf-wp-uploads"; 
$ssf_wp_upload_path=$ssf_wp_uploads_path; 
$top_nav_base="/".substr($_SERVER["PHP_SELF"],1)."?page=";
$admin_nav_base=$ssf_wp_siteurl."/wp-admin/admin.php?page="; 
$text_domain="superstorefinder-wp";
$view_link="| <a href='".$admin_nav_base.$ssf_wp_admin_dir."/pages/stores.php'>".__(" View Store List", $text_domain)."</a> <script>setTimeout(function(){jQuery('.ssf_wp_admin_success').fadeOut('slow');}, 6000);</script>";
$view_link_region="| <a href='".$admin_nav_base.$ssf_wp_admin_dir."/pages/region.php'>".__(" View Region List", $text_domain)."</a> <script>setTimeout(function(){jQuery('.ssf_wp_admin_success').fadeOut('slow');}, 6000);</script>";
$web_domain=str_replace("www.","",$_SERVER['HTTP_HOST']);

		define('SSF_WP_SITEURL', $ssf_wp_siteurl); define('SSF_WP_BLOG_CHARSET', $ssf_wp_blog_charset); define('SSF_WP_ADMIN_EMAIL', $ssf_wp_admin_email); define('SSF_WP_SITE_NAME', $ssf_wp_site_name);
		define('SSF_WP_DIR', $ssf_wp_dir);
		define('SSF_WP_PUB_DIR', $ssf_wp_dir);
		define('SSF_WP_CSS_DIR', SSF_WP_PUB_DIR."/css");

		define('SSF_WP_JS_DIR', SSF_WP_PUB_DIR."/js");

		define('SSF_WP_IMAGES_DIR_ORIGINAL', SSF_WP_PUB_DIR."/images");
		define('SSF_WP_INC_DIR', $ssf_wp_inc_dir);
		define('SSF_WP_ACTIONS_DIR', SSF_WP_INC_DIR."/actions");
		define('SSF_WP_INCLUDES_DIR', SSF_WP_INC_DIR."/includes");
		define('SSF_WP_ADMIN_DIR', $ssf_wp_admin_dir);
		define('SSF_WP_INFO_DIR', SSF_WP_ADMIN_DIR."/info");
		define('SSF_WP_PAGES_DIR', SSF_WP_ADMIN_DIR."/pages");

		define('SSF_WP_ADDONS_DIR_ORIGINAL', SSF_WP_ADMIN_DIR."/addons");
		define('SSF_WP_LANGUAGES_DIR_ORIGINAL', SSF_WP_ADMIN_DIR."/languages");
		define('SSF_WP_THEMES_DIR_ORIGINAL', SSF_WP_ADMIN_DIR."/themes");
		define('SSF_WP_BASE', $ssf_wp_base);
		define('SSF_WP_PUB_BASE', SSF_WP_BASE);
		define('SSF_WP_CSS_BASE', SSF_WP_PUB_BASE."/css");

		define('SSF_WP_JS_BASE', SSF_WP_PUB_BASE."/js");

		define('SSF_WP_IMAGES_BASE_ORIGINAL', SSF_WP_PUB_BASE."/images");
		define('SSF_WP_INC_BASE', SSF_WP_BASE."/ssf-wp-inc");
		define('SSF_WP_ACTIONS_BASE', SSF_WP_INC_BASE."/actions");
		define('SSF_WP_INCLUDES_BASE', SSF_WP_INC_BASE."/includes");
		define('SSF_WP_ADMIN_BASE', SSF_WP_BASE."/ssf-wp-admin");
		define('SSF_WP_INFO_BASE', SSF_WP_ADMIN_BASE."/info");
		define('SSF_WP_PAGES_BASE', SSF_WP_ADMIN_BASE."/pages");

		define('SSF_WP_ADDONS_BASE_ORIGINAL', SSF_WP_ADMIN_BASE."/addons");
		define('SSF_WP_LANGUAGES_BASE_ORIGINAL', SSF_WP_ADMIN_BASE."/languages");
		define('SSF_WP_THEMES_BASE_ORIGINAL', SSF_WP_ADMIN_BASE."/themes");
		define('SSF_WP_PATH', $ssf_wp_path);
		define('SSF_WP_PUB_PATH', SSF_WP_PATH);
		define('SSF_WP_CSS_PATH', SSF_WP_PUB_PATH."/css");

		define('SSF_WP_JS_PATH', SSF_WP_PUB_PATH."/js");

		define('SSF_WP_IMAGES_PATH_ORIGINAL', SSF_WP_PUB_PATH."/images");
		define('SSF_WP_INC_PATH', SSF_WP_PATH."/ssf-wp-inc");
		define('SSF_WP_ACTIONS_PATH', SSF_WP_INC_PATH."/actions");
		define('SSF_WP_INCLUDES_PATH', SSF_WP_INC_PATH."/includes");
		define('SSF_WP_ADMIN_PATH', SSF_WP_PATH."/ssf-wp-admin");
		define('SSF_WP_INFO_PATH', SSF_WP_ADMIN_PATH."/info");
		define('SSF_WP_PAGES_PATH', SSF_WP_ADMIN_PATH."/pages");
		define('SSF_WP_CSV_PATH_ORIGINAL', SSF_WP_PUB_PATH."/csv");

		define('SSF_WP_ADDONS_PATH_ORIGINAL', SSF_WP_ADMIN_PATH."/addons");
		define('SSF_WP_LANGUAGES_PATH_ORIGINAL', SSF_WP_ADMIN_PATH."/languages");
		define('SSF_WP_THEMES_PATH_ORIGINAL', SSF_WP_ADMIN_PATH."/themes");
		define('SSF_WP_UPLOADS_BASE', $ssf_wp_uploads_base);
		define('SSF_WP_UPLOADS_PATH', $ssf_wp_uploads_path);
		define('SSF_WP_TOP_NAV_BASE', $top_nav_base);
		define('SSF_WP_ADMIN_NAV_BASE', $admin_nav_base);
		define('SSF_WP_TEXT_DOMAIN', $text_domain);
		define('SSF_WP_VIEW_LINK', $view_link);
		define('SSF_WP_WEB_DOMAIN', $web_domain);

		define('SSF_WP_ADDONS_BASE', SSF_WP_UPLOADS_BASE."/addons");
		define('SSF_WP_CACHE_BASE', SSF_WP_UPLOADS_BASE."/cache");
		define('SSF_WP_CUSTOM_CSS_BASE', SSF_WP_UPLOADS_BASE."/custom-css");
		define('SSF_WP_CUSTOM_CSV_BASE', SSF_WP_UPLOADS_BASE."/csv");

		define('SSF_WP_IMAGES_BASE', SSF_WP_UPLOADS_BASE."/images");
		define('SSF_WP_LANGUAGES_BASE', SSF_WP_UPLOADS_BASE."/languages");
		define('SSF_WP_THEMES_BASE', SSF_WP_UPLOADS_BASE."/themes");

		define('SSF_WP_ADDONS_PATH', SSF_WP_UPLOADS_PATH."/addons");
		define('SSF_WP_ADDONS_MARKER', SSF_WP_ADDONS_PATH."/marker");
		define('SSF_WP_CACHE_PATH', SSF_WP_UPLOADS_PATH."/cache");
		define('SSF_WP_CUSTOM_CSS_PATH', SSF_WP_UPLOADS_PATH."/custom-css");
		define('SSF_WP_CUSTOM_CSV_PATH', SSF_WP_UPLOADS_PATH."/csv");

		define('SSF_WP_IMAGES_PATH', SSF_WP_UPLOADS_PATH."/images");
		define('SSF_WP_LANGUAGES_PATH', SSF_WP_UPLOADS_PATH."/languages");
		define('SSF_WP_THEMES_PATH', SSF_WP_UPLOADS_PATH."/themes");

		define('SSF_WP_INFORMATION_PAGE', SSF_WP_TOP_NAV_BASE.SSF_WP_PAGES_DIR."/quickstart.php");
		define('SSF_WP_MANAGE_LOCATIONS_PAGE', SSF_WP_TOP_NAV_BASE.SSF_WP_PAGES_DIR."/stores.php");
		define('SSF_WP_ADD_LOCATIONS_PAGE', SSF_WP_MANAGE_LOCATIONS_PAGE."&pg=add-store");
		define('SSF_WP_MANAGE_REGION_PAGE', SSF_WP_TOP_NAV_BASE.SSF_WP_PAGES_DIR."/region.php");
		define('SSF_WP_ADD_REGION_PAGE', SSF_WP_MANAGE_REGION_PAGE."&pg=add-region");
		define('SSF_WP_ADD_ONS_PAGE', SSF_WP_TOP_NAV_BASE.SSF_WP_PAGES_DIR."/Add-ons.php");
		define('SSF_WP_MARKERS_PAGE', SSF_WP_TOP_NAV_BASE.SSF_WP_DIR."/categories.php");
		define('SSF_WP_IMPORT_PAGE', SSF_WP_TOP_NAV_BASE.SSF_WP_PAGES_DIR."/import.php");
		define('SSF_WP_SETTINGS_PAGE1', SSF_WP_TOP_NAV_BASE.SSF_WP_PAGES_DIR."/settings.php");
		define('SSF_WP_SETTINGS_PAGE', SSF_WP_SETTINGS_PAGE1); 

		define('SSF_WP_PARENT_PAGE', SSF_WP_INFORMATION_PAGE); 
		define('SSF_WP_PARENT_URL', preg_replace("@".preg_quote(SSF_WP_TOP_NAV_BASE)."@", "",SSF_WP_PARENT_PAGE)); 

$ssf_wp_aps=glob(SSF_WP_ADDONS_PATH.'/*addons-platform*', GLOB_NOSORT); 
if (!empty($ssf_wp_aps)){
	$ssf_wp_addons_platform_dir = basename(current($ssf_wp_aps));
	foreach ($ssf_wp_aps as $ssf_wp_ap_path) {
		if (file_exists($ssf_wp_ap_path.'/'.basename($ssf_wp_ap_path).'.php')) {
			$ssf_wp_addons_platform_dir = basename($ssf_wp_ap_path);
			break;
		} 
	}
	define('SSF_WP_ADDONS_PLATFORM_DIR', $ssf_wp_addons_platform_dir);
	define('SSF_WP_ADDONS_PLATFORM_PATH', SSF_WP_ADDONS_PATH.'/'.SSF_WP_ADDONS_PLATFORM_DIR );
	define('SSF_WP_ADDONS_PLATFORM_BASE', SSF_WP_ADDONS_BASE.'/'.SSF_WP_ADDONS_PLATFORM_DIR );
	define('SSF_WP_ADDONS_PLATFORM_FILE', SSF_WP_ADDONS_PLATFORM_PATH.'/'.SSF_WP_ADDONS_PLATFORM_DIR.'.php');
	
}?>