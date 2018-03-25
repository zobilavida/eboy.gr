<?php 
    include("../../ssf-wp-inc/includes/ssf-wp-env.php");
	global $wpdb;
	if(isset($_POST['status'])){
	$wpdb->query($wpdb->prepare("UPDATE ".SSF_WP_ADDON_TABLE." SET ssf_wp_addon_status='".$_POST['status']."' WHERE ssf_wp_adon_id='%d'", $_POST['id']));
	}
	else if(isset($_POST['addon']) && $_POST['addon']=='insert'){
		$ssf_distance=$_POST['ssf_distance'];
		$ssf_matrix=$_POST['ssf_matrix'];
		$array = array();
		$array['distance'] = array();
		$array['matrix'] = array();	
		$addonCheck=$wpdb->get_results("SELECT ssf_wp_addon_token FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_wp_adon_id='".$_POST['id']."'", ARRAY_A);
		$fetcharr=json_decode($addonCheck[0]['ssf_wp_addon_token']);
		if(!empty($fetcharr->distance)){
		foreach ($fetcharr->distance as $row){
			foreach ($row as $key=>$value){
				$array['distance'][] = array('distance' => "$value");		 
			}
		}
		if(!empty($ssf_distance)){
		$array['distance'][] = array('distance' => "$ssf_distance");
		}
		$array['matrix'] = array('matrix' => "$ssf_matrix");
		}else{
		$array['distance'][] = array('distance' => "$ssf_distance");
		$array['matrix'] = array('matrix' => "$ssf_matrix");
		}

		$json = json_encode($array);
		$wpdb->query($wpdb->prepare("UPDATE ".SSF_WP_ADDON_TABLE." SET ssf_wp_addon_token='".$json."' WHERE ssf_wp_adon_id='%d'", $_POST['id']));
		
	}
	else if(isset($_POST['addon']) && $_POST['addon']=='select'){
		$addonCheck=$wpdb->get_results("SELECT ssf_wp_addon_token FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_addon_name='Distance-Addon'", ARRAY_A);
		if(!empty($addonCheck[0]['ssf_wp_addon_token'])){
		$fetcharr=json_decode($addonCheck[0]['ssf_wp_addon_token']);
			if(!empty($fetcharr->matrix)){
			foreach ($fetcharr->matrix as $row){
					$matrix=$row;
				}
			}
			if(!empty($fetcharr->distance)){
			foreach ($fetcharr->distance as $row){
				foreach ($row as $key=>$value){
				echo "<span class='distanceResults'><span>".$value."</span><span> ".$matrix." </span><span><a id='".$value."'><i class='fa fa-times' aria-hidden='true'></i></a></span></span>";
				}		
			  }
			}
			else{
					echo "<span>You have no available Distance !</span>";
			  }
			}
			else{
					echo "<span>You have no available Distance !</span>";
			  }
		
	}
	else if(isset($_POST['addon']) && $_POST['addon']=='remove'){
		$addonCheck=$wpdb->get_results("SELECT ssf_wp_addon_token FROM ".SSF_WP_ADDON_TABLE." WHERE ssf_addon_name='Distance-Addon'", ARRAY_A);
		$array = array();
		$array['distance'] = array();
		$array['matrix'] = array();	
		$UpdateRecords='';
		$matrix='';
		if(!empty($addonCheck[0]['ssf_wp_addon_token'])){
		$fetcharr=json_decode($addonCheck[0]['ssf_wp_addon_token']);
		if(!empty($fetcharr->matrix)){
			foreach ($fetcharr->matrix as $row){
					$matrix=$row;
				}
			}
			foreach ($fetcharr->distance as $row){
				foreach ($row as $key=>$value){
				if($value!=$_POST['remove']){
				$array['distance'][] = array('distance' => "$value");
				$UpdateRecords.="<span class='distanceResults'><span>".$value."</span><span> ".$matrix." </span><span><a id='".$value."'><i class='fa fa-times' aria-hidden='true'></i></a></span></span>";
				}
				}		
			}
			
		$array['matrix'] = array('matrix' => "$matrix");	
		$json = json_encode($array);
		$wpdb->query($wpdb->prepare("UPDATE ".SSF_WP_ADDON_TABLE." SET ssf_wp_addon_token='".$json."' WHERE ssf_addon_name='%s'", 'Distance-Addon'));
			}
			if($UpdateRecords){
			   print $UpdateRecords;
			}
			else{
			   print "<span>You have no available Distance !</span>";
			  }
		
	}
?>