<?php
/*
 * About Us/Me
 * @copyright Wiloke
 * @website https://wiloke.com
 */
use WilokeWidget\Supports\Helpers;

class WilokeListingPrice extends WP_Widget
{
	public $aDef = array('icon'=> 'icon_currency', 'title' => '%segment%');

	public function __construct(){
		parent::__construct('wiloke_price_segment', WILOKE_WIDGET_PREFIX . ' (OSP) Price Segment', array('classname'=>'widget wiloke_price_segment', 'description'=>esc_html__('OSP - Only Single Page. This widget is only available for single listing page.', 'wiloke')) );
	}

	public function form($aInstance){
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		Helpers::textField(esc_html__('Icon', 'wiloke'), $this->get_field_id('icon'), $this->get_field_name('icon'), $aInstance['icon'], __('You can get your icon at <a href="http://fontawesome.io/get-started/" target="_blank">Font Awesome</a> Or <a href="http://fontawesome.io or https://www.elegantthemes.com/blog/resources/elegant-icon-font" target="_blank">Elegan Icons</a> '));
		Helpers::textField(esc_html__('Title', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title'], esc_html__('You can add text before or after %segment%. For example: %segment% Segment, you should be "$$$ - Expensive Segment" on the front end', 'listgo'));
	}

	public function update($aNewinstance, $aOldinstance) {
		$aInstance = $aOldinstance;
		foreach ( $aNewinstance as $key => $val ){
			$aInstance[$key] = strip_tags($val);
		}

		return $aInstance;
	}

	public function widget($atts, $aInstance){
		global $post, $wiloke, $WilokeListGoFunctionalityApp;;
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		if ( !is_singular('listing') ){
			return false;
		}
		$aSettings = Wiloke::getPostMetaCaching($post->ID, 'listing_price');

		if ( empty($aSettings) || empty($aSettings['price_segment']) ){
			return false;
		}

		if ( empty($aSettings['price_from']) && empty($aSettings['price_to']) ){
			return false;
		}

		$currency = WilokePublic::getPaymentField('currency_code');
		$symbol   = $WilokeListGoFunctionalityApp['currencySymbol'][$currency];

		if ( empty($aSettings['price_segment']) ){
			$aInstance['title'] = esc_html__('Price range', 'wiloke');
		}else{
			$name = isset($wiloke->aThemeOptions['header_search_'.$aSettings['price_segment'].'_cost_label']) ? $wiloke->aThemeOptions['header_search_'.$aSettings['price_segment'].'_cost_label'] : ucfirst($aSettings['price_segment']);
			$aInstance['title'] = str_replace('%segment%', $name, $aInstance['title']);
			switch ($aSettings['price_segment']){
				case 'moderate':
					$active = $symbol.$symbol;
					$deactive = $symbol.$symbol;
					break;
				case 'expensive':
					$active = $symbol.$symbol.$symbol;
					$deactive = $symbol;
					break;
				case 'ultra_high':
					$active = $symbol.$symbol.$symbol.$symbol;
					$deactive = '';
					break;
				default:
					$active = $symbol;
					$deactive = $symbol.$symbol.$symbol;
					break;
			}

			$active = '<span class="active">'.$active.'</span>';
			$deactive = empty($deactive) ? '' : '<span class="deactive">'.$deactive.'</span>';
			$aInstance['title'] = $active.$deactive . ' ' . $aInstance['title'];
		}

		echo $atts['before_widget'];

		$icon = '';
		$title = str_replace('_', ' ', $aInstance['title']);
		
		if ( !empty($aInstance['icon']) ) {
			$icon = '<i class="'. esc_attr($aInstance['icon']) .'"></i>';
		}

		echo $atts['before_title'] . $icon . $title . $atts['after_title']; ?>
		
		<div class="wiloke_price-range">
			<?php esc_html_e('Price Range: ', 'wiloke'); ?> <span class="wiloke_price-range__price"><?php echo esc_html($aSettings['price_from']) ?> - <?php echo esc_html($aSettings['price_to']); ?></span>
            <?php do_action('wiloke-listgo-widgets/app/Widgets/WilokeListingPrice.php'); ?>
		</div>
		<?php
		echo $atts['after_widget'];
	}
}
