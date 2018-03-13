<?php
namespace WilokeListGoFunctionality\AlterTable;

class AlterTableNotifications implements AlterTableInterface{
	public $version = '1.1';
	public static $tblName = 'wiloke_listgo_notifications';

	public function __construct() {
		add_action('plugins_loaded', array($this, 'createTable'));
	}

	/**
	 * type: review / newlisting
	 */
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

		$sql = "CREATE TABLE $tblName(
				author_ID bigint(20) NOT NULL DEFAULT 0,
				receive_ID bigint(20) NOT NULL DEFAULT 0,
				object_ID bigint(20) NOT NULL DEFAULT 0,
				type VARCHAR(15) NOT NULL,
				created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
			) $charsetCollate";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		update_option(self::$tblName, $this->version);
	}

	public function deleteTable() {
		// TODO: Implement deleteTable() method.
	}
}