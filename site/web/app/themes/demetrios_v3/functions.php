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
  'lib/customizer.php' // Theme customizer
];

foreach ($demetrios_3_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'demetrios_3'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

// Include custom navwalker
require_once('bs4navwalker.php');

// Register WordPress nav menu
register_nav_menu('top', 'Top menu');

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
		if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
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
		}
	}

}


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
  $delimiter = '&raquo;'; // delimiter between crumbs
  $home = 'Home'; // text for the 'Home' link
  $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
  $before = '<span class="current">'; // tag before the current crumb
  $after = '</span>'; // tag after the current crumb

  global $post;
  $homeLink = get_bloginfo('url');

  if (is_home() || is_front_page()) {

    if ($showOnHome == 1) echo '<div id="crumbs"><a href="' . $homeLink . '">' . $home . '</a></div>';

  } else {

    echo '<div id="crumbs"><a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';

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

    echo '</div>';

  }
} // end the_breadcrumb()

//// BREADCRUMB END ////
add_filter( 'eboywp_pager_html', function( $output, $params ) {
    $output = '<nav aria-label="Resources Pagination"><ul class="pagination mt-1 justify-content-center">';
    $page = $params['page'];
    $i = 1;
    $total_pages = $params['total_pages'];
    $limit = ($total_pages >= 5) ? 3 : $total_pages;
    $prev_disabled = ($params['page'] <= 1) ? 'disabled' : '';
    $output .= '<li class="page-item ' . $prev_disabled . '"><a class="eboywp-page page-link" data-page="' . ($page - 1) . '">Prev</a></li>';
    $loop = ($limit) ? $limit : $total_pages;
    while($i <= $loop) {
      $active = ($i == $page) ? 'active' : '';
      $output .= '<li class="page-item ' . $active . '"><a class="eboywp-page page-link" data-page="' . $i . '">' . $i . '</a></li>';
      $i++;
    }
    if($limit && $total_pages > '3') {
      $output .= ($page > $limit && $page != ($total_pages - 1) && $page <= ($limit + 1)) ? '<li class="page-item active"><a class="eboywp-page page-link" data-page="' . $page . '">' . $page . '</a></li>' : '';
      $output .= '<li class="page-item disabled"><a class="eboywp-page page-link">...</a></li>';
      $output .= ($page > $limit && $page != ($total_pages - 1) && $page > ($limit + 1)) ? '<li class="page-item active"><a class="eboywp-page page-link" data-page="' . $page . '">' . $page . '</a></li>' : '';
      $output .= ($page > $limit && $page != ($total_pages - 1) && $page != ($total_pages - 2) && $page > ($limit + 1)) ? '<li class="page-item disabled"><a class="eboywp-page page-link">...</a></li>' : '';
      $active = ($page == ($total_pages - 1)) ? 'active' : '';
      $output .= '<li class="page-item ' . $active . '"><a class="eboywp-page page-link" data-page="' . ($total_pages - 1) .'">' . ($total_pages - 1) .'</a></li>';
    }
    $next_disabled = ($page >= $total_pages) ? 'disabled' : '';
    $output .= '<li class="page-item ' . $next_disabled . '"><a class="eboywp-page page-link" data-page="' . ($page + 1) . '">Next</a></li>';
    $output .= '</ul></nav>';
    return $output;
}, 10, 2 );

add_filter( 'gform_field_container', 'add_bootstrap_container_class', 10, 6 );
function add_bootstrap_container_class( $field_container, $field, $form, $css_class, $style, $field_content ) {
  $id = $field->id;
  $field_id = is_admin() || empty( $form ) ? "field_{$id}" : 'field_' . $form['id'] . "_$id";
  return '<li id="' . $field_id . '" class="' . $css_class . ' form-group">{FIELD_CONTENT}</li>';
}


// Google API Key
function my_acf_google_map_api( $api ){

	$api['key'] = 'AIzaSyAY55sLjGdZyuE5fX9gIH0NegqSeB24LEU';

	return $api;

}

add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');

