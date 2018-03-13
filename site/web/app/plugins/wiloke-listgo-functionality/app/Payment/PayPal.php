<?php
/**
 * Handle PayPal Payment
 * @since 1.0
 * @author Wiloke
 * @package Wiloke/Themes
 * @subpackage Listgo
 * @link https://wiloke.com
 */
namespace WilokeListGoFunctionality\Payment;


use WilokeListGoFunctionality\AlterTable\AlterTablePaymentHistory;
use WilokeListGoFunctionality\CustomerPlan\CustomerPlan;
use WilokeListGoFunctionality\Payment\Payment as WilokePayment;
use WilokeListGoFunctionality\Submit\User as WilokeUser;
use WilokeListGoFunctionality\Payment\TwoCheckout as WilokeTwoCheckout;

use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\EBLBaseComponents\AddressType;
use PayPal\EBLBaseComponents\BillingAgreementDetailsType;
use PayPal\EBLBaseComponents\PaymentDetailsItemType;
use PayPal\EBLBaseComponents\PaymentDetailsType;
use PayPal\EBLBaseComponents\SetExpressCheckoutRequestDetailsType;
use PayPal\PayPalAPI\SetExpressCheckoutReq;
use PayPal\PayPalAPI\SetExpressCheckoutRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use WilokeListGoFunctionality\Register\RegisterPricingSettings;
use WilokeListGoFunctionality\Register\RegisterWilokeSubmission;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsReq;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsRequestType;
use PayPal\EBLBaseComponents\DoExpressCheckoutPaymentRequestDetailsType;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentReq;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentRequestType;

# Recurring Payments
use PayPal\EBLBaseComponents\ActivationDetailsType;
use PayPal\EBLBaseComponents\BillingPeriodDetailsType;
use PayPal\EBLBaseComponents\CreateRecurringPaymentsProfileRequestDetailsType;
use PayPal\EBLBaseComponents\CreditCardDetailsType;
use PayPal\EBLBaseComponents\RecurringPaymentsProfileDetailsType;
use PayPal\EBLBaseComponents\ScheduleDetailsType;
use PayPal\PayPalAPI\CreateRecurringPaymentsProfileReq;
use PayPal\PayPalAPI\CreateRecurringPaymentsProfileRequestType;
use WilokeListGoFunctionality\AlterTable\AlterTablePayPalErrorLog;

use PayPal\Core\PPLoggingManager;
use PayPal\Exception\PPConfigurationException;
use PayPal\Exception\PPConnectionException;

use PayPal\PayPalAPI\GetRecurringPaymentsProfileDetailsReq;
use PayPal\PayPalAPI\GetRecurringPaymentsProfileDetailsRequestType;

use PayPal\EBLBaseComponents\UpdateRecurringPaymentsProfileRequestDetailsType;
use PayPal\PayPalAPI\UpdateRecurringPaymentsProfileReq;
use PayPal\PayPalAPI\UpdateRecurringPaymentsProfileRequestType;
use PayPal\Exception\PPMissingCredentialException;
use PayPal\Exception\PPInvalidCredentialException;

use PayPal\EBLBaseComponents\ManageRecurringPaymentsProfileStatusRequestDetailsType;
use PayPal\PayPalAPI\ManageRecurringPaymentsProfileStatusReq;
use PayPal\PayPalAPI\ManageRecurringPaymentsProfileStatusRequestType;

use WilokeListGoFunctionality\Payment\FreePost as WilokeFreePost;

use PayPal\EBLBaseComponents\BillOutstandingAmountRequestDetailsType;
use PayPal\PayPalAPI\BillOutstandingAmountReq;
use PayPal\PayPalAPI\BillOutstandingAmountRequestType;

final class PayPal{
	protected static $_aConfigs = array();
	protected static $_aPayPalConfiguration = array();
	protected static $billingAgreementText = 'Iagree';
	public static $tokenSessionKey = 'wiloke_listgo_trade_token';
	private static $billingPeriod = 'Day';
	private static $totalBillingCycles = 0;
	private static $autoBillOutstandingAmount = 'AddToNextBilling';
	private static $aUserPaymentInfo = array();
	private static $packageID = null;
	private static $thankyouUrl = null;
	private static $suspendKey = 'Suspend';
	private static $cancelKey = 'Cancel';
	private static $failedKey = 'Failed';
	private static $reactivateKey = 'Reactivate';
	private static $activeProfileKey = 'ActiveProfile';
	private static $pendingProfileKey = 'PendingProfile';
	private static $expiredProfileKey = 'ExpiredProfile';
	private static $suspendedProfile = 'SuspendedProfile';
	private static $cancelledProfile = 'CancelledProfile';
	private static $listCustomerPlanNeedToCheckKey = 'wiloke_submission_list_of_customer_need_to_check';
	private static $aPaymentType = array('NonRecurring', 'RecurringPayPal');

	public function __construct() {
		add_action('init', array($this, 'updatePayPalHistory'), 1);
		add_action('wp_ajax_wiloke_credit_card_with_paypal', array($this, 'proceedCreditCardWithPayPal'));
		add_action('wp_ajax_wiloke_submission_change_plan_with_paypal', array($this, 'changePlan'));
		add_action('wp_ajax_wiloke_paypal_bill_outstanding_amount', array($this, 'billingOutstandingAmount'));
		add_action('wiloke/wiloke-listgo-functionality/changedPlan', array($this, 'afterChangedPlan'));
	}

	public static function setUserPaymentInfo($aUserPaymentInfo){
		self::$aUserPaymentInfo = $aUserPaymentInfo;
	}

	public static function setPackageID($packageID){
		self::$packageID = $packageID;
	}

	## This function is very important. An user change their plan might keep more than 1 subscription. We need to daily
	## check to make sure that there are no more activate in an account
	public static function dailyCheckCustomerPlan(){
		wp_schedule_event( time(), 'daily', 'wiloke_submssion_paypal_daily_check' );
	}

	private static function getListCustomersNeedToCheck(){
		$aListCustomers = get_option(self::$listCustomerPlanNeedToCheckKey);
		return !empty($aListCustomers) ? $aListCustomers : array();
	}

	private static function setListCustomersNeedToCheck($customerID){
		$aListCustomers = self::getListCustomersNeedToCheck();
		if ( empty($aListCustomers) || !in_array($customerID, $aListCustomers) ){
			$aListCustomers[] = $customerID;
			update_option(self::$listCustomerPlanNeedToCheckKey, $aListCustomers);
		}
		return false;
	}

