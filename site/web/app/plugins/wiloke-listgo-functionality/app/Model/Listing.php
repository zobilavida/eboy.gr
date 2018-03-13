<?php
/**
 * We will check listings status daily
 * @since 1.2.1
 */

namespace WilokeListGoFunctionality\Model;

use WilokeListGoFunctionality\Model\GeoPosition;

class Listing{
	public function init(){
		add_action('update_post_meta', array($this, 'updateListingMeta'), 20, 4);
		add_action('added_post_meta', array($this, 'addedListingMeta'), 20, 4);
		add_action('deleted_post_meta', array($this, 'deletedListingMeta'), 20, 4);
	}

	public function updateListingMeta($metaID, $objectID, $metaKey, $aMetaValue){
		if ( $metaKey != 'listing_settings' ){
			return false;
		}
		$aMetaValue = maybe_unserialize($aMetaValue);
		$this->updateListingToGeoLocation($objectID, $aMetaValue);
	}

	public function addedListingMeta($metaID, $objectID, $metaKey, $aMetaValue){
		if ( $metaKey != 'listing_settings' ){
			return false;
		}
		$aMetaValue = maybe_unserialize($aMetaValue);
		$this->updateListingToGeoLocation($objectID, $aMetaValue);
	}

	public function deletedListingMeta($metaID, $objectID, $metaKey, $aMetaValue){

		if ( $metaKey != 'listing_settings' ){
			return false;
		}
		$aMetaValue = maybe_unserialize($aMetaValue);
		$this->updateListingToGeoLocation($objectID, $aMetaValue);
	}

	public function updateListingToGeoLocation($objectID, $aMetaValue){
		if ( !isset($aMetaValue['map']) || !isset($aMetaValue['map']['latlong']) ){
			return false;
		}

		$aLatLng = explode(',', $aMetaValue['map']['latlong']);

		if ( !GeoPosition::checkGeoExisting($objectID) ){
			GeoPosition::addGeoPosition($aLatLng[0], $aLatLng[1], $objectID);
		}else{
			GeoPosition::updateGeoPosition($aLatLng[0], $aLatLng[1], $objectID);
		}
	}
}