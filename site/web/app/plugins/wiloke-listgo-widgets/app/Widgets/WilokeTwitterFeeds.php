<?php
use WilokeWidget\Supports\Helpers;

class WilokeTwitterFeeds extends WP_Widget
{
	public $aDef = array('title'=>'Twitter Feed', 'number_of_feeds'=>4);
	public function __construct()
	{
		parent::__construct('wiloke_twitter_feeds', WILOKE_WIDGET_PREFIX . esc_html__( 'Twitter Feed', 'wiloke'), array('classname'=>'widget_twitter'));
	}

	public function form($aInstance)
	{
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		Helpers::textField( esc_html__('Title', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
		Helpers::textField( esc_html__('Number of Feeds', 'wiloke'), $this->get_field_id('number_of_feeds'), $this->get_field_name('number_of_feeds'), $aInstance['number_of_feeds']);
		Helpers::description(esc_html__('Ensure that Twitter Information has been configured. If you still not do that, please go to Settings -> Wiloke Twitter', 'wiloke'));
	}

	public function update($aNewinstance, $aOldinstance)
	{
		$aInstance = $aOldinstance;
		foreach ( $aNewinstance as $key => $val )
		{
			if ( $key == 'number_of_feeds' )
			{
				$aInstance[$key] = (int)$val;
			}else{
				$aInstance[$key] = strip_tags($val);
			}
		}
		return $aInstance;
	}

	public function widget($atts, $aInstance) {
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		$tweets = wiloke_listgo_widget_get_cache($atts);
		$aTweets = json_decode($tweets, true);
		$isError = false;

		if ( empty($aTweets) || (isset($aTweets['errors']) || !empty($aTweets['errors'])) || !is_array($aTweets) ){

			$aTwitter = get_option('_wiloke_twitter_settings');

			if ( empty($aTwitter) ){
				if ( current_user_can('edit_theme_options') ){
					WilokeAlert::render_alert( __('Please supply your Twitter Information: Settings -> Wiloke Twitter.', 'wiloke'));
				}
				return false;
			}

			$aTwitter = array_map('trim', $aTwitter);

			if ( empty($tweets) ){
				require_once plugin_dir_path(__FILE__).'twitter/twitteroauth.php';
				$initTWitter = new TwitterOAuth($aTwitter['consumer_key'], $aTwitter['consumer_secret'], $aTwitter['access_token'], $aTwitter['access_token_secret']);
				$initTWitter->ssl_verifypeer = true;
				$tweets = $initTWitter->get('statuses/user_timeline', array('screen_name' => $aTwitter['username'], 'include_rts' => 'false', 'count' => $aInstance['number_of_feeds']));
			}

			if ( empty($tweets) ){
				if ( current_user_can('edit_theme_options') ){
					WilokeAlert::render_alert( __('Invalid or expired token. Please go to Settings -> Wiloke Twitter and check it again.', 'wiloke'));
				}
				return false;
			}

			wiloke_listgo_widget_set_cache($atts, $tweets);
			$aTweets = json_decode($tweets, true);

			if ( empty($aTweets) || (isset($aTweets['errors']) && !empty($aTweets['errors'])) || !is_array($aTweets) ){
				if ( current_user_can('edit_theme_options') ){
					$isError = true;
				}
			}
		}

		echo $atts['before_widget'];
		if ( !empty($aInstance['title']) ) {
			echo $atts['before_title'] . esc_html($aInstance['title']) . $atts['after_title'];
		}

		if (!$isError){
			?>
			<div class="widget-twitter twitter-slider owl-carousel">
				<?php
				foreach ( $aTweets as $aTweet ){
					$aTweet = is_object($aTweet) ? get_object_vars($aTweet) : $aTweet;
					$tweet =  preg_replace('/http?s:\/\/([^\s]+)/i', '<a href="http://$1" target="_blank">$1</a>', $aTweet['text']);
					Wiloke::wiloke_kses_simple_html("<p>{$tweet}</p>", false);
				}
				?>
			</div>
			<?php
		}else{
			if ( current_user_can('edit_theme_options') ){
				WilokeAlert::render_alert( __('There is no any tweet yet. Please go to your Twitter and tweets something.', 'wiloke'));
			}
		}
		
		echo $atts['after_widget'];
	}
}