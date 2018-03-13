<?php
/*
 * Check Payment Method
 * @since 1.0
 */
namespace WilokeListGoFunctionality\Payment;
use Aws\CloudFront\Exception\Exception;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentRelationships;
use WilokeListGoFunctionality\Payment\Payment as WilokePayment;

use WilokeListGoFunctionality\AlterTable\AlterTablePaymentHistory;
use WilokeListGoFunctionality\AlterTable\AlterTablePackageStatus;
use WilokeListGoFunctionality\Register\RegisterPricingSettings;
use WilokeListGoFunctionality\Register\RegisterWilokeSubmission;
use WilokeListGoFunctionality\Submit\AddListing;
use WilokeListGoFunctionality\CustomerPlan\CustomerPlan as WilokeCustomerPlan;

final class CheckPayment{
	public static $method   = 'checkpayment';
	private $_packageID     = null;
	private $_userID        = null;
	private $_status        = null;
	private $_paymentID     = null;
	private $_aPackageInformation     = null;
	private $_listingID     = null;

	/**
	 * Add New payment
	 */
	public function addNewPayment($packageID){
		$aConfiguration = \WilokePublic::getPaymentField();

		if ( empty($aConfiguration) ){
			if ( current_user_can('administrator') ){
				wp_die( esc_html__('Payment could not process because you have not configure your Payment.', 'wiloke') );
			}else{
				wp_die( esc_html__('OOps! Something went wrong. Please report this issue to ', 'wiloke') . get_option('admin_email') );
			}
		}

		if ( $aConfiguration['billing_type'] === 'RecurringPayments' ){
			wp_die( esc_html__('You do not have permission to access this page', 'wiloke') );
		}

		$this->_packageID   = absint($packageID);
		$this->_userID      = get_current_user_id();
		$this->_status      = 'processing';
		$this->_listingID   = \Wiloke::getSession(AddListing::$listingIDSessionKey);

		$validatedPurcharsedPackage = $this->checkPackageRelationStatus();

		$packageExists = $this->checkPackageExists();

		if ( !$validatedPurcharsedPackage && $packageExists && !empty($this->_listingID) ){
			$this->_insertPaymentHistory();
			$this->_insertPaymentRelationship();
			\Wiloke::removeSession(Payment::$packageIDSessionKey);
			\Wiloke::removeSession(AddListing::$listingIDSessionKey);

			// send mail here
			header('Location: '.Payment::redirectTo('thankyou', true));
		}else{
			$oError = new \WP_Error('broken', esc_html__('The user already purchased this package. It stills available now.', 'wiloke'));
			echo ($oError->get_error_message());
		}
	}

	public function checkPackageExists(){
		$post = get_posts(
			array(
				'post_type'     => 'pricing',
				'post_status'   =>  'public',
				'ID'            => $this->_packageID
			)
		);
		if ( empty($post) || is_wp_error($post) ){
			return false;
		}

		return true;
	}

	protected function _insertPaymentRelationship(){
		Payment::$latestPaymentID = $this->_paymentID;
		Payment::_updatePaymentRelationships($this->_packageID);
	}

	public function manuallyAddPackage($packageID, $userID, $status){
		$this->_packageID   = absint($packageID);
		$this->_userID      = absint($userID);
		$this->_status      = $status;
		$validatedPurcharsedPackage = $this->checkPackageRelationStatus();

		if ( !$validatedPurcharsedPackage ){
			return $this->_insertPaymentHistory();
		}else{
			return new \WP_Error('broken', esc_html__('The user already purcharsed this package. It stills available now.', 'wiloke'));
		}
	}

	public function manuallyUpdatePackage($packageID, $userID, $status, $paymentID){
		$this->_packageID   = absint($packageID);
		$this->_userID      = absint($userID);
		$this->_paymentID   = absint($paymentID);
		$this->_status      = $status;

		$paymentExist = $this->checkPaymentHistoryStatus();
		if ( !$paymentExist ){
			return new \WP_Error('broken', esc_html__('This payment does not exist.', 'wiloke'));
		}else{
			$oError = $this->_updatePaymentHistory();
			if ( is_wp_error($oError) ){
				return $oError;
			}
			$this->_updateListingsStatus();

			$oError = $this->_updatePackageStatus();
			if ( is_wp_error($oError) ){
				return $oError;
			}
			return (object)array(
				'payment_ID' => $this->_paymentID,
				'user_ID'    => $this->_userID,
				'package_ID' => $this->_packageID,
				'status'     => $this->_status,
				'message'    => esc_html__('Updated Payment successfully', 'wiloke'),
				'success'    => true
			);
		}
	}

