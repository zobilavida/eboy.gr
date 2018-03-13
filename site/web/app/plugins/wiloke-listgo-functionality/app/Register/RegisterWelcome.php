<?php
namespace WilokeListGoFunctionality\Register;

if ( !defined('IMPORT_DEBUG') ){
	define( 'IMPORT_DEBUG', false );
}


class RegisterWelcome{
	public $slug = 'wiloke-welcome';
	public $firstTimeSetup = 'wiloke_listgo_is_firstime_setup';
	protected $importDir = 'dummy';
	protected $ds = '/';
	protected $homePageSlug = 'search-form-right-style-2';
	protected $menuName = 'Mega Menu';
	protected $megaMenuStyleName = 'menu-horizontal';
	protected $menuLocation = 'listgo_menu';
	protected $aListGoMenuCollection = array('Mega Menu', 'Home Pages', 'Home Layouts', 'Home Creative');
	protected $isPassedMemoryCheck = false;

	public function __construct() {
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('init', array($this, 'register'));
		add_action('admin_init', array($this, 'redirectToSetup'));
		add_action('wp_ajax_wiloke_listgo_importing_demo', array($this, 'runSetup'));
	}

	protected function checkMemory(){
	    if ( $this->isPassedMemoryCheck ){
	        return true;
        }

        $postMaxUploadSize = ini_get('post_max_size');
		$postMaxUploadSize = str_replace('M', '', $postMaxUploadSize);
		$postMaxUploadSize = absint($postMaxUploadSize);
		if ( $postMaxUploadSize < 5 ){
            return false;
        }

		$uploadMaxFileSize = ini_get('upload_max_filesize');
		$uploadMaxFileSize = str_replace('M', '', $uploadMaxFileSize);
		$uploadMaxFileSize = absint($uploadMaxFileSize);
		if ( $uploadMaxFileSize < 5 ){
			return false;
		}

		$this->isPassedMemoryCheck = true;
		return true;
    }

	public function canUnZip(){
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		$fields = array( 'action', '_wp_http_referer', '_wpnonce' );
		$canUnZip = false;
		if ( false !== ( $creds = request_filesystem_credentials( '', '', false, false, $fields ) ) ) {

			if ( ! WP_Filesystem( $creds ) ) {
				request_filesystem_credentials( wp_nonce_url(admin_url('admin.php?page=wiloke-welcome')), '', true, false, $fields ); // Setup WP_Filesystem.
			}else{
                $canUnZip = true;
			}
		}

		return $canUnZip;
    }

	protected function _importXML($file, $isAttachment=true){
		if ( !class_exists('\Wiloke_Import') ){
			require_once plugin_dir_path(dirname(__FILE__)) . 'Lib/wordpress-importer/wordpress-importer.php';
		}
		$oWPImport = new \Wiloke_Import;
        ob_start();
        $oWPImport->fetch_attachments = $isAttachment;
        $oWPImport->import($file);
        $res = ob_get_contents();
        ob_clean();
        return ($res !== 404);
	}

	public function installedMsg($name, $isSuccess=true){
        return !$isSuccess ? sprintf(esc_html__('We could not found %s plugin. Maybe the plugin has been removed'), $name) : sprintf(esc_html__('%s plugin has been installed'), $name);
    }

	/**
	 * Retrieve the download URL for a WP repo package.
	 *
	 * @since 2.5.0
	 *
	 * @param string $slug Plugin slug.
	 * @return string Plugin download URL.
	 */
	protected function _getWPRepoDownloadUrl( $slug ) {
		$source = '';
		$api    = $this->_getPluginsAPI( $slug );
		if ( !is_wp_error($api) || false !== $api && isset( $api->download_link ) ) {
			$source = $api->download_link;
		}

		return $source;
	}
    
	protected function _unZipFile($package, $isLive=false){
		WP_Filesystem();
		$status = unzip_file( $package,  ABSPATH.'/wp-content/plugins');
		if ( $isLive ){
			@unlink($package);
        }
        if ( $status ){
		    return true;
        }

		return false;
    }
	
    protected function _installPlugin($slug){
	    $downloadLink = $this->_getWPRepoDownloadUrl($slug);
	    if ( empty($downloadLink) ){
		    return false;
	    }
	    $package = download_url( $downloadLink, 18000 );
	    if (is_wp_error($package)){
	        return false;
        }

        return $this->_unZipFile($package, true);
    }

    protected function _installFromWilokeServer($downloadLink){
        if ( strpos($downloadLink, 'https://goo') !== false ){
	        $package = download_url( $downloadLink, 18000 );
	        if (is_wp_error($package)){
		        return false;
	        }
	        return $this->_unZipFile($package, true);
        }

        return false;
    }

    protected function setWilokeSubmissionPages(){
        $query = new \WP_Query(
            array(
                'post_type' => 'page',
                'posts_per_page' => -1,
                'post_status' => 'publish'
            )
        );
	    $aSubmissionPages = \Wiloke::getOption(RegisterWilokeSubmission::$submissionConfigurationKey);

	    $aPages = array(
            'wiloke-submission/addlisting.php' => 'addlisting',
            'wiloke-submission/checkout.php' => 'checkout',
            'wiloke-submission/myaccount.php' => 'myaccount',
            'wiloke-submission/payment-thankyou.php' => 'thankyou',
            'wiloke-submission/payment-cancel.php' => 'cancel'
	    );
        $aPageTemplates = array_keys($aPages);

        if ( $query->have_posts() ){
            while ($query->have_posts()){
                $query->the_post();
                $pageTemplateSlug = get_page_template_slug($query->post);

                if ( in_array($pageTemplateSlug, $aPageTemplates) ){
	                $aSubmissionPages[$aPages[$pageTemplateSlug]] = $query->post->ID;
                }else if ( $query->post->post_name == 'wiloke-package' || ($query->post->post_name==='pricing-tables') ){
	                $aSubmissionPages['package'] = $query->post->ID;
                }
            }
            wp_reset_postdata();
        }

	    \Wiloke::updateOption(RegisterWilokeSubmission::$submissionConfigurationKey, $aSubmissionPages);
        return true;
    }

