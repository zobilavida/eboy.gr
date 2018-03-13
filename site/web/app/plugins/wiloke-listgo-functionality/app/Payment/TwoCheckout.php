<?php
/**
 * Handle 2Checkout Payment
 * @since 1.1.3
 * @author Wiloke
 * @package Wiloke/Themes
 * @subpackage Listgo
 * @link https://wiloke.com
 */
namespace WilokeListGoFunctionality\Payment;


use WilokeListGoFunctionality\AlterTable\AlterTablePackageStatus;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentHistory;
use WilokeListGoFunctionality\CustomerPlan\CustomerPlan as WilokeCustomerPlan;
use WilokeListGoFunctionality\Email\SendMail;
use WilokeListGoFunctionality\Payment\Payment as WilokePayment;
use WilokeListGoFunctionality\Register\RegisterWilokeSubmission as RegisterWilokeSubmission;
use WilokeListGoFunctionality\Submit\User as WilokeUser;
use WilokeListGoFunctionality\Payment\PayPal as WilokePayPal;
use WilokeListGoFunctionality\Payment\FreePost as WilokeFreePost;
use WilokeListGoFunctionality\Email\SendMail as WilokeSendMail;
use WilokeListGoFunctionality\Frontend\FrontendListingManagement as WilokeFrontendListingManagement;

class TwoCheckout{
	private static $token = null;
	private static $method = '2checkout';
	private static $aConfiguration = null;
	private static $paymentStatus = 'processing';
	private static $aPaymentInfo = array();
	private static $paymentID = null;
	private static $aUserInfo = array();
	private static $packageID = null;
	private static $aPaymentType = array('NonRecurring', 'RecurringPayPal');
	private static $paymentType = null;
	private static $aSubmitInfo = array();
	private static $aInsMessage = array();
	private static $myID = null;
	private static $saleID = null;
	private static $profileStatus = null;
	private static $aUserPaymentInfo;
	private static $activeProfileKey = 'ActiveProfile';
	private static $pendingProfileKey = 'PendingProfile';
	private static $expiredProfileKey = 'ExpiredProfile';
	private static $suspendedProfile = 'SuspendedProfile';
	private static $cancelledProfile = 'CancelledProfile';
	private static $userRefundDataKey = 'wiloke_submission_user_refund_data_';
	private static $refundedStatus = 'Refunded';
	private static $listingID = null;

	public function __construct() {
		add_action('init', array($this, 'listenInstantNotification'));
		add_action('wp_ajax_wiloke_submission_change_plan_with_2checkout', array($this, 'changePlan'));
		add_action('wp_ajax_wiloke_submission_handle_2checkout', array($this, 'handleSubmit'));
		add_action('wiloke/wiloke-listgo-functionality/changedPlan', array($this, 'afterChangedPlan'));
	}

	private static function getConfiguration(){
		if ( !empty(self::$aConfiguration) ){
			return true;
		}

		$options = \WilokePublic::getPaymentField();

		if ( empty($options) ){
			self::$aConfiguration = false;
		}

		self::$aConfiguration = $options;
	}

	private static function updatePaymentHistoryStatus($paymentID, $status, $profileStatus){
		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$wpdb->update(
			$historyTbl,
			array(
				'status'=>$status,
				'profile_status'=>$profileStatus
			),
			array(
				'ID' => $paymentID
			),
			array(
				'%s'
			),
			array(
				'%d'
			)
		);
	}

	private static function updatePaymentHistory(){
		WilokePayment::updatePaymentHistory(self::$paymentStatus, self::$token, self::$aPaymentInfo, self::$profileStatus, self::$saleID);
		self::$paymentID = WilokePayment::$latestPaymentID;
	}

	public function handleSubmit(){
		session_start();
		if ( !isset($_POST['security']) || empty($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Wrong Security Code', 'wiloke')
				)
			);
		}

		self::getConfiguration();

