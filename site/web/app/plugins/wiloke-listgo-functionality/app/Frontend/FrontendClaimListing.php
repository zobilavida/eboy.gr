<?php
namespace WilokeListGoFunctionality\Frontend;
use WilokeListGoFunctionality\Register\RegisterClaim;

class FrontendClaimListing{
    protected $aData;
    protected $userID;
    public static $listingHasManyClaimedKey = 'wiloke_listing_has_many_claimed';
    public static $aClaimStatus = array();

	public function __construct() {
		add_action('wp_ajax_wiloke_claim_listing', array($this, 'claimListing'));
		add_action('wiloke/listgo/single/after_title', array($this, 'renderClaimStatus'));
		add_action('wiloke/listgo/templates/single-listing/before_tab-description_close', array($this, 'renderClaimBtn'));
		add_action('wiloke/listgo/single-listing/after_related_post', array($this, 'renderEditListingBtn'), 10, 1);
		add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('wp_ajax_wiloke_edit_listing_claimed', array($this, 'updateClaimedListing'));
	}

	public static function getClaimerInfo($post=null){
        if ( empty($post) ){
            global $post;
        }

		$aUser = \Wiloke::getUserMeta($post->post_author);
		$components['recipient'] = $aUser['user_email'];
		$aPostMeta =  \Wiloke::getPostMetaCaching($post->ID, 'listing_claim');

		if ( isset($aPostMeta['status']) && $aPostMeta['status'] === 'claimed' ){
			$claimedID = get_post_meta($post->ID, RegisterClaim::$listingClaimRelationshipKey, true);
			$aClaimedInfo = \Wiloke::getPostMetaCaching($claimedID, RegisterClaim::$metaKey);
			return \Wiloke::getUserMeta($aClaimedInfo['claimed_by']);
		}
		return false;
    }

