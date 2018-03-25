<?php
if (!defined("DB_USER")){
	function ssf_wp_load_config($path, $lvl, $file="wp-load.php"){
		if ($lvl>30){die('reached 30 levels of search'); } else {$lvl++;}
		return file_exists($path."/".$file)? $path."/".$file : call_user_func(__FUNCTION__, "../".$path, $lvl);
	}
	include(ssf_wp_load_config(".", 0));
	$username=DB_USER;
	$password=DB_PASSWORD;
	$database=DB_NAME;
	$host=DB_HOST;
}

?>