<?php
use WilokeWidget\Supports\Helpers;

class WilokeMailChimp extends WP_Widget
{
	public $aDef = array('title'=>'Subscrible');
	public function __construct()
	{
		parent::__construct('wiloke_mailchimp', WILOKE_WIDGET_PREFIX . esc_html__( 'MailChimp', 'wiloke'), array('classname'=>'widget_newsletter'));
	}

	public function form($aInstance)
	{
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		Helpers::textField( esc_html__('Title', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
		Helpers::description(esc_html__('Ensure that MailChimp Information has been configured. If you still not do that, please go to Settings -> Wiloke MailChimp', 'wiloke'));
	}

	public function update($aNewinstance, $aOldinstance)
	{
		$aInstance = $aOldinstance;
		foreach ( $aNewinstance as $key => $val )
		{
			$aInstance[$key] = strip_tags($val);
		}
		return $aInstance;
	}

	public function widget($atts, $aInstance) {
		$aInstance = wp_parse_args($aInstance, $this->aDef);

		echo $atts['before_widget'];

		if ( !empty($aInstance['title']) ) {
			print $atts['before_title'] . esc_html($aInstance['title']) . $atts['after_title'];
		}

		$aSubscribeSettings = \Wiloke::getOption('wiloke_subscribe_settings');

		if ( empty($aSubscribeSettings) ){
			WilokeAlert::render_alert(__('You have not configured MailChimp yet! Please go to Settings -> Wiloke MailChimp to do that', 'wiloke'), 'warning');
		}

		?>
		<div class="mailchimp__content">
			<p><?php echo esc_html($aSubscribeSettings['description']); ?></p>
			<form class="pi_subscribe">
				<input type="email" class="pi-subscribe-email wiloke-subscribe-email form-remove" placeholder="<?php esc_html_e('Enter your email...', 'wiloke'); ?>" value="">
				<button type="submit" class="wiloke-submit-subscribe pi-btn pi-subscribe form-remove" data-sendingtext="<?php esc_html_e('Sending', 'wiloke'); ?>"><?php esc_html_e('Send', 'wiloke'); ?> <i class="icon_mail_alt"></i></button>
				<p class="message"></p>
			</form>
		</div>
		<?php
		echo $atts['after_widget'];
	}
}