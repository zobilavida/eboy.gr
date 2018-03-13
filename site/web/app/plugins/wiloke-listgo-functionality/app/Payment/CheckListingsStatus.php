<?php
/**
 * We will check listings status daily
 * @since 1.0
 */

namespace WilokeListGoFunctionality\Payment;
use WilokeListGoFunctionality\AlterTable\AlterTablePackageStatus;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentHistory;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentRelationships;
use WilokeListGoFunctionality\Email\SendMail;
use WilokeListGoFunctionality\CustomerPlan\CustomerPlan;
use WilokeListGoFunctionality\Payment\Paypal as WilokePayPal;

class CheckListingsStatus{
	public static $activationName = 'wiloke_submission_checking_listing_status';
	public $timeZoneString = '';
	public $aExpired = array();
	public $aExcludes = array();
	public $aPackageInformation = array();
	public $aUserID = array();
	public $aCustomerPlanExpired = array();
	protected $aUserPaymentInfo = array();
	protected $aRecurringProfileDetail = array();
	protected $aCustomersStatus = array();
	protected $aLastUpdatedPaymentAt = array();

	public function __construct() {
		add_action(self::$activationName, array($this, 'checkingProcessChecking'));
		add_action('wiloke/wiloke-listgo-functionality/app/customerplan/updatedCustomerPlan', array($this, 'rePublishListing'), 10, 1);
		add_action('wiloke/wiloke-listgo-functionality/app/customerplan/updatedCustomerPlan', array($this, 'updateFeatureToListing'), 10, 1);
	}

	private function getRecurringProfileDetails($profileID){
		if ( isset($this->aRecurringProfileDetail[$profileID]) ){
			return $this->aRecurringProfileDetail[$profileID];
		}

		$this->aRecurringProfileDetail[$profileID] = WilokePayPal::getRecurringPaymentProfileDetails($profileID);
		return $this->aRecurringProfileDetail[$profileID];
	}

