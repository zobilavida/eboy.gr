<?php
/**
 * telesphorus includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/telesphorus/pull/1042
 */
$sage_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/wrapper.php',   // Theme wrapper class
  'lib/tgm-plugin-activation.php',   // TGM plugin activation
  'lib/customizer.php',
  'lib/bs4navwalker.php',
  'lib/theme-settings.php'
  //'theme-customizations.php' // Theme menu
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'telesphorus'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);



/* Include Redux
if ( is_admin() ) {
    include 'admin/admin-init.php';
}
*/

// Add svg & swf support
function cc_mime_types( $mimes ){
    $mimes['svg'] = 'image/svg+xml';
    $mimes['swf']  = 'application/x-shockwave-flash';

    return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );


function get_hero_content(){
  $image_url = get_theme_mod( 'hero_image_background', '' );
  $video_url = get_theme_mod( 'hero_video_background', '' );
  if( get_theme_mod('hero_image_background') ){?>

    <video poster="<?php echo esc_url( get_theme_mod( 'hero_image_background' ) ); ?>" class="video-fluid" playsinline autoplay muted loop>
      <source src="<?php echo esc_url( get_theme_mod( 'hero_video_background' ) ); ?>" type="video/mp4">
    </video>
<?php }else{
  //your code

}
}
add_action ('telesphorus_hero', 'get_hero_content');