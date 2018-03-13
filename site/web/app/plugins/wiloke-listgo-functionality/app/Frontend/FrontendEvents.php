<?php
namespace WilokeListGoFunctionality\Frontend;

use WilokeListGoFunctionality\AlterTable\AlterTablePaymentEventRelationship as AlterTablePaymentEventRelationship;
use WilokeListGoFunctionality\Frontend\FrontendListingManagement as WilokeListingManage;
use WilokeListGoFunctionality\Submit\User as WilokeUser;
use WilokeListGoFunctionality\CustomerPlan\CustomerPlan as WilokeCustomerPlan;
use WilokeListGoFunctionality\Payment\EventPayment as WilokeEventPayment;
use WilokeListGoFunctionality\Payment\Payment as WilokePayment;
use WilokeListGoFunctionality\Frontend\ConvertTime as WilokeConvertTime;
use WilokeListGoFunctionality\Model\GeoPosition as WilokeGeoPosition;
use WilokeListGoFunctionality\AlterTable\AlterTableGeoPosition;

class FrontendEvents{
    protected $postType = 'event';
    protected $eventListingRelationshipKey = 'wiloke_listing_event_relationship';
    protected $eventSettingsKey  = 'event_settings';
    protected $eventPublishedAtTimeZoneKey  = 'event_published_at_timezone';
    public static $hasEvent = null;
    public $isAllowAddingEvent = null;
    public $aEventStatus = array();
	public static $showEventOnCarouselKey = 'toggle_show_events_on_event_carousel';
	public static $showEventOnListKey = 'toggle_show_events_on_event_listing';

	public function __construct() {
		add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'), 10);
		add_action('wp_footer', array($this, 'insertAddEventTemplateToFooter'), 10);
		add_action('wiloke/wiloke-listgo-functionality/triggerCreateNewEvent', array($this, 'createNewEvent'), 10, 3);
		add_action('wiloke/listgo/templates/single-listing/bottom_nav_tab', array($this, 'addEventTab'), 10);
		add_action('wiloke/listgo/templates/single-listing/before_nav_tab_close', array($this, 'addEventToSecondaryTab'), 10);
		add_action('wiloke/listgo/templates/single-listing/before_close_content', array($this, 'printEventContent'), 10);
		add_action('wp_ajax_wiloke_listgo_event_update_event', array($this, 'updateEvent'));
		add_action('wp_ajax_wiloke_get_event_data', array($this, 'fetchEventData'));
		add_action('wp_ajax_wiloke_fetch_events', array($this, 'fetchEvents'));
		add_action('wp_ajax_nopriv_wiloke_fetch_events', array($this, 'fetchEvents'));
		add_action('wiloke/listgo/single/after_title', array($this, 'renderEventStautusOnSingle'));
		add_action('wiloke/listgo/admin/public/template/vc/listing-layout/after_title', array($this, 'renderEventStautusOnSingle'));
	}

	public function renderEventStautusOnSingle($post){
		$aEventIDs = \Wiloke::getPostMetaCaching($post->ID, $this->eventListingRelationshipKey);
		if ( $this->maybeAllEvenInTrash($aEventIDs) ){
		    return false;
        }
		$aEventStatus = $this->theFirstEventStatus($aEventIDs);
        ?>
        <?php if ( ($aEventStatus['status'] == 'upcomming') || ($aEventStatus['status'] == 'ongoing') ) : ?>
        <span class="listing__icon-notif <?php echo esc_attr($aEventStatus['status']); ?>" data-tooltip="<?php echo esc_html($aEventStatus['longname']); ?>"><i class="fa fa-bullhorn"></i></span>
        <?php endif; ?>
        <?php
    }

	public function theFirstEventStatus($aEventIDs){
	    if ( empty($aEventIDs) ){
	        return array(
	            'status'     => 'create',
                'name'       => __('Create', 'wiloke'),
                'longname'   => __('Create Event', 'wiloke'),
            );
        }

        $aEventSettings = \Wiloke::getPostMetaCaching(array_shift($aEventIDs), 'event_settings');
        $aEventStatus = self::checkEventStatus($aEventSettings);
        return $aEventStatus;
    }
	
