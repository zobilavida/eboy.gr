<?php
function ssf_wp_move_upload_directories() {
	global $ssf_wp_uploads_path, $ssf_wp_path;
		$ssf_wp_uploads_arr=wp_upload_dir();
	if (!is_dir($ssf_wp_uploads_arr['baseurl'])) {
		mkdir($ssf_wp_uploads_arr['baseurl'], 0755, true);
	}
	if (!is_dir(SSF_WP_UPLOADS_PATH)) {
		mkdir(SSF_WP_UPLOADS_PATH, 0755, true);
	}
	if (is_dir(SSF_WP_ADDONS_PATH_ORIGINAL) && !is_dir(SSF_WP_ADDONS_PATH)) {
		ssf_wp_copyr(SSF_WP_ADDONS_PATH_ORIGINAL, SSF_WP_ADDONS_PATH);
		chmod(SSF_WP_ADDONS_PATH, 0755);
	}
	if (is_dir(SSF_WP_THEMES_PATH_ORIGINAL) && !is_dir(SSF_WP_THEMES_PATH)) {
		ssf_wp_copyr(SSF_WP_THEMES_PATH_ORIGINAL, SSF_WP_THEMES_PATH);
		chmod(SSF_WP_THEMES_PATH, 0755);
	}
	if (is_dir(SSF_WP_LANGUAGES_PATH_ORIGINAL) && !is_dir(SSF_WP_LANGUAGES_PATH)) {
		ssf_wp_copyr(SSF_WP_LANGUAGES_PATH_ORIGINAL, SSF_WP_LANGUAGES_PATH);
		chmod(SSF_WP_LANGUAGES_PATH, 0755);
	}
	if (is_dir(SSF_WP_IMAGES_PATH_ORIGINAL) && !is_dir(SSF_WP_IMAGES_PATH)) {
		ssf_wp_copyr(SSF_WP_IMAGES_PATH_ORIGINAL, SSF_WP_IMAGES_PATH);
		chmod(SSF_WP_IMAGES_PATH, 0755);
	}
	
	if (is_dir(SSF_WP_CSV_PATH_ORIGINAL) && !is_dir(SSF_WP_CUSTOM_CSV_PATH)) {
		ssf_wp_copyr(SSF_WP_CSV_PATH_ORIGINAL, SSF_WP_CUSTOM_CSV_PATH);
		chmod(SSF_WP_CUSTOM_CSV_PATH, 0755);
	}
	
	
	if (!is_dir(SSF_WP_CUSTOM_CSS_PATH)) {
		mkdir(SSF_WP_CUSTOM_CSS_PATH, 0755, true);
	}
	if (!is_dir(SSF_WP_CACHE_PATH)) {
	      mkdir(SSF_WP_CACHE_PATH, 0755, true);
	}
	ssf_wp_ht(SSF_WP_CACHE_PATH, 'ht');
	ssf_wp_ht(SSF_WP_ADDONS_PATH);
	ssf_wp_ht(SSF_WP_UPLOADS_PATH);
}
function ssf_wp_ht($path, $type='index'){
	if(is_dir($path) && !is_file($path."/.htaccess") && !is_file($path."/index.php")) {
		if ($type == 'ht') {
$htaccess = <<<EOQ
<FilesMatch "\.(php|gif|jpe?g|png|css|js|csv|xml|json)$">
Allow from all
</FilesMatch>
order deny,allow
deny from all
allow from none
Options All -Indexes
EOQ;
			$filename = $path."/.htaccess";
			$file_handle = @ fopen($filename, 'w+');
			@fwrite($file_handle, $htaccess);
			@fclose($file_handle);
			@chmod($file_handle, 0644);
		} elseif ($type == 'index') {
			$index ='<?php /*empty; prevents directory browsing*/ ?>';
			$filename = $path."/index.php";
			$file_handle = @ fopen($filename, 'w+');
			@fwrite($file_handle, $index);
			@fclose($file_handle);
			@chmod($file_handle, 0644);
		}	
	} elseif (is_dir($path) && is_file($path."/.htaccess") && $type == 'index') {
		
		@unlink($path."/.htaccess");
		$index ='<?php /*empty; prevents directory browsing*/ ?>';
		$filename = $path."/index.php";
		$file_handle = @ fopen($filename, 'w+');
		@fwrite($file_handle, $index);
		@fclose($file_handle);
		@chmod($file_handle, 0644);		
	}
}
/* -----------------*/

function ssfParseToXML($htmlStr) 
{
    $xmlStr=str_replace("&",'&amp;',$htmlStr); 
	$xmlStr=str_replace('<','&lt;',$xmlStr); 
	$xmlStr=str_replace('>','&gt;',$xmlStr); 
	$xmlStr=str_replace('"','&quot;',$xmlStr); 
	$xmlStr=str_replace("'",'&#39;',$xmlStr); 
	$xmlStr=str_replace("," ,"&#44;" ,$xmlStr);
	return $xmlStr; 
} 

function ssfParseToHXML($htmlStr) 
{ 
	$xmlStr=str_replace('&nbsp;',' ',$htmlStr); 
	return $xmlStr; 
} 
/*-----------------*/
function filter_ssf_wp_mdo($the_arr) {
	$input_zone_clause = ($the_arr['input_zone'] == $GLOBALS['input_zone_type']);
	
	$output_zone_clause = ( !isset($the_arr['output_zone']) || !isset($GLOBALS['output_zone_type']) || ($the_arr['output_zone'] == $GLOBALS['output_zone_type']) );
	
	return ($input_zone_clause && $output_zone_clause);
}
function ssf_wp_md_initialize() {
	global $ssf_wp_vars;
	include(SSF_WP_INCLUDES_PATH."/settings-options.php");
	ssf_wp_data('ssf_wp_vars', 'add', $ssf_wp_vars);
	foreach ($ssf_wp_mdo as $value) {
				
		if (isset($value['field_name']) && !is_array($value['field_name']) ) {
			$value['default'] = (!isset($value['default']))? "" : $value['default'];
			
			$default_not_set = !isset($ssf_wp_vars[$value['field_name']]);
			$default_set_but_value_set_to_blank = (isset($ssf_wp_vars[$value['field_name']]) && strlen(trim($ssf_wp_vars[$value['field_name']])) == 0);
			
			if ( ($default_not_set || $default_set_but_value_set_to_blank) ) {
				
				$ssf_wp_vars[$value['field_name']] = $value['default'];
			} 
						
			$varname = "ssf_wp_".$value['field_name'];  
			global $$varname;
			$$varname = $ssf_wp_vars[$value['field_name']]; 
			
		} elseif (isset($value['field_name']) && is_array($value['field_name']) ) {
		   
			$value['default'] = (!isset($value['default']))? array_fill(0, count($value['field_name']), "") : $value['default'];
		
			
			$ctr = 0;	
			foreach ($value['default'] as $the_default) {
				
				$the_field = $value['field_name'][$ctr];
				$d_n_s = !isset($ssf_wp_vars[$the_field]);
				$d_s_b_v_s_t_b = (isset($ssf_wp_vars[$the_field]) && strlen(trim($ssf_wp_vars[$the_field])) == 0);
		
				if ( ($d_n_s || $d_s_b_v_s_t_b) ) {
					$ssf_wp_vars[$the_field] = $the_default;
				}
				
				$varname = "ssf_wp_".$the_field;  
				global $$varname;
				$$varname = $ssf_wp_vars[$the_field];
				$ctr++;
			} 
		    
		}
	}
}
/*-----------------*/
function ssf_wp_initialize_variables() {
	
global $ssf_wp_height, $ssf_wp_width, $ssf_wp_width_units, $ssf_wp_height_units, $ssf_wp_radii;
global $ssf_wp_icon, $ssf_wp_icon2, $ssf_wp_google_map_domain, $ssf_wp_google_map_country, $ssf_wp_theme, $ssf_wp_base, $ssf_wp_uploads_base, $ssf_wp_location_table_view;
global $ssf_wp_search_label, $ssf_wp_zoom_level, $ssf_wp_use_city_search, $ssf_wp_use_name_search, $ssf_wp_name;
global $ssf_wp_radius_label, $ssf_wp_website_label, $ssf_wp_directions_label, $ssf_wp_num_initial_displayed, $ssf_wp_load_locations_default;
global $ssf_wp_distance_unit, $ssf_wp_map_overview_control, $ssf_wp_admin_locations_per_page, $ssf_wp_instruction_message;
global $ssf_wp_map_character_encoding, $ssf_wp_start, $ssf_wp_map_language, $ssf_wp_map_region, $ssf_wp_sensor, $ssf_wp_geolocate;
global $ssf_wp_map_settings, $ssf_wp_remove_credits, $ssf_wp_api_key, $ssf_wp_location_not_found_message, $ssf_wp_no_results_found_message; 
global $ssf_wp_load_results_with_locations_default, $ssf_wp_vars, $ssf_wp_city_dropdown_label, $ssf_wp_scripts_load, $ssf_wp_scripts_load_home, $ssf_wp_scripts_load_archives_404;
global $ssf_wp_hours_label, $ssf_wp_phone_label, $ssf_wp_fax_label, $ssf_wp_email_label, $google_map_region, $google_map_language, $ssf_user_role;

$ssf_wp_vars=ssf_wp_data('ssf_wp_vars'); 
ssf_wp_md_initialize();

if(!isset($ssf_wp_vars['map_settings']))
	{
	$ssf_wp_vars['sensor']="false";
	$ssf_wp_vars['geolocate']="";
    $ssf_wp_vars['api_key']="";
	$ssf_wp_vars['google_map_country']= "United States";
	$ssf_wp_vars['google_map_domain']="maps.google.com";
	$ssf_wp_vars['map_region']="";
	$ssf_wp_vars['map_language']= "en";
	$ssf_wp_vars['map_character_encoding']="";
	$ssf_wp_vars['start']=date("Y-m-d H:i:s");
	$ssf_wp_vars['name']="Super Store Finder";
	$ssf_wp_vars['admin_locations_per_page']="100";
	$ssf_wp_vars['location_table_view']="Normal";
	$ssf_wp_vars['map_settings']="google.maps.MapTypeId.ROADMAP";
	}
	else{
		if (strlen(trim($ssf_wp_vars['sensor'])) == 0) {	$ssf_wp_vars['sensor'] = ($ssf_wp_vars['geolocate'] == '1')? "true" : "false";	}
$ssf_wp_sensor=$ssf_wp_vars['sensor'];


if ($ssf_wp_vars['api_key'] === NULL) {	$ssf_wp_vars['api_key']="";	}
$ssf_wp_api_key=$ssf_wp_vars['api_key'];

if (strlen(trim($ssf_wp_vars['google_map_country'])) == 0) {	$ssf_wp_vars['google_map_country']="United States";}
$ssf_wp_google_map_country=$ssf_wp_vars['google_map_country'];

if (strlen(trim($ssf_wp_vars['google_map_domain'])) == 0) {	$ssf_wp_vars['google_map_domain']="maps.google.com";}
$ssf_wp_google_map_domain=$ssf_wp_vars['google_map_domain'];

if ($ssf_wp_vars['map_region'] === NULL) {	$ssf_wp_vars['map_region']="";	}
$ssf_wp_map_region=$ssf_wp_vars['map_region'];

if (strlen(trim($ssf_wp_vars['map_language'])) == 0) {	$ssf_wp_vars['map_language']="en";	}
$ssf_wp_map_language=$ssf_wp_vars['map_language'];

if ($ssf_wp_vars['map_character_encoding'] === NULL) {	$ssf_wp_vars['map_character_encoding']="";		}
$ssf_wp_map_character_encoding=$ssf_wp_vars['map_character_encoding'];


if (strlen(trim($ssf_wp_vars['start'])) == 0) { 	$ssf_wp_vars['start']=date("Y-m-d H:i:s"); 	} 
$ssf_wp_start=$ssf_wp_vars['start']; 

if (strlen(trim($ssf_wp_vars['name'])) == 0) {	$ssf_wp_vars['name']="Super Store Finder";	}  
$ssf_wp_name=$ssf_wp_vars['name'];


if (strlen(trim($ssf_wp_vars['admin_locations_per_page'])) == 0) {	$ssf_wp_vars['admin_locations_per_page']="100";	}
$ssf_wp_admin_locations_per_page=$ssf_wp_vars['admin_locations_per_page'];

if (strlen(trim($ssf_wp_vars['location_table_view'])) == 0) {	$ssf_wp_vars['location_table_view']="Normal";	}
$ssf_wp_location_table_view=$ssf_wp_vars['location_table_view'];


if (strlen(trim($ssf_wp_vars['map_settings'])) == 0) {	$ssf_wp_vars['map_settings']="google.maps.MapTypeId.ROADMAP";}
elseif ($ssf_wp_vars['map_settings']=="G_NORMAL_MAP"){	$ssf_wp_vars['map_settings']='google.maps.MapTypeId.ROADMAP';}
elseif ($ssf_wp_vars['map_settings']=="G_SATELLITE_MAP"){	$ssf_wp_vars['map_settings']='google.maps.MapTypeId.SATELLITE';}
elseif ($ssf_wp_vars['map_settings']=="G_HYBRID_MAP"){	$ssf_wp_vars['map_settings']='google.maps.MapTypeId.HYBRID';}
elseif ($ssf_wp_vars['map_settings']=="G_PHYSICAL_MAP"){	$ssf_wp_vars['map_settings']='google.maps.MapTypeId.TERRAIN';}
$ssf_wp_map_settings=$ssf_wp_vars['map_settings'];
		
		
	}

	ssf_wp_data('ssf_wp_vars', 'add', $ssf_wp_vars);
}
/*--------------------------*/
function ssf_wp_choose_units($unit, $input_name) {
	$unit_arr[]="%";$unit_arr[]="px";$unit_arr[]="em";$unit_arr[]="pt";
	$select_field="<select name='$input_name'>";

	
	foreach ($unit_arr as $value) {
		$selected=($value=="$unit")? " selected='selected' " : "" ;
		if (!($input_name=="height_units" && $unit=="%")) {
			$select_field.="\n<option value='$value' $selected>$value</option>";
		}
	}
	$select_field.="</select>";
	return $select_field;
}


/*----------------------------*/

function ssf_wp_install_tables() {
	global $wpdb, $ssf_wp_db_version, $ssf_wp_path, $ssf_wp_uploads_path, $ssf_wp_hook;
if (!defined("SSF_WP_TABLE") || !defined("SSF_WP_TAG_TABLE") || !defined("SSF_WP_SETTING_TABLE") || !defined("SSF_WP_ADDON_TABLE") || !defined("SSF_WP_REGION_TABLE") || !defined("SSF_WP_SOCIAL_TABLE")){ 
	$ssf_wp_db_prefix = $wpdb->prefix; 
}

	if (!defined("SSF_WP_TABLE")){ define("SSF_WP_TABLE", $ssf_wp_db_prefix."ssf_wp_stores");}
	if (!defined("SSF_WP_TAG_TABLE")){ define("SSF_WP_TAG_TABLE", $ssf_wp_db_prefix."ssf_wp_tag"); }
	if (!defined("SSF_WP_SETTING_TABLE")){ define("SSF_WP_SETTING_TABLE", $ssf_wp_db_prefix."ssf_wp_setting"); }
	if (!defined("SSF_WP_ADDON_TABLE")){ define("SSF_WP_ADDON_TABLE", $ssf_wp_db_prefix."ssf_wp_addon"); }
	if (!defined("SSF_WP_REGION_TABLE")){ define("SSF_WP_REGION_TABLE", $ssf_wp_db_prefix."ssf_wp_region"); }
	if (!defined("SSF_WP_SOCIAL_TABLE")){ define("SSF_WP_SOCIAL_TABLE", $ssf_wp_db_prefix."store_ratings"); }

	$table_name = SSF_WP_TABLE;
	$sql = "CREATE TABLE ".$table_name." (
			ssf_wp_id mediumint(8) unsigned NOT NULL auto_increment,
			ssf_wp_store varchar(255) NULL,
			ssf_wp_address varchar(255) NULL,
			ssf_wp_address2 varchar(255) NULL,
			ssf_wp_city varchar(255) NULL,
			ssf_wp_state varchar(255) NULL,
			ssf_wp_country varchar(255) NULL,
			ssf_wp_zip varchar(255) NULL,
			ssf_wp_latitude varchar(255) NULL,
			ssf_wp_longitude varchar(255) NULL,
			ssf_wp_tags mediumtext NULL,
			ssf_wp_description mediumtext NULL,
			ssf_wp_url varchar(255) NULL,
			ssf_wp_ext_url varchar(255) NULL,
			ssf_wp_contact_email int(11) NULL,
			ssf_wp_embed_video mediumtext NULL,
			ssf_wp_default_media varchar(255) NULL,
			ssf_wp_hours mediumtext NULL,
			ssf_wp_phone varchar(255) NULL,
			ssf_wp_fax varchar(255) NULL,
			ssf_wp_email varchar(255) NULL,
			ssf_wp_image varchar(255) NULL,
			ssf_wp_is_published varchar(1) NULL,
			PRIMARY KEY  (ssf_wp_id)
			) ENGINE=innoDB  DEFAULT CHARACTER SET=utf8  DEFAULT COLLATE=utf8_unicode_ci;";
	$table_name_2 = SSF_WP_TAG_TABLE;
	$sql .= "CREATE TABLE ".$table_name_2." (
			ssf_wp_tag_id bigint(20) unsigned NOT NULL auto_increment,
			ssf_wp_tag_name varchar(255) NULL,
			ssf_wp_tag_slug varchar(255) NULL,
			ssf_wp_tag_icon varchar(255) NULL,
			ssf_wp_id mediumint(8) NULL,
			PRIMARY KEY  (ssf_wp_tag_id)
			) ENGINE=innoDB  DEFAULT CHARACTER SET=utf8  DEFAULT COLLATE=utf8_unicode_ci;";

	$table_name_3 = SSF_WP_SETTING_TABLE;
	$sql .= "CREATE TABLE ".$table_name_3." (
			ssf_wp_setting_id bigint(20) unsigned NOT NULL auto_increment,
			ssf_wp_setting_name varchar(255) NULL,
			ssf_wp_setting_value longtext NULL,
			PRIMARY KEY  (ssf_wp_setting_id)
			) ENGINE=innoDB  DEFAULT CHARACTER SET=utf8  DEFAULT COLLATE=utf8_unicode_ci;";
			
	$table_name_4 = SSF_WP_ADDON_TABLE;
	$sql .= "CREATE TABLE ".$table_name_4." (
			ssf_wp_adon_id bigint(20) unsigned NOT NULL auto_increment,
			ssf_wp_addon_name varchar(255) NULL,
			ssf_addon_name varchar(255) NULL,
			ssf_wp_addon_token varchar(255) NULL,
			ssf_wp_addon_status varchar(255) NOT NULL DEFAULT 'on',
			PRIMARY KEY  (ssf_wp_adon_id)
			) ENGINE=innoDB  DEFAULT CHARACTER SET=utf8  DEFAULT COLLATE=utf8_unicode_ci;";
			
	$table_name_5 = SSF_WP_REGION_TABLE;
	$sql .= "CREATE TABLE ".$table_name_5." (
			ssf_wp_region_id bigint(20) unsigned NOT NULL auto_increment,
			ssf_wp_region_name varchar(255) NULL,
			ssf_wp_address_name varchar(255) NULL,
			PRIMARY KEY  (ssf_wp_region_id)
			) ENGINE=innoDB  DEFAULT CHARACTER SET=utf8  DEFAULT COLLATE=utf8_unicode_ci;";
			
	$table_name_6 = SSF_WP_SOCIAL_TABLE;
	   $sql .= "CREATE TABLE IF NOT EXISTS ".$table_name_6." (
			`ssf_wp_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`ssf_wp_store_id` int(11) NULL,
			`ssf_wp_ratings_score` int(11)NULL,
			`ssf_wp_comment` mediumtext NULL,
			`ssf_comment_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			`ssf_wp_user_name` varchar(255)  NULL,
			`ssf_wp_user_email` varchar(255)  NULL,
			PRIMARY KEY (`ssf_wp_id`)
	) ENGINE=innoDB  DEFAULT CHARACTER SET=utf8  DEFAULT COLLATE=utf8_unicode_ci;";
	 
	
	if($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) != $table_name || $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name_2)) != $table_name_2 || $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name_3)) != $table_name_3 || $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name_4)) != $table_name_4 || $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name_5)) != $table_name_5 || $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name_6)) != $table_name_6) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		ssf_wp_data("ssf_wp_db_version", 'add', $ssf_wp_db_version);
		ssf_wp_initialize_variables();
	}
	$installed_ver = ssf_wp_data("ssf_wp_db_version");
	if( $installed_ver != $ssf_wp_db_version ) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		ssf_wp_data("ssf_wp_db_version", 'update', $ssf_wp_db_version);
	}
	if (ssf_wp_data("ssf_wp_db_prefix")===""){
		ssf_wp_data('ssf_wp_db_prefix', 'update', $ssf_wp_db_prefix);
	}
	ssf_wp_move_upload_directories();
}

/*-------------------------------*/

function ssf_wp_head_scripts() {

	global $ssf_wp_dir, $ssf_wp_base, $ssf_wp_uploads_base, $ssf_wp_path, $ssf_wp_uploads_path, $wpdb, $pagename, $ssf_wp_map_language, $post, $ssf_wp_vars; 		
	$on_ssf_wp_page=""; $ssf_wp_code_is_used_in_posts=""; $post_ids_array="";
	if (empty($ssf_wp_vars['scripts_load']) || $ssf_wp_vars['scripts_load'] != 'all') {

		if (empty($_GET['p'])){ $_GET['p']=""; } if (empty($_GET['page_id'])){ $_GET['page_id']=""; }

		$on_ssf_wp_page=$wpdb->get_results("SELECT post_name, post_content FROM ".SSF_WP_DB_PREFIX."posts WHERE LOWER(post_content) LIKE '%[super-store-finder%' AND (post_name='$pagename' OR ID='".esc_sql($_GET['p'])."' OR ID='".esc_sql($_GET['page_id'])."')", ARRAY_A);		

		$ssf_wp_code_is_used_in_posts=$wpdb->get_results("SELECT post_name, ID FROM ".SSF_WP_DB_PREFIX."posts WHERE LOWER(post_content) LIKE '%[super-store-finder%' AND post_type='post'", ARRAY_A);

		if ($ssf_wp_code_is_used_in_posts) {

			$ssf_wp_post_ids=$ssf_wp_code_is_used_in_posts;

			foreach ($ssf_wp_post_ids as $val) { $post_ids_array[]=$val['ID'];}

		} else {		

			$post_ids_array=array(pow(10,15)); 

		}
	}
	$show_on_all_pages = ( !empty($ssf_wp_vars['scripts_load']) && $ssf_wp_vars['scripts_load'] == 'all' );
	$show_on_front_page = ( is_front_page() && (!isset($ssf_wp_vars['scripts_load_home']) || $ssf_wp_vars['scripts_load_home']==1) );
	$show_on_archive_404_pages = ( (is_archive() || is_404()) && $ssf_wp_code_is_used_in_posts && (!isset($ssf_wp_vars['scripts_load_archives_404']) || $ssf_wp_vars['scripts_load_archives_404']==1) );
	$show_on_custom_post_types = ( is_singular() && !is_singular(array('page', 'attachment', 'post')) && !is_front_page() );
	$show_on_page_templates = ( is_page_template() && !is_front_page() );
	$on_ssf_wp_post = is_single($post_ids_array);

	

	//if (is_page() || is_single()) {

	if ($show_on_all_pages || $on_ssf_wp_page  || $show_on_archive_404_pages || $show_on_front_page  || $on_ssf_wp_post || $show_on_custom_post_types || function_exists('show_ssf_wp_scripts') || $show_on_page_templates) {

		$GLOBALS['is_on_ssf_wp_page'] = 1;

		$google_map_domain=($ssf_wp_vars['google_map_domain']!="")? $ssf_wp_vars['google_map_domain'] : "maps.google.com";

		//  || is_search()



		$sens=(!empty($ssf_wp_vars['sensor']) && ($ssf_wp_vars['sensor'] === "true" || $ssf_wp_vars['sensor'] === "false" ))? "&amp;sensor=".$ssf_wp_vars['sensor'] : "&amp;sensor=false" ;

		$lang_loc=(!empty($ssf_wp_vars['map_language']))? "&amp;language=".$ssf_wp_vars['map_language'] : "" ; 

		$region_loc=(!empty($ssf_wp_vars['map_region']))? "&amp;region=".$ssf_wp_vars['map_region'] : "" ;

		$key=(!empty($ssf_wp_vars['api_key']))? "&amp;key=".$ssf_wp_vars['api_key'] : "" ;



		if (empty($_POST) && 1==2) { 

			$nm=(!empty($post->post_name))? $post->post_name : $pagename ;

			$p=(!empty($post->ID))? $post->ID : esc_sql($_GET['p']) ;

		
		} else {

			ssf_wp_dyn_js();

		}

		$has_custom_css=(file_exists(SSF_WP_CUSTOM_CSS_PATH."/mega-superstorefinder.css"))? SSF_WP_CUSTOM_CSS_BASE : SSF_WP_CSS_BASE; 

		//mega locator
		$fileCounter=0;
		wp_enqueue_style( 'mega-font-awesome' , $has_custom_css.'/font-awesome.css' , true , '4.1' );
		wp_enqueue_style( 'mega-normalize' , $has_custom_css.'/normalize.css' , true , '2.0' );
		wp_enqueue_style( 'mega-superstorefinder' , $has_custom_css.'/mega-superstorefinder.css' , true , '1.0' );
		$ssf_font_familly=(trim($ssf_wp_vars['ssf_font_familly'])!="")? ssfParseToXML($ssf_wp_vars['ssf_font_familly']) : "";
		if(!empty($ssf_font_familly)){
	    	wp_enqueue_style( 'mega-google-fonts' , 'https://fonts.googleapis.com/css?family='.$ssf_font_familly);	
		}
		wp_enqueue_script( 'mega-modernize' , SSF_WP_JS_BASE.'/vendors/modernizr.min.js' , array( 'jquery' ) , '1.0' , true );
		wp_enqueue_script( 'mega-polyfills' , SSF_WP_JS_BASE.'/polyfills/html5shiv.3.7.0.min.js' , array( 'jquery' ) , '3.7' , true );
		wp_enqueue_script( 'mega-homebrew' , SSF_WP_JS_BASE.'/plugins/homebrew.js' , array( 'jquery' ) , '1.0' , true );
		wp_enqueue_script( 'mega-fastclicks' , SSF_WP_JS_BASE.'/plugins/fastclick.min.js' , array( 'jquery' ) , '3.0' , true );
		wp_enqueue_script( 'mega-init' , SSF_WP_JS_BASE.'/init.js' , array( 'jquery' ) , '1.0' , true );
		wp_enqueue_script( 'mega-openclose' , SSF_WP_JS_BASE.'/mega-openclose.js' , array( 'jquery' ) , '1.0' , true );	
		
		/**.** Marker cluster addon **.**/
		
		
		$clusterCheck=$wpdb->get_results("SELECT * FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_wp_addon_name='ssf-marker-cluster-wp' AND ssf_wp_addon_status='on' ", ARRAY_A);
		if(!empty($clusterCheck) && is_dir(SSF_WP_ADDONS_PATH.'/ssf-marker-cluster-wp'))
		{
			$fileCounter++;
			print "<script>var markerCategory=true; </script>";
			wp_enqueue_script( 'mega-Cluster' , SSF_WP_ADDONS_BASE.'/ssf-marker-cluster-wp/markerclusterer.js' , array( 'jquery' ) , '1.0' , true );
		}else{
			print "<script>var markerCategory=false; </script>";
		}
		
		/**.**  rating and cooment addon **.**/
		$addonRating=$wpdb->get_results("SELECT * FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_wp_addon_name='ssf-rating-addon-wp' AND ssf_wp_addon_status='on' ", ARRAY_A);
		if(!empty($addonRating) && is_dir(SSF_WP_ADDONS_PATH.'/ssf-rating-addon-wp'))
		{		
		//rating Adon Code //
		if(!empty($addonRating)){
			$rattingStatusVal= $addonRating[0]['ssf_wp_addon_token'];
			if(!empty($rattingStatusVal)){
				$ratVal=explode("#",$rattingStatusVal);
				if(!empty($ratVal[2])){
				$recaptchaKey=trim($ratVal[2]);
				}else{
				$recaptchaKey='6Ld4qiETAAAAAMdDBxFcuBcHqmoIHFcszteXE7i3';
				}
			}
			else{
				$recaptchaKey='6Ld4qiETAAAAAMdDBxFcuBcHqmoIHFcszteXE7i3';
			}
		}
		print "<script> var ssf_reCaptcha='$recaptchaKey'; </script>";
		wp_enqueue_script( 'mega-superstorfinder' , SSF_WP_ADDONS_BASE.'/ssf-rating-addon-wp/mega-superstorefinder.js' , array( 'jquery' ) , '1.0' , true );
		wp_enqueue_script( 'mega-comment' , SSF_WP_ADDONS_BASE.'/ssf-rating-addon-wp/social-superstorefinder.js' , array( 'jquery' ) , '1.0' , true );
		wp_enqueue_script( 'mega-rating' , SSF_WP_ADDONS_BASE.'/ssf-rating-addon-wp/ssf-rating/jquery.raty.js' , array( 'jquery' ) , '1.0' , true );
		$fileCounter++;	
	
		 
		}
		
		
		/**.** Marker distance addon **.**/
		$addonDistance=$wpdb->get_results("SELECT * FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_wp_addon_name='ssf-distance-addon-wp' AND ssf_wp_addon_status='on' ", ARRAY_A);
		if(!empty($addonDistance) && is_dir(SSF_WP_ADDONS_PATH.'/ssf-distance-addon-wp'))
		{
		 print "<script> var addonDistanceCheck=true; </script>";
		 if($fileCounter!=2){
		 wp_enqueue_script( 'mega-superstorfinder' , SSF_WP_ADDONS_BASE.'/ssf-distance-addon-wp/mega-superstorefinder.js' , array( 'jquery' ) , '1.0' , true );
		 }
		 $fileCounter++;	
		}else{		
		 print "<script> var addonDistanceCheck=false; </script>";
		}
		
		/**.** Marker multicategory addon **.**/
		$multiCateory=$wpdb->get_results("SELECT * FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_wp_addon_name='ssf-multi-category-wp' AND ssf_wp_addon_status='on' ", ARRAY_A);
		if(!empty($multiCateory) && is_dir(SSF_WP_ADDONS_PATH.'/ssf-multi-category-wp'))
		{
		 print "<script> var addonMultiCategory=true; </script>"; 
		 print "<style>#productsServicesFilterOptions i.icon{
				display:none;
			}</style>";
         if($fileCounter!=2){
		 wp_enqueue_script( 'mega-superstorfinder' , SSF_WP_ADDONS_BASE.'/ssf-multi-category-wp/mega-superstorefinder.js' , array( 'jquery' ) , '1.0' , true );
		 }
		 $fileCounter++;	
		}else{
			print "<script> var addonMultiCategory=false; </script>"; 
		}
		if($fileCounter==1)
		{
		wp_enqueue_script( 'mega-superstorfinder' , SSF_WP_ADDONS_BASE.'/ssf-marker-cluster-wp/mega-superstorefinder.js' , array( 'jquery' ) , '1.0' , true );	
		}
			
		if($fileCounter==0)
		{ wp_enqueue_script( 'mega-superstorfinder' , SSF_WP_JS_BASE.'/mega-superstorefinder.js' , array( 'jquery' ) , '1.0' , true ); }

		// mega locator


		if (function_exists("do_ssf_wp_hook")){do_ssf_wp_hook('ssf_wp_addon_head_styles');}
		ssf_wp_move_upload_directories();

	}
}

function ssf_wp_footer_scripts(){

	if (!did_action('wp_head')){ ssf_wp_head_scripts();} //if wp_head missing

}

add_action('wp_print_footer_scripts', 'ssf_wp_footer_scripts');



function ssf_wp_jq() {wp_enqueue_script( 'jquery');}

add_action('wp_enqueue_scripts', 'ssf_wp_jq');

/*-----------------------------------*/

