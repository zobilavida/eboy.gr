<?php

namespace Roots\Sage\Setup;

use Roots\Sage\Assets;

/**
 * Theme setup
 */
function setup() {
  // Enable features from Soil when plugin is activated
  // https://eboy.io/plugins/soil/
  add_theme_support('soil-clean-up');
  add_theme_support('soil-nav-walker');
  //add_theme_support('soil-nice-search');
  add_theme_support('soil-jquery-cdn');
  add_theme_support('soil-js-to-footer');
  add_theme_support('soil-relative-urls');
  add_theme_support('woocommerce');
  //add_theme_support( 'wc-product-gallery-zoom' );
  //add_theme_support( 'wc-product-gallery-lightbox' );
  //add_theme_support( 'wc-product-gallery-slider' );
  add_theme_support( 'woocommerce', array(
    'thumbnail_image_width'         => 612,
  //  'gallery_thumbnail_image_width' => 160,
    'single_image_width'            => 560,
) );

  // Make theme available for translation
  // Community translations can be found at https://github.com/eboy/demetrios_3-translations
  load_theme_textdomain('demetrios_3', get_template_directory() . '/lang');

  // Enable plugins to manage the document title
  // http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
  add_theme_support('title-tag');

  // Register wp_nav_menu() menus
  // http://codex.wordpress.org/Function_Reference/register_nav_menus
  register_nav_menus([
    'primary_navigation' => __('Primary Navigation', 'demetrios_3'),
    'top_navigation' => __('Top menu', 'demetrios_3'),
    'side_navigation' => __('Side menu', 'demetrios_3')
  ]);

  // Enable post thumbnails
  // http://codex.wordpress.org/Post_Thumbnails
  // http://codex.wordpress.org/Function_Reference/set_post_thumbnail_size
  // http://codex.wordpress.org/Function_Reference/add_image_size
  add_theme_support('post-thumbnails');
  add_image_size( 'carousel-size-1', 2000, 800, true );
  add_image_size( 'carousel-size-2', 1440, 700, true );
  add_image_size( 'carousel-size-3', 800, 500, true );
  add_image_size( 'carousel-size-4', 600, 500, true );
  add_image_size( 'img-half-xl', 1257, 9999 );
  add_image_size( 'img-half-lg', 582, 9999 );
  add_image_size( 'img-half-md', 479, 9999 );
  add_image_size( 'img-half-sm', 387, 9999 );
  add_image_size( 'img-half-xs', 507, 9999 );
  add_image_size( 'product-lg', 612, 918, true );
  add_image_size( 'product-md', 461, 9999 );
  add_image_size( 'product-sm', 345, 9999 );
  add_image_size( 'product-xs', 1080, 9999 );
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
    'name'          => __('Primary', 'demetrios_3'),
    'id'            => 'sidebar-primary',
    'before_widget' => '<section class="widget %1$s %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>'
  ]);

  register_sidebar([
    'name'          => __('Footer', 'demetrios_3'),
    'id'            => 'sidebar-footer',
    'before_widget' => '<section class="widget %1$s %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h5>',
    'after_title'   => '</h5>'
  ]);
  register_sidebar([
    'name'          => __('Footer Bottom', 'demetrios_3'),
    'id'            => 'sidebar-footer-bottom',
    'before_widget' => '<div class="col-12">',
    'before_widget' => '<section class="px-5 py-4 %1$s %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h4>',
    'after_title'   => '</h4>'
  ]);
  register_sidebar([
    'name'          => __('Header Left', 'demetrios_3'),
    'id'            => 'sidebar-header-left',
    'before_widget' => '<section class="widget %1$s %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>'
  ]);
  register_sidebar([
    'name'          => __('Header Right', 'demetrios_3'),
    'id'            => 'sidebar-header-right',
    'before_widget' => '<div class="px-2 d-flex align-items-center %1$s %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>'
  ]);
  register_sidebar([
    'name'          => __('Top Slider', 'demetrios_3'),
    'id'            => 'sidebar-top-slider',
    'before_widget' => '<div class="col-12">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>'
  ]);
  register_sidebar([
    'name'          => __('Next to Main Menu', 'demetrios_3'),
    'id'            => 'main-next',
    'before_widget' => '<li class="menu-item-widget py-2 pl-4">',
    'after_widget'  => '</li>',
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
  //  is_page_template('template-custom.php'),
    is_page(),
    is_product(),
    is_product_category(),
  ]);

  return apply_filters('demetrios_3/display_sidebar', $display);
}

/**
 * Theme assets
 */
function assets() {
  wp_enqueue_style('demetrios_3/css', Assets\asset_path('styles/main.css'), false, null);

  if (is_single() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }

  wp_enqueue_script('demetrios_3/js', Assets\asset_path('scripts/main.js'), ['jquery'], null, true);
//  wp_enqueue_script('demetrios_3/IfBreakpoint_js', Assets\asset_path('scripts/IfBreakpoint.js'), ['demetrios_3/js'], null, true);
//  wp_enqueue_script('demetrios_3/front_js', Assets\asset_path('scripts/front.min.js'), ['demetrios_3/js'], null, true);
//  wp_enqueue_script('demetrios_3/query_string', Assets\asset_path('scripts/query-string.js'), ['demetrios_3/js'], null, true);

}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 100);
