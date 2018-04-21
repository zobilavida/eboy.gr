<?php
/* Custom functions code goes here. */
add_filter("gform_pre_render_1", "populate_dropdown");
add_filter("gform_admin_pre_render_1", "populate_dropdown");

function populate_dropdown($form){
global $wpdb; //Accessing WP Database (non-WP Table) use code below.
$results = $wpdb->get_results("SELECT ssf_wp_store from wp_ssf_wp_stores");

$choices = array();
$choices[] = array("text" => "Select a Dealer", "value" => "");

foreach ($results as $result) {
$choices[] = array("text" => $result->ssf_wp_store, "value" => $result->ssf_wp_store);
}

foreach($form["fields"] as &$field){
if($field["id"] == 1){
//  $field["cssClass"] = 'dealer-county';
$field["choices"] = $choices;
}
}

return $form;
}
