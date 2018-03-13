<?php
function wiloke_shortcode_half_map($aAtts){
	$aAtts = shortcode_atts(
		array(
			'layout'                        => 'listing--list',
			'show_terms'                    => 'both',
			'link_to_map_page_additional_class' => 'listgo_panto_marker',
			'link_to_map_page_text'         => esc_html__('Pan to marker', 'listgo'),
			'listing_location'              => '',
			'order_by'                      => 'post_date',
			'posts_per_page'                => 4,
			'listing_cat'                   => '',
			's'                             => '',
			'disable_draggable_when_init'   => 'disable',
			'toggle_render_address'         => 'enable',
			'popup_showing_tax'             => 'listing_location',
			'center'                        => null,
			'before_item_class'             => 'col-sm-6',
			'item_class' 		            => 'listing listing--grid',
			'max_cluster_radius'            => '',
			'map_theme'                     => '',
            'max_zoom'                      => '',
            'min_zoom'                      => '',
            'center_zoom'                   => '',
			'extract_class'                 => '',
			'css'                           => ''
		),
		$aAtts
	);

	wp_enqueue_script('listgo-halfmap');

	$aAtts['maxClusterRadius']  = $aAtts['max_cluster_radius'];
	$aAtts['mapTheme']          = $aAtts['map_theme'];
	$aAtts['maxZoom']           = $aAtts['max_zoom'];
	$aAtts['minZoom']           = $aAtts['min_zoom'];
	$aAtts['centerZoom']        = $aAtts['center_zoom'];

	unset($aAtts['max_cluster_radius']);
	unset($aAtts['map_theme']);
	unset($aAtts['min_zoom']);
	unset($aAtts['center_zoom']);

	if ( empty($aAtts['center']) ){
	    unset($aAtts['center']);
    }

	$aAtts['s_current_cat'] = $currentCat = isset($_REQUEST['s_listing_cat']) ? $_REQUEST['s_listing_cat'] : '';
	$isFocusSearch = false;
	if ( isset($_REQUEST['s_search']) && !empty($_REQUEST['s_search']) ){
		$search = $aAtts['s'] = $_REQUEST['s_search'];
		if ( empty($currentCat) ){
			$isFocusSearch = true;
		}
	}else{
		$search = $aAtts['s'] = '';
	}

	$aAtts['s_current_location'] = $currentLocation = isset($_REQUEST['s_listing_location']) && $_REQUEST['s_listing_location'] !== 'all' ? explode(',', $_REQUEST['s_listing_location']) : '';
	$aAtts['s_current_tag'] = $currentLocation = isset($_REQUEST['s_listing_tag']) ? $_REQUEST['s_listing_tag'] : '';
	unset($aAtts['s_current_cat']);
	unset($aAtts['s']);
	unset($aAtts['s_current_location']);
	unset($aAtts['s_current_tag']);

	global $wiloke;

	$wrapperClass = $aAtts['extract_class'] . ' ' . vc_shortcode_custom_css_class($aAtts['css'], ' ');
	$uid = uniqid();
	ob_start(); ?>

	<div id="<?php echo esc_attr($uid) ?>" class="listgo-map-container listgo-listlayout-on-page-template <?php echo esc_attr($wrapperClass); ?>">
		<?php
		if ( empty($wiloke->aThemeOptions['general_mapbox_api']) || empty($wiloke->aThemeOptions['general_map_theme']) ){
			if ( current_user_can('edit_theme_options') ){
				WilokeAlert::render_alert( __('The <strong>MapBox Token</strong> and <strong>MapBox theme</strong> are required. You can find these settings by clicking on <strong>Theme Options</strong> at the top bar -> <strong>General</strong> or <strong>Appearance</strong> -> <strong>Theme Options</strong> -> <strong>General</strong>. ', 'listgo'), 'warning' );
			}
		}
		?>
		<div id="listgo-half-map-wrap" class="listgo-half-map-wrap" data-id="<?php echo esc_attr($uid) ?>" data-configs="<?php echo esc_attr(json_encode($aAtts)); ?>">
			<span class="header-page__breadcrumb-filter"><i class="fa fa-filter"></i> Filter</span>
            <div id="wiloke-half-results" class="wiloke-half-results">
                <div class="wiloke-listing-layout" data-atts="<?php echo esc_attr(json_encode($aAtts)); ?>">
                    <div class="from-wide-listing">
                        <div class="from-wide-listing__header">
                            <span class="from-wide-listing__header-title"><?php echo esc_html__('Filter', 'listgo') ?></span>
                            <span class="from-wide-listing__header-close"><span>×</span> <?php echo esc_html__('Close', 'listgo') ?></span>
                        </div>
                        <?php WilokePublic::searchForm(null, true, array(), 'listing-template'); ?>
                        <div id="listgo-mobile-search-only" class="from-wide-listing__footer">
                            <span><?php echo esc_html__('Apply', 'listgo') ?></span>
                        </div>
                    </div>

                    <div class="listgo-wrapper-grid-items row row-clear-lines" data-col-lg="3">
                        <?php
                        $aArgs = array(
                            'posts_per_page' => $aAtts['posts_per_page'],
                            'post_type'      => 'listing',
                            'post_status'    => 'publish'
                        );

                        $aArgs['meta_query'] = array(
                            'relation' => 'AND',
                            array(
                                'meta_key' => 'listing_settings',
                                'value'    => 'latlong";s:0:',
                                'compare'  => 'NOT LIKE'
                            ),
                            array(
                                'meta_key' => 'wiloke_submission_do_not_show_on_map',
                                'value'    => 'disable',
                                'compare'  => '!='
                            )
                        );

                        if ( $isFocusSearch && !empty($aAtts['s']) ){
                            $aArgs['s'] = $aAtts['s'];
                        }

                        $query = new WP_Query($aArgs);

                        if ( $query->have_posts() ){
                            while ($query->have_posts()){
                                $query->the_post();
                                WilokePublic::listingQuery($aAtts, null, true);
                            }
                            wp_reset_postdata();
                        }
                        ?>
                    </div>

                    <div id="wiloke-listgo-listlayout-pagination" class="nav-links text-center" data-total="<?php echo esc_attr($query->found_posts); ?>" data-postsperpage="<?php echo esc_attr($aAtts['posts_per_page']); ?>"></div>

                </div>
            </div>
			<div id="listgo-half-map" data-disabledraggableoninit="" class="listgo-half-map listgo-map">
				<span class="listgo-half-map__close"></span>
			</div>
		</div>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
