<?php
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentHistory;
use WilokeListGoFunctionality\Submit\User as WilokeUser;
use WilokeListGoFunctionality\Frontend\FrontendListingManagement as WilokeFrontEndManagement;
use WilokeListGoFunctionality\CustomerPlan\CustomerPlan as WilokeCustomerPlan;
use WilokeListGoFunctionality\Payment\Payment as WilokePayment;

global $WilokeListGoFunctionalityApp;
?>
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
		<h2 class="author-page__title">
			<i class="icon_documents_alt"></i> <?php esc_html_e('Billing', 'listgo'); ?>
		</h2>
		<?php
		$postsPerPage = 100;
		global $wpdb;
		$tblHistory = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

		$aResults = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT $tblHistory.* FROM $tblHistory WHERE $tblHistory.user_ID=%d AND (status = 'Success' OR status = 'completed' OR status='Voided') AND (information<>'') AND ( (profile_status IS NULL) OR (profile_status = 'PendingProfile') OR (profile_status = 'ActiveProfile') OR (profile_status='SuspendedProfile')  ) ORDER BY $tblHistory.created_at DESC LIMIT $postsPerPage",
				WilokePublic::$oUserInfo->ID
			)
		);

		$total = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(ID) FROM $tblHistory WHERE $tblHistory.user_ID=%d AND (status = 'Success' OR status = 'completed' OR status='Voided') AND (information<>'') AND ( (profile_status IS NULL) OR (profile_status = 'PendingProfile') OR (profile_status = 'ActiveProfile') OR (profile_status='SuspendedProfile') )",
				WilokePublic::$oUserInfo->ID
			)
		);

		$aWilokeSMSettings = WilokePublic::getPaymentField();
		$aUserPaymentInfo = WilokeCustomerPlan::getCustomerPlan();

		if ( (WilokePayment::getBillingType() === 'RecurringPayments') && !WilokeCustomerPlan::isNonRecurringPlan() ) :
			$aUserPaymentInfo = WilokeCustomerPlan::getCustomerPlan(true);
			$packageID = isset($aUserPaymentInfo['packageID']) ? $aUserPaymentInfo['packageID'] : '';
			$aPackageInfo = Wiloke::getPostMetaCaching($packageID, 'pricing_settings');

            $aArgs = array(
                'post_type' => 'pricing',
                'post_status' => 'publish'
            );

            if ( isset($aWilokeSMSettings['customer_plans']) && !empty($aWilokeSMSettings['customer_plans']) ){
                $aArgs['post__in'] = explode(',', $aWilokeSMSettings['customer_plans']);
                $aArgs['orderby'] = 'post__in';
            }else{
                $aArgs['posts_per_page'] = -1;
            }

            $currentGateWay = isset($aUserPaymentInfo['gateWay']) ? strtolower($aUserPaymentInfo['gateWay']) : '';
            $query = new WP_Query($aArgs);
            if ( $query->have_posts() ) :
		?>
                <div id="wiloke-my-subscription-plan-wrapper" class="account-page">
                    <h4><?php esc_html_e('My Subscription', 'listgo'); ?><span class="wiloke-my-plan-name"><?php echo !empty($packageID) ? get_the_title($packageID) : esc_html__('No Plan', 'listgo'); ?></span> - <?php esc_html_e('Remaining Listing(s)', 'listgo') ?> <span class="wiloke-my-plan-name"><?php echo esc_html(Wiloke::getSession(WilokeCustomerPlan::$myRemainListingsKey)); ?></span></h4>
                    <?php if ( isset($_REQUEST['status']) && ($_REQUEST['status'] === 'changed_plan') && isset($_REQUEST['payment_method']) && ($_REQUEST['payment_method'] === 'paypal') && ( isset($_REQUEST['token']) && ( Wiloke::getSession(WilokeFrontEndManagement::$latestTokenKey) !== $_REQUEST['token']) ) ) : ?>
                    <div id="wiloke-transaction-message" class="wil-alert wil-alert-has-icon" data-token="<?php echo esc_attr($_REQUEST['token']); ?>">
                        <span class="wil-alert-icon"><i class="icon_error-triangle_alt"></i></span>
                        <p class="wil-alert-message"><?php esc_html_e('Updating Your Business Plan ... ', 'listgo'); ?></p>
                    </div>
                    <?php endif; ?>

                    <div id="wiloke-failed-change-plan" class="wil-alert wil-alert-has-icon alert-danger hidden">
                        <span class="wil-alert-icon"><i class="icon_error-triangle_alt"></i></span>
                        <p class="wil-alert-message"></p>
                    </div>
                    <div id="wiloke-success-change-plan" class="wil-alert wil-alert-has-icon alert-success hidden">
                        <span class="wil-alert-icon"><i class="icon_box-checked"></i></span>
                        <p class="wil-alert-message"></p>
                    </div>

                    <div class="billing-row">

                        <div class="billing-left">
                            <form id="wiloke-modify-subscription-plan-form" action="" method="POST">
                                <p>
                                    <label for="wiloke-submission-customer-plan"><?php esc_html_e('Want to Modify Your Plan?', 'listgo'); ?></label>
                                    <select name="wiloke_submission_customer_plan" id="wiloke-submission-customer-plan">
                                        <?php if ( empty($packageID) ) : ?>
                                        <option value="">---</option>
                                        <?php endif; ?>
                                        <?php
                                            while ($query->have_posts()) :
                                                if ( empty($packageID) ){
                                                    $packageID = $query->post->ID;
                                                }
                                                $query->the_post();
                                        ?>
                                                <option value="<?php echo esc_attr($query->post->ID); ?>" <?php selected($packageID, $query->post->ID); ?>><?php echo esc_html($query->post->post_title); ?></option>
                                        <?php
                                            endwhile;
                                        ?>
                                    </select>
                                </p>
                                <p>
                                    <label for="wiloke-submission-proceed-with"><?php esc_html_e('Proceed with', 'listgo'); ?></label>
                                    <select name="wiloke_submission_proceed_with" id="wiloke-submission-proceed-with">
                                        <?php if ( $aGateWays = WilokePayment::getPaymentGateWays() ) : ?>
                                            <?php foreach ($aGateWays as $gateway => $label)  :?>
                                                <option value="<?php echo esc_attr($gateway); ?>" <?php selected($gateway, $currentGateWay); ?>><?php echo esc_html($label); ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </p>
                                <button type="submit" class="listgo-btn btn-primary listgo-btn--sm"><?php esc_html_e('Update Plan', 'listgo'); ?></button>
                            </form>

                            <?php
                            ## Out Standing Amount
                            if ( !empty(WilokeCustomerPlan::$getRPPDetailsResponse) && !empty(WilokeCustomerPlan::getOutStandingBalance(WilokeCustomerPlan::$getRPPDetailsResponse)) ):
                                ?>
                                <div id="wiloke-outstanding-balance" class="account-page">
                                    <h4><?php esc_html_e('Whoops! There\'s an outstanding balance on your account', 'listgo'); ?></h4>
                                    <p><?php Wiloke::wiloke_kses_simple_html(__('We noticed that your account has an outstanding balance of %s. We can\'t automatically deduct from PayPal account, so to pay the balance please click on Bill Outstanding Amount above', 'listgo'), WilokeCustomerPlan::renderOutStandingAmount()); ?></p>

                                    <div id="wiloke-failed-bill-outstanding" class="wil-alert wil-alert-has-icon alert-danger hidden">
                                        <span class="wil-alert-icon"><i class="icon_error-triangle_alt"></i></span>
                                        <p class="wil-alert-message"></p>
                                    </div>
                                    <div id="wiloke-success-bill-outstanding" class="wil-alert wil-alert-has-icon alert-success hidden">
                                        <span class="wil-alert-icon"><i class="icon_box-checked"></i></span>
                                        <p class="wil-alert-message"></p>
                                    </div>

                                    <form id="wiloke-billing-outstanding-amount-form" action="" method="POST">
                                        <button type="submit" class="listgo-btn btn-primary listgo-btn--sm"><?php esc_html_e('Bill Outstanding Amount', 'listgo'); ?></button>
                                    </form>
                                </div>
                            <?php endif; ?>

                            <!-- Billing History -->
                            <div id="wiloke-listgo-my-billing" class="account-page" data-total="<?php echo esc_attr($total); ?>" data-postsperpage="<?php echo esc_attr($postsPerPage); ?>">
                                <h4><?php esc_html_e('Billing History', 'listgo'); ?></h4>
                                <?php if ( !empty($aResults) && !is_wp_error($aResults) ) : ?>
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th><?php esc_html_e('Date', 'listgo'); ?></th>
                                            <th><?php esc_html_e('GateWay', 'listgo'); ?></th>
                                            <th><?php esc_html_e('Amount', 'listgo'); ?></th>
                                            <th><?php esc_html_e('Package Info', 'listgo'); ?></th>
                                            <th><?php esc_html_e('Status', 'listgo'); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody id="wiloke-listgo-show-listings" class="f-listings">
                                        <?php
                                        foreach ($aResults as $oResult){
                                            WilokePublic::renderBillingItem($oResult);
                                        }
                                        ?>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="5" id="wiloke-listgo-pagination" class="nav-links text-center"></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                    <?php
                                else:
                                    WilokeAlert::render_alert( __('There is no billing yet.', 'listgo'), 'info', false,  false);
                                endif; wp_reset_postdata();
                                ?>
                            </div>
                            <!-- End / Billing History -->

                        </div>

                        <div class="billing-right">
                            <div id="wiloke-show-package-detail">
                                <?php echo do_shortcode('[wiloke_pricing specify_ids="'.$packageID.'" is_check_billing_type="no"]'); ?>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
            endif; wp_reset_postdata();
        else: ?>
        <!-- Billing History -->
        <div id="wiloke-listgo-my-billing" class="account-page" data-total="<?php echo esc_attr($total); ?>" data-postsperpage="<?php echo esc_attr($postsPerPage); ?>" style="padding: 40px;">
            <h4><?php esc_html_e('Billing History', 'listgo'); ?></h4>
            <?php if ( !empty($aResults) && !is_wp_error($aResults) ) : ?>
                <table class="table">
                    <thead>
                    <tr>
                        <th><?php esc_html_e('Date', 'listgo'); ?></th>
                        <th><?php esc_html_e('Gateway', 'listgo'); ?></th>
                        <th><?php esc_html_e('Amount', 'listgo'); ?></th>
                        <th><?php esc_html_e('Package Info', 'listgo'); ?></th>
                        <th><?php esc_html_e('Status', 'listgo'); ?></th>
                    </tr>
                    </thead>
                    <tbody id="wiloke-listgo-show-billings" class="f-listings">
                        <?php
                        foreach ($aResults as $oResult){
                            WilokePublic::renderBillingItem($oResult);
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" id="wiloke-billing-pagination" class="nav-links text-center"></td>
                        </tr>
                    </tfoot>
                </table>
                <?php
            else:
                WilokeAlert::render_alert( __('There is no billing yet.', 'listgo'), 'info', false,  false);
            endif; wp_reset_postdata();
            ?>
        </div>
        <!-- End / Billing History -->
        <?php
        endif;
        ?>
        <?php
            $aGateWays = WilokePayment::acceptGateWays();
            if ( !empty($aGateWays) && in_array('2checkout', $aGateWays) ) :
	            $aCardInfo = WilokeUser::getCard();
	            if ( empty($aCardInfo['card_name']) ){
		            $aCardInfo = apply_filters('wiloke/wiloke-listgo-functionality/app/frontend/FrontendTwoCheckout/cardInfo', $aCardInfo);
	            }
        ?>
            <div id="wiloke-listgo-my-credit-debit-card-wrapper" class="account-page">
                <h4><?php esc_html_e('Credit / Debit Card', 'listgo'); ?></h4>
                <form id="wiloke-my-credi-debit-card-form" action="<?php echo esc_url(admin_url('admin-ajax.php?action=wiloke_save_card')); ?>" method="POST">
                    <div class="form form--profile">
                        <div class="row">

                            <div id="wiloke-save-card-msg-wrapper" class="col-md-12 hidden">
                                <div class="wil-alert wil-alert-has-icon alert-success">
                                    <span class="wil-alert-icon"><i class="icon_box-checked"></i></span>
                                    <p class="wil-alert-message"></p>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-item">
                                    <label for="cardNumber" class="label"><?php esc_html_e('Card Number', 'listgo'); ?></label>
                                    <span class="input-text">
                                        <input id="cardNumber" name="card_number" type="text" value="<?php echo esc_attr($aCardInfo['card_number']); ?>" required>
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-4">

                                <label class="label"><?php esc_html_e('Expiration Date (MM/YYYY)', 'listgo'); ?></label>

                                <div class="row">

                                    <div class="col-xs-6">
                                        <div class="form-item">
                                            <span class="input-text">
                                                <input id="expMonth" type="text" size="2" name="expMonth" value="<?php echo esc_attr($aCardInfo['expMonth']); ?>" placeholder="<?php esc_html_e('MM', 'listgo') ?>" required>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-xs-6">
                                        <div class="form-item">
                                            <span class="input-text">
                                                <input id="expYear" type="text" size="4" name="expYear" value="<?php echo esc_attr($aCardInfo['expYear']); ?>" placeholder="<?php esc_html_e('YYYY', 'listgo') ?>" required>
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="form-item">
                                    <label for="cvv" class="label"><?php esc_html_e('CVC', 'listgo'); ?></label>
                                    <span class="input-text">
                                        <input id="cvv" type="text" name="cvv" value="<?php echo esc_attr($aCardInfo['cvv']); ?>" required>
                                    </span>
                                </div>
                            </div>


                            <div class="col-sm-12">
                                <div class="form-item">
                                    <label for="cardName" class="label"><?php esc_html_e('Name On Card', 'listgo'); ?></label>
                                    <span class="input-text">
                                        <input id="cardName" type="text" name="card_name" value="<?php echo esc_attr($aCardInfo['card_name']); ?>" required>
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-item">
                                    <label for="cardAddress" class="label"><?php esc_html_e('Address', 'listgo'); ?></label>
                                    <span class="input-text">
                                        <input id="cardAddress" name="card_address1" type="text" value="<?php echo esc_attr($aCardInfo['card_address1']); ?>" required>
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-item">
                                    <label for="cardCity" class="label"><?php esc_html_e('Card holder\'s City', 'listgo'); ?></label>
                                    <span class="input-text">
                                        <input id="cardCity" name="card_city" type="text" value="<?php echo esc_attr($aCardInfo['card_city']); ?>" required>
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-item">
                                    <label for="cardCountry" class="label"><?php esc_html_e('Card holder\'s Country', 'listgo'); ?></label>

                                    <select name="card_country" id="cardCountry">
                                        <?php foreach ($WilokeListGoFunctionalityApp['countryCode'] as $symbol => $country) :?>
                                            <option <?php selected($symbol, $aCardInfo['card_country']); ?> value="<?php echo esc_attr($symbol); ?>"><?php echo esc_html($country); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-item">
                                    <label for="cardEmail" class="label"><?php esc_html_e('Card holder\'s Email', 'listgo'); ?></label>
                                    <span class="input-text">
                                        <input id="cardEmail" name="card_email" type="text" value="<?php echo esc_attr($aCardInfo['card_email']); ?>" required>
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-item">
                                    <label for="cardPhone" class="label"><?php esc_html_e('Card holder\'s Phone', 'listgo'); ?></label>
                                    <span class="input-text">
                                        <input id="cardPhone" name="card_phone" type="text" value="<?php echo esc_attr($aCardInfo['card_phone']); ?>" required>
                                    </span>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="profile-actions">
                                    <input type="submit" class="listgo-btn btn-primary listgo-btn--sm" value="<?php esc_html_e('Save Card', 'listgo'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <?php endif; ?>
	</div>
</div>