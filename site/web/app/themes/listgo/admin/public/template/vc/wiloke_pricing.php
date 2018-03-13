<?php
use \WilokeListGoFunctionality\Register\RegisterPricingSettings;
use WilokeListGoFunctionality\Payment\Payment as WilokePayment;
use WilokeListGoFunctionality\CustomerPlan\CustomerPlan as WilokeCustomerPlan;

function wiloke_shortcode_pricing($atts){
	$atts = shortcode_atts(
		array(
			'post_ids'       => '',
			'specify_ids'    => '',
			'items_per_row'  => 'col-md-4',
			'btn_name'       => '',
			'image_size'     => 'medium',
			'overlay_color'  => '',
			'css'            => '',
			'extract_class'  => '',
            'is_check_billing_type' => 'yes'
		),
		$atts
	);
    $aWilokeSMSettings = WilokePublic::getPaymentField();
    $aCustomerPlan = WilokeCustomerPlan::getCustomerPlan();

	$currencySymbol = WilokePayment::getCurrency();
	if ( empty($aWilokeSMSettings['addlisting']) ){
		return WilokeAlert::message( __('You need to configure Payment page before: Wiloke Submission ->  Settings. If you do not see Wiloke Submission on the left menu, please go to Appearance -> Install Plugins -> Installing and Activating Wiloke Listgo Functionality plugin.', 'listgo'), true );
	}

    if ( $atts['is_check_billing_type'] == 'yes' ) {
	    if ( ($aWilokeSMSettings['billing_type'] === 'RecurringPayments') && !empty($aCustomerPlan) ){
		    $billingUrl = WilokePublic::addQueryToLink(get_permalink($aWilokeSMSettings['myaccount']), 'mode=my-billing');
		    $addListingUrl = WilokePublic::addQueryToLink(get_permalink($aWilokeSMSettings['addlisting']), 'package_id='.$aCustomerPlan['packageID']);

		    return WilokeAlert::render_alert(sprintf(__('Whoops! You already purchased a package plan before. You can change your plan by going to <a href="%s">Dashboard\'s Billing</a> section or go to <a href="%s">Add Listing page</a> to add a new listing.', 'listgo'), esc_url($billingUrl), esc_url($addListingUrl)
		    ), 'info', true, false );
	    }
    }

	$pageUrl = get_permalink($aWilokeSMSettings['addlisting']);
	$wrapperClass = $atts['extract_class'] . vc_shortcode_custom_css_class($atts['css'], ' ');

	if ( !empty($atts['specify_ids']) ){
		$aPostIds = array($atts['specify_ids']);
    }else{
		$aPostIds = isset($aWilokeSMSettings['customer_plans']) && !empty($aWilokeSMSettings['customer_plans']) ? explode(',', $aWilokeSMSettings['customer_plans']) :  null;
    }

	ob_start();
	?>
    <div class="<?php echo esc_attr($wrapperClass); ?>">
        <div class="row">
            <?php
                if ( Wiloke::$wilokePredis && Wiloke::$wilokePredis->exists(RegisterPricingSettings::$redisPricing) ){
                    if ( empty($aPostIds) ){
                        $aAllPackages = Wiloke::$wilokePredis->HGETALL(RegisterPricingSettings::$redisPricing);
                        if ( !empty($aAllPackages) ){
                            foreach ( $aAllPackages  as $oPost ) {
                                $oPost = json_decode($oPost);
                                if ( get_post_status($oPost->ID) !== 'publish' ){
                                    continue;
                                }
                                wiloke_listgo_render_pricing_item($atts, $oPost, $pageUrl, $currencySymbol);
                            }
                        }
                    }else{
                        foreach ( $aPostIds  as $postID ) {
                            $oPost = Wiloke::hGet(RegisterPricingSettings::$redisPricing, $postID, true );
	                        if ( get_post_status($postID) !== 'publish' ){
		                        continue;
	                        }
                            if ( !empty($oPost) ){
                                $oPost = (object) $oPost;
                                wiloke_listgo_render_pricing_item($atts, $oPost, $pageUrl, $currencySymbol);
                            }
                        }
                    }
                }else{
                    if ( !empty($aPostIds) ){
                        $query = new WP_Query(
                            array(
                                'post_type'     => 'pricing',
                                'post__in'      => $aPostIds,
                                'orderby'       => 'post__in',
                                'post_status'   => 'public'
                            )
                        );
                    }else{
                        $query = new WP_Query(
                            array(
                                'post_type'     => 'pricing',
                                'posts_per_page'=> -1,
                                'post_status'   => 'public'
                            )
                        );
                    }

                    if ( !$query->have_posts() ){
                        wp_reset_postdata();
                        return WilokeAlert::render_alert( esc_html__('It seems this post is not publish.', 'listgo'), 'notice', true );
                    }
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        wiloke_listgo_render_pricing_item($atts, $query->post, $pageUrl, $currencySymbol);
                    }
                    wp_reset_postdata();
                }
            ?>
        </div>
    </div>
    <?php
	return ob_get_clean();
}

