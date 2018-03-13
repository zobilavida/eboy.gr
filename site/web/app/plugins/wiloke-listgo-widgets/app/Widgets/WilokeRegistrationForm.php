<?php
use WilokeWidget\Supports\Helpers;

class WilokeRegistrationForm extends WP_Widget
{
	public $aDef = array('title'=>'Register Now', 'username_label'=>'Username', 'email_label'=>'Email address', 'description'=>'By creating an account you agree to our <a href="#">Terms and Conditions</a> and our <a href="#">Privacy Policy</a>.', 'btn_text'=>'Register', 'always_show'=>'disbale', 'password_label'=>'Password');
	public function __construct()
	{
		parent::__construct('wiloke_registration_form', WILOKE_WIDGET_PREFIX . esc_html__( 'Registration', 'wiloke'), array('classname'=>'widget_registration_form listgo-register'));
	}

	public function form($aInstance)
	{
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		Helpers::textField( esc_html__('Register Now', 'wiloke'), $this->get_field_id('title'), $this->get_field_name('title'), $aInstance['title']);
		Helpers::textField( esc_html__('Email address Label', 'wiloke'), $this->get_field_id('email_label'), $this->get_field_name('email_label'), $aInstance['email_label']);
		Helpers::textField( esc_html__('Username Label', 'wiloke'), $this->get_field_id('username_label'), $this->get_field_name('username_label'), $aInstance['username_label']);
		Helpers::textField( esc_html__('Password Label', 'wiloke'), $this->get_field_id('password_label'), $this->get_field_name('password_label'), $aInstance['password_label']);
		Helpers::textField( esc_html__('Register Button', 'wiloke'), $this->get_field_id('btn_text'), $this->get_field_name('btn_text'), $aInstance['btn_text']);
	}

	public function update($aNewinstance, $aOldinstance)
	{
		$aInstance = $aOldinstance;
		foreach ( $aNewinstance as $key => $val )
		{
			$aInstance[$key] = strip_tags($val, '<a><br><p><i><strong>');
		}
		return $aInstance;
	}

	public function widget($atts, $aInstance) {
		if ( is_user_logged_in() ){
			return false;
		}
		global $wiloke;

		$aInstance = wp_parse_args($aInstance, $this->aDef);
		echo $atts['before_widget'];
		?>
		<div id="wiloke-widget-signup-signin-wrapper">
			<div class="listgo-register-header">
				<span class="listgo-register-header-line1"></span>
				<span class="listgo-register-header-line2"></span>
				<h4><?php echo esc_html($aInstance['title']) ?></h4>
			</div>
			<div class="listgo-register-form">
				<form id="wiloke-widget-signup-form" class="signup-form" action="#">
					<p class="print-msg-here error-msg"></p>
					<div class="form-item">
						<label for="email" class="label"><?php echo esc_html($aInstance['email_label']) ?> <sup>*</sup></label>
						<span class="input-text">
							<input id="email" type="email" name="email" value="" required>
						</span>
					</div>
                    <div class="form-item">
                        <label for="username" class="label"><?php echo esc_html($aInstance['username_label']) ?> <sup>*</sup></label>
                        <span class="input-text">
							<input id="username" type="text" name="username" value="" required>
						</span>
                    </div>
                    <div class="form-item">
                        <label for="password" class="label"><?php echo esc_html($aInstance['password_label']) ?> <sup>*</sup></label>
                        <span class="input-text">
							<input id="password" type="password" name="password" value="" required>
						</span>
                    </div>

                    <?php do_action('wiloke/signup-form/before-submit-button'); ?>
					<div class="form-item">
						<button type="submit" id="signup-btn" class="listgo-btn btn-primary listgo-btn--full signup-btn"><?php echo esc_html($aInstance['btn_text']); ?></button>
					</div>
					<?php if ( !empty($wiloke->aThemeOptions['sign_in_desc']) ) : ?>
						<div class="form-item form-description text-center" style="font-style: italic">
							<?php \Wiloke::wiloke_kses_simple_html($wiloke->aThemeOptions['sign_in_desc']); ?>
						</div>
					<?php endif; ?>
				</form>
			</div>
		</div>
		<?php
		echo $atts['after_widget'];
	}
}