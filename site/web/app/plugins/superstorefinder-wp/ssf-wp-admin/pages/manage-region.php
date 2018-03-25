<?php
include_once(SSF_WP_INCLUDES_PATH."/top-nav.php");
?>
<div class='wrap'>
<?php

if (!empty($_GET['edit'])){ print "<style>#wpadminbar {display:none !important;}</style>"; }

ssf_wp_initialize_variables();

$hidden="";
foreach($_GET as $key=>$val) {
	
	if ($key!="q" && $key!="o" && $key!="d" && $key!="changeView" && $key!="start") {
		$hidden.="<input type='hidden' value='$val' name='$key'>\n"; 
	}
}

include(SSF_WP_ACTIONS_PATH."/process-region.php");

print "<table style='width:100%'><tr><td>";
print "<div class='mng_loc_forms_links'>";

if (empty($_GET['q'])){ $_GET['q']=""; }
$search_value = ($_GET['q']==="")? "" : ssf_comma(stripslashes($_GET['q'])) ;

print "<div><form name='searchForm'><!--input type='button' class='button-primary' value='Add New' onclick=\"\$aLD=jQuery('#addLocationsDiv');if(\$aLD.css('display')!='block'){\$aLD.fadeIn();}else{\$aLD.fadeOut();}return false;\">&nbsp;&nbsp;--><input value='".$search_value."' name='q' type='text' placeholder='Search'>$hidden</form></div>";

print "<div>
<nobr><select name='ssf_wp_admin_locations_per_page' onchange=\"LF=document.forms['locationForm'];salpp=document.createElement('input');salpp.type='hidden';salpp.value=this.value;salpp.name='ssf_wp_admin_locations_per_page';LF.appendChild(salpp);LF.act.value='locationsPerPage';LF.submit();\">
<optgroup label='# ".__("Locations", SSF_WP_TEXT_DOMAIN)."'>";

$opt_arr=array(10,25,50,100,200,300,400,500,1000,2000,4000,5000,10000);
foreach ($opt_arr as $value) {
	$selected=($ssf_wp_admin_locations_per_page==$value)? " selected " : "";
	print "<option value='$value' $selected>$value</option>";
}
print "</optgroup></select>
</nobr>
</div>";

if (!empty($_GET['_wpnonce'])){ $_SERVER['REQUEST_URI'] = str_replace("&_wpnonce=".$_GET['_wpnonce'], "", $_SERVER['REQUEST_URI']);}

$is_normal_view = ($ssf_wp_vars['location_table_view']=="Normal");
$is_using_tagger = (ssf_wp_data('ssf_wp_location_updater_type')=="Tagging");

function regeocoding_link(){
	global $wpdb, $where, $master_check, $ssf_wp_uploads_path, $web_domain, $extra, $ssf_wp_base, $ssf_wp_uploads_base, $text_domain;
	if (file_exists(SSF_WP_ADDONS_PATH."/csv-xml-importer-exporter/re-geo-link.php")) {
		include(SSF_WP_ADDONS_PATH."/csv-xml-importer-exporter/re-geo-link.php");	
	}
}
if (function_exists("addto_ssf_wp_hook")) {addto_ssf_wp_hook('ssf_wp_mgmt_bar_links', 'regeocoding_link', '', '', 'csv-xml-importer-exporter');} 
else {regeocoding_link();}

if (file_exists(SSF_WP_ADDONS_PATH."/multiple-field-updater/multiLocationUpdate.php") && !function_exists("do_ssf_wp_hook")) {
	print "<div> | ".$updater_type."</div>";
}

print "</div>";
print "</td><td>";


ssf_wp_region_query_defaults();

if(file_exists(SSF_WP_ADDONS_PATH."/csv-xml-importer-exporter/re-geo-query.php")){
	include(SSF_WP_ADDONS_PATH."/csv-xml-importer-exporter/re-geo-query.php");
}
	$numMembers=$wpdb->get_results("SELECT ssf_wp_region_id FROM ".SSF_WP_REGION_TABLE." $where");
	$numMembers2=count($numMembers); 
	$start=(empty($_GET['start']))? 0 : $_GET['start'];
	$num_per_page=$ssf_wp_vars['admin_locations_per_page']; //edit this to determine how many locations to view per page of 'Manage Locations' page
	if ($numMembers2!=0) {include(SSF_WP_INCLUDES_PATH."/search-links.php");}


print "</td></tr></table>";

print "<form name='locationForm' method='post' enctype='multipart/form-data'>";

if(empty($_GET['d'])) {$_GET['d']="";} if(empty($_GET['o'])) {$_GET['o']="";}


$master_check = (!empty($master_check))? $master_check : "" ;
include(SSF_WP_INCLUDES_PATH."/region-management.php");
print "<table class='widefat' cellspacing=0 id='loc_table'>
<thead><tr>
<th colspan='1'><input type='checkbox' onclick='checkAll(this,document.forms[\"locationForm\"])' id='master_checkbox' $master_check></th>

<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_id&d=$d'>".__("ID", SSF_WP_TEXT_DOMAIN)."</a></th>";

