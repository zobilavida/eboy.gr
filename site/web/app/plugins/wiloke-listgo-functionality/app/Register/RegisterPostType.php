<?php
/**
 * Register Post Type
 * @since 1.0
 */
namespace WilokeListGoFunctionality\Register;

class RegisterPostType implements RegisterInterface{
    public function __construct()
    {
        add_action('init', array($this, 'register'));
	    add_filter('posts_where', array($this, 'editListTableQuery'), 10, 2);
	    add_filter('save_post_listing', array($this, 'updateLatLongForEachListing'), 10, 2);
	    add_action('init', array($this, 'updateLatitudeLongitudeForEachListing'));
    }

    public function updateLatitudeLongitudeForEachListing(){
    	if ( !get_option('wiloke_listgo_updated_latitude_and_longitude') ){
    		$query = new \WP_Query(
    			array(
    				'post_type' => 'listing',
				    'posts_per_page' => -1,
				    'post_status' => 'publish'
			    )
		    );

    		if ( $query->have_posts() ){
    			while ($query->have_posts()){
				    $query->the_post();
				    $aSettings = get_post_meta($query->post->ID, 'listing_settings', true);
				    if ( isset($aSettings['map']) && isset($aSettings['map']['latlong']) ){
						update_post_meta($query->post->ID, 'listgo_listing_latlong', $aSettings['map']['latlong']);
				    }
    			}
			    wp_reset_postdata();
		    }

    		update_option('wiloke_listgo_updated_latitude_and_longitude', true);
	    }
    }

    public function updateLatLongForEachListing($postID){
    	if ( !current_user_can('edit_posts') ){
    		return false;
	    }

	    if ( isset($_POST['listing_settings']) && isset($_POST['listing_settings']['map']['latlong']) ){
		    update_post_meta($postID, 'listgo_listing_latlong', $_POST['listing_settings']['map']['latlong']);
	    }
    }

    public function editListTableQuery($where, $q){
	    if( is_admin()
			&& $q->is_main_query()
			&& !filter_input( INPUT_GET, 'post_status' )
			&& ( $oScreen = get_current_screen() ) instanceof \WP_Screen
			&& ('edit-listing' === $oScreen->id)
			&& ($oScreen->post_type === 'listing')
	    ){
		    global $wpdb;
		    $where .=" AND {$wpdb->posts}.post_status NOT IN ('expired', 'processing', 'temporary_closed')";
	    }

	    return $where;
    }