	private function isPayPalGateWay($token){
		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$gateway = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT method FROM $historyTbl WHERE token=%s AND package_type=%s",
				$token, 'pricing'
			)
		);

		return (strtolower($gateway) == 'paypal');
	}

	public function updatePayPalHistory() {
		if ( !isset($_REQUEST['payment_method']) || ($_REQUEST['payment_method'] !== 'paypal') ){
			return false;
		}

		if ( isset($_REQUEST['wiloke_mode']) && ($_REQUEST['wiloke_mode'] === 'remaining') ) {
			return false;
		}

		if ( !isset($_REQUEST['token']) || empty($_REQUEST['token']) ){
			return false;
		}

		if ( empty(self::$_aPayPalConfiguration) ){
			self::_getPayPalConfiguration();
		}

		$token = $_REQUEST['token'];
		if ( !$this->isPayPalGateWay($token) ){
			return false;
		}

		$aCustomerPlan = CustomerPlan::getCustomerPlan(true);
//
		if ( isset($aCustomerPlan['paymentToken']) && ($aCustomerPlan['paymentToken'] === $token) ){
			return false;
		}

		$getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($token);

		$getExpressCheckoutReq = new GetExpressCheckoutDetailsReq();
		$getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

		/*
		 * 	 ## Creating service wrapper object
		Creating service wrapper object to make API call and loading
		Configuration::getAcctAndConfig() returns array that contains credential and config parameters
		*/
		$paypalService = new PayPalAPIInterfaceServiceService(self::$_aPayPalConfiguration);
		$getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);
		if ( ($getECResponse->Ack === 'Success') && !empty(\Wiloke::getSession(self::$tokenSessionKey)) ){
			$aPackageInfo = \Wiloke::getPostMetaCaching(\Wiloke::getSession(WilokePayment::$packageIDSessionKey), 'pricing_settings');
			WilokePayment::updatePaymentHistory('pending', $getECResponse->GetExpressCheckoutDetailsResponseDetails->Token, $getECResponse);
			$aData = array(
				'token'     => $getECResponse->GetExpressCheckoutDetailsResponseDetails->Token,
				'payerID'   => $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerID,
				'amount'    => $getECResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails[0]->OrderTotal->value
			);
			if ( empty($aPackageInfo) ){
				return false;
			}
			## Do Express Check out if you are using no-recurring or it's a Forever package
			if ( (self::getBillingType() == 'None') || !isset($aPackageInfo['regular_period']) || empty($aPackageInfo['regular_period']) ){
				self::_doExpressCheckout($aData);
			}else{
				self::createRecurringPayments($getECResponse);
			}
		}

		self::removeSession();
	}

	protected static function _getPayPalConfiguration(){
		if ( !empty(self::$_aConfigs) ){
			return false;
		}
		self::$_aConfigs = \WilokePublic::getPaymentField();

		if ( empty(self::$_aConfigs) ){
			if ( current_user_can('administrator') ){
				wp_die( esc_html__('Payment could not process because you have not configure your PayPal. Please go to Pricing -> Settings to complete it', 'wiloke') );
			}else{
				wp_die( esc_html__('OOps! Something went wrong. Please report this issue to ', 'wiloke') . get_option('admin_email') );
			}
		}

		self::_payPalConfiguration();
	}

	protected static function _payPalConfiguration(){
		if ( empty(self::$_aConfigs) ){
			self::_getPayPalConfiguration();
		}

		$aUploadDir = wp_upload_dir();
		$wilokeDir = $aUploadDir['basedir'].'/wiloke';
		if ( !file_exists($wilokeDir) ) {
			wp_mkdir_p($wilokeDir);
		}

		self::$_aPayPalConfiguration = array(
			'mode'              => self::$_aConfigs['mode'],
			'log.LogEnabled'    => true,
			'log.FileName'      => $wilokeDir.self::$_aConfigs['logfilename'],
			'log.LogLevel'      => 'FINE'
		);

		if ( self::$_aConfigs['mode'] === 'live' ){
			$aPayPalAPI = array(
				"acct1.UserName"  => self::$_aConfigs['live_username'],
				"acct1.Password"  => self::$_aConfigs['live_password'],
				"acct1.Signature" => self::$_aConfigs['live_signature']
			);
		}else{
			$aPayPalAPI = array(
				"acct1.UserName"  => self::$_aConfigs['sandbox_username'],
				"acct1.Password"  => self::$_aConfigs['sandbox_password'],
				"acct1.Signature" => self::$_aConfigs['sandbox_signature']
			);
		}
		self::$_aPayPalConfiguration = array_merge(self::$_aPayPalConfiguration, $aPayPalAPI);
	}

	public static function getPayPalConfiguration(){
		self::_payPalConfiguration();
		return self::$_aPayPalConfiguration;
	}

	private static function renderThankyouUrl(){
		return \WilokePublic::addQueryToLink(self::$thankyouUrl, "payment_method=paypal");
	}

	private static function _setExPressCheckout(){
		$aPackageName = get_the_title(self::$packageID);
		$aPackageInfo = \Wiloke::getPostMetaCaching(self::$packageID, 'pricing_settings');
		self::_getPayPalConfiguration();
		// Now we set value before redirecting to PayPal
		$currencyCode       = self::$_aConfigs['currency_code'];
		$instPaymentDetails = new PaymentDetailsType();
		$instItemDetails    = new PaymentDetailsItemType();

		$instItemDetails->Name          = $aPackageName;

		$amount = CustomerPlan::getCurrentOutStandingAmount() + absint($aPackageInfo['price']) + WilokeTwoCheckout::calculateAmountWasUsed();
		$instItemDetails->Amount        = new BasicAmountType($currencyCode, $amount);
		$instItemDetails->Quantity      = 1;
		$instItemDetails->ItemCategory  = 'Physical';


		$instPaymentDetails->PaymentDetailsItem[0] = $instItemDetails;
		$instPaymentDetails->PaymentAction = self::getPaymentAction($aPackageInfo);
		$instPaymentDetails->OrderTotal = new BasicAmountType($currencyCode, absint($amount));

		$setECReqDetails = new SetExpressCheckoutRequestDetailsType();
		$setECReqDetails->PaymentDetails[0] = $instPaymentDetails;

		/*
		 * (Required) URL to which the buyer is returned if the buyer does not approve the use of PayPal to pay you. For digital goods, you must add JavaScript to this page to close the in-context experience.
		 */
		if ( isset(self::$_aConfigs['cancel']) && !empty(self::$_aConfigs['cancel']) ){
			self::$_aConfigs['cancel'] = get_permalink(self::$_aConfigs['cancel']);
			self::$_aConfigs['cancel'] .= strpos(self::$_aConfigs['cancel'], '?') === false ? '?payment_method=paypal' : '&payment_method=paypal';
		}else{
			self::$_aConfigs['cancel'] = home_url('/');
		}

		$setECReqDetails->CancelURL = self::$_aConfigs['cancel'];

		/*
		 * (Required) URL to which the buyer's browser is returned after choosing to pay with PayPal. For digital goods, you must add JavaScript to this page to close the in-context experience.
		 */


		$setECReqDetails->ReturnURL = self::renderThankyouUrl();
		$setECReqDetails->NoShipping = 0;

		// Billing agreement details
		$billingAgreementDetails = new BillingAgreementDetailsType(self::getBillingType());
		$setECReqDetails->BillingAgreementDetails = array($billingAgreementDetails);

		# This config is very important with recurring payment
		$billingAgreementDetails->BillingAgreementDescription = self::$billingAgreementText;

		// Display options
		$setECReqDetails->BrandName = self::$_aConfigs['brandname'];

		$setECReqType = new SetExpressCheckoutRequestType();
		$setECReqType->SetExpressCheckoutRequestDetails = $setECReqDetails;
		$setECReq = new SetExpressCheckoutReq();
		$setECReq->SetExpressCheckoutRequest = $setECReqType;
		$instPayPalService = new PayPalAPIInterfaceServiceService(self::$_aPayPalConfiguration);

		$setECResponse = $instPayPalService->SetExpressCheckout($setECReq);

		if ( isset($setECResponse->Ack) && $setECResponse->Ack === 'Success'  ){
			\Wiloke::setSession(self::$tokenSessionKey, $setECResponse->Token);
			WilokePayment::insertPaymentHistory($setECResponse->Token, 'paypal');
			return $setECResponse;
		}

		return false;
	}

	private static function getPaymentAction($aPackageInfo){
		if ( (self::getBillingType() === 'None') || !isset($aPackageInfo['regular_period']) || empty($aPackageInfo['regular_period']) ){
			return 'Sale';
		}else{
			return 'Authorization';
		}
	}

	private static function getBillingType(){
		if ( !isset(self::$_aConfigs['billing_type']) ){
			return 'None';
		}

		return trim(self::$_aConfigs['billing_type']);
	}

	public static function getPackageIDByToken($token){
		global $wpdb;
		$tbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT package_ID FROM $tbl WHERE token=%s",
				$token
			)
		);
	}

	public static function getPaymentIDByToken($token){
		global $wpdb;
		$tbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $tbl WHERE token=%s AND method=%s AND user_ID=%d",
				$token, 'paypal', get_current_user_id()
			)
		);
	}

	private static function getTransactionToken($paymentID){
		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$token = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT token FROM $historyTbl WHERE ID=%d",
				$paymentID
			)
		);

		return $token;
	}

	private static function createRecurringPayments($getECResponse){
		global $wpdb;
		self::_getPayPalConfiguration();
		$packageID = self::getPackageIDByToken($getECResponse->GetExpressCheckoutDetailsResponseDetails->Token);
		$aPackageInfo = \Wiloke::getPostMetaCaching($packageID, 'pricing_settings');

		if ( empty($aPackageInfo) ){
			self::writeErrorLog($getECResponse->GetExpressCheckoutDetailsResponseDetails->Token, array(
				'package_not_exist' => sprintf(esc_html__('The Package with ID: %s does not exist', 'wiloke'), \Wiloke::getSession(WilokePayment::$packageIDSessionKey))
			));
			return false;
		}

		$currencyCode = self::$_aConfigs['currency_code'];

		$shippingAddress = new AddressType();
		$shippingAddress->Name = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->Address->Name;
		$shippingAddress->Street1 = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->Address->Street1;
		$shippingAddress->Street2 = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->Address->Street2;
		$shippingAddress->CityName = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->Address->CityName;
		$shippingAddress->StateOrProvince = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->Address->StateOrProvince;
		$shippingAddress->PostalCode = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->Address->PostalCode;
		$shippingAddress->Country = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->Address->Country;
		$shippingAddress->Phone = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->Address->Phone;

		/*
		 *  You can include up to 10 recurring payments profiles per request. The
		order of the profile details must match the order of the billing
		agreement details specified in the SetExpressCheckout request which
		takes mandatory argument:

		* `billing start date` - The date when billing for this profile begins.
		`Note:
		The profile may take up to 24 hours for activation.`
		*/
		$RPProfileDetails = new RecurringPaymentsProfileDetailsType();
		$RPProfileDetails->SubscriberName = $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->Address->Name;

		$RPProfileDetails->BillingStartDate = date_i18n(DATE_ATOM);
		$RPProfileDetails->SubscriberShippingAddress  = $shippingAddress;
		$activationDetails = new ActivationDetailsType();

		#Caculate OutStanding Amount
		$aCurrentUserInfo = CustomerPlan::getCustomerPlan();
		WilokeTwoCheckout::setUserPaymentInfo($aCurrentUserInfo);
		$twoCheckoutAmountWasUsed = CustomerPlan::getCurrentOutStandingAmount() + WilokeTwoCheckout::calculateAmountWasUsed();
		/*
		 * (Optional) Initial non-recurring payment amount due immediately upon profile creation. Use an initial amount for enrolment or set-up fees.
		 */
		## Plus outstanding balance if it's existing
//		$amount = CustomerPlan::getCurrentOutStandingAmount() + absint($getECResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails[0]->OrderTotal->value);
//		$amount = CustomerPlan::getCurrentOutStandingAmount() + absint($getECResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails[0]->OrderTotal->value);
		$activationDetails->InitialAmount = new BasicAmountType($currencyCode, $twoCheckoutAmountWasUsed);
		/*
		 *  (Optional) Action you can specify when a payment fails. It is one of the following values:

			ContinueOnFailure – By default, PayPal suspends the pending profile in the event that the initial payment amount fails. You can override this default behavior by setting this field to ContinueOnFailure. Then, if the initial payment amount fails, PayPal adds the failed payment amount to the outstanding balance for this recurring payment profile.

			When you specify ContinueOnFailure, a success code is returned to you in the CreateRecurringPaymentsProfile response and the recurring payments profile is activated for scheduled billing immediately. You should check your IPN messages or PayPal account for updates of the payment status.

			CancelOnFailure – If this field is not set or you set it to CancelOnFailure, PayPal creates the recurring payment profile, but places it into a pending status until the initial payment completes. If the initial payment clears, PayPal notifies you by IPN that the pending profile has been activated. If the payment fails, PayPal notifies you by IPN that the pending profile has been canceled.

		 */
//		$activationDetails->FailedInitialAmountAction = 'CancelOnFailure';

		/*
		 *  Regular payment period for this schedule which takes mandatory
		params:

		* `Billing Period` - Unit for billing during this subscription period. It is one of the
		following values:
		* Day
		* Week
		* SemiMonth
		* Month
		* Year
		For SemiMonth, billing is done on the 1st and 15th of each month.
		`Note:
		The combination of BillingPeriod and BillingFrequency cannot exceed
		one year.`
		* `Billing Frequency` - Number of billing periods that make up one billing cycle.
		The combination of billing frequency and billing period must be less
		than or equal to one year. For example, if the billing cycle is
		Month, the maximum value for billing frequency is 12. Similarly, if
		the billing cycle is Week, the maximum value for billing frequency is
		52.
		`Note:
		If the billing period is SemiMonth, the billing frequency must be 1.`
		* `Billing Amount`
		*/
		$paymentBillingPeriod =  new BillingPeriodDetailsType();
		$paymentBillingPeriod->BillingFrequency = trim($aPackageInfo['regular_period']);
		$paymentBillingPeriod->BillingPeriod = self::$billingPeriod;
		$paymentBillingPeriod->TotalBillingCycles = self::$totalBillingCycles; // run forever
		$paymentBillingPeriod->Amount = new BasicAmountType($currencyCode, trim(absint($aPackageInfo['price'])));
		$paymentBillingPeriod->ShippingAmount = new BasicAmountType($currencyCode, 0);
		$paymentBillingPeriod->TaxAmount = new BasicAmountType($currencyCode, 0);

		/*
		 * 	 Describes the recurring payments schedule, including the regular
		payment period, whether there is a trial period, and the number of
		payments that can fail before a profile is suspended which takes
		mandatory params:

		* `Description` - Description of the recurring payment.
		`Note:
		You must ensure that this field matches the corresponding billing
		agreement description included in the SetExpressCheckout request.`
		* `Payment Period`
		*/
		$scheduleDetails = new ScheduleDetailsType();
		$scheduleDetails->Description = self::$billingAgreementText; // very important

		$scheduleDetails->ActivationDetails = $activationDetails;

		if( ($aPackageInfo['trial_price'] != '') && ($aPackageInfo['trial_period'] != '') && empty(self::isEverUseThisPackage($packageID)) ) {
			$trialBillingPeriod =  new BillingPeriodDetailsType();
			$trialBillingPeriod->BillingFrequency = trim($aPackageInfo['trial_period']);
			$trialBillingPeriod->BillingPeriod = 'Day';
			$trialBillingPeriod->TotalBillingCycles = 1;
			$trialBillingPeriod->Amount = new BasicAmountType($currencyCode, trim($aPackageInfo['trial_price']));
			$trialBillingPeriod->ShippingAmount = new BasicAmountType($currencyCode, 0);
			$trialBillingPeriod->TaxAmount = new BasicAmountType($currencyCode, 0);
			$scheduleDetails->TrialPeriod  = $trialBillingPeriod;
		}

		$scheduleDetails->PaymentPeriod = $paymentBillingPeriod;
		$scheduleDetails->MaxFailedPayments = isset(self::$_aConfigs['maxFailedPayments']) ? absint(self::$_aConfigs['maxFailedPayments']) : 3;
		$scheduleDetails->AutoBillOutstandingAmount = self::$autoBillOutstandingAmount;

		/*
		 * 	 `CreateRecurringPaymentsProfileRequestDetailsType` which takes
		mandatory params:

		* `Recurring Payments Profile Details`
		* `Schedule Details`
		*/
		$createRPProfileRequestDetail = new CreateRecurringPaymentsProfileRequestDetailsType();
		if(trim($getECResponse->GetExpressCheckoutDetailsResponseDetails->Token) != '') {
			$createRPProfileRequestDetail->Token  = $getECResponse->GetExpressCheckoutDetailsResponseDetails->Token;
		} else {

			/*
			 * 	 Either EC token or a credit card number is required.If you include
			both token and credit card number, the token is used and credit card number is
			ignored
			In case of setting EC token,
			`createRecurringPaymentsProfileRequestDetails.setToken("EC-5KH01765D1724703R");`
			A timestamped token, the value of which was returned in the response
			to the first call to SetExpressCheckout. Call
			CreateRecurringPaymentsProfile once for each billing
			agreement included in SetExpressCheckout request and use the same
			token for each call. Each CreateRecurringPaymentsProfile request
			creates a single recurring payments profile.
			`Note:
			Tokens expire after approximately 3 hours.`

			Credit card information for recurring payments using direct payments.
			*/
			$creditCard = new CreditCardDetailsType();
			$creditCard->CreditCardNumber = $_REQUEST['creditCardNumber'];

			/*
			 *  Type of credit card. For UK, only Maestro, MasterCard, Discover, and
			Visa are allowable. For Canada, only MasterCard and Visa are
			allowable and Interac debit cards are not supported. It is one of the
			following values:

			* Visa
			* MasterCard
			* Discover
			* Amex
			* Solo
			* Switch
			* Maestro: See note.
			`Note:
			If the credit card type is Maestro, you must set currencyId to GBP.
			In addition, you must specify either StartMonth and StartYear or
			IssueNumber.`
			*/
			$creditCard->CreditCardType = $_REQUEST['creditCardType'];
			$creditCard->CVV2 = $_REQUEST['cvv'];
			$creditCard->ExpMonth = $_REQUEST['expMonth'];
			$creditCard->ExpYear = $_REQUEST['expYear'];
			$createRPProfileRequestDetail->CreditCard = $creditCard;
		}
		$createRPProfileRequestDetail->ScheduleDetails = $scheduleDetails;
		$createRPProfileRequestDetail->RecurringPaymentsProfileDetails = $RPProfileDetails;
		$createRPProfileRequest = new CreateRecurringPaymentsProfileRequestType();
		$createRPProfileRequest->CreateRecurringPaymentsProfileRequestDetails = $createRPProfileRequestDetail;


		$createRPProfileReq =  new CreateRecurringPaymentsProfileReq();
		$createRPProfileReq->CreateRecurringPaymentsProfileRequest = $createRPProfileRequest;

		/*
		 *  ## Creating service wrapper object
		Creating service wrapper object to make API call and loading
		Configuration::getAcctAndConfig() returns array that contains credential and config parameters
		*/
		$paypalService = new PayPalAPIInterfaceServiceService(self::$_aPayPalConfiguration);
		try {
			/* wrap API method calls on the service object with a try catch */
			$createRPProfileResponse = $paypalService->CreateRecurringPaymentsProfile($createRPProfileReq);
		} catch (\Exception $ex) {
			$errMsg = self::getExceptionMessage($ex);
			self::error($ex, $getECResponse->GetExpressCheckoutDetailsResponseDetails->Token);
		}

		if ( isset($errMsg) ){
			return $errMsg;
		}

		if(isset($createRPProfileResponse)) {
			$paymentHistoryTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
			$paymentID = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT ID FROM $paymentHistoryTbl WHERE token=%s",
					$getECResponse->GetExpressCheckoutDetailsResponseDetails->Token
				)
			);

			WilokePayment::updatePaymentHistory($createRPProfileResponse->Ack, $getECResponse->GetExpressCheckoutDetailsResponseDetails->Token, $createRPProfileResponse, $createRPProfileResponse->CreateRecurringPaymentsProfileResponseDetails->ProfileStatus, $createRPProfileResponse->CreateRecurringPaymentsProfileResponseDetails->ProfileID);

			if ( $createRPProfileResponse->Ack === 'Success' ){
				CustomerPlan::setCustomerPlan(
					array(
						'profileID'=>$createRPProfileResponse->CreateRecurringPaymentsProfileResponseDetails->ProfileID,
						'packageID'=>$packageID,
						'paymentID'=>$paymentID,
						'paymentToken'=>$getECResponse->GetExpressCheckoutDetailsResponseDetails->Token,
						'gateWay'  => 'paypal'
					),
					self::$aPaymentType[1]
				);
				do_action('wiloke/wiloke-listgo-functionality/changedPlan');
			}else{
				return $createRPProfileResponse->Errors[0]->LongMessage;
			}

			return true;
		}
		return esc_html__('Something went wrong', 'wiloke');
	}

	public static function getExpressCheckout($token){
		self::_getPayPalConfiguration();
		$getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($token);

		$getExpressCheckoutReq = new GetExpressCheckoutDetailsReq();
		$getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

		/*
		 * 	 ## Creating service wrapper object
		Creating service wrapper object to make API call and loading
		Configuration::getAcctAndConfig() returns array that contains credential and config parameters
		*/
		$paypalService = new PayPalAPIInterfaceServiceService(self::$_aPayPalConfiguration);
		try {
			/* wrap API method calls on the service object with a try catch */
			$getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);
		} catch (\Exception $ex) {
			$aMessage = self::error($ex, $token);
		}

		if ( isset($aMessage) ){
			return array(
				'status' => 'error',
				'msg'    => $aMessage['message_detail']
			);
		}

		if(isset($getECResponse)) {
			if ( $getECResponse->Ack === 'Success' ){
				return array(
					'status' => 'success'
				);
			}
			return array(
				'status' => 'error',
				'msg'    => $getECResponse->GetExpressCheckoutDetailsResponseDetails->Errors[0]->LongMessage
			);
		}
	}

	public static function isEverUseThisPackage($packageID){
		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $historyTbl WHERE package_ID=%d AND user_ID=%d",
				$packageID, get_current_user_id()
			)
		);
	}

	public static function getExceptionMessage($ex){
		$ex_message = $ex->getMessage();
		$ex_detailed_message = '';
		if($ex instanceof PPConnectionException) {
			$ex_detailed_message = esc_html__('Error connecting to ', 'wiloke') . $ex->getUrl();
		} else if($ex instanceof PPMissingCredentialException || $ex instanceof PPInvalidCredentialException) {
			$ex_detailed_message = $ex->errorMessage();
		} else if($ex instanceof PPConfigurationException) {
			$ex_detailed_message = esc_html__('Invalid configuration. Please check your configuration file', 'wiloke');
		}

		return esc_html__('Message:', 'wiloke') . $ex_message . esc_html__('. Detailed Message:', 'wiloke') . $ex_detailed_message;
	}

	public function billingOutstandingAmount(){
		if ( !isset($_POST['security']) || (!check_ajax_referer('wiloke-nonce', 'security', false)) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Wrong security code', 'wiloke')
				)
			);
		}

		self::getPayPalConfiguration();

		$aCustomerPlan = CustomerPlan::getCustomerPlan(true);

		if ( empty($aCustomerPlan) || !isset($aCustomerPlan['profileID']) || empty($aCustomerPlan['profileID']) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Outstanding balance must be > 0', 'wiloke')
				)
			);
		}

		$aProfileInfo = self::getRecurringPaymentProfileDetails($aCustomerPlan['profileID']);

		if ( $aProfileInfo['status'] === 'error' ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Outstanding balance must be > 0', 'wiloke')
				)
			);
		}

		$amount = !empty($aProfileInfo['msg']->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsSummary->OutstandingBalance) ? $aProfileInfo['msg']->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsSummary->OutstandingBalance->value : 0;
		if ( empty($amount) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Outstanding balance must be > 0', 'wiloke')
				)
			);
		}

		$billOutstandingAmtReqestDetail = new BillOutstandingAmountRequestDetailsType();

		/*
		 * (Optional) The amount to bill. The amount must be less than or equal to the current outstanding balance of the profile. If no value is specified, PayPal attempts to bill the entire outstanding balance amount.
		 */
		$billOutstandingAmtReqestDetail->Amount = new BasicAmountType(self::$_aConfigs['currency_code'], $amount);

		/*
		 *  (Required) Recurring payments profile ID returned in the CreateRecurringPaymentsProfile response.
		Note:The profile must have a status of either Active or Suspended.
		 */
		$billOutstandingAmtReqestDetail->ProfileID = $aCustomerPlan['profileID'];

		$billOutstandingAmtReqest = new BillOutstandingAmountRequestType();
		$billOutstandingAmtReqest->BillOutstandingAmountRequestDetails = $billOutstandingAmtReqestDetail;


		$billOutstandingAmtReq =  new BillOutstandingAmountReq();
		$billOutstandingAmtReq->BillOutstandingAmountRequest = $billOutstandingAmtReqest;

		/*
		 * 	 ## Creating service wrapper object
		Creating service wrapper object to make API call and loading
		Configuration::getAcctAndConfig() returns array that contains credential and config parameters
		*/
		$paypalService = new PayPalAPIInterfaceServiceService(self::getPayPalConfiguration());
		try {
			/* wrap API method calls on the service object with a try catch */
			$billOutstandingAmtResponse = $paypalService->BillOutstandingAmount($billOutstandingAmtReq);
		} catch (\Exception $ex) {
			$aError = self::error($ex, $aCustomerPlan['paymentToken']);
			$error = $aError['message'] . ' ' . $aError['message_detail'];
		}

		if ( isset($error) ){
			wp_send_json_error(
				array(
					'msg' => $error
				)
			);
		}

		if ( $billOutstandingAmtResponse->Ack !== 'Success' ){
			wp_send_json_error(
				array(
					'msg' => $billOutstandingAmtResponse->Errors[0]->LongMessage
				)
			);
		}else{
			self::manageRecurringPaymentProfile(self::$reactivateKey, $aCustomerPlan['profileID'], $aCustomerPlan['paymentID'], $aCustomerPlan['packageID']);
			wp_send_json_success(
				array(
					'msg' => esc_html__('Congratulation! Your payment has been successfully.', 'wiloke')
				)
			);
		}
	}

	public static function checkOutStandingAmount(){
		if ( CustomerPlan::getCurrentOutStandingAmount() > 0 ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Please bill outstanding amount before changing your plan.', 'wiloke')
				)
			);
		}
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

	public static function ensureNotDownGradeFromPremiumToFree($aCustomerPlan){
		if ( strtolower($aCustomerPlan['gateWay']) === 'paypal' ){
			if ( !empty($aCustomerPlan) && !empty($aCustomerPlan['profileID']) ){
				$aProfileInfo = self::getRecurringPaymentProfileDetails($aCustomerPlan['profileID']);
				if ( $aProfileInfo['status'] == 'success' ){
					$profileStatus = $aProfileInfo['msg']->GetRecurringPaymentsProfileDetailsResponseDetails->ProfileStatus;
					if ( ($aProfileInfo['msg']->Ack == 'Success') && ($profileStatus == self::$activeProfileKey || $profileStatus == self::$pendingProfileKey)   ){
						wp_send_json_error(
							array(
								'msg' => esc_html__('You can not downgrade from Premium Plan to Free Plan', 'wiloke')
							)
						);
					}
				}
			}
		}
	}

	public static function ensureTooTransactionIsNotTooClose(){
		if ( isset(self::$aUserPaymentInfo['profileID']) && !empty(self::$aUserPaymentInfo['profileID']) && (strtolower(self::$aUserPaymentInfo['gateWay']) == 'paypal') ){
			$aCurrentProfileStatus = self::getRecurringPaymentProfileDetails(self::$aUserPaymentInfo['profileID']);
			if ( $aCurrentProfileStatus['status'] !== 'error' ){
				if ( $aCurrentProfileStatus['msg']->GetRecurringPaymentsProfileDetailsResponseDetails->ProfileStatus == self::$pendingProfileKey ){
					wp_send_json_error(
						array(
							'msg' => esc_html__('Transaction refused because the time of the update is too close to the preview update.', 'wiloke')
						)
					);
				}
			}
		}
	}

	public static function ensureThatItIsNewPlan(){
		if ( (absint(self::$packageID) === absint(self::$aUserPaymentInfo['packageID'])) && !self::isUpdatePackage() ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Knock ... Knock ... You are really using this plan!', 'wiloke')
				)
			);
		}
	}

	public function changePlan(){
		global $wiloke;
		if ( !isset($_POST['security']) || (!check_ajax_referer('wiloke-nonce', 'security', false)) || !isset($_POST['packageID']) || empty($_POST['packageID']) ){
			wp_send_json_error(
				array(
					'msg' => $wiloke->aConfigs['translation']['deniedaccess']
				)
			);
		}

		self::$packageID = trim($_POST['packageID']);

		if ( get_post_status(self::$packageID) !== 'publish' ){
			wp_send_json_error(
				array(
					'msg' => $wiloke->aConfigs['translation']['deniedaccess']
				)
			);
		}
		$aPackageInfo = \Wiloke::getPostMetaCaching(self::$packageID, 'pricing_settings');
		$aCustomerPlan = CustomerPlan::getCustomerPlan(true);

		## If it's a free package
		if ( empty($aPackageInfo['price']) ){
			if ( !empty($aCustomerPlan) ){
				$aCurrentPackageInfo = \Wiloke::getPostMetaCaching($aCustomerPlan['packageID'], 'pricing_settings');
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
							'msg' => esc_html__('You have exceeded the number of listings for this package', 'wiloke')
						)
					);
				}else{
					if ( !WilokeFreePost::isPackageExists(self::$packageID, true) ){
						WilokeFreePost::insertPaymentHistory(self::$packageID);
					}
				}

