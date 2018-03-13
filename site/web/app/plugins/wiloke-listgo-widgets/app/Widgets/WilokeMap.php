<?php
/*
 * About Us/Me
 * @copyright Wiloke
 * @website https://wiloke.com
 */
use WilokeWidget\Supports\Helpers;

class WilokeMap extends WP_Widget
{
	public $aDef = array('title' => '', 'get_direction_text'=>'(Get directions)');
	public function __construct()
	{
		parent::__construct('widget_map', WILOKE_WIDGET_PREFIX . '(OSP) Map', array('classname'=>'widget_map', 'description'=>esc_html__('OSP - Only Single Page. This widget is only available for single listing page.', 'wiloke')) );
	}

	public function form($aInstance)
	{
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		Helpers::textField( esc_html__('Title', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
		Helpers::textField( esc_html__('Get Direction Text', 'wiloke'), $this->get_field_id('get_direction_text'), $this->get_field_name('get_direction_text'), $aInstance['get_direction_text']);
	}

	public function update($aNewinstance, $aOldinstance){
		$aInstance = $aOldinstance;
		foreach ( $aNewinstance as $key => $val )
		{
			$aInstance[$key] = strip_tags($val);
		}
		return $aInstance;
	}

	public function widget($atts, $aInstance)
	{
		global $post;
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		if ( !is_singular('listing') ){
			return false;
		}
		$aSettings = Wiloke::getPostMetaCaching($post->ID, 'listing_settings');

		if ( empty($aSettings) || !isset($aSettings['map']['latlong']) || empty($aSettings['map']['latlong']) ){
			return false;
		}

		$aListingCats = Wiloke::getPostTerms($post, 'listing_cat');

		if ( !empty($aListingCats) ) {
			foreach ($aListingCats as $oTerm){
				$aOptions = Wiloke::getTermOption($oTerm->term_id);
				if ( isset($aOptions['map_marker_image']) && !empty($aOptions['map_marker_image']) ){
					$mapMarker = $aOptions['map_marker_image'];
					break;
				}
			}
		}

		if ( !isset($mapMarker) ){
			$mapMarker = get_template_directory_uri() . '/img/icon-marker.png';
		}

		echo $atts['before_widget'];
			if ( !empty($aInstance['title']) ){
				echo $atts['before_title'] . '<i class="icon_pin_alt"></i>' . esc_html($aInstance['title']) . $atts['after_title'];
			}

			?>
			<div class="widgetmap">
				<div id="widget-map" class="widget-map" data-map="<?php echo esc_attr($aSettings['map']['latlong']); ?>" data-marker="<?php echo esc_url($mapMarker); ?>"></div>
				<p><?php echo esc_html($aSettings['map']['location']); ?> <a target="_blank" href="<?php echo esc_url('//maps.google.com/maps?daddr='.$aSettings['map']['latlong']); ?>"><?php echo esc_html($aInstance['get_direction_text']); ?></a></p>
			</div>
			<?php
		echo $atts['after_widget'];
	}
}
