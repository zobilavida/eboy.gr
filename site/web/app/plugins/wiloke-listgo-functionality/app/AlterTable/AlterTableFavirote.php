<?php
/*
 |--------------------------------------------------------------------------
 | Favorite Table
 |--------------------------------------------------------------------------
 | This table container list of favorite of user
 |
 */

namespace WilokeListGoFunctionality\AlterTable;

class AlterTableFavirote implements AlterTableInterface{
	public static $tblName = 'wiloke_listgo_favorite';
	public $version = '1.2';

	public function __construct() {
		add_action('plugins_loaded', array($this, 'createTable'));
		add_action('wp_ajax_wiloke_toggle_favorite_list', array($this, 'toggleFavoriteList'));
		add_action('wp_ajax_nopriv_wiloke_toggle_favorite_list', array($this, 'toggleFavoriteList'));
	}

	public function createTable() {
		if ( get_option(self::$tblName) && (version_compare(get_option(self::$tblName), $this->version, '>=')) ){
			return false;
		}

		global $wpdb;
		$tblName = $wpdb->prefix . self::$tblName;

		if ($result = $wpdb->query("SHOW TABLES LIKE '".$tblName."'") ){
			update_option(self::$tblName, $this->version);
			return false;
		}

		$charsetCollate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $tblName (
          user_ID bigint(9) DEFAULT 0,
          post_ID bigint(9) DEFAULT 0 NOT NULL,
          user_IP VARCHAR(100)  NOT NULL,
          status INT DEFAULT 0
        ) $charsetCollate";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		update_option(self::$tblName, $this->version);
	}

	public function deleteTable() {
		// TODO: Implement deleteTable() method.
	}

	/**
	 * Add a post to favorite listing
	 * @since 1.0
	 */
	public function toggleFavoriteList(){
		if ( !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error();
		}

		if ( !isset($_POST['ID']) || empty($_POST['ID']) ){
			wp_send_json_error();
		}

		$userIP = \Wiloke::clientIP();

		if ( !$userIP ){
			wp_send_json_error();
		}

		$userID = is_user_logged_in() ? get_current_user_id() : 0;
		$userID = absint($userID);

		global $wpdb;
		$tableName = $wpdb->prefix . 'wiloke_listgo_favorite';

		if ( !empty($userID) ){
			$sql = "SELECT status FROM $tableName WHERE post_ID=%d AND user_ID=%d";

			$status = $wpdb->get_var(
				$wpdb->prepare($sql, $_POST['ID'], $userID)
			);
		}else{
			$sql = "SELECT status FROM $tableName WHERE post_ID=%d AND user_IP=%s";
			$status = $wpdb->get_var(
				$wpdb->prepare($sql, $_POST['ID'], $userIP)
			);
		}

		if ( $status !== null ){
			$status = absint($status);
			$newStatus = $status === 1 ? 0 : 1;

			if ( !empty($userID) ){
				$wpdb->update(
					$tableName,
					array(
						'status'    => $newStatus
					),
					array(
						'user_ID' => $userID,
						'post_ID' => $_POST['ID']
					),
					array(
						'%d'
					),
					array(
						'%d',
						'%d'
					)
				);
			}else{
				$wpdb->update(
					$tableName,
					array(
						'status'  => $newStatus,
						'user_ID' => $userID
					),
					array(
						'user_IP' => $userIP,
						'post_ID' => $_POST['ID']
					),
					array(
						'%d',
						'%d'
					),
					array(
						'%s',
						'%d'
					)
				);
			}
		}else{
			$status = 0;
			$wpdb->insert(
				$tableName,
				array(
					'status'  => 1,
					'user_ID' => $userID,
					'post_ID' => $_POST['ID'],
					'user_IP' => $userIP
				),
				array(
					'%d',
					'%d',
					'%d',
					'%s'
				)
			);
		}

		if ( $status === 0 ){
			wp_send_json_success('added');
		}else{
			wp_send_json_success('removed');
		}

	}

	/**
	 * get status
	 * @since 1.0
	 */
	public static function getStatus($postID){
		global $wpdb;

		$userID = is_user_logged_in() ? get_current_user_id() : 0;
		$tblName = $wpdb->prefix . self::$tblName;

		if ( !empty($userID) ){
			$sql = "SELECT status FROM $tblName WHERE post_ID=%d AND user_ID=%d";
			$status = $wpdb->get_var(
				$wpdb->prepare($sql,$postID, $userID)
			);
		}

//		else{
//			$userIP = \Wiloke::clientIP();
//			if ( !$userIP ){
//				return false;
//			}
//			$status = $wpdb->get_var(
//				$wpdb->prepare(
//					"SELECT status FROM $tblName WHERE user_IP = %s AND post_ID = %d",
//					$userIP,
//					$postID
//				)
//			);
//		}

		if ( !isset($status) || empty($status) ){
			return 0;
		}

		return 1;
	}
}
