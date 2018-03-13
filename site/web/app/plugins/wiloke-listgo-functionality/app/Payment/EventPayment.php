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
use FakerPress\Module\User;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentEventRelationship;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentHistory;
use WilokeListGoFunctionality\CustomerPlan\CustomerPlan as WilokeCustomerPlan;
use WilokeListGoFunctionality\CustomerPlan\CustomerPlan;
use WilokeListGoFunctionality\Submit\User as WilokeUser;
use WilokeListGoFunctionality\Payment\Payment as WilokePayment;

## Set Express Checkout
use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\EBLBaseComponents\AddressType;
use PayPal\EBLBaseComponents\BillingAgreementDetailsType;
use PayPal\EBLBaseComponents\PaymentDetailsItemType;
use PayPal\EBLBaseComponents\PaymentDetailsType;
use PayPal\EBLBaseComponents\SetExpressCheckoutRequestDetailsType;
use PayPal\PayPalAPI\SetExpressCheckoutReq;
use PayPal\PayPalAPI\SetExpressCheckoutRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;

## Do Express Checkout
use PayPal\EBLBaseComponents\DoExpressCheckoutPaymentRequestDetailsType;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentReq;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentRequestType;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsReq;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsRequestType;

use WilokeListGoFunctionality\AlterTable\AlterTablePayPalErrorLog;

use PayPal\Exception\PPConfigurationException;
use PayPal\Exception\PPConnectionException;

use WilokeListGoFunctionality\Payment\PayPal as WilokePayPal;
use WilokeListGoFunctionality\Payment\TwoCheckout as WilokeTwoCheckout;
use WilokeListGoFunctionality\Frontend\FrontendListingManagement as WilokeFrontendListingManagement;
use WilokeListGoFunctionality\Model\GeoPosition as WilokeGeoPosition;

class EventPayment{
	protected $packageID = null;
	protected $listingID = null;
	public static $showEventOnCarouselKey = 'toggle_show_events_on_event_carousel';
	public static $showEventOnListKey = 'toggle_show_events_on_event_listing';
	protected $eventPricingKey = 'event_pricing_settings';
	protected $eventPayPalTokenSession = 'wiloke_listgo_paypal_event_token';
	protected $workingOnListingSession = 'wiloke_listgo_working_on_listing_id';
	protected $eventPlanIDSession = 'wiloke_listgo_event_plan_id';
	protected $eventBelongsToKey = 'wiloke_event_belongs_to';
	protected $token = null;
	protected $paymentMethod = null;
	protected $aPaymentConfiguration = null;
	protected $latestPaymentID = null;
	protected $temporaryEventContentKey = 'wiloke_listgo_temporary_save_event_content';
	protected $aEventContent = null;
	protected $deleteEventContentAfter = 3600;
	private $aPayPalConfiguration = array();
	private $failedKey = 'Failed';
	private $successKey = 'Success';
	private $postType = 'event-pricing';
	protected $eventListingRelationshipKey = 'wiloke_listing_event_relationship';
	protected $eventSettingsKey  = 'event_settings';
	protected $aCustomerInfo = array();

	private $aCardInfo = array(
		'card_name'     => '',
		'cvv'           => '',
		'expMonth'      => '',
		'expYear'       => '',
		'first_name'    => '',
		'last_name'     => '',
		'card_address1' => '',
		'card_city'     => '',
		'card_number'   => '',
		'card_country'  => '',
		'card_email'    => '',
		'card_phone'    => ''
	);

	public function __construct() {
		add_action('init', array($this, 'updatePayPalResult'), 1);
		add_action('save_post_event', array($this, 'addEventToEventRelationshipTbl'), 10, 2);
		add_action('wp_ajax_wiloke_buy_event_plan', array($this, 'buyEventPlan'));
		add_action('wp_ajax_wiloke_listgo_delete_event', array($this, 'deleteEvent'));
		add_action('wiloke/wiloke-listgo-functionality/Payment/EventPayment/payment_completed', array($this, 'paymentCompleted'));
		add_action('update_post_meta', array($this, 'updateListingEventMetaDataRelationship'), 20, 4);
		add_action('added_post_meta', array($this, 'addedListingEventMetaDataRelationship'), 20, 4);
		add_action('deleted_post_meta', array($this, 'deletedListingEventMetaDataRelationship'), 20, 4);
//		add_action('wp_trash_event', array($this, 'deletedListingEventMetaDataRelationship'));
		add_action('init', array($this, 'reUpdateEventSpecialOffer'));
	}

