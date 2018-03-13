<?php
/*
 * About Us/Me
 * @copyright Wiloke
 * @website https://wiloke.com
 */
use WilokeWidget\Supports\Helpers;

class WilokeOpeningHours extends WP_Widget
{
	public $aDef = array('title' => 'Opening Hours');

	public function __construct(){
		parent::__construct('wiloke_opening_hours', WILOKE_WIDGET_PREFIX . ' (OSP) Opening Hours', array('classname'=>'widget widget_author_calendar', 'description'=>esc_html__('OSP - Only Single Page. This widget is only available for single listing page.', 'wiloke')) );
	}

	public function form($aInstance){
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		Helpers::textField(esc_html__('Title', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
	}

	public function update($aNewinstance, $aOldinstance) {
		$aInstance = $aOldinstance;
		foreach ( $aNewinstance as $key => $val ){
			$aInstance[$key] = strip_tags($val);
		}

		return $aInstance;
	}

	public function widget($atts, $aInstance){
		global $post, $wiloke;
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		if ( !is_singular('listing') ){
			return false;
		}
		$toggleBusinessHour = get_post_meta($post->ID, 'wiloke_toggle_business_hours', true);
		if ( isset($toggleBusinessHour) && $toggleBusinessHour === 'disable' ){
			return false;
		}

		$aSettings = Wiloke::getPostMetaCaching($post->ID, 'wiloke_listgo_business_hours');

		if ( empty($aSettings) ){
			return false;
		}
		$aDays = array(
			esc_html__('Monday', 'listgo'),
			esc_html__('Tuesday', 'listgo'),
			esc_html__('Wednesday', 'listgo'),
			esc_html__('Thursday', 'listgo'),
			esc_html__('Friday', 'listgo'),
			esc_html__('Saturday', 'listgo'),
			esc_html__('Sunday', 'listgo')
		);

		echo $atts['before_widget'];
		echo $atts['before_title'] . '<i class="icon_clock_alt"></i>' . ' '  . esc_html($aInstance['title']) . $atts['after_title'];
		?>
		<div class="widget_author-calendar">
			<ul>
				<?php
                foreach ( $aDays as $key => $day ) :
                    if ( has_action('wiloke-listgo-widgets/wilokeopeninghours') ) :
                        do_action('wiloke-listgo-widgets/wilokeopeninghours', $key, $day, $aSettings);
                    else:
                ?>
                    <li>
                        <span class="day"><?php echo esc_html($day); ?></span>
                        <?php if ( isset($aSettings[$key]['closed']) && !empty($aSettings[$key]['closed']) ) : ?>
                        <span class="time time--close"><?php esc_html_e('Closed', 'wiloke'); ?></span>
                        <?php else: ?>
                        <span class="time"><?php echo esc_html($aSettings[$key]['start_hour'].':'.$aSettings[$key]['start_minutes'] . ' ' . strtoupper($aSettings[$key]['start_format'])); ?> - <?php echo esc_html($aSettings[$key]['close_hour'].':'.$aSettings[$key]['close_minutes']  . ' ' . strtoupper($aSettings[$key]['close_format'])); ?></span>
                        <?php endif; ?>
                    </li>
				<?php
                    endif;
                endforeach;
                ?>
			</ul>
		</div>
		<?php
		echo $atts['after_widget'];
	}
}