	public function updateClaimedListing(){
	    global $wiloke;
		if (  !isset($_POST['security']) || empty($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error(
				array(
					'message' => $wiloke->aConfigs['translation']['securitycodewrong']
				)
			);
		}

		parse_str($_POST['data'], $this->aData);

		if ( !isset($this->aData['post_id']) || empty($this->aData['post_id']) ){
			wp_send_json_error(
				array(
					'message' => esc_html__('The listing does not exist.', 'wiloke')
				)
			);
		}

		$dontHavePermissionMsg = esc_html__( 'You do not permission to edit this article. Please contact admin to make a deal.', 'wiloke' );

		if ( !is_user_logged_in() ){
			wp_send_json_error(
				array(
					'message' => $dontHavePermissionMsg
				)
			);
		}

		$this->userID = get_current_user_id();

		if ( !isset($this->aData['listing_title']) || empty($this->aData['listing_title']) ){
			wp_send_json_error(array(
				'listing_title' => $wiloke->aConfigs['translation']['titlerequired']
			));
		}

		if ( !isset($_POST['content']) || empty($_POST['content']) ){
			wp_send_json_error(array(
				'listing_content' => $wiloke->aConfigs['translation']['contentrequired']
			));
		}
		$this->aData['content'] = $_POST['content'];

		$isPassedClaimVerification = self::verifyClaim($this->aData['post_id'], $this->userID);

		if ( !$isPassedClaimVerification ){
			wp_send_json_error(
				array(
					'message' => $dontHavePermissionMsg
				)
			);
        }
		$toggleBSH = isset($this->aData['toggle_business_hours']) ? $this->aData['toggle_business_hours'] : 'disable';
		update_post_meta($this->aData['post_id'], 'wiloke_toggle_business_hours', $toggleBSH);

        $status = $this->updateListing();
		if ( $status ){
			do_action('wiloke/wiloke_submission/afterUpdated', $this->aData, $this->userID);
            wp_send_json_success();
        }else{
            wp_send_json_error(
                array(
                    'message' => esc_html__('Something went wrong', 'wiloke')
                )
            );
        }
    }

	private function updateListing(){
		$aData = array(
			'ID'            => $this->aData['post_id'],
			'post_title'    => wp_strip_all_tags($this->aData['listing_title']),
			'post_content'  => $this->aData['content'],
			'post_type'     => 'listing'
		);

		$postID = wp_update_post($aData);
		update_post_meta($postID, '_wp_page_template', $this->aData['listing_style']);

		if ( !empty($postID) ){
			$this->listingID = $postID;
			$this->updatePostMeta($postID);
			$this->setPostTerms($postID);
			return true;
		}

		return false;
	}

	private function updatePostMeta($postID){
		$aListingSettings['map']['location']  = $this->aData['listing_address'];
		$aListingSettings['map']['latlong']   = $this->aData['listing_latlng'];
		$aListingSettings['phone_number']     = $this->aData['listing_phonenumber'];
		$aListingSettings['website']          = $this->aData['listing_website'];

		update_post_meta($postID, 'listing_settings', $aListingSettings);

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

		update_post_meta($postID, 'listing_price', $this->aData['listing_price']);
		update_post_meta($postID, 'listing_social_media', $this->aData['listing']['social']);
		update_post_meta($postID, 'wiloke_listgo_business_hours', $this->aData['listgo_bh']);
		set_post_thumbnail($postID, $this->aData['featured_image']);
	}

	private function setPostTerms($postID){
		if ( !empty($this->aData['listing_tags']) ){
			wp_set_post_terms($postID, $this->aData['listing_tags'], 'listing_tag', false);
		}
	}

	public function enqueueScripts(){
		if ( is_page_template('templates/edit-claimed.php') ){
			global $wiloke;
			wp_enqueue_media();
			wp_enqueue_script('select2', plugin_dir_url(dirname(__FILE__)) . '../public/asset/select2/js/select2.full.min.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
			wp_enqueue_style('select2', plugin_dir_url(dirname(__FILE__)) . '../public/asset/select2/css/select2.min.css', array(), WILOKE_LISTGO_FC_VERSION);
			wp_enqueue_script('addlisting', plugin_dir_url(dirname(__FILE__)) . '../public/source/js/addlisting.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
			wp_enqueue_style('addlisting', plugin_dir_url(dirname(__FILE__)) . '../public/source/css/addlisting.css', array(), WILOKE_LISTGO_FC_VERSION);
			wp_enqueue_script('wiloke-mapextend', get_template_directory_uri() . '/admin/source/js/mapextend.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
			wp_enqueue_style('wiloke-mapextend', get_template_directory_uri() . '/admin/source/css/mapextend.css', array(), WILOKE_LISTGO_FC_VERSION);
			wp_enqueue_script('wiloke-edit-claimed', plugin_dir_url(dirname(__FILE__)) . '../public/source/js/edit-claimed.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
		}
    }

	public static function verifyClaim($postID, $userID=null){
		if ( current_user_can('edit_theme_options') ){
			return true;
		}

		$aClaimed = \Wiloke::getPostMetaCaching($postID, 'listing_claim');

		if ( !isset($aClaimed['status']) || $aClaimed['status'] != 'claimed' ){
			return false;
		}

		$claimedID = get_post_meta($postID, RegisterClaim::$listingClaimRelationshipKey, true);
		$aClaimedInfo = \Wiloke::getPostMetaCaching($claimedID, RegisterClaim::$metaKey);

		$userID = empty($userID) ? \WilokePublic::$oUserInfo->ID : $userID;
		if ( $aClaimedInfo['claimed_by'] != $userID){
			return false;
		}

		return true;
    }

	public function renderEditListingBtn($post){
		if( empty(\WilokePublic::$oUserInfo) || current_user_can('edit_theme_options') ){
			return false;
		}

		if ( !self::verifyClaim($post->ID) ){
		    return false;
        }

		global $wiloke;

		if ( !isset($wiloke->aThemeOptions['listing_claimed_template']) || empty($wiloke->aThemeOptions['listing_claimed_template']) ){
			$pageUrl = '#';
		}else{
			$pageUrl = get_permalink($wiloke->aThemeOptions['listing_claimed_template']);
			$pageUrl = \WilokePublic::addQueryToLink($pageUrl, 'listing_id='.$post->ID);
		}

		?>
		<div class="listing-single-actions">
			<a href="<?php echo esc_url($pageUrl); ?>" class="listgo-btn listgo-btn--sm listgo-btn--round"><i class="fa fa-pencil"></i><span><?php esc_html_e('Edit Listing', 'listgo'); ?></span></a>
		</div>
		<?php
	}

	public function claimListing(){
	    global $wiloke;
		if ( !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Wrong Security Code', 'wiloke')
				)
			);
		}

		if ( empty($_POST['phone']) && ( !isset($wiloke->aThemeOptions['listing_toggle_claim_required_phone']) || $wiloke->aThemeOptions['listing_toggle_claim_required_phone'] == 'enable') ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Please supply your phone number', 'wiloke')
				)
			);
		}

		if ( !isset($_POST['claimID']) || empty($_POST['claimID']) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Something went wrong', 'wiloke')
				)
			);
		}

