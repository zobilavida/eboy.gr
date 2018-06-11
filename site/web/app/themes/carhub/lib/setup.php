<?php

namespace Roots\Sage\Setup;

use Roots\Sage\Assets;

/**
 * Theme setup
 */
function setup() {
  // Enable features from Soil when plugin is activated
  // https://roots.io/plugins/soil/
  add_theme_support('soil-js-to-footer');
  add_theme_support('soil-clean-up');
  add_theme_support('soil-nav-walker');
  add_theme_support('soil-nice-search');
//  add_theme_support('soil-jquery-cdn', 'wp_head');
  add_theme_support('soil-relative-urls');
  add_theme_support('soil-google-analytics', 'UA-2151567-57', 'wp_head');
  add_theme_support('soil-relative-urls');
  add_theme_support('soil-disable-asset-versioning');


  // Make theme available for translation
  // Community translations can be found at https://github.com/roots/sage-translations
  load_theme_textdomain('sage', get_template_directory() . '/lang');

  // Enable plugins to manage the document title
  // http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
  add_theme_support('title-tag');

  // Register wp_nav_menu() menus
  // http://codex.wordpress.org/Function_Reference/register_nav_menus
  register_nav_menus([
    'primary_navigation' => __('Primary Navigation', 'sage')
  ]);

  // Enable post thumbnails
  // http://codex.wordpress.org/Post_Thumbnails
  // http://codex.wordpress.org/Function_Reference/set_post_thumbnail_size
  // http://codex.wordpress.org/Function_Reference/add_image_size
  add_theme_support('post-thumbnails');

  // Enable post formats
  // http://codex.wordpress.org/Post_Formats
  add_theme_support('post-formats', ['aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio']);

  // Enable HTML5 markup support
  // http://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
  add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

  // Use main stylesheet for visual editor
  // To add custom styles edit /assets/styles/layouts/_tinymce.scss
  add_editor_style(Assets\asset_path('styles/main.css'));
}
add_action('after_setup_theme', __NAMESPACE__ . '\\setup');

/**
 * Register sidebars
 */
function widgets_init() {
  register_sidebar([
    'name'          => __('Primary', 'sage'),
    'id'            => 'sidebar-primary',
    'before_widget' => '<section class="widget %1$s %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>'
  ]);

  register_sidebar([
    'name'          => __('Footer', 'sage'),
    'id'            => 'sidebar-footer',
    'before_widget' => '<section class="widget %1$s %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>'
  ]);
}
add_action('widgets_init', __NAMESPACE__ . '\\widgets_init');

/**
 * Determine which pages should NOT display the sidebar
 */
function display_sidebar() {
  static $display;

  isset($display) || $display = !in_array(true, [
    // The sidebar will NOT be displayed if ANY of the following return true.
    // @link https://codex.wordpress.org/Conditional_Tags
    is_404(),
    is_front_page(),
    is_page_template('template-custom.php'),
    is_product(),
  ]);

  return apply_filters('sage/display_sidebar', $display);
}

/**
 * Theme assets
 */
function assets() {
  wp_enqueue_style('sage/css', Assets\asset_path('styles/main.css'), false, null);

  if (is_single() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }
$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
  wp_enqueue_script('sage/js', Assets\asset_path('scripts/main.js'), ['jquery'], null, true);
  wp_enqueue_script('jquery-form');
  //wp_enqueue_script( 'crispshop',  Assets\asset_path('scripts/crispshop.js'), array('jquery'), '1.0.0', true );
  //wp_localize_script( 'crispshop', 'crispshop_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
  //wp_enqueue_script('sage/gijgo', Assets\asset_path('scripts/gijgo.js'), ['sage/js'], null, true);
  //wp_enqueue_script('sage/add-to-cart', Assets\asset_path('scripts/add-to-cart.js'), array( 'jquery' ), WC_VERSION, true );
//  wp_enqueue_script( 'wc-bookings-booking-form', WC_BOOKINGS_PLUGIN_URL . '/assets/js/booking-form.js', array( 'jquery', 'jquery-blockui' ), WC_BOOKINGS_VERSION, true );
  //wp_enqueue_script( 'wc-bookings-date-picker', WC_BOOKINGS_PLUGIN_URL . '/assets/js/date-picker.js', array( 'wc-bookings-booking-form', 'jquery-ui-datepicker', 'underscore' ), WC_BOOKINGS_VERSION, true );
  //wp_enqueue_script('sage/cart-fragments', Assets\asset_path('scripts/cart-fragments.js'), array( 'jquery', 'js-cookie' ), WC_VERSION, true );
  //wp_enqueue_script('sage/single-product', Assets\asset_path('scripts/single-product.js'), array( 'jquery' ), WC_VERSION, true );
  //wp_enqueue_script('sage/woocommerce', Assets\asset_path('scripts/woocommerce.js'), ['sage/js'], null, true);



  ///wp_enqueue_script('sage/date-picker', Assets\asset_path('scripts/date-picker.js'), ['sage/js'], null, true);


}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 100);
