<?php
include_once(SSF_WP_INCLUDES_PATH."/top-nav.php");

if (!function_exists("ssf_wp_initialize_variables")) { include("../ssf-wp-functions.php"); }
?>
<div class='wrap'>
<?php 
$msg="";
ssf_wp_initialize_variables(); 

global $wpdb;
$recaptchaKey='';
$rattingAddon=$wpdb->get_results("SELECT * FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_wp_addon_name='ssf-rating-addon-wp' AND ssf_wp_addon_status='on' ", ARRAY_A);
if(!empty($rattingAddon)){
wp_enqueue_style( 'mega-comment-css' , SSF_WP_ADDONS_BASE.'/ssf-rating-addon-wp/social-admin.css' , true , '1.0' );
wp_enqueue_script( 'mega-comment-js', SSF_WP_ADDONS_BASE."/ssf-rating-addon-wp/social-admin.js", "jQuery");
}


if(!empty($_GET['delete']))
{
	if (!empty($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], "delete-location_".$_GET['delete'])){
		$locales=$wpdb->get_results("SELECT * FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_wp_adon_id='".$_GET['delete']."' ", ARRAY_A);
	if(!empty($locales))
	{ 
	foreach ($locales as $value) {
		if($value['ssf_wp_addon_name']=='ssf-custom-marker-wp'){
			$fileName=SSF_WP_PATH.'/categories.php';
			unlink($fileName);
			$wpdb->query($wpdb->prepare("DELETE FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_wp_adon_id='%d'", $_GET['delete'])); 
			print "<script>location.reload();</script>";
		}
		else{
		$dir=SSF_WP_ADDONS_PATH.'/'.$value['ssf_wp_addon_name'];
			if (is_dir($dir)){ 
				$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
				$files = new RecursiveIteratorIterator($it,
				RecursiveIteratorIterator::CHILD_FIRST);
				foreach($files as $file) {
				if ($file->isDir()){
				rmdir($file->getRealPath());
				} else {
				unlink($file->getRealPath());
				}
			   }
			   rmdir($dir);
				$wpdb->query($wpdb->prepare("DELETE FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_wp_adon_id='%d'", $_GET['delete'])); 
			}
		}
	  }
	
	}
		
	} 
}
	
if(isset($_FILES['fupload']) && $_FILES['fupload']['error'] != 4) {
    $filename = $_FILES['fupload']['name'];
    $source = $_FILES['fupload']['tmp_name'];
    $type = $_FILES['fupload']['type']; 
     
    $name = explode('.', $filename); 
    $target = SSF_WP_ADDONS_PATH.'/'.$name[0].'/';  
	$dir = SSF_WP_ADDONS_PATH.'/'.$name[0]; 
     
    // Ensures that the correct file was chosen
    $accepted_types = array('application/zip', 
                                'application/x-zip-compressed', 
                                'multipart/x-zip', 
                                'application/s-compressed');
 
    foreach($accepted_types as $mime_type) {
        if($mime_type == $type) {
            $okay = true;
            break;
        } 
    }
       
  //Safari and Chrome don't register zip mime types. Something better could be used here.
    $okay = strtolower($name[1]) == 'zip' ? true: false;
	$okayMarker = strtolower($name[0]) == 'ssf-marker-cluster-wp' ? true: false;
	$okayCatgory = strtolower($name[0]) == 'ssf-multi-category-wp' ? true: false;
	$okayDistance = strtolower($name[0]) == 'ssf-distance-addon-wp' ? true: false;
	$okayRating = strtolower($name[0]) == 'ssf-rating-addon-wp' ? true: false;
	$okayTheme = strtolower($name[0]) == 'ssf-super-theme-wp' ? true: false;
	$okayCustom = strtolower($name[0]) == 'ssf-custom-marker-wp' ? true: false;
	
    if(!$okayMarker && !$okayCatgory && !$okayDistance && !$okayRating && !$okayTheme && !$okayCustom)
	{
		$addonCheck=false;
	}else { $addonCheck=true;   }
    if(!$okay || !$addonCheck) {
		  print "<div class='ssf_wp_admin_warning' >".__("Please upload a valid add-on zip package for Super Store Finder", SSF_WP_TEXT_DOMAIN)."</div> <!--meta http-equiv='refresh' content='0'-->";  
    }
	else{
   $locales=$wpdb->get_results("SELECT * FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_wp_addon_name='".$name[0]."' ", ARRAY_A);
	if(empty($locales))
	{ 
    if($okayCustom){
		$target = SSF_WP_PATH.'/'; 
	}else{
	   mkdir($target, 0777, true);
	   @chmod($target, 0777);
	}
    $saved_file_location = $target . $filename;
     
    if(move_uploaded_file($source, $saved_file_location)) {
        openZip($saved_file_location);
    } else {
        die("There was a problem. Sorry!");
    }
 		if($okayMarker) { $AddonName='Cluster Marker'; } else if($okayCatgory){ $AddonName='Multi-Category';  }
		else if($okayDistance){ $AddonName='Distance-Addon'; } else if($okayRating){ $AddonName='Rating-Addon'; }
		else if($okayTheme){ $AddonName='Super-theme'; }else if($okayCustom){ $AddonName='Custom-Marker'; }
			$q = $wpdb->prepare("INSERT INTO ".SSF_WP_ADDON_TABLE." (ssf_wp_addon_name, ssf_addon_name, ssf_wp_addon_token) VALUES (%s, %s, %s)", $name[0] , $AddonName, ''); 
	 		$wpdb->query($q);
			print "<div class='ssf_wp_admin_success' >".__("Add-on successfully installed.", SSF_WP_TEXT_DOMAIN)."</div> <!--meta http-equiv='refresh' content='0'-->";
			if($okayCustom){
				print "<script>location.reload();</script>";
			}
		}
	  else{
		  print "<div class='ssf_wp_admin_success' >".__("This add-on has already being installed.", SSF_WP_TEXT_DOMAIN)."</div> <!--meta http-equiv='refresh' content='0'-->";
	  }
	
  }
}
print "<div class='ssf_wp_admin_success' id='add_ons_status' style='display:none;'>Status Changed successfully ! </div>";
print "<div class='ssf_wp_admin_success' id='addon_ons_status' style='display:none;'>Data successfully ! </div>";