	/**
	 * Updating Post Status via manual
	 * @since 1.0
	 */
	protected function _updatePostStatus(){
		global $wpdb;
		$tblRelationShip    = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;
		$tblPaymentHistory  = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$aResults = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT $tblRelationShip.object_ID FROM $tblRelationShip INNER JOIN $tblPaymentHistory ON ($tblRelationShip.payment_ID = $tblPaymentHistory.ID) WHERE $tblPaymentHistory.package_ID=%d AND $tblPaymentHistory.ID=%d",
				$this->_packageID, $this->_paymentID
			)
		);

		if ( !empty($aResults) ){
			foreach ( $aResults as $oResult ){
				if ( get_post_field('post_status', $oResult->object_ID) === 'processing'  ){
					wp_update_post(
						array(
							'ID'            => $oResult->object_ID,
							'post_status'   => 'pending'
						)
					);
				}
			}
		}
	}

	public function getPaymentInformation($paymentID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$aData = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT user_ID, package_ID, status FROM $tblName WHERE ID=%d",
				$paymentID
			),
			ARRAY_A
		);
		return $aData;
	}

	protected function checkPackageRelationStatus(){
		global $wpdb;
		$tblStatus = $wpdb->prefix . AlterTablePackageStatus::$tblName;
		$tblRelationShip = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;

		$status = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT $tblStatus.payment_ID FROM $tblStatus INNER JOIN $tblRelationShip ON ($tblRelationShip.payment_ID=$tblStatus.payment_ID) WHERE $tblStatus.package_ID=%d AND $tblStatus.user_ID=%d AND $tblStatus.status=%s",
				$this->_packageID, $this->_userID, 'available'
			)
		);

		if ( empty($status) ){
			return false;
		}

		return true;
	}

	protected function checkPaymentHistoryStatus(){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$status = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $tblName WHERE ID=%d AND user_ID=%d",
				$this->_paymentID, $this->_userID
			)
		);

		if ( empty($status) ){
			return false;
		}

		return true;
	}

	private function _insertPaymentHistory(){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$token   = WilokePayment::generateToken();
		$this->_aPackageInformation = \Wiloke::getPostMetaCaching($this->_packageID, 'pricing_settings');
		$price = isset($this->_aPackageInformation['price']) ? absint($this->_aPackageInformation['price']) : 0;
		WilokePayment::getPaymentConfiguration();
		$aBuyerInformation = $this->_paymentInformation();

		$status = $wpdb->insert(
			$tblName,
			array(
				'package_ID'    => $this->_packageID,
				'package_type'  => get_post_field('post_type', $this->_packageID),
				'user_ID'       => $this->_userID,
				'token'         => $token,
				'method'        => self::$method,
				'information'   => json_encode($aBuyerInformation),
				'status'        => $this->_status,
				'created_at'    => date('Y-m-d'),
				'updated_at'    => date('Y-m-d'),
				'total'         => $price,
				'currency'      => WilokePayment::$aPaymentConfiguration['currency_code']
			),
			array(
				'%d',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s'
			)
		);

		if ( empty($status) ){
			return new \WP_Error('broken', esc_html__('Could not insert payment history. Please contact us at sale@wiloke.com or open a topic via http://support.wiloke.com to report this issue.', 'wiloke'));
		}

		$this->_paymentID = $wpdb->insert_id;
		$oError = $this->_insertPackageStatus();

		if ( is_wp_error($oError) ){
			return $oError;
		}

		do_action('wiloke_submission/payment_history/', $this->_userID, $this->_paymentID, $this->_status, $this->_packageID);

		$this->updateEventPlanToUserMeta();
		return (object)array(
			'payment_ID' => $this->_paymentID,
			'user_ID'    => $this->_userID,
			'package_ID' => $this->_packageID,
			'status'     => $this->_status,
			'message'    => esc_html__('Insert Payment successfully', 'wiloke'),
			'success'    => true
		);
	}

	protected function _insertPackageStatus(){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePackageStatus::$tblName;

		$status = $wpdb->insert(
			$tblName,
			array(
				'package_ID'    => $this->_packageID,
				'user_ID'       => $this->_userID,
				'payment_ID'    => $this->_paymentID,
				'package_information'   => json_encode($this->_aPackageInformation),
				'status'        => 'available'
			),
			array(
				'package_ID'   => '%d',
				'user_ID'      => '%d',
				'payment_ID'   => '%d',
				'package_information'  => '%s',
				'status'       => '%s'
			)
		);

		if ( !$status ){
			return new \WP_Error('broken', esc_html__('Could not insert Package Status', 'wiloke'));
		}

		return true;
	}

	protected function _paymentInformation(){
		$aUserInfo = get_userdata($this->_userID);
		return array(
			'title'         => get_the_title($this->_packageID),
			'user_ID'       => $this->_userID,
			'amount'        => $this->_aPackageInformation['price'],
			'buyer_email'   => $aUserInfo->user_email,
			'purchased_at'  => date('Y-m-d')
		);
	}

	private function _updatePackageStatus(){
		global $wpdb;
		$aPackageInformation = \Wiloke::getPostMetaCaching($this->_packageID, 'pricing_settings');
		if ( empty($aPackageInformation) ){
			return new \WP_Error('broken', esc_html__('The package does not exist. Please select another one.', 'wiloke'));
		}

		$tblPackageStatus = $wpdb->prefix . AlterTablePackageStatus::$tblName;
		$tblPaymentRelationship = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;

		if ( !isset($aPackageInformation['number_of_posts']) || empty($aPackageInformation['number_of_posts']) ){
			$newStatus = 'available';
		}else{
			$totalPostUsed = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(object_ID) FROM $tblPaymentRelationship WHERE package_ID=%d AND payment_ID=%d",
					$this->_packageID, $this->_paymentID
				)
			);

			if ( absint($totalPostUsed) === absint($aPackageInformation['number_of_posts']) ){
				$newStatus = 'unavailable';
			}else{
				$newStatus = 'available';
			}
		}

		$status = $wpdb->update(
			$tblPackageStatus,
			array(
				'status' => $newStatus,
				'package_information' => json_encode($aPackageInformation)
			),
			array(
				'payment_ID' => $this->_paymentID,
				'user_ID'    => $this->_userID
			),
			array(
				'%s',
				'%s'
			),
			array(
				'%d',
				'%d'
			)
		);

		if ( $status === false ){
			return new \WP_Error('broken', esc_html__('Could not update Payment History. The possible reason: Wrong Payment ID. Wrong Customer ID', 'wiloke'));
		}

		return true;
	}

	protected function _updatePaymentHistory(){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$aUpdates = array(
			'status'    => $this->_status,
			'user_ID'   => $this->_userID,
			'package_ID'=> $this->_packageID,
			'package_type'=> get_post_field('post_type', $this->_packageID)
		);

		$aFormats = array(
			'%s',
			'%d',
			'%d'
		);

		$status = $wpdb->update(
			$tblName,
			$aUpdates,
			array(
				'ID' => $this->_paymentID
			),
			$aFormats,
			array(
				'%d'
			)
		);

		if ( $status === false ){
			return new \WP_Error('broken', esc_html__('Could not update Payment History. The possible reason: Wrong Payment ID.', 'wiloke'));
		}

		$this->updateEventPlanToUserMeta();
		do_action('wiloke_submission/payment_history/', $this->_userID, $this->_paymentID, $this->_status, $this->_packageID);
		return true;
	}

	protected function _updateListingsStatus(){
		if ( $this->_status === 'failed' || $this->_status === 'canceled' ){
			$listingStatus = 'trash';
		}elseif($this->_status==='completed' || $this->_status === 'pending'){
			$listingStatus = 'pending';
		}

		if ( !isset($listingStatus) ){
			return true;
		}

		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;
		$aListingIDs = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT object_ID FROM $tblName WHERE payment_ID=%d",
				$this->_paymentID
			),
			ARRAY_N
		);

		foreach ( $aListingIDs as $listingID ){
			$currentStatus = get_post_status($listingID);
			if ( $currentStatus === 'processing' || $currentStatus === 'trash' || $currentStatus === 'processing' ){
				wp_update_post(
					array(
						'ID'            => $listingID,
						'post_type'     => 'listing',
						'post_status'   => $listingStatus
					)
				);
			}
		}

		return true;
	}

	/*
     * @since 1.2.2
	 * This step is very important, It helps to detect whether user has to buy a new plan or not
	 */
	protected function updateEventPlanToUserMeta(){
		if ( $this->_status === 'completed' ){
			WilokeCustomerPlan::updateEventPlanIDToCustomerPlan($this->_packageID, $this->_userID);
			WilokeCustomerPlan::updatePaymentEventIDToCustomerPlan($this->_paymentID, $this->_userID);
		}else{
			$aCustomerPlan = WilokeCustomerPlan::getCustomerPlanByID($this->_userID);
			if ( isset($aCustomerPlan['paymentEventID']) && ($aCustomerPlan['paymentEventID'] == $this->_paymentID) ){
				WilokeCustomerPlan::removeEventPlan($this->_userID);
			}
		}
	}

}