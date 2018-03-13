<?php
use WilokeWidget\Supports\Helpers;

class WilokeSimplePostsSlider extends WP_Widget
{
	public $aDef = array('title'=>'Latest Articles', 'number_of_posts'=>4, 'post_type'=>'event');
	public function __construct()
	{
		parent::__construct('wiloke_simple_posts_slider', WILOKE_WIDGET_PREFIX . esc_html__( 'Posts Slider', 'wiloke'), array('classname'=>'widget_simple_posts_slider'));
	}

	public function form($aInstance)
	{
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		Helpers::textField( esc_html__('Title', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
		Helpers::textField( esc_html__('Number of Feeds', 'wiloke'), $this->get_field_id('number_of_posts'), $this->get_field_name('number_of_posts'), $aInstance['number_of_posts']);
		Helpers::selectField(
			esc_html__('Post Type', 'wiloke'),
			$this->get_field_id('post_type'),
			$this->get_field_name('post_type'),
			array(
				'post'      => esc_html__('Post', 'wiloke'),
				'listing'   => esc_html__('Listing', 'wiloke'),
				'event'     => esc_html__('Event', 'wiloke')
			),
			$aInstance['post_type']
		);
	}

	public function update($aNewinstance, $aOldinstance)
	{
		$aInstance = $aOldinstance;
		foreach ( $aNewinstance as $key => $val )
		{
			if ( $key == 'number_of_posts' )
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
		$aData =  wiloke_listgo_widget_get_cache($atts);

		if ( empty($aData) ){
			$query = new WP_Query(
				array(
					'post_type'         => $aInstance['post_type'],
					'posts_per_pages'   => $aInstance['number_of_posts'],
					'post_status'       => 'publish'
				)
			);
			$aData = array();
			if ( $query->have_posts() ){
				while ($query->have_posts()){
					$query->the_post();
					$aData[$query->post->ID]['title'] = $query->post->post_title;
					$aData[$query->post->ID]['link'] = get_permalink($query->post->ID);
				}
				wiloke_listgo_widget_set_cache($atts, $aData);
			}
			wp_reset_postdata();
		}else{
			$aData = json_decode($aData, true);
		}

		if ( empty($aData) ){
			return false;
		}
		echo $atts['before_widget'];

		if ( !empty($aInstance['title']) ) {
			echo $atts['before_title'] . esc_html($aInstance['title']) . $atts['after_title'];
		}

		?>
		<div class="wiloke-simple-posts-slider owl-carousel">
		<?php foreach($aData as $aInfo) : ?>
			<p><a href="<?php echo esc_url($aInfo['link']); ?>"><?php echo esc_html($aInfo['title']); ?></a></p>
		<?php endforeach; ?>
		</div>
		<?php
		echo $atts['after_widget'];
	}
}