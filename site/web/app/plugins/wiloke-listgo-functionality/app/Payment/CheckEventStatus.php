<?php
/**
 * We will check listings status daily
 * @since 1.0
 */

namespace WilokeListGoFunctionality\Payment;
use WilokeListGoFunctionality\Frontend\FrontendEvents as WilokeFrontendEvents;

class CheckEventStatus{
	public function __construct() {
		add_action('wiloke_submission/automatically_delete_listing', array($this, 'checkEventStatus'));
	}

	public function checkEventStatus(){
		$query = new \WP_Query(
			array(
				'post_type' => 'event',
				'post_status' => 'publish',
				'posts_per_page' => -1
			)
		);

		if ( $query->have_posts() ){
			while ($query->have_posts()){
				$query->the_post();
				$aSettings = \Wiloke::getPostMetaCaching($query->post->ID, 'event_settings');
				$aStatus = WilokeFrontendEvents::checkEventStatus($aSettings);

				if ( $aStatus['status'] === 'expired' ){
					wp_update_post(
						array(
							'ID' => $query->post->ID,
							'post_status' => 'draft'
						)
					);
				}
			}
		}
	}
}