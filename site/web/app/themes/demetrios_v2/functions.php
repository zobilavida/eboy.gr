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

function video_background(){
  $video_background = get_field( "video_background" );

  if( $video_background ) {

?>
<section class="home-section home-parallax bg-dark-30" id="home" data-background="assets/images/finance/finance_header_bg.png">
  <div class="video-player" data-property="{videoURL:'<?php echo $video_background; ?>', containment:'.home-section', startAt:30, mute:false, autoPlay:true, loop:true, opacity:.5, showControls:false, showYTLogo:false, vol:25}"></div>
  <div class="video-controls-box">
    <div class="container">
      <div class="video-controls"><a class="fa fa-volume-up" id="video-volume" href="#">&nbsp;</a><a class="fa fa-pause" id="video-play" href="#">&nbsp;</a></div>
    </div>
  </div>
  <div class="titan-caption">
    <div class="caption-content">
      <div class="font-alt mb-30 titan-title-size-1">Hello &amp; welcome</div>
      <div class="font-alt mb-40 titan-title-size-4">We are Titan</div><a class="section-scroll btn btn-border-w btn-round" href="#about">Learn More</a>
    </div>
  </div>
</section><?php
} else { echo "Niente";}

}
add_action( 'demetrios', 'video_background', 10 );

function fasa_1(){
  $fasa_1 = get_field( "fasa_1" );
  $button_1_text = get_field( "button_1_text" );

  if( $fasa_1 ) {

?>
<section class="module-extra-small bg-dark">
  <div class="container">
    <div class="row">
      <div class="col-sm-6 col-md-8 col-lg-6 col-lg-offset-2">
        <div class="callout-text font-alt">
          <h4 style="margin-top: 0px; font-;"><?php echo $fasa_1; ?></h4>
          <p style="margin-bottom: 0px;">We are always here for you</p>
        </div>
      </div>
      <div class="col-sm-6 col-md-4 col-lg-2">
        <div class="callout-btn-box"><a class="btn btn-w btn-round" href="#"><?php echo $button_1_text; ?></a></div>
      </div>
    </div>
  </div>
</section><?php
} else { echo "Niente fasa";}

}
add_action( 'custom_fasa_1', 'fasa_1', 15 );