function ssf_wp_add_options_page() {

	global $ssf_wp_dir, $ssf_wp_base, $ssf_wp_uploads_base, $text_domain, $ssf_wp_top_nav_links, $ssf_wp_vars, $ssf_wp_version,$wpdb;

	$parent_url = SSF_WP_PARENT_URL; 

	$warning_count = 0;
	

	$warning_title = __("Update(s) currently available for Store Locator", SSF_WP_TEXT_DOMAIN) . ":";

	$ssf_wp_vars['ssf_wp_latest_version_check_time'] = (empty($ssf_wp_vars['ssf_wp_latest_version_check_time']))? date("Y-m-d H:i:s") : $ssf_wp_vars['ssf_wp_latest_version_check_time'];

	if (empty($ssf_wp_vars['ssf_wp_latest_version']) || (time() - strtotime($ssf_wp_vars['ssf_wp_latest_version_check_time']))/60>=(60*12)){ //12-hr db caching of version info

		

		$ssf_wp_latest_version = ''; 

		$ssf_wp_vars['ssf_wp_latest_version_check_time'] = date("Y-m-d H:i:s");

		$ssf_wp_vars['ssf_wp_latest_version'] = $ssf_wp_latest_version;

	} else {

		$ssf_wp_latest_version = $ssf_wp_vars['ssf_wp_latest_version'];

	}

	

	if (strnatcmp($ssf_wp_latest_version, $ssf_wp_version) > 0) { 

		$warning_title .= "\n- Store Locator v{$ssf_wp_latest_version} " . __("is available, you are using", SSF_WP_TEXT_DOMAIN). " v{$ssf_wp_version}";

		$warning_count++;

		$ssf_wp_plugin = SSF_WP_DIR . "/super-store-finder.php";

		$ssf_wp_update_link = admin_url('update.php?action=upgrade-plugin&plugin=' . $ssf_wp_plugin);

		$ssf_wp_update_link_nonce = wp_nonce_url($ssf_wp_update_link, 'upgrade-plugin_' . $ssf_wp_plugin);

		$ssf_wp_update_msg = "&nbsp;&gt;&nbsp;<a href='$ssf_wp_update_link_nonce' style='color:#900; font-weight:bold;' onclick='confirmClick(\"".__("You will now be updating to Store Locator", SSF_WP_TEXT_DOMAIN)." v$ssf_wp_latest_version, ".__("click OK or Confirm to continue", SSF_WP_TEXT_DOMAIN).".\", this.href); return false;'>".__("Update to", SSF_WP_TEXT_DOMAIN)." $ssf_wp_latest_version</a>";

	} else {

		$ssf_wp_update_msg = "";

	}

	if ( defined("SSF_WP_ADDONS_PLATFORM_DIR") ) {

	   $ssf_wp_vars['ssf_wp_latest_ap_check_time'] = (empty($ssf_wp_vars['ssf_wp_latest_ap_check_time']))? date("Y-m-d H:i:s") : $ssf_wp_vars['ssf_wp_latest_ap_check_time'];

	   if ( (empty($ssf_wp_vars['ssf_wp_latest_ap_version']) || (time() - strtotime($ssf_wp_vars['ssf_wp_latest_ap_check_time']))/60>=(60*12)) ) { //12-hr db caching of version info

		$ap_update = ssf_wp_remote_data(array(

			'pagetype' => 'ap',

			'dir' => SSF_WP_ADDONS_PLATFORM_DIR, 

			'key' => ssf_wp_data('ssf_wp_license_' . SSF_WP_ADDONS_PLATFORM_DIR)

		));

		$ap_latest_version = (!empty($ap_update[0]))? preg_replace("@\.zip|".SSF_WP_ADDONS_PLATFORM_DIR."\.@", "", $ap_update[0]['filename']) : 0;


		$ssf_wp_vars['ssf_wp_latest_ap_check_time'] = date("Y-m-d H:i:s");

		$ssf_wp_vars['ssf_wp_latest_ap_version'] = $ap_latest_version;

	   } else {

		$ap_latest_version = $ssf_wp_vars['ssf_wp_latest_ap_version'];

	   }


	   $ap_readme = SSF_WP_ADDONS_PLATFORM_PATH."/readme.txt"; 

	   if (file_exists($ap_readme)) {

		$rm_txt = file_get_contents($ap_readme);

		preg_match("/\n[ ]*stable tag:[ ]?([^\n]+)(\n)?/i", $rm_txt, $cv); 

		$ap_version = (!empty($cv[1]))? trim($cv[1]) : "1.0" ;

	   } else {$ap_version = "1.6";}



	   if (strnatcmp($ap_latest_version, $ap_version) > 0) {

		$ap_title = ucwords(str_replace("-", " ", SSF_WP_ADDONS_PLATFORM_DIR));

		$warning_title .= "\n- $ap_title v{$ap_latest_version} " . __("is available, you are using", SSF_WP_TEXT_DOMAIN). " v{$ap_version}";

		$warning_count++;

	   }

	} 
	$ssf_role='';
	function get_ssf_current_user_role() {
		global $wp_roles;
		$current_user = wp_get_current_user();
		$roles = $current_user->roles;
		$role = array_shift($roles);
		return trim($role);
    }
	$ssf_role=get_ssf_current_user_role();
	if(!isset($ssf_wp_vars['ssf_user_role'])){
		$ssf_wp_vars['ssf_user_role']='administrator';
	}
    $userRole=(trim($ssf_wp_vars['ssf_user_role'])!="")? $ssf_wp_vars['ssf_user_role'] : "administrator";
	$ex_cat = explode(",", $userRole);
    $ex_cat = array_map( 'trim', $ex_cat );
if(in_array($ssf_role,$ex_cat) || $ssf_role=='administrator'){
	$notify = ($warning_count > 0)?  " <span class='update-plugins count-$warning_count' title='$warning_title'><span class='update-count'>" . $warning_count . "</span></span>" : "" ;
	$ssf_wp_menu_pages['main'] = array('title' => __("Super Store Finder", SSF_WP_TEXT_DOMAIN)."$notify", 'capability' => $ssf_role, 'page_url' =>  $parent_url, 'icon' => SSF_WP_BASE.'/images/logo.ico.png', 'menu_position' => 47);
	$ssf_wp_menu_pages['sub']['information'] = array('parent_url' => $parent_url, 'title' => __("Quick Start", SSF_WP_TEXT_DOMAIN), 'capability' => $ssf_role, 'page_url' => $parent_url);
	$ssf_wp_menu_pages['sub']['locations'] = array('parent_url' => $parent_url, 'title' => __("Stores", SSF_WP_TEXT_DOMAIN), 'capability' => $ssf_role, 'page_url' => SSF_WP_PAGES_DIR.'/stores.php');
	$ssf_wp_menu_pages['sub']['region'] = array('parent_url' => $parent_url, 'title' => __("Regions", SSF_WP_TEXT_DOMAIN), 'capability' => $ssf_role, 'page_url' => SSF_WP_PAGES_DIR.'/region.php'); 
	$locales=$wpdb->get_results("SELECT * FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_wp_addon_name='ssf-custom-marker-wp' AND ssf_wp_addon_status='on'", ARRAY_A);
	if(!empty($locales))
	{ 
	 $ssf_wp_menu_pages['sub']['categories'] = array('parent_url' => $parent_url, 'title' => __("Categories", SSF_WP_TEXT_DOMAIN), 'capability' => $ssf_role, 'page_url' => SSF_WP_DIR.'/categories.php');
	}
	$ssf_wp_menu_pages['sub']['import'] = array('parent_url' => $parent_url, 'title' => __("Import/Export", SSF_WP_TEXT_DOMAIN), 'capability' => $ssf_role, 'page_url' => SSF_WP_PAGES_DIR.'/import.php');
	$ssf_wp_menu_pages['sub']['Add-ons'] = array('parent_url' => $parent_url, 'title' => __("Add-ons", SSF_WP_TEXT_DOMAIN), 'capability' => $ssf_role, 'page_url' => SSF_WP_PAGES_DIR.'/Add-ons.php');
	$ssf_wp_menu_pages['sub']['settings'] = array('parent_url' => $parent_url, 'title' => __("Settings", SSF_WP_TEXT_DOMAIN), 'capability' => $ssf_role, 'page_url' => SSF_WP_PAGES_DIR.'/settings.php');
	ssf_wp_menu_pages_filter($ssf_wp_menu_pages);
   }
}



function ssf_wp_menu_pages_filter($ssf_wp_menu_pages) {

	if (function_exists('do_ssf_wp_hook')){do_ssf_wp_hook('ssf_wp_menu_pages_filter', '', array(&$ssf_wp_menu_pages));}
	
	foreach ($ssf_wp_menu_pages as $menu_type => $value) {

		if ($menu_type == 'main') {

			add_menu_page ($value['title'], $value['title'], $value['capability'], $value['page_url'], '', $value['icon'], $value['menu_position']);

		}

		if ($menu_type == 'sub'){

			foreach ($value as $sub_value) {

				 add_submenu_page($sub_value['parent_url'], $sub_value['title'], $sub_value['title'], $sub_value['capability'], $sub_value['page_url']);

			}

		}

	}

}

function ssf_wp_where_clause_filter(&$where){

	if (function_exists("do_ssf_wp_hook")) {do_ssf_wp_hook("ssf_wp_where_clause_filter");}

}

/*---------------------------------------------------*/

function ssf_wp_add_admin_javascript() {

        global $ssf_wp_base, $ssf_wp_uploads_base, $ssf_wp_dir, $google_map_domain, $ssf_wp_path, $ssf_wp_uploads_path, $ssf_wp_map_language, $ssf_wp_vars;

		wp_enqueue_script( 'prettyPhoto', SSF_WP_JS_BASE."/jquery.prettyPhoto.js", "jQuery");
		wp_enqueue_script( 'ssf_wp_func', SSF_WP_JS_BASE."/functions.js", "jQuery");
		wp_enqueue_script('nicEdit', SSF_WP_JS_BASE."/nicEdit.js", "jQuery" );
		wp_enqueue_script('chosenJquery', SSF_WP_JS_BASE."/chosen/chosen.jquery.js", "jQuery" );
         wp_enqueue_script('chosenProto', SSF_WP_JS_BASE."/chosen/chosen.proto.min.js", "jQuery" );

        print "<script type='text/javascript'>";
		
        $admin_js = "
		
        var ssf_wp_dir='".$ssf_wp_dir."';

        var ssf_wp_google_map_country='".$ssf_wp_vars['google_map_country']."';

        var ssf_wp_base='".SSF_WP_BASE."';

        var ssf_wp_path='".SSF_WP_PATH."';
		
		var default_distance = '';
	
		var init_zoom = '';
	
		var zoomhere_zoom = '';
	
		var geo_settings = '';
	
		var default_location = '';
	
		var style_map_color = '';

        var ssf_wp_uploads_base='".SSF_WP_UPLOADS_BASE."';

        var ssf_wp_uploads_path='".SSF_WP_UPLOADS_PATH."';

        var ssf_wp_addons_base=ssf_wp_uploads_base+'".str_replace(SSF_WP_UPLOADS_BASE, '', SSF_WP_ADDONS_BASE)."';

        var ssf_wp_addons_path=ssf_wp_uploads_path+'".str_replace(SSF_WP_UPLOADS_PATH, '', SSF_WP_ADDONS_PATH)."';

        var ssf_wp_includes_base=ssf_wp_base+'".str_replace(SSF_WP_BASE, '', SSF_WP_INCLUDES_BASE)."';

        var ssf_wp_includes_path=ssf_wp_path+'".str_replace(SSF_WP_PATH, '', SSF_WP_INCLUDES_PATH)."';

        var ssf_wp_cache_path=ssf_wp_uploads_path+'".str_replace(SSF_WP_UPLOADS_PATH, '', SSF_WP_CACHE_PATH)."';

        var ssf_wp_pages_base=ssf_wp_base+'".str_replace(SSF_WP_BASE, '', SSF_WP_PAGES_BASE)."'";

        print preg_replace("@[\\\]@", "\\\\\\", $admin_js); 

        print "</script>\n";

        if (preg_match("@add-store\.php|locations\.php@", $_SERVER['REQUEST_URI'])) {

			if (!file_exists(SSF_WP_ADDONS_PATH."/point-click-add/point-click-add.js")) {

				$sens=(!empty($ssf_wp_vars['sensor']))? "sensor=".$ssf_wp_vars['sensor'] : "sensor=false" ;

				$lang_loc=(!empty($ssf_wp_vars['map_language']))? "&amp;language=".$ssf_wp_vars['map_language'] : "" ; 

				$region_loc=(!empty($ssf_wp_vars['map_region']))? "&amp;region=".$ssf_wp_vars['map_region'] : "" ;

				$key=(!empty($ssf_wp_vars['api_key']))? "&amp;key=".$ssf_wp_vars['api_key'] : "" ;

				

			}

            if (file_exists(SSF_WP_ADDONS_PATH."/point-click-add/point-click-add.js")) {

				$sens=(!empty($ssf_wp_vars['sensor']))? "sensor=".$ssf_wp_vars['sensor'] : "sensor=false" ;

				$char_enc='&amp;oe='.$ssf_wp_vars['map_character_encoding'];

				$google_map_domain=(!empty($ssf_wp_vars['google_map_domain']))? $ssf_wp_vars['google_map_domain'] : "maps.google.com";

				$api=ssf_wp_data('store_locator_api_key');

			}

        }

		if (function_exists('do_ssf_wp_hook')){do_ssf_wp_hook('ssf_wp_addon_admin_scripts');}

}

function ssf_wp_remove_conflicting_scripts(){

	if (preg_match("@".SSF_WP_DIR."@", $_SERVER['REQUEST_URI'])){

		wp_dequeue_script('ui-tabs'); 

	}

}

add_action('admin_enqueue_scripts', 'ssf_wp_remove_conflicting_scripts');

add_action( 'admin_enqueue_scripts', 'ssf_mw_enqueue_color_picker' );

function ssf_mw_enqueue_color_picker( $hook_suffix ) {

    // first check that $hook_suffix is appropriate for your administrator's page

    wp_enqueue_style( 'wp-color-picker' );

    wp_enqueue_script( 'my-script-handle', plugins_url('js/docs.js', __FILE__ ), array( 'wp-color-picker' ), false, true );

}



function ssf_wp_add_admin_stylesheet() {

  		global $ssf_wp_base;
		wp_enqueue_style( 'mega-font-awesome' , SSF_WP_CSS_BASE.'/font-awesome.css' , true , '4.1' );
		wp_enqueue_style( 'mega-ssf-wp-admin' , SSF_WP_CSS_BASE.'/ssf-wp-admin.css' , true , '1.0' );
		wp_enqueue_style( 'mega-ssf-wp-font' , SSF_WP_CSS_BASE.'/ssf-wp-pop.css' , true , '1.0' );
		wp_enqueue_style('mega-ssf-wp-chosen' , SSF_WP_JS_BASE.'/chosen/chosen.min.css' , true , '1.0' );

}


/*---------------------------------*/

function ssf_wp_ssf_set_query_defaults() {

	global $where, $o, $d, $ssf_wp_searchable_columns, $wpdb;

	$extra="";  

	if (function_exists("do_ssf_wp_hook") && !empty($ssf_wp_searchable_columns) && !empty($_GET['q'])) {

		foreach ($ssf_wp_searchable_columns as $value) {

			$extra .= $wpdb->prepare(" OR $value LIKE '%%%s%%'", $_GET['q']);

		}

	}

	$where=(!empty($_GET['q']))? $wpdb->prepare(" WHERE ssf_wp_store LIKE '%%%s%%' OR ssf_wp_address LIKE '%%%s%%' OR ssf_wp_city LIKE '%%%s%%' OR ssf_wp_state LIKE '%%%s%%' OR ssf_wp_zip LIKE '%%%s%%' OR ssf_wp_tags LIKE '%%%s%%'", $_GET['q'], $_GET['q'], $_GET['q'], $_GET['q'], $_GET['q'], $_GET['q'])." ".$extra : "" ; //die($where);

	

	$o=(!empty($_GET['o']))? esc_sql($_GET['o']) : "ssf_wp_store";

	$d=(empty($_GET['d']) || $_GET['d']=="DESC")? "ASC" : "DESC";

}

//for region page function
function ssf_wp_region_query_defaults() {
	global $where, $o, $d, $ssf_wp_searchable_columns, $wpdb;
	$extra="";  
	if (function_exists("do_ssf_wp_hook") && !empty($ssf_wp_searchable_columns) && !empty($_GET['q'])) {
		foreach ($ssf_wp_searchable_columns as $value) {
			$extra .= $wpdb->prepare(" OR $value LIKE '%%%s%%'", $_GET['q']);
		}
	}
	
	$where=(!empty($_GET['q']))? $wpdb->prepare(" WHERE ssf_wp_region_name LIKE '%%%s%%' OR ssf_wp_region_address LIKE '%%%s%%' ", $_GET['q'], $_GET['q'])." ".$extra : "" ; //die($where);
	
	$o=(!empty($_GET['o']))? esc_sql($_GET['o']) : "ssf_wp_region_name";
	$d=(empty($_GET['d']) || $_GET['d']=="DESC")? "ASC" : "DESC";
}
//region code end



function ssf_set_query_defaults() {ssf_wp_ssf_set_query_defaults();}

/*--------------------------------------------------------------*/

function ssf_do_hyperlink(&$text, $target="'_blank'", $type="both"){

  if ($type=="both" || $type=="protocol") {	

   

   $text = preg_replace("@[a-zA-Z]+://([.]?[a-zA-Z0-9_/?&amp;%20,=\-\+\-\#])+@s", "<a href=\"\\0\" target=$target>\\0</a>", $text);

  }

  if ($type=="both" || $type=="noprotocol") {

   

   $text = preg_replace("@(^| )(www([.]?[a-zA-Z0-9_/=-\+-\#])*)@s", "\\1<a href=\"http://\\2\" target=$target>\\2</a>", $text);

  }

  return $text;

}

/*-------------------------------------------------------------*/

function ssf_comma($a) {
    $a=str_replace("&", "&amp;", $a);
	$a=str_replace('"', "&quot;", $a);
	$a=str_replace("'", "&#39;", $a);
	$a=str_replace(">", "&gt;", $a);
	$a=str_replace("<", "&lt;", $a);
	$a=str_replace(" & ", " &amp; ", $a);
	return str_replace("," ,"&#44;" ,$a);
}

/*------------------------------------------------------------*/

if (!function_exists('addon_activation_message')) {

	function addon_activation_message($url_of_upgrade="") {

		global $ssf_wp_dir, $text_domain;

		print "<div style='background-color:#eee; border:solid silver 1px; padding:7px; color:black; display:block;'>".__("You haven't activated this upgrade yet", SSF_WP_TEXT_DOMAIN).". ";

		if (function_exists('do_ssf_wp_hook') && !preg_match("/addons\-platform/", $url_of_upgrade) ){

			print "<a href='".SSF_WP_ADDONS_PAGE."'>".__("Activate", SSF_WP_TEXT_DOMAIN)."</a></div><br>";

		} else {

			print __("Go to pull-out Dashboard, and activate under 'Activation Keys' section.", SSF_WP_TEXT_DOMAIN)."</div><br>";

		}

	}

}

/*-----------------------------------------------------------*/

function ssf_url_test($url){

	if (preg_match("@^https?://@i", $url)) {

		return TRUE; 

	} else {

		return FALSE; 

	}

}

/*---------------------------------------------------------------*/

function ssf_wp_neat_title($ttl,$separator="_") {

	$normalizeChars = array(

    'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',

    'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',

    'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',

    'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',

    'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',

    'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',

    'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f', 

    'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T');

	$ttl = strtr($ttl, $normalizeChars);

	$ttl = html_entity_decode( str_replace("&#39;","'",$ttl) );

	$ttl = preg_replace("/@+/", "$separator", 

			preg_replace("/[^[:alnum:]]/", "@", 

				trim(

					preg_replace("/[^[:alnum:]]/", " ", 

						str_replace("'", "", 

							ssf_wp_truncate(

								trim(

									strtolower($ttl)

								), 

							100)

						)

					)

				)

			)

		);

	return $ttl;

}

/*-------------------------------*/

function ssf_wp_truncate($var,$length=50,$mode="return", $type=1) {

	if (strlen($var)>$length) {

		if ($type==1) { 

			$var=substr($var,0,$length);

			$var=preg_replace("@[[:space:]]{1}.{1,10}$@s", "", $var); 

			$var=$var."...";

		}

		elseif ($type==2) { 

			$r_num=mt_rand();

			$r_num2=$r_num."_2";

			$var1=substr($var,0,$length);

			$var2=substr($var,$length, strlen($var)-$length);

			$var="<span id='$r_num'>$var1</span><span id='$r_num2' style='display:none'>".$var1.$var2."</span><a href='#' onclick=\"show('$r_num');show('$r_num2');this.innerHTML=(this.innerHTML.indexOf('more')!=-1)?'(...less)':'(more...)';return false;\">(more...)</a>";

		}

		elseif ($type==3) { 

			$var=substr($var,0,$length);

			$var=$var."...";

		}

	}

	if ($mode!="print") {

		return $var;

	}

	else {

		print $var;

	}

}

/*-----------------------------------------------------------*/

function ssf_wp_process_tags($tag_string, $db_action="insert", $ssf_wp_id="") {

	global $wpdb;

	$id_string="";

	if (!is_array($ssf_wp_id) && preg_match("@,@", $ssf_wp_id)) {

		$id_string=$ssf_wp_id;

		$ssf_wp_id=explode(",",$id_string);

		$rplc_arr=array_fill(0, count($ssf_wp_id), "%d"); 

		$id_string=implode(",", array_map(array($wpdb, "prepare"), $rplc_arr, $ssf_wp_id)); 

	} elseif (is_array($ssf_wp_id)) {

		$rplc_arr=array_fill(0, count($ssf_wp_id), "%d"); 

 		$id_string=implode(",", array_map(array($wpdb, "prepare"), $rplc_arr, $ssf_wp_id)); 

	} else {

		$id_string=$wpdb->prepare("%d", $ssf_wp_id); 

	}
	$tag_string=str_replace('@','',$tag_string); 
	//$tag_string=str_replace('&','',$tag_string); 
	$tag_string=str_replace('#','',$tag_string); 
	$tag_string=str_replace('$','',$tag_string); 
	$tag_string=str_replace('%','',$tag_string); 
	$tag_string=str_replace('!','',$tag_string);

	if (preg_match("@,@", $tag_string)) { 

		//$tag_string=preg_replace('/[^A-Za-z0-9_\-, ]/', '', $tag_string); 

		$ssf_wp_tag_array=array_map('trim',explode(",",trim($tag_string))); 

		//$ssf_wp_tag_array=array_map('strtolower', $ssf_wp_tag_array); 

	} else { 

		//$tag_string=preg_replace('/[^A-Za-z0-9_\-, ]/', '', $tag_string); 

		$ssf_wp_tag_array[]=trim($tag_string); 

	} 

	

	if ($db_action=="insert" || $db_action=="insertTags") {
		
		if($db_action=="insertTags"){
			$result=$wpdb->get_results("SELECT ssf_wp_tag_slug FROM ".SSF_WP_TAG_TABLE." WHERE ssf_wp_id IN ($id_string)");
			if($result){
				foreach ($result as $row)  {
					array_push($ssf_wp_tag_array,$row->ssf_wp_tag_slug);
				}
				   $ssf_wp_tag_array=array_unique($ssf_wp_tag_array);
				   $wpdb->query("DELETE FROM ".SSF_WP_TAG_TABLE." WHERE ssf_wp_id IN ($id_string)");
			}else{
				$ssf_wp_tag_array=$ssf_wp_tag_array;
			}			
		}else{
		   $wpdb->query("DELETE FROM ".SSF_WP_TAG_TABLE." WHERE ssf_wp_id IN ($id_string)");  //clear current tags for locations being modified 
		}

		$query="INSERT INTO ".SSF_WP_TAG_TABLE." (ssf_wp_tag_slug, ssf_wp_id) VALUES ";

		if (!is_array($ssf_wp_id)) {

			$main_ssf_wp_id=($ssf_wp_id==="")? $wpdb->insert_id : $ssf_wp_id ; 

			foreach ($ssf_wp_tag_array as $value)  {

				if (trim($value)!="") {

					$query.="('$value', '$main_ssf_wp_id'),";

				}

			}

		} elseif (is_array($ssf_wp_id)) {

			foreach ($ssf_wp_id as $value2) {

				$main_ssf_wp_id=$value2;

				foreach ($ssf_wp_tag_array as $value)  {

					if (trim($value)!="") {

						$query.="('$value', '$main_ssf_wp_id'),";

					}

				}

			}

		}

		$query=substr($query, 0, strlen($query)-1); 

	} elseif ($db_action=="delete") {

		if (trim($tag_string)==="") {

			$query="DELETE FROM ".SSF_WP_TAG_TABLE." WHERE ssf_wp_id IN ($id_string)";

			

		} else {

			$t_string=implode("','", $ssf_wp_tag_array);  

			$query="DELETE FROM ".SSF_WP_TAG_TABLE." WHERE ssf_wp_id IN ($id_string) AND ssf_wp_tag_slug IN ('".trim($t_string)."')"; 

		}

	} 

	$wpdb->query($query);

}

/*-----------------------------------------------------------*/

function ssf_wp_ty($file){

	$ty = '';

	return $ty;

}

/*-----------------------------------------------------------*/

function ssf_wp_prepare_tag_string($ssf_wp_tags) {
	$ssf_wp_tags=preg_replace('/\,+/', ', ', $ssf_wp_tags); 
	$ssf_wp_tags=str_replace('@','',$ssf_wp_tags); 
	//$ssf_wp_tags=str_replace('& ','',$ssf_wp_tags); 
	$ssf_wp_tags=str_replace('#','',$ssf_wp_tags); 
	$ssf_wp_tags=str_replace('$','',$ssf_wp_tags); 
	$ssf_wp_tags=str_replace('%','',$ssf_wp_tags); 
	$ssf_wp_tags=str_replace('!','',$ssf_wp_tags);

	$ssf_wp_tags=preg_replace('/(\&\#44\;)+/', '&#44; ', $ssf_wp_tags); 

	//$ssf_wp_tags=preg_replace('/[^A-Za-z0-9_\-,]/', '', $ssf_wp_tags); 
	
	if (substr($ssf_wp_tags, 0, 1) == ",") {

		$ssf_wp_tags=substr($ssf_wp_tags, 1, strlen($ssf_wp_tags));

	}

	if (substr($ssf_wp_tags, strlen($ssf_wp_tags)-1, 1) != "," && trim($ssf_wp_tags)!="") {
		
		$ssf_wp_tags.=",";

	}

	$ssf_wp_tags=preg_replace('/\,+/', ', ', $ssf_wp_tags);

	$ssf_wp_tags=preg_replace('/(\&\#44\;)+/', '&#44; ', $ssf_wp_tags);

	$ssf_wp_tags=preg_replace('/[ ]*,[ ]*/', ', ', $ssf_wp_tags); 

 	$ssf_wp_tags=preg_replace('/[ ]*\&\#44\;[ ]*/', '&#44; ', $ssf_wp_tags); 

	$ssf_wp_tags=trim($ssf_wp_tags);
	$ssf_wp_tags=str_replace(', ,',', ',$ssf_wp_tags);

	return $ssf_wp_tags;

}

/*-----------------------------------------------------------*/

function ssf_wp_data($setting_name, $i_u_d_s="select", $setting_value="") {
	
	global $wpdb;

	if ($i_u_d_s == "insert" || $i_u_d_s == "add" || $i_u_d_s == "update") {

		$setting_value = (is_array($setting_value))? serialize($setting_value) : $setting_value;

		$exists = $wpdb->get_var($wpdb->prepare("SELECT ssf_wp_setting_id FROM ".SSF_WP_SETTING_TABLE." WHERE ssf_wp_setting_name = %s", $setting_name));
        
		if (!$exists) {	

			$q = $wpdb->prepare("INSERT INTO ".SSF_WP_SETTING_TABLE." (ssf_wp_setting_name, ssf_wp_setting_value) VALUES (%s, %s)", $setting_name, $setting_value); 

		} else { 
			$q = $wpdb->prepare("UPDATE ".SSF_WP_SETTING_TABLE." SET ssf_wp_setting_value = %s WHERE ssf_wp_setting_name = %s", $setting_value, $setting_name);

		}

		$wpdb->query($q);

	} elseif ($i_u_d_s == "delete") {

		$q = $wpdb->prepare("DELETE FROM ".SSF_WP_SETTING_TABLE." WHERE ssf_wp_setting_name = %s", $setting_name);

		$wpdb->query($q);

	} elseif ($i_u_d_s == "select" || $i_u_d_s == "get") {
		$q = $wpdb->prepare("SELECT ssf_wp_setting_value FROM ".SSF_WP_SETTING_TABLE." WHERE ssf_wp_setting_name = %s", $setting_name);

		$r = $wpdb->get_var($q);

		$r = (@unserialize($r) !== false || $r === 'b:0;')? unserialize($r) : $r;  //checking if stored in serialized form
		
		return $r;
	  } 
	 
}




/*----------------------------------------------------------------*/

function ssf_wp_dyn_js($post_content=""){

	global $ssf_wp_dir, $ssf_wp_base, $ssf_wp_uploads_base, $ssf_wp_path, $ssf_wp_uploads_path, $wpdb, $ssf_wp_version, $pagename, $ssf_wp_map_language, $post, $ssf_wp_vars;

	global $superstorefinder;
	$wpml_current_language = apply_filters( 'wpml_current_language', NULL ); 
	
	

	//general
	//general
	if(isset($_REQUEST['boxsearch']) && $_REQUEST['boxsearch']!=""){
	$mt = 'specific';
	$default_location = $_REQUEST['boxsearch'];
	} else {
	$mt=(trim($ssf_wp_vars['ssf_wp_map_settings'])!="")? $ssf_wp_vars['ssf_wp_map_settings'] : "geo"; 
	$default_location=(trim($ssf_wp_vars['default_location'])!="")? $ssf_wp_vars['default_location'] : "New York, US"; 
	}


	$ssf_layout=(trim($ssf_wp_vars['ssf_layout'])!="")? $ssf_wp_vars['ssf_layout'] : "bottom"; 
	
	$labeled_marker=(trim($ssf_wp_vars['labeled_marker'])!="")? $ssf_wp_vars['labeled_marker'] : "1"; 
	
	$google_map_region=(trim($ssf_wp_vars['google_map_region'])!="")? $ssf_wp_vars['google_map_region'] : "World"; 
		
	$google_map_language=(trim($ssf_wp_vars['google_map_language'])!="")? $ssf_wp_vars['google_map_language'] : "en"; 
	
	$zoom_level=(trim($ssf_wp_vars['zoom_level'])!="")? $ssf_wp_vars['zoom_level'] : "auto"; 
	
	$map_mouse_scroll=(trim($ssf_wp_vars['map_mouse_scroll'])!="")? $ssf_wp_vars['map_mouse_scroll'] : "0"; 
	
	$ssf_mobile_fields=(trim($ssf_wp_vars['ssf_mobile_fields'])!="")? $ssf_wp_vars['ssf_mobile_fields'] : "1"; 
	
	$show_scroll_set=(trim($ssf_wp_vars['show_scroll_set'])!="")? $ssf_wp_vars['show_scroll_set'] : "1";

	$dir=SSF_WP_UPLOADS_PATH."/images/icons/";
	if(file_exists($dir.'/custom-marker.png'))
	{
		$custom_marker="custom-marker.png";
	}
   else {
	   $custom_marker="map-pin.png";
   }
   
   if(file_exists($dir.'/custom-marker-active.png'))
	{
		$custom_marker_active="custom-marker-active.png";
	}
   else {
	   $custom_marker_active="map-pin-active.png";
   }
	
	if($zoom_level!='auto'){
		$init_zoom=$zoom_level;
	} else {
		$init_zoom=7;
	}



	//styles

	$ssf_wp_map_code=(trim($ssf_wp_vars['ssf_wp_map_code'])!="")? $ssf_wp_vars['ssf_wp_map_code'] : "";
	
	$ssf_wp_page_bg=(trim($ssf_wp_vars['style_map_bg'])!="")? ssfParseToXML($ssf_wp_vars['style_map_bg']) : "";

	$style_map_color=(trim($ssf_wp_vars['style_map_color'])!="")? ssfParseToXML($ssf_wp_vars['style_map_color']) : "";

	$style_top_bar_bg=(trim($ssf_wp_vars['style_top_bar_bg'])!="")? ssfParseToXML($ssf_wp_vars['style_top_bar_bg']) : "";

	$style_top_bar_font=(trim($ssf_wp_vars['style_top_bar_font'])!="")? ssfParseToXML($ssf_wp_vars['style_top_bar_font']) : "";
	
	$style_geo_font=(trim($ssf_wp_vars['style_geo_font'])!="")? ssfParseToXML($ssf_wp_vars['style_geo_font']) : "";

	$style_top_bar_border=(trim($ssf_wp_vars['style_top_bar_border'])!="")? ssfParseToXML($ssf_wp_vars['style_top_bar_border']) : "";
	
	$filter_font_color=(trim($ssf_wp_vars['filter_font_color'])!="")? ssfParseToXML($ssf_wp_vars['filter_font_color']) : "";

	$style_results_bg=(trim($ssf_wp_vars['style_results_bg'])!="")? ssfParseToXML($ssf_wp_vars['style_results_bg']) : "";

	$style_results_hl_bg=(trim($ssf_wp_vars['style_results_hl_bg'])!="")? ssfParseToXML($ssf_wp_vars['style_results_hl_bg']) : "";

	$style_results_hover_bg=(trim($ssf_wp_vars['style_results_hover_bg'])!="")? ssfParseToXML($ssf_wp_vars['style_results_hover_bg']) : "";

	$style_results_font=(trim($ssf_wp_vars['style_results_font'])!="")? ssfParseToXML($ssf_wp_vars['style_results_font']) : "";

	$style_results_distance_font=(trim($ssf_wp_vars['style_results_distance_font'])!="")? ssfParseToXML($ssf_wp_vars['style_results_distance_font']) : "";

	$style_distance_toggle_bg=(trim($ssf_wp_vars['style_distance_toggle_bg'])!="")? ssfParseToXML($ssf_wp_vars['style_distance_toggle_bg']) : "";

	$style_contact_button_bg=(trim($ssf_wp_vars['style_contact_button_bg'])!="")? ssfParseToXML($ssf_wp_vars['style_contact_button_bg']) : "";

	$style_contact_button_font=(trim($ssf_wp_vars['style_contact_button_font'])!="")? ssfParseToXML($ssf_wp_vars['style_contact_button_font']) : "";
	
	$style_info_link_bg=(trim($ssf_wp_vars['style_info_link_bg'])!="")? ssfParseToXML($ssf_wp_vars['style_info_link_bg']) : "";

	$style_info_link_font=(trim($ssf_wp_vars['style_info_link_font'])!="")? ssfParseToXML($ssf_wp_vars['style_info_link_font']) : "";
	
	$previousnextbuttonbg=(trim($ssf_wp_vars['previousnextbuttonbg'])!="")? ssfParseToXML($ssf_wp_vars['previousnextbuttonbg']) : "";
	
	$previousnextbuttonclr=(trim($ssf_wp_vars['previousnextbuttonclr'])!="")? ssfParseToXML($ssf_wp_vars['previousnextbuttonclr']) : "";
	
	$storesnearyoucolor=(trim($ssf_wp_vars['storesnearyou'])!="")? ssfParseToXML($ssf_wp_vars['storesnearyou']) : "";
	

	$style_button_bg=(trim($ssf_wp_vars['style_button_bg'])!="")? ssfParseToXML($ssf_wp_vars['style_button_bg']) : "";

	$style_button_font=(trim($ssf_wp_vars['style_button_font'])!="")? ssfParseToXML($ssf_wp_vars['style_button_font']) : "";

	$style_list_number_bg=(trim($ssf_wp_vars['style_list_number_bg'])!="")? ssfParseToXML($ssf_wp_vars['style_list_number_bg']) : "";

	$style_list_number_font=(trim($ssf_wp_vars['style_list_number_font'])!="")? ssfParseToXML($ssf_wp_vars['style_list_number_font']) : "";
	
	$style_list_number_circle=(trim($ssf_wp_vars['style_list_number_circle'])!="")? ssfParseToXML($ssf_wp_vars['style_list_number_circle']) : "";
	$style_list_number_circle_active=(trim($ssf_wp_vars['style_list_number_circle_active'])!="")? ssfParseToXML($ssf_wp_vars['style_list_number_circle_active']) : "";
	$style_list_number_bg_active=(trim($ssf_wp_vars['style_list_number_bg_active'])!="")? ssfParseToXML($ssf_wp_vars['style_list_number_bg_active']) : "";
	$style_list_number_font_active=(trim($ssf_wp_vars['style_list_number_font_active'])!="")? ssfParseToXML($ssf_wp_vars['style_list_number_font_active']) : "";
	
	
	$ssf_map_position=(trim($ssf_wp_vars['ssf_map_position'])!="")? ssfParseToXML($ssf_wp_vars['ssf_map_position']) : "true";
	
	$info_window_buttons=(trim($ssf_wp_vars['info_window_buttons'])!="")? ssfParseToXML($ssf_wp_vars['info_window_buttons']) : "";
	
	$infowindowlink=(trim($ssf_wp_vars['infowindowlink'])!="")? ssfParseToXML($ssf_wp_vars['infowindowlink']) : "";
	
	$geo_location_icon=(trim($ssf_wp_vars['geo_location_icon'])!="")? $ssf_wp_vars['geo_location_icon'] : "1";
	

	//labels
	
$stores_near_you=(trim($ssf_wp_vars['stores_near_you'])!="")? ssfParseToXML($ssf_wp_vars['stores_near_you']) : "Stores near you";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $stores_near_you, $stores_near_you );
$stores_near_you = apply_filters( 'wpml_translate_single_string', $stores_near_you, 'superstorefinder-wp', $stores_near_you);

$search_label=(trim($ssf_wp_vars['search_label'])!="")? ssfParseToXML($ssf_wp_vars['search_label']) : "Search for nearby stores";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $search_label, $search_label );
$search_label = apply_filters( 'wpml_translate_single_string', $search_label, 'superstorefinder-wp', $search_label);
$external_url_label=(trim($ssf_wp_vars['Url_label'])!="")? ssfParseToXML($ssf_wp_vars['Url_label']) : "View More";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $external_url_label, $external_url_label );
$external_url_label = apply_filters( 'wpml_translate_single_string', $external_url_label, 'superstorefinder-wp', $external_url_label);
$ssf_distance_limit=(trim($ssf_wp_vars['ssf_distance_km'])!="")? ssfParseToXML($ssf_wp_vars['ssf_distance_km']) : "30";
$ssf_conatct_email=(trim($ssf_wp_vars['ssf_conatct_email'])!="")? ssfParseToXML($ssf_wp_vars['ssf_conatct_email']) : "";

$outlet_label=(trim($ssf_wp_vars['outlet_label'])!="")? ssfParseToXML($ssf_wp_vars['outlet_label']) : "outlets";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $outlet_label, $outlet_label );
$outlet_label = apply_filters( 'wpml_translate_single_string', $outlet_label, 'superstorefinder-wp', $outlet_label);

$of_label=(trim($ssf_wp_vars['of_label'])!="")? ssfParseToXML($ssf_wp_vars['of_label']) : "of";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $of_label, $of_label );
$of_label = apply_filters( 'wpml_translate_single_string', $of_label, 'superstorefinder-wp', $of_label);

$clear_all_label=(trim($ssf_wp_vars['clear_all_label'])!="")? ssfParseToXML($ssf_wp_vars['clear_all_label']) : "Clear All";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $clear_all_label, $clear_all_label );
$clear_all_label = apply_filters( 'wpml_translate_single_string', $clear_all_label, 'superstorefinder-wp', $clear_all_label);

$show_all_label=(trim($ssf_wp_vars['show_all_label'])!="")? ssfParseToXML($ssf_wp_vars['show_all_label']) : "Show All";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $show_all_label, $show_all_label );
$show_all_label = apply_filters( 'wpml_translate_single_string', $show_all_label, 'superstorefinder-wp', $show_all_label);

