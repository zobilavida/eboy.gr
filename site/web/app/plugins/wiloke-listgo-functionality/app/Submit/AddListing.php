<?php
/**
 * This class cares everything relate to Submit Listing
 *
 * @since 1.0
 * @author wiloke
 * @package Wiloke/Themes
 * @subpackage Listgo
 * @link https://wiloke.com
 */

namespace WilokeListGoFunctionality\Submit;
use WilokeListGoFunctionality\AlterTable\AlterTablePackageStatus;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentHistory;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentRelationships;
use WilokeListGoFunctionality\Email\SendMail;
use WilokeListGoFunctionality\Payment\Payment;
use WilokeListGoFunctionality\Payment\CheckPayment;
use WilokeListGoFunctionality\Payment\FreePost;
use WilokeListGoFunctionality\Payment\PayPal;
use WilokeListGoFunctionality\Register\RegisterPricingSettings;
use WilokeListGoFunctionality\Register\RegisterWilokeSubmission;
use WilokeListGoFunctionality\Submit\User;
use WilokeListGoFunctionality\CustomerPlan\CustomerPlan;
use WilokeListGoFunctionality\Frontend\FrontendListingManagement;
use WilokeListGoFunctionality\Frontend\FrontendClaimListing;

class AddListing{
	public static $listingIDSessionKey = 'wiloke_listgo_listing_id';
	public static $aListingStatuses = array('renew', 'pending', 'expired', 'processing');
	public static $postType = 'listing';
	public static $packageIDOfListing = 'wiloke_listgo_package_id_of_listing';
	public $aTermAllows = array('listing_location', 'listing_cat', 'listing_tag');
	protected $_allowSize = 0;
	public $sessisonSubmittedStatus = 'wiloke_submission_submitted_at';
	public static $termCreatedByKey = 'wiloke_location_created_by';
	private $aParsePlaceInfo = array();
	private $aCountry = array();
	private $oLocation = array();
	private $locationID = null;
	private $parentLocationID = -1;
	private $countryLocationID = -1;
	private $administratorLevel1 = null;
	public $aData = array();
	public $listingID = null;
	public $placeID = null;
	private $menuOrder = 1;
	private static $passedPreviewKey = 'wiloke_submission_passed_preview';
	private $_userID = null;
	private $billingType = '';
	private $paymentID = null;
	private $aErrorSubmitting = false;
	private $parentType = null;

	public function __construct() {
		add_action('pre_post_update', array($this, 'refuseEditPendingStatus'));
		add_action('admin_init', array($this, 'redirectWilokeSubmissionToFrontEnd'), 10, 1);
		add_action('wp_ajax_wiloke_preview_listing', array($this, 'handlePreview'));
		add_action('wp_ajax_nopriv_wiloke_preview_listing', array($this, 'handlePreview'));
		add_action('wp_ajax_nopriv_wiloke_submit_listing', array($this, 'handleSubmit'));
		add_action('wp_ajax_wiloke_submit_listing', array($this, 'handleSubmit'));
		add_action('wp_ajax_wiloke_edit_published_listing', array($this, 'editPublishedListing'));
		add_action('wp_ajax_wiloke_find_open_table_id', array($this, 'findOpenTableID'));
		add_action('wp_ajax_nopriv_wiloke_find_open_table_id', array($this, 'findOpenTableID'));
		add_action('init', array($this, 'addListingStatus'));
		add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('wp_ajax_add_listing_fetch_term', array($this, 'fetchTerms'));
		add_action('wp_ajax_nopriv_wiloke_submission_insert_media', array($this, 'prepareInsertMedia'));
		add_action('wp_ajax_wiloke_submission_insert_media', array($this, 'prepareInsertMedia'));
		add_action('wiloke/payment/success', array($this, 'notifyAdminAboutNewlySubmission'));
		add_action('post_updated', array($this, 'autoApproved'), 10, 2);
		add_action('post_updated', array($this, 'notifyCustomerAboutTheirListing'), 10, 3);
		add_action('wiloke_submission/payment_history/', array($this, 'notifyCustomerAboutTheirPayment'), 10, 4);
		add_action('wiloke_submission/automatically_delete_listing', array($this, 'automaticallyDeleteListing'), 10);
		add_action('ajax_query_attachments_args', array($this, 'mediaAccess'));
		add_action('wiloke/wiloke_submission/afterUpdated', array($this, 'afterUpdatedClaimListing'), 10, 2);
		add_action('wiloke/payment/success', array($this, 'removeAddListingSession'));
		add_action('wiloke/payment/cancelled', array($this, 'removeAddListingSession'));
		add_action('wp_footer', array($this, 'confirmEditPopup'));
		add_action('wiloke/listgo/single-listing/after_related_post', array($this, 'renderPaymentEndEditButton'), 10, 1);
		add_action('wiloke/listgo/wiloke-submission/addlisting/before_listing_information', array($this, 'addOpenTableToListing'), 10, 4);
	}

	public function findOpenTableID(){
		$restaurant = html_entity_decode(addslashes($_POST['term']));
		// Send API Call using WP's HTTP API
		$aResponse = wp_remote_get('https://opentable.herokuapp.com/api/restaurants?name=' . $restaurant);

		if ( is_wp_error($aResponse)  ){
            wp_send_json_error(
                array(
                    'msg' => esc_html__('We found no table what you are looking for. Please try with another keywords', 'wiloke')
                )
            );
        }else{
			wp_send_json_success(
				array(
					'data' => $aResponse['body']
                )
            );
        }
    }

	public function addOpenTableToListing($postID, $packageID, $aPackageSettings, $aGeneralSettings){
	    if ( isset($aPackageSettings['toggle_open_table']) && ($aPackageSettings['toggle_open_table'] == 'enable') ){
		    include plugin_dir_path(__FILE__) . 'fields/opentable.php';
        }
    }

	public function renderPaymentEndEditButton($post){
        if ( !isset($post->post_status) || ($post->post_status == 'publish') || ($post->post_status == 'pending') ){
            return '';
        }

        if ( !empty(\WilokePublic::$oUserInfo->ID) &&  ($post->post_author == \WilokePublic::$oUserInfo->ID) && ((\WilokePublic::$oUserInfo->role === 'wiloke_submission') || current_user_can('edit_theme_options'))  ) :
            	$addListingPage = \WilokePublic::getPaymentField('addlisting', true);
        ?>
            <div class="listing-single-actions">
                <a href="<?php echo esc_url(\WilokePublic::addQueryToLink($addListingPage, 'post_id='.$post->ID.'&package_id='.self::getPackageID($post->ID))); ?>" class="listgo-btn listgo-btn--sm listgo-btn--round"><i class="fa fa-pencil"></i><span><?php esc_html_e('Edit Listing', 'wiloke'); ?></span></a>
                <a id="wiloke-listgo-submit-listing" data-postid="<?php echo esc_attr($post->ID); ?>" href="#" class="listgo-btn listgo-btn--round listgo-btn--sm btn-primary not-active"><i class="fa fa-send"></i><span><?php esc_html_e('Submit Listing', 'wiloke'); ?></span></a>
            </div>
		<?php
		endif;
    }

	public function removeAddListingSession(){
		\Wiloke::removeSession(self::$passedPreviewKey);
	}

	public static function getPassedPreviewKey(){
		return self::$passedPreviewKey;
	}
	
	public static function renderMessageOnThankYoupage(){
		if ( isset($_REQUEST['token']) && !empty($_REQUEST['token']) ){
			return PayPal::getExpressCheckout($_REQUEST['token']);
		}
		return true;
	}

	/*
	 * User can only see her media / his media
	 */
	public function mediaAccess($aArgs){
		$userID = get_current_user_id();

		if ( !empty($userID) ){
			$aUserMeta = \Wiloke::getUserMeta($userID);
			if ( $aUserMeta['role'] === 'wiloke_submission' ){
				$aArgs['author'] = get_current_user_id();
			}
		}
		return $aArgs;
	}

	public static function afterPluginActivation(){
		if (!wp_next_scheduled ( 'wiloke_submission/automatically_delete_listing' )) {
			wp_schedule_event(time(), 'daily', 'wiloke_submission/automatically_delete_listing');
		}
	}

