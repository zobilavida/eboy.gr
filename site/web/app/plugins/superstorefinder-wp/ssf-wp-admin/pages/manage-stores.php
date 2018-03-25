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

include(SSF_WP_ACTIONS_PATH."/process-stores.php");

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


ssf_wp_ssf_set_query_defaults();

if(file_exists(SSF_WP_ADDONS_PATH."/csv-xml-importer-exporter/re-geo-query.php")){
	include(SSF_WP_ADDONS_PATH."/csv-xml-importer-exporter/re-geo-query.php");
}


	$numMembers=$wpdb->get_results("SELECT ssf_wp_id FROM ".SSF_WP_TABLE." $where");
	$numMembers2=count($numMembers); 
	$start=(empty($_GET['start']))? 0 : $_GET['start'];
	$num_per_page=$ssf_wp_vars['admin_locations_per_page']; //edit this to determine how many locations to view per page of 'Manage Locations' page
	if ($numMembers2!=0) {include(SSF_WP_INCLUDES_PATH."/search-links.php");}


print "</td></tr></table>";

print "<form name='locationForm' method='post' enctype='multipart/form-data'>";

if(empty($_GET['d'])) {$_GET['d']="";} if(empty($_GET['o'])) {$_GET['o']="";}


$master_check = (!empty($master_check))? $master_check : "" ;
include(SSF_WP_INCLUDES_PATH."/stores-management.php");
print "<table class='widefat' cellspacing=0 id='loc_table'>
<thead><tr >
<th colspan='1'><input type='checkbox' onclick='checkAll(this,document.forms[\"locationForm\"])' id='master_checkbox' $master_check></th>

<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_id&d=$d'>".__("ID", SSF_WP_TEXT_DOMAIN)."</a></th>";

if (function_exists("do_ssf_wp_hook") && !empty($ssf_wp_columns)){
	do_ssf_wp_location_table_header();
} else {
	
	$th_co = ($is_normal_view)? "</th>\n<th>" : ", " ;
	$th_style = ($is_normal_view)? "" : "style='white-space: nowrap;' " ;
	
	print "<th {$th_style}><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_store&d=$d'>".__("Name", SSF_WP_TEXT_DOMAIN)."</a>{$th_co}
<a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_address&d=$d'>".__("Street", SSF_WP_TEXT_DOMAIN)."</a>{$th_co}
<a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_address2&d=$d'></a>{$th_co}
<a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_city&d=$d'>".__("City", SSF_WP_TEXT_DOMAIN)."</a>{$th_co}
<a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_state&d=$d'>".__("State", SSF_WP_TEXT_DOMAIN)."</a>{$th_co}
<a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_zip&d=$d'>".__("Zip", SSF_WP_TEXT_DOMAIN)."</a></th>
<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_tags&d=$d'>".__("Tags", SSF_WP_TEXT_DOMAIN)."</a></th>";

	if ($ssf_wp_vars['location_table_view']!="Normal") {
		print "<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_description&d=$d'>".__("Description", SSF_WP_TEXT_DOMAIN)."</a></th>
<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_url&d=$d'>".__("URL", SSF_WP_TEXT_DOMAIN)."</a></th>
<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_ext_url&d=$d'>".__("External YRL", SSF_WP_TEXT_DOMAIN)."</a></th>
<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_hours&d=$d'>".__("Hours", SSF_WP_TEXT_DOMAIN)."</th>
<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_phone&d=$d'>".__("Phone", SSF_WP_TEXT_DOMAIN)."</a></th>
<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_fax&d=$d'>".__("Fax", SSF_WP_TEXT_DOMAIN)."</a></th>
<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_email&d=$d'>".__("Email", SSF_WP_TEXT_DOMAIN)."</a></th>
<th><a href='".str_replace("&o=$_GET[o]&d=$_GET[d]", "", $_SERVER['REQUEST_URI'])."&o=ssf_wp_image&d=$d'>".__("Image", SSF_WP_TEXT_DOMAIN)."</a></th>";
	}
}

