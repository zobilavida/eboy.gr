<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/wrapper.php',   // Theme wrapper class
  'lib/customizer.php', // Theme customizer
  'lib/button.php' // Load Gravity Forms via AJAX
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

//Remove <p> from body
// remove_filter ('the_content', 'wpautop');

// Register Custom Navigation Walker (Soil)
require_once('bs4navwalker.php');

//declare your new menu
register_nav_menus( array(
    'primary' => __( 'Primary Menu', 'sage' ),
    'pages' => __( 'In Menu', 'sage' ),
) );

// Add svg support
function cc_mime_types( $mimes ){
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

//enable logo uploading via the customize theme page

function themeslug_theme_customizer( $wp_customize ) {
    $wp_customize->add_section( 'themeslug_logo_section' , array(
    'title'       => __( 'Logo', 'themeslug' ),
    'priority'    => 30,
    'description' => 'Upload a logo to replace the default site name and description in the header',
) );
$wp_customize->add_setting( 'themeslug_logo' );
$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'themeslug_logo', array(
    'label'    => __( 'Logo', 'themeslug' ),
    'section'  => 'themeslug_logo_section',
    'settings' => 'themeslug_logo',
    'extensions' => array( 'jpg', 'jpeg', 'gif', 'png', 'svg' ),
) ) );
}
add_action('customize_register', 'themeslug_theme_customizer');



