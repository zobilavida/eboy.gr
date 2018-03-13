<?php
/*
 * This is an intermedia step. Package ID and payment method will be saved here
 *
 * @since 1.0
 * @author Wiloke
 * @package Wiloke/Themes
 * @subpackage Listgo
 * @link https://wiloke.com
 */

namespace WilokeListGoFunctionality\Payment;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentRelationships;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentHistory;
use WilokeListGoFunctionality\AlterTable\AlterTablePackageStatus;
use WilokeListGoFunctionality\CustomerPlan\CustomerPlan;
use WilokeListGoFunctionality\Register\RegisterPricingSettings;
use WilokeListGoFunctionality\Register\RegisterWilokeSubmission;
use WilokeListGoFunctionality\Submit\AddListing;

class Payment{
	public static $postType = 'listings';
	public static $submissionType = 'wiloke-submission';

	public static $packageIDSessionKey = 'wiloke_listgo_package_id';
	public static $packageAvailableSession = 'wiloke_listgo_package_available';

	public $aPaymentSettings = array();
	public static $latestPaymentID = 0;
	public static $latestListingID = null;
	public static $aPaymentConfiguration = array();
	private static $aConfigs = array();

	public static $userID = null;

	public function __construct() {
		add_action('wp_ajax_wiloke_submission_delete_order', array($this, 'deleteOrder'));

		// Important: Add Package Type to existing Payments
		add_action('admin_init', array($this, 'addPackageTypeToExistingPayments'));
	}

	public function addPackageTypeToExistingPayments(){
		global $wpdb;

		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$tblExisting = $wpdb->query(
			$wpdb->prepare(
				"SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=%s AND TABLE_NAME=%s AND COLUMN_NAME=%s",
				$wpdb->dbname, $historyTbl, 'package_type'
			)
		);

		if ( $tblExisting && !get_option('wiloke_listgo_added_package_type_to_payment') ){

			$aPayments = $wpdb->get_results(
				"SELECT * FROM $historyTbl",
				ARRAY_A
			);

			if ( $aPayments ){
				foreach ( $aPayments as $aPayment ){
					$wpdb->update(
						$historyTbl,
						array(
							'package_type' => get_post_field('post_type', $aPayment['package_ID'])
						),
						array(
							'ID' => $aPayment['ID']
						),
						array(
							'%s'
						),
						array(
							'%d'
						)
					);
				}
			}

			update_option('wiloke_listgo_added_package_type_to_payment', true);
		}
	}

	public static function getPaymentGateWays($isRemoveCheckPayment=false){
		$aGateWays = self::acceptGateWays();
		if ( empty($aGateWays) ){
			return false;
		}


		$aAllGateWays = array(
			'checkpayment'  => esc_html__('Check payment', 'wiloke'),
			'paypal'        => esc_html__('Proceed with PayPal', 'wiloke'),
			'2checkout'     => esc_html__('Credit Card (2Checkout)', 'wiloke')
		);

		$aGateWaysAndLabels = array();
		foreach ( $aGateWays  as $gateway ){
			$aGateWaysAndLabels[$gateway] = $aAllGateWays[$gateway];
		}

		if ( $isRemoveCheckPayment ){
			unset($aGateWaysAndLabels['checkpayment']);
		}

		return $aGateWaysAndLabels;
	}

	public static function acceptGateWays(){
		$aConfiguration = self::getPaymentConfiguration();
		if ( !isset($aConfiguration['payment_gateways']) || empty($aConfiguration['payment_gateways']) ){
			return false;
		}

		$aSetupGateWays = explode(',', $aConfiguration['payment_gateways']);
		$aSetupGateWays = array_map('trim', $aSetupGateWays);

		if ( isset($aConfiguration['billing_type']) && ($aConfiguration['billing_type'] === 'RecurringPayments') ){
			$checkpaymentKey = array_search('checkpayment', $aSetupGateWays);
			if ( $checkpaymentKey !== false ){
				unset($aSetupGateWays[$checkpaymentKey]);
			}

		}

		return $aSetupGateWays;
	}