if (function_exists("do_ssf_wp_hook") && !empty($ssf_wp_columns)){
	do_ssf_wp_location_table_header();
} else {
	
	$th_co = ($is_normal_view)? "</th>\n<th>" : ", " ;
	$th_style = ($is_normal_view)? "" : "style='white-space: nowrap;' " ;
	
	print "<th {$th_style}><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_region_name&d=$d'>".__("Region Name", SSF_WP_TEXT_DOMAIN)."</a></th>";
	print "<th {$th_style}><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_address_name&d=$d'>".__("Region Address", SSF_WP_TEXT_DOMAIN)."</a></th>";

}

print "
<th>".__("Actions", SSF_WP_TEXT_DOMAIN)."</th>
</tr></thead>";
	$o=esc_sql($o); $d=esc_sql($d); 
	$start=esc_sql($start); $num_per_page=esc_sql($num_per_page); 
	if ($locales=$wpdb->get_results("SELECT * FROM ".SSF_WP_REGION_TABLE." $where ORDER BY $o $d LIMIT $start, $num_per_page", ARRAY_A)) { 
		if (function_exists("do_ssf_wp_hook") && !empty($ssf_wp_columns)){
			
			$colspan=($ssf_wp_vars['location_table_view']!="Normal")? 	(count($ssf_wp_columns)-count($ssf_wp_omitted_columns)+4) : (count($ssf_wp_normal_columns)-3+4);
		} else {
			$colspan=($ssf_wp_vars['location_table_view']!="Normal")? 	18 : 11;
		}
		
		$bgcol="";
		foreach ($locales as $value) {
			$bgcol=($bgcol==="" || $bgcol=="#eee")?"#fff":"#eee";			
			$value=array_map("trim",$value);
			
			if (!empty($_GET['edit']) && $value['ssf_wp_region_id']==$_GET['edit']) {
				ssf_wp_single_region_info($value, $colspan, $bgcol);
			}
			else {
				if(empty($_GET['edit'])) {$_GET['edit']="";}
				$edit_link = str_replace("&edit=$_GET[edit]", "",$_SERVER['REQUEST_URI'])."&edit=" . $value['ssf_wp_region_id'] ."#a$value[ssf_wp_region_id]'";
				
				print "<tr style='background-color:$bgcol' id='ssf_wp_tr-$value[ssf_wp_region_id]'>
			<th><input type='checkbox' name='ssf_wp_region_id[]' value='$value[ssf_wp_region_id]'></th>
			
			<td> $value[ssf_wp_region_id] </td>";

				if (function_exists("do_ssf_wp_hook") && !empty($ssf_wp_columns)){
					do_ssf_wp_location_table_body($value);
				} else {
					if ($is_normal_view) {
						
						$tco_region = $tco_address = "</td>\n<td>";
						$strong_addr_open = $strong_addr_close = "";
					} else {
						$tco_region = (!empty($value['ssf_wp_region_name']) && !empty($value['ssf_wp_region_name']))? "<br>" : "" ;
						$tco_address = (!empty($value['ssf_wp_address_name']) && !empty($value['ssf_wp_address_name']))? "<br>" : "" ;
						$strong_addr_open = "<strong>"; $strong_addr_close = "</strong>";
					}
					
					print "<td> $value[ssf_wp_region_name]{$tco_region}$value[ssf_wp_address_name]";

					if ($ssf_wp_vars['location_table_view']!="Normal") {
						print "<td>$value[ssf_wp_region_name]</td>";
						print "<td>$value[ssf_wp_address_name]</td>";
					}
				}

				print "<td><a class='edit_loc_link' href='".$edit_link." id='$value[ssf_wp_region_id]'><span class='fa fa-pencil'>&nbsp;</span>".__("Edit", SSF_WP_TEXT_DOMAIN)."</a>&nbsp;| <a class='del_loc_link' href='".wp_nonce_url("$_SERVER[REQUEST_URI]&delete=$value[ssf_wp_region_id]", "delete-location_".$value['ssf_wp_region_id'])."' onclick=\"confirmClick('Are you sure you wish to delete this Region?', this.href); return false;\" id='$value[ssf_wp_region_id]'><span class='fa fa-trash'>&nbsp;</span>".__("Delete", SSF_WP_TEXT_DOMAIN)."</a></td>
				</tr>";
			}
		}
	} else {
		$cleared=(!empty($_GET['q']))? str_replace("q=".str_replace(" ", "+", $_GET['q']) , "", $_SERVER['REQUEST_URI']) : $_SERVER['REQUEST_URI'] ;
		$notice=(!empty($_GET['q']))? __("No Locations Showing for this Search of ", SSF_WP_TEXT_DOMAIN)."<b>\"$_GET[q]\"</b> | <a href='$cleared'>".__("Clear&nbsp;Results", SSF_WP_TEXT_DOMAIN)."</a> $view_link" : __("You have no available Region", SSF_WP_TEXT_DOMAIN);
		print "<tr><td colspan='5'>$notice | <a href='".SSF_WP_ADD_REGION_PAGE."'>".__("Add a Region", SSF_WP_TEXT_DOMAIN)."</a></td></tr>";
	}
	print "</table>
	<input name='act' type='hidden'><br>";
	wp_nonce_field("manage-locations_bulk");

if ($numMembers2!=0) {include(SSF_WP_INCLUDES_PATH."/search-links.php");}

print "</form>"; 
?>
</div>
<?php include(SSF_WP_INCLUDES_PATH."/ssf-wp-footer.php"); ?>