	public function reUpdateEventSpecialOffer(){
		if ( !get_option('wiloke_listgo_re_updated_event_special_offer') ){
			$query = new \WP_Query(
				array(
					'post_type' => 'event',
					'post_status' => 'publish',
					'posts_per_page' => -1
				)
			);

			if ( $query->have_posts() ){
				while ($query->have_posts()) {
					$query->the_post();
					$aUserMeta = \WilokePublic::getUserMeta($query->post->post_author);
					if ( $aUserMeta['role'] == 'administrator' ){
						update_post_meta($query->post->ID, self::$showEventOnCarouselKey, 'enable');
						update_post_meta($query->post->ID, self::$showEventOnListKey, 'enable');
					}else{
						$this->updateSpecialEventOffer($query->post->ID, $query->post->post_author);
					}
				}
			}
			update_option('wiloke_listgo_re_updated_event_special_offer', true);
		}
	}

	public function paymentCompleted(){
		\Wiloke::removeSession($this->eventPlanIDSession);
		\Wiloke::removeSession($this->eventPayPalTokenSession);
		\Wiloke::removeSession($this->workingOnListingSession);
	}

	public function getCustomerInfo($customerID){
		if ( !empty($this->aCustomerInfo) ){
			return $this->aCustomerInfo;
		}

		$this->aCustomerInfo = WilokeCustomerPlan::getCustomerPlanByID($customerID);
		return $this->aCustomerInfo;
	}

	public function deleteEvent(){
		if ( !isset($_POST['security']) || empty($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error(
				array(
					'msg'  => esc_html__('Wrong Security code', 'wiloke')
				)
			);
		}

		if ( !isset($_POST['eventID']) || empty($_POST['eventID']) ){
			wp_send_json_error(
				array(
					'msg'  => esc_html__('You don\'t have permission to access this page.', 'wiloke')
				)
			);
		}

		if ( !current_user_can('edit_theme_options') && (get_post_field('post_author', $_POST['eventID']) != get_current_user_id()) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('You don\'t have permission to access this area', 'wiloke')
				)
			);
		}

