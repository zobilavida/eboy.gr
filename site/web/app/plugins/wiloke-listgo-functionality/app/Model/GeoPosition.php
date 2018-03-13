<?php
/**
 * We will check listings status daily
 * @since 1.2.1
 */

namespace WilokeListGoFunctionality\Model;


use WilokeListGoFunctionality\AlterTable\AlterTableGeoPosition;

class GeoPosition{
	public static $tblName = null;

	public function __construct() {
		add_action('admin_init', array($this, 'reUpdateGeoCodeDB'));
	}

	public static function searchLocationWithin($centerLat, $centerLng, $distance=5, $unit = 'km', $limit = null, $aOtherData=array()){
		global $wpdb;
		$geoTbl = $wpdb->prefix . AlterTableGeoPosition::$tblName;

		$limit = !empty($limit) ? abs($limit) : 1000;
		$distance = empty($distance) ? 10 : abs($distance);

		$unit = $unit == 'km' ? 6371 : 3959;

		if ( !isset($aOtherData['posts__not_in']) || empty($aOtherData['posts__not_in']) ){
			$aObjectIDs = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT SQL_CALC_FOUND_ROWS postID, ( %d * acos( cos( radians('%s') ) * cos( radians( $geoTbl.lat ) ) * cos( radians( $geoTbl.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $geoTbl.lat ) ) ) ) as distance FROM $geoTbl HAVING distance < %d ORDER BY distance LIMIT 0,%d",
					$unit, $centerLat, $centerLng, $centerLat, $distance, $limit
				),
				ARRAY_A
			);
		}else{
			$aOtherData['posts__not_in'] = array_map('abs', $aOtherData['posts__not_in']);
			$aObjectIDs = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT SQL_CALC_FOUND_ROWS postID, ( %d * acos( cos( radians('%s') ) * cos( radians( $geoTbl.lat ) ) * cos( radians( $geoTbl.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $geoTbl.lat ) ) ) ) as distance FROM $geoTbl HAVING distance < %d AND postID NOT IN(%s) ORDER BY distance LIMIT 0,%d",
					$unit, $centerLat, $centerLng, $centerLat, $distance, implode(',', $aOtherData['posts__not_in']), $limit
				),
				ARRAY_A
			);
		}

		$totalPosts = $wpdb->get_var("SELECT FOUND_ROWS()");

		if ( empty($aObjectIDs) ){
			return false;
		}

		$aObjectIDs = array_map(function($aObject){
			return $aObject['postID'];
		}, $aObjectIDs);

		return array(
			'IDs' => $aObjectIDs,
			'total' => $totalPosts
		);
	}


	public function reUpdateGeoCodeDB(){
		if ( !get_option('wiloke_listgo_reupdated_listing_geocode') ){

			$query = new \WP_Query(
				array(
					'post_type'         => 'event',
					'posts_status'      => 'publish',
					'posts_per_page'    =>  -1
				)
			);

			if ( $query->have_posts() ){
				while ($query->have_posts()){
					$query->the_post();
					$aEventSettings = \Wiloke::getPostMetaCaching($query->post->ID, 'event_settings');
					if ( !empty($aEventSettings['latitude']) && !empty($aEventSettings['longitude']) && !empty($aEventSettings['belongs_to']) ){
						if ( !self::checkGeoExisting($query->post->ID) ){
							self::addGeoPosition($aEventSettings['latitude'], $aEventSettings['longitude'], $query->post->ID);
						}else{
							self::updateGeoPosition($aEventSettings['latitude'], $aEventSettings['longitude'], $query->post->ID);
						}
						update_post_meta($query->post->ID, 'wiloke_event_belongs_to', $aEventSettings['belongs_to']);
					}
				}
			}


			$query = new \WP_Query(
				array(
					'post_type'     => 'listing',
					'posts_status'  => 'publish',
					'posts_per_page'=>  -1
				)
			);

			if ( $query->have_posts() ){
				while ($query->have_posts()){
					$query->the_post();
					$aListingSettings = \Wiloke::getPostMetaCaching($query->post->ID, 'listing_settings');
					if ( !empty($aListingSettings['map']['latlong'])  ){
						$aLatLng = explode(',', $aListingSettings['map']['latlong']);
						if ( !self::checkGeoExisting($query->post->ID) ){
							self::addGeoPosition($aLatLng[0], $aLatLng[1], $query->post->ID);
						}else{
							self::updateGeoPosition($aLatLng[0], $aLatLng[1], $query->post->ID);
						}
					}
				}
			}

			update_option('wiloke_listgo_reupdated_listing_geocode', true);
		}
	}

	public static function getTblName($wpdb){
		self::$tblName = $wpdb->prefix . AlterTableGeoPosition::$tblName;
	}

	public static function addGeoPosition($lat, $lng, $postID){
		global $wpdb;
		self::getTblName($wpdb);

		$wpdb->insert(
			self::$tblName,
			array(
				'lat' => $lat,
				'lng' => $lng,
				'postID' => $postID
			),
			array(
				'%s',
				'%s',
				'%d'
			)
		);

		return $wpdb->insert_id;
	}

	public static function updateGeoPosition($lat, $lng, $postID){
		global $wpdb;
		self::getTblName($wpdb);

		$wpdb->update(
			self::$tblName,
			array(
				'lat' => $lat,
				'lng' => $lng
			),
			array(
				'postID' => $postID,
			),
			array(
				'%s',
				'%s'
			),
			array(
				'%d'
			)
		);
	}

	public static function checkGeoExisting($postID){
		global $wpdb;
		self::getTblName($wpdb);

		$id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM ".self::$tblName." WHERE postID=%d",
				$postID
			)
		);

		if ( empty($id) ){
			return false;
		}

		return $id;
	}
}