$by_region_label=(trim($ssf_wp_vars['by_region_label'])!="")? ssfParseToXML($ssf_wp_vars['by_region_label']) : "By Region";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $by_region_label, $by_region_label );
$by_region_label = apply_filters( 'wpml_translate_single_string', $by_region_label, 'superstorefinder-wp', $by_region_label);
$by_distance_label=(trim($ssf_wp_vars['by_distance_label'])!="")? ssfParseToXML($ssf_wp_vars['by_distance_label']) : "By Radius";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $by_distance_label, $by_distance_label );
$by_distance_label = apply_filters( 'wpml_translate_single_string', $by_distance_label, 'superstorefinder-wp', $by_distance_label);
$all_category=(trim($ssf_wp_vars['all_category'])!="")? ssfParseToXML($ssf_wp_vars['all_category']) : "All Category";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $all_category, $all_category );
$all_category = apply_filters( 'wpml_translate_single_string', $all_category, 'superstorefinder-wp', $all_category);

$by_category=(trim($ssf_wp_vars['by_category'])!="")? ssfParseToXML($ssf_wp_vars['by_category']) : "Category";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $by_category, $by_category );
$by_category = apply_filters( 'wpml_translate_single_string', $by_category, 'superstorefinder-wp', $by_category);

$select_label=(trim($ssf_wp_vars['select_label'])!="")? ssfParseToXML($ssf_wp_vars['select_label']) : "Select";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $select_label, $select_label );
$select_label = apply_filters( 'wpml_translate_single_string', $select_label, 'superstorefinder-wp', $select_label);

$cancel_label=(trim($ssf_wp_vars['cancel_label'])!="")? ssfParseToXML($ssf_wp_vars['cancel_label']) : "Cancel";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $cancel_label, $cancel_label );
$cancel_label = apply_filters( 'wpml_translate_single_string', $cancel_label, 'superstorefinder-wp', $cancel_label);

$filter_label=(trim($ssf_wp_vars['filter_label'])!="")? ssfParseToXML($ssf_wp_vars['filter_label']) : "Filters";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $filter_label, $filter_label );
$filter_label = apply_filters( 'wpml_translate_single_string', $filter_label, 'superstorefinder-wp', $filter_label);

$short_search_label=(trim($ssf_wp_vars['short_search_label'])!="")? ssfParseToXML($ssf_wp_vars['short_search_label']) : "Search";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $short_search_label, $short_search_label );
$short_search_label = apply_filters( 'wpml_translate_single_string', $short_search_label, 'superstorefinder-wp', $short_search_label);

$description_label=(trim($ssf_wp_vars['description_label'])!="")? ssfParseToXML($ssf_wp_vars['description_label']) : "Description";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $description_label, $description_label );
$description_label = apply_filters( 'wpml_translate_single_string', $description_label, 'superstorefinder-wp', $description_label);

$website_label=(trim($ssf_wp_vars['website_label'])!="")? ssfParseToXML($ssf_wp_vars['website_label']) : "Website";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $website_label, $website_label );
$website_label = apply_filters( 'wpml_translate_single_string', $website_label, 'superstorefinder-wp', $website_label);

$exturl_label=(trim($ssf_wp_vars['exturl_label'])!="")? ssfParseToXML($ssf_wp_vars['exturl_label']) : "External URL";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $exturl_label, $exturl_label );
$exturl_label = apply_filters( 'wpml_translate_single_string', $exturl_label, 'superstorefinder-wp', $exturl_label);
$exturl_link=(trim($ssf_wp_vars['exturl_link'])!="")? ssfParseToXML($ssf_wp_vars['exturl_link']) : "true";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $exturl_link, $exturl_link );
$exturl_link = apply_filters( 'wpml_translate_single_string', $exturl_link, 'superstorefinder-wp', $exturl_link);

$hours_label=(trim($ssf_wp_vars['hours_label'])!="")? ssfParseToXML($ssf_wp_vars['hours_label']) : "Operating Hours";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $hours_label, $hours_label );
$hours_label = apply_filters( 'wpml_translate_single_string', $hours_label, 'superstorefinder-wp', $hours_label);

$phone_label=(trim($ssf_wp_vars['phone_label'])!="")? ssfParseToXML($ssf_wp_vars['phone_label']) : "Telephone";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $phone_label, $phone_label );
$phone_label = apply_filters( 'wpml_translate_single_string', $phone_label, 'superstorefinder-wp', $phone_label);

$fax_label=(trim($ssf_wp_vars['fax_label'])!="")? ssfParseToXML($ssf_wp_vars['fax_label']) : "Fax";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $fax_label, $fax_label );
$fax_label = apply_filters( 'wpml_translate_single_string', $fax_label, 'superstorefinder-wp', $fax_label);

$email_label=(trim($ssf_wp_vars['email_label'])!="")? ssfParseToXML($ssf_wp_vars['email_label']) : "Email";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $email_label, $email_label );
$email_label = apply_filters( 'wpml_translate_single_string', $email_label, 'superstorefinder-wp', $email_label);
$contact_us_label=(trim($ssf_wp_vars['ssf_contact_us'])!="")? ssfParseToXML($ssf_wp_vars['ssf_contact_us']) : "Contact Us"; 
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $contact_us_label, $contact_us_label );
$contact_us_label = apply_filters( 'wpml_translate_single_string', $contact_us_label, 'superstorefinder-wp', $contact_us_label);
$ssf_close_btn=(trim($ssf_wp_vars['ssf_close_btn'])!="")? ssfParseToXML($ssf_wp_vars['ssf_close_btn']) : "Close"; 
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $ssf_close_btn, $ssf_close_btn );
$ssf_close_btn = apply_filters( 'wpml_translate_single_string', $ssf_close_btn, 'superstorefinder-wp', $ssf_close_btn);

$direction_label=(trim($ssf_wp_vars['direction_label'])!="")? ssfParseToXML($ssf_wp_vars['direction_label']) : "Get Directions";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $direction_label, $direction_label );
$direction_label = apply_filters( 'wpml_translate_single_string', $direction_label, 'superstorefinder-wp', $direction_label);

$streetview_label=(trim($ssf_wp_vars['streetview_label'])!="")? ssfParseToXML($ssf_wp_vars['streetview_label']) : "Street View";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $streetview_label, $streetview_label );
$streetview_label = apply_filters( 'wpml_translate_single_string', $streetview_label, 'superstorefinder-wp', $streetview_label);
$ssf_next_label=(trim($ssf_wp_vars['ssf_next_label'])!="")? ssfParseToXML($ssf_wp_vars['ssf_next_label']) : "Next"; 
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $ssf_next_label, $ssf_next_label );
$ssf_next_label = apply_filters( 'wpml_translate_single_string', $ssf_next_label, 'superstorefinder-wp', $ssf_next_label);
$ssf_prev_label=(trim($ssf_wp_vars['ssf_prev_label'])!="")? ssfParseToXML($ssf_wp_vars['ssf_prev_label']) : "Prev"; 
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $ssf_prev_label, $ssf_prev_label );
$ssf_prev_label = apply_filters( 'wpml_translate_single_string', $ssf_prev_label, 'superstorefinder-wp', $ssf_prev_label);

// notification

$loadingGoogleMap=(trim($ssf_wp_vars['loadingGoogleMap'])!="")? ssfParseToXML($ssf_wp_vars['loadingGoogleMap']) : "Loading Google Maps...";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $loadingGoogleMap, $loadingGoogleMap );
$loadingGoogleMap = apply_filters( 'wpml_translate_single_string', $loadingGoogleMap, 'superstorefinder-wp', $loadingGoogleMap);

$loadingGoogleMapUtilities=(trim($ssf_wp_vars['loadingGoogleMapUtilities'])!="")? ssfParseToXML($ssf_wp_vars['loadingGoogleMapUtilities']) : "Loading Google Map Utilities...";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $loadingGoogleMapUtilities, $loadingGoogleMapUtilities );
$loadingGoogleMapUtilities = apply_filters( 'wpml_translate_single_string', $loadingGoogleMapUtilities, 'superstorefinder-wp', $loadingGoogleMapUtilities);

$startSearch=(trim($ssf_wp_vars['startSearch'])!="")? ssfParseToXML($ssf_wp_vars['startSearch']) : "Load complete. Start your search!";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $startSearch, $startSearch );
$startSearch = apply_filters( 'wpml_translate_single_string', $startSearch, 'superstorefinder-wp', $startSearch);

$gettingUserLocation=(trim($ssf_wp_vars['gettingUserLocation'])!="")? ssfParseToXML($ssf_wp_vars['gettingUserLocation']) : "Getting your current location...";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $gettingUserLocation, $gettingUserLocation );
$gettingUserLocation = apply_filters( 'wpml_translate_single_string', $gettingUserLocation, 'superstorefinder-wp', $gettingUserLocation);

$lookingForNearbyStores=(trim($ssf_wp_vars['lookingForNearbyStores'])!="")? ssfParseToXML($ssf_wp_vars['lookingForNearbyStores']) : "Looking for nearby stores...";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $lookingForNearbyStores, $lookingForNearbyStores );
$lookingForNearbyStores = apply_filters( 'wpml_translate_single_string', $lookingForNearbyStores, 'superstorefinder-wp', $lookingForNearbyStores);

$lookingForStoresNearLocation=(trim($ssf_wp_vars['lookingForStoresNearLocation'])!="")? ssfParseToXML($ssf_wp_vars['lookingForStoresNearLocation']) : "Looking for nearby stores...";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $lookingForStoresNearLocation, $lookingForStoresNearLocation );
$lookingForStoresNearLocation = apply_filters( 'wpml_translate_single_string', $lookingForStoresNearLocation, 'superstorefinder-wp', $lookingForStoresNearLocation);

$filteringStores=(trim($ssf_wp_vars['filteringStores'])!="")? ssfParseToXML($ssf_wp_vars['filteringStores']) : "Filtering for nearby stores...";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $filteringStores, $filteringStores );
$filteringStores = apply_filters( 'wpml_translate_single_string', $filteringStores, 'superstorefinder-wp', $filteringStores);

$cantLocateUser=(trim($ssf_wp_vars['cantLocateUser'])!="")? ssfParseToXML($ssf_wp_vars['cantLocateUser']) : "We are having trouble locating you. Try using our search and filter functions instead.";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $cantLocateUser, $cantLocateUser );
$cantLocateUser = apply_filters( 'wpml_translate_single_string', $cantLocateUser, 'superstorefinder-wp', $cantLocateUser);

$notAllowedUserLocation=(trim($ssf_wp_vars['notAllowedUserLocation'])!="")? ssfParseToXML($ssf_wp_vars['notAllowedUserLocation']) : "Location service is not enabled.";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $notAllowedUserLocation, $notAllowedUserLocation );
$notAllowedUserLocation = apply_filters( 'wpml_translate_single_string', $notAllowedUserLocation, 'superstorefinder-wp', $notAllowedUserLocation);

$noStoresNearSearchLocation=(trim($ssf_wp_vars['noStoresNearSearchLocation'])!="")? ssfParseToXML($ssf_wp_vars['noStoresNearSearchLocation']) : "No nearby were found. Why not try a different location?";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $noStoresNearSearchLocation, $noStoresNearSearchLocation );
$noStoresNearSearchLocation = apply_filters( 'wpml_translate_single_string', $noStoresNearSearchLocation, 'superstorefinder-wp', $noStoresNearSearchLocation);

$noStoresNearUser=(trim($ssf_wp_vars['noStoresNearUser'])!="")? ssfParseToXML($ssf_wp_vars['noStoresNearUser']) : "No nearby were found. Why not try using our search?";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $noStoresNearUser, $noStoresNearUser );
$noStoresNearUser = apply_filters( 'wpml_translate_single_string', $noStoresNearUser, 'superstorefinder-wp', $noStoresNearUser);

$noStoresFromFilter=(trim($ssf_wp_vars['noStoresFromFilter'])!="")? ssfParseToXML($ssf_wp_vars['noStoresFromFilter']) : "No nearby were found. Try using different filter options instead.";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $noStoresFromFilter, $noStoresFromFilter );
$noStoresFromFilter = apply_filters( 'wpml_translate_single_string', $noStoresFromFilter, 'superstorefinder-wp', $noStoresFromFilter);

$cantGetStoresInfo=(trim($ssf_wp_vars['cantGetStoresInfo'])!="")? ssfParseToXML($ssf_wp_vars['cantGetStoresInfo']) : "It seems that we are unable to load stores information. Please try again later.";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $cantGetStoresInfo, $cantGetStoresInfo );
$cantGetStoresInfo = apply_filters( 'wpml_translate_single_string', $cantGetStoresInfo, 'superstorefinder-wp', $cantGetStoresInfo);

$ssfContinueAnyway=(trim($ssf_wp_vars['ssfContinueAnyway'])!="")? ssfParseToXML($ssf_wp_vars['ssfContinueAnyway']) : "Continue anyway";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $ssfContinueAnyway, $ssfContinueAnyway );
$ssfContinueAnyway = apply_filters( 'wpml_translate_single_string', $ssfContinueAnyway, 'superstorefinder-wp', $ssfContinueAnyway);
$ssfShareLocation=(trim($ssf_wp_vars['ssfShareLocation'])!="")? ssfParseToXML($ssf_wp_vars['ssfShareLocation']) : "Share my location";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $ssfShareLocation, $ssfShareLocation );
$ssfShareLocation = apply_filters( 'wpml_translate_single_string', $ssfShareLocation, 'superstorefinder-wp', $ssfShareLocation);
$noStoresFound=(trim($ssf_wp_vars['noStoresFound'])!="")? ssfParseToXML($ssf_wp_vars['noStoresFound']) : "No nearby stores found.";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $noStoresFound, $noStoresFound );
$noStoresFound = apply_filters( 'wpml_translate_single_string', $noStoresFound, 'superstorefinder-wp', $noStoresFound);

$storesFound=(trim($ssf_wp_vars['storesFound'])!="")? ssfParseToXML($ssf_wp_vars['storesFound']) : "Nearby stores found.";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $storesFound, $storesFound );
$storesFound = apply_filters( 'wpml_translate_single_string', $storesFound, 'superstorefinder-wp', $storesFound);

$generalError=(trim($ssf_wp_vars['generalError'])!="")? ssfParseToXML($ssf_wp_vars['generalError']) : "We have encountered an error.";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $generalError, $generalError );
$generalError = apply_filters( 'wpml_translate_single_string', $generalError, 'superstorefinder-wp', $generalError);

//regions
$region_show=(trim($ssf_wp_vars['region_show'])!="")? ssfParseToXML($ssf_wp_vars['region_show']) : "1";
$category_show=(trim($ssf_wp_vars['category_show'])!="")? ssfParseToXML($ssf_wp_vars['category_show']) : "1";
$ssf_radius_show=(trim($ssf_wp_vars['ssf_radius_show'])!="")? ssfParseToXML($ssf_wp_vars['ssf_radius_show']) : "1";
$show_all_show=(trim($ssf_wp_vars['show_all_show'])!="")? ssfParseToXML($ssf_wp_vars['show_all_show']) : "1";
$show_result_list=(trim($ssf_wp_vars['show_result_list'])!="")? ssfParseToXML($ssf_wp_vars['show_result_list']) : "1";
$StreetView_set=(trim($ssf_wp_vars['StreetView_set'])!="")? ssfParseToXML($ssf_wp_vars['StreetView_set']) : "yes";
$GetDirTop_set=(trim($ssf_wp_vars['GetDirTop_set'])!="")? ssfParseToXML($ssf_wp_vars['GetDirTop_set']) : "yes";
$GetDirBottom_set=(trim($ssf_wp_vars['GetDirBottom_set'])!="")? ssfParseToXML($ssf_wp_vars['GetDirBottom_set']) : "yes";

$show_search_bar=(trim($ssf_wp_vars['show_search_bar'])!="")? ssfParseToXML($ssf_wp_vars['show_search_bar']) : "1";
$show_image_list=(trim($ssf_wp_vars['show_image_list'])!="")? ssfParseToXML($ssf_wp_vars['show_image_list']) : "yes";
$pagination_setting=(trim($ssf_wp_vars['pagination_setting'])!="")? ssfParseToXML($ssf_wp_vars['pagination_setting']) : "0";
//***.**Contact Us Label**.***//
$contact_us_store=(trim($ssf_wp_vars['contact_us_store'])!="")? ssfParseToXML($ssf_wp_vars['contact_us_store']) : "Contact Store";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $contact_us_store, $contact_us_store );
$contact_us_store = apply_filters( 'wpml_translate_single_string', $contact_us_store, 'superstorefinder-wp', $contact_us_store);
$contact_us_name=(trim($ssf_wp_vars['contact_us_name'])!="")? ssfParseToXML($ssf_wp_vars['contact_us_name']) : "Name";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $contact_us_name, $contact_us_name );
$contact_us_name = apply_filters( 'wpml_translate_single_string', $contact_us_name, 'superstorefinder-wp', $contact_us_name);
$contact_plc_name=(trim($ssf_wp_vars['contact_plc_name'])!="")? ssfParseToXML($ssf_wp_vars['contact_plc_name']) : "Please enter your name";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $contact_plc_name, $contact_plc_name );
$contact_plc_name = apply_filters( 'wpml_translate_single_string', $contact_plc_name, 'superstorefinder-wp', $contact_plc_name);
$contact_us_email=(trim($ssf_wp_vars['contact_us_email'])!="")? ssfParseToXML($ssf_wp_vars['contact_us_email']) : "Email";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $contact_us_email, $contact_us_email );
$contact_us_email = apply_filters( 'wpml_translate_single_string', $contact_us_email, 'superstorefinder-wp', $contact_us_email);
$contact_plc_email=(trim($ssf_wp_vars['contact_plc_email'])!="")? ssfParseToXML($ssf_wp_vars['contact_plc_email']) : "Please enter your email address";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $contact_plc_email, $contact_plc_email );
$contact_plc_email = apply_filters( 'wpml_translate_single_string', $contact_plc_email, 'superstorefinder-wp', $contact_plc_email);
$rating_comment=(trim($ssf_wp_vars['rating_comment_label'])!="")? ssfParseToXML($ssf_wp_vars['rating_comment_label']) : "Comments";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $rating_comment, $rating_comment );
$rating_comment = apply_filters( 'wpml_translate_single_string', $rating_comment, 'superstorefinder-wp', $rating_comment);
$contact_us_phone=(trim($ssf_wp_vars['contact_us_phone'])!="")? ssfParseToXML($ssf_wp_vars['contact_us_phone']) : "Telephone";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $contact_us_phone, $contact_us_phone );
$contact_us_phone = apply_filters( 'wpml_translate_single_string', $contact_us_phone, 'superstorefinder-wp', $contact_us_phone);
$contact_plc_phone=(trim($ssf_wp_vars['contact_plc_phone'])!="")? ssfParseToXML($ssf_wp_vars['contact_plc_phone']) : "Please enter your number";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $contact_plc_phone, $contact_plc_phone );
$contact_plc_phone = apply_filters( 'wpml_translate_single_string', $contact_plc_phone, 'superstorefinder-wp', $contact_plc_phone);
$contact_us_msg=(trim($ssf_wp_vars['contact_us_msg'])!="")? ssfParseToXML($ssf_wp_vars['contact_us_msg']) : "Message";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $contact_us_msg, $contact_us_msg );
$contact_us_msg = apply_filters( 'wpml_translate_single_string', $contact_us_msg, 'superstorefinder-wp', $contact_us_msg);
$contact_plc_msg=(trim($ssf_wp_vars['contact_plc_msg'])!="")? ssfParseToXML($ssf_wp_vars['contact_plc_msg']) : "Include all the details you can";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $contact_plc_msg, $contact_plc_msg );
$contact_plc_msg = apply_filters( 'wpml_translate_single_string', $contact_plc_msg, 'superstorefinder-wp', $contact_plc_msg);
$contact_us_btn=(trim($ssf_wp_vars['contact_us_btn'])!="")? ssfParseToXML($ssf_wp_vars['contact_us_btn']) : "Send Message";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $contact_us_btn, $contact_us_btn );
$contact_us_btn = apply_filters( 'wpml_translate_single_string', $contact_us_btn, 'superstorefinder-wp', $contact_us_btn);
$ssf_msg_sucess=(trim($ssf_wp_vars['msg_sucess'])!="")? ssfParseToXML($ssf_wp_vars['msg_sucess']) : "Message sent successfully";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $ssf_msg_sucess, $ssf_msg_sucess );
$ssf_msg_sucess = apply_filters( 'wpml_translate_single_string', $ssf_msg_sucess, 'superstorefinder-wp', $ssf_msg_sucess);
$ssf_msg_fail=(trim($ssf_wp_vars['msg_fail'])!="")? ssfParseToXML($ssf_wp_vars['msg_fail']) : "Message delivery failed";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $ssf_msg_fail, $ssf_msg_fail );
$ssf_msg_fail = apply_filters( 'wpml_translate_single_string', $ssf_msg_fail, 'superstorefinder-wp', $ssf_msg_fail);
$review_label=(trim($ssf_wp_vars['review_label'])!="")? ssfParseToXML($ssf_wp_vars['review_label']) : "Reviews";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $review_label, $review_label );
$review_label = apply_filters( 'wpml_translate_single_string', $review_label, 'superstorefinder-wp', $review_label);
$google_api_key=(trim($ssf_wp_vars['google_api_key'])!="")? ssfParseToXML($ssf_wp_vars['google_api_key']) : "";
$scroll_setting=(trim($ssf_wp_vars['scroll_setting'])!="")? ssfParseToXML($ssf_wp_vars['scroll_setting']) : "0";

$search_bar_position=(trim($ssf_wp_vars['search_bar_position'])!="")? ssfParseToXML($ssf_wp_vars['search_bar_position']) : "false";

$state_label_show=(trim($ssf_wp_vars['state_label_show'])!="")? ssfParseToXML($ssf_wp_vars['state_label_show']) : "false";
$zip_label_show=(trim($ssf_wp_vars['zip_label_show'])!="")? ssfParseToXML($ssf_wp_vars['zip_label_show']) : "false";
$notification_bar=(trim($ssf_wp_vars['notification_bar'])!="")? ssfParseToXML($ssf_wp_vars['notification_bar']) : "false";
$tel_fax_link=(trim($ssf_wp_vars['tel_fax_link'])!="")? ssfParseToXML($ssf_wp_vars['tel_fax_link']) : "false";

$rating_select_validation=(trim($ssf_wp_vars['rating_select_validation'])!="")? ssfParseToXML($ssf_wp_vars['rating_select_validation']) : "Reviews";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $rating_select_validation, $rating_select_validation );
$rating_select_validation = apply_filters( 'wpml_translate_single_string', $rating_select_validation, 'superstorefinder-wp', $rating_select_validation);
	
$ssf_wp_state_label=(trim($ssf_wp_vars['state_label'])!="")? ssfParseToXML($ssf_wp_vars['state_label']) : "State";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $ssf_wp_state_label, $ssf_wp_state_label );
$ssf_wp_state_label = apply_filters( 'wpml_translate_single_string', $ssf_wp_state_label, 'superstorefinder-wp', $ssf_wp_state_label);

$ssf_wp_zip_label=(trim($ssf_wp_vars['zip_label'])!="")? ssfParseToXML($ssf_wp_vars['zip_label']) : "zip";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $ssf_wp_zip_label, $ssf_wp_zip_label );
$ssf_wp_zip_label = apply_filters( 'wpml_translate_single_string', $ssf_wp_zip_label, 'superstorefinder-wp', $ssf_wp_zip_label);

$review_and_ratings=(trim($ssf_wp_vars['review_and_ratings'])!="")? ssfParseToXML($ssf_wp_vars['review_and_ratings']) : "Reviews & Ratings";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $review_and_ratings, $review_and_ratings );
$review_and_ratings = apply_filters( 'wpml_translate_single_string', $review_and_ratings, 'superstorefinder-wp', $review_and_ratings);

$review_submit_button=(trim($ssf_wp_vars['review_submit_button'])!="")? ssfParseToXML($ssf_wp_vars['review_submit_button']) : "Submit Your Review";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $review_submit_button, $review_submit_button );
$review_submit_button = apply_filters( 'wpml_translate_single_string', $review_submit_button, 'superstorefinder-wp', $review_submit_button);

$login_button_msg=(trim($ssf_wp_vars['login_button_msg'])!="")? ssfParseToXML($ssf_wp_vars['login_button_msg']) : "Please sign in / sign up to leave rating and review.";
do_action( 'wpml_register_single_string', 'superstorefinder-wp', $login_button_msg, $login_button_msg );
$login_button_msg = apply_filters( 'wpml_translate_single_string', $login_button_msg, 'superstorefinder-wp', $login_button_msg);

$marker_label_color=(trim($ssf_wp_vars['marker_label_color'])!="")? ssfParseToXML($ssf_wp_vars['marker_label_color']) : "";

$ssf_mobile_gesture=(trim($ssf_wp_vars['mobile_gesture'])!="")? ssfParseToXML($ssf_wp_vars['mobile_gesture']) : "false";
$ssf_font_familly=(trim($ssf_wp_vars['ssf_font_familly'])!="")? ssfParseToXML($ssf_wp_vars['ssf_font_familly']) : "";
//distance addon value

$distanceList='';
$distanceInInfo='';
$distanceOnMobile='';
$matrix='';
$distancePanel=false;
if($ssf_radius_show=="1"){
$distanceAddon=$wpdb->get_results("SELECT * FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_wp_addon_name='ssf-distance-addon-wp' AND ssf_wp_addon_status='on' ", ARRAY_A);
	if(!empty($distanceAddon)){
	$distanceList.="<div class='filter__row' id='filter__distance'>
				  <div class='filter__toggler arrow-toggler'>$by_distance_label</div>
				  <div class='filter__toggler-contents togglerify-slider'>
				  <ul class='small-block-grid-2 space-top'>";
			$fetcharr=json_decode($distanceAddon[0]['ssf_wp_addon_token']);
			if(!empty($distanceAddon[0]['ssf_wp_addon_token'])){
			if(!empty($fetcharr->matrix)){
			foreach ($fetcharr->matrix as $row){
					$matrix=strtolower($row);
				}
			}		
			$checkedTrue=true;
			foreach ($fetcharr->distance as $row){
				foreach ($row as $key=>$value){
				$label=$value;
				if(trim($matrix)=="km"){
				  $value=$value;
				}else{
				 $value=$value*1.60934;
				}
				if($checkedTrue==true){
				$checked='checked';
				$ssf_distance_limit=$value;
				$checkedTrue=false;
				}else{
				$checked='';
				}
				$distanceList.="<li><label class='label--vertical-align  ssflabel'><input id='storesdistance' class='js-inputify' name='storesdistance' type='radio' value='".$value."' $checked />".$label ." ".$matrix."</label></li>";
				}		
			}	
	}		
	$distanceList.="</ul></div></div>";
	$distanceInInfo="<div class='store-distance' id='info-distance' style='padding:0 15px'></div>";
	$distanceOnMobile="<div class='infobox__row store-distance'> Placeholder distance.</div>";
	$distancePanel=true;
	}
}
$matrix=(trim($matrix)!="")? $matrix : "km";		
print "<script type=\"text/javascript\">\n//<![CDATA[\n";
print  
"var ssf_wp_base='".SSF_WP_BASE."';
var ssf_matrix='".$matrix."';
var default_distance = '';
var zoomhere_zoom = '';
var geo_settings = '';
var ssf_wp_map_code; ";
if($ssf_wp_map_code!=""){
 print "var ssf_wp_map_code=$ssf_wp_map_code;";
}

if($google_map_region=='World'){
$google_map_region='';
$defulat__map_region='true';
}else{
$defulat__map_region='false';
}

if(empty($ssf_distance_limit)){
$ssf_distance_limit=(trim($ssf_wp_vars['ssf_distance_km'])!="")? ssfParseToXML($ssf_wp_vars['ssf_distance_km']) : "30";
}
$PanBYMap='true';
if($ssf_layout!='bottom'){
	$PanBYMap='false';
	
}

 if($search_bar_position=='true' && $ssf_layout=='bottom'){
	 $scroll_to_top=200;
 }else{
	 $scroll_to_top=100;
 }

print "\n

var style_map_color = '$style_map_color';
var ssf_wp_uploads_base='".SSF_WP_UPLOADS_BASE."';
var ssf_wp_addons_base=ssf_wp_uploads_base+'".str_replace(SSF_WP_UPLOADS_BASE, '', SSF_WP_ADDONS_BASE)."';
var ssf_wp_includes_base=ssf_wp_base+'".str_replace(SSF_WP_BASE, '', SSF_WP_INCLUDES_BASE)."';
var ssf_wp_zoom_level=''; 
var map_mouse_scroll = '$map_mouse_scroll';
var default_location = '$default_location';
var ssf_default_category='';
var ssf_wp_map_settings='$mt'; 
var zoom_level='$zoom_level';
var init_zoom=$init_zoom; 
var labeled_marker='$labeled_marker'; 
var custom_marker='$custom_marker'; 
var custom_marker_active='$custom_marker_active'; 
var ssf_wp_stores_near_you='$stores_near_you'; 
var ssf_wp_search_label='$search_label'; 
var ssf_wp_ext_url_label='$external_url_label';
var ssf_distance_limit=$ssf_distance_limit;
var ssf_wp_outlet_label='$outlet_label'; 
var ssf_wp_of_label='$of_label'; 
var ssf_wp_clear_all_label='$clear_all_label'; 
var ssf_wp_show_all_label='$show_all_label'; 
var ssf_wp_by_region_label='$by_region_label'; 
var ssf_wp_by_category='$by_category'; 
var ssf_wp_select_label='$select_label'; 
var ssf_wp_cancel_label='$cancel_label'; 
var ssf_wp_filter_label='$filter_label'; 
var ssf_wp_short_search_label='$short_search_label'; 
var ssf_wp_website_label='$website_label'; 
var ssf_wp_hours_label='$hours_label';
var ssf_wp_phone_label='$phone_label';
var ssf_wp_exturl_label='$exturl_label';
var ssf_wp_exturl_link='$exturl_link';
var ssf_wp_fax_label='$fax_label';
var ssf_wp_email_label='$email_label';
var ssf_wp_direction_label='$direction_label';
var ssf_wp_streetview_label='$streetview_label';
var ssf_wp_loadingGoogleMap='$loadingGoogleMap';
var ssf_wp_loadingGoogleMapUtilities='$loadingGoogleMapUtilities';
var ssf_wp_startSearch='$startSearch';
var ssf_wp_gettingUserLocation='$gettingUserLocation';
var ssf_wp_lookingForNearbyStores='$lookingForNearbyStores';
var ssf_wp_lookingForStoresNearLocation='$lookingForStoresNearLocation';
var ssf_wp_filteringStores='$filteringStores';
var ssf_wp_cantLocateUser='$cantLocateUser';
var ssf_wp_notAllowedUserLocation='$notAllowedUserLocation';
var ssf_wp_noStoresNearSearchLocation='$noStoresNearSearchLocation';
var ssf_wp_noStoresNearUser='$noStoresNearUser';
var ssf_wp_noStoresFromFilter='$noStoresFromFilter';
var ssf_wp_cantGetStoresInfo='$cantGetStoresInfo';
var ssf_noStoresFound='$noStoresFound';
var ssf_storesFound='$storesFound';
var ssf_generalError='$generalError';
var ssf_msg_sucess='$ssf_msg_sucess';
var ssf_msg_fail='$ssf_msg_fail';
var ssf_cont_us_name='$contact_us_name';
var ssf_cont_us_email='$contact_us_email';
var ssf_cont_us_msg='$contact_us_msg';
var ssf_show_image_list='$show_image_list';
var ssf_pagination='$pagination_setting';
var ssfContinueAnyway='$ssfContinueAnyway';
var ssfShareLocation='$ssfShareLocation';
var ssf_next_label='$ssf_next_label';
var ssf_prev_label='$ssf_prev_label';
var scroll_to_top=$scroll_to_top;
var google_api_key='$google_api_key';
var review_label='$review_label';
var contact_plc_name='$contact_plc_name';
var contact_plc_email='$contact_plc_email';
var contact_plc_msg='$contact_plc_msg';
var rating_select_validation='$rating_select_validation';
var scroll_setting='$scroll_setting';
var ssf_m_rgn='$google_map_region';
var ssf_m_lang='$google_map_language';
var ssf_tel_fax_link='$tel_fax_link';
var ssf_defualt_region='$defulat__map_region';
var ssf_map_position='$ssf_map_position';
var ssf_mobile_gesture='$ssf_mobile_gesture';
var ssf_pan_by_map='$PanBYMap';
var wmpl_ssf_lang='$wpml_current_language';
\n";
	print "//]]>\n</script>\n";

// WPML Translation
$cancel_filter= apply_filters('widget_title', $cancel_label );
$search_filter = apply_filters('widget_title', $search_label);
$external_url_filter = apply_filters('widget_title', $external_url_label);
$outlet_filter = apply_filters('widget_title', $outlet_label);
$of_filter = apply_filters('widget_title', $of_label);
$clear_all_filter = apply_filters('widget_title', $clear_all_label);
$show_all_filter = apply_filters('widget_title', $show_all_label);
$by_region_filter = apply_filters('widget_title', $by_region_label);
$by_category_filter = apply_filters('widget_title', $by_category);
$filter_filter = apply_filters('widget_title', $filter_label);
$short_search_filter = apply_filters('widget_title', $short_search_label);
$website_filter = apply_filters('widget_title', $website_label);
$hours_filter = apply_filters('widget_title', $hours_label);
$phone_filter = apply_filters('widget_title', $phone_label);
$exturl_filter = apply_filters('widget_title', $exturl_label);
$fax_filter = apply_filters('widget_title', $fax_label);
$email_filter = apply_filters('widget_title', $email_label);
$direction_filter = apply_filters('widget_title', $direction_label);
$streetview_filter = apply_filters('widget_title', $streetview_label);
$loadingGoogleMap_filter = apply_filters('widget_title', $loadingGoogleMap);
$loadingGoogleMapUtilities_filter = apply_filters('widget_title', $loadingGoogleMapUtilities);
$startSearch_filter = apply_filters('widget_title', $startSearch);
$gettingUserLocation_filter = apply_filters('widget_title', $gettingUserLocation);
$lookingForNearbyStores_filter = apply_filters('widget_title', $lookingForNearbyStores);
$lookingForStoresNearLocation_filter = apply_filters('widget_title', $lookingForStoresNearLocation);
$filteringStores_filter = apply_filters('widget_title', $filteringStores);
$cantLocateUser_filter = apply_filters('widget_title', $cantLocateUser);
$notAllowedUserLocation_filter = apply_filters('widget_title', $notAllowedUserLocation);
$noStoresNearSearchLocation_filter = apply_filters('widget_title', $noStoresNearSearchLocation);
$noStoresNearUser_filter = apply_filters('widget_title', $noStoresNearUser);
$noStoresFromFilter_filter = apply_filters('widget_title', $noStoresFromFilter);
$cantGetStoresInfo_filter = apply_filters('widget_title', $cantGetStoresInfo);
$noStoresFound_filter = apply_filters('widget_title', $noStoresFound);
$storesFound_filter = apply_filters('widget_title', $storesFound);
$generalError_filter = apply_filters('widget_title', $generalError);
$ssf_msg_sucess_filter = apply_filters('widget_title', $ssf_msg_sucess);
$ssf_msg_fail_filter = apply_filters('widget_title', $ssf_msg_fail);
$contact_us_name_filter = apply_filters('widget_title', $contact_us_name);
$contact_us_email_filter = apply_filters('widget_title', $contact_us_email);
$contact_us_msg_filter = apply_filters('widget_title', $contact_us_msg);
$ssfContinueAnyway_filter = apply_filters('widget_title', $ssfContinueAnyway);
$ssfShareLocation_filter = apply_filters('widget_title', $ssfShareLocation);
$ssf_next_label_filter = apply_filters('widget_title', $ssf_next_label);
$ssf_prev_label_filter = apply_filters('widget_title', $ssf_prev_label);
$ssf_wp_state_label_filter = apply_filters('widget_title', $ssf_wp_state_label);
$ssf_wp_zip_label_filter = apply_filters('widget_title', $ssf_wp_zip_label);
$review_label_filter = apply_filters('widget_title', $review_label);
$rating_select_validation_filter = apply_filters('widget_title', $rating_select_validation);
$review_and_ratings = apply_filters('widget_title', $review_and_ratings);
$review_submit_button = apply_filters('widget_title', $review_submit_button);


