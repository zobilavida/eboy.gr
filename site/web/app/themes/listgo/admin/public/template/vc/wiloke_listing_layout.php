<?php
use WilokeListGoFunctionality\Frontend\FrontendListingManagement as WilokeFrontendListingManagement;
use WilokeListGoFunctionality\Model\GeoPosition;

function wiloke_shortcode_listing_layout($atts){
    global $wiloke;
	$atts = shortcode_atts(
		array(
			'layout'                            => 'listing--list',
			'get_posts_from'                    => '',
			'listing_cat'                       => '',
			'listing_location'                  => '',
			'include'                           => '',
			'show_terms'                        => 'both',
			'filter_type'                       => 'navigation',
			'btn_name'                          => esc_html__('Load More', 'listgo'),
			'viewmore_page_link'                => '#',
			'btn_position'                      => 'text-center',
			'order_by'                          => 'post_date',
			'display_style'                     => 'all',
			'btn_style'							=> 'listgo-btn--default',
			'btn_size'                          => 'listgo-btn--small',
			'posts_per_page'                    => 10,
			'image_size'                        => 'medium',
			'toggle_render_favorite'            => 'enable',
			'favorite_description'              => esc_html__('Save', 'listgo'),
			'toggle_render_view_detail'         => 'enable',
			'view_detail_text'                  => '',
			'toggle_render_find_direction'      => 'enable',
			'find_direction_text'               => '',
            'toggle_render_link_to_map_page'    => 'enable',
            'link_to_map_page_text'             => '',
            'toggle_render_post_excerpt'        => 'enable',
            'toggle_render_address'             => 'enable',
            'toggle_render_author'              => 'enable',
            'toggle_render_rating'              => 'enable',
			'limit_character'                   => 100,
			'filter_result_description'         => '*open_result* %found_listing% %result_text=Result|Results% *end_result* in %total_listing% Destinations',
			'block_id'                          => '',
			'css'                               => '',
			'map_page'                          => '',
			'term_ids'                          => '',
			'post_authors'                      => '',
			'created_at'                        => '',
			'extract_class'                     => '',
			'location_latitude_longitude'       => '',
			's_within_radius'                   => 5,
			's_unit'                            => 'km',
			'sidebar'							=> 'no'
		),
		$atts
	);

	$wrapperClass = 'wiloke-listing-layout ' . ' ' . $atts['extract_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');

	if ( ($atts['get_posts_from'] !== 'listing_cat') && ($atts['get_posts_from'] !== 'listing_location') ){
		$atts['filter_type'] = 'none';
    }

	$atts['listing_cat'] = !empty($atts['listing_cat']) ? explode(',', $atts['listing_cat']) : '';
	$atts['listing_location'] = !empty($atts['listing_location']) ? explode(',', $atts['listing_location']) : '';

	if ( empty($atts['posts_per_page']) && $atts['posts_per_page'] !='no' ){
        $atts['posts_per_page'] = get_option('posts_per_page');
    }

	$aArgs = array(
		'post_type'       => 'listing',
		'post_status'     => 'publish',
		'orderby'         => $atts['order_by'],
		'posts_per_page'  => $atts['posts_per_page']
	);

	$atts['wrapper_class'] = 'listings ' . str_replace('listing', 'listings', $atts['layout']);

	$atts['item_class'] = 'listing ' . $atts['layout'];

	if ( strpos($atts['layout'], 'grid') !== false ) {
		if( !empty($atts['sidebar']) && $atts['sidebar'] != 'no' ) {
			$atts['before_item_class'] = 'col-sm-6 col-lg-6';
		    $cols = '2';
		} else {
		    $atts['before_item_class'] = 'col-sm-6 col-lg-4';
		    $cols = '3';
	    }
	}else{
		$cols = '';
		if ( $atts['layout'] == 'circle-thumbnail' || $atts['layout'] == 'creative-rectangle' ){
			$atts['before_item_class'] = 'col-sm-12 col-md-12';
        }else{
			$atts['before_item_class'] = 'col-xs-12';
        }
	}

	$blockID = !empty($atts['block_id']) ? $atts['block_id'] : uniqid('listing_layout_');

	unset($atts['post_status']);
	unset($atts['post_type']);

	$wrapperClass = trim($wrapperClass);
	ob_start();
	if ( empty($atts['map_page']) ){
        $atts['map_page'] = $wiloke->aThemeOptions['header_search_map_page'];
    }
    $mapPage = get_permalink($atts['map_page']);

	if ( strpos($atts['image_size'], ',') ){
		$atts['image_size'] = array_map('trim', explode(',', $atts['image_size']));
	}
	$aTaxesQuery = null;

	$atts['location_latitude_longitude'] = isset($_REQUEST['location_latitude_longitude'])? $_REQUEST['location_latitude_longitude'] : $atts['location_latitude_longitude'];
	$atts['s_within_radius'] = isset($_REQUEST['s_within_radius'])? $_REQUEST['s_within_radius'] : $atts['s_within_radius'];
	$atts['s_unit'] = isset($_REQUEST['s_unit']) ? $_REQUEST['s_unit'] : $atts['s_unit'];

	if ( !empty($_REQUEST['location_term_id']) ){
		$atts['location_latitude_longitude']='';
    }

	if ( !empty($atts['location_latitude_longitude']) ){
		$aLatLng = explode(',', $atts['location_latitude_longitude']);
        $aListingInRadius = GeoPosition::searchLocationWithin(trim($aLatLng[0]), trim($aLatLng[1]), $atts['s_within_radius'], $atts['s_unit']);
        if ( empty($aListingInRadius) ){
            $aTaxesQuery = -1;
        }else{
        	$aArgs['post__in'] = $aListingInRadius['IDs'];
        }
		$ignoreParseLocation = true;
	}

	if ( !isset($ignoreParseLocation)  ){
		$aTaxesQuery = WilokePublic::getTaxesInSearchQuery();
		if ( $aTaxesQuery != -1 ){
			if ( isset($_REQUEST['s_search']) && (!isset($_REQUEST['s_listing_cat']) || empty($_REQUEST['s_listing_cat']) )){
				$aArgs['s'] = $_REQUEST['s_search'];
			}

			if ( !empty($aTaxesQuery) ){
				$aArgs['tax_query'] = $aTaxesQuery;
			}else{
				if ( ( $atts['get_posts_from'] === 'listing_cat' ) || ( $atts['get_posts_from'] === 'listing_location' ) )  {
					$aArgs['tax_query'] = array(
						array(
							'taxonomy' => $atts['get_posts_from'],
							'field'    => 'term_id',
							'terms'    => array_map( 'absint', $atts[ $atts['get_posts_from'] ] )
						)
					);
				} elseif ( $atts['get_posts_from'] === 'custom' && ! empty( $atts['include'] ) ) {
					$aArgs['post__in'] = explode( ',', $atts['include'] );
				}else if ( $atts['get_posts_from'] === 'post_author' ){
					$aArgs['author__in'] = explode( ',', $atts['post_authors'] );
				}
			}

			if ( is_author() ){
				$pinnedListingID = WilokeFrontendListingManagement::getPinnedToTop();
				if ( !empty($pinnedListingID) ){
					$aArgs['post__not_in'] = $pinnedListingID;
				}
			}
		}
    }

    ?>
    <div class="listgo-listlayout-on-page-template">
        <div id="<?php echo esc_attr($blockID); ?>" class="<?php echo esc_attr($wrapperClass); ?>" data-atts="<?php echo esc_attr(json_encode($atts)); ?>" data-createdat="<?php echo esc_attr($atts['created_at']); ?>">
            
            <?php 
            if ( $aTaxesQuery !== -1 ){
            	$query = new WP_Query($aArgs);
            	wiloke_shortcode_listing_layout_filter($atts, $query);
            }
            ?>

            <div class="wiloke-listgo-listlayout <?php echo esc_attr($atts['wrapper_class']); ?>">

                <div class="listgo-wrapper-grid-items row row-clear-lines" data-col-lg="<?php echo esc_attr($cols); ?>" data-total="<?php echo esc_attr($query->found_posts); ?>">
	                <?php
                    if ( isset($pinnedListingID) && !empty($pinnedListingID) ){
	                    WilokePublic::listingQuery( $atts, $mapPage, true, $pinnedListingID );
                    }

                    if ( isset($query) && $query->have_posts() ) :
	                    while ( $query->have_posts() ) :
		                    $query->the_post();
		                    WilokePublic::listingQuery( $atts, $mapPage );
	                    endwhile;
                    else:
                        ?>
                        <div class="col-xs-12">
                        <?php
	                    if ( is_author() ) {
		                    WilokeAlert::render_alert( esc_html__( 'Whoops! We found no articles of this author!', 'listgo' ), 'info', false, false );
	                    } else {
		                    WilokeAlert::render_alert( esc_html__( 'Sorry --- We couldn\'t find what you are looking for. You should try: Searching a different area, A more general search (Paris, Shopping, etc), Checking your spelling.', 'listgo' ), 'danger', false, false );
	                    }
                        ?>
                        </div>
	                    <?php
                    endif;
                    wp_reset_postdata();
                    ?>
                </div>

                <?php 
                if ( isset($query) && ($atts['display_style'] === 'pagination') ) : 
                ?>
                    <div id="wiloke-listgo-listlayout-pagination" class="nav-links text-center" data-total="<?php echo esc_attr($query->found_posts); ?>" data-postsperpage="<?php echo esc_attr($atts['posts_per_page']); ?>"></div>
                <?php elseif ( $atts['display_style'] === 'loadmore' ) : ?>
                    <?php if ( ( $atts['posts_per_page'] < $query->found_posts ) ) : ?>
                    <div class="landmarks__all <?php echo esc_attr($atts['btn_position']); ?>">
                        <a href="#" class="listgo-btn btn-primary <?php echo esc_attr($atts['btn_size']); ?> <?php echo esc_attr($atts['btn_style']); ?> listgo-loadmore" data-total="<?php echo esc_attr($query->found_posts); ?>"><?php echo esc_html($atts['btn_name']); ?> <i class="fa fa-arrow-circle-o-right"></i></a>
                    </div>
                    <?php endif; ?>
                <?php elseif($atts['display_style'] === 'link_to_page') : ?>
                <div class="landmarks__all <?php echo esc_attr($atts['btn_position']); ?>">
                    <a href="<?php echo esc_url(get_permalink($atts['viewmore_page_link'])); ?>" class="<?php echo esc_attr($atts['btn_size']); ?>  <?php echo esc_attr($atts['btn_style']); ?> listgo-btn btn-primary"><?php echo esc_html($atts['btn_name']); ?> <i class="fa fa-arrow-circle-o-right"></i></a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

function wiloke_shortcode_listing_layout_filter($atts, $query){
	if ( ($atts['filter_type'] === 'none') || empty($query->found_posts) ){
        return false;
	}

	if ( $atts['get_posts_from'] === 'listing_cat' ){
		$oNavFilters = Wiloke::getTermCaching('listing_cat', $atts['listing_cat']);
	}else{
		$oNavFilters = Wiloke::getTermCaching('listing_location', $atts['listing_location']);
	}

	if ( $atts['filter_type'] === 'navigation' ) :
    ?>
        <div class="nav-filter" data-filterby="<?php echo esc_attr($atts['get_posts_from']); ?>">
            <a class="active" data-filter="all" data-total="<?php echo esc_attr($query->found_posts); ?>" href="#"><?php esc_html_e('All', 'listgo'); ?></a>
            <?php foreach ( $oNavFilters as $oNavFilter ) : ?>
            <a href="#" data-filter="<?php echo esc_attr($oNavFilter->term_id); ?>" data-total="<?php echo esc_attr($oNavFilter->count); ?>"><?php echo esc_html($oNavFilter->name); ?></a>
            <?php endforeach; ?>
        </div>
    <?php else:
        preg_match('/(?<=%result_text=)([^%]+)/', $atts['filter_result_description'], $aMatched);
        $singularRes = $pluralRes = '';
        if ( isset($aMatched[0]) ){
            if ( strpos($aMatched[0], '|') !== false ){
                $aParseMatched = explode('|', $aMatched[0]);
	            $singularRes = $aParseMatched[0];
	            $pluralRes = $aParseMatched[1];
            }else{
	            $singularRes = $pluralRes = $aMatched[0];
            }
        }

        $resultStructure = preg_replace_callback('/%result_text=([^%]+)%/', function($aMatched){
            return 'RESULT_TEXT_HERE';
        }, $atts['filter_result_description']);
    ?>
        <div class="listing__result">
            <div class="listing__result-filter">
                <label for="listgo-dropdown-filter"><?php esc_html_e('Find By ', 'listgo'); ?></label>
                <select id="listgo-dropdown-filter" class="listgo-dropdown-filter" data-filterby="<?php echo esc_attr($atts['get_posts_from']); ?>">
                    <option class="active" value="all" data-filter="all" data-total="<?php echo esc_attr($query->found_posts); ?>"><?php esc_html_e('All', 'listgo'); ?></option>
                    <?php foreach ( $oNavFilters as $oNavFilter ) : ?>
                    <option data-filter="<?php echo esc_attr($oNavFilter->term_id); ?>" value="<?php echo esc_attr($oNavFilter->term_id); ?>" data-total="<?php echo esc_attr($oNavFilter->count); ?>"><?php echo esc_html($oNavFilter->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="listing__result-right hidden" data-singularres="<?php echo esc_attr($singularRes); ?>"  data-pluralres="<?php echo esc_attr($pluralRes); ?>" data-result="<?php echo esc_attr($resultStructure); ?>">
            </div>
        </div>
    <?php endif;
}