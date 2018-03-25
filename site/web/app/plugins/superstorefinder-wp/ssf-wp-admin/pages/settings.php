<?php
include_once(SSF_WP_INCLUDES_PATH."/top-nav.php");
?>
<div class='wrap'>
<?php 
$resize_image_width = 38;
if (empty($_POST)) {ssf_wp_move_upload_directories();}
if (!empty($_POST['ssf_wp_map_settings'])) { 
    
    function ssf_wp_md_save($data) {
	global $ssf_wp_vars;
	  
	   //print_r($data); 
	  
	    if(!empty($_FILES)){
	
         // isset Determines if a variable is set and is not NULL. Set Size Limit less then 10 MB=10485760 bytes. Extension must be png jpg.
		    if(!empty($_FILES['custom_marker']['name'])){
				
			$valid_exts = array("jpg","jpeg","gif","png");
			$ext = end(explode(".",strtolower(trim($_FILES["custom_marker"]["name"]))));
			if(in_array($ext,$valid_exts)){
				$max_dimension = 800; // Max new width or height, can not exceed this value.
				 $dir=SSF_WP_UPLOADS_PATH."/images/icons/";
				$postvars = array(
				"image"    => 'custom-marker.png',
				"image_tmp"    => $_FILES["custom_marker"]["tmp_name"],
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
					
					$width2 = $size[0];
					$height2 = $size[0]/$ratio;
					}
					else {
					$width = 58*$ratio;
					$height = 58;
					
					$width2 = $size[0]*$ratio;
					$height2 = $size[0];
					}
					
					$tmp = imagecreatetruecolor($width,$height);
					if($ext == "gif" or $ext == "png"){
						imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
						imagealphablending($tmp, false);
						imagesavealpha($tmp, true);
						imagecopyresampled($tmp,$image,0,0,0,0,$width,$height,$size[0],$size[1]);
						$filename = $dir.$postvars["image"];
						imagepng($tmp,$filename,9);
					}
					
					else
					{
						$whiteBackground = imagecolorallocate($tmp, 255, 255, 255); 
						imagefill($tmp,0,0,$whiteBackground); // fill the background with white
						imagecopyresampled($tmp,$image,0,0,0,0,$width,$height,$size[0],$size[1]);
						$filename = $dir.$postvars["image"];
						imagejpeg($tmp,$filename,100);
					}
					imagedestroy($image);
					imagedestroy($tmp);
				}
			}
			  
				
				
          if(!empty($_FILES['custom_marker_active']['name'])){
			$valid_exts = array("jpg","jpeg","gif","png");
			$ext = end(explode(".",strtolower(trim($_FILES["custom_marker_active"]["name"]))));
			if(in_array($ext,$valid_exts)){
				$max_dimension = 800; // Max new width or height, can not exceed this value.
				 $dir=SSF_WP_UPLOADS_PATH."/images/icons/";
				$postvars = array(
				"image"    => 'custom-marker-active.png',
				"image_tmp"    => $_FILES["custom_marker_active"]["tmp_name"],
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
						$filename = $dir.$postvars["image"];
						imagepng($tmp,$filename,9);
					}
					
					else
					{
						$whiteBackground = imagecolorallocate($tmp, 255, 255, 255); 
						imagefill($tmp,0,0,$whiteBackground); // fill the background with white
						imagecopyresampled($tmp,$image,0,0,0,0,$width,$height,$size[0],$size[1]);
						$filename = $dir.$postvars["image"];
						imagejpeg($tmp,$filename,100);
					}
					imagedestroy($image);
					imagedestroy($tmp);
				}
			}
	    }
	
	
	$ssf_wp_vars['sensor']=(empty($_POST['ssf_wp_geolocate']))? "false" : "true";
	if(!isset($ssf_wp_vars['map_settings']))
	{
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
    
	foreach ($data as $value) {
	    
	    if (!empty($value['field_name'])) {
		$fname = $value['field_name'];
		if($value['field_name']=='ssf_user_role'){
		   if(!empty($_POST['ssf_user_role'])){
		     array_push($_POST['ssf_user_role'],"administrator");
			  $_POST['ssf_user_role']=implode(',',$_POST['ssf_user_role']);
		   }else{
		     $_POST['ssf_user_role']="administrator";
		   }
		}
		
		  if($fname=="ssf_wp_map_code" && $_POST['ssf_wp_map_code']!=' ' && !empty($_POST['ssf_wp_map_code'])){	
	                if(!preg_match_all('/\[(.*?)\]/',$_POST['ssf_wp_map_code'],$match)) {  			
	                     echo __("<div class='ssf-wp-menu-alert'> Please enter the correct map code format</div> ", SSF_WP_TEXT_DOMAIN);  			
	                         $_POST['ssf_wp_map_code']='';			
	                        }			
	                        			
	                }
		
		if($fname=="custom_marker"){
		
		$ssf_wp_vars['custom_marker']='custom_marker';
		
		}else if($fname=="custom_marker_active"){
		
		$ssf_wp_vars['custom_marker_active']='custom_marker_active';
		
		
		}else 
		
		if (!empty($value['field_type']) && $value['field_type'] == "checkbox") {
			
			if (is_array($fname)) {
			
				foreach ($fname as $the_field) {
					$ssf_wp_vars[$the_field] = (empty($_POST["ssf_wp_".$the_field]))? 0 : $_POST["ssf_wp_".$the_field] ;
				}
			} else {
				$ssf_wp_vars[$fname] = (empty($_POST["ssf_wp_".$fname]))? 0 : $_POST["ssf_wp_".$fname] ;
			}
			
			 
			
		} else {
			if (is_array($fname)) {
				$fctr = 0;
				foreach ($fname as $the_field) {
					$post_data = (isset($_POST["ssf_wp_".$the_field]))? $_POST["ssf_wp_".$the_field] : $_POST[$the_field] ;
					$post_data = (!empty($value['stripslashes'][$fctr]) && $value['stripslashes'][$fctr] == 1)? stripslashes($post_data) : $post_data;
					$post_data = (!empty($value['numbers_only'][$fctr]) && $value['numbers_only'][$fctr] == 1)? preg_replace("@[^0-9]@", "", $post_data) : $post_data;
					$ssf_wp_vars[$the_field] = $post_data;
					$fctr++;
				}
			} else {
				$post_data = (isset($_POST["ssf_wp_".$fname]))? $_POST["ssf_wp_".$fname] : $_POST[$fname] ;
				$post_data = (!empty($value['stripslashes']) && $value['stripslashes'] == 1)? stripslashes($post_data) : $post_data;
				$post_data = (!empty($value['numbers_only']) && $value['numbers_only'] == 1)? preg_replace("@[^0-9]@", "", $post_data) : $post_data;
				$ssf_wp_vars[$fname] = $post_data;
			}
		}
	    }
	    
	}      
	          
	ssf_wp_data('ssf_wp_vars', 'update', $ssf_wp_vars);
	
    }
    
    ssf_wp_initialize_variables();
    include(SSF_WP_INCLUDES_PATH."/settings-options.php");
    ssf_wp_md_save($ssf_wp_mdo);
    unset($ssf_wp_mdo);  
 
 ssf_wp_initialize_variables(); 
 
	print "<div class='ssf_wp_admin_success' >".__(" Settings successfully saved.", SSF_WP_TEXT_DOMAIN)." $view_link</div> <!--meta http-equiv='refresh' content='0'-->";
}




$update_button="<input type='submit' value='".__("Save Settings", SSF_WP_TEXT_DOMAIN)."' class='button-primary'>";

$reset_button='<input type="button" onclick="resetSSFform()" value="Restore Default Settings" class="button">&nbsp;&nbsp;';

print "
<div class='input_section'>
	<form method='post' name='settings' id='settings' enctype=\"multipart/form-data\">
	
					<div class='input_title'>
						
						<h3><span class='fa fa-cog'>&nbsp;</span> Settings</h3>
						<span class='submit'>
						$reset_button $update_button
						</span>
							
						<div class='clearfix'></div>
					</div>
					<div class='all_options'>
	
";

function ssf_wp_md_display($data, $input_zone, $template, $additional_classes = "") {
    
    $GLOBALS['input_zone_type'] = $input_zone;
    $filtered_data = array_filter($data, "filter_ssf_wp_mdo");
    unset($GLOBALS['input_zone_type']);
	
	$showregion=0;
	$showContact=0;
    $labels_ctr = 0;
	$showReview = 0;
    foreach ($filtered_data as $key => $value) {
    
    	if ($template == 1) {
		
		$the_row_id = (!empty($value["row_id"]))? " id = '$value[row_id]' " : "";
		$hide_row = (!empty($value['hide_row']) && $value['hide_row'] == true)? "style='display:none' " : "" ;
		$colspan = (!empty($value['colspan']) && $value['colspan'] > 1)? "colspan = '$value[colspan]'" : "" ;
		
		print "<div class='option_input option_text'>
					<label for='shortname_logo'>".$value['label'];
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
		
		if($value['label']=="Search bar caption"){
		print "<div class='input_title'>
						
						<h3><span class='fa fa-bookmark'>&nbsp;</span> Labels</h3>
						
						<div class='clearfix'></div>
					</div>";
		}
		
		
		if($value['label']=="Loading Google Maps..."){
		print "<div class='input_title'>
						
						<h3><span class='fa fa-exclamation-circle'>&nbsp;</span> Notification</h3>
						
						<div class='clearfix'></div>
					</div>";
		}
		
		
		if($value['label']=="We have encountered an error."){
		  $showregion=1;
		}
		
		
		
		
		print "<div class='option_input option_text'>";
		print "<label for='shortname_logo'>".$value['label']."</label>".$value['input_template']."<small></small><div class='clearfix'></div></div>";
	
if($showregion==1){
$showregion=0;
		print "<div class='input_title'>
						
						<h3><span class='fa fa-map-marker'>&nbsp;</span> Filter Panel </h3>
						
						<div class='clearfix'></div>
					</div>";
		}



if($value['label']=="Show Search Bar"){
		  $showContact=1;
		}
if($showContact==1){
$showContact=0;
		print "<div class='input_title'>
						
						<h3><span class='fa fa-bookmark'>&nbsp;</span> Contact Form </h3>
						
						<div class='clearfix'></div>
					</div>";
		}
		
		
if($value['label']=="Message delivery failed"){
		  $showReview=1;
		}
if($showReview==1){
$showReview=0;
		print "<div class='input_title'>
						
						<h3><span class='fa fa-star-o'>&nbsp;</span> Ratings & Review </h3>
						
						<div class='clearfix'></div>
					</div>";
		}		
		

	//}
    	}
    	
      //}
    	
    }
    
    print "</div>";
}

include(SSF_WP_INCLUDES_PATH."/settings-options.php");


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


print "<div class='input_title'>
						
						<h3><span class='fa fa-paint-brush'>&nbsp;</span> Styles &amp; Colors - (<a href='https://superstorefinder.net/superstorefinderwp/user-guide/#document-15' target='new'>Color Chart</a>)</h3> 
						
						<div class='clearfix'></div>
					</div>";
					
ssf_wp_md_display($ssf_wp_mdo, 'labels', 2, "right_side");


print "<div class='input_title'>
						<span class='submit'>
						$update_button
						</span>
						<div class='clearfix'></div>
					</div>";

print "</form>";

?>
</div>
<?php include(SSF_WP_INCLUDES_PATH."/ssf-wp-footer.php"); ?>

	<script>
	jQuery('.chosen-select').chosen();
	function deleteMarker(a){
		var url='<?php echo SSF_WP_BASE;?>/ssf-wp-admin/pages/';
     jQuery.ajax({
		  type: 'POST',
		  data: {img:a} ,
		  url: '<?php echo SSF_WP_BASE;?>/ssf-wp-admin/pages/imageDelete.php',
		  dataType:'json',
		  success: function(data, textStatus, XMLHttpRequest){
             jQuery('#marker-'+a).append("<span style='color:green; margin-left:50px; float:right;'>Deleted Successfully</span>");
		     jQuery('#marker-'+a).fadeOut(400);
			 
			 if(a=='a'){
			 jQuery('#custom_marker').prop('disabled', false);
			 } else {
			 jQuery('#custom_marker_active').prop('disabled', false);
			 }
		  },
		  error: function(MLHttpRequest, textStatus, errorThrown){
		  }
		  });
	
	}
	function resetSSFform(){
			if(confirm('Are you sure you want to reset the setting ?')){
				jQuery.ajax({
				  type: 'POST',
				  data: {restore:'restore'} ,
				  url: '<?php echo SSF_WP_BASE;?>/ssf-wp-admin/pages/settingRestore.php',
				  dataType:'json',
				  success: function(data, textStatus, XMLHttpRequest){
					location.reload();
				  },
				  error: function(MLHttpRequest, textStatus, errorThrown){
				   location.reload();
				  }
				});
				
			}else{
			  return false;
			}
	}
</script>
