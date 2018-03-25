<?php
if (is_dir(SSF_WP_THEMES_PATH)) {
	$theme_dir=opendir(SSF_WP_THEMES_PATH); 
	$theme_str="";
	while (false !== ($a_theme=readdir($theme_dir))) {
		if (!preg_match("@^\.{1,2}.*$@", $a_theme) && !preg_match("@\.(php|txt|htm(l)?)@", $a_theme)) {

			$selected=($a_theme==$ssf_wp_vars['theme'])? " selected " : "";
			$theme_str.="<option value='$a_theme' $selected>$a_theme</option>\n";
		}
	}
}

$zl_arr=array();
for ($i=0; $i<=19; $i++) {
	$zl_arr[]=$i;
}

$map_settings["".__("Geo Location", SSF_WP_TEXT_DOMAIN).""]="geo";
$map_settings["".__("Show All Stores", SSF_WP_TEXT_DOMAIN).""]="showall";
$map_settings["".__("Show Specific Location", SSF_WP_TEXT_DOMAIN).""]="specific";

$map_settings_options="";

foreach($map_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['ssf_wp_map_settings']==$value)? " selected " : "";
	$map_settings_options.="<option value='$value' $selected2>$key</option>\n";
}




$layout_settings_options="";
$selected1=($ssf_wp_vars['ssf_layout']=='bottom')? " checked " : "";
$selected2=($ssf_wp_vars['ssf_layout']=='left')? " checked " : "";
$selected3=($ssf_wp_vars['ssf_layout']=='right')? " checked " : "";
$layout_settings_options.="<img class='ssf_layout_settings' src='".SSF_WP_BASE."/images/icons/ssf-bottom.png'><span class='ssf_layout'><input name='ssf_layout' type='radio' value='bottom' $selected1>&nbsp;Standard</span>\n";
$layout_settings_options.="<img class='ssf_layout_settings' src='".SSF_WP_BASE."/images/icons/ssf-left.png'><span class='ssf_layout'><input name='ssf_layout' type='radio' value='left' $selected2>&nbsp;Left</span>\n";
$layout_settings_options.="<img class='ssf_layout_settings' src='".SSF_WP_BASE."/images/icons/ssf-right.png'><span class='ssf_layout'><input name='ssf_layout' type='radio' value='right' $selected3>&nbsp;Right</span>\n";



$labeled_marker_settings["".__("Yes", SSF_WP_TEXT_DOMAIN).""]="1";
$labeled_marker_settings["".__("No", SSF_WP_TEXT_DOMAIN).""]="0";


$labeled_marker_settings_options="";

foreach($labeled_marker_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['labeled_marker']==$value)? " selected " : "";
	$labeled_marker_settings_options.="<option value='$value' $selected2>$key</option>\n";
}


$view_mobile_settings["".__("Show less details", SSF_WP_TEXT_DOMAIN).""]="1";
$view_mobile_settings["".__("Show more details", SSF_WP_TEXT_DOMAIN).""]="0";


$view_mobile_settings_options="";

foreach($view_mobile_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['ssf_mobile_fields']==$value)? " selected " : "";
	$view_mobile_settings_options.="<option value='$value' $selected2>$key</option>\n";
}

// regions

$set_region="";

foreach($labeled_marker_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['region_show']==$value)? " selected " : "";
	$set_region.="<option value='$value' $selected2>$key</option>\n";
}

$set_category="";

foreach($labeled_marker_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['category_show']==$value)? " selected " : "";
	$set_category.="<option value='$value' $selected2>$key</option>\n";
}

$set_show_all="";

foreach($labeled_marker_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['show_all_show']==$value)? " selected " : "";
	$set_show_all.="<option value='$value' $selected2>$key</option>\n";
}

$show_result_list="";
foreach($labeled_marker_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['show_result_list']==$value)? " selected " : "";
	$show_result_list.="<option value='$value' $selected2>$key</option>\n";
}

$show_search_bar="";
foreach($labeled_marker_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['show_search_bar']==$value)? " selected " : "";
	$show_search_bar.="<option value='$value' $selected2>$key</option>\n";
}

$map_mouse_scroll="";
foreach($labeled_marker_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['map_mouse_scroll']==$value)? " selected " : "";
	$map_mouse_scroll.="<option value='$value' $selected2>$key</option>\n";
}

$show_scroll_set_settings='';

foreach($labeled_marker_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['show_scroll_set']==$value)? " selected " : "";
	$show_scroll_set_settings.="<option value='$value' $selected2>$key</option>\n";
}

$zoom_level="";

$zoom_level_settings["".__("auto", SSF_WP_TEXT_DOMAIN).""]="auto";
$zoom_level_settings["".__("1", SSF_WP_TEXT_DOMAIN).""]="1";
$zoom_level_settings["".__("2", SSF_WP_TEXT_DOMAIN).""]="2";
$zoom_level_settings["".__("3", SSF_WP_TEXT_DOMAIN).""]="3";
$zoom_level_settings["".__("4", SSF_WP_TEXT_DOMAIN).""]="4";
$zoom_level_settings["".__("5", SSF_WP_TEXT_DOMAIN).""]="5";
$zoom_level_settings["".__("6", SSF_WP_TEXT_DOMAIN).""]="6";
$zoom_level_settings["".__("7", SSF_WP_TEXT_DOMAIN).""]="7";
$zoom_level_settings["".__("8", SSF_WP_TEXT_DOMAIN).""]="8";
$zoom_level_settings["".__("9", SSF_WP_TEXT_DOMAIN).""]="9";
$zoom_level_settings["".__("10", SSF_WP_TEXT_DOMAIN).""]="10";
$zoom_level_settings["".__("11", SSF_WP_TEXT_DOMAIN).""]="11";
$zoom_level_settings["".__("12", SSF_WP_TEXT_DOMAIN).""]="12";
$zoom_level_settings["".__("13", SSF_WP_TEXT_DOMAIN).""]="13";
$zoom_level_settings["".__("14", SSF_WP_TEXT_DOMAIN).""]="14";
$zoom_level_settings["".__("15", SSF_WP_TEXT_DOMAIN).""]="15";
$zoom_level_settings["".__("16", SSF_WP_TEXT_DOMAIN).""]="16";
$zoom_level_settings["".__("17", SSF_WP_TEXT_DOMAIN).""]="17";
$zoom_level_settings["".__("18", SSF_WP_TEXT_DOMAIN).""]="18";
$zoom_level_settings["".__("19", SSF_WP_TEXT_DOMAIN).""]="19";
$zoom_level_settings["".__("20", SSF_WP_TEXT_DOMAIN).""]="20";


