<?php
namespace WilokeListGoFunctionality\AlterTable;

class AlterTableReport implements AlterTableInterface{
	public static $tblName = 'wiloke_listgo_report';
	public $version = '1.0';

	public function __construct() {
		add_action('plugins_loaded', array($this, 'createTable'));
	}

	public function createTable() {
		if ( get_option(self::$tblName) && (version_compare(get_option(self::$tblName), $this->version, '>=')) ){
			return false;
		}

		global $wpdb;
		$realName = $wpdb->prefix . self::$tblName;

		if ($result = $wpdb->query("SHOW TABLES LIKE '".$realName."'") ){
			update_option(self::$tblName, $this->version);
			return false;
		}

		$charsetCollate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $realName(
			post_ID bigint(9) DEFAULT 0  NOT NULL,
			reported_by_IP VARCHAR(100)  NOT NULL,
			reason VARCHAR(500) NOT NULL,
			reported_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
		) $charsetCollate";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);
		update_option(self::$tblName, $this->version);
	}

	public function deleteTable() {
		// TODO: Implement deleteTable() method.
	}
}