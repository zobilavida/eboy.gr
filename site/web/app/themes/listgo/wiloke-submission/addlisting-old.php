<?php
use WilokeListGoFunctionality\Payment\Payment as WilokePayment;
use WilokeListGoFunctionality\Submit\AddListing as WilokeAddListing;
use WilokeListGoFunctionality\CustomerPlan\CustomerPlan as WilokeCustomerPlan;
use WilokeListGoFunctionality\Frontend\FrontendListingManagement;

WilokePayment::setPackageChosen();
global $wiloke;
get_header();
    if ( have_posts() ){
        while (have_posts()){
            the_post();
            $aPageSettings = Wiloke::getPostMetaCaching($post->ID, 'page_settings');
            WilokePublic::singleHeaderBg($post, $aPageSettings);
            $checkOutUrl = WilokePublic::getPaymentField('checkout');
            $checkOutUrl = !empty($checkOutUrl) ? get_permalink($checkOutUrl) : '#';
            $postID = '';
	        $toggleBusinessHoursStatus = '';
	        $aToggleBusinessHours = array(
                'enable' => esc_html__('Enable', 'listgo'),
                'disable' => esc_html__('Disable', 'listgo')
            );
	        $customerPlanStatus = WilokeCustomerPlan::controlAddListingPage();
	        if ( $customerPlanStatus !== true ) : ?>
                <div class="col-lg-8 col-lg-offset-2">
			        <?php echo $customerPlanStatus; ?>
                </div>
	        <?php elseif( !isset($_REQUEST['package_id']) || empty($_REQUEST['package_id']) ) : ?>
                <div class="col-lg-8 col-lg-offset-2">
			        <?php
			        FrontendListingManagement::message(
                        array(
                            'message' => sprintf(__('You need to select a package before. <a href="%s">Return Package Page</a>', 'listgo'), get_permalink(WilokePublic::getPaymentField('package')))
                        )
                        , 'danger', false
                    );
			        ?>
                </div>
	        <?php elseif(  (get_post_status($_REQUEST['package_id']) != 'publish') ): ?>
                <div class="col-lg-8 col-lg-offset-2">
			        <?php
			        FrontendListingManagement::message(
				        array(
					        'message' => sprintf(__('This package does not exist.<a href="%s">Return Package Page</a>', 'listgo'), get_permalink(WilokePublic::getPaymentField('package')))
				        ), 'danger', false);
			        ?>
                </div>
		        <?php
            elseif ( is_user_logged_in() && !current_user_can('edit_theme_options') && WilokePublic::$oUserInfo->role !== 'wiloke_submission' ) :
		        FrontendListingManagement::message(
                    array(
                        'message' => __('You don\'t have permission to access this page.', 'listgo')
                    ), 'danger', false
                );
	        else:
		        $isEditable = true;
		        if ( (isset($_REQUEST['post_id']) && !empty($_REQUEST['post_id'])) ){
			        $postID = $_REQUEST['post_id'];
			        $postStatus = get_post_field('post_status', $postID);
			        $toggleBusinessHoursStatus = get_post_meta($postID, 'wiloke_toggle_business_hours', true);
			        if ( $postStatus == 'pending' ){
				        $isEditable = false;
				        FrontendListingManagement::message(
					        array(
						        'message' => esc_html__( 'We are reviewing your listing. Please wait for replying from us.', 'listgo' )
					        ),
					        'danger'
				        );
			        }else{
				        if ( (get_post_field('post_status', $postID) == 'publish') && !FrontendListingManagement::publishedListingEditable()) {
					        $isEditable = false;
					        FrontendListingManagement::message(
						        array(
							        'message' => esc_html__( 'You can not edit a published listing.', 'listgo' )
						        ),
						        'danger'
					        );
				        }
			        }
		        }

		        if ( $isEditable ) :
                    $aListingLocations = get_terms(
                        array(
                            'taxonomy'  => 'listing_location',
                            'hide_empty'=> false
                        )
                    );

                    $aListingCats = get_terms(
                        array(
                            'taxonomy'  => 'listing_cat',
                            'hide_empty'=> false
                        )
                    );

                    if ( !empty($postID) ){
                        $oListingInfo     = get_post($postID);
                        $listingContent   = $oListingInfo->post_content;
                        $title            = $oListingInfo->post_title;
                        $aCurrentLocation = Wiloke::getPostTerms($oListingInfo, 'listing_location');
                        $aCurrentCats     = Wiloke::getPostTerms($oListingInfo, 'listing_cat');
                        $aCurrentTags     = Wiloke::getPostTerms($oListingInfo, 'listing_tag');
                        $aSocialMedia     = Wiloke::getPostMetaCaching($oListingInfo->ID, 'listing_social_media');
                    }else{
                        $listingContent   = '';
                        $title            = '';
                        $aCurrentTags = array();
                        $aCurrentCats = array();
                        $aCurrentLocation = array();
                    }

                    if ( !empty($aCurrentLocation) ){
                        $aCurrentLocation = array_map(function($oTerm){
                            return $oTerm->term_id;
                        }, $aCurrentLocation);
                    }

                    if ( !empty($aCurrentCats) ){
                        $aCurrentCats = array_map(function($oTerm){
                            return $oTerm->term_id;
                        }, $aCurrentCats);
                    }

                    if ( $aCurrentTags ){
                        $aCurrentTags = array_map(function($oTerm){
                            return $oTerm->slug;
                        }, $aCurrentTags);
                    }else{
	                    $aCurrentTags = array();
                    }

                    $aSocials = array(
                        'facebook', 'twitter', 'google-plus', 'linkedin', 'tumblr', 'instagram', 'pinterest', 'vimeo', 'youtube', 'whatsapp'
                    );

                    $aPriceSegment = array_merge(
                        array(
                            ''   => esc_html__('Rather not say', 'listgo')
                        ),
                        $wiloke->aConfigs['frontend']['price_segmentation']
                    );

                    $aPrice = Wiloke::getPostMetaCaching($postID, 'listing_price');

                    $aPrice = wp_parse_args(
                        $aPrice,
                        array(
                            'price_segment' => '',
                            'price_from'  => '',
                            'price_to'    => ''
                        )
                    );
                    $aGeneralSettings   = Wiloke::getPostMetaCaching($postID, 'listing_settings');
                    $featuredImageID  = get_post_thumbnail_id($postID);
                    $featuredImageUrl = !empty($featuredImageID) ? get_the_post_thumbnail_url($postID) : get_template_directory_uri() . '/img/featured-image.jpg';

                    $aBusinessHours     = Wiloke::getPostMetaCaching($postID, 'wiloke_listgo_business_hours');
                    $aPackageSettings = WilokeAddListing::packageAllow();
                    $toggleUploadGallery = !isset($aPackageSettings['toggle_allow_add_gallery']) ? 'enable' : $aPackageSettings['toggle_allow_add_gallery'];

                    $addLocationBy = WilokePublic::addLocationBy();
                    $firstGroupClass = WilokePublic::addLocationBy() === 'default' ? 'col-sm-6' : 'col-sm-4';

			        $defaultTemplate = 'templates/single-listing-traditional.php';
			        if ( isset($aPackageSettings['toggle_listing_template']) && ($aPackageSettings['toggle_listing_template'] == 'disable') ){
				        $defaultTemplate = $wiloke->aThemeOptions['listing_layout'];
			        }

			        if ( empty($postID) ){
				        $currentTemplate = $defaultTemplate;
			        }else{
				        $currentTemplate   = get_page_template_slug($postID);
				        $currentTemplate   = !empty($currentTemplate) ? $currentTemplate : 'templates/single-listing-traditional.php';
			        }
	            ?>
                    <div class="page-addlisting">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-10 col-lg-offset-1">
                                <h4 class="author-page__title"><i class="icon_pencil-edit"></i> <?php esc_html_e('Add Listing', 'listgo'); ?></h4>
                                <div class="account-page">
                                    <div class="form form--profile">
                                        
                                        <div class="clearfix"></div>
                                
                                        <form id="wiloke-form-preview-listing" class="old-style" action="<?php echo esc_url($checkOutUrl); ?>" data-uploadfilesize="<?php echo esc_attr(WilokePublic::getMaxFileSize()); ?>" method="POST" data-isuserloggedin="<?php echo esc_attr(is_user_logged_in()); ?>">
                                            <input type="hidden" name="package_id" value="<?php echo esc_attr($_REQUEST['package_id']); ?>">
                                            <input type="hidden" name="listing_id" value="<?php echo esc_attr($postID); ?>">
                                
                                            <div id="wiloke-print-msg-here" class="wiloke-print-msg-here"></div>
                                
                                            <div class="row">
                                                <?php do_action('wiloke/listgo/wiloke-submission/addlisting/before_add_listing', $postID); ?>

                                                <!-- Group Style -->
                                                <div class="add-listing-group">
                                                    <label for="listing_template" class="label"><?php echo sprintf(__('Select Template (%d)', 'listgo'), count($wiloke->aConfigs['addlisting']['templates'])); ?></label>
                                                    <div class="add-listing__style owl-carousel">
			                                            <?php
			                                            $templateStatus = 'enable';
			                                            foreach ( $wiloke->aConfigs['addlisting']['templates'] as $templateName => $aTemplate ) :
				                                            if ( $templateName !== 'templates/single-listing-traditional.php' ){
					                                            if ( isset($aPageSettings['toggle_listing_template']) && ($aPageSettings['toggle_listing_template'] == 'disable')  ){
						                                            $templateStatus = 'disable';
					                                            }
				                                            }
				                                            ?>
                                                            <div class="add-listing__style-item <?php echo esc_attr($currentTemplate===$templateName) ? 'add-listing__style-selected' : ''; ?> <?php echo esc_attr($templateStatus); ?>" data-preview-title="<?php echo esc_url(WilokeAddListing::getImgPreview($aTemplate['preview-title'])); ?>" data-preview-category="<?php echo esc_url(WilokeAddListing::getImgPreview($aTemplate['preview-category'])); ?>" data-template="<?php echo esc_attr($templateName); ?>">
                                                                <div class="add-listing__style-media">
                                                                    <div class="add-listing__style-img">
                                                                        <img data-src="<?php echo esc_url(WilokeAddListing::getImgPreview($aTemplate['preview'])); ?>" alt="" class="owl-lazy">
                                                                    </div>
                                                                    <span class="add-listing__style-status"><i class="icon_check"></i></span>
                                                                </div>
                                                                <span class="add-listing__style-label" data-activated="<?php esc_html_e('Activated', 'listgo'); ?>"><?php echo esc_html($aTemplate['name']); ?></span>
                                                            </div>
			                                            <?php endforeach; ?>
                                                    </div>
                                                    <input id="listing_style" type="hidden" name="listing_style" value="<?php echo esc_attr($currentTemplate); ?>">
                                                </div>
                                                <!-- End Group Style -->

                                                <!-- Listing Title -->
                                                <div class="col-sm-4">
                                                    <div class="form-item">
                                                        <label for="listing_title" class="label"><?php esc_html_e('Listing Title', 'listgo'); ?> <sup>*</sup></label>
                                                        <span class="input-text">
                                                            <input id="listing_title" type="text" name="listing_title" value="<?php echo esc_attr($title); ?>" required>
                                                        </span>
                                                    </div>
                                                </div>
                                                <!-- End / Listing Title -->

                                                <!-- Listing Category -->
                                                <div class="col-sm-4">
                                                    <div class="form-item">
                                                        <label for="listing_cats" class="label"><?php esc_html_e('Listing Categories', 'listgo'); ?> <sup>*</sup></label>
                                                        <span class="input-select2">
                                                            <select id="listing_cats" name="listing_cats[]" data-placeholder="<?php esc_html_e('Select Listing Categories', 'listgo'); ?>" multiple required>
                                                                <?php
                                                                if ( !empty($aListingCats) && !is_wp_error($aListingCats) ) :
	                                                                foreach ( $aListingCats as $aListingCat ) :
		                                                                $selected = '';
		                                                                if ( !empty($aCurrentCats) && in_array(absint($aListingCat->term_id), $aCurrentCats) ){
			                                                                $selected = 'selected';
		                                                                }
		                                                                ?>
                                                                        <option value="<?php echo esc_attr($aListingCat->term_id) ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($aListingCat->name); ?></option>
		                                                                <?php
	                                                                endforeach;
                                                                endif;
                                                                ?>
                                                            </select>
                                                        </span>
                                                    </div>
                                                </div>
                                                <!-- END / Listing Category -->

                                                <!-- Listing Location -->
                                                <div class="col-sm-4">
                                                    <div class="form-item">
                                                        <label for="listing_location" class="label"><?php esc_html_e('Listing Location', 'listgo'); ?> <sup>*</sup></label>
                                                        <span class="input-select2">
                                                            <select id="listing_location" name="listing_location add_listing_location_by_default" data-placeholder="<?php esc_html_e('Select Your listing location', 'listgo'); ?>" required>
                                                                <?php
                                                                if ( !empty($aListingLocations) && !is_wp_error($aListingLocations) ) :
	                                                                foreach ( $aListingLocations as $oListingLocation ) :
		                                                                $selected = '';
		                                                                if ( !empty($aCurrentLocation) && in_array(absint($oListingLocation->term_id), $aCurrentLocation) ){
			                                                                $selected = 'selected';
		                                                                }
		                                                                ?>
                                                                        <option value="<?php echo esc_attr($oListingLocation->term_id) ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($oListingLocation->name); ?></option>
		                                                                <?php
	                                                                endforeach;
                                                                endif;
                                                                ?>
                                                            </select>
                                                        </span>
                                                    </div>
                                                </div>
                                                <!-- END / Listing Location -->

                                                <!-- Listing Map -->
                                                <div class="col-sm-12">
                                                    <div class="form-item">
                                                        <label for="wiloke-latlong" class="label"><?php esc_html_e('Listing Google Address', 'listgo'); ?></label>
                                                        <div class="wiloke-latlongwrapper">
                                                            <input id="wiloke-location" type="text" class="text" placeholder="<?php esc_html_e('Listing Address, Eg:  Manchester, United Kingdom', 'listgo') ?>" name="listing_address" value="<?php echo isset($aGeneralSettings['map']) ? esc_html($aGeneralSettings['map']['location']) : ''; ?>" required>
                                                            <input id="wiloke-latlong" type="text" name="listing_latlng" value="<?php echo isset($aGeneralSettings['map']) ? esc_html($aGeneralSettings['map']['latlong']) : ''; ?>" placeholder="<?php esc_html_e('Latitude and Longitude, Eg: 53.480759,-2.242631', 'listgo') ?>" required>
                                                            <input id="wiloke-place-information" type="hidden" name="listing_place_information" required>
                                                            <div id="wiloke-map" class="wiloke-map"></div>
                                                        </div>
                                                    </div>
		                                            <?php
		                                            if ( !isset($wiloke->aThemeOptions['general_map_api']) || empty($wiloke->aThemeOptions['general_map_api']) ) {
			                                            WilokeAlert::message(esc_html__('Please go to Appearance -> Theme Options -> General and supply your Google API key', 'listgo'), false);
		                                            }
		                                            ?>
                                                </div>
                                                <!-- End / Listing Map -->

                                                <!-- Featured -->
                                                <div class="col-sm-12">
                                                    <?php if ( is_user_logged_in() ) : ?>
                                                        <div class="form-item">
                                                            <!-- Featured Image And Header Image -->
                                                            <label for="listing_cats" class="label"><?php esc_html_e('Featured Image', 'listgo'); ?></label>
                                                            <div class="add-listing-img wiloke-js-upload">
                                                                <img class="wiloke-preview" src="<?php echo esc_url($featuredImageUrl); ?>" alt="<?php esc_html_e('Featured Image', 'listgo'); ?>">
                                                                <input type="hidden" id="wiloke_feature_image" class="wiloke-insert-id" name="featured_image" value="<?php echo esc_attr($featuredImageID); ?>">
                                                            </div>
                                                        </div>
                                                     <?php else : ?>
                                                        <div class="form-item upload-file">
                                                            <div id="wiloke-show-featured-image" class="wil-addlisting-gallery single-upload">
                                                                <ul class="wil-addlisting-gallery__list"></ul>
                                                            </div>
                                                            <label class="input-upload-file">
                                                                <input type="hidden" id="wiloke_feature_image" class="wiloke-insert-id" name="featured_image" value="">
                                                                <input id="wiloke-upload-feature-image" class="wiloke-simple-upload wiloke_feature_image" name="wiloke_raw_featured_image" type="file" value="">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path></svg>
                                                                <span><?php esc_html_e('Upload Featured Image', 'listgo'); ?></span>
                                                            </label>
                                                            <span class="input-text wiloke-submission-reminder"><?php echo esc_html__('The image size should smaller or equal ', 'listgo') . WilokePublic::getMaxFileSize(); ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <!-- End / Featured -->

                                                <!-- Listing Content -->
                                                <div class="col-sm-12">
                                                    <div class="form-item">
                                                        <label for="listing_content" class="label"><?php esc_html_e('Listing Content', 'listgo'); ?> <sup>*</sup></label>
                                                        <span class="input-text">
                                                        <?php wp_editor($listingContent, 'listing_content', array(
	                                                        'tinymce' => array(
		                                                        'content_css' => WP_PLUGIN_URL . '/wiloke-listgo-functionality/public/source/css/placeholder-editor.css'
	                                                        )
                                                        )); ?>
                                                </span>
                                                    </div>
                                                </div>
                                                <!-- End / Listing Content -->

	                                            <?php do_action('wiloke/listgo/wiloke-submission/addlisting/before_listing_information', $postID, $_REQUEST['package_id'], $aPackageSettings, $aGeneralSettings, 'old-style'); ?>
                                                                
                                                <div class="col-sm-6">
                                                    <div class="form-item">
                                                        <label for="listing_phone" class="label"><?php esc_html_e('Phone', 'listgo'); ?></label>
                                                        <span class="input-text">
                                                            <input id="listing_phone" type="text" name="listing_phonenumber" value="<?php echo isset($aGeneralSettings['phone_number']) ? esc_html($aGeneralSettings['phone_number']) : ''; ?>">
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-item">
                                                        <label for="listing_website" class="label"><?php esc_html_e('Website', 'listgo'); ?></label>
                                                        <span class="input-text">
                                                            <input id="listing_website" type="text" name="listing_website" value="<?php echo isset($aGeneralSettings['website']) ? esc_url($aGeneralSettings['website']) : ''; ?>">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                
                                            <h4 class="profile-title"><?php esc_html_e('Price Settings', 'listgo'); ?></h4>
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-item">
                                                        <label for="price_range" class="label"><?php esc_html_e('Price Segment', 'listgo'); ?></label>
                                                        <span>
                                                            <select id="price_range" name="listing_price[price_segment]">
                                                                <?php
                                                                foreach ( $aPriceSegment as $segment => $definition ) :
	                                                                $definition = isset($wiloke->aThemeOptions['header_search_'.$segment.'_cost_label']) ? $wiloke->aThemeOptions['header_search_'.$segment.'_cost_label'] : $definition;

	                                                            ?>
                                                                ?>
                                                                    <option value="<?php echo esc_attr($segment); ?>" <?php selected($aPrice['price_segment'], $segment); ?>><?php echo esc_html($definition); ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-item">
                                                        <label for="price_from" class="label"><?php esc_html_e('Minimum Price', 'listgo'); ?></label>
                                                        <span class="input-text">
                                                            <input id="price_from" type="text" name="listing_price[price_from]" value="<?php echo esc_attr($aPrice['price_from']); ?>">
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-item">
                                                        <label for="price_to" class="label"><?php esc_html_e('Maximum Price', 'listgo'); ?></label>
                                                        <span class="input-text">
                                                            <input id="price_to" type="text" name="listing_price[price_to]" value="<?php echo esc_attr($aPrice['price_to']); ?>">
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                
                                            <!-- Business Hours -->
                                            <h4 class="profile-title"><?php esc_html_e('Business Hours', 'listgo'); ?></h4>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-item">
                                                        <label for="wiloke-toggle-business-hours" class="label"><?php esc_html_e('Toggle Business Hours', 'listgo'); ?></label>
                                                        <span>
                                                            <select id="wiloke-toggle-business-hours" name="toggle_business_hours">
                                                                <?php foreach ( $aToggleBusinessHours as $option => $name ) : ?>
                                                                    <option value="<?php echo esc_attr($option); ?>" <?php selected($toggleBusinessHoursStatus, $option); ?>><?php echo esc_html($name); ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div id="wiloke-tbl-business-hours" class="col-sm-12">
                                                    <table class="table table-bordered profile-hour">
                                                        <thead>
                                                        <tr>
                                                            <th><?php esc_html_e('Day', 'listgo'); ?></th>
                                                            <th><?php esc_html_e('Start time', 'listgo'); ?></th>
                                                            <th><?php esc_html_e('End time', 'listgo'); ?></th>
                                                            <th><?php esc_html_e('Closed', 'listgo'); ?></th>
                                                        </tr>
                                                        </thead>
                            
                                                        <?php foreach ( $wiloke->aConfigs['frontend']['listing']['business_hours']['days'] as $key => $day ) :
                                                               $aValues = isset($aBusinessHours[$key]) ? $aBusinessHours[$key] : $wiloke->aConfigs['frontend']['listing']['business_hours']['default'];
                                                        ?>
                                                            <tr>
                                                                <td class="business-day" data-title="<?php esc_html_e('Day', 'listgo'); ?>"><?php echo esc_html($day); ?></td>
                                                                <td class="business-start" data-title="<?php echo esc_html('Start time', 'listgo'); ?>">
                                                                    <span class="listgo-bsh-item"><input type="number" name="listgo_bh[<?php echo esc_attr($key) ?>][start_hour]" max="<?php echo esc_attr(apply_filters('wiloke/listgo/wiloke-submission/addlisting/time_format', 12)); ?>" min="0" value="<?php echo esc_attr($aValues['start_hour']); ?>"></span>
                                                                    <span class="listgo-bsh-item"><input type="number" name="listgo_bh[<?php echo esc_attr($key) ?>][start_minutes]" max="60" min="0" value="<?php echo esc_attr($aValues['start_minutes']); ?>"></span>
                                                                    <span class="listgo-bsh-item listgo-bsh-item-last"><select name="listgo_bh[<?php echo esc_attr($key) ?>][start_format]" class="listgo-time-format">
                                                                        <option value="AM" <?php selected($aValues['start_format'], 'AM'); ?>><?php esc_html_e('AM', 'listgo'); ?></option>
                                                                        <option value="PM" <?php selected($aValues['start_format'], 'PM'); ?>><?php esc_html_e('PM', 'listgo'); ?></option>
                                                                    </select></span>
                                                                </td>
                                                                <td class="business-end" data-title="<?php echo esc_html('End time', 'listgo'); ?>">
                                                                    <span class="listgo-bsh-item"><input type="number" name="listgo_bh[<?php echo esc_attr($key) ?>][close_hour]" max="<?php echo esc_attr(apply_filters('wiloke/listgo/wiloke-submission/addlisting/time_format', 12)); ?>" min="0" value="<?php echo esc_attr($aValues['close_hour']); ?>"></span>
                                                                    <span class="listgo-bsh-item"><input type="number" name="listgo_bh[<?php echo esc_attr($key) ?>][close_minutes]" max="60" min="0" value="<?php echo esc_attr($aValues['close_minutes']); ?>"></span>
                                                                    <span class="listgo-bsh-item listgo-bsh-item-last"><span class="listgo-bsh-item listgo-bsh-item-last"><select name="listgo_bh[<?php echo esc_attr($key) ?>][close_format]" class="listgo-time-format">
                                                                        <option value="AM" <?php selected($aValues['close_format'], 'AM'); ?>><?php esc_html_e('AM', 'listgo'); ?></option>
                                                                        <option value="PM" <?php selected($aValues['close_format'], 'PM'); ?>><?php esc_html_e('PM', 'listgo'); ?></option>
                                                                    </select></span>
                                                                </td>
                                                                <td class="business-close" data-title="<?php echo esc_html('Close', 'listgo'); ?>">
                                                                    <label for="bh-closed-<?php echo esc_attr($key); ?>" class="input-checkbox">
                                                                        <input id="bh-closed-<?php echo esc_attr($key); ?>" type="checkbox" name="listgo_bh[<?php echo esc_attr($key) ?>][closed]" value="1" <?php echo isset($aValues['closed']) && $aValues['closed'] === '1' ? 'checked' : ''; ?> value="1">
                                                                        <span></span>
                                                                    </label>
                                                                </td>
                                                            </tr>
                                                         <?php endforeach; ?>
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- END / Business Hours -->
                                
                                            <h4 class="profile-title"><?php esc_html_e('Social Media', 'listgo'); ?></h4>
                                            <!-- Social Media -->
                                            <div class="row">
                                                <?php foreach ($aSocials as $social) : $name = $social === 'google-plus' ? esc_html__('Google+', 'listgo') : ucfirst($social); ?>
                                                <div class="col-sm-6">
                                                    <div class="form-item">
                                                        <label for="<?php echo esc_attr($social); ?>" class="label"><?php echo esc_html($name); ?></label>
                                                        <span class="input-text input-icon-left">
                                                            <input id="<?php echo esc_attr($social); ?>" name="listing[social][<?php echo esc_attr($social); ?>]" type="text" value="<?php echo isset($aSocialMedia[$social]) ? esc_url($aSocialMedia[$social]) : ''; ?>">
                                                            <i class="input-icon fa fa-<?php echo esc_attr($social); ?>"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <!-- END / Social Media -->

	                                        <?php do_action('wiloke/listgo/wiloke-submission/addlisting/after_listing_information', $postID, $_REQUEST['package_id']); ?>
                                            
                                            <!-- Gallery -->
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-item">
                                                        <label for="listing_tags" class="label"><?php esc_html_e('Keywords', 'listgo'); ?></label>
                                                        <span class="input-text">
                                                            <textarea name="listing_tags" id="listing_tags" rows="4" cols="10" placeholder="<?php esc_html_e('Each keyword is sereparated by a comma. Eg: wordpress,drupal', 'listgo'); ?>"><?php echo esc_textarea(implode(',', $aCurrentTags)); ?></textarea>
                                                        </span>
                                                    </div>
                                                </div>
	                                            <?php
	                                            if ( $toggleUploadGallery === 'enable' ) :
	                                            $aGalleries = Wiloke::getPostMetaCaching($postID, 'gallery_settings');
	                                            ?>
                                                <div class="col-sm-12">
                                                    <?php if ( is_user_logged_in() ) : ?>
                                                    <div class="form-item">
                                                        <label class="label"><?php esc_html_e('Add Gallery', 'listgo'); ?></label>
                                                        <div class="wil-addlisting-gallery">
                                                            <ul id="wiloke-preview-gallery" class="wil-addlisting-gallery__list">
                                                            <?php if ( isset($aGalleries['gallery']) && !empty($aGalleries['gallery']) ) : foreach ( $aGalleries['gallery'] as $id => $url  ) : if ( !empty($id) ) :?>
                                                                <li data-id="<?php echo esc_attr($id); ?>" class="bg-scroll gallery-item" style="background-image: url(<?php echo esc_url(wp_get_attachment_image_url($id, 'thumbnail')) ?>);">
                                                                    <span class="wil-addlisting-gallery__list-remove"><?php esc_html_e('Remove', 'listgo'); ?></span>
                                                                </li>
                                                                <?php endif; endforeach; endif; ?>
                                                                <li id="wiloke-listgo-add-gallery" class="wil-addlisting-gallery__placeholder" title="<?php esc_html_e('Upload Gallery', 'listgo'); ?>">
                                                                    <button data-multiple="true" class="wiloke-js-upload"><i class="fa fa-camera"></i></button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <input type="hidden" id="listing_gallery" class="wiloke-insert-id" name="listing_gallery" value="">
                                                    </div>
                                                    <?php else: ?>
                                                    <div class="form-item upload-file">
                                                        <div class="wil-addlisting-gallery">
                                                            <ul id="wiloke-show-gallery" class="wil-addlisting-gallery__list"></ul>
                                                        </div>
                                                        <label class="input-upload-file">
                                                            <input id="wiloke_submission_listing_gallery" class="listing_gallery" name="listing_gallery" type="hidden" value="">
                                                            <input id="wiloke-upload-gallery-image" class="wiloke-simple-upload wiloke_gallery_image" data-ismultiple="true" name="wiloke_raw_gallery_image[]" type="file" multiple value="">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path></svg>
                                                            <span><?php esc_html_e('Upload Gallery Image', 'listgo'); ?></span>
                                                        </label>
                                                        <span class="input-text wiloke-submission-reminder">
                                                            <?php echo esc_html__('The image size should smaller or equal ', 'listgo') . WilokePublic::getMaxFileSize(); ?>
                                                        </span>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <!-- END / Gallery -->
                                
                                            <?php include get_template_directory() . '/wiloke-submission/signup-signin-in-addlisting.php'; ?>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="profile-actions">
                                                        <div id="wiloke-print-msg-here" class="wiloke-print-msg-here"></div>
                                                        <div class="profile-actions__right">
	                                                        <?php if ( WilokeAddListing::isEditingPublishedListing($postID) ) : ?>
                                                                <button data-edittype="<?php echo esc_attr(FrontendListingManagement::publishedListingEditable()) ?>" id="wiloke-listgo-update-listing" class="listgo-btn btn-primary" href="<?php echo esc_url(get_permalink($postID)); ?>"><?php esc_html_e('Update', 'listgo'); ?></button>
	                                                        <?php else: ?>
                                                                <button type="submit" id="wiloke-listgo-preview-listing" class="listgo-btn btn-primary" href="<?php echo esc_url($checkOutUrl); ?>"><?php esc_html_e('Preview', 'listgo'); ?></button>
	                                                        <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
	            <?php
                endif;
            endif;
        }
    }
    wp_reset_postdata();
get_footer();