$translations = array(
'ssf_wp_stores_near_you' => $cancel_filter,
'ssf_wp_search_label' => $search_label,
'ssf_wp_ext_url_label' => $external_url_filter,
'ssf_wp_stores_near_you' => $external_url_filter,
'ssf_wp_outlet_label' => $outlet_filter,
'ssf_wp_of_label' => $of_filter,
'ssf_wp_clear_all_label' => $clear_all_filter,
'ssf_wp_show_all_label' => $show_all_filter,
'ssf_wp_by_region_label' => $by_region_filter,
'ssf_wp_by_category' => $by_category_filter,
'ssf_wp_select_label' => $select_label,
'ssf_wp_cancel_label' => $cancel_filter,
'ssf_wp_filter_label' => $filter_filter,
'ssf_wp_short_search_label' => $short_search_filter,
'ssf_wp_website_label' => $website_filter,
'ssf_wp_hours_label' => $hours_filter,
'ssf_wp_phone_label' => $phone_filter,
'ssf_wp_exturl_label' => $exturl_filter,
'ssf_wp_fax_label' => $fax_filter,
'ssf_wp_email_label' => $email_filter,
'ssf_wp_direction_label' => $direction_filter,
'ssf_wp_streetview_label' => $streetview_filter,
'ssf_wp_loadingGoogleMap' => $loadingGoogleMap_filter,
'ssf_wp_loadingGoogleMapUtilities' => $loadingGoogleMapUtilities_filter,
'ssf_wp_startSearch' => $startSearch_filter,
'ssf_wp_gettingUserLocation' => $gettingUserLocation_filter,
'ssf_wp_lookingForNearbyStores' => $lookingForNearbyStores_filter,
'ssf_wp_lookingForStoresNearLocation' => $lookingForStoresNearLocation_filter,
'ssf_wp_filteringStores' => $filteringStores_filter,
'ssf_wp_cantLocateUser' => $cantLocateUser_filter,
'ssf_wp_notAllowedUserLocation' => $notAllowedUserLocation_filter,
'ssf_wp_noStoresNearSearchLocation' => $noStoresNearSearchLocation_filter,
'ssf_wp_noStoresNearUser' => $noStoresNearUser_filter,
'ssf_wp_noStoresFromFilter' => $noStoresFromFilter_filter,
'ssf_wp_cantGetStoresInfo' => $cantGetStoresInfo_filter,
'ssf_noStoresFound' => $noStoresFound_filter,
'ssf_storesFound' => $storesFound_filter,
'ssf_generalError' => $generalError_filter,
'ssf_msg_sucess' => $ssf_msg_sucess_filter,
'ssf_msg_fail' => $ssf_msg_fail_filter,
'ssf_cont_us_name' => $contact_us_name_filter,
'ssf_cont_us_email' => $contact_us_email_filter,
'ssf_cont_us_msg' => $contact_us_msg_filter,
'ssfContinueAnyway' => $ssfContinueAnyway_filter,
'ssfShareLocation' => $ssfShareLocation_filter,
'ssf_next_label' => $ssf_next_label_filter,
'ssf_prev_label' => $ssf_prev_label_filter,
'ssf_wp_state_label' => $ssf_wp_state_label_filter,
'ssf_wp_zip_label' => $ssf_wp_zip_label_filter,
'review_label' => $review_label_filter,
'rating_select_validation' => $rating_select_validation_filter,
'review_and_ratings' => $review_and_ratings,
'review_submit_button' => $review_submit_button);


	
	if (function_exists("do_ssf_wp_hook")){do_ssf_wp_hook('ssf_wp_addon_head_scripts'); }

	if (function_exists("do_ssf_wp_hook")){ 

		print "<script>\n//<![CDATA[\n";

		ssf_wp_js_hooks();

		print "\n//]]>\n</script>\n";

	}

	print "<style>";

	// fixing street view in firefox issue

	print ".ssf-main-content img {

    max-width: none !important; }";

if($notification_bar=="true"){ 
    print ".store-locator__map-status.is-shown.is-transitionable{
	   min-height:50px;
	}";
}

if($notification_bar=="false"){ 
	print ".store-locator__map-status__inner{
	display: none !important;
	}";
}

if($marker_label_color!=""){
	print ".store-locator__map-pin { color: $marker_label_color !important; } ";
}

if($info_window_buttons!=""){
	print "#storeLocatorInfobox .infobox__cta,
			#storeLocatorInfobox .infobox__stv,
			#storeLocatorInfobox .infobox__comment,
			#mobileStoreLocatorInfobox .infobox__cta,
			#mobileStoreLocatorInfobox .infobox__stv,
			#mobileStoreLocatorInfobox .infobox__comment
			{ 
			    color: $info_window_buttons !important; 
			} ";
}

if($infowindowlink!=""){
	print "#storeLocatorInfobox .store-website a,
			#storeLocatorInfobox .store-email a,
			#storeLocatorInfobox .store-tel a,
			#storeLocatorInfobox .store-fax a,
			.infobox__comment{ 
			    color: $infowindowlink !important; 
			} ";
}



if($style_top_bar_bg!=""){ 

print ".ssf-panel {

  background-color: $style_top_bar_bg !important;

  border: 0 solid #ddd;

}

.store-locator__filter-toggler-cell {

background-color: $style_top_bar_bg !important;

}";

 } 
 
 if($search_bar_position=='true' && $ssf_layout=='bottom'){
 
    print "@media only screen and (min-width: 64.8em) {
    #ssf_adress_input_box,
    #filter_left_panel{
       position: relative;
        top: -170px;
        height:auto !important;
    }
	#store-locator-section-bg{
     margin-top:170px;
    }
	}";
 }
 
 

if($previousnextbuttonclr!=""){
	print "#page_navigation .pagination-btn{ 
			    color: $previousnextbuttonclr !important; 
			} ";
}

if($previousnextbuttonbg!=""){
	print "#page_navigation .pagination-btn{ 
			    background-color: $previousnextbuttonbg !important; 
			} ";
}

if($storesnearyoucolor!=""){
	print "h2.title.space-bottom-3x{ 
			    color: $storesnearyoucolor !important; 
			} ";
}

if($ssf_font_familly!=""){
print "body .ssf-main-content,
		#applyFilterOptions,
		#applyFilterOptionsCancel,
		.pagination-btn,
		#mainIntMapPopupHolder,
		#mainPopupContat,
		#infobox__body,
		.store-locator-map .store-locator__infobox{
		font-family: '$ssf_font_familly' !important; 
  }";
}

if($ssf_wp_page_bg!=""){ 

 print ".store-locator-section-bg {
    background-color: $ssf_wp_page_bg !important;
}";

}



if($style_list_number_font!=""){ 

 print ".store-locator__infobox .infobox__marker {

 color: $style_list_number_font !important;
 
 }";

}

if($style_list_number_bg!=""){ 

 print ".store-locator__infobox .infobox__marker {

 background-color: $style_list_number_bg !important;

 }";

}

if($style_list_number_circle!=""){ 

 print ".store-locator__infobox .infobox__marker {

 border: 3px solid $style_list_number_circle !important;

 }";

}

if($style_list_number_font_active!=""){ 

 print ".store-locator__infobox.is-active .infobox__marker {

 color: $style_list_number_font_active !important;
 
 

 }";

}

if($style_list_number_bg_active!=""){ 

 print ".store-locator__infobox.is-active .infobox__marker {

 background-color: $style_list_number_bg_active !important;

 }";

}

if($style_list_number_circle_active!=""){ 

 print ".store-locator__infobox.is-active .infobox__marker {

 border: 3px solid $style_list_number_circle_active !important;

 }";

}


	

if($style_results_bg!=""){ 

print ".store-locator__infobox {

background-color: $style_results_bg !important;

}

.store-locator-map .store-locator__infobox:before {
border-right-color: $style_results_bg !important;
}
";

 }

 if($style_results_hl_bg!=""){ 

 print ".store-locator__infobox.is-active {

    background-color: $style_results_hl_bg !important;

}";


 }
 
  if($style_results_hover_bg!=""){ 

 print ".store-locator__infobox:hover,

.store-locator__infobox:hover {

    background-color: $style_results_hover_bg !important;

}";


 }
 

if($style_results_font!=""){ 


print ".store-locator__infobox {

color: $style_results_font !important;

}";

 }


if($style_top_bar_border!=""){ 

print ".filter__row {

border-color: $style_top_bar_border !important;

 }

  .store-locator__filter-toggler-cell {

  border-color: $style_top_bar_border !important;

  }";

 }
 
 
if($filter_font_color!=""){ 
	   print ".filter__row label.ssflabel{
			color: $filter_font_color !important;
	 }";
}

if($style_results_distance_font!=""){ 

print "#filterOptionsClearer {

color: $style_results_distance_font !important;

 }

 #filterShowAll {

color: $style_results_distance_font !important;

 }";

 }

 if($style_top_bar_font!=""){ 

print ".filter-popup {

color: $style_top_bar_font !important;

 }";

 }
 
  if($style_geo_font!=""){ 

print ".store-locator__geolocator {

color: $style_geo_font !important;

 }";

 }
 
 

if($style_contact_button_bg!=""){ 

print ".store-locator__infobox.store-locator__infobox--main {

    background-color: $style_contact_button_bg !important; 

}

.store-locator-map .store-locator__infobox:before {

        content: '';

        border: 12px solid transparent;

        border-left: 0;

        border-right-color: $style_contact_button_bg !important;

 }";

 }

if($style_contact_button_font!=""){

print ".store-locator__infobox.store-locator__infobox--main {

    color: $style_contact_button_font !important; 

}
.icon-plus::before { background-color: $style_contact_button_font !important;  }
.icon-plus::after { background-color: $style_contact_button_font !important;  }
.icon-minus::after { background-color: $style_contact_button_font !important;  }
.ssf-open-hour { color: $style_contact_button_font !important;  }



#ssf-contact-form .ssf_cont_store,.ssf_cont_lab { color: $style_contact_button_font !important; }

";

}

if($style_info_link_font!=""){

print ".btn-super-info {

    color: $style_info_link_font !important; 

}";

}

if($style_info_link_bg!=""){

print ".btn-super-info {

    background-color: $style_info_link_bg !important; 

}";

}




if($style_button_font!=""){

print "#applyFilterOptions, .ssf-button {

    color: $style_button_font !important; 



}";

} else {

print "#applyFilterOptions, .ssf-button {

    color: #fff !important; 



}";

}


if($show_scroll_set!="1"){
	print "#mainBackToTop { display:none; }";
}

if($style_button_bg!=""){

print "#applyFilterOptions, .ssf-button {

    background-color: $style_button_bg !important; 

}
#ssf-contact-form button[type='button']{
	background: $style_button_bg !important; 
}";

} 

if($style_distance_toggle_bg!=""){ 

print "#storeLocator__storeList .infobox__cta {

color: $style_distance_toggle_bg !important;

 }";

 }

 
if($style_results_bg!=""){ 

print ".main-back-to-top {

background-color: $style_results_bg !important;

}";

}

if($StreetView_set=="no"){
	print ".infobox__stv{
			display:none !important;
		}";	
}

if($GetDirTop_set=="no"){
	print ".store-locator__infobox--mobile .infobox__cta, .infobox__body .infobox__cta{
			display:none !important;
		}";	
}

if($GetDirBottom_set=="no"){
	print "#storeLocator__storeList .infobox__cta {
			display:none !important;
		}";	
}

if($show_result_list!='1')
{
	print "#storeLocator__storeListRow{
		display: none !important;
	}
	#page_navigation{
		display: none !important;
	}";
}

if($show_image_list=="no"){ 

 print ".infobox__row--marker, .ssf_image_setting {

 display: none !important;

 }";

} 
else if($show_image_list=="showboth")
{
	 print ".infobox__row--marker{

 display: none !important;

 }";
}

else if($show_image_list=="yes")
{
	 print ".ssf_image_setting{

 display: none !important;

 }";
}

if($show_search_bar!='1')
{
 print "@media only screen and (min-width: 64.063em){ 
		 #ssf_adress_input_box{
			 display: none !important;
		 }
 }";
 $ssf_hide_input_box='display:none !important;';
}
else{
	
	$ssf_hide_input_box='';
}

if($geo_location_icon!='1'){
print ".store-locator__geolocator-cell{
	    	display:none !important;
	}";
}

if($ssf_layout!='bottom'){
	
	
	if($style_top_bar_bg!=""){ 
		print "@media only screen and (min-width: 64.063em) { 
		#super-left-panel {
		  background-color: $style_top_bar_bg !important;
		  border: 0 solid #ddd;
		 }
		}";
 } 
	print ".store-locator__map-status{
	       z-index: 101;
	}";
print "@media only screen and (min-width: 64.063em) {
	#filter_left_panel.large-3{
	   width:100% !important;
	   max-width :100% !important;
	}
	#storeLocator__storeList .medium-4{
	   width:100% !important;
	   max-width :100% !important;
	}
	#storeLocator__topHalf{
		max-width:100% !important;
	}
	#ssf_adress_input_box{
		padding-left:0px !important;
		padding-right:0px !important;
		max-height: 800px;
		overflow: hidden;
	}
	#storeLocator__storeListRow {
		z-index: 10;
		float: left;
		width: 100%;
		padding-bottom:100px;
	}
	#super-left-panel {
		height: 800px;
		overflow-y: visible;
		overflow: hidden;
		overflow: auto;
		padding-left: 0px;
		padding-right: 0px;
	  }
	#storeLocator__storeListRow.pad{
		padding:15px !important;
		margin-left: 0px;
		margin-right: 0px;
	}
	
	.filter-radio.filter-popup.shadowed{
		box-shadow: none !important;
	}
}";


print "#filter_left_panel{
	padding-left: 0px !important; 
    padding-right: 0px !important; 
}
.ssf-main-content {
margin-top: 20px;
}";

print ".infobox__closer {
	top: auto !important;
   }
   .store-locator-top-half.has-searched{
		height: auto !important;
	}
	#store-locator-section-bg{
		padding:0px;
	}
";


print "@media only screen and (max-width: 64em) {
    .store-locator__actions-bar {
        padding: 0 5px
    }
	.store-locator-map-holder{
		position:absolute !important;
		
	}
	#storeLocator__storeListRow {
		    margin-top: 430px;
	}
	.store-locator__map-status{
	       top: 60px !important;
	}
	
}

@media only screen and (max-width: 40em) {
	#storeLocator__storeListRow {
		    margin-top: 250px;
	}
	#storeLocator__storeListRow.pad{
		padding: 0px;
	}
	.infobox__closer {
		z-index:101;
		margin-top:-15px;
	}
}

.title.space-bottom-3x{
	padding-top: 20px;
}
.store-locator__actions-bar .icon--search {
    border-radius: 0;
}
.store-locator-bottom-half .ssf-panel .ssf-column .title {
    display: none;
}
.store-locator-bottom-half #storeLocator__storeListRow .ssf-column {
    width: 100% !important;
}
.store-locator-bottom-half #storeLocator__storeListRow .infobox__row--marker {
    padding: 0;
    float: left;
}
.store-locator-bottom-half #storeLocator__storeListRow .infobox__row--marker .infobox__marker {
    font-size: 24px;
    width: 54px;
    height: 54px;
    line-height: 49px !important
}
@media (max-width: 600px) {
    .store-locator-bottom-half #storeLocator__storeListRow .infobox__row--marker .infobox__marker {
        font-size: 16px;
        width: 45px;
        height: 45px;
        line-height: 43px !important
    }
}
.store-locator-bottom-half #storeLocator__storeListRow .infobox__body {
    height: 45px !important;
}
.store-locator-bottom-half #storeLocator__storeListRow .infobox__body .infobox__title {
    padding-top: 0;
    padding-bottom: 0;
    padding-right: 0;
    width: 80%;
    width: calc(100% - 60px);
}
@media (max-width: 600px) {
    .store-locator-bottom-half #storeLocator__storeListRow .infobox__body .infobox__title {
        font-size: 16px;
    }
}
.store-locator-bottom-half #storeLocator__storeListRow .infobox__body .store-address {
    padding-top: 0;
    padding-bottom: 0;
}

.store-locator-bottom-half #storeLocator__storeListRow .infobox__body .store-distance {
	display:inline !important;
}


.store-locator-bottom-half #storeLocator__storeListRow .infobox__cta {
    display: none;
}
.store-locator-bottom-half #storeLocator__storeListRow .store-location {
    padding-top: 30px;
    padding-bottom: 25px;
    display: inline-block;
}
.store-locator-bottom-half #storeLocator__storeListRow .store-address {
    padding-top: 30px;
    display: inline-block;
}

}";
}
print "</style>";
	$byregion = '';
	$regionlist = '';

	if($region_show=="1"){
		if ($locales=$wpdb->get_results("SELECT * FROM ".SSF_WP_REGION_TABLE." ", ARRAY_A)) { 
	 	foreach ($locales as $value) {
			$region_name_wpml = $value['ssf_wp_region_name'];
			do_action( 'wpml_register_single_string', 'superstorefinder-wp', $region_name_wpml, $region_name_wpml );
            $region_name_wpml = apply_filters( 'wpml_translate_single_string', $region_name_wpml, 'superstorefinder-wp', $region_name_wpml);

			$regionlist .= "<li> <label class='ssflabel'><input id='storesRegion' class='js-inputify' name='storesRegion' type='radio' value='".$value['ssf_wp_address_name']."' /> ".$region_name_wpml." </label></li>";			
		 }
		 
	}
	
	$byregion = "<div class='filter__row filter__row--regions' id='filter__states'>
				  <div class='filter__toggler arrow-toggler'> $by_region_label</div>
				  <div class='filter__toggler-contents togglerify-slider'>
					 <ul class='small-block-grid-2 space-top'>
						$regionlist
					 </ul>
				  </div>
			   </div>";
	} 
	
	
	$multiCateory=$wpdb->get_results("SELECT * FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_wp_addon_name='ssf-multi-category-wp' AND ssf_wp_addon_status='on' ", ARRAY_A);
	
	/*Front Categogy Grid show hide code */
	$allCategory='';
	
		if(!empty($multiCateory) && $category_show=="1")
		{
			$allCategory="<div class='filter__row filter__row--services' id='filter__services'>
			   <div class='filter__toggler arrow-toggler'> $by_category</div>
				<div class='filter__toggler-contents togglerify-slider' id='productsServicesFilterOptions'>
					<label class='label--vertical-align ssflabel'>
						<div class='label__input-icon'><i class='icon icon--input icon--checkbox-btn'></i></div>
					<div class='label__contents'><input type='checkbox' id='storesProductsServices' name='storesProductsServices'  value='default' />$all_category</div>
				   </label>
				</div>
			</div>";
		}
		else if(empty($multiCateory) && $category_show=="1"){ 
		$allCategory="<div class='filter__row filter__row--services' id='filter__services'>
			   <div class='filter__toggler arrow-toggler'> $by_category</div>
				<div class='filter__toggler-contents togglerify-slider' id='productsServicesFilterOptions'>
					<label class='label--vertical-align ssflabel'>
						<div class='label__input-icon'><i class='icon icon--input icon--radio-btn'></i></div>
					<div class='label__contents'><input type='radio' id='storesProductsServices' name='storesProductsServices'  value='default' />$all_category</div>
				   </label>
				</div>
			</div>";
		}
		
	
	/*Front Show Grid  show hide code*/	
		$show_Front_All='';
		if($show_all_show=="1"){
		$show_Front_All="<div class='filter__row hide-for-medium-down'><div class='filter__items-counter'> <span id='storeLocator__currentStoreCount'>0</span> $of_label <span id='storeLocator__totalStoreCount'>0</span> $outlet_label<br /> <a href='#show-all' class='ssflinks' id='filterShowAll'>$show_all_label</a></div><a class='filter__options-clearer ssflinks' href='#clear-all' id='filterOptionsClearer'>$clear_all_label </a></div>";
		}
		
$left_col_grid='';	
$filteActionButton="<div class='filter__row filter__row--cta'><div class='ssf-row'><div class='small-6 ssf-column'> <a id='applyFilterOptionsCancel' class='grey expand show-for-medium-down' data-close-popup='true' href='#'>$cancel_label</a></div><div class='small-6 large-offset-6 ssf-column'> <a class='ssf-button expand' data-close-popup='true' href='#' id='applyFilterOptions'>$select_label</a></div></div></div>";

if($region_show!="1" && $category_show!="1" && $distancePanel==false){
	$filteActionButton='';
}

if($region_show!="1" && $show_all_show!="1" && $category_show!="1" && $distancePanel==false){	
	$search_adress_size='12';
	
	if($show_result_list!='1')
   {
	$LeftPanel='';
	$MapPanel='12';
   }else{
	  $LeftPanel='3';
	  $MapPanel='9'; 
   }
	
}
else{
	$left_col_grid="<div class='large-3 ssf-column' id='filter_left_panel'>
                                    <div class='filter-radio filter-popup filter-popup--medium-down ssf-panel shadowed'>
									<div class='closePopUp'><a  data-close-popup='true' href='#' id='applyFilterOptionss'><i class='fa fa-times' aria-hidden='true'></i></a></div>
									  $show_Front_All
                                       $byregion
									   $distanceList
									   $allCategory
									   $filteActionButton
                                    </div>
                                 </div>";
   $search_adress_size='9';
   $LeftPanel='3';
   $MapPanel='9';

	
}



if($ssf_mobile_fields!=1){
$mobile_info_view="<div class='infobox__body'>
<div class='infobox__row infobox__title store-location'> Placeholder store name.</div>
$distanceOnMobile
<div class='store-storeReview'> </div>
<div class='infobox__row store-address'> Placeholder address.</div>
<div class='infobox__row store-website'> Website placeholder.</div>
<div class='infobox__row store-email'>  Email placeholder.</div>
<div class='infobox__row store-tel'>  Telephone placeholder.</div>
<div class='infobox__row store-fax'>  Fax placeholder.</div>
<div class='infobox__row store-description'>  Description placeholder.</div>
<div id='info-operating-hours' class='infobox__subtitle info-operatinghour'>
	<div  class='info__toggler actives' style='cursor:pointer;' id='openhouropen'></div> 
	<div style='display:inline-block;'>$hours_label</div></div>
<div class='info__toggler-contents togglerify-slider infobox__row store-operating-hours' style=''>
</div>
<div class='store-exturl'> &nbsp;</div>
<div class='store-contact-us' id='store-contact-us'>
		<a onclick='showConatctPopup();'  data-plugins='open-modal' data-template='modal-photo-viewer'>
		<div class='btn-super-info'> $contact_us_label</div></a>
</div>	
<div class='infobox__row store-products-services'> &nbsp;</div>
</div>";
}else{
$mobile_info_view="<div class='infobox__body'>
<div class='infobox__row infobox__title store-location'> Placeholder store name.</div>
$distanceOnMobile
<div class='store-storeReview'> </div>
<div class='infobox__row store-address'> Placeholder address.</div>
</div>";	
}
$mobileCatList='';
if($region_show=="1" || $category_show=="1" || $distanceList!='' || $distancePanel==true){
	$mobileCatList="<td class='ssf-hide-for-large-up store-locator__filter-toggler-cell' id='storeLocatorFilterToggler'>
                                                   <div class='store-locator__filter-toggler'> $filter_label</div>
                                                </td>";
}
		
/**.** Ratting and review addon check **.**/
$cmnt_user_name='';
$cmnt_user_email='';
$cmnt_readonly='';
$reviewAlert="<button name='submit' type='button' id='PostYourReviewBtn'> <span class='fa fa-plus'> </span>". $review_submit_button."</button>";
$commentShowCheck=true;
$commentShow=$wpdb->get_results("SELECT ssf_wp_addon_token FROM ".SSF_WP_ADDON_TABLE." WHERE  ssf_wp_addon_token!='public' AND ssf_addon_name='Rating-Addon' AND ssf_wp_addon_status='on' ", ARRAY_A);
if(!empty($commentShow)){
$rattingStatus= $commentShow[0]['ssf_wp_addon_token'];
if(!empty($rattingStatus)){
$ratVal=explode("#",$rattingStatus);
$rattingStatus=$ratVal[0];
$rattingURL=$ratVal[1];
if($rattingStatus=='user'){
 $current_user = wp_get_current_user();
	if ( 0 == $current_user->ID ) {
		 $commentShowCheck=false;
		 if(empty($rattingURL)){
		 $rattingURL=get_site_url().'/wp-admin/';
		 }
		 $reviewAlert='<a href="'.$rattingURL.'"><button class="ssfAskLogin" type="button">'. $login_button_msg.'</button></a>';
	} 
	else{
		 $cmnt_user_name=$current_user->display_name ;
		 $cmnt_user_email=$current_user->user_email;
		 $cmnt_readonly='readonly';
	}
  }
}
}

$rattingAddon=$wpdb->get_results("SELECT * FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_wp_addon_name='ssf-rating-addon-wp' AND ssf_wp_addon_status='on' ", ARRAY_A);
if(!empty($rattingAddon)){
 $ratingLink="<a  onclick='showCommentPopup();'  data-plugins='open-modal' data-template='modal-photo-viewer'  class='infobox__row infobox__comment'>".$review_and_ratings."</a>";
 $ratingPopUp="<div class='main-popup-holder' id='mainIntMapPopupHolder'> 
<div class='ssf-popup' id='modernIntBrowserPopup'>

<a href='javascript:hideCommentPopup();' id='intmapmodel'  class='popup-closer ssflinks'>$ssf_close_btn</a> 
<div class='rating-panel'>
<div class='ratingTopPanel'>
	<div class='ssf_review_store'>".$review_and_ratings."</div>
	<div id='popReviewsRatings'></div>
 </div>
<div class='ratingStatusPanel'>
	<div id='ssf-comment-status'></div>
	<div id='avg_ratings'></div>
</div>
 <div id='commentRatList'></div>
 <form id='ssf-comment-form' action='#' method='post' name='formSave' role='form'>
 <div id='commentRatArea' style='padding-top:30px;'>
 <div class='option_input option_text'>
	<div class='ssfRatLabel'><div class='ssf-red-star'>$contact_us_name (</div><div class='ssf-red-star-close'>)</div></div>
	<div class='ssfRatInput'><input  type='text' name='ssf_user_name' id='ssf_user_name' value='$cmnt_user_name' placeholder='$contact_plc_name' $cmnt_readonly></div>
	<div class='clearfix'></div>
	</div>
<div class='option_input option_text'>
	<div class='ssfRatLabel'><div class='ssf-red-star'>$contact_us_email (</div><div class='ssf-red-star-close'>)</div></div>
	<div class='ssfRatInput'><input  type='text' name='ssf_user_email' id='ssf_user_email' value='$cmnt_user_email' placeholder='$contact_plc_email' $cmnt_readonly></div>
	<div class='clearfix'></div>
</div>
<div class='option_input option_text'>
	<div class='ssfRatLabel'>$rating_comment </div>
	<div class='ssfRatInput'><textarea placeholder='$rating_comment' id='ssf_comment' name='ssf_comment'  required></textarea><input type='hidden' id='ssfStoreId'></div>
	<div class='clearfix'></div>
</div>
<div class='option_input option_text'>
<div id='rattingCaptcha'></div>
 </div>

<div class='option_input option_text'>
<div id='ssf-Rating'></div>
<div><button name='submit' type='button' id='ratingSubmit'><span class='fa fa-plus'></span> ".$review_submit_button."</button></div>
</div>
</div>
<div id='PostYourReview'>$reviewAlert</div>
</form>
</div></div></div>";
}else{
 $ratingLink='';
 $ratingPopUp='';

}		

$state_panel='';
$zip_panel='';
    if($state_label_show=='true'){
	 $state_panel="<div  id='info-state' class='infobox__subtitle' > $ssf_wp_state_label</div>
                                       <div class='store-state'>
                                          zip placeholder
                                       </div>";
									   
		}
    if($zip_label_show=='true'){		
	$zip_panel="<div  id='info-zip' class='infobox__subtitle' > $ssf_wp_zip_label</div>
								   <div class='store-zip'>
									  state placeholder
								   </div>";
     }
	 
	 
	 if($ssf_layout=='left'){
		 $superstore_ui="<div class='ssf-row store-locator-top-half' id='storeLocator__topHalf' style='display:none;'>
                                <div class='large-$LeftPanel ssf-column' id='super-left-panel'>
                                    <div class='ssf-panel store-locator__actions-bar' id='ssf_adress_input_box'>
                                       <table>
                                          <tbody>
                                             <tr class='searchbar_tr' style='background:none !important;'>
                                                <td class='searchbar_td' style='$ssf_hide_input_box'>
                                                   <div class='field-holder expand'> <input class='icon icon--search icon--dark sprite-icons-2x field-holder__icon' type='submit' value='' style='background: url(".SSF_WP_BASE."/images/icons/search.png) no-repeat !important;' /> <input class='ssf-field expand' id='storeLocator__searchBar' placeholder='$search_label' type='text' /><input type='hidden' class='ssf_store_id' id='ssf_store_id' value=''></div>
                                                </td>
                                                <td class='store-locator__geolocator-cell' style='$ssf_hide_input_box'>
                                                   <div class='store-locator__geolocator' id='geolocator'> <span class='fa fa-crosshairs'>&nbsp;</span></div>
                                                </td>
												$mobileCatList
                                             </tr>
                                          </tbody>
                                       </table>
                                    </div>
									 $left_col_grid                               
								<div class='ssf-row ssf-panel pad medium-pad-2x' id='storeLocator__storeListRow'>
									 <div class='store-locator__infobox store-locator__infobox--main store-locator__infobox--mobile' id='mobileStoreLocatorInfobox'>
									 <div class='infobox__closer'> &nbsp;</div>
									 <div id='info-img' class='info-img' style='position: relative;cursor: pointer;background-size: cover;' data-plugins='open-modal' data-template='modal-photo-viewer'></div>
										$mobile_info_view
										$ratingLink
										<a id='dirbutton' target='new' class='infobox__row infobox__cta' href='#directions'>$direction_label </a>
										<a id='dirbutton' class='infobox__row infobox__stv' href='#streetview'>$streetview_label</a>
									 </div>
                                    <div class='hide-for-small-down'>
                                       <h2 class='title space-bottom-3x'> $stores_near_you</h2><div id='ssf-anchor'></div>
                                    </div>
                                    <span class='store-locator__store-list medium-12' id='storeLocator__storeList'>&nbsp; </span>
									<div><input type='hidden' id='current_page' /></div>
								    <div><input type='hidden' id='show_per_page' /></div>
							 	    <div id='page_navigation' style='margin-top:15px; text-align:center;'></div>
                                 </div>
								 </div>
                              <div class='store-locator-map-holder ssf-column large-$MapPanel'>
							      <div class='store-locator__map-status' id='storeLocator__mapStatus'>
									  <div class='store-locator__map-status__spinner'> &nbsp;</div>
									  <div class='store-locator__map-status__inner' id='storeLocator__mapStatus__inner'> &nbsp;</div>
									  <div class='store-locator__map-status__closer' id='storeLocator__mapStatus__closer'> &nbsp;</div>
                                  </div>
                                 <div class='store-locator-map' id='storeLocatorMap'> &nbsp;</div>
                              </div>
                             </div>";
		 
	 }else if($ssf_layout=='right'){
		 
		 $superstore_ui="<div class='ssf-row store-locator-top-half' id='storeLocator__topHalf' style='display:none;'>
		                        <div class='store-locator-map-holder ssf-column large-$MapPanel'>
								 <div class='store-locator__map-status' id='storeLocator__mapStatus'>
									  <div class='store-locator__map-status__spinner'> &nbsp;</div>
									  <div class='store-locator__map-status__inner' id='storeLocator__mapStatus__inner'> &nbsp;</div>
									  <div class='store-locator__map-status__closer' id='storeLocator__mapStatus__closer'> &nbsp;</div>
                                  </div>
                                 <div class='store-locator-map' id='storeLocatorMap'> &nbsp;</div>
                              </div>
                                <div class='large-$LeftPanel ssf-column' id='super-left-panel'  style='float: right;'>
                                    <div class='ssf-panel store-locator__actions-bar' id='ssf_adress_input_box'>
                                       <table>
                                          <tbody>
                                             <tr class='searchbar_tr' style='background:none !important;'>
                                                <td class='searchbar_td' style='$ssf_hide_input_box'>
                                                   <div class='field-holder expand'> <input class='icon icon--search icon--dark sprite-icons-2x field-holder__icon' type='submit' value='' style='background: url(".SSF_WP_BASE."/images/icons/search.png) no-repeat !important;' /> <input class='ssf-field expand' id='storeLocator__searchBar' placeholder='$search_label' type='text' /><input type='hidden' class='ssf_store_id' id='ssf_store_id' value=''></div>
                                                </td>
                                                <td class='store-locator__geolocator-cell' style='$ssf_hide_input_box'>
                                                   <div class='store-locator__geolocator' id='geolocator'> <span class='fa fa-crosshairs'>&nbsp;</span></div>
                                                </td>
												$mobileCatList
                                             </tr>
                                          </tbody>
                                       </table>
                                    </div>
									 $left_col_grid
								<div class='ssf-row ssf-panel pad medium-pad-2x' id='storeLocator__storeListRow'>
								    <div class='store-locator__infobox store-locator__infobox--main store-locator__infobox--mobile' id='mobileStoreLocatorInfobox'>
									<div class='infobox__closer'> &nbsp;</div>
									 <div id='info-img' class='info-img' style='position: relative;cursor: pointer;background-size: cover;' data-plugins='open-modal' data-template='modal-photo-viewer'></div>
										$mobile_info_view
										$ratingLink
										<a id='dirbutton' target='new' class='infobox__row infobox__cta' href='#directions'>$direction_label </a>
										<a id='dirbutton' class='infobox__row infobox__stv' href='#streetview'>$streetview_label</a>
									 </div>
                                    <div class='hide-for-small-down'>
                                       <h2 class='title space-bottom-3x'> $stores_near_you</h2><div id='ssf-anchor'></div>
                                    </div>
                                    <span class='store-locator__store-list medium-12' id='storeLocator__storeList'>&nbsp; </span>
									<div><input type='hidden' id='current_page' /></div>
								    <div><input type='hidden' id='show_per_page' /></div>
								    <div id='page_navigation' style='margin-top:15px; text-align:center;'></div>
                                 </div>
                              </div>
                             </div>";
		 
		 
	 }else{
		 $superstore_ui="<div class='store-locator-map-holder'>
                                 <div class='store-locator-map' id='storeLocatorMap'> &nbsp;</div>
                              </div>
							  
                              <div class='ssf-row large-pad-top-4x store-locator-top-half' id='storeLocator__topHalf' style='display:none;'>
                                 $left_col_grid
                                 <div class='large-$search_adress_size ssf-column' id='ssf_adress_input_box'>
                                    <div class='ssf-panel shadowed store-locator__actions-bar'>
                                       <table>
                                          <tbody>
                                             <tr class='searchbar_tr' style='background:none !important;'>
                                                <td class='searchbar_td' style='$ssf_hide_input_box'>
                                                   <div class='field-holder expand'> <input class='icon icon--search icon--dark sprite-icons-2x field-holder__icon' type='submit' value='' style='background: url(".SSF_WP_BASE."/images/icons/search.png) no-repeat !important;' /> <input class='ssf-field expand' id='storeLocator__searchBar' placeholder='$search_label' type='text' /><input type='hidden' class='ssf_store_id' id='ssf_store_id' value=''></div>
                                                </td>
                                                <td class='store-locator__geolocator-cell' style='$ssf_hide_input_box'>
                                                   <div class='store-locator__geolocator' id='geolocator'> <span class='fa fa-crosshairs'>&nbsp;</span></div>
                                                </td>
                                                $mobileCatList
                                             </tr>
                                          </tbody>
                                       </table>
                                      <div class='store-locator__map-status' id='storeLocator__mapStatus'>
									  <div class='store-locator__map-status__spinner'> &nbsp;</div>
									  <div class='store-locator__map-status__inner' id='storeLocator__mapStatus__inner'> &nbsp;</div>
									  <div class='store-locator__map-status__closer' id='storeLocator__mapStatus__closer'> &nbsp;</div>
                                      </div>
                                    </div>
                                 </div>
                              </div>
							  

                              <div class='store-locator-bottom-half' id='storeLocator__bottomHalf'>
                                 <div class='store-locator__infobox store-locator__infobox--main store-locator__infobox--mobile' id='mobileStoreLocatorInfobox'>
								 <div id='info-img' class='info-img' style='position: relative;cursor: pointer;background-size: cover;' data-plugins='open-modal' data-template='modal-photo-viewer'></div>

                                    <div class='infobox__closer'> &nbsp;</div>
                                    $mobile_info_view
									$ratingLink
                                    <a id='dirbutton' target='new' class='infobox__row infobox__cta' href='#directions'>$direction_label </a>
				    				<a id='dirbutton' class='infobox__row infobox__stv' href='#streetview'>$streetview_label</a>
                                 </div>

                                 <div class='ssf-row ssf-panel pad medium-pad-2x' id='storeLocator__storeListRow'>
                                    <div class='ssf-column hide-for-small-down'>
                                       <h2 class='title space-bottom-3x'> $stores_near_you</h2><div id='ssf-anchor'></div>
                                    </div>
                                    <span class='store-locator__store-list' id='storeLocator__storeList'>&nbsp; </span>
                                 </div>
								<div><input type='hidden' id='current_page' /></div>
								<div><input type='hidden' id='show_per_page' /></div>
								<div id='page_navigation' style='margin-top:15px; text-align:center;'></div>
                              </div>";
		 
		 
	 }
		
	$superstorefinder = "
	<div id='ssf-dummy-blck' style='height:700px;'><div id='ssf-preloader'>
	<div id='ssf-overlay'>
    	<center><img src='".SSF_WP_BASE."/images/icons/spinner.gif' alt='Loading'></center>
    </div>
