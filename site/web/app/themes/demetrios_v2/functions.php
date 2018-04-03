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
  'lib/customizer.php' // Theme customizer
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
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

if ( !class_exists( 'ReduxFramework' ) && file_exists( dirname( __FILE__ ) . '/ReduxFramework/ReduxCore/framework.php' ) ) {
    require_once( dirname( __FILE__ ) . '/ReduxFramework/ReduxCore/framework.php' );
}
if ( !isset( $redux_demo ) && file_exists( dirname( __FILE__ ) . '/ReduxFramework/sample/sample-config.php' ) ) {
    require_once( dirname( __FILE__ ) . '/ReduxFramework/sample/sample-config.php' );
}

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


// Google API Key
function my_acf_google_map_api( $api ){

	$api['key'] = 'AIzaSyAY55sLjGdZyuE5fX9gIH0NegqSeB24LEU';

	return $api;

}

add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');



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

function video_background(){
  $video_background = get_field( "video_background" );
  $video_background_text_1 = get_field( "video_background_text_1" );
  $video_background_text_2 = get_field( "video_background_text_2" );
  $video_background_button = get_field( "video_background_button" );
  $video_background_button_url = get_field( "video_background_button_url" );

  if( $video_background ) {

?>
<section class="home-section bg-dark-30" id="home" data-background="assets/images/finance/finance_header_bg.png">
  <div class="video-player" data-property="{videoURL:'<?php echo $video_background; ?>', containment:'.home-section', startAt:30, mute:false, autoPlay:true, loop:true, opacity:.5, showControls:false, showYTLogo:false, vol:25}"></div>

  <div class="titan-caption">
    <div class="caption-content">
      <div class="font-alt mb-30"><h2><?php echo $video_background_text_1; ?></h2></div>
      <div class="font-alt mb-40 titan-title-size-4 pb-3"><?php echo $video_background_text_2; ?></div><a class="section-scroll btn btn-border-w btn-round" href="<?php echo $video_background_button_url; ?>"><?php echo $video_background_button; ?></a>
    </div>
  </div>
</section><?php
} else { echo "Niente";}

}
add_action( 'demetrios', 'video_background', 10 );

function fasa_1(){
  $fasa_1 = get_field( "fasa_1" );
  $fasa_2 = get_field( "fasa_2" );
  $button_1_text = get_field( "button_1_text" );

  if( $fasa_1 ) {

?>
<section class="module-extra-small bg-dark">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-sm-12">
        <div class="callout-text font-alt">
          <h4 style="margin-top: 0px; font-;"><?php echo $fasa_1; ?></h4>
          <p style="margin-bottom: 0px;"><?php echo $fasa_2; ?></p>
        </div>
      </div>
      <div class="col-lg-6 col-sm-12 text-center">
        <div class="btn btn-border-w btn-round">
        <a href="#">  <?php echo $button_1_text; ?></a>
        </div>
      </div>

    </div>
  </div>
</section><?php
} else { echo "Niente fasa";}

}
add_action( 'custom_fasa_1', 'fasa_1', 15 );

function parallax_1(){
  $parallax_1 = get_field( "parallax_1" );
  $parallax_1_text_1 = get_field( "parallax_1_text_1" );
  $parallax_1_text_2 = get_field( "parallax_1_text_2" );
  $parallax_1_button = get_field( "parallax_1_button" );
  $parallax_1_button_url = get_field( "parallax_1_button_url" );


  if( $parallax_1 ) {

?>
<section class="module bg-dark-60 parallax-bg h-50" data-background="<?php echo $parallax_1; ?>" style="background-position: 50% 15%;">

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
<section class="module pt-5 pb-5 h-50">
  <div class="container-fluid h-100">
    <div class="row justify-content-center pb-5">
      <div class="col-12 text-center">
        <h3><?php echo $section_half_1_header; ?></h3>
      </div>
    </div>
  <div class="row position-relative m-0">
    <div class="col-xs-12 col-md-6 side-image" >
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
<div class="container">
  <div class="row justify-content-center pb-5">
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
  $external_img_2 = get_field( "external_img_2" );
  $external_img_3 = get_field( "external_img_3" );
  $external_text_1 = get_field( "external_text_1" );
  $external_text_2 = get_field( "external_text_2" );
  $external_text_3 = get_field( "external_text_3" );

  if( $external_img_1 ) {

?>
<section class="module pt-5 pb-5 h-50">
  <div class="container-fluid h-100">
    <div class="row justify-content-center pb-5">
      <div class="col-12 text-center">
        <h3><?php echo $section_half_1_header; ?></h3>
      </div>
    </div>
    <div class="row">
      <div class="col-4">
        <img src="<?php echo $external_img_1; ?>" class="img-fluid">
      </div>
      <div class="col-4">
        <img src="<?php echo $external_img_2; ?>" class="img-fluid">
      </div>
      <div class="col-4">
        <img src="<?php echo $external_img_3; ?>" class="img-fluid">
      </div>
    </div>
  </div>
</section>

<?php
} else { echo "Niente half_1";}

}
add_action( 'custom_external', 'external_1', 15 );


function store_finder(){


        ?>
        <div class="container">
          <div class="row">
            <div class="col-lg-4">
              <?php echo eboywp_display( 'facet', 'country' ); ?>
              </div>
              <div class="col-lg-4">
                <?php echo eboywp_display( 'facet', 'country' ); ?>
                </div>
                <div class="col-lg-4">
                  <?php echo eboywp_display( 'facet', 'country' ); ?>
                  </div>
            </div>
            <div class="row">
              <div class="col-lg-6">

                </div>
                <div class="col-lg-6">

                  </div>
              </div>
        </div>

              <div class="container-fluid p-0" id="wrapper">
                <div class="row">
                  <div class="col-12">
   <div class="container-fluid p-0" id="google_map">
  <?php echo eboywp_display( 'facet', 'location' ); ?>
   </div>

   <div class="container" id="over_map">
     <div class="row">
       <div class="col-4 div-w">
<?php echo eboywp_display( 'facet', 'store_category' ); ?>
</div>
 <div class="col-8">
    <?php echo eboywp_display( 'facet', 'proximity' ); ?>
    </div>
   </div>
</div>
</div>
</div>
</div>


        <div class="container">
          <div class="row">
            <div class="col-12">
              <?php echo eboywp_display( 'template', 'stores' ); ?>
              </div>
            </div>
        </div>
        <?php
}
add_action( 'custom_store_finder', 'store_finder', 15 );


function book(){
        ?>
        <div class="container">
          <div class="row">
            <div class="col-lg-5 widget">

                <?php echo eboywp_display( 'facet', 'country_dropdown' ); ?>

            </div>
            <div class="col-lg-2 widget">

              <?php echo eboywp_display( 'facet', 'state_dropdown' ); ?>

            </div>
            <div class="col-lg-5 widget">

                <?php echo eboywp_display( 'facet', 'citydropdown' ); ?>

            </div>
                </div>
              <?php echo eboywp_display( 'template', 'stores_radios' ); ?>

              <?php gravity_form_enqueue_scripts( 1, false ); ?>
              <?php gravity_form( 1, false, false, false, '', false ); ?>

              </div>

        <?php
}
add_action( 'custom_book', 'book', 15 );

add_filter("gform_init_scripts_footer", "init_scripts");
function init_scripts() {
return true;
}