function openZip($file_to_open) {
    global $target;
     
    $zip = new ZipArchive();
    $x = $zip->open($file_to_open);
    if($x === true) {
        $zip->extractTo($target);
        $zip->close();
         
        unlink($file_to_open);
    } else {
        die("There was a problem. Please try again.");
    }
}

print "
<div class='input_section'>
	<form method='post' name='settings' enctype='multipart/form-data'>
	
					<div class='input_title'>
						
						<h3><span class='fa fa-plug'>&nbsp;</span> Add-ons (More info on add-ons available <a href='http://superstorefinder.net/superstorefinderwp/user-guide/#document-26' target='new'>here</a>)</h3>
						<span class='submit'>
						</span>
						<div class='clearfix'></div>
					</div>
					<div class='all_options'>

	
";

function ssf_wp_md_display($data, $input_zone, $template, $additional_classes = "") {
    global $wpdb;
    
    $GLOBALS['input_zone_type'] = $input_zone;
    $filtered_data = array_filter($data, "filter_ssf_wp_mdo");
    unset($GLOBALS['input_zone_type']);
	$showregion=0;
    $labels_ctr = 0;
    foreach ($filtered_data as $key => $value) {
      
    	if ($template == 1) {
		
		$the_row_id = (!empty($value["row_id"]))? " id = '$value[row_id]' " : "";
		$hide_row = (!empty($value['hide_row']) && $value['hide_row'] == true)? "style='display:none' " : "" ;
		$colspan = (!empty($value['colspan']) && $value['colspan'] > 1)? "colspan = '$value[colspan]'" : "" ;
		
		print "<div class='option_input option_text'><label for='shortname_logo'>".$value['label'];
		if (!empty($value['more_info_label'])) {
			print "&nbsp;(<a href='#$value[more_info_label]' rel='ssf_wp_pop'>?</a>)&nbsp;";
		}
		print "</label>";
	    if (empty($value['colspan']) || $value['colspan'] < 2) {
		   print "".$value['input_template']."<div class='clearfix'></div></div>";
	    }
    	} elseif ($template == 2) {
		
		if ($labels_ctr % 3 == 0) {
			$the_row_id = (!empty($value["row_id"]))? " id = '$value[row_id]' " : "";
		}	
		
		if($value['label']=="We have encountered an error."){
		  $showregion=1;
		}
			print "<div class='option_input option_text'>";
			print "<label for='shortname_logo'>".$value['label']."</label>".$value['input_template']."<small></small><div class='clearfix'></div></div>";
    	}
		
		print '<div class="option_input option_text"><label for="shortname_logo">Add-ons list</label><br><br><table class="widefat"><thead><th colspan="2">Addon Name</th><th>Status</th><th>Actions</th></thead>';
		$locales=$wpdb->get_results("SELECT * FROM ".SSF_WP_ADDON_TABLE." ", ARRAY_A);
		
		if(!empty($locales))
	{
		foreach ($locales as $value) {
			$media1 ='';
			$media2 ='';
		  if($value['ssf_wp_addon_status']=='on' || $value['ssf_wp_addon_status']=='') { $media1 = 'checked'; }
		  else { $media2 = 'checked'; }
		 // print '<tr><td>'.$value['ssf_addon_name'].'</td>';
		  if($value['ssf_addon_name']=='Custom-Marker'){
			  print '<input type="hidden" id="CustomMarkerAddon" value="'.$value['ssf_wp_adon_id'].'">';
		  }
		  if($value['ssf_addon_name']=='Distance-Addon'){
		   print '<tr><td>'.$value['ssf_addon_name'].'</td>';
		   print '<td id="ssfDistanceRow"><a id="ssfAddonOpen"><span class="fa fa-2x fa-code-fork" aria-hidden="true"></a>
				  <a id="ssfAddonAdd"></span><span class="fa fa-2x fa-plus" aria-hidden="true"></span></a>
				  <a id="ssfAddonSetting"><span class="fa fa-2x fa-cog" aria-hidden="true"></span></a>
				  <input type="hidden" id="addonUpdateAddon" value="'.$value['ssf_wp_adon_id'].'"></td>';
		  }else if($value['ssf_addon_name']=='Rating-Addon'){
		   print '<tr><td>'.$value['ssf_addon_name'].'</td>';
		   print '<td id="ssfRattingRow"><a id="ssfCoomentAddonOpen"><span class="fa fa-2x fa-list" aria-hidden="true"></a>
		   <a id="ssfCoomentSetting"><span class="fa fa-2x fa-cog" aria-hidden="true"></span></a></td>';	
		  }else{
		   print '<tr><td colspan="2">'.$value['ssf_addon_name'].'</td>';
		  }
		  print "<td><label class='ssflabel'><input id='".$value['ssf_wp_adon_id']."' class='js-inputify switch-status' name='ssf_wp_addon_status".$value['ssf_wp_adon_id']."' type='radio' value='on' $media1 /> ON </label> <label class='ssflabel'><input id='".$value['ssf_wp_adon_id']."' class='js-inputify switch-status' name='ssf_wp_addon_status".$value['ssf_wp_adon_id']."' type='radio' value='off' $media2 /> OFF </label></td>";	
		  print "<td><a class='del_loc_link' href='".wp_nonce_url("$_SERVER[REQUEST_URI]&delete=$value[ssf_wp_adon_id]", "delete-location_".$value['ssf_wp_adon_id'])."' onclick=\"confirmClick('Are you sure you wish to delete this Add-ons?', this.href); return false;\" id='$value[ssf_wp_adon_id]'><span class='fa fa-trash'>&nbsp;</span>".__("Delete", SSF_WP_TEXT_DOMAIN)."</a></td></tr>";
}
	} else{
		print '<tr><td colspan="3">There are no available add-ons.</td></tr>';
		
	}
print '</table><div class="clearfix"></div></div>';
    }
    print "</div>";
	
}

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


