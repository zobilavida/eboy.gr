<?php
namespace WilokeListGoFunctionality\CustomerPlan;

use PayPal\PayPalAPI\GetRecurringPaymentsProfileDetailsReq;
use PayPal\PayPalAPI\GetRecurringPaymentsProfileDetailsRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use WilokeListGoFunctionality\AlterTable\AlterTablePackageStatus;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentHistory;
use WilokeListGoFunctionality\Payment\Payment;
use WilokeListGoFunctionality\Register\RegisterWilokeSubmission;
use WilokeListGoFunctionality\Submit\AddListing;
use WilokeListGoFunctionality\Submit\User as WilokeUser;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentRelationships;
use WilokeListGoFunctionality\Payment\PayPal as WilokePayPal;
use WilokeListGoFunctionality\Frontend\FrontendListingManagement;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsReq;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsRequestType;
use WilokeListGoFunctionality\Payment\TwoCheckout as WilokeTwoCheckout;
use WilokeListGoFunctionality\Payment\EventPayment as WilokeEventPayment;

class CustomerPlan{
	protected static $aConfigs = array();
	public static $getRPPDetailsResponse;
	protected static $aUserPaymentInfo = null;
	private static $customerStatus = null;
	private static $customerPayPalPlanKey = 'wiloke_submission_customer_paypal_plan';
	public static $myRemainListingsKey = 'wiloke_submission_my_remain_listings';
	private static $checkedMyPlanKey = 'wiloke_submission_checked_my_plan';
	private static $checkMyRemainOnFirstInit = 'wiloke_submission_checked_my_remain_on_first_init';
	private static $nonRecurringKey = 'NonRecurring';
	private static $recurringKey = 'RecurringPayPal';
	private static $freeKey = 'Free';
	private static $unlimitedKey = 'umlimited';
	private static $totalFreePosts = 10000000000000;
	private static $isTest = false;

	public function __construct() {
		add_action('init', array($this, 'init'), 2);
		add_action('wiloke/wiloke-listgo-functionality/submit/instered_listing', array($this, 'updateCustomerInfo'));
		add_action('wiloke/wiloke-listgo-functionality/submit/updated_listing', array($this, 'updateCustomerInfo'));
		add_action('wiloke/wiloke-listgo-functionality/App/Submit/Add/packageAllow/packageID', array(__CLASS__, 'detectCustomerPackageID'));
		add_action('init', array(__CLASS__, 'reCalculateRemainListings'), 3);
		add_action('wp_logout', array($this, 'destroyAllSessionWhenUserLogout'));
		add_action('wp_ajax_wiloke_submission_render_package_preview', array($this, 'renderPackagePreview'));
//		add_action('init', array($this, 'testfat'));
	}

	public function testfat(){
		delete_user_meta(get_current_user_id(), self::$customerPayPalPlanKey);
//		self::reCalculateRemainListings(true);
	}

	public function restartEverything(){
		$userID = get_current_user_id();
		delete_user_meta($userID, self::$customerPayPalPlanKey);

		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$packageStatusTbl = $wpdb->prefix . AlterTablePackageStatus::$tblName;

		$wpdb->delete(
			$historyTbl,
			array(
				'user_ID'=>$userID
			),
			array(
				'%d'
			)
		);

		$wpdb->delete(
			$packageStatusTbl,
			array(
				'user_ID'=>$userID
			),
			array(
				'%d'
			)
		);
	}

