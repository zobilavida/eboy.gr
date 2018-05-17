<?php
/**
 * Sage includes
 *
 * The $demetrios_3_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/eboy/demetrios_3/pull/1042
 */
$demetrios_3_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/wrapper.php',   // Theme wrapper class
  'lib/customizer.php', // Theme customizer
  'plugins/facetwp/index.php', // Theme extends
  'bs4navwalker.php',
  'bs4navwalker_right.php',
  'wp-bootstrap-navwalker.php',
  'wp-bootstrap-navwalker-top.php',
  'custom-nav-walker.php',
  'recalculate-acf-locations.php',
  'create-license.php'
];

$api_params = array(
'slm_action' => 'slm_check',
'secret_key' => '5afd5ed9ba1853.54896313',
'license_key' => 'KEYTOCHECK',
);
// Send query to the license manager server
$response = wp_remote_get(add_query_arg($api_params, 'https://eboy.gr/wp'), array('timeout' => 20, 'sslverify' => false));

foreach ($demetrios_3_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'demetrios_3'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);



// Add svg & swf support
function cc_mime_types( $mimes ){
    $mimes['svg'] = 'image/svg+xml';
    $mimes['swf']  = 'application/x-shockwave-flash';

    return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );
//enable logo uploading via the customize theme page

function themeslug_theme_customizer( $wp_customize ) {
    $wp_customize->add_section( 'themeslug_logo_section' , array(
    'title'       => __( 'Logo', 'themeslug' ),
    'priority'    => 30,
    'description' => 'Upload a logo to replace the default site name and description     in the header',
) );
$wp_customize->add_setting( 'themeslug_logo' );
$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize,     'themeslug_logo', array(
    'label'    => __( 'Logo', 'themeslug' ),
    'section'  => 'themeslug_logo_section',
    'settings' => 'themeslug_logo',
    'extensions' => array( 'jpg', 'jpeg', 'gif', 'png', 'svg' ),
) ) );
}

add_filter( 'searchwp_short_circuit', '__return_true' );

add_action ('customize_register', 'themeslug_theme_customizer');

function ravs_slider_image_sizes( $image_sizes ){

   // size for slider
   $slider_image_sizes = array( 'carousel-size-1', 'carousel-size-2','carousel-size-3', 'carousel-size-4'  );

   // for ex: $slider_image_sizes = array( 'thumbnail', 'medium' );

   // instead of unset sizes, return your custom size for slider image
   if( isset($_REQUEST['post_id']) && 'sliders' === get_post_type( $_REQUEST['post_id'] ) )
       return $slider_image_sizes;

   return $image_sizes;
}

add_filter( 'intermediate_image_sizes', 'ravs_slider_image_sizes', 999 );


function itsg_create_sitemap() {

    $postsForSitemap = get_posts(array(
        'numberposts' => -1,
        'orderby' => 'modified',
        'post_type'  => array( 'post', 'page', 'product' ),
        'order'    => 'DESC'
    ));

    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    foreach( $postsForSitemap as $post ) {
        setup_postdata( $post );

        $postdate = explode( " ", $post->post_modified );

        $sitemap .= '<url>'.
          '<loc>' . get_permalink( $post->ID ) . '</loc>' .
          '<lastmod>' . $postdate[0] . '</lastmod>' .
          '<changefreq>monthly</changefreq>' .
         '</url>';
      }

    $sitemap .= '</urlset>';

    $fp = fopen( ABSPATH . 'sitemap.xml', 'w' );

    fwrite( $fp, $sitemap );
    fclose( $fp );
}

add_action( 'publish_post', 'itsg_create_sitemap' );
add_action( 'publish_page', 'itsg_create_sitemap' );
add_action( 'save_post_my_post_type', 'itsg_create_sitemap' );


/**
 * Optimize WooCommerce Scripts
 * Remove WooCommerce Generator tag, styles, and scripts from non WooCommerce pages.
 */
add_action( 'wp_enqueue_scripts', 'child_manage_woocommerce_styles', 99 );

function child_manage_woocommerce_styles() {
	//remove generator meta tag
	remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );

	//first check that woo exists to prevent fatal errors
	if ( function_exists( 'is_woocommerce' ) ) {
		//dequeue scripts and styles
		if (  ! is_cart() && ! is_checkout() ) {
			wp_dequeue_style( 'woocommerce_frontend_styles' );
			wp_dequeue_style( 'woocommerce_fancybox_styles' );
			wp_dequeue_style( 'woocommerce_chosen_styles' );
			wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
      wp_dequeue_style('woocommerce-smallscreen');
      wp_dequeue_style('woocommerce-layout');
      wp_dequeue_style('woocommerce-general');
			wp_dequeue_script( 'wc_price_slider' );
			wp_dequeue_script( 'wc-single-product' );
			wp_dequeue_script( 'wc-add-to-cart' );
			wp_dequeue_script( 'wc-cart-fragments' );
			wp_dequeue_script( 'wc-checkout' );
			wp_dequeue_script( 'wc-add-to-cart-variation' );
			wp_dequeue_script( 'wc-single-product' );
			wp_dequeue_script( 'wc-cart' );
			wp_dequeue_script( 'wc-chosen' );
			wp_dequeue_script( 'woocommerce' );
			wp_dequeue_script( 'prettyPhoto' );
			wp_dequeue_script( 'prettyPhoto-init' );
			wp_dequeue_script( 'jquery-blockui' );
			wp_dequeue_script( 'jquery-placeholder' );
			wp_dequeue_script( 'fancybox' );
			wp_dequeue_script( 'jqueryui' );
    //  wp_deregister_script( 'jquery' );
//     wp_deregister_script( 'js-cookie' );
		}
	}

}

function vc_remove_wp_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'vc_remove_wp_ver_css_js', 9999 );
add_filter( 'script_loader_src', 'vc_remove_wp_ver_css_js', 9999 );

