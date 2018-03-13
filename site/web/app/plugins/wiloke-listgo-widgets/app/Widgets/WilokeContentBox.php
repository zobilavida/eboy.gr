<?php
/*
 * About Us/Me
 * @copyright Wiloke
 * @website https://wiloke.com
 */
use WilokeWidget\Supports\Helpers;

class WilokeContentBox extends WP_Widget
{
	public $aDef = array('title' => 'Discover Sondoong Cave', 'content'=>array(array('icon'=>'icon_lightbulb_alt', 'heading'=>'Deeply Design Tour', 'description'=>'All our tours are carefully designed by the British Caving Research Association.'), array('icon'=>'icon_like_alt', 'heading'=>'Highest Experience', 'description'=>'We only take small groups of tourists with qualified experienced guides\', \'content'), array('icon'=>'icon_chat_alt', 'heading'=>'Best Service', 'description'=>'We only use the best brands of helmets, head torches, life jackets and safety equipment')), 'btn_name'=>'Sign Our Team Today', 'btn_link'=>'#', 'btn_target'=>'_self');
	public function __construct()
	{
		parent::__construct('widget_services', WILOKE_WIDGET_PREFIX . ' Content Box', array('classname'=>'widget_services') );
	}

	public function form($aInstance)
	{
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		Helpers::textField(esc_html__('Title', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);

		echo '<div class="wiloke-group-wrapper">';
        foreach ( $aInstance['content'] as $key => $aValue ){
            $this->cluster($aValue, $key);
        }
        echo '</div>';
        Helpers::btnField(esc_html__('Add New', 'wiloke'), $this->get_field_id('add_new'));
        Helpers::textField(esc_html__('Button Name', 'wiloke'), $this->get_field_id('btn_name'), $this->get_field_name('btn_name'), $aInstance['btn_name']);
        Helpers::textField(esc_html__('Button Link', 'wiloke'), $this->get_field_id('btn_link'), $this->get_field_name('btn_link'), $aInstance['btn_link']);
        Helpers::selectField(esc_html__('Button Target', 'wiloke'), $this->get_field_id('btn_target'), $this->get_field_name('btn_target'), array('_self'=>esc_html__('Open the page in the same frame', 'wiloke'), '_blank'=>esc_html__('Open the page in a new window', 'wiloke')), $aInstance['btn_target']);
	}

	public function cluster($aValue, $key){
	    echo '<div class="wiloke-group" data-order="'.esc_attr($key).'">';
            Helpers::textField(esc_html__('Heading', 'wiloke'), $this->get_field_id('content['.$key.'][heading]'), $this->get_field_name('content['.$key.'][heading]'), $aValue['heading']);
            Helpers::textField(esc_html__('Icon', 'wiloke'), $this->get_field_id('content['.$key.'][icon]'), $this->get_field_name('content['.$key.'][icon]'), $aValue['icon'], \Wiloke::wiloke_kses_simple_html('<a href="https://goo.gl/VBiKV5" target="_blank">'.esc_html__('How to fill up an icon to the field?').'</a>', true));
            Helpers::textareaField(esc_html__('Description', 'wiloke'), $this->get_field_id('content['.$key.'][description]'), $this->get_field_name('content['.$key.'][description]'), $aValue['description']);
            echo '<a href="#" class="wiloke-widget wiloke-remove-group"><i class="dashicons dashicons-no"></i></a>';
        echo '</div>';
	}

	public function update($aNewinstance, $aOldinstance) {
	    return $aNewinstance;
    }

	public function widget($atts, $aInstance)
	{
		if ( empty($aInstance) ){
			return false;
		}
		$aInstance = wp_parse_args($aInstance, $this->aDef);

        echo $atts['before_widget'];
            if ( !empty($aInstance['title']) ) {
                print $atts['before_title'] . esc_html($aInstance['title']) . $atts['after_title'];
            }

            echo '<ul>';
            foreach ( $aInstance['content'] as $aValue ) :
            ?>
                <li>
                    <h6 class="widget_services__title">
                        <i class="<?php echo esc_attr($aValue['icon']); ?>"></i> <?php echo esc_html($aValue['heading']); ?>
                    </h6>
                    <p><?php \Wiloke::wiloke_kses_simple_html($aValue['description']); ?></p>
                </li>
            <?php
            endforeach;
            echo '</ul>';

            if ( !empty($aInstance['btn_link']) ) :
            ?>
            <a href="<?php echo esc_url($aInstance['btn_link']); ?>" target="<?php echo esc_attr($aInstance['btn_target']); ?>" class="widget_services__line listgo-btn btn-primary"><?php echo esc_html($aInstance['btn_name']); ?> <i class="fa fa-arrow-circle-right"></i></a>
            <?php
            endif;
        echo $atts['after_widget'];
	}
}