// general
$ssf_wp_mdo[] = array("field_name" => "default_location", "default" => "New York, US", "input_zone" => "defaults", "label" =>  __("File", SSF_WP_TEXT_DOMAIN), "input_template" => "<input type='file' name='fupload' value='$ssf_wp_vars[default_location]'> <input type='submit' value='Upload' class='button-primary' name='upload'>");


ssf_wp_md_display($ssf_wp_mdo, 'defaults', 1);

if (function_exists('icl_register_string')) {
    
	$GLOBALS['input_zone_type'] = "labels";
	$labels_arr = array_filter($ssf_wp_mdo, "filter_ssf_wp_mdo");
	unset($GLOBALS['input_zone_type']);
	
	
	foreach ($labels_arr as $value) {
		$the_field = $value['field_name'];
		$varname = "ssf_wp_".$the_field;
		
		icl_register_string(SSF_WP_DIR, $value['label'], $ssf_wp_vars[$the_field]);
	}
	
	
	icl_register_string(SSF_WP_DIR, 'Search Button Filename', "search_button.png");
	icl_register_string(SSF_WP_DIR, 'Search Button Filename (Down State)', "search_button_down.png");
	icl_register_string(SSF_WP_DIR, 'Search Button Filename (Over State)', "search_button_over.png");
}