if ( ! function_exists('custom_sliders_post_type') ) {

// Register Custom Post Type
function custom_sliders_post_type() {

	$labels = array(
		'name'                  => _x( 'Sliders', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Slider', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Sliders', 'text_domain' ),
		'name_admin_bar'        => __( 'Slider', 'text_domain' ),
		'archives'              => __( 'Sliders Archives', 'text_domain' ),
		'attributes'            => __( 'Sliders Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Slider:', 'text_domain' ),
		'all_items'             => __( 'All Sliders', 'text_domain' ),
		'add_new_item'          => __( 'Add New Slider', 'text_domain' ),
		'add_new'               => __( 'Add New Slider', 'text_domain' ),
		'new_item'              => __( 'New Slider', 'text_domain' ),
		'edit_item'             => __( 'Edit Slider', 'text_domain' ),
		'update_item'           => __( 'Update Slider', 'text_domain' ),
		'view_item'             => __( 'View Slider', 'text_domain' ),
		'view_items'            => __( 'View Sliders', 'text_domain' ),
		'search_items'          => __( 'Search Slider', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into Slider', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Slider', 'text_domain' ),
		'items_list'            => __( 'Sliders list', 'text_domain' ),
		'items_list_navigation' => __( 'Sliders list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter Sliders list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Slider', 'text_domain' ),
		'description'           => __( 'Sliders Directory Listing', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'taxonomies'            => array( 'store_category' ),
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
		'capability_type'       => 'post',
	);
	register_post_type( 'sliders', $args );

}
add_action( 'init', 'custom_sliders_post_type', 0 );

}



if ( ! function_exists('custom_stores_post_type') ) {

// Register Custom Post Type
function custom_stores_post_type() {

	$labels = array(
		'name'                  => _x( 'Stores', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Store', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Stores', 'text_domain' ),
		'name_admin_bar'        => __( 'Store', 'text_domain' ),
		'archives'              => __( 'Stores Archives', 'text_domain' ),
		'attributes'            => __( 'Stores Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Store:', 'text_domain' ),
		'all_items'             => __( 'All Stores', 'text_domain' ),
		'add_new_item'          => __( 'Add New Store', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Store', 'text_domain' ),
		'edit_item'             => __( 'Edit Store', 'text_domain' ),
		'update_item'           => __( 'Update Store', 'text_domain' ),
		'view_item'             => __( 'View Store', 'text_domain' ),
		'view_items'            => __( 'View Stores', 'text_domain' ),
		'search_items'          => __( 'Search Store', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into Store', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Store', 'text_domain' ),
		'items_list'            => __( 'Stores list', 'text_domain' ),
		'items_list_navigation' => __( 'Stores list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter Stores list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Store', 'text_domain' ),
		'description'           => __( 'Stores Directory Listing', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'taxonomies'            => array( 'store_category' ),
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
		'capability_type'       => 'post',
	);
	register_post_type( 'stores', $args );

}
add_action( 'init', 'custom_stores_post_type', 0 );

}

// Register Custom Taxonomy
function custom_store_cats() {

	$labels = array(
		'name'                       => _x( 'Store Category', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Stores Category', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Stores Categories', 'text_domain' ),
		'all_items'                  => __( 'All Stores Categories', 'text_domain' ),
		'parent_item'                => __( 'Parent Stores Categories', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New Stores Categories Name', 'text_domain' ),
		'add_new_item'               => __( 'Add New Store Category', 'text_domain' ),
		'edit_item'                  => __( 'Edit Store Category', 'text_domain' ),
		'update_item'                => __( 'Update Store Category', 'text_domain' ),
		'view_item'                  => __( 'View Store Category', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate Stores Categories with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove Stores Categories', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular Stores Categories', 'text_domain' ),
		'search_items'               => __( 'Search Stores Categories', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
		'no_terms'                   => __( 'No Stores Categories', 'text_domain' ),
		'items_list'                 => __( 'Stores Categories list', 'text_domain' ),
		'items_list_navigation'      => __( 'Stores Categories list navigation', 'text_domain' ),
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
	register_taxonomy( 'store_cat', array( 'stores' ), $args );

}
add_action( 'init', 'custom_store_cats', 0 );



// Register Custom Taxonomy
function custom_store_locs() {

	$labels = array(
		'name'                       => _x( 'Store location', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Stores location', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Stores locations', 'text_domain' ),
		'all_items'                  => __( 'All Stores locations', 'text_domain' ),
		'parent_item'                => __( 'Parent Stores locations', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New Stores locations Name', 'text_domain' ),
		'add_new_item'               => __( 'Add New Store location', 'text_domain' ),
		'edit_item'                  => __( 'Edit Store location', 'text_domain' ),
		'update_item'                => __( 'Update Store location', 'text_domain' ),
		'view_item'                  => __( 'View Store location', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate Stores locations with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove Stores locations', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular Stores locations', 'text_domain' ),
		'search_items'               => __( 'Search Stores locations', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
		'no_terms'                   => __( 'No Stores locations', 'text_domain' ),
		'items_list'                 => __( 'Stores locations list', 'text_domain' ),
		'items_list_navigation'      => __( 'Stores locations list navigation', 'text_domain' ),
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
	register_taxonomy( 'store_loc', array( 'stores' ), $args );

}
add_action( 'init', 'custom_store_locs', 0 );


// Admin Columns
function stores_columns($columns)
{
    $columns = array(
        'cb'         => '<input type="checkbox" />',
        'title'     => 'Store Name',
        'country'     => 'Country',
				'state'     => 'State',
				'city'     => 'City',
				'street'     => 'Street Address',
				'zip'     => 'Zip',
        'date'        =>    'Date',
    );
    return $columns;
}

function stores_columns_fields($column)
{
    global $post;

    if ($column == 'country') {
        echo get_field( "country", $post->ID );
    }
    else {
         echo '';
    }
		if ($column == 'state') {
        echo get_field( "state", $post->ID );
    }
    else {
         echo '';
    }
		if ($column == 'street') {
        echo get_field( "street_address", $post->ID );
    }
    else {
         echo '';
    }
		if ($column == 'zip') {
        echo get_field( "zip", $post->ID );
    }
    else {
         echo '';
    }
		if ($column == 'city') {
        echo get_field( "city", $post->ID );
    }
    else {
         echo '';
    }
}

add_action("manage_stores_posts_custom_column", "stores_columns_fields");
add_filter("manage_stores_posts_columns", "stores_columns");

//// BREADCRUMB START ////
 function the_breadcrumb() {

  $showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
  $delimiter = '&#8728;'; // delimiter between crumbs
  $home = 'Home'; // text for the 'Home' link
  $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
  $before = '<span class="current">'; // tag before the current crumb
  $after = '</span>'; // tag after the current crumb

  global $post;
  $homeLink = get_bloginfo('url');

  if (is_home() || is_front_page()) {

    if ($showOnHome == 1) echo '<span class="align-text-bottom"><a href="' . $homeLink . '">' . $home . '</a></span>';

  } else {

    echo '<span class="align-text-bottom"><a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';

    if ( is_category() ) {
      $thisCat = get_category(get_query_var('cat'), false);
      if ($thisCat->parent != 0) echo get_category_parents($thisCat->parent, TRUE, ' ' . $delimiter . ' ');
      echo $before . 'Archive by category "' . single_cat_title('', false) . '"' . $after;

    } elseif ( is_search() ) {
      echo $before . 'Search results for "' . get_search_query() . '"' . $after;

    } elseif ( is_day() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
      echo $before . get_the_time('d') . $after;

    } elseif ( is_month() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo $before . get_the_time('F') . $after;

    } elseif ( is_year() ) {
      echo $before . get_the_time('Y') . $after;

    } elseif ( is_single() && !is_attachment() ) {
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>';
        if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
      } else {
        $cat = get_the_category(); $cat = $cat[0];
        $cats = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
        if ($showCurrent == 0) $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
        echo $cats;
        if ($showCurrent == 1) echo $before . get_the_title() . $after;
      }

    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
      $post_type = get_post_type_object(get_post_type());
      echo $before . $post_type->labels->singular_name . $after;

    } elseif ( is_attachment() ) {
      $parent = get_post($post->post_parent);
      $cat = get_the_category($parent->ID); $cat = $cat[0];
      echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
      echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>';
      if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;

    } elseif ( is_page() && !$post->post_parent ) {
      if ($showCurrent == 1) echo $before . get_the_title() . $after;

    } elseif ( is_page() && $post->post_parent ) {
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ($parent_id) {
        $page = get_page($parent_id);
        $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
        $parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse($breadcrumbs);
      for ($i = 0; $i < count($breadcrumbs); $i++) {
        echo $breadcrumbs[$i];
        if ($i != count($breadcrumbs)-1) echo ' ' . $delimiter . ' ';
      }
      if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;

    } elseif ( is_tag() ) {
      echo $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;

    } elseif ( is_author() ) {
       global $author;
      $userdata = get_userdata($author);
      echo $before . 'Articles posted by ' . $userdata->display_name . $after;

    } elseif ( is_404() ) {
      echo $before . 'Error 404' . $after;
    }

    if ( get_query_var('paged') ) {
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
      echo __('Page') . ' ' . get_query_var('paged');
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
    }

    echo '</span>';

  }
} // end the_breadcrumb()

//// BREADCRUMB END ////
add_filter( 'facetwp_pager_html', function( $output, $params ) {
    $output = '<nav aria-label="Resources Pagination"><ul class="pagination mt-1 justify-content-center">';
    $page = $params['page'];
    $i = 1;
    $total_pages = $params['total_pages'];
    $limit = ($total_pages >= 5) ? 3 : $total_pages;
    $prev_disabled = ($params['page'] <= 1) ? 'disabled' : '';
    $output .= '<li class="page-item ' . $prev_disabled . '"><a class="facetwp-page page-link" data-page="' . ($page - 1) . '">Prev</a></li>';
    $loop = ($limit) ? $limit : $total_pages;
    while($i <= $loop) {
      $active = ($i == $page) ? 'active' : '';
      $output .= '<li class="page-item ' . $active . '"><a class="facetwp-page page-link" data-page="' . $i . '">' . $i . '</a></li>';
      $i++;
    }
    if($limit && $total_pages > '3') {
      $output .= ($page > $limit && $page != ($total_pages - 1) && $page <= ($limit + 1)) ? '<li class="page-item active"><a class="facetwp-page page-link" data-page="' . $page . '">' . $page . '</a></li>' : '';
      $output .= '<li class="page-item disabled"><a class="facetwp-page page-link">...</a></li>';
      $output .= ($page > $limit && $page != ($total_pages - 1) && $page > ($limit + 1)) ? '<li class="page-item active"><a class="facetwp-page page-link" data-page="' . $page . '">' . $page . '</a></li>' : '';
      $output .= ($page > $limit && $page != ($total_pages - 1) && $page != ($total_pages - 2) && $page > ($limit + 1)) ? '<li class="page-item disabled"><a class="facetwp-page page-link">...</a></li>' : '';
      $active = ($page == ($total_pages - 1)) ? 'active' : '';
      $output .= '<li class="page-item ' . $active . '"><a class="facetwp-page page-link" data-page="' . ($total_pages - 1) .'">' . ($total_pages - 1) .'</a></li>';
    }
    $next_disabled = ($page >= $total_pages) ? 'disabled' : '';
    $output .= '<li class="page-item ' . $next_disabled . '"><a class="facetwp-page page-link" data-page="' . ($page + 1) . '">Next</a></li>';
    $output .= '</ul></nav>';
    return $output;
}, 10, 2 );



// Google API Key
function my_acf_google_map_api( $api ){

	$api['key'] = 'AIzaSyAY55sLjGdZyuE5fX9gIH0NegqSeB24LEU';

	return $api;

}

add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');

function custom_header(){

      // below content only show when page id is 12
      $home_logo = get_field( "logo_home" );
      $sandwitch_small = '<img class="ico" src=" ' .get_template_directory_uri() .'/dist/images/sc_sandwitch_small.svg">';

?>

<nav class="navbar navbar-expand-sm sticky navbar-light bg-white px-2">
    <div class="container">
      <div class="d-flex flex-row justify-content-between align-items-center w-100 h-100">

<div class="d-flex h-100"><a class="" href="<?= esc_url(home_url('/')); ?>">
  <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
</a>
</div>

<div class="d-flex d-sm-none justify-content-end w-100 h-100 header-right p-1">

  <?php dynamic_sidebar('sidebar-header-right'); ?>

</div>
<div class="d-block d-sm-none p-0">
  <button class="hamburger small-hamburger hamburger--vortex btn btn-light pl-4 pt-2" type="button">
  <span class="hamburger-box">
    <span class="hamburger-inner"></span>
  </span>
</button>
</div>

<div class="p-1 d-none d-lg-block d-lg-none w-75" id="navbar1">

  <?php
  wp_nav_menu([
    'menu'            => 'top',
    'theme_location'  => 'top',
   'items_wrap' => '<ul id="fals" class="navbar-nav float-right">%3$s',
    'container'       => false,
    'container_id'    => '',
    'container_class' => '',
    'menu_id'         => fals,
    'menu_class'      => 'navbar-nav',
    'depth'           => 2,
    'fallback_cb'     => 'bs4Navwalker::fallback',
    'walker'          => new bs4Navwalker()
  ]);
  ?>

  <button class="hamburger big-hamburger hamburger--vortex btn btn-light pl-4 pt-2" type="button">
  <span class="hamburger-box">
    <span class="hamburger-inner"></span>
  </span>
</button>

</ul>
</div>






</div>
  </div>

</nav>
<div class="side-panel side-panel-top d-sm-none " id="slider-top" >
<?php
       wp_nav_menu( array(
           'theme_location'    => 'top',
           'depth'             => 2,
           'container'         => 'div',
           'container_class'   => 'collapse navbar-collapse',
           'container_id'      => 'bs-example-navbar-collapse-1',
           'menu_class'        => 'nav navbar-nav',
           'fallback_cb'       => 'WP_Bootstrap_Navwalker_top::fallback',
           'walker'            => new WP_Bootstrap_Navwalker_top()
  ) );
       ?>

</ul>
</div>



  <?php

}
add_action('demetrios_custom_header', 'custom_header');


function demetrios_side_menu (){ ?>
<div id="slider" class="side-panel side-panel-right">
  <?php
         wp_nav_menu( array(
             'theme_location'    => 'side_navigation',
             'depth'             => 2,
             'container'         => 'div',
             'container_class'   => 'collapse navbar-collapse',
             'container_id'      => 'bs-example-navbar-collapse-1',
             'menu_class'        => 'nav navbar-nav',
             'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
             'walker'            => new WP_Bootstrap_Navwalker()
 		) );
         ?>
  <?php dynamic_sidebar('main-next'); ?>

</div>
<?php }
add_action('demetrios_custom_side_menu', 'demetrios_side_menu', 10 );

####################################################
#    VIDEO
####################################################

function demetrios_front_video(){

  $video = get_field( "video_file" );
  $video_background_text_1 = get_field( "video_background_text_1" );
  $video_background_text_2 = get_field( "video_background_text_2" );
  $video_link = get_field( "video_link" );
  $video_bg_image = get_field( "video_bg_image" );
?>
<section class="video h-25">


  <div class="container h-100 d-flex video-background-image" data-background="<?php //echo $video_bg_image; ?>">
    <div class="row m-auto">
      <div class="col-12 text-center">
    <div class="display-1-w pb-4"><?php echo $video_background_text_1; ?></div>

    <?php if( $video_background_text_2 ) { ?><a href="<?php echo $video_link; ?>" class="btn btn-outline-light btn-lg" > <?php echo $video_background_text_2; ?> </a><?php }?>
    </div>
    </div>

        <video id="video-background" preload="" muted="" autoplay="" loop="">
          <source src="<?php echo $video; ?>" type="video/mp4">
        </video>

  </div>


</section>


<?php
}

add_action('demetrios_custom_video', 'demetrios_front_video');

    ####################################################
#    C A R O U S E L
    ####################################################

function demetrios_front_carousel(){
  ?>
  <section class="top-carousel">
    <div id="carousel" class="carousel slide carousel-fade" data-ride="carousel" data-interval="6000">
      <div class="carousel-inner" role="listbox">
          <?php
          // the query
          $$wpb_rest_query = new WP_Query(array('post_type'=>'sliders', 'post_status'=>'publish', 'offset' => 0, 'posts_per_page'=>1)); ?>
          <?php if ( $$wpb_rest_query->have_posts() ) : ?>
          <!-- the loop -->
          <?php while ( $$wpb_rest_query->have_posts() ) : $$wpb_rest_query->the_post(); ?>
          <div class="carousel-item active h-100">
          <picture>
         <source srcset="<?php the_post_thumbnail_url( 'carousel-size-1' ); ?>" media="(min-width: 1400px)">
         <source srcset="<?php the_post_thumbnail_url( 'carousel-size-2' ); ?>" media="(min-width: 769px)">
          <source srcset="<?php the_post_thumbnail_url( 'carousel-size-3' ); ?>" media="(min-width: 577px)">
         <img srcset="<?php the_post_thumbnail_url( 'carousel-size-4' ); ?>" alt="Demetrios Wedding" class="d-block img-fluid">
        </picture>
        <div class="carousel-caption">
        <div>
         <h2><?php the_title(); ?></h2>
         <p>We meticously build each site to get results</p>
         <span class="btn btn-sm btn-outline-secondary">Learn More</span>
        </div>
        </div>
        </div>
        <?php endwhile; ?>
        <!-- end of the loop -->
        <?php wp_reset_postdata(); ?>
          <?php else : ?>
              <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
          <?php endif; ?>
  <?php
  // the query
  $$wpb_rest_query = new WP_Query(array('post_type'=>'sliders', 'post_status'=>'publish', 'offset' => -1)); ?>
  <?php if ( $$wpb_rest_query->have_posts() ) : ?>
  <!-- the loop -->
  <?php while ( $$wpb_rest_query->have_posts() ) : $$wpb_rest_query->the_post(); ?>
  <div class="carousel-item h-100">
<picture>
<source srcset="<?php the_post_thumbnail_url( 'carousel-size-1' ); ?>" media="(min-width: 1400px)">
<source srcset="<?php the_post_thumbnail_url( 'carousel-size-2' ); ?>" media="(min-width: 769px)">
<source srcset="<?php the_post_thumbnail_url( 'carousel-size-3' ); ?>" media="(min-width: 577px)">
 <img srcset="<?php the_post_thumbnail_url( 'carousel-size-4' ); ?>" alt="responsive image" class="d-block img-fluid">
</picture>
<div class="carousel-caption">
<div>
 <h2><?php the_title(); ?></h2>
 <p>We meticously build each site to get results</p>
 <span class="btn btn-sm btn-outline-secondary">Learn More</span>
</div>
</div>
</div>
<?php endwhile; ?>
<!-- end of the loop -->
<?php wp_reset_postdata(); ?>
<?php else : ?>
      <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
  <?php endif; ?>
  </div>
  </div>
  </section>
  <?php
  }
add_action( 'demetrios', 'demetrios_front_carousel', 10 );


function demetrios_front_fasa_book(){
    $fasa_1 = get_field( "fasa_1" );
    $fasa_2 = get_field( "fasa_2" );
    $button_1_text = get_field( "button_1_text" );
  ?>
  <section class="fasa_book p-5">
<div class="container-fluid fasa_book">
  <div class="row">
    <div class="container">
      <div class="row">
        <div class="col-lg-7">
          <h4><?php echo $fasa_1; ?></h4>
          <h5><?php echo $fasa_2; ?></h5>
          </div>
          <div class="col-lg-5">
            <div class="btn btn-border-w btn-round">
            <a href="#">  <?php echo $button_1_text; ?></a>
            </div>
            </div>
          </div>
      </div>
    </div>
</div>


  </section>
  <?php

}
add_action( 'demetrios_fasa', 'demetrios_front_fasa_book', 10 );

function parallax_1(){
  $parallax_1 = get_field( "parallax_1" );
  $parallax_1_text_1 = get_field( "parallax_1_text_1" );
  $parallax_1_text_2 = get_field( "parallax_1_text_2" );
  $parallax_1_button = get_field( "parallax_1_button" );
  $parallax_1_button_url = get_field( "parallax_1_button_url" );


  if( $parallax_1 ) {

?>
<section class="module bg-dark-90 parallax-bg h-25" data-background="<?php echo $parallax_1; ?>" style="background-position: 50% 15%;">

    <div class="titan-caption">
      <div class="caption-content">
        <div class="font-alt mb-30"><h2><?php echo $parallax_1_text_1; ?></h2></div>
        <div class="pb-3"><?php echo $parallax_1_text_2; ?></div><a class="section-scroll btn btn-border-w btn-round" href="<?php echo $parallax_1_button_url; ?>"><?php echo $parallax_1_button; ?></a>
      </div>
    </div>

</section>

<?php
} else { echo "Niente parallax_1";}

}
add_action( 'custom_parallax_1', 'parallax_1', 15 );



function half_1(){
  $image = get_field( "half_1" );
  $image_object = get_field('half_1');
  $image_size_0 = 'img-half-xl';
  $image_size_1 = 'img-half-lg';
  $image_size_2 = 'img-half-md';
  $image_size_3 = 'img-half-sm';
  $image_size_4 = 'img-half-xs';
  $image_url_0 = $image_object['sizes'][$image_size_0];
  $image_url_1 = $image_object['sizes'][$image_size_1];
  $image_url_2 = $image_object['sizes'][$image_size_2];
  $image_url_3 = $image_object['sizes'][$image_size_3];
  $image_url_4 = $image_object['sizes'][$image_size_4];
  $section_half_1_header = get_field( "section_half_1_header" );
  $half_1_text_1 = get_field( "half_1_text_1" );
  $half_1_text_2 = get_field( "half_1_text_2" );
  $half_1_text_3 = get_field( "half_1_text_3" );
  $half_1_button = get_field( "half_1_button" );
  $half_1_button_url = get_field( "half_1_button_url" );
  $calendar = get_field( "calendar" );

  if( $image ) {

?>
<section class="module pt-1">
  <div class="container-fluid h-100 pl-0">
    <div class="row justify-content-center">
      <div class="col-12 text-center">
        <div class="display-3-b-it p-1"><?php echo $section_half_1_header; ?></div>
      </div>
    </div>
  <div class="row position-relative m-0">
    <div class="col-12 col-md-6 side-image p-0" >

      <picture>
        <source srcset="<?php echo $image_url_0; ?>" media="(min-width: 1400px)">
      <source srcset="<?php echo $image_url_1;  ?>" media="(min-width: 769px)">
      <source srcset="<?php echo $image_url_2; ?>" media="(min-width: 577px)">
      <img srcset="<?php echo $image_url_3;?>" alt="responsive image" class="d-block img-fluid">
      </picture>
    </div>
    <div class="col-xs-12 col-md-6 col-md-offset-6 side-image-text">
      <div class="row h-100">
        <div class="col-sm-12 align-self-center pl-5">

  <div class="display-1-g"><?php echo $half_1_text_1; ?></div>
  <div class="display-1-b"><?php echo $half_1_text_2; ?></div>
  <?php if( $calendar ) {?> <div class="simply-countdown-inline"> </div><?}?>
<div class="display-2-b pt-2"><?php echo $half_1_text_3; ?></div>

  <a class="btn btn-outline-secondary" href="<?php echo $half_1_button_url; ?>"><?php echo $half_1_button; ?></a>

        </div>
      </div>
    </div>
  </div>
  </div>
</section>

<?php
} else { echo "Xωρις half_1";}

}
add_action( 'custom_half_1', 'half_1', 15 );


function parallax_2(){
  $section_parallax_2_header = get_field( "section_parallax_2_header" );
  $parallax_2 = get_field( "parallax_2" );
  $parallax_2_text_1 = get_field( "parallax_2_text_1" );
  $parallax_2_text_2 = get_field( "parallax_2_text_2" );
  $parallax_2_button = get_field( "parallax_2_button" );
  $parallax_2_button_url = get_field( "parallax_2_button_url" );


  if( $parallax_2 ) {

?>
<div class="container-fluid bg-white">
  <div class="row justify-content-center pb-1">
    <div class="col-12 text-center">
        <div class="display-3-b-it p-1"><?php echo $section_parallax_2_header; ?></div>
    </div>
  </div>
</div>
<section class="module bg-dark-60 parallax-bg h-30" data-background="<?php echo $parallax_2; ?>" style="background-position: 50% 15%;">

  <div class="container h-100">
      <div class="row h-100 align-items-end pb-3">
          <div class="col-lg-2 col-sm-1 col-1">

          </div>
          <div class="col-lg-8 col-sm-10 col-10 text-center pb-5 malakia-koutaki">
            <div class="display-2-g pt-2"><?php echo $parallax_2_text_1; ?></div>
            <div class="display-3-b pt-2 pb-4"><?php echo $parallax_2_text_2; ?></div>

              <a class="btn btn-outline-secondary" href="<?php echo $parallax_2_button_url; ?>"><?php echo $parallax_2_button; ?></a>
          </div>
          <div class="col-lg-2 col-sm-1 col-1">

          </div>
      </div>
  </div>
</section>
<?php
} else { echo "χωρος parallax_2";}

}
add_action( 'custom_parallax_2', 'parallax_2', 15 );


function external_1(){
  $section_external_header = get_field( "section_external_header" );
  //$external_img_1 = get_field( "external_img_1" );
  $external_1_button = get_field( "external_butt_1" );
  $external_1_button_url = get_field( "external_butt_1_url" );
//  $external_img_2 = get_field( "external_img_2" );
  $external_2_button = get_field( "external_butt_2" );
  $external_2_button_url = get_field( "external_butt_2_url" );
  //$external_img_3 = get_field( "external_img_3" );
  $external_3_button = get_field( "external_butt_3" );
  $external_3_button_url = get_field( "external_butt_3_url" );

  $image_object_1 = get_field('external_img_1');
  $image_object_2 = get_field('external_img_2');
  $image_object_3 = get_field('external_img_3');
  $image_size_0 = 'img-half-xl';
  $image_size_1 = 'img-half-lg';
  $image_size_2 = 'img-half-md';
  $image_size_3 = 'img-half-sm';
  $image_size_4 = 'img-half-xs';
  $image_url_1_0 = $image_object_1['sizes'][$image_size_0];
  $image_url_1_1 = $image_object_1['sizes'][$image_size_1];
  $image_url_1_2 = $image_object_1['sizes'][$image_size_2];
  $image_url_1_3 = $image_object_1['sizes'][$image_size_3];
  $image_url_1_4 = $image_object_1['sizes'][$image_size_4];

  $image_url_2_0 = $image_object_2['sizes'][$image_size_0];
  $image_url_2_1 = $image_object_2['sizes'][$image_size_1];
  $image_url_2_2 = $image_object_2['sizes'][$image_size_2];
  $image_url_2_3 = $image_object_2['sizes'][$image_size_3];
  $image_url_2_4 = $image_object_2['sizes'][$image_size_4];

  $image_url_3_0 = $image_object_3['sizes'][$image_size_0];
  $image_url_3_1 = $image_object_3['sizes'][$image_size_1];
  $image_url_3_2 = $image_object_3['sizes'][$image_size_2];
  $image_url_3_3 = $image_object_3['sizes'][$image_size_3];
  $image_url_3_4 = $image_object_3['sizes'][$image_size_4];

  if( $section_external_header ) {

?>
<section class="module pt-1">
  <div class="container-fluid h-100">
    <div class="row justify-content-center">
      <div class="col-12 text-center">
        <div class="display-3-b-it p-1"><?php echo $section_external_header; ?></div>
      </div>
    </div>
    <div class="row">
      <div class="col-4 text-center p-0">
        <a href="<?php echo $external_1_button_url; ?>">
          <picture>
            <source srcset="<?php echo $image_url_1_0; ?>" media="(min-width: 1400px)">
          <source srcset="<?php echo $image_url_1_1;  ?>" media="(min-width: 769px)">
          <source srcset="<?php echo $image_url_1_2; ?>" media="(min-width: 577px)">
          <img srcset="<?php echo $image_url_1_3;?>" alt="responsive image" class="d-block img-fluid">
          </picture>
        </a>
        <a class="display-2-g mt-3" href="<?php echo $external_1_button_url; ?>"><?php echo $external_1_button; ?></a>
      </div>
      <div class="col-4 text-center p-0">
        <a href="<?php echo $external_2_button_url; ?>">
          <picture>
            <source srcset="<?php echo $image_url_2_0; ?>" media="(min-width: 1400px)">
          <source srcset="<?php echo $image_url_2_1;  ?>" media="(min-width: 769px)">
          <source srcset="<?php echo $image_url_2_2; ?>" media="(min-width: 577px)">
          <img srcset="<?php echo $image_url_2_3;?>" alt="responsive image" class="d-block img-fluid">
          </picture>
        </a>
        <a class="display-2-g mt-3" href="<?php echo $external_2_button_url; ?>"><?php echo $external_2_button; ?></a>
      </div>
      <div class="col-4 text-center p-0">
        <a href="<?php echo $external_3_button_url; ?>">
          <picture>
            <source srcset="<?php echo $image_url_3_0; ?>" media="(min-width: 1400px)">
          <source srcset="<?php echo $image_url_3_1;  ?>" media="(min-width: 769px)">
          <source srcset="<?php echo $image_url_3_2; ?>" media="(min-width: 577px)">
          <img srcset="<?php echo $image_url_3_3;?>" alt="responsive image" class="d-block img-fluid">
          </picture>
        </a>
        <a class="display-2-g mt-3" href="<?php echo $external_3_button_url; ?>"><?php echo $external_3_button; ?></a>
      </div>
    </div>
  </div>
</section>

<?php
} else { echo "";}

}
add_action( 'custom_external', 'external_1', 15 );

function parallax_3(){
  $section_parallax_3_header = get_field( "section_parallax_3_header" );
  $parallax_3 = get_field( "parallax_3" );
  $parallax_3_text_1 = get_field( "parallax_3_text_1" );
  $parallax_3_text_2 = get_field( "parallax_3_text_2" );
  $parallax_3_button = get_field( "parallax_3_button" );
  $parallax_3_button_url = get_field( "parallax_3_button_url" );


  if( $parallax_3 ) {

?>
<div class="container-fluid bg-white">
  <div class="row justify-content-center pt-1">
    <div class="col-12 text-center">
      <h3><?php echo $section_parallax_3_header; ?></h3>
    </div>
  </div>
</div>
<section class="module bg-dark-60 parallax-bg h-25" data-background="<?php echo $parallax_3; ?>" style="background-position: 50% 15%;">

  <div class="container h-100">
      <div class="row h-100 align-items-end pb-3">
          <div class="col-lg-2 col-sm-1 col-1">

          </div>
          <div class="col-lg-8 col-sm-10 col-10 text-center pb-5 malakia-koutaki">
            <div class="display-2-g pt-2"><?php echo $parallax_3_text_1; ?></div>
            <div class="display-3-b pt-2 pb-4"><?php echo $parallax_3_text_2; ?></div>

              <a class="btn btn-outline-secondary" href="<?php echo $parallax_3_button_url; ?>"><?php echo $parallax_3_button; ?></a>
          </div>
          <div class="col-lg-2 col-sm-1 col-1">

          </div>
      </div>
  </div>

</section>
<?php
} else { echo "Niente parallax_3";}

}
add_action( 'custom_parallax_3', 'parallax_3', 15 );

function button_book(){
?>
<div class="mybutton mybutton_vertical">
<a href="/demetrios/book-an-appointment/" class="btn btn-info feedback" role="button">Book an Appointment</a>
</div>

<?php
}
add_action( 'demetrios_butt_book', 'button_book', 0 );

function demetrios_footer_buttons(){
?>
<div class="d-flex d-sm-none bottom_buttons text-center w-100">
  <div class="button_botom_1 w-50 px-1 py-2"><a href="https://eboy.gr/demetrios/store-finder/">Stores near you</a></div>
  <div class="button_botom_2 w-50 px-1 py-2"><a href="https://eboy.gr/demetrios/book-an-appointment/">Book an Appointment</a></div>

</div>
<?php
}
add_action( 'demetrios_footer', 'demetrios_footer_buttons', 10 );




add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) {
	if ( isset( $query->query_vars['facetwp'] ) ) {
		$is_main_query = (bool) $query->query_vars['facetwp'];
	}
	return $is_main_query;
}, 10, 2 );

function store_finder(){
        ?>
<?php  echo facetwp_display( 'facet', 'map' ); ?>

      <?php
}
add_action( 'custom_store_finder', 'store_finder', 15 );



function store_finder_split_2(){
        ?>
        <div class="container p-0">
          <div id="accordion">
  <div class="card">


    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
      <div class="card-body">
        <div class="d-flex flex-row">
  <div class="proximity p-0 w-100"><?php  echo facetwp_display( 'facet', 'proximity' ); ?></div>


</div>

      </div>
    </div>

  </div>

    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
      <div class="card">
      <div class="card-body">
        <div class="row p-4">
        <div class="col-4">
        <?php  echo facetwp_display( 'facet', 'country_proximity' ); ?>
      </div>
      <div class="col-4">
      <?php  echo facetwp_display( 'facet', 'state' ); ?>
    </div>
    <div class="col-4">
    <?php  echo facetwp_display( 'facet', 'city' ); ?>
    <!-- <?php  //echo facetwp_display( 'facet', 'rating' ); ?> -->
  </div>
  </div>      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12 text-center">
      <div class="choose_search d-flex flex-row justify-content-center align-items-center">
  <div class="pb-2"><h5 class="mb-0"><button class="btn btn-link btn_location " onclick="FWP.reset()">Enter your location</button></h5>
  </div>
  <div class="pb-2">- OR -</div>
  <div class="pb-2">
    <h5 class="mb-0"><button class="btn btn-link collapsed btn_country" onclick="FWP.reset()">Choose by country</button></h5>
  </div>
</div>
  </div>
</div>
</div>
        <div class="row">
            <div class="col-12 px-5 pb-3">
            <?php //echo facetwp_display( 'facet', 'country_or_city' ); ?>
            <?php  echo facetwp_display( 'facet', 'store_category' ); ?>
            </div>
              </div>

        <div class="row px-3">
              <div class="col-12">
                <div class="row p-2">
            <?php echo facetwp_display('counts'); ?>
            <?php echo facetwp_display('selections'); ?>
            </div>
            </div>
            </div>
        </div>
<?php
  // WP_Query arguments
  $args = array(
    "post_type" => "stores",
    "post_status" => "publish",
  //  'meta_key'			=> 'rating',
  	'orderby'			=> 'post__in',
    "order" => "DESC",
    "posts_per_page" => 5,
    'facetwp' => true
  );

  $query = new WP_Query( $args );
  ?>
  <div class="facetwp-template container">
    <div class="row">
    <?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
    $email_2 = get_field( "email_2" );
    $street_address = get_field( "street_address" );
    $phone = get_field( "phone" );
    $phone_icon = '<img class="ico" src=" ' .get_template_directory_uri() .'/dist/images/phone.svg">';
    $directions_icon = '<img class="ico svg-convert" src=" ' .get_template_directory_uri() .'/dist/images/directions.svg">';
    $city = get_field( "city" );
    $country = get_field( "country" );
    $term_list = wp_get_post_terms($post->ID, 'store_cat', array("fields" => "all"));

    $location = get_field('location');
    $distance = facetwp_get_distance();
    ?>
<div class="col-lg-12 py-3 px-2">
  <div class="card">
  <div class="card-header">
    <div class="container">
      <div class="row">
        <div class="col-lg-9 col-8 p-0">
    <?php the_title(); ?>
      </div>
      <div class="col-lg-3 col-4 text-right p-0">
        <?php if ( false !== $distance ) {
        echo round( $distance, 2 );
        echo ' Km';
    } ?>
  </div>
  </div>
</div>
  </div>
    <div class="card-body">
      <h5 class="card-title"><?php echo $street_address; ?>, <?php echo $city; ?>, <?php echo $country; ?></h5>
       <footer class="blockquote-footer">
  <?php echo wp_strip_all_tags(
    get_the_term_list( get_the_ID(), 'store_cat', ' ', ' , ', ' ')
);?>
  <span class="float-right pt-3">
    <a class="btn btn-outline-primary btn-sm" href="tel:<?php echo $phone; ?>"><?php echo $phone_icon; ?> <?php echo $phone; ?></a>
 <a class="btn btn-primary btn-sm" href="https://www.google.com/maps?saddr=Current+Location&daddr=<?php  echo $location['lat'] . ',' . $location['lng']; ?>"><?php echo $directions_icon; ?> <?php _e('Get Directions','demetrios'); ?></a>
</span>
  </footer>
    </div>
  </div>
</div>
  <?php endwhile; ?>
  <div class="col-12">
  <?php echo facetwp_display( 'pager' ); ?>
  </div>
</div>
    <?php // joints_page_navi(); ?>
  <?php else : ?>
            <?php wp_reset_postdata();?>
    <?php get_template_part( 'templates/unit', 'missing' ); ?>

  <?php endif; ?>
  </div>
<?php
}
add_action( 'custom_store_spilt_finder_2', 'store_finder_split_2', 15 );

function book(){
        ?>
        <div class="container-fluid back-grey500 top-page ">
          <div class="row">
            <div class="col-12">
              <div class="container py-4">
                <div class="row">
                  <div class="col-lg-6">
                  <h1><?php echo esc_html( get_the_title() ); ?></h1>
                 </div>
                 <div class="col-lg-6 text-right">
                   <?php if (function_exists('the_breadcrumb')) the_breadcrumb(); ?>
                </div>
            </div>
            </div>
            </div>
            </div>
            </div>
        <div class="container mt-5 book-appontment">
          <div class="row">
            <div class="col-12">
              <?php gravity_form_enqueue_scripts( 1, false ); ?>
              <?php gravity_form('Book an appointment', false, false, false, '', false); ?>
              </div>
                </div>
              </div>
        <?php
}
add_action( 'custom_book', 'book', 15 );


function woo_cat_thumb() {

  if ( is_product_category() ){

      global $wp_query;

      // get the query object
      $cat = $wp_query->get_queried_object();

      // get the thumbnail id using the queried category term_id
      $thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );

      // get the image URL
      $image = wp_get_attachment_url( $thumbnail_id );
      ?>


      <section class="module bg-dark-60 parallax-bg h-100" data-background="<?php echo $image ?>" style="background-position: 50% 35%;">


          <div class="container h-100">
              <div class="row product-cat-if-head h-100 align-items-end p-5">
                  <div class="col-lg-2 col-sm-1 col-1">

                  </div>
                  <div class="col-lg-8 col-sm-10 col-10 text-center">
                    <h1 class="woocommerce-products-header__title page-title"><?php single_term_title(); ?></h1>

                </div>
                  <div class="col-lg-2 col-sm-1 col-1">

                  </div>
              </div>
          </div>

      </section>

<?php
}


}
add_action( 'demetrios_woo_cat_thumb', 'woo_cat_thumb', 10);



add_action( 'init', 'woo_remove_wc_breadcrumbs' );
function woo_remove_wc_breadcrumbs() {
    remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
}

remove_action( 'woocommerce_before_shop_loop' , 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );

function new_loop_shop_per_page( $cols ) {
  // $cols contains the current number of products per page based on the value stored on Options -> Reading
  // Return the number of products you wanna show per page.
  $cols = 8;
  return $cols;
}



add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 45 );
//remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );



//remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
//add_action( 'woocommerce_after_single_product_c', 'woocommerce_output_related_products', 10 );

add_filter("gform_init_scripts_footer", "init_scripts");
function init_scripts() {
return true;
}

// Auto-populate end date if it is empty.
function update_end_date_cf( $value, $post_id, $field ) {

   //don't use get_field() because it retrieves the value
   //in a preformatted way different as it is saved in database
   $end_date = get_post_meta( $post_id, 'email_2', true );
   $start_date = get_post_meta( $post_id, 'email_1', true );

   if ($end_date == '' && $start_date != '') {
      $value = $start_date;
   }

   return $value;

}
add_filter('acf/update_value/name=email_2', 'update_end_date_cf', 10, 3);

remove_action( 'woocommerce_shop_loop_item_title' , 'woocommerce_template_loop_product_title', 10 );

function woocommerce_template_loop_product_title_custom() {

  $url = get_permalink($product_id)
  ?>
  <div class="d-flex justify-content-between align-items-center flex-wrap product_archive_view_info">
    <div class="py-2 pl-4"><?php the_title('<h4>', '</h4>'); ?></div>
    <div class="p-2"><a class="details" href="<?php echo $url; ?>" >Details</a></div>
  </div>

<?php

}
add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title_custom', 10);

foreach ( array( 'pre_term_description' ) as $filter ) {
    remove_filter( $filter, 'wp_filter_kses' );
}
foreach ( array( 'term_description' ) as $filter ) {
    remove_filter( $filter, 'wp_kses_data' );
}

add_filter("gform_field_content", "bootstrap_styles_for_gravityforms_fields", 10, 5);
function bootstrap_styles_for_gravityforms_fields($content, $field, $value, $lead_id, $form_id){

	// Currently only applies to most common field types, but could be expanded.

	if($field["type"] != 'hidden' && $field["type"] != 'list' && $field["type"] != 'multiselect' && $field["type"] != 'checkbox' && $field["type"] != 'fileupload' && $field["type"] != 'date' && $field["type"] != 'html' && $field["type"] != 'address') {
		$content = str_replace('class=\'medium', 'class=\'form-control medium', $content);
	}

	if($field["type"] == 'name' || $field["type"] == 'address') {
		$content = str_replace('<input ', '<input class=\'form-control\' ', $content);
	}

  if($field["type"] == 'text') {
    $content = str_replace('<input ', '<input class=\'form-control\' ', $content);
  }

	if($field["type"] == 'textarea') {
		$content = str_replace('class=\'textarea', 'class=\'form-control textarea', $content);
	}

	if($field["type"] == 'checkbox') {
		$content = str_replace('li class=\'', 'li class=\'checkbox ', $content);
		$content = str_replace('<input ', '<input style=\'margin-left:1px;\' ', $content);
	}

	if($field["type"] == 'radio') {
		$content = str_replace('li class=\'', 'li class=\'radio ', $content);
		$content = str_replace('<input ', '<input style=\'margin-left:1px;\' ', $content);
	}

	return $content;

} // End bootstrap_styles_for_gravityforms_fields()
add_filter("gform_submit_button", "form_submit_button", 10, 2);
function form_submit_button($button, $form){
    return "<div class='container'><div class='row p-3'><button class='btn btn-primary btn-lg btn-block form_submit' id='gform_submit_button_{$form["id"]}' disabled><span>Submit</span></button></div></div>";
}


add_filter( 'gform_pre_render_1', 'populate_posts' );
add_filter( 'gform_pre_validation_1', 'populate_posts' );
add_filter( 'gform_pre_submission_filter_1', 'populate_posts' );
add_filter( 'gform_admin_pre_render_1', 'populate_posts' );
function populate_posts( $form ) {
    foreach ( $form['fields'] as &$field ) {
        if ( strpos( $field->cssClass, 'country_selector' ) === false ) {
            continue;
        }
        $new_field_choices = array();
     	 $terms = get_terms( 'store_loc', array('hide_empty' => true));
     	 foreach ($terms as $term) {
     	 	$new_field_choices[] = array(
     	 		'text' => $term->name,
     	 		'value' => $term->name
     	 	);
     	 }
     	$field->choices = $new_field_choices;
    }
    return $form;
}


add_filter( 'gform_pre_render_2', 'populate_posts' );
add_filter( 'gform_pre_validation_2', 'populate_posts' );
add_filter( 'gform_pre_submission_filter_2', 'populate_posts' );
add_filter( 'gform_admin_pre_render_2', 'populate_posts' );
function populate_posts_store_finder( $form ) {
    foreach ( $form['fields'] as &$field ) {
        if ( strpos( $field->cssClass, 'country_selector' ) === false ) {
            continue;
        }
        $new_field_choices = array();
     	 $terms = get_terms( 'store_loc', array('hide_empty' => true));
     	 foreach ($terms as $term) {
     	 	$new_field_choices[] = array(
     	 		'text' => $term->name,
     	 		'value' => $term->name
     	 	);
     	 }
     	$field->choices = $new_field_choices;
    }
    return $form;
}



add_filter( 'facetwp_result_count', function( $output, $params ) {
    $output = $params['lower'] . '-' . $params['upper'] . ' of ' . $params['total'] . ' Retailers' . '&nbsp;';
    return $output;
}, 10, 2 );


add_filter( 'facetwp_facet_dropdown_show_counts', '__return_false' );

add_filter( 'facetwp_facetwp_checkbox_show_counts', '__return_false' );

add_filter( 'facetwp_proximity_store_distance', '__return_true' );

add_filter( 'facetwp_map_marker_args', function( $args, $post_id ) {
    $args['icon'] = get_template_directory_uri() . '/dist/images/map_pin.svg';
    return $args;
}, 10, 2 );

add_action( 'pre_get_posts', function( $query ) {
    if ( ! class_exists( 'FacetWP_Helper' ) ) {
        return;
    }

    $facets_in_use = FWP()->facet->facets;
    $prefix = FWP()->helper->get_setting( 'prefix' );
    $using_sort = isset( FWP()->facet->http_params['get'][ $prefix . 'sort' ] );

    $is_main_query = false;
    if ( is_array( FWP()->facet->template ) ) {
        if ( 'wp' != FWP()->facet->template['name'] || true === $query->get( 'facetwp' ) ) {
            $is_main_query = true;
        }
    }

    if ( ! empty( $facets_in_use ) && ! $using_sort && $is_main_query ) {
        foreach ( $facets_in_use as $f ) {
            if ( 'proximity' == $f['type'] && ! empty( $f['selected_values'] ) ) {
                $query->set( 'orderby', 'post__in' );
                $query->set( 'order', 'ASC' );
            }
        }
    }
});



function namespace_footer_sidebar_params($params) {

    $sidebar_id = $params[0]['id'];

    if ( $sidebar_id == 'sidebar-footer' ) {

        $total_widgets = wp_get_sidebars_widgets();
        $sidebar_widgets = count($total_widgets[$sidebar_id]);

        $params[0]['before_widget'] = str_replace('<section class="widget ', '<section class="widget text-center p-4 col-xs-6 col-md-' . floor(12 / $sidebar_widgets) . ' ', $params[0]['before_widget']);
    }

    return $params;
}
add_filter('dynamic_sidebar_params','namespace_footer_sidebar_params');




add_action( 'woocommerce_single_product_summary', 'wc_next_prev_products_links', 60 );
function wc_next_prev_products_links() { ?>
  <div class="row">
  <div class="col-12 text-right">
  <div class="btn-group" role="group" aria-label="Basic example">
  <button type="button" class="btn btn-secondary btn-sm btn_previous">
    <?php previous_post_link( '%link', '&nbsp; &nbsp; Previous' ); ?>
  </button>

  <button type="button" class="btn btn-secondary btn-sm btn_next">
	<?php next_post_link( '%link', 'Next &nbsp; &nbsp;' ); ?>
  </button>
  </div>
  </div>
  </div>
  <?php
}

add_filter( 'woocommerce_single_product_carousel_options', 'ud_update_woo_flexslider_options' );

function ud_update_woo_flexslider_options( $options ) {

    $options['directionNav'] = true;

    return $options;
}

//remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
//add_action( 'woocommerce_single_product_summary', 'woocommerce_show_product_thumbnails', 30 );
function isa_woocommerce_all_pa(){

    global $product;
    $attributes = $product->get_attributes();

    if ( ! $attributes ) {
        return;
    }

    $out = '<ul class="custom-attributes">';

    foreach ( $attributes as $attribute ) {


        // skip variations
        if ( $attribute->get_variation() ) {
        continue;
        }
        $name = $attribute->get_name();
        if ( $attribute->is_taxonomy() ) {

            $terms = wp_get_post_terms( $product->get_id(), $name, 'all' );
            // get the taxonomy
            $tax = $terms[0]->taxonomy;
            // get the tax object
            $tax_object = get_taxonomy($tax);
            // get tax label
            if ( isset ( $tax_object->labels->singular_name ) ) {
                $tax_label = $tax_object->labels->singular_name;
            } elseif ( isset( $tax_object->label ) ) {
                $tax_label = $tax_object->label;
                // Trim label prefix since WC 3.0
                if ( 0 === strpos( $tax_label, 'Product ' ) ) {
                   $tax_label = substr( $tax_label, 8 );
                }
            }


            $out .= '<li class="' . esc_attr( $name ) . '">';
            $out .= '<span class="attribute-label">' . esc_html( $tax_label ) . ': </span> ';
            $out .= '<span class="attribute-value">';
            $tax_terms = array();
            foreach ( $terms as $term ) {
                $single_term = esc_html( $term->name );
                // Show terms as links, when available
                if ( $single_product ) {
                    $term_link = get_term_link( $term );
                    if ( ! is_wp_error( $term_link ) ) {
                        $single_term = '<a href="' . esc_url( $term_link ) . '">' . esc_html( $term->name ) . '</a>';
                    }
                }                array_push( $tax_terms, $single_term );
            }
            $out .= implode(', ', $tax_terms);
            $out .= '</span></li>';

        } else {
            $value_string = implode( ', ', $attribute->get_options() );
            $out .= '<li class="' . sanitize_title($name) . ' ' . sanitize_title( $value_string ) . '">';
            $out .= '<span class="attribute-label">' . $name . ': </span> ';
            $out .= '<span class="attribute-value">' . esc_html( $value_string ) . '</span></li>';
        }
    }

    $out .= '</ul>';

    echo $out;
}
add_action('demetrios_product_attributes', 'isa_woocommerce_all_pa', 25);

remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail');

function woocommerce_template_loop_product_thumbnail_responsive() {
$product_lg = wp_get_attachment_image_src( get_post_thumbnail_id(),'product-lg' );
$product_md = wp_get_attachment_image_src( get_post_thumbnail_id(),'product-md' );
$product_sm = wp_get_attachment_image_src( get_post_thumbnail_id(),'product-sm' );
$product_xs = wp_get_attachment_image_src( get_post_thumbnail_id(),'product-xs' );
$image_id = $image->id;
$image_alt = get_post_meta( $image->id, '_wp_attachment_image_alt', true);
//echo '<img data-src="' . $image_src[0] . '" width="100" height="100">';
//echo '<img src="' . $image_src[0] . '" class="img-fluid" alt="Demetrios Wedding Dresses">';

echo '<picture>';
echo '<source srcset=" ' . $product_lg[0] . ' " media="(min-width: 1400px)">';
echo '<source srcset=" ' . $product_sm[0] . ' " media="(min-width: 769px)">';
echo '<source srcset=" ' . $product_xs[0] . ' " media="(min-width: 577px)">';
echo '<img srcset=" ' . $product_lg[0] . ' " alt=" ' . $image_alt .' " class="img-fluid">';
echo '</picture>';
}
add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail_responsive', 10);


function product_carousel() {
  global $product;
  $thumb_id = get_post_thumbnail_id();
  $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'product-lg', true);
  $thumb_url = $thumb_url_array[0];
  $attachment_ids = $product->get_gallery_attachment_ids();
  $attachment_small_ids = $product->get_gallery_attachment_ids();
//  $attachment_ids_array = wp_get_attachment_image_src($attachment_ids, 'product-lg', true);
//  $attachment_ids_url = $attachment_ids_url[0];


  $thumb_small_id = get_post_thumbnail_id();
  $thumb_small_url_array = wp_get_attachment_image_src($thumb_small_id, 'shop_thumbnail', true);
  $thumb_small_url = $thumb_small_url_array[0];
  //$gallery_image = echo $shop_thumbnail_image_url = wp_get_attachment_image_src( $attachment_id, 'product-lg' )[0];
  $number = 1;
  ?>


  <div class="row">
  <div class="col-12">
  <div id="myCarousel" class="carousel slide">
    <!-- main slider carousel nav controls -->

    <div class="row">
    <div class="col-3 pr-2">
        <div class="item pb-3 active">
            <a id="carousel-selector-0" class="selected" data-slide-to="0" data-target="#myCarousel">
                <img src="<?php echo $thumb_small_url; ?>" class="img-fluid" alt="Example">
            </a>
        </div>
        <?php
          foreach( $attachment_small_ids as $attachment_small_id ) {
            echo '<div class="item pb-3">';
            echo '  <a id="carousel-selector" class="" data-slide-to="' . $number++ . '" data-target="#myCarousel">';
        //  echo wp_get_attachment_image($attachment_id, 'product-lg');
          echo '<img src=" ' . $shop_thumbnail_image_url = wp_get_attachment_image_src( $attachment_small_id, 'shop_thumbnail' )[0] . ' " class="img-fluid" alt="Example">';
          echo '</a>';
          echo '</div>';
            }
        ?>
    </div>
    <!-- main slider carousel items -->
    <div class="carousel-inner col-9 px-2">
        <div class="active item carousel-item" data-slide-number="0">
            <img src="<?php echo $thumb_url; ?>" class="img-fluid" alt="Example">
        </div>
        <?php
          foreach( $attachment_ids as $attachment_id ) {
            echo '<div class="item carousel-item" data-slide-number="' . $number++ . '">';
        //  echo wp_get_attachment_image($attachment_id, 'product-lg');
          echo '<img src=" ' . $shop_thumbnail_image_url = wp_get_attachment_image_src( $attachment_id, 'product-lg' )[0] . ' " class="img-fluid" alt="Example">';
          echo '</div>';
            }
        ?>
        <a class="carousel-control previous pt-3" href="#myCarousel" data-slide="prev"><i class="fa fa-chevron-left"></i></a>
        <a class="carousel-control next pt-3" href="#myCarousel" data-slide="next"><i class="fa fa-chevron-right"></i></a>
    </div>
  </div>
  </div>
</div>
</div>
<?
}
add_action('demetrios_product_carousel', 'product_carousel', 10);


add_filter( 'woocommerce_get_image_size_gallery_thumbnail', function( $size ) {
return array(
'width' => 152.28,
'height' => 228.42,
'crop' => 0,
);
} );

add_filter( 'the_content', 'wti_remove_autop_for_image', 0 );


function social_sharing()
{
	extract(shortcode_atts(array(), $atts));
	return'

    <div class="col-12 p-0">
		<a class="p-3 social-sharing-icon social-sharing-icon-facebook" target="_new" href="http://www.facebook.com/share.php?u=' . urlencode(get_the_permalink()) . '&title=' . urlencode(get_the_title()). '"></a>
		<a class="p-3 social-sharing-icon social-sharing-icon-twitter" target="_new" href="http://twitter.com/home?status='. urlencode(get_the_title()). '+'. urlencode(get_the_permalink()) . '"></a>
		<a class="p-3 social-sharing-icon social-sharing-icon-pinterest" target="_new" href="https://pinterest.com/pin/create/button/?url=' . urlencode(get_the_permalink()) . '&media=' . urlencode(get_template_directory_uri()."/img/logo.png") . '&description=' . urlencode(get_the_title()). '"></a>
		<a class="p-3 social-sharing-icon social-sharing-icon-google-plus" target="_new" href="https://plus.google.com/share?url=' . urlencode(get_the_permalink()) . '"></a>
		<a class="p-3 social-sharing-icon social-sharing-icon-linkedin" target="_new" href="http://www.linkedin.com/shareArticle?mini=true&url=' . urlencode(get_the_permalink()) . '&title=' . urlencode(get_the_title()) . '&source=' . get_bloginfo("url") . '"></a>
		<a class="p-3 social-sharing-icon social-sharing-icon-tumblr" target="_new" href="http://www.tumblr.com/share?v=3&u=' . urlencode(get_the_permalink()) . '&t=' . urlencode(get_the_title()). '"></a>
		<a class="p-3 social-sharing-icon social-sharing-icon-email" target="_new" href="mailto:?subject=' . urlencode(get_the_permalink()) . '&body=Check out this article I came across '. get_the_permalink() .'"></a>
    </div>

';
}
add_shortcode("social_sharing", "social_sharing");

function social_sharing_2()
{
	extract(shortcode_atts(array(), $atts));
	return'

    <div class="p-2">
		<a class="p-3 social-sharing-icon social-sharing-icon-facebook" target="_new" href="http://www.facebook.com/share.php?u=' . urlencode(get_the_permalink()) . '&title=' . urlencode(get_the_title()). '"></a>
		<a class="p-3 social-sharing-icon social-sharing-icon-twitter" target="_new" href="http://twitter.com/home?status='. urlencode(get_the_title()). '+'. urlencode(get_the_permalink()) . '"></a>
		<a class="p-3 social-sharing-icon social-sharing-icon-pinterest" target="_new" href="https://pinterest.com/pin/create/button/?url=' . urlencode(get_the_permalink()) . '&media=' . urlencode(get_template_directory_uri()."/img/logo.png") . '&description=' . urlencode(get_the_title()). '"></a>
		<a class="p-3 social-sharing-icon social-sharing-icon-google-plus" target="_new" href="https://plus.google.com/share?url=' . urlencode(get_the_permalink()) . '"></a>
		<a class="p-3 social-sharing-icon social-sharing-icon-linkedin" target="_new" href="http://www.linkedin.com/shareArticle?mini=true&url=' . urlencode(get_the_permalink()) . '&title=' . urlencode(get_the_title()) . '&source=' . get_bloginfo("url") . '"></a>
		<a class="p-3 social-sharing-icon social-sharing-icon-tumblr" target="_new" href="http://www.tumblr.com/share?v=3&u=' . urlencode(get_the_permalink()) . '&t=' . urlencode(get_the_title()). '"></a>
		<a class="p-3 social-sharing-icon social-sharing-icon-email" target="_new" href="mailto:?subject=' . urlencode(get_the_permalink()) . '&body=Check out this article I came across '. get_the_permalink() .'"></a>
    </div>

';
}
add_shortcode("social_sharing_2", "social_sharing_2");

function wti_remove_autop_for_image( $content )
{
     global $post;

     // Check for single page and image post type and remove
     if ( is_single() && $post->post_type == 'product' )
          remove_filter('the_content', 'wpautop');

     return $content;
}

add_action('woocommerce_archive_description', 'woocommerce_category_description', 2);

function woocommerce_template_single_whishlist() {
  echo '<div class="pb-2 pr-2 title-item">';
  echo do_shortcode("[ti_wishlists_addtowishlist]");
  echo '</div>';

}
add_action ('woocommerce_single_product_summary', 'woocommerce_template_single_whishlist', 7);

function woocommerce_template_single_title_open() {
  echo '<div class="d-flex flex-wrap justify-content-between align-items-center custom-title-item">';

}
add_action ('woocommerce_single_product_summary', 'woocommerce_template_single_title_open', 1);

function woocommerce_template_single_title_close() {
  echo '</div>';

}
add_action ('woocommerce_single_product_summary', 'woocommerce_template_single_title_close', 9);

function woocommerce_template_single_social() {
  echo '<div class="pb-2 pr-2 title-item">';
  echo do_shortcode("[social_sharing]");
  echo '</div>';

}
add_action ('woocommerce_single_product_summary', 'woocommerce_template_single_social', 8);

function open_big_image() {
  ?>
  <div class="slide-pan " id="carouselExampleControls" data-ride="carousel">


  <div class="tile carousel-item active" data-scale="1.1" data-image="<?php echo wp_get_attachment_url(get_post_thumbnail_id()); ?>"></div>



  </div>

<?php
}
add_action ('woocommerce_after_single_product_summary', 'open_big_image', 5);




function woocommerce_output_related_products_single() {
  ?>
<div class="container demetrios-product-title title-unzoomed">
  <div class="row">
    <div class="col-12">
      <h1><?php do_action( 'woocommerce_shop_loop_item_title' ); ?></h1>
    </div>
  </div>
</div>
<?
}
add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products_single', 17 );

add_action( 'wp', 'init' );

function init() {

  if ( is_product() ) {

    // yipee, this works!
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
  //  add_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 20 );

  }

}

function demetrios_pages_header_custom_1() {
?>
<div class="container-fluid back-grey500 top-page">
  <div class="row">
    <div class="col-12">
      <div class="container py-4">
        <div class="row align-items-center h-100">
          <div class="col-6">
          <h1><?php echo esc_html( get_the_title() ); ?></h1>
         </div>
         <div class="col-6 text-right">
           <?php if (function_exists('the_breadcrumb')) the_breadcrumb(); ?>
        </div>
    </div>
    </div>
    </div>
    </div>
    </div>
<?php
}
add_action ('demetrios_pages_header', 'demetrios_pages_header_custom_1', 10, 10 );

function custom_whishlist() {
?>
  <div class="container test">
    <div class="row">
      <div class="col-12">
        Test
        </div>
        </div>
      </div>

<?php
}
add_action( 'demetrios_custom_whishlist', 'custom_whishlist', 10, 10 );


function demetrios_wishlist_custom_description() {
	global $product;
	if ( ! $product->post->post_excerpt ) return;
	?>
	<div itemprop="description">
		<?php echo apply_filters( 'woocommerce_short_description', $product->post->post_content ); ?>
	</div>
	<?php
}
add_action('demetrios_wishlist_custom', 'demetrios_wishlist_custom_description', 5);

function wishlist_custom_notices(){
?>
<div class="container-fluid pt-2">
  <div class="row">
    <div class="col-12 text-center">
<?php if ( function_exists( 'wc_print_notices' ) ) {
  wc_print_notices();
} ?>

</div>
</div>
</div>
<?php
}
add_action('demetrios_wishlist_custom_notices', 'wishlist_custom_notices', 10, 5);



function demetrios_wishlist_meta_custom() {
  ?>
  <div class="col-12">
  <?php do_action( 'woocommerce_product_meta_start' ); ?>

  <?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>


  <?php endif; ?>

  <?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( '', '', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>

  <?php echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>

  <?php do_action( 'woocommerce_product_meta_end' ); ?>
</div>
<?php
}


add_action ('demetrios_wishlist_meta', 'demetrios_wishlist_meta_custom', 10);
