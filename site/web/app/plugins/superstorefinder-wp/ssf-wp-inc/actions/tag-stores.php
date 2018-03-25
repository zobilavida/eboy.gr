<?php

if (!empty($_POST)) {extract($_POST);}


if (is_array($ssf_wp_id)==1) {
	$rplc_arr=array_fill(0, count($ssf_wp_id), "%d");
	$id_string=implode(",", array_map(array($wpdb, "prepare"), $rplc_arr, $ssf_wp_id)); 	
} else {
	$id_string=$wpdb->prepare("%d", $ssf_wp_id);
}
if ($act=="add_tag") {
		$SSfcateGory=ssf_wp_prepare_tag_string($ssf_wp_tags);
		$wpdb->query($wpdb->prepare("UPDATE ".SSF_WP_TABLE." SET ssf_wp_tags=CONCAT(IFNULL(ssf_wp_tags, ''), %s ) WHERE ssf_wp_id IN ($id_string)", ssf_comma(stripslashes($SSfcateGory)))); 
		ssf_wp_process_tags(ssf_wp_prepare_tag_string($ssf_wp_tags), "insertTags", $ssf_wp_id); 
}
elseif ($act=="remove_tag") {

	if (empty($ssf_wp_tags)) {

		$wpdb->query("UPDATE ".SSF_WP_TABLE." SET ssf_wp_tags='' WHERE ssf_wp_id IN ($id_string)");
		ssf_wp_process_tags("", "delete", $id_string);
	}
	else {		
		
		$wpdb->query($wpdb->prepare("UPDATE ".SSF_WP_TABLE." SET ssf_wp_tags=REPLACE(ssf_wp_tags, %s, '') WHERE ssf_wp_id IN ($id_string)", $ssf_wp_tags.",")); 
		$wpdb->query($wpdb->prepare("UPDATE ".SSF_WP_TABLE." SET ssf_wp_tags=REPLACE(ssf_wp_tags, %s, '') WHERE ssf_wp_id IN ($id_string)", $ssf_wp_tags."&#44;")); 
		ssf_wp_process_tags($ssf_wp_tags, "delete", $id_string); 
	}
}
?>