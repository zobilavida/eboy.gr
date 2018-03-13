<?php
namespace WilokeListGoFunctionality\Frontend;

use WilokeListGoFunctionality\AlterTable\AlterTablePriceSegment as PriceSegmentTbl;

class FrontendPriceSegment{
	public $segment = '';
	public $postID = '';
	protected $tblName = '';
	protected $isExisted = null;
	protected $metaKey = 'listing_price';

	public function __construct() {
		add_action('before_delete_post', array($this, 'beforeDeletePosts'), 10);
		add_action('save_post_listing', array($this, 'hasChangeSegment'), 99, 2);

	}

	public function createSegmentData($postID, $post){
		if ( $post->post_type !== 'listing' || $post->post_status !== 'publish' ){
			return false;
		}

		$aPrice = \Wiloke::getPostMetaCaching($postID, 'listing_price');
		$this->postID  = absint($postID);
		$this->segment = sanitize_text_field($aPrice['price_segment']);
		$this->prepareData();
	}

	public function hasChangeSegment($postID, $post){
		if ( $post->post_type !== 'listing' || $post->post_status !== 'publish' ){
			return false;
		}

		if ( !isset($_POST['listing_price']) ){
			return false;
		}

		$this->postID  = absint($postID);
		$this->segment = sanitize_text_field($_POST['listing_price']['price_segment']);
		$this->prepareData();
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
		$this->postID = abs($this->postID);

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
		$this->tblName = empty($this->tblName) ? $wpdb->prefix . PriceSegmentTbl::$tblName : $this->tblName;

		$postID = $wpdb->get_var(
			$wpdb->prepare("SELECT {$this->tblName}.post_ID FROM $this->tblName WHERE {$this->tblName}.post_ID=%d", $this->postID)
		);

		if ( empty($postID) ){
			return false;
		}

		return true;
	}

	protected function delete(){
		global $wpdb;
		$this->tblName = empty($this->tblName) ? $wpdb->prefix . PriceSegmentTbl::$tblName : $this->tblName;
		$wpdb->delete(
			$this->tblName,
			array(
				'post_ID'  => $this->postID
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
				'segment'     => $this->segment
			),
			array(
				'post_ID'       => $this->postID
			),
			array(
				'%s'
			),
			array(
				'%d'
			)
		);
	}

	protected function insert(){
		global $wpdb;

		$wpdb->insert(
			$this->tblName,
			array(
				'segment'    => $this->segment,
				'post_ID'    => $this->postID
			),
			array(
				'%s',
				'%d'
			)
		);
	}
}