print "<div class='input_title'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			     <div class=\"clearfix\"></div>
				</div>
				</div>";

print "</form>";

?>

<div class="main-popup-holder" id="mainIntMapPopupHolder">
<div class="ssfpopup" id="modernIntBrowserPopup">
<a href="javascript:hidePopup();" id="intmapmodel"  class="popup-closer"><i class="fa fa-times fa-2x" aria-hidden="true"></i>
</a> 
<div class="pad-horizontal-2x" class="popup-img" id="popupIntData">
<form method='post' name='distanceAddons' enctype='multipart/form-data'>
<div class="option_input option_text" id="ssfAddonOpenShow" style="display:none;">

<div class="clearfix"></div>
</div>

<div class="option_input option_text" id="ssfAddonAddShow" style="display:none;">
<label for="shortname_logo"> Distance :</label>
<input type="text" id="ssf_distance" name="ssf_distance">

<div class="clearfix"></div>
</div>

<div class="option_input option_text" id="ssfAddonSettingShow" style="display:none;">
<label for="shortname_logo"> Matrix :</label>
<select name="ssf_matrix" id="ssf_matrix">
<option value='Miles'>Miles</option>
<option value='KM'>KM</option>
</select>
<div class="clearfix"></div>
</div>

<div class="option_input option_text">
<input class="button-primary" type="button" id="actionDistanceAddon" name="saveDistance" value="Save">
</div>
<input type='hidden' id='addonShowList'/>

</form>
</div>
</div></div>


<!-- Ratting And comment Pop-up -->
<?php 
$rattingStatus='public';
$rattingURL='';
$rattingAddon=$wpdb->get_results("SELECT * FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_wp_addon_name='ssf-rating-addon-wp' AND ssf_wp_addon_status='on' ", ARRAY_A);
if(!empty($rattingAddon)){
$rattingStatusVal= $rattingAddon[0]['ssf_wp_addon_token'];
if(!empty($rattingStatusVal)){
	$ratVal=explode("#",$rattingStatusVal);
	$rattingStatus=$ratVal[0];
	$rattingURL=$ratVal[1];
	$recaptchaKey=$ratVal[2];
	}
}

?>
<div class='main-popup-holder' id='mainPopupHolder' >
                     <div class='ssfpopup' id='modernBrowserPopup' style='max-width: 500px;'>
                        <a href='javascript:hideCommentPopup();' class='popup-closer ssflinks' id='listCloseBtn'><i class="fa fa-times fa-2x" aria-hidden="true"></i></a> 
                        <h1 class='popup-title'>Reviews & Ratings </h1>
						<div id='comment_review_status'></div>
                        <div class='pad-horizontal-2x' class='popup-img' style='max-width:500px !important;'>
						<div id='commentListShow'> 
						<div id='searchRat'><input type='search' id='filterReview' name='search' placeholder='search'></div>
						<div id="commentRatList"></div>
						<div id="paginationList"></div>
						</div>
						<div class="option_input option_text" id="ssfCommentSettingShow" style="display:none;">
						<label for="shortname_logo"> Reviews & Ratings setting  :</label>
						<select name="ssf_review_set" id="ssf_review_set">
						<option value='public' <?php if($rattingStatus=='public') { echo "selected";  } ?>>Public rating</option>
						<option value='user' <?php if($rattingStatus=='user') { echo "selected";  } ?>>WP user rating</option>
						</select>
						<br/><br/>
						<label for="shortname_logo"> reCAPTCHA API keys :</label>
						<input type="text" id="recaptchaKey" name="recaptchaKey" value="<?php echo $recaptchaKey; ?>"> 
						(<a href='http://superstorefinder.net/support/knowledgebase/creating-recaptcha-key-for-reviews-ratings-add-on/' target='new'>Learn More</a>)
						
						<div id="LoginURLPanel" <?php if($rattingStatus=='public') { echo "style='display:none;'";  } ?>>
						<br>
						<label for="shortname_logo"> Login URL  :</label>
						<input type="text" value="<?php echo $rattingURL; ?>"  name="LoginURL" id="LoginURL">
						</div>
						
						<div class="option_input option_text">
							<input class="button-primary" type="button" id="actionCommentset" name="actionCommentset" value="Save">
						</div>
						</div>
						
						
						
						</div>                      
                     </div>
                  </div>
<!-- Ratting And comment code end -->