foreach($zoom_level_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['zoom_level']==$value)? " selected " : "";
	$zoom_level.="<option value='$value' $selected2>$key</option>\n";
}

$image_set="";
$image_settings["".__("Yes", SSF_WP_TEXT_DOMAIN).""]="yes";
$image_settings["".__("No", SSF_WP_TEXT_DOMAIN).""]="no";
$image_settings["".__("Image/Video", SSF_WP_TEXT_DOMAIN).""]="showboth";

foreach($image_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['show_image_list']==$value)? " selected " : "";
	$image_set.="<option value='$value' $selected2>$key</option>\n";
}


$StreetView_settings="";
$ShowStreetView["".__("Yes", SSF_WP_TEXT_DOMAIN).""]="yes";
$ShowStreetView["".__("No", SSF_WP_TEXT_DOMAIN).""]="no";

foreach($ShowStreetView as $key=>$value) {
	$selected2=($ssf_wp_vars['StreetView_set']==$value)? " selected " : "";
	$StreetView_settings.="<option value='$value' $selected2>$key</option>\n";
}

$GetDirTop_settings="";
$GetDirTop["".__("Yes", SSF_WP_TEXT_DOMAIN).""]="yes";
$GetDirTop["".__("No", SSF_WP_TEXT_DOMAIN).""]="no";

foreach($GetDirTop as $key=>$value) {
	$selected2=($ssf_wp_vars['GetDirTop_set']==$value)? " selected " : "";
	$GetDirTop_settings.="<option value='$value' $selected2>$key</option>\n";
}

$GetDirBottom_settings="";
$GetDirBottom["".__("Yes", SSF_WP_TEXT_DOMAIN).""]="yes";
$GetDirBottom["".__("No", SSF_WP_TEXT_DOMAIN).""]="no";

foreach($GetDirBottom as $key=>$value) {
	$selected2=($ssf_wp_vars['GetDirBottom_set']==$value)? " selected " : "";
	$GetDirBottom_settings.="<option value='$value' $selected2>$key</option>\n";
}




$pagination_set="";
$pagination_settings["".__("Off", SSF_WP_TEXT_DOMAIN).""]="0";
$pagination_settings["".__("9", SSF_WP_TEXT_DOMAIN).""]="9";
$pagination_settings["".__("15", SSF_WP_TEXT_DOMAIN).""]="15";
$pagination_settings["".__("27", SSF_WP_TEXT_DOMAIN).""]="27";
$pagination_settings["".__("54", SSF_WP_TEXT_DOMAIN).""]="54";
$pagination_settings["".__("90", SSF_WP_TEXT_DOMAIN).""]="90";


foreach($pagination_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['pagination_setting']==$value)? " selected " : "";
	$pagination_set.="<option value='$value' $selected2>$key</option>\n";
}



$exturl_link="";
$exturl_link_settings["".__("New window", SSF_WP_TEXT_DOMAIN).""]="true";
$exturl_link_settings["".__("Existing window", SSF_WP_TEXT_DOMAIN).""]="false";


foreach($exturl_link_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['exturl_link']==$value)? " selected " : "";
	$exturl_link.="<option value='$value' $selected2>$key</option>\n";
}





$disableda = '';
$disabledb = '';
// general
$marker_upload_path="../wp-content/uploads/ssf-wp-uploads/images/icons/";
if(file_exists($marker_upload_path."custom-marker.png")) {
		  $cmval = 'custom-marker.png';
	      $custom_marker='<div id="marker-a" style="display:inline-block;"><img src="'.$marker_upload_path.'custom-marker.png?t='.time().'" width="38px">';
	      $custom_marker_btn="<input type=\"button\" onclick=\"deleteMarker('a');\" class=\"btn btn-danger\"  value=\"Delete\"></div>";
			$disableda="disabled='disabled'";
		}
		else{
		$cmval = '';
			 $custom_marker_btn='';
			 $custom_marker='';
		}


if(file_exists($marker_upload_path."custom-marker-active.png")) {
		$cmaval = 'custom-marker-active.png';
	     $custom_marker_img='<div id="marker-b" style="display:inline-block;"><img src="'.$marker_upload_path.'custom-marker-active.png?t='.time().'" width="38px">';
	     $custom_marker_button="<input type=\"button\" onclick=\"deleteMarker('b');\" class=\"btn btn-danger\"  value=\"Delete\"></div>";
		$disabledb="disabled='disabled'";

		}
		else{
		$cmaval = '';
			 $custom_marker_button='';
			 $custom_marker_img='';
		}
		

	if($ssf_wp_vars['ssf_wp_map_code'])	{
	$ssf_wp_vars['ssf_wp_map_code'] = stripslashes($ssf_wp_vars['ssf_wp_map_code']);
	}


