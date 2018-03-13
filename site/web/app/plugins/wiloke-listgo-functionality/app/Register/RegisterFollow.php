<?php
namespace WilokeListGoFunctionality\Register;
use WilokeListGoFunctionality\AlterTable\AltertableFollowing;

class RegisterFollow implements RegisterInterface{
	public $slug = 'listgo-followers';
	public static $redisFollowing = 'wiloke_listgo_following';
	public static $redisFollower = 'wiloke_listgo_follower';

	public function __construct() {
		add_action('admin_menu', array($this, 'register'));
		add_action('admin_enqueue_scripts', array($this, 'adminScripts'), 10, 1);
		add_action('wp_ajax_wiloke_follow', array($this, 'handleFollow'));
		add_action('wp_ajax_wiloke_follow_export', array($this, 'exportFile'));
	}

	public function handleFollow(){
		global $wiloke;
		if ( !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error($wiloke->aConfigs['translation']['ajaxerror']);
		}

		if ( !isset($_POST['author_id']) || empty($_POST['author_id']) ){
			wp_send_json_error($wiloke->aConfigs['translation']['ajaxerror']);
		}

		$authorID = absint($_POST['author_id']);
		$userInfo = get_user_meta($authorID, 'nickname', true);

		if(empty($userInfo)){
			wp_send_json_error($wiloke->aConfigs['translation']['usernotexists']);
		}

		$currentUserID  = get_current_user_id();

		if ( empty($currentUserID) ){
			wp_send_json_error($wiloke->aConfigs['translation']['needsingup']);
		}

		if ( $authorID === $currentUserID ){
			wp_send_json_error($wiloke->aConfigs['translation']['needsingup']);
		}

		if ( absint($_POST['author_id']) === absint($currentUserID) ){
			wp_send_json_error(esc_html__('You can not follow your self', 'wiloke'));
		}

		$aUserInfo = \WilokePublic::getUserMeta($currentUserID);

		if ( empty($aUserInfo['user_email']) ){
			wp_send_json_error($wiloke->aConfigs['translation']['signupbutnotemail']);
		}

		global $wpdb;
		$tblName = $wpdb->prefix . AltertableFollowing::$tblName;

		$isExisted = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT follower_ID FROM $tblName WHERE follower_ID=%d AND user_ID=%d",
				$currentUserID, $authorID
			)
		);

		if ( !empty($isExisted) ){
			$wpdb->delete(
				$tblName,
				array(
					'follower_ID' => $currentUserID,
					'user_ID'     => $authorID
				),
				array(
					'%d',
					'%d'
				)
			);

			if ( \Wiloke::$wilokePredis ){
				$following = \Wiloke::hGet(self::$redisFollowing, $currentUserID, true);
                $aOldFollow = $following;
				if ( !empty($following) ){
					$key = array_search($authorID, $following);
					unset($following[$key]);
				}

				\Wiloke::$wilokePredis->hdel(self::$redisFollowing, $currentUserID);
				\Wiloke::hSet(self::$redisFollowing, $currentUserID, $following);

				$follower = \Wiloke::hGet(self::$redisFollower, $authorID, true);

				if ( !empty($follower) ){
					$key = array_search($currentUserID, $follower);
					unset($follower[$key]);
				}

				\Wiloke::$wilokePredis->hdel(self::$redisFollowing, $authorID);
				\Wiloke::hSet(self::$redisFollower, $authorID, $follower);
			}

			wp_send_json_success(
				array(
					'msg'    =>$wiloke->aConfigs['translation']['followsuccess'],
					'status' => 'unfollowing',
                    'text'   => esc_html__('Follow', 'wiloke')
				)
			);
        }else{
			$wpdb->insert(
				$tblName,
				array(
					'follower_ID' => $currentUserID,
					'user_ID'     => $authorID
				),
				array(
					'%d',
					'%d'
				)
			);

			if ( \Wiloke::$wilokePredis ){
				$following = \Wiloke::hGet(self::$redisFollowing, $currentUserID, true);
				$following = !empty($following) ? $following : array();
				array_push($following, $authorID);
				\Wiloke::hSet(self::$redisFollowing, $currentUserID, $following);

				$follower = \Wiloke::hGet(self::$redisFollower, $authorID, true);
				$follower = !empty($follower) ? $follower : array();
				array_push($follower, $currentUserID);
				\Wiloke::hSet(self::$redisFollower, $authorID, $follower);
			}

			wp_send_json_success(
                array(
                    'msg'    =>$wiloke->aConfigs['translation']['followsuccess'],
                    'status' => 'following',
                    'text'   => esc_html__('Following', 'wiloke')
                )
            );
        }
	}

	public function adminScripts($hook){
		if ( $hook === 'toplevel_page_'.$this->slug ){
			wp_enqueue_style($this->slug, plugin_dir_url(__FILE__) . '../../admin/assets/semantic-ui/form.min.css', array(), WILOKE_LISTGO_FC_VERSION);
			wp_enqueue_script('listgo-follow', plugin_dir_url(__FILE__) . '../../admin/js/follow-table.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
		}
	}

	public function register() {
		add_menu_page(esc_html__('Followers', 'wiloke'), esc_html__('Followers', 'wiloke'), 'publish_posts', $this->slug, array($this, 'settings'), 'dashicons-smiley', 28);
	}

	public function exportFile(){
		if ( !current_user_can('edit_posts') ){
			return false;
		}


		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");

		// force download
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");

		// disposition / encoding on response body
		header("Content-Disposition: attachment;filename=following".date('Y-m-D') . '.csv');
		header("Content-Transfer-Encoding: binary");
		$csv_header = '';
		$csv_header .= 'NickName, Email Address';
		$aFollowers = $this->getFollowing();

		$csv_row ='';
		foreach ( $aFollowers as $aFollower ){
			$csv_row .= $aFollower['display_name'] . ', ' . $aFollower['user_email'] . "\n";
		}
		echo $csv_header . $csv_row;
		die();
	}

	public function settings(){
		?>
		<div id="listgo-table-wrapper" style="margin: 20px; auto;">
			<table id="listgo-table" class="ui striped table">
				<thead>
					<tr>
						<th colspan="3"><?php esc_html_e('List of followers', 'wiloke'); ?></th>
					</tr>
					<tr>
						<th>#</th>
						<th><?php esc_html_e('Nick name', 'wiloke'); ?></th>
						<th><?php esc_html_e('Email address', 'wiloke'); ?></th>
					</tr>
                </thead>
                <tbody>
                    <?php
                        $aFollowers = $this->getFollowing();
                        if ( empty($aFollowers) ) :
                    ?>
                        <tr>
                            <td colspan="3"><?php esc_html_e('You have not anyone following yet', 'wiloke'); ?></td>
                        </tr>
                    <?php
                        else:
                            foreach ( $aFollowers as $key => $aFollower ) :
                    ?>
                        <tr>
                            <td><?php echo esc_attr($key); ?></td>
                            <td><?php echo isset($aFollower['display_name']) ? esc_html($aFollower['display_name']) : esc_html__('No name', 'wiloke'); ?></td>
                            <td><?php echo esc_html($aFollower['user_email']); ?></td>
                        </tr>
                    <?php
                            endforeach;
                        endif;
                    ?>
                </tbody>
			</table>
			<div style="margin-top: 20px;">
			<?php if ( !empty($aFollowers) ) : ?>
				<?php echo wp_nonce_field('wiloke_admin_nonce_action', 'wiloke_admin_nonce_field'); ?>
				<a class="js_export button button-primary" href="<?php echo esc_url(admin_url('admin-ajax.php?action=wiloke_follow_export')); ?>"><?php esc_html_e('Export Following', 'wiloke'); ?></a>
			<?php endif; ?>
			</div>
		</div>
		<?php
	}

	public function getFollowing(){
		$userID = get_current_user_id();

		if ( \Wiloke::$wilokePredis ){
			$aFollowers = \Wiloke::hGet(self::$redisFollower, $userID, true);
			if ( !empty($aFollowers) ){
				foreach ( $aFollowers as $key => $userID ){
					$aFollowers[$key] = \Wiloke::hGet(\WilokeUser::$redisKey, $userID, true);
				}
			}
		}else{
			global $wpdb;
			$tblUser = $wpdb->prefix . 'users';
			$tblFollowing = $wpdb->prefix . AltertableFollowing::$tblName;

			$aFollowers = $wpdb->get_results("SELECT $tblUser.user_nicename, $tblUser.user_email FROM $tblUser INNER JOIN $tblFollowing ON ($tblFollowing.user_ID = $tblUser.ID) WHERE $tblUser.ID = {$userID}", ARRAY_A);
		}

		return $aFollowers;
	}
}