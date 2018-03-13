<?php
namespace WilokeListGoFunctionality\Frontend;

use WilokeListGoFunctionality\AlterTable\AlterTablePaymentHistory;
use WilokeListGoFunctionality\Submit\AddListing;
use WilokeListGoFunctionality\Submit\User as WilokeSubmissionUser;
use WilokeListGoFunctionality\AlterTable\AlterTableReviews;

class FrontendListingManagement{
	public static $pinToTopAuthorPageKey = 'wiloke_listgo_pin_to_top_of_author_page_';
	public static $listingViewKey = 'wiloke_listing_view_';
	public static $latestTokenKey = 'wiloke_latest_token';

	public static $aListingCounter = null;

	protected $authorID = null;
	protected $listingID = null;

	public function __construct() {
		add_action('wp_ajax_wiloke_listgo_pin_to_top', array($this, 'setPinnedToTop'));
		add_action('wp_ajax_wiloke_submission_statistic_view', array($this, 'updateListingView'));
		add_action('wp_ajax_nopriv_wiloke_submission_statistic_view', array($this, 'updateListingView'));
		add_action('wp_ajax_wiloke_change_plan_message', array($this, 'renderChangePlanMessage'));
		add_action('wp_footer', array($this, 'addCodeToFooterPage'));
	}

	public function addCodeToFooterPage(){
	    global $post;
	    $aPaymentSettings = \WilokePublic::getPaymentField();
	    if ( empty($aPaymentSettings) || !isset($post->ID) ){
	        return false;
        }

        if ( $aPaymentSettings['myaccount'] == $post->ID ){
            ?>
            <div id="wiloke-confirm-change-plan-wrapper" class="wil-modal wil-modal--fade">
                <div class="wil-modal__wrap">
                    <div class="wil-modal__content">
                        <div class="claim-form">
                            <h2 class="claim-form-title"><?php esc_html_e('Are you sure?', 'wiloke'); ?></h2>
                            <div class="claim-form-content">
                                <form class="form" action="">
                                    <p class="text-center"><?php esc_html_e('Just a friendly popup to ensure that you want to change your subscription level?', 'wiloke'); ?></p>
                                    <div class="add-listing-actions">
                                        <button id="wiloke-cancel-change-plan" type="submit" class="listgo-btn"><?php esc_html_e('Cancel') ?></button>
                                        <button id="wiloke-want-to-change-plan" type="submit" class="listgo-btn btn-primary"><?php esc_html_e('Yes') ?></button>
                                    </div>
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
    }

	public function updateListingView(){
        if ( !isset($_POST['listingID']) || !isset($_POST['currentUserID']) || !isset($_POST['authorID']) || !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) || !isset($_POST['currentUserID']) ){
            die();
        }

        if ( !WilokeSubmissionUser::isUserIDExists($_POST['currentUserID']) ){
            die();
        }

		if ( get_post_field('post_status', $_POST['listingID']) !== 'publish' ){
			return false;
		}

        $this->setListingView($_POST['authorID'], $_POST['listingID'], $_POST['currentUserID']);
    }

    public static function getListingView($authorID, $listingID){
	    $key = self::$listingViewKey . $authorID;
	    if ( \Wiloke::$wilokePredis && \Wiloke::$wilokePredis->exists($key) ){
	        $aCurrentView = \Wiloke::$wilokePredis->get($key);
        }else{
		    $aCurrentView = get_option($key);
        }

        if ( empty($aCurrentView) ){
	        return 1;
        }

	    $aCurrentView = json_decode($aCurrentView, true);

	    if ( !isset($aCurrentView[$listingID]) ){
            return 1;
        }

        return count($aCurrentView[$listingID]);
    }

    public function setListingView($authorID, $listingID, $currentUserID){
	    $key = self::$listingViewKey . $authorID;
        $aCurrentView = get_option($key);
	    $aCurrentView = empty($aCurrentView) ? array() : json_decode($aCurrentView, true);
	    $aCurrentView[$listingID][] = $currentUserID;

	    $aCurrentView = json_encode($aCurrentView);

	    if ( \Wiloke::$wilokePredis ){
		    \Wiloke::$wilokePredis->set($key, $aCurrentView);
	    }

	    update_option($key, $aCurrentView);
    }