function custom_header(){
  if (is_page('Home')) {
      // below content only show when page id is 12
      $home_logo = get_field( "home_logo" );

?>

<nav class="navbar navbar-expand-sm sticky-top navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= esc_url(home_url('/')); ?>">
          <img class="logo_2" src='<?php echo $home_logo; ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>
          <img class="logo hidden" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>

        </a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbar1">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbar1">

            <?php
            wp_nav_menu([
              'menu'            => 'top',
              'theme_location'  => 'top',
              'container'       => '',
              'container_id'    => '',
              'container_class' => '',
              'menu_id'         => false,
              'menu_class'      => 'navbar-nav ml-auto',
              'depth'           => 2,
              'fallback_cb'     => 'bs4navwalker::fallback',
              'walker'          => new bs4navwalker()
            ]);
            ?>
        </div>
    </div>
</nav>

<?php
  }
  // if page id is not 12 & 14 then below line will be print
  else {
  ?>
  <nav class="navbar navbar-expand-sm sticky-top navbar-light bg-white">
      <div class="container">
          <a class="navbar-brand" href="<?= esc_url(home_url('/')); ?>">
            <img class="logo" src='<?php echo esc_url( get_theme_mod( 'themeslug_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'>

          </a>
          <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbar1">
              <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbar1">

              <?php
              wp_nav_menu([
                'menu'            => 'top',
                'theme_location'  => 'top',
                'container'       => '',
                'container_id'    => '',
                'container_class' => '',
                'menu_id'         => false,
                'menu_class'      => 'navbar-nav ml-auto',
                'depth'           => 2,
                'fallback_cb'     => 'bs4navwalker::fallback',
                'walker'          => new bs4navwalker()
              ]);
              ?>
          </div>
      </div>
  </nav>

  <?php

    }
}
add_action('demetrios_custom_header', 'custom_header');
####################################################
#    VIDEO
####################################################

function demetrios_front_video(){

  $video = get_field( "video_url" );
  $text_over_video = get_field( "text_over_video" );

?>
<section class="video">



      <div class="jumbotron" data-background="<?php echo $parallax_1; ?>" style="background-position: 50% 15%;">
        <video id="video-background" preload="" muted="" autoplay="" loop="">
          <source src="<?php echo $video; ?>" type="video/mp4">
        </video>
        <div class="titan-caption">
          <div class="caption-content">
          <?php echo $text_over_video; ?>
        </div>
        </div>
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
        <div class="font-alt mb-40 titan-title-size-4 pb-3"><?php echo $parallax_1_text_2; ?></div><a class="section-scroll btn btn-border-w btn-round" href="<?php echo $parallax_1_button_url; ?>"><?php echo $parallax_1_button; ?></a>
      </div>
    </div>

</section>

<?php
} else { echo "Niente parallax_1";}

}
add_action( 'custom_parallax_1', 'parallax_1', 15 );



function half_1(){
  $half_1 = get_field( "half_1" );
  $section_half_1_header = get_field( "section_half_1_header" );
  $half_1_text_1 = get_field( "half_text_1" );
  $half_1_text_2 = get_field( "half_text_2" );
  $half_1_button = get_field( "half_1_button" );
  $half_1_button_url = get_field( "half_1_button_url" );

  if( $half_1 ) {

?>
<section class="module pt-1">
  <div class="container-fluid h-100 pl-0">
    <div class="row justify-content-center">
      <div class="col-12 text-center">
        <h3><?php echo $section_half_1_header; ?></h3>
      </div>
    </div>
  <div class="row position-relative m-0">
    <div class="col-xs-12 col-md-6 side-image  pl-0" >
      <img src="<?php echo $half_1; ?>" class="img-fluid">
    </div>
    <div class="col-xs-12 col-md-6 col-md-offset-6 side-image-text">
      <div class="row h-100">
        <div class="col-sm-12 align-self-center pl-5">
<span class="align-middle">
  <h2><?php echo $half_1_text_1; ?></h2>
  <div class="font-alt mb-40 titan-title-size-4 pb-3"><?php echo $half_1_text_2; ?></div><a class="section-scroll btn btn-border-d btn-round" href="<?php echo $half_1_button_url; ?>"><?php echo $half_1_button; ?></a>

</span>
        </div>
      </div>
    </div>
  </div>
  </div>
</section>

<?php
} else { echo "Niente half_1";}

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
      <h3><?php echo $section_parallax_2_header; ?></h3>
    </div>
  </div>
</div>
<section class="module bg-dark-60 parallax-bg h-25" data-background="<?php echo $parallax_2; ?>" style="background-position: 50% 15%;">

    <div class="titan-caption">
      <div class="caption-content">
        <div class="font-alt mb-30"><h2><?php echo $parallax_2_text_1; ?></h2></div>
        <div class="font-alt mb-40 titan-title-size-4 pb-3"><?php echo $parallax_2_text_2; ?></div><a class="section-scroll btn btn-border-w btn-round" href="<?php echo $parallax_2_button_url; ?>"><?php echo $parallax_2_button; ?></a>
      </div>
    </div>

</section>
<?php
} else { echo "Niente parallax_2";}

}
add_action( 'custom_parallax_2', 'parallax_2', 15 );