	public function searchListingInCatAndLocation($locationID=null, $catID=null, $limit, $aListingNotIn=array(), $aListingsIn=array(), $aEventIDsIn=array()){
		global $wpdb;
		$termRelationshipTbl = $wpdb->prefix . 'term_relationships';
		$postMetaTbl = $wpdb->prefix . 'postmeta';

        $sql = "SELECT SQL_CALC_FOUND_ROWS $postMetaTbl.post_id FROM $postMetaTbl LEFT JOIN $termRelationshipTbl ON ($postMetaTbl.meta_value = $termRelationshipTbl.object_id) WHERE meta_key='wiloke_event_belongs_to'";
        $concat = ' AND ';
		$limit = empty($limit) ? 1000 : abs($limit);

		if ( !empty($aListingsIn) ){
			$aListingsIn = array_map('abs', $aListingsIn);
			$sql .= $concat . "$postMetaTbl.meta_value IN(".implode(',', $aListingsIn).'")"';
			$concat = ' AND ';
        }

		if ( !empty($aEventIDsIn) ){
			$aEventIDsIn = array_map('abs', $aEventIDsIn);
			$sql .= $concat . "$postMetaTbl.post_id IN(".implode(',', $aEventIDsIn).'")"';
			$concat = ' AND ';
		}

        if ( !empty($locationID) || !empty($catID) ){
		    $aTermIDs = array();

		    if ( !empty($locationID) ){
			    $aTermIDs = array(abs($locationID));
			    $aLocationChildren = get_term_children( $locationID, 'listing_location' );
			    if ( !empty($aLocationChildren) && !is_wp_error($aLocationChildren) ){
				    $aTermIDs = array_merge($aTermIDs, $aLocationChildren);
			    }
            }

            if ( !empty($catID) ){
	            $aTermIDs[] = abs($catID);
	            $aCatChildren = get_term_children( $catID, 'listing_cat' );
	            if ( !empty($aCatChildren) && !is_wp_error($aCatChildren) ){
		            $aTermIDs = array_merge($aTermIDs, $aCatChildren);
	            }
            }
            $sql .= $concat . "$termRelationshipTbl.term_taxonomy_id IN (".implode(',', $aTermIDs).")";
	        $concat =  " AND ";
        }

		if ( !empty($aListingNotIn) ){
			$aListingNotIn = array_map('abs', $aListingNotIn);
			$sql .= $concat . " $termRelationshipTbl.object_id NOT IN(".implode(',', $aListingNotIn).'")"';
        }

        $sql .= " ORDER BY $postMetaTbl.post_id LIMIT 0,".abs($limit);

		$aListingIDs = $wpdb->get_results($sql, ARRAY_A);

		if ( empty($aListingIDs) ){
			return false;
		}

		$totalPosts = $wpdb->get_var("SELECT FOUND_ROWS()");

		$aObjectIDs = array_map(function($aListingIDs){
			return $aListingIDs['post_id'];
		}, $aListingIDs);

		return array(
			'IDs' => $aObjectIDs,
			'total' => $totalPosts
		);
    }

    public function maybeAllEvenInTrash($aEvents){
	    if ( empty($aEvents) ){
	        return true;
        }

	    foreach ( $aEvents as $eventID ){
	        if ( !empty($eventID) && get_post_field('post_status', $eventID) == 'publish' ){
	            return false;
            }
        }

        return true;
    }

	public function searchLocationWithin($centerLat, $centerLng, $limit, $distance=5, $eventsNotIn=array()){
	    global $wpdb;
	    $geoTbl = $wpdb->prefix . AlterTableGeoPosition::$tblName;

		$limit = empty($limit) ? 100000 : abs($limit);
		$distance = empty($distance) ? 10 : abs($distance);

	    if ( empty($eventsNotIn) ){
		    $aObjectIDs = $wpdb->get_results(
			    $wpdb->prepare(
				    "SELECT SQL_CALC_FOUND_ROWS postID, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $geoTbl.lat ) ) * cos( radians( $geoTbl.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $geoTbl.lat ) ) ) ) as distance FROM $geoTbl HAVING distance < %d ORDER BY distance LIMIT 0,%d",
				    $centerLat, $centerLng, $centerLat, $distance, $limit
			    ),
			    ARRAY_A
		    );
        }else{
		    $aObjectIDs = $wpdb->get_results(
			    $wpdb->prepare(
				    "SELECT SQL_CALC_FOUND_ROWS postID, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $geoTbl.lat ) ) * cos( radians( $geoTbl.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $geoTbl.lat ) ) ) ) as distance FROM $geoTbl HAVING distance < %d AND postID NOT IN(%s) ORDER BY distance LIMIT 0,%d",
				    $centerLat, $centerLng, $centerLat, $distance, implode(',', $eventsNotIn), $limit
			    ),
			    ARRAY_A
		    );
        }

		$totalPosts = $wpdb->get_var("SELECT FOUND_ROWS()");

        if ( empty($aObjectIDs) ){
            return false;
        }

		$aObjectIDs = array_map(function($aObject){
            return $aObject['postID'];
		}, $aObjectIDs);

        return array(
            'IDs' => $aObjectIDs,
            'total' => $totalPosts
        );
	}

