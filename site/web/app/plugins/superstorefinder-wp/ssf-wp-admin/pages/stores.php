<?php


if (empty($_POST)) {ssf_wp_move_upload_directories();}
if (empty($_GET['pg'])) {
	include(SSF_WP_PAGES_PATH."/manage-stores.php");
} else {
	$the_page = SSF_WP_PAGES_PATH."/".$_GET['pg'].".php";
	if (file_exists($the_page)) {
		include($the_page);
	}
}


?>