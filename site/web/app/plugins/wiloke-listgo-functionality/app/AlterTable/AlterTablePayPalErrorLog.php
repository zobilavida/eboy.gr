<?php
/*
 |--------------------------------------------------------------------------
 | Error Log Table
 |--------------------------------------------------------------------------
 | This table container list of favorite of user
 |
 */

namespace WilokeListGoFunctionality\AlterTable;


class AlterTablePayPalErrorLog{
	static public $tblName = 'wiloke_listgo_paypal_error_log';
	public $version = '1.5';

	public function __construct() {
		add_action('plugins_loaded', array($this, 'createTable'));
	}

	/**
	 * This table shows the package information at the time when customer purchased this package and status of the package: available or unavailable (Number of posts of the package was used already
	 * @since 1.0
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
			user_ID bigint(20) NOT NULL DEFAULT 0,
			payment_ID bigint(20) NOT NULL DEFAULT 0,
			reason TEXT NULL 
		) $charsetCollate";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);
		update_option(self::$tblName, $this->version);
	}

	public function deleteTable(){

	}
}