	public function fetchEvents(){
	    if ( empty($_GET['paged']) || empty($_GET['configuration']) ){
	        wp_send_json_error(array(
	                'msg' => WilokeListingManage::message(
                        array(
                            'message' => esc_html__('Whoops! Sorry, We can\'t find what you are looking for.', 'wiloke')
                        ),
                        'danger',
                        true
                    )
                )
            );
        }

		$aConfiguration = json_decode(stripslashes($_GET['configuration']), true);
		$aConfiguration['posts_per_page'] = abs($aConfiguration['posts_per_page']);

        $aArgs = array(
	        'post_type'     => 'event',
	        'post_status'   => array('publish'),
	        'paged'         => $_GET['paged'],
	        'posts_per_page'=> $aConfiguration['posts_per_page'] > 30 ? 30 : $aConfiguration['posts_per_page']
        );

        $atts['limit_character'] = !isset($_GET['limitCharacter']) || empty($_GET['limitCharacter']) ? 100 : $_GET['limitCharacter'];

        $maxPosts = null;

        $errMsg = WilokeListingManage::message(
	        array(
		        'message' => esc_html__('Whoops! Sorry, We can\'t find what you are looking for.', 'wiloke')
	        ),
	        'danger',
            true
        );

        if ( isset($_GET['latLng']) && !empty($_GET['latLng']) ){
	        $distance = isset($_GET['distance']) && !empty($_GET['distance']) ? abs($_GET['distance']) : 5;
            $aLatLng = explode(',', $_GET['latLng']);
	        $eventsNotIn = '';
            if ( $_GET['prevLatLng'] == $_GET['latLng'] ){
                $eventsNotIn = $_GET['eventsNotIn'];
            }

            $aEventData = $this->searchLocationWithin(trim($aLatLng[0]), trim($aLatLng[1]), 1, $distance, $eventsNotIn);

            if ( empty($aEventData) ){
	            wp_send_json_error(
		            array(
			            'msg' => $errMsg
		            )
	            );
            }else{
                $aEventIDs = $aEventData['IDs'];
                if ( isset($_GET['catID']) && !empty($_GET['catID']) ){
	                $aEventIDs = $this->searchListingInCatAndLocation('', $_GET['catID'], '', '', '', $aEventIDs);
                }

	            $aArgs['post__in'] = $aEventIDs;
	            $maxPosts = $aEventData['total'];
	            unset($aArgs['paged']);
	            unset($aArgs['post__not_in']);
	            unset($aArgs['posts_per_page']);
            }
        }else if (!empty($_GET['locationID']) || !empty($_GET['catID'])){
	        $aListingNotIn = '';
	        if ( ($_GET['prevCatID'] != $_GET['catID']) && ($_GET['locationID'] != $_GET['prevLocationID']) ){
	            $aListingNotIn =  $aArgs['post__not_in'];
		        unset($aArgs['post__not_in']);
	        }

	        $aListingData = $this->searchListingInCatAndLocation($_GET['locationID'], $_GET['catID'], $aConfiguration['posts_per_page'], $aListingNotIn);

            if ( empty($aListingData) ){
	            wp_send_json_error(
		            array(
			            'msg' => $errMsg
		            )
	            );
            }else{
	            $aArgs['post__in'] = $aListingData['IDs'];
	            $maxPosts = $aListingData['total'];
	            unset($aArgs['paged']);
	            unset($aArgs['post__not_in']);
	            unset($aArgs['posts_per_page']);
            }
        }

        if ( empty($_GET['catID']) ){
            if ( !empty($_GET['s']) ){
                $aArgs['s'] = trim($_GET['s']);
            }
        }
        
        $query = new \WP_Query($aArgs);

        if ( $query->have_posts() ){
            global $post;
	        $maxPosts = $query->found_posts;
            ob_start();
            while ($query->have_posts()){
                $query->the_post();
                include WILOKE_PUBLIC_DIR . 'template/vc/events-layout/default.php';
            }
            $content = ob_get_contents();
            ob_end_clean();
        }else{
            wp_send_json_error(
                array(
                    'msg' => $errMsg
                )
            );
        }

        wp_send_json_success(
            array(
                'msg' => $content,
                'totalposts'=>$maxPosts
            )
        );
    }

	public static function searchForm(){
		$aLocations = get_terms(
			array(
				'taxonomy'    => 'listing_location',
				'hide_empty'  => 1,
				'number'      => 10
			)
		);
		$aCategories = \Wiloke::getTaxonomyHierarchy('listing_cat');

		if ( !empty($aLocations) && !is_wp_error($aLocations) ){
			$aLocations = json_encode($aLocations);
		}else{
			$aLocations = '';
		}
		?>

        <form method="GET" id="listgo-event-searchfrom" class="form form--listing">
            <div class="col-sm-6">
                <div class="form-item item--search">
                    <label for="s_search" class="label"><?php echo esc_html_e('Enter event name or Select a suggestion category', 'wiloke'); ?></label>
                    <span class="input-text input-icon-inside">
                        <input id="s_search" class="disabled-autocomplete-ajax" type="text" name="listgo_event_name_or_cat" value="">
                        <?php
                        if ( !empty($aCategories) && !is_wp_error($aCategories) ) :
                            ?>
                            <input type="hidden" id="wiloke-original-search-suggestion" value="<?php echo esc_attr(json_encode($aCategories)); ?>">
                            <input type="hidden" id="s_listing_cat" name="s_listing_cat" value="">
                        <?php endif; ?>
                        <i class="input-icon icon_search"></i>
                    </span>
                </div>
            </div>
            <div class="col-sm-6">
                <?php
                    \WilokePublic::renderLocationField(esc_html__('Location', 'wiloke'), $aLocations);
                ?>
            </div>
        </form>
        <?php
    }

	public static function hasEvent(){
	    if ( self::$hasEvent !== null ){
	        return self::$hasEvent;
        }

		$query = new \WP_Query(
			array(
				'post_type'      => 'event-pricing',
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'orderby'        => 'menu_order'
			)
		);

		if ( $query->have_posts() ){
		    self::$hasEvent = true;
        }else{
			self::$hasEvent = false;
        }
		return self::$hasEvent;
    }