//				self::switchAllCustomerActivatingAccountToSuspend();
			}else{
				WilokeFreePost::insertPaymentHistory(self::$packageID);
			}

			wp_send_json_success(
				array(
					'msg' => esc_html__('Congratulation! Your new plan has been updated.', 'wiloke')
				)
			);
		}

		self::$aUserPaymentInfo = CustomerPlan::getCustomerPlan(true);
		if ( empty(self::$aUserPaymentInfo) ){
			self::$thankyouUrl = \WilokePublic::addQueryToLink(\WilokePublic::getPaymentField('myaccount', true), "mode=my-billing&amp;status=changed_plan");
			\Wiloke::setSession(WilokePayment::$packageIDSessionKey, self::$packageID);
			$oStatus = self::_setExPressCheckout();
			if ( $oStatus === false ){
				wp_send_json_success(
					array(
						'msg' => $wiloke->aConfigs['translation']['deniedaccess']
					)
				);
			}

			$url = self::createRedirectToPayPalUrl($oStatus->Token);
			wp_send_json_success(
				array(
					'status' => 'redirect',
					'msg' => urlencode($url)
				)
			);
		}else{
			# Step 0: make sure that they want to renew package
			$currentPaymentGateWay = strtolower(self::$aUserPaymentInfo['gateWay']);
			if ( $currentPaymentGateWay == 'paypal' ){
				self::ensureThatItIsNewPlan();
				self::ensureTooTransactionIsNotTooClose();
			}elseif($currentPaymentGateWay=='2checkout'){
				WilokeTwoCheckout::setPackageID(self::$packageID);
				WilokeTwoCheckout::setUserPaymentInfo(self::$aUserPaymentInfo);
				WilokeTwoCheckout::ensureTooTransactionIsNotTooClose();
				WilokeTwoCheckout::ensureThatItIsNewPlan();
			}

			# Step 1: Check profile is existed or not. If empty Go to create new Profile, else reactivate previous
			$aPreviousTransaction = self::getExistingTransactions();
			if ( !empty($aPreviousTransaction) ){
				## Reactivate account if the previous transaction is existed
				$oProfileDetails = $aPreviousTransaction['msg'];
				## Only reactive if previous price matched currently price
				if ( self::passedReactivateConditional($aPackageInfo, $oProfileDetails) ){
					$status = self::manageRecurringPaymentProfile(self::$reactivateKey, $oProfileDetails->GetRecurringPaymentsProfileDetailsResponseDetails->ProfileID, $aPreviousTransaction['payment_ID'], self::$packageID);
					if ( $status === true ){
						## If the changing has been successful, We move all activating account to suspend except the latest plan
//						self::switchAllCustomerActivatingAccountToSuspend();
						do_action('wiloke/wiloke-listgo-functionality/changedPlan');
						wp_send_json_success(
							array(
								'status' => 'success',
								'msg' => esc_html__('Congratulation! Your new plan has been changed.', 'wiloke' )
							)
						);
					}
				}
			}

			## Create new profile
			self::$thankyouUrl = \WilokePublic::addQueryToLink(\WilokePublic::getPaymentField('myaccount', true), "mode=my-billing&amp;status=changed_plan");
			\Wiloke::setSession(WilokePayment::$packageIDSessionKey, self::$packageID);
			$oStatus = self::_setExPressCheckout();

			if ( $oStatus === false ){
				wp_send_json_success(
					array(
						'msg' => $wiloke->aConfigs['translation']['deniedaccess']
					)
				);
			}

			$url = self::createRedirectToPayPalUrl($oStatus->Token);
			wp_send_json_success(
				array(
					'status' => 'redirect',
					'msg' => urlencode($url)
				)
			);
		}
	}

	/**
	 * Make sure all activating account switch to Suspend status
	 * @since 1.0
	 */
	private static function switchAllCustomerActivatingAccountToSuspend(){
		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$aTransactions = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $historyTbl WHERE user_ID=%d AND method=%s AND (profile_status=%s OR profile_status=%s)",
				get_current_user_id(), 'paypal', self::$activeProfileKey, self::$pendingProfileKey
			),
			ARRAY_A
		);

		if ( !empty($aTransactions) ){
			$aCustomerPlan = CustomerPlan::getCustomerPlan(true);

			if ( empty($aCustomerPlan) ){
				return false;
			}

			foreach ( $aTransactions as $aTransaction ){
				if ( !empty($aTransaction['profile_ID'])){
					$aRecurringPaymentProfileDetails = self::getRecurringPaymentProfileDetails($aTransaction['profile_ID']);

					if ( $aTransaction['profile_ID'] == $aCustomerPlan['profileID'] ){
						continue;
					}

					if ( ($aRecurringPaymentProfileDetails['status'] !== 'error') || ($aRecurringPaymentProfileDetails['msg']->GetRecurringPaymentsProfileDetailsResponseDetails->ProfileStatus == self::$activeProfileKey) ){
						## Move all activating transaction to suspend. We need to care about customer
						self::manageRecurringPaymentProfile(self::$suspendKey, $aRecurringPaymentProfileDetails['msg']->GetRecurringPaymentsProfileDetailsResponseDetails->ProfileID, $aTransaction['ID']);
					}
				}
			}
		}
	}

	public function afterChangedPlan(){
		self::switchAllCustomerActivatingAccountToSuspend();
	}

	/*
	 * If user change package that matched with current user info (refer to User.php to understand more)
	 * Make sure that they want to renew it
	 */
	private static function isUpdatePackage(){
		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$accountStatus = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT status FROM $historyTbl WHERE ID=%d",
				self::$aUserPaymentInfo['paymentID']
			)
		);

		if ( $accountStatus === 'Success' ){
			return false;
		}

		return true;
	}

	private static function passedReactivateConditional($aPackageInfo, $oPreviousTransaction){
		if ( absint($aPackageInfo['price']) !== absint($oPreviousTransaction->GetRecurringPaymentsProfileDetailsResponseDetails->RegularRecurringPaymentsPeriod->Amount->value) ){
			return false;
		}

		if ( absint($aPackageInfo['regular_period']) !== absint($oPreviousTransaction->GetRecurringPaymentsProfileDetailsResponseDetails->RegularRecurringPaymentsPeriod->BillingFrequency) ){
			return false;
		}

		return true;
	}

	public static function getRecurringPaymentProfileDetails($profileID){
		self::_getPayPalConfiguration();
		/*
		 * Obtain information about a recurring payments profile.
		 */
		$getRPPDetailsReqest = new GetRecurringPaymentsProfileDetailsRequestType();
		/*
		 * (Required) Recurring payments profile ID returned in the CreateRecurringPaymentsProfile response. 19-character profile IDs are supported for compatibility with previous versions of the PayPal API.
		 */
		$getRPPDetailsReqest->ProfileID = $profileID;


		$getRPPDetailsReq = new GetRecurringPaymentsProfileDetailsReq();
		$getRPPDetailsReq->GetRecurringPaymentsProfileDetailsRequest = $getRPPDetailsReqest;

		/*
		 * 	 ## Creating service wrapper object
		Creating service wrapper object to make API call and loading
		Configuration::getAcctAndConfig() returns array that contains credential and config parameters
		*/
		$paypalService = new PayPalAPIInterfaceServiceService(self::$_aPayPalConfiguration);
		try {
			/* wrap API method calls on the service object with a try catch */
			$getRPPDetailsResponse = $paypalService->GetRecurringPaymentsProfileDetails($getRPPDetailsReq);
		} catch (\Exception $ex) {
			$errMsg = self::getExceptionMessage($ex);
		}

		if ( isset($errMsg) ){
			return array(
				'status' => 'error',
				'msg' => $errMsg
			);
		}

		if(isset($getRPPDetailsResponse)) {
			if ( $getRPPDetailsResponse->Ack !== 'Success' ){
				return array(
					'status' => 'error',
					'msg' => $getRPPDetailsResponse->Errors[0]->ShortMessage
				);
			}

			return array(
				'status' => 'success',
				'msg' => $getRPPDetailsResponse
			);
		}

		return array(
			'status' => 'error',
			'msg' => esc_html__('Something went wrong', 'wiloke')
		);
	}

	private static function getExistingTransactions(){
		global $wpdb;
		$historyTblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$aInformation = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $historyTblName WHERE user_ID=%d AND package_ID=%d AND method=%s AND ( (profile_status=%s) OR (profile_status=%s) ) ORDER BY ID DESC",
				get_current_user_id(), self::$packageID, 'paypal', self::$suspendedProfile, self::$pendingProfileKey
			),
			ARRAY_A
		);

		$aRecurringPaymentDetail = self::getRecurringPaymentProfileDetails($aInformation['profile_ID']);

		if ( $aRecurringPaymentDetail['msg']->Ack !== 'Success' ){
			return false;
		}

		$aRecurringPaymentDetail['payment_ID'] = $aInformation['ID'];
		$aRecurringPaymentDetail['token'] = $aInformation['token'];
		return $aRecurringPaymentDetail;
	}

	private static function manageRecurringPaymentProfile($action, $profileID, $paymentID, $packageID = null){
		self::_getPayPalConfiguration();

		if ( ($action === self::$reactivateKey) && empty($packageID) ){
			return esc_html__('Something went wrong', 'wiloke');
		}

		$token = self::getTransactionToken($paymentID);

		if ( empty($token) ){
			return esc_html__('Something went wrong', 'wiloke');
		}
		/*
		 * The ManageRecurringPaymentsProfileStatus API operation cancels, suspends, or reactivates a recurring payments profile.
		 */
		$manageRPPStatusReqestDetails = new ManageRecurringPaymentsProfileStatusRequestDetailsType();
		/*
		 *  (Required) The action to be performed to the recurring payments profile. Must be one of the following:

			Cancel – Only profiles in Active or Suspended state can be canceled.

			Suspend – Only profiles in Active state can be suspended.

			Reactivate – Only profiles in a suspended state can be reactivated.

		 */
		$manageRPPStatusReqestDetails->Action =  $action;
		/*
		 * (Required) Recurring payments profile ID returned in the CreateRecurringPaymentsProfile response.
		 */
		$manageRPPStatusReqestDetails->ProfileID =  $profileID;

		$manageRPPStatusReqest = new ManageRecurringPaymentsProfileStatusRequestType();
		$manageRPPStatusReqest->ManageRecurringPaymentsProfileStatusRequestDetails = $manageRPPStatusReqestDetails;


		$manageRPPStatusReq = new ManageRecurringPaymentsProfileStatusReq();
		$manageRPPStatusReq->ManageRecurringPaymentsProfileStatusRequest = $manageRPPStatusReqest;

		/*
		 * 	 ## Creating service wrapper object
		Creating service wrapper object to make API call and loading
		Configuration::getAcctAndConfig() returns array that contains credential and config parameters
		*/
		$paypalService = new PayPalAPIInterfaceServiceService(self::$_aPayPalConfiguration);

		try {
			/* wrap API method calls on the service object with a try catch */
			$manageRPPStatusResponse = $paypalService->ManageRecurringPaymentsProfileStatus($manageRPPStatusReq);
		} catch (\Exception $ex) {
			$ex_message = $ex->getMessage();
			$ex_detailed_message = '';
			if($ex instanceof PPConnectionException) {
				$ex_detailed_message = esc_html__('Error connecting to', 'wiloke') . $ex->getUrl();
			} else if($ex instanceof PPMissingCredentialException || $ex instanceof PPInvalidCredentialException) {
				$ex_detailed_message = $ex->errorMessage();
			} else if($ex instanceof PPConfigurationException) {
				$ex_detailed_message = esc_html__('Invalid configuration. Please check your configuration file', 'wiloke');
			}

			$errorMsg = esc_html__('Message:', 'wiloke') . $ex_message . esc_html__('. Detailed Message:', 'wiloke') . $ex_detailed_message;
		}

		if ( isset($errorMsg) ){
			return $errorMsg;
		}

		if(isset($manageRPPStatusResponse)) {
			WilokePayment::updatePaymentHistory($manageRPPStatusResponse->Ack, $token, $manageRPPStatusResponse, self::controlProfileStatus($action), $profileID, true);
			if ( $manageRPPStatusResponse->Ack === 'Success' ){
				if ( $action === self::$reactivateKey ){
					CustomerPlan::setCustomerPlan(
						array(
							'profileID'=>$profileID,
							'packageID'=>$packageID,
							'paymentID'=>$paymentID,
							'paymentToken'=>$token,
							'gateWay'  => 'paypal'
						),
						self::$aPaymentType[1]
					);
				}
				return true;
			}else{
				return $manageRPPStatusResponse->Errors[0]->ShortMessage;
			}
		}
		return esc_html__('Something went wrong', 'wiloke');
	}

	private static function controlProfileStatus($status){
		if ( $status === self::$suspendKey ){
			return 'SuspendedProfile';
		}else if ( $status == self::$cancelKey ){
			return 'CancelledProfile';
		}else if ( $status == self::$reactivateKey ){
			return 'ActiveProfile';
		}
	}

	private static function updateRecurringPayment(){
		self::_getPayPalConfiguration();

		$aPackageInfo = \Wiloke::getPostMetaCaching(self::$packageID, 'pricing_settings');

		/*
		 * Obtain information about a recurring payments profile.
		 */
		$getRPPDetailsReqest = new GetRecurringPaymentsProfileDetailsRequestType();
		/*
		 * (Required) Recurring payments profile ID returned in the CreateRecurringPaymentsProfile response. 19-character profile IDs are supported for compatibility with previous versions of the PayPal API.
		 */
		$getRPPDetailsReqest->ProfileID = trim(self::$aUserPaymentInfo['profileID']);


		$getRPPDetailsReq = new GetRecurringPaymentsProfileDetailsReq();
		$getRPPDetailsReq->GetRecurringPaymentsProfileDetailsRequest = $getRPPDetailsReqest;

		/*
		 * 	 ## Creating service wrapper object
		Creating service wrapper object to make API call and loading
		Configuration::getAcctAndConfig() returns array that contains credential and config parameters
		*/
		$paypalService = new PayPalAPIInterfaceServiceService(self::$_aPayPalConfiguration);

		$getRPPDetailsResponse = $paypalService->GetRecurringPaymentsProfileDetails($getRPPDetailsReq);

		$shippingAddress = new AddressType();
		$shippingAddress->Name = $getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->SubscriberName;
		$shippingAddress->Street1 = $getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsProfileDetails->SubscriberShippingAddress->Street1;
		$shippingAddress->Street2 = '';
		$shippingAddress->CityName = $getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsProfileDetails->SubscriberShippingAddress->CityName;
		$shippingAddress->StateOrProvince = $getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsProfileDetails->SubscriberShippingAddress->StateOrProvince;
		$shippingAddress->PostalCode = $getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsProfileDetails->SubscriberShippingAddress->PostalCode;
		$shippingAddress->Country = $getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsProfileDetails->SubscriberShippingAddress->Country;
		$shippingAddress->Phone = $getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsProfileDetails->SubscriberShippingAddress->Phone;

		$updateRPProfileRequestDetail = new UpdateRecurringPaymentsProfileRequestDetailsType();

		$updateRPProfileRequestDetail->SubscriberName = $getRPPDetailsResponse->GetRecurringPaymentsProfileDetailsResponseDetails->SubscriberName;
		$updateRPProfileRequestDetail->BillingStartDate = date_i18n(DATE_ATOM);
		$updateRPProfileRequestDetail->SubscriberShippingAddress  = $shippingAddress;

		$paymentBillingPeriod =  new BillingPeriodDetailsType();
		$paymentBillingPeriod->BillingFrequency = absint($aPackageInfo['regular_period']);
		$paymentBillingPeriod->BillingPeriod = self::$billingPeriod;
		$paymentBillingPeriod->TotalBillingCycles = self::$totalBillingCycles;

		$paymentBillingPeriod->Amount = new BasicAmountType(self::$_aConfigs['currency_code'], trim(absint($aPackageInfo['price'])));
		$paymentBillingPeriod->ShippingAmount = new BasicAmountType(self::$_aConfigs['currency_code'], 0);
		$paymentBillingPeriod->TaxAmount = new BasicAmountType(self::$_aConfigs['currency_code'], 0);

		/*
		* `Description` - Description of the recurring payment.
		*/
		$updateRPProfileRequestDetail->Description = self::$billingAgreementText;


//		if( $_REQUEST['trialBillingFrequency'] != "" && $_REQUEST['trialBillingAmount'] != "") {
//			$trialBillingPeriod =  new BillingPeriodDetailsType();
//			$trialBillingPeriod->BillingFrequency = $_REQUEST['trialBillingFrequency'];
//			$trialBillingPeriod->BillingPeriod = $_REQUEST['trialBillingPeriod'];
//			$trialBillingPeriod->TotalBillingCycles = $_REQUEST['trialBillingCycles'];
//			$trialBillingPeriod->Amount = new BasicAmountType($currencyCode, $_REQUEST['trialBillingAmount']);
//			$trialBillingPeriod->ShippingAmount = new BasicAmountType($currencyCode, $_REQUEST['trialShippingAmount']);
//			$trialBillingPeriod->TaxAmount = new BasicAmountType($currencyCode, $_REQUEST['trialTaxAmount']);
//			$updateRPProfileRequestDetail->TrialPeriod  = $trialBillingPeriod;
//		}

		$updateRPProfileRequestDetail->PaymentPeriod = $paymentBillingPeriod;
		$updateRPProfileRequestDetail->MaxFailedPayments =  isset(self::$_aConfigs['maxFailedPayments']) ? absint(self::$_aConfigs['maxFailedPayments']) : 3;

		$updateRPProfileRequestDetail->AutoBillOutstandingAmount = self::$autoBillOutstandingAmount;

//		if($_REQUEST['creditCardNumber'] != null){
//			// Credit card information cannot be updated for profiles created through Express Checkout, since the payment is tied to the PayPal account and not a credit card.
//			$creditCard = new CreditCardDetailsType();
//			$creditCard->CreditCardNumber = $_REQUEST['creditCardNumber'];
//			$creditCard->CreditCardType = $_REQUEST['creditCardType'];
//			$creditCard->CVV2 = $_REQUEST['cvv'];
//			$creditCard->ExpMonth = $_REQUEST['expMonth'];
//			$creditCard->ExpYear = $_REQUEST['expYear'];
//			$updateRPProfileRequestDetail->CreditCard = $creditCard;
//		}

		$updateRPProfileRequestDetail->ProfileID = trim(self::$aUserPaymentInfo['profileID']);

		$updateRPProfileRequest = new UpdateRecurringPaymentsProfileRequestType();
		$updateRPProfileRequest->UpdateRecurringPaymentsProfileRequestDetails = $updateRPProfileRequestDetail;


		$updateRPProfileReq =  new UpdateRecurringPaymentsProfileReq();
		$updateRPProfileReq->UpdateRecurringPaymentsProfileRequest = $updateRPProfileRequest;

		/*
		 *  ## Creating service wrapper object
		Creating service wrapper object to make API call and loading
		Configuration::getAcctAndConfig() returns array that contains credential and config parameters
		*/
		$paypalService = new PayPalAPIInterfaceServiceService(self::$_aPayPalConfiguration);

		try {
			/* wrap API method calls on the service object with a try catch */
			$updateRPProfileResponse = $paypalService->UpdateRecurringPaymentsProfile($updateRPProfileReq);
		} catch (\Exception $ex) {
			$ex_message = $ex->getMessage();
			$ex_detailed_message = '';
			if($ex instanceof PPConnectionException) {
				$ex_detailed_message = "Error connecting to " . $ex->getUrl();
			} else if($ex instanceof PPMissingCredentialException || $ex instanceof PPInvalidCredentialException) {
				$ex_detailed_message = $ex->errorMessage();
			} else if($ex instanceof PPConfigurationException) {
				$ex_detailed_message = "Invalid configuration. Please check your configuration file";
			}

			$errMsg = esc_html__('Message:', 'wiloke') . $ex_message . esc_html__('. Detailed Message:', 'wiloke') . $ex_detailed_message;
		}

		if ( isset($errMsg) ){
			return $errMsg;
		}

		if(isset($updateRPProfileResponse)) {
			WilokePayment::updatePaymentHistory($updateRPProfileResponse->Ack, self::$aUserPaymentInfo['paymentToken'], $updateRPProfileResponse);
			if ( $updateRPProfileResponse->Ack !== 'Success' ){
				return $updateRPProfileResponse->Errors[0]->LongMessage;
			}

			return true;
		}

		return esc_html__('Something went wrong', 'wiloke');
	}

	protected static function error($ex, $token){
		$aError['message'] = $ex->getMessage();
		$aError['type'] = get_class($ex);

		if($ex instanceof PayPal\Exception\PPConnectionException) {
			$aError['message_detail'] = esc_html__('Error connecting to ', 'wiloke') . $ex->getUrl();
		} else if($ex instanceof PayPal\Exception\PPMissingCredentialException || $ex instanceof PayPal\Exception\PPInvalidCredentialException) {
			$aError['message_detail'] = $ex->errorMessage();
		} else if($ex instanceof PayPal\Exception\PPConfigurationException) {
			$aError['message_detail'] = esc_html__('Invalid configuration. Please check your configuration file', 'wiloke');
		}

		$eError['log_at'] = date_i18n(DATE_ATOM);

		self::writeErrorLog($token, $aError);
		return $aError;
	}

	protected static function writeErrorLog($token, $aError){
		global $wpdb;

		$paymentHistoryTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$paymentID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $paymentHistoryTbl WHERE token=%s",
				$token
			)
		);

		$logTbl = $wpdb->prefix . AlterTablePayPalErrorLog::$tblName;

		$wpdb->insert(
			$logTbl,
			array(
				'payment_ID' => $paymentID,
				'user_ID' => get_current_user_id(),
				'reason'  => json_encode($aError)
			)
		);

		$wpdb->update(
			$paymentHistoryTbl,
			array(
				'status' => self::$failedKey
			),
			array(
				'ID' => $paymentID
			),
			array('%s'),
			array('%d')
		);
	}

	private static function _doExpressCheckout($aData){
		self::_getPayPalConfiguration();
		$aData['token'] = trim($aData['token']);
		$aData['payerID'] = trim($aData['payerID']);

		$token         = urlencode($aData['token']);
		$payerId       = urlencode($aData['payerID']);
		$paymentAction = urlencode('Sale');
		$isError = false;

		$getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($token);
		$getExpressCheckoutReq = new GetExpressCheckoutDetailsReq();
		$getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;
		$paypalService = new PayPalAPIInterfaceServiceService(self::$_aPayPalConfiguration);

		try {
			/* wrap API method calls on the service object with a try catch */
			$getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);
		} catch (\Exception $ex) {
			self::error($ex, $token);
			WilokePayment::updatePaymentHistory(self::$failedKey, $aData['token'], $getExpressCheckoutReq);
			$isError = true;
		}

		if ( $isError ){
			return false;
		}

		/*
		 * The total cost of the transaction to the buyer. If shipping cost (not applicable to digital goods) and tax charges are known, include them in this value. If not, this value should be the current sub-total of the order. If the transaction includes one or more one-time purchases, this field must be equal to the sum of the purchases. Set this field to 0 if the transaction does not include a one-time purchase such as when you set up a billing agreement for a recurring payment that is not immediately charged. When the field is set to 0, purchase-specific fields are ignored.
		*/
		$orderTotal = new BasicAmountType();
		$orderTotal->currencyID = self::$_aConfigs['currency_code'];
		$orderTotal->value = $aData['amount'];

		$paymentDetails= new PaymentDetailsType();
		$paymentDetails->OrderTotal = $orderTotal;

		$DoECRequestDetails = new DoExpressCheckoutPaymentRequestDetailsType();
		$DoECRequestDetails->PayerID = $payerId;
		$DoECRequestDetails->Token = $token;
		$DoECRequestDetails->PaymentAction = $paymentAction;
		$DoECRequestDetails->PaymentDetails[0] = $paymentDetails;
		$DoECRequest = new DoExpressCheckoutPaymentRequestType();
		$DoECRequest->DoExpressCheckoutPaymentRequestDetails = $DoECRequestDetails;
		$DoECReq = new DoExpressCheckoutPaymentReq();
		$DoECReq->DoExpressCheckoutPaymentRequest = $DoECRequest;

		try {
			/* wrap API method calls on the service object with a try catch */
			$DoECResponse = $paypalService->DoExpressCheckoutPayment($DoECReq);
		} catch (\Exception $ex) {
			self::error($ex, $token);
			WilokePayment::updatePaymentHistory(self::$failedKey, $aData['token'], $getECResponse);
			$isError = true;
		}

		if ( $isError ){
			return false;
		}

		WilokePayment::updatePaymentHistory($DoECResponse->Ack, $aData['token'], $getECResponse);

		CustomerPlan::setCustomerPlan(
			array(
				'packageID' => \Wiloke::getSession(WilokePayment::$packageIDSessionKey),
				'profileID' => null,
				'paymentID' => self::getPaymentIDByToken($aData['token']),
				'paymentToken' => $aData['token'],
				'gateWay'   => 'paypal'
			),
			self::$aPaymentType[0]
		);

		if ( self::getBillingType() !== 'None' ){
			do_action('wiloke/wiloke-listgo-functionality/changedPlan');
//			self::switchAllCustomerActivatingAccountToSuspend();
		}

	}

	public static function sendPaymentHistoryCustomer($DoECResponse){
		self::_getPayPalConfiguration();
		$userID    = get_current_user_id();
		$oUser = get_userdata($userID);
		$userEmail = $oUser->user_email;
		$aHeaders[] = 'Content-Type: text/html; charset=UTF-8';

		if ( $DoECResponse->Ack === 'Success' ){
			$subject   = esc_html__('Receipt for your payment to ', 'wiloke') . self::$_aConfigs['brandname'];
			$message   = esc_html__('Thank you for your order. The below is your order information: ', 'wiloke');
		}else{
			$subject   = esc_html__('Failed Payment Notification', 'wiloke') . self::$_aConfigs['brandname'];
			$message   = esc_html__('Thank you for using our service. Unfortunately, Your payment via PayPal has been failed. The below is your order information: ', 'wiloke');
		}

		$message  .= esc_html__('Payment Method: PayPal', 'wiloke');
		$message  .= esc_html__('Transition ID: ', 'wiloke') . $DoECResponse->DoExpressCheckoutPaymentResponseDetails->PaymentInfo[0]->TransactionID;

		$message .= esc_html__('Please feel free to contact us with any questions.', 'wiloke');
		$message .= esc_html__('Thanks again!', 'wiloke');
		$message .= self::$_aConfigs['brandname'];
		$message .= '<a href="'.esc_url(home_url('/')).'">'.esc_html(home_url('/')).'</a>';

		wp_mail($userEmail, $subject, $message, $aHeaders);
	}

	public static function removeSession(){
		\Wiloke::removeSession(self::$tokenSessionKey);
	}

	public static function setExPressCheckout(){
		self::_getPayPalConfiguration();
		# In case the set express checkout be processing via ajax
		$packageID = \Wiloke::getSession(WilokePayment::$packageIDSessionKey);

		if ( $packageID === false || empty($packageID) ){
			return false;
		}

		if ( !get_post_status($packageID) === 'publish' ){
			wp_die( esc_html__('The package does not exists.', 'wiloke') );
		}

		if ( isset(self::$_aConfigs['thankyou']) && !empty(self::$_aConfigs['thankyou']) ){
			self::$_aConfigs['thankyou'] = get_permalink(self::$_aConfigs['thankyou']);
		}else{
			self::$_aConfigs['thankyou'] = home_url('/');
		}

		self::$thankyouUrl = self::$_aConfigs['thankyou'];

		self::$packageID = $packageID;
		$oStatus = self::_setExPressCheckout();

		if ( $oStatus === false ){
			return false;
		}else{
			header('Location: '.self::createRedirectToPayPalUrl($oStatus->Token));
		}
	}

	private static function createRedirectToPayPalUrl($token){
		self::_getPayPalConfiguration();
		if ( self::$_aConfigs['mode'] === 'live' ){
			return 'https://www.paypal.com/webscr?cmd=_express-checkout&token='.$token;
		}else{
			return 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token='.$token;
		}
	}

	public function processPayment(){
		global $wiloke;
		$result = $this->setExPressCheckout();
		if ( !$result ){
			wp_die($wiloke->aConfigs['translation']['somethingwrong']);
		}

		header('Location'.$result);
	}
}