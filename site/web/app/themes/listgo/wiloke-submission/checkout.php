<?php
/*
 * Template name: Checkout
 */
global $wiloke;
use WilokeListGoFunctionality\Payment\PayPal as WilokeListgoPayPal;
use WilokeListGoFunctionality\Payment\Payment as WilokeListgoPayment;
use WilokeListGoFunctionality\Payment\CheckPayment as WilokeListgoCheckPayment;

if ( !isset($_REQUEST['package_id']) || empty($_REQUEST['package_id']) ){
    wp_die( esc_html__('You do not have permission to access this page', 'listgo') );
}

$postLink = get_permalink($post->ID);
if ( strpos($postLink, '?') !== false ){
	$postLink .= "&amp;process=payment&amp;package_id=".$_REQUEST['package_id']."&amp;payment_method=";
}else{
	$postLink .= "?process=payment&amp;package_id=".$_REQUEST['package_id']."&amp;payment_method=";
}

if ( isset($_REQUEST['payment_method']) ){
    $aGateWays = WilokeListgoPayment::acceptGateWays();
    if ( empty($aGateWays) ){
        if ( current_user_can('edit_theme_options') ){
            WilokeAlert::render_alert(esc_html__('You have not setup any payment gateways yet. Please go to Wiloke Submission -> Settings and set one.', 'listgo'), 'warning');
        }else{
	        wp_die( esc_html__('We do not accept any payment method at the moment', 'listgo') );
        }
    }

    if ( !in_array($_REQUEST['payment_method'], $aGateWays) ){
        wp_die( sprintf(esc_html__('We do not accept payment via %s', 'listgo'), $_REQUEST['payment_method']) );
    }

    if ( $_REQUEST['payment_method'] === 'paypal' ){
	    WilokeListgoPayPal::setExPressCheckout();
    }elseif( $_REQUEST['payment_method'] === 'checkpayment' ){
        $instCheckpayment = new WilokeListgoCheckPayment();
	    $instCheckpayment->addNewPayment($_REQUEST['package_id']);
    }
}

get_header();

    if ( !WilokeListgoPayment::checkSession() ) {

	    WilokeAlert::render_alert($wiloke->aConfigs['translation']['packagenotexist'], 'warning', false);

    } else {

	    while(have_posts()) : the_post();

            $aPageSettings = Wiloke::getPostMetaCaching($post->ID, 'page_settings');

		    WilokePublic::singleHeaderBg($post, $aPageSettings); ?>

            <div class="page-checkout">

                <div class="container">

                    <div class="page-checkout__content">
                        
                        <h3><?php esc_html_e('Your package', 'listgo'); ?></h3>

                        <?php $aPackageInfo = Wiloke::getPostMetaCaching($_REQUEST['package_id'], 'pricing_settings'); ?>
                        <input type="hidden" id="post_id" name="post_id" value="<?php echo isset($_REQUEST['post_id']) ? esc_attr($_REQUEST['post_id']) : ''; ?>">
                        <div class="table-responsive">
                            <table class="table page-checkout__table">
                                <tr>
                                    <th><?php esc_html_e('Package', 'listgo'); ?></th>
                                    <th><?php esc_html_e('Amount', 'listgo'); ?></th>
                                    <th><?php esc_html_e('Listing availability', 'listgo'); ?></th>
                                    <th><?php esc_html_e('Number of listings', 'listgo'); ?></th>
                                    <th><?php esc_html_e('Publish on map', 'listgo'); ?></th>
                                </tr>
                                <tr>
                                    <td><?php echo esc_html(get_the_title($_REQUEST['package_id'])); ?></td>
                                    <td><?php echo esc_html(WilokeListGoFunctionality\Payment\Payment::renderPrice($aPackageInfo['price'])); ?></td>
                                    <td><?php echo empty($aPackageInfo['duration']) ? esc_html__('Unlimited availability', 'listgo') : esc_html($aPackageInfo['duration']); ?></td>
                                    <td><?php echo empty($aPackageInfo['number_of_posts']) ? esc_html__('Unlimited listings', 'listgo') : esc_html($aPackageInfo['number_of_posts']); ?></td>
                                    <td><?php echo ($aPackageInfo['publish_on_map']==='enable') ? esc_html__('Enable', 'listgo') : esc_html__('Disable', 'listgo'); ?></td>
                                </tr>
                            </table>
                        </div>
                                       
                        <div class="fr">
                            <h3><?php esc_html_e('Proceed to checkout', 'listgo'); ?></h3>
                            <?php if ( $aGateWays = WilokeListgoPayment::getPaymentGateWays() ) : ?>
                            <div class="page-checkout__buttons">
                                <?php foreach ($aGateWays as $gateway => $label)  :?>
                                    <a id="wiloke-proceed-with-<?php echo esc_attr($gateway); ?>" class="listgo-btn listgo-btn--sm listgo-btn--round" href="<?php echo esc_url($postLink.$gateway); ?>"><?php echo esc_html($label); ?></a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                    </div>

                </div>

            </div>

		    <?php

	    endwhile; 

        wp_reset_postdata();
    }

get_footer();