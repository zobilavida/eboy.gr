<?php
namespace WilokeListGoFunctionality\Frontend;


use WilokeListGoFunctionality\AlterTable\AlterTableNotifications;
use WilokeListGoFunctionality\AlterTable\AlterTableReviews;
use WilokeListGoFunctionality\AlterTable\AltertableFollowing;
use WilokeListGoFunctionality\Register\RegisterFollow;
use WilokeListGoFunctionality\Frontend\FrontendListingManagement;
use WilokeListGoFunctionality\Submit\User as WilokeUser;
use WilokeListGoFunctionality\Frontend\FrontendClaimListing as WilokeFrontendClaimListing;

class Notification{
	public static $latestCheckedKey = '';
	public static $newestNotificationKey = 'newest_notifications';
	public static $allNotificationsKey = 'all_notifications';
	public static $reviewAndPostKey = 'review_post_relationship';
	public static $hideNotificationInDashboardsKey = 'hide_notifications_in_dashboard_';
	public static $maxListingsOnMenu=7;
	public static $maxNotifications=100;
	protected static $isFetchViaAjax = false;
	protected static $aPostsNotIn = array();
	protected static $filterBy = '';
	protected static $cursor = 0;

	public function __construct() {
		add_action('post_updated', array($this, 'pushListing'), 10, 3);
		add_action('wiloke/wiloke_submission/save_review', array($this, 'pushReview'), 10, 3);
		add_action('wiloke/wiloke_submission/add_notification', array($this, 'addNotification'), 10, 4);
		add_action('wiloke_submission/payment_history/', array($this, 'notifyAboutNewPurchased'), 10, 5);
		add_action('wp_ajax_fetch_more_notifications', array($this, 'ajaxFetchNotification'));
		add_action('wp_ajax_remove_more_notifications', array($this, 'ajaxRemoveNotificationItem'));
		add_action('wiloke/listgo/wiloke-submission/account/user-dashboard/alert', array($this, 'printAllNotificationOnDashboard'), 10, 1);
		add_action('wp_ajax_wiloke_dismiss_notification', array($this, 'dismissNotification'));

		#Migrated from Wiloke Public
		add_action('wp_ajax_wiloke_listgo_update_fetch_notifications', array($this, 'fetchNotifications'));
		add_action('wp_ajax_wiloke_listgo_update_lastcheck_notification', array($this, 'updateLastCheckedNotifications'));
	}

	public static function fetchNotifications(){
		if ( !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) || empty($_POST['user_id']) ){
			return false;
		}

		$currentUserID = $_POST['user_id'];
		$lastCheck = \Wiloke::$wilokePredis ? \Wiloke::$wilokePredis->get(\Wiloke::$prefix.'notification_latest_check|'.$currentUserID) : get_option(\Wiloke::$prefix.'latest_check_notification|'.$currentUserID);
		$lastCheck = absint($lastCheck);

		$countStillNotCheck = 0;
		$notifications = array();