    public function register()
    {
    	global $wiloke, $WilokeListGoFunctionalityApp;
        if ( isset($WilokeListGoFunctionalityApp['post_types']) ){
            foreach ( $WilokeListGoFunctionalityApp['post_types'] as $postType => $aArgs ){
            	if ( $postType == 'listing' && isset($wiloke->aThemeOptions['custom_listing_single_slug']) && !empty($wiloke->aThemeOptions['custom_listing_single_slug']) ){
            		$aArgs['rewrite']['slug'] = trim(stripslashes($wiloke->aThemeOptions['custom_listing_single_slug']));
	            }

	            switch ($postType){
		            case 'listing':
		            	$aArgs['labels'] =  array(
				            'name'               => _x( 'Listings', 'admin menu', 'wiloke' ),
				            'singular_name'      => _x( 'Listing', 'admin menu', 'wiloke' ),
				            'menu_name'          => _x( 'Listings', 'admin bar', 'wiloke' ),
				            'name_admin_bar'     => esc_html__( 'Listing', 'wiloke' ),
				            'add_new'            => esc_html__( 'Add New', 'wiloke' ),
				            'add_new_item'       => esc_html__( 'Add New Listing', 'wiloke' ),
				            'new_item'           => esc_html__( 'New Listing', 'wiloke' ),
				            'edit_item'          => esc_html__( 'Edit Listing', 'wiloke' ),
				            'view_item'          => esc_html__( 'View Listing', 'wiloke' ),
				            'all_items'          => esc_html__( 'All Listings', 'wiloke' ),
				            'search_items'       => esc_html__( 'Search Listings', 'wiloke' ),
				            'parent_item_colon'  => esc_html__( 'Parent Listings:', 'wiloke' ),
				            'not_found'          => esc_html__( 'No Listings found.', 'wiloke' ),
				            'not_found_in_trash' => esc_html__( 'No Listings found in Trash.', 'wiloke' )
			            );
		            	break;
		            case 'claim':
		            	$aArgs['labels'] = array(
				            'name'               => _x( 'Claims', 'post type general name', 'wiloke' ),
				            'singular_name'      => _x( 'Claim', 'post type singular name', 'wiloke' ),
				            'menu_name'          => _x( 'Claims', 'admin menu', 'wiloke' ),
				            'name_admin_bar'     => _x( 'Claim', 'add new on admin bar', 'wiloke' ),
				            'add_new'            => _x( 'Add Claim', 'listing', 'wiloke' ),
				            'add_new_item'       => esc_html__( 'Add New Claim', 'wiloke' ),
				            'new_item'           => esc_html__( 'New Claim', 'wiloke' ),
				            'edit_item'          => esc_html__( 'Edit Claim', 'wiloke' ),
				            'view_item'          => esc_html__( 'View Claim', 'wiloke' ),
				            'all_items'          => esc_html__( 'All Claims', 'wiloke' ),
				            'search_items'       => esc_html__( 'Search Claims', 'wiloke' ),
				            'not_found'          => esc_html__( 'No Claims found.', 'wiloke' ),
				            'not_found_in_trash' => esc_html__( 'No Claims found in Trash.', 'wiloke' )
			            );
		            	break;
		            case 'testimonial':
			            $aArgs['labels'] = array(
				            'name'               => _x( 'Testimonial', 'post type general name', 'wiloke' ),
				            'singular_name'      => _x( 'Testimonial', 'post type singular name', 'wiloke' ),
				            'menu_name'          => _x( 'Testimonials', 'admin menu', 'wiloke' ),
				            'name_admin_bar'     => _x( 'Testimonial', 'add new on admin bar', 'wiloke' ),
				            'add_new'            => _x( 'Add New', 'Testimonial', 'wiloke' ),
				            'add_new_item'       => esc_html__( 'Add New Testimonial', 'wiloke' ),
				            'new_item'           => esc_html__( 'New Testimonial', 'wiloke' ),
				            'edit_item'          => esc_html__( 'Edit Testimonial', 'wiloke' ),
				            'view_item'          => esc_html__( 'View Testimonial', 'wiloke' ),
				            'all_items'          => esc_html__( 'All Testimonials', 'wiloke' ),
				            'search_items'       => esc_html__( 'Search Testimonials', 'wiloke' ),
				            'parent_item_colon'  => esc_html__( 'Parent Testimonials:', 'wiloke' ),
				            'not_found'          => esc_html__( 'No Testimonials found.', 'wiloke' ),
				            'not_found_in_trash' => esc_html__( 'No Testimonials found in Trash.', 'wiloke' )
			            );
			            break;
		            case  'event':
			            $aArgs['labels'] = array(
				            'name'               => _x( 'Event', 'post type general name', 'wiloke' ),
				            'singular_name'      => _x( 'Event', 'post type singular name', 'wiloke' ),
				            'menu_name'          => _x( 'Events', 'admin menu', 'wiloke' ),
				            'name_admin_bar'     => _x( 'Events', 'add new on admin bar', 'wiloke' ),
				            'add_new'            => _x( 'Add New', 'Event', 'wiloke' ),
				            'add_new_item'       => esc_html__( 'Add New Event', 'wiloke' ),
				            'new_item'           => esc_html__( 'New Event', 'wiloke' ),
				            'edit_item'          => esc_html__( 'Edit Event', 'wiloke' ),
				            'view_item'          => esc_html__( 'View Events', 'wiloke' ),
				            'all_items'          => esc_html__( 'All Events', 'wiloke' ),
				            'search_items'       => esc_html__( 'Search Events', 'wiloke' ),
				            'parent_item_colon'  => esc_html__( 'Parent Events:', 'wiloke' ),
				            'not_found'          => esc_html__( 'No Events found.', 'wiloke' ),
				            'not_found_in_trash' => esc_html__( 'No Events found in Trash.', 'wiloke' )
			            );
			            break;
		            case 'pricing':
		            	$aArgs['labels'] = array(
				            'name'               => _x( 'Listing Pricings', 'post type general name', 'wiloke' ),
				            'singular_name'      => _x( 'Listing Pricings', 'post type singular name', 'wiloke' ),
				            'menu_name'          => _x( 'Listing Pricings', 'admin menu', 'wiloke' ),
				            'name_admin_bar'     => _x( 'Listing Pricings', 'add new on admin bar', 'wiloke' ),
				            'add_new'            => _x( 'Add Pricing', 'Listing Pricing', 'wiloke' ),
				            'add_new_item'       => esc_html__( 'Add New Listing Pricing', 'wiloke' ),
				            'new_item'           => esc_html__( 'New Listing Pricing', 'wiloke' ),
				            'edit_item'          => esc_html__( 'Edit Listing Pricing', 'wiloke' ),
				            'view_item'          => esc_html__( 'View Listing Pricings', 'wiloke' ),
				            'all_items'          => esc_html__( 'All Listing Pricings', 'wiloke' ),
				            'search_items'       => esc_html__( 'Search Listing Pricing', 'wiloke' ),
				            'parent_item_colon'  => esc_html__( 'Parent Listing Pricing:', 'wiloke' ),
				            'not_found'          => esc_html__( 'No Listing Pricing found.', 'wiloke' ),
				            'not_found_in_trash' => esc_html__( 'No Listing Pricing found in Trash.', 'wiloke' )
			            );
		            	break;
		            case 'event-pricing':
			            $aArgs['labels'] = array(
				            'name'               => _x( 'Event Pricings', 'post type general name', 'wiloke' ),
				            'singular_name'      => _x( 'Event Pricings', 'post type singular name', 'wiloke' ),
				            'menu_name'          => _x( 'Event Pricings', 'admin menu', 'wiloke' ),
				            'name_admin_bar'     => _x( 'Event Pricings', 'add new on admin bar', 'wiloke' ),
				            'add_new'            => _x( 'Add Event', 'Event Pricing', 'wiloke' ),
				            'add_new_item'       => esc_html__( 'Add New Event Pricing', 'wiloke' ),
				            'new_item'           => esc_html__( 'New Event Pricing', 'wiloke' ),
				            'edit_item'          => esc_html__( 'Edit Event Pricing', 'wiloke' ),
				            'view_item'          => esc_html__( 'View Event Pricings', 'wiloke' ),
				            'all_items'          => esc_html__( 'All Event Pricings', 'wiloke' ),
				            'search_items'       => esc_html__( 'Search Event Pricing', 'wiloke' ),
				            'parent_item_colon'  => esc_html__( 'Parent Event Pricing:', 'wiloke' ),
				            'not_found'          => esc_html__( 'No Event Pricing found.', 'wiloke' ),
				            'not_found_in_trash' => esc_html__( 'No Event Pricing found in Trash.', 'wiloke' )
			            );
			            break;
		            case 'review':
		            	$aArgs['labels'] = array(
				            'name'               => _x( 'Reviews', 'post type general name', 'wiloke' ),
				            'singular_name'      => _x( 'Review', 'post type singular name', 'wiloke' ),
				            'menu_name'          => _x( 'Reviews', 'admin menu', 'wiloke' ),
				            'name_admin_bar'     => _x( 'Review', 'add new on admin bar', 'wiloke' ),
				            'add_new'            => _x( 'Add New', 'Testimonial', 'wiloke' ),
				            'add_new_item'       => esc_html__( 'Add New Review', 'wiloke' ),
				            'new_item'           => esc_html__( 'New Review', 'wiloke' ),
				            'edit_item'          => esc_html__( 'Edit Review', 'wiloke' ),
				            'view_item'          => esc_html__( 'View Review', 'wiloke' ),
				            'all_items'          => esc_html__( 'All Reviews', 'wiloke' ),
				            'search_items'       => esc_html__( 'Search Reviews', 'wiloke' ),
				            'parent_item_colon'  => esc_html__( 'Parent Reviews:', 'wiloke' ),
				            'not_found'          => esc_html__( 'No Reviews found.', 'wiloke' ),
				            'not_found_in_trash' => esc_html__( 'No Reviews found in Trash.', 'wiloke' )
			            );
		            	break;
	            }

                register_post_type($postType, $aArgs);
            }
        }
    }
}