</div></div>
	<div class='ssf-main-content' role='main' id='mainContent'>
                        <div class='ssf-content-section'>
                           <div class='section pad-bottom-2x medium-pad-bottom-4x store-locator-section' style='display:none;' id='store-locator-section-bg'>
							  $superstore_ui
                              <div class='store-locator__infobox store-locator__infobox--main store-locator__infobox--in-map' id='storeLocatorInfobox'>
							  <div id='info-img' class='info-img' style='position: relative;cursor: pointer;background-size: cover;' data-plugins='open-modal' data-template='modal-photo-viewer'></div>
                                 <div class='infobox__inner'>
                                    <div class='infobox__closer'> &nbsp;</div>
                                    <div class='infobox__body'>
                                       <div class='infobox__row infobox__title store-location'> Placeholder store name</div>
									   $distanceInInfo
									   <div class='store-storeReview'> </div>
                                       <div class='infobox__row store-address'> Placeholder for address</div>
							 <div class='infobox__row'>
							 $state_panel
							 $zip_panel
							 <div  id='info-website' class='infobox__subtitle' > $website_label</div>
                                       <div class='store-website'>
                                          <div class='infobox__subtitle'> $website_label</div>
                                          Website placeholder
                                       </div>
							<div  id='info-email' class='infobox__subtitle' > $email_label</div>
							<div class='store-email'>
                                          <div class='infobox__subtitle'> $email_label</div>
                                          Email placeholder
                                       </div>
							<div  id='info-tel' class='infobox__subtitle' > $phone_label</div>
							<div class='store-tel'>
                                          <div class='infobox__subtitle'> $phone_label</div>
                                          Telephone placeholder
                                       </div>
							<div  id='info-fax' class='infobox__subtitle' > $fax_label</div>
							<div class='store-fax'>
                                          <div class='infobox__subtitle'> $fax_label</div>
                                          Fax placeholder
                                       </div>		   
									   
 					<div id='info-description' class='infobox__subtitle'> $description_label</div>
					<div class='store-description'>
                                          Description placeholder
                                       </div>
					<div id='info-operating-hours' class='infobox__subtitle info-operatinghour'>
						<div  class='info__toggler actives' style='cursor:pointer;' id='openhouropen'></div> 
						<div style='display:inline-block;'>$hours_label</div></div>
					<div class='info__toggler-contents togglerify-slider infobox__row store-operating-hours' style=''>
				 </div>
				   <div  id='info-exturl' class='infobox__subtitle' > </div>
				   <div class='store-exturl'>
					  <div class='infobox__subtitle'> $exturl_label</div>
					  Ext placeholder
				   </div>
				   <div class='store-contact-us' id='store-contact-us'>
							<a onclick='showConatctPopup();'  data-plugins='open-modal' data-template='modal-photo-viewer'>
							<div class='btn-super-info'> $contact_us_label</div>
						</a></div>	
				   
					<div style='clear:both;'>&nbsp;</div>
					</div>
					$ratingLink
                    <a id='dirbutton' target='new' class='infobox__row infobox__cta' href='#directions'>$direction_label </a>
				    <a class='infobox__row infobox__stv' href='#streetview'>$streetview_label</a>
					</div>
					</div>
					</div>
					</div>
					</div>
					</div>
				  <div class='main-popup-holder' id='mainPopupHolder'  style='display:none;'>
                     <div class='ssf-popup' id='modernBrowserPopup'>
                        <a href='javascript:hidePopup();' class='popup-closer ssflinks'>$ssf_close_btn</a> 
                        <h3 class='popup-title'></h3>
                        <div class='pad-horizontal-2x popup-img' style='text-align:center;'><img id='popup-image' src='' style='max-width:550px !important'/></div>
                        <script> function hidePopup() { jQuery('#mainPopupHolder, #modernBrowserPopup').removeClass('is-shown'); } 
						function showPopup(t,i) { 
						jQuery('.popup-title').html(t);
						jQuery('#popup-image').attr('src',i);
						jQuery('#mainPopupHolder, #modernBrowserPopup').addClass('is-shown'); } 
						</script> 
                     </div>
                  </div>

				  <div class='main-popup-holder' id='mainPopupContat' style='display:none;'>
                     <div class='ssf-popup' id='modernBrowserConatct'>
                        <a href='javascript:hideConatctPopup();' class='popup-closer ssflinks'>$ssf_close_btn</a> 
						<form id='ssf-contact-form' action='#' method='post' name='form' role='form'>
						<div><h3 class='ssf_cont_store'>$contact_us_store</h3><h4 id='ssf-msg-status'></h4></div>
						<div>
						<label>
						<div class='ssf_cont_lab ssf-red-star' >$contact_us_name: (</div><div class=' ssf-red-star-close'>)</div>
						<input placeholder='$contact_plc_name' name='ssf_cont_name' type='text' tabindex='1' required autofocus>
						</label>
						</div>
						<div>
						<label>
						<div class='ssf_cont_lab ssf-red-star' >$contact_us_email: (</div><div class=' ssf-red-star-close'>)</div>
						<input placeholder='$contact_plc_email' name='ssf_cont_email' type='email' tabindex='2' required>
						</label>
						</div>
						<div>
						<label>
						<div class='ssf_cont_lab'>$contact_us_phone</div>
						<input placeholder='$contact_plc_phone' name='ssf_cont_phone' type='tel' tabindex='3'>
						</label>
						</div>
						<div>
						<label>
						<div class='ssf_cont_lab ssf-red-star'>$contact_us_msg: (</div><div class=' ssf-red-star-close'>)</div>
						<textarea placeholder='$contact_plc_msg' name='ssf_cont_msg' tabindex='4' required></textarea>
						</label>
						</div>
						<div>
						<button name='submit' type='button' id='contact-submit'>$contact_us_btn</button>
						</div>
                        </form>
                        <script> function hideConatctPopup() { jQuery('#mainPopupContat, #modernBrowserConatct').removeClass('is-shown'); } 
						function showConatctPopup() { 
						jQuery('#mainPopupContat, #modernBrowserConatct').addClass('is-shown'); } 
						</script> 
                     </div>
                  </div>
				  $ratingPopUp
";
}


function getGoogleMapsApi(){
global $ssf_wp_vars;
	$google_api_key=(trim($ssf_wp_vars['google_api_key'])!="")? ssfParseToXML($ssf_wp_vars['google_api_key']) : "";
	if(!empty($google_api_key)){
	$google_api_key='key='.$google_api_key.'&';	
	}else{
	$google_api_key='';	
	}
 return $google_api_key;
}

/*---------------------------------------*/

function ssf_wp_location_form($mode="add", $pre_html="", $post_html=""){
    
	$google_api_key=getGoogleMapsApi();
	wp_enqueue_script( 'mega-finder-geo' , SSF_WP_JS_BASE.'/geoip.php' , array( 'jquery' ) , '1.0' , true );
	wp_enqueue_script( 'mega-superstorfinder' , 'https://maps.googleapis.com/maps/api/js?'.$google_api_key.'sensor=false' , array( 'jquery' ) , '1.0' , true );

	wp_enqueue_script( 'mega-finder' , SSF_WP_JS_BASE.'/super-store-finder.js' , array( 'jquery' ) , '1.0' , true );

	$html="<style>.newstore_map {

    clear: both;

    display: block;

    height: 250px;

    width: 100%;

}</style><div class='input_section'>

	<form name='manualAddForm' method='post' enctype='multipart/form-data'>

	$pre_html

					<div class='input_title'>

						<h3><span class='fa fa-pencil'>&nbsp;</span> Add a Store</h3>

						<span class='submit'>
						<a href='".SSF_WP_ADMIN_NAV_BASE.SSF_WP_ADMIN_DIR."/pages/stores.php' style='margin-right:10px;'>
						<input type='button' value='".__("Cancel", SSF_WP_TEXT_DOMAIN)."' class='button-primary'></a>
						<input type='submit' value='".__("Save this Store", SSF_WP_TEXT_DOMAIN)."' class='button-primary'>

						</span>

						<div class='clearfix'></div>

					</div>

					<div class='all_options'>

					<div class='option_input option_text'>

					<div id='map_canvas' class='newstore_map'>

					</div>

					</div>

				
					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Name</label>

					<input  type='text' name='ssf_wp_store'>

					<small>Enter your store name</small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Categories / Tags</label>

					<input  type='text' name='ssf_wp_tags'>
					
					<small>Enter categories / tags separated with commas</small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Address</label>

					<input type='text' name='ssf_wp_address' id='address'> 

					<small>The Latitude and Longitude will be automatically detected upon entering address</small>

					<div class='clearfix'></div>

					<br>

					<label for='shortname_logo'>

					City</label>

					<input type='text' name='ssf_wp_city' id='ssf_wp_city'>

					<small></small>

					<div class='clearfix'></div>
				
					<br>

					<label for='shortname_logo'>

					State</label>

					<input type='text' name='ssf_wp_state' id='ssf_wp_state'>

					<small></small>

					<div class='clearfix'></div>

					<br>

					<label for='shortname_logo'>

					Zip</label>

					<input  type='text' name='ssf_wp_zip' id='ssf_wp_zip'>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Phone</label>

					<input type='text' name='ssf_wp_phone'>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Fax</label>

					<input type='text' name='ssf_wp_fax'>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Email</label>

					<input type='text' name='ssf_wp_email'>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Website</label>

					<input type='text' name='ssf_wp_url'>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					External URL</label>

					<input type='text' name='ssf_wp_ext_url'>

					<small></small>

					<div class='clearfix'></div>

					</div>
					
					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Contact Us Email</label>
                    <select name='ssf_wp_contact_email'>
					<option value='0'>None</option>
					<option value='1'>Store's Email</option>
					<option value='2'>Settings' Email</option>
					</select>

					<small>If store's Email or <a  href='".SSF_WP_ADMIN_NAV_BASE.SSF_WP_ADMIN_DIR."/pages/settings.php'>Setting's</a> Email is not available, contact button will not be shown</small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo' style='line-height:100px;'>

					Description</label>

					<textarea name='ssf_wp_description' style='width:380px' id='ssf_wp_html_description'> </textarea>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo' style='line-height:100px;'>

					Opening Hours</label>

					<textarea id='ssf_wp_html_hours' style='width:380px' name='ssf_wp_hours'> </textarea>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Image</label>

					<input type='file' name='ssf_wp_image'>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Custom Marker</label>

					<input type='file' name='ssf_wp_marker'>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Embed Video</label>

					<input type='text' name='ssf_wp_embed_video'>

					<small>Learn how to embed videos from YouTube, Vimeo, etc <a href='http://superstorefinder.net/superstorefinderwp/user-guide/#document-8' target='new'>here</a></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Default Media</label><br/><br/>

					<input type='radio' name='ssf_wp_default_media' value='image' checked> Image <br/>

					<input type='radio' name='ssf_wp_default_media' value='video'> Video <br/>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Latitude</label>

					<input type='text' name='ssf_wp_latitude' id='ssf_wp_latitude'>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					 Longitude</label>

					<input type='text' name='ssf_wp_longitude' id='ssf_wp_longitude'>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='input_title'>

						<span class='submit'>
						<a href='".SSF_WP_ADMIN_NAV_BASE.SSF_WP_ADMIN_DIR."/pages/stores.php' style='margin-right:10px;'>
						<input type='button' value='".__("Cancel", SSF_WP_TEXT_DOMAIN)."' class='button-primary'></a>
						<input type='submit' value='".__("Save this Store", SSF_WP_TEXT_DOMAIN)."' class='button-primary'>

						</span>

						<div class='clearfix'></div>

					</div></div>"; 

		$html.=(function_exists("do_ssf_wp_hook"))? do_ssf_wp_hook("ssf_wp_add_location_fields",  "append-return") : "" ;

		$html.=wp_nonce_field("add-location_single", "_wpnonce", true, false);

		$html.="

	$post_html

</form></div>";
   print "<script>bkLib.onDomLoaded(function() { new nicEditor({fullPanel : true}).panelInstance('ssf_wp_html_hours'); });
   				bkLib.onDomLoaded(function() { new nicEditor({fullPanel : true}).panelInstance('ssf_wp_html_description'); });</script>";
	return $html;

}

//add states
function ssf_wp_add_region()
{
global $wpdb;
	$fieldList=""; $valueList="";
	foreach ($_POST as $key=>$value) {
		if (preg_match("@ssf_wp_@", $key)) {
			$fieldList.="$key,";
			if (is_array($value)){
				$value=serialize($value); //for arrays being submitted
				$valueList.="'$value',";
			} else {
				$valueList.=$wpdb->prepare("%s", ssf_comma(stripslashes($value))).",";
				
			}
		}
	}

	$fieldList=substr($fieldList, 0, strlen($fieldList)-1);
	$valueList=substr($valueList, 0, strlen($valueList)-1);
	$wpdb->query("INSERT INTO ".SSF_WP_REGION_TABLE." ($fieldList) VALUES ($valueList)");
	}

function ssf_wp_region_form($mode="add", $pre_html="", $post_html=""){
	$html="<div class='input_section'>
	<form name='manualAddForm' method='post' enctype='multipart/form-data'>
	$pre_html
					<div class='input_title'>
						<h3><span class='fa fa-pencil'>&nbsp;</span> Add a Region</h3>
						<span class='submit'>
						<a href='".SSF_WP_ADMIN_NAV_BASE.SSF_WP_ADMIN_DIR."/pages/region.php' style='margin-right:10px;'>
						<input type='button' value='".__("Cancel", SSF_WP_TEXT_DOMAIN)."' class='button-primary'></a>
						<input type='submit' value='".__("Save this Region", SSF_WP_TEXT_DOMAIN)."' class='button-primary'>

						</span>
						
						<div class='clearfix'></div>
					</div>
					<div class='all_options'>
					<div class='option_input option_text'>
					<label for='shortname_logo'>
					Region Name</label>
					<input  type='text' name='ssf_wp_region_name'>
					<small>Enter region name</small>
					<div class='clearfix'></div>
					</div>
					<div class='option_input option_text'>
					<label for='shortname_logo'>
					Region Address</label>
					<input  type='text' name='ssf_wp_address_name'>
					<small>Enter region address</small>
					<div class='clearfix'></div>
					</div>
					<div class='input_title'>
						<span class='submit'>
						<a href='".SSF_WP_ADMIN_NAV_BASE.SSF_WP_ADMIN_DIR."/pages/region.php' style='margin-right:10px;'>
						<input type='button' value='".__("Cancel", SSF_WP_TEXT_DOMAIN)."' class='button-primary'></a>
						<input type='submit' value='".__("Save this Region", SSF_WP_TEXT_DOMAIN)."' class='button-primary'>
						</span>
						<div class='clearfix'></div>
					</div></div>"; 
		
		
		$html.=(function_exists("do_ssf_wp_hook"))? do_ssf_wp_hook("ssf_wp_add_location_fields",  "append-return") : "" ;
		$html.=wp_nonce_field("add-location_single", "_wpnonce", true, false);
		$html.="
	$post_html
</form></div>";
   
	
	return $html;
}


//end state

function ssf_wp_add_location() {

	global $wpdb;
	$fieldList=""; $valueList="";

	foreach ($_POST as $key=>$value) {
		if (preg_match("@ssf_wp_@", $key)) {
			if ($key=="ssf_wp_tags") {
				$value=ssf_wp_prepare_tag_string($value);
			}

			$fieldList.="$key,";
			if (is_array($value)){
				$value=serialize($value); //for arrays being submitted
				$valueList.="'$value',";
			} else {
				$valueList.=$wpdb->prepare("%s", ssf_comma(stripslashes($value))).",";
			}
		}
	}

	//add store

	$fieldList=substr($fieldList, 0, strlen($fieldList)-1);
	$valueList=substr($valueList, 0, strlen($valueList)-1);
	$wpdb->query("INSERT INTO ".SSF_WP_TABLE." ($fieldList) VALUES ($valueList)");
	$new_loc_id=$wpdb->insert_id;
	//$address="$_POST[ssf_wp_address], $_POST[ssf_wp_city], $_POST[ssf_wp_state] $_POST[ssf_wp_zip]";
	//ssf_wp_do_geocoding($address);// comment by anubhav 28/07/2015 

 	if (!empty($_POST['ssf_wp_tags'])){
		ssf_wp_process_tags($_POST['ssf_wp_tags'], "insert", $new_loc_id);
	}


	/*********** image upload *************/

	if(!empty($_FILES['ssf_wp_image']['name'])){
	$valid_exts = array("jpg","jpeg","gif","png");
	$ext = explode(".",strtolower(trim($_FILES["ssf_wp_image"]["name"])));
	$ext = end($ext);
			if(in_array($ext,$valid_exts)){
				$max_dimension = 800; // Max new width or height, can not exceed this value.
				 $dir=SSF_WP_UPLOADS_PATH."/images/".$new_loc_id;
				 if(!is_dir($dir))
				 {
					 mkdir($dir, 0777, true);
					 @chmod($dir, 0777);
				 }
				$postvars = array(
				"image"    => trim(strtolower(str_replace(' ','_',preg_replace('/[^a-zA-Z0-9\-_. ]/','',$_FILES["ssf_wp_image"]["name"])))),
				"image_tmp"    => $_FILES["ssf_wp_image"]["tmp_name"],
				"image_size"    => (int)$_FILES["ssf_wp_image"]["size"],
				"image_max_width"    => (int)100,
				"image_max_height"   => (int)100

				);

					if($ext == "jpg" || $ext == "jpeg"){
					  $image = imagecreatefromjpeg($postvars["image_tmp"]);
					}
					else if($ext == "gif"){
					  $image = imagecreatefromgif($postvars["image_tmp"]);
					}

					else if($ext == "png"){
					  $image = imagecreatefrompng($postvars["image_tmp"]);
					}
				    $size = getimagesize($postvars["image_tmp"]);
					$ratio = $size[0]/$size[1]; // width/height
					if( $ratio > 1) {
						$width = $size[0];
						$height = $size[0]/$ratio;
					}

					else {
					
						$width = $size[0]*$ratio;
						$height = $size[0];
					}
					$tmp = imagecreatetruecolor($width,$height);

					if($ext == "gif" or $ext == "png"){
						imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
						imagealphablending($tmp, false);
						imagesavealpha($tmp, true);
						imagecopyresampled($tmp,$image,0,0,0,0,$width2,$height2,$size[0],$size[1]);
						$filename = $dir.'/'.$postvars["image"];
						imagepng($tmp,$filename,9);
						
					}

					else

					{
						$whiteBackground = imagecolorallocate($tmp, 255, 255, 255); 
						imagefill($tmp,0,0,$whiteBackground); // fill the background with white
						imagecopyresampled($tmp,$image,0,0,0,0,$width,$height,$size[0],$size[1]);
						$filename = $dir.'/'.$postvars["image"];
						imagejpeg($tmp,$filename,100);
						
					}

					imagedestroy($image);
					imagedestroy($tmp);
				}
			}


			/*custom marker add with store image */

		if(!empty($_FILES['ssf_wp_marker']['name'])){
			$dir=SSF_WP_UPLOADS_PATH."/images/icons/".$new_loc_id;
			 if(!is_dir($dir))
			{
				mkdir($dir, 0777, true);
				@chmod($dir, 0777);
			}
			$valid_exts = array("jpg","jpeg","gif","png");
			$ext = explode(".",strtolower(trim($_FILES["ssf_wp_marker"]["name"])));
			$ext = end($ext);
			if(in_array($ext,$valid_exts)){
				$max_dimension = 800; // Max new width or height, can not exceed this value.
				$postvars = array(
				"image"    => 'store-marker.png',
				"image_tmp"    => $_FILES["ssf_wp_marker"]["tmp_name"],
				);

					if($ext == "jpg" || $ext == "jpeg"){
						$image = imagecreatefromjpeg($postvars["image_tmp"]);
					}

					else if($ext == "gif"){
						$image = imagecreatefromgif($postvars["image_tmp"]);
					}

					else if($ext == "png"){
						$image = imagecreatefrompng($postvars["image_tmp"]);
					}

					$size = getimagesize($postvars["image_tmp"]);
					$ratio = $size[0]/$size[1]; // width/height
					if( $ratio > 1) {
						$width = 58;
						$height = 58/$ratio;
					}

					else {
						$width = 58*$ratio;
						$height = 58;
					}

					$tmp = imagecreatetruecolor($width,$height);
					if($ext == "gif" or $ext == "png"){
						imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
						imagealphablending($tmp, false);
						imagesavealpha($tmp, true);
						imagecopyresampled($tmp,$image,0,0,0,0,$width,$height,$size[0],$size[1]);
						$filename = $dir.'/'.$postvars["image"];
						imagepng($tmp,$filename,9);
					}

					else
					{
						$whiteBackground = imagecolorallocate($tmp, 255, 255, 255); 
						imagefill($tmp,0,0,$whiteBackground); // fill the background with white
						imagecopyresampled($tmp,$image,0,0,0,0,$width,$height,$size[0],$size[1]);
						$filename = $dir.'/'.$postvars["image"];
						imagejpeg($tmp,$filename,100);
					}

					imagedestroy($image);
					imagedestroy($tmp);
				}
			}
}

/*--------------------------------------------------*/

function ssf_wp_define_db_tables() {
	global $wpdb; 
	$ssf_wp_db_prefix = $wpdb->prefix; 
	if (!defined('SSF_WP_DB_PREFIX')){ define('SSF_WP_DB_PREFIX', $ssf_wp_db_prefix); }
	if (!empty($ssf_wp_db_prefix)) {
		if (!defined('SSF_WP_TABLE')){ define('SSF_WP_TABLE', SSF_WP_DB_PREFIX."ssf_wp_stores"); }
		if (!defined('SSF_WP_TAG_TABLE')){ define('SSF_WP_TAG_TABLE', SSF_WP_DB_PREFIX."ssf_wp_tag"); }
		if (!defined('SSF_WP_SETTING_TABLE')){ define('SSF_WP_SETTING_TABLE', SSF_WP_DB_PREFIX."ssf_wp_setting"); }
		if (!defined('SSF_WP_ADDON_TABLE')){ define('SSF_WP_ADDON_TABLE', SSF_WP_DB_PREFIX."ssf_wp_addon"); }
		if (!defined('SSF_WP_REGION_TABLE')){ define('SSF_WP_REGION_TABLE', SSF_WP_DB_PREFIX."ssf_wp_region"); }
		if (!defined('SSF_WP_SOCIAL_TABLE')){ define('SSF_WP_SOCIAL_TABLE', SSF_WP_DB_PREFIX."store_ratings"); }
	}

}

ssf_wp_define_db_tables(); 

/*----------------------------------------------------*/

function ssf_wp_single_location_info($value, $colspan, $bgcol) {

	global $ssf_wp_hooks;

	$_GET['edit'] = $value['ssf_wp_id']; 

	print "<tr style='background-color:$bgcol' id='ssf_wp_tr_data-$value[ssf_wp_id]'>";

	$cancel_onclick = "location.href=\"".str_replace("&edit=$_GET[edit]", "",$_SERVER['REQUEST_URI'])."\"";

	$dir=SSF_WP_UPLOADS_PATH."/images/".$_GET['edit']."/";

	if (is_dir($dir)){

	$image_upload_path="../wp-content/uploads/ssf-wp-uploads/images/";

	$images = @scandir($dir);

		foreach($images as $k=>$v):

		endforeach;

		$imageEdit='<div id="editImage'.$_GET['edit'].'" style="display:inline-block;"><img src="'.$image_upload_path.$_GET['edit'].'/'.$v.'" style="max-width:300px;">';

	    $imageEdit_btn="<input type=\"button\" onclick=\"deleteMarker($_GET[edit]);\" class=\"btn btn-danger\"  value=\"Delete\"></div>";

		$desabled="disabled='disabled'";

	}

	else

	{

		$imageEdit='';

		$imageEdit_btn='';

		$desabled='';

	}

	/* custom marker edir code */

	$dir_marker=SSF_WP_UPLOADS_PATH."/images/icons/".$_GET['edit']."/";

	

	if (is_dir($dir_marker)){

	$image_upload_path="../wp-content/uploads/ssf-wp-uploads/images/icons/";

	$images = @scandir($dir_marker);

		foreach($images as $k=>$v):

		endforeach;

		$markerEdit='<div id="editCmarker'.$_GET['edit'].'" style="display:inline-block;"><img src="'.$image_upload_path.$_GET['edit'].'/'.$v.'" style="max-width:300px;">';

	    $markerEdit_btn="<input type=\"button\" onclick=\"delMarker($_GET[edit]);\" class=\"btn btn-danger\"  value=\"Delete\"></div>";

		$des_marker="disabled='disabled'";

	}

	else

	{

		$markerEdit='';

		$markerEdit_btn='';

		$des_marker='';

	}

	/* end custom */

	$media1 = '';

	$media2 = '';


	if($value['ssf_wp_default_media']=='image' || $value['ssf_wp_default_media']=='') { $media1 = "checked"; }

	if($value['ssf_wp_default_media']=='video') { $media2 = "checked"; }
	
	$contactEmail='';
	$contactEmail1='';
	$contactEmail2='';
	if($value['ssf_wp_contact_email']=='1'){ $contactEmail1='selected'; }
	else if($value['ssf_wp_contact_email']=='2'){ $contactEmail2='selected'; }
	else {  $contactEmail='selected'; }
   
    $google_api_key=getGoogleMapsApi();
	wp_enqueue_script( 'mega-finder-geo' , SSF_WP_JS_BASE.'/geoip.php' , array( 'jquery' ) , '1.0' , true );
	wp_enqueue_script( 'mega-superstorfinder' , 'https://maps.googleapis.com/maps/api/js?'.$google_api_key.'sensor=false' , array( 'jquery' ) , '1.0' , true );
	wp_enqueue_script( 'mega-finder' , SSF_WP_JS_BASE.'/super-store-finder.js' , array( 'jquery' ) , '1.0' , true );

	
	print "<style>.newstore_map {

    clear: both;

    display: block;

    height: 250px;

    width: 100%;

}</style>";

	print "<td colspan='$colspan'>

	<div class='input_section'>

	<a name='a$value[ssf_wp_id]'></a>

	<form name='manualAddForm'  method='post' enctype='multipart/form-data'>

					<div class='input_title'>

						<h3><span class='fa fa-pencil'>&nbsp;</span> Edit Store</h3>

						<span class='submit'>

						<input type='submit' value='".__("Save", SSF_WP_TEXT_DOMAIN)."' class='button-primary'> <input type='button' class='ssf-button' value='".__("Cancel", SSF_WP_TEXT_DOMAIN)."' onclick='$cancel_onclick'>

						</span>

						<div class='clearfix'></div>

					</div>

					<div class='all_options'>

					<div class='option_input option_text'>

					<div id='map_canvas' class='newstore_map'>

					</div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Name</label>

					<input type='text' name='ssf_wp_store-$value[ssf_wp_id]' id='ssf_wp_store-$value[ssf_wp_id]' value='$value[ssf_wp_store]'>

					<small>Enter your store name</small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Categories / Tags</label>

					<input type='text' name='ssf_wp_tags-$value[ssf_wp_id]' id='ssf_wp_tags-$value[ssf_wp_id]' value='$value[ssf_wp_tags]' size='13'>
					
					<small>Enter categories / tags separated with commas</small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Address</label>

					<input type='text' name='ssf_wp_address-$value[ssf_wp_id]' id='address' value='$value[ssf_wp_address]' size='13'>

					<small>The Latitude and Longitude will be automatically detected upon entering address</small>

					<div class='clearfix'></div>

					<br>

					<label for='shortname_logo'>

					City</label>

					<input type='text' name='ssf_wp_city-$value[ssf_wp_id]' id='ssf_wp_city' value='$value[ssf_wp_city]' size='13'>

					<small></small>

					<div class='clearfix'></div>

					<br>

					<label for='shortname_logo'>

					State</label>

					<input type='text' name='ssf_wp_state-$value[ssf_wp_id]' id='ssf_wp_state' value='$value[ssf_wp_state]' size='4'>

					<small></small>

					<div class='clearfix'></div>

					

					<br>

					<label for='shortname_logo'>

					Zip</label>

					<input type='text' name='ssf_wp_zip-$value[ssf_wp_id]' id='ssf_wp_zip' value='$value[ssf_wp_zip]' size='6'>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Phone</label>

					<input type='text' name='ssf_wp_phone-$value[ssf_wp_id]' id='ssf_wp_phone-$value[ssf_wp_id]' value='$value[ssf_wp_phone]' size='13'>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Fax</label>

					<input type='text' name='ssf_wp_fax-$value[ssf_wp_id]' id='ssf_wp_fax-$value[ssf_wp_id]' value='$value[ssf_wp_fax]' size='13'>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Email</label>

					<input type='text' name='ssf_wp_email-$value[ssf_wp_id]' id='ssf_wp_email-$value[ssf_wp_id]' value='$value[ssf_wp_email]' size='13'>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Website</label>

					<input type='text' name='ssf_wp_url-$value[ssf_wp_id]' id='ssf_wp_url-$value[ssf_wp_id]' value='$value[ssf_wp_url]' size='13'>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					External URL</label>

					<input type='text' name='ssf_wp_ext_url-$value[ssf_wp_id]' id='ssf_wp_ext_url-$value[ssf_wp_id]' value='$value[ssf_wp_ext_url]' size='13'>

					<small></small>

					<div class='clearfix'></div>

					</div>
					
					
					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Contact Us Email</label>
                    <select name='ssf_wp_contact_email-$value[ssf_wp_id]' id='ssf_wp_contact_email-$value[ssf_wp_id]'>
					<option value='0' $contactEmail>None</option>
					<option value='1' $contactEmail1>Store's Email</option>
					<option value='2' $contactEmail2>Settings' Email</option>
					</select>

					<small>If store's Email or <a  href='".SSF_WP_ADMIN_NAV_BASE.SSF_WP_ADMIN_DIR."/pages/settings.php'>Setting's</a> Email is not available, contact button will not be shown</small>
					<div class='clearfix'></div>

					</div>
					

					<div class='option_input option_text'>

					<label for='shortname_logo' style='line-height:100px;'>

					Description</label>

				<textarea name='ssf_wp_description-$value[ssf_wp_id]' id='ssf_wp_html_description' style='width:380px'>$value[ssf_wp_description]</textarea>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo' style='line-height:100px;'>

					Opening Hours</label>

					<textarea id='ssf_wp_html_hours' name='ssf_wp_hours-$value[ssf_wp_id]' style='width:380px'>$value[ssf_wp_hours] </textarea>
					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Image</label>

					<input type='file' class='custom_marker' name='ssf_wp_image' id='ssf_wp_image'  ".$desabled." size='13'>".$imageEdit.$imageEdit_btn."

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Custom Marker</label>

					<input type='file' class='custom_marker' name='ssf_wp_marker' id='ssf_wp_marker'  ".$des_marker." size='13'>".$markerEdit.$markerEdit_btn."

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Embed Video</label>

					<input type='text' name='ssf_wp_embed_video-$value[ssf_wp_id]' id='ssf_wp_embed_video-$value[ssf_wp_id]' value='$value[ssf_wp_embed_video]' >

					<small>Learn how to embed videos from YouTube, Vimeo, etc <a href='http://superstorefinder.net/superstorefinderwp/user-guide/#document-8' target='new'>here</a></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Default Media</label><br/><br/>

					<input type='radio' name='ssf_wp_default_media-$value[ssf_wp_id]' id='ssf_wp_default_media-$value[ssf_wp_id]' value='image' $media1> Image <br/>

					<input type='radio' name='ssf_wp_default_media-$value[ssf_wp_id]' id='ssf_wp_default_media-$value[ssf_wp_id]' value='video' $media2> Video <br/>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					Latitude</label>

					<input type='text' name='ssf_wp_latitude-$value[ssf_wp_id]' id='ssf_wp_latitude' value='$value[ssf_wp_latitude]'>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='option_input option_text'>

					<label for='shortname_logo'>

					 Longitude</label>

					<input type='text' name='ssf_wp_longitude-$value[ssf_wp_id]' id='ssf_wp_longitude' value='$value[ssf_wp_longitude]'>

					<small></small>

					<div class='clearfix'></div>

					</div>

					<div class='input_title'>

						<span class='submit'>

						<input type='submit' value='".__("Save", SSF_WP_TEXT_DOMAIN)."' class='button-primary'> <input type='button' class='ssf-button' value='".__("Cancel", SSF_WP_TEXT_DOMAIN)."' onclick='$cancel_onclick'>

						

						</span>

						<div class='clearfix'></div>

					</div></div>";

		

		if (function_exists("do_ssf_wp_hook")) {

			ssf_wp_show_custom_fields();

		}

	if (function_exists("do_ssf_wp_hook")) {do_ssf_wp_hook("ssf_wp_single_location_edit", "select-top");}

	print "</form></td>";



print "</tr>";

print "<script>bkLib.onDomLoaded(function() { new nicEditor({fullPanel : true}).panelInstance('ssf_wp_html_hours'); });
   				bkLib.onDomLoaded(function() { new nicEditor({fullPanel : true}).panelInstance('ssf_wp_html_description'); });</script>";

	}


	
	
