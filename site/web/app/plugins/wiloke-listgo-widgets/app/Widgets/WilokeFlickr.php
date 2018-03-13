<?php
use WilokeWidget\Supports\Helpers;

class WilokeFlickr extends WP_Widget
{
	public $aDef = array('title' =>'Flickr', 'flickr_id'=>'113963751@N02', 'number_of_photos' => 6, 'flickr_display' => 'latest', 'items_per_row' => 'widget_photo-col-3',);
	public function __construct()
	{
		$args = array('classname'=>'widget_flickr widget_photo', 'description'=>'');
		parent::__construct("wiloke_flickrfeed", WILOKE_WIDGET_PREFIX . esc_html__('Flickr Feed', 'wiloke'), $args);
	}

	public function form($aInstance)
	{
		$aInstance = wp_parse_args( $aInstance, $this->aDef );
		Helpers::textField(esc_html__('Title', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
		Helpers::textField(esc_html__('Flickr ID (*)', 'wiloke'), $this->get_field_id('flickr_id'), $this->get_field_name('flickr_id'), $aInstance['flickr_id'], ' Find Your ID at( <a target="_blank" href="http://www.idgettr.com">idGettr</a> )');
		Helpers::textField(esc_html__('Number Of Photos', 'wiloke'), $this->get_field_id('number_of_photos'), $this->get_field_name('number_of_photos'), $aInstance['number_of_photos']);
		Helpers::selectField( esc_html__('Images Per Row', 'wiloke'), $this->get_field_id('items_per_row'), $this->get_field_name('items_per_row'), array(
            'widget_photo-col-3' => esc_html__('3 Items / Row', 'wiloke'),
            'widget_photo-col-4' => esc_html__('4 Items / Row', 'wiloke'),
            'widget_photo-col-2' => esc_html__('2 Items / Row', 'wiloke'),
            'widget_photo-col-6' => esc_html__('6 Items / Row', 'wiloke')
		), $aInstance['items_per_row']);
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

	public function widget($atts, $aInstance)
	{
		$aInstance = wp_parse_args($aInstance, $this->aDef);

		if ( empty($aInstance['flickr_id']) ) {
			if ( current_user_can('edit_theme_options') ){
				esc_html_e('Please supply Flickr ID for this widget', 'wiloke');
			}
			return false;
		}else {
			$aFlickr = wiloke_listgo_widget_get_cache($atts);
			if ( empty($aFlickr) ){
				$aFlickr = $this->parseFeed($aInstance['flickr_id'], $aInstance['number_of_photos']);
				if ( !empty($aFlickr) ){
					wiloke_listgo_widget_set_cache($atts, $aFlickr);
				}
			}else{
				$aFlickr = json_decode($aFlickr, true);
			}
		}

		if ( empty($aFlickr) ){
			return false;
		}

		echo $atts['before_widget'];

			if ( !empty($aInstance['title']) )
			{
				print $atts['before_title'] . esc_html($aInstance['title']) . $atts['after_title'];
			}
		?>
        <ul class="popup-gallery <?php echo esc_attr($aInstance['items_per_row']); ?>">
            <?php foreach ( $aFlickr as $aInfo ) : ?>
                <li><a href="<?php echo esc_url($aInfo['full']); ?>" class="bg-scroll lazy" data-src="<?php echo esc_url($aInfo['thumb']); ?>" data-title="<?php echo esc_attr($aInfo['title']); ?>" data-linkto="<?php echo esc_attr($aInfo['link']); ?>" target="_blank"><img class="lazy" data-src="<?php echo esc_url($aInfo['thumb']); ?>" alt="<?php echo esc_attr('Flickr Feed', 'wiloke'); ?>" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw=="></a></li>
            <?php endforeach; ?>
        </ul>
		<?php
		echo $atts['after_widget'];
	}

	public function parseFeed($id,$n)
	{
		$url = "http://api.flickr.com/services/feeds/photos_public.gne?id={$id}&lang=it-it&format=rss_200&amp;set=".$n;
		$response = wp_remote_get( esc_url_raw( $url ),  array( 'decompress' => false ) );
		if ( !is_wp_error( $response ) )
		{
			$response = wp_remote_retrieve_body($response);
			preg_match_all('#<item>(.*)</item>#Us', $response, $items);
			$total = count($items[1]);
			$total = $total > $n ? $n : $total;
			$aFlickr = array();

			for($i=0;$i<$total;$i++)
			{
				$item = $items[1][$i];

				preg_match('/<title>(.*)<\/title>/', $item, $temp);
				$title = $temp[1];

				preg_match('/<link>(.*)<\/link>/', $item, $temp);
				$link = $temp[1];

				preg_match('/(?:<media:thumbnail\surl=\"|\')([^\"\']+)/', $item, $temp);
				$fullSize = str_replace('_s.', '.', $temp[1]);
				$thumb = str_replace('_s.', '_m.', $temp[1]);

				$aFlickr[] = array(
					'title'=> $title,
					'link' => $link,
					'thumb'=> $thumb,
					'full' => $fullSize
				);
			}

			return $aFlickr;

		}else{
			return false;
		}
	}
}
