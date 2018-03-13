<?php
/**
 * We will check listings status daily
 * @since 1.2.1
 */
namespace WilokeListGoFunctionality\Model;


use WilokeListGoFunctionality\AlterTable\AlterTableReviews as WilokeReviewTbl;

class Review{
	public static $averageRatingMetaKey = 'wiloke_average_rating';

	public function __construct() {
		add_action('post_updated', array($this, 'updateReviewTable'), 10, 3);
		add_action('post_updated', array($this, 'updateReviewAfterUpdated'), 10, 3);
	}

	public function updateReviewAfterUpdated($reviewID, $oAfter, $oBefore){
		if ( $oAfter->post_type !== 'review' ){
			return false;
		}

		if ( ($oBefore->post_status == 'publish') || ($oAfter->post_status != 'publish') ){
			return false;
		}

		global $wpdb;
		$tblName = $wpdb->prefix . WilokeReviewTbl::$tblName;

		$postID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_ID FROM $tblName WHERE review_ID=%d",
				$reviewID
			)
		);

		if ( !empty($postID) ){
			return false;
		}

		$wpdb->insert(
			$tblName,
			array(
				'user_ID'   => $oAfter->post_author,
				'post_ID'   => get_post_meta($reviewID, 'wiloke_listgo_review_belong_to', true),
				'review_ID' => $reviewID,
				'rating'    => get_post_meta($reviewID, 'wiloke_listgo_rating_score', true)
			),
			array(
				'%d',
				'%d',
				'%d',
				'%d'
			)
		);

		$this->calculateAverageRating($reviewID);
	}

	public function updateReviewTable($reviewID, $oAfter, $oBefore){
		if ( $oAfter->post_type !== 'review' ){
			return false;
		}

		if ( ($oBefore->post_status != 'publish') || ($oAfter->post_status == 'publish') ){
			return false;
		}


		$this->calculateAverageRating($reviewID);
		$this->deleteRatingFromTable($reviewID);
	}

	private function calculateAverageRating($reviewID){
		global $wpdb;
		$tblRating = $wpdb->prefix . WilokeReviewTbl::$tblName;
		$postID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_ID FROM $tblRating WHERE review_ID=%d",
				$reviewID
			)
		);

		$average = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT AVG(rating) FROM $tblRating WHERE post_ID=%d",
				$postID
			)
		);

		update_post_meta($reviewID, self::$averageRatingMetaKey, absint($average));
	}

	private function deleteRatingFromTable($reviewID){
		global $wpdb;
		$tblName = $wpdb->prefix . WilokeReviewTbl::$tblName;

		$aData = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT rating, post_ID FROM $tblName WHERE review_ID=%d",
				$reviewID
			),
			ARRAY_A
		);

		update_post_meta($reviewID, 'wiloke_listgo_rating_score', $aData['rating']);
		update_post_meta($reviewID, 'wiloke_listgo_review_belong_to', $aData['post_ID']);

		$wpdb->delete(
			$tblName,
			array(
				'review_ID' => $reviewID
			),
			array(
				'%d'
			)
		);
	}
}