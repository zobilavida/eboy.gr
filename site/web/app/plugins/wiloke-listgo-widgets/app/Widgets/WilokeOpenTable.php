<?php
/*
 * About Us/Me
 * @copyright Wiloke
 * @website https://wiloke.com
 */
use WilokeWidget\Supports\Helpers;

class WilokeOpenTable extends WP_Widget
{
	public $aDef = array('title' => 'Make a Reservation', 'btn_name'=>'Find a Table');

	public function __construct(){
		parent::__construct('wiloke_opentable', WILOKE_WIDGET_PREFIX . ' (OSP) Open Table', array('classname'=>'widget widget_opentable', 'description'=>esc_html__('OSP - Only Single Page. This widget is only available for single listing page.', 'wiloke')) );
		add_action('wp_enqueue_scripts', array($this, 'enqueueScript'));
	}

	public function enqueueScript(){
	    wp_enqueue_script('jquery-ui-datapicker');
    }

	public function form($aInstance){
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		Helpers::textField(esc_html__('Title', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
		Helpers::textField(esc_html__('Button Name', 'wiloke'), $this->get_field_id('btn_name'), $this->get_field_name('btn_name'), $aInstance['btn_name']);
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

		$aListingSettings = Wiloke::getPostMetaCaching($post->ID, 'listing_open_table_settings');
		if ( empty($aListingSettings) || !isset($aListingSettings['restaurant_id']) || empty($aListingSettings['restaurant_id']) ){
		    return '';
        }

		echo $atts['before_widget'];
		echo $atts['before_title']  . esc_html($aInstance['title']) . $atts['after_title'];
		?>
        <form method="get" action="//www.opentable.com/restaurant-search.aspx" target="_blank">
            <div class="form-item listgo-datepicker-wrapper">
                <div id="listgo-open-table-startdate"></div>
                <input id="listgo-open-table-startdate-input" name="startDate" type="hidden" value="">
            </div>
            <div class="form-item listgo-time-wrapper">
            	<i class="icon_clock_alt"></i>
                <select name="ResTime">
			        <?php
			        $inc = 30 * 60;
			        $start = ( strtotime( '7AM' ) );
			        $end = ( strtotime( '11:59PM' ) );
			        for ( $i = $start; $i <= $end; $i += $inc ) {
				        $time      = date( 'g:i A', $i );
				        $timeValue = date( 'g:ia', $i );
				        $default   = "7:00pm";
				        ?>
				        <option value="<?php echo esc_attr($timeValue); ?>" <?php selected($default, $timeValue); ?>><?php echo esc_html($time); ?></option>
                        <?php
			        }
			        ?>
                </select>
            </div>
            <div class="form-item listgo-party-size-wrapper">
            	<i class="icon_group"></i>
                <select name="partySize">
                    <option value="1"><?php esc_html_e('1 Person', 'wiloke'); ?></option>
                    <option value="2" selected="selected"><?php esc_html_e('2 People', 'wiloke'); ?></option>
                    <option value="3"><?php esc_html_e('3 People', 'wiloke'); ?></option>
                    <option value="4"><?php esc_html_e('4 People', 'wiloke'); ?></option>
                    <option value="5"><?php esc_html_e('5 People', 'wiloke'); ?></option>
                    <option value="6"><?php esc_html_e('6 People', 'wiloke'); ?></option>
                    <option value="7"><?php esc_html_e('7 People', 'wiloke'); ?></option>
                    <option value="8"><?php esc_html_e('8 People', 'wiloke'); ?></option>
                    <option value="9"><?php esc_html_e('9 People', 'wiloke'); ?></option>
                    <option value="10"><?php esc_html_e('10 People', 'wiloke'); ?></option>
                </select>

            </div>

            <div class="listgo-button-wrap">
                <button type="submit" class="listgo-btn listgo-btn--lg listgo-btn--block btn-primary"><?php echo esc_html($aInstance['btn_name']); ?></button>
            </div>
            <input type="hidden" name="RestaurantID" class="RestaurantID" value="<?php echo esc_attr($aListingSettings['restaurant_id']); ?>">
            <input type="hidden" name="rid" class="rid" value="<?php echo esc_attr($aListingSettings['restaurant_id']); ?>">
            <input type="hidden" name="GeoID" class="GeoID" value="15">
            <input type="hidden" name="txtDateFormat" class="txtDateFormat" value="MM/dd/yyyy">
            <input type="hidden" name="RestaurantReferralID" class="RestaurantReferralID" value="<?php echo esc_attr($aListingSettings['restaurant_id']); ?>">
        </form>
		<?php
		echo $atts['after_widget'];
	}
}
