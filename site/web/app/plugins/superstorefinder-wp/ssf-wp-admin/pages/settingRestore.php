<?php 
    include("../../ssf-wp-inc/includes/ssf-wp-env.php");
	global $wpdb;
	if(isset($_POST['restore'])){
	    $setting_name='ssf_wp_vars';
		$setting_value='';
		$q = $wpdb->prepare("UPDATE ".SSF_WP_SETTING_TABLE." SET ssf_wp_setting_value = %s WHERE ssf_wp_setting_name = %s", $setting_value, $setting_name);
		$wpdb->query($q);
		
		 $dir=SSF_WP_UPLOADS_PATH."/images/icons/";
		if(file_exists($dir.'custom-marker.png')){
			unlink($dir.'custom-marker.png');
		}
		if(file_exists($dir.'custom-marker-active.png')){
			unlink($dir.'custom-marker-active.png'); 
		}
		
		
		ssf_wp_restore_variables();
	}
	
	function ssf_wp_restore_variables() {	
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

	ssf_wp_data('ssf_wp_vars', 'update', $ssf_wp_vars);
}
?>