/** super store map region and language list **/
function super_store_region_list() {
	$regions = array (
	    array('value' => 'World', 'name' => 'World Wide (Default)'),
	    array('value' => 'US', 'name' => 'United States'),
		array('value' => 'AF', 'name' => 'Afghanistan'),
		array('value' => 'AL', 'name' => 'Albania'),
		array('value' => 'DZ', 'name' => 'Algeria'),
		array('value' => 'AS', 'name' => 'American Samoa'),
		array('value' => 'AD', 'name' => 'Andorra'),
		array('value' => 'AO', 'name' => 'Angola'),
		array('value' => 'AI', 'name' => 'Anguilla'),
		array('value' => 'AQ', 'name' => 'Antarctica'),
		array('value' => 'AG', 'name' => 'Antigua & Barbuda'),
		array('value' => 'AR', 'name' => 'Argentina'),
		array('value' => 'AM', 'name' => 'Armenia'),
		array('value' => 'AW', 'name' => 'Aruba'),
		array('value' => 'AC', 'name' => 'Ascension Island'),
		array('value' => 'AU', 'name' => 'Australia'),
		array('value' => 'AT', 'name' => 'Austria'),
		array('value' => 'AZ', 'name' => 'Azerbaijan'),
		array('value' => 'BS', 'name' => 'Bahamas'),
		array('value' => 'BH', 'name' => 'Bahrain'),
		array('value' => 'BD', 'name' => 'Bangladesh'),
		array('value' => 'BB', 'name' => 'Barbados'),
		array('value' => 'BY', 'name' => 'Belarus'),
		array('value' => 'BE', 'name' => 'Belgium'),
		array('value' => 'BZ', 'name' => 'Belize'),
		array('value' => 'BJ', 'name' => 'Benin'),
		array('value' => 'BM', 'name' => 'Bermuda'),
		array('value' => 'BT', 'name' => 'Bhutan'),
		array('value' => 'BO', 'name' => 'Bolivia'),
		array('value' => 'BA', 'name' => 'Bosnia & Herzegovina'),
		array('value' => 'BW', 'name' => 'Botswana'),
		array('value' => 'BV', 'name' => 'Bouvet Island'),
		array('value' => 'BR', 'name' => 'Brazil'),
		array('value' => 'IO', 'name' => 'British Indian Ocean Territory'),
		array('value' => 'VG', 'name' => 'British Virgin Islands'),
		array('value' => 'BN', 'name' => 'Brunei'),
		array('value' => 'BG', 'name' => 'Bulgaria'),
		array('value' => 'BF', 'name' => 'Burkina Faso'),
		array('value' => 'BI', 'name' => 'Burundi'),
		array('value' => 'KH', 'name' => 'Cambodia'),
		array('value' => 'CM', 'name' => 'Cameroon'),
		array('value' => 'CA', 'name' => 'Canada'),
		array('value' => 'IC', 'name' => 'Canary Islands'),
		array('value' => 'CV', 'name' => 'Cape Verde'),
		array('value' => 'BQ', 'name' => 'Caribbean Netherlands'),
		array('value' => 'KY', 'name' => 'Cayman Islands'),
		array('value' => 'CF', 'name' => 'Central African Republic'),
		array('value' => 'EA', 'name' => 'Ceuta & Melilla'),
		array('value' => 'TD', 'name' => 'Chad'),
		array('value' => 'CL', 'name' => 'Chile'),
		array('value' => 'CN', 'name' => 'China'),
		array('value' => 'CX', 'name' => 'Christmas Island'),
		array('value' => 'CP', 'name' => 'Clipperton Island'),
		array('value' => 'CC', 'name' => 'Cocos (Keeling) Islands'),
		array('value' => 'CO', 'name' => 'Colombia'),
		array('value' => 'KM', 'name' => 'Comoros'),
		array('value' => 'CD', 'name' => 'Congo (DRC)'),
		array('value' => 'CG', 'name' => 'Congo (Republic)'),
		array('value' => 'CK', 'name' => 'Cook Islands'),
		array('value' => 'CR', 'name' => 'Costa Rica'),
		array('value' => 'HR', 'name' => 'Croatia'),
		array('value' => 'CU', 'name' => 'Cuba'),
		array('value' => 'CW', 'name' => 'Curaçao'),
		array('value' => 'CY', 'name' => 'Cyprus'),
		array('value' => 'CZ', 'name' => 'Czech Republic'),
		array('value' => 'CI', 'name' => 'Côte d’Ivoire'),
		array('value' => 'DK', 'name' => 'Denmark'),
		array('value' => 'DG', 'name' => 'Diego Garcia'),
		array('value' => 'DJ', 'name' => 'Djibouti'),
		array('value' => 'DM', 'name' => 'Dominica'),
		array('value' => 'DO', 'name' => 'Dominican Republic'),
		array('value' => 'EC', 'name' => 'Ecuador'),
		array('value' => 'EG', 'name' => 'Egypt'),
		array('value' => 'SV', 'name' => 'El Salvador'),
		array('value' => 'GQ', 'name' => 'Equatorial Guinea'),
		array('value' => 'ER', 'name' => 'Eritrea'),
		array('value' => 'EE', 'name' => 'Estonia'),
		array('value' => 'ET', 'name' => 'Ethiopia'),
		array('value' => 'FK', 'name' => 'Falkland Islands (Islas Malvinas)'),
		array('value' => 'FO', 'name' => 'Faroe Islands'),
		array('value' => 'FJ', 'name' => 'Fiji'),
		array('value' => 'FI', 'name' => 'Finland'),
		array('value' => 'FR', 'name' => 'France'),
		array('value' => 'GF', 'name' => 'French Guiana'),
		array('value' => 'PF', 'name' => 'French Polynesia'),
		array('value' => 'TF', 'name' => 'French Southern Territories'),
		array('value' => 'GA', 'name' => 'Gabon'),
		array('value' => 'GM', 'name' => 'Gambia'),
		array('value' => 'GE', 'name' => 'Georgia'),
		array('value' => 'DE', 'name' => 'Germany'),
		array('value' => 'GH', 'name' => 'Ghana'),
		array('value' => 'GI', 'name' => 'Gibraltar'),
		array('value' => 'GR', 'name' => 'Greece'),
		array('value' => 'GL', 'name' => 'Greenland'),
		array('value' => 'GD', 'name' => 'Grenada'),
		array('value' => 'GP', 'name' => 'Guadeloupe'),
		array('value' => 'GU', 'name' => 'Guam'),
		array('value' => 'GT', 'name' => 'Guatemala'),
		array('value' => 'GG', 'name' => 'Guernsey'),
		array('value' => 'GN', 'name' => 'Guinea'),
		array('value' => 'GW', 'name' => 'Guinea-Bissau'),
		array('value' => 'GY', 'name' => 'Guyana'),
		array('value' => 'HT', 'name' => 'Haiti'),
		array('value' => 'HM', 'name' => 'Heard & McDonald Islands'),
		array('value' => 'HN', 'name' => 'Honduras'),
		array('value' => 'HK', 'name' => 'Hong Kong'),
		array('value' => 'HU', 'name' => 'Hungary'),
		array('value' => 'IS', 'name' => 'Iceland'),
		array('value' => 'IN', 'name' => 'India'),
		array('value' => 'ID', 'name' => 'Indonesia'),
		array('value' => 'IR', 'name' => 'Iran'),
		array('value' => 'IQ', 'name' => 'Iraq'),
		array('value' => 'IE', 'name' => 'Ireland'),
		array('value' => 'IM', 'name' => 'Isle of Man'),
		array('value' => 'IL', 'name' => 'Israel'),
		array('value' => 'IT', 'name' => 'Italy'),
		array('value' => 'JM', 'name' => 'Jamaica'),
		array('value' => 'JP', 'name' => 'Japan'),
		array('value' => 'JE', 'name' => 'Jersey'),
		array('value' => 'JO', 'name' => 'Jordan'),
		array('value' => 'KZ', 'name' => 'Kazakhstan'),
		array('value' => 'KE', 'name' => 'Kenya'),
		array('value' => 'KI', 'name' => 'Kiribati'),
		array('value' => 'XK', 'name' => 'Kosovo'),
		array('value' => 'KW', 'name' => 'Kuwait'),
		array('value' => 'KG', 'name' => 'Kyrgyzstan'),
		array('value' => 'LA', 'name' => 'Laos'),
		array('value' => 'LV', 'name' => 'Latvia'),
		array('value' => 'LB', 'name' => 'Lebanon'),
		array('value' => 'LS', 'name' => 'Lesotho'),
		array('value' => 'LR', 'name' => 'Liberia'),
		array('value' => 'LY', 'name' => 'Libya'),
		array('value' => 'LI', 'name' => 'Liechtenstein'),
		array('value' => 'LT', 'name' => 'Lithuania'),
		array('value' => 'LU', 'name' => 'Luxembourg'),
		array('value' => 'MO', 'name' => 'Macau'),
		array('value' => 'MK', 'name' => 'Macedonia (FYROM)'),
		array('value' => 'MG', 'name' => 'Madagascar'),
		array('value' => 'MW', 'name' => 'Malawi'),
		array('value' => 'MY', 'name' => 'Malaysia'),
		array('value' => 'MV', 'name' => 'Maldives'),
		array('value' => 'ML', 'name' => 'Mali'),
		array('value' => 'MT', 'name' => 'Malta'),
		array('value' => 'MH', 'name' => 'Marshall Islands'),
		array('value' => 'MQ', 'name' => 'Martinique'),
		array('value' => 'MR', 'name' => 'Mauritania'),
		array('value' => 'MU', 'name' => 'Mauritius'),
		array('value' => 'YT', 'name' => 'Mayotte'),
		array('value' => 'MX', 'name' => 'Mexico'),
		array('value' => 'FM', 'name' => 'Micronesia'),
		array('value' => 'MD', 'name' => 'Moldova'),
		array('value' => 'MC', 'name' => 'Monaco'),
		array('value' => 'MN', 'name' => 'Mongolia'),
		array('value' => 'ME', 'name' => 'Montenegro'),
		array('value' => 'MS', 'name' => 'Montserrat'),
		array('value' => 'MA', 'name' => 'Morocco'),
		array('value' => 'MZ', 'name' => 'Mozambique'),
		array('value' => 'MM', 'name' => 'Myanmar (Burma)'),
		array('value' => 'NA', 'name' => 'Namibia'),
		array('value' => 'NR', 'name' => 'Nauru'),
		array('value' => 'NP', 'name' => 'Nepal'),
		array('value' => 'NL', 'name' => 'Netherlands'),
		array('value' => 'NC', 'name' => 'New Caledonia'),
		array('value' => 'NZ', 'name' => 'New Zealand'),
		array('value' => 'NI', 'name' => 'Nicaragua'),
		array('value' => 'NE', 'name' => 'Niger'),
		array('value' => 'NG', 'name' => 'Nigeria'),
		array('value' => 'NU', 'name' => 'Niue'),
		array('value' => 'NF', 'name' => 'Norfolk Island'),
		array('value' => 'KP', 'name' => 'North Korea'),
		array('value' => 'MP', 'name' => 'Northern Mariana Islands'),
		array('value' => 'NO', 'name' => 'Norway'),
		array('value' => 'OM', 'name' => 'Oman'),
		array('value' => 'PK', 'name' => 'Pakistan'),
		array('value' => 'PW', 'name' => 'Palau'),
		array('value' => 'PS', 'name' => 'Palestine'),
		array('value' => 'PA', 'name' => 'Panama'),
		array('value' => 'PG', 'name' => 'Papua New Guinea'),
		array('value' => 'PY', 'name' => 'Paraguay'),
		array('value' => 'PE', 'name' => 'Peru'),
		array('value' => 'PH', 'name' => 'Philippines'),
		array('value' => 'PN', 'name' => 'Pitcairn Islands'),
		array('value' => 'PL', 'name' => 'Poland'),
		array('value' => 'PT', 'name' => 'Portugal'),
		array('value' => 'PR', 'name' => 'Puerto Rico'),
		array('value' => 'QA', 'name' => 'Qatar'),
		array('value' => 'RO', 'name' => 'Romania'),
		array('value' => 'RU', 'name' => 'Russia'),
		array('value' => 'RW', 'name' => 'Rwanda'),
		array('value' => 'RE', 'name' => 'Réunion'),
		array('value' => 'WS', 'name' => 'Samoa'),
		array('value' => 'SM', 'name' => 'San Marino'),
		array('value' => 'SA', 'name' => 'Saudi Arabia'),
		array('value' => 'SN', 'name' => 'Senegal'),
		array('value' => 'RS', 'name' => 'Serbia'),
		array('value' => 'SC', 'name' => 'Seychelles'),
		array('value' => 'SL', 'name' => 'Sierra Leone'),
		array('value' => 'SG', 'name' => 'Singapore'),
		array('value' => 'SX', 'name' => 'Sint Maarten'),
		array('value' => 'SK', 'name' => 'Slovakia'),
		array('value' => 'SI', 'name' => 'Slovenia'),
		array('value' => 'SB', 'name' => 'Solomon Islands'),
		array('value' => 'SO', 'name' => 'Somalia'),
		array('value' => 'ZA', 'name' => 'South Africa'),
		array('value' => 'GS', 'name' => 'South Georgia & South Sandwich Islands'),
		array('value' => 'KR', 'name' => 'South Korea'),
		array('value' => 'SS', 'name' => 'South Sudan'),
		array('value' => 'ES', 'name' => 'Spain'),
		array('value' => 'LK', 'name' => 'Sri Lanka'),
		array('value' => 'BL', 'name' => 'St. Barthélemy'),
		array('value' => 'SH', 'name' => 'St. Helena'),
		array('value' => 'KN', 'name' => 'St. Kitts & Nevis'),
		array('value' => 'LC', 'name' => 'St. Lucia'),
		array('value' => 'MF', 'name' => 'St. Martin'),
		array('value' => 'PM', 'name' => 'St. Pierre & Miquelon'),
		array('value' => 'VC', 'name' => 'St. Vincent & Grenadines'),
		array('value' => 'SD', 'name' => 'Sudan'),
		array('value' => 'SR', 'name' => 'Suriname'),
		array('value' => 'SJ', 'name' => 'Svalbard & Jan Mayen'),
		array('value' => 'SZ', 'name' => 'Swaziland'),
		array('value' => 'SE', 'name' => 'Sweden'),
		array('value' => 'CH', 'name' => 'Switzerland'),
		array('value' => 'SY', 'name' => 'Syria'),
		array('value' => 'ST', 'name' => 'São Tomé & Príncipe'),
		array('value' => 'TW', 'name' => 'Taiwan'),
		array('value' => 'TJ', 'name' => 'Tajikistan'),
		array('value' => 'TZ', 'name' => 'Tanzania'),
		array('value' => 'TH', 'name' => 'Thailand'),
		array('value' => 'TL', 'name' => 'Timor-Leste'),
		array('value' => 'TG', 'name' => 'Togo'),
		array('value' => 'TK', 'name' => 'Tokelau'),
		array('value' => 'TO', 'name' => 'Tonga'),
		array('value' => 'TT', 'name' => 'Trinidad & Tobago'),
		array('value' => 'TA', 'name' => 'Tristan da Cunha'),
		array('value' => 'TN', 'name' => 'Tunisia'),
		array('value' => 'TR', 'name' => 'Turkey'),
		array('value' => 'TM', 'name' => 'Turkmenistan'),
		array('value' => 'TC', 'name' => 'Turks & Caicos Islands'),
		array('value' => 'TV', 'name' => 'Tuvalu'),
		array('value' => 'UM', 'name' => 'U.S. Outlying Islands'),
		array('value' => 'VI', 'name' => 'U.S. Virgin Islands'),
		array('value' => 'UG', 'name' => 'Uganda'),
		array('value' => 'UA', 'name' => 'Ukraine'),
		array('value' => 'AE', 'name' => 'United Arab Emirates'),
		array('value' => 'GB', 'name' => 'United Kingdom'),		
		array('value' => 'UY', 'name' => 'Uruguay'),
		array('value' => 'UZ', 'name' => 'Uzbekistan'),
		array('value' => 'VU', 'name' => 'Vanuatu'),
		array('value' => 'VA', 'name' => 'Vatican City'),
		array('value' => 'VE', 'name' => 'Venezuela'),
		array('value' => 'VN', 'name' => 'Vietnam'),
		array('value' => 'WF', 'name' => 'Wallis & Futuna'),
		array('value' => 'EH', 'name' => 'Western Sahara'),
		array('value' => 'YE', 'name' => 'Yemen'),
		array('value' => 'ZM', 'name' => 'Zambia'),
		array('value' => 'ZW', 'name' => 'Zimbabwe'),
		array('value' => 'AX', 'name' => 'Åland Islands'),
	);
  return $regions;
}

function super_store_language_list(){
	$mapLanguage = array (
	    array('value' => 'en', 'name' => 'English (Default)'),
		array('value' => 'ar', 'name' => 'Arabic'),
		array('value' => 'kn', 'name' => 'Kannada'),
		array('value' => 'bg', 'name' => 'Bulgarian'),
		array('value' => 'ko', 'name' => 'Korean'),
		array('value' => 'bn', 'name' => 'Bengali'),
		array('value' => 'lt', 'name' => 'Lithuanian'),
		array('value' => 'ca', 'name' => 'Catalan'),
		array('value' => 'lv', 'name' => 'Latvian'),
		array('value' => 'cs', 'name' => 'Czech'),
		array('value' => 'ml', 'name' => 'Malayalam'),
		array('value' => 'da', 'name' => 'Danish'),
		array('value' => 'mr', 'name' => 'Marathi'),
		array('value' => 'de', 'name' => 'German'),
		array('value' => 'nl', 'name' => 'Dutch'),
		array('value' => 'el', 'name' => 'Greek'),
		array('value' => 'no', 'name' => 'Norwegian'),
		array('value' => 'pl', 'name' => 'Polish'),
		array('value' => 'en-AU', 'name' => 'English (Australian)'),
		array('value' => 'pt', 'name' => 'Portuguese'),
		array('value' => 'en-GB', 'name' => 'English (Great Britain)'),
		array('value' => 'pt-BR', 'name' => 'Portuguese (Brazil)'),
		array('value' => 'es', 'name' => 'Spanish'),
		array('value' => 'pt-PT', 'name' => 'Portuguese (Portugal)'),
		array('value' => 'eu', 'name' => 'Basque'),
		array('value' => 'ro', 'name' => 'Romanian'),
		array('value' => 'eu', 'name' => 'Basque'),
		array('value' => 'ru', 'name' => 'Russian'),
		array('value' => 'fa', 'name' => 'Farsi'),
		array('value' => 'sk', 'name' => 'Slovak'),
		array('value' => 'fi', 'name' => 'Finnish'),
		array('value' => 'sl', 'name' => 'Slovenian'),
		array('value' => 'fil', 'name' => 'Filipino'),
		array('value' => 'sr', 'name' => 'Serbian'),
		array('value' => 'fr', 'name' => 'French'),
		array('value' => 'sv', 'name' => 'Swedish'),
		array('value' => 'gl', 'name' => 'Galician'),
		array('value' => 'ta', 'name' => 'Tamil'),
		array('value' => 'gu', 'name' => 'Gujarati'),
		array('value' => 'te', 'name' => 'Telugu'),
		array('value' => 'hi', 'name' => 'Hindi'),
		array('value' => 'th', 'name' => 'Thai'),
		array('value' => 'hr', 'name' => 'Croatian'),
		array('value' => 'tl', 'name' => 'Tagalog'),
		array('value' => 'hu', 'name' => 'Hungarian'),
		array('value' => 'tr', 'name' => 'Turkish'),
		array('value' => 'id', 'name' => 'Indonesian'),
		array('value' => 'uk', 'name' => 'Ukrainian'),
		array('value' => 'it', 'name' => 'Italian'),
		array('value' => 'vi', 'name' => 'Vietnamese'),
		array('value' => 'iw', 'name' => 'Hebrew'),
		array('value' => 'zh-CN', 'name' => 'Chinese (Simplified)'),
		array('value' => 'ja', 'name' => 'Japanese'),
		array('value' => 'zh-TW', 'name' => 'Chinese (Traditional)'),
	);
	return $mapLanguage;
}

//********region and language list function end here *********//