    public static function paymentMethods(){
	    $aPaymentGateWays = WilokePayment::getPaymentGateWays(true);
	    if ( !empty($aPaymentGateWays) ):
	        ?>
            <table class="addlisting-popup__package">
			    <?php
                $order = 0;
			    foreach ($aPaymentGateWays as $gateWay => $name) :
                    $checked = empty($order) ? 'checked' : '';
				    ?>
                    <tr id="listgo-gateway-<?php echo esc_attr($gateWay); ?>">
                        <td class="addlisting-popup__package-action" data-title="<?php echo esc_attr($name); ?>">
                            <label class="addlisting-popup__radio">
                                <input type="radio" name="event_payment_method" value="<?php echo esc_attr($gateWay); ?>" <?php echo esc_attr($checked); ?>><span></span>
                            </label>
                        </td>

                        <td class="addlisting-popup__package-description">
                            <div class="addlisting-popup__package-name">
                                <strong><?php echo esc_html($name); ?></strong>
                            </div>
                        </td>
                        <?php if ( $gateWay == '2checkout' ) : ?>
                        <td class="addlisting-popup__package-form">
                            <div class="wiloke-event-method__form">
	                            <?php include plugin_dir_path(__FILE__) . 'templates/card-form.php'; ?>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
				    <?php
                $order++;
			    endforeach;
			    ?>
            </table>
            <?php
        else:
            WilokeListingManage::message(
                array(
                    'msg' => esc_html__('Oops! The website has not any payment gateway yet.', 'wiloke')
                )
            );
        endif;
    }

	public static function fetchEventPlan(){
        $query = new \WP_Query(
            array(
                'post_type'      => 'event-pricing',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'orderby'        => 'menu_order'
            )
        );

        if ( $query->have_posts() ):
            $order = 0;
            ?>
            <table class="addlisting-popup__package">
            <?php
            while ( $query->have_posts() ) :
                $query->the_post();
                if ( empty($order) ){
	                $activeClass = 'active';
	                $checked = 'checked';
                }else{
	                $activeClass = '';
	                $checked = '';
                }
                $aEventSettings = \Wiloke::getPostMetaCaching($query->post->ID, 'event_pricing_settings');

                if ( empty($aEventSettings['price']) || empty($aEventSettings['number_of_posts']) ){
		            return '';
	            }

                ?>
                    <tr class="<?php echo esc_attr($activeClass); ?>" data-title="<?php echo esc_attr($query->post->ID); ?>">
                        <td class="addlisting-popup__package-action" data-title="<?php echo esc_attr($query->post->post_title); ?>">
                            <label class="addlisting-popup__radio">
                                <input type="radio" name="event_plan_id" value="<?php echo esc_attr($query->post->ID); ?>" <?php echo esc_attr($checked); ?>><span></span>
                            </label>
                        </td>

                        <td class="addlisting-popup__package-price-td" data-title="<?php esc_html_e('Price', 'wiloke'); ?>">
                            <span class="addlisting-popup__package-price">
                                <sup class="addlisting-popup__package-price-currency color-yelow"><?php echo esc_html(WilokePayment::getCurrency()); ?></sup>
                                <span class="addlisting-popup__package-price-amount color-yelow"><?php echo esc_html($aEventSettings['price']); ?></span>
                                <span class="addlisting-popup__package-price-slash">/</span>
                                <span class="addlisting-popup__package-price-time color-green"><?php echo esc_html($aEventSettings['number_of_posts']); ?> <?php echo absint($aEventSettings['number_of_posts']) > 1 ? esc_html__('Event', 'wiloke') : esc_html__('Events', 'wiloke'); ?></span>
                            </span>
                        </td>
                        <td class="addlisting-popup__package-description" data-title="<?php esc_html_e('Description', 'wiloke'); ?>">
                            <div>
                                <strong><?php echo esc_html($query->post->post_title); ?></strong>
                                <p><?php echo esc_html($query->post->post_content); ?></p>
                            </div>
                        </td>
                    </tr>
                <?php
	            $order++;
            endwhile;
            ?>
            </table>
            <?php
        endif;
        wp_reset_postdata();
    }