		wp_delete_post($_POST['eventID'], true);
	}

	public function buyEventPlan(){
		session_start();

		if ( !isset($_POST['security']) || empty($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error(
				array(
					'msg'  => esc_html__('Wrong Security code', 'wiloke')
				)
			);
		}

		if ( empty($_POST['listingID']) || (get_post_field('post_author', $_POST['listingID']) != get_current_user_id()) ){
			wp_send_json_error(
				array(
					'msg'  => esc_html__('You don\'t have permission to access this page.', 'wiloke')
				)
			);
		}

		$aRawData = json_decode(urldecode(base64_decode($_POST['data'])), true);
		$aParsedData = array();

		foreach ( $aRawData as $aField ){
			$name = strip_tags($aField['name']);
			$aParsedData[$name] = sanitize_text_field($aField['value']);
		}

		$this->packageID = $aParsedData['event_plan_id'];
		$this->listingID = $_POST['listingID'];
		$this->paymentMethod = $aParsedData['event_payment_method'];

		$aPaymentGateWays = WilokePayment::getPaymentGateWays(true);
		$aPaymentGateWays = array_keys($aPaymentGateWays);

		if ( empty($aPaymentGateWays) || !in_array($this->paymentMethod, $aPaymentGateWays) ){
			wp_send_json_error(
				array(
					'msg'  => esc_html__('This payment method is not supported by the website. Please try another payment method.', 'wiloke')
				)
			);
		}

		if ( empty($_POST['data']) ){
			wp_send_json_error(
				array(
					'msg'  => esc_html__('Please fill up all event information', 'wiloke')
				)
			);
		}

		if ( empty($_POST['event_content']) ){
			wp_send_json_error(
				array(
					'msg'  => esc_html__('Event Content is required', 'wiloke')
				)
			);
		}

		if ( empty($aParsedData['event_plan_id']) ){
			wp_send_json_error(
				array(
					'msg'  => esc_html__('Please select an event plan.', 'wiloke')
				)
			);
		}

		if ( empty($aParsedData['event_payment_method']) ){
			wp_send_json_error(
				array(
					'msg'  => esc_html__('Please select your payment method', 'wiloke')
				)
			);
		}

		$aParsedData['event_content'] = wp_kses_post($_POST['event_content']);

		$this->aEventContent = json_encode($aParsedData);

		if ( empty($aParsedData['start_on']) || empty($aParsedData['end_on']) ){
			wp_send_json_error(
				array(
					'msg'  => esc_html__('Please back to step 2 and fill up all require settings.', 'wiloke')
				)
			);
		}

		if ( empty($this->aPaymentConfiguration) ){
			$this->aPaymentConfiguration = WilokePayment::getPaymentConfiguration();
		}

		if ( $this->paymentMethod === 'paypal' ){
			$status = $this->setExpressCheckout();
			if ( !$status ){
				wp_send_json_error(
					array(
						'msg' => esc_html__('Something went wrong', 'wiloke')
					)
				);
			}else{
				\Wiloke::setSession($this->eventPayPalTokenSession, $this->token);
				\Wiloke::setSession($this->eventPlanIDSession, $this->packageID);
				\Wiloke::setSession($this->workingOnListingSession, $this->listingID);
				$this->temporarySaveEventContent();
				$generateDirectUrl = $this->createRedirectToPayPalUrl($this->token);
				wp_send_json_success(
					array(
						'status' => 'redirect',
						'msg' => urlencode($generateDirectUrl)
					)
				);
			}
		}elseif ( $this->paymentMethod === '2checkout' ){
			$this->token = trim($_POST['token']);
			$this->paymentWithTwoCheckout();
		}
	}

	private function paymentWithTwoCheckout(){
		$this->insertPaymentHistory();
		$aEventPlanSettings = \Wiloke::getPostMetaCaching($this->packageID, 'event_pricing_settings');
		$aPayWithTwoCheckout = WilokeTwoCheckout::payWithTwoCheckout($this->latestPaymentID, $aEventPlanSettings['price'], $this->token);

		if ( $aPayWithTwoCheckout['status'] == 'error' ){
			$this->updatePaymentHistory($this->failedKey, $this->token, $aPayWithTwoCheckout);
			wp_send_json_error($aPayWithTwoCheckout);
		}else{
			$this->updatePaymentHistory($this->successKey, $this->token, $aPayWithTwoCheckout['information']);
			\Wiloke::setSession($this->eventPayPalTokenSession, $this->token);
			\Wiloke::setSession($this->eventPlanIDSession, $this->packageID);
			\Wiloke::setSession($this->workingOnListingSession, $this->listingID);
			WilokeCustomerPlan::updateEventPlanIDToCustomerPlan($this->packageID);
			WilokeCustomerPlan::updatePaymentEventIDToCustomerPlan($this->latestPaymentID);
			$this->temporarySaveEventContent();
			$this->createNewEvent();
			do_action('wiloke/wiloke-listgo-functionality/Payment/EventPayment/payment_completed');

			wp_send_json_success(
				array(
					'msg'    => urlencode(get_permalink($this->listingID)),
					'status' => 'redirect'
				)
			);
		}
	}

	protected function temporarySaveEventContent(){
		set_transient($this->temporaryEventContentKey.'_'.$this->latestPaymentID, $this->aEventContent, $this->deleteEventContentAfter);
	}

	public function createNewEvent($postStatus=''){
		$listingID = \Wiloke::getSession($this->workingOnListingSession);
		$aCustomerPlan = WilokeCustomerPlan::getCustomerPlan();

		$rawEvent = get_transient($this->temporaryEventContentKey.'_'.$aCustomerPlan['paymentEventID']);
		delete_transient($this->temporaryEventContentKey.'_'.$aCustomerPlan['eventPlanID']);
		$aParsedData = json_decode($rawEvent, true);

		if ( $this->paymentMethod == '2checkout' ){
			$aNewCardInfo = array();
			foreach ( $this->aCardInfo as $key ){
				$aNewCardInfo[$key] = $aParsedData[$key];
				unset($aParsedData[$key]);
			}

			WilokeUser::saveCard($aNewCardInfo);
		}

		$aEventPlanSettings = \Wiloke::getPostMetaCaching($aCustomerPlan['paymentEventID'], 'event_pricing_settings');
		$aEventInfo = array(
			'post_type'     => 'event',
			'post_title'    => $aParsedData['event_title'],
			'post_content'  => $aParsedData['event_content'],
			'post_status'   => empty($postStatus) ? 'publish' : $postStatus,
			'post_author'   => get_current_user_id(),
			'menu_order'    => abs($aEventPlanSettings['price'])
		);
		$eventID = wp_insert_post($aEventInfo);


		if ( isset($aParsedData['belongs_to']) && !empty($aParsedData['belongs_to']) ){
			update_post_meta($eventID, 'event_published_at_timezone', self::getPostDateInTimeZone($aParsedData['belongs_to']));
		}

		set_post_thumbnail($eventID, $aParsedData['event_featured_image']);
		unset($aParsedData['event_title']);
		unset($aParsedData['event_content']);
		unset($aParsedData['event_featured_image']);
		$aParsedData['belongs_to'] = $listingID;

		update_post_meta($eventID, $this->eventSettingsKey, $aParsedData);
	}

	private function renderThankyouUrl(){
		return \WilokePublic::addQueryToLink(get_permalink($this->listingID), 'payment_method=event-paypal');
	}

	private function setExpressCheckout(){
		$aPackageName = get_the_title($this->packageID);
		$aPackageInfo = \Wiloke::getPostMetaCaching($this->packageID, $this->eventPricingKey);
		$aPayPalConfiguration = WilokePayment::getPaymentConfiguration();
		$this->aPaymentConfiguration = $aPayPalConfiguration;
		// Now we set value before redirecting to PayPal
		$currencyCode       = $aPayPalConfiguration['currency_code'];
		$instPaymentDetails = new PaymentDetailsType();
		$instItemDetails    = new PaymentDetailsItemType();

		$instItemDetails->Name          = $aPackageName;

		$amount = absint($aPackageInfo['price']);
		$instItemDetails->Amount        = new BasicAmountType($currencyCode, $amount);
		$instItemDetails->Quantity      = 1;
		$instItemDetails->ItemCategory  = 'Physical';


		$instPaymentDetails->PaymentDetailsItem[0] = $instItemDetails;
		$instPaymentDetails->PaymentAction = 'Sale';
		$instPaymentDetails->OrderTotal = new BasicAmountType($currencyCode, absint($amount));

		$setECReqDetails = new SetExpressCheckoutRequestDetailsType();
		$setECReqDetails->PaymentDetails[0] = $instPaymentDetails;

		/*
		 * (Required) URL to which the buyer is returned if the buyer does not approve the use of PayPal to pay you. For digital goods, you must add JavaScript to this page to close the in-context experience.
		 */
		if ( isset($aPayPalConfiguration['cancel']) && !empty($aPayPalConfiguration['cancel']) ){
			$aPayPalConfiguration['cancel'] = get_permalink($aPayPalConfiguration['cancel']);
			$aPayPalConfiguration['cancel'] .= strpos($aPayPalConfiguration['cancel'], '?') === false ? '?payment_method=event-paypal' : '&payment_method=event-paypal';
		}else{
			$aPayPalConfiguration['cancel'] = home_url('/');
		}

		$setECReqDetails->CancelURL = $aPayPalConfiguration['cancel'];

		/*
		 * (Required) URL to which the buyer's browser is returned after choosing to pay with PayPal. For digital goods, you must add JavaScript to this page to close the in-context experience.
		 */


		$setECReqDetails->ReturnURL = $this->renderThankyouUrl();
		$setECReqDetails->NoShipping = 0;

		// Billing agreement details
		$billingAgreementDetails = new BillingAgreementDetailsType('None');
		$setECReqDetails->BillingAgreementDetails = array($billingAgreementDetails);

		// Display options
		$setECReqDetails->BrandName = $aPayPalConfiguration['brandname'];

		$setECReqType = new SetExpressCheckoutRequestType();
		$setECReqType->SetExpressCheckoutRequestDetails = $setECReqDetails;
		$setECReq = new SetExpressCheckoutReq();
		$setECReq->SetExpressCheckoutRequest = $setECReqType;
		$instPayPalService = new PayPalAPIInterfaceServiceService(WilokePayPal::getPayPalConfiguration());

		$setECResponse = $instPayPalService->SetExpressCheckout($setECReq);

		if ( isset($setECResponse->Ack) && $setECResponse->Ack === $this->successKey  ){
			$this->token = $setECResponse->Token;
			$this->paymentMethod = 'paypal';
			$this->insertPaymentHistory();
			return $setECResponse;
		}

		return false;
	}

	private function createRedirectToPayPalUrl($token){
		if ( $this->aPaymentConfiguration['mode'] === 'live' ){
			return 'https://www.paypal.com/webscr?cmd=_express-checkout&token='.$token.'#tab-event';
		}else{
			return 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token='.$token.'#tab-event';
		}
	}

	public function updatePayPalResult(){
		if ( !isset($_REQUEST['payment_method']) || ($_REQUEST['payment_method'] !== 'event-paypal') ){
			return false;
		}

		if ( !isset($_REQUEST['token']) || empty($_REQUEST['token']) ){
			return false;
		}

		$token = trim($_REQUEST['token']);

		if ( !$this->validateToken($token) ){
			return false;
		}

		$getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($token);

		$getExpressCheckoutReq = new GetExpressCheckoutDetailsReq();
		$getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;

		$paypalService = new PayPalAPIInterfaceServiceService(WilokePayPal::getPayPalConfiguration());
		$getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);
		if ( ($getECResponse->Ack === $this->successKey) && !empty(\Wiloke::getSession($this->eventPayPalTokenSession)) ){
			$aPackageInfo = \Wiloke::getPostMetaCaching(\Wiloke::getSession($this->eventPlanIDSession), $this->eventPricingKey);
			$aData = array(
				'token'     => $getECResponse->GetExpressCheckoutDetailsResponseDetails->Token,
				'payerID'   => $getECResponse->GetExpressCheckoutDetailsResponseDetails->PayerInfo->PayerID,
				'amount'    => $getECResponse->GetExpressCheckoutDetailsResponseDetails->PaymentDetails[0]->OrderTotal->value
			);

			if ( empty($aPackageInfo) ){
				return false;
			}
			$status = $this->doExpressCheckout($aData);

			if ( $status ){
				$this->createNewEvent();
				do_action('wiloke/wiloke-listgo-functionality/Payment/EventPayment/payment_completed');
			}
		}
	}

	private function validateToken($compareWith){
		$myToken = \Wiloke::getSession($this->eventPayPalTokenSession);

		if ( $myToken != $compareWith ){
			return false;
		}

		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$status = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT status FROM $historyTbl WHERE token=%s",
				$compareWith
			)
		);

		return $status == 'pending';
	}

	private function doExpressCheckout($aData){
		$aData['token'] = trim($aData['token']);
		$aData['payerID'] = trim($aData['payerID']);

		$token         = urlencode($aData['token']);
		$payerId       = urlencode($aData['payerID']);
		$paymentAction = urlencode('Sale');
		$isError = false;

		$getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($token);
		$getExpressCheckoutReq = new GetExpressCheckoutDetailsReq();
		$getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;
		$paypalService = new PayPalAPIInterfaceServiceService(WilokePayPal::getPayPalConfiguration());

		try {
			/* wrap API method calls on the service object with a try catch */
			$getECResponse = $paypalService->GetExpressCheckoutDetails($getExpressCheckoutReq);
		} catch (\Exception $ex) {
			$this->error($ex, $token);
			$this->updatePaymentHistory($this->failedKey, $aData['token'], $getExpressCheckoutReq);
			$isError = true;
		}

		if ( $isError ){
			return false;
		}

		/*
		 * The total cost of the transaction to the buyer. If shipping cost (not applicable to digital goods) and tax charges are known, include them in this value. If not, this value should be the current sub-total of the order. If the transaction includes one or more one-time purchases, this field must be equal to the sum of the purchases. Set this field to 0 if the transaction does not include a one-time purchase such as when you set up a billing agreement for a recurring payment that is not immediately charged. When the field is set to 0, purchase-specific fields are ignored.
		*/
		$orderTotal = new BasicAmountType();
		$orderTotal->currencyID = $this->aPaymentConfiguration['currency_code'];
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
			$this->error($ex, $token);
			$this->updatePaymentHistory($this->failedKey, $aData['token'], $getECResponse);
			$isError = true;
		}

		if ( $isError ){
			return false;
		}

		$this->updatePaymentHistory($DoECResponse->Ack, $aData['token'], $getECResponse);
		WilokeCustomerPlan::updateEventPlanIDToCustomerPlan(\Wiloke::getSession($this->eventPlanIDSession));
		WilokeCustomerPlan::updatePaymentEventIDToCustomerPlan($this->getPaymentIDByToken($aData['token']));

		return true;
	}

	protected function getPaymentIDByToken($token){
		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $historyTbl WHERE token=%s",
				$token
			)
		);
	}

	protected function writeErrorLog($token, $aError){
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
				'status' => $this->failedKey
			),
			array(
				'ID' => $paymentID
			),
			array('%s'),
			array('%d')
		);
	}

	private function updatePaymentHistory($status, $token, $getExpressCheckoutReq){
		global $wpdb;
		$paymentHistoryTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$wpdb->update(
			$paymentHistoryTbl,
			array(
				'status' => $status,
				'information' => json_encode($getExpressCheckoutReq)
			),
			array(
				'token' => $token
			),
			array(
				'%s',
				'%s'
			),
			array('%s')
		);

		if ( ($status === $this->failedKey) ){
			do_action('wiloke/wiloke-listgo-functionality/Payment/EventPayment/payment_completed');
		}
	}

	protected function error($ex, $token){
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

		$this->writeErrorLog($token, $aError);
		return $aError;
	}

	private function insertPaymentHistory(){
		global $wpdb;
		$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$aEventSettings = \Wiloke::getPostMetaCaching($this->packageID, $this->eventPricingKey);
		$status = $wpdb->insert(
			$historyTbl,
			array(
				'user_ID'       => get_current_user_id(),
				'package_ID'    => $this->packageID,
				'package_type'  => get_post_field('post_type', $this->packageID),
				'token'         => $this->token,
				'method'        => $this->paymentMethod,
				'information'   => '',
				'total'         => abs($aEventSettings['price']),
				'currency'      => $this->aPaymentConfiguration['currency_code'],
				'status'        => 'pending'
			),
			array(
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s'
			)
		);

		if ( empty($status) ){
			if ( current_user_can('publish_posts') ){
				$msg = ( sprintf(__('We found an error on the line %s in the %s of wiloke-listgo-functionality plugin. Please contact us at sale@wiloke.com or sale@wiloke.com to report this issue', 'wiloke'), __LINE__, __FILE__) );
			}else{
				$msg = esc_html__('Something went wrong', 'wiloke');
			}
			wp_send_json_error(
				array(
					'msg' => $msg
				)
			);
		}

		$this->latestPaymentID = $wpdb->insert_id;
		return true;
	}

	public function addEventToEventRelationshipTbl($eventID, $event){
		if ( empty($this->isEventExisting($eventID)) ){
			$aAuthorInfo = \Wiloke::getUserMeta($event->post_author);
			if ( ($aAuthorInfo['role'] == WilokeUser::$wilokeSubmissionRole) || current_user_can('edit_theme_options') ){
				global $wpdb;
				$paymentEventRelationshipTbl = $wpdb->prefix . AlterTablePaymentEventRelationship::$tblName;
				$aCustomerPlan = $this->getCustomerInfo($event->post_author);

				$wpdb->insert(
					$paymentEventRelationshipTbl,
					array(
						'payment_ID' => $aCustomerPlan['paymentEventID'],
						'event_ID'   => $aCustomerPlan['eventPlanID'],
						'object_ID'  => $eventID
					),
					array(
						'%d',
						'%d',
						'%d'
					)
				);
			}
		}
	}

	public function addedListingEventMetaDataRelationship($metaID, $objectID, $metaKey, $aMetaValue){
		if ( $metaKey != 'event_settings' ){
			return false;
		}

		$aMetaValue = maybe_unserialize($aMetaValue);

		if ( empty($aMetaValue) || empty($aMetaValue['belongs_to']) ){
			return false;
		}

		$aEventsBelongsToListing = \Wiloke::getPostMetaCaching($aMetaValue['belongs_to'], $this->eventListingRelationshipKey);

		if ( empty($aEventsBelongsToListing) || (!empty($aEventsBelongsToListing) && !in_array($objectID, $aEventsBelongsToListing)) ){
			$aEventsBelongsToListing[] = $objectID;
			update_post_meta($aMetaValue['belongs_to'], $this->eventListingRelationshipKey, $aEventsBelongsToListing);
			update_post_meta($objectID, $this->eventBelongsToKey, $aMetaValue['belongs_to']);
		}

		if ( !empty($aMetaValue['latitude']) && !empty($aMetaValue['longitude']) ){
			if ( !WilokeGeoPosition::checkGeoExisting($objectID) ){
				WilokeGeoPosition::addGeoPosition($aMetaValue['latitude'], $aMetaValue['longitude'], $objectID);
			}else{
				WilokeGeoPosition::updateGeoPosition($aMetaValue['latitude'], $aMetaValue['longitude'], $objectID);
			}
		}

		$this->updateSpecialEventOffer($objectID);
	}

	public static function getPostDateInTimeZone($listingID){
		$timeZone = '';
		$aLocation = wp_get_post_terms($listingID, 'listing_location');
		if ( !empty($aLocation) && !is_wp_error($aLocation) ){
			$oLocation = end($aLocation);
			$aLocationData = \Wiloke::getTermOption($oLocation->term_id);
			if ( isset($aLocationData['timezone']) ){
				$timeZone = $aLocationData['timezone'];
			}
		}

		date_default_timezone_set(\WilokePublic::timezoneString($timeZone));
		return date("Y-m-d H:i:s");
	}

	public function deletedListingEventMetaDataRelationship($metaID, $objectID, $metaKey, $aMetaValue){
		$aMetaValue = maybe_unserialize($aMetaValue);

		if ( empty($aMetaValue) || empty($aMetaValue['belongs_to']) ){
			return false;
		}

		$aEventsBelongsToListing = \Wiloke::getPostMetaCaching($aMetaValue['belongs_to'], $this->eventListingRelationshipKey);
		$key = array_search($objectID, $aEventsBelongsToListing);
		unset($aEventsBelongsToListing[$key]);
		update_post_meta($aMetaValue['belongs_to'], $this->eventListingRelationshipKey, $aEventsBelongsToListing);
		delete_post_meta($objectID, $this->eventBelongsToKey);
	}

	public function updateListingEventMetaDataRelationship($metaID, $objectID, $metaKey, $aMetaValue){
		if ( $metaKey != 'event_settings' ){
			return false;
		}

		$aPreviousSettings = \Wiloke::getPostMetaCaching($objectID, $this->eventSettingsKey);
		$aMetaValue = maybe_unserialize($aMetaValue);

		if ( !WilokeGeoPosition::checkGeoExisting($objectID) ){
			WilokeGeoPosition::addGeoPosition($aMetaValue['latitude'], $aMetaValue['longitude'], $objectID);
		}else{
			WilokeGeoPosition::updateGeoPosition($aMetaValue['latitude'], $aMetaValue['longitude'], $objectID);
		}

		if ( !empty($aPreviousSettings) && !empty($aPreviousSettings['belongs_to']) ){
			$aEventsBelongsToListing = \Wiloke::getPostMetaCaching($aPreviousSettings['belongs_to'], $this->eventListingRelationshipKey);

			if ( $aPreviousSettings['belongs_to'] == $aMetaValue['belongs_to'] ){
				return false;
			}

			$findCurrentKey = array_search($objectID, $aEventsBelongsToListing);

			unset($aEventsBelongsToListing[$findCurrentKey]);
			update_post_meta($aPreviousSettings['belongs_to'], $this->eventListingRelationshipKey, $aEventsBelongsToListing);
			update_post_meta($objectID, $this->eventBelongsToKey, $aMetaValue['belongs_to']);
		}

		if ( empty($aMetaValue) || empty($aMetaValue['belongs_to']) ){
			return false;
		}

		$aEventsBelongsToListing = \Wiloke::getPostMetaCaching($aMetaValue['belongs_to'], $this->eventListingRelationshipKey);

		if ( empty($aEventsBelongsToListing) || (!empty($aEventsBelongsToListing) && !in_array($objectID, $aEventsBelongsToListing)) ){
			$aEventsBelongsToListing[] = $objectID;
			update_post_meta($aMetaValue['belongs_to'], $this->eventListingRelationshipKey, $aEventsBelongsToListing);
		}

		$this->updateSpecialEventOffer($objectID);
	}

	protected function updateSpecialEventOffer($eventID, $userID=null){
		$isShowEventOnEventCarousel = 'disable';
		$isShowEventOnEventList = 'disable';

		if ( current_user_can('edit_theme_options') ){
			$showEventOnCarouselStatus = 'enable';
			$showEventOnListStatus = 'enable';
		}else{
			$userID = empty($userID) ? get_current_user_id() : $userID;
			$aCustomerPlan = WilokeCustomerPlan::getCustomerPlan($userID);
			$showEventOnCarouselStatus = get_post_meta($aCustomerPlan['eventPlanID'], self::$showEventOnCarouselKey, true);
			$showEventOnListStatus = get_post_meta($aCustomerPlan['eventPlanID'], self::$showEventOnListKey, true);
		}

		update_post_meta($eventID, self::$showEventOnCarouselKey, $showEventOnCarouselStatus);
		update_post_meta($eventID, self::$showEventOnListKey, $showEventOnListStatus);
	}

	protected function isEventExisting($eventID){
		global $wpdb;
		$paymentEventRelationshipTbl = $wpdb->prefix . AlterTablePaymentEventRelationship::$tblName;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT object_ID FROM $paymentEventRelationshipTbl WHERE object_ID=%d",
				$eventID
			)
		);
	}

	public static function getRemainingEvent($userID=null){
		if ( current_user_can('edit_theme_options') ){
			return 10000;
		}

		$userID = empty($userID) ? get_current_user_id() : $userID;
		$aCustomerPlan = WilokeCustomerPlan::getCustomerPlan($userID);

		if ( empty($aCustomerPlan['paymentEventID']) ){
			return 0;
		}

		global $wpdb;
		$eventPaymentRelationshipTbl = $wpdb->prefix . AlterTablePaymentEventRelationship::$tblName;

		$eventUsed = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(object_ID) FROM $eventPaymentRelationshipTbl WHERE event_ID=%d AND payment_ID=%d",
				$aCustomerPlan['eventPlanID'], $aCustomerPlan['paymentEventID']
			)
		);

		if ( empty($eventUsed) ){
			$eventUsed = 0;
		}

		$aEventSettings = \Wiloke::getPostMetaCaching($aCustomerPlan['eventPlanID'], 'event_pricing_settings');
		$totalEventsAllowed = absint($aEventSettings['number_of_posts']);
		$remaining = $totalEventsAllowed - absint($eventUsed);

		if ( $remaining <= 0 ){
			return 0;
		}

		return $remaining;
	}
}