	public function deleteOrder(){
		if ( !current_user_can('edit_theme_options') || !isset($_POST['payment_ID']) || empty($_POST['payment_ID']) ){
			wp_send_json_error();
		}

		global $wpdb;
		$tblPaymentHistory = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$tblPaymentRelationships = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;
		$tblPackageStatus = $wpdb->prefix . AlterTablePackageStatus::$tblName;

		$wpdb->delete(
			$tblPaymentHistory,
			array(
				'ID' => $_POST['payment_ID']
			),
			array(
				'%d'
			)
		);

		$wpdb->update(
			$tblPackageStatus,
			array(
				'payment_ID' => $_POST['payment_ID'],
				'updated_at' => time()
			),
			array(
				'status'     => 'unavailable'
			),
			array(
				'%d',
				'%d'
			),
			array(
				'%s'
			)
		);

		$aPostIDs = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT object_ID from $tblPaymentRelationships WHERE payment_ID=%d",
				$_POST['payment_ID']
			),
			ARRAY_N
		);

		if ( !empty($aPostIDs) ){
			foreach ( $aPostIDs as $postID ){
				wp_update_post(
					array(
						'ID'            => $postID,
						'post_type'     => 'listing',
						'post_status'   => 'trash'
					)
				);
			}
		}

		wp_send_json_success();
	}

	/**
	 * Check package status
	 * @since 1.0
	 */
	private static function _checkPackageStatus(){
		global $wpdb;
		$userID = get_current_user_id();
		$tblPackageStatus = $wpdb->prefix . AlterTablePackageStatus::$tblName;
		$tblRelationship = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;

		$aPackages = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT package_information, payment_ID, package_ID FROM $tblPackageStatus WHERE user_ID=%d AND status=%s",
				$userID, 'available'
			),
			ARRAY_A
		);

		if ( !empty($aPackages) ){
			foreach ( $aPackages as $aPackage ){
				$aPackageInfo = \Wiloke::getPostMetaCaching($aPackage['package_ID'], 'pricing_settings');
				if ( isset($aPackageInfo['price']) && !empty($aPackageInfo['price']) ){
					$aPackageInfo = json_decode($aPackage['package_information'], true);
				}

				if ( !empty($aPackageInfo['number_of_posts']) ){
					$total = $wpdb->get_var($wpdb->prepare(
						"SELECT COUNT(object_ID) FROM $tblRelationship WHERE payment_ID=%d",
						$aPackage['payment_ID']
					));

					if ( absint($total) >= absint($aPackageInfo['number_of_posts']) ){
						$wpdb->update(
							$tblPackageStatus,
							array(
								'status' => 'unavailable'
							),
							array(
								'payment_ID' => $aPackage['payment_ID'],
								'user_ID'    => $userID
							),
							array(
								'%s'
							),
							array(
								'%d',
								'%d'
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Get Package Status
	 * @since 1.0
	 */
	public static function getPackageStatus($packageID){
		global $wpdb;
		$userID = get_current_user_id();

		if ( empty($userID) ){
			return false;
		}

		$tblPackageStatus = $wpdb->prefix . AlterTablePackageStatus::$tblName;

		$aPackages = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT package_ID FROM $tblPackageStatus WHERE user_ID=%d AND status=%s AND package_ID=%d",
				$userID, 'available', $packageID
			),
			ARRAY_A
		);

		if ( !empty($aPackages) ){
			return true;
		}

		return false;
	}

	private static function getWilokeSubmissionConfiguration(){
		if ( !empty(self::$aConfigs) ){
			return false;
		}

		self::$aConfigs = get_option(RegisterWilokeSubmission::$submissionConfigurationKey);

		if ( !empty(self::$aConfigs) ){
			self::$aConfigs = json_decode(stripslashes(self::$aConfigs), true);
		}else{
			return false;
		}
	}

	public static function getBillingType(){
		self::getWilokeSubmissionConfiguration();
		if ( !isset(self::$aConfigs['billing_type']) ){
			return 'None';
		}

		return trim(self::$aConfigs['billing_type']);
	}

	/**
	 * Set user's available packages as soon as user has been logged in
	 *
	 * @since 1.0
	 */
	public function setPackageAvailable($userID){
		self::_setPackageAvailable($userID);
	}

	protected static function _setPackageAvailable($userID){
		global $wpdb;
		$tblStatus = $wpdb->prefix . AlterTablePackageStatus::$tblName;

		$aResults = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT package_ID FROM $tblStatus WHERE status=%s AND user_ID=%d",
				"available", $userID
			),
			ARRAY_A
		);

		if ( !empty($aResults) ){
			setcookie(self::$packageAvailableSession, json_encode($aResults), 0, '/');
		}
	}

	public static function checkSession(){
		return \Wiloke::getSession(self::$packageIDSessionKey);
	}

	private static function _updatePackageStatus(){
		global $wpdb;
		$tblPackageStatus = $wpdb->prefix . AlterTablePackageStatus::$tblName;

		$aInfo = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $tblPackageStatus WHERE payment_ID=%d",
				self::$latestPaymentID
			),
			ARRAY_A
		);

		if ( empty($aInfo) ){
			return false;
		}

		if ( $aInfo['status'] === 'unavailable' ){
			return false;
		}

		$aPackageInformation = \Wiloke::getPostMetaCaching($aInfo['package_ID'], 'pricing_settings');

		if ( !isset($aPackageInformation['number_of_posts']) || empty($aPackageInformation['number_of_posts']) ){
			return false;
		}

		$tblPaymentRelationship = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;

		$totalPostUsed = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(object_ID) FROM $tblPaymentRelationship WHERE package_ID=%d AND payment_ID=%d",
				$aInfo['package_ID'], $aInfo['payment_ID']
			)
		);

		if ( absint($totalPostUsed) === absint($aPackageInformation['number_of_posts']) ){
			$wpdb->update(
				$tblPackageStatus,
				array(
					'status' => 'unavailable'
				),
				array(
					'payment_ID' => self::$latestPaymentID,
					'user_ID'    => get_current_user_id()
				),
				array(
					'%s'
				),
				array(
					'%d',
					'%d'
				)
			);

			return true;
		}

		return false;
	}

	/**
	 * Update Payment
	 * @since 1.0
	 */
	public static function updatePaymentHistory($status, $token, $aAllInfo, $profileStatus=null, $profileID=null, $isFocus=false){
		$validateToken = !$isFocus ? self::_validateTokenBeforeUpdating($token) : true;
		if ( $validateToken ){
			$aAllInfo = is_object($aAllInfo) ? get_object_vars($aAllInfo) : $aAllInfo;
			$aAllInfo['IP_address'] = \Wiloke::clientIP();
			self::_updatePaymentHistory($status, $token, $aAllInfo, $profileStatus, $profileID);
		}
	}

	public static function getCurrency(){
		global $WilokeListGoFunctionalityApp;

		if ( empty(self::$aPaymentConfiguration) ){
			self::getPaymentConfiguration();
		}

		if ( empty(self::$aPaymentConfiguration) ){
			return $WilokeListGoFunctionalityApp['currencySymbol']['USD'];
		}else{
			return $WilokeListGoFunctionalityApp['currencySymbol'][self::$aPaymentConfiguration['currency_code']];
		}
	}

	public static function redirectTo($page, $returnHomeIfEmpty=true){
		if ( empty(self::$aPaymentConfiguration) ){
			self::getPaymentConfiguration();
		}

		if ( isset(self::$aPaymentConfiguration[$page]) ){
			return get_permalink(self::$aPaymentConfiguration[$page]);
		}

		return $returnHomeIfEmpty ? home_url('/') : false;
	}

	public static function renderPrice($price='', $isReturn=false){
		$currency = self::getCurrency();
		$currencyPosition = self::$aPaymentConfiguration['currency_position'];

		switch ($currencyPosition){
			case 'right':
				$price = $price . $currency;
				break;
			case 'right_space':
				$price = $price . ' ' . $currency;
				break;
			case 'left_space':
				$price = $currency . ' ' . $price;
				break;
			default:
				$price = $currency . $price;
				break;
		}

		if ( $isReturn ){
			return $price;
		}

		echo esc_html($price);
	}

	public static function getPaymentConfiguration(){
		if ( !empty(self::$aPaymentConfiguration) ){
			return self::$aPaymentConfiguration;
		}

		self::$aPaymentConfiguration = \WilokePublic::getPaymentField();
		if ( empty(self::$aPaymentConfiguration) ){
			self::$aPaymentConfiguration = array(
				'mode'              => 'sandbox',
				'currency_code'     => 'USD',
				'currency_position' => 'left'
			);
		}

		return self::$aPaymentConfiguration;
	}

	private static function _validateTokenBeforeUpdating($token){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$status = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT status FROM $tblName WHERE token=%s",
				$token
			)
		);

		if ( strtolower($status) === 'success' ){
			return false;
		}

		return true;
	}

	private static function _updatePaymentHistory($status, $token, $aAllInfo, $profileStatus, $profileID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$result = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $tblName WHERE token=%s",
				$token
			)
		);

		$packageID = \Wiloke::getSession(self::$packageIDSessionKey);
		$listingID = \Wiloke::getSession(AddListing::$listingIDSessionKey);

		## Proceed update post if customer creates listing and buy a package together
		if ( !empty($listingID) ){
			$postStatus = get_post_field('post_status', $listingID);
			if ( $status === 'pending' || $status === 'completed' || strtolower($status) == 'success' ){
				if ( $postStatus === 'expired' ){
					$newStatus = 'renew';
				}else{
					$newStatus = 'pending';
				}
			}elseif ( $status === 'processing' || $status === 'processed' ){
				$newStatus = $postStatus;
			}else{
				$newStatus = 'trash';
			}
			self::updatePostStatus($newStatus);
		}

		if (!empty($result)){
			self::$latestPaymentID = $result;
			$status = $wpdb->update(
				$tblName,
				array(
					'status'        =>  $status,
					'information'   => json_encode($aAllInfo),
					'updated_at'    => date(DATE_ATOM),
					'profile_status'=> $profileStatus,
					'profile_ID'    => $profileID
				),
				array(
					'token' => $token
				),
				array(
					'%s', '%s', '%s'
				),
				array(
					'%s'
				)
			);
			do_action('wiloke_submission/payment_history/', get_current_user_id(), self::$latestPaymentID, $status, $packageID, $profileID);
			self::_updatePaymentRelationships($packageID);
			if ( empty($status) ){
				wiloke_error_while_adding_listing(__LINE__, __FILE__);
			}
		}else{
			\Wiloke::removeSession(self::$packageIDSessionKey);
			\Wiloke::removeSession(AddListing::$listingIDSessionKey);
			wiloke_error_while_adding_listing(__LINE__, __FILE__);
		}
	}

	public static function updatePostStatus($status){
		$listingID = \Wiloke::getSession(AddListing::$listingIDSessionKey);
		wp_update_post(
			array(
				'ID'            => $listingID,
				'post_status'   => $status
			)
		);
	}

	protected static function createAuthor(){
		if ( !is_user_logged_in() ){

		}
	}

	private static function _maybePaymentExists($packageID){
		global $wpdb;
		$tblPackageStatus = $wpdb->prefix . AlterTablePackageStatus::$tblName;

		self::$latestPaymentID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT payment_ID FROM $tblPackageStatus WHERE package_ID=%d AND user_ID=%d AND status=%s",
				$packageID, self::$userID, 'available'
			)
		);

		self::$latestPaymentID = !empty(self::$latestPaymentID) ? self::$latestPaymentID : 0;
	}

	public static function _updatePaymentRelationships($packageID){
		global $wpdb;
		$listingID = \Wiloke::getSession(AddListing::$listingIDSessionKey);

		$tblName = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;
		$currentStatus = get_post_field('post_status', $listingID);

		if ( $currentStatus !== 'expired' ){
			$wpdb->update(
				$tblName,
				array(
					'payment_ID' => self::$latestPaymentID
				),
				array(
					'object_ID' => $listingID
				),
				array(
					'%d'
				),
				array(
					'%d'
				)
			);
			self::_updatePackageStatus();
		}else{
			self::_insertPaymentRelationships($packageID);
		}
	}

	private static function _insertPaymentRelationships($packageID){
		$listingID = \Wiloke::getSession(AddListing::$listingIDSessionKey);

		if ( empty($listingID) ){
			wiloke_error_while_adding_listing(__LINE__, __FILE__);
		}

		if ( empty(self::$userID) ){
			self::$userID = get_current_user_id();
		}

		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;
		$currentStatus = get_post_field('post_status', $listingID);
		$objectStatus = $currentStatus === 'expired' ? 'renew' : 'addnew';
		self::_maybePaymentExists($packageID);

		$status = $wpdb->insert(
			$tblName,
			array(
				'package_ID' => $packageID,
				'payment_ID' => self::$latestPaymentID,
				'object_ID'  => $listingID,
				'status'     => $objectStatus
			),
			array(
				'%d', '%d', '%d', '%s'
			)
		);

		self::_updatePackageStatus();

		if (empty($status)){
			wiloke_error_while_adding_listing(__LINE__, __FILE__);
		}
	}

	public static function insertPaymentRelationships($packageID){
		self::_insertPaymentRelationships($packageID);
	}

	private static function _insertPaymentHistory($token, $method){
		$packageIDSessionKey = \Wiloke::getSession(self::$packageIDSessionKey);

		if ( empty($packageIDSessionKey) ){
			wiloke_error_while_adding_listing(__LINE__, __FILE__);
		}

		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		self::$userID = get_current_user_id();

		$paymentID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $tblName WHERE token=%s",
				$token
			)
		);

		$packageInfo = \Wiloke::getPostMetaCaching($packageIDSessionKey, 'pricing_settings');
		$price = !isset($packageInfo['price']) || empty($packageInfo['price']) ? 0 : absint($packageInfo['price']);

		self::getPaymentConfiguration();
		// Only insert payment if it does not exist
		if ( empty($paymentID) ){
			$status = $wpdb->insert(
				$tblName,
				array(
					'user_ID'    => self::$userID,
					'package_ID' => $packageIDSessionKey,
					'package_type' => get_post_field('post_type', $packageIDSessionKey),
					'token'      => $token,
					'method'     => $method,
					'information'=> '',
					'total'      => $price,
					'currency'   => self::$aPaymentConfiguration['currency_code'],
					'status'     => 'processing'
				),
				array(
					'%d',
					'%d',
					'%s',
					'%s',
					'%s',
					'%d',
					'%s',
					'%s'
				)
			);

			if ( empty($status) ){
				wiloke_error_while_adding_listing(__LINE__, __FILE__);
			}

			// Updating session insert id
			self::$latestPaymentID = $wpdb->insert_id;
			self::_insertPackageStatus($packageIDSessionKey, self::$latestPaymentID);
		}else{
			self::$latestPaymentID = $paymentID;
		}
	}

	public static function insertPaymentHistory($token, $method){
		self::_insertPaymentHistory($token, $method);
	}

	private static function _insertPackageStatus($packageID, $paymentID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePackageStatus::$tblName;
		$userID = !empty(self::$userID) ? self::$userID : get_current_user_id();

		$aPackageInfo = get_post_meta($packageID, 'pricing_settings', true);
		$aPackageInfo['title'] = get_the_title($packageID);
		$status = 'available';

		$wpdb->insert(
			$tblName,
			array(
				'package_ID'          => $packageID,
				'user_ID'             => $userID,
				'payment_ID'          => $paymentID,
				'package_information' => json_encode($aPackageInfo),
				'status'              => $status
			),
			array(
				'%d', '%d', '%d', '%s', '%s'
			)
		);
	}

	public static function insertPackageStatus($packageID, $paymentID){
		self::_insertPackageStatus($packageID, $paymentID);
	}

	public static function addSession($content){
		global $post;
		$aPaymentSettings = \WilokePublic::getPaymentField();
		// Detect current page. If it's add listing template, We will put package id to session store
		if ( $aPaymentSettings['addlisting'] === $post->ID ){
			self::setPackageChosen();
		}

		return $content;
	}

	public static function setPackageChosen(){
		if ( isset($_REQUEST['package_id']) && !empty($_REQUEST['package_id']) ){
			\Wiloke::setSession(self::$packageIDSessionKey, $_REQUEST['package_id']);
		}
	}

	public static function randomString(){
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < 5; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public static function generateToken(){
		$token = self::_generateToken();
		return $token;
	}

	private static function _generateToken(){
		$now = time();
		$token = $now . self::randomString();
		$token = md5($token);
		return $token;
	}
}