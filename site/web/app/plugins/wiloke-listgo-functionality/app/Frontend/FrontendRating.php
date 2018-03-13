<?php
namespace WilokeListGoFunctionality\Frontend;


use WilokeListGoFunctionality\AlterTable\AlterTableReviews as WilokeReviewTbl;

class FrontendRating{
	protected $averageRating = 0;
	protected $postID = '';
	public static $averageRatingMetaKey = 'wiloke_average_rating';

	public function __construct() {
		add_action('wiloke/wiloke_submission/save_review', array($this, 'processingCalculationAverageRating'), 10, 2);
	}

	public function processingCalculationAverageRating($reviewID, $postID){
		if ( empty($postID) ){
			return false;
		}

		$this->postID = $postID;
		$this->calculateAverageRating();
	}

	protected function calculateAverageRating(){
		global $wpdb;
		$tblRating = $wpdb->prefix . WilokeReviewTbl::$tblName;
		$average = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT AVG(rating) FROM $tblRating WHERE post_ID=%d",
				$this->postID
			)
		);

		update_post_meta($this->postID, self::$averageRatingMetaKey, absint($average));
	}
}