function SuperStoreFontFamilly(){
	
$super_font_list = array (
	  array('value' => ' ', 'name' => 'Roboto (Default)'),
	  array('value' => 'ABeeZee', 'name' => 'ABeeZee'),
      array('value' => 'Abel', 'name' => 'Abel'),
      array('value' => 'Abhaya Libre', 'name' => 'Abhaya Libre'),
      array('value' => 'Abril Fatface', 'name' => 'Abril Fatface'),
      array('value' => 'Aclonica', 'name' => 'Aclonica'),
      array('value' => 'Acme', 'name' => 'Acme'),
      array('value' => 'Actor', 'name' => 'Actor'),
      array('value' => 'Adamina', 'name' => 'Adamina'),
      array('value' => 'Advent Pro', 'name' => 'Advent Pro'),
      array('value' => 'Aguafina Script', 'name' => 'Aguafina Script'),
      array('value' => 'Akronim', 'name' => 'Akronim'),
      array('value' => 'Aladin', 'name' => 'Aladin'),
      array('value' => 'Aldrich', 'name' => 'Aldrich'),
      array('value' => 'Alef', 'name' => 'Alef'),
      array('value' => 'Alegreya', 'name' => 'Alegreya'),
      array('value' => 'Alegreya SC', 'name' => 'Alegreya SC'),
      array('value' => 'Alegreya Sans', 'name' => 'Alegreya Sans'),
      array('value' => 'Alegreya Sans SC', 'name' => 'Alegreya Sans SC'),
      array('value' => 'Alex Brush', 'name' => 'Alex Brush'),
      array('value' => 'Alfa Slab One', 'name' => 'Alfa Slab One'),
      array('value' => 'Alice', 'name' => 'Alice'),
      array('value' => 'Alike', 'name' => 'Alike'),
      array('value' => 'Alike Angular', 'name' => 'Alike Angular'),
      array('value' => 'Allan', 'name' => 'Allan'),
      array('value' => 'Allerta', 'name' => 'Allerta'),
      array('value' => 'Allerta Stencil', 'name' => 'Allerta Stencil'),
      array('value' => 'Allura', 'name' => 'Allura'),
      array('value' => 'Almendra', 'name' => 'Almendra'),
      array('value' => 'Almendra Display', 'name' => 'Almendra Display'),
      array('value' => 'Almendra SC', 'name' => 'Almendra SC'),
      array('value' => 'Amarante', 'name' => 'Amarante'),
      array('value' => 'Amaranth', 'name' => 'Amaranth'),
      array('value' => 'Amatic SC', 'name' => 'Amatic SC'),
      array('value' => 'Amatica SC', 'name' => 'Amatica SC'),
      array('value' => 'Amethysta', 'name' => 'Amethysta'),
      array('value' => 'Amiko', 'name' => 'Amiko'),
      array('value' => 'Amiri', 'name' => 'Amiri'),
      array('value' => 'Amita', 'name' => 'Amita'),
      array('value' => 'Anaheim', 'name' => 'Anaheim'),
      array('value' => 'Andada', 'name' => 'Andada'),
      array('value' => 'Andika', 'name' => 'Andika'),
      array('value' => 'Angkor', 'name' => 'Angkor'),
      array('value' => 'Annie Use Your Telescope', 'name' => 'Annie Use Your Telescope'),
      array('value' => 'Anonymous Pro', 'name' => 'Anonymous Pro'),
      array('value' => 'Antic', 'name' => 'Antic'),
      array('value' => 'Antic Didone', 'name' => 'Antic Didone'),
      array('value' => 'Antic Slab', 'name' => 'Antic Slab'),
      array('value' => 'Anton', 'name' => 'Anton'),
      array('value' => 'Arapey', 'name' => 'Arapey'),
      array('value' => 'Arbutus', 'name' => 'Arbutus'),
      array('value' => 'Arbutus Slab', 'name' => 'Arbutus Slab'),
      array('value' => 'Architects Daughter', 'name' => 'Architects Daughter'),
      array('value' => 'Archivo', 'name' => 'Archivo'),
      array('value' => 'Archivo Black', 'name' => 'Archivo Black'),
      array('value' => 'Archivo Narrow', 'name' => 'Archivo Narrow'),
      array('value' => 'Aref Ruqaa', 'name' => 'Aref Ruqaa'),
      array('value' => 'Arima Madurai', 'name' => 'Arima Madurai'),
      array('value' => 'Arimo', 'name' => 'Arimo'),
      array('value' => 'Arizonia', 'name' => 'Arizonia'),
      array('value' => 'Armata', 'name' => 'Armata'),
      array('value' => 'Arsenal', 'name' => 'Arsenal'),
      array('value' => 'Artifika', 'name' => 'Artifika'),
      array('value' => 'Arvo', 'name' => 'Arvo'),
      array('value' => 'Arya', 'name' => 'Arya'),
      array('value' => 'Asap', 'name' => 'Asap'),
      array('value' => 'Asap Condensed', 'name' => 'Asap Condensed'),
      array('value' => 'Asar', 'name' => 'Asar'),
      array('value' => 'Asset', 'name' => 'Asset'),
      array('value' => 'Assistant', 'name' => 'Assistant'),
      array('value' => 'Astloch', 'name' => 'Astloch'),
      array('value' => 'Asul', 'name' => 'Asul'),
      array('value' => 'Athiti', 'name' => 'Athiti'),
      array('value' => 'Atma', 'name' => 'Atma'),
      array('value' => 'Atomic Age', 'name' => 'Atomic Age'),
      array('value' => 'Aubrey', 'name' => 'Aubrey'),
      array('value' => 'Audiowide', 'name' => 'Audiowide'),
      array('value' => 'Autour One', 'name' => 'Autour One'),
      array('value' => 'Average', 'name' => 'Average'),
      array('value' => 'Average Sans', 'name' => 'Average Sans'),
      array('value' => 'Averia Gruesa Libre', 'name' => 'Averia Gruesa Libre'),
      array('value' => 'Averia Libre', 'name' => 'Averia Libre'),
      array('value' => 'Averia Sans Libre', 'name' => 'Averia Sans Libre'),
      array('value' => 'Averia Serif Libre', 'name' => 'Averia Serif Libre'),
      array('value' => 'Bad Script', 'name' => 'Bad Script'),
      array('value' => 'Bahiana', 'name' => 'Bahiana'),
      array('value' => 'Baloo', 'name' => 'Baloo'),
      array('value' => 'Baloo Bhai', 'name' => 'Baloo Bhai'),
      array('value' => 'Baloo Bhaijaan', 'name' => 'Baloo Bhaijaan'),
      array('value' => 'Baloo Bhaina', 'name' => 'Baloo Bhaina'),
      array('value' => 'Baloo Chettan', 'name' => 'Baloo Chettan'),
      array('value' => 'Baloo Da', 'name' => 'Baloo Da'),
      array('value' => 'Baloo Paaji', 'name' => 'Baloo Paaji'),
      array('value' => 'Baloo Tamma', 'name' => 'Baloo Tamma'),
      array('value' => 'Baloo Tammudu', 'name' => 'Baloo Tammudu'),
      array('value' => 'Baloo Thambi', 'name' => 'Baloo Thambi'),
      array('value' => 'Balthazar', 'name' => 'Balthazar'),
      array('value' => 'Bangers', 'name' => 'Bangers'),
      array('value' => 'Barrio', 'name' => 'Barrio'),
      array('value' => 'Basic', 'name' => 'Basic'),
      array('value' => 'Battambang', 'name' => 'Battambang'),
      array('value' => 'Baumans', 'name' => 'Baumans'),
      array('value' => 'Bayon', 'name' => 'Bayon'),
      array('value' => 'Belgrano', 'name' => 'Belgrano'),
      array('value' => 'Bellefair', 'name' => 'Bellefair'),
      array('value' => 'Belleza', 'name' => 'Belleza'),
      array('value' => 'BenchNine', 'name' => 'BenchNine'),
      array('value' => 'Bentham', 'name' => 'Bentham'),
      array('value' => 'Berkshire Swash', 'name' => 'Berkshire Swash'),
      array('value' => 'Bevan', 'name' => 'Bevan'),
      array('value' => 'Bigelow Rules', 'name' => 'Bigelow Rules'),
      array('value' => 'Bigshot One', 'name' => 'Bigshot One'),
      array('value' => 'Bilbo', 'name' => 'Bilbo'),
      array('value' => 'Bilbo Swash Caps', 'name' => 'Bilbo Swash Caps'),
      array('value' => 'BioRhyme', 'name' => 'BioRhyme'),
      array('value' => 'BioRhyme Expanded', 'name' => 'BioRhyme Expanded'),
      array('value' => 'Biryani', 'name' => 'Biryani'),
      array('value' => 'Bitter', 'name' => 'Bitter'),
      array('value' => 'Black Ops One', 'name' => 'Black Ops One'),
      array('value' => 'Bokor', 'name' => 'Bokor'),
      array('value' => 'Bonbon', 'name' => 'Bonbon'),
      array('value' => 'Boogaloo', 'name' => 'Boogaloo'),
      array('value' => 'Bowlby One', 'name' => 'Bowlby One'),
      array('value' => 'Bowlby One SC', 'name' => 'Bowlby One SC'),
      array('value' => 'Brawler', 'name' => 'Brawler'),
      array('value' => 'Bree Serif', 'name' => 'Bree Serif'),
      array('value' => 'Bubblegum Sans', 'name' => 'Bubblegum Sans'),
      array('value' => 'Bubbler One', 'name' => 'Bubbler One'),
      array('value' => 'Buda', 'name' => 'Buda'),
      array('value' => 'Buenard', 'name' => 'Buenard'),
      array('value' => 'Bungee', 'name' => 'Bungee'),
      array('value' => 'Bungee Hairline', 'name' => 'Bungee Hairline'),
      array('value' => 'Bungee Inline', 'name' => 'Bungee Inline'),
      array('value' => 'Bungee Outline', 'name' => 'Bungee Outline'),
      array('value' => 'Bungee Shade', 'name' => 'Bungee Shade'),
      array('value' => 'Butcherman', 'name' => 'Butcherman'),
      array('value' => 'Butterfly Kids', 'name' => 'Butterfly Kids'),
      array('value' => 'Cabin', 'name' => 'Cabin'),
      array('value' => 'Cabin Condensed', 'name' => 'Cabin Condensed'),
      array('value' => 'Cabin Sketch', 'name' => 'Cabin Sketch'),
      array('value' => 'Caesar Dressing', 'name' => 'Caesar Dressing'),
      array('value' => 'Cagliostro', 'name' => 'Cagliostro'),
      array('value' => 'Cairo', 'name' => 'Cairo'),
      array('value' => 'Calligraffitti', 'name' => 'Calligraffitti'),
      array('value' => 'Cambay', 'name' => 'Cambay'),
      array('value' => 'Cambo', 'name' => 'Cambo'),
      array('value' => 'Candal', 'name' => 'Candal'),
      array('value' => 'Cantarell', 'name' => 'Cantarell'),
      array('value' => 'Cantata One', 'name' => 'Cantata One'),
      array('value' => 'Cantora One', 'name' => 'Cantora One'),
      array('value' => 'Capriola', 'name' => 'Capriola'),
      array('value' => 'Cardo', 'name' => 'Cardo'),
      array('value' => 'Carme', 'name' => 'Carme'),
      array('value' => 'Carrois Gothic', 'name' => 'Carrois Gothic'),
      array('value' => 'Carrois Gothic SC', 'name' => 'Carrois Gothic SC'),
      array('value' => 'Carter One', 'name' => 'Carter One'),
      array('value' => 'Catamaran', 'name' => 'Catamaran'),
      array('value' => 'Caudex', 'name' => 'Caudex'),
      array('value' => 'Caveat', 'name' => 'Caveat'),
      array('value' => 'Caveat Brush', 'name' => 'Caveat Brush'),
      array('value' => 'Cedarville Cursive', 'name' => 'Cedarville Cursive'),
      array('value' => 'Ceviche One', 'name' => 'Ceviche One'),
      array('value' => 'Changa', 'name' => 'Changa'),
      array('value' => 'Changa One', 'name' => 'Changa One'),
      array('value' => 'Chango', 'name' => 'Chango'),
      array('value' => 'Chathura', 'name' => 'Chathura'),
      array('value' => 'Chau Philomene One', 'name' => 'Chau Philomene One'),
      array('value' => 'Chela One', 'name' => 'Chela One'),
      array('value' => 'Chelsea Market', 'name' => 'Chelsea Market'),
      array('value' => 'Chenla', 'name' => 'Chenla'),
      array('value' => 'Cherry Cream Soda', 'name' => 'Cherry Cream Soda'),
      array('value' => 'Cherry Swash', 'name' => 'Cherry Swash'),
      array('value' => 'Chewy', 'name' => 'Chewy'),
      array('value' => 'Chicle', 'name' => 'Chicle'),
      array('value' => 'Chivo', 'name' => 'Chivo'),
      array('value' => 'Chonburi', 'name' => 'Chonburi'),
      array('value' => 'Cinzel', 'name' => 'Cinzel'),
      array('value' => 'Cinzel Decorative', 'name' => 'Cinzel Decorative'),
      array('value' => 'Clicker Script', 'name' => 'Clicker Script'),
      array('value' => 'Coda', 'name' => 'Coda'),
      array('value' => 'Coda Caption', 'name' => 'Coda Caption'),
      array('value' => 'Codystar', 'name' => 'Codystar'),
      array('value' => 'Coiny', 'name' => 'Coiny'),
      array('value' => 'Combo', 'name' => 'Combo'),
      array('value' => 'Comfortaa', 'name' => 'Comfortaa'),
      array('value' => 'Coming Soon', 'name' => 'Coming Soon'),
      array('value' => 'Concert One', 'name' => 'Concert One'),
      array('value' => 'Condiment', 'name' => 'Condiment'),
      array('value' => 'Content', 'name' => 'Content'),
      array('value' => 'Contrail One', 'name' => 'Contrail One'),
      array('value' => 'Convergence', 'name' => 'Convergence'),
      array('value' => 'Cookie', 'name' => 'Cookie'),
      array('value' => 'Copse', 'name' => 'Copse'),
      array('value' => 'Corben', 'name' => 'Corben'),
      array('value' => 'Cormorant', 'name' => 'Cormorant'),
      array('value' => 'Cormorant Garamond', 'name' => 'Cormorant Garamond'),
      array('value' => 'Cormorant Infant', 'name' => 'Cormorant Infant'),
      array('value' => 'Cormorant SC', 'name' => 'Cormorant SC'),
      array('value' => 'Cormorant Unicase', 'name' => 'Cormorant Unicase'),
      array('value' => 'Cormorant Upright', 'name' => 'Cormorant Upright'),
      array('value' => 'Courgette', 'name' => 'Courgette'),
      array('value' => 'Cousine', 'name' => 'Cousine'),
      array('value' => 'Coustard', 'name' => 'Coustard'),
      array('value' => 'Covered By Your Grace', 'name' => 'Covered By Your Grace'),
      array('value' => 'Crafty Girls', 'name' => 'Crafty Girls'),
      array('value' => 'Creepster', 'name' => 'Creepster'),
      array('value' => 'Crete Round', 'name' => 'Crete Round'),
      array('value' => 'Crimson Text', 'name' => 'Crimson Text'),
      array('value' => 'Croissant One', 'name' => 'Croissant One'),
      array('value' => 'Crushed', 'name' => 'Crushed'),
      array('value' => 'Cuprum', 'name' => 'Cuprum'),
      array('value' => 'Cutive', 'name' => 'Cutive'),
      array('value' => 'Cutive Mono', 'name' => 'Cutive Mono'),
      array('value' => 'Damion', 'name' => 'Damion'),
      array('value' => 'Dancing Script', 'name' => 'Dancing Script'),
      array('value' => 'Dangrek', 'name' => 'Dangrek'),
      array('value' => 'David Libre', 'name' => 'David Libre'),
      array('value' => 'Dawning of a New Day', 'name' => 'Dawning of a New Day'),
      array('value' => 'Days One', 'name' => 'Days One'),
      array('value' => 'Dekko', 'name' => 'Dekko'),
      array('value' => 'Delius', 'name' => 'Delius'),
      array('value' => 'Delius Swash Caps', 'name' => 'Delius Swash Caps'),
      array('value' => 'Delius Unicase', 'name' => 'Delius Unicase'),
      array('value' => 'Della Respira', 'name' => 'Della Respira'),
      array('value' => 'Denk One', 'name' => 'Denk One'),
      array('value' => 'Devonshire', 'name' => 'Devonshire'),
      array('value' => 'Dhurjati', 'name' => 'Dhurjati'),
      array('value' => 'Didact Gothic', 'name' => 'Didact Gothic'),
      array('value' => 'Diplomata', 'name' => 'Diplomata'),
      array('value' => 'Diplomata SC', 'name' => 'Diplomata SC'),
      array('value' => 'Domine', 'name' => 'Domine'),
      array('value' => 'Donegal One', 'name' => 'Donegal One'),
      array('value' => 'Doppio One', 'name' => 'Doppio One'),
      array('value' => 'Dorsa', 'name' => 'Dorsa'),
      array('value' => 'Dosis', 'name' => 'Dosis'),
      array('value' => 'Dr Sugiyama', 'name' => 'Dr Sugiyama'),
      array('value' => 'Droid Sans', 'name' => 'Droid Sans'),
      array('value' => 'Droid Sans Mono', 'name' => 'Droid Sans Mono'),
      array('value' => 'Droid Serif', 'name' => 'Droid Serif'),
      array('value' => 'Duru Sans', 'name' => 'Duru Sans'),
      array('value' => 'Dynalight', 'name' => 'Dynalight'),
      array('value' => 'EB Garamond', 'name' => 'EB Garamond'),
      array('value' => 'Eagle Lake', 'name' => 'Eagle Lake'),
      array('value' => 'Eater', 'name' => 'Eater'),
      array('value' => 'Economica', 'name' => 'Economica'),
      array('value' => 'Eczar', 'name' => 'Eczar'),
      array('value' => 'El Messiri', 'name' => 'El Messiri'),
      array('value' => 'Electrolize', 'name' => 'Electrolize'),
      array('value' => 'Elsie', 'name' => 'Elsie'),
      array('value' => 'Elsie Swash Caps', 'name' => 'Elsie Swash Caps'),
      array('value' => 'Emblema One', 'name' => 'Emblema One'),
      array('value' => 'Emilys Candy', 'name' => 'Emilys Candy'),
      array('value' => 'Encode Sans', 'name' => 'Encode Sans'),
      array('value' => 'Encode Sans Condensed', 'name' => 'Encode Sans Condensed'),
      array('value' => 'Encode Sans Expanded', 'name' => 'Encode Sans Expanded'),
      array('value' => 'Encode Sans Semi Condensed', 'name' => 'Encode Sans Semi Condensed'),
      array('value' => 'Encode Sans Semi Expanded', 'name' => 'Encode Sans Semi Expanded'),
      array('value' => 'Engagement', 'name' => 'Engagement'),
      array('value' => 'Englebert', 'name' => 'Englebert'),
      array('value' => 'Enriqueta', 'name' => 'Enriqueta'),
      array('value' => 'Erica One', 'name' => 'Erica One'),
      array('value' => 'Esteban', 'name' => 'Esteban'),
      array('value' => 'Euphoria Script', 'name' => 'Euphoria Script'),
      array('value' => 'Ewert', 'name' => 'Ewert'),
      array('value' => 'Exo', 'name' => 'Exo'),
      array('value' => 'Exo 2', 'name' => 'Exo 2'),
      array('value' => 'Expletus Sans', 'name' => 'Expletus Sans'),
      array('value' => 'Fanwood Text', 'name' => 'Fanwood Text'),
      array('value' => 'Farsan', 'name' => 'Farsan'),
      array('value' => 'Fascinate', 'name' => 'Fascinate'),
      array('value' => 'Fascinate Inline', 'name' => 'Fascinate Inline'),
      array('value' => 'Faster One', 'name' => 'Faster One'),
      array('value' => 'Fasthand', 'name' => 'Fasthand'),
      array('value' => 'Fauna One', 'name' => 'Fauna One'),
      array('value' => 'Faustina', 'name' => 'Faustina'),
      array('value' => 'Federant', 'name' => 'Federant'),
      array('value' => 'Federo', 'name' => 'Federo'),
      array('value' => 'Felipa', 'name' => 'Felipa'),
      array('value' => 'Fenix', 'name' => 'Fenix'),
      array('value' => 'Finger Paint', 'name' => 'Finger Paint'),
      array('value' => 'Fira Mono', 'name' => 'Fira Mono'),
      array('value' => 'Fira Sans', 'name' => 'Fira Sans'),
      array('value' => 'Fira Sans Condensed', 'name' => 'Fira Sans Condensed'),
      array('value' => 'Fira Sans Extra Condensed', 'name' => 'Fira Sans Extra Condensed'),
      array('value' => 'Fjalla One', 'name' => 'Fjalla One'),
      array('value' => 'Fjord One', 'name' => 'Fjord One'),
      array('value' => 'Flamenco', 'name' => 'Flamenco'),
      array('value' => 'Flavors', 'name' => 'Flavors'),
      array('value' => 'Fondamento', 'name' => 'Fondamento'),
      array('value' => 'Fontdiner Swanky', 'name' => 'Fontdiner Swanky'),
      array('value' => 'Forum', 'name' => 'Forum'),
      array('value' => 'Francois One', 'name' => 'Francois One'),
      array('value' => 'Frank Ruhl Libre', 'name' => 'Frank Ruhl Libre'),
      array('value' => 'Freckle Face', 'name' => 'Freckle Face'),
      array('value' => 'Fredericka the Great', 'name' => 'Fredericka the Great'),
      array('value' => 'Fredoka One', 'name' => 'Fredoka One'),
      array('value' => 'Freehand', 'name' => 'Freehand'),
      array('value' => 'Fresca', 'name' => 'Fresca'),
      array('value' => 'Frijole', 'name' => 'Frijole'),
      array('value' => 'Fruktur', 'name' => 'Fruktur'),
      array('value' => 'Fugaz One', 'name' => 'Fugaz One'),
      array('value' => 'GFS Didot', 'name' => 'GFS Didot'),
      array('value' => 'GFS Neohellenic', 'name' => 'GFS Neohellenic'),
      array('value' => 'Gabriela', 'name' => 'Gabriela'),
      array('value' => 'Gafata', 'name' => 'Gafata'),
      array('value' => 'Galada', 'name' => 'Galada'),
      array('value' => 'Galdeano', 'name' => 'Galdeano'),
      array('value' => 'Galindo', 'name' => 'Galindo'),
      array('value' => 'Gentium Basic', 'name' => 'Gentium Basic'),
      array('value' => 'Gentium Book Basic', 'name' => 'Gentium Book Basic'),
      array('value' => 'Geo', 'name' => 'Geo'),
      array('value' => 'Geostar', 'name' => 'Geostar'),
      array('value' => 'Geostar Fill', 'name' => 'Geostar Fill'),
      array('value' => 'Germania One', 'name' => 'Germania One'),
      array('value' => 'Gidugu', 'name' => 'Gidugu'),
      array('value' => 'Gilda Display', 'name' => 'Gilda Display'),
      array('value' => 'Give You Glory', 'name' => 'Give You Glory'),
      array('value' => 'Glass Antiqua', 'name' => 'Glass Antiqua'),
      array('value' => 'Glegoo', 'name' => 'Glegoo'),
      array('value' => 'Gloria Hallelujah', 'name' => 'Gloria Hallelujah'),
      array('value' => 'Goblin One', 'name' => 'Goblin One'),
      array('value' => 'Gochi Hand', 'name' => 'Gochi Hand'),
      array('value' => 'Gorditas', 'name' => 'Gorditas'),
      array('value' => 'Goudy Bookletter 1911', 'name' => 'Goudy Bookletter 1911'),
      array('value' => 'Graduate', 'name' => 'Graduate'),
      array('value' => 'Grand Hotel', 'name' => 'Grand Hotel'),
      array('value' => 'Gravitas One', 'name' => 'Gravitas One'),
      array('value' => 'Great Vibes', 'name' => 'Great Vibes'),
      array('value' => 'Griffy', 'name' => 'Griffy'),
      array('value' => 'Gruppo', 'name' => 'Gruppo'),
      array('value' => 'Gudea', 'name' => 'Gudea'),
      array('value' => 'Gurajada', 'name' => 'Gurajada'),
      array('value' => 'Habibi', 'name' => 'Habibi'),
      array('value' => 'Halant', 'name' => 'Halant'),
      array('value' => 'Hammersmith One', 'name' => 'Hammersmith One'),
      array('value' => 'Hanalei', 'name' => 'Hanalei'),
      array('value' => 'Hanalei Fill', 'name' => 'Hanalei Fill'),
      array('value' => 'Handlee', 'name' => 'Handlee'),
      array('value' => 'Hanuman', 'name' => 'Hanuman'),
      array('value' => 'Happy Monkey', 'name' => 'Happy Monkey'),
      array('value' => 'Harmattan', 'name' => 'Harmattan'),
      array('value' => 'Headland One', 'name' => 'Headland One'),
      array('value' => 'Heebo', 'name' => 'Heebo'),
      array('value' => 'Henny Penny', 'name' => 'Henny Penny'),
      array('value' => 'Herr Von Muellerhoff', 'name' => 'Herr Von Muellerhoff'),
      array('value' => 'Hind', 'name' => 'Hind'),
      array('value' => 'Hind Guntur', 'name' => 'Hind Guntur'),
      array('value' => 'Hind Madurai', 'name' => 'Hind Madurai'),
      array('value' => 'Hind Siliguri', 'name' => 'Hind Siliguri'),
      array('value' => 'Hind Vadodara', 'name' => 'Hind Vadodara'),
      array('value' => 'Holtwood One SC', 'name' => 'Holtwood One SC'),
      array('value' => 'Homemade Apple', 'name' => 'Homemade Apple'),
      array('value' => 'Homenaje', 'name' => 'Homenaje'),
      array('value' => 'IM Fell DW Pica', 'name' => 'IM Fell DW Pica'),
      array('value' => 'IM Fell DW Pica SC', 'name' => 'IM Fell DW Pica SC'),
      array('value' => 'IM Fell Double Pica', 'name' => 'IM Fell Double Pica'),
      array('value' => 'IM Fell Double Pica SC', 'name' => 'IM Fell Double Pica SC'),
      array('value' => 'IM Fell English', 'name' => 'IM Fell English'),
      array('value' => 'IM Fell English SC', 'name' => 'IM Fell English SC'),
      array('value' => 'IM Fell French Canon', 'name' => 'IM Fell French Canon'),
      array('value' => 'IM Fell French Canon SC', 'name' => 'IM Fell French Canon SC'),
      array('value' => 'IM Fell Great Primer', 'name' => 'IM Fell Great Primer'),
      array('value' => 'IM Fell Great Primer SC', 'name' => 'IM Fell Great Primer SC'),
      array('value' => 'Iceberg', 'name' => 'Iceberg'),
      array('value' => 'Iceland', 'name' => 'Iceland'),
      array('value' => 'Imprima', 'name' => 'Imprima'),
      array('value' => 'Inconsolata', 'name' => 'Inconsolata'),
      array('value' => 'Inder', 'name' => 'Inder'),
      array('value' => 'Indie Flower', 'name' => 'Indie Flower'),
      array('value' => 'Inika', 'name' => 'Inika'),
      array('value' => 'Inknut Antiqua', 'name' => 'Inknut Antiqua'),
      array('value' => 'Irish Grover', 'name' => 'Irish Grover'),
      array('value' => 'Istok Web', 'name' => 'Istok Web'),
      array('value' => 'Italiana', 'name' => 'Italiana'),
      array('value' => 'Italianno', 'name' => 'Italianno'),
      array('value' => 'Itim', 'name' => 'Itim'),
      array('value' => 'Jacques Francois', 'name' => 'Jacques Francois'),
      array('value' => 'Jacques Francois Shadow', 'name' => 'Jacques Francois Shadow'),
      array('value' => 'Jaldi', 'name' => 'Jaldi'),
      array('value' => 'Jim Nightshade', 'name' => 'Jim Nightshade'),
      array('value' => 'Jockey One', 'name' => 'Jockey One'),
      array('value' => 'Jolly Lodger', 'name' => 'Jolly Lodger'),
      array('value' => 'Jomhuria', 'name' => 'Jomhuria'),
      array('value' => 'Josefin Sans', 'name' => 'Josefin Sans'),
      array('value' => 'Josefin Slab', 'name' => 'Josefin Slab'),
      array('value' => 'Joti One', 'name' => 'Joti One'),
      array('value' => 'Judson', 'name' => 'Judson'),
      array('value' => 'Julee', 'name' => 'Julee'),
      array('value' => 'Julius Sans One', 'name' => 'Julius Sans One'),
      array('value' => 'Junge', 'name' => 'Junge'),
      array('value' => 'Jura', 'name' => 'Jura'),
      array('value' => 'Just Another Hand', 'name' => 'Just Another Hand'),
      array('value' => 'Just Me Again Down Here', 'name' => 'Just Me Again Down Here'),
      array('value' => 'Kadwa', 'name' => 'Kadwa'),
      array('value' => 'Kalam', 'name' => 'Kalam'),
      array('value' => 'Kameron', 'name' => 'Kameron'),
      array('value' => 'Kanit', 'name' => 'Kanit'),
      array('value' => 'Kantumruy', 'name' => 'Kantumruy'),
      array('value' => 'Karla', 'name' => 'Karla'),
      array('value' => 'Karma', 'name' => 'Karma'),
      array('value' => 'Katibeh', 'name' => 'Katibeh'),
      array('value' => 'Kaushan Script', 'name' => 'Kaushan Script'),
      array('value' => 'Kavivanar', 'name' => 'Kavivanar'),
      array('value' => 'Kavoon', 'name' => 'Kavoon'),
      array('value' => 'Kdam Thmor', 'name' => 'Kdam Thmor'),
      array('value' => 'Keania One', 'name' => 'Keania One'),
      array('value' => 'Kelly Slab', 'name' => 'Kelly Slab'),
      array('value' => 'Kenia', 'name' => 'Kenia'),
      array('value' => 'Khand', 'name' => 'Khand'),
      array('value' => 'Khmer', 'name' => 'Khmer'),
      array('value' => 'Khula', 'name' => 'Khula'),
      array('value' => 'Kite One', 'name' => 'Kite One'),
      array('value' => 'Knewave', 'name' => 'Knewave'),
      array('value' => 'Kotta One', 'name' => 'Kotta One'),
      array('value' => 'Koulen', 'name' => 'Koulen'),
      array('value' => 'Kranky', 'name' => 'Kranky'),
      array('value' => 'Kreon', 'name' => 'Kreon'),
      array('value' => 'Kristi', 'name' => 'Kristi'),
      array('value' => 'Krona One', 'name' => 'Krona One'),
      array('value' => 'Kumar One', 'name' => 'Kumar One'),
      array('value' => 'Kumar One Outline', 'name' => 'Kumar One Outline'),
      array('value' => 'Kurale', 'name' => 'Kurale'),
      array('value' => 'La Belle Aurore', 'name' => 'La Belle Aurore'),
      array('value' => 'Laila', 'name' => 'Laila'),
      array('value' => 'Lakki Reddy', 'name' => 'Lakki Reddy'),
      array('value' => 'Lalezar', 'name' => 'Lalezar'),
      array('value' => 'Lancelot', 'name' => 'Lancelot'),
      array('value' => 'Lateef', 'name' => 'Lateef'),
      array('value' => 'Lato', 'name' => 'Lato'),
      array('value' => 'League Script', 'name' => 'League Script'),
      array('value' => 'Leckerli One', 'name' => 'Leckerli One'),
      array('value' => 'Ledger', 'name' => 'Ledger'),
      array('value' => 'Lekton', 'name' => 'Lekton'),
      array('value' => 'Lemon', 'name' => 'Lemon'),
      array('value' => 'Lemonada', 'name' => 'Lemonada'),
      array('value' => 'Libre Barcode 128', 'name' => 'Libre Barcode 128'),
      array('value' => 'Libre Barcode 128 Text', 'name' => 'Libre Barcode 128 Text'),
      array('value' => 'Libre Barcode 39', 'name' => 'Libre Barcode 39'),
      array('value' => 'Libre Barcode 39 Extended', 'name' => 'Libre Barcode 39 Extended'),
      array('value' => 'Libre Barcode 39 Extended Text', 'name' => 'Libre Barcode 39 Extended Text'),
      array('value' => 'Libre Barcode 39 Text', 'name' => 'Libre Barcode 39 Text'),
      array('value' => 'Libre Baskerville', 'name' => 'Libre Baskerville'),
      array('value' => 'Libre Franklin', 'name' => 'Libre Franklin'),
      array('value' => 'Life Savers', 'name' => 'Life Savers'),
      array('value' => 'Lilita One', 'name' => 'Lilita One'),
      array('value' => 'Lily Script One', 'name' => 'Lily Script One'),
      array('value' => 'Limelight', 'name' => 'Limelight'),
      array('value' => 'Linden Hill', 'name' => 'Linden Hill'),
      array('value' => 'Lobster', 'name' => 'Lobster'),
      array('value' => 'Lobster Two', 'name' => 'Lobster Two'),
      array('value' => 'Londrina Outline', 'name' => 'Londrina Outline'),
      array('value' => 'Londrina Shadow', 'name' => 'Londrina Shadow'),
      array('value' => 'Londrina Sketch', 'name' => 'Londrina Sketch'),
      array('value' => 'Londrina Solid', 'name' => 'Londrina Solid'),
      array('value' => 'Lora', 'name' => 'Lora'),
      array('value' => 'Love Ya Like A Sister', 'name' => 'Love Ya Like A Sister'),
      array('value' => 'Loved by the King', 'name' => 'Loved by the King'),
      array('value' => 'Lovers Quarrel', 'name' => 'Lovers Quarrel'),
      array('value' => 'Luckiest Guy', 'name' => 'Luckiest Guy'),
      array('value' => 'Lusitana', 'name' => 'Lusitana'),
      array('value' => 'Lustria', 'name' => 'Lustria'),
      array('value' => 'Macondo', 'name' => 'Macondo'),
      array('value' => 'Macondo Swash Caps', 'name' => 'Macondo Swash Caps'),
      array('value' => 'Mada', 'name' => 'Mada'),
      array('value' => 'Magra', 'name' => 'Magra'),
      array('value' => 'Maiden Orange', 'name' => 'Maiden Orange'),
      array('value' => 'Maitree', 'name' => 'Maitree'),
      array('value' => 'Mako', 'name' => 'Mako'),
      array('value' => 'Mallanna', 'name' => 'Mallanna'),
      array('value' => 'Mandali', 'name' => 'Mandali'),
      array('value' => 'Manuale', 'name' => 'Manuale'),
      array('value' => 'Marcellus', 'name' => 'Marcellus'),
      array('value' => 'Marcellus SC', 'name' => 'Marcellus SC'),
      array('value' => 'Marck Script', 'name' => 'Marck Script'),
      array('value' => 'Margarine', 'name' => 'Margarine'),
      array('value' => 'Marko One', 'name' => 'Marko One'),
      array('value' => 'Marmelad', 'name' => 'Marmelad'),
      array('value' => 'Martel', 'name' => 'Martel'),
      array('value' => 'Martel Sans', 'name' => 'Martel Sans'),
      array('value' => 'Marvel', 'name' => 'Marvel'),
      array('value' => 'Mate', 'name' => 'Mate'),
      array('value' => 'Mate SC', 'name' => 'Mate SC'),
      array('value' => 'Maven Pro', 'name' => 'Maven Pro'),
      array('value' => 'McLaren', 'name' => 'McLaren'),
      array('value' => 'Meddon', 'name' => 'Meddon'),
      array('value' => 'MedievalSharp', 'name' => 'MedievalSharp'),
      array('value' => 'Medula One', 'name' => 'Medula One'),
      array('value' => 'Meera Inimai', 'name' => 'Meera Inimai'),
      array('value' => 'Megrim', 'name' => 'Megrim'),
      array('value' => 'Meie Script', 'name' => 'Meie Script'),
      array('value' => 'Merienda', 'name' => 'Merienda'),
      array('value' => 'Merienda One', 'name' => 'Merienda One'),
      array('value' => 'Merriweather', 'name' => 'Merriweather'),
      array('value' => 'Merriweather Sans', 'name' => 'Merriweather Sans'),
      array('value' => 'Metal', 'name' => 'Metal'),
      array('value' => 'Metal Mania', 'name' => 'Metal Mania'),
      array('value' => 'Metamorphous', 'name' => 'Metamorphous'),
      array('value' => 'Metrophobic', 'name' => 'Metrophobic'),
      array('value' => 'Michroma', 'name' => 'Michroma'),
      array('value' => 'Milonga', 'name' => 'Milonga'),
      array('value' => 'Miltonian', 'name' => 'Miltonian'),
      array('value' => 'Miltonian Tattoo', 'name' => 'Miltonian Tattoo'),
      array('value' => 'Miniver', 'name' => 'Miniver'),
      array('value' => 'Miriam Libre', 'name' => 'Miriam Libre'),
      array('value' => 'Mirza', 'name' => 'Mirza'),
      array('value' => 'Miss Fajardose', 'name' => 'Miss Fajardose'),
      array('value' => 'Mitr', 'name' => 'Mitr'),
      array('value' => 'Modak', 'name' => 'Modak'),
      array('value' => 'Modern Antiqua', 'name' => 'Modern Antiqua'),
      array('value' => 'Mogra', 'name' => 'Mogra'),
      array('value' => 'Molengo', 'name' => 'Molengo'),
      array('value' => 'Molle', 'name' => 'Molle'),
      array('value' => 'Monda', 'name' => 'Monda'),
      array('value' => 'Monofett', 'name' => 'Monofett'),
      array('value' => 'Monoton', 'name' => 'Monoton'),
      array('value' => 'Monsieur La Doulaise', 'name' => 'Monsieur La Doulaise'),
      array('value' => 'Montaga', 'name' => 'Montaga'),
      array('value' => 'Montez', 'name' => 'Montez'),
      array('value' => 'Montserrat', 'name' => 'Montserrat'),
      array('value' => 'Montserrat Alternates', 'name' => 'Montserrat Alternates'),
      array('value' => 'Montserrat Subrayada', 'name' => 'Montserrat Subrayada'),
      array('value' => 'Moul', 'name' => 'Moul'),
      array('value' => 'Moulpali', 'name' => 'Moulpali'),
      array('value' => 'Mountains of Christmas', 'name' => 'Mountains of Christmas'),
      array('value' => 'Mouse Memoirs', 'name' => 'Mouse Memoirs'),
      array('value' => 'Mr Bedfort', 'name' => 'Mr Bedfort'),
      array('value' => 'Mr Dafoe', 'name' => 'Mr Dafoe'),
      array('value' => 'Mr De Haviland', 'name' => 'Mr De Haviland'),
      array('value' => 'Mrs Saint Delafield', 'name' => 'Mrs Saint Delafield'),
      array('value' => 'Mrs Sheppards', 'name' => 'Mrs Sheppards'),
      array('value' => 'Mukta', 'name' => 'Mukta'),
      array('value' => 'Mukta Mahee', 'name' => 'Mukta Mahee'),
      array('value' => 'Mukta Malar', 'name' => 'Mukta Malar'),
      array('value' => 'Mukta Vaani', 'name' => 'Mukta Vaani'),
      array('value' => 'Muli', 'name' => 'Muli'),
      array('value' => 'Mystery Quest', 'name' => 'Mystery Quest'),
      array('value' => 'NTR', 'name' => 'NTR'),
      array('value' => 'Neucha', 'name' => 'Neucha'),
      array('value' => 'Neuton', 'name' => 'Neuton'),
      array('value' => 'New Rocker', 'name' => 'New Rocker'),
      array('value' => 'News Cycle', 'name' => 'News Cycle'),
      array('value' => 'Niconne', 'name' => 'Niconne'),
      array('value' => 'Nixie One', 'name' => 'Nixie One'),
      array('value' => 'Nobile', 'name' => 'Nobile'),
      array('value' => 'Nokora', 'name' => 'Nokora'),
      array('value' => 'Norican', 'name' => 'Norican'),
      array('value' => 'Nosifer', 'name' => 'Nosifer'),
      array('value' => 'Nothing You Could Do', 'name' => 'Nothing You Could Do'),
      array('value' => 'Noticia Text', 'name' => 'Noticia Text'),
      array('value' => 'Noto Sans', 'name' => 'Noto Sans'),
      array('value' => 'Noto Serif', 'name' => 'Noto Serif'),
      array('value' => 'Nova Cut', 'name' => 'Nova Cut'),
      array('value' => 'Nova Flat', 'name' => 'Nova Flat'),
      array('value' => 'Nova Mono', 'name' => 'Nova Mono'),
      array('value' => 'Nova Oval', 'name' => 'Nova Oval'),
      array('value' => 'Nova Round', 'name' => 'Nova Round'),
      array('value' => 'Nova Script', 'name' => 'Nova Script'),
      array('value' => 'Nova Slim', 'name' => 'Nova Slim'),
      array('value' => 'Nova Square', 'name' => 'Nova Square'),
      array('value' => 'Numans', 'name' => 'Numans'),
      array('value' => 'Nunito', 'name' => 'Nunito'),
      array('value' => 'Nunito Sans', 'name' => 'Nunito Sans'),
      array('value' => 'Odor Mean Chey', 'name' => 'Odor Mean Chey'),
      array('value' => 'Offside', 'name' => 'Offside'),
      array('value' => 'Old Standard TT', 'name' => 'Old Standard TT'),
      array('value' => 'Oldenburg', 'name' => 'Oldenburg'),
      array('value' => 'Oleo Script', 'name' => 'Oleo Script'),
      array('value' => 'Oleo Script Swash Caps', 'name' => 'Oleo Script Swash Caps'),
      array('value' => 'Open Sans', 'name' => 'Open Sans'),
      array('value' => 'Open Sans Condensed', 'name' => 'Open Sans Condensed'),
      array('value' => 'Oranienbaum', 'name' => 'Oranienbaum'),
      array('value' => 'Orbitron', 'name' => 'Orbitron'),
      array('value' => 'Oregano', 'name' => 'Oregano'),
      array('value' => 'Orienta', 'name' => 'Orienta'),
      array('value' => 'Original Surfer', 'name' => 'Original Surfer'),
      array('value' => 'Oswald', 'name' => 'Oswald'),
      array('value' => 'Over the Rainbow', 'name' => 'Over the Rainbow'),
      array('value' => 'Overlock', 'name' => 'Overlock'),
      array('value' => 'Overlock SC', 'name' => 'Overlock SC'),
      array('value' => 'Overpass', 'name' => 'Overpass'),
      array('value' => 'Overpass Mono', 'name' => 'Overpass Mono'),
      array('value' => 'Ovo', 'name' => 'Ovo'),
      array('value' => 'Oxygen', 'name' => 'Oxygen'),
      array('value' => 'Oxygen Mono', 'name' => 'Oxygen Mono'),
      array('value' => 'PT Mono', 'name' => 'PT Mono'),
      array('value' => 'PT Sans', 'name' => 'PT Sans'),
      array('value' => 'PT Sans Caption', 'name' => 'PT Sans Caption'),
      array('value' => 'PT Sans Narrow', 'name' => 'PT Sans Narrow'),
      array('value' => 'PT Serif', 'name' => 'PT Serif'),
      array('value' => 'PT Serif Caption', 'name' => 'PT Serif Caption'),
      array('value' => 'Pacifico', 'name' => 'Pacifico'),
      array('value' => 'Padauk', 'name' => 'Padauk'),
      array('value' => 'Palanquin', 'name' => 'Palanquin'),
      array('value' => 'Palanquin Dark', 'name' => 'Palanquin Dark'),
      array('value' => 'Pangolin', 'name' => 'Pangolin'),
      array('value' => 'Paprika', 'name' => 'Paprika'),
      array('value' => 'Parisienne', 'name' => 'Parisienne'),
      array('value' => 'Passero One', 'name' => 'Passero One'),
      array('value' => 'Passion One', 'name' => 'Passion One'),
      array('value' => 'Pathway Gothic One', 'name' => 'Pathway Gothic One'),
      array('value' => 'Patrick Hand', 'name' => 'Patrick Hand'),
      array('value' => 'Patrick Hand SC', 'name' => 'Patrick Hand SC'),
      array('value' => 'Pattaya', 'name' => 'Pattaya'),
      array('value' => 'Patua One', 'name' => 'Patua One'),
      array('value' => 'Pavanam', 'name' => 'Pavanam'),
      array('value' => 'Paytone One', 'name' => 'Paytone One'),
      array('value' => 'Peddana', 'name' => 'Peddana'),
      array('value' => 'Peralta', 'name' => 'Peralta'),
      array('value' => 'Permanent Marker', 'name' => 'Permanent Marker'),
      array('value' => 'Petit Formal Script', 'name' => 'Petit Formal Script'),
      array('value' => 'Petrona', 'name' => 'Petrona'),
      array('value' => 'Philosopher', 'name' => 'Philosopher'),
      array('value' => 'Piedra', 'name' => 'Piedra'),
      array('value' => 'Pinyon Script', 'name' => 'Pinyon Script'),
      array('value' => 'Pirata One', 'name' => 'Pirata One'),
      array('value' => 'Plaster', 'name' => 'Plaster'),
      array('value' => 'Play', 'name' => 'Play'),
      array('value' => 'Playball', 'name' => 'Playball'),
      array('value' => 'Playfair Display', 'name' => 'Playfair Display'),
      array('value' => 'Playfair Display SC', 'name' => 'Playfair Display SC'),
      array('value' => 'Podkova', 'name' => 'Podkova'),
      array('value' => 'Poiret One', 'name' => 'Poiret One'),
      array('value' => 'Poller One', 'name' => 'Poller One'),
      array('value' => 'Poly', 'name' => 'Poly'),
      array('value' => 'Pompiere', 'name' => 'Pompiere'),
      array('value' => 'Pontano Sans', 'name' => 'Pontano Sans'),
      array('value' => 'Poppins', 'name' => 'Poppins'),
      array('value' => 'Port Lligat Sans', 'name' => 'Port Lligat Sans'),
      array('value' => 'Port Lligat Slab', 'name' => 'Port Lligat Slab'),
      array('value' => 'Pragati Narrow', 'name' => 'Pragati Narrow'),
      array('value' => 'Prata', 'name' => 'Prata'),
      array('value' => 'Preahvihear', 'name' => 'Preahvihear'),
      array('value' => 'Press Start 2P', 'name' => 'Press Start 2P'),
      array('value' => 'Pridi', 'name' => 'Pridi'),
      array('value' => 'Princess Sofia', 'name' => 'Princess Sofia'),
      array('value' => 'Prociono', 'name' => 'Prociono'),
      array('value' => 'Prompt', 'name' => 'Prompt'),
      array('value' => 'Prosto One', 'name' => 'Prosto One'),
      array('value' => 'Proza Libre', 'name' => 'Proza Libre'),
      array('value' => 'Puritan', 'name' => 'Puritan'),
      array('value' => 'Purple Purse', 'name' => 'Purple Purse'),
      array('value' => 'Quando', 'name' => 'Quando'),
      array('value' => 'Quantico', 'name' => 'Quantico'),
      array('value' => 'Quattrocento', 'name' => 'Quattrocento'),
      array('value' => 'Quattrocento Sans', 'name' => 'Quattrocento Sans'),
      array('value' => 'Questrial', 'name' => 'Questrial'),
      array('value' => 'Quicksand', 'name' => 'Quicksand'),
      array('value' => 'Quintessential', 'name' => 'Quintessential'),
      array('value' => 'Qwigley', 'name' => 'Qwigley'),
      array('value' => 'Racing Sans One', 'name' => 'Racing Sans One'),
      array('value' => 'Radley', 'name' => 'Radley'),
      array('value' => 'Rajdhani', 'name' => 'Rajdhani'),
      array('value' => 'Rakkas', 'name' => 'Rakkas'),
      array('value' => 'Raleway', 'name' => 'Raleway'),
      array('value' => 'Raleway Dots', 'name' => 'Raleway Dots'),
      array('value' => 'Ramabhadra', 'name' => 'Ramabhadra'),
      array('value' => 'Ramaraja', 'name' => 'Ramaraja'),
      array('value' => 'Rambla', 'name' => 'Rambla'),
      array('value' => 'Rammetto One', 'name' => 'Rammetto One'),
      array('value' => 'Ranchers', 'name' => 'Ranchers'),
      array('value' => 'Rancho', 'name' => 'Rancho'),
      array('value' => 'Ranga', 'name' => 'Ranga'),
      array('value' => 'Rasa', 'name' => 'Rasa'),
      array('value' => 'Rationale', 'name' => 'Rationale'),
      array('value' => 'Ravi Prakash', 'name' => 'Ravi Prakash'),
      array('value' => 'Redressed', 'name' => 'Redressed'),
      array('value' => 'Reem Kufi', 'name' => 'Reem Kufi'),
      array('value' => 'Reenie Beanie', 'name' => 'Reenie Beanie'),
      array('value' => 'Revalia', 'name' => 'Revalia'),
      array('value' => 'Rhodium Libre', 'name' => 'Rhodium Libre'),
      array('value' => 'Ribeye', 'name' => 'Ribeye'),
      array('value' => 'Ribeye Marrow', 'name' => 'Ribeye Marrow'),
      array('value' => 'Righteous', 'name' => 'Righteous'),
      array('value' => 'Risque', 'name' => 'Risque'),
      array('value' => 'Roboto', 'name' => 'Roboto'),
      array('value' => 'Roboto Condensed', 'name' => 'Roboto Condensed'),
      array('value' => 'Roboto Mono', 'name' => 'Roboto Mono'),
      array('value' => 'Roboto Slab', 'name' => 'Roboto Slab'),
      array('value' => 'Rochester', 'name' => 'Rochester'),
      array('value' => 'Rock Salt', 'name' => 'Rock Salt'),
      array('value' => 'Rokkitt', 'name' => 'Rokkitt'),
      array('value' => 'Romanesco', 'name' => 'Romanesco'),
      array('value' => 'Ropa Sans', 'name' => 'Ropa Sans'),
      array('value' => 'Rosario', 'name' => 'Rosario'),
      array('value' => 'Rosarivo', 'name' => 'Rosarivo'),
      array('value' => 'Rouge Script', 'name' => 'Rouge Script'),
      array('value' => 'Rozha One', 'name' => 'Rozha One'),
      array('value' => 'Rubik', 'name' => 'Rubik'),
      array('value' => 'Rubik Mono One', 'name' => 'Rubik Mono One'),
      array('value' => 'Ruda', 'name' => 'Ruda'),
      array('value' => 'Rufina', 'name' => 'Rufina'),
      array('value' => 'Ruge Boogie', 'name' => 'Ruge Boogie'),
      array('value' => 'Ruluko', 'name' => 'Ruluko'),
      array('value' => 'Rum Raisin', 'name' => 'Rum Raisin'),
      array('value' => 'Ruslan Display', 'name' => 'Ruslan Display'),
      array('value' => 'Russo One', 'name' => 'Russo One'),
      array('value' => 'Ruthie', 'name' => 'Ruthie'),
      array('value' => 'Rye', 'name' => 'Rye'),
      array('value' => 'Sacramento', 'name' => 'Sacramento'),
      array('value' => 'Sahitya', 'name' => 'Sahitya'),
      array('value' => 'Sail', 'name' => 'Sail'),
      array('value' => 'Saira', 'name' => 'Saira'),
      array('value' => 'Saira Condensed', 'name' => 'Saira Condensed'),
      array('value' => 'Saira Extra Condensed', 'name' => 'Saira Extra Condensed'),
      array('value' => 'Saira Semi Condensed', 'name' => 'Saira Semi Condensed'),
      array('value' => 'Salsa', 'name' => 'Salsa'),
      array('value' => 'Sanchez', 'name' => 'Sanchez'),
      array('value' => 'Sancreek', 'name' => 'Sancreek'),
      array('value' => 'Sansita', 'name' => 'Sansita'),
      array('value' => 'Sarala', 'name' => 'Sarala'),
      array('value' => 'Sarina', 'name' => 'Sarina'),
      array('value' => 'Sarpanch', 'name' => 'Sarpanch'),
      array('value' => 'Satisfy', 'name' => 'Satisfy'),
      array('value' => 'Scada', 'name' => 'Scada'),
      array('value' => 'Scheherazade', 'name' => 'Scheherazade'),
      array('value' => 'Schoolbell', 'name' => 'Schoolbell'),
      array('value' => 'Scope One', 'name' => 'Scope One'),
      array('value' => 'Seaweed Script', 'name' => 'Seaweed Script'),
      array('value' => 'Secular One', 'name' => 'Secular One'),
      array('value' => 'Sedgwick Ave', 'name' => 'Sedgwick Ave'),
      array('value' => 'Sedgwick Ave Display', 'name' => 'Sedgwick Ave Display'),
      array('value' => 'Sevillana', 'name' => 'Sevillana'),
      array('value' => 'Seymour One', 'name' => 'Seymour One'),
      array('value' => 'Shadows Into Light', 'name' => 'Shadows Into Light'),
      array('value' => 'Shadows Into Light Two', 'name' => 'Shadows Into Light Two'),
      array('value' => 'Shanti', 'name' => 'Shanti'),
      array('value' => 'Share', 'name' => 'Share'),
      array('value' => 'Share Tech', 'name' => 'Share Tech'),
      array('value' => 'Share Tech Mono', 'name' => 'Share Tech Mono'),
      array('value' => 'Shojumaru', 'name' => 'Shojumaru'),
      array('value' => 'Short Stack', 'name' => 'Short Stack'),
      array('value' => 'Shrikhand', 'name' => 'Shrikhand'),
      array('value' => 'Siemreap', 'name' => 'Siemreap'),
      array('value' => 'Sigmar One', 'name' => 'Sigmar One'),
      array('value' => 'Signika', 'name' => 'Signika'),
      array('value' => 'Signika Negative', 'name' => 'Signika Negative'),
      array('value' => 'Simonetta', 'name' => 'Simonetta'),
      array('value' => 'Sintony', 'name' => 'Sintony'),
      array('value' => 'Sirin Stencil', 'name' => 'Sirin Stencil'),
      array('value' => 'Six Caps', 'name' => 'Six Caps'),
      array('value' => 'Skranji', 'name' => 'Skranji'),
      array('value' => 'Slabo 13px', 'name' => 'Slabo 13px'),
      array('value' => 'Slabo 27px', 'name' => 'Slabo 27px'),
      array('value' => 'Slackey', 'name' => 'Slackey'),
      array('value' => 'Smokum', 'name' => 'Smokum'),
      array('value' => 'Smythe', 'name' => 'Smythe'),
      array('value' => 'Sniglet', 'name' => 'Sniglet'),
      array('value' => 'Snippet', 'name' => 'Snippet'),
      array('value' => 'Snowburst One', 'name' => 'Snowburst One'),
      array('value' => 'Sofadi One', 'name' => 'Sofadi One'),
      array('value' => 'Sofia', 'name' => 'Sofia'),
      array('value' => 'Sonsie One', 'name' => 'Sonsie One'),
      array('value' => 'Sorts Mill Goudy', 'name' => 'Sorts Mill Goudy'),
      array('value' => 'Source Code Pro', 'name' => 'Source Code Pro'),
      array('value' => 'Source Sans Pro', 'name' => 'Source Sans Pro'),
      array('value' => 'Source Serif Pro', 'name' => 'Source Serif Pro'),
      array('value' => 'Space Mono', 'name' => 'Space Mono'),
      array('value' => 'Special Elite', 'name' => 'Special Elite'),
      array('value' => 'Spectral', 'name' => 'Spectral'),
      array('value' => 'Spicy Rice', 'name' => 'Spicy Rice'),
      array('value' => 'Spinnaker', 'name' => 'Spinnaker'),
      array('value' => 'Spirax', 'name' => 'Spirax'),
      array('value' => 'Squada One', 'name' => 'Squada One'),
      array('value' => 'Sree Krushnadevaraya', 'name' => 'Sree Krushnadevaraya'),
      array('value' => 'Sriracha', 'name' => 'Sriracha'),
      array('value' => 'Stalemate', 'name' => 'Stalemate'),
      array('value' => 'Stalinist One', 'name' => 'Stalinist One'),
      array('value' => 'Stardos Stencil', 'name' => 'Stardos Stencil'),
      array('value' => 'Stint Ultra Condensed', 'name' => 'Stint Ultra Condensed'),
      array('value' => 'Stint Ultra Expanded', 'name' => 'Stint Ultra Expanded'),
      array('value' => 'Stoke', 'name' => 'Stoke'),
      array('value' => 'Strait', 'name' => 'Strait'),
      array('value' => 'Sue Ellen Francisco', 'name' => 'Sue Ellen Francisco'),
      array('value' => 'Suez One', 'name' => 'Suez One'),
      array('value' => 'Sumana', 'name' => 'Sumana'),
      array('value' => 'Sunshiney', 'name' => 'Sunshiney'),
      array('value' => 'Supermercado One', 'name' => 'Supermercado One'),
      array('value' => 'Sura', 'name' => 'Sura'),
      array('value' => 'Suranna', 'name' => 'Suranna'),
      array('value' => 'Suravaram', 'name' => 'Suravaram'),
      array('value' => 'Suwannaphum', 'name' => 'Suwannaphum'),
      array('value' => 'Swanky and Moo Moo', 'name' => 'Swanky and Moo Moo'),
      array('value' => 'Syncopate', 'name' => 'Syncopate'),
      array('value' => 'Tangerine', 'name' => 'Tangerine'),
      array('value' => 'Taprom', 'name' => 'Taprom'),
      array('value' => 'Tauri', 'name' => 'Tauri'),
      array('value' => 'Taviraj', 'name' => 'Taviraj'),
      array('value' => 'Teko', 'name' => 'Teko'),
      array('value' => 'Telex', 'name' => 'Telex'),
      array('value' => 'Tenali Ramakrishna', 'name' => 'Tenali Ramakrishna'),
      array('value' => 'Tenor Sans', 'name' => 'Tenor Sans'),
      array('value' => 'Text Me One', 'name' => 'Text Me One'),
      array('value' => 'The Girl Next Door', 'name' => 'The Girl Next Door'),
      array('value' => 'Tienne', 'name' => 'Tienne'),
      array('value' => 'Tillana', 'name' => 'Tillana'),
      array('value' => 'Timmana', 'name' => 'Timmana'),
      array('value' => 'Tinos', 'name' => 'Tinos'),
      array('value' => 'Titan One', 'name' => 'Titan One'),
      array('value' => 'Titillium Web', 'name' => 'Titillium Web'),
      array('value' => 'Trade Winds', 'name' => 'Trade Winds'),
      array('value' => 'Trirong', 'name' => 'Trirong'),
      array('value' => 'Trocchi', 'name' => 'Trocchi'),
      array('value' => 'Trochut', 'name' => 'Trochut'),
      array('value' => 'Trykker', 'name' => 'Trykker'),
      array('value' => 'Tulpen One', 'name' => 'Tulpen One'),
      array('value' => 'Ubuntu', 'name' => 'Ubuntu'),
      array('value' => 'Ubuntu Condensed', 'name' => 'Ubuntu Condensed'),
      array('value' => 'Ubuntu Mono', 'name' => 'Ubuntu Mono'),
      array('value' => 'Ultra', 'name' => 'Ultra'),
      array('value' => 'Uncial Antiqua', 'name' => 'Uncial Antiqua'),
      array('value' => 'Underdog', 'name' => 'Underdog'),
      array('value' => 'Unica One', 'name' => 'Unica One'),
      array('value' => 'UnifrakturCook', 'name' => 'UnifrakturCook'),
      array('value' => 'UnifrakturMaguntia', 'name' => 'UnifrakturMaguntia'),
      array('value' => 'Unkempt', 'name' => 'Unkempt'),
      array('value' => 'Unlock', 'name' => 'Unlock'),
      array('value' => 'Unna', 'name' => 'Unna'),
      array('value' => 'VT323', 'name' => 'VT323'),
      array('value' => 'Vampiro One', 'name' => 'Vampiro One'),
      array('value' => 'Varela', 'name' => 'Varela'),
      array('value' => 'Varela Round', 'name' => 'Varela Round'),
      array('value' => 'Vast Shadow', 'name' => 'Vast Shadow'),
      array('value' => 'Vesper Libre', 'name' => 'Vesper Libre'),
      array('value' => 'Vibur', 'name' => 'Vibur'),
      array('value' => 'Vidaloka', 'name' => 'Vidaloka'),
      array('value' => 'Viga', 'name' => 'Viga'),
      array('value' => 'Voces', 'name' => 'Voces'),
      array('value' => 'Volkhov', 'name' => 'Volkhov'),
      array('value' => 'Vollkorn', 'name' => 'Vollkorn'),
      array('value' => 'Voltaire', 'name' => 'Voltaire'),
      array('value' => 'Waiting for the Sunrise', 'name' => 'Waiting for the Sunrise'),
      array('value' => 'Wallpoet', 'name' => 'Wallpoet'),
      array('value' => 'Walter Turncoat', 'name' => 'Walter Turncoat'),
      array('value' => 'Warnes', 'name' => 'Warnes'),
      array('value' => 'Wellfleet', 'name' => 'Wellfleet'),
      array('value' => 'Wendy One', 'name' => 'Wendy One'),
      array('value' => 'Wire One', 'name' => 'Wire One'),
      array('value' => 'Work Sans', 'name' => 'Work Sans'),
      array('value' => 'Yanone Kaffeesatz', 'name' => 'Yanone Kaffeesatz'),
      array('value' => 'Yantramanav', 'name' => 'Yantramanav'),
      array('value' => 'Yatra One', 'name' => 'Yatra One'),
      array('value' => 'Yellowtail', 'name' => 'Yellowtail'),
      array('value' => 'Yeseva One', 'name' => 'Yeseva One'),
      array('value' => 'Yesteryear', 'name' => 'Yesteryear'),
      array('value' => 'Yrsa', 'name' => 'Yrsa'),
      array('value' => 'Zeyada', 'name' => 'Zeyada'),
      array('value' => 'Zilla Slab', 'name' => 'Zilla Slab'),
      array('value' => 'Zilla Slab Highlight', 'name' => 'Zilla Slab Highlight'),
);

return $super_font_list;

}



