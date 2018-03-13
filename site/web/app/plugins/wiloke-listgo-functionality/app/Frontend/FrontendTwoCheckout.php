<?php
namespace WilokeListGoFunctionality\Frontend;

use WilokeListGoFunctionality\Payment\Payment as WilokePayment;
use WilokeListGoFunctionality\Submit\User as WilokeUser;

class FrontendTwoCheckout{
	public function __construct() {
		add_action('wp_footer', array($this, 'addCreditCardPopUpToFooter'), 10);
		add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'), 10);
	}

	public function enqueueScripts(){
		$aConfiguration = WilokePayment::getPaymentConfiguration();
		global $post;
		if ( !isset($post->ID) || ($post->ID != $aConfiguration['checkout']) && ($post->ID != $aConfiguration['myaccount']) ){
			return false;
		}

		wp_enqueue_script('2co', 'https://www.2checkout.com/checkout/api/2co.min.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true );
		wp_enqueue_script('wiloke-listgo-checkout', plugin_dir_url(dirname(__FILE__)) . '../public/source/js/checkout.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true );
	}

	public function addCreditCardPopUpToFooter(){
		$aConfiguration = WilokePayment::getPaymentConfiguration();
		if ( !isset($aConfiguration['billing_type']) && ($aConfiguration['billing_type'] !== 'RecurringPayments') ){
			return false;
		}

		global $post, $WilokeListGoFunctionalityApp;

		if ( !isset($post->ID) || ($post->ID != $aConfiguration['checkout']) ){
			return false;
		}
		$aCardInfo = WilokeUser::getCard();

        if ( empty($aCardInfo['card_name']) ){
	        $aCardInfo = apply_filters('wiloke/wiloke-listgo-functionality/app/frontend/FrontendTwoCheckout/cardInfo', $aCardInfo);
        }

		?>
		<div id="wiloke-form-two-checkout-wrapper" class="wil-modal wil-modal--fade">
			<div class="wil-modal__wrap">
				<div class="wil-modal__content">
					<div class="claim-form">
						<h2 class="claim-form-title"><?php echo esc_html(get_option('blogname')); ?></h2>
						<div class="claim-form-content">
							<form id="wiloke-form-creditcard-with-2checkout" method="POST" class="form" action="">
                                <?php do_action('wiloke/wiloke-listgo-functionality/app/frontend/FrontendTwoCheckout/popupMessage'); ?>
								<p class="hidden message error" style="color: red"></p>

                                <div class="row">

                                	<div class="col-sm-6">
                                		<div class="form-item">
                                		    <label for="cardName"><?php esc_html_e('Card holder\'s name', 'wiloke'); ?></label>
                                		    <input id="cardName" name="card_name" type="text" value="<?php echo esc_attr($aCardInfo['card_name']); ?>" required>
                                		</div>
                                		<div class="form-item">
                                		    <label for="cardAddress"><?php esc_html_e('Card holder\'s Street Address', 'wiloke'); ?></label>
                                		    <input id="cardAddress" name="card_address1" type="text" value="<?php echo esc_attr($aCardInfo['card_address1']); ?>" required>
                                		</div>
                                		<div class="form-item">
                                		    <label for="cardCity"><?php esc_html_e('Card holder\'s City', 'wiloke'); ?></label>
                                		    <input id="cardCity" name="card_city" type="text" value="<?php echo esc_attr($aCardInfo['card_city']); ?>" required>
                                		</div>
                                		<div class="form-item">
                                		    <label for="cardCountry"><?php esc_html_e('Card holder\'s Country', 'wiloke'); ?></label>
                                            <select name="card_country" id="cardCountry">
				                                <?php foreach ($WilokeListGoFunctionalityApp['countryCode'] as $symbol => $country) :?>
                                                    <option <?php selected($symbol, $aCardInfo['card_country']); ?> value="<?php echo esc_attr($symbol); ?>"><?php echo esc_html($country); ?></option>
				                                <?php endforeach; ?>
                                            </select>
                                		</div>
                                		<div class="form-item">
                                		    <label for="cardEmail"><?php esc_html_e('Card holder\'s Email', 'wiloke'); ?></label>
                                		    <input id="cardEmail" name="card_email" type="text" value="<?php echo esc_attr($aCardInfo['card_email']); ?>" required>
                                		</div>
                                	</div>

									<div class="col-sm-6">
										<div class="form-item">
                                		    <label for="cardPhone"><?php esc_html_e('Card holder\'s Phone', 'wiloke'); ?></label>
                                		    <input id="cardPhone" name="card_phone" type="text" value="<?php echo esc_attr($aCardInfo['card_phone']); ?>" required>
                                		</div>

										<div class="form-item">
											<label for="cardNumber"><?php esc_html_e('Card Number', 'wiloke'); ?></label>
											<input id="cardNumber" name="card_number" type="text" autocomplete="off" value="<?php echo esc_attr($aCardInfo['card_number']); ?>" required>
										</div>
										<div class="form-item">
											<label><?php esc_html_e('Expiration Date (MM/YYYY)', 'wiloke'); ?></label>
											<div class="row">
												<div class="col-xs-6">
													<input id="expMonth" size="2" name="expMonth" type="text" value="<?php echo esc_attr($aCardInfo['expMonth']); ?>"  placeholder="<?php esc_html_e('MM', 'wiloke') ?>" required>
												</div>
												<div class="col-xs-6">
													<input id="expYear" name="expYear" size="4" type="text" placeholder="<?php esc_html_e('YYYY', 'wiloke') ?>" value="<?php echo esc_attr($aCardInfo['expYear']); ?>" required>
												</div>
											</div>
											
										</div>
										<div class="form-item">
											<label for="cardCvv"><?php esc_html_e('CVC', 'wiloke'); ?></label>
											<input id="cardCvv" name="cvv" type="text" autocomplete="off" required value="<?php echo esc_attr($aCardInfo['cvv']); ?>">
										</div>
									</div>

                                </div>

                                <input type="submit" value="<?php esc_html_e('Proceed Payment With 2Checkout', 'wiloke'); ?>">
								
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