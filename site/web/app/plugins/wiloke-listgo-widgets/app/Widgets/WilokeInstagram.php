<?php
use WilokeWidget\Supports\Helpers;

class WilokeInstagram extends WP_Widget
{
	public $aDef = array( 'title' =>'Instagram', 'user_id'=>'', 'username'=>'', 'number_of_photos' => 6, 'items_per_row' => 'widget_photo-col-3', 'access_token' => '');
	public function __construct()
	{
		$args = array('classname'=>'widget_instagram widget_wiloke_instagram widget_photo', 'description'=>'');
		parent::__construct("wiloke_instagram", WILOKE_WIDGET_PREFIX . esc_html__('Instagram Feed', 'wiloke'), $args);
	}

	public function form($aInstance)
	{
		$aInstance            = wp_parse_args( $aInstance, $this->aDef );
		$aInstagramSettings   = get_option('_pi_instagram_settings');

		if ( isset($aInstagramSettings['access_token']) && !empty($aInstagramSettings['access_token']) )
		{
			Helpers::textField( esc_html__('Title', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
			Helpers::textField( esc_html__('Instagram user name', 'wiloke'), $this->get_field_id('username'), $this->get_field_name('username'), $aInstance['username']);
			Helpers::description(esc_html__('Leave empty if you want to use the default setting in Settings -> Wiloke Instagram', 'wiloke'));
			Helpers::textField( esc_html__('Number Of Photos', 'wiloke'), $this->get_field_id('number_of_photos'), $this->get_field_name('number_of_photos'), $aInstance['number_of_photos']);
			Helpers::selectField( esc_html__('Images Per Row', 'wiloke'), $this->get_field_id('items_per_row'), $this->get_field_name('items_per_row'), array(
                'widget_photo-col-3' => esc_html__('3 Items / Row', 'wiloke'),
                'widget_photo-col-4' => esc_html__('4 Items / Row', 'wiloke'),
                'widget_photo-col-2' => esc_html__('2 Items / Row', 'wiloke'),
                'widget_photo-col-6' => esc_html__('6 Items / Row', 'wiloke')
            ), $aInstance['items_per_row']);
		}else{
			Helpers::description(
				sprintf(
					__('Instagram Access Token is required. Please <a target="_blank" href="%s"> click me to provide your instagram information.</a>', 'wiloke'),
					esc_url(admin_url('options-general.php?page=wiloke-instagram'))
				)
			);
		}

	}

	public function update($aNewinstance, $aOldinstance)
	{
		$aInstance = $aOldinstance;
		foreach ( $aNewinstance as $key => $val )
		{
			if ( $key == 'number_of_photos' )
			{
				$aInstance[$key] = (int)$val;
			}else{
				$aInstance[$key] = strip_tags($val);
			}
		}

		return $aInstance;
	}

	public function widget( $atts, $aInstance )
	{
		$aInstance  = wp_parse_args($aInstance, $this->aDef);

		$aInstagramSettings         = get_option('_pi_instagram_settings');
		$aInstance['access_token']  = isset($aInstagramSettings['access_token']) ? $aInstagramSettings['access_token'] : '';
		$cacheInstagram = null;

		if ( empty($aInstance['access_token']) )
		{
			if ( current_user_can('edit_theme_options') )
			{
				esc_html_e('Please config your instagram', 'wiloke');
			}
		}else{
			$aFeeds = wiloke_listgo_widget_get_cache($atts);
			if ( empty($aFeeds) ){
				if ( !empty($aInstance['username']) )
				{
					$type = 'username';
					$info = $aInstance['username'];
				}else{
					$type = 'self';
					$info = $aInstagramSettings['userid'];
				}
				$aFeeds = $this->getFeeds($info, $aInstance['access_token'], $aInstance['number_of_photos'], $type);
				if ( !empty($aFeeds) ){
					wiloke_listgo_widget_set_cache($atts, $aFeeds);
				}
			}else{
				$aFeeds = json_decode($aFeeds, true);
			}
		}

		if ( empty($aFeeds) ){
			return false;
		}

		echo $atts['before_widget'];

		if ( !empty($aInstance['title']) )
		{
			echo $atts['before_title'] . esc_html($aInstance['title']) . $atts['after_title'];
		}
		?>
		<ul class="popup-gallery <?php echo esc_attr($aInstance['items_per_row']); ?>">
			<?php foreach ($aFeeds as $aFeed) : ?>
			<li>
				<a href="<?php echo esc_url($aFeed['full']); ?>" class="bg-scroll lazy" data-src="<?php echo esc_url($aFeed['thumb']); ?>" target="_blank" data-title="<?php echo esc_attr($aFeed['title'] . ' @' . $aFeed['username']); ?>" data-linkto="<?php echo esc_url('https://www.instagram.com/'.$aFeed['username']); ?>">
					<img class="lazy" data-src="<?php echo esc_url($aFeed['thumb']); ?>" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" alt="<?php echo esc_attr($aFeed['title']); ?>">
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php
		echo $atts['after_widget'];
	}

	public function getUserID($info, $accessToken, $args)
	{
		$url = 'https://api.instagram.com/v1/users/search?q='.$info.'&access_token='.$accessToken;
		$oSearchProfile = wp_remote_get( esc_url_raw( $url ), $args);
		if ( !empty($oSearchProfile) && !is_wp_error($oSearchProfile) )
		{
			$oSearchProfile = wp_remote_retrieve_body($oSearchProfile);
			$oSearchProfile = json_decode($oSearchProfile);

			if ( $oSearchProfile->meta->code === 200 )
			{
				foreach ( $oSearchProfile->data as $oInfo )
				{
					if ( $oInfo->username === $info )
					{
						return $oInfo->id;
					}
				}
			}
		}

		return false;
	}

	public function getFeeds($info, $accessToken, $count=6, $type='self')
	{
		$args = array( 'decompress' => false, 'timeout' => 30, 'sslverify'   => true );
		if ( $type == 'self' )
		{
			return $this->fetchFeeds($info, $accessToken, $count, $args);
		}else{
			$userID = $this->getUserID($info, $accessToken, $args);

			if ( !empty($userID) )
			{
				return $this->fetchFeeds($userID, $accessToken, $count, $args);
			}
		}

	}

	public function fetchFeeds($info, $accessToken, $count, $args)
	{
		$url   = 'https://api.instagram.com/v1/users/'.$info.'/media/recent?access_token='.$accessToken.'&count='.$count;

		$getInstagram = wp_remote_get( esc_url_raw( $url ), $args);

		if ( !is_wp_error($getInstagram) )
		{
			$getInstagram = wp_remote_retrieve_body($getInstagram);
			$getInstagram = json_decode($getInstagram);
			$aFeeds = array();
			if ( $getInstagram->meta->code === 200 )
			{
				for ( $i=0; $i<$count; $i++ )
				{
					if ( isset($getInstagram->data[$i]) ) {
					
						if ( !empty($getInstagram->data[$i]->caption) ){
							$title = $getInstagram->data[$i]->caption->text;
						}elseif ( !empty($getInstagram->data[$i]->location) ){
							$title = !empty($getInstagram->data[$i]->location->name);
						}else{
							$title = $getInstagram->data[$i]->user->username;
						}

						$aFeeds[] = array(
							'username'  => $getInstagram->data[$i]->user->username,
							'thumb'     => $getInstagram->data[$i]->images->thumbnail->url,
							'title'     => $title,
							'link'      => $getInstagram->data[$i]->link,
							'full'      => $getInstagram->data[$i]->images->standard_resolution->url
						);
					}
				}
				return $aFeeds;
			}
		}

		return false;
	}
}
