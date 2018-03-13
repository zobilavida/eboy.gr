<?php
namespace WilokeListGoFunctionality\AlterTable;

class AlterTableGeoPosition implements AlterTableInterface{
	public static $tblName = 'wiloke_listgo_geo_position';

	public $version = '1.1';

	public function __construct() {
		add_action('plugins_loaded', array($this, 'createTable'));
	}

	public function createTable() {
		if ( get_option(self::$tblName) && (version_compare(get_option(self::$tblName), $this->version, '>=')) ){
			return false;
		}

		global $wpdb;
		$tblName = $wpdb->prefix . self::$tblName;
		$postsTbl = $wpdb->prefix . 'posts';

		if ($result = $wpdb->query("SHOW TABLES LIKE '".$tblName."'") ){
			update_option(self::$tblName, $this->version);
			return false;
		}

		$charsetCollect = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $tblName(
			ID bigint(20) NOT NULL AUTO_INCREMENT,
			lat DECIMAL(11, 8) NOT NULL,
			lng DECIMAL(11, 8) NOT NULL,
			postID bigint(20) UNSIGNED NOT NULL,
			PRIMARY KEY (ID),
			FOREIGN KEY (postID) REFERENCES $postsTbl(ID) ON DELETE CASCADE
		) $charsetCollect";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		update_option(self::$tblName, $this->version);
	}

	public function deleteTable() {
		// TODO: Implement deleteTable() method.
	}
}