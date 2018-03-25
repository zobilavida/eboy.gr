// Javascript for Admin Area
// Google Map API Standard Code

var map;
var marker;

var geocoder;

var region = 'us';

var markers = new Array();

jQuery(document).ready(function(){

	var address = geoip_city()+', '+geoip_country_name();
	var lat = "";
	var lng = "";
	var location = "";
		
	if(jQuery('#map_canvas').length) {

	geocoder = new google.maps.Geocoder();
	
		geocoder.geocode( {'address':address,'region':region}, function(results, status) {
		
			if(status == google.maps.GeocoderStatus.OK) {
			
				var lat = results[0].geometry.location.lat();
				var lng = results[0].geometry.location.lng();
				var location = results[0].geometry.location;
				if (jQuery("input[name=ssf_wp_longitude]" ).length ) {
				 jQuery('#ssf_wp_longitude').val(lng.toFixed(7)); // lat
			     jQuery('#ssf_wp_latitude').val(lat.toFixed(7)); // long
				}
		
				var gmap_marker = false;
				if(jQuery('#ssf_wp_latitude').length) {
				
					val = jQuery('#ssf_wp_latitude').val()*1;
					
					if(val != '' && !isNaN(val)) {
						lat = val;
						gmap_marker = true;
					}
					
				}
		

				if(jQuery('#ssf_wp_longitude').length) {
				
					val = jQuery('#ssf_wp_longitude').val()*1;
					
					if(val != '' && !isNaN(val)) {
						lng = val;
					}
				}

				geocoder = new google.maps.Geocoder();

				var latlng = new google.maps.LatLng(lat,lng);

				var myOptions = {
					zoom: 9,
					center: latlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};

				map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

				if(gmap_marker) {

					/*var marker = new google.maps.Marker({
						map: map,
						position: latlng
					});*/
				}
				
				marker = new google.maps.Marker({
				  position: latlng, 
				  map: map, 
				  title: 'Drag Me',
				  draggable: true
				});
				
				var infowindow = new google.maps.InfoWindow({
								maxWidth: "300",
								content: 'Drag Me'
				});
							
				infowindow.open(map,marker);
				
				google.maps.event.addListener(marker, 'drag', function(event) {
					 jQuery('#ssf_wp_longitude').val(event.latLng.lng().toFixed(7)); // lat
					 jQuery('#ssf_wp_latitude').val(event.latLng.lat().toFixed(7)); // long
				});
				 
				google.maps.event.addListener(marker, 'dragend', function(event) {
					 jQuery('#ssf_wp_longitude').val(event.latLng.lng().toFixed(7)); // lat
					 jQuery('#ssf_wp_latitude').val(event.latLng.lat().toFixed(7)); // long
				});
		
		}});
	}

	if(jQuery('#address').length) {

		jQuery('#address, #ssf_wp_city, #ssf_wp_state, #ssf_wp_zip').blur(function(){

			var address='';
			address +=' '+ jQuery('#address').val();
			address +=' '+ jQuery('#ssf_wp_city').val();
			address +=' '+ jQuery('#ssf_wp_state').val();
			address +=' '+ jQuery('#ssf_wp_zip').val();
			if(address != '') {

				get_coordinate(address,region);
			}
		});
	}
	
});


/**
 * Get address location
 */
 
function get_coordinate(address, region) {
	
	if(region==null || region == '' || region == 'undefined') {
		region = 'us';
	}

	if(address != '') {
		jQuery('#ajax_msg').html('<p>Loading location</p>');

		geocoder.geocode( {'address':address,'region':region}, function(results, status) {

			if(status == google.maps.GeocoderStatus.OK) {
				jQuery('#ajax_msg').html('<p></p>');
				// populate form field with geo location
				jQuery('#ssf_wp_longitude').val( results[0].geometry.location.lng().toFixed(7));
				jQuery('#ssf_wp_latitude').val( results[0].geometry.location.lat().toFixed(7));

				map.setZoom(10);

				map.setCenter(results[0].geometry.location);

				// Google Map Marker

				marker.setPosition(results[0].geometry.location);
				
			} else {

				jQuery('#ajax_msg').html('<p>Google map geocoder failed: '+status+'</p>');
			}
		});
	}
}