	public static function isStandingoutBalance(){
		if ( !isset($_REQUEST['package_id']) || empty($_REQUEST['package_id']) ){
			return false;
		}
		$packageID = absint($_REQUEST['package_id']);

		$userID = get_current_user_id();
		if ( empty($userID)  ){
			return false;
		}

		global $wpdb;
		$paymentTblName = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$aPaymentStatus = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT status, ID from $paymentTblName WHERE package_ID=%d AND user_ID=%d AND package_typ=%s ORDER BY ID DESC",
				absint($packageID), $userID, 'pricing'
			),
			ARRAY_A
		);

		if ( isset($aPaymentStatus['status']) && ($aPaymentStatus['status'] === 'processing') ){
			$packageTblName = $wpdb->prefix . AlterTablePackageStatus::$tblName;

			$packageStatus = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT status from $packageTblName WHERE package_ID=%d AND payment_ID=%d",
					absint($packageID), absint($aPaymentStatus['ID'])
				)
			);

			if ( $packageStatus === 'unavailable' ){
				wp_redirect(esc_url(home_url('/')));
				exit();
			}
		}

		return false;
	}

	/**
	 * @since 1.0
	 */
	public function automaticallyDeleteListing(){
		$aWilokeSubmission = array();
		Payment::getPaymentConfiguration();
		if ( !isset(Payment::$aPaymentConfiguration['delete_listing_conditional']) || empty(Payment::$aPaymentConfiguration['delete_listing_conditional']) ){
			return false;
		}

		$query = new \WP_Query(
			array(
				'post_type'       => 'listing',
				'post_status'     => array('processing', 'draft', 'trash', 'auto-draft'),
				'posts_per_page'  => 50
			)
		);

		$compareWith = absint(Payment::$aPaymentConfiguration['delete_listing_conditional'])*60*60*1000;

		if ( $query->have_posts() ){
			while ($query->have_posts()){
				$query->the_post();
				$aUser = \Wiloke::getUserMeta($query->post->post_author);
				if ( in_array($query->post->post_author, $aWilokeSubmission) || (isset($aUser['role']) && ($aUser['role'] === self::$postType)) ){
					$aWilokeSubmission[] = $query->post->post_author;
					if ( strtotime($query->post->post_date) >= $compareWith ){
						$aTerms = \Wiloke::getPostTerms($query->post, 'listing_location');
						if ( !empty($aTerms) && !is_wp_error($aTerms) ){
							foreach ( $aTerms as $oTerm ){
								$authorCreated = get_term_meta($oTerm->term_id, self::$termCreatedByKey, true);
								if ( $authorCreated == $query->post->post_author ){
									$totalPostInTermNow = $this->countTotalListingsInTerm($oTerm);
									if ( $totalPostInTermNow < 2 ){
										wp_delete_term($oTerm->term_id, 'listing_location');
									}
								}
							}
						}
						wp_delete_post($query->post->ID);
					}
				}
			}
		}
		wp_reset_postdata();
	}

	protected function countTotalListingsInTerm($oTerm){
		global $wpdb;
		$tblName = $wpdb->prefix .'term_relationships';

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT($tblName.object_id) FROM $tblName WHERE $tblName.term_taxonomy_id=%d",
				$oTerm->term_id
			)
		);

		return $count;
	}

	/**
	 * Notify payment status to customer
	 * @since 1.0
	 */
	public function notifyCustomerAboutTheirPayment($userID, $paymentID, $paymentStatus, $packageID){
		global $wpdb;
		$tblHistory = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
		$oPaymentInfo = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $tblHistory WHERE ID=%d",
				$paymentID
			)
		);

		if ( empty($oPaymentInfo) ){
			return false;
		}
		$aUserInfo = \Wiloke::getUserMeta($userID);

		if ( !isset($aUserInfo['user_email']) || empty($aUserInfo['user_email']) ){
			return false;
		}

		$instEmail = new SendMail();

		if ( $oPaymentInfo->status === 'pending' || $oPaymentInfo->status === 'completed' || $oPaymentInfo->status == 'Success' ){
			$instEmail->subject = esc_html__('{{wiloke_brandname}} - Your payment has been successfully', 'wiloke');
		}elseif ($oPaymentInfo->status === 'processing'){
			$instEmail->subject = sprintf(esc_html__('{{wiloke_brandname}} - Your order is processing (%s)', 'wiloke'), $oPaymentInfo->ID);
		}else if ($oPaymentInfo->status === 'Failed'){
			$instEmail->subject = sprintf(esc_html__('{{wiloke_brandname}} - Failed Invoice (%s)', 'wiloke'), $oPaymentInfo->ID);
		}

		$instEmail->to = $aUserInfo['user_email'];
		$instEmail->notifyPayment($oPaymentInfo);
	}

	public function autoApproved($postID, $oPostAfter){
		if ( $oPostAfter->post_type !== self::$postType ){
			return false;
		}

		Payment::getPaymentConfiguration();
		if ( empty(Payment::$aPaymentConfiguration) || !isset(Payment::$aPaymentConfiguration['approved_method']) || (Payment::$aPaymentConfiguration['approved_method'] !== 'auto_approved_after_payment') ){
			return false;
		}

		$aUser = \Wiloke::getUserMeta($oPostAfter->post_author);

		if ( $aUser['role'] !== User::$wilokeSubmissionRole ){
			return false;
		}

		if ( $oPostAfter->post_status === 'renew' || $oPostAfter->post_status === 'pending' ){
			wp_update_post(
				array(
					'ID' => $postID,
					'post_status' => 'publish'
				)
			);
		}
	}

	/**
	 * Congratulations! Your Listing has been approved
	 * @since 1.0
	 */
	public function notifyCustomerAboutTheirListing($postID, $oPostAfter, $oPostBefore){
		if ( $oPostAfter->post_type !== self::$postType || !in_array($oPostBefore->post_status, self::$aListingStatuses) ){
			return false;
		}

		$instEmail = new SendMail();

		if ( $oPostAfter->post_status === 'publish' ){
			$aUserInfo = \Wiloke::getUserMeta($oPostBefore->post_author);
			if ( !isset($aUserInfo['user_email']) || empty($aUserInfo['user_email']) ){
				return false;
			}
			$this->resetNotificationTime($oPostBefore->post_author);
			$instEmail->subject = esc_html__('{{wiloke_brandname}} - Your submission has been approved', 'wiloke');
			$instEmail->to = $aUserInfo['user_email'];
			$instEmail->approved($oPostAfter);
		}
	}

	public function resetNotificationTime($userID){
		if ( \Wiloke::$wilokePredis ){
			\Wiloke::$wilokePredis->del(\Wiloke::$prefix.'notification_latest_check|'.$userID);
			delete_option(\Wiloke::$prefix.'notification_latest_check|'.$userID);
		}
	}

	/**
	 * Notify Admin about the newly-submission
	 * @since 1.0
	 */
	public function notifyAdminAboutNewlySubmission(){
		if ( !isset($_REQUEST['customer_id']) || empty($_REQUEST['customer_id']) || !isset($_REQUEST['post_id']) || empty($_REQUEST['post_id']) ){
			return false;
		}

		if ( !\Wiloke::getSession($this->sessisonSubmittedStatus) ){
			return false;
		}

		\Wiloke::removeSession($this->sessisonSubmittedStatus);

		$postStatus = get_post_field('post_status', $_REQUEST['post_id']);
		$postAuthor = get_post_field('post_author', $_REQUEST['post_id']);

		if ( absint($postAuthor) !== absint($_REQUEST['customer_id']) ){
			return false;
		}

		$user = get_userdata($_REQUEST['customer_id']);

		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$oSettings  = get_option(RegisterWilokeSubmission::$submissionConfigurationKey);
		$oSettings  = json_decode($oSettings);
		$brand      = wp_specialchars_decode($oSettings->brandname, ENT_QUOTES);
		if ( $postStatus === 'renew' ){
			$message  = sprintf(__('%s just renew an article on your site %s.'), $user->user_login, $brand) . "\r\n\r\n";
		}else{
			$message  = sprintf(__('%s just submitted a new article on your site %s.'), $user->user_login, $brand) . "\r\n\r\n";
		}
		$message .= sprintf(__('Article title: <a href="%s">%s</a>', 'wiloke'), esc_url(get_permalink($_REQUEST['post_id'])), get_the_title($_REQUEST['post_id'])) . "\r\n";

		$instEmail = new SendMail();
		$instEmail->to = get_option('admin_email');
		$instEmail->subject = sprintf(__('New Submission On %s From %s:', 'wiloke'), $brand, $user->user_login);
		$instEmail->body = $message;
		$instEmail->sendMail();
	}

	/**
	 * Get Package ID
	 * @since 1.0
	 */
	public static function getPackageID($listingID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;
		$packageID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT package_ID FROM $tblName WHERE object_ID=%d",
				$listingID
			)
		);

		return $packageID;
	}

	/**
	 * Get Lasted Submission ID
	 * @since 1.0
	 */
	public static function getLastestSubmissionID($packageID){
		global $wpdb;
		$tblPaymentRelationShip  = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;
		$tblPosts  = $wpdb->prefix . 'posts';

		$postID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT $tblPaymentRelationShip.object_ID FROM $tblPaymentRelationShip INNER JOIN $tblPosts ON ($tblPosts.ID = $tblPaymentRelationShip.object_ID) WHERE $tblPaymentRelationShip.package_ID=%d AND $tblPosts.post_status=%s AND $tblPosts.post_type=%d AND $tblPosts.post_author=%d",
				$packageID, 'processing', 'listing', get_current_user_id()
			)
		);

		return $postID;
	}

	/**
	 * Fetch term
	 * @since 1.0
	 */
	public function fetchTerms(){
		if ( !isset($_GET['term']) || empty($_GET['term']) || !isset($_GET['security']) || check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error();
		}

		if ( !in_array($_GET['term'], $this->aTermAllows) ){
			wp_send_json_error();
		}

		$oTerms = get_terms( array(
			'taxonomy'  => $_GET['term'],
			'hide_empty' => false,
		));

		if ( empty($oTerms) || is_wp_error($oTerms) ){
			wp_send_json_error();
		}

		wp_send_json_success($oTerms);
	}


	/**
	 * Add scripts to the add listing page
	 * @since 1.0
	 */
	public function enqueueScripts(){
		global $post;
		if ( is_page_template('wiloke-submission/addlisting.php') || ( isset($post) && ($post->post_type === 'listing') && ($post->post_status !== 'publish') ) ){
			wp_enqueue_script('backbone');
			wp_enqueue_script('underscore');
			wp_enqueue_media();
			wp_enqueue_script('select2', plugin_dir_url(dirname(__FILE__)) . '../public/asset/select2/js/select2.full.min.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
			wp_enqueue_style('select2', plugin_dir_url(dirname(__FILE__)) . '../public/asset/select2/css/select2.min.css', array(), WILOKE_LISTGO_FC_VERSION);
			wp_enqueue_script('addlisting', plugin_dir_url(dirname(__FILE__)) . '../public/source/js/addlisting.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
			wp_enqueue_style('addlisting', plugin_dir_url(dirname(__FILE__)) . '../public/source/css/addlisting.css', array(), WILOKE_LISTGO_FC_VERSION);

			if ( function_exists('su_query_asset') ){
				su_query_asset( 'css', array( 'simpleslider', 'farbtastic', 'magnific-popup', 'font-awesome' ) );
				su_query_asset( 'js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse', 'simpleslider', 'farbtastic', 'magnific-popup', 'qtip', 'jquery-hotkeys') );

				if ( !wp_script_is('su-generator', 'enqueued') )
				{
					wp_register_script( 'su-generator', WP_PLUGIN_URL . '/shortcodes-ultimate/assets/js/generator.js', array('magnific-popup', 'qtip' ), WILOKE_LISTGO_FC_VERSION, true );
					wp_enqueue_script('su-generator');
					wp_register_style( 'su-generator', WP_PLUGIN_URL . '/shortcodes-ultimate/assets/css/generator.css', array(), WILOKE_LISTGO_FC_VERSION);
					wp_enqueue_style('su-generator');
				}
			}
			wp_enqueue_script('wiloke-mapextend', get_template_directory_uri() . '/admin/source/js/mapextend.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);

			if ( \WilokePublic::addLocationBy() === 'default' ){
				wp_enqueue_style('wiloke-mapextend', get_template_directory_uri() . '/admin/source/css/mapextend.css', array(), WILOKE_LISTGO_FC_VERSION);
			}

			wp_enqueue_script('wiloke-findtableid', get_template_directory_uri() . '/admin/source/js/findtableid.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
		}
	}

	/**
	 * Redirect all Wiloke Submissions to front-end excerpt ajax way
	 * @since 1.0
	 */
	public function redirectWilokeSubmissionToFrontEnd(){
		if ( defined('DOING_AJAX') && DOING_AJAX )
		{
			$isAllowed = true;
		}

		if ( !isset($isAllowed) ){
			$userID = get_current_user_id();
			$oUserData = get_userdata($userID);

			if ( in_array(User::$wilokeSubmissionRole, $oUserData->roles) ){
				$aPaymentSettings = get_option(RegisterWilokeSubmission::$submissionConfigurationKey);
				$aPaymentSettings = !empty($aPaymentSettings) ? json_decode(stripslashes($aPaymentSettings), true) : '';

				if ( isset($aPaymentSettings['myaccount']) ){
						wp_redirect(get_permalink($aPaymentSettings['myaccount']));
					exit();
				}
			}
		}
	}

	/**
	 * If you are wiloke submission, you are not allow to edit a pending status
	 * @since 1.0
	 */
	public function refuseEditPendingStatus($postID){
		Payment::getPaymentConfiguration();
		if ( !empty(Payment::$aPaymentConfiguration) && isset(Payment::$aPaymentConfiguration['approved_method']) && (Payment::$aPaymentConfiguration['approved_method'] === 'auto_approved_after_payment') ){
			return true;
		}

		$postStatus = get_post_field('post_status', $postID);

		if ( $postStatus === 'processing' ){
			return true;
		}

		$userID     = get_current_user_id();
		$oUserData  = get_userdata($userID);

		if ( in_array(User::$wilokeSubmissionRole, $oUserData->roles) ){
			if ( $postStatus === 'pending' || $postStatus === 'renew' ){
				$oError = new \WP_Error( 'broke', esc_html__( 'You can not edit this article while we are reviewing.', 'wiloke' ) );
//				echo $oError->get_error_message();
				return false;
			}

			if ( $postStatus === 'publish' ){
				$oError = new \WP_Error( 'broke', esc_html__( 'You do not permission to edit this article. Please contact admin to make a deal.', 'wiloke' ) );
//				echo $oError->get_error_message();
				return false;
			}
		}

		return true;
	}

	/**
	 * Adding custom post statuses for the listing type
	 * @since 1.0
	 */
	public function addListingStatus(){
		global $WilokeListGoFunctionalityApp;
		foreach ($WilokeListGoFunctionalityApp['post_statuses'] as $postStatus => $aConfig){
			register_post_status($postStatus, $aConfig);
		}
	}

	private function authentication(){
		if ( !isset($this->aData['package_id']) || empty($this->aData['package_id']) ){
			return array(
				'status' => 'error',
				'msg'    => esc_html__('You need to select a package before', 'wiloke')
			);
		}

		if ( get_post_field('post_status', $this->aData['package_id']) !== 'publish' ){
			return array(
				'status' => 'error',
				'msg'    => esc_html__('The package does not exist', 'wiloke')
			);
		}

		$aPackageInfo = \Wiloke::getPostMetaCaching($this->aData['package_id'], 'pricing_settings');

		if ( empty($aPackageInfo) ){
			return array(
				'status' => 'error',
				'msg'    => esc_html__('The package does not exist', 'wiloke')
			);
		}

		$aUserInformation = CustomerPlan::getCustomerPlan(true);
		if ( empty($aUserInformation) ){
			## Adding a new record to payment history table if it's a free package
			if ( empty($aPackageInfo['price']) ){
				if ( $this->isExceededFreeListing($this->aData['package_id'], $aPackageInfo, $this->aData['post_id']) ){
					return array(
						'status' => 'error',
						'msg'    => esc_html__('You have exceeded the number of listings for this plan. Please upgrade to higher plan to continue adding the listing', 'wiloke')
					);
				}else{
					$this->paymentID = FreePost::insertPaymentHistory($this->aData['package_id'], $this->_userID);
				}
			}
			$this->billingType = 'Free';

			return array(
				'status' => 'success'
			);
		}

		if ( Payment::getBillingType() !== 'None' ){
			if ( $aUserInformation['packageID'] != $this->aData['package_id'] ){
				return array(
					'status' => 'error',
					'msg'    => esc_html__('Wrong package plan.', 'wiloke')
				);
			}

			if ( !empty($aPackageInfo['number_of_listings']) && !$this->isExceededListings($aPackageInfo) ){
				return array(
					'status' => 'error',
					'msg'    => esc_html__('You have exceeded the number of listings for this plan. Please upgrade to higher plan to continue adding the listing', 'wiloke')
				);
			}

			if ( CustomerPlan::isFreePlan($aUserInformation) ){
				$this->billingType = 'Free';
			}elseif ( CustomerPlan::isNonRecurringPlan($aUserInformation) ){
				$this->billingType = 'None';
			}else{
				$this->billingType = 'RecurringPayments';
			}

			return array(
				'status' => 'success'
			);
		}else{
			## Only check if it's free package and the package is limited number of listing
			if ( empty($aPackageInfo['price']) && $this->isExceededFreeListing($this->aData['package_id'], $aPackageInfo, $this->aData['post_id']) ){
				return array(
					'status' => 'error',
					'msg'    => esc_html__('You have exceeded the number of listings for this plan. Please upgrade to higher plan to continue adding the listing', 'wiloke')
				);
			}
			$this->billingType = 'None';
			return array(
				'status' => 'success'
			);
		}
	}

	private function isExceededFreeListing($packageID, $aPackageInfo, $postID=null){
		global $wpdb;

		if ( empty($aPackageInfo['number_of_posts']) ){
			return false;
		}

		if ( !empty($postID) ){
			return false;
		}

		$packageStatusTbl = $wpdb->prefix . AlterTablePackageStatus::$tblName;

		$status = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT status FROM $packageStatusTbl  WHERE $packageStatusTbl.package_ID=%d AND $packageStatusTbl.user_ID=%d",
				$packageID, get_current_user_id()
			)
		);

		return ($status === 'unavailable');
	}

	private function isExceededListings($aPackageInfo){
		$myRemainingListing = \Wiloke::getSession(CustomerPlan::getMyRemainListingKey());
		if ( empty($this->aData['post_id']) ){
			if ( !empty($aPackageInfo['number_of_posts']) && $myRemainingListing <= 0 ){
				return true;
			}

			return false;
		}else{
			return get_post_field('post_author', $this->aData['post_id'] == $this->_userID);
		}
	}

	/*
	 * There are two kind of billingType: Free, None (NonRecurring Payment) and RecurringPayments
	 * Note that it's author billingType not Wiloke Submission Settings
	 */
	private function getPackageAvailable(){
		global $wpdb;
		$tblStatus = $wpdb->prefix . AlterTablePackageStatus::$tblName;
		$relationShipTbl = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;
		$aCustomerPlan = CustomerPlan::getCustomerPlan();
		if ( $this->billingType == 'RecurringPayments' ){
			Payment::$latestPaymentID = isset($aCustomerPlan['paymentID']) ? $aCustomerPlan['paymentID'] : 0;
		}else{
			if ( empty($this->paymentID) ){
				if ( empty($this->aData['post_id']) ){
					$aResult = $wpdb->get_row(
						$wpdb->prepare(
							"SELECT package_ID, payment_ID FROM $tblStatus WHERE status=%s AND user_ID=%d AND package_ID=%d ORDER BY payment_ID DESC",
							"available", $this->_userID, $this->aData['package_id']
						),
						ARRAY_A
					);
					if ( !empty($aResult) ){
						if ( $aResult['status'] !== 'available' ){
							$this->aErrorSubmitting = array(
								esc_html__('OOps! You have used all of free listings. Please purchasing a premium package to continue adding listing.', 'wiloke')
							);
						}else{
							Payment::$latestPaymentID = $aResult['payment_ID'];
						}
					}
				}else{
					Payment::$latestPaymentID = $wpdb->get_var(
						$wpdb->prepare(
							"SELECT payment_ID FROM $relationShipTbl WHERE package_ID=%s AND object_ID=%d ORDER BY payment_ID DESC",
							$this->aData['package_id'], $this->aData['post_id']
						)
					);
				}
			}else{
				Payment::$latestPaymentID = $this->paymentID;
			}
		}
	}

	/**
	 * Insert Media
	 * @since 1.0
	 */
	public function prepareInsertMedia(){
		if ( !isset($_GET['security']) || !wp_verify_nonce($_GET['security'], 'wiloke-nonce') ){
			wp_send_json_error(
				array(
					$_GET['where'] => esc_html__('Security code is wrong. This upload has been rejected.', 'wiloke')
				)
			);
		}

		$this->_allowSize = ini_get('max_file_uploads');
		$this->_allowSize = str_replace('M', '', $this->_allowSize);
		$this->_allowSize = absint($this->_allowSize)*1024*1024;

		if ( $_GET['type'] === 'single' ){
			$attachtID = $this->_uploadImg($_FILES[$_GET['name']]);
			if ( is_wp_error($attachtID) ){
				wp_send_json_error(
					array(
						$_GET['where'] => $attachtID->get_error_message()
					)
				);
			}

			wp_send_json_success(array(
				'message' => $attachtID
			));
		}else{
			$aFiles = $_FILES[$_GET['name']];
			foreach ( $aFiles['size'] as $key => $name ){
				$aFileUpload = array(
					'name'     => $aFiles['name'][$key],
					'type'     => $aFiles['type'][$key],
					'tmp_name' => $aFiles['tmp_name'][$key],
					'error'    => $aFiles['error'][$key],
					'size'     => $aFiles['size'][$key],
				);
				$attachID = $this->_uploadImg($aFileUpload);
				if ( !is_wp_error($attachID) && !empty($attachID) ){
					$aAttachIDs[] = $attachID;
				}
			}

			if ( empty($aAttachIDs) ){
				wp_send_json_error(
					array(
						'message' => esc_html__('There are no files have been uploaded', 'wiloke')
					)
				);
			}else{
				wp_send_json_success(
					array(
						'message' => implode(',', $aAttachIDs)
					)
				);
			}
		}
	}

	/**
	 * Checking File
	 * @since 1.0
	 */
	private function _uploadImg($aFile){
		$aConditionals = array('image/jpeg', 'image/png', 'image/gif', 'image/jpg');
		if ( empty($aFile['error']) && ($aFile['size'] <= $this->_allowSize) ){
			if ( in_array($aFile['type'], $aConditionals) ){
				$attachID = $this->_insertAttachment($aFile);
				return $attachID;
			}
		}
	}

	/**
	 * Processing Insert Attachment
	 * @since 1.0
	 */
	protected function _insertAttachment($aFile, $parentPostID=0){
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$upload_overrides = array('test_form' => false);
		$aMoveFile = wp_handle_upload($aFile, $upload_overrides );

		// Check the type of file. We'll use this as the 'post_mime_type'.
		$fileType = wp_check_filetype( basename($aMoveFile['file']), null );

		// Get the path to the upload directory.
		$wp_upload_dir = wp_upload_dir();

		// Prepare an array of post data for the attachment.
		$attachment = array(
			'guid'           => $wp_upload_dir['url'] . '/' . basename($aMoveFile['file']),
			'post_mime_type' => $fileType['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename($aMoveFile['file']) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);
		// Insert the attachment.
		$attachID = wp_insert_attachment($attachment, $aMoveFile['file'], $parentPostID);

		if ( empty($attachID) ){
			return new \WP_Error('broke', esc_html__('We regret to say that this file could not upload. The possible reason: Wrong file type or the file size is bigger than the allowance value', 'wiloke'));
		}

		// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		require_once(ABSPATH . 'wp-admin/includes/image.php');

		// Generate the metadata for the attachment, and update the database record.
		$aAttachData = wp_generate_attachment_metadata($attachID, $aMoveFile['file']);
		wp_update_attachment_metadata($attachID, $aAttachData);
		return $attachID;
	}

	public function editPublishedListing(){
		$errorMsg  = esc_html__('You do not have permission to access this page', 'wiloke');
		if ( !isset($_POST['listingID']) || empty($_POST['listingID']) ){
			wp_send_json_error(
				array(
					'msg' => $errorMsg
				)
			);
		}

		if ( !isset($_POST['security']) || empty($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error(
				array(
					'msg' => $errorMsg
				)
			);
		}

		if ( !self::isEditingPublishedListing($_POST['listingID']) ){
			wp_send_json_error(
				array(
					'msg' => $errorMsg
				)
			);
		}

		parse_str($_POST['data'], $this->aData);

		$this->aData['post_id'] = $_POST['listingID'];
		$editingType = FrontendListingManagement::publishedListingEditable();

		if ( $editingType == 'allow_need_review' ){
			$aData['post_status'] = 'pending';
		}

		$this->aData['content'] = wp_kses_post($_POST['content']);

		$updatedStatus = $this->_updateListing();

		if ( $updatedStatus ){
			wp_send_json_success(
				array(
					'redirect' => urlencode(get_permalink($this->aData['post_id'])),
					'msg' => esc_html__('Congratulations! Your listing has been updated.', 'wiloke')
				)
			);
		}else{
			wp_send_json_error(
				array(
					'msg' => esc_html__('Something went wrong', 'wiloke')
				)
			);
		}
	}

	/**
	 * Handle Submission
	 * @since 1.0
	 */
	public function handleSubmit(){
		session_start();
		global $wiloke, $wpdb;
		if (  !isset($_POST['security']) || empty($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error(
				array(
					'message' => $wiloke->aConfigs['translation']['securitycodewrong']
				)
			);
		}

		if ( !isset($_POST['post_id']) || empty($_POST['post_id']) ){
			wp_send_json_error(
				array(
					'message' => esc_html__('The listing does not exist.', 'wiloke')
				)
			);
		}

		if ( \Wiloke::getSession(self::$passedPreviewKey) != $_POST['post_id'] ){
			wp_send_json_error(
				array(
					'message' => esc_html__('You do not permission to access this page', 'wiloke')
				)
			);
		}

		$postAuthor = get_post_field('post_author', $_POST['post_id']);
		$userID  = get_current_user_id();

		if ( absint($userID) !== absint($postAuthor) ){
			wp_send_json_error(
				array(
					'message' => esc_html__('You do not have permission to submit this post.', 'wiloke')
				)
			);
		}

		$packageID = \Wiloke::getSession(Payment::$packageIDSessionKey);

		if ( get_post_field('post_status', $packageID) !== 'publish' ){
			wp_send_json_error(
				array(
					'message'    => esc_html__('The package does not exist', 'wiloke')
				)
			);
		}

		$aPackageInfo = \Wiloke::getPostMetaCaching($packageID, 'pricing_settings');

		if ( empty($aPackageInfo) ){
			return array(
				'status' => 'error',
				'message'    => esc_html__('The package does not exist', 'wiloke')
			);
		}

		\Wiloke::setSession(self::$listingIDSessionKey, $_POST['post_id']);
		Payment::getPaymentConfiguration();

		if ( empty(Payment::$aPaymentConfiguration) ){
			wp_send_json_error(
				array(
					'message' => esc_html__('Payment Service need configuring, please contact the administrator to report this issue', 'wiloke')
				)
			);
		}

		$checkoutPage = get_permalink(Payment::$aPaymentConfiguration['checkout']);

		if ( strpos($checkoutPage, '?') === false ){
			$checkoutPage = $checkoutPage . '?package_id=' . $packageID;
		}else{
			$checkoutPage = $checkoutPage . '&package_id=' . $packageID;
		}

		$checkoutPage .= "&post_id=".$_POST['post_id'];
		\Wiloke::setSession($this->sessisonSubmittedStatus, 1);

		if ( Payment::getBillingType() === 'None' ){
			$tblPackageRelationship = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;

			$aPackageAndPayment = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT package_ID, payment_ID FROM $tblPackageRelationship WHERE object_ID=%d AND status != 'expired' ORDER BY payment_ID DESC",
					$_POST['post_id']
				),
				ARRAY_A
			);

			if ( empty($aPackageAndPayment) ){
				wp_send_json_error(
					array(
						'message' => esc_html__('You need to be selected a package before', 'wiloke')
					)
				);
			}

			if ( empty($aPackageInfo['price']) ){
				if ( $this->isExceededFreeListing($packageID, $aPackageInfo, $_POST['post_id']) ){
					wp_send_json_error(
						array(
							'message' => esc_html__('You have exceeded the number of listings for this package. Please upgrade your package to continue adding listing.', 'wiloke')
						)
					);
				}else{
					if ( !FreePost::isPackageExists($packageID, false) ){
						$aPackageAndPayment['payment_ID'] = FreePost::insertPaymentHistory($packageID);
					}
				}
			}else{
				$tblPaymentHistory = $wpdb->prefix . AlterTablePaymentHistory::$tblName;
				$status = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT status FROM $tblPaymentHistory WHERE ID=%d AND user_ID=%d AND package_ID=%d AND package_type=%s",
						$aPackageAndPayment['payment_ID'], $userID, $aPackageAndPayment['package_ID'], 'pricing'
					)
				);

				if ( strtolower($status) !== 'success' && $status !== 'pending' && $status !== 'completed' && $status !== 'processing' && $status !== 'freelisting' ){
					wp_send_json_success(
						array(
							'redirect' => urlencode($checkoutPage)
						)
					);
				}
			}


			Payment::$latestPaymentID = $aPackageAndPayment['payment_ID'];
		}else{
			$aCustomerPlan = CustomerPlan::getCustomerPlan(true);
			if ( empty($aCustomerPlan)  ){
				wp_send_json_success(
					array(
						'redirect' => urlencode($checkoutPage)
					)
				);
			}else{
				if ( $aCustomerPlan['packageID'] != $packageID ){
					wp_send_json_error(
						array(
							'message' => esc_html__('Wrong Package Plan.', 'wiloke')
						)
					);
				}

				if ( \Wiloke::getSession(CustomerPlan::getMyRemainListingKey()) < 0 ){
					wp_send_json_error(
						array(
							'message' => esc_html__('You have exceeded the number of listings for this package. Please upgrade your package to continue adding listing.', 'wiloke')
						)
					);
				}

				Payment::$latestPaymentID = $aCustomerPlan['paymentID'];
			}
		}

		\Wiloke::setSession(self::$listingIDSessionKey, $_POST['post_id']);
		Payment::_updatePaymentRelationships($packageID);
		\Wiloke::removeSession(self::$listingIDSessionKey);

		$current    = get_post_field('post_status', $_POST['post_id']);
		$newStatus  = $current === 'expired' ? 'renew' : 'pending';
		wp_update_post(
			array(
				'post_type'	  => 'listing',
				'ID'          => $_POST['post_id'],
				'post_status' => $newStatus,
				'post_author' => $userID
			)
		);

		$aPricingSettings = \Wiloke::getPostMetaCaching($_POST['post_id'], 'pricing_settings');
		update_post_meta($_POST['post_id'], 'wiloke_submission_do_not_show_on_map', $aPricingSettings['publish_on_map']);
		update_post_meta($_POST['post_id'], 'listing_claim', array('status'=>'claimed'));

		## Add Featured Listing
		if ( isset($aPricingSettings['toggle_add_feature_listing']) && ($aPricingSettings['toggle_add_feature_listing'] == 'enable') ){
			update_post_meta($_POST['post_id'], 'wiloke_listgo_toggle_highlight', 1);
		}else{
			update_post_meta($_POST['post_id'], 'wiloke_listgo_toggle_highlight', 0);
		}

		$thankyouUrl = get_permalink(Payment::$aPaymentConfiguration['thankyou']);
		\Wiloke::removeSession(self::$passedPreviewKey);
		wp_send_json_success(
			array(
				'redirect' => urlencode(\WilokePublic::addQueryToLink($thankyouUrl, 'customer_id='.get_current_user_id().'&post_id='.$_POST['post_id']))
			)
		);
	}

	/**
	 * Handle Preview
	 * @since 1.0
	 */
	public function handlePreview(){
		session_start();
		global $wiloke;
		if ( !is_user_logged_in() ){
			if (  !isset($_POST['security']) || empty($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
				wp_send_json_error(
					array(
						'message' => $wiloke->aConfigs['translation']['securitycodewrong']
					)
				);
			}
		}
		parse_str($_POST['data'], $this->aData);

		$this->aData['post_id'] = $this->aData['listing_id'];
		unset($this->aData['listing_id']);

		if ( !is_user_logged_in() ){
			if ( isset($this->aData['createaccount']) && ($this->aData['createaccount'] === 'on') ){
			    $aVerifyEmail = User::verifyEmail($this->aData['wiloke_reg_email']);
			    if ( !$aVerifyEmail['success'] ){
				    wp_send_json_error(array(
					    'wiloke-reg-invalid-email' => $aVerifyEmail['message']
				    ));
                }

				if ( isset($wiloke->aThemeOptions['toggle_google_recaptcha']) && ($wiloke->aThemeOptions['toggle_google_recaptcha'] == 'enable') ){
					$aVerifiedreCaptcha = User::verifyGooglereCaptcha($this->aData['g-recaptcha-response']);
					if ( $aVerifiedreCaptcha['status'] == 'error' ){
						wp_send_json_error(array(
							'reject' => $aVerifiedreCaptcha['message']
						));
					}
					unset($this->aData['g-recaptcha-response']);
				}

				$aResult = User::createUserByEmail($this->aData['wiloke_reg_email'], $this->aData['wiloke_reg_password']);

				if ( $aResult['success'] === false ){
					wp_send_json_error(array(
						'wiloke-reg-invalid-email' => $aResult['message']
					));
				}else{
					$this->_userID = $aResult['message'];
				}
			}else{
				$aResult = User::signOn($this->aData['wiloke_user_login'], $this->aData['wiloke_my_password']);
				if ( $aResult['success'] === false ){
					wp_send_json_error(array(
						'wiloke-signup-failured' => $aResult['message']
					));
				}else{
					$this->_userID = absint($aResult['message']);
				}
			}
			Payment::$userID = $this->_userID;
		}else{
			$this->_userID = get_current_user_id();
		}

		if ( empty($this->_userID) ){
			wp_send_json_error(array(
				'wiloke-user-login' => esc_html__('You entered a wrong email or wrong password', 'wiloke'),
				'wiloke-reg-email'  => esc_html__('Please enter an email address to sign up.', 'wiloke'),
			));
		}

		if ( !isset($this->aData['listing_title']) || empty($this->aData['listing_title']) ){
			wp_send_json_error(array(
				'listing_title' => $wiloke->aConfigs['translation']['titlerequired']
			));
		}

		$aAuthentication = $this->authentication();

		## It's very important. If user's plan is available still. They don't need to redirect to checkout page.
		$this->getPackageAvailable();
		if ( $aAuthentication['status'] == 'error' ){
			wp_send_json_error(array(
				'reject' => $aAuthentication['msg']
			));
		}

		if ( !isset($_POST['content']) || empty($_POST['content']) ){
			wp_send_json_error(array(
				'listing_content' => $wiloke->aConfigs['translation']['contentrequired']
			));
		}

		$this->aData['content'] = $_POST['content'];
		$this->aData['acfData'] = $_POST['acfData'];

		if ( isset($this->aData['post_id']) && !empty($this->aData['post_id']) ){
			$authorID = get_post_field('post_author',$this->aData['post_id'] );
			if ( absint($authorID) !== absint($this->_userID) ){
				wp_send_json_error( array(
					'reject' => $wiloke->aConfigs['translation']['deninedsubmission']
				) );
			}

			if ( get_post_field('post_type', $this->aData['post_id']) != 'listing' ){
				wp_send_json_error( array(
					'reject' => $wiloke->aConfigs['translation']['deninedsubmission']
				) );
            }
		}

		$processStatus = true;

		if ( !empty($this->aData['post_id']) ){
			$postStatus = get_post_field('post_status', $this->aData['post_id'] );
			if ( $postStatus === 'pending' ){
				wp_send_json_error( array(
					'reject' => $wiloke->aConfigs['translation']['isreviewing']
				));
			}else{
				$processStatus = $this->_updateListing();
			}
		}else{
			$processStatus = $this->_insertListing();
		}

		if ( !$processStatus ){
			wp_send_json_error( array(
				'reject' => $wiloke->aConfigs['translation']['somethingwrong']
			));
		}

		\Wiloke::setSession(self::$passedPreviewKey, $this->listingID);
		wp_send_json_success(array('next'=>get_permalink($this->listingID)));
	}

	public function getCheckoutUrl(){
		$aPaymentSettings = get_option(RegisterWilokeSubmission::$submissionConfigurationKey);
		$aPaymentSettings = !empty($aPaymentSettings) ? json_decode(stripslashes($aPaymentSettings), true) : '';

		if ( !isset($aPaymentSettings['checkout']) || empty($aPaymentSettings['checkout']) ){
			return false;
		}else{
			$checkoutPage =  get_permalink($aPaymentSettings['checkout']);
			$checkoutPage .= strpos($checkoutPage, '?') !== false ? "&package_id=".$this->aData['package_id'] : "?package_id=".$this->aData['package_id'];
			return $checkoutPage;
		}
	}

	public function getThankYouPageUrl(){
		$aPaymentSettings = get_option(RegisterWilokeSubmission::$submissionConfigurationKey);
		$aPaymentSettings = !empty($aPaymentSettings) ? json_decode(stripslashes($aPaymentSettings), true) : '';

		if ( !isset($aPaymentSettings['thankyou']) || empty($aPaymentSettings['thankyou']) ){
			return false;
		}else{
			$thanksPage =  get_permalink($aPaymentSettings['thankyou']);
			$thanksPage .= strpos($thanksPage, '?') !== false ? "&wiloke_mode=remaining" : "?wiloke_mode=remaining" ;
			return $thanksPage;
		}
	}

	public static function getPricingField($field='', $postID){
		$aSettings = \Wiloke::getPostMetaCaching($postID, 'pricing_settings');

		if ( !empty($field) ){
			return isset($aSettings[$field]) ? $aSettings[$field] : '';
		}

		return $aSettings;
	}

	private function updateACFCustomFields(){
        if ( empty($this->aData['acfData']) ){
            return true;
        }
		$aPackageSettings = \Wiloke::getPostMetaCaching($this->aData['package_id'], 'pricing_settings');

		if ( !isset($aPackageSettings['afc_custom_field']) || empty($aPackageSettings['afc_custom_field']) ){
			return false;
		}

		if ( (get_post_field('post_type', $aPackageSettings['afc_custom_field']) != 'acf') || (get_post_field('post_status', $aPackageSettings['afc_custom_field']) != 'publish') ){
			return false;
		}

        $aCustomFields = json_decode(stripslashes($this->aData['acfData']), true);
		$this->listingID = abs($this->listingID);
        foreach ( $aCustomFields as $key => $val ){
            update_field($key, $val, $this->listingID);
        }
    }

	private function updateOpenTableData($aPricingSettings, $postID){
		if ( isset($aPricingSettings['toggle_open_table']) && ($aPricingSettings['toggle_open_table'] == 'enable') ){
			if ( isset($this->aData['listing_open_table_settings']) ){
				$aOpenTableData = array();
				foreach ($this->aData['listing_open_table_settings'] as $key => $val){
					$aOpenTableData[sanitize_text_field($key)] =  sanitize_text_field($val);
				}
				update_post_meta($postID, 'listing_open_table_settings', $aOpenTableData);
			}
		}
	}

	private function _updatePostMeta($postID){
		update_post_meta($postID, self::$packageIDOfListing, $this->aData['package_id']);
		$aPackageSettings = \Wiloke::getPostMetaCaching($this->aData['package_id'], 'pricing_settings');

		$aListingSettings['map']['location']  = $this->aData['listing_address'];
		$aListingSettings['map']['latlong']   = $this->aData['listing_latlng'];
		$aListingSettings['phone_number']     = $this->aData['listing_phonenumber'];
		$aListingSettings['website']          = $this->aData['listing_website'];

		update_post_meta($postID, 'listing_settings', $aListingSettings);
		update_post_meta($postID, 'listgo_listing_latlong', $aListingSettings['map']['latlong']);

		if ( !isset($aPackageSettings['toggle_allow_add_gallery']) || ($aPackageSettings['toggle_allow_add_gallery']  === 'enable') ){
			if ( isset($this->aData['listing_gallery']) && !empty($this->aData['listing_gallery']) ){
				$aGallery = array_map('absint', explode(',', $this->aData['listing_gallery']));
				$aImages = array();
				foreach ( $aGallery as $galleryID ){
					if ( !empty($galleryID) ){
						$aImages[$galleryID] = wp_get_attachment_image_url($galleryID);
					}
				}
				$aListOfImages['gallery'] = $aImages;
				update_post_meta($postID, 'gallery_settings', $aListOfImages);
			}else{
				update_post_meta($postID, 'gallery_settings', false);
			}
		}

		$this->updateOpenTableData($aPackageSettings, $postID);

		update_post_meta($postID, 'listing_price', $this->aData['listing_price']);
		update_post_meta($postID, 'listing_social_media', $this->aData['listing']['social']);

		$toggleBSH = isset($this->aData['toggle_business_hours']) ? $this->aData['toggle_business_hours'] : 'disable';
		update_post_meta($postID, 'wiloke_toggle_business_hours', $toggleBSH);

		$aBusinessHours = apply_filters('wiloke/wiloke-listgo-functionality/app/submit/addlisting', $this->aData['listgo_bh']);
		update_post_meta($postID, 'wiloke_listgo_business_hours', $aBusinessHours);

		set_post_thumbnail($postID, $this->aData['featured_image']);
	}

	/*
	 * After Updated Claim Listing
	 * @since 1.0
	 */
	public function afterUpdatedClaimListing($aData, $userID){
		$this->_userID = $userID;
		$this->aData = $aData;
		if ( \WilokePublic::addLocationBy() == 'default' ){
			$this->insertLocationByDefault($this->aData['post_id']);
		}else{
			$this->insertLocationByGoogle($this->aData['post_id']);
		}
	}

	private function _setPostTerms($postID){
		if ( \WilokePublic::addLocationBy() == 'default' ){
			$this->insertLocationByDefault($postID);
		}else{
			$this->insertLocationByGoogle($postID);
		}

		$aListingCats = $this->aData['listing_cats'];
		foreach ( $aListingCats as $key => $termID  ){
			$termID = absint($termID);
			if ( !term_exists($termID, 'listing_cat') ){
				unset($aListingCats[$termID]);
			}else{
				$aListingCats[$key] = $termID;
			}
		}
		wp_set_post_terms($postID, $aListingCats, 'listing_cat', true);

		if ( !empty($this->aData['listing_tags']) ){
			wp_set_post_terms($postID, $this->aData['listing_tags'], 'listing_tag', false);
		}
	}

	public function convertNameToSlug($slug, $isReplaceDash=false){
		$slug = sanitize_title($slug);
		return $isReplaceDash ? str_replace('-', '', $slug) : $slug;
	}

	private function insertLocationByDefault($postID){
		$listingLocation = absint($this->aData['listing_location']);
		if ( term_exists($listingLocation, 'listing_location') ){
			wp_set_post_terms($postID, $listingLocation, 'listing_location', true);
		}
	}

	protected function findLocationByName($aName){
		$aLocation = get_term_by('slug', $this->convertNameToSlug($aName['long_name']), 'listing_location');
		if ( !empty($aLocation) && !is_wp_error($aLocation) ){
			return $aLocation;
		}

		$aLocation = get_term_by('slug', $this->convertNameToSlug($aName['long_name'], true), 'listing_location');
		if ( !empty($aLocation) && !is_wp_error($aLocation) ){
			return $aLocation;
		}

		$aLocation = get_term_by('slug', $this->convertNameToSlug($aName['short_name']), 'listing_location');
		if ( !empty($aLocation) && !is_wp_error($aLocation) ){
			return $aLocation;
		}

		$aLocation = get_term_by('slug', $this->convertNameToSlug($aName['short_name'], true), 'listing_location');
		if ( !empty($aLocation) && !is_wp_error($aLocation) ){
			return $aLocation;
		}

		return false;
	}

	protected function findLocationByPlaceID($name){
		global $wiloke;
		$url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?input='.$name.'&types=geocode&key='.$wiloke->aThemeOptions['general_map_api'];

		$aPlaceInfo = wp_remote_get(esc_url_raw($url));

		if ( is_wp_error($aPlaceInfo) ){
			return false;
		}

		$aPlaceInfo = wp_remote_retrieve_body( $aPlaceInfo );
		$aPlaceInfo = json_decode($aPlaceInfo, true);

		if ( !isset($aPlaceInfo['predictions']) || empty($aPlaceInfo['predictions']) ) {
			return false;
		}

		$aTerms = get_terms(array(
			'taxonomy'   => 'listing_location',
			'hide_empty' => false,
			'meta_query' => array(
				array(
					'key'       => 'wiloke_listing_location_place_id',
					'value'     => $aPlaceInfo['predictions'][0]['place_id'],
					'compare'   => '='
				)
			)
		));

		if ( empty($aTerms) || is_wp_error($aTerms) ){
			return false;
		}

		return array(
			'term'      => $aTerms[0],
			'term_id'   => $aTerms[0]->term_id,
			'place_id'  => $aPlaceInfo['body']['predictions'][0]['place_id']
		);
	}

	private function maybeTermCountryExisting(){
		$oParent = get_term_by('slug', $this->convertNameToSlug($this->aCountry['long_name']), 'listing_location');
		if ( !empty($oParent) && !is_wp_error($oParent) ){
			$this->countryLocationID = $oParent->term_id;
			return true;
		}

		$oParent = get_term_by('slug', $this->convertNameToSlug($this->aCountry['long_name'], true), 'listing_location');
		if ( !empty($oParent) && !is_wp_error($oParent) ){
			$this->countryLocationID = $oParent->term_id;
			return true;
		}

		$oParent = get_term_by('slug', $this->convertNameToSlug($this->aCountry['short_name']), 'listing_location');
		if ( !empty($oParent) && !is_wp_error($oParent) ){
			$this->countryLocationID = $oParent->term_id;
			return true;
		}

		$oParent = get_term_by('slug', $this->convertNameToSlug($this->aCountry['short_name'], true), 'listing_location');
		if ( !empty($oParent) && !is_wp_error($oParent) ){
			$this->countryLocationID = $oParent->term_id;
			return true;
		}

		$aParent = $this->findLocationByPlaceID($this->aCountry['long_name']);

		if ( $aParent ){
			$this->countryLocationID = $oParent['term_id'];
			return true;
		}

		return false;
	}

	private function maybeTermParentExisting(){
		$oParent = get_term_by('slug', $this->convertNameToSlug($this->administratorLevel1['long_name']), 'listing_location');
		if ( !empty($oParent) && !is_wp_error($oParent) ){
			$this->parentLocationID = $oParent->term_id;
			return true;
		}

		$oParent = get_term_by('slug', $this->convertNameToSlug($this->administratorLevel1['long_name'], true), 'listing_location');
		if ( !empty($oParent) && !is_wp_error($oParent) ){
			$this->parentLocationID = $oParent->term_id;
			return true;
		}

		$oParent = get_term_by('slug', $this->convertNameToSlug($this->administratorLevel1['short_name']), 'listing_location');
		if ( !empty($oParent) && !is_wp_error($oParent) ){
			$this->parentLocationID = $oParent->term_id;
			return true;
		}

		$oParent = get_term_by('slug', $this->convertNameToSlug($this->administratorLevel1['short_name'], true), 'listing_location');
		if ( !empty($oParent) && !is_wp_error($oParent) ){
			$this->parentLocationID = $oParent->term_id;
			return true;
		}

		$aParent = $this->findLocationByPlaceID($this->administratorLevel1['long_name']);

		if ( $aParent ){
			$this->parentLocationID = $oParent['term_id'];
			return true;
		}

		return false;
	}

	private function parseLocationCategoryName(){
		if ( $this->aParsePlaceInfo['address_components'][0]['types'][0] === 'administrative_area_level_2' ){
			return $this->aParsePlaceInfo['address_components'][0];
		}else{
			foreach ( $this->aParsePlaceInfo['address_components'] as $aPolitical ){
				if ( $aPolitical['types'][0] === 'administrative_area_level_2' ){
					return $aPolitical;
					break;
				}

				if($aPolitical['types'][0] === 'administrative_area_level_1'){
					$this->administratorLevel1 = $aPolitical;
				}
			}
		}

		if ( !empty($this->administratorLevel1) ){
			return $this->administratorLevel1;
		}

		$total = count($this->aParsePlaceInfo['address_components']);
		if ( $total < 3 ){
			return $this->aParsePlaceInfo['address_components'][0];
		}else{
			return $this->aParsePlaceInfo['address_components'][$total-2];
		}
	}

	private function setMenuOrder($packageID){
		if ( empty($packageID) ){
			return 0;
		}
		$aPackageData = \Wiloke::getPostMetaCaching($packageID, 'pricing_settings');
		$price = isset($aPackageData['price']) ? absint($aPackageData['price']) : 0;

		$this->menuOrder = 100000000*($price)*($this->menuOrder);
		return $this->menuOrder;
	}

	private function insertNewTerm($name){
		if ( !empty($this->parentLocationID) && !empty($this->administratorLevel1) ){
			$aNewTerm = wp_insert_term( $this->administratorLevel1['long_name'], 'listing_location', array(
				'parent' => $this->countryLocationID
			));
			$this->parentLocationID = $aNewTerm['term_id'];
		}

		$aNewTerm = wp_insert_term( $name, 'listing_location', array(
			'parent' => $this->parentLocationID
		));
		$this->locationID = $aNewTerm['term_id'];

		\Wiloke::updateOption('_wiloke_cat_settings_'.$this->locationID, array(
			'placeid' => $this->placeID
		));
		update_term_meta($this->locationID, self::$termCreatedByKey, $this->_userID);
		update_term_meta($this->locationID, 'wiloke_listing_location_place_id', $this->placeID);
	}

	public function detectCountry($aData){
		$total = count($aData);

		for ( $order = absint($total - 2); $order >= 0; $order-- ){
			if ( $aData[$order]['types'][0] === 'country' ){
				$this->aCountry = $aData[$order];
				break;
			}
		}
	}

	private function insertLocationByGoogle($postID){
		if ( !isset($this->aData['listing_place_information']) || empty($this->aData['listing_place_information']) ){
			return false;
		}

		$this->aParsePlaceInfo = base64_decode($this->aData['listing_place_information']);

		if ( empty($this->aParsePlaceInfo) ){
			return false;
		}

		$this->aParsePlaceInfo = json_decode($this->aParsePlaceInfo, true);
		$aLocation = $this->parseLocationCategoryName();

		if ( !empty($this->administratorLevel1) && $this->administratorLevel1['long_name'] == $aLocation['long_name'] ){
			$this->administratorLevel1 = null;
		}

		$oLocationExist = $this->findLocationByName($aLocation);
		$this->aCountry = end($this->aParsePlaceInfo['address_components']);
		if ( $this->aCountry['types'][0] !== 'country' ){
			$this->detectCountry($this->aParsePlaceInfo['address_components']);
		}

		if ( !$oLocationExist ){
			$oLocationExist = $this->findLocationByPlaceID($aLocation['long_name']);
			if ( !$oLocationExist ){
				if ( !empty($this->administratorLevel1) ){
					$this->maybeTermParentExisting();
				}

				if ( !empty($this->aCountry) ){
					$this->maybeTermCountryExisting();
				}

				if ( !empty($aLocation['long_name']) ){
					$this->insertNewTerm($aLocation['long_name']);
				}
			}else{
				$this->aLocation  = $oLocationExist['term'];
				$this->locationID = $oLocationExist['term_id'];
				$this->placeID    = $oLocationExist['place_id'];
			}
		}else{
			$this->oLocation = $oLocationExist;
			$this->locationID = $oLocationExist->term_id;
		}

		if ( !empty($this->locationID) ){
			wp_set_post_terms($postID, $this->locationID, 'listing_location');
		}
		return true;
	}

	protected function allowRenderingTarget($target){
		$aAuthorMeta = \Wiloke::getUserMeta(get_current_user_id());
		if ( $aAuthorMeta['role'] == 'wiloke_submission' ){
			$aPackageSettings = \Wiloke::getPostMetaCaching($this->aData['package_id'], 'pricing_settings');
			if ( isset($aPackageSettings[$target]) && $aPackageSettings[$target] == 'disable' ){
				return false;
			}
		}

		return true;
	}

	public static function isEditingPublishedListing($postID){
		if ( empty($postID) ){
			return false;
		}

		if ( get_post_field('post_status', $postID) !== 'publish' ){
			return false;
		}

		if ( current_user_can('edit_theme_options') ){
		    return true;
        }

		if ( get_post_field('post_author', $postID) != get_current_user_id() ){
			return false;
		}

		if ( !FrontendListingManagement::publishedListingEditable() ){
			return false;
		}

		return true;
	}

	private function _updateListing(){
		$userID = !empty($this->_userID) ? $this->_userID : get_current_user_id();
		$packageID = get_post_meta($this->aData['post_id'], self::$packageIDOfListing, true);
		$aData = array(
			'ID'            => $this->aData['post_id'],
			'post_title'    => wp_strip_all_tags($this->aData['listing_title']),
			'post_content'  => $this->aData['content'],
			'post_type'     => 'listing',
			'post_author'   => $userID,
			'menu_order'    => $this->setMenuOrder($packageID)
		);

		if ( (get_post_field('post_status', $this->aData['post_id']) == 'publish') ){
			$editType = FrontendListingManagement::publishedListingEditable();
			if ( !$editType ){
				return false;
			}

			if ( $editType == 'allow_need_review' ){
				$aData['post_status'] = 'pending';
			}

		}

		$postID = wp_update_post($aData);
		global $wiloke;
		if ( !$this->allowRenderingTarget('toggle_listing_template') ){
			$this->aData['listing_style'] = $wiloke->aThemeOptions['listing_layout'];
		}

		update_post_meta($postID, '_wp_page_template', $this->aData['listing_style']);
		$postStatus = get_post_status($postID);

		if ( $postStatus === 'expired' ){
			$this->listingID = $postID;
			Payment::insertPaymentRelationships($this->aData['package_id']);
		}

		if ( !empty($postID) ){
			$this->listingID = $postID;
			\Wiloke::setSession(self::$listingIDSessionKey, $postID);
			$this->_updatePostMeta($postID);
			$this->updateACFCustomFields();
			$this->_setPostTerms($postID);
			do_action('wiloke/wiloke-listgo-functionality/submit/updated_listing', $this->listingID, $this->aData);
			return true;
		}
		
		return false;
	}

	/**
	 * @param $_packageStatus: 1. free -> $postStatus = pending 2. Has payment ID -> $postStatus = pending 3. empty -> $postStatus = processing
	 */
	private function _insertListing(){
		global $wiloke;
		$userID = !empty($this->_userID) ? $this->_userID : get_current_user_id();
		// Create post object
		$aData = array(
			'post_title'    => wp_strip_all_tags($this->aData['listing_title']),
			'post_content'  => $this->aData['content'],
			'post_type'     => 'listing',
			'post_status'   => 'processing',
			'post_author'   => $userID,
			'menu_order'    => $this->setMenuOrder($this->aData['package_id'])
		);

		// Insert the post into the database
		$postID = wp_insert_post($aData);
		if ( empty($postID) || is_wp_error($postID) ){
			return false;
		}

		if ( !$this->allowRenderingTarget('toggle_listing_template') ){
			$this->aData['listing_style'] = $wiloke->aThemeOptions['listing_layout'];
		}

		update_post_meta($postID, '_wp_page_template', $this->aData['listing_style']);
		$this->_updatePostMeta($postID);
		$this->_setPostTerms($postID);
		\Wiloke::setSession(self::$listingIDSessionKey, $postID);
		Payment::insertPaymentRelationships($this->aData['package_id']);

		$this->listingID = $postID;
		$this->updateACFCustomFields();
		do_action('wiloke/wiloke-listgo-functionality/submit/instered_listing', $this->listingID, $this->aData);
		return true;
	}

	public static function packageAllow(){
		$packageID = trim($_REQUEST['package_id']);
		$packageID = apply_filters('wiloke/wiloke-listgo-functionality/App/Submit/Add/packageAllow/packageID', $packageID);
		$aPackageSettings = \Wiloke::getPostMetaCaching($packageID, 'pricing_settings');
		return $aPackageSettings;
	}

	public function confirmEditPopup(){
		if ( !is_page_template('wiloke-submission/addlisting.php') && !is_page_template('wiloke-submission/addlisting-old.php')  ){
			return '';
		}

		if ( !isset($_REQUEST['post_id']) || empty($_REQUEST['post_id']) ){
			return '';
		}

		if ( !self::isEditingPublishedListing($_REQUEST['post_id']) ){
			return '';
		}

		?>
		<div id="wiloke-form-update-listing-wrapper" class="wil-modal wil-modal--fade">
			<div class="wil-modal__wrap">
				<div class="wil-modal__content">
					<div class="claim-form">
						<h2 class="claim-form-title"><?php esc_html_e('Update This Listing', 'listgo'); ?></h2>
						<div class="claim-form-content">
							<form id="wiloke-form-confirm-update-listing" method="POST" class="form" action="#">
								<p><?php esc_html_e('This listing will be switched to hidden status temporary while reviewing. Do you want to continue?', 'listgo'); ?></p>
								<button id="listgo-cancel-edit-listing" class="listgo-btn addlisting-popup__btn"><?php esc_html_e('Cancel', 'wiloke'); ?></button>
								<button id="listgo-continue-editing-listing" class="listgo-btn addlisting-popup__btn primary"><?php esc_html_e('Yes', 'wiloke'); ?></button>
							</form>
						</div>
						<div class="wil-modal__close"></div>
					</div>
				</div>
			</div>
			<div class="wil-modal__overlay"></div>
		</div>
		<?php
	}

	public static function allowAddingListing(){
		if ( !current_user_can('edit_theme_options') && ( \WilokePublic::$oUserInfo->role != User::$wilokeSubmissionRole ) ){
		    return false;
        }

	    $aPaymentSettings = \WilokePublic::getPaymentField();

		if ( isset($aPaymentSettings['toggle']) && ($aPaymentSettings['toggle'] == 'enable') ) {
            return true;
        }

        return false;
    }

    public static function getImgPreview($previewName){
        $directoryUrl = apply_filters('wiloke/wiloke-listgo-functionality/app/submit/preview_uri', get_template_directory_uri() . '/img/preview/');

        return $directoryUrl . $previewName;
    }
}