	public static function getTotalReviewedOfAllListings(){
        $userID = get_current_user_id();
        if ( empty($userID) ){
            return false;
        }

        global $wpdb;
        $ratingTbl = $wpdb->prefix . AlterTableReviews::$tblName;
        $postTbl = $wpdb->prefix . 'posts';

        $result = $wpdb->get_var(
            $wpdb->prepare(
                    "SELECT COUNT($ratingTbl.review_ID) FROM $ratingTbl WHERE $ratingTbl.post_ID IN (SELECT $postTbl.ID FROM $postTbl WHERE $postTbl.post_author = %d)",
                $userID
            )
        );

        if ( !$result ){
            return 0;
        }

        return $result;
    }

    public static function getTotalPostByStatus($status='publish'){
	    $userID = get_current_user_id();
	    if ( empty($userID) ){
	        return 0;
        }

        global $wpdb;

	    if ( isset(self::$aListingCounter[$status]) ){
	        return self::$aListingCounter[$status];
        }

	    if ( $status !== 'any' ){
	        if ( $status == 'processing' ){
		        $total = $wpdb->get_var(
			        $wpdb->prepare(
				        "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_author=%d AND (post_status=%s) AND post_type=%s",
				        $userID, 'processing', 'listing'
			        )
		        );
            }else{
		        $total = $wpdb->get_var(
			        $wpdb->prepare(
				        "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_author=%d AND post_status=%s AND post_type=%s",
				        $userID, $status, 'listing'
			        )
		        );
            }
        }else{
		    $total = $wpdb->get_var(
			    $wpdb->prepare(
				    "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_author=%d AND post_status IN ('publish', 'expired', 'processing', 'renew', 'pending') AND post_type=%s",
				    $userID, 'listing'
			    )
		    );
        }

	    if ( !$total ){
	        return 0;
        }

        return $total;
    }

    public static function publishedListingEditable(){
	    $aPaymentSettings = \WilokePublic::getPaymentField();
        if ( isset($aPaymentSettings['published_listing_editable']) && ($aPaymentSettings['published_listing_editable'] == 'not_allow') ){
            return false;
        }
        return $aPaymentSettings['published_listing_editable'];
    }

    public static function getTotalViewedOfAllListings(){
	    $key = self::$listingViewKey . get_current_user_id();
	    if ( \Wiloke::$wilokePredis && \Wiloke::$wilokePredis->exists($key) ){
		    $aData = \Wiloke::$wilokePredis->get($key);
	    }else{
		    $aData = get_option($key);
        }

        if ( empty($aData) ){
	        return 0;
        }

        $aData = json_decode($aData, true);
        if ( empty($aData) ){
            return 0;
        }

        $total = 0;
        foreach ( $aData as $totalViewOfEach ){
            $total += count($totalViewOfEach);
        }

        return $total;
    }

	public static function message($aInfo, $status='primary', $isReturn=false){
        if ( $isReturn ){
            ob_start();
        }
        if ( isset($aInfo['icon']) ){
	        $icon = $aInfo['icon'];
        }else{
	        $icon = $status == 'danger' ? 'icon_error-triangle_alt' : 'icon_box-checked';
        }
	    ?>
        <div class="wil-alert wil-alert-has-icon alert-<?php echo esc_attr($status); ?>">
            <span class="wil-alert-icon"><i class="<?php echo esc_attr($icon); ?>"></i></span>
            <?php if ( isset($aInfo['title']) ) : ?>
            <strong class="wil-alert-title"><?php echo esc_html($aInfo['title']); ?></strong>
            <?php endif; ?>
            <p class="wil-alert-message"><?php \Wiloke::wiloke_kses_simple_html($aInfo['message']); ?></p>
            <?php if ( isset($aInfo['can_remove']) ) : ?>
            <span class="wil-alert-remove" data-id="<?php echo esc_attr($aInfo['objectID']); ?>"></span>
            <?php endif; ?>
        </div>
        <?php
        if ( $isReturn ){
            $content = ob_get_clean();
            return $content;
        }
    }

	public static function getPinnedToTop($authorID=null){
		if ( empty($authorID) ){
			$authorID = get_queried_object_id();
		}

		$key = self::$pinToTopAuthorPageKey . $authorID;

		if ( \Wiloke::$wilokePredis && \Wiloke::$wilokePredis->exists($key) ){
			return \Wiloke::$wilokePredis->get($key);
		}else{
			return get_option($key);
		}
	}