	protected function getAvailableWidgets(){
		global $wp_registered_widget_controls;
		$widget_controls = $wp_registered_widget_controls;
		$available_widgets = array();
		foreach ( $widget_controls as $widget ) {
			if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[$widget['id_base']] ) ) { // no dupes
				$available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
				$available_widgets[$widget['id_base']]['name'] = $widget['name'];
			}
		}
		return apply_filters( 'wie_available_widgets', $available_widgets );
	}

	protected function importingWidgets($data){
	    global $wp_registered_sidebars;
	    $data = json_decode($data);
	    // Have valid data?
	    // If no data or could not decode
	    if ( empty($data) || ! is_object($data) ) {
	        return true;
	    }

	    // Hook before import
	    do_action( 'wie_before_import' );
	    $data = apply_filters( 'wie_import_data', $data );

	    // Get all available widgets site supports
	    $available_widgets = $this->getAvailableWidgets();

	    // Get all existing widget instances
	    $widget_instances = array();
	    foreach ( $available_widgets as $widget_data ) {
	        $widget_instances[$widget_data['id_base']] = get_option( 'widget_' . $widget_data['id_base'] );
	    }

	    // Begin results
	    $results = array();
	    $widget_message_type = 'success';
	    $widget_message = '';

	    // Loop import data's sidebars
	    foreach ( $data as $sidebar_id => $widgets ) {

	        // Skip inactive widgets
	        // (should not be in export file)
	        if ( 'wp_inactive_widgets' == $sidebar_id ) {
	            continue;
	        }

	        // Check if sidebar is available on this site
	        // Otherwise add widgets to inactive, and say so
	        if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
	            $sidebar_available = true;
	            $use_sidebar_id = $sidebar_id;
	            $sidebar_message_type = 'success';
	            $sidebar_message = '';
	        } else {
	            $sidebar_available = false;
	            $use_sidebar_id = 'wp_inactive_widgets'; // add to inactive if sidebar does not exist in theme
	            $sidebar_message_type = 'error';
	            $sidebar_message = esc_html__( 'Widget area does not exist in theme (using Inactive)', 'wiloke' );
	        }

	        // Result for sidebar
	        $results[$sidebar_id]['name'] = ! empty( $wp_registered_sidebars[$sidebar_id]['name'] ) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id; // sidebar name if theme supports it; otherwise ID
	        $results[$sidebar_id]['message_type'] = $sidebar_message_type;
	        $results[$sidebar_id]['message'] = $sidebar_message;
	        $results[$sidebar_id]['widgets'] = array();

	        // Loop widgets
	        foreach ( $widgets as $widget_instance_id => $widget ) {

	            $fail = false;

	            // Get id_base (remove -# from end) and instance ID number
	            $id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
	            $instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );

	            // Does site support this widget?
	            if ( ! $fail && ! isset( $available_widgets[$id_base] ) ) {
	                $fail = true;
	                $widget_message_type = 'error';
	                $widget_message = esc_html__( 'Site does not support widget', 'wiloke' ); // explain why widget not imported
	            }

	            $widget = apply_filters( 'wie_widget_settings', $widget ); // object
	            $widget = json_decode( wp_json_encode( $widget ), true );

	            $widget = apply_filters( 'wie_widget_settings_array', $widget );

	            // Does widget with identical settings already exist in same sidebar?
	            if ( ! $fail && isset( $widget_instances[$id_base] ) ) {

	                // Get existing widgets in this sidebar
	                $sidebars_widgets = get_option( 'sidebars_widgets' );
	                $sidebar_widgets = isset( $sidebars_widgets[$use_sidebar_id] ) ? $sidebars_widgets[$use_sidebar_id] : array(); // check Inactive if that's where will go

	                // Loop widgets with ID base
	                $single_widget_instances = ! empty( $widget_instances[$id_base] ) ? $widget_instances[$id_base] : array();
	                foreach ( $single_widget_instances as $check_id => $check_widget ) {

	                    // Is widget in same sidebar and has identical settings?
	                    if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {

	                        $fail = true;
	                        $widget_message_type = 'warning';
	                        $widget_message = esc_html__( 'Widget already exists', 'wiloke' ); // explain why widget not imported

	                        break;

	                    }

	                }

	            }

	            // No failure
	            if ( ! $fail ) {
	                // Add widget instance
	                $single_widget_instances = get_option( 'widget_' . $id_base ); // all instances for that widget ID base, get fresh every time
	                $single_widget_instances = ! empty( $single_widget_instances ) ? $single_widget_instances : array( '_multiwidget' => 1 ); // start fresh if have to
	                $single_widget_instances[] = $widget; // add it

	                // Get the key it was given
	                end( $single_widget_instances );
	                $new_instance_id_number = key( $single_widget_instances );

	                // If key is 0, make it 1
	                // When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it)
	                if ( '0' === strval( $new_instance_id_number ) ) {
	                    $new_instance_id_number = 1;
	                    $single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
	                    unset( $single_widget_instances[0] );
	                }

	                // Move _multiwidget to end of array for uniformity
	                if ( isset( $single_widget_instances['_multiwidget'] ) ) {
	                    $multiwidget = $single_widget_instances['_multiwidget'];
	                    unset( $single_widget_instances['_multiwidget'] );
	                    $single_widget_instances['_multiwidget'] = $multiwidget;
	                }

	                // Update option with new widget
	                update_option( 'widget_' . $id_base, $single_widget_instances );

	                // Assign widget instance to sidebar
	                $sidebars_widgets = get_option( 'sidebars_widgets' ); // which sidebars have which widgets, get fresh every time

	                // Avoid rarely fatal error when the option is an empty string
	                // https://github.com/churchthemes/widget-importer-exporter/pull/11
	                if ( ! $sidebars_widgets ) {
	                    $sidebars_widgets = array();
	                }

	                $new_instance_id = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance
	                $sidebars_widgets[$use_sidebar_id][] = $new_instance_id; // add new instance to sidebar
	                update_option( 'sidebars_widgets', $sidebars_widgets ); // save the amended data

	                // After widget import action
	                $after_widget_import = array(
	                    'sidebar'           => $use_sidebar_id,
	                    'sidebar_old'       => $sidebar_id,
	                    'widget'            => $widget,
	                    'widget_type'       => $id_base,
	                    'widget_id'         => $new_instance_id,
	                    'widget_id_old'     => $widget_instance_id,
	                    'widget_id_num'     => $new_instance_id_number,
	                    'widget_id_num_old' => $instance_id_number
	                );
	                do_action( 'wie_after_widget_import', $after_widget_import );

	                // Success message
	                if ( $sidebar_available ) {
	                    $widget_message_type = 'success';
	                    $widget_message = esc_html__( 'Imported', 'wiloke' );
	                } else {
	                    $widget_message_type = 'warning';
	                    $widget_message = esc_html__( 'Imported to Inactive', 'wiloke' );
	                }

	            }

	            // Result for widget instance
	            $results[$sidebar_id]['widgets'][$widget_instance_id]['name'] = isset( $available_widgets[$id_base]['name'] ) ? $available_widgets[$id_base]['name'] : $id_base; // widget name or ID if name not available (not supported by site)
	            $results[$sidebar_id]['widgets'][$widget_instance_id]['title'] = ! empty( $widget['title'] ) ? $widget['title'] : esc_html__( 'No Title', 'wiloke' ); // show "No Title" if widget instance is untitled
	            $results[$sidebar_id]['widgets'][$widget_instance_id]['message_type'] = $widget_message_type;
	            $results[$sidebar_id]['widgets'][$widget_instance_id]['message'] = $widget_message;
	        }

	    }

	    return apply_filters( 'wie_import_results', $results );
	}

	protected function cleanListGoMenu(){
		global $wpdb;
		$termsTbl = $wpdb->prefix . 'terms';
		$taxonomyTbl = $wpdb->prefix . 'term_taxonomy';
		$aTermIDs = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT $termsTbl.term_id FROM $termsTbl INNER JOIN $taxonomyTbl ON ($termsTbl.term_id = $taxonomyTbl.term_id) WHERE $termsTbl.name IN ('".implode('\',\'', $this->aListGoMenuCollection)."') AND $taxonomyTbl.taxonomy=%s",
                'nav_menu'
            ),
        ARRAY_A
        );

		if ( !empty($aTermIDs) ){
		    $aTermsCollection = array();
		    foreach ( $aTermIDs as $oTerm ){
			    $aTermsCollection[] = $oTerm['term_id'];
			    wp_delete_term(intval($oTerm['term_id']), 'nav_menu');
            }

			$args = array(
				'post_type' => 'nav_menu_item',
				'tax_query' => array(
					array(
						'taxonomy'  => 'nav_menu',
						'field'     => 'id',
						'terms'     => $aTermsCollection
					)
				)
			);

			$oQuery = new \WP_Query( $args );
			if ( $oQuery->have_posts() ){
			    while ($oQuery->have_posts()){
				    $oQuery->the_post();
				    wp_delete_post($oQuery->post->ID, true);
                }
            }
        }
    }

    protected function cleanHelloWorld(){
	    global $wpdb;
	    $postsTbl = $wpdb->prefix . 'posts';
	    $postID = $wpdb->get_var(
	        $wpdb->prepare(
                "SELECT ID FROM $postsTbl WHERE post_title=%s",
                'Hello World'
            )
        );
	    wp_delete_post($postID, true);
    }

    protected function reUpdateTermParent($aData){
        $aData = json_decode($aData, true);
        if ( empty($aData) ){
            return false;
        }

        foreach ( $aData as $taxonomyName => $aInfo ){
            foreach ( $aInfo as $parentSlug => $aChildrenSlug ){
                $oParentTerm = get_term_by('slug', $parentSlug, $taxonomyName);

                if ( empty($oParentTerm) || is_wp_error($oParentTerm) ){
                    continue;
                }

                foreach ($aChildrenSlug as $childSlug){
	                $oChildInfo = get_term_by('slug', $childSlug, $taxonomyName);
	                if ( empty($oChildInfo) || is_wp_error($oChildInfo) ){
		                continue;
	                }

	                wp_update_term($oChildInfo->term_id, $taxonomyName, array(
                        'parent' => $oParentTerm->term_id
                    ));
                }
            }
        }

        return true;
    }

	protected function setupMenu(){
	    global $wpdb;
	    $termTbl = $wpdb->prefix . 'terms';
	    $termID = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT term_id from $termTbl WHERE name=%s",
                $this->menuName
            )
        );

	    if ( empty($termID) ){
	        return false;
        }

        $aNavData = array();
		$termID = absint($termID);

		$aNavData['megamenu'] = 1;
		$postsTbl = $wpdb->prefix . 'posts';
		$menuStyleID = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $postsTbl WHERE post_name=%s AND post_type=%s",
				$this->megaMenuStyleName, 'wiloke-menu'
			)
		);

		$aNavData['menu'] = $menuStyleID;

		$termsTbl = $wpdb->prefix . 'terms';
		$termTaxonomyTbl = $wpdb->prefix . 'term_taxonomy';

		$megaMenuID = $wpdb->get_var("SELECT $termsTbl.term_id FROM $termsTbl LEFT JOIN $termTaxonomyTbl ON ($termTaxonomyTbl.term_id = $termsTbl.term_id) WHERE $termsTbl.name = 'Mega Menu' AND $termTaxonomyTbl.taxonomy = 'nav_menu'");

		if ( !empty($megaMenuID) ){
			$this->reUpdateHomeMenu($megaMenuID);
			$this->reUpdateExploreMegaMenu($megaMenuID);
			$this->reUpdateListingsMenu($megaMenuID);
			$this->reUpdateDestination($megaMenuID);
		}

		set_theme_mod( 'nav_menu_locations', array($this->menuLocation=>$termID) );
		update_term_meta( $termID, 'wiloke-nav-data', $aNavData );
		return true;
	}

	protected function reUpdateDestination($megaMenuID){
		global $wpdb;
		$termRelationshipsTbl = $wpdb->prefix . 'term_relationships';
		$postTbl = $wpdb->prefix . 'posts';

		$postID = $wpdb->get_var("SELECT $termRelationshipsTbl.object_id  FROM $termRelationshipsTbl LEFT JOIN $postTbl ON ($postTbl.ID = $termRelationshipsTbl.object_id) WHERE $postTbl.post_title='Destinations' AND $termRelationshipsTbl.term_taxonomy_id=".abs($megaMenuID));

		if ( empty($postID) ){
			return false;
		}

		$aData = get_post_meta($postID, 'wiloke-nav-item-data', true);
		$aData['megamenu'] = 1;
		$aData['content'] = '[vc_row][vc_column][wiloke_list_of_terms_on_mega_menu taxonomy="listing_location" listing_location="5,4,43,59" display="slider" nav="yes" xl_per_row="4" lg_per_row="4" md_per_row="4" sm_per_row="2" xs_per_row="1" space="20"][/vc_column][/vc_row]';
		update_post_meta($postID, 'wiloke-nav-item-data', $aData);
	}

	protected function reUpdateListingsMenu($megaMenuID){
		global $wpdb;
		$termRelationshipsTbl = $wpdb->prefix . 'term_relationships';
		$postTbl = $wpdb->prefix . 'posts';
		$termsTbl = $wpdb->prefix . 'terms';
		$termTaxonomyTbl = $wpdb->prefix . 'term_taxonomy';

		$postID = $wpdb->get_var("SELECT $termRelationshipsTbl.object_id  FROM $termRelationshipsTbl LEFT JOIN $postTbl ON ($postTbl.ID = $termRelationshipsTbl.object_id) WHERE $postTbl.post_type='nav_menu_item' AND $postTbl.post_title='Listings' AND $termRelationshipsTbl.term_taxonomy_id=".abs($megaMenuID));

		if ( empty($postID) ){
			return false;
		}

		$homeLayoutID = $wpdb->get_var("SELECT $termsTbl.term_id FROM $termsTbl LEFT JOIN $termTaxonomyTbl ON ($termTaxonomyTbl.term_id = $termsTbl.term_id) WHERE $termsTbl.name = 'Home Layouts' AND $termTaxonomyTbl.taxonomy = 'nav_menu'");

		$aData = get_post_meta($postID, 'wiloke-nav-item-data', true);
		$aData['megamenu'] = 1;
		$aData['content'] = '[vc_row][vc_column offset="vc_col-lg-3"][vc_custom_heading text="Top Rated" font_container="tag:h4|font_size:14px|text_align:left|color:%23f5af02" use_theme_fonts="yes" css=".vc_custom_1501237410106{margin-bottom: 10px !important;border-bottom-width: 1px !important;padding-bottom: 10px !important;border-bottom-color: rgba(255,255,255,0.1) !important;border-bottom-style: solid !important;}"][wiloke_list_of_listings_on_mega_menu get_posts_by="top_rated" number_of_listings="10" display="simple"][/vc_column][vc_column offset="vc_col-lg-3"][vc_custom_heading text="Listing Layouts" font_container="tag:h4|font_size:14px|text_align:left|color:%23f5af02" use_theme_fonts="yes" css=".vc_custom_1503460509414{margin-bottom: 10px !important;border-bottom-width: 1px !important;padding-bottom: 10px !important;border-bottom-color: rgba(255,255,255,0.1) !important;border-bottom-style: solid !important;}"][vc_wp_custommenu nav_menu="'.$homeLayoutID.'"][/vc_column][vc_column offset="vc_col-lg-6"][vc_custom_heading text="Single Styles" font_container="tag:h4|font_size:14px|text_align:left|color:%23f5af02" use_theme_fonts="yes" css=".vc_custom_1503474512896{margin-bottom: 30px !important;border-bottom-width: 1px !important;padding-bottom: 10px !important;border-bottom-color: rgba(255,255,255,0.1) !important;border-bottom-style: solid !important;}"][vc_raw_html]JTNDZGl2JTIwY2xhc3MlM0QlMjJ3aWxva2UtbWVudS1waG90b3MlMjB3aWxva2UtbWVudS1zbGlkZXIlMjBvd2wtY2Fyb3VzZWwlMjIlMjBkYXRhLWNvbC14bCUzRCUyMjMlMjIlMjBkYXRhLWNvbC1sZyUzRCUyMjMlMjIlMjBkYXRhLWNvbC1tZCUzRCUyMjMlMjIlMjBkYXRhLWNvbC1zbSUzRCUyMjIlMjIlMjBkYXRhLWNvbC14cyUzRCUyMjElMjIlMjBkYXRhLXNwYWNlJTNEJTIyMjAlMjIlMjIlMjBkYXRhLW5hdiUzRCUyMjElMjIlMjBkYXRhLWRvdHMlM0QlMjIlMjIlM0UlMEElMEElM0NkaXYlMjBjbGFzcyUzRCUyMndpbG9rZS1tZW51LXBob3RvJTIyJTNFJTNDYSUyMGhyZWYlM0QlMjJodHRwcyUzQSUyRiUyRmxpc3Rnby53aWxva2UuY29tJTJGbGlzdGluZyUyRmNoaWxlYW4tcGF0YWdvbmlhJTJGJTIyJTNFJTNDaW1nJTIwc3JjJTNEJTIyaHR0cHMlM0ElMkYlMkZsaXN0Z28ud2lsb2tlLmNvbSUyRndwLWNvbnRlbnQlMkZ1cGxvYWRzJTJGMjAxNyUyRjA4JTJGc2luZ2xlXzAwNl9fLmpwZyUyMiUyMCUyRiUzRSUzQyUyRmElM0UlM0MlMkZkaXYlM0UlMEElMEElM0NkaXYlMjBjbGFzcyUzRCUyMndpbG9rZS1tZW51LXBob3RvJTIyJTNFJTNDYSUyMGhyZWYlM0QlMjJodHRwcyUzQSUyRiUyRmxpc3Rnby53aWxva2UuY29tJTJGbGlzdGluZyUyRmdhZ2Utc3BhJTJGJTIyJTNFJTNDaW1nJTIwc3JjJTNEJTIyaHR0cHMlM0ElMkYlMkZsaXN0Z28ud2lsb2tlLmNvbSUyRndwLWNvbnRlbnQlMkZ1cGxvYWRzJTJGMjAxNyUyRjA4JTJGc2luZ2xlXzAwNy5qcGclMjIlMjAlMkYlM0UlM0MlMkZhJTNFJTNDJTJGZGl2JTNFJTBBJTBBJTNDZGl2JTIwY2xhc3MlM0QlMjJ3aWxva2UtbWVudS1waG90byUyMiUzRSUzQ2ElMjBocmVmJTNEJTIyaHR0cHMlM0ElMkYlMkZsaXN0Z28ud2lsb2tlLmNvbSUyRmxpc3RpbmclMkZob2EtbHUtYW5kLXRhbS1jb2MlMkYlMjIlM0UlM0NpbWclMjBzcmMlM0QlMjJodHRwcyUzQSUyRiUyRmxpc3Rnby53aWxva2UuY29tJTJGd3AtY29udGVudCUyRnVwbG9hZHMlMkYyMDE3JTJGMDglMkZzaW5nbGVfMDAzLmpwZyUyMiUyMCUyRiUzRSUzQyUyRmElM0UlM0MlMkZkaXYlM0UlMEElMEElM0NkaXYlMjBjbGFzcyUzRCUyMndpbG9rZS1tZW51LXBob3RvJTIyJTNFJTNDYSUyMGhyZWYlM0QlMjJodHRwcyUzQSUyRiUyRmxpc3Rnby53aWxva2UuY29tJTJGbGlzdGluZyUyRm1pY2tpZXMtZGFpcnktYmFyJTJGJTIyJTNFJTNDaW1nJTIwc3JjJTNEJTIyaHR0cHMlM0ElMkYlMkZsaXN0Z28ud2lsb2tlLmNvbSUyRndwLWNvbnRlbnQlMkZ1cGxvYWRzJTJGMjAxNyUyRjA4JTJGc2luZ2xlXzAwMS5qcGclMjIlMjAlMkYlM0UlM0MlMkZhJTNFJTNDJTJGZGl2JTNFJTBBJTBBJTNDZGl2JTIwY2xhc3MlM0QlMjJ3aWxva2UtbWVudS1waG90byUyMiUzRSUzQ2ElMjBocmVmJTNEJTIyaHR0cHMlM0ElMkYlMkZsaXN0Z28ud2lsb2tlLmNvbSUyRmxpc3RpbmclMkZtYXR0cy1iaWctYnJlYWtmYXN0JTJGJTIyJTNFJTNDaW1nJTIwc3JjJTNEJTIyaHR0cHMlM0ElMkYlMkZsaXN0Z28ud2lsb2tlLmNvbSUyRndwLWNvbnRlbnQlMkZ1cGxvYWRzJTJGMjAxNyUyRjA4JTJGc2luZ2xlXzAwMi5qcGclMjIlMjAlMkYlM0UlM0MlMkZhJTNFJTNDJTJGZGl2JTNFJTBBJTBBJTBBJTNDZGl2JTIwY2xhc3MlM0QlMjJ3aWxva2UtbWVudS1waG90byUyMiUzRSUzQ2ElMjBocmVmJTNEJTIyaHR0cHMlM0ElMkYlMkZsaXN0Z28ud2lsb2tlLmNvbSUyRmxpc3RpbmclMkZ0aGUtbGF1bmRyeSUyRiUyMiUzRSUzQ2ltZyUyMHNyYyUzRCUyMmh0dHBzJTNBJTJGJTJGbGlzdGdvLndpbG9rZS5jb20lMkZ3cC1jb250ZW50JTJGdXBsb2FkcyUyRjIwMTclMkYwOCUyRnNpbmdsZV8wMDUuanBnJTIyJTIwJTJGJTNFJTNDJTJGYSUzRSUzQyUyRmRpdiUzRSUwQSUwQSUzQ2RpdiUyMGNsYXNzJTNEJTIyd2lsb2tlLW1lbnUtcGhvdG8lMjIlM0UlM0NhJTIwaHJlZiUzRCUyMmh0dHBzJTNBJTJGJTJGbGlzdGdvLndpbG9rZS5jb20lMkZsaXN0aW5nJTJGYmlrZS10b3Vycy1ob2xseXdvb2QlMkYlMjIlM0UlM0NpbWclMjBzcmMlM0QlMjJodHRwcyUzQSUyRiUyRmxpc3Rnby53aWxva2UuY29tJTJGd3AtY29udGVudCUyRnVwbG9hZHMlMkYyMDE3JTJGMDglMkZzaW5nbGVfMDA0LmpwZyUyMiUyMCUyRiUzRSUzQyUyRmElM0UlM0MlMkZkaXYlM0UlMEElMEElMEElM0MlMkZkaXYlM0U=[/vc_raw_html][/vc_column][/vc_row]';
		update_post_meta($postID, 'wiloke-nav-item-data', $aData);
	}

	protected function reUpdateExploreMegaMenu($megaMenuID){
		global $wpdb;
		$termRelationshipsTbl = $wpdb->prefix . 'term_relationships';
		$postTbl = $wpdb->prefix . 'posts';

		$exploreID = $wpdb->get_var("SELECT $termRelationshipsTbl.object_id  FROM $termRelationshipsTbl LEFT JOIN $postTbl ON ($postTbl.ID = $termRelationshipsTbl.object_id) WHERE $postTbl.post_type='nav_menu_item' AND $postTbl.post_title='Explore' AND $termRelationshipsTbl.term_taxonomy_id=".abs($megaMenuID));

		if ( empty($exploreID) ){
			return false;
		}

		$aData = get_post_meta($exploreID, 'wiloke-nav-item-data', true);
		$aData['megamenu'] = 1;
		update_post_meta($exploreID, 'wiloke-nav-item-data', $aData);
    }

	protected function reUpdateHomeMenu($megaMenuID){
        global $wpdb;
        $termsTbl = $wpdb->prefix . 'terms';
        $termTaxonomyTbl = $wpdb->prefix . 'term_taxonomy';
		$termRelationshipsTbl = $wpdb->prefix . 'term_relationships';
		$postTbl = $wpdb->prefix . 'posts';

		$homeMenuID = $wpdb->get_var("SELECT $termRelationshipsTbl.object_id FROM $termRelationshipsTbl LEFT JOIN $postTbl ON ($postTbl.ID = $termRelationshipsTbl.object_id) WHERE $postTbl.post_title='Home' AND $termRelationshipsTbl.term_taxonomy_id=".abs($megaMenuID));

		if ( empty($homeMenuID) ){
		    return false;
        }

        $aBrandMenus = $wpdb->get_results("SELECT $termsTbl.term_id FROM $termsTbl LEFT JOIN $termTaxonomyTbl ON ($termTaxonomyTbl.term_id = $termsTbl.term_id) WHERE $termsTbl.name IN ('Home Pages', 'Home Creative', 'Home Layouts') AND $termTaxonomyTbl.taxonomy = 'nav_menu'", ARRAY_A);

        if ( empty($aBrandMenus) ){
            return false;
        }

        $sc = $shortcodes = '';
		$aMenuNames = array('Home Pages', 'Home Creative', 'Home Layouts');
		foreach ( $aBrandMenus as $key => $aNavItem ){
			$sc .= '[vc_column width="1/3"][vc_wp_custommenu title="'.$aMenuNames[$key].'" nav_menu="'.$aNavItem['term_id'].'"][/vc_column]';
		}

		if ( !empty($sc) ){
		    $shortcodes = '[vc_row]' . $sc . '[/vc_row]';
        }

		$aData = get_post_meta($homeMenuID, 'wiloke-nav-item-data', true);
		$aData['content'] = $shortcodes;
		$aData['megamenu'] = 1;

		update_post_meta($homeMenuID, 'wiloke-nav-item-data', $aData);
    }

	protected function createMapPage(){
	    $query = new \WP_Query(
	        array(
                'post_type'      => 'page',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'meta_key'       => '_wp_page_template',
                'meta_value'     => 'templates/listing-map.php'
            )
        );

	    $pageID = null;
	    if ( $query->have_posts() ){
	        while ($query->have_posts()){
		        $query->the_post();
		        $pageID = $query->post->ID;
            }
            return $pageID;
        }

	    $pageID = wp_insert_post(
            array(
                'post_title'    => 'Map Template',
                'post_type'     => 'page',
                'post_status'   => 'publish'
            )
        );

		if(!is_wp_error($pageID)){
			update_post_meta($pageID, 'page_template', 'templates/listing-map.php');
			return $pageID;
		}

		return '';
    }

    protected function setFrontPage(){
        $query = new \WP_Query(
            array(
                'post_type'         => 'page',
                'posts_per_page'    => 1,
                'post_status'       => 'publish',
                'name'              => $this->homePageSlug
            )
        );
	    $postID = '';
        if ( $query->have_posts() ){
            while ($query->have_posts()){
	            $query->the_post();
	            $postID = $query->post->ID;
            }
        }

        return $postID;
    }

	public function runSetup(){
	    if ( !isset($_POST['security']) || !check_ajax_referer('wiloke-setup-nonce-action', 'security',false) ){
	        wp_send_json_error(
                array(
                    'msg' => esc_html__('Oops! Something when wrong', 'wiloke')
                )
            );
        }

        if ( !$this->checkMemory() ){
	        wp_send_json_error(
		        array(
			        'msg' => esc_html__('Please log into your hosting provider -> PHP Configuration and increment upload_max_filesize, post_max_size to 40M. You can set back the default after the import is complete.', 'wiloke')
		        )
	        );
        }

        if ( !get_option('wiloke_tmp_clean_menu') ){
	        update_option('wiloke_tmp_clean_menu', true);
	        $this->cleanListGoMenu();
        }

		if ( !get_option('wiloke_tmp_delete_hello_world') ){
			update_option('wiloke_tmp_delete_hello_world', true);
			$this->cleanHelloWorld();
		}

        parse_str($_POST['data'], $aData);
        $canUnzip = isset($_POST['canUnzip']) ? $_POST['canUnzip'] : false;
        if ( !$canUnzip || $canUnzip === 'false'){
            $canUnzip = $this->canUnZip();
        }

        if ( !$canUnzip ){
            wp_send_json_error(
                array(
                    'msg' => esc_html__('We could not install plugins because your server does not UnZip function. Please click on FAQ tab and refer to Manually Install Plugins', 'wiloke'),
                    'item_error'=>true
                )
            );
        }

        global $wiloke;
		$isIgnoreWooCommerce  = (isset($aData['except_woocommerce']) && !empty($aData['except_woocommerce']));

	    $aPlugins       = $wiloke->aConfigs['install_plugins'];
		$pluginDir      = ABSPATH.'/wp-content/plugins';
		$dummyDir       = get_template_directory() . $this->ds . $this->importDir . $this->ds;
		$installPluginSuccessfully = true;
		$aActivatingPlugins = get_option('active_plugins');
		$aTmpSavingPluginInstallation = get_option('wiloke_tmp_saving_plugin_installation');
		$aTmpSavingPluginInstallation = empty($aTmpSavingPluginInstallation) ? array() : $aTmpSavingPluginInstallation;

        foreach ( $aPlugins as $aPlugin ){
            $fineInit = isset($aPlugin['file_init']) ? $aPlugin['slug'] . $this->ds . $aPlugin['file_init'] : $aPlugin['slug'] . $this->ds . $aPlugin['slug'] .'.php';
            if ( in_array($fineInit, $aActivatingPlugins) || in_array($fineInit, $aTmpSavingPluginInstallation) || ( $isIgnoreWooCommerce && ($aPlugin['slug']==='woocommerce') ) ){
                continue;
            }

            if ( empty($aPlugin['source']) ){
                if ( is_dir($pluginDir.$this->ds.$aPlugin['slug']) ){
	                if ( !in_array($fineInit, $aActivatingPlugins) && ($aPlugin['slug'] !== 'woocommerce') ){
		                array_push($aActivatingPlugins, $fineInit);
                    }else{
		                array_push($aTmpSavingPluginInstallation, $fineInit);
                    }
                }else{
	                if ( $this->_installPlugin($aPlugin['slug']) ){
	                    if ( $aPlugin['slug'] !== 'woocommerce' ){
		                    array_push($aActivatingPlugins, $fineInit);
                        }else{
		                    array_push($aTmpSavingPluginInstallation, $fineInit);
                        }
                    }else{
		                wp_send_json_error(
			                array(
				                'msg' => esc_html__('Your server does not allow to install plugins via our channel. Please click on FAQs tab and read Manually Install Plugins to know how to install plugins manually.', 'wiloke')
			                )
		                );
                    }
                }
            }else{
	            if ( strpos($aPlugin['source'], 'https://goo') !== false ){
	                if ( $aPlugin['slug'] == 'login-with-social' ){
	                    continue;
                    }

		            if ( is_file(WP_PLUGIN_DIR . '/' . $aPlugin['slug']) ){
			            array_push($aTmpSavingPluginInstallation, $fineInit);
			            continue;
		            }

                    if ( $this->_installFromWilokeServer($aPlugin['source']) ){
	                    array_push($aActivatingPlugins, $fineInit);
                    }else{
	                    wp_send_json_error(
		                    array(
			                    'msg' => esc_html__('Your server does not allow to install plugins via our chanel. Please click on FAQs tab and read Manually Install Plugins to know how to install plugins manually.', 'wiloke')
		                    )
	                    );
                    }
                }else{
		            if ( !is_file($aPlugin['source']) ){
			            array_push($aTmpSavingPluginInstallation, $fineInit);
			            continue;
		            }

		            if ( is_dir($pluginDir . $this->ds . $aPlugin['slug']) ){
			            if ( !in_array($fineInit, $aActivatingPlugins) ){
				            array_push($aActivatingPlugins, $fineInit);
			            }
		            }else{
			            if( $this->_unZipFile($aPlugin['source']) ){
				            if ( !in_array($fineInit, $aActivatingPlugins) ){
					            array_push($aActivatingPlugins, $fineInit);
				            }
			            }else{
				            wp_send_json_error(
					            array(
						            'msg' => esc_html__('Your server does not allow to install plugins via our chanel. Please click on FAQs tab and read Manually Install Plugins to know how to install plugins manually.', 'wiloke')
					            )
				            );
			            }
		            }
                }
            }

            if ( $installPluginSuccessfully ){
                update_option( 'active_plugins', $aActivatingPlugins );
            }

            update_option('wiloke_tmp_saving_plugin_installation', $aTmpSavingPluginInstallation);
            wp_send_json_success(
                array(
                    'msg'   => $this->installedMsg($aPlugin['name'], $installPluginSuccessfully),
                    'item_error'=>!$installPluginSuccessfully
                )
            );
        }

        if ( $aData['method'] === 'install_plugins_only' ){
            delete_option('wiloke_tmp_saving_plugin_installation');
	        delete_option('wiloke_tmp_demos_file');
	        delete_option('wiloke_tmp_installed_demo');
	        wp_send_json_success(
		        array(
			        'msg'   => esc_html__('Congratulations! All plugins have been installed. Now let\'s build your website or click on FAQs tab to refer the useful information.', 'wiloke'),
                    'done'  => true
		        )
	        );
        }

        $aXML = get_option('wiloke_tmp_demos_file');
        $aDemoInstalled = get_option('wiloke_tmp_installed_demo');
		$aDemoInstalled = empty($aDemoInstalled) ? array() : $aDemoInstalled;
        if ( empty($aXML) ){
	        $aXML = glob($dummyDir.'*.xml');
	        natsort($aXML);
            update_option('wiloke_tmp_demos_file', $aXML);
        }

        if ( count($aXML) === count($aDemoInstalled) ){
            if ( !get_option('wiloke_tmp_setup_wiloke_submission') ){
	            $this->setWilokeSubmissionPages();
	            update_option('wiloke_tmp_setup_wiloke_submission', true);
	            wp_send_json_success(
		            array(
			            'msg'   => esc_html__('Wiloke Submission\'s pages have been setup', 'wiloke'),
			            'item_error'=>false
		            )
	            );
            }
            
            if ( !get_option('wiloke_permanent_imported_themeoptions') ){
                $themeOptionsDir = $dummyDir . 'themeoptions.json';
                update_option('wiloke_backup_themeoptions', json_encode(get_option('wiloke_themeoptions')));
                $aThemeOptions = json_decode(file_get_contents($themeOptionsDir), true);
                $aThemeOptions['listing_search_page']     = 'map';
	            $aThemeOptions['header_search_map_page']  = $this->createMapPage();
                update_option('wiloke_themeoptions', $aThemeOptions);
	            update_option('wiloke_permanent_imported_themeoptions', true);
                wp_send_json_success(
                    array(
                        'msg'   => esc_html__('The theme options data has been imported.', 'wiloke')
                    )
                );
            }

	        if ( !get_option('wiloke_permanent_imported_tax_options') ) {
		        $aTaxOptions = glob($dummyDir.'taxonomy-options-*.json');
		        if ( !empty($aTaxOptions) ){
			        foreach ( $aTaxOptions as $fileDir ){
			            $aTaxData = file_get_contents($fileDir);
			            if ( !empty($aTaxData) ){
			                $aTaxData = json_decode($aTaxData, true);
                            if ( empty($aTaxData) ){
	                            $aTaxData = json_decode(stripslashes($aTaxData), true);
                            }

                            if ( !empty($aTaxData) ){
	                            foreach ( $aTaxData['data'] as $termSlug => $aTermOptions ){
		                            $oDetectTerm = get_term_by('slug', $termSlug, $aTaxData['taxonomy']);
		                            if ( !empty($oDetectTerm) && !is_wp_error($oDetectTerm) ){
			                            \Wiloke::updateOption('_wiloke_cat_settings_'.$oDetectTerm->term_id, $aTermOptions);
                                    }
	                            }
                            }

                        }
                    }
			        update_option('wiloke_permanent_imported_tax_options', true);
			        wp_send_json_success(
				        array(
					        'msg'   => esc_html__('Taxonomies options have been imported.', 'wiloke')
				        )
			        );
		        }
	        }

	        if ( !get_option('wiloke_permanet_reupdate_term_parent') ){
                update_option('wiloke_permanet_reupdate_term_parent', true);
		        $termParent = $dummyDir . 'reupdatetermparent.json';
		        if ( !empty($termParent) ){
			        $aData = file_get_contents($termParent);
			        $status = $this->reUpdateTermParent($aData);
			        if ( $status ){
				        wp_send_json_success(
					        array(
						        'msg'   => esc_html__('Terms\'s Parent have been updated.', 'wiloke')
					        )
				        );
                    }else{
				        wp_send_json_error(
					        array(
						        'msg'   => esc_html__('Failed update Terms\'s Parent', 'wiloke')
					        )
				        );
                    }
                }
            }

	        if ( !get_option('wiloke_tpm_widgets') ) {
	            $widgetsDir = $dummyDir . 'widgets.wie';
	            $this->importingWidgets(file_get_contents($widgetsDir));
	            update_option('wiloke_tpm_widgets', true);
	            wp_send_json_success(
	                array(
	                    'msg'   => esc_html__('Widgets have been imported.', 'wiloke')
	                )
	            );
	        }

	        if ( !get_option('wiloke_tmp_setup_menu') ) {
		        $status = $this->setupMenu();
		        update_option('wiloke_tmp_setup_menu', true);
		        $msg = $status ? esc_html__('The mega menu has been setup', 'wiloke') : esc_html__('We could not setup the mega menu', 'wiloke');
		        wp_send_json_success(
			        array(
				        'msg'   => $msg
			        )
		        );
	        }

            delete_option('wiloke_tmp_setup_wiloke_submission');
            delete_option('wiloke_tmp_demos_file');
            delete_option('wiloke_tmp_saving_plugin_installation');
            delete_option('wiloke_tmp_installed_demo');
            delete_option('wiloke_tmp_setup_menu');
            delete_option('wiloke_tpm_widgets');
            delete_option('wiloke_themeoptions');
	        delete_option('wiloke_tmp_clean_menu');
	        delete_option('wiloke_tmp_delete_hello_world');

            if ( $postID = $this->setFrontPage() ){
                update_option('show_on_front', 'page');
                update_option('page_on_front', $postID);
            }

            wp_send_json_success(
	            array(
		            'msg'   => esc_html__('Congratulations! All plugins have been installed. Now let\'s build data or click on FAQs tab to refer the useful information.', 'wiloke'),
		            'done'  => true
	            )
            );
        }else{
	        foreach ( $aXML as $file ){
		        if ( empty($aDemoInstalled) || !in_array($file, $aDemoInstalled) ){
			        array_push($aDemoInstalled, $file);
                    if ( $isIgnoreWooCommerce && strpos($file, 'woocommerce') ){
                        continue;
                    }
                    $aParseFile = explode('/', $file);
                    if ( strpos($file, '-woocommerce.xml') === false ){
	                    $status = $this->_importXML($file);
                    }else{
	                    if ( empty($aActivatingPlugins) || !in_array('woocommerce/woocommerce.php', $aActivatingPlugins) ){
		                    $status = false;
                        }else{
		                    $status = $this->_importXML($file);
                        }
                    }

			        $fileName = end($aParseFile);
                    $msg = $status ? sprintf(esc_html__('%s has been imported', 'wiloke'), $fileName) : sprintf(esc_html__('%s has been failed.', 'wiloke'), $fileName);

                    update_option('wiloke_tmp_installed_demo', $aDemoInstalled);
                    wp_send_json_success(
                        array(
                            'msg'   => $msg,
                            'item_error'=>!$status
                        )
                    );
		        }
	        }
        }
    }

	public function register(){
		add_action('admin_menu', array($this, 'registerMenu'));
	}

	public function getImg($name){
	    return 'https://landing.wiloke.com/listgo/wiloke-guide/' . $name;
    }

	/**
	 * Try to grab information from WordPress API.
	 *
	 * @since 2.5.0
	 *
	 * @param string $slug Plugin slug.
	 * @return object Plugins_api response object on success, WP_Error on failure.
	 */
	protected function _getPluginsAPI( $slug ) {
		static $api = array(); // Cache received responses.

		if ( ! isset( $api[ $slug ] ) ) {
			if ( ! function_exists( 'plugins_api' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			}

			$response = plugins_api( 'plugin_information', array( 'slug' => $slug, 'fields' => array( 'sections' => false ) ) );

			$api[ $slug ] = false;

			if ( is_wp_error( $response ) ) {
				return $response;
			} else {
				$api[ $slug ] = $response;
			}
		}

		return $api[ $slug ];
	}

	public function registerMenu(){
		add_menu_page(esc_html__('Wiloke Listgo', 'wiloke'), esc_html__('Wiloke Guide', 'wiloke'), 'read', $this->slug, array($this, 'setupArea'),'dashicons-nametag', 100);
	}

	public function renderPopupItem($imgName){
        ?>
        <a href="<?php echo $this->getImg($imgName) ?>"><img class="ui image" src="<?php echo $this->getImg($imgName) ?>"></a>
        <?php
    }

	public function setupArea(){
		?>
		<div id="wiloke-submission-wrapper">
			<div class="form ui">
				<h1 class="dividing header"><?php esc_html_e('Welcome to Wiloke!', 'wiloke'); ?></h1>
				<div class="ui message info">
                    <p><?php \Wiloke::wiloke_kses_simple_html(__('First of all, thanks for using Wiloke Theme! The below are some useful information that explain how to setup the theme and how to set up the theme\'s elements. But, in case, you have any beyond question, don\'t hesitate to open a topic via Support Forum - <a href="http://support.wiloke.com" target="_blank">support.wiloke.com</a> - or mail to us - <a href="mailto:sale@wiloke.com">sale@wiloke.com</a>.', 'wiloke')); ?></p>
                    <p>
                        <?php esc_html_e('Don\'t forget to follow our fan page. We announce Wiloke\'s news via', 'wiloke') ?>
                        <a href="https://www.facebook.com/wilokewp/" target="_blank">Facebook chanel</a> and <a href="https://twitter.com/wilokethemes" target="_blank">Twitter chanel</a>
                    </p>
                    <p><?php \Wiloke::wiloke_kses_simple_html(__('If you love theme and satisfied with our service, please give us a <a href="http://themeforest.net/downloads" target="_blank">rate of 5 stars</a>, it will give us lots of energies!', 'wiloke')); ?></p>
                </div>
			</div>

	        <div class="ui top attached tabular menu">
	            <a class="active item" data-tab="setup"><?php esc_html_e('Import Demo', 'wiloke'); ?></a>
	            <a class="item" data-tab="faq"><?php esc_html_e('FAQs', 'wiloke'); ?></a>
	        </div>

	        <div class="ui bottom attached active tab segment" data-tab="setup">
	            <div class="ui icon message">
	                <i class="pied piper alternate icon"></i>
	                <div class="content">
	                    <div class="header">
	                        <?php esc_html_e('Before importing demos, please pay attention two things: '); ?>
	                    </div>
	                    <div class="ui segment">
	                        <p><?php \Wiloke::wiloke_kses_simple_html(__('Make sure that your website data is empty. You can do that by using WordPress Reset plugin. Please go to <strong>Plugins</strong> -> <strong>Add New</strong> -> Entering <strong>WordPress Reset</strong> keyword -> <strong>Installing</strong> and <strong>Activating</strong> the plugin then Go to <strong>Tools</strong> -> <strong>WordPress Reset</strong> -> <strong>Clicking on Reset button</strong>.  If you already created your data before but now You only want to import a Home page, please go to <strong>Wiloke Service</strong> -> <strong>Import Demo</strong>, We also add the new demos via this chanel.', 'wiloke'), false); ?></p>
	                    </div>
	                    <div class="ui segment">
	                        <p><?php \Wiloke::wiloke_kses_simple_html(__('It may takes up to almost 10 minutes, so please patient. But if it takes more than 10 minutes, please access to your WordPress folder - Using FileZilla or the same tools - Opening <strong>wp-config.php</strong> -> Put <strong>define("FS_METHOD", "direct")</strong> before <strong>That\'s all, stop editing! Happy blogging.</strong> text -> Back to the page and click <strong>Install demo & Setup required plugins</strong> again.', 'wiloke')); ?></p>
	                    </div>
	                </div>
	            </div>

	            <div class="ui segment">
		            <?php wp_nonce_field('wiloke-setup-nonce-action', 'wiloke-setup-nonce-field'); ?>
	                <h3 class="header ui"><?php esc_html_e('Installing plugins only', 'wiloke'); ?></h3>
	                <p><?php esc_html_e('Installing plugins without importing the demos data.', 'wiloke'); ?></p>
                    <p class="message ui warning"><?php esc_html_e('Notice: Downloading Wiloke Social Login plugin takes more time than normal in this chanel, it is faster if you click on Appearance -> Install Plugins -> Installing Wiloke Social Login Plugin.', 'wiloke'); ?></p>
                    <form id="wiloke-install-plugins" class="wiloke-setup form ui" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST">
	                    <div class="field">
	                        <div class="ui test toggle checkbox">
	                            <input id="except-woocommerce" type="checkbox" name="except_woocommerce" value="1">
	                            <label for="except-woocommerce"><?php esc_html_e('Except WooCommerce', 'wiloke'); ?></label>
	                        </div>
	                    </div>
	                    <input type="hidden" name="method" value="install_plugins_only">
	                    <div class="field">
	                        <button type="submit" class="ui green basic button"><?php esc_html_e('Install', 'wiloke'); ?></button>
	                    </div>
	                    <div class="message ui success notification available">
	                        <p class="system-running"><?php esc_html_e('System is running ...', 'wiloke'); ?></p>
	                        <ul class="list"></ul>
	                    </div>
	                </form>
	            </div>

	            <div class="ui segment">
	                <h3 class="header ui"><?php esc_html_e('Installing plugins & Importing Demos', 'wiloke'); ?></h3>
	                <p><?php esc_html_e('Installing all plugins without importing the demos data.', 'wiloke'); ?></p>
                    <p class="message ui warning"><?php esc_html_e('Notice: Downloading Wiloke Social Login plugin takes more time than normal in this chanel, it is faster if you click on Appearance -> Install Plugins -> Installing Wiloke Social Login Plugin.', 'wiloke'); ?></p>
	                <form id="wiloke-install-plugins-and-import-demos" class="wiloke-setup form ui" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST">
	                    <div class="field">
	                        <div class="ui test toggle checkbox">
	                            <input id="except-woocommerce" type="checkbox" name="except_woocommerce" value="1">
	                            <label for="except-woocommerce"><?php esc_html_e('Except WooCommerce', 'wiloke'); ?></label>
	                        </div>
	                    </div>
	                    <input type="hidden" name="method" value="install_the_both">
	                    <div class="field">
	                        <button type="submit" class="ui green basic button"><?php esc_html_e('Install & Import', 'wiloke'); ?></button>
	                    </div>
	                    <div class="message ui success notification available">
		                    <p class="system-running"><?php esc_html_e('System is running ...', 'wiloke'); ?></p>
	                        <ul class="list"></ul>
	                    </div>
	                </form>
	            </div>
	        </div>

	        <div class="ui bottom attached tab segment" data-tab="faq">
	            <div class="ui styled fluid accordion">
	                <div class="title">
	                    <i class="dropdown icon"></i>
		                <?php esc_html_e('Wiloke Submission', 'wiloke'); ?>
	                </div>
	                <div class="content">
	                    <div class="plugin-import-demo form">
	                        <div class="ui info message">
	                            <p><?php \Wiloke::wiloke_kses_simple_html(__('Wiloke Submission is a main featured of the theme. It allows your users to upload an article to your site. The user can be upload as free or premium or the both, let\'s example, You create 3 packages, there are 2 packages are premium packages and the last is free package.', 'wiloke')); ?></p>
	                        </div>
	                    </div>
	                    <div class="title">
	                        <i class="dropdown icon"></i>
	                        <?php esc_html_e('How to setup Wiloke Submission?', 'wiloke'); ?>
	                    </div>
	                    <div class="content">
	                        <ol>
	                            <li><?php \Wiloke::wiloke_kses_simple_html(esc_html__('Firstly, ensure that Wiloke Listgo Functionality is activated, If you do not do that, please follow the first guide - Plugin Installation - to know t to do it.', 'wiloke')); ?></li>
	                            <li>
	                                <?php esc_html_e('Setup Payment Pages', 'wiloke'); ?>
	                                <p class="message info ui" style="margin-top: 20px;"><?php \Wiloke::wiloke_kses_simple_html(__('We have 6 payment pages: <strong>Package</strong>, <strong>Add listing</strong>, <strong>Checkout</strong>, <strong>My account</strong>, <strong>Thank you</strong> and <strong>Cancel</strong>. You can manually set those pages by going to Pages -> Add New -> Creating the payment pages, then go to <strong>Wiloke Submission</strong> -> </Settings> to complete the last step, but We highly recommend using <strong>Automatically Install Payment Page</strong> feature: ', 'wiloke')); ?></p>
	                                <ol style="margin-top: 20px;" class="list">
	                                    <li><?php \Wiloke::wiloke_kses_simple_html(__('Click on <strong>Wiloke Submission</strong> from the admin sidebar', 'wiloke')); ?></li>
	                                    <li><?php \Wiloke::wiloke_kses_simple_html(__('Click on <strong>Install</strong> button and wait for few seconds.', 'wiloke')); ?></li>
	                                </ol>
	                            </li>
	                            <li>
	                                <?php esc_html_e('Now you need to setup your packages', 'wiloke'); ?>
	                                <ol class="list" style="margin-top: 20px;">
	                                    <li><?php \Wiloke::wiloke_kses_simple_html(__('Click on <strong>Pricings</strong> -> <strong>Add New</strong>', 'wiloke')); ?></li>
	                                    <li>Now setup your package</li>
	                                    <li>
	                                        <h5 class="heading"><?php esc_html_e('Finally, We need to assign our packages to the Package page: ', 'wiloke') ?></h5>
	                                        <ol class="list">
	                                            <li><?php \Wiloke::wiloke_kses_simple_html(__('Click on <strong>Pages</strong> -> Looking for <strong>Wiloke Package</strong>', 'wiloke')); ?></li>
	                                            <li><?php \Wiloke::wiloke_kses_simple_html(__('Switch WordPress Editor to <strong>BACKEND EDITOR</strong> mode', 'wiloke')); ?></li>
	                                            <li><?php \Wiloke::wiloke_kses_simple_html(__('Click on <strong>Add Element</strong> -> Looking for <strong>Packages</strong> shortcode -> <strong>Add your package here</strong>', 'wiloke')); ?></li>
	                                        </ol>
	                                    </li>
	                                </ol>
	                            </li>
	                        </ol>
                            <p class="message ui danger">From ListGo 1.0.9, We moved Package Plan to Wiloke Submission -> Settings -> Customer Plan, please read this tutorial to know more <a target="_blank" href="https://blog.wiloke.com/setting-wiloke-submission/">HOW TO SETTING UP WILOKE SUBMISSION?</a></p>
	                    </div>
	                    <div class="ui images gallery-popup">
                            <iframe width="560" height="315" src="https://www.youtube.com/embed/qvIKbzQjJf8" frameborder="0" allowfullscreen></iframe>
	                    </div>
	                </div>

                    <div class="title">
                        <i class="dropdown icon"></i>
			            <?php esc_html_e('How to setup a partner account?', 'wiloke'); ?>
                    </div>
                    <div class="content">
                        <div class="ui message info">
				            <?php esc_html_e('It is so cool if we have a partner in any relationship.', 'wiloke'); ?>
                        </div>

                        <h4 class="header ui"><?php esc_html_e('Creating a partner account with full permissions', 'wiloke'); ?></h4>
                        <div class="ui segment">
				            <?php \Wiloke::wiloke_kses_simple_html(__('If you want to create an account with full permissions - it means they can do anything you do - You just need to click on <strong>Users</strong> -> <strong>Add New</strong> -> and set role as <strong>Administrator</strong>', 'wiloke')); ?>
                        </div>

                        <h4 class="header ui"><?php esc_html_e('Creating a partner account with Wiloke Submission permission.', 'wiloke'); ?></h4>
                        <div class="ui segment">
				            <?php esc_html_e('This feature allow to create a partner account but We will limit its permission, This account can submit a listing through Front-end. Please follow these steps below: ', 'wiloke'); ?>
                            <ol>
                                <li><?php \Wiloke::wiloke_kses_simple_html(__('Go to <strong>Pricings</strong> -> <strong>Add New</strong> -> Creating a package, We will leave empty the following fields: Price, Duration, Number of posts. This package <strong>SHOULD NOT</strong> publish on the <strong>Package</strong> page (Refer to Wiloke Submission section to know what Package page is).', 'wiloke')); ?></li>
                                <li><?php \Wiloke::wiloke_kses_simple_html(__('Go to <strong>Users</strong> -> <strong>Add New</strong> -> Grant <strong>Wiloke Submission</strong> role to this user.', 'wiloke')); ?></li>
                                <li><?php \Wiloke::wiloke_kses_simple_html(__('Finally, click on <strong>Wiloke Submission</strong> -> <strong>Invoices</strong> -> <strong>Add new order</strong> -> Grandt package that you created at the first step to the user, who you created a the below step.', 'wiloke')); ?></li>
                            </ol>
                        </div>

                        <div class="ui images gallery-popup">
				            <?php
				            $this->renderPopupItem('13.png');
				            $this->renderPopupItem('14.png');
				            $this->renderPopupItem('15.png');
				            ?>
                        </div>
                    </div>

	                <div class="title">
	                    <i class="dropdown icon"></i>
	                    <?php esc_html_e('How to create a Map page?', 'wiloke'); ?>
	                </div>
	                <div class="content">
	                    <h4 class="ui header"><?php esc_html_e('We support 2 ways to create a Map page:', 'wiloke'); ?></h4>
	                    <div class="ui segment">
	                        <?php \Wiloke::wiloke_kses_simple_html(sprintf(__('<a href="%s" class="single-popup"><img title="Click to show popup" class="ui top aligned small image space" src="%s"></a> Using <strong>Listings In Map</strong> shortcode. You should use the shortcode, in case, you want to <strong style="color:red;">combine</strong> Map section with the other sections. You can refer this demo <a href="https://listgo.wiloke.com/home-gallery-2/" target="_blank">Home Gallery 2</a>. We highly recommend you to enable <strong>Disable draggable when init</strong> feature of the shortcode.', 'wiloke'), $this->getImg('1.png'), $this->getImg('1.png'))); ?>
	                    </div>

	                    <div class="ui segment">
		                    <?php \Wiloke::wiloke_kses_simple_html(sprintf(__('<a href="%s" class="single-popup"><img class="ui top aligned small image space" src="%s"></a> Using <strong>Map</strong> template. This is faster way to create the Map page. You should use this way, in case, you want to create a full screen Map page.', 'wiloke'), $this->getImg('2.png'), $this->getImg('2.png'))); ?>
	                    </div>

	                    <h4 class="ui header"><?php esc_html_e('Map maker', 'wiloke'); ?></h4>
	                    <div class="ui segment">
		                    <p><?php \Wiloke::wiloke_kses_simple_html(sprintf(__('Click on <strong>Listings</strong> -> <strong>Add New</strong>, You will see a box called <strong>Listing Categories</strong>, <strong style="color:red">each category represent one service</strong>, this is our idea.', 'wiloke'))); ?></p>
		                    <p><?php \Wiloke::wiloke_kses_simple_html(sprintf(__('Each category has its own map marker: <strong>Listings</strong> -> <strong>Listing Categories</strong> -> <strong>Select a category</strong>, You will see Map marker setting here. So, let\'s an example, if a Listing belongs to <strong>Restaurent</strong> Category, it will be set the maker of this category. If a listing has more than 1 Category, the first category will be choosen.', 'wiloke'))); ?></p>
	                        <div class="ui images gallery-popup">
			                    <?php
			                    $this->renderPopupItem('10.png');
			                    $this->renderPopupItem('11.png');
			                    $this->renderPopupItem('12.png');
			                    ?>
	                        </div>
	                    </div>

                        <h4 class="ui header"><?php esc_html_e('Mapbox Theme', 'wiloke'); ?></h4>
                        <div class="ui segment">
                            <div class="ui images gallery-popup">
				                <?php
				                $this->renderPopupItem('map-theme-1.png');
				                $this->renderPopupItem('map-theme-2.png');
				                ?>
                            </div>
                        </div>

	                </div>

                    <div class="title">
                        <i class="dropdown icon"></i>
			            <?php esc_html_e('Why Map doesn\'t work?', 'wiloke'); ?>
                    </div>
                    <div class="content">
                        <div class="plugin-import-demo form">
                            <div class="ui error message">
                                <p><?php \Wiloke::wiloke_kses_simple_html(__('Google Map API key and Map box token are required by the theme. Please go to <strong>Appearance</strong> -> <strong>Theme Options</strong> -> <strong>General</strong> -> <strong>Scroll down to the bottom of your screen</strong>', 'wiloke')); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="title">
                        <i class="dropdown icon"></i>
                        <?php esc_html_e('Wiloke Map Icons', 'wiloke'); ?>
                    </div>
                    <div class="content">
                        <div class="plugin-import-demo form">
                            <div class="ui error message">
                                <p><?php \Wiloke::wiloke_kses_simple_html(__('You can download our Map Icons here <a href="https://www.dropbox.com/s/l67lf2t135j1ns0/map-icons.zip?dl=0" target="_blank">Wiloke Map Icons</a>', 'wiloke')); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="title">
                        <i class="dropdown icon"></i>
			            <?php esc_html_e('How to use Wiloke Design Tool?', 'wiloke'); ?>
                    </div>
                    <div class="content">
                        <div class="ui message info">
				            <?php esc_html_e('Wiloke Design Tool is a feature developed by Wiloke team. It allows to create a modern layout in seconds.', 'wiloke'); ?>
                        </div>

                        <div class="ui segment">
				            <?php esc_html_e('To use Wiloke Design Tool, please follow the steps below: ', 'wiloke'); ?>
                            <ol>
                                <li><?php \Wiloke::wiloke_kses_simple_html(__('Go to <strong>Pages</strong> -> <strong>Add New</strong>', 'wiloke')); ?></li>
                                <li><?php \Wiloke::wiloke_kses_simple_html(__('Ensure that you are using <strong>Back end</strong> Editor mode. Now click on Add New ShortCode icon.', 'wiloke')); ?></li>
                                <li><?php \Wiloke::wiloke_kses_simple_html(__('Looking for <strong>Wiloke Design Tool</strong> or Navigate to <strong>Wiloke</strong> tab then select <strong>Wiloke Design Tool. Click on it.</strong>', 'wiloke')); ?></li>
                                <li><?php \Wiloke::wiloke_kses_simple_html(__('What does Item Type mean?', 'wiloke')); ?>
                                    <ul class="ui list">
                                        <li><strong>Listing:</strong> <?php \Wiloke::wiloke_kses_simple_html(__('Listing mode means the Wiloke Design Tool will show the listings in Listing Locations / Listing Categories - it depends on <strong>Get Listings By</strong> settings. For example, If you choose Get Listings By Listing Location, and at <strong>Pickup Listing Locations</strong> you want to use France, Vietnam. Wiloke Design Tools will show listings, which belong to France, and Vietnam.', 'wiloke')); ?></li>
                                        <li><strong>Taxonomy:</strong> <?php \Wiloke::wiloke_kses_simple_html('Taxonomy mode means the Wiloke Design Tool will show themselves Listing Locations / Listing Categories - it depends on <strong>Get Listings By</strong> settings. For example, similar to above example, but in the case, Vietnam and France will be shown. When user click on Vietnam box, it leads to Vietnam category.'.'wiloke'); ?></li>
                                    </ul>
                                </li>
                            </ol>
                        </div>
                        <div class="ui segment">
                            <iframe width="560" height="315" src="https://www.youtube.com/embed/15VSSGqDvKI" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>

                    <div class="title">
                        <i class="dropdown icon"></i>
			            <?php esc_html_e('How to create a sidebar on the listing page?', 'wiloke'); ?>
                    </div>
                    <div class="content">
                        <div class="ui segment">
				            <?php \Wiloke::wiloke_kses_simple_html(__('Before we get into create a sidebar for the lising page, We want to show you how to set a style for your listing.')); ?>
                            <ol>
                                <li><?php \Wiloke::wiloke_kses_simple_html(__('Click on <strong>Listings</strong> -> <strong>Add New</strong> from the admin sidebar', 'wiloke')); ?></li>
                                <li><?php \Wiloke::wiloke_kses_simple_html(__('Look at the right of your screen, you will see <strong>Page Attributes box</strong>, Page template should be under the box, and it is Page style setting. We offer two styles for you: <a href="https://listgo.wiloke.com/listing/mickies-dairy-bar/" target="_blank">Classic Layout</a> and <a href="https://listgo.wiloke.com/listing/matts-big-breakfast/" target="_blank">Creative Layout</a>', 'wiloke')); ?></li>
                            </ol>
                        </div>

                        <div class="ui segment">
                            <p class="ui message info"><?php esc_html_e('Sidebar layout helps to show all listing information such as: Listing Gallery, Business Hours, Location.', 'wiloke') ?></p>
                            <p class="ui message error"><?php esc_html_e('Wiloke ListGo Widgets plugin is required by this feature.', 'wiloke') ?></p>
                            <ol>
                                <li><?php \Wiloke::wiloke_kses_simple_html(__('Click on <strong>Appearance</strong> -> <strong>Widgets</strong> from the admin sidebar', 'wiloke')); ?></li>
                                <li><?php \Wiloke::wiloke_kses_simple_html(__('Here you will see <strong>Single Listing Sidebar</strong> area. All Widget Items that belong to <strong>Single Listing Sidebar</strong> always contain <strong>OSP</strong> in its own name. Dragging these widgets into <strong>Single Listing Sidebar</strong>', 'wiloke')); ?></li>
                            </ol>
                        </div>
                        <div class="ui segment">
                            <iframe width="560" height="315" src="https://www.youtube.com/embed/AvNGpRqio84" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>

                    <div class="title">
                        <i class="dropdown icon"></i>
			            <?php esc_html_e('Wiloke Minify Scripts', 'wiloke'); ?>
                    </div>
                    <div class="content">
                        <div class="plugin-import-demo form">
                            <div class="ui success message">
                                <p><?php \Wiloke::wiloke_kses_simple_html(__('<strong>Wiloke Minify Scripts</strong> is a feature of <strong>Wiloke Service</strong>, it helps to speed up your website. To use this feature, please go to Wiloke Service -> Minify Scripts.', 'wiloke')); ?></p>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Run after the plugin is activated
	 * @since 1.0
	 */
	public function redirectToSetup(){
	    if ( get_option($this->firstTimeSetup) ){
            return false;
        }

        update_option($this->firstTimeSetup, true);
        wp_redirect(admin_url('admin.php?page='.rawurldecode($this->slug)));
        exit();
	}

	/**
	 * Enqueue Scripts
	 * @since 1.0
	 */
	public function enqueueScripts($hook){
		if ( strpos($hook, $this->slug) !== false ){
		    wp_enqueue_script('jquery-ui-tooltip');
			wp_enqueue_script('semantic-ui', plugin_dir_url(dirname(__FILE__)) . '../admin/assets/semantic-ui/semantic.min.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
			wp_enqueue_script('magnific-popup', plugin_dir_url(dirname(__FILE__)) . '../admin/assets/magnific-popup/jquery.magnific-popup.min.js', array('jquery'), WILOKE_LISTGO_FC_VERSION, true);
		    // wp_enqueue_script('jquery-ui-accordion');
			wp_enqueue_style('magnific-popup', plugin_dir_url(dirname(__FILE__)) . '../admin/assets/magnific-popup/jquery.magnific-popup.min.css', array(), WILOKE_LISTGO_FC_VERSION);
			wp_enqueue_style('semantic-ui', plugin_dir_url(dirname(__FILE__)) . '../admin/assets/semantic-ui/form.min.css', array(), WILOKE_LISTGO_FC_VERSION, false);
		}
	}
}