$ssf_wp_mdo[] = array("field_name" => "ssf_layout", "default" => "bottom", "input_zone" => "defaults", "label" => __("Layout", SSF_WP_TEXT_DOMAIN), "input_template" => "$layout_settings_options");

$ssf_wp_mdo[] = array("field_name" => "ssf_wp_map_settings", "default" => "geo", "input_zone" => "defaults", "label" => __("Default Map Settings", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='ssf_wp_map_settings'>\n$map_settings_options</select>");

$ssf_wp_mdo[] = array("field_name" => "default_location", "default" => "New York, US", "input_zone" => "defaults", "label" =>  __("Default Location", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text'  name='default_location' value='$ssf_wp_vars[default_location]'>");


$regions = super_store_region_list();
    $region_list='';
	foreach ($regions as $region)  {
	        $selected2=($ssf_wp_vars['google_map_region']==$region['value'])? " selected " : "";
		    $region_list .="<option value='".$region['value']."' ".$selected2.">".$region['name']."</option>";
    } 
$ssf_wp_mdo[] = array("field_name" => "google_map_region", "default" => "World", "input_zone" => "defaults", "label" =>  __("Google Map Region", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='google_map_region'  class='chosen-select'>\n$region_list</select>");



$mapLanguage = super_store_language_list();
    $languge_list='';
	foreach ($mapLanguage as $lng)  {
	        $selected2=($ssf_wp_vars['google_map_language']==$lng['value'])? " selected " : "";
		    $languge_list .="<option value='".$lng['value']."' ".$selected2.">".$lng['name']."</option>";
    } 
$ssf_wp_mdo[] = array("field_name" => "google_map_language", "default" => "en", "input_zone" => "defaults", "label" =>  __("Google Map Language", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='google_map_language'  class='chosen-select'>\n$languge_list</select>");

if (function_exists("get_ssf_current_user_role")){
$ssf_role=get_ssf_current_user_role();
}else{
$ssf_role='administrator';
}
if($ssf_role=='administrator'){
$user_role_set='';
$ex_cat = explode(",", $ssf_wp_vars['ssf_user_role']);
$ex_cat = array_map( 'trim', $ex_cat );
$user_role_settings["".__("Administrator", SSF_WP_TEXT_DOMAIN).""]="administrator";
$user_role_settings["".__("Author", SSF_WP_TEXT_DOMAIN).""]="author";
$user_role_settings["".__("Subscriber", SSF_WP_TEXT_DOMAIN).""]="subscriber";
$user_role_settings["".__("Contributor", SSF_WP_TEXT_DOMAIN).""]="contributor";
$user_role_settings["".__("Editor", SSF_WP_TEXT_DOMAIN).""]="editor";

foreach($user_role_settings as $key=>$value) {
	$selected2=(in_array($value,$ex_cat))? 'selected="selected"' : '';	
	$desable=($value=='administrator')? 'disabled' : '';
	$user_role_set.="<option value='$value' $selected2 $desable>$key</option>\n";
}

$ssf_wp_mdo[] = array("field_name" => "ssf_user_role", "default" => "administrator", "input_zone" => "defaults", "label" =>  __("User Role", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='ssf_user_role[]' class='chosen-select' multiple>\n$user_role_set</select>");
}

$SSFFontFamilly = SuperStoreFontFamilly();
    $font_list='';
	foreach ($SSFFontFamilly as $fonts)  {
	        $selected2=($ssf_wp_vars['ssf_font_familly']==$fonts['value'])? " selected " : "";
		    $font_list .="<option value='".$fonts['value']."' ".$selected2.">".$fonts['name']."</option>";
    } 
$ssf_wp_mdo[] = array("field_name" => "ssf_font_familly", "default" => " ", "input_zone" => "defaults", "label" =>  __("Font Family", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='ssf_font_familly'  class='chosen-select'>\n$font_list</select>");


$notification_setting="";
$notification_condition["".__("On", SSF_WP_TEXT_DOMAIN).""]="true";
$notification_condition["".__("Off", SSF_WP_TEXT_DOMAIN).""]="false";
foreach($notification_condition as $key=>$value) {
	$selected2=($ssf_wp_vars['notification_bar']==$value)? " selected " : "";
	$notification_setting.="<option value='$value' $selected2>$key</option>\n";
}

$ssf_wp_mdo[] = array("field_name" => "notification_bar", "default" => "true", "input_zone" => "defaults", "label" => __("Notification bar", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='notification_bar'>\n$notification_setting</select>");

$tel_fax_link_setting="";
$tel_fax_condition["".__("On", SSF_WP_TEXT_DOMAIN).""]="true";
$tel_fax_condition["".__("Off", SSF_WP_TEXT_DOMAIN).""]="false";
foreach($tel_fax_condition as $key=>$value) {
	$selected2=($ssf_wp_vars['tel_fax_link']==$value)? " selected " : "";
	$tel_fax_link_setting.="<option value='$value' $selected2>$key</option>\n";
}

$ssf_wp_mdo[] = array("field_name" => "tel_fax_link", "default" => "false", "input_zone" => "defaults", "label" => __("Tel/Fax Link", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='tel_fax_link'>\n$tel_fax_link_setting</select>");

$ssf_wp_mdo[] = array("field_name" => "labeled_marker", "default" => "1", "input_zone" => "defaults", "label" => __("Labeled Marker", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='labeled_marker'>\n$labeled_marker_settings_options</select>");

$ssf_wp_mdo[] = array("field_name" => "show_scroll_set", "default" => "1", "input_zone" => "defaults", "label" => __("Show Scroll To Top", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='show_scroll_set'>\n$show_scroll_set_settings</select>");


$ssf_wp_mdo[] = array("field_name" => "ssf_distance_km", "default" => "30", "input_zone" => "defaults", "label" => __("Distance (km)", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='ssf_distance_km' value=\"$ssf_wp_vars[ssf_distance_km]\" size='14'>");


$ssf_wp_mdo[] = array("field_name" => "zoom_level", "default" => "auto", "input_zone" => "defaults", "label" => __("Zoom Level", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='zoom_level'>\n$zoom_level</select>");

$map_position="";
$map_position_settings["".__("Centralized Search Location", SSF_WP_TEXT_DOMAIN).""]="true";
$map_position_settings["".__("Fit Markers On Map", SSF_WP_TEXT_DOMAIN).""]="false";


foreach($map_position_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['ssf_map_position']==$value)? " selected " : "";
	$map_position.="<option value='$value' $selected2>$key</option>\n";
}
$ssf_wp_mdo[] = array("field_name" => "ssf_map_position", "default" => "true", "input_zone" => "defaults", "label" => __("Advanced Zoom", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='ssf_map_position'>\n$map_position</select>");

$ssf_wp_mdo[] = array("field_name" => "map_mouse_scroll", "default" => "0", "input_zone" => "defaults", "label" => __("Map Mouse Scroll", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='map_mouse_scroll'>\n$map_mouse_scroll</select>");

$scroll_setting="";
$scroll_condition["".__("Auto (Default)", SSF_WP_TEXT_DOMAIN).""]="0";
$scroll_condition["".__("Top page", SSF_WP_TEXT_DOMAIN).""]="1";
$scroll_condition["".__("None", SSF_WP_TEXT_DOMAIN).""]="2";


foreach($scroll_condition as $key=>$value) {
	$selected2=($ssf_wp_vars['scroll_setting']==$value)? " selected " : "";
	$scroll_setting.="<option value='$value' $selected2>$key</option>\n";
}


$ssf_wp_mdo[] = array("field_name" => "scroll_setting", "default" => "true", "input_zone" => "defaults", "label" => __("Scroll settings", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='scroll_setting'>\n$scroll_setting</select>");


$geo_location_icon="";
$location_icon_condition["".__("Show", SSF_WP_TEXT_DOMAIN).""]="1";
$location_icon_condition["".__("Hide", SSF_WP_TEXT_DOMAIN).""]="2";


foreach($location_icon_condition as $key=>$value) {
	$selected2=($ssf_wp_vars['geo_location_icon']==$value)? " selected " : "";
	$geo_location_icon.="<option value='$value' $selected2>$key</option>\n";
}


$ssf_wp_mdo[] = array("field_name" => "geo_location_icon", "default" => "1", "input_zone" => "defaults", "label" => __("Geo Location Icon", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='geo_location_icon'>\n$geo_location_icon</select>");


$search_bar_position="";
$bar_position_condition["".__("Overlapped on Top of Map", SSF_WP_TEXT_DOMAIN).""]="false";
$bar_position_condition["".__("Above the Map", SSF_WP_TEXT_DOMAIN).""]="true";


foreach($bar_position_condition as $key=>$value) {
	$selected2=($ssf_wp_vars['search_bar_position']==$value)? " selected " : "";
	$search_bar_position.="<option value='$value' $selected2>$key</option>\n";
}

$ssf_wp_mdo[] = array("field_name" => "search_bar_position", "default" => "false", "input_zone" => "defaults", "label" => __("Search bar position", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='search_bar_position'>\n$search_bar_position</select> <small>(For Standard Layout Only)</small>");

$ssf_wp_mdo[] = array("field_name" => "google_api_key", "default" => "", "input_zone" => "defaults", "label" => __("Google API key", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='google_api_key' value=\"$ssf_wp_vars[google_api_key]\">");

$ssf_wp_mdo[] = array("field_name" => "ssf_conatct_email", "default" => "", "input_zone" => "defaults", "label" => __("Contact Email 
", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='ssf_conatct_email' value=\"$ssf_wp_vars[ssf_conatct_email]\" size='14'>");

$ssf_wp_mdo[] = array("field_name" => "ssf_mobile_fields", "default" => "1", "input_zone" => "defaults", "label" => __("Mobile fields 
", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='ssf_mobile_fields'>\n$view_mobile_settings_options</select>");

$mobile_gesture="";
$gesture_condition["".__("Yes", SSF_WP_TEXT_DOMAIN).""]="true";
$gesture_condition["".__("No", SSF_WP_TEXT_DOMAIN).""]="false";
foreach($gesture_condition as $key=>$value) {
	$selected2=($ssf_wp_vars['mobile_gesture']==$value)? " selected " : "";
	$mobile_gesture.="<option value='$value' $selected2>$key</option>\n";
}

$ssf_wp_mdo[] = array("field_name" => "mobile_gesture", "default" => "false", "input_zone" => "defaults", "label" => __("Mobile Gesture
", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='mobile_gesture'>\n$mobile_gesture</select>");

$ssf_wp_mdo[] = array("field_name" => "custom_marker", "default" => "$cmval", "input_zone" => "defaults", "label" =>  __("Custom Marker", SSF_WP_TEXT_DOMAIN), "input_template" => "<input id='custom_marker' type='file' $disableda class='custom_marker' name='custom_marker' value=\"$cmval\">$custom_marker $custom_marker_btn");

$ssf_wp_mdo[] = array("field_name" => "custom_marker_active", "default" => "$cmaval", "input_zone" => "defaults", "label" =>  __("Custom Marker Active", SSF_WP_TEXT_DOMAIN), "input_template" => "<input id='custom_marker_active' type='file' $disabledb class='custom_marker' name='custom_marker_active' value='$cmaval'> $custom_marker_img $custom_marker_button");

$ssf_wp_mdo[] = array("field_name" => "marker_label_color", "default" => "", "input_zone" => "defaults", "output_zone" => "ssf_wp_dyn_js", "label" => __("Marker Letter Color", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='marker_label_color' value=\"$ssf_wp_vars[marker_label_color]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "pagination_setting", "default" => "0", "input_zone" => "defaults", "label" => __("Pagination 
", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='pagination_setting'>\n$pagination_set</select>");
// styles

$ssf_wp_mdo[] = array("field_name" => "style_map_color", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Map Hue Color", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_map_color' value=\"$ssf_wp_vars[style_map_color]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_map_bg", "default" =>"", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Main background color", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_map_bg' value=\"$ssf_wp_vars[style_map_bg]\" class=\"my-color-field\" >");

//map custom code	
		
$ssf_wp_mdo[] = array("field_name" => "ssf_wp_map_code", "default" => " ", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Map Custom Code", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='ssf_wp_map_code' value='$ssf_wp_vars[ssf_wp_map_code]' size='14'> <small>Leave blank to set as default. Refer documentation guide <a href='http://superstorefinder.net/support/knowledgebase/can-i-use-custom-google-map-styles-code/' target='new'>here</a></small>");			
	

$ssf_wp_mdo[] = array("field_name" => "style_top_bar_bg", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Top Bar Background", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_top_bar_bg' value=\"$ssf_wp_vars[style_top_bar_bg]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_top_bar_font", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Top Bar Font", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_top_bar_font' value=\"$ssf_wp_vars[style_top_bar_font]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_geo_font", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Geo Icon", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_geo_font' value=\"$ssf_wp_vars[style_geo_font]\" class=\"my-color-field\" >");


$ssf_wp_mdo[] = array("field_name" => "style_top_bar_border", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Filter Border", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_top_bar_border' value=\"$ssf_wp_vars[style_top_bar_border]\" class=\"my-color-field\" >");


$ssf_wp_mdo[] = array("field_name" => "filter_font_color", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Filter Font Color", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='filter_font_color' value=\"$ssf_wp_vars[filter_font_color]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_results_distance_font", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Show All Font", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_results_distance_font' value=\"$ssf_wp_vars[style_results_distance_font]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_results_bg", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Results Background", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_results_bg' value=\"$ssf_wp_vars[style_results_bg]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_results_hl_bg", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Results Highlighted Background", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_results_hl_bg' value=\"$ssf_wp_vars[style_results_hl_bg]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_results_hover_bg", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Results Hover Background", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_results_hover_bg' value=\"$ssf_wp_vars[style_results_hover_bg]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_results_font", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Results Font", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_results_font' value=\"$ssf_wp_vars[style_results_font]\" class=\"my-color-field\" >");



$ssf_wp_mdo[] = array("field_name" => "style_distance_toggle_bg", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Direction Font", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_distance_toggle_bg' value=\"$ssf_wp_vars[style_distance_toggle_bg]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_contact_button_bg", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Info Window Background", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_contact_button_bg' value=\"$ssf_wp_vars[style_contact_button_bg]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_contact_button_font", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Info Window Font", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_contact_button_font' value=\"$ssf_wp_vars[style_contact_button_font]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_info_link_bg", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Info Window Button Background", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_info_link_bg' value=\"$ssf_wp_vars[style_info_link_bg]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_info_link_font", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Info Window Button Font", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_info_link_font' value=\"$ssf_wp_vars[style_info_link_font]\" class=\"my-color-field\" >");



$ssf_wp_mdo[] = array("field_name" => "info_window_buttons", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Info Window Street View/Direction", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='info_window_buttons' value=\"$ssf_wp_vars[info_window_buttons]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "infowindowlink", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Infowindow Link", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='infowindowlink' value=\"$ssf_wp_vars[infowindowlink]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_button_bg", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Buttons Background", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_button_bg' value=\"$ssf_wp_vars[style_button_bg]\" class=\"my-color-field\" >");



$ssf_wp_mdo[] = array("field_name" => "style_button_font", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Buttons Font", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_button_font' value=\"$ssf_wp_vars[style_button_font]\" class=\"my-color-field\" >");


$ssf_wp_mdo[] = array("field_name" => "previousnextbuttonbg", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Previous / Next Button Background", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='previousnextbuttonbg' value=\"$ssf_wp_vars[previousnextbuttonbg]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "previousnextbuttonclr", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Previous / Next Font Color", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='previousnextbuttonclr' value=\"$ssf_wp_vars[previousnextbuttonclr]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "storesnearyou", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Stores Near You Color", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='storesnearyou' value=\"$ssf_wp_vars[storesnearyou]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_list_number_bg", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Number List Background", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_list_number_bg' value=\"$ssf_wp_vars[style_list_number_bg]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_list_number_font", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Number List Font", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_list_number_font' value=\"$ssf_wp_vars[style_list_number_font]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_list_number_circle", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Number List Circle", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_list_number_circle' value=\"$ssf_wp_vars[style_list_number_circle]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_list_number_circle_active", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Number List Circle Active", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_list_number_circle_active' value=\"$ssf_wp_vars[style_list_number_circle_active]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_list_number_bg_active", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Number List Background Active", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_list_number_bg_active' value=\"$ssf_wp_vars[style_list_number_bg_active]\" class=\"my-color-field\" >");

$ssf_wp_mdo[] = array("field_name" => "style_list_number_font_active", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Number List Font Active", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_list_number_font_active' value=\"$ssf_wp_vars[style_list_number_font_active]\" class=\"my-color-field\" >");




// labels


$ssf_wp_mdo[] = array("field_name" => "search_label", "default" => "Search for nearby stores", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Search bar caption", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='search_label' value=\"$ssf_wp_vars[search_label]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "outlet_label", "default" => "outlets", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("outlets", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='outlet_label' value=\"$ssf_wp_vars[outlet_label]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "of_label", "default" => "of", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("of", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='of_label' value=\"$ssf_wp_vars[of_label]\" size='14'>","stripslashes" => 1);


$ssf_wp_mdo[] = array("field_name" => "clear_all_label", "default" => "Clear All", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Clear All", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='clear_all_label' value=\"$ssf_wp_vars[clear_all_label]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "show_all_label", "default" => "Show All", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Show All", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='show_all_label' value=\"$ssf_wp_vars[show_all_label]\" size='14'>");

$ssf_wp_mdo[] = array("field_name" => "stores_near_you", "default" => "Stores near you", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Stores near you", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='stores_near_you' value=\"$ssf_wp_vars[stores_near_you]\" size='14'>","stripslashes" => 1);




$ssf_wp_mdo[] = array("field_name" => "by_region_label", "default" => "By Region", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("By Region", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='by_region_label' value=\"$ssf_wp_vars[by_region_label]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "by_category", "default" => "Category", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Category", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='by_category' value=\"$ssf_wp_vars[by_category]\" size='14'>","stripslashes" => 1);
$ssf_wp_mdo[] = array("field_name" => "all_category", "default" => "All Category", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("All Category", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='all_category' value=\"$ssf_wp_vars[all_category]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "select_label", "default" => "Select", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Select", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='select_label' value=\"$ssf_wp_vars[select_label]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "cancel_label", "default" => "Cancel", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Cancel", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='cancel_label' value=\"$ssf_wp_vars[cancel_label]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "filter_label", "default" => "Filters", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Filter", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='filter_label' value=\"$ssf_wp_vars[filter_label]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "short_search_label", "default" => "Search", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Search", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='short_search_label' value=\"$ssf_wp_vars[short_search_label]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "ssf_next_label", "default" => "Next", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Next", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='ssf_next_label' value=\"$ssf_wp_vars[ssf_next_label]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "ssf_prev_label", "default" => "Prev", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Prev", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='ssf_prev_label' value=\"$ssf_wp_vars[ssf_prev_label]\" size='14'>","stripslashes" => 1);


$ssf_wp_mdo[] = array("field_name" => "description_label", "default" => "Description", "input_zone" => "labels", "output_zone" => "description_label", "label" => __("Description", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='description_label' value=\"".$ssf_wp_vars['description_label']."\" size='14'>", "stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "website_label", "default" => "Website", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Website", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='website_label' value=\"".$ssf_wp_vars['website_label']."\" size='14'>", "stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "exturl_label", "default" => "External URL", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("External URL", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='exturl_label' value=\"".$ssf_wp_vars['exturl_label']."\" size='14'>", "stripslashes" => 1);


$ssf_wp_mdo[] = array("field_name" => "exturl_link", "default" => "true", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("External link", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='exturl_link'>$exturl_link</select>", "stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "hours_label", "default" => "Operating Hours", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Hours", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='ssf_wp_hours_label' value=\"".$ssf_wp_vars['hours_label']."\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "phone_label", "default" => "Telephone", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Telephone", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='ssf_wp_phone_label' value=\"".$ssf_wp_vars['phone_label']."\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "fax_label", "default" => "Fax", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Fax", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='ssf_wp_fax_label' value=\"".$ssf_wp_vars['fax_label']."\" size='14'>","stripslashes" => 1);


$zip_label="";
$zip_label_settings["".__("On", SSF_WP_TEXT_DOMAIN).""]="true";
$zip_label_settings["".__("Off", SSF_WP_TEXT_DOMAIN).""]="false";


foreach($zip_label_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['zip_label_show']==$value)? " selected " : "";
	$zip_label.="<option value='$value' $selected2>$key</option>\n";
}


$ssf_wp_mdo[] = array("field_name" => "zip_label", "default" => "Zip", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Zip", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='ssf_wp_zip_label' value=\"".$ssf_wp_vars['zip_label']."\" size='14'>");

$ssf_wp_mdo[] = array("field_name" => "zip_label_show", "default" => "false", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Zip label show", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='zip_label_show'>$zip_label</select>", "stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "state_label", "default" => "State", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("State", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='ssf_wp_state_label' value=\"".$ssf_wp_vars['state_label']."\" size='14'>","stripslashes" => 1);

$state_label="";
$state_label_settings["".__("On", SSF_WP_TEXT_DOMAIN).""]="true";
$state_label_settings["".__("Off", SSF_WP_TEXT_DOMAIN).""]="false";


foreach($state_label_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['state_label_show']==$value)? " selected " : "";
	$state_label.="<option value='$value' $selected2>$key</option>\n";
}


$ssf_wp_mdo[] = array("field_name" => "state_label_show", "default" => "false", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("State label show", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='state_label_show'>$state_label</select>", "stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "email_label", "default" => "Email", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js",  "label" => __("Email", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='ssf_wp_email_label' value=\"".$ssf_wp_vars['email_label']."\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "direction_label", "default" => "Get Directions", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js",  "label" => __("Get Directions", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='direction_label' value=\"".$ssf_wp_vars['direction_label']."\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "streetview_label", "default" => "Street View", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js",  "label" => __("Street View", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='streetview_label' value=\"".$ssf_wp_vars['streetview_label']."\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "Url_label", "default" => "View More", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("View More", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='Url_label' value=\"$ssf_wp_vars[Url_label]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "ssf_contact_us", "default" => "Contact Us", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Contact Us", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='ssf_contact_us' value=\"$ssf_wp_vars[ssf_contact_us]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "ssf_close_btn", "default" => "Close", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Pop-Up Close Label", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='ssf_close_btn' value=\"$ssf_wp_vars[ssf_close_btn]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "by_distance_label", "default" => "By Radius", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("By Radius", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='by_distance_label' value=\"$ssf_wp_vars[by_distance_label]\" size='14'>","stripslashes" => 1);

// notification


$ssf_wp_mdo[] = array("field_name" => "loadingGoogleMap", "default" => "Loading Google Maps...", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Loading Google Maps...", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='loadingGoogleMap' value=\"".$ssf_wp_vars['loadingGoogleMap']."\" size='14'>", "stripslashes" => 1);					
$ssf_wp_mdo[] = array("field_name" => "loadingGoogleMapUtilities", "default" => "Loading Google Map Utilities...", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Loading Google Map Utilities...", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='loadingGoogleMapUtilities' value=\"".$ssf_wp_vars['loadingGoogleMapUtilities']."\" size='14'>", "stripslashes" => 1);
$ssf_wp_mdo[] = array("field_name" => "startSearch", "default" => "Load complete. Start your search!", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Load complete. Start your search!", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='startSearch' value=\"".$ssf_wp_vars['startSearch']."\" size='14'>", "stripslashes" => 1);
$ssf_wp_mdo[] = array("field_name" => "gettingUserLocation", "default" => "Getting your current location...", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Getting your current location...", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='gettingUserLocation' value=\"".$ssf_wp_vars['gettingUserLocation']."\" size='14'>", "stripslashes" => 1);
$ssf_wp_mdo[] = array("field_name" => "lookingForNearbyStores", "default" => "Looking for nearby stores...", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Looking for nearby stores...", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='lookingForNearbyStores' value=\"".$ssf_wp_vars['lookingForNearbyStores']."\" size='14'>", "stripslashes" => 1);
$ssf_wp_mdo[] = array("field_name" => "lookingForStoresNearLocation", "default" => "Looking for nearby stores...", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Looking for nearby stores...", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='lookingForStoresNearLocation' value=\"".$ssf_wp_vars['lookingForStoresNearLocation']."\" size='14'>", "stripslashes" => 1);
$ssf_wp_mdo[] = array("field_name" => "filteringStores", "default" => "Filtering for nearby stores...", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Filtering for nearby stores...", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='filteringStores' value=\"".$ssf_wp_vars['filteringStores']."\" size='14'>", "stripslashes" => 1);
$ssf_wp_mdo[] = array("field_name" => "cantLocateUser", "default" => "We are having trouble locating you. Try using our search and filter functions instead.", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("We are having trouble locating you. Try using our search and filter functions instead.", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='cantLocateUser' value=\"".$ssf_wp_vars['cantLocateUser']."\" size='14'>", "stripslashes" => 1);
$ssf_wp_mdo[] = array("field_name" => "notAllowedUserLocation", "default" => "Location service is not enabled.", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Location service is not enabled.", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='notAllowedUserLocation' value=\"".$ssf_wp_vars['notAllowedUserLocation']."\" size='14'>", "stripslashes" => 1);
$ssf_wp_mdo[] = array("field_name" => "noStoresNearSearchLocation", "default" => "No nearby were found. Why not try a different location?", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("No nearby were found. Why not try a different location?", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='noStoresNearSearchLocation' value=\"".$ssf_wp_vars['noStoresNearSearchLocation']."\" size='14'>", "stripslashes" => 1);
$ssf_wp_mdo[] = array("field_name" => "noStoresNearUser", "default" => "No nearby were found. Why not try using our search?", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("No nearby were found. Why not try using our search?", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='noStoresNearUser' value=\"".$ssf_wp_vars['noStoresNearUser']."\" size='14'>", "stripslashes" => 1);
$ssf_wp_mdo[] = array("field_name" => "noStoresFromFilter", "default" => "No nearby were found. Try using different filter options instead.", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("No nearby were found. Try using different filter options instead.", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='noStoresFromFilter' value=\"".$ssf_wp_vars['noStoresFromFilter']."\" size='14'>", "stripslashes" => 1);
$ssf_wp_mdo[] = array("field_name" => "cantGetStoresInfo", "default" => "It seems that we are unable to load stores information. Please try again later.", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("It seems that we are unable to load stores information. Please try again later.", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='cantGetStoresInfo' value=\"".$ssf_wp_vars['cantGetStoresInfo']."\" size='14'>", "stripslashes" => 1);
$ssf_wp_mdo[] = array("field_name" => "noStoresFound", "default" => "No nearby stores found.", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("No nearby stores found.", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='noStoresFound' value=\"".$ssf_wp_vars['noStoresFound']."\" size='14'>", "stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "storesFound", "default" => "Nearby stores found.", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Nearby stores found", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='storesFound' value=\"".$ssf_wp_vars['storesFound']."\" size='14'>", "stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "ssfContinueAnyway", "default" => "Continue anyway", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Continue anyway", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='ssfContinueAnyway' value=\"".$ssf_wp_vars['ssfContinueAnyway']."\" size='14'>", "stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "ssfShareLocation", "default" => "Share my location", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", 
"label" => __("Share my location", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='ssfShareLocation' value=\"".$ssf_wp_vars['ssfShareLocation']."\" size='14'>", "stripslashes" => 1);



$ssf_wp_mdo[] = array("field_name" => "generalError", "default" => "We have encountered an error.", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("We have encountered an error.", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='generalError' value=\"".$ssf_wp_vars['generalError']."\" size='14'>", "stripslashes" => 1);


// region


$ssf_wp_mdo[] = array("field_name" => "region_show", "default" => "1", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("By Region Visible", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='region_show'>\n$set_region</select>", "stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "category_show", "default" => "1", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Categories Visible", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='category_show'>\n$set_category</select>", "stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "show_all_show", "default" => "1", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Show All link Visible", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='show_all_show'>\n$set_show_all</select>", "stripslashes" => 1);

$SsfRadiusShow="";
foreach($labeled_marker_settings as $key=>$value) {
	$selected2=($ssf_wp_vars['ssf_radius_show']==$value)? " selected " : "";
	$SsfRadiusShow.="<option value='$value' $selected2>$key</option>\n";
}

$ssf_wp_mdo[] = array("field_name" => "ssf_radius_show", "default" => "1", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("By Radius Visible", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='ssf_radius_show'>\n$SsfRadiusShow</select>", "stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "show_image_list", "default" => "yes", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Result List Header", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='show_image_list'>\n$image_set</select>", "stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "show_result_list", "default" => "1", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Show Result List", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='show_result_list'>\n$show_result_list</select>", "stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "StreetView_set", "default" => "yes", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Show Street View", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='StreetView_set'>\n$StreetView_settings</select>", "stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "GetDirTop_set", "default" => "yes", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Get Direction Infowindow", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='GetDirTop_set'>\n$GetDirTop_settings</select>", "stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "GetDirBottom_set", "default" => "yes", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Get Direction Results", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='GetDirBottom_set'>\n$GetDirBottom_settings</select>", "stripslashes" => 1);


$ssf_wp_mdo[] = array("field_name" => "show_search_bar", "default" => "1", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Show Search Bar", SSF_WP_TEXT_DOMAIN), "input_template" => "<select name='show_search_bar'>\n$show_search_bar</select>", "stripslashes" => 1);





//Contact us form Label

$ssf_wp_mdo[] = array("field_name" => "contact_us_store", "default" => "Contact Store", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Contact Heading Label", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='contact_us_store' value=\"$ssf_wp_vars[contact_us_store]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "contact_us_name", "default" => "Name", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Contact Name", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='contact_us_name' value=\"$ssf_wp_vars[contact_us_name]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "contact_plc_name", "default" => "Please enter your name", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Name Placeholder", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='contact_plc_name' value=\"$ssf_wp_vars[contact_plc_name]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "contact_us_email", "default" => "Email", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Contact E-mail", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='contact_us_email' value=\"$ssf_wp_vars[contact_us_email]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "contact_plc_email", "default" => "Please enter your email address", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Email Placeholder", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='contact_plc_email' value=\"$ssf_wp_vars[contact_plc_email]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "contact_us_phone", "default" => "Telephone", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Contact Telephone", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='contact_us_phone' value=\"$ssf_wp_vars[contact_us_phone]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "contact_plc_phone", "default" => "Please enter your number", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Telephone Placeholder", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='contact_plc_phone' value=\"$ssf_wp_vars[contact_plc_phone]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "contact_us_msg", "default" => "Message", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Contact Message", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='contact_us_msg' value=\"$ssf_wp_vars[contact_us_msg]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "contact_plc_msg", "default" => "Please enter your Message", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Message Placeholder", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='contact_plc_msg' value=\"$ssf_wp_vars[contact_plc_msg]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "contact_us_btn", "default" => "Send Message", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Submit Button", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='contact_us_btn' value=\"$ssf_wp_vars[contact_us_btn]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "msg_sucess", "default" => "Message sent successfully", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Message sent successfully", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='msg_sucess' value=\"$ssf_wp_vars[msg_sucess]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "msg_fail", "default" => "Message delivery failed", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Message delivery failed", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='msg_fail' value=\"$ssf_wp_vars[msg_fail]\" size='14'>","stripslashes" => 1);

/**Ratting and review settings **/
$ssf_wp_mdo[] = array("field_name" => "review_label", "default" => "reviews", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Reviews", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='review_label' value=\"$ssf_wp_vars[review_label]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "review_and_ratings", "default" => "Reviews & Ratings", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Reviews & Ratings", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='review_and_ratings' value=\"$ssf_wp_vars[review_and_ratings]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "ratting_label", "default" => "Rating", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Rating", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='ratting_label' value=\"$ssf_wp_vars[ratting_label]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "reviewed_by", "default" => "Reviewed by", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Reviewed by", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='reviewed_by' value=\"$ssf_wp_vars[reviewed_by]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "review_submit_button", "default" => " Submit Your Review", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Reviews Submit button", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='review_submit_button' value=\"$ssf_wp_vars[review_submit_button]\" size='14'>","stripslashes" => 1);


$ssf_wp_mdo[] = array("field_name" => "no_ratting_msg", "default" => "No ratings for this product", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("No Rating message", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='no_ratting_msg' value=\"$ssf_wp_vars[no_ratting_msg]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "ratting_submit_msg", "default" => "Your rating has been added successfully", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Rating successfully  message", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='ratting_submit_msg' value=\"$ssf_wp_vars[ratting_submit_msg]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "allready_voted_msg", "default" => "You have already voted", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("You have already voted", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='allready_voted_msg' value=\"$ssf_wp_vars[allready_voted_msg]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "login_button_msg", "default" => "Please sign in / sign up to leave rating and review.", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Login button label", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='login_button_msg' value=\"$ssf_wp_vars[login_button_msg]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "rating_comment_label", "default" => "Comments", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Comments Label", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='rating_comment_label' value=\"$ssf_wp_vars[rating_comment_label]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "reCAPTCHA_warning", "default" => "Please enter your reCAPTCHA", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("reCAPTCHA validation message", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='reCAPTCHA_warning' value=\"$ssf_wp_vars[reCAPTCHA_warning]\" size='14'>","stripslashes" => 1);

$ssf_wp_mdo[] = array("field_name" => "rating_select_validation", "default" => "Please enter your rating", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Rating validation message", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='rating_select_validation' value=\"$ssf_wp_vars[rating_select_validation]\" size='14'>","stripslashes" => 1);


/**.** End here **.**/
?>