	public function setPinnedToTop(){
		if ( !isset($_POST['listingID']) || empty($_POST['listingID']) || !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('Wrong Security code', 'wiloke')
				)
			);
		}

		$authorID = get_current_user_id();
		$oListing = get_post($_POST['listingID']);

		if ( $oListing->post_author != $authorID ){
			wp_send_json_error(
				array(
					'msg' => esc_html__('You don\'t have permission to edit this listing', 'wiloke')
				)
			);
		}

		$key = self::$pinToTopAuthorPageKey . $authorID;
		if ( $_POST['status'] === 'pinned' ){
			if ( \Wiloke::$wilokePredis && \Wiloke::$wilokePredis->exists($key) ){
				\Wiloke::$wilokePredis->del($key, $oListing->ID);
			}
			delete_option($key);
			$newStatus = 'unpinned';
		}else{
			if ( \Wiloke::$wilokePredis && \Wiloke::$wilokePredis->exists($key) ){
				\Wiloke::$wilokePredis->set($key, $oListing->ID);
			}
			update_option($key, $oListing->ID);
			$newStatus = 'pinned';
		}

		wp_send_json_success(
			array(
				'new_status' => $newStatus,
                'title' => $newStatus === 'pinned' ? esc_html__('Unpin from the top of the author page', 'wiloke') : esc_html__('Pin to top of your author page')
			)
		);
	}

	public static function renderListingManagementItem($post, $checkoutPage, $pricingPage, $pinnedID=null){
		global $wiloke;
	    $thumbnail = has_post_thumbnail($post->ID) ? get_the_post_thumbnail_url($post->ID) : get_template_directory_uri() . '/img/no-image.jpg';
		$addListingPage = \WilokePublic::getPaymentField('addlisting', true);
		$isFeatured = get_post_meta($post->ID, 'wiloke_listgo_toggle_highlight', true);
		?>
		<div class="f-listings-item">
			<div class="f-listings-item__media">
                <?php if ( !empty($isFeatured) ) : ?>
				 <span class="onfeatued"><i class="fa fa-star-o"></i></span>
                <?php endif; ?>
				<a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php \Wiloke::lazyLoad($thumbnail); ?></a>
			</div>

			<div class="overflow-hidden">
				<div class="f-listings-item__meta f-listings-item__meta-top">
                    <span class="status status-<?php echo esc_attr($post->post_status); ?>">
                        <i class="<?php echo esc_attr(\WilokePublic::$aPostStatusIcons[$post->post_status]); ?>"></i>
	                    <?php
	                    switch ($post->post_status) {
		                    case 'publish':
			                    esc_html_e('Published', 'wiloke');
			                    break;
		                    case 'processing':
			                    esc_html_e('Unpaid', 'wiloke');
			                    break;
		                    case 'pending':
			                    esc_html_e('Pending', 'wiloke');
			                    break;
		                    case 'expired':
			                    esc_html_e('Expired', 'wiloke');
			                    break;
		                    case 'temporary_closed':
			                    esc_html_e('Temporary Closed', 'wiloke');
			                    break;
		                    default:
			                    echo esc_html(ucfirst($post->post_status));
			                    break;
	                    }
	                    ?>
                    </span>
					<?php if ( ($post->post_status == 'publish') && ($pinnedID == $post->ID) ): ?>
					<span class="sticky wiloke-listgo-pinned-<?php echo esc_attr($post->ID); ?>">
                        <i class="icon_ribbon_alt"></i> <?php esc_html_e('Pinned Listing', 'listgo'); ?>
                    </span>
					<?php endif ?>
				</div>

				<h2 class="f-listings-item__title"><a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php echo get_the_title($post->ID); ?></a></h2>
				<div class="f-listings-item__meta">
					<?php if ( $post->post_status == 'publish' ) :  ?>
					<span>
						<?php if ( $pinnedID == $post->ID ) : ?>
						<a class="wiloke-pin-to-top" data-status="pinned" data-postid="<?php echo esc_attr($post->ID); ?>" href="#"><i class="fa fa-thumb-tack"></i> <?php esc_html_e('Pinned Listing', 'wiloke'); ?></a>
						<?php else : ?>
						<a class="wiloke-pin-to-top" data-status="unpinned" data-postid="<?php echo esc_attr($post->ID); ?>" href="#" data-tooltip="<?php esc_html_e('Pin to top of your author page', 'wiloke'); ?>"><i class="fa fa-thumb-tack"></i> <?php esc_html_e('Pin To Top', 'wiloke'); ?></a>
						<?php endif; ?>
					</span>
					<?php endif; ?>
                    <span>
                        <a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><i class="fa fa-eye"></i> <?php esc_html_e('View', 'wiloke'); ?></a>
                    </span>
					<?php
					if ( $post->post_status === 'processing' ) :
						if ( strpos($checkoutPage, '?') ){
							$leadToCheckOutPage = $checkoutPage . '&package_id='.get_post_meta($post->ID, AddListing::$packageIDOfListing, true) . '&post_id='.$post->ID;
						}else{
							$leadToCheckOutPage = $checkoutPage . '?package_id='.get_post_meta($post->ID, AddListing::$packageIDOfListing, true) . '&post_id='.$post->ID;
						}
					?>
						<span>
                            <a href="<?php echo esc_url(\WilokePublic::addQueryToLink($addListingPage, 'post_id='.$post->ID.'&package_id='.AddListing::getPackageID($post->ID))); ?>"><i class="fa fa-pencil-square-o"></i> <?php esc_html_e('Edit', 'wiloke'); ?></a>
                        </span>
						<span>
                            <a href="<?php echo esc_url($leadToCheckOutPage); ?>"><i class="fa fa-credit-card-alt"></i> <?php esc_html_e('Pay & Publish', 'wiloke'); ?></a>
                        </span>
						<?php
					elseif($post->post_status === 'expired') :
						?>
						<span>
                            <a href="<?php echo esc_url(\WilokePublic::addQueryToLink($pricingPage, 'listing_id='.$post->ID)); ?>"><i class="fa-refresh"></i> <?php esc_html_e('Renew', 'wiloke'); ?></a>
                        </span>
					<?php endif; ?>
					<?php if ( $post->post_status === 'publish' || $post->post_status === 'temporary_closed' ) : $active = $post->post_status === 'temporary_closed' ? 'active' : ''; ?>
					<?php if ( self::publishedListingEditable() ) :  ?>
                        <span>
                            <a href="<?php echo esc_url(\WilokePublic::addQueryToLink($addListingPage, 'post_id='.$post->ID.'&package_id='.AddListing::getPackageID($post->ID))); ?>"><i class="fa fa-pencil-square-o"></i> <?php esc_html_e('Edit', 'wiloke'); ?></a>
                        </span>
                    <?php endif; ?>
                    <span class="wiloke-listgo-temporary-closed <?php echo esc_attr($active); ?>" data-postid="<?php echo esc_attr($post->ID); ?>">
                        <a href="#"><i class="fa fa-clock-o"></i> <?php esc_html_e('Temporary Closed', 'wiloke'); ?></a>
                    </span>
					<?php endif; ?>
					<span class="wiloke-listgo-remove-listing" data-postid="<?php echo esc_attr($post->ID); ?>">
                        <a href="#"><i class="fa fa-trash-o"></i> <?php esc_html_e('Remove', 'wiloke'); ?></a>
                    </span>
				</div>
			</div>
		</div>
		<?php
	}

	public static function renderChangePlanMessage(){
        if ( !isset($_GET['security']) || !isset($_GET['token']) || empty($_GET['token']) || (!check_ajax_referer('wiloke-nonce', 'security', false)) ){
	        wp_send_json_error();
        }

        if ( !is_user_logged_in() ){
	        wp_send_json_error();
        }

        global $wpdb;
		\Wiloke::setSession(self::$latestTokenKey, $_GET['token']);

        $historyTbl = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$aData = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $historyTbl WHERE user_ID=%d AND token=%s and method=%s ORDER BY ID DESC",
                get_current_user_id(), $_GET['token'], 'paypal'
            ),
            ARRAY_A
        );

        if ( empty($aData) ){
	        wp_send_json_error();
        }

        if ( $aData['status'] === 'Success' ){
	        wp_send_json_success(
		        array(
                    'packageID' => absint($aData['package_ID']),
			        'msg'       => esc_html__('Congratulation! Your plan has been upgraded.', 'wiloke')
		        )
	        );
        }

        $aPayPalResponse = json_decode($aData['information'], true);

        if ( empty($aPayPalResponse) ){
	        wp_send_json_error(
		        array(
			        'msg' => esc_html__('Something went to wrong', 'wiloke'),
			        'icon' => 'icon_error-triangle_alt'
		        )
	        );
        }

		wp_send_json_error(
            array(
                'msg' => $aPayPalResponse['Errors'][0]['LongMessage'],
                'icon' => 'icon_error-triangle_alt'
            )
        );

        wp_send_json_error();
    }
}