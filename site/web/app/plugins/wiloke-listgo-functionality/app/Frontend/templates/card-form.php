<?php
use WilokeListGoFunctionality\Submit\User as WilokeUser;
global $post, $WilokeListGoFunctionalityApp;
$aCardInfo = WilokeUser::getCard();
?>
<div id="wiloke-credit-debit-card" class="wiloke-event-method__form">
    <div class="addlisting-popup__event-method-form">
        <?php do_action('wiloke/wiloke-listgo-functionality/app/frontend/template/card-form/after_form_open'); ?>
        <div class="form form--profile">
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

                <div class="row">
                    <div class="col-sm-12">
                        <div class="profile-actions">
                            <button id="listgo-save-card" class="listgo-btn btn-primary listgo-btn--sm pull-right"><?php esc_html_e('Save Card', 'listgo'); ?></button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php do_action('wiloke/wiloke-listgo-functionality/app/frontend/template/card-form/before_form_close'); ?>
    </div>
</div>