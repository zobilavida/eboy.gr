<?php
global $wpdb;
$ssf_wp_top_nav_hash[]='information';
$ssf_wp_top_nav_links[SSF_WP_INFORMATION_PAGE]=__("<span class='fa fa-bolt'>&nbsp;</span> Quick Start", SSF_WP_TEXT_DOMAIN);
$ssf_wp_top_nav_hash[]='stores';
$ssf_wp_top_nav_links[SSF_WP_MANAGE_LOCATIONS_PAGE]=__("<span class='fa fa-map-marker'>&nbsp;</span> Stores", SSF_WP_TEXT_DOMAIN);
$ssf_wp_top_nav_hash[]='add-store';
$ssf_wp_top_nav_links[SSF_WP_ADD_LOCATIONS_PAGE]=__("<span class='fa fa-plus'>&nbsp;</span> Add a Store", SSF_WP_TEXT_DOMAIN);
$ssf_wp_top_nav_hash[]='region';
$ssf_wp_top_nav_links[SSF_WP_MANAGE_REGION_PAGE]=__("<span class='fa fa-globe'>&nbsp;</span> Regions", SSF_WP_TEXT_DOMAIN);
$ssf_wp_top_nav_hash[]='add-region';
$ssf_wp_top_nav_links[SSF_WP_ADD_REGION_PAGE]=__("<span class='fa fa-plus'>&nbsp;</span> Add a Region", SSF_WP_TEXT_DOMAIN);
$locales=$wpdb->get_results("SELECT * FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_wp_addon_name='ssf-custom-marker-wp' AND ssf_wp_addon_status='on'", ARRAY_A);
if(!empty($locales))
{ 
	$ssf_wp_top_nav_hash[]='categories';
	$ssf_wp_top_nav_links[SSF_WP_MARKERS_PAGE]=__("<span class='fa fa-bookmark'>&nbsp;</span> Categories", SSF_WP_TEXT_DOMAIN);
}
$ssf_wp_top_nav_hash[]='import';
$ssf_wp_top_nav_links[SSF_WP_IMPORT_PAGE]=__("<span class='fa  fa-book'>&nbsp;</span> Import/Export", SSF_WP_TEXT_DOMAIN);
$ssf_wp_top_nav_hash[]='Add-ons';
$ssf_wp_top_nav_links[SSF_WP_ADD_ONS_PAGE]=__("<span class='fa fa-plug'>&nbsp;</span> Add-ons", SSF_WP_TEXT_DOMAIN);
$ssf_wp_top_nav_hash[]='settings'; 
$ssf_wp_top_nav_links[SSF_WP_SETTINGS_PAGE1]=__("<span class='fa fa-cog'>&nbsp;</span> Settings", SSF_WP_TEXT_DOMAIN);

if (function_exists("do_ssf_wp_hook")){
	do_ssf_wp_hook("ssf_wp_top_nav_links", "", array(&$ssf_wp_top_nav_hash, &$ssf_wp_top_nav_links, &$ssf_wp_top_nav_sub_links));
}
	
print "<br>";
$style_var = "";
if (!empty($_POST['ssf_wp_thanks'])) {$ssf_wp_vars['thanks'] = $_POST['ssf_wp_thanks']; unset($_POST);}
$ssf_wp_thanks = (!empty($ssf_wp_vars['thanks']))? $ssf_wp_vars['thanks'] : "";
print <<<EOQ
<ul class="tablist">\n
EOQ;
$ctr=0; $tsn_links_js="<script>\nvar tsn_link_arr = [];"; $tsn_links_output="";
$tm_st = ((time() - strtotime($ssf_wp_vars["start"]))/60/60/24>=30);
foreach ($ssf_wp_top_nav_links as $key=>$value) {
	$current_var=(preg_match("@$_GET[page]@",$key))? "current_top_link" : "" ;
	
	if($ssf_wp_top_nav_hash[$ctr]=="add-store"){
	
	$addStore= "<a href=\"$key\"  id='__$ssf_wp_top_nav_hash[$ctr]' class='button button-primary' style=''>$value</a>\n";
	
	}else if($ssf_wp_top_nav_hash[$ctr]=="add-region"){
	
	$addRegion="<a href=\"$key\"  id='__$ssf_wp_top_nav_hash[$ctr]' class='button button-primary' style=''>$value</a>\n";
	
	} else {
	print "<li class=\"top_nav_li $ssf_wp_top_nav_hash[$ctr]\" id=\"$current_var\"><a href=\"$key\"  id='__$ssf_wp_top_nav_hash[$ctr]' class='button button-primary' style=''>$value</a></li>\n";
	}

	$tsn_links_js.="tsn_link_arr['$ssf_wp_top_nav_hash[$ctr]']='';";

	if (!empty($ssf_wp_top_nav_sub_links[$ssf_wp_top_nav_hash[$ctr]])) {
		$cur = ""; $ctr2=0;
		foreach ($ssf_wp_top_nav_sub_links[$ssf_wp_top_nav_hash[$ctr]] as $key2=>$value2) {
			if (preg_match("@$ssf_wp_top_nav_hash[$ctr]@", $_SERVER['REQUEST_URI'])) {
				$cur = "current_sub_link";
				$tsn_links_output.="<a href='$value2' class='$cur'>$key2</a>";
			}
			
			$tsn_links_js .= "tsn_link_arr['$ssf_wp_top_nav_hash[$ctr]']+=\"<a href='$value2' class='top_nav_sub_a $cur' id='$ssf_wp_top_nav_hash[$ctr]_$ctr2' onmouseover='level3_links(this, &quot;__$ssf_wp_top_nav_hash[$ctr]&quot;, &quot;show&quot;)' onmouseout='level3_links(this, &quot;__$ssf_wp_top_nav_hash[$ctr]&quot;, &quot;hide&quot;)'>$key2</a>\";";
				if (!empty($ssf_wp_top_nav_sub2_links[$value2])) {
					$tsn_links_js.= "tsn_link_arr['$ssf_wp_top_nav_hash[$ctr]_$ctr2']='';";
					foreach ($ssf_wp_top_nav_sub2_links[$value2] as $level3_title => $level3_url) {
						
						$tsn_links_js.= "tsn_link_arr['{$ssf_wp_top_nav_hash[$ctr]}_{$ctr2}']+=\"<a href='$level3_url' class='' id=''>$level3_title</a>\"; " ;
					}
				}
			$ctr2++;
		}
	}
	$ctr++;
}


