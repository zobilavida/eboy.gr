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
    'option_type'   => 'theme_mod',
    'option_name'   => 'telesphorus_sage',
) );


Kirki::add_panel( 'header', array(
    'priority'    => 10,
    'title'       => __( 'Header', 'telesphorus' ),
    'description' => __( 'This panel will provide all the options of the header.', 'telesphorus' ),
) );

/* adding header_logo section*/
Kirki::add_section( 'header_logo', array(
    'title'          => __( 'Logo' ),
    'description'    => __( 'Add a logo.', 'telesphorus' ),
    'panel'          => 'header', // Not typically needed.
    'priority'       => 10,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );
/* adding header_logo_setting field */
Kirki::add_field( 'logo', array(
    'settings' => 'header_logo_setting',
    'label'    => __( 'Setting for the logo', 'telesphorus' ),
    'section'  => 'header_logo',
    'type'     => 'image',
    'priority' => 10,
    'default'  => '',
) );

/* adding header_color section*/
Kirki::add_section( 'header_color', array(
    'title'          => __( 'Color',  'telesphorus' ),
    'description'    => __( 'Choose Header Background color.', 'telesphorus' ),
    'panel'          => 'header', // Not typically needed.
    'priority'       => 20,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );
/* adding header_color_setting field */
Kirki::add_field( 'kirki_demo', array(
	'type'        => 'color',
	'settings'    => 'color',
	'description' => esc_attr__( 'Description.', 'kirki-demo' ),
	'label'       => __( 'Background Color', 'telesphorus' ),
	'section'     => 'header_color',
	'default'     => '#333333',
	'priority'    => 10,
	'transport'   => 'auto',
	'choices'     => array(
		'alpha' => true,
	),
	'output' => array(
		array(
			'element'  => 'header',
			'property' => 'background-color',
		),
	),
) );

	}
}
add_action( 'after_setup_theme', 'my_plugin_include_kirki' );
