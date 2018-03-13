<?php
/**
 * The class handle everything related to front-end
 *
 * @since       1.0
 * @link        http://wiloke.com
 * @author      Wiloke Team
 * @package     WilokeFramework
 * @subpackpage WilokeFramework/admin/front-end
 */
use WilokeListGoFunctionality\AlterTable\AlterTableFavirote;
use WilokeListGoFunctionality\Register\RegisterFollow;
use WilokeListGoFunctionality\AlterTable\AltertableFollowing;
use WilokeListGoFunctionality\Register\RegisterWilokeSubmission;
use WilokeListGoFunctionality\AlterTable\AlterTablePackageStatus;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentRelationships;
use WilokeListGoFunctionality\Submit\User as WilokeSubmissionUser;
use WilokeListGoFunctionality\Register\RegisterWilokeSubmission as WilokeWilokeSubmission;
use WilokeListGoFunctionality\Submit\AddListing;
use WilokeListGoFunctionality\AlterTable\AlterTableReviews;
use WilokeListGoFunctionality\Frontend\Notification as WilokeNotification;
use WilokeListGoFunctionality\AlterTable\AlterTableNotifications;
use WilokeListGoFunctionality\Register\RegisterBadges;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentHistory;
use WilokeListGoFunctionality\Payment\Payment;
use WilokeListGoFunctionality\AlterTable\AlterTablePriceSegment as WilokePriceSegmentTbl;
use WilokeListGoFunctionality\AlterTable\AlterTableBusinessHours as WilokeBusinessHoursTbl;
use WilokeListGoFunctionality\Frontend\FrontendRating;
use WilokeListGoFunctionality\Register\RegisterClaim as WilokeClaimSystem;
use WilokeListGoFunctionality\Frontend\FrontendClaimListing as WilokeFrontendClaimListing;
use WilokeListGoFunctionality\Frontend\FrontendListingManagement as WilokeFrontendListingManagement;
use Predis\Collection\Iterator;
use WilokeListGoFunctionality\CustomerPlan\CustomerPlan as WilokeCustomerPlan;
use WilokeListGoFunctionality\Frontend\FrontendManageSingleListing;
use WilokeListGoFunctionality\Model\GeoPosition;

/**
 * Deny directly access
 * @since 1.0
 */
if ( !defined('ABSPATH') || !class_exists('WilokePublic') ){
    return false;
}

class WilokePublic
{
    public static $oUserInfo;
    public static $aUsersData;

    public static $aTemporaryCaching = array();

    public static $aPostStatusIcons = array(
        'renew'     => 'icon_cloud-upload_alt',
        'publish'   => 'icon_like',
        'expired'   => 'icon_lock_alt',
        'pending'   => 'icon_cloud-upload_alt',
        'processing'=> 'icon_loading',
        'temporary_closed' => 'fa fa-lock'
    );

    public static $current_layout = 'wiloke_current_layout';

    public static $single_post_settings = 'single_post_settings';

    public static $thanksForReviewingKey = 'wiloke_listgo_thanks_reviewing';
    public static $scoreThanksForReviewingKey = 'wiloke_listgo_score_thanks_reviewing';

    public static $googleMap = 'https://www.google.com/maps/place/';

    public static $aListingTermRelationship = array();

    public static $oListingCollections;
    public static $aTermRelationships;
    protected static $aPaymentFields = array();
    public static $aClaimStatus = array();

    public function __construct() {
        add_action('init', array($this, 'init'), 1);
        add_action('wp_ajax_wiloke_search_suggestion', array($this, 'searchSuggestion'));
        add_action('wp_ajax_nopriv_wiloke_search_suggestion', array($this, 'searchSuggestion'));
        add_action('wp_ajax_nopriv_wiloke_loadmore_listing_layout', array($this, 'loadmore_listing_layout'));
        add_action('wp_ajax_wiloke_loadmore_listing_layout', array($this, 'loadmore_listing_layout'));

        add_action('wp_ajax_wiloke_loadmore_map', array($this, 'loadmore_map'));
        add_action('wp_ajax_nopriv_wiloke_loadmore_map', array($this, 'loadmore_map'));
        add_action('wp_ajax_nopriv_wiloke_get_term_children', array($this, 'getTermChildren'));
        add_action('wp_ajax_wiloke_get_term_children', array($this, 'getTermChildren'));

        add_action('wp_ajax_nopriv_wiloke_listgo_submit_review', array($this, 'submitReview'));
        add_action('wp_ajax_wiloke_listgo_submit_review', array($this, 'submitReview'));

        add_action('wp_ajax_nopriv_wiloke_listgo_thanks_reviewing', array($this, 'thanksForReviewing'));
        add_action('wp_ajax_wiloke_listgo_thanks_reviewing', array($this, 'thanksForReviewing'));

        add_action('wp_ajax_nopriv_wiloke_listgo_fetch_new_reviews', array($this, 'fetchNewReviews'));
        add_action('wp_ajax_wiloke_listgo_fetch_new_reviews', array($this, 'fetchNewReviews'));

        add_action('wp_ajax_wiloke_fetch_listing', array($this, 'fetch_listing'));
        add_action('wp_ajax_nopriv_wiloke_fetch_listing', array($this, 'fetch_listing'));

        add_action('save_post', array($this, 'updateListingCaching'), 10, 2);
        add_action('before_delete_post', array($this, 'deleteListingCaching'), 10);
//        add_action('admin_init', array($this, 'putDataToRedis'));

        add_action('wp_ajax_wiloke_listgo_update_profile', array($this, 'updateProfile'));
        add_action('wp_ajax_wiloke_new_listing_management', array($this, 'fetchNewListingItemForManagenent'));
        add_action('wp_ajax_wiloke_listgo_temporary_closed_listing', array($this, 'temporaryClosedListing'));
        add_action('wp_ajax_wiloke_listgo_remove_listing', array($this, 'removeListing'));

        add_action('wp_ajax_wiloke_listgo_fetch_favorites', array($this, 'fetchNewFavoriteItems'));
        add_action('wp_ajax_wiloke_listgo_fetch_my_billings', array($this, 'fetchMyBillingHistory'));
        add_filter('get_avatar', array($this, 'filterAvatar'), 1, 3);
        add_filter('embed_oembed_html', array($this, 'removeVideoOutOfContent'), 99, 1);
    }


    public function init(){
        if ( is_admin() ){
            return false;
        }

        if ( is_user_logged_in() ){
            $userID = get_current_user_id();
            self::$oUserInfo = self::getUserMeta($userID);
            if ( Wiloke::$wilokePredis ){
               Wiloke::hSet(WilokeUser::$redisKey, $userID, self::$oUserInfo);
            }
            self::$oUserInfo = (object)self::$oUserInfo;
        }
    }

    public static function getColorByAnphabet($anphabet){
        global $wiloke;
        $anphabet = strtolower($anphabet);
        foreach ( $wiloke->aConfigs['frontend']['anphabets'] as $key => $aAnphabets ){
            $aAnphabets = explode(',', $aAnphabets);
            if ( in_array($anphabet, $aAnphabets) ){
                return $wiloke->aConfigs['frontend']['color_picker'][$key];
                break;
            }
        }

        return $wiloke->aConfigs['frontend']['color_picker'][$key];
    }

    public static function timezoneString($time=''){

        if ( !empty($time) ){

            if ( (strpos($time, 'UTC')  === false) ){
                return $time;
            }
            $time = str_replace(array('UTC', '+'), array('', ''), $time);

            if ( empty($time) ){
                return 'UTC';
            }

            $utc_offset = intval($time*3600);

            if ( $timezone = timezone_name_from_abbr( '', $utc_offset ) ) {
                return $timezone;
            }
        }else{
            // if site timezone string exists, return it
            if ( $timezone = get_option( 'timezone_string' ) ) {
                return $timezone;
            }

            // get UTC offset, if it isn't set then return UTC
            if ( 0 === ( $utc_offset = intval( get_option( 'gmt_offset', 0 ) ) ) ) {
                return 'UTC';
            }

            // adjust UTC offset from hours to seconds
            $utc_offset *= 3600;

            // attempt to guess the timezone string from the UTC offset
            if ( $timezone = timezone_name_from_abbr( '', $utc_offset ) ) {
                return $timezone;
            }
        }

        // last try, guess timezone string manually
        foreach ( timezone_abbreviations_list() as $abbr ) {
            foreach ( $abbr as $city ) {
                if ( (bool) date( 'I' ) === (bool) $city['dst'] && $city['timezone_id'] && intval( $city['offset'] ) === $utc_offset ) {
                    return $city['timezone_id'];
                }
            }
        }

        // fallback to UTC
        return 'UTC';
    }

    public static function businessStatus($post){
        $aPostTerms = Wiloke::getPostTerms($post, 'listing_location');
        $timeZone = '';

        if ( !empty($aPostTerms) && !is_wp_error($aPostTerms) ){
            if ( !isset($aPostTerms->errors) ){
                $aTermSetting = Wiloke::getTermOption($aPostTerms[0]->term_id);
                if ( isset($aTermSetting['timezone']) ){
                    $timeZone = $aTermSetting['timezone'];
                }
            }
        }

        date_default_timezone_set(self::timezoneString($timeZone));
        $aBusinessHours  = Wiloke::getPostMetaCaching($post->ID, 'wiloke_listgo_business_hours');
        $time = time();
        $today = date('N');
        $oNewDateTime = new DateTime();
        $today = absint($today) - 1;

        if ( $aBusinessHours[$today]['start_minutes'] == 0 ){
            $aBusinessHours[$today]['start_minutes'] = '00';
        }

        if ( $aBusinessHours[$today]['close_minutes'] == 0 ){
            $aBusinessHours[$today]['close_minutes'] = '00';
        }

        $opening = $aBusinessHours[$today]['start_hour'].':'.$aBusinessHours[$today]['start_minutes'] . ' ' . $aBusinessHours[$today]['start_format'];
        $closed  = $aBusinessHours[$today]['close_hour'].':'.$aBusinessHours[$today]['close_minutes'] . ' ' . $aBusinessHours[$today]['close_format'];
        $nextDayInfo = '';
        $closesInInfo = '';

        $currentTime = $oNewDateTime->setTimestamp($time);
        $startTime = DateTime::createFromFormat('h:i A', $opening);
        $endTime   = DateTime::createFromFormat('h:i A', $closed);
        $sixAM     = DateTime::createFromFormat('h:i A', '6:00:00 AM');

        $isSpecialEndTime = false;

        if ( isset($aBusinessHours[$today]['closed']) && !empty($aBusinessHours[$today]['closed']) ){
            $status = 'closed';
        }else{
            # We need to detect the format of closed time, it very important

            if ( ($aBusinessHours[$today]['close_format'] !== 'AM') || ( ($aBusinessHours[$today]['close_format'] === 'AM') && ($endTime > $sixAM) ) ){
                if ( ($currentTime < $startTime) || ($currentTime > $endTime) ){
                    $status = 'closed';
                }else{
                    $status = 'opening';
                }
            }else{
                $isSpecialEndTime = true;
                if ( ($currentTime > $endTime) && ($currentTime < $startTime) ){
                    $status = 'closed';
                }else{
                    $status = 'opening';
                }
            }
        }

        if ( $status === 'closed' ){
            if ( $isSpecialEndTime ){
                $nextDay = $today;
                $formatKey = 'close_format';
            }else{
                if ( $currentTime < $startTime ){
                    $nextDay = $today;
                }else{
                    $nextDay = $today == 6 ? 0 : absint($today) + 1;
                }
                $formatKey = 'start_format';
            }
            
            if ( isset($aBusinessHours[$nextDay]['start_hour']) && !isset($aBusinessHours[$nextDay]['closed']) ){
                $nextDayInfo = esc_html__('Opens at', 'listgo') . ' '. $aBusinessHours[$nextDay]['start_hour'].':'.$aBusinessHours[$nextDay]['start_minutes'] . ' ' . $aBusinessHours[$nextDay][$formatKey];
                $nextDayInfo = apply_filters('wiloke/listgo/admim/public/filterNextOpenIn', $nextDayInfo, $aBusinessHours, $nextDay);
                if ( !$isSpecialEndTime ){
                    if ( $currentTime < $startTime ){
                        $nextDayInfo .= ' ' . esc_html__('today', 'listgo');
                    }else{
                        $nextDayInfo .= ' ' . esc_html__('tomorrow', 'listgo');
                    }
                }else{
                    $nextDayInfo .= ' ' . esc_html__('today', 'listgo');
                }
            }
        }else{
            $oDiff = $endTime->diff($currentTime);
            if ( $oDiff->h >= 1 && $oDiff->h <= 2 ){
                $closesInInfo = esc_html__('Closes in', 'listgo') . ' ' . $oDiff->h . ' ' . esc_html__('hour', 'listgo') . ' ' . $oDiff->i . ' ' . esc_html__('minutes', 'listgo');
            }else if($oDiff->h == 0){
                $closesInInfo = esc_html__('Closes in', 'listgo') . ' ' . $oDiff->i . ' ' . esc_html__('minutes', 'listgo');
            }
        }

        return array(
            'status'       => $status,
            'nextdayinfo'  => $nextDayInfo,
            'closesininfo' => $closesInInfo
        );
    }

    /**
     * Render Listing status such as it's closed now or it's ads
     * @since 1.0
     */
    public static function renderListingStatus($post, $ignoreClosein = false){
        $isRender = apply_filters('wiloke/listgo/isRenderListingStatus', true, $post);
        if ( !$isRender ){
            return false;
        }
        $items = '';
        $aUser = Wiloke::getUserMeta($post->post_author);
        if ( $aUser === 'wiloke_submisison' ){
            $items .= '<span class="onads">'.esc_html__('Ad', 'listgo').'</span>';
        }

        $toggleBusinessHour = get_post_meta($post->ID, 'wiloke_toggle_business_hours', true);
        if ( isset($toggleBusinessHour) && $toggleBusinessHour === 'disable' ){
            return false;
        }

        $aBusinessStatus = self::businessStatus($post);

        if ( !empty($aBusinessStatus['closesininfo']) && !$ignoreClosein ){
            $items .= '<span class="closesin orange">'.$aBusinessStatus['closesininfo'].'</span>';
        }elseif($aBusinessStatus['status'] === 'opening'){
            $items .= '<span class="onopen green">'.esc_html__('Open now', 'listgo').'</span>';
        }else if ( $aBusinessStatus['status'] === 'closed' ){
            $items .= '<span class="onclose red">'.esc_html__('Closed now', 'listgo').'</span>';
            if ( !empty($aBusinessStatus['nextdayinfo']) ){
                $items .= '<span class="onopensin yellow">'.$aBusinessStatus['nextdayinfo'].'</span>';
            }
        }

        if ( empty($items) ){
            return '';
        }

        echo '<span class="ongroup">' . $items . '</span>';
    }

    public function addHiddenFilterForContactForm7OnListingPage($aFields){
        global $post;
        if ( isset($post->post_type) && $post->post_type === 'listing' ){
            $aFields = array(
                '_wiloke_post_author_email' => $post->ID
            );
        }
        return $aFields;
    }

    public function filterRecipientOfContactFormSeven($components, $aCurrent, $self){
        if ( isset($_POST['_wiloke_post_author_email']) && !empty($_POST['_wiloke_post_author_email']) ){
            $post = get_post(absint($_POST['_wiloke_post_author_email']));
            if ( !is_wp_error($post) && !empty($post) ){
                $aUser = Wiloke::getUserMeta($post->post_author);
                $components['recipient'] = $aUser['user_email'];
                $aClaimerInfo = WilokeFrontendClaimListing::getClaimerInfo($post);
                if ( isset($aClaimerInfo['user_email']) && !empty($aClaimerInfo['user_email']) ){
                    $components['recipient'] = $aClaimerInfo['user_email'];
                }
            }
        }

        return $components;
    }

