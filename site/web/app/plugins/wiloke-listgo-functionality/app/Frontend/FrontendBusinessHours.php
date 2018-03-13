<?php
namespace WilokeListGoFunctionality\Frontend;

use WilokeListGoFunctionality\AlterTable\AlterTableBusinessHours as BusinessHoursTbl;

class FrontendBusinessHours{
	public $openTime = '';
	public $alwaysOpen = 'yes';
	public $closeTime = '';
	public $dayOfWeek = '';
	public $postID = 0;
	protected $tblName = '';
	protected $isExisted = null;
	protected $metaKey = 'wiloke_listgo_business_hours';

	public function __construct() {
		add_action('before_delete_post', array($this, 'beforeDeletePosts'), 10);
		add_action('save_post_listing', array($this, 'hasChangeBusinessHours'), 10, 2);
	}

	public function createBusinessHoursData($postID, $post){
		if ( $post->post_type !== 'listing' || $post->post_status !== 'publish' ){
			return false;
		}
		$this->postID = $postID;

		$aBusinessHours = get_post_meta($postID, $this->metaKey, true);
		$toggle = get_post_meta($postID, 'wiloke_toggle_business_hours', true);
		$this->alwaysOpen = $toggle === 'disable' ? 'yes' : 'no';

		foreach ( $aBusinessHours as $dayOfWeeks => $aData ){
			$this->dayOfWeek = $dayOfWeeks;
			if ( isset($aData['closed']) ){
				$this->openTime  = '00:00:00';
				$this->closeTime = '00:00:00';
			}else{
				$this->openTime  = $aData['start_hour'] . ':' . $aData['start_minutes'] . ' ' . $aData['start_format'];
				$this->closeTime = $aData['close_hour'] . ':' . $aData['close_minutes'] . ' ' . $aData['close_format'];
			}
			$this->prepareData();
		}
	}

	public function hasChangeBusinessHours($postID, $post){
		if ( $post->post_type !== 'listing' || $post->post_status !== 'publish' ){
			return false;
		}
		$this->postID = $postID;

		if ( !isset($_POST['listgo_bh']) ){
			return false;
		}

		$toggleBusinessHour = sanitize_text_field($_POST['wiloke_toggle_business_hours']);
		$this->alwaysOpen = $toggleBusinessHour === 'disable' ? 'yes' : 'no';
		unset($_POST['listgo_bh']['toggle_business_hour']);
		$aBusinessHours = $_POST['listgo_bh'];
		foreach ( $aBusinessHours as $dayOfWeeks => $aData ){
			$this->dayOfWeek = $dayOfWeeks;
			if ( isset($aData['closed']) ){
				$this->openTime  = '00:00:00';
				$this->closeTime = '00:00:00';
			}else{
				$this->openTime  = $aData['start_hour'] . ':' . $aData['start_minutes'] . ' ' . $aData['start_format'];
				$this->closeTime = $aData['close_hour'] . ':' . $aData['close_minutes'] . ' ' . $aData['close_format'];
			}
			$this->prepareData();
		}
	}

	public function beforeDeletePosts($postID){
		if ( get_post_field('post_type', $postID) === 'listing' ){
			$this->postID = absint($postID);
			$this->delete();
		}
	}

	/*
	 * Convert Hours, day of week before inserting/updating
	 * @since 1.0
	 */
	public function prepareData(){
		$this->openTime  = !empty($this->openTime) ? date('H:i:s', strtotime($this->openTime)) : '00:00:00';
		$this->closeTime = !empty($this->closeTime) ? date('H:i:s', strtotime($this->closeTime)) : '00:00:00';
		$this->dayOfWeek = abs($this->dayOfWeek);
		$this->postID    = abs($this->postID);

		if ( empty($this->postID) ){
			return false;
		}

		$this->isExisted = $this->isValueExist();
		if ( $this->isExisted ){
			$this->update();
		}else{
			$this->insert();
		}
	}

	protected function isValueExist(){
		global $wpdb;
		$this->tblName = empty($this->tblName) ? $wpdb->prefix . BusinessHoursTbl::$tblName : $this->tblName;

		$postID = $wpdb->get_var(
			$wpdb->prepare("SELECT {$this->tblName}.post_ID FROM $this->tblName WHERE {$this->tblName}.post_ID=%d AND {$this->tblName}.day_of_week=%d", $this->postID, $this->dayOfWeek)
		);

		if ( empty($postID) ){
			return false;
		}

		return true;
	}

	protected function delete(){
		global $wpdb;
		$this->tblName = empty($this->tblName) ? $wpdb->prefix . BusinessHoursTbl::$tblName : $this->tblName;
		$wpdb->delete(
			$this->tblName,
			array(
				'post_ID'       => $this->postID
			),
			array(
				'%d'
			)
		);
	}

	protected function update(){
		global $wpdb;
		$wpdb->update(
			$this->tblName,
			array(
				'open_time'     => $this->openTime,
				'close_time'    => $this->closeTime,
				'always_open'   => $this->alwaysOpen,
			),
			array(
				'post_ID'       => $this->postID,
				'day_of_week'   => $this->dayOfWeek
			),
			array(
				'%s',
				'%s',
				'%s'
			),
			array(
				'%d',
				'%d'
			)
		);
	}

	protected function insert(){
		global $wpdb;

		$wpdb->insert(
			$this->tblName,
			array(
				'open_time'     => $this->openTime,
				'close_time'    => $this->closeTime,
				'day_of_week'   => $this->dayOfWeek,
				'always_open'   => $this->alwaysOpen,
				'post_ID'       => $this->postID
			),
			array(
				'%s',
				'%s',
				'%d',
				'%s',
				'%d'
			)
		);
	}
}