function wiloke_listgo_render_pricing_item($atts, $oPost, $pageUrl, $currencySymbol){
    if ( get_post_status($oPost->ID) !== 'publish' ||  (get_post_field('post_type', $oPost->ID) !== 'pricing') ){
        return false;
    }

	$aData['post_meta'] = Wiloke::getPostMetaCaching($oPost->ID, 'pricing_settings');
	$aData['featured_image'] = get_the_post_thumbnail_url($oPost->ID, $atts['image_size']);
	$headerClass = 'pricing__header';
	if ( !empty($aData['featured_image']) ){
		$headerClass .= ' bg-scroll';
    }
	$aStatus = WilokePublic::getRemainingOfPackage($oPost->ID, $aData['post_meta']);

	$disabledBtn = '';
	$needToPayForLoan = false;
	if ( ($aStatus['purchased']===1) && empty($aStatus['remaining']) && ($aStatus['type'] === 'free') ){
        $isExpiredFreePackage = true;
		$disabledBtn = 'disabled';
    }else{
	    $isExpiredFreePackage = false;
    }

    if ( empty($aStatus['remaining']) && isset($aStatus['is_processing']) && $aStatus['is_processing'] ){
	    $disabledBtn = 'disabled';
	    $needToPayForLoan = true;
    }

	if ( !isset($aData['post_meta']['price']) || empty($aData['post_meta']['price']) ){
        $price = esc_html__('Free', 'listgo');
    }else{
		$price = class_exists('WilokeListGoFunctionality\Payment\Payment') ? WilokePayment::renderPrice($aData['post_meta']['price'], true) : '<sup>'.esc_html($currencySymbol).'</sup>' . $aData['post_meta']['price'];
    }

	$listingID = '';
    if ( isset($_REQUEST['listing_id']) && !empty($_REQUEST['listing_id']) ){
	    $listingID = '&post_id='.$_REQUEST['listing_id'];
    }

    ?>
    <div class="<?php echo esc_attr($atts['items_per_row']); ?>">
        <div class="pricing <?php echo !empty($aStatus['purchased']) ? 'purchased' : 'not-purchase'; ?>">
            <div class="<?php echo esc_attr($headerClass); ?>" style="background-image: url(<?php echo esc_url($aData['featured_image']); ?>)">
                <h4 class="pricing__title"><?php echo esc_html($oPost->post_title); ?></h4>
                <div class="pricing__price"><?php echo esc_html($price); ?></div>

                <?php if ( $isExpiredFreePackage ) : ?>
                <p class="pricing__desc"><?php esc_html_e('You already have used up all listings in the free package.', 'listgo'); ?></p>
                <?php else: ?>
                    <?php if ( $needToPayForLoan ) : ?>
                        <p class="pricing__desc red"><?php esc_html_e('Whoops! There\'s an outstanding balance on this package. Please make a payment to continue using.', 'listgo'); ?></p>
                    <?php else: ?>
                        <?php if ( isset($aData['post_meta']['description']) ) : ?>
                        <p class="pricing__desc"><?php echo esc_html($aData['post_meta']['description']); ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ( $aData['post_meta']['highlight'] === 'enable' ) : ?>
                <span class="onfeatued"><i class="fa fa-star-o"></i></span>
                <?php endif; ?>

	            <?php if ( !empty($aStatus['purchased']) ) : ?>
		            <?php if ( $aStatus['remaining'] === -1 ) : ?>
                        <i class="wil-remaining green"><?php esc_html_e('Purchased', 'listgo'); ?></i>
		            <?php elseif ( !empty($aStatus['remaining']) ): ?>
                        <i class="wil-remaining"><?php esc_html_e('Remaining: ', 'listgo') ?> <?php echo esc_attr(absint($aStatus['remaining'])); ?></i>
		            <?php endif; ?>
	            <?php endif; ?>

                <?php if ( isset($atts['overlay_color']) && !empty($atts['overlay_color']) ) : ?>
                <div class="overlay" style="background-color: <?php echo esc_attr($atts['overlay_color']); ?>"></div>
                <?php endif; ?>
            </div>

            <div class="pricing__content">
                <?php
                if ( !empty($oPost->post_content) ){
                    $content = str_replace(array('<ul>', '<li><del>', '<li>'), array('<ul class="wil-icon-list">', '<li class="disable"><i class="icon_close_alt2"></i> ', '<li><i class="icon_box-checked"></i>'), $oPost->post_content);
                    Wiloke::wiloke_kses_simple_html($content);
                }
                ?>
            </div>

            <?php if ( $atts['is_check_billing_type'] == 'yes' ) : ?>
            <div class="pricing__foot">
                <a href="<?php echo esc_url(WilokePublic::addQueryToLink($pageUrl, 'package_id='.$oPost->ID.$listingID)); ?>" class="listgo-btn btn-black listgo-btn--full <?php echo esc_attr($disabledBtn); ?>"><?php echo esc_html($atts['btn_name']); ?></a>
            </div>
            <?php endif; ?>

        </div>
    </div>
    <?php
}
