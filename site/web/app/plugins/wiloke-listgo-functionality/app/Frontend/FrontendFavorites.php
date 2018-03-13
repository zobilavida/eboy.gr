<?php
namespace WilokeListGoFunctionality\Frontend;
use WilokeListGoFunctionality\AlterTable\AlterTableFavirote as WilokeFavoritesTbl;

class FrontendFavorites{
	public static $redisFollowing = 'wiloke_listgo_following';
	public static $redisFollower = 'wiloke_listgo_follower';

	public function __construct() {
		add_action('wp_ajax_wiloke_remove_favorite', array($this, 'removeFavorite'));
	}

	public function removeFavorite(){
		check_ajax_referer('wiloke-nonce', 'security');
		global $wpdb;
		$tableName = $wpdb->prefix . WilokeFavoritesTbl::$tblName;

		$wpdb->delete(
			$tableName,
			array(
				'user_ID' => get_current_user_id(),
				'post_ID' => $_POST['ID']
			),
			array(
				'%d',
				'%d'
			)
		);
	}
}