	public function init(){
		if ( !is_user_logged_in() || is_admin() ){
			return false;
		}
		if ( \WilokePublic::$oUserInfo->role !== WilokeUser::$wilokeSubmissionRole ){
			return false;
		}

		self::paypalConfiguration();

		if ( !isset(self::$aConfigs['toggle']) || self::$aConfigs['toggle'] === 'disable' ){
			return false;
		}

		if ( !isset(self::$aConfigs['billing_type']) || self::$aConfigs['billing_type'] === 'None' ){
			return false;
		}

		self::getCustomerPlan();

		if ( empty(self::$aUserPaymentInfo) ){
			return false;
		}

		## Check Payment Status
		$gateWay = self::getGateWay();
		$gateWay = strtolower($gateWay);
		if ( $gateWay === 'paypal' ){
			self::getRecurringPaymentsProfileDetails();
			if ( !self::$getRPPDetailsResponse ){
				return false;
			}

			self::$customerStatus = self::$getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->ProfileStatus;
		}elseif ( $gateWay == '2checkout' ){
			global $wpdb;
			$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
			self::$customerStatus = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT profile_status FROM $historyTbl WHERE ID=%d",
					self::$aUserPaymentInfo['paymentID']
				)
			);
		}
	}

	private static function getGateWay($userID=null){
		if ( isset(self::$customerStatus['gateWay']) && !empty(self::$customerStatus['gateWay']) ){
			return self::$customerStatus['gateWay'];
		}

		if ( !empty($userID) ){
			$aUserPaymentInfo = self::getCustomerPlanByID($userID);
		}else{
			$aUserPaymentInfo = self::$aUserPaymentInfo;
		}

		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$gateWay = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT method FROM $historyTbl WHERE ID=%d",
				$aUserPaymentInfo['paymentID']
			)
		);

		$aUserPaymentInfo['gateWay'] = $gateWay;
		self::setCustomerPlan($aUserPaymentInfo, $aUserPaymentInfo['paymentType']);
		return $gateWay;
	}

	public function renderPackagePreview(){
		if ( empty($_GET['packageID']) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Please select a package.', 'listgo')
				)
			);
		}

		if ( get_post_status($_GET['packageID']) !== 'publish' ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('The package does not exist.', 'listgo')
				)
			);
		}

		ob_start();
		echo do_shortcode('[wiloke_pricing is_check_billing_type="no" specify_ids="'.esc_attr($_GET['packageID']).'"]');
		$content = ob_get_clean();
		wp_send_json_success(
			array(
				'msg' => $content
			)
		);
	}

	public static function detectCustomerPackageID($packageID){
		if ( self::isRecurringPlan() ){
			return self::$aUserPaymentInfo['packageID'];
		}
		return $packageID;
	}

	public function destroyAllSessionWhenUserLogout(){
		session_destroy();
	}

	public static function getMyRemainListingKey(){
		return self::$myRemainListingsKey;
	}

	public function updateCustomerInfo(){
		self::reCalculateRemainListings(true);
	}

	## After Customer added a new listing We will recalculate their listing available
	public static function reCalculateRemainListings($isFocus=false){
		if ( (!is_user_logged_in() || is_admin() || empty(\WilokePublic::$oUserInfo->role) || (\WilokePublic::$oUserInfo->role != WilokeUser::$wilokeSubmissionRole) || \Wiloke::getSession(self::$checkMyRemainOnFirstInit)) && !$isFocus ){
			return false;
		}

		self::getCustomerPlan($isFocus);
		self::paypalConfiguration();
		if ( !$isFocus && (!isset(self::$aConfigs['toggle']) || self::$aConfigs['toggle'] === 'disable') ){
			return false;
		}

		if ( empty(self::$aUserPaymentInfo) ){
			if ( get_post(get_current_user_id(), self::$checkedMyPlanKey, true) ){
				\Wiloke::setSession(self::$myRemainListingsKey, 0);
				return false;
			}

			update_user_meta(get_current_user_id(), self::$checkedMyPlanKey, 1);

			$aPlan = self::getHighestPackagePurchased();

			if ( empty($aPlan) ){
				\Wiloke::setSession(self::$myRemainListingsKey, 0);
				return false;
			}
			if ( empty($aPlan['number_of_posts']) ){
				\Wiloke::setSession(self::$myRemainListingsKey, self::$totalFreePosts);
			}else{
				\Wiloke::setSession(self::$myRemainListingsKey, $aPlan['remain']);
			}

			self::setCustomerPlan(
				array(
					'packageID'    => $aPlan['package_ID'],
					'paymentID'    => $aPlan['ID'],
					'profileID'    => null,
					'paymentToken' => $aPlan['token'],
					'gateWay'      => $aPlan['gateWay']
				),
				$aPlan['paymentType']
			);
		}else{
			if ( !isset(self::$aUserPaymentInfo['packageID']) || empty(self::$aUserPaymentInfo['packageID']) ){
				return false;
			}

			$aPackageInfo = \Wiloke::getPostMetaCaching(self::$aUserPaymentInfo['packageID'], 'pricing_settings');

			if ( empty($aPackageInfo) ){
				\Wiloke::setSession(self::$myRemainListingsKey, 0);
				return false;
			}

			if ( empty($aPackageInfo['number_of_posts']) ){
				\Wiloke::setSession(self::$myRemainListingsKey, self::$totalFreePosts);
			}else{
				if ( self::isRecurringPlan() && ($aPackageInfo['number_of_listing_per'] == 'regular_period') && !empty($aPackageInfo['regular_period']) ){
					$countListingsAdded = self::countTotalListingsByPeriodRegular($aPackageInfo);
				}else{
					$countListingsAdded = self::countTotalListingsByPaymentID(self::$aUserPaymentInfo['paymentID']);
				}

				$remain = absint($aPackageInfo['number_of_posts']) - absint($countListingsAdded);
				if ( $remain > 0 ){
					\Wiloke::setSession(self::$myRemainListingsKey, $remain);
				}else{
					\Wiloke::setSession(self::$myRemainListingsKey, 0);
				}

				if ( self::isNonRecurringPlan() && $remain <= 0 ){
					delete_user_meta(get_current_user_id(), self::$customerPayPalPlanKey);
				}
			}
		}
		\Wiloke::setSession(self::$checkMyRemainOnFirstInit, 1);
		return false;
	}

	protected static function getHighestPackagePurchased(){
		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$aAllPlan = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $historyTbl WHERE user_ID=%d AND token<>'' AND (status=%s OR status=%s OR status=%s) AND profile_ID IS NULL AND package_type=%s",
				get_current_user_id(), 'pending', 'completed', 'Success', 'pricing'
			),
			ARRAY_A
		);

		if ( empty($aAllPlan) ){
			return false;
		}

		$aPassed = array();
		$currentPrice = 0;
		$aSecondary = array();

		foreach ( $aAllPlan as $aPlan ){
			$aPackageInfo = \Wiloke::getPostMetaCaching($aPlan['package_ID'], 'pricing_settings');
			if ( empty($aPackageInfo['number_of_posts']) ){
				if ( !empty($aPackageInfo['price']) ){
					return $aPackageInfo;
				}
				$aSecondary = $aPackageInfo;
				continue;
			}

			if ( $aPlan['total'] < $currentPrice ){
				continue;
			}

			if ( ($aPlan['method'] === 'checkpayment') ){
				$totalListingsAdded = self::countTotalListingsByPaymentID($aPlan['ID']);
				if ( absint($totalListingsAdded) >= absint($aPackageInfo['number_of_posts']) ){
					continue;
				}

				$currentPrice = $aPlan['total'];
				$aPassed = $aPlan;
				$aPassed['remain'] = absint($aPackageInfo['number_of_posts']) - absint($totalListingsAdded);
				$aPassed['paymentType'] = self::$nonRecurringKey;
			}elseif ( $aPlan['method'] === 'paypal' ){
				$oPasteInformation = json_decode($aPlan['information']);

				if ( empty($oPasteInformation) || ( !isset($oPasteInformation->DoExpressCheckoutPaymentResponseDetails) && !isset($oPasteInformation->GetExpressCheckoutDetailsResponseDetails)) ){
					continue;
				}

				if ( !empty($oPasteInformation->Errors) ){
					continue;
				}

				$totalListingsAdded = self::countTotalListingsByPaymentID($aPlan['ID']);
				if ( absint($totalListingsAdded) >= absint($aPackageInfo['number_of_posts']) ){
					continue;
				}

				$currentPrice = $aPlan['total'];
				$aPassed = $aPlan;
				$aPassed['remain'] = absint($aPackageInfo['number_of_posts']) - absint($totalListingsAdded);
				$aPassed['paymentType'] = self::$nonRecurringKey;
			}else if ( $aPlan['method'] === 'freelisting' ){
				$totalListingsAdded = self::countTotalListingsByPaymentID($aPlan['ID']);
				if ( absint($totalListingsAdded) >= absint($aPackageInfo['number_of_posts']) ){
					continue;
				}

				$currentPrice = 0;
				$aPassed = $aPlan;
				$aPassed['remain'] = absint($aPackageInfo['number_of_posts']) - absint($totalListingsAdded);
				$aPassed['paymentType'] = self::$freeKey;
			}
		}

		if ( empty($aPassed) ){
			if ( !empty($aSecondary) ){
				return $aSecondary;
			}
			return false;
		}

		return $aPassed;
	}

	protected static function countTotalListingsByPaymentID($paymentID){
		global $wpdb;
		$paymentRelationshipTbl = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(object_ID) FROM $paymentRelationshipTbl WHERE payment_ID=%d",
				$paymentID
			)
		);
	}

	private static function passedProfileStatus($status){
		if ( $status === 'ActiveProfile' || $status === 'PendingProfile' ){
			return true;
		}
		return false;
	}

	protected static function countTotalListingsByPeriodRegular($aPackageInfo){
		global $wpdb;
		$postsTbl = $wpdb->prefix . 'posts';

		if ( self::$aUserPaymentInfo['gateWay'] == 'paypal' ){
			self::getRecurringPaymentsProfileDetails(true);
			if ( empty(self::$getRPPDetailsResponse) || !self::passedProfileStatus(self::$getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->ProfileStatus)){
				return self::$totalFreePosts+100000;
			}

			$nextBillingDate = self::$getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsSummary->NextBillingDate;

			if ( empty($nextBillingDate) ){
				$billingStarDate = self::$getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsProfileDetails->BillingStartDate;

				$total = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(ID) FROM $postsTbl WHERE (post_date BETWEEN %s AND (%s + INTERVAL %d DAY) ) AND  post_author=%d",
						$billingStarDate, $billingStarDate, absint($aPackageInfo['regular_period']), get_current_user_id()
					)
				);
				return $total;
			}

		}elseif( self::$aUserPaymentInfo['gateWay'] == '2checkout' ){
			$aSaleDetails = WilokeTwoCheckout::getDetailSale(
				array(
					'sale_id' => self::$aUserPaymentInfo['profileID']
				)
			);
			if ( empty($aSaleDetails) || ($aSaleDetails['response_code'] != 'OK') ){
				return self::$totalFreePosts+100000;
			}

			$aLineItems = WilokeTwoCheckout::getRecurringLineItems($aSaleDetails);
			if ( empty($aLineItems) ){
				return self::$totalFreePosts+100000;
			}

			$nextBillingDate = $aLineItems[0]['billing']['date_next'];

		}else{
			return self::$totalFreePosts+100000;
		}

		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(ID) FROM $postsTbl WHERE (post_date BETWEEN (%s - INTERVAL %d DAY) AND %s) AND  post_author=%d",
				$nextBillingDate, absint($aPackageInfo['regular_period']), $nextBillingDate, get_current_user_id()
			)
		);

		return intval($total);
	}

	public static function isNonRecurringPlan($aUserPaymentInfo=null){
		if ( empty($aUserPaymentInfo) ){
			$aUserPaymentInfo = self::getCustomerPlan();
		}

		if ( empty($aUserPaymentInfo['paymentType']) ){
			return false;
		}

		return $aUserPaymentInfo['paymentType'] == self::$nonRecurringKey;
	}

	public static function isRecurringPlan($aUserPaymentInfo=null){
		if ( empty($aUserPaymentInfo) ){
			$aUserPaymentInfo = self::getCustomerPlan();
		}

		if ( !isset($aUserPaymentInfo['paymentType']) || empty($aUserPaymentInfo['paymentType']) ){
			return false;
		}

		return $aUserPaymentInfo['paymentType'] == self::$recurringKey;
	}

	public static function isFreePlan($aUserPaymentInfo=null){
		if ( empty($aUserPaymentInfo) ){
			$aUserPaymentInfo = self::getCustomerPlan();
		}

		if ( empty($aUserPaymentInfo['paymentType']) ){
			return false;
		}

		return $aUserPaymentInfo['paymentType'] == self::$freeKey;
	}

	protected static function _getCustomerPlan($isFocus){
		if ( !empty(self::$aUserPaymentInfo) && !$isFocus ){
			return false;
		}
		self::$aUserPaymentInfo = get_user_meta(get_current_user_id(), self::$customerPayPalPlanKey, true);
	}

	public static function getCustomerPlanByID($userID){
		$aCustomerPlan = get_user_meta($userID, self::$customerPayPalPlanKey, true);

		if ( !isset($aCustomerPlan['gateWay']) && !current_user_can('edit_theme_options') ){
			if ( isset($aCustomerPlan['paymentID']) && !empty($aCustomerPlan['paymentID']) ){
				global $wpdb;
				$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
				$gateWay = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT method FROM $historyTbl WHERE ID=%d",
						$aCustomerPlan['paymentID']
					)
				);

				$aUserPaymentInfo['gateWay'] = $gateWay;
				self::setCustomerPlan($aUserPaymentInfo, $aUserPaymentInfo['paymentType']);
			}
		}
		return $aCustomerPlan;
	}

	public static function getCustomerPlan($isFocus=false){
		self::_getCustomerPlan($isFocus);
		return self::$aUserPaymentInfo;
	}

	public static function renderOutStandingAmount(){
		return Payment::getCurrency() . absint(self::$getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsSummary->OutstandingBalance->value);
	}

	public static function getOutStandingBalance($getRPPDetailsResponse){
		return !empty($getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsSummary->OutstandingBalance) ? absint($getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsSummary->OutstandingBalance->value) : 0;
	}

	/*
	 * $aData: packageID, profileID (payment profile id), paymentID, paymentToken, $paymentType: required
	 */
	public static function setCustomerPlan($aData, $paymentType, $userID=null){
		$userID = empty($userID) ? get_current_user_id() : $userID;
		self::_setCustomerPlan($aData, $paymentType, $userID);
	}

	private static function _setCustomerPlan($aData, $paymentType, $userID){
		$aData['paymentType'] = $paymentType;
		$aPackageSettings = \Wiloke::getPostMetaCaching($aData['packageID'], 'pricing_settings');

		if ( empty(WilokeEventPayment::getRemainingEvent($userID)) ){
			$aData['eventPlanID'] = isset($aPackageSettings['event_pricing_package']) ? $aPackageSettings['event_pricing_package'] : '';
			$aData['paymentEventID'] = empty($aData['eventPlanID']) ? '' :  $aData['paymentID'] ;
		}

		update_user_meta($userID, self::$customerPayPalPlanKey, $aData);
		self::getCustomerPlan(true);
		self::reCalculateRemainListings(true);
		do_action('wiloke/wiloke-listgo-functionality/app/customerplan/updatedCustomerPlan', $userID);
	}

	public static function updateEventPlanIDToCustomerPlan($eventPlanID, $customerID=null){
		$customerID = empty($customerID) ? get_current_user_id() : $customerID;
		$aData = get_user_meta($customerID, self::$customerPayPalPlanKey, true);
		$aData['eventPlanID'] = $eventPlanID;
		update_user_meta($customerID, self::$customerPayPalPlanKey, $aData);
	}

	public static function updatePaymentEventIDToCustomerPlan($paymentEventID, $customerID=null){
		$customerID = empty($customerID) ? get_current_user_id() : $customerID;
		$aData = get_user_meta($customerID, self::$customerPayPalPlanKey, true);
		$aData['paymentEventID'] = $paymentEventID;
		update_user_meta($customerID, self::$customerPayPalPlanKey, $aData);
	}

	public static function removeEventPlan($customerID=null){
		$customerID = empty($customerID) ? get_current_user_id() : $customerID;
		$aData = get_user_meta($customerID, self::$customerPayPalPlanKey, true);
		unset($aData['paymentEventID']);
		unset($aData['eventPlanID']);
		update_user_meta($customerID, self::$customerPayPalPlanKey, $aData);
	}

	public static function getCurrentOutStandingAmount(){
		self::getCustomerPlan(true);

		if ( empty(self::$aUserPaymentInfo) || !isset(self::$aUserPaymentInfo['profileID']) || empty(self::$aUserPaymentInfo['profileID']) ){
			return 0;
		}

		self::getRecurringPaymentsProfileDetails();
		if ( empty(self::$getRPPDetailsResponse) ){
			return 0;
		}

		return !empty(self::$getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsSummary->OutstandingBalance) ? absint(self::$getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsSummary->OutstandingBalance->value) : 0;
	}

	public static function customerStatus(){
		if ( !isset($_REQUEST['package_id']) || empty($_REQUEST['package_id']) ){
			return 'SelectPackage';
		}

		if ( current_user_can('edit_theme_options') ){
			return 'AddListingPage';
		}

		$postID = isset($_REQUEST['post_id']) && !empty($_REQUEST['post_id']) ? $_REQUEST['post_id'] : '';
		if ( !empty($postID) && get_post_field('post_author', $_REQUEST['post_id']) != get_current_user_id() ){
			return 'StopEditing';
		}

		if ( isset($_REQUEST['post_id']) && AddListing::isEditingPublishedListing($_REQUEST['post_id']) ){
			return 'AddListingPage';
		}

		self::paypalConfiguration();
		self::getCustomerPlan();
		if ( empty(self::$aUserPaymentInfo) ){
			return 'AddListingPage';
		}
		$myRemainListings = \Wiloke::getSession(self::$myRemainListingsKey);
		if ( $myRemainListings > 0 ){
			return 'AddListingPage';
		}

		# Recurring Status
		if ( (self::$customerStatus === 'Suspended') || (self::$customerStatus === 'Cancelled') || (self::$customerStatus === 'Expired')  ){
			return 'Suspended';
		}

		if ( self::isNonRecurringPlan() ){
			if ( Payment::getBillingType() == 'RecurringPayments' ){
				if ( self::isExceededListings(self::$aUserPaymentInfo['packageID'], $postID) ){
					return 'ExceededListings';
				}else{
					return 'AddListingPage';
				}
			}

			return 'AddListingPage';
		}

		if ( self::isFreePlan() ){
			if ( self::isExceededListings($_REQUEST['package_id'], $postID) ){
				return 'ExceededListings';
			}else{
				return 'AddListingPage';
			}
		}

		if ( self::isExceededListings($_REQUEST['package_id'], $postID) ){
			return 'ExceededListings';
		}else{
			return 'AddListingPage';
		}
	}

	public static function isExceededListings($packageID, $postID){
		$myRemainingListing = \Wiloke::getSession(CustomerPlan::getMyRemainListingKey());
		$aPackageInfo = \Wiloke::getPostMetaCaching($packageID, 'pricing_settings');

		if ( empty($postID) ){
			if ( Payment::getBillingType() == 'None' ){
				if ( empty($aPackageInfo['price']) && ($myRemainingListing <= 0) ){
					return true;
				}
			}else{
				return ($myRemainingListing <= 0);
			}
			return false;
		}

		return false;
	}

	public static function exceededMessage(){
		ob_start();
		if ( Payment::getBillingType() == 'None' ){
			$aInfo = array(
				'message' => sprintf( __('You have exceeded the number of listings for this plan. Please go to <a href="%s">Package Plan</a> and purchase a new plan to continue adding listing', 'wiloke'), esc_url(\WilokePublic::getPaymentField('package', true)) ),
				'icon' => 'icon_error-triangle_alt'
			);
		}else{
			$myAccount = \WilokePublic::getPaymentField('myaccount', true);
			$aInfo = array(
				'message' => sprintf( __('You have exceeded the number of listings for this plan. Fortunately, You can continue submitting by upgrading to higher plan. <a href="%s">Yes, I want to upgrade my plan.</a>', 'wiloke'), esc_url(\WilokePublic::addQueryToLink($myAccount, 'mode=my-billing')) ),
				'icon' => 'icon_error-triangle_alt'
			);
		}

		FrontendListingManagement::message($aInfo, 'danger');
		$content = ob_get_clean();
		return $content;
	}

	public static function suspendedMessage(){
		$myAccount = \WilokePublic::getPaymentField('myaccount', true);
		ob_start();
		$aInfo = array(
			'title'   => esc_html__('Whoops! There\'s an outstanding balance on your account', 'wiloke'),
			'message' => sprintf( __('We can not automatically deduct from your PayPal account, so to pay the balance you\'ll need make a payment through  PayPal in the Dashboard\'s Billing section: <a href="%s">Go to My Dashboard</a> Please let us know if you have any additional questions by contacting us at <a href="mail:%s">%s</a>', 'wiloke'),  esc_url(\WilokePublic::addQueryToLink($myAccount, 'mode=my-billing')), esc_attr(get_option('admin_email'))),
			'icon' => 'icon_error-triangle_alt'
		);
		FrontendListingManagement::message($aInfo, 'danger');
		$content = ob_get_clean();
		return $content;
	}

	public static function planIsAvailableMessage(){
		ob_start();
		$aInfo = array(
			'title'   => esc_html__('Your subscription is still available now', 'wiloke'),
			'message' => sprintf( __('<a href="%s">Go to Add Listing page</a>', 'wiloke'),  esc_url(self::renderAddListingLink()))
		);
		FrontendListingManagement::message($aInfo, 'success');
		$content = ob_get_clean();
		return $content;
	}

	public static function selectPackageMessage(){
		ob_start();
		$aInfo = array(
			'title'   => esc_html__('You need to select one package before', 'wiloke'),
			'message' => sprintf( __('<a href="%s">Go to Add Pricing page</a>', 'wiloke'),  esc_url(\WilokePublic::getPaymentField('package')))
		);
		FrontendListingManagement::message($aInfo, 'success');
		$content = ob_get_clean();
		return $content;
	}

	public static function stopEditingMessage(){
		ob_start();
		$aInfo = array(
			'title'   => esc_html__('It is not yours listing.', 'wiloke'),
			'message' => esc_html__('You do not permission to access this page.', 'wiloke'),
			'icon' => 'icon_error-triangle_alt'
		);
		FrontendListingManagement::message($aInfo, 'danger');
		$content = ob_get_clean();
		return $content;
	}

	public static function includingYourOutStandingAmountMessage(){
		$outStandingAmount = self::getCurrentOutStandingAmount();
		if ( empty($outStandingAmount) ){
			return false;
		}

		ob_start();
		$aInfo = array(
			'message' => sprintf( __('We notice that your account has an outstanding balance of %s, so the payment will include the package price and your outstanding amount.', 'wiloke'),  self::renderOutStandingAmount())
		);
		FrontendListingManagement::message($aInfo, 'success');
		$content = ob_get_clean();
		return $content;
	}

	public static function controlPackagePage(){
		$status = self::customerStatus();

		if ( $status === 'StopEditing' ){
			self::stopEditingMessage();
		}

		if ( $status === 'AddListingPage' ){
			self::planIsAvailableMessage();
		}

		if ( $status === 'ExceededListings' ){
			return self::exceededMessage();
		}

		if ( $status === 'Suspended' ){
			return self::suspendedMessage();
		}

		if ( $status === 'SelectPackage' ){
			return self::selectPackageMessage();
		}

		return true;
	}

	public static function controlAddListingPage(){
		$status = self::customerStatus();
		if ( $status === 'ExceededListings' ){
			return self::exceededMessage();
		}

		if ( $status === 'Suspended' ){
			return self::suspendedMessage();
		}

		return true;
	}

	public static function renderAddListingLink(){
		if ( !is_user_logged_in() || empty(self::$aUserPaymentInfo) ){
			return \WilokePublic::getPaymentField('package', true);
		}

		if ( self::isNonRecurringPlan() || self::isFreePlan() ){
			$isLinkToPackage = true;
			if( \Wiloke::getSession(self::$myRemainListingsKey) > 0 ){
				$isLinkToPackage = false;
			}else{
				if ( Payment::getBillingType() != 'None' ){
					$isLinkToPackage = false;
				}
			}

			if ( $isLinkToPackage ){
				return \WilokePublic::getPaymentField('package', true);
			}else{
				$link = \WilokePublic::getPaymentField('addlisting', true);
				$link = \WilokePublic::addQueryToLink($link, 'package_id='.self::$aUserPaymentInfo['packageID']);
				return $link;
			}
		}

		if (  \Wiloke::getSession(self::$myRemainListingsKey) > 0 ){
			$link = \WilokePublic::getPaymentField('addlisting', true);
			$link = \WilokePublic::addQueryToLink($link, 'package_id='.self::$aUserPaymentInfo['packageID']);
			return $link;
		}

		return \WilokePublic::getPaymentField('package', true);
	}

	protected static function getTotalListings(){
		global $wpdb;

		$totalListings = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_author=%d AND post_type=%s",
					get_current_user_id(), 'listing'
			)
		);
		return absint($totalListings);
	}

	/**
	 * Detecting Customer Plan after logged in
	 * @since 1.0.9
	 */

	public function checkCustomerPlan(){

	}

	protected static function paypalConfiguration(){
		if ( !empty(self::$aConfigs) ){
			return false;
		}

		self::$aConfigs = get_option(RegisterWilokeSubmission::$submissionConfigurationKey);

		if ( !empty(self::$aConfigs) ){
			self::$aConfigs = json_decode(stripslashes(self::$aConfigs), true);
		}else{
			if ( current_user_can('administrator') ){
				wp_die( esc_html__('Payment could not process because you have not configure your PayPal. Please go to Pricing -> Settings to complete it', 'wiloke') );
			}else{
				wp_die( esc_html__('OOps! Something went wrong. Please report this issue to ', 'wiloke') . get_option('admin_email') );
			}
		}
	}

	protected static function getRecurringPaymentsProfileDetails($isFocus=false){
		if ( !empty(self::$getRPPDetailsResponse) && !$isFocus ){
			return false;
		}

		/*
		 * Obtain information about a recurring payments profile.
		 */

		$getRPPDetailsReqest = new GetRecurringPaymentsProfileDetailsRequestType();
		/*
		 * (Required) Recurring payments profile ID returned in the CreateRecurringPaymentsProfile response. 19-character profile IDs are supported for compatibility with previous versions of the PayPal API.
		 */
		$getRPPDetailsReqest->ProfileID = self::$aUserPaymentInfo['profileID'];


		$getRPPDetailsReq = new GetRecurringPaymentsProfileDetailsReq();
		$getRPPDetailsReq->GetRecurringPaymentsProfileDetailsRequest = $getRPPDetailsReqest;

		/*
		 * 	 ## Creating service wrapper object
		Creating service wrapper object to make API call and loading
		Configuration::getAcctAndConfig() returns array that contains credential and config parameters
		*/

		$paypalService = new PayPalAPIInterfaceServiceService(WilokePayPal::getPayPalConfiguration());

		try {
			/* wrap API method calls on the service object with a try catch */
			$getRPPDetailsResponse = $paypalService->GetRecurringPaymentsProfileDetails($getRPPDetailsReq);
		} catch (\Exception $ex) {
			self::$getRPPDetailsResponse = false;
		}

		if(isset($getRPPDetailsResponse)) {
			self::$getRPPDetailsResponse = $getRPPDetailsResponse;
		}
	}
}