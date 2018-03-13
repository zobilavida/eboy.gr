<?php
/**
 * Contains all information about those packages that our customers have been purchased
 *
 * @since 1.0
 * @author Wiloke
 * @package Wiloke/Themes
 * @subpackage Listgo
 * @link https://wiloke.com
 */

namespace WilokeListGoFunctionality\AlterTable;

class AlterTablePackageStatus implements AlterTableInterface{
	static public $tblName = 'wiloke_listgo_package_status';
	public $version = '1.0';

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
			package_ID bigint(20) NOT NULL,
			user_ID bigint(20) NOT NULL,
			payment_ID bigint(20) NOT NULL,
			package_information TEXT NOT NULL,
			status VARCHAR (20) NOT NULL
		) $charsetCollate";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		update_option(self::$tblName, $this->version);
	}

	public function deleteTable() {
		// TODO: Implement deleteTable() method.
	}
}