function external_1(){
  $section_external_header = get_field( "section_external_header" );
  $external_img_1 = get_field( "external_img_1" );
  $external_1_button = get_field( "external_butt_1" );
  $external_1_button_url = get_field( "external_butt_1_url" );
  $external_img_2 = get_field( "external_img_2" );
  $external_2_button = get_field( "external_butt_2" );
  $external_2_button_url = get_field( "external_butt_2_url" );
  $external_img_3 = get_field( "external_img_3" );
  $external_3_button = get_field( "external_butt_3" );
  $external_3_button_url = get_field( "external_butt_3_url" );

  if( $section_external_header ) {

?>
<section class="module pt-1">
  <div class="container-fluid h-100">
    <div class="row justify-content-center">
      <div class="col-12 text-center">
        <h3><?php echo $section_external_header; ?></h3>
      </div>
    </div>
    <div class="row">
      <div class="col-4 text-center p-0">
        <a href="<?php echo $external_1_button_url; ?>">
        <img src="<?php echo $external_img_1; ?>" class="img-fluid">
        </a>
        <a class="section-scroll btn btn-border-g btn-round mt-3" href="<?php echo $external_1_button_url; ?>"><?php echo $external_1_button; ?></a>
      </div>
      <div class="col-4 text-center p-0">
        <a href="<?php echo $external_2_button_url; ?>">
        <img src="<?php echo $external_img_2; ?>" class="img-fluid">
        </a>
        <a class="section-scroll btn btn-border-g btn-round mt-3" href="<?php echo $external_2_button_url; ?>"><?php echo $external_2_button; ?></a>
      </div>
      <div class="col-4 text-center p-0">
        <a href="<?php echo $external_3_button_url; ?>">
        <img src="<?php echo $external_img_3; ?>" class="img-fluid">
        </a>
        <a class="section-scroll btn btn-border-g btn-round mt-3" href="<?php echo $external_3_button_url; ?>"><?php echo $external_3_button; ?></a>
      </div>
    </div>
  </div>
</section>

<?php
} else { echo "Niente half_1";}

}
add_action( 'custom_external', 'external_1', 15 );

function parallax_3(){
  $section_parallax_3_header = get_field( "section_parallax_3_header" );
  $parallax_3 = get_field( "parallax_3" );
  $parallax_3_text_1 = get_field( "parallax_3_text_1" );
  $parallax_3_text_2 = get_field( "parallax_3_text_2" );
  $parallax_3_button = get_field( "parallax_3_button" );
  $parallax_3_button_url = get_field( "parallax_3_button_url" );


  if( $section_parallax_3_header ) {

?>
<div class="container-fluid bg-white">
  <div class="row justify-content-center pt-1">
    <div class="col-12 text-center">
      <h3><?php echo $section_parallax_3_header; ?></h3>
    </div>
  </div>
</div>
<section class="module bg-dark-60 parallax-bg h-25" data-background="<?php echo $parallax_3; ?>" style="background-position: 50% 15%;">

    <div class="titan-caption">
      <div class="caption-content">
        <div class="font-alt mb-30"><h2><?php echo $parallax_3_text_1; ?></h2></div>
        <div class="font-alt mb-40 titan-title-size-4 pb-3"><?php echo $parallax_3_text_2; ?></div><a class="section-scroll btn btn-border-w btn-round" href="<?php echo $parallax_3_button_url; ?>"><?php echo $parallax_3_button; ?></a>
      </div>
    </div>

</section>
<?php
} else { echo "Niente parallax_3";}

}
add_action( 'custom_parallax_3', 'parallax_3', 15 );

