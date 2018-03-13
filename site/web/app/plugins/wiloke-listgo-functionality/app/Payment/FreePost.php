<?php
/**
 * It's special case. When this post is free
 * @since 1.0
 */

/*
 * Check Payment Method
 * @since 1.0
 */
namespace WilokeListGoFunctionality\Payment;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentRelationships;
use WilokeListGoFunctionality\Payment\Payment as WilokePayment;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentHistory;
use WilokeListGoFunctionality\CustomerPlan\CustomerPlan;

class FreePost{
	public static $method = 'freelisting';
	private static $paymentType = 'Free';

	public static function updateCustomerPlan($packageID, $paymentID, $paymentToken){
		CustomerPlan::setCustomerPlan(
			array(
				'packageID'    => $packageID,
				'profileID'    => 'xxx',
				'paymentID'    => $paymentID,
				'paymentToken' => $paymentToken,
				'gateWay'      => 'manually'
			),
			self::$paymentType
		);
	}

	public static function insertPaymentHistory($packageID, $userID=null){
		$paymentID = self::_insertPaymentHistory($packageID, $userID);
		return $paymentID;
	}

	private static function _insertPaymentHistory($packageID, $userID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$userID = !empty($userID) ? $userID : get_current_user_id();
		$paymentToken = WilokePayment::generateToken();

		$wpdb->insert(
			$tblName,
			array(
				'user_ID'       => $userID,
				'package_ID'    => $packageID,
				'package_type'  => get_post_field('post_type', $packageID),
				'token'         => $paymentToken,
				'method'        => self::$method,
				'information'   => json_encode(array(
					'user_ID'   => $userID,
					'IP_address'=> \Wiloke::clientIP()
				)),
				'status'        => 'completed'
			),
			array(
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s'
			)
		);
		$paymentID = $wpdb->insert_id;
		if ( empty($paymentID) ){
			return false;
		}

		WilokePayment::$userID = $userID;
		WilokePayment::insertPackageStatus($packageID, $paymentID);

		CustomerPlan::setCustomerPlan(
			array(
				'profileID' => null,
				'packageID' => $packageID,
				'paymentID' => $paymentID,
				'paymentToken' => $paymentToken,
				'gateWay'   => 'manually'
			),
			self::$paymentType
		);

		return $paymentID;
	}

	public static function isPackageExists($packageID, $isUpdateCustomerPlan=true){
		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$aValue = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $historyTbl WHERE user_ID=%d AND package_ID=%d",
				get_current_user_id(), $packageID
			),
			ARRAY_A
		);

		if ( empty($aValue) ){
			return false;
		}

		if ( $isUpdateCustomerPlan ){
			CustomerPlan::setCustomerPlan(
				array(
					'profileID' => null,
					'packageID' => $packageID,
					'paymentID' => $aValue['ID'],
					'paymentToken' => $aValue['token'],
					'gateWay'   => 'manually'
				),
				self::$paymentType
			);
		}
		return true;
	}

	public static function isExceededFreePlan($packageID, $aPackageInfo){
		if ( !is_user_logged_in() ){
			return false;
		}

		if ( empty($aPackageInfo['number_of_posts']) ){
			return false;
		}
		global $wpdb;

		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$freePaymentID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $historyTbl WHERE user_ID=%d AND method=%s AND package_type=%s",
				get_current_user_id(), 'freelisting', 'pricing'
			)
		);

		if ( empty($freePaymentID) ){
			return false;
		}

		$paymentRelationship = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;
		$addedListingWithFreeCounter = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(object_ID) FROM $paymentRelationship WHERE payment_ID=%d",
				$freePaymentID
			)
		);

		if ( $addedListingWithFreeCounter >= $aPackageInfo['number_of_posts'] ){
			return true;
		}

		return false;
	}
}