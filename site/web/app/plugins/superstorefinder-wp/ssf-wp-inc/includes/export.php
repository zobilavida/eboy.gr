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

// general

$ssf_wp_mdo[] = array("field_name" => "default_location", "default" => "New York, US", "input_zone" => "defaults", "label" =>  __("File", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='file' name='default_location' value='$ssf_wp_vars[default_location]'> Having issues with import ?  (<a target='new' href='http://superstorefinder.net/support/knowledgebase/nothing-happens-when-i-click-import-and-geocode/'>Read more</a>)");


// styles

$ssf_wp_mdo[] = array("field_name" => "style_map_color", "default" => "", "input_zone" => "labels", "output_zone" => "ssf_wp_dyn_js", "label" => __("Export", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='text' name='style_map_color' value=\"$ssf_wp_vars[style_map_color]\" class=\"my-color-field\" >");



?>