function button_book(){
  $button_book = get_field( "button_book" );
  $button_book_url = get_field( "button_book_url" );

  if( $button_book ) {

?>
<div id="mybutton">
<button class="feedback"><?php echo $button_book; ?></button>
</div>
<?php
} else { echo "Niente button"; }

}
add_action( 'demetrios_butt_book', 'button_book', 0 );


function my_eboywp_is_main_query( $is_main_query, $query ) {
    if ( isset( $query->query_vars['eboywp'] ) ) {
        $is_main_query = true;
    }
    return $is_main_query;
}
add_filter( 'eboywp_is_main_query', 'my_eboywp_is_main_query', 10, 2 );

function store_finder(){
        ?>
        <div class="container-fluid p-0" id="wrapper">

          <div class="row">
            <div class="col-12 p-0">
<div class="container-fluid p-0" id="google_map">
<?php echo eboywp_display( 'facet', 'location' ); ?>
<div class="section" id="contact">
    <div style="width:100px; height:200px; background:#ccc;z-index:999999;position:absolute;">test</div>
</div>
</div>
</div>
</div>
</div>
      <?php
}
add_action( 'custom_store_finder', 'store_finder', 15 );





function store_finder_split(){
        ?>
        <div class="container">
        <div class="row pt-3">
          <div class="col-4">
          <?php echo eboywp_display( 'facet', 'country_dropdown' ); ?>
          </div>
         <div class="col-4">

        <?php echo eboywp_display( 'facet', 'state_dropdown' ); ?>

        </div>
        <div class="col-4">

       <?php echo eboywp_display( 'facet', 'city_dropdown' ); ?>

       </div>
       <div class="col-12 p-3">
       <div class="my_hr" >
             <span class="my_hr_span">
               Available Collections
             </span>
           </div>
           </div>
        <div class="col-12">
        <?php echo eboywp_display( 'facet', 'store_category' ); ?>
        </div>
        <div class="col-12 p-3">
        <div class="my_hr" >
              <span class="my_hr_span">
                View Stores
              </span>
            </div>
            </div>
        </div>

        </div>
<?php
  // WP_Query arguments
  $args = array(
    "post_type" => "stores",
    "post_status" => "publish",
    "orderby" => "title",
    "order" => "ASC",
    "posts_per_page" => 35,
    'eboywp' => true // Also tried without this and accompanying function in functions.php
  );
  // The Query
  $query = new WP_Query( $args );
  ?>
  <div class="eboywp-template container">
    <div class="row">
    <?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
    $email_2 = get_field( "email_2" );
    $street_address = get_field( "street_address" );
    $phone = get_field( "phone" );
    ?>
<div class="col-lg-6 py-3">
<div class="card">
<div class="card-body">
  <h5 class="card-title"><?php the_title(); ?></h5>
  <p class="card-text"><?php echo $street_address; ?></p>
  <a class="btn btn-primary" href="https://www.google.com/maps?saddr=My+Location&daddr=<?php $location = get_field('location'); echo $location['lat'] . ',' . $location['lng']; ?>"><?php _e('Get Directions','roots'); ?></a>
</div>
</div>
</div>


  <?php endwhile; ?>
</div>
    <?php // joints_page_navi(); ?>

  <?php else : ?>
            <?php wp_reset_postdata();?>
    <?php get_template_part( 'parts/content', 'missing' ); ?>

  <?php endif; ?>
  </div>
<?php
}
add_action( 'custom_store_spilt_finder', 'store_finder_split', 15 );


function book(){
        ?>
        <div class="container-fluid back-grey500 top-page">
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

        <div class="container px-4 mt-5 book-appontment">
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


      <section class="module bg-dark-60 parallax-bg h-100" data-background="<?php echo $image ?>" style="background-position: 50% 15%;">

          <div class="titan-caption">
            <div class="caption-content">
              <div class="font-alt mb-30 p-5 mt-5 mb-5"><h2><?php single_term_title(); ?></h2></div>
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
  $cols = 20;
  return $cols;
}

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs' );

function woocommerce_output_product_data_tabs_no() {
  ?>
<div class="clear pt-5">
</div>

<?php



}
add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs_no', 10);


add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 45 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );


function woocommerce_template_loop_product_thumbnail_responsive() {

  echo  woocommerce_get_product_thumbnail();



}

add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail_responsive', 15 );

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