	public function updateFeatureToListing($userID){
		if ( CustomerPlan::isRecurringPlan() ){
			$aUserPaymentInfo = CustomerPlan::getCustomerPlan();
			$aRecurringProfileDetails = $this->getRecurringProfileDetails($aUserPaymentInfo['profileID']);
			if ( ($aRecurringProfileDetails['status'] != 'error') ) {
				$profileStatus = $aRecurringProfileDetails['msg']->GetRecurringPaymentsProfileDetailsResponseDetails->ProfileStatus;
				if ( ( $profileStatus == 'ActiveProfile' ) || ( $profileStatus == 'PendingProfile' ) ) {

					$aPackageSettings = \Wiloke::getPostMetaCaching($aUserPaymentInfo['packageID'], 'pricing_settings');
					global $wpdb;
					$postTbl = $wpdb->prefix . 'posts';

					$oPosts = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT ID FROM $postTbl WHERE post_type=%s AND post_author=%d",
							'listing', $userID
						)
					);

					if ( empty( $oPosts ) ) {
						return false;
					}

					foreach ( $oPosts as $oPost ) {
						update_post_meta( $oPost->ID, 'wiloke_submission_do_not_show_on_map', $aPackageSettings['publish_on_map'] );

						if ( isset( $aPackageSettings['toggle_add_feature_listing'] ) && ( $aPackageSettings['toggle_add_feature_listing'] == 'enable' ) ) {
							update_post_meta( $oPost->ID, 'wiloke_listgo_toggle_highlight', 1 );
						} else {
							update_post_meta( $oPost->ID, 'wiloke_listgo_toggle_highlight', 0 );
						}
						$this->setMenuOrder( $oPost->ID, $aPackageSettings );
					}
				}
			}
		}
	}

	private function setMenuOrder($postID, $aPackageSettings){
		$price = isset($aPackageSettings['price']) ? absint($aPackageSettings['price']) : 0;
		$menuOrder = 100000000*($price);
		wp_update_post(
			array(
				'ID' => $postID,
				'menu_order' => $menuOrder,
				'post_type' => 'listing'
			)
		);
	}


	public function rePublishListing($userID){
		global $wpdb;
		$aUserPaymentInfo = CustomerPlan::getCustomerPlan(true);

		if (!empty($aUserPaymentInfo) && !empty($aUserPaymentInfo['profileID'])){
			$aRecurringProfileDetails = $this->getRecurringProfileDetails($aUserPaymentInfo['profileID']);
			if ( ($aRecurringProfileDetails['status'] != 'error') ){
				$profileStatus = $aRecurringProfileDetails['msg']->GetRecurringPaymentsProfileDetailsResponseDetails->ProfileStatus;

				if ( ($profileStatus == 'ActiveProfile') || ($profileStatus == 'PendingProfile') ){
					$postsTbl = $wpdb->prefix . 'posts';
					$paymentrelationShipTbl = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;
					$aResults = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT * FROM $postsTbl WHERE post_status=%s AND post_author=%d AND post_type=%s",
							'expired', $userID, 'listing'
						)
					);

					if ( !empty($aResults) ){
						foreach ( $aResults as $oResult ){
							wp_update_post(
								array(
									'ID' => $oResult->ID,
									'post_status' => 'publish',
									'post_type'   => 'listing',
									'post_author' => $oResult->post_author
								)
							);

							$wpdb->update(
								$paymentrelationShipTbl,
								array(
									'status' => 'addnew'
								),
								array(
									'object_ID' => $oResult->ID
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
				}
			}
		}
	}

	protected function getUserPaymentInfo($userID){
		if ( isset($this->aUserPaymentInfo[$userID]) ){
			return $this->aUserPaymentInfo[$userID];
		}

		$this->aUserPaymentInfo[$userID] = CustomerPlan::getCustomerPlanByID($userID);
		return $this->aUserPaymentInfo[$userID];
	}

	public function checkingProcessChecking($aExcludes=array()){
		global $wpdb;
		$tblRelationShips = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;
		$tblPackageStatus = $wpdb->prefix . AlterTablePackageStatus::$tblName;
		Payment::getPaymentConfiguration();
		$moveInTrashAfter = isset(Payment::$aPaymentConfiguration['move_listing_to_expired_store_after']) ? absint(Payment::$aPaymentConfiguration['move_listing_to_expired_store_after']) : 0;

		$currentTime = new \DateTime('now');

		$sql = "SELECT * FROM $tblRelationShips WHERE (status='addnew' OR status='renew')";

		if ( !empty($this->aExcludes) ){
			$exclude = implode(',', $this->aExcludes);
			$sql .= " AND object_ID NOT IN (".esc_sql($exclude).")";
		}

		$sql .= " LIMIT 50";

		$aResults = $wpdb->get_results($sql,ARRAY_A);

		if ( !empty($aResults) ){
			foreach ( $aResults as $aResult ){
				$aPaymentIDs[] = $aResult['payment_ID'];
				$this->aExcludes[] = $aResult['object_ID'];
			}

			$i = 0;
			foreach ( $aResults as $aResult ) {
				$aUserPaymentInfo = $this->getUserPaymentInfo( $aResult['object_ID'] );
				if ( empty( $aUserPaymentInfo ) || !isset( $aUserPaymentInfo['paymentType'] ) || !CustomerPlan::isRecurringPlan( $aUserPaymentInfo ) ) {
					if ( ! isset( $this->aPackageInformation[ $aResult['payment_ID'] ] ) ) {
						$aPackageResult = $wpdb->get_row(
							$wpdb->prepare(
								"SELECT package_information, user_ID, payment_ID, package_ID FROM $tblPackageStatus WHERE payment_ID=%d",
								$aResult['payment_ID']
							),
							ARRAY_A
						);

						$this->aPackageInformation[ $aResult['payment_ID'] ] = json_decode( $aPackageResult['package_information'], true );
						$this->aUserID[ $aResult['payment_ID'] ] = $aPackageResult['user_ID'];
					}

					if (
						(
							CustomerPlan::isNonRecurringPlan($aUserPaymentInfo)
							&&
							(
								(
									isset($this->aUserPaymentInfo[$aResult['payment_ID']]['regular_period'])
							        && empty($this->aUserPaymentInfo[$aResult['payment_ID']]['regular_period'])
								)
								||
								(
									!isset($this->aUserPaymentInfo[$aResult['payment_ID']]['regular_period'])
									&& empty($this->aUserPaymentInfo[$aResult['payment_ID']]['duration'])
								)
							)
						)
						||
						(
							CustomerPlan::isFreePlan($aUserPaymentInfo)
							&& !empty( $this->aPackageInformation[ $aResult['payment_ID'] ]['duration'])
					     )
					) {
						$duration = $this->aPackageInformation[ $aResult['payment_ID'] ]['duration'];
						$duration = absint( $duration );
						$getPostDate = get_the_date( 'Y-m-d', $aResult['object_ID'] );
						$createdAt   = new \DateTime( $getPostDate );
						$interval    = $createdAt->diff( $currentTime );
						$diff = $interval->format('%a');
						if ( absint($diff) > absint( $duration ) ) {
							$this->aExpired[ $i ]['listing_ID'] = $aResult['object_ID'];
							$this->aExpired[ $i ]['user_ID']    = $this->aUserID[ $aResult['payment_ID'] ];
							$this->aExpired[ $i ]['expired']    = $interval;
							$this->aExpired[ $i ]['type']    = 'NonRecurring';

							if ( $interval > absint($moveInTrashAfter) ) {
								if ( get_post_field('post_status') == 'publish' ){
									$this->moveListingToExpiredStore($aResult);
								}else{
									$this->moveListingToTrashStore($aResult);
								}
							}
						}
					}
				}else{
					if ( $aUserPaymentInfo['gateWay'] == 'paypal' ){
						$aRecurringProfileDetails = WilokePayPal::getRecurringPaymentProfileDetails($aUserPaymentInfo['profileID']);
						if ( $aRecurringProfileDetails['status'] == 'error' ){
							$this->moveListingToExpiredStore($aResult);
							$this->aExpired[$i]['user_ID'] = $aResult['user_ID'];
							$this->aExpired[$i]['package_id'] = $aUserPaymentInfo['packageID'];
							$this->aExpired[ $i ]['type']    = 'Recurring';
						}else{
							if ( !empty($aRecurringProfileDetails['msg']->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsSummary->OutstandingBalance) && absint($aRecurringProfileDetails['msg']->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsSummary->OutstandingBalance->value) > 0 ){
								$this->aExpired[$i]['user_ID'] = $aResult['user_ID'];
								$this->aExpired[$i]['package_ID'] = $aUserPaymentInfo['packageID'];
								$this->aExpired[ $i ]['type']    = 'Recurring';

								$latestPayment = new \DateTime($aRecurringProfileDetails['msg']->GetRecurringPaymentsProfileDetailsResponseDetails->RecurringPaymentsSummary->LastPaymentDate);
								$interval    = $latestPayment->diff( $currentTime );
								$diff = $interval->format('%a');
								if ( absint($diff) > absint($moveInTrashAfter) ) {
									if ( get_post_field('post_status') == 'publish' ){
										$this->moveListingToExpiredStore($aResult);
									}else{
										$this->moveListingToTrashStore($aResult);
									}
								}
							}
						}
					}elseif( $aUserPaymentInfo['gateWay'] == '2checkout' ){
						$customerStatus = $this->getCustomersStatus($aResult['user_ID'], $aResult['payment_ID']);
						if ( $customerStatus == 'SuspendedProfile' || $customerStatus == 'CancelledProfile' ){
							$lastUpdated = $this->lastUpdatedPaymentAt($aResult['user_ID'], $aResult['payment_ID']);
							$this->aExpired[$i]['user_ID'] = $aResult['user_ID'];
							$this->aExpired[$i]['package_ID'] = $aUserPaymentInfo['packageID'];
							$this->aExpired[ $i ]['type']    = 'Recurring';
							$latestPayment = new \DateTime($lastUpdated);
							$interval    = $latestPayment->diff( $currentTime );
							$diff = $interval->format('%a');
							if ( absint($diff) > absint($moveInTrashAfter) ) {
								if ( get_post_field('post_status') == 'publish' ){
									$this->moveListingToExpiredStore($aResult);
								}else{
									$this->moveListingToTrashStore($aResult);
								}
							}
						}
					}

				}

				$i++;
			}
			$this->announceCustomer();
			array_map(array(__CLASS__, 'checkingProcessChecking'), array());
		}

		return false;
	}

	protected function lastUpdatedPaymentAt($userID, $paymentID){
		if ( isset($this->aLastUpdatedPaymentAt[$userID]) ){
			return $this->aLastUpdatedPaymentAt[$userID];
		}else{
			global $wpdb;
			$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
			$this->aLastUpdatedPaymentAt[$userID] = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT updated_at FROM $historyTbl WHERE ID=%d",
					$paymentID
				)
			);

			return $this->aLastUpdatedPaymentAt[$userID];
		}
	}

	protected function getCustomersStatus($userID, $paymentID){
		if ( isset($this->aCustomersStatus[$userID]) ){
			return $this->aCustomersStatus[$userID];
		}else{
			global $wpdb;
			$historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
			$this->aCustomersStatus[$userID] = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT profile_status FROM $historyTbl WHERE ID=%d",
					$paymentID
				)
			);

			return $this->aCustomersStatus[$userID];
		}
	}

	protected function moveListingToTrashStore($aResult){
		wp_update_post(
			array(
				'post_type'   => 'listing',
				'ID'          => $aResult['object_ID'],
				'post_status' => 'trash'
			)
		);
	}

	protected function moveListingToExpiredStore($aResult){
		global $wpdb;
		wp_update_post(
			array(
				'post_type'   => 'listing',
				'ID'          => $aResult['object_ID'],
				'post_status' => 'expired'
			)
		);
		$tblRelationShips = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;
		$wpdb->update(
			$tblRelationShips,
			array(
				'status' => 'expired'
			),
			array(
				'object_ID' => $aResult['object_ID']
			),
			array(
				'%s'
			),
			array(
				'%d'
			)
		);
	}

	public function announceCustomer(){
		$instEmail = new SendMail;
		if ( !empty($this->aExpired) ){
			foreach ( $this->aExpired as $aData ){
				if ( $aData['type'] != 'Recurring' ){
					$instEmail->expired($aData);
				}else{
					$instEmail->outstandingBalance($aData);
				}
			}
		}
	}

	public function checkingListingScheduled(){
		if ( !wp_next_scheduled ( self::$activationName ) ) {
			wp_schedule_event(time(), 'daily', self::$activationName);
		}
	}

	public function deactivateCheckingListingScheduled(){
		wp_clear_scheduled_hook(self::$activationName);
	}
}