$tsn_links_js.="jQuery(document).ready(function(){ {$style_var} });\n";
$tsn_links_js.="</script>";


$ssf_wp_vars['ssf_wp_latest_version_check_time'] = (empty($ssf_wp_vars['ssf_wp_latest_version_check_time']))? date("Y-m-d H:i:s") : $ssf_wp_vars['ssf_wp_latest_version_check_time'];
if (empty($ssf_wp_vars['ssf_wp_latest_version']) || (time() - strtotime($ssf_wp_vars['ssf_wp_latest_version_check_time']))/60>=(60*12)){ 
	
	$ssf_wp_latest_version = '';
	
	
	$ssf_wp_vars['ssf_wp_latest_version_check_time'] = date("Y-m-d H:i:s");
	$ssf_wp_vars['ssf_wp_latest_version'] = $ssf_wp_latest_version;
} else {
	$ssf_wp_latest_version = $ssf_wp_vars['ssf_wp_latest_version'];
}

if (strnatcmp($ssf_wp_latest_version, $ssf_wp_version) > 0) { 
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
		preg_match("/\n[ ]*stable tag:[ ]?([^\n]+)(\n)?/i", $rm_txt, $cv); //var_dump($rm_txt); var_dump($cv);
		$ap_version = (!empty($cv[1]))? trim($cv[1]) : "1.0" ;
	} else {$ap_version = "1.0";}
	
	$ap_title = ucwords(str_replace("-", " ", SSF_WP_ADDONS_PLATFORM_DIR));
	$ap_update_msg = ucwords(str_replace("-", " ", SSF_WP_ADDONS_PLATFORM_DIR))." Version $ap_latest_version is available";
	$ap_update = (strnatcmp($ap_latest_version, $ap_version) > 0)? "&nbsp;|&nbsp;<a href='#' style='color:#900; font-weight: bold;' onclick='alert(\"$ap_title v$ap_latest_version ".__("is available for download -- you are currently using v$ap_version. \\n\\n\\t1) Please use the download link from the email receipt sent to you for your $ap_title purchase, \\n\\n\\t2) Extract the zip file to your computer, then \\n\\n\\t3) Upload the &apos;".SSF_WP_ADDONS_PLATFORM_DIR."&apos; folder to &apos;".SSF_WP_ADDONS_PATH."&apos; on your website using FTP for the latest $ap_title version", SSF_WP_TEXT_DOMAIN).".\"); return false;' title='$ap_update_msg'>Get the latest version{$ap_latest_version}</a>" : "" ;
} else { $ap_update = ""; }



print "</ul>
<div class='clearfix'></div>
";

if (!extension_loaded("curl")) {
	if (!empty($_GET['curl_msg']) && $_GET['curl_msg'] == 1){$ssf_wp_vars['curl_msg'] = 'hide'; }
	if (empty($ssf_wp_vars['curl_msg']) || $ssf_wp_vars['curl_msg'] != 'hide') {
		print "<br><div class='ssf-wp-menu-alert' style='line-height: 22px;'><b>".__("Important Note", SSF_WP_TEXT_DOMAIN).":</b><br>
		".__("It appears that you do not have <a href='http://us3.php.net/manual/en/book.curl.php' target='_blank'>cURL</a> actively running on this website.  cURL or <a href='http://us3.php.net/manual/en/function.file-get-contents.php' target='_blank'>file_get_contents()</a> needs to be active in order to run Super Store Finder", SSF_WP_TEXT_DOMAIN).".
		<br>
(<a href='".$_SERVER['REQUEST_URI']."&curl_msg=1'>".__("Hide Message", SSF_WP_TEXT_DOMAIN)."</a>)
		</div>";
			
	}
}