<script type="text/javascript">
	var removeDisList=function(){
	  var removeVal=this.id;
	  var addon='remove';
		 jQuery.ajax({
			  type: 'POST',
			  data: {addon:addon,remove:removeVal} ,
			  url: '<?php echo SSF_WP_BASE;?>/ssf-wp-admin/pages/Update-Add-ons.php',
			  success: function(data, textStatus, XMLHttpRequest){
				  jQuery('#ssfAddonOpenShow').html(data);
				  jQuery("#ssfAddonOpenShow a").click(removeDisList); 
				  jQuery('#addon_ons_status').html('Distance Remove successfully !');
				  jQuery('#addon_ons_status').show().fadeOut(5000);
			  },
			  error: function(MLHttpRequest, textStatus, errorThrown){
			  jQuery('#ssfAddonOpenShow').html(textStatus);
			  }
		  });
	 }
 
jQuery(".switch-status").click(function() {
        var id = this.id;
		var status = this.value;
		var url='<?php echo SSF_WP_BASE;?>/ssf-wp-admin/pages/';
		 jQuery.ajax({
			  type: 'POST',
			  data: {id:id,status:status} ,
			  url: '<?php echo SSF_WP_BASE;?>/ssf-wp-admin/pages/Update-Add-ons.php',
			  dataType:'json',
			  success: function(data, textStatus, XMLHttpRequest){
				  jQuery('#add_ons_status').show().fadeOut(5000);
				  if(jQuery('#CustomMarkerAddon').val()==id){
				      location.reload();
				  }
			  },
			  error: function(MLHttpRequest, textStatus, errorThrown){
			  }
		  });
        })
		
	jQuery("#ssfDistanceRow a").click(function() {
				var id=this.id;
				jQuery('#mainIntMapPopupHolder, #modernIntBrowserPopup').addClass('is-shown');
				jQuery('#intmapmodel').css('display','inline-block');	
				jQuery('#'+id+'Show').show(); 	
                jQuery('#addonShowList').val(id+'Show');	
                if(id=='ssfAddonOpen'){
				jQuery('#actionDistanceAddon').hide();
					var addon='select';
						 jQuery.ajax({
							  type: 'POST',
							  data: {addon:addon} ,
							  url: '<?php echo SSF_WP_BASE;?>/ssf-wp-admin/pages/Update-Add-ons.php',
							  success: function(data, textStatus, XMLHttpRequest){
								  jQuery('#ssfAddonOpenShow').html(data);
								  jQuery("#ssfAddonOpenShow a").click(removeDisList); 
							  },
							  error: function(MLHttpRequest, textStatus, errorThrown){
							  jQuery('#ssfAddonOpenShow').html(textStatus);
							  }
						  });
					}else{
					jQuery('#actionDistanceAddon').show();
					}
	} );
	
	
	
	
	
	var hidePopup = function(e){
						jQuery('#intmapmodel').css('display','none');							
						var currntDiv=jQuery('#addonShowList').val();
						jQuery('#'+currntDiv).hide(); 
						jQuery('#mainIntMapPopupHolder, #modernIntBrowserPopup').removeClass('is-shown'); 
						
						}
						
						
jQuery("#actionDistanceAddon").click(function() {
        var ssf_distance = jQuery('#ssf_distance').val();
		var ssf_matrix = jQuery('#ssf_matrix').val();
		var message;
		if(ssf_distance==''){
		   message='Matrix Changed successfully !';
		}else{
			message='Distance Added successfully !';
		}
		var id=jQuery('#addonUpdateAddon').val();				
		var url='<?php echo SSF_WP_BASE;?>/ssf-wp-admin/pages/';
		var addon='insert';
		 jQuery.ajax({
			  type: 'POST',
			  data: {addon:addon,id:id,ssf_distance:ssf_distance,ssf_matrix:ssf_matrix} ,
			  url: '<?php echo SSF_WP_BASE;?>/ssf-wp-admin/pages/Update-Add-ons.php',
			  dataType:'json',
			  success: function(data, textStatus, XMLHttpRequest){
				  jQuery('#addon_ons_status').html(message);
				  jQuery('#addon_ons_status').show().fadeOut(5000);
				  jQuery('#ssf_distance').val('');
			  },
			  error: function(MLHttpRequest, textStatus, errorThrown){
				  jQuery('#addon_ons_status').html(message);
				  jQuery('#addon_ons_status').show().fadeOut(5000);
				  jQuery('#ssf_distance').val('');
			  }
		  });
        })						
 

</script>
</div>
<?php include(SSF_WP_INCLUDES_PATH."/ssf-wp-footer.php"); ?>