print "<th>(Lat, Lon)</th>
<th colspan='1'>".__("Actions", SSF_WP_TEXT_DOMAIN)."</th>
</tr></thead>";

	$o=esc_sql($o); $d=esc_sql($d); 
	$start=esc_sql($start); $num_per_page=esc_sql($num_per_page); 
	if ($locales=$wpdb->get_results("SELECT * FROM ".SSF_WP_TABLE." $where ORDER BY $o $d LIMIT $start, $num_per_page", ARRAY_A)) { 
		if (function_exists("do_ssf_wp_hook") && !empty($ssf_wp_columns)){
			
			$colspan=($ssf_wp_vars['location_table_view']!="Normal")? 	(count($ssf_wp_columns)-count($ssf_wp_omitted_columns)+4) : (count($ssf_wp_normal_columns)-3+4);
		} else {
			$colspan=($ssf_wp_vars['location_table_view']!="Normal")? 	18 : 11;
		}
		
		$bgcol="";
		
		foreach ($locales as $value) {
			$bgcol=($bgcol==="" || $bgcol=="#eee")?"#fff":"#eee";			
			$bgcol=($value['ssf_wp_latitude']=="" || $value['ssf_wp_longitude']=="")? " rgb(255, 124, 86)" : $bgcol;			
			$value=array_map("trim",$value);
			
			if (!empty($_GET['edit']) && $value['ssf_wp_id']==$_GET['edit']) {
				ssf_wp_single_location_info($value, $colspan, $bgcol);
			}
			else {
				$value['ssf_wp_url']=(!ssf_url_test($value['ssf_wp_url']) && trim($value['ssf_wp_url'])!="")? "http://".$value['ssf_wp_url'] : $value['ssf_wp_url'] ;
				$value['ssf_wp_url']=($value['ssf_wp_url']!="")? "<a href='$value[ssf_wp_url]' target='blank'>".__("View", SSF_WP_TEXT_DOMAIN)."</a>" : "" ;
				
				$value['ssf_wp_ext_url']=(!ssf_url_test($value['ssf_wp_ext_url']) && trim($value['ssf_wp_ext_url'])!="")? "http://".$value['ssf_wp_ext_url'] : $value['ssf_wp_ext_url'] ;
				$value['ssf_wp_ext_url']=($value['ssf_wp_ext_url']!="")? "<a href='$value[ssf_wp_ext_url]' target='blank'>".__("View", SSF_WP_TEXT_DOMAIN)."</a>" : "" ;
				
				$value['ssf_wp_image']=($value['ssf_wp_image']!="")? "<a href='$value[ssf_wp_image]' target='blank'>".__("View", SSF_WP_TEXT_DOMAIN)."</a>" : "" ;
				$value['ssf_wp_description']=($value['ssf_wp_description']!="")? "<a href='#description-$value[ssf_wp_id]' rel='ssf_wp_pop'>".__("View", SSF_WP_TEXT_DOMAIN)."</a><div id='description-$value[ssf_wp_id]' style='display:none;'>".ssf_comma($value['ssf_wp_description'])."</div>" : "" ;
			
				if(empty($_GET['edit'])) {$_GET['edit']="";}
				$edit_link = str_replace("&edit=$_GET[edit]", "",$_SERVER['REQUEST_URI'])."&edit=" . $value['ssf_wp_id'] ."#a$value[ssf_wp_id]'";
				
				print "<tr style='background-color:$bgcol' id='ssf_wp_tr-$value[ssf_wp_id]'>
			<th><input type='checkbox' name='ssf_wp_id[]' value='$value[ssf_wp_id]'></th>
			
			<td> $value[ssf_wp_id] </td>";

				if (function_exists("do_ssf_wp_hook") && !empty($ssf_wp_columns)){
					do_ssf_wp_location_table_body($value);
				} else {
					if ($is_normal_view) {
						
						$tco_address = $tco_address2 = $tco_city = $tco_state = $tco_zip = "</td>\n<td>";
						$strong_addr_open = $strong_addr_close = "";
					} else {
						$tco_address = (!empty($value['ssf_wp_address']) && !empty($value['ssf_wp_store']))? "<br>" : "" ;
						$tco_address2 = (!empty($value['ssf_wp_address2']))? ", " : "" ; 
						$tco_address2 = (empty($value['ssf_wp_address']) && !empty($value['ssf_wp_address2']))? "<br>" : $tco_address2 ;
						$tco_city = (!empty($value['ssf_wp_city']) || !empty($value['ssf_wp_state']) || !empty($value['ssf_wp_zip']))? "<br>" : "" ;
						$tco_state = (!empty($value['ssf_wp_city']))? ", " : "" ;
						$tco_zip = (!empty($value['ssf_wp_zip']))? " " : "" ;
						$strong_addr_open = "<strong>"; $strong_addr_close = "</strong>";
					}
					
					print "<td> $value[ssf_wp_store]{$tco_address}
$value[ssf_wp_address]{$tco_address2}
$value[ssf_wp_address2]{$tco_city}
$value[ssf_wp_city]{$tco_state}
$value[ssf_wp_state]{$tco_zip}
$value[ssf_wp_zip]</td>
<td>$value[ssf_wp_tags]</td>";

					if ($ssf_wp_vars['location_table_view']!="Normal") {
						print "<td>$value[ssf_wp_description]</td>
<td>$value[ssf_wp_url]</td>
<td>$value[ssf_wp_hours]</td>
<td>$value[ssf_wp_phone]</td>
<td>$value[ssf_wp_fax]</td>
<td>$value[ssf_wp_email]</td>
<td>$value[ssf_wp_image]</td>";
					}
				}
				print "<td title='(".$value['ssf_wp_latitude'].", ".$value['ssf_wp_longitude'].")' style='cursor:help;'>(".round($value['ssf_wp_latitude'],2).", ".round($value['ssf_wp_longitude'],2).")</td>
				<td><a class='edit_loc_link' href='".$edit_link." id='$value[ssf_wp_id]'><span class='fa fa-pencil'>&nbsp;</span>".__("Edit", SSF_WP_TEXT_DOMAIN)."</a>&nbsp;| <a class='del_loc_link' href='".wp_nonce_url("$_SERVER[REQUEST_URI]&delete=$value[ssf_wp_id]", "delete-location_".$value['ssf_wp_id'])."' onclick=\"confirmClick('Are you sure you wish to delete this store?', this.href); return false;\" id='$value[ssf_wp_id]'><span class='fa fa-trash'>&nbsp;</span>".__("Delete", SSF_WP_TEXT_DOMAIN)."</a></td>
				</tr>";
			}
		}
	} else {
		$cleared=(!empty($_GET['q']))? str_replace("q=".str_replace(" ", "+", $_GET['q']) , "", $_SERVER['REQUEST_URI']) : $_SERVER['REQUEST_URI'] ;
		$notice=(!empty($_GET['q']))? __("No Locations Showing for this Search of ", SSF_WP_TEXT_DOMAIN)."<b>\"$_GET[q]\"</b> | <a href='$cleared'>".__("Clear&nbsp;Results", SSF_WP_TEXT_DOMAIN)."</a> $view_link" : __("You have no available stores", SSF_WP_TEXT_DOMAIN);
		print "<tr><td colspan='5'>$notice | <a href='".SSF_WP_ADD_LOCATIONS_PAGE."'>".__("Add a Store", SSF_WP_TEXT_DOMAIN)."</a></td></tr>";
	}
	print "</table>
	<input name='act' type='hidden'><br>";
	wp_nonce_field("manage-locations_bulk");

