<?php
 include("../../ssf-wp-inc/includes/ssf-wp-env.php");
  global $wpdb;
  function parseToEXPRT($htmlStr) 
{ 
$xmlStr=str_replace('&lt;','<',$htmlStr); 
$xmlStr=str_replace('&gt;','>',$xmlStr); 
$xmlStr=str_replace('&quot;','"',$xmlStr); 
$xmlStr=str_replace("&#44;","," ,$xmlStr);
$xmlStr=str_replace('&#39;',"'",$xmlStr); 
$xmlStr=str_replace('&amp;',"&",$xmlStr); 
return $xmlStr; 

} 
 
 $locales=$wpdb->get_results("SELECT * FROM ".SSF_WP_TABLE." ", ARRAY_A)  ;
//$data=array();
$content='Name,CategoriesTags,Address,City,State,Country,Zip,Phone,Fax,email,website,External URL,description,Operating Hours';
$content .="\n";
	foreach ($locales as $value) {
$content .='"'.parseToEXPRT($value["ssf_wp_store"]).'","'.parseToEXPRT($value["ssf_wp_tags"]).'","'.parseToEXPRT($value["ssf_wp_address"]).'","'.parseToEXPRT($value["ssf_wp_city"]).'","'.$value["ssf_wp_state"].'","'.$value["ssf_wp_country"].'","'.$value["ssf_wp_zip"].'","'.$value["ssf_wp_phone"].'","'.$value["ssf_wp_fax"].'","'.$value["ssf_wp_email"].'","'.$value["ssf_wp_url"].'","'.$value["ssf_wp_ext_url"].'","'.$value["ssf_wp_description"].'","'.parseToEXPRT($value["ssf_wp_hours"]).'"';
$content .="\n";
}

//print $content;
$file=SSF_WP_UPLOADS_PATH."/csv/Store.csv";
    @chmod($file,0666);
    $fp = fopen($file,'w');
    fwrite($fp,$content);
    fclose($fp);
    if (file_exists($file)) {
		ob_start();
        header('Content-Description: File Transfer');
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename='.basename($file));
        
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
		echo "\xEF\xBB\xBF"; // UTF-8 BOM
        readfile($file);
        exit;
    }

?>
