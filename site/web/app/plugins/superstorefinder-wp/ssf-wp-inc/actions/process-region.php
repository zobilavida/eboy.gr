<?php
	if (!empty($_GET['delete'])) {

		if (!empty($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], "delete-location_".$_GET['delete'])){
			$wpdb->query($wpdb->prepare("DELETE FROM ".SSF_WP_REGION_TABLE." WHERE ssf_wp_region_id='%d'", $_GET['delete'])); 
			//ssf_wp_process_tags("", "delete", $_GET['delete']); 
		} 
	}
	if (!empty($_POST) && !empty($_GET['edit']) && $_POST['act']!="delete") {
		$field_value_str=""; 
		foreach ($_POST as $key=>$value) {
			if (preg_match("@\-$_GET[edit]@", $key)) {
				$key=str_replace("-$_GET[edit]", "", $key); 
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
		 
		$wpdb->query($wpdb->prepare("UPDATE ".SSF_WP_REGION_TABLE." SET ".str_replace("%", "%%", $field_value_str)." WHERE ssf_wp_region_id='%d'", $_GET['edit']));  
		print "<script>location.replace('".str_replace("&edit=$_GET[edit]", "", $_SERVER['REQUEST_URI'])."');</script>"; 
	}
	
	if (!empty($_POST['act']) && !empty($_POST['ssf_wp_region_id']) && $_POST['act']=="delete") {
			include(SSF_WP_ACTIONS_PATH."/delete-region.php");
	}?>