    public static function checkAjaxSecurity($aData){
        if ( !isset($aData['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
            return false;
        }
        return true;
    }

    public function fetch_listing(){
        $postsPerPage = isset($_POST['posts_per_page']) && absint($_POST['posts_per_page']) <= 30 ? $_POST['posts_per_page'] : 30;

        $aArgs = array(
            'post_type'        => 'listing',
            'posts_per_page'   => absint($postsPerPage),
            'post__not_in'     => $_POST['post__not_in'],
            'post_status'      => 'publish'
        );

        if ( !empty($_POST['term']) && $_POST['term'] !== 'all' ){
            $aArgs['tax_query'] = array(
                array(
                    'taxonomy'  => $_POST['filter_by'],
                    'field'     => 'slug',
                    'terms'     => $_POST['term']
                )
            );
        }

        $query = new WP_Query($aArgs);

        $data = self::listingInfo($query);

        wp_send_json_success(array('content'=>$data));
    }

    public function loadmore_listing_layout(){
        if ( !check_ajax_referer('wiloke-nonce', 'security', false) ){
            wp_send_json_error();
        }

        $aData = $_POST;
        $hasCat = false;
        if ( !$aData['is_focus_query'] && (!isset($aData['post__not_in']) || empty($aData['post__not_in'])) ){
            wp_send_json_error();
        }

        if ( $aData['is_open_now'] === 'true' || $aData['is_highest_rated'] === 'true' || (!empty($aData['price_segment']) && ($aData['price_segment'] !== 'all')) ){
            $this->complexSearching($aData);
        }else{
            $postsPerPage = isset($aData['posts_per_page']) && absint($aData['posts_per_page']) <= 30 ? $aData['posts_per_page'] : 30;
            $orderBy = isset($aData['order_by']) ? $aData['order_by']  : 'post_date';

            $aArgs = array(
                'post_type'        => 'listing',
                'posts_per_page'   => intval($postsPerPage),
                'post__not_in'     => $aData['post__not_in'],
                'post_status'      => 'publish',
                'order_by'         => $orderBy,
                'order'            => 'DESC'
            );
            if ( !empty($aData['paged']) && ($aData['atts']['display_style'] === 'pagination') ){
                $aArgs['paged'] = $aData['paged'];
            }

            if ( empty($aData['filter_by']) || ($aData['filter_by'] == 'all') ){
                if ( ($aData['listing_locations'] !== 'all') && !empty($aData['listing_locations']) ){
                    if ( isset($aData['latLng']) && !empty($aData['latLng']) ){
                        $aLatLng = explode(',', $aData['latLng']);
                        $distance = isset($aData['sWithin']) ? abs($aData['sWithin']) :  5;
                        $unit = isset($aData['sUnit']) ? trim($aData['sUnit']) :  'km';
                        $aListingInRadius = GeoPosition::searchLocationWithin(trim($aLatLng[0]), trim($aLatLng[1]), $distance, $unit);
                        if ( empty($aListingInRadius) ){
                            wp_send_json_error();
                        }
                        $ignoreParseLocation = true;
                        $aArgs['post__in'] = $aListingInRadius['IDs'];
                    }

                    if ( !isset($ignoreParseLocation) ){
                        $aLocationData = self::parseLocationQuery($aData);
                        if ( empty($aLocationData) ){
                            wp_send_json_error();
                        }
                        $aArgs['tax_query'][] = $aLocationData;
                    }
                }

                if ( !empty($aData['listing_cats']) ){
                    $hasCat = true;
                    $aArgs['tax_query'][] = array(
                        'taxonomy'  => 'listing_cat',
                        'field'     => 'term_id',
                        'terms'     => is_array($aData['listing_cats']) ? array_map('absint', $aData['listing_cats']) : absint($aData['listing_cats'])
                    );
                }
            }else{
                $aArgs['tax_query'][] = array(
                    'taxonomy'  => $aData['filter_by'],
                    'field'     => 'term_id',
                    'terms'     =>  absint($aData['term'])
                );
            }

            if ( !empty($aData['listing_tags']) ){
                $hasCat = true;
                $aArgs['tax_query'][] = array(
                    'taxonomy'  => 'listing_tag',
                    'field'     => 'term_id',
                    'terms'     => is_array($aData['listing_tags']) ? array_map('absint', $aData['listing_tags']) : absint($aData['listing_tags'])
                );
            }

            if ( isset($aArgs['tax_query']) && !empty($aArgs['tax_query']) && (count($aArgs['tax_query']) > 1) ){
                $aArgs['tax_query']['relation'] = 'AND';
            }

            if ( !empty($aData['s']) && !$hasCat ){
                $aArgs['s'] = $aData['s'];
            }
            
            if ( isset($aArgs['paged']) && !empty($aArgs['paged']) ){ 
                unset($aArgs['post__not_in']);
            }

            $query = new WP_Query($aArgs);

            if ( $query->have_posts() ){
                global $wiloke;
                $mapPage = isset($aData['atts']['map_page']) && !empty($aData['atts']['map_page']) ? get_permalink($aData['atts']['map_page']) : get_permalink($wiloke->aThemeOptions['header_search_map_page']);

                ob_start();
                while ( $query->have_posts() ){
                    $query->the_post();
                    self::listingQuery($aData['atts'], $mapPage, true);
                }
                $content = ob_get_clean();
                wp_send_json_success(array('content'=>$content, 'type'=>'db', 'total'=>$query->found_posts));
            }else{
                wp_send_json_error();
            }
        }

    }

    public function complexSearching($aData){
        global $wpdb, $wiloke;
        $termRelationShipsTbl   = $wpdb->prefix . 'term_relationships';
        $businessHoursTbl       = $wpdb->prefix . WilokeBusinessHoursTbl::$tblName;
        $priceSegmentTbl        = $wpdb->prefix . WilokePriceSegmentTbl::$tblName;
        $postMetaTbl            = $wpdb->prefix . 'postmeta';
        $postsTbl               = $wpdb->prefix . 'posts';

        $select = "SELECT DISTINCT $postsTbl.ID";
        $join = "";
        $concat = " WHERE ";
        $conditional = "";
        $joinedTerm = false;

        if ( isset($aData['latLng']) && !empty($aData['latLng']) ){
            $aLatLng = explode(',', $aData['latLng']);
            $aLatLng = array_map('abs', $aLatLng);
            $distance = isset($aData['sWithin']) ? abs($aData['sWithin']) :  5;
            $unit = isset($aData['sUnit']) ? trim($aData['sUnit']) :  'km';
            $aListingInRadius = GeoPosition::searchLocationWithin($aLatLng[0], $aLatLng[1], $distance, $unit);
            if ( empty($aListingInRadius) ){
                wp_send_json_error();
            }

            $ignoreParseLocation = true;
            $aListingIDs[] = $aListingInRadius['IDs'];
        }

        if ( !isset($ignoreParseLocation) ){
            if ( !empty($aData['listing_locations']) ){
                $aParseLocation = self::parseLocationQuery($aData, true);
                if ( empty($aParseLocation) ){
                    wp_send_json_error();
                }
                $aData['listing_locations'] = $aParseLocation['terms'];
            }
        }
        if ( !empty($aData['listing_tags']) ){
            $aTagIDs = array_map('absint', $aData['listing_tags']);
            $postIDsInTags = "SELECT $termRelationShipsTbl.object_id FROM $termRelationShipsTbl WHERE $termRelationShipsTbl.term_taxonomy_id IN (".implode(',', $aTagIDs).")";
            $join .= " INNER JOIN $termRelationShipsTbl ON ($termRelationShipsTbl.object_ID = $postsTbl.ID)";
            $conditional .= $concat . "$postsTbl.ID IN(".$postIDsInTags. ")";
            $concat = " AND ";
            $joinedTerm = true;
        }

        if ( !empty($aData['listing_cats']) ){
            $aData['listing_cats'] = explode(',', $aData['listing_cats']);
            $aCatIDs = array_map('absint', $aData['listing_cats']);
            if ( $joinedTerm ){
                $join .= " INNER JOIN $termRelationShipsTbl ON ($termRelationShipsTbl.object_ID = $postsTbl.ID)";
            }
            $postIDsInCats = "SELECT $termRelationShipsTbl.object_id FROM $termRelationShipsTbl WHERE $termRelationShipsTbl.term_taxonomy_id IN (".implode(',', $aCatIDs).")";
            $conditional .= $concat . "$postsTbl.ID IN(".$postIDsInCats. ")";
            $concat = " AND ";
            $joinedTerm = true;
        }

        if ( !empty($aData['listing_locations']) ){
            if ( $joinedTerm ){
                $join .= " INNER JOIN $termRelationShipsTbl ON ($termRelationShipsTbl.object_ID = $postsTbl.ID)";
            }

            if ( !isset($ignoreParseLocation) ){
                if ( is_array($aData['listing_locations']) ){
                    $listingLocationID =  implode(',', $aData['listing_locations']);
                    $postIDsInLocation = "SELECT DISTINCT $termRelationShipsTbl.object_id FROM $termRelationShipsTbl WHERE $termRelationShipsTbl.term_taxonomy_id IN {$listingLocationID}";
                }else{
                    $listingLocationID =  absint($aData['listing_locations']);
                    $aLocationChildren = get_term_children($listingLocationID, 'listing_location');
                    if ( !empty($aLocationChildren) && !is_wp_error($aLocationChildren) ){
                        $postIDsInLocation = "SELECT DISTINCT $termRelationShipsTbl.object_id FROM $termRelationShipsTbl WHERE $termRelationShipsTbl.term_taxonomy_id IN (".implode(',', $aLocationChildren) . ")";
                    }else{
                        $postIDsInLocation = "SELECT DISTINCT $termRelationShipsTbl.object_id FROM $termRelationShipsTbl WHERE $termRelationShipsTbl.term_taxonomy_id = {$listingLocationID}";
                    }
                }

                $conditional .= $concat . "$postsTbl.ID IN(".$postIDsInLocation. ")";
                $concat = " AND ";
            }

            if ( $aData['is_open_now'] !== 'false' ){
                $aTermSetting = Wiloke::getTermOption($aData['listing_locations']);
                $timeZone = '';

                if ( isset($aTermSetting['timezone']) ){
                    $timeZone = $aTermSetting['timezone'];
                }

                date_default_timezone_set(self::timezoneString($timeZone));
                $now   = date('H:i:s');
                $today = date('N');

                $join .= " INNER JOIN $businessHoursTbl ON ($businessHoursTbl.post_ID = $postsTbl.ID)";
                $conditional .= $concat . $wpdb->prepare("( ($businessHoursTbl.always_open = %s) OR (day_of_week = %d AND  ( ( %s >= open_time AND close_time > %s AND close_time > %s) || ( (%s >= open_time || close_time > %s) AND (close_time < %s)) ) ) )",
                'yes', $today, $now, $now, '6:00:00', $now, $now, '6:00:00'
                );

                $concat = " AND ";
            }
        }

        if ( $aData['is_highest_rated'] !== 'false' ){
            $select .= ", $postMetaTbl.meta_value as average_rating";
            $join .= " INNER JOIN $postMetaTbl ON ($postMetaTbl.post_id = $postsTbl.ID)";
            $conditional .= $concat . $wpdb->prepare(
                "$postMetaTbl.meta_key=%s AND $postMetaTbl.meta_value >= %d",
                FrontendRating::$averageRatingMetaKey, 3
            );
            $concat = " AND ";
        }

        if ( $aData['price_segment'] !== 'all' ){
            $join .= " INNER JOIN $priceSegmentTbl ON ($priceSegmentTbl.post_ID = $postsTbl.ID)";
            $conditional .= $concat . $wpdb->prepare(
                "$priceSegmentTbl.segment=%s",
                $aData['price_segment']
            );
            $concat = " AND ";
        }

        if ( !empty($aData['post__not_in']) ){
            $aData['post__not_in'] = array_map('absint', $aData['post__not_in']);
            $conditional .=  $concat . "$postsTbl.ID NOT IN (".implode(',', $aData['post__not_in']) . ")";
            $concat = " AND ";
        }

        $select .= " FROM $postsTbl";

        if ( isset($ignoreParseLocation) ){
            $conditional .= $concat . $wpdb->prepare(
            "$postsTbl.post_type=%s AND $postsTbl.post_status=%s AND $postsTbl.ID IN (".implode(",", $aListingIDs) . ")",
                'listing', 'publish'
            );
        }else{
            $conditional .= $concat . $wpdb->prepare(
            "$postsTbl.post_type=%s AND $postsTbl.post_status=%s",
                'listing', 'publish'
            );
        }

        $postsPerPage = isset($aData['posts_per_page']) && absint($aData['posts_per_page']) <= 30 ? absint($aData['posts_per_page']) : 30;
        $conditional = trim($conditional) . " GROUP BY $postsTbl.ID";

        if ( !isset($ignoreParseLocation) ){
            $sqlCountTotal = "SELECT COUNT(DISTINCT $postsTbl.ID) FROM $postsTbl" . $join . " " . $conditional;
            $total = $wpdb->get_var($sqlCountTotal);
        }else{
            $total = count($aListingIDs);
        }

        $conditional .= $aData['is_highest_rated'] === 'true' ? " ORDER BY average_rating DESC" : " ORDER BY $postsTbl.post_date DESC";

        if ( !isset($ignoreParseLocation) ){
            $limit = " LIMIT " . $postsPerPage;
        }else{
            $limit = "";
        }
        $sqlMainQuery = $select . $join . " " . $conditional . $limit;

        $aResults = $wpdb->get_results($sqlMainQuery);

        if ( !empty($aResults) ){
            $mapPage = isset($aData['atts']['map_page']) && !empty($aData['atts']['map_page']) ? get_permalink($aData['atts']['map_page']) : get_permalink($wiloke->aThemeOptions['header_search_map_page']);
            ob_start();
            foreach ( $aResults as $oResult ){
                self::listingQuery($aData['atts'], $mapPage, true, $oResult->ID);
            }
            $content = ob_get_clean();
            wp_send_json_success(array('content'=>$content, 'type'=>'db', 'total'=>$total));
        }else{
            wp_send_json_error();
        }
    }

    public static function listingQuery($atts, $mapPage='', $isGetInfo=true, $postID=null){
        global $wiloke;
        $aInfo = $terms = array();
        if ( empty($postID) ){
            global $post;
            $postID = $post->ID;
        }else{
            $post = get_post($postID);
        }

        if ( !empty(self::$oListingCollections) && isset(self::$oListingCollections[$postID]) ){
            $aListing       = json_decode(self::$oListingCollections[$postID], true);
            $aFeaturedImage = $aListing['featured_image'];
            $aPageSettings  = $aListing['listing_settings'];
            $thumbnail      = get_the_post_thumbnail_url($postID);
            $terms          = $aListing['terms_id'];
            $aInfo          = $aListing;
        }else{
            if( !has_post_thumbnail($postID) || (!$aFeaturedImage = Wiloke::generateSrcsetImg(get_post_thumbnail_id($postID), $atts['image_size'])) ){
                if ( !empty($wiloke->aThemeOptions['listing_header_image']['id']) ){
                    $aFeaturedImage = Wiloke::generateSrcsetImg($wiloke->aThemeOptions['listing_header_image']['id'], $atts['image_size']);
                    if ( !$aFeaturedImage ){
                        $aFeaturedImage['main']['src'] = $wiloke->aThemeOptions['listing_header_image']['url'];
                        $aFeaturedImage['main']['width'] = $wiloke->aThemeOptions['listing_header_image']['width'];
                        $aFeaturedImage['main']['height'] = $wiloke->aThemeOptions['listing_header_image']['height'];
                        $aFeaturedImage['srcset'] = '';
                        $aFeaturedImage['sizes']  = '';
                    }
                }else{
                    $aFeaturedImage['main']['src']      = get_template_directory_uri() . '/img/featured-image.jpg';
                    $aFeaturedImage['main']['width']    = 1000;
                    $aFeaturedImage['main']['height']   = 500;
                    $aFeaturedImage['srcset']           = '';
                    $aFeaturedImage['sizes']            = '';
                }
            }
            $thumbnail     = get_the_post_thumbnail_url($postID);
            $aPageSettings = Wiloke::getPostMetaCaching($postID, 'listing_settings');

            if ( $atts['show_terms'] === 'listing_location' ){
                $terms = wp_get_post_terms($postID, 'listing_location', array('fields'=>'ids'));
            }elseif ( $atts['show_terms'] === 'listing_cat' ){
                $terms = wp_get_post_terms($postID, 'listing_cat', array('fields'=>'ids'));
            }else{
                $terms = wp_get_post_terms($postID, array('listing_location', 'listing_cat'), array('fields'=>'ids'));
            }

            if ( $isGetInfo ){
                $aListingLocation   = Wiloke::getPostTerms($post, 'listing_location');
                $aListingLocation   = self::putTermLinks($aListingLocation);

                $aListingListingCat = Wiloke::getPostTerms($post, 'listing_cat');
                $aListingListingCat = self::putTermLinks($aListingListingCat);

                $aListingTags       = Wiloke::getPostTerms($post, 'listing_tag');
                $aListingTags       = self::putTermLinks($aListingTags);

                $aListingLocationIDs    = self::getTermIDs($aListingLocation);
                $aListingListingCatIDs  = self::getTermIDs($aListingListingCat);
                $aListingTagIDs         = self::getTermIDs($aListingTags);
                $locationPlaceID = null;
                $firstLocationID = null;
                $parentLocation = null;

                if ( isset($aListingLocationIDs[0]) ){
                    $locationPlaceID = get_term_meta($aListingLocationIDs[0], 'wiloke_listing_location_place_id', true);
                    $parentLocation   = $aListingLocation[0]->parent;
                    $firstLocationID = $aListingLocationIDs[0];
                }

                $aTerms = array_merge($aListingLocationIDs, $aListingListingCatIDs, $aListingTagIDs);
                $favoriteClass = '';
                if ( class_exists('WilokeListGoFunctionality\AlterTable\AlterTableFavirote') ){
                    $favorite = AlterTableFavirote::getStatus($post->ID);
                    $favoriteClass = !empty($favorite) ? ' active' : '';
                }
                
                $aFirstCatInfo = array();
                if ( isset($aListingListingCat[0]) ){
                    $aFirstCatInfo = Wiloke::getTermOption($aListingListingCat[0]->term_id);
                }

                $aInfo = array(
                    'ID'            => $post->ID,
                    'link'          => get_permalink($post->ID),
                    'title'         => $post->post_title,
                    'is_featured_post' => get_post_meta($post->ID, 'wiloke_listgo_toggle_highlight', true),
                    'post_except'   => Wiloke::wiloke_content_limit(200, $post, false, $post->post_content, true),
                    'post_date'     => $post->post_date,
                    'business_status'=>self::businessStatus($post),
                    'post_date_with_format' => get_the_date($post->ID),
                    'average_rating'        => self::calculateAverageRating($post),
                    'featured_image'        => $aFeaturedImage,
                    'thumbnail'        => $thumbnail,
                    'favorite_class'   => $favoriteClass,
                    'author'           => self::getAuthorInfo($post->post_author, true),
                    'author_id'        => $post->post_author,
                    'comment_count'    => $post->comment_count,
                    'listing_settings' => $aPageSettings,
                    'terms_id'         => $aTerms,
                    'listing_location' => $aListingLocation,
                    'placeID'          => $locationPlaceID,
                    'first_location_id'=> $firstLocationID,
                    'parentLocationID' => $parentLocation,
                    'listing_cat'      => $aListingListingCat,
                    'listing_cat_settings' => $aFirstCatInfo,
                    'listing_tag'      => $aListingTags
                );
            }
        }

        $termClasses = !empty($terms) && !is_wp_error($terms) ? implode(' ', $terms) : '';

        if ( file_exists(get_template_directory() . '/admin/public/template/vc/listing-layout/'.$atts['layout'].'.php') ){
            include get_template_directory() . '/admin/public/template/vc/listing-layout/'.$atts['layout'].'.php';
        }else{
            include get_template_directory() . '/admin/public/template/vc/listing-layout/rest-layouts.php';
        }
    }

    public static function renderPriceSegment($post){
        $aPriceSettings = Wiloke::getPostMetaCaching($post->ID, 'listing_price');
        if ( empty($aPriceSettings) || empty($aPriceSettings['price_segment']) ){
            return false;
        }
        ?>
        <span class="listing__price">
            <label class="listing__price-label"><?php esc_html_e('Price:', 'listgo'); ?></label>
            <span class="listing__price-amount"><?php echo esc_html($aPriceSettings['price_from']); ?></span>
            <span class="listing__price-amount"><?php echo esc_html($aPriceSettings['price_to']); ?></span>
        </span>
        <?php
    }

    public static function renderFeaturedIcon($post){
        $isFeatured = get_post_meta($post->ID, 'wiloke_listgo_toggle_highlight', true);
        if ( !empty($isFeatured) ) :
        ?>
        <span class="onfeatued"><i class="fa fa-star-o"></i></span>
        <?php
        endif;
    }

    public static function putTermLinks($aTerms){
        if ( is_wp_error($aTerms) || empty($aTerms) ){
            return false;
        }

        foreach ( $aTerms as $key => $oTerm ){
            $oTerm = get_object_vars($oTerm);
            $oTerm['link'] = get_term_link($oTerm['term_id']);
            $oTerm = (object)$oTerm;
            $aTerms[$key] = (object)$oTerm;
            Wiloke::setTermsCaching('listing_location', array($oTerm->term_id=>$oTerm));
        }

        return $aTerms;
    }

    public static function getTermIDs($aTerms){
        $aTermIDs = array();
        if ( !is_wp_error($aTerms) && !empty($aTerms) ){
            foreach ( $aTerms as $oTerm ){
                $aTermIDs[] = $oTerm->term_id;
            }
        }

        return $aTermIDs;
    }

    public static function getSetting($pageKey, $tfoKey, $aPageSettings){
        if ( !isset($aPageSettings[$pageKey]) || ($aPageSettings[$pageKey] === 'inherit') ){
            global $wiloke;
            $aValue = $wiloke->aThemeOptions[$tfoKey];
            if ( isset($aValue['rgba']) ){
                return $aValue['rgba'];
            }

            return $aValue;
        }

        return $aPageSettings[$pageKey];
    }

    public static function renderMenu(){
        global $wiloke;
        $menu = $wiloke->aConfigs['frontend']['register_nav_menu']['menu'][0]['key'];
        if ( has_nav_menu($menu) ){
            wp_nav_menu($wiloke->aConfigs['frontend']['register_nav_menu']['config'][$menu]);
        }
    }

    public static function getAuthorInfo($authorID, $isHidePrivateInfo=false){
        $aAuthor['link'] = get_author_posts_url($authorID);
	    $aUserMeta = Wiloke::getUserMeta($authorID);
	    $avatar = Wiloke::getUserAvatar($authorID, $aUserMeta, array(35, 35));

	    $aAuthor['avatar'] = $avatar;
	    $aAuthor['nickname'] = $aUserMeta['display_name'];
	    $aAuthor['other'] = $aUserMeta;

	    if ( strpos($avatar,'profile-picture.jpg') !== false ){
	        $firstCharacter = strtoupper(substr($aAuthor['nickname'], 0, 1));
            $aAuthor['avatar_color'] = self::getColorByAnphabet($firstCharacter);
            $aAuthor['user_first_character'] = $firstCharacter;
	    }

	    if ( $isHidePrivateInfo ){
            unset($aAuthor['other']['first_name']);
            unset($aAuthor['other']['last_name']);
            unset($aAuthor['other']['rich_editing']);
            unset($aAuthor['other']['comment_shortcuts']);
            unset($aAuthor['other']['use_ssl']);
            unset($aAuthor['other']['use_ssl']);
            unset($aAuthor['other']['show_admin_bar_front']);
            unset($aAuthor['other']['locale']);
            unset($aAuthor['other']['wp_capabilities']);
            unset($aAuthor['other']['wp_user_level']);
            unset($aAuthor['other']['dismissed_wp_pointers']);
            unset($aAuthor['other']['show_welcome_panel']);
            unset($aAuthor['other']['session_tokens']);
            unset($aAuthor['other']['community-events-location']);
            unset($aAuthor['other']['wp_user-settings']);
            unset($aAuthor['other']['wp_user-settings-time']);
            unset($aAuthor['other']['wp_user-settings-time']);
            unset($aAuthor['other']['nav_menu_recently_edited']);
            unset($aAuthor['other']['managenav-menuscolumnshidden']);
            unset($aAuthor['other']['metaboxhidden_nav-menus']);
            unset($aAuthor['other']['default_password_nag']);
            unset($aAuthor['other']['wp_dashboard_quick_press_last_post_id']);
            unset($aAuthor['other']['metaboxhidden_nav-menus']);
	    }
	    return $aAuthor;
    }

    public static function getUserAvatar($userID=null, $size=null){
        $size = empty($size) ? array(65, 65) : $size;
        if ( !empty($userID) ){
            $aUserInfo = Wiloke::getUserMeta($userID);
        }else{
            $aUserInfo = get_object_vars(self::$oUserInfo);
        }

        $avatar = isset($aUserInfo['meta']['wiloke_profile_picture']) && !empty($aUserInfo['meta']['wiloke_profile_picture']) ? wp_get_attachment_image_url($aUserInfo['meta']['wiloke_profile_picture'], $size) : get_template_directory_uri() . '/img/profile-picture.jpg';
        return $avatar;
    }

	public static function renderAuthor($post, $atts=null){
        if ( !empty($atts) && $atts['toggle_render_author'] === 'disable' ){
            return false;
        }

	    $aAuthor = self::getAuthorInfo($post->post_author);
		?>
        <div class="listing__author <?php echo esc_attr(strpos($aAuthor['avatar'], 'profile-picture.jpg') ? 'listing__author--no-avatar' : '') ?>">
            <a href="<?php echo esc_url($aAuthor['link']); ?>">
                <?php
                if ( strpos($aAuthor['avatar'], 'profile-picture.jpg') === false ) {
                    ?>
                    <img src="<?php echo esc_url($aAuthor['avatar']); ?>" alt="<?php echo esc_attr($aAuthor['nickname']); ?>" height="65" width="65" class="avatar">
                    <?php
                } else {
                    $firstCharacter = strtoupper(substr($aAuthor['nickname'], 0, 1));
                    echo '<span style="background-color: '.esc_attr(self::getColorByAnphabet($firstCharacter)).'" class="widget_author__avatar-placeholder">'. esc_html($firstCharacter) .'</span>';
                }
                ?>
                <h6><?php echo esc_html($aAuthor['nickname']); ?></h6>
            </a>
        </div>
		<?php
	}

	public static function getRated($postID, $reviewID){
	    global $wpdb;
	    $tblName = $wpdb->prefix . AlterTableReviews::$tblName;

        return $wpdb->get_var(
	        $wpdb->prepare(
                "SELECT rating FROM $tblName WHERE post_ID=%d AND review_ID=%d",
                $postID, $reviewID
	        )
	    );
	}

    public static function renderContent($post, $atts=null){
        if ( empty($atts) ){
            $atts = array(
                'limit_character' => 100,
                'toggle_render_post_excerpt' => 'enable',
                'toggle_render_address' => 'enable'
            );
        }

	    $aListing = Wiloke::getPostMetaCaching($post->ID, 'listing_settings');
	    if ( !empty($aListing) ){
	        ?>
            <div class="listing__content">
                <?php
                if ( $atts['toggle_render_post_excerpt'] === 'enable' ){
                    echo '<p>';
                    Wiloke::wiloke_content_limit($atts['limit_character'], $post, false, $post->post_content, false);
                    echo '</p>';
                }

                if ( $atts['toggle_render_address'] === 'enable' ) :
                ?>
                    <div class="address">
                        <?php
                            if ( isset($aListing['map']['location']) && !empty($aListing['map']['location']) ){
                                echo '<span class="address-location"><strong>'.esc_html__('Location', 'listgo').':</strong> ' . $aListing['map']['location'] . '</span>';
                            }

                            if ( isset($aListing['website']) && !empty($aListing['website']) ){
                                echo '<span class="address-website"><strong>'.esc_html__('Website', 'listgo').':</strong> ' . '<a target="_blank" href="'.esc_url($aListing['website']).'">'.$aListing['website'].'</a>' . '</span>';
                            }

                            if ( isset($aListing['phone_number']) && !empty($aListing['phone_number']) ){
                                echo '<span class="address-phone_number"><strong>'.esc_html__('Phone', 'listgo').':</strong> <a href="tel:'.esc_attr($aListing['phone_number']).'">'.$aListing['phone_number'].'</a></span>';
                            }
                        ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        }
    }

	public static function calculateAverageRating($post){
	    global $wpdb;
	    if ( !class_exists('WilokeListGoFunctionality\AlterTable\AlterTableReviews') ){
	        return 0;
	    }
	    $tblName = $wpdb->prefix . AlterTableReviews::$tblName;
	    $average = $wpdb->get_var(
	        $wpdb->prepare(
                "SELECT AVG(rating) FROM $tblName WHERE post_ID=%d",
                $post->ID
	        )
	    );

	    return number_format(round($average, 1), 1, '.', '');
	}

	public static function renderAverageRating($post, $atts=null){
	    if ( !empty($atts) && $atts['toggle_render_rating'] === 'disable' ){
            return false;
        }
	    $average = self::calculateAverageRating($post);
        ?>
        <div class="listgo__rating">
            <span class="rating__star">
                <?php for ( $i = 1; $i <= 5; $i++) : ?>
                    <i class="<?php echo esc_attr(self::getStarClass($average, $i)); ?>"></i>
                <?php endfor; ?>
            </span>
            <span class="rating__number"><?php echo esc_html($average); ?></span>
        </div>
        <?php
	}

    public static function renderFavorite($post, $atts=null, $additionalClass=''){
        $itemClass = 'tb__cell ' . $additionalClass;
        $itemClass = trim($itemClass);

        $text = esc_html__('Save', 'listgo');
        if ( !empty($atts) ){
            if ( $atts['toggle_render_favorite'] === 'disable' ){
                return false;
            }

            $text = !empty($atts['favorite_description']) ? $atts['favorite_description'] : $text;
        }
        $favoriteClass = 'js_favorite';

	    if ( class_exists('WilokeListGoFunctionality\AlterTable\AlterTableFavirote') ){
		    $favorite = AlterTableFavirote::getStatus($post->ID);
		    $favoriteClass .= !empty($favorite) ? ' active' : '';
	    }

        ?>
        <div class="<?php echo esc_attr($itemClass); ?>">
            <a href="#" class="<?php echo esc_attr($favoriteClass); ?>" data-postid="<?php echo esc_attr($post->ID); ?>" data-tooltip="<?php echo esc_attr($text); ?>">
                <i class="icon_heart_alt"></i>
            </a>
        </div>
        <?php
    }

    public static function showTermInGrid($aTerms){
        if ( !empty($aTerms) && !is_wp_error($aTerms) ) :
        ?>
        <div class="listing__cat">
            <a href="<?php echo esc_url(get_term_link($aTerms[0]->term_id)); ?>"><?php echo esc_html($aTerms[0]->name); ?></a>
            <?php
            unset($aTerms[0]);
            if ( !empty($aTerms) ) :
            ?>
            <span class="listing__cat-more">+</span>
            <ul class="listing__cats">
                <?php foreach ( $aTerms as $oTerm ) : ?>
                <li>
                    <a href="<?php echo esc_url(get_term_link($oTerm->term_id)); ?>"><?php echo esc_html($oTerm->name); ?></a>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
        <?php
        endif;
    }

    public function searchSuggestion(){
        $aData = $_GET;

        if ( !isset($aData['security']) || !check_ajax_referer('wiloke-nonce', 'security', true) ){
            wp_send_json_error(array('message'=>esc_html__('Something went wrong', 'listgo')));
        }

        if ( !isset($aData['s']) || empty($aData['s']) ){
            wp_send_json_error(array('message'=>esc_html__('Please enter in your destination', 'listgo')));
        }

        $aArgs = array(
            's'                 => $aData['s'],
            'posts_per_page'    => 5,
            'post_type'         => 'listing',
            'post_status'       => 'publish'
        );

        $aTaxQuery = array();
        $nothingFoundText = esc_html__('Nothing found. Please try another keyword.', 'listgo');

        if ( isset($aData['listing_locations']) && !empty($aData['listing_locations']) ){
            $maybeSearch = $aData['listing_locations'];
            $aData['listing_locations'] = isset($aData['location_term_id']) && !empty($aData['location_term_id']) ? $aData['location_term_id'] : $aData['listing_locations'];

            $aLocationArgs = self::parseLocationQuery($aData);
            if ( empty($aLocationArgs) ){
                wp_send_json_error(array('message'=>$nothingFoundText));
            }else{
                $aTaxQuery[] = $aLocationArgs;
            }
        }

        if ( isset($aData['listing_cats']) && !empty($aData['listing_cats']) && ($aData['listing_cats'] !== 'all') ){
            $aTaxQuery[] = array(
                'taxonomy' => 'listing_cat',
                'field'    => 'term_id',
                'terms'    => $aData['listing_cats']
            );
        }

        if ( isset($aData['listing_tags']) && !empty($aData['listing_tags']) && ($aData['listing_tags'] !== 'all') ){
            $aTaxQuery[] = array(
                'taxonomy' => 'listing_tag',
                'field'    => 'term_id',
                'terms'    => $aData['listing_tags']
            );
        }

        if ( empty($aTaxQuery) && Wiloke::$wilokePredis && Wiloke::$wilokePredis->exists(Wiloke::$prefix."listing_titles") ){
            $pattern = '*'.trim(strtolower($aData['s'])).'*';
            $aInfo = array();
            foreach (new Iterator\SetKey(Wiloke::$wilokePredis, Wiloke::$prefix."listing_titles", $pattern , 1000) as $listingID){
                $listingID = end(explode('|', $listingID));
                $listingID = absint($listingID);
                if ( !empty($listingID) ){
                    $post = json_decode(Wiloke::$wilokePredis->hGet(Wiloke::$prefix."listing|$listingID", 'post_data'), true);
                    $post['post_type'] = 'listing';
                    $post = (object)$post;
                    $aInfo[$listingID] = self::createListingInfo($post);
                }
            }
            if ( !empty($aInfo) ){
                wp_send_json_success($aInfo);
            }else{
               wp_send_json_error(array('message'=>$nothingFoundText));
            }
        }else{
            if ( !empty($aTaxQuery) ){ 
                if ( count($aTaxQuery) > 1 ){ 
                    $aArgs['tax_query']['relation'] = 'AND';
                }
                $aArgs['tax_query'][] = $aTaxQuery;
            }

            $query = new WP_Query($aArgs);

            $aInfo = array();

            if ( $query->have_posts() ){
                while($query->have_posts()){
                    $query->the_post();
                    $aInfo[$query->post->ID] = self::listingInfo($query->post);
                }
                wp_send_json_success($aInfo);
            }else{
                wp_send_json_error(array('message'=>$nothingFoundText));
            }
        }
    }

    public function render_sharing_box()
    {
        if ( class_exists('WilokeSharingPost') )
        {
            echo do_shortcode('[wiloke_sharing_post]');
        }
    }

	public static function render_attributes($aAttributes){
        if ( empty($aAttributes) ){
            return '';
        }

		$atts = '';
		foreach ( $aAttributes as $key => $val ) {
			$atts .= esc_attr($key) . '="' . esc_attr($val) . '" ';
		}

		echo trim($atts);
	}

    public function solve_stupid_idea_of_wordpressdotcom($aFields)
    {
        if ( !empty(self::$oUserInfo) )
        {
            return $aFields;
        }

        $aComments = $aFields['comment'];
        unset($aFields['comment']);

        return (array)$aFields + (array)$aComments;
    }

    public function loadmore_map(){
        if ( check_ajax_referer('wiloke-nonce', 'security', false) ){
            if ( !isset($_POST['post__not_in']) || empty($_POST['post__not_in']) || !isset($_POST['atts']) || empty($_POST['atts']) ){
                wp_send_json_error();
            }

            $aPostNotIn = array_map('absint', $_POST['post__not_in']);
            $aAtts = $_POST['atts'];
            $aAtts['post__not_in'] = $aPostNotIn;
            $aNewListing = WilokePublic::getMap($aAtts);
            if ( empty($aNewListing) ){
                wp_send_json_error();
            }
            wp_send_json_success($aNewListing);
        }
    }

    public static function hasLocation($postID){
        $aMapSettings = Wiloke::getPostMetaCaching($postID, 'listing_settings');
        if ( empty($aMapSettings) || empty($aMapSettings['map']) || empty($aMapSettings['map']['latlong']) ){
            return false;
        }
        return true;
    }

    public static function getMap($aAtts, $isFocusSearch=false){
        $aListings = array();
        if ( $isFocusSearch && !empty($aAtts['s']) ){
            $aArgs = array(
                'posts_per_page'    => 1,
                'post_type'         => 'listing',
                's'                 => $aAtts['s'],
                'post_status'       => 'publish'
            );
            $query = new WP_Query($aArgs);
            if ( $query->have_posts() ){
                while ($query->have_posts()){
                    $query->the_post();
                    if ( self::hasLocation($query->post->ID) ){
                        $aListings[] = self::createListingInfo($query->post);
                    }
                }
                wp_reset_postdata();
            }

            return $aListings;
        }

        if ( Wiloke::$wilokePredis && Wiloke::$wilokePredis->exists(Wiloke::$prefix."listing_ids") && ($aAtts['source_map']==='all') ){
            $cursor = isset($aAtts['cursor']) ? absint($aAtts['cursor']) : 0;
            $aListingIDs = Wiloke::$wilokePredis->sscan(Wiloke::$prefix."listing_ids", $cursor, array('COUNT'=>100000));
            if ( isset($aListingIDs[1]) ){
                foreach ( $aListingIDs[1] as $listingID ){
                    if ( isset($aAtts['post__not_in']) && !empty($aAtts['post__not_in']) && in_array(absint($listingID), $aAtts['post__not_in']) ){
                        continue;
                    }
	                $post = json_decode(Wiloke::$wilokePredis->hGet(Wiloke::$prefix."listing|$listingID", 'post_data'), true);
	                $post['post_type'] = 'listing';
	                $post = (object)$post;

	                if ( self::hasLocation($post->ID) ){
	                    $aListings[] = self::createListingInfo($post);
	                }
                }
            }
        }else{
            $aArgs = array(
                'posts_per_page'    => 10,
                'post_type'         => 'listing',
                'orderby'           => 'post_date',
                'post_status'       => 'publish'
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

            $aTaxQuery = array();
            if ( !empty($aAtts['s_current_cat']) || !empty($aAtts['s_current_location']) ){
                if ( !empty($aAtts['s_current_cat']) ){
                    $aTaxQuery[] = array(
                        'taxonomy' => 'listing_cat',
                        'field'    => 'term_id',
                        'terms'    => $aAtts['s_current_cat']
                    );
                }

                if ( !empty($aAtts['s_current_tag']) ){
                    $aTaxQuery[] = array(
                        'taxonomy' => 'listing_tag',
                        'field'    => 'term_id',
                        'terms'    => $aAtts['s_current_tag']
                    );
                }

                if ( isset($aAtts['s_current_location']) && !empty($aAtts['s_current_location']) ){
                    $aAtts['listing_locations'] = $aAtts['s_current_location'];
                    $aTaxQuery[] = self::parseLocationQuery($aAtts);
                }

                if ( !empty($aTaxQuery) ){
                    $aTaxQuery['relation'] = 'AND';
                    $aArgs['tax_query'] = $aTaxQuery;
                }
            }else{
                if ( $aAtts['source_map'] === 'listing_cat' ){
                    $aArgs['tax_query'] = array(
                        array(
                            'taxonomy' => 'listing_cat',
                            'field'    => 'term_id',
                            'terms'    => $aAtts['listing_cat_ids']
                        )
                    );
                }elseif( $aAtts['listing_location'] && ($aAtts['listing_location'] !== 'all') ){
                    $aArgs['tax_query'] = array(
                        array(
                            'taxonomy' => 'listing_location',
                            'field'    => 'term_id',
                            'terms'    => $aAtts['listing_location_ids']
                        )
                    );
                }
            }

            if ( isset($aAtts['post__not_in']) ){
                $aArgs['post__not_in'] = $aAtts['post__not_in'];
            }

            $query = new WP_Query($aArgs);
            $aListings = array();

            if ( $query->have_posts() ){
                while ($query->have_posts()){
                    $query->the_post();
                    if ( self::hasLocation($query->post->ID) ){
                        $aListings[] = self::createListingInfo($query->post);
                    }
                }
                wp_reset_postdata();
            }
        }
	    return $aListings;
    }

    public static function createListingInfo($post){
        $parentLocation = $firstLocationID = $locationPlaceID = null;
        $aListingCats        = Wiloke::getPostTerms($post, 'listing_cat');
        $aListingLocations   = Wiloke::getPostTerms($post, 'listing_location', true);
        $aTags               = Wiloke::getPostTerms($post, 'listing_tag');
        $aListingCatIDs      = array();
        $aListingLocationIDs = array();
        $aTagIDs             = array();
        foreach ( $aListingCats as $key => $oTerm ){
            $oTerm = get_object_vars($oTerm);
            $oTerm['link'] = get_term_link($oTerm['term_id']);
            $oTerm = (object)$oTerm;
            $aListingCats[$key] = (object)$oTerm;
            Wiloke::setTermsCaching('listing_cat', array($oTerm->term_id=>$oTerm));
            $aListingCatIDs[] = $oTerm->term_id;
        }

        foreach ( $aListingLocations as $key => $oTerm ){
            $oTerm = get_object_vars($oTerm);
            $oTerm['link'] = get_term_link($oTerm['term_id']);
            $oTerm = (object)$oTerm;
            $aListingLocations[$key] = (object)$oTerm;
            Wiloke::setTermsCaching('listing_location', array($oTerm->term_id=>$oTerm));
            $aListingLocationIDs[] = $oTerm->term_id;
        }

        foreach ( $aTags as $key => $oTerm ){
            $oTerm = get_object_vars($oTerm);
            $oTerm['link'] = get_term_link($oTerm['term_id']);
            $oTerm = (object)$oTerm;
            $aTags[$key] = (object)$oTerm;
            Wiloke::setTermsCaching('listing_tag', array($oTerm->term_id=>$oTerm));
            $aTagIDs[] = $oTerm->term_id;
        }

        $favorite = 0;
        if ( class_exists('WilokeListGoFunctionality\AlterTable\AlterTableFavirote') ){
            $favorite = AlterTableFavirote::getStatus($post->ID);
        }

        $aFirstCatInfo = array();
        if ( isset($aListingCatIDs[0]) ){
            $aFirstCatInfo = Wiloke::getTermOption($aListingCatIDs[0]);
        }

        $authorID = isset($post->post_author) ? $post->post_author : $post->author_id;
        if ( isset($aListingLocationIDs[0]) ){
            $locationPlaceID = get_term_meta($aListingLocationIDs[0], 'wiloke_listing_location_place_id', true);
            $parentLocation = $aListingLocations[0]->parent;
            $firstLocationID = $aListingLocationIDs[0];
        }

        $aListing = array(
            'ID'                    => $post->ID,
            'title'                 => isset($post->post_title) ? $post->post_title : $post->title,
            'link'                  => get_permalink($post->ID),
            'featured_image'        => get_the_post_thumbnail_url($post->ID, 'medium'),
            'thumbnail'             => get_the_post_thumbnail_url($post->ID, 'thumbnail'),
            'listing_settings'      => Wiloke::getPostMetaCaching($post->ID, 'listing_settings'),
            'listing_cat'           => $aListingCats,
            'average_rating'        => self::calculateAverageRating($post),
            'tag'                   => $aTags,
            'listing_location'      => $aListingLocations,
            'listing_cat_id'        => $aListingCatIDs,
            'listing_location_id'   => $aListingLocationIDs,
            'first_cat_info'        => $aFirstCatInfo,
            'placeID'               => $locationPlaceID,
            'first_location_id'     => $firstLocationID,
            'parentLocationID'      => $parentLocation,
            'tag_id'                => $aTagIDs,
            'author'                => self::getAuthorInfo($authorID, true),
            'listing_cat_settings'  => $aFirstCatInfo,
            'business_status'       => self::businessStatus($post),
            'favorite'              => $favorite
        );

        return $aListing;
    }

    public static function searchForm($mapPage=null, $isShowAdvancedFilter=false, $atts=array(), $currentlySearchOn=null){
        global $wiloke, $post;
        $inputName = 's_search';
        $pageID = '';
        if ( empty($mapPage) ){
            if ( !empty($wiloke->aThemeOptions) ){
                if ( $wiloke->aThemeOptions['listing_search_page'] === 'self' ){
                    $mapPage = get_permalink($post->ID);
                    $pageID = $post->ID;
                }else{
                    if ( isset($wiloke->aThemeOptions['header_search_map_page']) && !empty($wiloke->aThemeOptions['header_search_map_page']) ){
                         $mapPage = get_permalink($wiloke->aThemeOptions['header_search_map_page']);
                         $pageID = $wiloke->aThemeOptions['header_search_map_page'];
                    }else{
                        $mapPage =  home_url('/');
                        $inputName = 's';
                    }
                }
            }else{
                $mapPage =  home_url('/');
                $inputName = 's';
            }
        }

        $aLocations = get_terms(
            array(
                'taxonomy'    => 'listing_location',
                'hide_empty'  => 1,
                'number'      => 10
            )
        );

        $aCategories = Wiloke::getTaxonomyHierarchy('listing_cat');

        $searchFieldTitle = isset($atts['search_field_title']) && !empty($atts['search_field_title']) ? $atts['search_field_title'] : esc_attr($wiloke->aThemeOptions['header_search_keyword_label']);
        $locationFieldTitle = isset($atts['location_field_title']) && !empty($atts['location_field_title']) ? $atts['location_field_title'] : esc_attr($wiloke->aThemeOptions['header_search_location_label']);

        $s = isset($_REQUEST['s_search']) ? $_REQUEST['s_search'] : get_search_query();
        $s = isset($_REQUEST['cache_previous_search']) && !empty($_REQUEST['cache_previous_search']) ? $_REQUEST['cache_previous_search'] : $s;

        $catID = '';
        if ( isset($_REQUEST['s_listing_cat']) ){
            $catID = $_REQUEST['s_listing_cat'][0];
        }elseif ( is_tax('listing_cat') ){
            $catID = get_queried_object()->term_id;
            $s = single_term_title('', false);
        }

        $sWithinRadius = $wiloke->aThemeOptions['listgo_search_default_radius'];
        if ( isset($_REQUEST['s_within_radius']) && !empty($_REQUEST['s_within_radius']) ){
           $sWithinRadius =  trim($_REQUEST['s_within_radius']);
        }

        $sUnit = 'km';
        if ( isset($_REQUEST['s_unit']) && !empty($_REQUEST['s_unit']) ){
           $sUnit = trim($_REQUEST['s_unit']);
        }
        $maxRadius = !isset($wiloke->aThemeOptions['listgo_search_max_radius']) ? 20 : $wiloke->aThemeOptions['listgo_search_max_radius'];
        $minRadius = !isset($wiloke->aThemeOptions['listgo_search_min_radius']) ? 1 : $wiloke->aThemeOptions['listgo_search_min_radius'];

        ?>
        <form action="<?php echo esc_url($mapPage); ?>" method="GET" id="listgo-searchform" class="form form--listing <?php echo esc_attr($currentlySearchOn); ?>">
            <?php if ( isset($atts['alignment']) && ( $atts['alignment'] === 'not_center' || $atts['alignment'] === 'not_center_2' || $atts['alignment'] === 'not_center_3') && !empty($atts['search_form_title']) ) : ?>
                <h3 class="wiloke-searchform-title"><?php echo esc_html($atts['search_form_title']); ?></h3>
            <?php endif; ?>
            <input type="hidden" name="page_id" value="<?php echo esc_attr($pageID); ?>">
            <div class="form-item item--search">
                <label for="<?php echo esc_attr($inputName); ?>" class="label"><?php echo esc_html($searchFieldTitle); ?></label>
                <span class="input-text input-icon-inside">
                    <input id="<?php echo esc_attr($inputName); ?>" type="text" name="<?php echo esc_attr($inputName); ?>" value="<?php echo esc_attr(stripslashes($s)); ?>">
                    <?php
                    if ( !empty($aCategories) && !is_wp_error($aCategories) ) :
                    ?>
                    <input type="hidden" id="wiloke-original-search-suggestion" value="<?php echo esc_attr(json_encode($aCategories)); ?>">
                    <input type="hidden" id="s_listing_cat" name="s_listing_cat[]" value="<?php echo esc_attr($catID); ?>">
                    <?php endif; ?>
                    <i class="input-icon icon_search"></i>
                    <input type="hidden" id="cache_previous_search" name="cache_previous_search" value="">
                </span>
            </div>
            <?php
            if ( !empty($aLocations) && !is_wp_error($aLocations) ){
                $aLocations = json_encode($aLocations);
            }else{
              $aLocations = '';
            }
            self::renderLocationField($locationFieldTitle, $aLocations);
            ?>
            <?php if ( $isShowAdvancedFilter ) : ?>
            <div class="form-item item--toggle-opennow">
                <label for="s_opennow" class="checkbox-btn">
                    <input id="s_opennow" type="checkbox" name="s_opennow" value="1">
                    <span class="checkbox-btn-span"><i class="fa fa-clock-o"></i><?php esc_html_e('Open Now', 'listgo'); ?></span>
                </label>
            </div>
            <div class="form-item item--toggle-highestrated">
                <label for="s_highestrated" class="checkbox-btn">
                    <input id="s_highestrated" type="checkbox" name="s_highestrated" value="1">
                    <span class="checkbox-btn-span"><i class="fa fa-star-o"></i><?php esc_html_e('Highest Rated', 'listgo'); ?></span>
                </label>
            </div>

            <div class="form-item item--price">
                <?php
                    $aPriceSegments = array_merge(
                        array('all' => esc_html__('Cost - It doesn\'t matter', 'listgo')),
                        $wiloke->aConfigs['frontend']['price_segmentation']
                    );
                ?>
                <span class="input-select2 input-icon-inside">
                    <select id="s_price_segment" name="s_price_segment" class="js_select2" data-placeholder="<?php echo esc_html__('Cost', 'listgo'); ?>">
                        <?php
                        foreach ( $aPriceSegments as $price => $name ) :
                            $name = isset($wiloke->aThemeOptions['header_search_'.$price.'_cost_label']) ? $wiloke->aThemeOptions['header_search_'.$price.'_cost_label'] : $name;
                        ?>
                            <option value="<?php echo esc_attr($price); ?>"><?php echo esc_html($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </span>
            </div>
            <div class="form-item item--radius">
                <div class="listgo-unit-wrapper">
                    <label class="label" for="s_unit"><?php esc_html_e('Radius', 'listgo'); ?></label>
                    <div class="listgo-unit">
                        <i class="arrow_carrot-down"></i>
                        <select id="s_unit" name="s_unit">
                            <option value="km" <?php selected($sUnit, 'km'); ?>><?php esc_html_e('Kilometer', 'listgo'); ?></option>
                            <option value="mi" <?php selected($sUnit, 'mi'); ?>><?php esc_html_e('Mile', 'listgo'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="input-slider" data-max-radius="<?php echo esc_attr($maxRadius); ?>" data-min-radius="<?php echo esc_attr($minRadius); ?>" data-current-radius="<?php echo esc_attr($sWithinRadius); ?>">
                    <input id="s_radius" name="s_radius" type="hidden" value="<?php echo esc_attr($sWithinRadius); ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php do_action('wiloke/listgo/admin/public/search-form'); ?>

            <div class="form-item item--submit">
                <input type="submit" value="<?php esc_html_e('Search Now', 'listgo'); ?>" />
            </div>
        </form>
        <?php
    }
    
    public static function renderLocationField($locationFieldTitle, $aLocations){
        $termID = '';
        $termName = '';
        if ( isset($_REQUEST['location_term_id']) ){
            $termID = $_REQUEST['location_term_id'];
            $termName = $_GET['s_listing_location'];
        }elseif ( is_tax('listing_location') ){
            $termID = get_queried_object()->term_id;
            $termName = single_term_title('', false);
        }
        ?>
        <div class="form-item item--localtion">
            <label for="s_listing_location" class="label"><?php echo esc_html($locationFieldTitle); ?></label>
            <span class="input-text input-icon-inside">
                <input type="text" id="s_listing_location" class="auto-location-by-google" name="s_listing_location" value="<?php echo esc_attr($termName); ?>">
                <input type="hidden" id="s-listing-location-suggestion" value="<?php echo esc_attr($aLocations); ?>">
                <input type="hidden" id="s-location-place-id" name="location_place_id" value="<?php echo isset($_REQUEST['location_place_id']) ? esc_attr($_REQUEST['location_place_id']) : ''; ?>">
                <input type="hidden" id="s-location-latitude-longitude-id" name="location_latitude_longitude" value="<?php echo isset($_REQUEST['location_latitude_longitude']) ? esc_attr($_REQUEST['location_latitude_longitude']) : ''; ?>">
                <input type="hidden" id="s-location-term-id" name="location_term_id" <?php echo esc_attr($termID); ?>>
                <i class="input-icon icon_pin_alt"></i>
            </span>
        </div>
        <?php
    }
    
    public static function renderBreadcrumb() { ?>
        <?php if ( !is_home() && !is_front_page() ) : ?>
        <div class="header-page__breadcrumb">
            <div class="container">
                <ol class="wo_breadcrumb">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'listgo'); ?></a></li>
                    <?php if ( is_author() ) : ?>
                    <li><span><?php echo esc_html(get_query_var('author_name')); ?></span></li>
                    <?php
                        elseif(is_tax() || is_archive() || is_search()) : $oTerm = get_queried_object();
                        if ( !empty($oTerm) ) :
                        ?>
                        <?php if ( !empty($oTerm->parent) ) : $oParent = Wiloke::getTermCaching($oTerm->taxonomy, $oTerm->parent); ?>
                            <li><a href="<?php echo esc_url($oParent->link); ?>"><?php echo esc_html($oParent->name); ?></a></li>
                        <?php endif; ?>
                        <li><span><?php echo esc_html($oTerm->name); ?></span></li>
                        <?php
                        else:
                            $title = is_search() ? esc_html__('Search', 'listgo') : get_the_archive_title();
                            if ( !empty($title) ) :
                        ?>
                            <li><span><?php echo esc_html($title); ?></span></li>
                        <?php endif; endif; ?>
                    <?php elseif ( is_page_template() ) : global $post; ?>
                    <li><span><?php echo esc_html($post->post_title); ?></span></li>
                    <?php elseif ( is_singular() && !is_page() ) : ?>
                        <?php  global $post; $taxName = $post->post_type === 'listing' ? 'listing_location' : 'category'; $oTerm = Wiloke::getPostTerms($post, $taxName); ?>
                        <?php if ( !empty($oTerm) && !is_wp_error($oTerm) ) : ?>
                            <?php if ( !empty($oTerm->parent) ) : $oParent = Wiloke::getTermCaching($taxName, $oTerm->term_id); ?>
                                <li><a href="<?php echo esc_url($oParent->link); ?>"><?php echo esc_html($oParent->name); ?></a></li>
                            <?php endif; ?>
                            <li><a href="<?php echo esc_url($oTerm->link); ?>"><?php echo esc_html($oTerm->name); ?></a></li>
                        <?php endif; ?>
                            <li><span><?php echo esc_html($post->post_title); ?></span></li>
                    <?php endif; ?>
                </ol>
                <span class="header-page__breadcrumb-filter"><i class="fa fa-filter"></i> <?php echo esc_html__('Filter', 'listgo'); ?></span>
            </div>
        </div>
        <?php endif;
    }

    public static function getHeaderSettingFromTerm($oObject){
        global $wiloke;
        $aTaxSettings = Wiloke::getTermOption($oObject->term_id);
        if ( isset($aTaxSettings['featured_image']) && !empty($aTaxSettings['featured_image']) ){
            $headerURL = wp_get_attachment_image_url($aTaxSettings['featured_image'], 'large');
        }elseif(isset($wiloke->aThemeOptions['listing_header_image']['id'])){
            $headerURL = wp_get_attachment_image_url($wiloke->aThemeOptions['listing_header_image']['id'], 'large');
        }elseif(isset($wiloke->aThemeOptions['listing_header_image']['url'])){
            $headerURL = $wiloke->aThemeOptions['listing_header_image']['url'];
        }

        if ( isset($aTaxSettings['header_overlay']) && !empty($aTaxSettings['header_overlay']) ){
            $overlayColor = $aTaxSettings['header_overlay'];
        }else{
            $overlayColor = !empty($wiloke->aThemeOptions['listing_header_overlay']) ? $wiloke->aThemeOptions['listing_header_overlay']['rgba'] : '';
        }

        return array(
            'headerurl' => $headerURL,
            'overlaycolor' => $overlayColor
        );
    }

    public static function headerPage(){
        global $post, $wiloke;
        $headerURL = null; $overlayColor = '';
        $postTitle = '';
        $desc = '';

        if ( is_home() || is_front_page() ) {
            $postTitle = get_option('blogname');
            $aPageSettings = Wiloke::getPostMetaCaching($post->ID, 'page_settings');
            if (!isset($aPageSettings['toggle_header_image']) || ($aPageSettings['toggle_header_image'] === 'disable') ){
                return false;
            }
        }else if ( is_search() ){
            $postTitle = esc_html__('Search results for: ', 'listgo') . get_query_var('s');
        }else if ( is_category() ) {
            $oObject = get_queried_object();
            $postTitle = esc_html__('Category: ', 'listgo') . $oObject->name;
            $aHeaderSettings = self::getHeaderSettingFromTerm($oObject);
            $headerURL = $aHeaderSettings['headerurl'];
            $overlayColor = $aHeaderSettings['overlaycolor'];
            $desc = $oObject->description;
        }elseif(is_tag()) {
            $oObject = get_queried_object();
            $postTitle = esc_html__('Tag: ', 'listgo') . $oObject->name;
            $aHeaderSettings = self::getHeaderSettingFromTerm($oObject);
            $headerURL = $aHeaderSettings['headerurl'];
             $desc = $oObject->description;
            $overlayColor = $aHeaderSettings['overlaycolor'];
        }elseif( is_singular() || is_page_template('templates/homepage.php') ){
            $aPageSettings = Wiloke::getPostMetaCaching($post->ID, 'page_settings');
            if ( is_page_template('templates/homepage.php') && ( !isset($aPageSettings['toggle_header_image']) || ($aPageSettings['toggle_header_image'] === 'disable')) ){
                return false;
            }

            if ( !empty($aPageSettings['header_image']) ){
                $headerURL = $aPageSettings['header_image'];
            }

            if ( !empty($aPageSettings['header_overlay']) ){
                $overlayColor = $aPageSettings['header_overlay'];
            }

            $postTitle = $post->post_title;
        }elseif(is_tax()){
            $oObject = get_queried_object();
            $postTitle = $oObject->name;
            $aHeaderSettings = self::getHeaderSettingFromTerm($oObject);
            $headerURL = $aHeaderSettings['headerurl'];
            $overlayColor = $aHeaderSettings['overlaycolor'];
            $desc = $oObject->description;
        }elseif(is_author()){
            $authorID = get_query_var( 'author' );
            $aAuthorInfo = Wiloke::getUserMeta($authorID);
            $postTitle = esc_html__('All posts by: ', 'listgo') . $aAuthorInfo['display_name'];
            if ( !empty($aAuthorInfo['meta']) ){
                $headerURL = !empty($aAuthorInfo['meta']['wiloke_cover_image']) ? wp_get_attachment_image_url($aAuthorInfo['meta']['wiloke_cover_image'], 'large') : '';
                $overlayColor = !empty($aAuthorInfo['meta']['wiloke_color_overlay']) ? $aAuthorInfo['meta']['wiloke_color_overlay'] : '';
            }
        }else if(is_archive()){
            $postTitle = esc_html__('Archive: ', 'listgo') . get_the_archive_title();
        }else if( is_page() || is_singular() ){
            $postTitle = get_the_title();
        }

        if ( !empty($wiloke->aThemeOptions) ){
            if ( empty($headerURL) && !empty($wiloke->aThemeOptions['blog_header_image']) ){
               $headerURL = wp_get_attachment_image_url($wiloke->aThemeOptions['blog_header_image']['id'], 'large');
               $headerURL = !empty($headerURL) ? $headerURL : $wiloke->aThemeOptions['blog_header_image']['url'];
            }

            if ( empty($overlayColor) && !empty($wiloke->aThemeOptions['blog_header_overlay']) ){
                $overlayColor = $wiloke->aThemeOptions['blog_header_overlay']['rgba'];
            }
        }

        ?>
        <div class="header-page bg-scroll lazy" data-src="<?php echo esc_url($headerURL); ?>">
  
            <div class="header-page__inner">
                <h2 class="header-page__title"><?php echo esc_html($postTitle); ?></h2>
                <?php if ( !empty($desc) ) : ?>
                <p class="term-description"><?php Wiloke::wiloke_kses_simple_html($desc); ?></p>
                <?php endif; ?>
            </div>
      
            <?php self::renderBreadcrumb(); ?>
            <div class="overlay" style="background-color: <?php echo esc_attr($overlayColor); ?>"></div>
        </div>
        <?php
    }

    public static function comment_template($comment, $args, $depth){
        $GLOBALS['comment'] = $comment;
        switch ( $comment->comment_type ) :
        case 'pingback' :
        case 'trackback':
        // Display trackbacks differently than normal comments.
        ?>
        <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
            <p><?php esc_html_e( 'Pingback:', 'listgo' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( esc_html__( '(Edit)', 'listgo' ), '<span class="edit-link">', '</span>' ); ?></p>
        <?php
            break;
        default :
        ?>
        <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
            <div id="comment-<?php comment_ID(); ?>" class="comment__inner">
                <div class="comment__avatar">
                    <?php
                        $commentID   = get_comment_ID();
                        $oAuthorInfo = get_comment($commentID);
                        $avatar = Wiloke::getUserAvatar($oAuthorInfo->user_id);
                    ?>
                    <a href="<?php comment_author_url($commentID); ?>">
                        <?php
                            if ( strpos($avatar, 'profile-picture.jpg') === false ) {
                                Wiloke::lazyLoad($avatar);
	                        } else {
                                $firstCharacter = strtoupper(substr($oAuthorInfo->comment_author, 0, 1));
		                        echo '<span style="background-color: '.esc_attr(WilokePublic::getColorByAnphabet($firstCharacter)).'" class="widget_author__avatar-placeholder">'. esc_html($firstCharacter) .'</span>';
	                        }
                        ?>
                    </a>
                </div>
                <div class="comment__body">

                    <?php echo sprintf('<cite class="comment__name">%1$s</cite>',get_comment_author_link()); ?>

                    <?php
                        printf( '<span class="comment__date">%1$s</span>',
                            /* translators: 1: date, 2: time */
                            Wiloke::wiloke_kses_simple_html(sprintf( '%1$s', get_comment_date()), true)
                        );
                        self::renderBadge($oAuthorInfo->user_id);
                    ?>

                    <?php if ( '0' == $comment->comment_approved ) : ?>
                        <p><?php esc_html_e( 'Your comment is awaiting moderation.', 'listgo' ); ?></p>
                    <?php endif; ?>

                    <div class="comment__content">
                        <?php comment_text(); ?>
                    </div>

                    <div class="comment__edit-reply">
                        <?php
                            comment_reply_link( array_merge( $args, array( 'reply_text' => esc_html__( 'Reply', 'listgo' ), 'after' => '', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) );
                            if ( current_user_can( 'edit_comment', $comment->comment_ID ) )
                            {
                                edit_comment_link( esc_html__( 'Edit', 'listgo' ), '', '' );
                            }
                        ?>
                    </div><!-- .reply -->

                </div>
            </div>
                
        <?php
        break;
        endswitch; // end comment_type check
    }

    public static function addFieldsToSearchForm(){
        if ( !is_page_template('templates/listing.php') && !is_tax('listing_cat') && !is_tax('listing_location') && !is_tax('listing_tag') ){
            return false;
        }

        global $wiloke;
        if ( !isset($wiloke->aThemeOptions['listing_toggle_search_by_tag']) || ($wiloke->aThemeOptions['listing_toggle_search_by_tag'] == 'enable') ) :
            $aTags = get_terms(
                array(
                    'taxonomy'   => 'listing_tag',
                    'hide_empty' => true
                )
            );
    
            $aSearching = isset($_REQUEST['s_listing_tag']) ? $_REQUEST['s_listing_tag'] : '';
    
            if ( !empty($aTags) && !is_wp_error($aTags) ) : ?>
                <div class="form-item item--tags">
                   <label class="label"><?php esc_html_e('Filter by tags', 'listgo'); ?> <span class="toggle-tags"></span></label>
                   <div class="item--tags-toggle">
    
                        <?php foreach ( $aTags as $oTag ) :
                       
                            $checked = !empty($aSearching) && in_array($oTag->term_id, $aSearching) ? 'checked' : ''; ?>
                       
                            <label for="s_listing_tag<?php echo esc_attr($oTag->term_id); ?>" class="input-checkbox">
                       
                                <input id="s_listing_tag<?php echo esc_attr($oTag->term_id); ?>" name="s_listing_tag[]" class="listgo-filter-by-tag" type="checkbox" value="<?php echo esc_attr($oTag->term_id); ?>" <?php echo esc_attr($checked); ?>>
                                <span></span>
                       
                                <?php echo esc_html($oTag->name); ?>
                       
                            </label>
                       
                       <?php endforeach; ?>
    
                   </div>
                </div>
           <?php
           endif;
       endif;
    }

    public static function selectField($aTerms, $label, $name, $itemID='', $hasAll=true, $default='', $hasMyLocation=false, $hideCountEqualToZero=false){
        if ( empty($itemID) ){
	        $itemID = $name;
	        $multiple = '';
        }else{
	        $multiple = 'multiple';
        }

        $fakeName = str_replace('[]', '', $name);
        if ( isset($_REQUEST[$fakeName]) ){
            $default = $_REQUEST[$fakeName];
        }

        global $wiloke;
        $worldwideText = isset($wiloke->aThemeOptions['listing_search_worldwide_text']) ? $wiloke->aThemeOptions['listing_search_worldwide_text'] : __('Worldwide', 'listgo');
        ?>
        <label for="<?php echo esc_attr($itemID); ?>" class="label"><?php echo esc_html($label); ?></label>
        <span class="input-select2 input-icon-inside">
            <select id="<?php echo esc_attr($itemID); ?>" name="<?php echo esc_attr($name); ?>" class="js_select2" <?php echo esc_attr($multiple); ?>>
                <?php if ( $hasAll ) : ?>
                <option value="all"><?php echo esc_html($worldwideText); ?></option>
                <?php endif; ?>
                <?php if ( $hasMyLocation ) : ?>
                <option value="mylocation"><?php esc_html_e('My Location', 'listgo'); ?></option>
                <?php endif; ?>

                <?php foreach ( $aTerms as $oCategory ) :
                    if ( $hideCountEqualToZero && $oCategory->count === 0 ){
                        continue;
                    }
                    $selected = '';
                    if ( !empty($default) ){
                        if ( $multiple ){
                            if ( in_array($oCategory->term_id, $default) ) {
                                $selected = 'selected';
                            }
                        }else{
                            if ( $oCategory->term_id === absint($default) ){
                                $selected = 'selected';
                            }
                        }
                    }

                    $aOptions = Wiloke::getOption('_wiloke_cat_settings_'.$oCategory->term_id);
                    $imgUrl = isset($aOptions['map_marker_image']) ? $aOptions['map_marker_image'] : '';
                ?>
                    <option <?php echo esc_attr($selected); ?> data-img="<?php echo esc_url($imgUrl); ?>" value="<?php echo esc_attr($oCategory->term_id); ?>"><?php echo esc_html($oCategory->name); ?></option>
                <?php endforeach; ?>
            </select>
        </span>
        <?php
    }

    public static function renderTaxonomy($postID, $termName='listing_location', $onlyOne=false){
        $oTerms = wp_get_post_terms($postID, $termName);
        $termOrder = 1;
        if ( !empty($oTerms) && !is_wp_error($oTerms) ) :
            if ( $onlyOne ) :
            ?>
            <div class="listing__cat">
                <a href="<?php echo esc_url(get_term_link($oTerms[0]->term_id)); ?>"><?php echo esc_html($oTerms[0]->name); ?></a>
            </div>
            <?php
            else:
                $total = count($oTerms);
                foreach ( $oTerms as $order => $oTerm ) :
                    $oTerm          = get_object_vars($oTerm);
                    $oTerm['link']  = get_term_link($oTerm['term_id']);
                    $oTerm          = (object)$oTerm;
                    $oTerms[$order] = $oTerm;

                    if($termOrder === 2){
                        ?>
                        <ul class="listing__cats">
                        <?php
                    }

                    if ($termOrder === 1) :
                        ?>
                        <a href="<?php echo esc_url($oTerm->link); ?>"><?php echo esc_html($oTerm->name); ?></a>
                        <?php
                        if ( $total > 1 ) :
                        ?>
                            <span class="listing__cat-more">+</span>
                        <?php
                        endif;
                    else:
                    ?>
                        <li><a href="<?php echo esc_url($oTerm->link); ?>"><?php echo esc_html($oTerm->name); ?></a></li>
                    <?php
                    Wiloke::setTermsCaching($oTerm->taxonomy, array($oTerm->term_id=>$oTerm));
                    endif;

                    if ( $total > 1 && $total === $termOrder ){
                        echo '</ul>';
                    }

                    $termOrder++;
                endforeach;
            endif;
        endif;
    }

    public static function renderMapPage($search='',$mapPage='', $atts=null, $hasIcon = true, $additionalClass=''){
        $text = esc_html__('Go to map', 'listgo');
        $itemClass = 'tb__cell ' . $additionalClass . (isset($atts['link_to_map_page_additional_class']) ? ' ' . $atts['link_to_map_page_additional_class'] : '');
        $itemClass = trim($itemClass);

        if ( !empty($atts) ){
            if ( $atts['toggle_render_link_to_map_page'] === 'disable' ){
                return false;
            }

            $text = empty($atts['link_to_map_page_text']) ? esc_html__('Go to map', 'listgo') : $atts['link_to_map_page_text'];
        }

        if ( $hasIcon ){
            $tooltip = $text;
            $text = '<i class="icon_pin_alt"></i>';
        }else{
            $tooltip = '';
        }

	    global $wiloke;
        $mapPage = isset($wiloke->aThemeOptions['header_search_map_page']) && !empty($wiloke->aThemeOptions['header_search_map_page']) ? get_permalink($wiloke->aThemeOptions['header_search_map_page']) : $mapPage;
        ?>
        <div class="<?php echo esc_attr($itemClass); ?>">
            <?php if ( !empty($tooltip) ) : ?>
            <a href="<?php echo esc_url($mapPage . '?' . $search); ?>" data-tooltip="<?php echo esc_attr($tooltip); ?>">
                <?php Wiloke::wiloke_kses_simple_html($text); ?>
            </a>
            <?php else: ?>
            <a href="<?php echo esc_url($mapPage . '?' . $search); ?>">
                <?php Wiloke::wiloke_kses_simple_html($text); ?>
            </a>
            <?php endif; ?>
        </div>
        <?php
    }

    public static function renderViewDetail($post, $atts=null, $additionalClass=''){
        $text = esc_html__('View Detail', 'listgo');
        $itemClass = 'tb__cell ' . $additionalClass;
        $itemClass = trim($itemClass);

        if ( !empty($atts) ){
            if ( $atts['toggle_render_view_detail'] === 'disable' ){
                return false;
            }
            $text = empty($atts['view_detail_text']) ? esc_html__('View Detail', 'listgo') : $atts['view_detail_text'];
        }

        ?>
        <div class="<?php echo esc_attr($itemClass); ?>">
            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php echo esc_attr($text); ?></a>
        </div>
        <?php
    }

    public static function renderFindDirection($aPageSettings, $atts=null, $additionalClass=''){
        $text = esc_html__('Find directions', 'listgo');
        $itemClass = 'tb__cell ' . $additionalClass;
        $itemClass = trim($itemClass);

        if ( !empty($atts) ){
            if ( $atts['toggle_render_find_direction'] === 'disable' ){
                return false;
            }
            $text = empty($atts['find_direction_text']) ? esc_html__('Find Directions', 'listgo') : $atts['find_direction_text'];
        }

        if ( !$aPageSettings || !isset($aPageSettings['map']) ){
            return false;
        }
        ?>
        <div class="<?php echo esc_attr($itemClass); ?>">
            <a href="<?php echo esc_url(self::$googleMap . $aPageSettings['map']['location'] . '/' . $aPageSettings['map']['latlong']); ?>" target="_blank" data-tooltip="<?php echo esc_attr($text); ?>">
                <i class="arrow_left-right_alt"></i>
            </a>
        </div>
        <?php
    }

    public function getTermChildren(){
        if ( !isset($_GET['term_id']) || !isset($_GET['taxonomy']) ){
            wp_send_json_success(array(-1));
        }
        wp_send_json_success(Wiloke::getTermChildren($_GET['term_id'], $_GET['taxonomy']));
    }

    public static function listingInfo($post){
        if ( !empty(self::$oListingCollections) && isset(self::$oListingCollections[$post->ID]) ){
            return json_decode(self::$oListingCollections[$post->ID], true);
        }

        if( !has_post_thumbnail($post->ID) || (!$aFeaturedImage = Wiloke::generateSrcsetImg(get_post_thumbnail_id($post->ID), 'large')) ){
            $aFeaturedImage['main']['src']      = get_template_directory_uri() . '/img/featured-image.jpg';
            $aFeaturedImage['main']['width']    = 1000;
            $aFeaturedImage['main']['height']   = 500;
            $aFeaturedImage['srcset']           = '';
            $aFeaturedImage['sizes']            = '';
        }

        $aPageSettings      = Wiloke::getPostMetaCaching($post->ID, 'listing_settings');
        $aListingLocation   = Wiloke::getPostTerms($post, 'listing_location');
        $aListingLocation   = self::putTermLinks($aListingLocation);

        $aListingListingCat = Wiloke::getPostTerms($post, 'listing_cat');
        $aListingListingCat = self::putTermLinks($aListingListingCat);
        $aFirstCatInfo = array();
        if ( isset($aListingListingCat[0]) ){
            $aFirstCatInfo = Wiloke::getTermOption($aListingListingCat[0]->term_id);
        }

        $aListingTags  = Wiloke::getPostTerms($post, 'listing_tag');
        $aListingTags  = self::putTermLinks($aListingTags);

        $aListingLocationIDs    = self::getTermIDs($aListingLocation);
        $aListingListingCatIDs  = self::getTermIDs($aListingListingCat);
        $aListingTagIDs         = self::getTermIDs($aListingTags);

        $locationPlaceID = '';
        $firstLocationID = '';
        $parentLocation = null;
        if ( isset($aListingLocationIDs[0]) ){
            $locationPlaceID = get_term_meta($aListingLocationIDs[0], 'wiloke_listing_location_place_id', true);
            $parentLocation = $aListingLocation[0]->parent;
            $firstLocationID = $aListingLocationIDs[0];
        }

        $aTerms = array_merge($aListingLocationIDs, $aListingListingCatIDs, $aListingTagIDs);

        return array(
            'ID'                    => $post->ID,
            'link'                  => get_permalink($post->ID),
            'title'                 => $post->post_title,
            'post_except'           => Wiloke::wiloke_content_limit(200, $post, false, $post->post_content, true),
            'post_date'             => $post->post_date,
            'post_date_with_format' => get_the_date($post->ID),
            'featured_image'        => $aFeaturedImage,
            'first_cat_info'        => $aFirstCatInfo,
            'author'                => self::getAuthorInfo($post->post_author, true),
            'author_id'             => $post->post_author,
            'comment_count'         => $post->comment_count,
            'listing_settings'      => $aPageSettings,
            'terms_id'              => $aTerms,
            'listing_location'      => $aListingLocation,
            'placeID'               => $locationPlaceID,
            'first_location_id'     => $firstLocationID,
            'parentLocationID'      => $parentLocation,
            'listing_cat'           => $aListingListingCat,
            'listing_tag'           => $aListingTags,
            'business_hours'        => self::businessStatus($post)
        );

    }

    public static function singleHeaderBg($post, $aPageSettings){
        $bgUrl = '';
        if ( isset($aPageSettings['header_image_id']) && !empty($aPageSettings['header_image_id']) ){
            $bgUrl =  wp_get_attachment_image_url($aPageSettings['header_image_id'], 'large');
        }else if ( has_post_thumbnail($post->ID) ){
            $bgUrl =  get_the_post_thumbnail_url($post->ID, 'large');
        }
        $overlayColor = isset($aPageSettings['header_overlay']) ? $aPageSettings['header_overlay'] : '';
        ?>
        <div class="header-page bg-scroll lazy" data-src="<?php echo esc_url($bgUrl); ?>">

            <div class="container">
                <div class="header-page__inner">
                    <h2 class="header-page__title"><?php echo esc_html($post->post_title); ?></h2>
                </div>
            </div>

            <div class="header-page__breadcrumb">
                <div class="container">
                    <ol class="wo_breadcrumb">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'listgo'); ?></a></li>
                        <li><span><?php echo esc_html($post->post_title); ?></span></li>
                    </ol>
                </div>
            </div>
            <div class="overlay" style="background-color: <?php echo esc_attr($overlayColor); ?>"></div>
        </div>
        <?php
    }

    public static function getNumberOfFollowing($userID=null){
        global $wpdb;
        $userID = empty($userID) ? self::$oUserInfo->ID : $userID;
        if ( Wiloke::$wilokePredis && Wiloke::$wilokePredis->exists(RegisterFollow::$redisFollowing) ){
            $aFollowings = Wiloke::hGet(RegisterFollow::$redisFollowing, $userID);
            return $aFollowings ? count($aFollowings) : 0;
        }else{
            $tblUser = $wpdb->prefix . 'users';
			$tblFollowing = $wpdb->prefix . AltertableFollowing::$tblName;
            $total = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT($tblFollowing.user_ID) FROM $tblUser INNER JOIN $tblFollowing ON ($tblFollowing.user_ID = $tblUser.ID) WHERE $tblFollowing.follower_ID = %d",
					$userID
				)
			);

            return $total;
        }
    }

    public static function getNumberOfFollowers($userID=null){
        global $wpdb;
        $userID = empty($userID) ? self::$oUserInfo->ID : $userID;
        if ( Wiloke::$wilokePredis && Wiloke::$wilokePredis->exists(RegisterFollow::$redisFollower) ){
            $aFollowers = Wiloke::hGet(RegisterFollow::$redisFollower, $userID);
            return $aFollowers ? count($aFollowers) : 0;
        }else{
            $tblUser = $wpdb->prefix . 'users';
			$tblFollowing = $wpdb->prefix . AltertableFollowing::$tblName;
            $total = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT($tblFollowing.follower_ID) FROM $tblUser INNER JOIN $tblFollowing ON ($tblFollowing.follower_ID = $tblUser.ID) WHERE $tblFollowing.user_ID = %d",
					$userID
				)
			);
            return $total;
        }
    }

    public static function accountHeaderBg($authorID=null){
        global $wiloke;
        if ( empty($authorID) ){
            $authorID = isset($_REQUEST['user']) && !empty($_REQUEST['user']) ? $_REQUEST['user'] : '';
        }

        if ( empty($authorID) ){
            $oUserInfo = self::$oUserInfo;
            $authorID = self::$oUserInfo->ID;
        }else{
            $oUserInfo = (object)Wiloke::getUserMeta($authorID);
        }

        if (isset($oUserInfo->meta['wiloke_cover_image']) && !empty($oUserInfo->meta['wiloke_cover_image'])){
            $bgUrl =  wp_get_attachment_image_url($oUserInfo->meta['wiloke_cover_image'], 'large');
        }else{
            $bgUrl =  get_template_directory_uri() . '/img/cover-image.jpeg';
        }
        $status = self::isFollowingThisUser($authorID);
        $overlayColor = isset($oUserInfo->meta['wiloke_color_overlay']) ? $oUserInfo->meta['wiloke_color_overlay'] : '';
        $profileUrl = self::getPaymentField('myaccount', true);
        $followingUrl = !empty($profileUrl) ? self::addQueryToLink($profileUrl, 'mode=following&user='.$oUserInfo->ID) : '#';
        $followersUrl = !empty($profileUrl) ? self::addQueryToLink($profileUrl, 'mode=followers&user='.$oUserInfo->ID) : '#';
        if ( is_author() ){
            $profileUrl = !empty($profileUrl) ? self::addQueryToLink($profileUrl, 'mode=profile&user='.$authorID) : '#';
        }else{
            $profileUrl = get_author_posts_url($authorID);
        }
        ?>
        <div class="header-page header-page--account bg-scroll lazy" data-src="<?php echo esc_url($bgUrl); ?>">
            <div class="container">
                <div class="header-page__account">
                    <div class="header-page__account-avatar">
                        <a href="<?php echo esc_url($profileUrl); ?>">
                            <div class="header-page__account-avatar-img bg-scroll">
                                <?php
                                $avatar = Wiloke::getUserAvatar($oUserInfo->ID, null, array(65, 65));
                                if ( strpos($avatar, 'profile-picture.jpg') === false ) {
                                    ?>
                                    <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($oUserInfo->display_name); ?>" height="65" width="65" class="avatar">
                                    <?php
                                } else {
                                    $firstCharacter = strtoupper(substr($oUserInfo->display_name, 0, 1));
                                    echo '<span style="background-color: '.esc_attr(self::getColorByAnphabet($firstCharacter)).'" class="widget_author__avatar-placeholder">'. esc_html($firstCharacter) .'</span>';
                                }
                                ?>
                            </div>
                            <h4 class="header-page__account-name"><?php echo esc_html($oUserInfo->display_name); ?></h4>
                            <?php self::renderBadge($oUserInfo->role); ?>
                        </a>
                    </div>
                    <div class="account-subscribe">
                        <span class="followers"><a href="<?php echo esc_url($followersUrl) ?>"><span class="count"><?php echo esc_html(self::getNumberOfFollowers($oUserInfo->ID)); ?></span> <?php esc_html_e('Followers', 'listgo'); ?></a></span>
                        <span class="following"><a href="<?php echo esc_url($followingUrl) ?>"><span class="count"><?php echo esc_html(self::getNumberOfFollowing($oUserInfo->ID)); ?></span> <?php esc_html_e('Following', 'listgo'); ?></a></span>
                        <?php if ( empty(self::$oUserInfo) || ( !empty($authorID) && (self::$oUserInfo->ID != $authorID) ) ) :  ?>
                        <a href="#" data-status="<?php echo esc_attr($status); ?>" class="listgo-btn listgo-btn--sm js_subscribe_on_profile" data-authorid="<?php echo esc_attr($authorID); ?>"><?php echo esc_attr($status) ? esc_html($wiloke->aConfigs['translation']['followingtext']) : esc_html($wiloke->aConfigs['translation']['unfollowingtext']); ?> <i class="fa fa-rss"></i></a>
                        <?php endif; ?>
                        <?php
                        if ( is_page_template('wiloke-submission/myaccount.php') ) :
                            $accountPage = WilokePublic::getPaymentField('myaccount', true); ?>
                        <a class="listgo-btn listgo-btn--sm" href="<?php echo esc_url(self::addQueryToLink($accountPage, 'mode=edit-profile')); ?>">
                            
                            <?php if (strpos($bgUrl, 'cover-image.jpeg') === false ): ?>
                                <i class="fa fa-edit"></i>
                            <?php endif ?>
                            
                            <?php esc_html_e('Edit Profile', 'listgo'); ?>
            
                            <?php if ( strpos($bgUrl, 'cover-image.jpeg') >= 0 ): ?>
                                <i class="fa fa-exclamation"></i> 
                            <?php endif ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="overlay" style="background-color: <?php echo esc_attr($overlayColor); ?>"></div>
        </div>
        <?php
    }

    public function putDataToRedis(){
        if ( Wiloke::$wilokePredis && is_admin() ){
            Wiloke::$wilokePredis->del(Wiloke::$prefix."listing_ids");
            if ( !Wiloke::$wilokePredis->exists(Wiloke::$prefix."listing_ids") ){
                $query = new WP_Query(
                    array(
                        'post_type' => 'listing',
                        'posts_per_page' => -1,
                        'post_status' => 'publish'
                    )
                );
                
                if ( $query->have_posts() ){
                    while ($query->have_posts()){
                        $query->the_post();
                        $this->updateListingCaching($query->post->ID, $query->post);
                    }
                }
            }
        }
    }
    
    /*
	 |--------------------------------------------------------------------------
	 | Caching
	 |--------------------------------------------------------------------------
	 | If Redis is supported by hosting, We will use Redis else We will use update option
     |
     | database
	 |
	 */
    public function updateListingCaching($postID, $post){
        if ( $post->post_status !== 'publish' ){
            return false;
        }

        if ( !empty(Wiloke::$wilokePredis) ){
            $aTaxes = get_post_taxonomies($postID);
            foreach ( $aTaxes as $taxName ){
                $aTaxInfo  = wp_get_post_terms($post->ID, $taxName);
                if ( !empty($aTaxInfo) && !is_wp_error($aTaxInfo) ){
                    foreach ( $aTaxInfo as $key => $oTerm ){
                        $oTerm = get_object_vars($oTerm);
                        $oTerm['link'] = get_term_link($oTerm['term_id']);
                        $oTerm = (object)$oTerm;
                        $aTaxInfo[$key] = $oTerm;

                        $aTermChildren = get_term_children($oTerm->term_id, $taxName);
                        if ( empty($oTermChildren) || is_wp_error($oTermChildren) ){
                           $aTermChildren =  array(-1);
                        }
                        Wiloke::setTermChildren($oTerm->term_id, $taxName, $aTermChildren);
                    }

                    Wiloke::$wilokePredis->hSet(Wiloke::$prefix."$post->post_type|termsinpost", $postID.'_'.$taxName, json_encode($aTaxInfo));
                }
            }
        }

        if ( $post->post_type === 'listing' && $post->post_status === 'publish' ){
            // Get Taxes of post
            if ( !empty(Wiloke::$wilokePredis) ){
                $aPost = self::listingInfo($post);
                Wiloke::$wilokePredis->hSet(Wiloke::$prefix."listing", $postID, json_encode($aPost));
                Wiloke::$wilokePredis->hSet(Wiloke::$prefix."listing|$postID", 'post_data', json_encode($aPost));
                Wiloke::$wilokePredis->hSet(Wiloke::$prefix."listing|$postID", 'post_date', strtotime($post->post_date));
                Wiloke::$wilokePredis->sAdd(Wiloke::$prefix."listing_ids", $postID);
                Wiloke::$wilokePredis->sAdd(Wiloke::$prefix."listing_titles", stripslashes(strtolower($post->post_title)).'|'.$postID);
            }
        }
    }

    public function editListingCat($termID){
        if ( Wiloke::$wilokePredis ){
            $oTerm = Wiloke::getTermCaching('listing_cat', $termID);
            if ( $oTerm->slug !== $_POST['slug'] ){
                $aListingsInTerm = Wiloke::$wilokePredis->zrange($oTerm->slug, 0, -1);
                if ( !empty($aListingsInTerm) ){
                    foreach ( $aListingsInTerm as $postID ){
                        Wiloke::$wilokePredis->zrem($oTerm->slug, $postID);
                        $aListingSettings = Wiloke::getPostMetaCaching($postID, 'listing_settings');
                        $aLatLng = explode(',', $aListingSettings['map']['latlong']);
                        Wiloke::$wilokePredis->geooadd($_POST['slug'], $aLatLng[1], $aLatLng[0], $postID);
                    }
                }
            }
        }
    }

    public function deleteListingCat($oTerm){
        if ( Wiloke::$wilokePredis ){
            $aListingsInTerm = Wiloke::$wilokePredis->zrange($oTerm->slug, 0, -1);
            if ( !empty($aListingsInTerm) ){
                foreach ( $aListingsInTerm as $postID ){
                    Wiloke::$wilokePredis->zrem($oTerm->slug, $postID);
                }
            }
        }
    }

    public function deleteListingCaching($postID){
        global $post_type;
        if ( current_user_can('edit_posts') ){
            if ( Wiloke::$wilokePredis ){
                $post = get_post($postID);
                $aCategories = Wiloke::getPostTerms($post, 'listing_cat');
                if ( !empty($aCategories) && !is_wp_error($aCategories) ){
                    Wiloke::$wilokePredis->zrem($aCategories[0]->slug, $postID);
                }
                if ( Wiloke::$wilokePredis->exists(Wiloke::$prefix.$post_type) ){
                    self::delRedisCaching($postID, $post_type);
                }
            }
        }
    }

    public static function delRedisCaching($postID, $postType){
        $aTaxes = get_post_taxonomies($postID);
        if ( !empty($aTaxes) && !is_wp_error($aTaxes) ){
            foreach ( $aTaxes as $taxName ){
                Wiloke::$wilokePredis->del(Wiloke::$prefix.$postType);
                Wiloke::$wilokePredis->hdel(Wiloke::$prefix."$postType|termsinpost", $postID.'_'.$taxName);
                Wiloke::$wilokePredis->hdel(Wiloke::$prefix."listing", $postID);
                Wiloke::$wilokePredis->hdel(Wiloke::$prefix."listing|$postID", 'post_data');
                Wiloke::$wilokePredis->hdel(Wiloke::$prefix."listing|$postID", 'post_date');
                Wiloke::$wilokePredis->srem(Wiloke::$prefix."listing_ids", $postID);
                if ( !$title = get_the_title($postID) ){
                    Wiloke::$wilokePredis->srem(Wiloke::$prefix."listing_titles", $title.'|'.$postID);
                }
            }
        }

    }

    public function runUpdateCache(){
        $query = new WP_Query(
            array(
                'post_type'       => 'listing',
                'posts_per_page'  => -1,
                'post_status'     => 'publish',
                'orderby'         => 'rand'
            )
        );

        if ( $query->have_posts() ){
            while ($query->have_posts()){
                $query->the_post();
                $this->updateListingCaching($query->post->ID, $query->post);
            }
        }
    }

    public function getCaching(){
        if ( !empty(Wiloke::$wilokePredis) ){
            // Thanks god, Redis is available on the hosting
             $aListings = Wiloke::$wilokePredis->hGetAll(self::$prefix.'listings');
        }else{
            $aListings = get_option(self::$prefix.'listings');
        }

        return $aListings;
    }

    public static function getPaymentField($field='', $isUrl=false){
        if ( !class_exists('WilokeListGoFunctionality\Register\RegisterWilokeSubmission') ){
            return false;
        }

        if ( empty(self::$aPaymentFields) ){
            $aData = get_option(RegisterWilokeSubmission::$submissionConfigurationKey);
            self::$aPaymentFields = $aData;
        }else{
            $aData = self::$aPaymentFields;
        }

        if ( empty($aData) ){
            return false;
        }
        
        $aData = json_decode($aData, true);
        if ( empty($aData) ){
            $aData = json_decode(stripslashes($aData), true);
        }
        
        if ( empty($field) ){
            return $aData;
        }

        $val = isset($aData[$field]) ? $aData[$field] : '';

        if ( $isUrl ){
            return get_permalink($val);
        }else{
            return $val;
        }
    }

    public static function getRemainingOfPackage($packageID, $aPostMeta){
        if ( empty(self::$oUserInfo->ID) ){
            return false;
        }

        global $wpdb;
        $tblPackageStatus   = $wpdb->prefix . AlterTablePackageStatus::$tblName;
        $tblPaymentHistgory = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

        $type = !isset($aPostMeta['price']) || empty($aPostMeta['price']) ? 'free' : 'premium';

        if ( $type === 'free' ){
            if ( empty($aPostMeta['number_of_posts']) ){
                return array(
                    'type'      => $type,
                    'purchased' => 0,
                    'remaining' => 0
                );
            }

            $aPackageStatus = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT package_information, payment_ID FROM $tblPackageStatus WHERE package_ID=%d AND user_ID=%d ORDER BY payment_ID DESC",
                    $packageID, self::$oUserInfo->ID
                ),
                ARRAY_A
            );

            if ( empty($aPackageStatus) ){
                return array(
                    'type'     => $type,
                    'purchased'=>0
                );
            }else{
                $tblRelationShips = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;
                $listingsUsed = $wpdb->get_var(
                    $wpdb->prepare(
                    "SELECT COUNT(object_ID) FROM $tblRelationShips WHERE payment_ID=%d",
                        $aPackageStatus['payment_ID']
                    )
                );
                $remaining = absint($aPostMeta['number_of_posts']) - absint($listingsUsed);
                return array(
                    'purchased' => 1,
                    'type'      => $type,
                    'remaining' => $remaining
                );
            }
        }else{
            $aUserPaymentInfo = WilokeCustomerPlan::getCustomerPlan();
            if ( !empty($aUserPaymentInfo) && WilokeCustomerPlan::isRecurringPlan($aUserPaymentInfo)  ){
                return array(
                    'purchased' => 0,
                    'type'      => $type,
                );
            }

            $aPackageStatus = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT payment_ID FROM $tblPackageStatus WHERE package_ID=%d AND user_ID=%d ORDER BY payment_ID DESC",
                    $packageID, self::$oUserInfo->ID
                ),
                ARRAY_A
            );
        }

        if ( !empty($aPackageStatus) ){
            $aPackageInformation = $aPostMeta;
            $tblRelationShips = $wpdb->prefix . AlterTablePaymentRelationships::$tblName;
            $aPaymentAndListingInfo = $wpdb->get_row(
                $wpdb->prepare(
                "SELECT COUNT(object_ID) as total_listings, $tblPaymentHistgory.status FROM $tblRelationShips LEFT JOIN $tblPaymentHistgory ON ($tblPaymentHistgory.ID = $tblRelationShips.payment_ID) WHERE payment_ID=%d ORDER BY $tblPaymentHistgory.ID DESC",
                    $aPackageStatus['payment_ID']
                ),
                ARRAY_A
            );

            if ( ($aPaymentAndListingInfo['status'] === 'completed') || ($aPaymentAndListingInfo['status'] === 'pending') || ($aPaymentAndListingInfo['status'] === 'processing') || ((strtolower($aPaymentAndListingInfo['status']) === 'success') )  ){
                if ( !empty($aPackageInformation['number_of_posts']) ){
                    $remaining = absint($aPackageInformation['number_of_posts']) - absint($aPaymentAndListingInfo['total_listings']);
                }else{
                    $remaining = -1;
                }

                return array(
                    'purchased' => 1,
                    'is_processing'=>($aPaymentAndListingInfo['status'] === 'processing'),
                    'type'      => $type,
                    'payment_status' => $aPaymentAndListingInfo['status'],
                    'remaining' => $remaining
                );
            }
        }

        return array(
            'purchased' => 0,
            'type'      => $type,
        );
    }

    public static function postMeta($post){
        global $wiloke;
    ?>
        <div class="listing-single__meta">
            <?php if ( $wiloke->aThemeOptions['listing_toggle_posted_on'] === 'enable' ) : ?>
                <div class="listing-single__date">
                    <span class="listing-single__label"><?php esc_html_e('Posted on', 'listgo'); ?></span>
                    <?php Wiloke::renderPostDate($post->ID); ?>
                </div>
            <?php endif; ?>

            <?php if ( $wiloke->aThemeOptions['listing_toggle_categories'] === 'enable' ) : ?>
                <div class="listing__meta-cat">
                    <?php Wiloke::theTerms('listing_cat', $post, '<span class="listing-single__label">', esc_html__('Category|Categories', 'listgo'), '</span>', ', ', ''); ?>
                </div>
            <?php endif; ?>

           <?php if ( $wiloke->aThemeOptions['listing_toggle_locations'] === 'enable' ) : ?>
                <div class="listing__meta-cat">
                    <?php Wiloke::theTerms('listing_location', $post, '<span class="listing-single__label">', esc_html__('Location|Locations', 'listgo'), '</span>', ', ', ''); ?>
                </div>
            <?php endif; ?>
            <?php if ( $wiloke->aThemeOptions['listing_toggle_rating_result'] === 'enable' ) : ?>
            <div class="listing-single__review">
                <span class="listing-single__label"><?php esc_html_e('Rating', 'listgo'); ?></span>
                <?php self::renderAverageRating($post, array('toggle_render_rating'=>'enable')); ?>
            </div>
            <?php endif; ?>
            <?php
            ob_start();
            WilokePublic::renderListingStatus($post, true);
            $status = ob_get_clean();
            if ( !empty($status) ) :
            ?>
            <div class="listing-single__status">
                <span class="listing-single__label"><?php esc_html_e('Status', 'listgo'); ?></span>
                <?php echo $status; ?>
            </div>
            <?php endif; ?>
        </div>
    <?php
    }

    public static function isFollowing($post){
        if ( empty(self::$oUserInfo) ){
            return false;
        }

        if ( Wiloke::$wilokePredis ){
            $aFollowers = Wiloke::hGet(RegisterFollow::$redisFollower, $post->post_author, true);
            if ( empty($aFollowers) || !in_array(self::$oUserInfo->ID, $aFollowers) ){
                return false;
            }

            return true;
        }else{
            global $wpdb;
            $tblName = $wpdb->prefix . AltertableFollowing::$tblName;

            $result = $wpdb->get_var($wpdb->prepare(
               "SELECT user_ID FROM $tblName WHERE user_ID=%d AND follower_ID=%d",
               $post->post_author,
               self::$oUserInfo->ID
            ));

            if ( empty($result) ){
                return false;
            }

            return true;
        }
    }

    public static function isFollowingThisUser($userID){
        if ( empty(self::$oUserInfo) || empty($userID) ){
            return false;
        }

        if ( Wiloke::$wilokePredis ){
            $aFollowers = Wiloke::hGet(RegisterFollow::$redisFollower, $userID, true);
            if ( empty($aFollowers) || !in_array(self::$oUserInfo->ID, $aFollowers) ){
                return false;
            }

            return true;
        }else{
            global $wpdb;
            $tblName = $wpdb->prefix . AltertableFollowing::$tblName;

            $result = $wpdb->get_var($wpdb->prepare(
               "SELECT user_ID FROM $tblName WHERE user_ID=%d AND follower_ID=%d",
               $userID,
               self::$oUserInfo->ID
            ));

            if ( empty($result) ){
                return false;
            }

            return true;
        }
    }

    public static function listingAction($post){
        global $wiloke;
        ?>
        <div class="listing-single__actions">
            <ul>
                <?php if ( $wiloke->aThemeOptions['listing_toggle_report'] === 'enable' ) : ?>
                    <li class="js_report action__report" data-tooltip="<?php esc_html_e('Report', 'listgo'); ?>">
                        <a href="#"><i class="icon_error-triangle_alt"></i></a>
                    </li>
                <?php endif; ?>

                <?php if ( ($wiloke->aThemeOptions['listing_toggle_sharing_posts'] === 'enable') && class_exists('WilokeSharingPost') ) : ?>
                    <li class="action__share" data-tooltip="<?php esc_html_e('Share', 'listgo'); ?>">
                        <a href="#"><i class="social_share"></i></a>
                        <?php echo do_shortcode('[wiloke_sharing_post]'); ?>
                    </li>
                <?php endif; ?>

                <?php
                if ( $wiloke->aThemeOptions['listing_toggle_add_to_favorite'] === 'enable' ) :
                    $class = 'js_favorite';
                    if ( class_exists('WilokeListGoFunctionality\AlterTable\AlterTableFavirote') ){
                        $favorite = AlterTableFavirote::getStatus($post->ID);
                        $class .= !empty($favorite) ? ' active' : '';
                    }
                    ?>
                    <li class="action__like" data-tooltip="<?php echo esc_attr($wiloke->aConfigs['translation']['addtofavorite']); ?>">
                        <a class="<?php echo esc_attr($class); ?>" href="#" data-postid="<?php echo esc_attr($post->ID); ?>"><i class="icon_heart_alt"></i></a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
        <?php
    }

    public static function getTopRatedListings($limit, $offset=0){
        global $wpdb;
        if ( !class_exists('WilokeListGoFunctionality\AlterTable\AlterTableReviews') ){
            return false;
        }
        $tblPosts   = $wpdb->prefix . 'posts';
        $tblRating  = $wpdb->prefix . AlterTableReviews::$tblName;

        $sql = "SELECT $tblPosts.ID, $tblPosts.post_title as title, $tblPosts.post_date, AVG($tblRating.rating) as rated_score, $tblRating.user_ID FROM $tblPosts LEFT JOIN $tblRating ON ($tblRating.post_ID = $tblPosts.ID) WHERE $tblPosts.post_type=%s AND $tblPosts.post_status=%s GROUP BY $tblPosts.ID ORDER BY rated_score DESC LIMIT $limit OFFSET $offset";

        $aResult = $wpdb->get_results(
            $wpdb->prepare(
                 $sql,
                 'listing', 'publish', $limit, $offset
            ),
            ARRAY_A
        );

        return $aResult;
    }

    public static function fetchReview($orderBy = 'post_date', $reviewID=0, $postID=null, $offset=0){
        global $wpdb, $post;
        $limit = get_option('comments_per_page');
        $tblPosts   = $wpdb->prefix . 'posts';
        $tblRating  = $wpdb->prefix . AlterTableReviews::$tblName;

        if ( !empty($offset) ){
            $offset = ($offset-1)*$limit;
        }

        $postID = !empty($postID) ? $postID : $post->ID;

        if ( $orderBy === 'post_date' ){
            $sql = "SELECT $tblPosts.ID, $tblPosts.post_title, $tblPosts.post_date, $tblPosts.post_content, $tblRating.rating, $tblRating.user_ID FROM $tblPosts LEFT JOIN $tblRating ON ($tblRating.review_ID = $tblPosts.ID) WHERE $tblRating.post_ID=%d AND $tblPosts.post_type=%s";

            if ( !empty($reviewID) ){
                $sql .= " AND $tblRating.review_ID=".esc_sql($reviewID);
            }else{
                $limit = absint($limit);
                $sql .= " ORDER BY $tblPosts.post_date DESC LIMIT $limit OFFSET $offset";
            }

            $oResult = $wpdb->get_results(
                $wpdb->prepare(
                     $sql,
                     $postID, 'review'
                )
            );
        }else{
            $tblPostMeta  = $wpdb->prefix . 'postmeta';

            $sql = "SELECT $tblPosts.ID, $tblPosts.post_title, $tblPosts.post_date, $tblPosts.post_content, $tblRating.rating, $tblRating.user_ID, COALESCE(SUM($tblPostMeta.meta_value), 0) as thanks_score FROM $tblPosts LEFT JOIN $tblRating ON ($tblRating.review_ID = $tblPosts.ID) LEFT JOIN $tblPostMeta ON ($tblPostMeta.post_id=$tblPosts.ID) WHERE $tblRating.post_ID=%d AND $tblPosts.post_type=%s GROUP BY $tblPosts.ID ORDER BY thanks_score DESC LIMIT $limit OFFSET $offset";

            $oResult = $wpdb->get_results(
                $wpdb->prepare(
                     $sql,
                     $postID, 'review', self::$scoreThanksForReviewingKey
                )
            );
        }

        return $oResult;
    }

    public static function totalReviews(){
        global $wpdb, $post;
        $tblRating  = $wpdb->prefix . AlterTableReviews::$tblName;

        $totalReviews = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT($tblRating.review_ID) FROM $tblRating WHERE $tblRating.post_ID=%d",
                $post->ID
            )
        );

        return $totalReviews;
    }

    public static function insertAttachment($aFile, $parentPostID=0){
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        $upload_overrides = array('test_form' => false);
        $aMoveFile = wp_handle_upload($aFile, $upload_overrides );

        // Check the type of file. We'll use this as the 'post_mime_type'.
        $fileType = wp_check_filetype( basename($aMoveFile['file']), null );

        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        // Prepare an array of post data for the attachment.
        $attachment = array(
            'guid'           => $wp_upload_dir['url'] . '/' . basename($aMoveFile['file']),
            'post_mime_type' => $fileType['type'],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename($aMoveFile['file']) ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );
        // Insert the attachment.
        $attachID = wp_insert_attachment($attachment, $aMoveFile['file'], $parentPostID);

        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Generate the metadata for the attachment, and update the database record.
        $aAttachData = wp_generate_attachment_metadata($attachID, $aMoveFile['file']);
        wp_update_attachment_metadata($attachID, $aAttachData);
        return $attachID;
    }

    public function submitReview(){
        $canUploadFile = current_user_can('upload_files');
        $aData = $_POST;

        if ( !isset($aData['post_ID']) || empty($aData['post_ID']) || !isset($aData['wiloke-listgo-nonce-field']) || empty($aData['wiloke-listgo-nonce-field']) ){
            wp_send_json_error();
        }

        global $wiloke;
        if ( !isset($aData['review']) || empty($aData['review']) ){
            wp_send_json_error(
              array(
                  'review' => esc_html__('Please write something about your review', 'listgo')
              )
            );
        }

        if ( !isset($aData['title']) || empty($aData['title']) ){
            wp_send_json_error(
              array(
                  'review' => esc_html__('Please enter a title for your review', 'listgo')
              )
            );
        }

        $postType = get_post_field('post_type', $aData['post_ID']);

        if ( $postType !== 'listing' ){
            wp_send_json_error();
        }

        $rating = !isset($aData['rating']) || empty($aData['rating']) || ( absint($aData['rating']) > 5 ) ? 5: absint($aData['rating']);
        $userID = get_current_user_id();

        if ( empty($userID) ){
            if ( empty($aData['email']) ){
                wp_send_json_error(array(
                    'email' => $wiloke->aConfigs['translation']['wrongemail']
                ));
            }

	        if ( empty($aData['password']) ){
		        wp_send_json_error(array(
			        'password' => esc_html__('We need your password', 'listgo')
		        ));
	        }

            $aVerifyEmail = WilokeSubmissionUser::verifyEmail($aData['email']);
            if ( !$aVerifyEmail['success'] ){
                wp_send_json_error(array(
                    'email' => $aVerifyEmail['message']
                ));
            }

            if ( isset($wiloke->aThemeOptions['toggle_google_recaptcha']) && ($wiloke->aThemeOptions['toggle_google_recaptcha'] == 'enable') ){
                $aVerifiedreCaptcha = WilokeSubmissionUser::verifyGooglereCaptcha($aData['g-recaptcha-response']);
                if ( $aVerifiedreCaptcha['status'] == 'error' ){
                    wp_send_json_error(array(
                        'g-recaptcha' => $aVerifiedreCaptcha['message']
                    ));
                }
                unset($aData['g-recaptcha-response']);
            }

            $aResult = WilokeSubmissionUser::createUserByEmail($aData['email'], $aData['password']);
            if ( $aResult['success'] === false ){
                wp_send_json_error(array(
                    'email' => $aResult['message']
                ));
            }
            $isRefresh = true;
            $userID = $aResult['message'];
        }

        $postID = wp_insert_post(
            array(
                'post_title'   => $aData['title'],
                'post_content' => $aData['review'],
                'post_status'  => 'publish',
                'post_type'    => 'review'
            )
        );

        // If, in case, customer upload photos
        if ( $canUploadFile ){
            $aAttachIDs = explode(',', $aData['upload_photos']);
        }else{
            if ( isset($_FILES['upload_photos']) && !empty($_FILES['upload_photos']) ){
                $aAttachIDs = array();
                $aPhotos = $_FILES['upload_photos'];
                $aConditionals = array('image/jpeg', 'image/png', 'image/gif', 'image/jpg');
                $size = self::getMaxFileSize();
                $size = absint(str_replace('M', '',$size));

                $allowKB = 1000*1000*$size;

                foreach ( $aPhotos['name'] as $key => $name ){

                    if ( empty($aPhotos['error'][$key]) && ($aPhotos['size'][$key] <= $allowKB) ){
                        if ( in_array($aPhotos['type'][$key], $aConditionals) ){
                            $aFileUpload = array(
                                'name'     => $aPhotos['name'][$key],
                                'type'     => $aPhotos['type'][$key],
                                'tmp_name' => $aPhotos['tmp_name'][$key],
                                'error'    => $aPhotos['error'][$key],
                                'size'     => $aPhotos['size'][$key]
                            );
                            $aAttachIDs[] = self::insertAttachment($aFileUpload);
                        }
                    }
                }
            }
        }

        if ( !empty($aAttachIDs) ){
            $aGallerySettings = Wiloke::getPostMetaCaching($postID, 'rewiew_settings');
            $aGalleryData = array();
            foreach ( $aAttachIDs as $galleryID ){
                $aGalleryData[$galleryID] = wp_get_attachment_image_url($galleryID, 'full');
            }
            $aGallerySettings['gallery'] = $aGalleryData;
            update_post_meta($postID, 'review_settings', $aGallerySettings);
        }

        global $wpdb;
        $tblName = $wpdb->prefix . AlterTableReviews::$tblName;
        $wpdb->insert(
            $tblName,
            array(
                'user_ID'   => isset($userID) ? $userID : $userID,
                'post_ID'   => absint($aData['post_ID']),
                'review_ID' => $postID,
                'rating'    => $rating
            ),
            array(
                '%d',
                '%d',
                '%d',
                '%d'
            )
        );
        update_post_meta($aData['post_ID'], self::$scoreThanksForReviewingKey, 0);

        $commentsCount = get_comments_number($aData['post_ID']);
        $tblPosts = $wpdb->prefix . 'posts';
        $wpdb->update(
            $tblPosts,
            array(
                'comment_count' => absint($commentsCount)+1
            ),
            array(
                'ID' => absint($aData['post_ID'])
            ),
            array(
                '%d'
            ),
            array(
                '%d'
            )
        );

        do_action('wiloke/wiloke_submission/save_review', $postID, $aData['post_ID'], $userID);
        if ( isset($isRefresh) ){
            wp_send_json_success(
                array(
                    'message' => esc_html__('Thanks for your reviewing!', 'listgo'),
                    'refresh' => 'yes',
                    'review_ID' => $postID
                )
            );
        }else{
            $oResult = self::fetchReview('post_date', $postID, $aData['post_ID']);
            ob_start();
            self::renderReviewItem($oResult[0]);
            $review = ob_get_clean();

            wp_send_json_success(
                array(
                    'message' => esc_html__('Thanks for your reviewing!', 'listgo'),
                    'review'  => $review,
                    'refresh' => 'no',
                    'review_ID' => $postID
                )
            );
        }
    }

    public function thanksForReviewing(){
        if ( !isset($_POST['post_ID']) || empty($_POST['post_ID']) || !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
            wp_send_json_error();
        }

        $postType = get_post_field('post_type', $_POST['post_ID']);
        if ( $postType !== 'review' ){
            wp_send_json_error();
        }
        $userID = get_current_user_id();
        $userID = !empty($userID) ? $userID : Wiloke::clientIP();
        $aCurrent = Wiloke::getPostMetaCaching($_POST['ID'], self::$thanksForReviewingKey);
        $aCurrent = !empty($aCurrent) ? $aCurrent : array();
        $aCurrent[] = $userID;

        $score = Wiloke::getPostMetaCaching($_POST['ID'], self::$scoreThanksForReviewingKey);
        $score = empty($score) ? 1 : $score + 1;

        update_post_meta($_POST['post_ID'], self::$scoreThanksForReviewingKey, $score);
        update_post_meta($_POST['post_ID'], self::$thanksForReviewingKey, $aCurrent);
        Wiloke::setPostMetaCaching($_POST['post_ID'], self::$thanksForReviewingKey, $aCurrent);
        wp_send_json_success();
    }

    public static function calculateRating(){
        global $wpdb, $post;
        $tblRating = $wpdb->prefix . AlterTableReviews::$tblName;
        $oResult = $wpdb->get_row(
            $wpdb->prepare(
                 "SELECT COUNT($tblRating.rating) as number_of_ratings, SUM(rating) as total, SUM(IF(rating=5, rating, 0)) as five_stars, SUM(IF(rating=4, rating, 0)) as four_stars, SUM(IF(rating=3, rating, 0)) as three_stars, SUM(IF(rating=2, rating, 0)) as two_stars, SUM(IF(rating=1, rating, 0)) as one_star FROM $tblRating WHERE post_ID=%d",
                 $post->ID
            ),
            ARRAY_A
        );

        return $oResult;
    }

    public static function averageRating($aAverages){
        $prefix = $aAverages['number_of_ratings'] > 1 ? esc_html__('Ratings', 'listgo') : esc_html__('Rating', 'listgo');
        $badStars = round($aAverages['total']/$aAverages['number_of_ratings'], 1);
        ?>
        <li class="review-rating__label">
            <?php self::renderStars($badStars); ?>
            <span class="review-rating__label-title"><?php echo esc_html($aAverages['number_of_ratings']) . ' ' . $prefix; ?></span>
        </li>
        <?php
    }

    public static function diagramLineStars($aAverages, $badStars=0){
        switch ($badStars ){
            case 5:
                $key = 'five_stars';
                break;
            case 4:
                $key = 'four_stars';
                break;
            case 3:
                $key = 'three_stars';
                break;
            case 2:
                $key = 'two_stars';
                break;
            default:
                $key = 'one_star';
                break;
        }

        $average = round($aAverages[$key]/$aAverages['total']*100);
        ?>
        <li class="review-rating__item">
            <?php self::renderStars($badStars); ?>
            <div class="review-rating__bar">
                <div class="review-rating__bar-percent" style="width: <?php echo esc_attr($average) ?>%"></div>
            </div>
        </li>
        <?php
    }

    public static function renderStars($score){
        ?>
        <span class="review-rating__star">
        <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
            <i class="<?php echo esc_attr(self::getStarClass($score, $i)); ?>"></i>
        <?php endfor; ?>
        </span>
        <?php
    }

    public static function getStarClass($score, $compareWith){
        if ( $compareWith < $score ){
            $class = 'fa fa-star';
        }elseif ($compareWith == $score){
            $class = 'fa fa-star';
        }else{
            $class = ceil($score) == $compareWith ? 'fa fa-star-half-o' : 'fa fa-star-o';
        }
        return $class;
    }

    public static function renderReviewItem($oResult){
        $aUserInfo = WilokePublic::getUserMeta($oResult->user_ID);
        $aThanksReviewing = Wiloke::getPostMetaCaching($oResult->ID, WilokePublic::$thanksForReviewingKey);
        $actived = '';
        $countThanks = 0;
        if ( $aThanksReviewing ){
            $currentUser = !empty(WilokePublic::$oUserInfo) ? self::$oUserInfo->ID : Wiloke::clientIP();
            $actived = in_array($currentUser, $aThanksReviewing) ? 'active disabled' : '';
            $countThanks = count($aThanksReviewing);
        }
        $avatar = Wiloke::getUserAvatar($oResult->user_ID, $aUserInfo, array(90, 90));
        ?>
        <li class="comment" data-reviewid="<?php echo esc_attr($oResult->ID); ?>">
            <div class="comment__inner">
                <div class="comment__avatar">
                    <a href="<?php echo get_author_posts_url($oResult->user_ID); ?>">
                    <?php
                    if ( strpos($avatar, 'profile-picture.jpg') === false ) {
                        ?>
                        <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($aUserInfo['display_name']); ?>" height="150" width="150" class="avatar">
                        <?php
                        } else {
                            $firstCharacter = strtoupper(substr($aUserInfo['display_name'], 0, 1));
                            echo '<span style="background-color: '.esc_attr(self::getColorByAnphabet($firstCharacter)).'" class="widget_author__avatar-placeholder">'. esc_html($firstCharacter) .'</span>';
                        }
                    ?>
                    </a>
                </div>
                
                <div class="comment__body">
                    <cite class="comment__name"><?php echo esc_html($oResult->post_title); ?></cite>

                    <span class="listgo__rating">
                        <span class="rating__star">
                            <?php
                            for ( $i = 1; $i<=5; $i++ ) :
                                $startStatus = $i <= absint($oResult->rating) ? 'fa fa-star' : 'fa fa-star-o';
                            ?>
                            <i class="<?php echo esc_attr($startStatus); ?>"></i>
                            <?php endfor; ?>
                        </span>
                        <span class="rating__number"><?php echo esc_html($oResult->rating); ?></span>
                    </span>

                    <span class="comment__date"><?php echo esc_html(date('M d, Y', strtotime($oResult->post_date))); ?></span>

                    <div class="comment__by-role">
                        <span class="comment__by"><span><?php echo esc_html__('By ', 'listgo') ?></span><?php echo esc_html($aUserInfo['display_name']); ?></span>
                        
                        <?php self::renderBadge($aUserInfo['role']); ?>
                    </div>

                    <div class="comment__content">
                        <?php Wiloke::wiloke_kses_simple_html($oResult->post_content); ?>
                        <?php
                            $aReviewSettings = Wiloke::getPostMetaCaching($oResult->ID, 'review_settings');
                            if ( isset($aReviewSettings['gallery']) && !empty($aReviewSettings['gallery']) ) :
                                echo '<div class="comment__gallery popup-gallery">';
                                    foreach ( $aReviewSettings['gallery'] as $galleryID => $originalImg ) :
                                        if ( empty($galleryID) || !is_numeric($galleryID) ){
                                            continue;
                                        }
                                        $thumb = wp_get_attachment_image_url($galleryID, 'thumbnail');
                        ?>
                                        <a href="<?php echo esc_url($originalImg); ?>" class="bg-scroll lazy" data-src="<?php echo esc_url($thumb); ?>"><img class="lazy" data-src="<?php echo esc_url($originalImg); ?>" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" alt="<?php echo esc_attr($oResult->post_title); ?>"></a>
                        <?php
                                    endforeach;
                                echo '</div>';
                            endif;
                        ?>
                    </div>

                    <div class="comment__reaction">
                        <!-- <ul class="comment__reaction-list">
                            <li>
                                <a href="#">
                                    <i class="wil-icon wil-icon-like"></i>
                                    <span>Like</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="wil-icon wil-icon-love"></i>
                                    <span>Love</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="wil-icon wil-icon-haha"></i>
                                    <span>Haha</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="wil-icon wil-icon-wow"></i>
                                    <span>Wow</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="wil-icon wil-icon-sad"></i>
                                    <span>Sad</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="wil-icon wil-icon-angry"></i>
                                    <span>Angry</span>
                                </a>
                            </li>
                        </ul> -->
                        <!-- <a class="wiloke-listgo-thanks-for-reviewing comment-like <?php echo esc_attr($actived); ?>" data-id="<?php echo esc_attr($oResult->ID); ?>" href="#" data-reaction="like">
                            <i class="wil-icon wil-icon-like"></i>
                        </a> -->

                        <a class="wiloke-listgo-thanks-for-reviewing comment-like <?php echo esc_attr($actived); ?>" data-id="<?php echo esc_attr($oResult->ID); ?>" href="#">
                            <i class="wil-icon wil-icon-like"></i>
                            <span class="comment-like__count"><?php echo esc_html($countThanks); ?></span>
                        </a>
                        <!-- <div class="wil-reacted">
                            <div class="wil-reacted__item">
                                <i class="wil-icon wil-icon-like"></i>
                                <span>22</span>
                            </div>
                            <div class="wil-reacted__item">
                                <i class="wil-icon wil-icon-love"></i>
                                <span>3</span>
                            </div>
                            <div class="wil-reacted__item">
                                <i class="wil-icon wil-icon-haha"></i>
                                <span>14</span>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </li>
        <?php
    }

    public function filterWilokeSharingPostsTitle(){
        return '';
    }

    public function filterWilokeSharingPostsCssClass($class){
        return $class . ' action__share-list';
    }

    public function filterWilokeSharingPostsRenderTitle($aAttribute){
        ob_start();
        ?>
         <i class="<?php echo esc_attr($aAttribute['name_class']); ?>"></i> <?php echo esc_html($aAttribute['title']); ?>
        <?php
        $content = ob_get_clean();
        return $content;
    }

    public static function renderUserBadge($role){
        $aBadge = RegisterBadges::getBadgeInfo($role);
        echo '<i class="fa fa-gitlab"></i>' . esc_html($aBadge['label']);
    }

    public function fetchNewReviews(){
        if ( !isset($_POST['post_ID']) || empty($_POST['post_ID']) || !isset($_POST['paged']) || empty($_POST['paged']) || !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce','security', false) ){
            wp_send_json_error();
        }
        $orderBy = $_POST['orderBy'] === 'newest_first' ? 'post_date' : 'top_rating';
        $oResults = self::fetchReview($orderBy, 0, $_POST['post_ID'], $_POST['paged']);

        if ( empty($oResults) ){
            wp_send_json_error();
        }

        ob_start();
        foreach ( $oResults as $aResult ){
            self::renderReviewItem($aResult);
        }
        $content = ob_get_clean();
        wp_send_json_success(
            array(
                'review' => $content
            )
        );
    }

    public static function getUserMeta($userID, $field=''){
        $aUser = Wiloke::getUserMeta($userID, $field);
        return $aUser;
    }

    public static function toggleTabStatus($name){
        global $wiloke;
        $status = !isset($wiloke->aThemeOptions[$name]) || ($wiloke->aThemeOptions[$name]  === 'enable') ? 'enable' : 'disable';
        return $status;
    }

    public static function renderListingTab($name){
        global $wiloke;
        $class = '';
        if ( $name === 'description' ){
            $status = self::toggleTabStatus('listing_toggle_tab_desc');
            $tabName = isset($wiloke->aThemeOptions['listing_tab_desc']) ? $wiloke->aThemeOptions['listing_tab_desc'] : esc_html__('Description', 'listgo');
            $class = 'active';
        }elseif ( $name === 'contact' ){
            $status = self::toggleTabStatus('listing_toggle_tab_contact_and_map');
	        $tabName = isset($wiloke->aThemeOptions['listing_tab_contact_and_map']) ? $wiloke->aThemeOptions['listing_tab_contact_and_map'] : esc_html__('Contact & Map', 'listgo');
        }else{
            $status = self::toggleTabStatus('listing_toggle_tab_review_and_rating');
	        $tabName = isset($wiloke->aThemeOptions['listing_tab_review_and_rating']) ? $wiloke->aThemeOptions['listing_tab_review_and_rating'] : esc_html__('Review & Rating', 'listgo');
        }

        if ( $status === 'enable' ) :
        ?>
        <li class="<?php echo esc_attr($class); ?>"><a href="#tab-<?php echo esc_attr($name); ?>"><?php echo esc_html($tabName); ?></a></li>
        <?php
        endif;
    }

    public function filterListOfSu($aShortcodes){
        $aShortcodes['list']['atts']['icon']['default'] = 'icon: check-square-o';
        $aShortcodes['list']['atts']['class']['default'] = 'wil-icon-list';
        $aShortcodes['list']['desc'] = esc_html__('Styled unordered list. If you want to mark a service as not supported, please add class not-supported to li tab. For example: &lt;li class="not-support">Game:&lt;/li> ', 'listgo');
        return $aShortcodes;
    }

    public static function renderRelatedPosts(){
        global $post, $wiloke;
        if ( isset($wiloke->aThemeOptions['listing_toggle_related_listings']) && ($wiloke->aThemeOptions['listing_toggle_related_listings'] === 'disable') ){
            return false;
        }

        $aArgs = array(
            'post_type'     => $post->post_type,
            'post_status'   => 'publish',
            'posts_per_page'=> 3,
            'post__not_in'  => array($post->ID)
        );

        $getRelatedBy = isset($wiloke->aThemeOptions['listing_related_listings_by']) ? $wiloke->aThemeOptions['listing_related_listings_by'] : 'author';
        switch ( $getRelatedBy ){
            case 'listing_location':
                $aListingLocations = Wiloke::getPostTerms($post, 'listing_location');
                if ( !empty($aListingLocations) && !is_wp_error($aListingLocations) ){
                    foreach ( $aListingLocations as $oListingLocation ){
                        $aTermIDs[] = $oListingLocation->term_id;
                    }
                    $aArgs['tax_query'][] = array(
                        'taxonomy'  => 'listing_location',
                        'field'     => 'term_id',
                        'terms'     => $aTermIDs
                    );
                }
                break;
            case 'listing_cat':
                $aListingCats = Wiloke::getPostTerms($post, 'listing_cat');
                if ( !empty($aListingCats) && !is_wp_error($aListingCats) ){
                    foreach ( $aListingCats as $oListingCat ){
                        $aTermIDs[] = $oListingCat->term_id;
                    }
                    $aArgs['tax_query'][] = array(
                        'taxonomy'  => 'listing_cat',
                        'field'     => 'term_id',
                        'terms'     => $aTermIDs
                    );
                }
                break;
            default:
                $aArgs['author'] = $post->post_author;
                break;
        }

        if ( isset($wiloke->aThemeOptions['listing_related_listings_title']) ){
            $heading = $wiloke->aThemeOptions['listing_related_listings_title'];
        }else{
            $heading = esc_html__('More Listings By ', 'listgo') . '%author%';
        }

        if ( strpos($heading, '%author%') !== false ){
            $aPostAuthor = WilokePublic::getUserMeta($post->post_author);
            $heading = str_replace('%author%', '<strong>'.$aPostAuthor['display_name'].'</strong>', $heading);
        }

        $query = new WP_Query($aArgs);
        $listingImage = null;
        $size = wp_is_mobile() ? array(345, 260) : array(185, 140);

        if ( $query->have_posts() ) :
        ?>
            <div class="listing-single__related">
                <h3 class="listing-single__related-title"><?php Wiloke::wiloke_kses_simple_html($heading); ?></h3>
                <div class="row row-clear-lines">
                    <?php
                    while ($query->have_posts()) : $query->the_post();
                    if (has_post_thumbnail($query->post->ID) ){
                        $thumbnail = get_the_post_thumbnail_url($query->post->ID, $size);
                    }else{
                         $listingImage = $listingImage == null ? wp_get_attachment_image_url($wiloke->aThemeOptions['listing_header_image']['id'], $size) : $listingImage;
                         $thumbnail = $listingImage;
                    }
                    ?>
                    <div class="col-sm-6 col-md-4">
                       <div class="listing_related-item">
                           <a href="<?php echo esc_url(get_permalink($query->post->ID)); ?>">
                                <div class="listing_related-item__media lazy" data-src="<?php echo esc_url($thumbnail); ?>">
                                   <img class="lazy" data-src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($query->post->post_title); ?>" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==">
                               </div>
                               
                               <div class="listing_related-item__body">
                                   <h2 class="listing_related-item__title"><?php echo esc_html($query->post->post_title); ?></h2>
                                   <?php WilokePublic::renderAverageRating($query->post, array('toggle_render_rating'=>'enable')); ?>
                               </div>
                           </a>
                       </div>
                   </div>
                    <?php endwhile;?>
                </div>
            </div>
        <?php
        endif;
        wp_reset_postdata();
    }

    public static function addQueryToLink($link, $query){
        if ( strpos($link, '?') !== false ){
            $link .= '&'.$query;
        }else{
            $link .= '?'.$query;
        }

        return $link;
    }

    public static function totalMyListings(){
        if ( isset(self::$aTemporaryCaching['total_my_listings']) ){
            return self::$aTemporaryCaching['total_my_listings'];
        }

        global $wpdb;
        $tblName = $wpdb->prefix . 'posts';
        $totalListings = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(ID) FROM $tblName WHERE post_author=%d AND post_type=%s AND post_status !='trash' AND post_status!='auto-draft'",
                self::$oUserInfo->ID, 'listing'
            )
        );

        self::$aTemporaryCaching['total_my_listings'] = $totalListings;

        return self::$aTemporaryCaching['total_my_listings'];
    }

    public static function totalMyFavorites(){
        if ( isset(self::$aTemporaryCaching['total_my_favorites']) ){
            return self::$aTemporaryCaching['total_my_favorites'];
        }

        global $wpdb;
        $tblName = $wpdb->prefix.AlterTableFavirote::$tblName;
        $totalFavorites = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(post_ID) FROM $tblName WHERE user_ID=%d",
                self::$oUserInfo->ID
            )
        );

        self::$aTemporaryCaching['total_my_favorites'] = $totalFavorites;

        return self::$aTemporaryCaching['total_my_favorites'];
    }

    public static function quickUserInformation(){
        if ( !class_exists('WilokeListGoFunctionality\Register\RegisterWilokeSubmission') ){
            return false;
        }

        if ( !empty(self::$oUserInfo) ) :
            global $woocommerce, $wiloke;
            $aWilokeSubmissionSettings = WilokeWilokeSubmission::getSettings();

            if ( !isset($aWilokeSubmissionSettings['toggle']) || $aWilokeSubmissionSettings['toggle'] === 'disable' ){
                return false;
            }

            $avatar = Wiloke::getUserAvatar(null, get_object_vars(self::$oUserInfo), 'thumbnail');

            $accountPage = get_permalink($aWilokeSubmissionSettings['myaccount']);
            if ( strpos($accountPage, '?') !== false ){
                $myListingPage  = $accountPage . '&mode=my-listings';
                $favouritesPage = $accountPage . '&mode=my-favorites';
            }else{
                $myListingPage  = $accountPage . '?mode=my-listings';
                $favouritesPage = $accountPage . '?mode=my-favorites';
            }

            $totalListings = self::totalMyListings();
            $totalFavorites = self::totalMyFavorites();
            $myAccount = get_permalink($aWilokeSubmissionSettings['myaccount']);
            $aBadge = RegisterBadges::getBadgeInfo(self::$oUserInfo->role);
        ?>
            <div class="header__user">
                <div class="tb">
                    <div class="tb__cell">
                        <div class="user__avatar">
                            <?php
                            if ( strpos($avatar, 'profile-picture.jpg') === false ) {
                            ?>
                            <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr(self::$oUserInfo->display_name); ?>" height="150" width="150" class="avatar">
	                        <?php
	                        } else {
                                $firstCharacter = strtoupper(substr(self::$oUserInfo->display_name, 0, 1));
		                        echo '<span style="background-color: '.esc_attr(self::getColorByAnphabet($firstCharacter)).'" class="widget_author__avatar-placeholder">'. esc_html($firstCharacter) .'</span>';
	                        }
	                        ?>
                        </div>
                    </div>
                </div>
                <div class="user__menu">
                    <ul>
                        <li class="user__menu__header wiloke-view-profile">
                            <div class="user__header__avatar">
                                <?php
                                    if ( strpos($avatar, 'profile-picture.jpg') === false ) {
                                    ?>
                                    <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr(self::$oUserInfo->display_name); ?>" height="150" width="150" class="avatar">
                                    <?php
                                    } else {
                                        $firstCharacter = strtoupper(substr(self::$oUserInfo->display_name, 0, 1));
                                        echo '<span style="background-color: '.esc_attr(self::getColorByAnphabet($firstCharacter)).'" class="widget_author__avatar-placeholder">'. esc_html($firstCharacter) .'</span>';
                                    }
                                ?>
                            </div>
                            <div class="user__header__info">
                                <a href="<?php echo esc_url($accountPage); ?>">
                                    <h6><?php echo esc_html(self::$oUserInfo->display_name); ?></h6>
                                    <span><?php echo esc_html($aBadge['label']); ?></span>
                                </a>
                            </div>
                        </li>
                        <li class="user__menu__item wiloke-view-dashboard">
                            <a href="<?php echo esc_url($accountPage); ?>">
                                <i class="fa fa-home"></i>
                                <?php esc_html_e('Dashboard', 'listgo'); ?>
                            </a>
                        </li>
                        <li class="user__menu__item wiloke-view-mylistings">
                            <a href="<?php echo esc_url($myListingPage); ?>">
                                <i class="fa fa-list"></i>
                                <?php esc_html_e('My listings', 'listgo'); ?>
                                <span class="count"><?php echo esc_html($totalListings); ?></span>
                            </a>
                        </li>
                        <li class="user__menu__item wiloke-view-favorites">
                            <a href="<?php echo esc_url($favouritesPage); ?>">
                                <i class="fa fa-heart-o"></i>
                                <?php esc_html_e('Favorites', 'listgo'); ?>
                                <span class="count"><?php echo esc_html($totalFavorites); ?></span>
                            </a>
                        </li>
                       <li class="user__menu__item wiloke-view-billing"><a href="<?php echo esc_url(WilokePublic::addQueryToLink($myAccount, 'mode=my-billing')); ?>"><i class="icon_creditcard"></i> <?php esc_html_e('Billing', 'listgo'); ?></a></li>
                         <?php if ( function_exists('is_woocommerce') ) : ?>
                        <li class="user__menu__item wiloke-view-woocommerce">
                            <a href="<?php echo esc_url(wc_get_cart_url()); ?>">
                                <i class="icon_creditcard"></i>
                                <?php  esc_html_e('My cart', 'listgo'); ?>
                                <span class="count"><?php echo esc_html($woocommerce->cart->cart_contents_count); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="user__menu__item  wiloke-view-logout">
                            <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>">
                                <i class="fa fa-arrow-circle-o-left"></i>
                                <?php esc_html_e('Logout', 'listgo'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        <?php
        endif;
    }

    public function updateProfile(){
        if ( !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
            wp_send_json_error(array(
                'message' => esc_html__('The security code is wrong.', 'listgo')
            ));
        }

        parse_str($_POST['data'], $aData);

        if ( !isset($aData['user_email']) || empty($aData['user_email']) || !is_email($aData['user_email']) ){
            wp_send_json_error(array(
                'user_email' => esc_html__('You entered an invalid email address.', 'listgo')
            ));
        }

        if ( !isset($aData['display_name']) || empty($aData['display_name']) ){
            wp_send_json_error(array(
                'display_name' => esc_html__('The display name is required.', 'listgo')
            ));
        }

        if ( !isset($aData['nickname']) || empty($aData['nickname']) ){
            wp_send_json_error(array(
                'nickname' => esc_html__('The Nickname is required.', 'listgo')
            ));
        }

        $userID = get_current_user_id();

        $aUserData = array(
            'ID'          => $userID,
            'nickname'    => $aData['nickname'],
            'display_name'=> $aData['display_name'],
            'user_email'  => $aData['user_email'],
            'description' => $aData['description'],
            'first_name'  => $aData['first_name'],
            'last_name'   => $aData['last_name'],
            'user_url'    => $aData['user_url']
        );

        if ( isset($aData['new_password']) && !empty($aData['new_password']) ){
            if ( empty($aData['current_password']) ){
                wp_send_json_error(array(
                    'current_password' => esc_html__('The current password is wrong.', 'listgo')
                ));
            }

            if ( empty($aData['confirm_new_password']) || ($aData['confirm_new_password'] !== $aData['new_password']) ){
                wp_send_json_error(array(
                    'confirm_new_password' => esc_html__('These password don\'t match.  Please try again!', 'listgo')
                ));
            }

            $oUserData = get_user_by('id', $userID);

            if ( !wp_check_password($aData['current_password'], $oUserData->user_pass, $userID) ){
                wp_send_json_error(array(
                    'current_password' => esc_html__('This password is wrong. Please try again!', 'listgo')
                ));
            }

            $aUserData['user_pass'] = $aData['new_password'];
        }
        update_user_meta($userID, 'wiloke_cover_image', $aData['wiloke_cover_image']);
        update_user_meta($userID, 'wiloke_profile_picture', $aData['wiloke_profile_picture']);
        update_user_meta($userID, 'wiloke_user_socials', $aData['wiloke_user_socials']);
        update_user_meta($userID, 'wiloke_color_overlay', $aData['wiloke_color_overlay']);
        update_user_meta($userID, 'wiloke_address', $aData['address']);
        update_user_meta($userID, 'wiloke_phone', $aData['wiloke_phone']);
        wp_update_user($aUserData);
        $aUserData = get_userdata($userID);
        $aUserData = get_object_vars($aUserData);
        WilokeUser::putUserToRedis($aUserData);

        wp_send_json_success(
            array(
                'message' => esc_html__('Congrats, Your information have been updated!', 'listgo')
            )
        );
    }

    public function fetchMyBillingHistory(){
        if ( !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
            wp_send_json_error();
        }

        $postsPerPage = isset($_POST['postsperpage']) && absint($_POST['postsperpage']) <= 30 ?  $_POST['postsperpage'] : 10;
        $paged = isset($_POST['paged']) && !empty($_POST['paged']) ? absint($_POST['paged']) : 1;
        $offset = $paged === 1 ? 0 : $paged*$postsPerPage - 1;

        global $wpdb;
        $tblHistory = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

        $aResults = $wpdb->get_results(
            $wpdb->prepare(
				"SELECT $tblHistory.* FROM $tblHistory WHERE $tblHistory.user_ID=%d AND (status = 'pending' OR status = 'completed') LIMIT $postsPerPage OFFSET $offset",
				WilokePublic::$oUserInfo->ID
			)
        );

        if ( !empty($aResults) && !is_wp_error($aResults) ){
            ob_start();
            foreach ( $aResults as $oResult ){
                self::renderBillingItem($oResult);
            }
            $content = ob_get_clean();
            wp_send_json_success(
                array(
                    'total'     => count($aResults),
                    'content'   => $content
                )
            );
        }else{
            wp_send_json_success(
                array(
                    'total'=> 0,
                    'content'   => esc_html__('There are no listings', 'listgo')
                )
            );
        }
    }

    public static function renderBillingItem($oBilling){
        ?>
        <tr>
            <td><?php echo date('Y/m/d', strtotime($oBilling->created_at)); ?></td>
            <td><?php echo esc_html($oBilling->method); ?></td>
            <td><?php echo empty($oBilling->total) ? esc_html__('Free', 'listgo') : Payment::renderPrice($oBilling->total); ?></td>
            <td><?php echo get_the_title($oBilling->package_ID); ?></td>
            <td><?php echo $oBilling->profile_status; ?></td>
        </tr>
        <?php
    }

    public function fetchNewListingItemForManagenent(){
        if ( !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
            wp_send_json_error();
        }

        $postsPerPage = isset($_POST['postsperpage']) && absint($_POST['postsperpage']) <= 30 ?  $_POST['postsperpage'] : 10;
        $aArgs = array(
            'post_type'         => 'listing',
            'author'            => get_current_user_id(),
            'posts_per_page'    => $postsPerPage,
            'paged'             => absint($_POST['paged'])
        );

        if ( $_POST['post_status'] === 'all' ){
            $aArgs['post_status'] = array('publish', 'pending', 'processing', 'expired', 'renew', 'temporary_closed');
        }else{
            $aArgs['post_status'] = $_POST['post_status'];
        }

        $query = new WP_Query($aArgs);

        if ( !$query->have_posts() ){
            wp_send_json_success(
                array(
                    'total'=> 0,
                    'content'   => esc_html__('There are no listings', 'listgo')
                )
            );
        }

        $aPaymentSettings = WilokePublic::getPaymentField();
        $checkoutPage = get_permalink($aPaymentSettings['checkout']);
        $pricingPage = get_permalink($aPaymentSettings['package']);
        $userID = get_current_user_id();
        $pinnedTop = WilokeFrontendListingManagement::getPinnedToTop($userID);
        ob_start();
        while ( $query->have_posts()  ){
            $query->the_post();
            WilokeFrontendListingManagement::renderListingManagementItem($query->post, $checkoutPage, $pricingPage, $pinnedTop);
        }
        $content = ob_get_clean();

        wp_send_json_success(
            array(
                'total'     => $query->found_posts,
                'content'   => $content
            )
        );
    }

    public static function renderFavoriteItem($post){
        $thumbnail = has_post_thumbnail($post->ID) ? get_the_post_thumbnail_url($post->ID) : get_template_directory_uri() . '/img/no-image.jpg';
        ?>
             <div class="f-listings-item">
                <div class="f-listings-item__media">
                    <a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php Wiloke::lazyLoad($thumbnail); ?></a>
                </div>
                <div class="overflow-hidden">
                    <h2 class="f-listings-item__title"><a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php echo get_the_title($post->ID); ?></a></h2>
                    <p><?php Wiloke::wiloke_content_limit(100, $post, false, $post->post_content, false); ?></p>
                </div>
                <div class="f-listings-item__meta">
                    <span>
                        <a class="js-remove-favorite" data-postid="<?php echo esc_attr($post->ID); ?>" href="#">
                            <i class="fa fa-trash-o"></i> <?php esc_html_e('Remove', 'listgo'); ?>
                        </a>
                    </span>
                </div>
            </div>
        <?php
    }

    public function fetchNewFavoriteItems(){
        if ( !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
            wp_send_json_error();
        }

        $postsPerPage = isset($_POST['postsperpage']) && absint($_POST['postsperpage']) <= 30 ?  $_POST['postsperpage'] : 10;
        $paged = isset($_POST['paged']) && !empty($_POST['paged']) ? absint($_POST['paged']) : 1;
        $offset = ($paged-1)*$postsPerPage;

        global $wpdb;
        $tblPosts = $wpdb->prefix . 'posts';
        $tblFavorite = $wpdb->prefix . AlterTableFavirote::$tblName;

        $aResults = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT $tblPosts.* FROM $tblPosts INNER JOIN (SELECT DISTINCT $tblFavorite.post_ID FROM $tblFavorite WHERE $tblFavorite.user_ID=%d ORDER BY $tblFavorite.post_ID DESC LIMIT $postsPerPage OFFSET $offset) as tblFavorites ON ($tblPosts.ID=tblFavorites.post_ID)",
				get_current_user_id()
			)
		);

        if ( !empty($aResults) && !is_wp_error($aResults) ){
            ob_start();
            foreach ( $aResults as $oResult ){
                self::renderFavoriteItem($oResult);
            }
            $content = ob_get_clean();
            wp_send_json_success(
                array(
                    'total'     => count($aResults),
                    'content'   => $content
                )
            );
        }else{
            wp_send_json_success(
                array(
                    'total'=> 0,
                    'content'   => esc_html__('There are no listings', 'listgo')
                )
            );
        }
    }

    public function temporaryClosedListing(){
        if ( !self::checkAjaxSecurity($_POST) || !isset($_POST['post_ID']) || empty($_POST['post_ID']) ){
            wp_send_json_error(
                esc_html__('You do not have permission to change the post status.', 'listgo')
            );
        }

        $userID = get_current_user_id();
        $postAuthor = get_post_field('post_author', $_POST['post_ID']);
        if (absint($postAuthor) !== absint($userID) ){
            wp_send_json_error(
                esc_html__('You are not author of the post.', 'listgo')
            );
        }

        $postStatus = get_post_field('post_status', $_POST['post_ID']);
        if ( $postStatus !== 'publish' && $postStatus !== 'temporary_closed' ){
            wp_send_json_error(
                esc_html__('You do not have permission to change the post status.', 'listgo')
            );
        }

        if ( $postStatus === 'publish' ){
            $newStatus = 'temporary_closed';
        }else{
            $newStatus = 'publish';
        }

        wp_update_post(
            array(
                'post_status'   => $newStatus,
                'post_type'     => 'listing',
                'post_author'   => $userID,
                'ID'            => $_POST['post_ID']
            )
        );

        wp_send_json_success($newStatus);
    }

    public function removeListing(){
        if ( !self::checkAjaxSecurity($_POST) || !isset($_POST['post_ID']) || empty($_POST['post_ID']) ){
            wp_send_json_error(
                esc_html__('You do not have permission to change the post status.', 'listgo')
            );
        }

        $userID = get_current_user_id();
        $postAuthor = get_post_field('post_author', $_POST['post_ID']);
        if (absint($postAuthor) !== absint($userID) ){
            wp_send_json_error(
                esc_html__('You are not author of the post.', 'listgo')
            );
        }

        wp_delete_post($_POST['post_ID']);
        wp_send_json_success();
    }

    public static function quickAddListingBtn(){
        if ( !class_exists('WilokeListGoFunctionality\Shortcodes\Shortcodes') ){
            return false;
        }

        if ( is_user_logged_in() ){
            if ( !current_user_can('edit_theme_options') && self::$oUserInfo->role !== 'wiloke_submission' ){
                return '';
            }
        }

        global $wiloke;
        $toggle = WilokePublic::getPaymentField('toggle');
        if ( empty($toggle) || $toggle === 'disable' ){
            return false;
        }
        $additionalClass = isset($wiloke->aThemeOptions['toggle_add_listing_btn_on_mobile']) ? $wiloke->aThemeOptions['toggle_add_listing_btn_on_mobile'] : 'add-listing-disable-on-mobile';
        ?>
        <div class="header__add-listing <?php echo esc_attr($additionalClass); ?>">
            <div class="tb">
                <div class="tb__cell">
                    <a href="<?php echo esc_url(WilokeCustomerPlan::renderAddListingLink()); ?>"><span><?php esc_html_e('+ Add Listing', 'listgo'); ?></span></a>
                </div>
            </div>
        </div>
        <?php
    }

    public function filterAvatar($avatar, $idOrEmail, $size){
        if ( is_numeric( $idOrEmail ) ){
            $newAvatar = Wiloke::getUserAvatar($idOrEmail, null, array($size, $size));
        }elseif( is_object( $idOrEmail ) ){
            if ( ! empty($idOrEmail->user_id) ) {
                $newAvatar = Wiloke::getUserAvatar($idOrEmail->user_id, null, array($size, $size));
            }
        }else{
            $oUser = get_user_by('email', $idOrEmail);
            if ( isset($oUser->ID) ){
                $newAvatar = Wiloke::getUserAvatar($oUser->ID, null, array($size, $size));
            }
        }

        if ( isset($newAvatar) && !empty($newAvatar) ){
            return '<img src="'.esc_url($newAvatar).'" alt="'.esc_html__('Avatar', 'listgo').'" width="'.esc_attr($size).'" height="'.esc_attr($size).'" class="avatar avatar-'.esc_attr($size).' photo">';
        }
        return $avatar;
    }

    public static function quickLoginRegisters(){
        if ( !empty(self::$oUserInfo) ){
            return false;
        }
        ?>
         <div class="header__user">
            <div class="tb">
                <div class="tb__cell">
                    <div class="user__icon" data-modal="#modal-login">
                        <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                             width="23px" height="23px" viewBox="0 0 448.000000 432.000000"
                             preserveAspectRatio="xMidYMid meet">
                            <g transform="translate(0.000000,432.000000) scale(0.100000,-0.100000)"
                            fill="#fff" stroke="none">
                            <path d="M2100 4314 c-84 -11 -201 -39 -280 -66 -191 -68 -331 -157 -481 -307
                            -188 -188 -299 -392 -355 -651 -26 -119 -26 -381 0 -500 55 -257 169 -466 355
                            -651 237 -237 514 -360 846 -375 224 -10 415 31 623 133 l112 56 48 -18 c208
                            -78 490 -269 657 -446 287 -303 482 -715 521 -1101 l7 -68 -1913 0 -1913 0 7
                            67 c43 417 266 864 582 1162 97 92 114 119 114 179 0 108 -99 183 -202 152
                            -51 -15 -214 -171 -331 -315 -306 -379 -488 -871 -491 -1327 -1 -149 4 -165
                            68 -212 l27 -21 2139 0 2139 0 27 21 c65 48 69 62 68 217 -5 520 -233 1063
                            -614 1461 -165 173 -334 302 -551 422 -57 32 -106 59 -108 60 -2 2 26 42 61
                            89 180 239 269 530 254 825 -16 330 -139 606 -375 841 -182 182 -382 293 -631
                            350 -83 19 -331 33 -410 23z m371 -343 c179 -46 319 -127 449 -260 129 -130
                            212 -278 257 -457 24 -95 24 -333 0 -428 -46 -185 -125 -324 -262 -461 -137
                            -137 -276 -216 -461 -262 -95 -24 -333 -24 -428 0 -182 46 -328 128 -462 261
                            -133 134 -215 280 -261 462 -24 95 -24 333 0 428 45 179 128 327 257 457 147
                            150 309 236 513 275 100 19 299 12 398 -15z"/>
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function putLoginRegisterToFooter(){
        if ( empty(self::$oUserInfo) ){
            include get_template_directory() . '/wiloke-submission/signup-signin-popup.php';
        }
    }

    public function addNewClassToTwitterButton($btnClass){
        return 'login__twitter';
    }

    public function addNewClassToFacebookButton($btnClass){
        return 'login__facebook';
    }
    
    public function addNewClassToGoogleButton($btnclass){
        return 'login__google';
    }

    public function beforeInsertUserWithSocialMediaLogin($aUserData){
        $aUserData['role'] = 'wiloke_submission';
        return $aUserData;
    }
    
    public function updateUserMeta($userID, $oUser, $method){
        if ( $method === 'twitter' ){
            update_user_meta($userID, 'wiloke_user_socials', array('twitter'=>'https://twitter.com/'.$oUser->screen_name));
            update_user_meta($userID, 'wiloke_address', $oUser->location);
        }else if ( $method === 'facebook' ){
            update_user_meta($userID, 'wiloke_user_socials', array('facebook'=>$oUser['link']));
        }else if ( $method === 'google' ){
            update_user_meta($userID, 'wiloke_user_socials', array('google-plus'=>'https://plus.google.com/u/0/'. $oUser['sub']));
        }
        wp_update_user( array ('ID' => $userID, 'role' => 'wiloke_submission') );

        $aUserData = get_userdata($userID);
        $aUserData = get_object_vars($aUserData);
        WilokeUser::putUserToRedis($aUserData);
    }
    
    public function afterLoggedWithSocialMediaRedirectTo($redirectTo, $isFirsTimeLogin){
        if ( $isFirsTimeLogin ){
            $myAccount = WilokePublic::getPaymentField('myaccount');
            if ( empty($myAccount) ){
                return '';
            }
            return get_permalink($myAccount);
        }
        global $wp;
        return home_url(add_query_arg(array(),$wp->request));
    }

    public static function renderBadge($input){
        if ( class_exists('WilokeListGoFunctionality\Register\RegisterBadges') ){
            if ( empty($input) ){
                $aBadge = RegisterBadges::getBadgeInfo(0);
            }else{
                if ( is_numeric($input) ){
                    $aUser = self::getUserMeta($input);
                    $role = $aUser['role'];
                }else{
                    $role = $input;
                }
                $aBadge = RegisterBadges::getBadgeInfo($role);
            }
            ?>
            <span class="member-item__role" style="color: <?php echo esc_attr($aBadge['color']); ?>">
                <?php if ( !empty($aBadge['image']) ) : ?>
                <img src="<?php echo esc_url($aBadge['image']); ?>" alt="<?php esc_html_e('Badge', 'listgo'); ?>">
                <?php else: ?>
                <i class="<?php echo esc_attr($aBadge['badge']); ?>"></i>
                <?php endif; ?>
                <?php echo esc_html($aBadge['label']); ?>
            </span>
            <?php
        }
    }

	public static function renderPostDateOnBlog(){
	    global $post;
        $aPostDate = get_the_date("d/M", $post->ID);
        $aPostDate = explode('/', $aPostDate);
	    ?>
        <div class="post__date">
            <span class="day"><?php echo esc_html($aPostDate[0]); ?></span>
            <span class="month"><?php echo esc_html($aPostDate[1]); ?></span>
        </div>
        <?php
	}

	public static function renderComment(){
	    $commentCount = get_comments_number();

	    if ( $commentCount <= 1 ) {
	        echo esc_html($commentCount) .  ' ' . esc_html__('Comment', 'listgo');
	    }else{
	        echo esc_html($commentCount) .  ' ' . esc_html__('Comments', 'listgo');
	    }
	}

	public static function renderPagination($wp_query=null, $atts=array()){
        if ( empty($wp_query) )
        {
            global $wp_query;
        }

        $cssClass = isset($atts['class']) ? 'nav-links ' . $atts['class'] :  'nav-links text-left';
        $paged = isset($atts['paged']) && !empty($atts['paged']) ? $atts['paged'] : get_query_var('paged', 1);
        ?>
        <div class="<?php echo esc_attr($cssClass); ?>">
            <?php
            $big = 999999999; // need an unlikely integer
            echo paginate_links( array(
                'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format'    => '?paged=%#%',
                'show_all'  => false,
                'prev_next' => true,
                'prev_text' => esc_html__('Previous', 'listgo'),
                'next_text' => esc_html__('Next', 'listgo'),
                'current'   => max( 1, $paged ),
                'total'     => $wp_query->max_num_pages
            ) );
            ?>
        </div>
        <?php
	}

	public static function getBlogSettings(){
	    global $wiloke;
	    if ( !empty($wiloke->aThemeOptions) && class_exists('ReduxFrameworkPlugin') ){
            if ( is_page_template('templates/blog-standard.php') ){
                $aArgs['layout'] = 'post__standard';
            }elseif ( is_page_template('templates/blog-grid.php') ){
                $aArgs['layout'] = 'post__grid';
            }else{
                $aArgs['layout'] = $wiloke->aThemeOptions['blog_layout'];
            }

            $aArgs['sidebar'] = $wiloke->aThemeOptions['blog_sidebar'];
            switch ($aArgs['sidebar']){
                case 'left':
                    $aArgs['main_class'] = 'col-md-9 col-md-push-3';
                    break;
                case 'right':
                    $aArgs['main_class'] = 'col-md-9';
                    break;
                default:
                    $aArgs['main_class'] = $aArgs['layout'] === 'post__standard' ? 'col-md-8 col-md-offset-2' : 'col-md-12';
                    break;
            }

            if ( $aArgs['layout'] === 'post__standard' ){
                $aArgs['img_size']      = 'large';
                $aArgs['item_class']    = 'col-xs-12';
            }else{
                $aArgs['img_size']      = 'wiloke_listgo_455x340';
                $aArgs['item_class']    = $wiloke->aThemeOptions['blog_layout_grid_on_desktops'] . ' ' . $wiloke->aThemeOptions['blog_layout_grid_on_smalls'];
            }
            $aArgs['limit_character']   = $wiloke->aThemeOptions['general_content_limit'];
        }else{
            $aArgs['main_class']        = 'col-md-8 col-md-offset-2';
            $aArgs['layout']            = 'post__standard';
            $aArgs['sidebar']           = 'no';
            $aArgs['item_class']        = 'col-xs-12';
            $aArgs['img_size']          = 'large';
            $aArgs['limit_character']   = 115;
        }

	    return $aArgs;
    }

    public static function getMaxFileSize(){
        return ini_get('upload_max_filesize');
    }

    public static function parseLocationQuery($aData, $isFocusGetTerm=false){
        $aArgs = array();
        $isFoundIt = false;
        $aData['listing_locations'] = trim($aData['listing_locations']);
        if ( isset($aData['location_place_id']) && !empty($aData['location_place_id']) ){ 
            $aDetectLocations = get_terms(
                array(
                    'taxonomy'   => 'listing_location',
                    'hide_empty' => true,
                    'meta_key'   => 'wiloke_listing_location_place_id',
                    'meta_value' => $aData['location_place_id']
                )
            );

            if ( !empty($aDetectLocations) && !is_wp_error($aDetectLocations) ){
                foreach ( $aDetectLocations as $oDetectedLocation ){
                    $aLocationIDs[] = $oDetectedLocation->term_id;
                }
                $aArgs = array(
                    'taxonomy'  => 'listing_location',
                    'field'     => 'term_id',
                    'terms'     => $aLocationIDs
                );

                $isFoundIt = true;
            }
        }

        if ( !$isFoundIt ){
            if ( is_numeric($aData['listing_locations']) ){
                $aArgs = array(
                    'taxonomy'  => 'listing_location',
                    'field'     => 'term_id',
                    'terms'     => absint($aData['listing_locations'])
                );
            }else{
                $aDetectLocations = get_terms(array('taxonomy'=>'listing_location', 'name__like' => strtolower($aData['listing_locations'])));
                if ( !empty($aDetectLocations) && !is_wp_error($aDetectLocations) ){
                    foreach ( $aDetectLocations as $oDetectedLocation ){
                        $aLocationIDs[] = $oDetectedLocation->term_id;
                    }
                    $aArgs = array(
                        'taxonomy'  => 'listing_location',
                        'field'     => 'term_id',
                        'terms'     => $aLocationIDs
                    );
                }else{
                    $aDetectLocations = get_term_by('slug', sanitize_title($aData['listing_locations']),'listing_location');
                    if ( !empty($aDetectLocations) && !is_wp_error($aDetectLocations) ){
                        $aArgs = array(
                            'taxonomy'  => 'listing_location',
                            'field'     => 'slug',
                            'terms'     => $aDetectLocations->term_id
                        );
                    }else{
                        $aGetFirstObject = explode(',', $aData['listing_locations']);
                        if ( count($aGetFirstObject) > 1 ){
                            $aDetectLocations = get_terms(array('taxonomy'=>'listing_location', 'name__like' => strtolower($aGetFirstObject[0])));
                            if ( !empty($aDetectLocations) && !is_wp_error($aDetectLocations) ){
                                foreach ( $aDetectLocations as $oDetectedLocation ){
                                    $aLocationIDs[] = $oDetectedLocation->term_id;
                                }
                                $aArgs = array(
                                    'taxonomy'  => 'listing_location',
                                    'field'     => 'term_id',
                                    'terms'     => $aLocationIDs
                                );
                            }
                        }
                    }
                }
            }
        }

        return $aArgs;
    }

    public static function getTaxesInSearchQuery(){
        $aTaxQuery = array();

        if ( isset($_REQUEST['s_listing_location']) && !empty($_REQUEST['s_listing_location']) ){
            $aData = $_REQUEST;
            $aData['listing_locations'] = isset($aData['s_listing_location']) ? $aData['s_listing_location'] : '';
            $aLocationData = self::parseLocationQuery($aData);
            if ( empty($aLocationData) ){
                return -1;
            }
            $aTaxQuery[] = $aLocationData;
        }

        if ( isset($_REQUEST['s_listing_tag']) && !empty($_REQUEST['s_listing_tag']) ){
            $aTaxQuery[] = array(
                'taxonomy' => 'listing_tag',
                'field'    => 'term_id',
                'terms'    => $_REQUEST['s_listing_tag']
            );
        }

        if ( isset($_REQUEST['s_listing_cat']) && !empty($_REQUEST['s_listing_cat']) ){
            if ( is_numeric($_REQUEST['s_listing_cat']) ){
                $field = 'term_id';
                $termID = absint($_REQUEST['s_listing_cat']);
            }else if ( is_array($_REQUEST['s_listing_cat']) ){
                $field = 'term_id';
                $termID = array_filter($_REQUEST['s_listing_cat'], function($val){
                    return !empty($val);
                });
            }else{
                $field = 'slug';
                $termID = trim($_REQUEST['s_listing_cat']);
            }

            if ( !empty($termID) ){
                $aTaxQuery[] = array(
                    'taxonomy' => 'listing_cat',
                    'field'    => $field,
                    'terms'    => $termID
                );
            }
        }

        if ( !empty($aTaxQuery) && count($aTaxQuery) > 1 ){
            $aTaxQuery['relation'] = 'AND';
        }

        return $aTaxQuery;
    }

    public function removeVideoOutOfContent($cached_html){
        if ( FrontendManageSingleListing::packageAllow('toggle_allow_embed_video') ){
            return $cached_html;
        }

        return '';
    }

    /**
	 * Add Location By
	 * @since 1.0s
	 */
	public static function addLocationBy(){
		global $wiloke;

		if ( empty($wiloke) ){
			return 'default';
		}

		if ( !isset($wiloke->aThemeOptions['add_listing_select_location_type']) ){
			return 'default';
		}

		return $wiloke->aThemeOptions['add_listing_select_location_type'];
	}

	public static function inListingTemplates($aTemplate){
	    global $wiloke, $post;
        if ( !is_array($aTemplate) ){
            $aTemplate = array($aTemplate);
        }

        if ( is_page_template('default') ){
            $currentTemplate = $wiloke->aThemeOptions['listing_layout'];
            $currentTemplate = strpos($currentTemplate, '.php') === false ? $currentTemplate . '.php' : $currentTemplate;
        }else{
            $currentTemplate = get_page_template_slug($post->ID);
        }

        return in_array($currentTemplate, $aTemplate);
	}

	public function addGooglereCAPTCHA(){
	    global $wiloke;
	    if ( !isset($wiloke->aThemeOptions['toggle_google_recaptcha']) || ($wiloke->aThemeOptions['toggle_google_recaptcha'] == 'disable') || is_user_logged_in() ){
            return '';
	    }
	    ?>
        <div class="form-item">
            <div class="g-recaptcha" data-sitekey="<?php echo esc_attr(trim($wiloke->aThemeOptions['google_recaptcha_site_key'])); ?>"></div>
        </div>
        <?php
	}

	public function addGooglereCAPTCHAToReviewForm(){
	    global $wiloke;
	    if ( !isset($wiloke->aThemeOptions['toggle_google_recaptcha']) || ($wiloke->aThemeOptions['toggle_google_recaptcha'] == 'disable') || is_user_logged_in() ){
            return '';
	    }
	    ?>
	    <div class="col-sm-12" style="margin-bottom: 30px;">
            <div class="g-recaptcha" data-sitekey="<?php echo esc_attr(trim($wiloke->aThemeOptions['google_recaptcha_site_key'])); ?>"></div>
        </div>
        <?php
	}
}