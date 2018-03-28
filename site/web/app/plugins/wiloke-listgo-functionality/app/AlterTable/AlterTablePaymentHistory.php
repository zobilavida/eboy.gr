<?php
/**
 * Saving all payment history into this store
 *
 * @since 1.0
 * @author Wiloke
 * @link https://wiloke.com
 * @package Wiloke/Themes
 * @subpackage ListGo
 */

namespace WilokeListGoFunctionality\AlterTable;

class AlterTablePaymentHistory implements AlterTableInterface{
	public static $tblName = 'wiloke_listgo_payment_history';
	public $version = '1.0';
	private $addedColumnkeys = 'wiloke_listgo_payment_history_added_columns';

	public function __construct() {
		add_action('plugins_loaded', array($this, 'createTable'));
		add_action('plugins_loaded', array($this, 'addProfileStatusColumn'));
		add_action('plugins_loaded', array($this, 'addProfileIDColumn'));
		add_action('plugins_loaded', array($this, 'addPackageType'));
	}

	public function addedColumn(){
		$value = get_option($this->addedColumnkeys);
		return empty($value) ? array() : $value;
	}

	public function addProfileStatusColumn(){
		global $wpdb;
		$columnName = 'profile_status';
		if ( !get_option(self::$tblName)){
			return false;
		}

		$tblName = $wpdb->prefix . self::$tblName;
		if ( !$wpdb->query("SHOW TABLES LIKE '".$tblName."'") ){
			return false;
		}

		if ( $this->checkColumnExisted($columnName) ){
			return false;
		}

		$wpdb->query("ALTER TABLE {$tblName} ADD {$columnName} VARCHAR(100) NULL AFTER status");
	}

	public function addPackageType(){
		global $wpdb;
		$columnName = 'package_type';
		if ( !get_option(self::$tblName) ){
			return false;
		}

		$tblName = $wpdb->prefix . self::$tblName;
		if ( !$wpdb->query("SHOW TABLES LIKE '".$tblName."'") ){
			return false;
		}

		if ( $this->checkColumnExisted($columnName) ){
			return false;
		}

		$wpdb->query("ALTER TABLE {$tblName} ADD {$columnName} VARCHAR(100) NULL AFTER package_ID");
	}

	public function addProfileIDColumn(){
		global $wpdb;
		$columnName = 'profile_ID';
		if ( !get_option(self::$tblName) ){
			return false;
		}

		$tblName = $wpdb->prefix . self::$tblName;
		if ( !$wpdb->query("SHOW TABLES LIKE '".$tblName."'") ){
			return false;
		}

		if ( $this->checkColumnExisted($columnName) ){
			return false;
		}

		$wpdb->query("ALTER TABLE {$tblName} ADD {$columnName} VARCHAR(50) NULL AFTER profile_status");
	}

	private function checkColumnExisted($columnName){
		global $wpdb;
		$tblName = $wpdb->prefix . self::$tblName;

		return $wpdb->query(
			$wpdb->prepare(
				"SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=%s AND TABLE_NAME=%s AND COLUMN_NAME=%s",
				$wpdb->dbname, $tblName, $columnName
			)
		);
	}

	/**
	 * wiloke_listgo_payment_history
	 *
	 * @param ID int Session ID
	 * @param package_ID int Package ID
	 * @param token string If the session is progressed by PayPal method, token is generated by PayPal else it will be generated by Wiloke System
	 * @param method string. At the version 1.0, We support 3 methods: Credit Card, PayPal and Check Payment
	 * @param status. There are 4 kind of statuses: pending, cancel, refund and approved
	 * @param created_at: When this post is created. This value will be updated as the customer click on Pay & Pushlic button
	 * @param updated_at: It's the same created_at at the first time and It will be updated at the next time, for example, after the PayPal session is completed
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

		$charsetCollect = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $tblName(
			ID bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			user_ID bigint(20) NOT NULL DEFAULT 0,
			package_ID bigint(20) NOT NULL DEFAULT 0,
			package_type VARCHAR (100) NOT NULL DEFAULT 'pricing',
			token VARCHAR (200) NOT NULL,
			method VARCHAR (50) NOT NULL,
			information TEXT NULL,
		  	total mediumint(20) NOT NULL,
		  	currency VARCHAR (100) NOT NULL,
			status VARCHAR (50) NOT NULL,
			profile_status VARCHAR (100) NULL,
			profile_ID VARCHAR (50) NULL,
			created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			updated_at TIMESTAMP NOT NULL DEFAULT 0
		) $charsetCollect";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		update_option(self::$tblName, $this->version);
	}

	public function deleteTable() {
		// TODO: Implement deleteTable() method.
	}
}