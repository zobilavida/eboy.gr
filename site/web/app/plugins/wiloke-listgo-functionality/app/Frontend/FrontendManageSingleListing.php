<?php
namespace WilokeListGoFunctionality\Frontend;

use WilokeListGoFunctionality\CustomerPlan\CustomerPlan;
use WilokeListGoFunctionality\Submit\AddListing;

class FrontendManageSingleListing{

	public function __construct() {
		add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
	}


	public function enqueueScripts(){
		if ( is_singular('listing') && is_user_logged_in() ){
			wp_enqueue_media();
		}
	}

	public static function packageAllow($target){
		global $post;

		if ( !isset($post->post_type) || $post->post_type !== 'listing' ){
			return true;
		}

		$packageID = \Wiloke::getPostMetaCaching($post->ID, AddListing::$packageIDOfListing);
		if ( empty($packageID) ){
			return true;
		}

		$packageID = CustomerPlan::detectCustomerPackageID($packageID);
		$aPackageSettings = \Wiloke::getPostMetaCaching($packageID, 'pricing_settings');
		if ( !isset($aPackageSettings[$target]) || ($aPackageSettings[$target] == 'enable') ){
			return true;
		}

		return false;
	}
}