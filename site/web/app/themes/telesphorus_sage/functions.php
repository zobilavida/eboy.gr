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
  'lib/customizer.php' // Theme customizer
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'telesphorus'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

include_once( dirname( __FILE__ ) . '/includes/kirki/kirki.php' );

function mytheme_kirki_configuration() {
    return array( 'url_path'     => get_stylesheet_directory_uri() . '/includes/kirki/' );
}
add_filter( 'kirki/config', 'mytheme_kirki_configuration' );

function up_kirki_section( $wp_customize ) {
 /**
 * Add panels
 */


 /**
 * Add sections
 */
     $wp_customize->add_section( 'body_options', array(
 'title'       => __( 'Body Options', 'telesphorus' ),
 'priority'    => 10,
// 'panel'       => 'telesphorus_options',
 ) );



}
add_action( 'customize_register', 'up_kirki_section' );

function up_kirki_fields( $wp_customize ) {

 /*General Options*/
 $fields[] = array(
   'type'        => 'color',
 	'settings'    => 'color_setting_hex',
 	'label'       => __( 'Body Background Color', 'textdomain' ),
 	'description' => esc_attr__( 'This is a color control - without alpha channel.', 'textdomain' ),
 	'section'     => 'body_options',
 	'default'     => '#0088CC',
);

$fields[] = array(
  'type'        => 'typography',
	'settings'    => 'my_setting',
	'label'       => esc_attr__( 'Control Label', 'textdomain' ),
	'section'     => 'body_options',
	'default'     => array(
		'font-family'    => 'Roboto',
		'variant'        => 'regular',
		'font-size'      => '14px',
		'line-height'    => '1.5',
		'letter-spacing' => '0',
		'color'          => '#333333',
		'text-transform' => 'none',
		'text-align'     => 'left',
	),
	'priority'    => 10,
	'output'      => array(
		array(
			'element' => 'body',
		),
	),
 );


    return $fields;
}

add_filter( 'kirki/fields', 'up_kirki_fields' );


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
