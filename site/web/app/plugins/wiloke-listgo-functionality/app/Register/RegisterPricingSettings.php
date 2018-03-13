<?php
namespace WilokeListGoFunctionality\Register;

class RegisterPricingSettings implements RegisterInterface{
	public $parent = 'pricing';
	public static $redisPricing = 'wiloke_pricing';
	public $slug = 'pricing-settings';

	public function __construct() {
		add_action('save_post', array($this, 'putToRedis'), 10, 2);
	}

	public function putToRedis($postID, $post){
        if ( $post->post_type === $this->parent ){
            global $wiloke;

            if ( \Wiloke::$wilokePredis ){
                $aCaching = array(
                    'ID'            => $post->ID,
                    'post_title'    => $post->post_title,
                    'post_content'  => $post->post_content,
                    'featured_image'=> get_the_post_thumbnail_url($post->ID, 'medium'),
                    'post_meta'     => \Wiloke::getPostMetaCaching($post->ID, $wiloke->aConfigs['metaboxes']['pricing_settings']['id'])
                );
                \Wiloke::$wilokePredis->hSet(self::$redisPricing, $post->ID, json_encode($aCaching));
            }
        }
    }

	public function register() {

	}
}