		if ( \Wiloke::$wilokePredis && \Wiloke::$wilokePredis->exists(\Wiloke::$prefix.self::$newestNotificationKey.':'.$currentUserID) ){
			$aLastestNews = \Wiloke::$wilokePredis->lrange(\Wiloke::$prefix.self::$newestNotificationKey.':'.$currentUserID, 0, self::$maxListingsOnMenu);
			if ( !empty($aLastestNews) ){
				foreach ( $aLastestNews as $time => $aNotification ){
					$aNotification = json_decode($aNotification, true);
					if ( $aNotification['created_at'] > $lastCheck ){
						$countStillNotCheck++;
					}
					$notifications[] = self::renderNotificationItem($aNotification);
				}
			}
		}else{
			if ( \Wiloke::$wilokePredis ){
				$hasNotNew = \Wiloke::$wilokePredis->get(\Wiloke::$prefix.'has_not_new_notification|'.$_POST['user_id']);
			}else{
				$hasNotNew = get_option(\Wiloke::$prefix.'has_not_new_notification|'.$_POST['user_id']);
			}

			if ( !$hasNotNew ){
				global $wpdb;
				$tblName = $wpdb->prefix . AlterTableNotifications::$tblName;
				$aNotifications = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT * FROM $tblName WHERE receive_ID=%d ORDER BY created_at DESC LIMIT 8",
						$currentUserID
					),
					ARRAY_A
				);
				setcookie(\Wiloke::$prefix.self::$newestNotificationKey.':'.$currentUserID, json_encode($aNotifications), time()+60*1000000000);
			}else{
				$aNotifications = $_COOKIE[\Wiloke::$prefix.self::$newestNotificationKey.':'.$currentUserID];
				$aNotifications = !empty($aNotifications) ? json_decode($aNotifications, true) : array();
			}

			foreach ( $aNotifications as $aNotification ){
				if ( strtotime($aNotification['created_at']) > $lastCheck ){
					$countStillNotCheck++;
				}
				$notifications[] = self::renderNotificationItem($aNotification);
			}
		}

		if ( empty($notifications) ){
			$notifications = \Wiloke::wiloke_kses_simple_html(__('<li><a href="#">You don\'t have any notification yet</a></li>', 'listgo'), true);
			$isEmpty = true;
		}else{
			$notifications = implode('', $notifications);
			$isEmpty = false;
		}
		wp_send_json_success(
			array(
				'countnew'      => $countStillNotCheck,
				'notifications' => $notifications,
				'is_empty'      => $isEmpty
			)
		);
	}

	/**
	 * Refer wiloke-listgo-functionality/app/FrontEnd/Notification.php to know more what is type and how many types.
	 * @since 1.0
	 */
	public static function renderNotificationItemInPluginFirst($aData){
		global $wpdb;
		$aAuthor = \Wiloke::getUserMeta($aData['author_ID']);
		$authorProfile = \Wiloke::getUserAvatar($aData['author_ID'], $aAuthor);

		switch ( $aData['type'] ){
			case 'purchased_new_plan':
				$url = '#';
				$subject = sprintf(__('purchased %s plan.', 'wiloke'), get_the_title($aData['object_ID']));
				$aUser['display_name'] = '';
				$thumbnail = get_the_post_thumbnail_url($aData['object_ID'], 'thumbnail');
				break;
			case 'review':
				if ( \Wiloke::$wilokePredis && \Wiloke::$wilokePredis->exists(self::$reviewAndPostKey.'|'.$aData['object_ID']) ){
					$postID = Wiloke::$wilokePredis->get(self::$reviewAndPostKey.'|'.$aData['object_ID']);
				}else{
					$tblReview = $wpdb->prefix . AlterTableReviews::$tblName;
					$postID = $wpdb->get_var(
						$wpdb->prepare(
							"SELECT post_ID FROM $tblReview WHERE review_ID=%d AND user_ID=%d",
							$aData['object_ID'], $aData['author_ID']
						)
					);
				}
				$subFix = '#tab-review&current-review='.$aData['object_ID'];
				$url = get_permalink($postID) . $subFix;
				$subject = sprintf(__('left a review on your listing: <strong>%s</strong>', 'listgo'), get_the_title($postID));
				$thumbnail = '';
				break;
			case 'approved':
				$url = get_permalink($aData['object_ID']);
				$subject = sprintf(__('Congrats! Your listing <strong>%s</strong> has been approved.', 'listgo'), get_the_title($aData['object_ID']));
				$aUser['display_name'] = '';
				$thumbnail = get_the_post_thumbnail_url($aData['object_ID'], 'thumbnail');
				break;
			default:
				$url = get_permalink($aData['object_ID']);
				$subject = esc_html__(' posted a new article: ', 'listgo') . '<strong>'.get_the_title($aData['object_ID']).'</strong>';
				$thumbnail = '';
				break;
		}

		?>
        <li class="notification-item" data-objectid="<?php echo esc_attr($aData['object_ID']); ?>" data-type="<?php echo esc_attr($aData['type']); ?>">
            <a href="<?php echo esc_url($url); ?>">
                <div class="notifications__avatar">
					<?php
					if ( strpos($authorProfile, 'profile-picture.jpg') === false ) {
						?>
                        <img src="<?php echo esc_url($authorProfile); ?>" alt="<?php echo esc_attr($aAuthor['display_name']); ?>" height="65" width="65" class="avatar">
						<?php
					} else {
						$firstCharacter = strtoupper(substr($aAuthor['display_name'], 0, 1));
						echo '<span style="background-color: '.esc_attr(\WilokePublic::getColorByAnphabet($firstCharacter)).'" class="widget_author__avatar-placeholder">'. esc_html($firstCharacter) .'</span>';
					}
					?>
                </div>
                <div class="overflow_hidden">
					<?php if ( !empty($thumbnail) ) : ?>
                        <div class="notifications__thumb">
                            <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php esc_html_e('Thumbnail', 'listgo'); ?>">
                        </div>
					<?php endif; ?>
                    <p class="notifications__title"><?php \Wiloke::wiloke_kses_simple_html($subject); ?></p>
                    <span class="notifications__date"><i class="icon_pencil-edit"></i> <?php echo esc_html(self::notificationCalculation($aData['created_at'])); ?></span>
                </div>
            </a>
            <span data-postid="<?php echo esc_attr($aData['object_ID']); ?>" class="notifications__remove">×</span>
        </li>
		<?php
	}

	public static function renderNotification(){
		if ( empty(\WilokePublic::$oUserInfo) || !class_exists('WilokeListGoFunctionality\Shortcodes\Shortcodes') ){
			return false;
		}

		$toggle = \WilokePublic::getPaymentField('toggle');
		if ( empty($toggle) || $toggle === 'disable' ){
			return false;
		}
		?>
        <div id="wiloke-notifications" class="header__notifications" data-userid="<?php echo esc_attr(\WilokePublic::$oUserInfo->ID) ?>">
            <div class="tb">
                <div class="tb__cell">
                    <div class="notifications__icon">
                        <i class="icon_lightbulb_alt"></i>
                        <span class="count"></span>
                    </div>
                </div>
            </div>

            <div class="notifications">

                <h6 class="notifications__label">
					<?php esc_html_e('Notifications', 'listgo'); ?> <span id="wiloke-listgo-count-newnotifications" class="count"></span>
                </h6>

                <ul class="notifications__list loading" style="min-height: 100px"></ul>
				<?php
				$myaccountLink = \WilokePublic::getPaymentField('myaccount', true);
				if ( !empty($myaccountLink) ){
					$profileUrl = \WilokePublic::addQueryToLink($myaccountLink, 'mode=notifications');
					echo '<div class="hidden notifications__more"><a href="'.esc_url($profileUrl).'">'.esc_html__('See all', 'listgo').'</a></div>';
				}
				?>
            </div>
        </div>
		<?php
	}

	public static function renderNotificationItem($aData){
		global $wpdb;
		$aUser = \WilokePublic::getUserMeta($aData['author_ID']);
		switch ( $aData['type'] ){
			case 'purchased_new_plan':
				$url = '#';
				$subject = sprintf(__('purchased %s plan.', 'wiloke'), get_the_title($aData['object_ID']));
				$img = get_the_post_thumbnail_url($aData['object_ID'], 'thumbnail');
				break;
			case 'review':
				if ( \Wiloke::$wilokePredis && \Wiloke::$wilokePredis->exists(self::$reviewAndPostKey.'|'.$aData['object_ID']) ){
					$postID = \Wiloke::$wilokePredis->get(self::$reviewAndPostKey.'|'.$aData['object_ID']);
				}else{
					$tblReview = $wpdb->prefix . AlterTableReviews::$tblName;
					$postID = $wpdb->get_var(
						$wpdb->prepare(
							"SELECT post_ID FROM $tblReview WHERE review_ID=%d AND user_ID=%d",
							$aData['object_ID'], $aData['author_ID']
						)
					);
				}
				$subFix = '#tab-review&current-review='.$aData['object_ID'];
				$url = get_permalink($postID) . $subFix;
				$subject = sprintf(__('left a review on your listing: <strong>%s</strong>', 'listgo'), get_the_title($postID));
				$img = isset($aUser['meta']['wiloke_profile_picture']) ? wp_get_attachment_image_url($aUser['meta']['wiloke_profile_picture'], 'thumbnail') : get_template_directory_uri() . '/img/profile-picture.jpg';
				break;
			case 'approved':
				$url = get_permalink($aData['object_ID']);
				$subject = sprintf(__('Congrats! Your listing <strong>%s</strong> has been approved.', 'listgo'), get_the_title($aData['object_ID']));
				$aUser['display_name'] = '';
				$img = get_the_post_thumbnail_url($aData['object_ID'], 'thumbnail');
				break;
			case 'approved_claim':
				$url = get_permalink($aData['object_ID']);
				$subject = sprintf(__('Congrats! Your claim <strong>%s</strong> has been approved.', 'listgo'), get_the_title($aData['object_ID']));
				$aUser['display_name'] = '';
				$img = get_the_post_thumbnail_url($aData['object_ID'], 'thumbnail');
				break;
			case 'declined_claim':
				$url = get_permalink($aData['object_ID']);
				$subject = sprintf(__('Unfortunately! Your claim <strong>%s</strong> has been declined.', 'listgo'), get_the_title($aData['object_ID']));
				$aUser['display_name'] = '';
				$img = get_the_post_thumbnail_url($aData['object_ID'], 'thumbnail');
				break;
			case 'claimed_listing':
				$aHasManyClaimIDs = \Wiloke::getPostMetaCaching($aData['object_ID'], WilokeFrontendClaimListing::$listingHasManyClaimedKey);
				$url = esc_url(admin_url('post.php?post='.$aHasManyClaimIDs[$aData['author_ID']].'&action=edit'));
				$subject = sprintf(__('claimed <strong>%s</strong>', 'listgo'), get_the_title($aData['object_ID']));
				$aUser['display_name'] = \Wiloke::getUserMeta($aData['author_ID'], 'display_name');
				$img = get_the_post_thumbnail_url($aData['object_ID'], 'thumbnail');
				break;
			default:
				$url = get_permalink($aData['object_ID']);
				$subject = esc_html__('posted a new article: ', 'listgo') . '<strong>'.get_the_title($aData['object_ID']).'</strong>';
				$img = isset($aUser['meta']['wiloke_profile_picture']) ? wp_get_attachment_image_url($aUser['meta']['wiloke_profile_picture'], 'thumbnail') : get_template_directory_uri() . '/img/profile-picture.jpg';
				break;
		}
		ob_start();
		?>
        <li>
            <a href="<?php echo esc_url($url); ?>">
                <?php if ( !empty($img) ) : ?>
                    <div class="notifications__avatar">
                        <img src="<?php echo esc_url($img); ?>" alt="<?php esc_html_e('Notification', 'listgo'); ?>">
                    </div>
                <?php endif; ?>
                <div class="overflow_hidden">
                    <p class="notifications__title"><strong><?php echo esc_html($aUser['display_name']); ?></strong> <?php \Wiloke::wiloke_kses_simple_html($subject); ?></p>
                    <span class="notifications__date"><i class="icon_pencil-edit"></i> <?php echo esc_html(self::notificationCalculation($aData['created_at'])); ?></span>
                </div>
            </a>
        </li>
		<?php
		$content = ob_get_clean();
		return $content;
	}

	public static function notificationCalculation($createdAt){
		$time = time();
		$createdAt = is_numeric($createdAt) ? absint($createdAt) : strtotime($createdAt);
		$minus = $time - absint($createdAt);
		$addedXMinutesBefore = ceil(\Wiloke::timeStampToMinutes($minus));

		if ( $addedXMinutesBefore <= 1 ){
			$posted = __('a few seconds ago', 'listgo');
		}elseif($addedXMinutesBefore < 60) {
			$posted = sprintf(__('%s minutes ago', 'listgo'), $addedXMinutesBefore);
		}else{
			$addedXHoursBefore = ceil(\Wiloke::timeStampToHours($minus));
			if ( $addedXHoursBefore < 24 ){
				$posted = sprintf(__('%s hours ago', 'listgo'), $addedXHoursBefore);
			}else{
				$posted = sprintf(__('%s days ago', 'listgo'), ceil($addedXHoursBefore/24));
			}
		}

		return $posted;
	}

	public function dismissNotification(){
        if ( !isset($_POST['objectID']) || !isset($_POST['currentUserID']) || !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
            die();
        }

        if ( !WilokeUser::isUserIDExists($_POST['currentUserID']) ){
            die();
        }

		$aIgnoreFollowingNotifications = get_option(\Wiloke::$prefix.self::$hideNotificationInDashboardsKey.$_POST['currentUserID']);
		$aIgnoreFollowingNotifications = empty($aIgnoreFollowingNotifications) ? array() : json_decode($aIgnoreFollowingNotifications, true);
        $aIgnoreFollowingNotifications[] = $_POST['objectID'];
		$aIgnoreFollowingNotifications = json_encode($aIgnoreFollowingNotifications);
		update_option(\Wiloke::$prefix.self::$hideNotificationInDashboardsKey.$_POST['currentUserID'], $aIgnoreFollowingNotifications);
		die();
    }

	public static function renderNotificationInDashboard($aNotification){
	    switch ($aNotification['type']){
		    case 'approved':
			    $aAlert['title'] = esc_html__('Your submitted has been approved', 'wiloke');
			    $aAlert['message'] = sprintf(__('Congrats! Your listing <a href="%s"><strong>%s</strong></a> has been approved.', 'wiloke'), get_permalink($aNotification['object_ID']), get_the_title($aNotification['object_ID']));
			    $status = 'success';
			    break;
		    case 'approved_claim':
			    $aAlert['title'] = esc_html__('Your claim has been approved', 'wiloke');
			    $aAlert['message'] = sprintf(__('Congrats! Your claim <a href="%s"><strong>%s</strong></a> has been approved.', 'wiloke'), get_permalink($aNotification['object_ID']), get_the_title($aNotification['object_ID']));
			    $status = 'success';
			    break;
		    case 'declined_claim':
			    $aAlert['title'] = esc_html__('Unfortunately, your claim has been declined!', 'wiloke');
			    $aAlert['message'] = sprintf(__('Unfortunately! Your claim <a href="%s"><strong>%s</strong></a> has been declined.', 'wiloke'), get_permalink($aNotification['object_ID']), get_the_title($aNotification['object_ID']));
			    $status = 'danger';
			    $aAlert['icon'] = 'icon_error-triangle_alt';
			    break;
        }

        if ( !isset($aAlert) ){
	        return false;
        }

		$aAlert['objectID'] = $aNotification['object_ID'];

		FrontendListingManagement::message($aAlert, $status);
    }

	public function printAllNotificationOnDashboard($oUserInfo){
        if ( empty($oUserInfo) ){
            return false;
        }

        $aIgnoreFollowingNotifications = get_option(\Wiloke::$prefix.self::$hideNotificationInDashboardsKey.$oUserInfo->ID);
		$aIgnoreFollowingNotifications = empty($aIgnoreFollowingNotifications) ? null : json_decode($aIgnoreFollowingNotifications, true);

		$counter = 0;
		if ( \Wiloke::$wilokePredis && \Wiloke::$wilokePredis->exists(\Wiloke::$prefix.self::$newestNotificationKey.':'.$oUserInfo->ID) ){
			$aLastestNews = \Wiloke::$wilokePredis->lrange(\Wiloke::$prefix.self::$newestNotificationKey.':'.$oUserInfo->ID, 0, self::$maxListingsOnMenu);
			if ( !empty($aLastestNews) ){
				foreach ( $aLastestNews as $time => $aNotification ){
					$aNotification = json_decode($aNotification, true);
                    if ( empty($aIgnoreFollowingNotifications) || !in_array($aNotification['object_ID'], $aIgnoreFollowingNotifications) ){
                        if ( $counter > 4 ){
                            return false;
                        }
                        self::renderNotificationInDashboard($aNotification);
	                    $counter++;
                    }
				}
			}
		}else{
		    global $wpdb;
            $tblName = $wpdb->prefix . AlterTableNotifications::$tblName;
            $aNotifications = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $tblName WHERE receive_ID=%d ORDER BY created_at DESC LIMIT 4",
                    $oUserInfo->ID
                ),
                ARRAY_A
            );

			foreach ( $aNotifications as $aNotification ){
				if ( empty($aIgnoreFollowingNotifications) || !in_array($aNotification['object_ID'], $aIgnoreFollowingNotifications) ){
					self::renderNotificationInDashboard($aNotification);
				}
			}
		}

	}

	/**
	 * Using push technology to push notification.
	 * @type: string The type helps you to understand that listing present to. approved, newpost, review
	 * @since 1.0
	 */
	public function pushListing($postID, $postAfter, $postBefore){
		if ( $postAfter->post_type !== 'listing' ){
			return false;
		}

		$aUserInfo = \Wiloke::getUserMeta($postAfter->post_author);

		if ( ($postAfter->post_status === 'trash' || $postAfter->post_status === 'auto-draft') && $aUserInfo['role'] === 'wiloke_submission' ){
			$this->_deleteNotification($postAfter->post_author, $postID);
			return false;
		}

		if ( $postAfter->post_status !== 'publish' || $postBefore->post_status === 'publish' ){
			return false;
		}

		if ( $aUserInfo['role'] === 'wiloke_submission' ) {
			$this->_updateNotification( get_current_user_id(), $postAfter->post_author, $postID, 'approved' );
		}

		if ( !$this->checkPostExists($postAfter) ){
			if ( \Wiloke::$wilokePredis ) {
				$aListOfFollowing = \Wiloke::hGet( RegisterFollow::$redisFollower, $postAfter->post_author, true );
				if ( ! empty( $aListOfFollowing ) ) {
					foreach ( $aListOfFollowing as $receiveID ) {
						$this->_updateNotification( $postAfter->post_author, $receiveID, $postID, 'newpost' );
					}
				}
			} else {
				global $wpdb;
				$tblName          = $wpdb->prefix . AltertableFollowing::$tblName;
				$aListOfFollowing = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT follower_ID FROM $tblName WHERE user_ID=%d",
						$aUserInfo['ID']
					),
					ARRAY_A
				);

				if ( ! empty( $aListOfFollowing ) ) {
					foreach ( $aListOfFollowing as $receiveID ) {
						$this->_updateNotification( $postAfter->post_author, $receiveID, $postID, 'newpost' );
					}
				}
			}
		}
	}

	public function checkPostExists($oPost){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTableNotifications::$tblName;
		$result = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT object_ID FROM $tblName WHERE object_ID=%d AND author_ID=%d",
				$oPost->ID, $oPost->post_author
			)
		);

		if ( empty($result) ){
			return false;
		}

		return true;
	}

	public function notifyAboutNewPurchased($customerID, $paymentID, $status, $packageID, $profileID){
        $this->_updateNotification($customerID, WilokeUser::getSuperAdminID(), $packageID, 'purchased_new_plan');
    }

	public function addNotification($authorID, $receiveID, $objectID, $type){
        $this->_updateNotification($authorID, $receiveID, $objectID, $type);
    }

	public function pushReview($reviewID, $postID, $authorID){
		$receiveID = get_post_field('post_author', $postID);
		if ( empty($receiveID) ){
			return false;
		}

		if ( \Wiloke::$wilokePredis ){
			\Wiloke::$wilokePredis->set(\Wiloke::$prefix.self::$reviewAndPostKey.'|'.$receiveID, $postID);
		}
		$this->_updateNotification($authorID, $receiveID, $reviewID, 'review');
		self::refreshNotification($authorID);
	}

	public static function refreshNotification($authorID){
		update_option(\Wiloke::$prefix.'latest_check_notification|'.$authorID, time());
		update_option(\Wiloke::$prefix.'has_not_new_notification|'.$authorID, false);
		if ( \Wiloke::$wilokePredis ){
			\Wiloke::$wilokePredis->set(\Wiloke::$prefix.'notification_latest_check|'.$authorID, time());
			\Wiloke::$wilokePredis->set(\Wiloke::$prefix.'has_not_new_notification|'.$authorID, false);
		}
	}

	/**
	 * Updating Notification
	 * @since 1.0
	 */
	protected function _updateNotification($authorID, $receiveID, $objectID, $type){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTableNotifications::$tblName;

		$wpdb->insert(
			$tblName,
			array(
				'author_ID' => $authorID,
				'receive_ID'=> $receiveID,
				'object_ID' => $objectID,
				'type'      => $type
			),
			array(
				'%d',
				'%d',
				'%d',
				'%s'
			)
		);

		if ( \Wiloke::$wilokePredis ){
			$aData = array(
				'author_ID' => $authorID,
				'object_ID' => $objectID,
				'type'      => $type,
				'created_at'=> time(),
			);
			// We only keep only 8 notifications
			$length = \Wiloke::$wilokePredis->lpush(\Wiloke::$prefix.self::$newestNotificationKey.':'.$receiveID, json_encode($aData));
			if ( $length > self::$maxListingsOnMenu ){
				\Wiloke::$wilokePredis->rpop(\Wiloke::$prefix.self::$newestNotificationKey);
			}
			\Wiloke::$wilokePredis->hset(\Wiloke::$prefix.self::$allNotificationsKey.'|'.$receiveID, $objectID, json_encode($aData));
		}
	}

	/**
	 * Delete Notification
	 * @since 1.0
	 */
	protected function _deleteNotification($receiveID, $objectID){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTableNotifications::$tblName;

		$wpdb->delete(
			$tblName,
			array(
				'receive_ID'=> $receiveID,
				'object_ID' => $objectID
			),
			array(
				'%d',
				'%d'
			)
		);

		if ( \Wiloke::$wilokePredis ){
			$aValue = \Wiloke::$wilokePredis->lrange(\Wiloke::$prefix.self::$newestNotificationKey.':'.$receiveID, 0, self::$maxListingsOnMenu);
			if ( !empty($aValue) ){
				foreach ( $aValue as $key => $oInfo ){
					$oInfo = json_decode($oInfo);
					if ( $objectID == $oInfo->object_ID ){
						\Wiloke::$wilokePredis->lrem(\Wiloke::$prefix.self::$newestNotificationKey.':'.$receiveID, 0, $key);
					}
				}
			}
			\Wiloke::$wilokePredis->hdel(\Wiloke::$prefix.self::$allNotificationsKey.'|'.$receiveID, $objectID);
		}
	}

	public function ajaxRemoveNotificationItem(){
		if ( check_ajax_referer('security', 'wiloke-nonce', false) ){
			wp_send_json_error();
		}

		$receivedID = get_current_user_id();
		if ( empty($receivedID) ){
			wp_send_json_error(esc_html__('You do not have permission to delete this notification', 'wiloke'));
		}

		self::_deleteNotification($receivedID, $_POST['object_ID']);
		wp_send_json_success();
	}

	public static function firstFetchNotification(){
		if ( \Wiloke::$wilokePredis ){
			$aResults = self::scanNotifications();
			if ( empty($aResults[1]) ){
				esc_html_e('Whoops! You don\'t have any notification yet! Try to follow some users and get useful articles', 'wiloke');
			}else{
				echo '<ul id="wiloke-show-notifications" data-cursor="'.esc_attr($aResults[0]).'" class="notifications__list">';
				foreach ( $aResults[1] as $aResult ){
					$aResult = json_decode($aResult, true);
					echo self::renderNotificationItem($aResult);
				}
				echo '</ul>';
			}
		}else{
			$aResults = self::queryNotifications();
			if ( empty($aResults) ){
				esc_html_e('Whoops! You don\'t have any notification yet! Try to follow some users and get useful articles', 'wiloke');
			}else{
				echo '<ul id="wiloke-show-notifications" data-cursor="0" class="notifications__list">';
					foreach ( $aResults as $aResult ){
						echo self::renderNotificationItem($aResult);
					}
				echo '</ul>';
			}
		}
	}

	public function ajaxFetchNotification(){
		if ( check_ajax_referer('security', 'wiloke-nonce', false) ){
			wp_send_json_error();
		}

		if ( \Wiloke::$wilokePredis ){
			if ( !isset($_GET['cursor']) || empty($_GET['cursor']) ){
				wp_send_json_error();
			}
			self::$cursor = absint($_GET['cursor']);
			$aResults = self::scanNotifications();
			if ( empty($aResults[1]) ){
				wp_send_json_error();
			}

			$aListOfNotifications = $aResults[1];
			self::$cursor = $aListOfNotifications[0];
			ob_start();
			foreach ( $aListOfNotifications as $aResult ){
				$aResult = is_array($aResult) ? $aResult : json_decode($aResult, true);
				self::renderNotificationItem($aResult);
			}
			$content = ob_get_clean();
		}else{
			self::$aPostsNotIn = isset($_GET['posts__not_in']) ? array_map('absint', explode(',', $_GET['posts__not_in'])) : '';
			self::$filterBy = $_GET['filter_by'];

			$aResults = self::queryNotifications();
			if ( empty($aResults) ){
				wp_send_json_error();
			}
			ob_start();
			foreach ($aResults as $aResult){
				self::renderNotificationItem($aResult);
			}
			$content = ob_get_clean();
		}

		wp_send_json_success(
			array(
				'notifications' => $content,
				'cursor' =>  self::$cursor
			)
		);
	}

	public static function countTotalNotifications(){
		if ( \Wiloke::$wilokePredis ){
			return \Wiloke::$wilokePredis->hlen(\Wiloke::$prefix.self::$allNotificationsKey.'|'.\WilokePublic::$oUserInfo->ID);
		}else{
			global $wpdb;
			$tblName = $wpdb->prefix . AlterTableNotifications::$tblName;
			return $wpdb->get_var($wpdb->prepare(
				"SELECT COUNT(object_ID) FROM $tblName WHERE receive_ID=%d",
				\WilokePublic::$oUserInfo->ID
            ));
		}
	}

	public static function scanNotifications(){
		$aResult = \Wiloke::$wilokePredis->hscan(\Wiloke::$prefix.self::$allNotificationsKey.'|'.\WilokePublic::$oUserInfo->ID, self::$cursor, array('COUNT'=>self::$maxNotifications));
		return $aResult;
	}

	public static function queryNotifications(){
		global $wpdb;
		$tblName = $wpdb->prefix . AlterTableNotifications::$tblName;
		$sql = "SELECT * FROM $tblName";
		$concat = " WHERE ";


		if ( !empty(self::$aPostsNotIn) ){
			$sql .= $concat . "$tblName.object_ID NOT IN (".implode(',', self::$aPostsNotIn).")";
			$concat = " AND ";
		}

		if ( !empty(self::$filterBy) ){
			if ( self::$filterBy === 'review' ){
				$sql .= $concat . "$tblName.type=%s";
			}else{
				$sql .= $concat . "$tblName.type!=%s";
			}

			$concat = ' AND ';
        }

        $sql .= $concat . " $tblName.receive_ID=".absint(get_current_user_id());

		$sql .= " LIMIT 10";

		if ( !empty(self::$filterBy) ){
			$sql = $wpdb->prepare(
				$sql,
				self::$filterBy
			);
		}

		$aResults = $wpdb->get_results($sql, ARRAY_A);
		return $aResults;
	}

	public function updateLastCheckedNotifications(){
		if ( !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error();
		}

        $currentUserID = get_current_user_id();
        update_option(\Wiloke::$prefix.'latest_check_notification|'.$currentUserID, time());
        update_option(\Wiloke::$prefix.'has_not_new_notification|'.$currentUserID, true);
        if ( \Wiloke::$wilokePredis ){
            \Wiloke::$wilokePredis->set(\Wiloke::$prefix.'notification_latest_check|'.$currentUserID, time());
            \Wiloke::$wilokePredis->set(\Wiloke::$prefix.'has_not_new_notification|'.$currentUserID, true);
        }
	}
}