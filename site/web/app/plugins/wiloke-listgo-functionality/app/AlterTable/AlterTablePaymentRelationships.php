<?php
/**
 * Showing the relationships between user ID and payment ID
 *
 * @since 1.0
 * @author Wiloke
 * @link https://wiloke.com
 * @package Wiloke/Themes
 * @subpackage ListGo
 */

namespace WilokeListGoFunctionality\AlterTable;

class AlterTablePaymentRelationships implements AlterTableInterface{
	public static $tblName = 'wiloke_listgo_payment_relationships';
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
			payment_ID bigint(20),
			package_ID bigint(20) NOT NULL,
			object_ID bigint(20) NOT NULL,
			status VARCHAR (100) NOT NULL
		) $charsetCollect";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		update_option(self::$tblName, $this->version);
	}

	public function deleteTable() {
		// TODO: Implement deleteTable() method.
	}
}