<?php
print "<table width='100%' cellpadding='5px' cellspacing='0' style='border:solid silver 1px' id='mgmt_bar' class='widefat'>
<thead><tr>

<th style='/*background-color:#000;*/ vertical-align:middle; font-family:inherit; font-size:12px;'>$addStore<input class='button-primary' type='button' value='".__("Delete", SSF_WP_TEXT_DOMAIN)."' onclick=\"if(confirm('".__("Are you sure you want to remove the store[s)", SSF_WP_TEXT_DOMAIN)."?')){LF=document.forms['locationForm'];LF.act.value='delete';LF.submit();}else{return false;}\"></th>
";
$extra=(!empty($extra))? $extra : "" ;

if (function_exists("addto_ssf_wp_hook")) {addto_ssf_wp_hook('ssf_wp_mgmt_bar_links', 'export_links', '', '', 'csv-xml-importer-exporter');} 
$mgmt_bgcolor=((!function_exists("addto_ssf_wp_hook") && file_exists(SSF_WP_ADDONS_PATH."/csv-xml-importer-exporter/export-links.php")) || (!empty($ssf_wp_hooks['ssf_wp_mgmt_bar_links'])) )? "/*background-color:#e6e6e6; background-image:none;*/ border-left: solid #ccc 1px; border-right: solid #ccc 1px;" : "";
print "<th style='width:30%; text-align:center; color:black; font-family:inherit; font-size:12px; {$mgmt_bgcolor} /**/' class='youhave'>";
function export_links() {
		global $ssf_wp_uploads_path, $web_domain, $extra, $ssf_wp_base, $ssf_wp_uploads_base, $text_domain;
		if (file_exists(SSF_WP_ADDONS_PATH."/csv-xml-importer-exporter/export-links.php")) {
			$ssf_wp_real_base=$ssf_wp_base; $ssf_wp_base=$ssf_wp_uploads_base;
			include(SSF_WP_ADDONS_PATH."/csv-xml-importer-exporter/export-links.php");
			$ssf_wp_base=$ssf_wp_real_base;
		}
		
} 
if (!function_exists("addto_ssf_wp_hook")) {export_links();}
if (function_exists("do_ssf_wp_hook")) { do_ssf_wp_hook('ssf_wp_mgmt_bar_links', 'select-right');  }

print "</th>";
print "<th style='/*background-color:#000;*/ width:50%; text-align:right; /*color:white;*/ vertical-align:middle; font-family:inherit; font-size:12px;'>";

  function multi_updater() {
	global $ssf_wp_uploads_path, $text_domain, $web_domain;
	if (file_exists(SSF_WP_ADDONS_PATH."/multiple-field-updater/multiple-field-update-form.php") && (ssf_wp_data('ssf_wp_location_updater_type')=="Multiple Fields" || function_exists("do_ssf_wp_hook"))) {
		include(SSF_WP_ADDONS_PATH."/multiple-field-updater/multiple-field-update-form.php");
	}
  }
	
	function tagger() {
		print "<span style='line-height:28px'>".__("Tags", SSF_WP_TEXT_DOMAIN)."</span>&nbsp;<input name='ssf_wp_tags' type='text'>&nbsp;<input class='button-primary' type='button' value='".__("Add Tag", SSF_WP_TEXT_DOMAIN)."' onclick=\"LF=document.forms['locationForm'];LF.act.value='add_tag';LF.submit();\">&nbsp;<input class='button-primary' type='button' value='".__("Remove Tag", SSF_WP_TEXT_DOMAIN)."' onclick=\"if(confirm('".__("Are you sure you wish to remove the tags", SSF_WP_TEXT_DOMAIN)."?')){LF=document.forms['locationForm'];LF.act.value='remove_tag';LF.submit();}else{return false;}\">";
	}
  
	if (function_exists("addto_ssf_wp_hook")) {
	    if (is_dir(SSF_WP_ADDONS_PATH."/multiple-field-updater/")){
		addto_ssf_wp_hook('ssf_wp_mgmt_bar_form', 'multi_updater', '', '', 'multiple-field-updater');
	    }
	    addto_ssf_wp_hook('ssf_wp_mgmt_bar_form', 'tagger');
	} elseif (!function_exists("addto_ssf_wp_hook")) {
		if (file_exists(SSF_WP_ADDONS_PATH."/multiple-field-updater/multiple-field-update-form.php") && ssf_wp_data('ssf_wp_location_updater_type')=="Multiple Fields") {
			multi_updater();
		} else {
			 tagger();
		}
	}

	if (function_exists("do_ssf_wp_hook")) {do_ssf_wp_hook('ssf_wp_mgmt_bar_form', 'select');
  }
print "</th></tr></thead></table>
";

?>