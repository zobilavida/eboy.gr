<?php
if (empty($_GET['pg'])) {
	include(SSF_WP_PAGES_PATH."/quickstart-content.php");
} else {
	$the_page = SSF_WP_PAGES_PATH."/".$_GET['pg'].".php";
	if (file_exists($the_page)) {
		include($the_page);
	}
}
?>