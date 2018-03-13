<?php
namespace WilokeListGoFunctionality\Frontend;

use WilokeListGoFunctionality\Payment\Payment as WilokePayment;


class FrontendCheckout{
	public function __construct() {
		add_action('wp_footer', array($this, 'addCreditCardPopUpToFooter'), 10);
		add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'), 10);
	}

	public function enqueueScripts(){
		$aConfiguration = WilokePayment::getPaymentConfiguration();
		global $post;

		if ( !isset($post->ID) || ($post->ID != $aConfiguration['checkout']) ){
			return false;
		}

		wp_enqueue_script('wiloke-listgo-checkout', plugin_dir_url(dirname(__FILE__)) . '../public/source/js/checkout.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true );
	}

	public function addCreditCardPopUpToFooter(){
		$aConfiguration = WilokePayment::getPaymentConfiguration();

		global $post;
		if ( $post->ID != $aConfiguration['checkout'] ){
			return false;
		}

		?>
		<div id="wiloke-form-claim-information-wrapper" class="wil-modal wil-modal--fade">
			<div class="wil-modal__wrap">
				<div class="wil-modal__content">
					<div class="claim-form">
						<h2 class="claim-form-title"><?php echo esc_html(get_option('blogname')); ?></h2>
						<div class="claim-form-content">
							<form id="wiloke-form-creditcard-with-paypal" method="POST" class="form" action="">
								<p class="hidden message error" style="color: red"></p>
								<p>
									<label for="card-number"><?php esc_html_e('Card Number', 'wiloke'); ?></label>
									<input id="card-number" name="card_number" type="text" required>
								</p>
								<p>
									<label for="card-name"><?php esc_html_e('Name On Card', 'wiloke'); ?></label>
									<input id="card-name" name="card_name" type="text" required>
								</p>
								<p>
									<label for="card-type"><?php esc_html_e('Card Type', 'wiloke'); ?></label>
									<select  id="card-type" name="card_type">
										<option value="Visa"><?php esc_html_e('Visa', 'wiloke'); ?></option>
										<option value="MasterCard"><?php esc_html_e('MasterCard', 'wiloke'); ?></option>
										<option value="Discover"><?php esc_html_e('Discover', 'wiloke'); ?></option>
										<option value="Amex"><?php esc_html_e('Amex', 'wiloke'); ?></option>
									</select>
								</p>
								<p>
									<label for="expiry-date"><?php esc_html_e('Expiry Date', 'wiloke'); ?></label>
									<select name="expMonth">
										<option value="1"><?php esc_html_e('Jan', 'wiloke'); ?></option>
										<option value="2"><?php esc_html_e('Feb', 'wiloke'); ?></option>
										<option value="3"><?php esc_html_e('Mar', 'wiloke'); ?></option>
										<option value="4"><?php esc_html_e('Apr', 'wiloke'); ?></option>
										<option value="5"><?php esc_html_e('May', 'wiloke'); ?></option>
										<option value="6"><?php esc_html_e('Jun', 'wiloke'); ?></option>
										<option value="7"><?php esc_html_e('Jul', 'wiloke'); ?></option>
										<option value="8"><?php esc_html_e('Aug', 'wiloke'); ?></option>
										<option value="9"><?php esc_html_e('Sep', 'wiloke'); ?></option>
										<option value="10"><?php esc_html_e('Oct', 'wiloke'); ?></option>
										<option value="11"><?php esc_html_e('Nov', 'wiloke'); ?></option>
										<option value="12"><?php esc_html_e('Dec', 'wiloke'); ?></option>
									</select>
									<select name="expYear">
										<?php
										foreach (range(date("Y") + 1, date("Y") + 5) as $year) {
											echo "<option value='".esc_attr($year)."'>".esc_html($year)."</option>";
										}
										?>
									</select>
								</p>
								<p>
									<label for="card-cvv"><?php esc_html_e('CVV', 'wiloke'); ?></label>
									<input id="card-cvv" name="cvv" type="text" required>
								</p>
								<input type="submit" value="<?php esc_html_e('Proceed Payment With PayPal', 'wiloke'); ?>">
							</form>
						</div>
						<div class="wil-modal__close"></div>
					</div>
				</div>
			</div>
			<div class="wil-modal__overlay"></div>
		</div>
		<?php
	}
}