//********region edit code start*********//
function ssf_wp_single_region_info($value, $colspan, $bgcol) {
	global $ssf_wp_hooks;
	$_GET['edit'] = $value['ssf_wp_region_id']; 
	
	print "<tr style='background-color:$bgcol' id='ssf_wp_tr_data-$value[ssf_wp_region_id]'>";
	$cancel_onclick = "location.href=\"".str_replace("&edit=$_GET[edit]", "",$_SERVER['REQUEST_URI'])."\"";
	print "<td colspan='$colspan'>
	
	
	<div class='input_section'>
	<a name='a$value[ssf_wp_region_id]'></a>
	<form name='manualAddForm'  method='post' enctype='multipart/form-data'>

					<div class='input_title'>
						
						<h3><span class='fa fa-pencil'>&nbsp;</span> Edit Region</h3>
						<div class='clearfix'></div>
					</div>
					<div class='all_options'>
					
					<div class='option_input option_text'>
					<label for='shortname_logo'>
					Region Name</label>
					<input type='text' name='ssf_wp_region_name-$value[ssf_wp_region_id]' id='ssf_wp_region_name-$value[ssf_wp_region_id]' value='$value[ssf_wp_region_name]'>
					<small>Enter Region name</small>
					<div class='clearfix'></div>
					</div>
					
					<div class='option_input option_text'>
					<label for='shortname_logo'>
					Region Address</label>
					<input type='text' name='ssf_wp_address_name-$value[ssf_wp_region_id]' id='ssf_wp_address_name-$value[ssf_wp_region_id]' value='$value[ssf_wp_address_name]'>
					<small>Enter Region Address</small>
					<div class='clearfix'></div>
					</div>
					
					<div class='input_title'>
						<span class='submit'>
						<input type='submit' value='".__("Save", SSF_WP_TEXT_DOMAIN)."' class='button-primary'> <input type='button' class='ssf-button' value='".__("Cancel", SSF_WP_TEXT_DOMAIN)."' onclick='$cancel_onclick'>
						
						</span>
						<div class='clearfix'></div>
					</div></div>";
		
		if (function_exists("do_ssf_wp_hook")) {
			ssf_wp_show_custom_fields();
		}
	if (function_exists("do_ssf_wp_hook")) {do_ssf_wp_hook("ssf_wp_single_location_edit", "select-top");}
	print "</form></td>";

print "</tr>";
	}
	
//*********region edit code end *************//	
	
/*-------------------------------------------*/

function ssf_wp_module($mod_name, $mod_heading="", $height="") {

	global $ssf_wp_vars, $wpdb;

	if (file_exists(SSF_WP_INCLUDES_PATH."/module-{$mod_name}.php")) {

		$css=(!empty($height))? "height:$height;" : "" ;

		print "<table class='widefat' style='background-color:transparent; border:0px; padding:4px; {$css}'>";

		if ($mod_heading){

			print "<thead><tr><th style='font-weight:bold; height:22px;'>$mod_heading</th></tr></thead>";

		}

		print "<tbody style='background-color:transparent; border:0px;'><tr><td style='background-color:transparent; border:0px;'>";

		include(SSF_WP_INCLUDES_PATH."/module-{$mod_name}.php");

		print "</td></tr></tbody></table><br>";

	}

}

/*--------------------------------------------*/

function ssf_wp_readme_parse($path_to_readme, $path_to_env){

include($path_to_env);

ob_start();

include($path_to_readme);

$txt=ob_get_contents();

ob_clean();

$toc=$txt;

	preg_match_all("@\=\=\=[ ]([^\=\=\=]+)[ ]\=\=\=@", $toc, $toc_match_0);

	preg_match_all("@\=\=[ ]([^\=\=]+)[ ]\=\=@", $toc, $toc_match_1); 

	preg_match_all("@\=[ ]([^\=]+)[ ]\=@", $toc, $toc_match_2); 

	$toc_cont="";

	foreach ($toc_match_2[1] as $heading) {

	    if (!in_array($heading, $toc_match_1[1]) && !in_array($heading, $toc_match_0[1]) && !preg_match("@^[0-9]+\.[0-9]+@", $heading)) {

		$toc_cont.="<li style='margin-left:30px; list-style-type:circle'><a href='#".ssf_comma($heading)."' style='text-decoration:none'>$heading</a></li></li>";

	    } elseif (!in_array($heading, $toc_match_0[1]) && !preg_match("@^[0-9]+\.[0-9]+@", $heading)) { 

	    	$toc_cont.="<li style='margin-left:15px; list-style-type:disc'><b><a href='#".ssf_comma($heading)."' style='text-decoration:none'>$heading</a></b></li>";

	    }

	}

$th_start="<th style='font-size:125%; font-weight:bold;'>";

$h2_start="<h2 style='font-family:Georgia; margin-bottom:0.05em;'>";

$h3_start="<h3 style='font-family:Georgia; margin-bottom:0.05em; margin-top:0.3em'>";

$txt=str_replace("=== ", "$h2_start", $txt);

$txt=str_replace(" ===", "</h2>", $txt);

$txt=str_replace("== ", "<table class='widefat' ><thead>$th_start", $txt);

$txt=str_replace(" ==", "</th></thead></table><!--a style='float:right' href='#readme_toc'>Table of Contents</a-->", $txt);

$txt=str_replace("= ", "$h3_start", $txt);

$txt=str_replace(" =", "</h3><a style='float:right; position:relative; top:-1.5em; font-size:10px' href='#readme_toc'>".__("table of contents", SSF_WP_TEXT_DOMAIN)."</a>", $txt);

$txt=preg_replace("@Tags:[ ]?[^\r\n]+\r\n@", "", $txt);

$txt=str_replace("</h2>", "</h2><a name='readme_toc'></a><div style='float:right;  width:500px; border-radius:1em; border:solid silver 1px; padding:7px; padding-top:0px; margin:10px; margin-right:0px;'><h3>".__("Table of Contents", SSF_WP_TEXT_DOMAIN)."</h2>$toc_cont</div>", $txt);

$txt=preg_replace_callback("@$h2_start<u>([^<.]*)</u></h1>@s", create_function('$matches', 

	'return "<h2 style=\'font-family:Georgia; margin-bottom:0.05em;\'><a name=\'".ssf_comma($matches[1])."\'></a>$matches[1]</u></h1>";'), $txt);

$txt=preg_replace_callback("@$th_start([^<.]*)</th>@s", create_function('$matches',

	'return "<th style=\'font-size:125%; font-weight:bold;\'><a name=\'".ssf_comma($matches[1])."\'></a>$matches[1]</th>";'), $txt);

$txt=preg_replace_callback("@$h3_start( )*([^<.]*)( )*</h3>@s", create_function('$matches',

	'return "<h3 style=\'font-family:Georgia; margin-bottom:0.05em; margin-top:0.3em\'><a name=\"".ssf_comma($matches[2])."\"></a>{$matches[1]}$matches[2]</h3>";'), $txt);

$txt=preg_replace("@\[([a-zA-Z0-9_/?&amp;\&\ \.%20,=\-\+\-\']+)*\]\(([a-zA-Z]+://)(([.]?[a-zA-Z0-9_/?&amp;%20,=\-\+\-\#]+)*)\)@s", "<a onclick=\"window.parent.open('\\2'+'\\3');return false;\" href=\"#\">\\1</a>", $txt);

$txt=preg_replace("@\*[ ]?[ ]?([^\r\n]+)*(\r\n)?@s", "<li style='margin-left:15px; margin-bottom:0px;'>\\1</li>", $txt);

$txt=preg_replace("@`([^`]+)*`@", "<strong class='ssf_wp_code code' style='padding:2px; border:0px'>\\1</strong>", $txt);

$txt=preg_replace("@__([^__]+)__@", "<strong>\\1</strong>", $txt);

$txt=preg_replace("@\r\n([0-9]\.)@", "\r\n&nbsp;&nbsp;&nbsp;\\1", $txt);

$txt=preg_replace("@([A-Za-z-0-9\/\\&;# ]+): @", "<strong>\\1: </strong>", $txt);


$txt=ssf_do_hyperlink($txt, "'_blank'", "protocol");

print nl2br($txt);

}

/*---------------------------------------------------------------*/

function ssf_wp_translate_stamp($dateVar="",$mode="return", $date_only=0, $abbreviate_month=0) {

if ($dateVar!="") {

		$mm=substr($dateVar,4,2);

		$dd=substr($dateVar,6,2);

		if ($dd<10) {$dd=str_replace("0","",$dd); } 		$yyyy=substr($dateVar,0,4);

		if (strlen($yyyy)==2 && $yyyy>=50) {

			$yyyy="19".$yyyy;

		}

		elseif (strlen($yyyy)==2 && $yyyy>=00 && $yyyy<50) {

			$yyyy="20".$yyyy;

		}

}

$months=array("January","February","March","April","May","June","July","August","September","October","November","December");

$dt="";

if (!empty($mm)) {

	$dt=$months[$mm-1];

	

	if ($abbreviate_month!=0) 

		$dt=substr($dt,0,3).".";

	

	if ($dd!="" && $yyyy!="")

		$dt.=" $dd, $yyyy";

}



if ($date_only==0) {

$hr=substr($dateVar,8,2);

$min=substr($dateVar,10,2);

$sec=substr($dateVar,12,2);

if ($hr<12) {$hr=str_replace("0","",$hr); }

if ($hr>12) {$hr=$hr-12; $suffix="pm";} else {$suffix="am";}

if ($hr==12) {$suffix="pm";}

if ($hr==0) {$hr=12;}



$dt.=" $hr:$min:$sec $suffix";



}



if ($mode!="print")

	return $dt;

elseif ($mode=="print")

	print $dt;



}

/*---------------------------------------------------------------*/

function ssf_wp_translate_date($dateVar="",$mode="return") {

if ($dateVar!="") {

		$parts=explode("/",$dateVar);

		$mm=trim($parts[0]);

		$dd=trim($parts[1]);

		if ($dd<10) {$dd=str_replace("0","",$dd); } 		$yyyy=trim($parts[2]);

		if (strlen($yyyy)==2 && $yyyy>=50) {

			$yyyy="19".$yyyy;

		}

		elseif (strlen($yyyy)==2 && $yyyy>=00 && $yyyy<50) {

			$yyyy="20".$yyyy;

		}

}

$months=array("January","February","March","April","May","June","July","August","September","October","November","December");



if ($mm!="") {

	$dt=$months[$mm-1];

	

	if ($dd!="" && $yyyy!="")

		$dt.="&nbsp;$dd,&nbsp;$yyyy";

}



if ($mode=="return")

	return $dt;

elseif ($mode=="print")

	print $dt;



}

/*-----------------------------------------------*/

add_action('admin_bar_menu', 'ssf_wp_admin_toolbar', 183);

function ssf_wp_admin_toolbar($admin_bar){

	/*

	$ssf_wp_admin_toolbar_array[] = array(

		'id'    => 'ssf-wp-menu',

		'title' => __('Super Store Finder', SSF_WP_TEXT_DOMAIN),

		'href'  => SSF_WP_INFORMATION_PAGE,	

		'meta'  => array(

			'title' => 'Super Store Finder Wordpress Plugin',			

		),

	);

	$ssf_wp_admin_toolbar_array[] = array(

		'id'    => 'ssf-wp-menu-news-upgrades',

		'parent' => 'ssf-wp-menu',

		'title' => __('Quick Start', SSF_WP_TEXT_DOMAIN),

		'href'  => SSF_WP_INFORMATION_PAGE,

		'meta'  => array(

			'title' => __('Quick Start', SSF_WP_TEXT_DOMAIN),

			'target' => '_self',

			'class' => 'ssf_wp_menu_class'

		),

	);

	$ssf_wp_admin_toolbar_array[] = array(

		'id'    => 'ssf-wp-menu-locations',

		'parent' => 'ssf-wp-menu',

		'title' => __('Stores', SSF_WP_TEXT_DOMAIN),

		'href'  => SSF_WP_MANAGE_LOCATIONS_PAGE,

		'meta'  => array(

			'title' => __('Stores', SSF_WP_TEXT_DOMAIN),

			'target' => '_self',

			'class' => 'ssf_wp_menu_class'

		),

	);

	$ssf_wp_admin_toolbar_array[] = array(

		'id'    => 'ssf-wp-menu-settings',

		'parent' => 'ssf-wp-menu',

		'title' => __('Settings', SSF_WP_TEXT_DOMAIN),

		'href'  => SSF_WP_SETTINGS_PAGE1,

		'meta'  => array(

			'title' => "Settings ".__('Settings', SSF_WP_TEXT_DOMAIN),

			'target' => '_self',

			'class' => 'ssf_wp_menu_class'

		),

	);

	

	if (function_exists('do_ssf_wp_hook')){ do_ssf_wp_hook('ssf_wp_admin_toolbar_filter', '', array(&$ssf_wp_admin_toolbar_array)); }

	

	foreach ($ssf_wp_admin_toolbar_array as $toolbar_page) {

		$admin_bar -> add_menu($toolbar_page);

	}*/

	

} 

/*---------------------------------------------------------------*/

function ssf_wp_permissions_check() {

	global $ssf_wp_vars;

	if (!empty($_POST['ssf_wp_folder_permission'])) {

		@array_map("chmod", $_POST['ssf_wp_folder_permission'], array_fill(0, count($_POST['ssf_wp_folder_permission']), 0755) );

	}

	if (!empty($_POST['ssf_wp_file_permission'])) {

		@array_map("chmod", $_POST['ssf_wp_file_permission'], array_fill(0, count($_POST['ssf_wp_file_permission']), 0644) );

	}

	$f_to_check = array(SSF_WP_UPLOADS_PATH);

	foreach ($f_to_check as $slf) {

		$dir_iterator = new RecursiveDirectoryIterator($slf);

		$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);

		$files = new RegexIterator($iterator, "/\.(php|gif|jpe?g|png|css|js|csv|xml|json|txt)/");

	}

	clearstatcache();

	$needs=0;

	

	foreach($iterator as $value) {

		if (is_dir($value) && 0755 !== (fileperms($value) & 0777)) {

			$needs_update["folder"][] = "$value - <b>".decoct(fileperms($value) & 0777)."</b>";

			$needs++;

		}

	}

	foreach($files as $value) {

		if (!is_dir($value) && 0644 !== (fileperms($value) & 0777)) {

			$needs_update["file"][] = "$value - <b>".decoct(fileperms($value) & 0777)."</b>";

			$needs++;

		}

	}

	$button_note = __("Note: Clicking this button should update permissions, however, if it doesn\'t, you may need to update permissions by using an FTP program.  Click &quot;OK&quot; or &quot;Confirm&quot; to continue ...", SSF_WP_TEXT_DOMAIN);
	if ($needs > 0){

		$output = "";

		print "<br><div class='ssf-wp-menu-alert' style='display:none;'><b>".__("Important Note", SSF_WP_TEXT_DOMAIN).":</b><br>".__("Some of your folders / files may need updating to the proper permissions (folders: 755 / files: 644), otherwise, all functionality may not work as intended.  View folders / files below", SSF_WP_TEXT_DOMAIN)." - <a href='#' onclick='show(\"file_perm_table\"); return false;'>".__("display / hide list of folders & files", SSF_WP_TEXT_DOMAIN)."</a>:<br>";

		print "<div style='float:right'>(<a href='".$_SERVER['REQUEST_URI']."&file_perm_msg=1'>".__("Hide This Notice Permanently", SSF_WP_TEXT_DOMAIN)."</a>)</div><br><br><table cellpadding='7px' id='file_perm_table' style='display:none;'><tr>";
 //print "<tr>";
	}

	if (!empty($needs_update["folder"])) {

		$output .= "<td style='vertical-align: top; width:50%'><form method='post'  onsubmit=\"return confirm('".$button_note."');\"><strong>".__("Folders", SSF_WP_TEXT_DOMAIN).":</strong><br><input type='submit' class='button-primary' value=\"".__("Update Checked Folders' Permissions", SSF_WP_TEXT_DOMAIN)."\"><br><br>";

		foreach ($needs_update["folder"] as $value) {

			$output .= "\n<input name='ssf_wp_folder_permission[]' checked='checked' type='checkbox' value='".substr($value, 0, -13)."'>&nbsp;/".str_replace(ABSPATH, '', $value)."<br>"; // "-13", removes 13 chars: " - <b> 777 </b>" at end of value

		}

		$output .= "</form></td>";	

	}

	if (!empty($needs_update["file"])) {

		$output .= "<td style='vertical-align: top; style: 50%;'><form method='post' onsubmit=\"return confirm('".$button_note."');\"><strong>".__("Files", SSF_WP_TEXT_DOMAIN).":</strong><br><input type='submit' class='button-primary' value=\"".__("Update Checked Files' Permissions", SSF_WP_TEXT_DOMAIN)."\"><br><br>";

		foreach ($needs_update["file"] as $value) {

			$output .= "\n<input name='ssf_wp_file_permission[]' checked='checked' type='checkbox' value='".substr($value, 0, -13)."'>&nbsp;/".str_replace(ABSPATH, '', $value)."<br>";

		}

		$output .= "</form></td>";	

	}

	if ($needs > 0){

		

		print $output."</tr></table></div>";

		$ssf_wp_vars['perms_need_update'] = 1;

	}

	

	if ($needs == 0) {

		$ssf_wp_vars['perms_need_update'] = 0;

	}

	

}

/*---------------------------------------------------------------*/

function ssf_wp_remote_data($val_arr, $decode_mode = 'json') {

	$pagetype = (!empty($val_arr['pagetype']))? $val_arr['pagetype'] : "none" ;

	$dir = (!empty($val_arr['dir']))? $val_arr['dir'] : "none" ;

	$key = (!empty($val_arr['key']))? "__".$val_arr['key'] : "" ;

	$start = (!empty($val_arr['start']))? $val_arr['start'] : 0 ;

	$val_host = (!empty($val_arr['host']))? $val_arr['host'] : 'superstorefinder.net' ;

	$val_url = (!empty($val_arr['url']))? $val_arr['url'] : "/show-data/". $pagetype ."/". $dir ."$key" ."/". $start ;

	$useragent = (!empty($val_arr['ua']))? $val_arr['ua'] : "Super Store Finder Wordpress Plugin" ;

	

	$target = "http://" . $val_host . $val_url;

  	

	$remote_access_fail = false;

	if (extension_loaded("curl") && function_exists("curl_init")) {

    			ob_start();

    			$ch = curl_init();

    			if (!empty($useragent) && $useragent != 'none'){ curl_setopt($ch, CURLOPT_USERAGENT, $useragent); }

    			curl_setopt($ch, CURLOPT_URL,$target);

    			curl_exec($ch);

		    	$returned_value = ob_get_contents();

			

   			ob_end_clean();

		} else {

	  		$request = '';

	  		$http_request  = "GET ". $val_url ." HTTP/1.0\r\n";

			$http_request .= "Host: ".$val_host."\r\n";

			$http_request .= "Content-Type: application/x-www-form-urlencoded; charset=" . SSF_WP_BLOG_CHARSET . "\r\n";

			$http_request .= "Content-Length: " . strlen($request) . "\r\n";

			if (!empty($useragent) && $useragent != 'none'){ $http_request .= "User-Agent: $useragent\r\n"; }

			$http_request .= "\r\n";

			$http_request .= $request;

			$response = '';

			if (false != ( $fs = @fsockopen($val_host, 80, $errno, $errstr, 10) ) ) {

				fwrite($fs, $http_request);

				while ( !feof($fs) )

					$response .= fgets($fs, 1160); 

				fclose($fs);

			}

			$returned_value = trim($response);

	}

	

	if (!empty($returned_value)) {

		$the_data = ($decode_mode != "serial")? json_decode($returned_value, true) : $returned_value;

		return $the_data;

	} else {

		return false;

	}

}

/*-----------------------------------------------*/

/// Loading SL Variables ///
global $wpdb;
if(defined('SSF_WP_SETTING_TABLE') && $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", SSF_WP_SETTING_TABLE)) == SSF_WP_SETTING_TABLE)
{
$ssf_wp_vars=ssf_wp_data('ssf_wp_vars');

if (!is_array($ssf_wp_vars)) {

	function ssf_wp_fix_corrupted_serialized_string($string) {

		$tmp = explode(':"', $string);

		$length = count($tmp);

		for($i = 1; $i < $length; $i++) {    

			list($string) = explode('"', $tmp[$i]);

        		$str_length = strlen($string);    

        		$tmp2 = explode(':', $tmp[$i-1]);

        		$last = count($tmp2) - 1;    

        		$tmp2[$last] = $str_length;         

        		$tmp[$i-1] = join(':', $tmp2);

    		}

    		return join(':"', $tmp);

	}

	$ssf_wp_vars = ssf_wp_fix_corrupted_serialized_string($ssf_wp_vars); 

	ssf_wp_data('ssf_wp_vars', 'update', $ssf_wp_vars);

	$ssf_wp_vars = unserialize($ssf_wp_vars); 

	}

}

if (defined('SSF_WP_ADDONS_PLATFORM_FILE') && file_exists(SSF_WP_ADDONS_PLATFORM_FILE)) {

	ssf_wp_initialize_variables(); 

	include_once(SSF_WP_ADDONS_PLATFORM_FILE);

}

//////



/*-----------------------------------*/

if (!function_exists("ssf_wp_do_geocoding")){

 function ssf_wp_do_geocoding($address, $ssf_wp_id="") {

   if (empty($_POST['no_geocode']) || $_POST['no_geocode']!=1){

	global $wpdb, $text_domain, $ssf_wp_vars;

	$delay = 100000; $ccTLD=$ssf_wp_vars['map_region']; $sensor=$ssf_wp_vars['sensor'];

	$base_url = "https://maps.googleapis.com/maps/api/geocode/json?";

	if ($sensor!="" && !empty($sensor) && ($sensor === "true" || $sensor === "false" )) {$base_url .= "sensor=".$sensor;} else {$base_url .= "sensor=false";}

	if ($ccTLD!="") {

		$base_url .= "&region=".$ccTLD;

		//die($base_url);

	}


	if (!empty($ssf_wp_vars['map_language'])) {

		$base_url .= "&language=".$ssf_wp_vars['map_language'];

	}


   $request_url = $base_url . "&address=" . urlencode(trim($address)); 
   
	if (extension_loaded("curl") && function_exists("curl_init")) {

		$cURL = curl_init();

		curl_setopt($cURL, CURLOPT_URL, $request_url);

		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);

		$resp_json = curl_exec($cURL);

		curl_close($cURL);  

	}else{

		$resp_json = file_get_contents($request_url) or die("url not loading");

	}

	//End of new code

	$resp = json_decode($resp_json, true); 

    $status = $resp['status']; //$status = "";

    $lat = (!empty($resp['results'][0]['geometry']['location']['lat']))? $resp['results'][0]['geometry']['location']['lat'] : "" ;

    $lng = (!empty($resp['results'][0]['geometry']['location']['lng']))? $resp['results'][0]['geometry']['location']['lng'] : "" ;

    if (strcmp($status, "OK") == 0) {

		// successful geocode

		$geocode_pending = false;

		$lat = $resp['results'][0]['geometry']['location']['lat'];

		$lng = $resp['results'][0]['geometry']['location']['lng'];

		if ($ssf_wp_id==="") {

			$query = sprintf("UPDATE ".SSF_WP_TABLE." SET ssf_wp_latitude = '%s', ssf_wp_longitude = '%s' WHERE ssf_wp_id = '%s' LIMIT 1;", esc_sql($lat), esc_sql($lng), esc_sql($wpdb->insert_id)); //die($query); 

		} else {

		$query = sprintf("UPDATE ".SSF_WP_TABLE." SET ssf_wp_latitude = '%s', ssf_wp_longitude = '%s' WHERE ssf_wp_id = '%s' LIMIT 1;", esc_sql($lat), esc_sql($lng), esc_sql($ssf_wp_id)); 

		}

//$update_result = $wpdb->query($query);

$update_result=0;
		if ($update_result === FALSE) {

			die("Invalid query: " . $wpdb->last_error);

		}

    } else if (strcmp($status, "OVER_QUERY_LIMIT") == 0) {
		$delay += 100000;

    } else {
		$geocode_pending = false;

		echo __("<div class='ssf-wp-menu-alert'> Failed to geocode address. Status: $status</div> ", SSF_WP_TEXT_DOMAIN);

    }

    usleep($delay);

  } else {

  	

  } @ob_flush(); flush();

 }

}

/*-------------------------------*/



if (!function_exists("ssf_wp_template")){
	
   function ssf_wp_template($content) {
	   

	global $ssf_wp_dir, $ssf_wp_base, $ssf_wp_uploads_base, $ssf_wp_path, $ssf_wp_uploads_path, $text_domain, $wpdb, $ssf_wp_vars;

	global $superstorefinder;

	if(! preg_match('|\[super-store-finder|i', $content)) {

		return $content;

	}

	else {

		//WPML Display Integration

		/*if (function_exists('icl_t')) { 

			include(SSF_WP_INCLUDES_PATH."/settings-options.php");

			$GLOBALS['input_zone_type'] = 'labels';

			$GLOBALS['output_zone_type'] = 'ssf_wp_template';

		

			$labels_arr = array_filter($ssf_wp_mdo, "filter_ssf_wp_mdo");

			unset($GLOBALS['input_zone_type']); unset($GLOBALS['output_zone_type']);

			//var_dump($labels_arr); die();

		

			foreach ($labels_arr as $value) {

				$the_field = $value['field_name'];

				$varname = "ssf_wp_".$the_field;

				$$varname = icl_t(SSF_WP_DIR, $value['label'], $$varname);

			}

			$ssf_wp_search_button = icl_t(SSF_WP_DIR,"Search Button Filename", $ssf_wp_search_button);

			$ssf_wp_search_button_down = icl_t(SSF_WP_DIR,"Search Button Filename (Down State)", $ssf_wp_search_button_down);

			$ssf_wp_search_button_over = icl_t(SSF_WP_DIR,"Search Button Filename (Over State)", $ssf_wp_search_button_over);

		}*/

		//End WPML
/***.*** Code for default category set on page ***.***/
		$start='[SUPER-STORE-FINDER CAT=';
		$end=']';
		$string = ' ' . $content;
		$ini = strpos($string, $start);
		if ($ini == 0)
		{
			 $ssf_default_category= '';
		}
		else {
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		$ssf_default_category=substr($string, $ini, $len);
	}

    $ssf_default_category=trim($ssf_default_category);
	if($ssf_default_category!=""){
		$superstorefinder.="<script>
		ssf_default_category='$ssf_default_category';
		</script>";
	}
	if($ssf_default_category!=''){
	    $replaceShorts='[SUPER-STORE-FINDER CAT='.$ssf_default_category.']';
		$content=str_replace($replaceShorts,$superstorefinder,$content);
	}else{
		$content=str_replace('[SUPER-STORE-FINDER]',$superstorefinder,$content);
	}
	return $content;
	//return preg_replace("@\[super-store-finder(.*)?\]@i", $superstorefinder, $content);
	   }
    }
} ?>