		if ( !self::isAcceptCheckoutGateWay() ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('You do not have permission to access this page.', 'wiloke')
				)
			);
		}

		require_once plugin_dir_path(__FILE__) . '../Lib/2Checkout/lib/Twocheckout.php';
		self::$token = $_POST['token'];
		self::$packageID = \Wiloke::getSession(WilokePayment::$packageIDSessionKey);

		$aPackageInfo = \Wiloke::getPostMetaCaching(self::$packageID, 'pricing_settings');
		// Insert Payment history
		self::insertPaymentHistory();
		self::clearSubmitInfo();
		if ( empty($aPackageInfo['number_of_posts']) || WilokePayment::getBillingType() == 'none' ){
			$aResult = self::nonRecurringPayment();
		}else{
			$aResult = self::recurringPayment();
		}

		if ( $aResult['status'] == 'error' ){
			self::$paymentStatus = 'Failed';
			self::$profileStatus = 'CancelledProfile';
			self::$aPaymentInfo['reason'] = $aResult['msg'];
			self::updatePaymentHistory();
			wp_send_json_error(
				array(
					'msg' => $aResult['msg']
				)
			);
		}
		self::$paymentStatus = 'Success';
		self::$profileStatus = self::$activeProfileKey;
		self::$aPaymentInfo = $aResult['msg'];
		self::$saleID = $aResult['msg']['response']['orderNumber'];
		self::updatePaymentHistory();

		WilokeCustomerPlan::setCustomerPlan(
			array(
				'profileID' => self::$saleID,
				'packageID' => self::$packageID,
				'paymentID' => self::getPaymentIDByToken(),
				'paymentToken' => self::$token,
				'gateWay'   => '2checkout'
			),
			self::$paymentType
		);

		WilokeUser::saveCard(self::$aSubmitInfo);
		$thankyouUrl = get_permalink(self::$aConfiguration['thankyou']);
		$thankyouUrl = \WilokePublic::addQueryToLink($thankyouUrl, "post_id=".$_POST['post_id']."&customer_id=".get_current_user_id());
		wp_send_json_success(
			array(
				'msg' => esc_html__('Thanks for your Order!', 'wiloke'),
				'redirect' => $thankyouUrl
			)
		);
	}

	private static function getPaymentIDByToken(){
		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $historyTbl WHERE token=%s",
				self::$token
			)
		);
	}

	private static function getPaymentIDByProfileID($profileID){
		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $historyTbl WHERE profile_ID=%s",
				$profileID
			)
		);
	}

	private static function getTokenBySaleID(){
		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT token FROM $historyTbl WHERE profileID=%s",
				self::$saleID
			)
		);
	}

	private static function removeAllSession(){
		\Wiloke::removeSession(WilokePayment::$packageIDSessionKey);
	}

	public static function setUserPaymentInfo($aUserPaymentInfo){
		self::$aUserPaymentInfo = $aUserPaymentInfo;
	}

	public static function setPackageID($packageID){
		self::$packageID = $packageID;
	}

	private static function calculatePeriod($aPackageInfo){
		$regularPeriod = absint($aPackageInfo['regular_period']);
		if ( $regularPeriod == 365 ){
			$regularPeriod = '1 Year';
		}else if ( $regularPeriod >= 30 ){
			$regularPeriod = floor($regularPeriod/30)  . ' Month';
		}else{
			$regularPeriod = floor($regularPeriod/7) . ' Week';
 		}

 		return $regularPeriod;
	}

	private static function isAcceptCheckoutGateWay(){
		$aParseGateWay = explode(',', self::$aConfiguration['payment_gateways']);
		if ( in_array('2checkout', $aParseGateWay) ){
			return true;
		}

		return false;
	}

	private static function getExistingTransactions(){
		global $wpdb;
		$historyTblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$aInformation = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $historyTblName WHERE user_ID=%d AND package_ID=%d AND method=%s AND ( (profile_status=%s) ORDER BY ID DESC",
				get_current_user_id(), self::$packageID, '2checkout', self::$suspendedProfile
			),
			ARRAY_A
		);

		return $aInformation;
	}

	private static function clearSubmitInfo(){
		foreach ( $_POST['formData'] as $key => $aData ){
			self::$aSubmitInfo[$aData['name']] = $aData['value'];
		}
	}

	private static function setConfiguration(){
		if ( !class_exists('\Twocheckout') ){
			require_once plugin_dir_path(__FILE__) . '../Lib/2Checkout/lib/Twocheckout.php';
		}

		if ( self::$aConfiguration['mode'] !== 'sandbox' ){
			\Twocheckout::privateKey(self::$aConfiguration['2co_live_private_key']);
			\Twocheckout::sellerId(self::$aConfiguration['2co_live_seller_id']);
			\Twocheckout::sandbox(false);
		}else{
			\Twocheckout::privateKey(self::$aConfiguration['2co_sandbox_private_key']);
			\Twocheckout::sellerId(self::$aConfiguration['2co_sandbox_seller_id']);
			\Twocheckout::sandbox(true);
		}

		if ( defined('TWOCHECKOUT_ADMIN_USER') && defined('TWOCHECKOUT_ADMIN_PASSWORD') ){
			\Twocheckout::username(TWOCHECKOUT_ADMIN_USER);
			\Twocheckout::password(TWOCHECKOUT_ADMIN_PASSWORD);
		}

	}

	private static function recurringPayment(){
		session_start();
		self::getConfiguration();
		if ( empty(self::$aConfiguration) ){
			self::$profileStatus = 'Failed';
			return array(
				'msg' => current_user_can('edit_theme_options') ? esc_html__('Please go to Wiloke Submission -> Settings and Complete your setting', 'wiloke') : esc_html__('Something went wrong', 'wiloke'),
				'status' => 'error'
			);
		}

		if ( !self::isAcceptCheckoutGateWay() ){
			return array(
				'status' => 'error',
				'msg'    => esc_html__('You do not permission to access this page.', 'wiloke')
			);
		}

		self::setConfiguration();
		self::$aUserInfo = \Wiloke::getUserMeta(get_current_user_id());

		$aResult = array(
			'status' => 'error',
			'msg'    => esc_html__('You have not configured 2Checkout yet.', 'wiloke')
		);

		if ( get_post_status(self::$packageID) !== 'publish' ){
			$aResult = array(
				'status' => 'error',
				'msg'    => esc_html__('The package does not exist', 'wiloke')
			);

			return $aResult;
		}

		self::$paymentType = self::$aPaymentType[1];
		$aPackageInfo = \Wiloke::getPostMetaCaching(self::$packageID, 'pricing_settings');

		try {
			$charge = \Twocheckout_Charge::auth(array(
				"merchantOrderId" => self::$paymentID,
				"token"      => self::$token,
				"currency"   => self::$aConfiguration['currency_code'],
				"billingAddr" => array(
					"name"          => self::$aSubmitInfo['card_name'],
					"addrLine1"     => self::$aSubmitInfo['card_address1'],
					"city"          => self::$aSubmitInfo['card_city'],
					"country"       => self::$aSubmitInfo['card_country'],
					"email"         => self::$aSubmitInfo['card_email'],
					"phoneNumber"   => self::$aSubmitInfo['card_phone']
				),
				"lineItems" => array(
					array(
						"type"          => "product",
						"name"          => get_the_title(self::$packageID),
						"price"         => $aPackageInfo['price'],
						"startupFee"    => self::calculateAmountWasUsed() - absint($aPackageInfo['trial_price']),
						"tangible"      => "N",
						"recurrence"    => self::calculatePeriod($aPackageInfo),
						"duration"      => "Forever",
						"quantity"      => 1,
						"productId"     => self::$packageID,
						"description"   => $aPackageInfo['description']
					)
				)
			));

			if ($charge['response']['responseCode'] == 'APPROVED') {
				$aResult = array(
					'status'    => 'success',
					'msg'       => $charge
				);
				self::removeAllSession();
			}
		} catch (\Twocheckout_Error $e) {
			$aResult = array(
				'msg'    => $e->getMessage(),
				'status' => 'error'
			);
		}
		return $aResult;
	}

	private static function nonRecurringPayment(){
		session_start();
		self::getConfiguration();
		if ( empty(self::$aConfiguration) ){
			self::updatePaymentHistory();
			return array(
				'msg' => current_user_can('edit_theme_options') ? esc_html__('Please go to Wiloke Submission -> Settings and Complete your setting', 'wiloke') : esc_html__('Something went wrong', 'wiloke'),
				'status' => 'error'
			);
		}

		if ( !self::isAcceptCheckoutGateWay() ){
			return array(
				'status' => 'error',
				'msg'    => esc_html__('You do not permission to access this page.', 'wiloke')
			);
		}

		self::setConfiguration();
		self::$aUserInfo = \Wiloke::getUserMeta(get_current_user_id());

		$aResult = array(
			'status' => 'error',
			'msg'    => esc_html__('You have not configured 2Checkout yet.', 'wiloke')
		);

		if ( get_post_status(self::$packageID) !== 'publish' ){
			$aResult = array(
				'status' => 'error',
				'msg'    => esc_html__('The package does not exist', 'wiloke')
			);

			return $aResult;
		}
		self::$paymentType = self::$aPaymentType[0];
		$aPackageInfo = \Wiloke::getPostMetaCaching(self::$packageID, 'pricing_settings');

		try {
			$charge = \Twocheckout_Charge::auth(array(
				"merchantOrderId" => self::$paymentID,
				"token"      => self::$token,
				"currency"   => self::$aConfiguration['currency_code'],
				"billingAddr" => array(
					"name"          => self::$aSubmitInfo['card_name'],
					"addrLine1"     => self::$aSubmitInfo['card_address1'],
					"city"          => self::$aSubmitInfo['card_city'],
					"country"       => self::$aSubmitInfo['card_country'],
					"email"         => self::$aSubmitInfo['card_email'],
					"phoneNumber"   => self::$aSubmitInfo['card_phone']
				),
				"total" => $aPackageInfo['price'],
				"startupFee" => self::calculateAmountWasUsed() - absint($aPackageInfo['trial_price'])
			));

			if ($charge['response']['responseCode'] == 'APPROVED') {
				$aResult = array(
					'status' => 'success',
					'msg' => $charge
				);
				self::removeAllSession();
			}
		} catch (\Twocheckout_Error $e) {
			$aResult = array(
				'msg'    => $e->getMessage(),
				'status' => 'error'
			);
		}
		return $aResult;
	}

	public static function payWithTwoCheckout($paymentID, $price, $token){
		if ( empty($token) ){
			return array(
				'msg' => esc_html__('A token is required', 'wiloke'),
				'status' => 'error'
			);
		}

		if ( empty($price) ){
			return array(
				'msg' => esc_html__('The price must be bigger than 0', 'wiloke'),
				'status' => 'error'
			);
		}

		self::getConfiguration();

		if ( empty(self::$aConfiguration) ){
			return array(
				'msg' => current_user_can('edit_theme_options') ? esc_html__('Please go to Wiloke Submission -> Settings and Complete your setting', 'wiloke') : esc_html__('2Checkout Error: Please contact the administrator to report this bug.', 'wiloke'),
				'status' => 'error',

			);
		}

		if ( !self::isAcceptCheckoutGateWay() ){
			return array(
				'status' => 'error',
				'msg'    =>  esc_html__('You do not permission to access this page.', 'wiloke')
			);
		}

		self::setConfiguration();

		$aResult = array(
			'status' => 'error',
			'msg'    =>  esc_html__('You do not permission to access this page.', 'wiloke')
		);
		self::$aSubmitInfo  = WilokeUser::getCard();

		try {
			$charge = \Twocheckout_Charge::auth(array(
				'merchantOrderId' => $paymentID,
				'token'      => $token,
				'currency'   => self::$aConfiguration['currency_code'],
				'billingAddr' => array(
					'name'          => self::$aSubmitInfo['card_name'],
					'addrLine1'     => self::$aSubmitInfo['card_address1'],
					'city'          => self::$aSubmitInfo['card_city'],
					'country'       => self::$aSubmitInfo['card_country'],
					'email'         => self::$aSubmitInfo['card_email'],
					'phoneNumber'   => self::$aSubmitInfo['card_phone']
				),
				'total' => $price
			));

			if ($charge['response']['responseCode'] == 'APPROVED') {
				$aResult = array(
					'status'        => 'success',
					'information'   => $charge,
					'settings'      => self::$aSubmitInfo
				);
			}
		} catch (\Twocheckout_Error $e) {
			$aResult = array(
				'msg'    => $e->getMessage(),
				'status' => 'error',
				'error_type' => 'card_empty'
			);
		}

		return $aResult;
	}

	private static function convertStatusLikePayPalStatus(){
		switch (self::$profileStatus){
			case 'RECURRING_COMPLETE':
			case 'RECURRING_RESTARTED':
			case 'ORDER_CREATED':
			case 'RECURRING_INSTALLMENT_SUCCESS':
				self::$profileStatus = self::$activeProfileKey;
				break;
			case 'RECURRING_STOPPED':
				self::$profileStatus = self::$suspendedProfile;
				break;
			case 'RECURRING_INSTALLMENT_FAILED':
				self::$profileStatus = self::$cancelledProfile;
				break;
		}
	}

	/*
	 * Insert Payment History After Clicked Proceed Payment with 2 Checkout
	 * @since 1.1.3
	 */
	private static function insertPaymentHistory(){
		session_start();
		WilokePayment::insertPaymentHistory(self::$token, self::$method);
		self::$paymentID = WilokePayment::$latestPaymentID;
	}

	private static function validateNotification(){
		self::getConfiguration();
		$secretWord = self::$aConfiguration['2checkout_secret_word'];
		$stringToHash = strtoupper(md5(self::$aInsMessage['sale_id'] . self::$myID . self::$aInsMessage['invoice_id'] . $secretWord));

		return ($stringToHash == self::$aInsMessage['md5_hash']);
	}

	private static function getMyID(){
		if ( self::$aConfiguration['mode'] != 'sandbox' ){
			self::$myID = self::$aConfiguration['2co_live_seller_id'];
		}else{
			self::$myID = self::$aConfiguration['2co_sandbox_seller_id'];
		}
	}

	public function listenInstantNotification(){
		if ( !isset($_REQUEST['wiloke-submission-listener']) || ($_REQUEST['wiloke-submission-listener'] !== '2Checkout') ){
			return false;
		}

		if ( !isset($_POST['message_type']) || empty($_POST['message_type']) ){
			return false;
		}

		foreach ($_POST as $k => $v) {
			self::$aInsMessage[$k] = $v;
		}

		self::getConfiguration();
		self::getMyID();

		if ( !self::validateNotification() ){
			return false;
		}

		self::$profileStatus = $_POST['message_type'];
		self::convertStatusLikePayPalStatus();
		self::$aPaymentInfo = $_POST;
		self::$saleID = $_POST['sale_id'];
		self::$token = self::getTokenBySaleID();

		if ( self::$profileStatus == self::$activeProfileKey ){
			self::activeRecurringPayment(self::$saleID);
		}
		self::updatePaymentHistory();

		## Ignore handle
//		do_action('wiloke/wiloke-listgo-functionality/changedPlan', array('2checkout'));
	}

	public static function getDetailSale($aParams){
		if ( !class_exists('\Twocheckout_Sale') ){
			require_once plugin_dir_path(__FILE__) . '../Lib/2Checkout/lib/Twocheckout.php';
			self::getConfiguration();
			self::setConfiguration();
		}
		$aSale = null;

		try{
			$aSale = \Twocheckout_Sale::retrieve($aParams);
			if ( empty($aSale) ||  ($aSale['sale']['sale_id'] != $aParams['sale_id']) ){
				$aSale = null;
			}
		} catch (\Twocheckout_Error $e) {

		}

		return $aSale;
	}

	protected static function activeRecurringPayment($saleID){
		self::getConfiguration();
		self::setConfiguration();
		\Twocheckout_Sale::active(array(
			'sale_id' => $saleID
		));
	}

	private static function getMyPaymentInfo($paymentID){
		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$aInfo = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $historyTbl WHERE ID=%d",
				$paymentID
			),
			ARRAY_A
		);

		return $aInfo;
	}

	private static function getProfileStatus(){
		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$status = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT profile_status FROM $historyTbl WHERE ID=%d",
				self::$aUserPaymentInfo['paymentID']
			)
		);

		return $status;
	}

	private static function passedReactivateConditional($aPackageInfo, $oPreviousTransaction){
		if ( absint($aPackageInfo['price']) !== absint($oPreviousTransaction['total']) ){
			return false;
		}
		return true;
	}

	public static function ensureThatItIsNewPlan(){
		if ( (absint(self::$packageID) === absint(self::$aUserPaymentInfo['packageID'])) && (self::getProfileStatus() === self::$activeProfileKey) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Knock ... Knock ... You are really using this plan!', 'wiloke')
				)
			);
		}
	}

	public static function ensureTooTransactionIsNotTooClose(){
		if ( isset(self::$aUserPaymentInfo['profileID']) && !empty(self::$aUserPaymentInfo['profileID']) && (strtolower(self::$aUserPaymentInfo['gateWay']) == '2checkout') ){
			global $wpdb;
			$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
			$updatedAt = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT updated_at FROM $historyTbl WHERE ID=%d",
					self::$aUserPaymentInfo['paymentID']
				)
			);

			$now = date_i18n(DATE_ATOM);
			$now = strtotime($now);
			$updatedAt = strtotime($updatedAt);
			$difference = round(abs($now - $updatedAt) / 60,2);

			if ( $difference < 10 ){
				wp_send_json_error(
					array(
						'msg' => esc_html__('Transaction refused because the time of the update is too close to the preview update.', 'wiloke')
					)
				);
			}

		}
	}

	public function changePlan(){
		session_start();

		global $wiloke;
		if ( !isset($_POST['security']) || (!check_ajax_referer('wiloke-nonce', 'security', false)) || !isset($_POST['packageID']) || empty($_POST['packageID']) ){
			wp_send_json_error(
				array(
					'msg' => $wiloke->aConfigs['translation']['deniedaccess']
				)
			);
		}

		self::$aSubmitInfo  = WilokeUser::getCard();

		if ( empty(self::$aSubmitInfo['card_name']) ){
			self::$aSubmitInfo = apply_filters('wiloke/wiloke-listgo-functionality/app/frontend/FrontendTwoCheckout/cardInfo', self::$aSubmitInfo);
		}

		self::$packageID    = trim($_POST['packageID']);
		self::$token        = trim($_POST['token']);
		\Wiloke::setSession(WilokePayment::$packageIDSessionKey, self::$packageID);

		if ( get_post_status(self::$packageID) !== 'publish' ){
			wp_send_json_error(
				array(
					'msg' => $wiloke->aConfigs['translation']['deniedaccess']
				)
			);
		}

		require_once plugin_dir_path(__FILE__) . '../Lib/2Checkout/lib/Twocheckout.php';

		$aPackageInfo = \Wiloke::getPostMetaCaching(self::$packageID, 'pricing_settings');
		self::$aUserPaymentInfo = WilokeCustomerPlan::getCustomerPlan(true);
		$aCurrentPackageInfo = \Wiloke::getPostMetaCaching(self::$aUserPaymentInfo['packageID'], 'pricing_settings');

		if ( empty($aPackageInfo['price']) ){
			if ( !empty(self::$aUserPaymentInfo)  ){
				if ( (absint($aCurrentPackageInfo['price']) > 0) ){
					wp_send_json_error(
						array(
							'msg' => esc_html__('You can not downgrade from Premium Plan to Free Plan', 'wiloke')
						)
					);
				}

				$isExceededPlan = WilokeFreePost::isExceededFreePlan(self::$packageID, $aPackageInfo);
				if ( $isExceededPlan ){
					wp_send_json_error(
						array(
							'msg' => esc_html__('You have exceeded the number of listings for this plan.', 'wiloke')
						)
					);
				}else{
					if ( !WilokeFreePost::isPackageExists(self::$packageID, true) ){
						WilokeFreePost::insertPaymentHistory(self::$packageID);
					}
				}
			}else{
				if ( !WilokeFreePost::isPackageExists(self::$packageID, true) ){
					WilokeFreePost::insertPaymentHistory(self::$packageID);
				}
			}

			wp_send_json_success(
				array(
					'msg' => esc_html__('Congratulation! Your new plan has been updated.', 'wiloke')
				)
			);
		}else{
			if ( !empty(self::$aUserPaymentInfo)  ){
				$currentPaymentGateWay = strtolower(self::$aUserPaymentInfo['gateWay']);
				if ( $currentPaymentGateWay == 'paypal' ){
					WilokePayPal::setUserPaymentInfo(self::$aUserPaymentInfo);
					WilokePayPal::setPackageID(self::$packageID);
					WilokePayPal::checkOutStandingAmount();
					WilokePayPal::ensureTooTransactionIsNotTooClose();
					WilokePayPal::ensureThatItIsNewPlan();
				}elseif($currentPaymentGateWay=='2checkout'){
					self::ensureTooTransactionIsNotTooClose();
					self::ensureThatItIsNewPlan();
				}
			}
			if ( empty($aPackageInfo['number_of_posts']) || (WilokePayment::getBillingType() == 'none') ){
				self::insertPaymentHistory();
				$aResult = self::nonRecurringPayment();
			}else{
				self::insertPaymentHistory();
				$aResult = self::recurringPayment();
			}

			if ( $aResult['status'] == 'error' ){
				self::$paymentStatus          = 'Failed';
				self::$profileStatus          = 'CancelledProfile';
				self::$aPaymentInfo['reason'] = $aResult['msg'];
				self::updatePaymentHistory();

				wp_send_json_error($aResult);
			}else{
				self::$paymentStatus = 'Success';
				self::$profileStatus = self::$activeProfileKey;
				self::$aPaymentInfo  = $aResult['msg'];
				self::$saleID        = $aResult['msg']['response']['orderNumber'];
				self::updatePaymentHistory();

				WilokeCustomerPlan::setCustomerPlan(
					array(
						'profileID' => self::$saleID,
						'packageID' => self::$packageID,
						'paymentID' => self::getPaymentIDByToken(),
						'paymentToken' => self::$token,
						'gateWay'   => '2checkout'
					),
					self::$paymentType
				);

//				self::stopAllRecurringPaymentsExceptCurrentPlan();
				do_action('wiloke/wiloke-listgo-functionality/changedPlan');
				wp_send_json_success(
					array(
						'msg'    => esc_html__('Congratulation! Your payment has been successfully.', 'wiloke'),
						'status' => 'reload'
					)
				);
			}

		}
	}

	public static function getRecurringLineItems($saleDetail){
		$i = 0;
		$invoiceData = array();

		while (isset($saleDetail['sale']['invoices'][$i])) {
			$invoiceData[$i] = $saleDetail['sale']['invoices'][$i];
			$i++;
		}

		$invoice = max($invoiceData);
		$i = 0;
		$lineitemData = array();

		while (isset($invoice['lineitems'][$i])) {
			if ($invoice['lineitems'][$i]['billing']['recurring_status'] == 'active') {
				$lineitemData[] = $invoice['lineitems'][$i];
			}
			$i++;
		};

		return $lineitemData;
	}

	private static function convertRecurrenceToDay($data){
		$data = strtolower($data);
		$getDay = explode(' ', $data);
		$getDay = $getDay[0];

		if ( strpos($data, 'year') !== false ){
			$hours = $getDay*365;
		}elseif ( strpos($data, 'month') !== false ){
			$hours = $getDay*30;
		}else{
			$hours = $getDay*7;
		}

		return $hours;
	}

	private static function insertRefundToPaymentHistory($paymentID, $aInformation){
		// Only insert payment if it does not exist
		if ( !empty($packageID) ) {
			global $wpdb;
			$tblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

			$aPaymentInfo = $wpdb->get_row(
				$wpdb->get_row(
					"SELECT * FROM $tblName WHERE ID=%d",
					$paymentID
				),
				ARRAY_A
			);

			if ( empty($aPaymentInfo) ){
				return false;
			}

			$wpdb->insert(
				$tblName,
				array(
					'user_ID'     => $aPaymentInfo['user_ID'],
					'package_ID'  => $aPaymentInfo['package_ID'],
					'package_type'=> get_post_field('post_type', $aPaymentInfo['package_ID']),
					'token'       => $aPaymentInfo['token'],
					'method'      => self::$method,
					'information' => json_encode($aInformation),
					'total'       => $aPaymentInfo['total'],
					'currency'    => $aPaymentInfo['currency'],
					'status'      => self::$refundedStatus
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
		}
	}

	/*
	 * This is very important function
	 * For example: Your customer purchased package A - 10$, Now they want to change their plan
	 * #1. We will calculate the number of day package A used
	 * #2. We plus AmountOfA/NumberOfDay + Current Package price
	 * #3. We refund the money of Package A
	 */
	public static function calculateAmountWasUsed(){
		self::getConfiguration();

		if ( empty(self::$aUserPaymentInfo) ){
			return 0;
		}

		if ( self::$aUserPaymentInfo['gateWay'] != '2checkout' ){
			return 0;
		}

		if ( !isset(self::$aConfiguration['2checkout_auto_refund']) || (self::$aConfiguration['2checkout_auto_refund'] == 'disable') ){
			return 0;
		}

		$aDetailSale = self::getDetailSale(
			array(
				'sale_id' => self::$aUserPaymentInfo['profileID']
			)
		);

		if ( !isset(self::$aConfiguration['2checkout_auto_refund']) || self::$aConfiguration['2checkout_auto_refund'] == 'disable' ){
			return 0;
		}

		if ( empty($aDetailSale) ){
			return 0;
		}


		if ( !isset($aDetailSale['sale']['invoices']) ){
			return 0;
		}

		$now  = current_time( 'timestamp', true );
		$amountSpent = 0;
		$aRefundInfo = array();

		$i = 0;
		$invoiceData = array();
		while (isset($aDetailSale['sale']['invoices'][$i])) {
			$invoiceData[$i] = $aDetailSale['sale']['invoices'][$i];
			$i++;
		}
		$invoice = max($invoiceData);
		$i = 0;

		while (isset($invoice['lineitems'][$i])) {
			if ($invoice['lineitems'][$i]['billing']['recurring_status'] == 'active') {
				$aLineItemInfo = $invoice['lineitems'][$i];

				$nextBillingToTimeStamp = strtotime($aLineItemInfo['billing']['date_next']);
				$frequency = self::convertRecurrenceToDay($aLineItemInfo['product_recurrence'])*24;
				$difference = round(abs($nextBillingToTimeStamp - $now) / 3600);
				$hoursUsed = ceil($frequency - $difference);

				$amountSpent += ceil((absint($aLineItemInfo['billing']['amount'])/$frequency)*$hoursUsed);

				$aRefundInfo[$aLineItemInfo['lineitem_id']] = array(
					'amount'    => $aLineItemInfo['billing']['amount'],
					'profileID' => self::$aUserPaymentInfo['profileID'],
					'paymentID' => self::$aUserPaymentInfo['paymentID']
				);

			}
			$i++;
		};

		if ( !empty($amountSpent) ){
			set_transient(self::$userRefundDataKey.get_current_user_id(), $aRefundInfo, 60 *10);
		}

		return absint($amountSpent);
	}

	private static function refundSale(){
		self::getConfiguration();
		if ( !isset(self::$aConfiguration['2checkout_auto_refund']) || self::$aConfiguration['2checkout_auto_refund'] == 'disable' ){
			return false;
		}

		$userID = get_current_user_id();
		$aRefundLineItems = get_transient(self::$userRefundDataKey.$userID);
		delete_transient(self::$userRefundDataKey.$userID);
		if ( !empty($aRefundLineItems) ){
			self::getConfiguration();
			self::setConfiguration();
			$insEmail = new WilokeSendMail();
			foreach ( $aRefundLineItems as $liteItem => $aInfo ){
				try {
					$aResult = \Twocheckout_Sale::refund(
						array(
							'lineitem_id'   => $liteItem,
							'category'      => 10,
							'comment'       => esc_html__('Changed to another plan', 'wiloke')
						)
					);
					if ( $aResult['response_code'] == 'OK' ){
						self::updatePaymentHistoryStatus($aInfo['paymentID'], self::$refundedStatus, self::$cancelledProfile);
						$insEmail->refund(get_current_user_id(), array('amount'=>$aInfo['amount']));
					}
				} catch (\Twocheckout_Error $e) {
				}
			}
		}
	}

	private static function stopRecurringPayment($saleID, $paymentID){
		self::getConfiguration();
		self::setConfiguration();

		try {
			\Twocheckout_Sale::stop(array(
				'sale_id' => $saleID
			));
			self::updatePaymentHistoryStatus($paymentID, 'Voided', self::$cancelledProfile);
		} catch (\Twocheckout_Error $e) {

		}
	}

	private static function stopAllRecurringPaymentsExceptCurrentPlan(){
		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$aListOfTwoCheckout = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $historyTbl WHERE profile_status=%s AND method=%s AND user_ID=%d",
				self::$activeProfileKey, '2checkout', get_current_user_id()
			),
			ARRAY_A
		);

		if ( !empty($aListOfTwoCheckout) ){
			$aUserPaymentInfo = WilokeCustomerPlan::getCustomerPlan(true);
			if ( empty($aUserPaymentInfo) ){
				return false;
			}

			foreach ( $aListOfTwoCheckout as $aPayment ){
				if ( $aUserPaymentInfo['profileID'] != $aPayment['profile_ID'] ){
					self::stopRecurringPayment($aPayment['profile_ID'], $aPayment['ID']);
				}
			}
			self::refundSale();
		}
	}

	public function afterChangedPlan($aIgnore=null){
		if ( !empty($aIgnore) && in_array(self::$method, $aIgnore) ){
			return false;
		}

		self::stopAllRecurringPaymentsExceptCurrentPlan();
	}
}