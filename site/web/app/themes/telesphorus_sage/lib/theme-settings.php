<?php



/**
 * Include files containing customizer sections, fields etc.
 * This is hooked on 'after_setup_theme'
 * so that it runs after all plugins have been instantiated.
 */
function my_plugin_include_kirki() {
	if ( class_exists( 'Kirki' ) ) {
	//	include 'path-to-include-kirki.php';
  function customizer_config() {

    $args = array(

        // Change the logo image. (URL) Point this to the path of the logo file in your theme directory
                // The developer recommends an image size of about 250 x 250
        'logo_image'   => get_template_directory_uri() . '/dist/images/logo_telesphorus.svg',

        // The color of active menu items, help bullets etc.
        'color_active' => '#444',

        // Color used for secondary elements and desable/inactive controls
        'color_light'  => '#eee',

        // Color used for button-set controls and other elements
        'color_select' => '#34495e',

                // You can add your own custom stylesheet for full control as well
                // For the parameter here, use the handle of your stylesheet you use in wp_enqueue
                'stylesheet_id' => 'customize-styles',

                // Only use this if you are bundling the plugin with your theme (see above)
              //  'url_path'     => get_template_directory_uri() . '/kirki/',


    );

    return $args;

}
add_filter( 'kirki/config', 'customizer_config' );


Kirki::add_config( 'telesphorus_sage', array(
    'capability'    => 'edit_theme_options',
    'option_type'   => 'option',
    'option_name'   => 'telesphorus_sage',
) );


Kirki::add_panel( 'header', array(
    'priority'    => 10,
    'title'       => __( 'Header', 'theme_slug' ),
    'description' => __( 'This panel will provide all the options of the header.', 'theme_slug' ),
) );

/* adding header_logo section*/
Kirki::add_section( 'header_logo', array(
    'title'          => __( 'Logo' ),
    'description'    => __( 'Add a logo.' ),
    'panel'          => 'header', // Not typically needed.
    'priority'       => 160,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );
/* adding header_logo_setting field */
Kirki::add_field( 'mk', array(
    'settings' => 'header_logo_setting',
    'label'    => __( 'Setting for the logo', 'theme_slug' ),
    'section'  => 'header_logo',
    'type'     => 'image',
    'priority' => 10,
    'default'  => '',
) );

	}
}
add_action( 'after_setup_theme', 'my_plugin_include_kirki' );
