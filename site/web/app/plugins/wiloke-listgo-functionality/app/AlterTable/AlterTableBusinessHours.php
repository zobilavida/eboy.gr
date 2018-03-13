<?php
/*
 |--------------------------------------------------------------------------
 | Favorite Table
 |--------------------------------------------------------------------------
 | This table container list of favorite of user
 |
 */

namespace WilokeListGoFunctionality\AlterTable;

class AlterTableBusinessHours implements AlterTableInterface{
	public static $tblName = 'wiloke_listgo_businesshours';
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

		$charsetCollate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $tblName (
          ID bigint(9) NOT NULL AUTO_INCREMENT,
          always_open VARCHAR (4) NOT NULL,
          post_ID bigint(9) unsigned NOT NULL,
          day_of_week bigint(100)  NOT NULL,
          open_time time NOT NULL,
          close_time time NOT NULL,
          PRIMARY KEY (`id`)
        ) $charsetCollate";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		update_option(self::$tblName, $this->version);
	}

	public function deleteTable() {
		// TODO: Implement deleteTable() method.
	}
}
