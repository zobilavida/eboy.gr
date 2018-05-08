<?php
global $ld_recalc;

$ld_recalc = array(
	'posts_per_run' => 16,
	'post_types' => array( 'stores' ),

	'scan_key' => 'acf-recalc-scan',
	'scan_identifier' => '2018-05-08', // Change this if you want to scan again in the future.

	'fields' => array(
		'google_map' => 'location',

		// the address from the google_map field will be used first, if it is available. otherwise, these will get used:
	    'address'   => 'street_address', // address may also include city, state, zip, if those fields are not used.
	    'city'      => 'city',
	    'state'     => 'state',
	    'zip'       => 'zip',
			'country'       => 'country'
	),
);

function recalc_acf_locations_init() {
	if ( !isset($_REQUEST['acf-recalc-locations']) ) return;

	global $ld_recalc;

	$args = array(
		'post_type' => $ld_recalc['post_types'],
		'meta_query' => array(
			'relation' => 'OR',
			array(
				'key' => $ld_recalc['scan_key'],
				'compare' => 'NOT EXISTS',
				'value' => ' ',
			),
			array(
				'key' => $ld_recalc['scan_key'],
				'compare' => '!=',
				'value' => $ld_recalc['scan_identifier'],
			),
		),
		'posts_per_page' => $ld_recalc['posts_per_run'],
	);

	$locations = new WP_Query($args);

	if ( $locations->have_posts() ) {

		echo '<h2>Scanning '. min($locations->found_posts, $ld_recalc['posts_per_run']).' of '. $locations->found_posts.'...</h2>';

		echo '<div style="font-size: 14px; font-family: Arial, sans-serif;">';

		foreach( $locations->posts as $i => $p ) {
			echo '<div style="width: 50%; float: left; overflow: auto;">';

			$url = get_permalink( $p->ID );
			$edit = get_edit_post_link( $p->ID );
			echo '<p><strong>'.($i+1).') '.ucwords(str_replace(array('-','_'), ' ', $p->post_type)).' #'. $p->ID .'</strong> - <a href="'.esc_attr($url).'" target="_blank">'. esc_html($p->post_title) .'</a> (<a href="'.esc_attr($edit).'" target="_blank">Edit</a>)</p>';

			echo '<pre style="border-bottom: 1px solid #666; padding-bottom: 15px; margin: 15px 0;">';
			recalc_acf_location_single( $p->ID );
			echo '</pre>';

			echo '</div>';
		}

		echo '</div>';
	}else{
		wp_die('<p><strong>ACF Recalc Locations:</strong> All locations have latitude/longitude present! All done!</p>');
	}

	exit;
}
add_action( 'wp', 'recalc_acf_locations_init' );


function recalc_acf_location_single( $post_id ) {
	global $ld_recalc;

	$location = get_field( $ld_recalc['fields']['google_map'], $post_id );
	$lookup_address = null;

	$result = null;
	$full_address = null;

	if ( isset($location['address']) ) {
		$full_address =  $location['address'];
	}else{
		if ( empty($ld_recalc['fields']['city']) || empty($ld_recalc['fields']['state']) || empty($ld_recalc['fields']['zip']) ) {
			$full_address = get_field( $ld_recalc['fields']['address'], $post_id );
		}else{
			$address = get_field( $ld_recalc['fields']['address'], $post_id );
			$city    = get_field( $ld_recalc['fields']['city'],    $post_id );
			$state   = get_field( $ld_recalc['fields']['state'],   $post_id );
			$zip     = get_field( $ld_recalc['fields']['zip'],     $post_id );
			$country     = get_field( $ld_recalc['fields']['country'],     $post_id );

			$full_address = sprintf('%s, %s, %s %s, %s', $address, $city, $state, $zip, $country );

		}
	}

	if ( empty($full_address) ) {
		echo '<strong>ERROR:</strong> Location does not have a valid google map address, and no fallback address field is given. Aborting operation.';
		exit;
	}else{
		echo '&ldquo;' . $full_address . '&rdquo;<br>';
	}

	$result = recalc_acf_location_lookup( $post_id, $full_address );

	if ( $result ) {
		echo 'Location has been found and saved!';
		return true;
	}else{
		echo '<h2>ERROR! Google map could not locate this address. Aborting operation.</h2>';
		exit;
	}
}