    public function fetchEventData(){
	    if ( empty($_POST['security']) || empty($_POST['eventID']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
		    wp_send_json_error(
			    array(
				    'msg' => esc_html__('Wrong Security Code', 'wiloke')
			    )
		    );
	    }

	    if ( $msg = $this->eventExpirationEdit($_POST['eventID']) ){
		    wp_send_json_error(
			    array(
				    'msg' => $msg
			    )
		    );
	    }

        if ( !current_user_can('edit_theme_options') && (get_post_field('post_author', $_POST['eventID']) != get_current_user_id()) ){
	        wp_send_json_error(
		        array(
			        'msg' => esc_html__('You don\'t have permission to access this area', 'wiloke')
		        )
	        );
        }

        $aData['event_title'] = get_the_title($_POST['eventID']);
        $aData['event_content'] = get_post_field('post_content', $_POST['eventID']);
        $aData['event_featured_image'] = get_post_thumbnail_id($_POST['eventID']);
        $aData['event_featured_image_url'] = !empty($aData['event_featured_image']) ? get_the_post_thumbnail_url($_POST['eventID'], 'large') : '';

        $aEventSettings = \Wiloke::getPostMetaCaching($_POST['eventID'], $this->eventSettingsKey);

        $aData = array_merge($aData, $aEventSettings);

        wp_send_json_success($aData);
    }

    public function eventExpirationEdit($eventID, $compareWith=null){
        if ( current_user_can('edit_theme_options') ){
            return false;
        }

	    $aEventSettings = \Wiloke::getPostMetaCaching($eventID, $this->eventSettingsKey);
	    $timeZone = '';
	    if ( !empty($aEventSettings['belongs_to']) ){
		    $aLocation = wp_get_post_terms($aEventSettings['belongs_to'], 'listing_location');
		    if ( !empty($aLocation) && !is_wp_error($aLocation) ){
			    $oLocation = end($aLocation);
			    $aLocationData = \Wiloke::getTermOption($oLocation->term_id);
			    if ( isset($aLocationData['timezone']) ){
				    $timeZone = $aLocationData['timezone'];
			    }
		    }
	    }

	    $createdAt = get_post_meta($eventID, $this->eventPublishedAtTimeZoneKey, true);
	    if ( empty($createdAt) ){
			$createdAt = get_post_field('post_date_gmt', $eventID);
	    }
	    
	    $createdAt = strtotime($createdAt);
	    date_default_timezone_set(\WilokePublic::timezoneString($timeZone));
	    $now = time();
	    $compareWith = empty($compareWith) ? 2100 : $compareWith;
	    if ( intval($now - $createdAt) >= absint($compareWith)  ){
		    return true;
	    }

	    return false;
    }

    public function createNewEvent($listingID, $customerID, $aParsedData){
        $aCustomerPlan = WilokeCustomerPlan::getCustomerPlan();
        $aEventPlanSettings = \Wiloke::getPostMetaCaching($aCustomerPlan['paymentEventID'], 'event_pricing_settings');

	    $eventID = wp_insert_post(
		    array(
			    'post_type'     => $this->postType,
			    // 'post_date_gmt' => WilokeEventPayment::getPostDateInTimeZone($listingID),
			    'post_title'    => $aParsedData['event_title'],
			    'post_content'  => $aParsedData['event_content'],
			    'post_status'   => 'publish',
			    'post_author'   => $customerID,
                'menu_order'    => abs($aEventPlanSettings['price'])
		    )
	    );

	    set_post_thumbnail($eventID, $aParsedData['event_featured_image']);
	    unset($aParsedData['event_title']);
	    unset($aParsedData['event_content']);
	    unset($aParsedData['event_featured_image']);
	    $aParsedData['belongs_to'] = $listingID;
	    update_post_meta($eventID, $this->eventSettingsKey, $aParsedData);
	    update_post_meta($eventID, $this->eventPublishedAtTimeZoneKey, WilokeEventPayment::getPostDateInTimeZone($listingID));
	    $aListingEvents = \Wiloke::getPostMetaCaching($listingID, $this->eventListingRelationshipKey);
	    $aListingEvents[] = $eventID;
	    update_post_meta($listingID, $this->eventListingRelationshipKey, $aListingEvents);

	    if ( current_user_can('edit_theme_options') ){
		    $showEventOnCarouselStatus = $showEventOnListStatus = 'enable';
        }else{
		    $showEventOnCarouselStatus 	= get_post_meta($aCustomerPlan['eventPlanID'], self::$showEventOnCarouselKey, true);
		    $showEventOnListStatus 		= get_post_meta($aCustomerPlan['eventPlanID'], self::$showEventOnListKey, true);
        }

	    update_post_meta($eventID, self::$showEventOnCarouselKey, $showEventOnCarouselStatus);
		update_post_meta($eventID, self::$showEventOnListKey, $showEventOnListStatus);

	    return $eventID;
    }

    public static function checkEventStatus($aEventSettings){
	    $timeZone = '';
        if ( !empty($aEventSettings['belongs_to']) ){
            $aLocation = wp_get_post_terms($aEventSettings['belongs_to'], 'listing_location');
            if ( !empty($aLocation) && !is_wp_error($aLocation) ){
	            $oLocation = end($aLocation);
	            $aLocationData = \Wiloke::getTermOption($oLocation->term_id);
	            if ( isset($aLocationData['timezone']) ){
		            $timeZone = $aLocationData['timezone'];
	            }
            }
        }

	    date_default_timezone_set(\WilokePublic::timezoneString($timeZone));
	    $now   = time();
        $start = trim($aEventSettings['start_on']) . ' ' . trim($aEventSettings['start_at']);
        $start = WilokeConvertTime::toTimestamp($start);

        $end = trim($aEventSettings['end_on']) . ' ' . trim($aEventSettings['end_at']);
	    $end = WilokeConvertTime::toTimestamp($end);

        if ( $now > $end ){
            return array(
                'status'    => 'expired',
                'name'      => __('Expired', 'wiloke'),
                'longname'  => __('Expired Event', 'wiloke')
            );
        }else if ( $now < $start ){
	        return array(
		        'status' => 'upcomming',
		        'name'   => __('Upcoming', 'wiloke'),
		        'longname'  => __('Upcoming Event', 'wiloke')
	        );
        }else{
	        return array(
		        'status' => 'ongoing',
		        'name'   => __('Ongoing', 'wiloke'),
		        'longname'  => __('Ongoing Event', 'wiloke')
	        );
        }
    }

    public function allowAddingEvent($post){
        if ( $this->isAllowAddingEvent !== null ){
            return $this->isAllowAddingEvent;
        }

        if ( current_user_can('edit_theme_options') ){
	        $this->isAllowAddingEvent = true;
	        return true;
        }

        if ( get_current_user_id() != $post->post_author ){
            $this->isAllowAddingEvent = false;
            return false;
        }

	    $this->isAllowAddingEvent = $post->post_status === 'publish';
        return $this->isAllowAddingEvent;
    }
    
	protected function parseEventData($aRawData){
		$aEventSettings = json_decode(urldecode(base64_decode($aRawData['data'])), true);
		$aParsedData = array();
		foreach ( $aEventSettings as $aField ){
			$name = strip_tags($aField['name']);
			$aParsedData[$name] = sanitize_text_field($aField['value']);
		}
		$aParsedData['event_content'] = wp_kses_post($aRawData['event_content']);

		return $aParsedData;
	}

	public function updateEvent(){
	    if ( empty($_POST['security']) || empty($_POST['listingID']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
	        wp_send_json_error(
                array(
                    'msg' => esc_html__('Wrong Security Code', 'wiloke')
                )
            );
        }

        $listingID = trim($_POST['listingID']);
        $customerID = get_current_user_id();

	    if ( (get_post_field('post_author', $listingID) != $customerID) && !current_user_can('edit_theme_options') ) {
		    wp_send_json_error(
			    array(
				    'msg' => esc_html__( 'You are not author of this listing', 'wiloke' )
			    )
		    );
	    }

		if ( empty($_POST['event_content']) ) {
			wp_send_json_error(
				array(
					'msg' => esc_html__( 'Event Content is required', 'wiloke' )
				)
			);
		}

        $aParsedData = $this->parseEventData($_POST);

        if ( !isset($_POST['eventID']) || empty($_POST['eventID']) ){
	        $remainingEvent = WilokeEventPayment::getRemainingEvent();

	        if (empty($remainingEvent) ){
		        wp_send_json_error(
			        array(
				        'msg' => esc_html__( 'You have exceeded the maximum number of listings allowed for this plan, please purchase a new event plan to add more.', 'wiloke' )
			        )
		        );
            }
	        $eventID = $this->createNewEvent($listingID, $customerID, $aParsedData);
	        ob_start();
            $this->renderEvent($eventID, false);
	        $content = ob_get_clean();
	        wp_send_json_success(
                array(
                    'msg' => $content,
                    'eventID' => $eventID
                )
            );
        }else{
            $eventID = trim($_POST['eventID']);
	        $aEventsInListing = \Wiloke::getPostMetaCaching($listingID, $this->eventListingRelationshipKey);
            if ( empty($aEventsInListing) || !in_array($eventID, $aEventsInListing) ){
                wp_send_json_error(
	                array(
		                'msg' => esc_html__('You don\'t have permission to access this area.', 'wiloke')
	                )
                );
            }

            if ( $msg = $this->eventExpirationEdit($eventID)  ){
	            wp_send_json_error(
		            array(
			            'msg' => $msg
		            )
	            );
            }

            wp_update_post(
                array(
                    'ID' => $eventID,
                    'post_title' => $aParsedData['event_title'],
                    'post_content' => $aParsedData['event_content']
                )
            );
            unset($aParsedData['event_title']);
            unset($aParsedData['event_content']);

            if ( !empty($aParsedData['event_featured_image']) ){
	            set_post_thumbnail($eventID, $aParsedData['event_featured_image']);
	            unset($aParsedData['event_featured_image']);
            }

            $aParsedData['belongs_to'] = $listingID;
            update_post_meta($eventID, $this->eventSettingsKey, $aParsedData);
	        ob_start();
	        $this->renderEvent($eventID, false);
	        $content = ob_get_clean();
	        wp_send_json_success(
		        array(
			        'msg'     => $content,
			        'eventID' => $eventID
		        )
	        );
        }

        wp_send_json_success(
            array(
                'eventID' => $eventID
            )
        );
    }
    
	public function enqueueScripts(){
	    global $post;
		if ( !is_singular('listing') || ( !current_user_can('edit_theme_options') && ($post->post_author != get_current_user_id()) ) ){
			return false;
		}

		wp_enqueue_script('2co', 'https://www.2checkout.com/checkout/api/2co.min.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true );

		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery-ui', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.min.css');
		wp_enqueue_style('wiloke-shortcode-popup', get_template_directory_uri() . '/css/popup-css.css', array(), WILOKE_LISTGO_FC_VERSION);
		wp_enqueue_script('listgo-add-events', WILOKE_LISTGO_FUNC_URL . 'public/source/js/addevent.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);

		wp_localize_script('listgo-add-events', 'WILOKE_LISTGO_EVENTS', array(
            'date_format' => get_option('date_format'),
            'saved_btn'   => esc_html__('Saved', 'wiloke'),
            'error_btn'   => esc_html__('Error', 'wiloke'),
            'create_btn'  => esc_html__('Create Event', 'wiloke'),
            'update_event'=> esc_html__('Update Event', 'wiloke'),
            'pay_btn'     => esc_html__('Pay & Publish', 'wiloke'),
            'twoCheckoutMissingCardData' => WilokeListingManage::message(
	            array(
		            'message' => esc_html__('Please expand the card form and complete all your information', 'wiloke'),
		            'icon' => 'icon_error-triangle_alt'
	            ),
	            'danger',
	            true
            )
        ));
	}

	public function addEventToSecondaryTab(){
	    if ( !empty($this->aEventStatus) ) :
        ?>
        <li class="tab-nav-event <?php echo esc_attr($this->aEventStatus['status']); ?>"><a href="#tab-event"><?php echo esc_html($this->aEventStatus['longname']); ?></a></li>
        <?php
        endif;
    }

	public function addEventTab(){
		global $post;
		if ( $post->post_status !== 'publish' ){
		    return '';
        }

		$aEventIDs = \Wiloke::getPostMetaCaching($post->ID, $this->eventListingRelationshipKey);
		$aEventStatus = array();
        $remainingEvent = WilokeEventPayment::getRemainingEvent();
		if ( !$this->allowAddingEvent($post) ):
            if ( !$this->maybeAllEvenInTrash($aEventIDs) ) :
	            $aEventStatus = $this->theFirstEventStatus($aEventIDs);
		?>
			<li class="tab-nav-event <?php echo esc_attr($aEventStatus['status']); ?>"><a href="#tab-event"><?php echo esc_html($aEventStatus['longname']); ?></a></li>
		<?php
            endif;
		else:
            if ( !$this->allowAddingEvent($post) ){
                return '';
            }

            if ( empty($aEventIDs) && !self::hasEvent() ){
		        return '';
            }

            if ( $this->maybeAllEvenInTrash($aEventIDs) && self::hasEvent() ) :
		?>
			<li class="tab-nav-event zero-event"><a href="#tab-event"><span id="listgo-add-event" class="add-event" data-remaining="<?php echo esc_attr($remainingEvent); ?>">+</span> <?php esc_html_e('Create Event', 'wiloke'); ?> <span class="wil-sos"></span></a></li>
		<?php
            else:
	            $aEventStatus = $this->theFirstEventStatus($aEventIDs);
            ?>
                <li class="tab-nav-event <?php echo esc_attr($aEventStatus['status']); ?>"><a href="#tab-event"><?php echo esc_html($aEventStatus['longname']); ?> <span class="wil-sos"></span></a></li>
            <?php
            endif;
		endif;

		$this->aEventStatus = $aEventStatus;
	}

	public function printEventContent(){
	    global $post;
		if ( $post->post_status !== 'publish' ){
			return '';
		}

	    $aEventIDs = \Wiloke::getPostMetaCaching($post->ID, $this->eventListingRelationshipKey);
		if ( !current_user_can('edit_theme_options') && ($post->post_author != get_current_user_id()) ):
            if ( empty($aEventIDs) ){
		        return '';
		    }
			?>
            <div id="tab-event" class="tab__panel">
                <?php
                $aEventIDs = array_reverse($aEventIDs);
                $order = 0;
                foreach ( $aEventIDs as $eventID ){
	                if ( get_post_field('post_status', $eventID) != 'publish' ){
		                continue;
	                }

	                $isPrintAddListingBtn = ($order == 0) && self::hasEvent();
	                $this->renderEvent($eventID, $isPrintAddListingBtn);
	                $order++;
                }
                ?>
            </div>
			<?php
		else:
            if ( empty($aEventIDs) && !self::hasEvent() ) {
	            return '';
            }

			if ( !$this->allowAddingEvent($post) ){
				return '';
			}

            ?>
            <div id="tab-event" class="tab__panel">
                <?php
                if (  $this->maybeAllEvenInTrash($aEventIDs) ) :
                    WilokeListingManage::message(
                        array(
                            'message' => esc_html__('You have not any event yet. Note that this message is only seen by admin', 'wiloke')
                        )
                    );
                else:
                    $aEventIDs = array_reverse($aEventIDs);
                    $order = 0;
                    foreach ( $aEventIDs as $eventID ){
	                    if ( get_post_field('post_status', $eventID) != 'publish' ){
		                    continue;
	                    }
	                    $isPrintAddListingBtn = ($order == 0) && self::hasEvent();
                        $this->renderEvent($eventID, $isPrintAddListingBtn);
	                    $order++;
                    }
                endif;
                ?>
            </div>
			<?php
		endif;
    }

    public function renderEvent($eventID, $isPrintAddListingBtn=true){
	    $aEventSettings = \Wiloke::getPostMetaCaching($eventID, $this->eventSettingsKey);
	    ?>
	    <?php
	    if ( !$this->maybeAllEvenInTrash($aEventSettings) && $isPrintAddListingBtn ) :
		    $remainingEvent = WilokeEventPayment::getRemainingEvent();
		    ?>
		    <?php if ( current_user_can('edit_theme_options') || (get_post_field('post_author', $eventID) == get_current_user_id()) ) : ?>
            <a id="listgo-add-event" class="listing-single__event-create add-event inline-add-event" href="#" data-remaining="<?php echo esc_attr($remainingEvent); ?>"><i class="icon_plus"></i> <?php esc_html_e('Add Event', 'wiloke'); ?></a>
	        <?php endif; ?>
	    <?php endif; ?>


        <div id="event-<?php echo esc_attr($eventID); ?>" class="listing-single__event">
		    <?php if ( has_post_thumbnail($eventID) ) : ?>
                <div class="listing-single__event-media">
				    <?php echo get_the_post_thumbnail($eventID, 'large'); ?>
                </div>
		    <?php endif; ?>

            <h2 class="listing-single__event-title"><?php echo get_the_title($eventID); ?></h2>

            <div class="listing-event__start">
                <table class="listing-event__table">
                    <thead>
                    <tr>
                        <th><?php esc_html_e('Address', 'wiloke'); ?></th>
                        <th><?php esc_html_e('From', 'wiloke'); ?></th>
                        <th><?php esc_html_e('To', 'wiloke'); ?></th>
                    </tr>
                    </thead>
                    <tr>
                        <td class="listing-event__address" title="Address">
                            <p>
                                <i class="color-yelow icon_pin_alt"></i> <?php echo esc_html_e($aEventSettings['place_detail']); ?>
                            </p>
                        </td>
                        <td class="listing-event__from" title="<?php esc_html_e('Events starts', 'wiloke'); ?>">
                            <p>
							    <?php if ( !empty($aEventSettings['start_at']) ) : ?>
                                    <i class="color-green icon_clock_alt"></i> <?php echo esc_html($aEventSettings['start_at']); ?> <br>
							    <?php endif; ?>
							    <?php if ( !empty($aEventSettings['start_on']) ) : ?>
                                    <i class="color-green icon_table"></i> <?php echo esc_html($aEventSettings['start_on']); ?>
							    <?php endif; ?>
                            </p>
                        </td>
                        <td class="listing-event__to"  title="<?php esc_html_e('Events closes', 'wiloke'); ?>">
                            <p>
							    <?php if ( !empty($aEventSettings['end_at']) ) : ?>
                                    <i class="color-red icon_clock_alt"></i> <?php echo esc_html($aEventSettings['end_at']); ?> <br>
							    <?php endif; ?>
							    <?php if ( !empty($aEventSettings['end_on']) ) : ?>
                                    <i class="color-red icon_table"></i> <?php echo esc_html($aEventSettings['end_on']); ?>
							    <?php endif; ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="listing-single__event-content">
			    <?php echo get_post_field('post_content', $eventID); ?>
            </div>

            <?php if ( current_user_can('edit_theme_options') || (get_post_field('post_author', $eventID) == get_current_user_id()) ) : ?>
            <div class="listing-single__event-actions">
	            <?php
	            $expiredEditing = $this->eventExpirationEdit($eventID, 1800);
                if ( !$expiredEditing && !current_user_can('edit_theme_options') ) :
	            ?>
                <div class="listing-single__event-msg">
		            <?php esc_html_e('This event can not be edited after', 'wiloke'); ?>
                    <span class="listing-single__event-countdown-edit">
                        <span class="listing-single__event-countdown-edit-label"><?php esc_html_e('Countdown:', 'wiloke'); ?></span>
                        <span id="listgo-countdown-editing-period-<?php echo esc_attr($eventID); ?>" class="listing-single__event-countdown-edit-time" data-created="<?php echo esc_attr(get_post_field('post_date', $eventID)); ?>"><?php echo esc_attr('30:00'); ?></span> <?php esc_html_e(' minutes', 'wiloke'); ?>
                    </span>
                </div>
	            <?php endif; ?>
                <div class="pull-right">
                    <?php if ( !$expiredEditing ) : ?>
                    <a id="listgo-edit-event-<?php echo esc_attr($eventID); ?>" class="listing-single__event-edit" data-id="<?php echo esc_attr($eventID); ?>" href="#"><i class="icon_pencil"></i> <span><?php esc_html_e('Edit', 'listgo'); ?></span></a>
                    <?php endif; ?>
                    <a id="listgo-delete-event-<?php echo esc_attr($eventID); ?>" class="listing-single__event-remove" data-id="<?php echo esc_attr($eventID); ?>" href="#"><i class="icon_close"></i> <span><?php esc_html_e('Remove', 'listgo'); ?></span></a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

	public function insertAddEventTemplateToFooter(){
		if ( !is_singular('listing') ){
			return false;
		}

		if ( !self::hasEvent() ){
			return '';
		}

		global $post;
		if ( !$this->allowAddingEvent($post) ){
			return false;
		}

		include plugin_dir_path(__FILE__) . 'templates/add-event.php';
		include plugin_dir_path(__FILE__) . 'templates/remove-event.php';
	}
}