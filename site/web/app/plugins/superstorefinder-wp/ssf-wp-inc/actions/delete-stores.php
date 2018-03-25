<?php
if ($_POST) {extract($_POST);}
if (is_array($ssf_wp_id)==1) {
	$rplc_arr=array_fill(0, count($ssf_wp_id), "%d");
	$id_string=implode(",", array_map(array($wpdb, "prepare"), $rplc_arr, $ssf_wp_id)); 
} else { 
	$id_string=$wpdb->prepare("%d", $ssf_wp_id); 
}
$wpdb->query("DELETE FROM ".SSF_WP_TABLE." WHERE ssf_wp_id IN ($id_string)");
$imageId = explode(',', $id_string); //split string into array seperated by ', '

$imageId = explode(',', $id_string); //split string into array seperated by ', '
			

			foreach($imageId as $value) //loop over values

       {    
	   	    $dir=SSF_WP_UPLOADS_PATH."/images/".$value;
			if (is_dir($dir)){
				$images = @scandir($dir);
				foreach($images as $k=>$v):
				endforeach;
				unlink($dir.'/'.$v);
				rmdir($dir);
			}
			
			$MarkerDir=SSF_WP_UPLOADS_PATH."/images/icons/".$value;
			if (is_dir($MarkerDir)){
				$images = @scandir($MarkerDir);
				foreach($images as $k=>$v):
				endforeach;
				unlink($MarkerDir.'/'.$v);
				rmdir($MarkerDir);
			}
			
		}

ssf_wp_process_tags("", "delete", $id_string);
?>