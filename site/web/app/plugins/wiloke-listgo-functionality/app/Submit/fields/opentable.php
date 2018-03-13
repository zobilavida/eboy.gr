<!-- Info -->
<?php
$aDefault = array(
	'restaurant_name' => '',
	'restaurant_id' => ''
);
$aOpenTableData = array();
if ( !empty($postID) ){
	$aOpenTableData = \Wiloke::getPostMetaCaching($postID, 'listing_open_table_settings');
}

$aOpenTableData = wp_parse_args($aOpenTableData, $aDefault);

?>
<div class="add-listing-group">
	<h4 class="add-listing-title"><?php esc_html_e('Open Table settings', 'wiloke'); ?></h4>
	<p class="add-listing-description"><?php \Wiloke::wiloke_kses_simple_html('Providing an option for visitors to book a reservation with <a href="https://www.opentable.com/" target="_blank">OpenTable</a> from this sidebar listing.', 'wiloke'); ?></p>
	<!-- Social Media -->
	<div class="row">
		<div class="col-sm-12">
			<div class="form-item">
				<label for="restaurant-name" class="label"><?php esc_html_e('Restaurant Name', 'wiloke'); ?></label>
				<span class="input-text">
                    <input id="restaurant-name" type="text" name="listing_open_table_settings[restaurant_name]" value="<?php echo esc_attr($aOpenTableData['restaurant_name']); ?>">
                    <input id="restaurant-id" type="hidden" name="listing_open_table_settings[restaurant_id]" value="<?php echo esc_attr($aOpenTableData['restaurant_id']); ?>">
                </span>
			</div>
		</div>
	</div>
</div>