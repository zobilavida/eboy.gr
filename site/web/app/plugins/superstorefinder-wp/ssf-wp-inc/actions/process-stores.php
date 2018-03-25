<?php
	if (!empty($_GET['delete'])) {

		if (!empty($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], "delete-location_".$_GET['delete'])){
			$wpdb->query($wpdb->prepare("DELETE FROM ".SSF_WP_TABLE." WHERE ssf_wp_id='%d'", $_GET['delete'])); 
			
			//**.**delete the store and markers images **.**// 
			$dir=SSF_WP_UPLOADS_PATH."/images/".$_GET['delete'];
			
			
			$dir=SSF_WP_UPLOADS_PATH."/images/".$_GET['delete'];
			if (is_dir($dir)){
				$images = @scandir($dir);
				foreach($images as $k=>$v):
				endforeach;
				unlink($dir.'/'.$v);
				rmdir($dir);
			}
			
			$MarkerDir=SSF_WP_UPLOADS_PATH."/images/icons/".$_GET['delete'];
			if (is_dir($MarkerDir)){
				$images = @scandir($MarkerDir);
				foreach($images as $k=>$v):
				endforeach;
				unlink($MarkerDir.'/'.$v);
				rmdir($MarkerDir);
			}
			
			ssf_wp_process_tags("", "delete", $_GET['delete']); 
		} 
	}
	if (!empty($_POST) && !empty($_GET['edit']) && $_POST['act']!="delete") {
		$field_value_str=""; 
		foreach ($_POST as $key=>$value) {
			if (preg_match("@\-$_GET[edit]@", $key)) {
				$key=str_replace("-$_GET[edit]", "", $key); 
				if ($key=="ssf_wp_tags") {

					$value=ssf_wp_prepare_tag_string($value);

				}
				
				if (is_array($value)){
					$value=serialize($value); 
					$field_value_str.=$key."='$value',";
				} else {
					$field_value_str.=$key."=".$wpdb->prepare("%s", trim(ssf_comma(stripslashes($value)))).", "; 
				}
				$_POST["$key"]=$value; 
			}
		}
		
		$field_value_str=substr($field_value_str, 0, strlen($field_value_str)-2);
		$edit=$_GET['edit']; extract($_POST);
		$the_address="$ssf_wp_address, $ssf_wp_city, $ssf_wp_state $ssf_wp_zip";
		
		if (empty($_POST['no_geocode']) || $_POST['no_geocode']!=1) { 
			$old_address=$wpdb->get_results("SELECT * FROM ".SSF_WP_TABLE." WHERE ssf_wp_id='".esc_sql($_GET['edit'])."'", ARRAY_A); 
		}
		
		
		 
		$wpdb->query($wpdb->prepare("UPDATE ".SSF_WP_TABLE." SET ".str_replace("%", "%%", $field_value_str)." WHERE ssf_wp_id='%d'", $_GET['edit'])); 
		
		
/*.*Image Edit Upload Code *.*/
		if(!empty($_FILES['ssf_wp_image']['name'])){
			$valid_exts = array("jpg","jpeg","gif","png");
			$ext = explode(".",strtolower(trim($_FILES["ssf_wp_image"]["name"])));
			$ext = end($ext);
			if(in_array($ext,$valid_exts)){
				$max_dimension = 800; // Max new width or height, can not exceed this value.
				 $dir=SSF_WP_UPLOADS_PATH."/images/".$_GET['edit'];
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

			

			/************custom marker edit with store image ********************************************/

			if(!empty($_FILES['ssf_wp_marker']['name'])){
				$dir=SSF_WP_UPLOADS_PATH."/images/icons/".$_GET['edit'];
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
	
	/* custom marker edit from store page */	
		
		
/*.*Image Edit Upload Code *.*/
		
		
		if(!empty($_POST['ssf_wp_tags'])){ssf_wp_process_tags($_POST['ssf_wp_tags'], "insert", $_GET['edit']);}
		
		/*if ((empty($_POST['ssf_wp_longitude']) || $_POST['ssf_wp_longitude']==$old_address[0]['ssf_wp_longitude']) && (empty($_POST['ssf_wp_latitude']) || $_POST['ssf_wp_latitude']==$old_address[0]['ssf_wp_latitude'])) {
			if ($the_address!=$old_address[0]['ssf_wp_address']." ".$old_address[0]['ssf_wp_address2'].", ".$old_address[0]['ssf_wp_city'].", ".$old_address[0]['ssf_wp_state']." ".$old_address[0]['ssf_wp_zip'] || ($old_address[0]['ssf_wp_latitude']==="" || $old_address[0]['ssf_wp_longitude']==="")) {
				ssf_wp_do_geocoding($the_address,$_GET['edit']);
			}
		}*/
		print "<script>location.replace('".str_replace("&edit=$_GET[edit]", "", $_SERVER['REQUEST_URI'])."');</script>";
	 }
	if (!empty($_POST['act']) && !empty($_POST['ssf_wp_id']) && $_POST['act']=="delete") {
		
		if (!empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], "manage-locations_bulk")){
			include(SSF_WP_ACTIONS_PATH."/delete-stores.php");
		} else {
			print "<div class='ssf-wp-menu-alert'>Security check doesn't validate for bulk deletion of locations.</div>";
		}
	}
	if (!empty($_POST['act']) && !empty($_POST['ssf_wp_id']) && preg_match("@tag@", $_POST['act'])) {
		
		include(SSF_WP_ACTIONS_PATH."/tag-stores.php");
	}
	if (!empty($_POST['act']) && ($_POST['act']=='add_multi' || $_POST['act']=='remove_multi')) {
		
		include(SSF_WP_ADDONS_PATH."/multiple-field-updater/multiLocationUpdate.php");
	}
	if (!empty($_POST['act']) && $_POST['act']=="locationsPerPage") {
		
		$ssf_wp_vars['admin_locations_per_page']=$_POST['ssf_wp_admin_locations_per_page'];
		ssf_wp_data('ssf_wp_vars', 'update', $ssf_wp_vars);
		extract($_POST);
	}
	if (!empty($_POST['act']) && $_POST['act']=="regeocode" && file_exists(SSF_WP_ADDONS_PATH."/csv-xml-importer-exporter/reGeo.php")) {
		include(SSF_WP_ADDONS_PATH."/csv-xml-importer-exporter/reGeo.php");
	}
	if (!empty($_GET['changeView']) && $_GET['changeView']==1) {
		if ($ssf_wp_vars['location_table_view']=="Normal") {
			$ssf_wp_vars['location_table_view']='Expanded';
			ssf_wp_data('ssf_wp_vars', 'update', $ssf_wp_vars);
			
		} else {
			$ssf_wp_vars['location_table_view']='Normal';
			ssf_wp_data('ssf_wp_vars', 'update', $ssf_wp_vars);
			
		}
		print "<script>location.replace('".str_replace("&changeView=1", "", $_SERVER['REQUEST_URI'])."');</script>";
	}
	if (!empty($_GET['changeUpdater']) && $_GET['changeUpdater']==1) {
		if (ssf_wp_data('ssf_wp_location_updater_type')=="Tagging") {
			ssf_wp_data('ssf_wp_location_updater_type', 'update', 'Multiple Fields');
			
		} else {
			ssf_wp_data('ssf_wp_location_updater_type', 'update', 'Tagging');
			
		}
		$_SERVER['REQUEST_URI']=str_replace("&changeUpdater=1", "", $_SERVER['REQUEST_URI']);
		print "<script>location.replace('$_SERVER[REQUEST_URI]');</script>";
	}
	
?>