function my_register_cpt() {

/* Create user CPT */
  $labels = array(
		'name'                  => _x( 'Users', 'Post Type General Name', 'sage' ),
		'singular_name'         => _x( 'User', 'Post Type Singular Name', 'sage' ),
		'menu_name'             => __( 'Users', 'sage' ),
		'name_admin_bar'        => __( 'Users', 'sage' )
	);
	$args = array(
		'label'                 => __( 'User', 'sage' ),
		'labels'                => $labels,
		'hierarchical'          => true,
		'public'                => true,
    'menu_position'         => 2
	);
	register_post_type( 'user', $args );




	$labels = array(
		'name'                  => _x( 'Hotels', 'Post Type General Name', 'sage' ),
		'singular_name'         => _x( 'Hotel', 'Post Type Singular Name', 'sage' ),
		'menu_name'             => __( 'Hotels', 'sage' ),
		'name_admin_bar'        => __( 'Hotels', 'sage' )
	);
	$args = array(
		'label'                 => __( 'Hotel', 'sage' ),
		'labels'                => $labels,
		'hierarchical'          => true,
		'public'                => true,
    'menu_position'         => 4
	);
	register_post_type( 'hotel', $args );
  $labels = array(
		'name'                  => _x( 'internships', 'Post Type General Name', 'sage' ),
		'singular_name'         => _x( 'internship', 'Post Type Singular Name', 'sage' ),
		'menu_name'             => __( 'Internships', 'sage' ),
		'name_admin_bar'        => __( 'Internships', 'sage' ),
		'archives'              => __( 'Internships Archives', 'sage' ),
		'parent_item_colon'     => __( 'Parent Item:', 'sage' ),
		'all_items'             => __( 'All Internships', 'sage' ),
		'add_new_item'          => __( 'Add New Internship', 'sage' ),
		'add_new'               => __( 'Add New Internship', 'sage' ),
		'new_item'              => __( 'New Internships', 'sage' ),
		'edit_item'             => __( 'Edit Internship', 'sage' ),
		'update_item'           => __( 'Update Internship', 'sage' ),
		'view_item'             => __( 'View Internship', 'sage' ),
		'search_items'          => __( 'Search Internships', 'sage' ),
		'not_found'             => __( 'No Internships found', 'sage' ),
		'not_found_in_trash'    => __( 'No Internships found in Trash', 'sage' ),
		'featured_image'        => __( 'Featured Image', 'sage' ),
		'set_featured_image'    => __( 'Set featured image', 'sage' ),
		'remove_featured_image' => __( 'Remove featured image', 'sage' ),
		'use_featured_image'    => __( 'Use as featured image', 'sage' ),
		'insert_into_item'      => __( 'Insert into Internship', 'sage' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Internship', 'sage' ),
		'items_list'            => __( 'Internships list', 'sage' ),
		'items_list_navigation' => __( 'Internships list navigation', 'sage' ),
		'filter_items_list'     => __( 'Filter Internships list', 'sage' ),
	);
	$args = array(
		'label'                 => __( 'internship', 'sage' ),
		'description'           => __( 'Post Type Description', 'sage' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'excerpt', 'custom-fields', 'buddypress-activity' ),

		'taxonomies'            => array( 'internship_cat', ' internship_lc', ' internship_lang' ),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'query_var'             => 'internship',

	);
	register_post_type( 'internship', $args );}
  $labels = array(
    'name'                  => _x( 'Applications', 'Post Type General Name', 'sage' ),
    'singular_name'         => _x( 'Application', 'Post Type Singular Name', 'sage' ),
    'menu_name'             => __( 'Applications', 'sage' ),
    'name_admin_bar'        => __( 'Applications', 'sage' ),
    'archives'              => __( 'Applications Archives', 'sage' ),
    'parent_item_colon'     => __( 'Parent Item:', 'sage' ),
    'all_items'             => __( 'All Applications', 'sage' ),
    'add_new_item'          => __( 'Add New Application', 'sage' ),
    'add_new'               => __( 'Add New Application', 'sage' ),
    'new_item'              => __( 'New Application', 'sage' ),
    'edit_item'             => __( 'Edit Application', 'sage' ),
    'update_item'           => __( 'Update Application', 'sage' ),
    'view_item'             => __( 'View Applications', 'sage' ),
    'search_items'          => __( 'Search Applications', 'sage' ),
    'not_found'             => __( 'No Applications found', 'sage' ),
    'not_found_in_trash'    => __( 'No Applications found in Trash', 'sage' ),
    'featured_image'        => __( 'Featured Image', 'sage' ),
    'set_featured_image'    => __( 'Set featured image', 'sage' ),
    'remove_featured_image' => __( 'Remove featured image', 'sage' ),
    'use_featured_image'    => __( 'Use as featured image', 'sage' ),
    'insert_into_item'      => __( 'Insert into Application', 'sage' ),
    'uploaded_to_this_item' => __( 'Uploaded to this Application', 'sage' ),
    'items_list'            => __( 'Applications list', 'sage' ),
    'items_list_navigation' => __( 'Applications list navigation', 'sage' ),
    'filter_items_list'     => __( 'Filter Applications list', 'sage' ),
  );
  $args = array(
    'label'                 => __( 'application', 'sage' ),
    'description'           => __( 'Post Type Description', 'sage' ),
    'labels'                => $labels,
    'supports'              => array( 'title', 'editor', 'excerpt', 'comments', 'custom-fields', 'page-attributes'),
    //'taxonomies'            => array( 'internship_cat', ' internship_lc' ),
    'hierarchical'          => false,
    'public'                => true,
    'show_ui'               => true,
    'show_in_menu'          => true,
    'menu_position'         => 5,
    'show_in_admin_bar'     => true,
    'show_in_nav_menus'     => true,
    'can_export'            => true,
    'has_archive'           => true,
    'exclude_from_search'   => false,
    'publicly_queryable'    => true,
    'query_var'             => 'application',
    'capability_type'       => 'post',
  );
  register_post_type( 'application', $args );
add_action( 'init', 'my_register_cpt' );

function record_cpt_activity_content( $cpt ) {

    if ( 'new_internship' === $cpt['type'] ) {



        $cpt['content'] = '%1$s';
    }


    return $cpt;
}
add_filter('bp_before_activity_add_parse_args', 'record_cpt_activity_content');


/* Add CPTs to author archives */
function custom_post_author_archive($query) {
    if ($query->is_author)
        $query->set( 'post_type', array('application', 'post') );
    remove_action( 'pre_get_posts', 'custom_post_author_archive' );
}
add_action('pre_get_posts', 'custom_post_author_archive');

function my_add_meta_boxes() {
	add_meta_box( 'internship-parent', 'Hotel', 'internship_attributes_meta_box', 'internship', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'my_add_meta_boxes' );

function internship_attributes_meta_box( $post ) {
	$post_type_object = get_post_type_object( $post->post_type );
	$pages = wp_dropdown_pages( array( 'post_type' => 'hotel', 'selected' => $post->post_parent, 'name' => 'parent_id', 'show_option_none' => __( '(no parent)' ), 'sort_column'=> 'menu_order, post_title', 'echo' => 0 ) );
	if ( ! empty( $pages ) ) {
		echo $pages;
	}
}

// Register Custom Taxonomy
function custom_internship_cats() {

	$labels = array(
		'name'                       => _x( 'Internship Category', 'Taxonomy General Name', 'sage' ),
		'singular_name'              => _x( 'Internships Category', 'Taxonomy Singular Name', 'sage' ),
		'menu_name'                  => __( 'Internships Categories', 'sage' ),
		'all_items'                  => __( 'All Internships Categories', 'sage' ),
		'parent_item'                => __( 'Parent Internships Categories', 'sage' ),
		'parent_item_colon'          => __( 'Parent Item:', 'sage' ),
		'new_item_name'              => __( 'New Internships Categories Name', 'sage' ),
		'add_new_item'               => __( 'Add New Internship Category', 'sage' ),
		'edit_item'                  => __( 'Edit Internship Category', 'sage' ),
		'update_item'                => __( 'Update Internship Category', 'sage' ),
		'view_item'                  => __( 'View Internship Category', 'sage' ),
		'separate_items_with_commas' => __( 'Separate Internships Categories with commas', 'sage' ),
		'add_or_remove_items'        => __( 'Add or remove Internships Categories', 'sage' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'sage' ),
		'popular_items'              => __( 'Popular Internships Categories', 'sage' ),
		'search_items'               => __( 'Search Internships Categories', 'sage' ),
		'not_found'                  => __( 'Not Found', 'sage' ),
		'no_terms'                   => __( 'No Internships Categories', 'sage' ),
		'items_list'                 => __( 'Internships Categories list', 'sage' ),
		'items_list_navigation'      => __( 'Internships Categories list navigation', 'sage' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'internship_cat', array( 'internship' ), $args );

}
add_action( 'init', 'custom_internship_cats', 0 );

// Register Custom Taxonomy
function custom_internship_lc() {

	$labels = array(
		'name'                       => _x( 'Internship Location', 'Taxonomy General Name', 'sage' ),
		'singular_name'              => _x( 'Internships Location', 'Taxonomy Singular Name', 'sage' ),
		'menu_name'                  => __( 'Internships Locations', 'sage' ),
		'all_items'                  => __( 'All Internships Locations', 'sage' ),
		'parent_item'                => __( 'Parent Internships Locations', 'sage' ),
		'parent_item_colon'          => __( 'Parent Item:', 'sage' ),
		'new_item_name'              => __( 'New Internships Locations Name', 'sage' ),
		'add_new_item'               => __( 'Add New Internship Location', 'sage' ),
		'edit_item'                  => __( 'Edit Internship Location', 'sage' ),
		'update_item'                => __( 'Update Internship Location', 'sage' ),
		'view_item'                  => __( 'View Internship Location', 'sage' ),
		'separate_items_with_commas' => __( 'Separate Internships Locations with commas', 'sage' ),
		'add_or_remove_items'        => __( 'Add or remove Internships Locations', 'sage' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'sage' ),
		'popular_items'              => __( 'Popular Internships Locations', 'sage' ),
		'search_items'               => __( 'Search Internships Locations', 'sage' ),
		'not_found'                  => __( 'Not Found', 'sage' ),
		'no_terms'                   => __( 'No Internships Locations', 'sage' ),
		'items_list'                 => __( 'Internships Locations list', 'sage' ),
		'items_list_navigation'      => __( 'Internships Locations list navigation', 'sage' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'internship_lc', array( 'internship' ), $args );

}
add_action( 'init', 'custom_internship_lc', 0 );

// Register Custom Taxonomy
function custom_internship_langs() {

	$labels = array(
		'name'                       => _x( 'Internship Languages', 'Taxonomy General Name', 'sage' ),
		'singular_name'              => _x( 'Internships Language', 'Taxonomy Singular Name', 'sage' ),
		'menu_name'                  => __( 'Internships Languages', 'sage' ),
		'all_items'                  => __( 'All Internships Languages', 'sage' ),
		'parent_item'                => __( 'Parent Internships Languages', 'sage' ),
		'parent_item_colon'          => __( 'Parent Item:', 'sage' ),
		'new_item_name'              => __( 'New Internships Languages Name', 'sage' ),
		'add_new_item'               => __( 'Add New Internship Languages', 'sage' ),
		'edit_item'                  => __( 'Edit Internship Languages', 'sage' ),
		'update_item'                => __( 'Update Internship Languages', 'sage' ),
		'view_item'                  => __( 'View Internship Language', 'sage' ),
		'separate_items_with_commas' => __( 'Separate Internships Languages with commas', 'sage' ),
		'add_or_remove_items'        => __( 'Add or remove Internships Languages', 'sage' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'sage' ),
		'popular_items'              => __( 'Popular Internships Languages', 'sage' ),
		'search_items'               => __( 'Search Internships Languages', 'sage' ),
		'not_found'                  => __( 'Not Found', 'sage' ),
		'no_terms'                   => __( 'No Internships Languages', 'sage' ),
		'items_list'                 => __( 'Internships Languages list', 'sage' ),
		'items_list_navigation'      => __( 'Internships Languages list navigation', 'sage' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'internship_lang', array( 'internship' ), $args );

}
add_action( 'init', 'custom_internship_langs', 0 );


function my_frontend_script() {
    wp_enqueue_script( 'my_script', get_template_directory_uri() . '/dist/scripts/my_script.js', array( 'jquery' ), '1.0.0', true );
    wp_localize_script( 'my_script', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

}

add_action( 'wp_enqueue_scripts', 'my_frontend_script' );

add_action( 'wp_ajax_my_delete_post', 'my_delete_post' );
function my_delete_post(){

    $permission = check_ajax_referer( 'my_delete_post_nonce', 'nonce', false );
    if( $permission == false ) {
        echo 'error';
    }
    else {
        wp_delete_post( $_REQUEST['id'] );
        echo 'success';
    }

    die();

}

class GWDayCount {
    private static $script_output;
    function __construct( $args ) {
        extract( wp_parse_args( $args, array(
            'form_id'          => false,
            'start_field_id'   => false,
            'end_field_id'     => false,
            'count_field_id'   => false,
            'include_end_date' => true,
            ) ) );
        $this->form_id        = $form_id;
        $this->start_field_id = $start_field_id;
        $this->end_field_id   = $end_field_id;
        $this->count_field_id = $count_field_id;
        $this->count_adjust   = $include_end_date ? 1 : 0;
        add_filter( "gform_pre_render_{$form_id}", array( &$this, 'load_form_script') );
        add_action( "gform_pre_submission_{$form_id}", array( &$this, 'override_submitted_value') );
    }
    function load_form_script( $form ) {
        // workaround to make this work for < 1.7
        $this->form = $form;
        add_filter( 'gform_init_scripts_footer', array( &$this, 'add_init_script' ) );
        if( self::$script_output )
            return $form;

        self::$script_output = true;
        return $form;
    }
    function add_init_script( $return ) {
        $start_field_format = false;
        $end_field_format = false;
        foreach( $this->form['fields'] as &$field ) {
            if( $field['id'] == $this->start_field_id )
                $start_field_format = $field['dateFormat'] ? $field['dateFormat'] : 'mdy';
            if( $field['id'] == $this->end_field_id )
                $end_field_format = $field['dateFormat'] ? $field['dateFormat'] : 'mdy';
        }
        $script = "new gwdc({
                formId:             {$this->form['id']},
                startFieldId:       {$this->start_field_id},
                startDateFormat:    '$start_field_format',
                endFieldId:         {$this->end_field_id},
                endDateFormat:      '$end_field_format',
                countFieldId:       {$this->count_field_id},
                countAdjust:        {$this->count_adjust}
            });";
        $slug = implode( '_', array( 'gw_display_count', $this->start_field_id, $this->end_field_id, $this->count_field_id ) );
        GFFormDisplay::add_init_script( $this->form['id'], $slug, GFFormDisplay::ON_PAGE_RENDER, $script );
        // remove filter so init script is not output on subsequent forms
        remove_filter( 'gform_init_scripts_footer', array( &$this, 'add_init_script' ) );
        return $return;
    }
    function override_submitted_value( $form ) {
        $start_date = false;
        $end_date = false;
        foreach( $form['fields'] as &$field ) {
            if( $field['id'] == $this->start_field_id )
                $start_date = self::parse_field_date( $field );
            if( $field['id'] == $this->end_field_id )
                $end_date = self::parse_field_date( $field );
        }
        if( $start_date > $end_date ) {
            $day_count = 0;
        } else {
            $diff = $end_date - $start_date;
            $day_count = $diff / ( 60 * 60 * 24 ); // secs * mins * hours
            $day_count = round( $day_count ) + $this->count_adjust;
        }
        $_POST["input_{$this->count_field_id}"] = $day_count;
    }
    static function parse_field_date( $field ) {
        $date_value = rgpost("input_{$field['id']}");
        $date_format = empty( $field['dateFormat'] ) ? 'mdy' : esc_attr( $field['dateFormat'] );
        $date_info = GFCommon::parse_date( $date_value, $date_format );
        if( empty( $date_info ) )
            return false;
        return strtotime( "{$date_info['year']}-{$date_info['month']}-{$date_info['day']}" );
    }
}
# Configuration
new GWDayCount( array(
    'form_id'        => 1,
    'start_field_id' => 7,
    'end_field_id'   => 8,
    'count_field_id' => 16
) );


// Gravity Forms – enable automatic entry deletion

add_action( 'gform_after_submission_5', 'remove_form_entry' );
function remove_form_entry( $entry ) {
    GFAPI::delete_entry( $entry['id'] );
}


// filter the Gravity Forms button type
add_filter("gform_submit_button", "form_submit_button", 10, 2);
$burl = bp_loggedin_user_domain();
function form_submit_button($button, $form){

    return "<button class='mybtn btn btn-primary btn-lg' id='gform_submit_button_{$form["id"]}' ><span>Submit</span></button>";
}



// custom confirmations

add_filter( 'gform_confirmation', 'custom_confirmation', 10, 4 );
function custom_confirmation( $confirmation, $form, $entry, $ajax ) {
    if( $form['id'] == '8' ) {
        $confirmation = array( 'redirect' => bp_loggedin_user_domain() );
    } elseif( $form['id'] == '11' ) {
        $confirmation = array( 'redirect' => bp_loggedin_user_domain() );
    }
    return $confirmation;

    add_filter("gform_confirmation_5", "confirm_change", 10, 4);
function confirm_change($confirmation, $form, $lead, $ajax){

    $url = bp_loggedin_user_domain();

    $confirmation = array('redirect' => $url);
    return $confirmation;
}
}
// filter the Gravity Forms button type
add_filter( 'gform_submit_button_3', 'form_apply_button', 10, 2 );
function form_apply_button( $button, $form ) {
    return "<button class='btn btn-primary btn-lg' id='gform_submit_button_{$form['id']}'><span>Apply</span></button>";
}

add_filter( 'gform_field_value_title', 'populate_title' );
function populate_title( $value ) {
   return get_the_title();
}

add_filter( 'gform_field_value_body', 'populate_body' );
function populate_body( $value ) {
   return get_the_content();
}

add_filter( 'gform_field_value_cat', 'populate_cat' );
function populate_cat( $value ) {

  global $post;
  $term_list = wp_get_post_terms($post->ID, 'internship_cat', array("fields" => "all"));
  foreach($term_list as $term_single) {

  }
return $term_single->name;
}

add_filter( 'gform_field_value_location', 'populate_location' );
function populate_location( $value ) {

  global $post;
  $term_list = wp_get_post_terms($post->ID, 'internship_lc', array("fields" => "all"));
  foreach($term_list as $term_single) {

  }
return $term_single->name;
}


add_filter( 'gform_field_value_start', 'populate_start' );
function populate_start( $value ) {

  global $post;
  $term_list = get_post_meta($post->ID, 'start_date', TRUE);

return $term_list;
}

add_filter( 'gform_field_value_usernamelink', 'populate_usernamelink' );
function populate_usernamelink( $value ) {
   return bp_loggedin_user_domain();
}


add_filter( 'gform_field_value_usernicename', 'populate_usernicename' );
function populate_usernicename( $value ) {
   return bp_core_get_user_displayname( bp_loggedin_user_id() );
}



add_action("gform_after_submission_2", "set_post_content", 10, 2);
function set_post_content($entry, $form){

//getting post
$post = get_post($entry["post_id"]);
$id = get_the_ID();

//changing post content
$post->post_title = " " . $entry[1] . " ";

$post->post_content = "" . $entry[2] . " ";

$internship_cat = " " . $entry[3] . " ";

$internship_cat_field = (int) $internship_cat;

$append = false;

wp_set_object_terms( $id, $internship_cat_field, 'internship_cat', $append );

$internship_lc = " " . $entry[4] . " ";

wp_set_object_terms( $id, $internship_lc, 'internship_lc', false );

$start_date = " " . $entry[5] . " ";
 update_post_meta($id, $start_date, 'start_date');

//updating post
wp_update_post($post);
}


// update the "110" to the ID of your form
add_action('gform_after_submission_3', 'gform_post_child');
function gform_post_child($entry) {

    $parent_post = $entry[3]; // update the "4" to the ID of your hidden field with the Embed Page ID
    $created_post = $entry['post_id'];

    if(!$parent_post || !$created_post)
        return;

    $post = get_post($created_post);
    $post->post_type = 'application'; // if you use a hierarchcial custom post type, change this to the name of that post type here
    $post->post_parent = $parent_post;

    wp_update_post($post);

}


add_filter( 'gform_field_container', 'add_bootstrap_container_class', 10, 6 );
function add_bootstrap_container_class( $field_container, $field, $form, $css_class, $style, $field_content ) {
  $id = $field->id;
  $field_id = is_admin() || empty( $form ) ? "field_{$id}" : 'field_' . $form['id'] . "_$id";
  return '<li id="' . $field_id . '" class="' . $css_class . ' form-group">{FIELD_CONTENT}</li>';
}



add_filter( 'gform_init_scripts_footer', '__return_true' );

// solution to move remaining JS from https://bjornjohansen.no/load-gravity-forms-js-in-footer
add_filter( 'gform_cdata_open', 'wrap_gform_cdata_open' );
function wrap_gform_cdata_open( $content = '' ) {
  $content = 'document.addEventListener( "DOMContentLoaded", function() { ';
  return $content;
}
add_filter( 'gform_cdata_close', 'wrap_gform_cdata_close' );
function wrap_gform_cdata_close( $content = '' ) {
  $content = ' }, false );';
  return $content;
}


function internship_cat_term_list( $args = array() )

{

    $default = array (
        'id'               => get_the_ID(),
        'taxonomy'         => 'internship_cat',
        'before'           => '<h4>',
        'after'            => '</h4>',
    );


    $options = array_merge( $default, $args );
    $terms   = get_the_terms( $options['id'], $options['taxonomy'] );
    $list    = array();
          $count = count($terms); //How many are they?
      if ( $count > 0 ){
    foreach ( $terms as $term )

  //  echo $options['before'];
  echo '<span class="label '. $term->slug .'">'. $term->name .'</span>';
  //  echo $options['after'];
    // . wp_sprintf_l( '%l', $list ) . $options['after'];
  }
  }


  function internship_cat_term_list_in( $args = array() )

  {

      $default = array (
          'id'               => get_the_ID(),
          'taxonomy'         => 'internship_cat',
          'before'           => '<div class="col-md-4"><div class="row">',
          'after'            => '</div></div>',
      );

      $tempalate = get_template_directory_uri();
      $options = array_merge( $default, $args );
      $terms   = get_the_terms( $options['id'], $options['taxonomy'] );
      $list    = array();
            $count = count($terms); //How many are they?
        if ( $count > 0 ){
      foreach ( $terms as $term )

    echo $options['before'];
    echo '<div class="col-sm-2">';
    echo '<p class="m-0-bottom"><img src="'.$tempalate.'/dist/images/ico_location.svg" class=""></p>';
    echo '</div>';
    echo '<div class="col-sm-9 pull-right">';
    echo '<p class="m-0-bottom"><strong>Category</strong></p>';
    echo '<span class="label '. $term->slug .'">'. $term->name .'</span>';
    echo '</div>';
    echo $options['after'];
      // . wp_sprintf_l( '%l', $list ) . $options['after'];
    }
    }

  function internship_lc_term_list( $args = array() )

  {

      $default = array (
          'id'               => get_the_ID(),
          'taxonomy'         => 'internship_lc',
          'before'           => '<h4>',
          'after'            => '</h4>',
      );

      $tempalate = get_template_directory_uri();
      $options = array_merge( $default, $args );
      $terms   = get_the_terms( $options['id'], $options['taxonomy'] );
      $list    = array();
            $count = count($terms); //How many are they?
        if ( $count > 0 ){
      foreach ( $terms as $term )

    //  echo $options['before'];

      echo '<span class="label label-info"><img src="'.$tempalate.'/dist/images/ico_location.svg" class="icon-all">'. $term->name .'</span>';
    //  echo $options['after'];
      // . wp_sprintf_l( '%l', $list ) . $options['after'];
    }
    }

    function internship_lc_term_list_in( $args = array() )

    {

        $default = array (
            'id'               => get_the_ID(),
            'taxonomy'         => 'internship_lc',
            'before'           => '<div class="col-md-4"><div class="row">',
            'after'            => '</div></div>',
        );

        $tempalate = get_template_directory_uri();
        $options = array_merge( $default, $args );
        $terms   = get_the_terms( $options['id'], $options['taxonomy'] );
        $list    = array();
              $count = count($terms); //How many are they?
          if ( $count > 0 ){
        foreach ( $terms as $term )

      echo $options['before'];
      echo '<div class="col-sm-2">';
      echo '<p class="m-0-bottom"><img src="'.$tempalate.'/dist/images/ico_location.svg" class=""></p>';
      echo '</div>';
      echo '<div class="col-sm-9 pull-right">';
      echo '<p class="m-0-bottom"><strong>Location</strong></p>';
      echo '<span class="label '. $term->slug .'">'. $term->name .'</span>';
      echo '</div>';
      echo $options['after'];
        // . wp_sprintf_l( '%l', $list ) . $options['after'];
      }
      }



function internship_lang_term_list( $args = array() )

{

    $default = array (
        'id'               => get_the_ID(),
        'taxonomy'         => 'internship_lang',
        'before'           => '<h4>',
        'after'            => '</h4>',
    );

    $tempalate = get_template_directory_uri();
    $options = array_merge( $default, $args );
    $terms   = get_the_terms( $options['id'], $options['taxonomy'] );
    $list    = array();
          $count = count($terms); //How many are they?
      if ( $count > 0 ){
    foreach ( $terms as $term )

    echo '<span class="label label-info"><img src="'.$tempalate.'/dist/images/ico_language.svg" class="icon-all">'. $term->name .'</span>';

    // . wp_sprintf_l( '%l', $list ) . $options['after'];
  }
  }

  function internship_lang_term_list_in( $args = array() )

  {

      $default = array (
          'id'               => get_the_ID(),
          'taxonomy'         => 'internship_lang',
          'before'           => '<div class="col-md-4"><div class="row">',
          'after'            => '</div></div>',
      );

      $tempalate = get_template_directory_uri();
      $options = array_merge( $default, $args );
      $terms   = get_the_terms( $options['id'], $options['taxonomy'] );
      $list    = array();
      echo $options['before'];
      echo '<div class="col-sm-2">';
      echo '<p class="m-0-bottom"><img src="'.$tempalate.'/dist/images/ico_language.svg" class=""></p>';
      echo '</div>';
      echo '<div class="col-sm-9 pull-right">';
      echo '<p class="m-0-bottom"><strong>Languages</strong></p>';
            $count = count($terms); //How many are they?
        if ( $count > 0 ){
      foreach ( $terms as $term )

    echo '<span class="label '. $term->slug .'">'. $term->name .', </span>';


      // . wp_sprintf_l( '%l', $list ) . $options['after'];
    }
    echo '</div>';
    echo $options['after'];
    }


function my_facetwp_is_main_query( $is_main_query, $query ) {
    if ( isset( $query->query_vars['facetwp'] ) ) {
        $is_main_query = true;
    }
    return $is_main_query;
}
add_filter( 'facetwp_is_main_query', 'my_facetwp_is_main_query', 10, 2 );




add_filter( 'bp_is_profile_cover_image_active', '__return_false' );
add_filter( 'bp_is_groups_cover_image_active', '__return_false' );

// my custom notification menu www.cityflavourmagazine.com

function my_bp_adminbar_notifications_menu() {
global $bp;

if ( !is_user_logged_in() )
    return false;



if ( $notifications = bp_notifications_get_notifications_for_user( $bp->loggedin_user->id ) ) {
  echo '<span>';
  echo count( $notifications );
  echo '</span>';

}

if ( $notifications ) {
    $counter = 0;
    for ( $i = 0; $i < count($notifications); $i++ ) {
        $alt = ( 0 == $counter % 2 ) ? ' class="btn btn-secondary btn-sm"' : '';

        echo '<button type="button" $alt  data-href='.$bp->loggedin_user->domain.'>';

        echo ''.$notifications[$i].'';
        echo '</button>';

         $counter++;
    }
} else {

  echo '<button type="button" class="btn btn-secondary btn-sm" data-href='.$bp->loggedin_user->domain.'>';
  echo ''._e( 'No notifications', 'buddypress' ).'';
  echo '</button>';
}
}