		$listingID = absint($_POST['claimID']);
		$userID = get_current_user_id();

		if ( empty($userID) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('You need to login before claiming this listing.', 'wiloke')
				)
			);
		}

		$aUserMeta = \Wiloke::getUserMeta($userID);
		$thank = \Wiloke::wiloke_kses_simple_html(sprintf('<h4 class="text-center">Thank you!</h4> <p class="text-center">Your request has been submitted. We\'ll get in touch with you at %s or %s as soon as possible.</p>', $_POST['phone'], $aUserMeta['user_email']), 'wiloke');

		$postType = get_post_type($listingID);
		if ( $postType !== 'listing' ){
			wp_send_json_success(
				array(
					'msg' => $thank
				)
			);
		}

		$status = \Wiloke::getPostMetaCaching($listingID, 'listing_claim');
		if ( isset($status['status']) && $status['status'] === 'claimed' ){
			wp_send_json_success(
				array(
					'msg' => $thank
				)
			);
		}

		$aListingsClaimedByUser = \Wiloke::getPostMetaCaching($userID, RegisterClaim::$userClaimedListingKey);

		if ( !empty($aListingsClaimedByUser) && in_array($listingID, $aListingsClaimedByUser) ){
			wp_send_json_success(
				array(
					'msg' => $thank
				)
			);
		}

		$postID = wp_insert_post(
			array(
				'post_title' => get_the_title($listingID) . ' - ' . esc_html__('Claimed By', 'wiloke') . ' ' . $aUserMeta['display_name'],
				'post_type'  => RegisterClaim::$postType,
				'post_status'=>'publish'
			)
		);

		$postAuthorID = get_post_field( 'post_author', $listingID );
		update_post_meta($postID, RegisterClaim::$metaKey, array(
			'listing'        => $listingID,
			'listing_author' => $postAuthorID,
			'claimed_by'     => $userID,
			'status'         => 'pending',
			'phone'          => $_POST['phone']
		));

        $aListClaimed = \Wiloke::getPostMetaCaching($listingID, self::$listingHasManyClaimedKey);
        $aListClaimed[$userID] = $postID;
        update_post_meta($listingID, self::$listingHasManyClaimedKey, $aListClaimed);
        do_action('wiloke/wiloke_submission/add_notification', $userID, $postAuthorID, $listingID, 'claimed_listing');
		wp_send_json_success(
			array(
				'msg' => $thank
			)
		);
	}

	public static function getClaimStatus(){
		global $post, $wiloke;

		if ( empty(self::$aClaimStatus) ){

			if ( !isset($wiloke->aThemeOptions['listing_toggle_claim_listings']) || $wiloke->aThemeOptions['listing_toggle_claim_listings'] !== 'enable' ){
				self::$aClaimStatus = false;
				return false;
			}

			if ( is_user_logged_in() ){
				if ( !empty(\WilokePublic::$oUserInfo) && (\WilokePublic::$oUserInfo->ID == $post->post_author) ){
					self::$aClaimStatus = false;
					return false;
				}
            }

			self::$aClaimStatus = \Wiloke::getPostMetaCaching($post->ID, 'listing_claim');
		}

		return self::$aClaimStatus;
	}

	public static function renderClaimStatus() {
		self::getClaimStatus();

		if ( !self::$aClaimStatus ){
			return false;
		}

		if ( isset(self::$aClaimStatus['status']) && self::$aClaimStatus['status'] === 'claimed' ) :
			?>
            <span class="listing__icon-notif claimed"  data-tooltip="<?php esc_html_e('Claimed', 'wiloke'); ?>"><i class="fa fa-check-circle"></i></span>
		<?php endif;
	}

	public static function renderClaimBtn() {
		self::getClaimStatus();
        
		if ( !self::$aClaimStatus || (isset(self::$aClaimStatus['status']) && self::$aClaimStatus['status'] === 'claimed') ){
			return false;
		}
		?>
        <div class="listing-single__claim">
            <div class="listing-single__claim-content">
                <h4 class="listing-single__claim-title"><?php esc_html_e('Is this your business?', 'wiloke'); ?></h4>
                <p class="listing-single__claim-description"><?php esc_html_e('Claim listing is the best way to manage and protect  your business', 'wiloke'); ?></p>
            </div>
            <a id="wiloke-claim-listing" href="#" data-modal="#wiloke-form-claim-information-wrapper" class="listgo-btn btn-primary"><?php esc_html_e('Claim it now!', 'wiloke'); ?></a>
        </div>
		<?php
	}
}