function recalc_acf_location_lookup( $post_id, $full_address ) {
	global $ld_recalc;

	$address_one_line = preg_replace('/ *(\r\n|\r|\n)+ */', " ", $full_address);

	$coords = recalc_acf_get_latlng( $address_one_line );

	if ( $coords ) {
		$location = get_field( $ld_recalc['fields']['google_map'], $post_id );

		if ( !is_array($location) ) {
			$location = array(
				'address' => '',
				'lat' => '',
				'lng' => ''
			);
		}

		if ( empty($location['address']) ) {
			$location['address'] = $address_one_line;
		}else{
			if ( $address_one_line === $location['address'] && $location['lat'] === $coords['lat'] && $location['lng'] === $coords['lng'] ) {
				echo 'Location is up to date, no changes were made.';
				update_post_meta( $post_id, $ld_recalc['scan_key'], $ld_recalc['scan_identifier'] );
				return true;
			}
		}

		$location['lat'] = $coords['lat'];
		$location['lng'] = $coords['lng'];

		$result = update_field( $ld_recalc['fields']['google_map'], $location, $post_id );

		if ( $result ) {
			// Save lat/long as separate meta keys too.
			update_post_meta( $post_id, 'latitude', $location['lat'] );
			update_post_meta( $post_id, 'longitude', $location['lng'] );

			update_post_meta( $post_id, $ld_recalc['scan_key'], $ld_recalc['scan_identifier'] );
			return true;
		}else{
			echo '<h2>ERROR! Failed to update the google map field for post ID #'. $post_id .':</h2>';
			exit;
		}
	}

	return false;
}


function recalc_acf_get_latlng( $address ) {
	// http://stackoverflow.com/a/8633623/470480
	$address = urlencode($address); // Spaces as + signs

	$request = wp_remote_get("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false");
	$json = wp_remote_retrieve_body( $request );

	if ( !$json ) {
		echo 'Google Maps returned an empty response';
		return false;
	}

	$data = json_decode($json);
	if ( !$data ) {
		echo '<h2>ERROR! Google Maps returned an invalid response, expected JSON data:</h2>';
		echo esc_html(print_r($json, true));
		exit;
	}

	if ( isset($data->{'error_message'}) ) {
		echo '<h2>ERROR! Google Maps API returned an error:</h2>';
		echo '<strong>'. esc_html($data->{'status'}) .'</strong> ' . esc_html($data->{'error_message'}) .'<br>';
		exit;
	}

	if ( empty($data->{'results'}[0]->{'geometry'}->{'location'}->{'lat'}) || empty($data->{'results'}[0]->{'geometry'}->{'location'}->{'lng'}) ) {
		echo '<h2>ERROR! Latitude/Longitude could not be found:</h2>';
		echo esc_html(print_r($data, true));
		exit;
	}

	$lat = $data->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
	$lng = $data->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};

	// Value can be negative, so check for specifically 0.
	if ( floatval( $lat ) === 0 || floatval( $lng ) === 0 ) {
		echo '<h2>ERROR! Latitude/Longitude is invalid (exactly zero):</h2>';
		var_dump('Latitude:', $lat);
		var_dump('Longitude:', $lng);
		var_dump('Result:', $data->{'results'}[0]);
		exit;
	}

	return array( 'lat' => $lat, 'lng' => $lng );
}


function recalc_acf_clean_address( $address ) {
	$address = preg_replace('/ *(\r\n|\r|\n)+ */', " ", $address); // No linebreaks
	return $address;
}