if ($numMembers2!=0) {include(SSF_WP_INCLUDES_PATH."/search-links.php");}

print "</form>"; 
?>
</div>
<?php include(SSF_WP_INCLUDES_PATH."/ssf-wp-footer.php"); ?>
<script>
	function deleteMarker(a){
		var url='<?php echo SSF_WP_BASE;?>/ssf-wp-admin/pages/';
     jQuery.ajax({
		  type: 'POST',
		  data: {img:a} ,
		  url: '<?php echo SSF_WP_BASE;?>/ssf-wp-admin/pages/imageDelete.php',
		  dataType:'json',
		  success: function(data, textStatus, XMLHttpRequest){
             jQuery('#editImage'+a).append("<span style='color:green; margin-left:50px; float:right;'>Deleted Successfully</span>");
		     jQuery('#editImage'+a).fadeOut(400);
			 jQuery('#ssf_wp_image').prop('disabled', false);
		  },
		  error: function(MLHttpRequest, textStatus, errorThrown){
		  }
		  });
	
	}
	
	function delMarker(a){
		var url='<?php echo SSF_WP_BASE;?>/ssf-wp-admin/pages/';
     jQuery.ajax({
		  type: 'POST',
		  data: {img_mrk:a} ,
		  url: '<?php echo SSF_WP_BASE;?>/ssf-wp-admin/pages/imageDelete.php',
		  dataType:'json',
		  success: function(data, textStatus, XMLHttpRequest){
             jQuery('#editCmarker'+a).append("<span style='color:green; margin-left:50px; float:right;'>Deleted Successfully</span>");
		     jQuery('#editCmarker'+a).fadeOut(400);
			 jQuery('#ssf_wp_marker').prop('disabled', false);
		  },
		  error: function(MLHttpRequest, textStatus, errorThrown){
		  }
		  });
	
	}
	
	function delLogo(a){
		var url='<?php echo SSF_WP_BASE;?>/ssf-wp-admin/pages/';
     jQuery.ajax({
		  type: 'POST',
		  data: {img_logo:a} ,
		  url: '<?php echo SSF_WP_BASE;?>/ssf-wp-admin/pages/imageDelete.php',
		  dataType:'json',
		  success: function(data, textStatus, XMLHttpRequest){
             jQuery('#editlogo'+a).append("<span style='color:green; margin-left:50px; float:right;'>Deleted Successfully</span>");
		     jQuery('#editlogo'+a).fadeOut(400);
			 jQuery('#ssf_wp_logo').prop('disabled', false);
		  },
		  error: function(MLHttpRequest, textStatus, errorThrown){
		  }
		  });
	
	}
	
</script>
