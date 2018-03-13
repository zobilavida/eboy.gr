<?php
/*
 * About Us/Me
 * @copyright Wiloke
 * @website https://wiloke.com
 */
use WilokeWidget\Supports\Helpers;

class WilokeContactFormSeven extends WP_Widget
{
	public $aDef = array('title' => 'Contact Me', 'description'=>'');
	public function __construct()
	{
		parent::__construct('wiloke_contact', WILOKE_WIDGET_PREFIX . ' (OSP) Contact Form', array('classname'=>'widget_contact', 'description'=>esc_html__('OSP - Only Single Page. This widget is only available for single listing page.', 'wiloke')) );
	}

	public function form($aInstance)
	{
		$aValue = wp_parse_args($aInstance, $this->aDef);
		Helpers::textField(esc_html__('Title', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aValue['title']);
		Helpers::description(esc_html__('Note that Contact Form 7 plugin is required by the widget. To create a contact form, please go to Appearance -> Theme Options -> Listing Settings -> Assign a contact form to Contact Form 7 to  Set Contact Form 7 setting.', 'wiloke'));
	}

	public function update($aNewinstance, $aOldinstance) {
		$aInstance = $aOldinstance;
		foreach ( $aNewinstance as $key => $val )
		{
            $aInstance[$key] = strip_tags($val);
		}

		return $aInstance;
    }

	public function widget($atts, $aInstance)
	{
		global $wiloke;
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		if ( !is_singular('listing') || !isset($wiloke->aThemeOptions['listing_contactform7']) || empty($wiloke->aThemeOptions['listing_contactform7']) ){
			return false;
		}

        echo $atts['before_widget'];
		if ( !empty($aInstance['title']) ){
			echo $atts['before_title'] . '<i class="icon_mail"></i>' . ' '  . esc_html($aInstance['title']) . $atts['after_title'];
		}
        ?>
        <div class="contactform-wrapper">
            <?php echo do_shortcode('[contact-form-7 id="'.esc_attr($wiloke->aThemeOptions['listing_contactform7']).'" title="Contact Form"]'); ?>
        </div>
        <?php
        echo $atts['after_widget'];
	}
}
