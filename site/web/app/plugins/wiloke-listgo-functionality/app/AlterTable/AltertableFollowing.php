<?php
namespace WilokeListGoFunctionality\AlterTable;

class AltertableFollowing implements AlterTableInterface{
	public static $tblName = 'wiloke_listgo_following';

	public $version = '1.0';

	public function __construct() {
		add_action('plugins_loaded', array($this, 'createTable'));
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

		$charsetCollect = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $tblName(
			user_ID bigint(20) NOT NULL DEFAULT 0,
			follower_ID bigint(20) NOT NULL DEFAULT 0
		) $charsetCollect";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		update_option(self::$tblName, $this->version);
	}

	public function deleteTable() {
		// TODO: Implement deleteTable() method.
	}
}