if (!empty($_GET['file_perm_msg']) && $_GET['file_perm_msg'] == 1){$ssf_wp_vars['file_perm_msg'] = 'hide'; }
if (empty($ssf_wp_vars['file_perm_msg']) || $ssf_wp_vars['file_perm_msg'] != 'hide') {
	$ssf_wp_vars['file_perm_check_time'] = (empty($ssf_wp_vars['file_perm_check_time']))? date("Y-m-d H:i:s") : $ssf_wp_vars['file_perm_check_time'];
	
	if (!isset($ssf_wp_vars['perms_need_update']) || $ssf_wp_vars['perms_need_update'] == 1 || ($ssf_wp_vars['perms_need_update'] == 0 && (time() - strtotime($ssf_wp_vars['file_perm_check_time']))/60 >= (60*1)) ) { // 1-hr checks, when last check showed no files needed permissions updates
		
		ssf_wp_permissions_check();
		$ssf_wp_vars['file_perm_check_time'] = date("Y-m-d H:i:s");
	}
}


if (!empty($_GET['csv_imp_msg']) && $_GET['csv_imp_msg'] == 1){$ssf_wp_vars['csv_imp_msg'] = 'hide'; }
if (empty($ssf_wp_vars['csv_imp_msg']) || $ssf_wp_vars['csv_imp_msg'] != 'hide') {
	$max_input_vars_value = ini_get('max_input_vars');
	$max_input_default = ( !empty($max_input_vars_value) && $max_input_vars_value <= 1000 ); 
	$csv_needs_mod = 
	( 
		( (file_exists(SSF_WP_ADDONS_PATH."/csv-xml-importer-exporter/csv-xml-importer-exporter.php") 
			&& ssf_wp_data('ssf_wp_activation_csv-xml-importer-exporter')!==NULL) 
			|| (file_exists(SSF_WP_ADDONS_PATH."/csv-importer-exporter-g2/csv-importer-exporter-g2.php") 
			&& ssf_wp_data('ssf_wp_activation_csv-importer-exporter-g2')!==NULL)
		) 
		&& strnatcmp(phpversion(), '5.3.9') >= 0 
		&& $max_input_default
	);
	
	if ($csv_needs_mod) {

	}
}


$ssf_wp_notice_id = 'files_in_addons_dir';
if (!empty($_GET[$ssf_wp_notice_id]) && $_GET[$ssf_wp_notice_id] == 1){$ssf_wp_vars[$ssf_wp_notice_id] = 'hide'; }
if (empty($ssf_wp_vars[$ssf_wp_notice_id]) || $ssf_wp_vars[$ssf_wp_notice_id] != 'hide') {
	$addons_contents = glob(SSF_WP_ADDONS_PATH."/*.*", GLOB_NOSORT);
	if (!empty($addons_contents)) {
	   foreach ($addons_contents as $a_item) {
		$the_a_file = str_replace(SSF_WP_ADDONS_PATH."/", "", $a_item);
		if (!is_dir($a_item) && $the_a_file != "index.php" && $the_a_file != "dummy.php" && !preg_match("@\.zip$@", $the_a_file) && !preg_match("@error@", $the_a_file) ) {
			$not_a_dir[] = $the_a_file;
		}
	   }
	   if (!empty($not_a_dir)) {
		print "<br><div class='ssf-wp-menu-alert' style='line-height: 22px;'><b>".__("Important Note", SSF_WP_TEXT_DOMAIN).":</b><br>
		".__("You have placed files in your 'addons' directory here", SSF_WP_TEXT_DOMAIN).": <b>/".str_replace(ABSPATH, "", SSF_WP_ADDONS_PATH)."/</b>. ".__("There should only be folders.  All addon-related files need to be inside of their proper addon folder in order to work with Store Locator", SSF_WP_TEXT_DOMAIN).". (<b>e.g.</b> <b style='color:DarkGreen'>".__("Correct", SSF_WP_TEXT_DOMAIN).":</b> /addons<b>/addons-platform/</b>addons-platform.php, <b style='color: DarkRed'>".__("Incorrect", SSF_WP_TEXT_DOMAIN).":</b> /addons/addons-platform.php) <br><br><b>".__("Files that need to be moved", SSF_WP_TEXT_DOMAIN)."</b>: ";
		print "".implode($not_a_dir, ",  ");
		print "<br><div style='float:right'>
(<a href='".$_SERVER['REQUEST_URI']."&{$ssf_wp_notice_id}=1'>".__("Hide Message Permanently", SSF_WP_TEXT_DOMAIN)."</a>)</div>
<br clear='all'>
		</div>";
	   }
      }
}


if (!empty($_POST) && function_exists("do_ssf_wp_hook")){ do_ssf_wp_hook("ssf_wp_admin_form_post"); /*print "<br>";*/ }
if (function_exists("do_ssf_wp_hook")) { do_ssf_wp_hook("ssf_wp_admin_data"); } 
?>