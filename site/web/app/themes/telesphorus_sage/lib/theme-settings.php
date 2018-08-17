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
Kirki::add_field( 'telesphorus_sage', array(
	'type'        => 'color',
	'settings'    => 'color-palette-3',
	'description' => esc_attr__( 'Light, tiny.', 'telesphorus' ),
	'label'       => __( 'Color-Palette control', 'telesphorus' ),
	'section'     => 'header_color',
	'default'     => '#ffffff',
	'priority'    => 10,
//	'transport'   => 'auto',
	'choices'     => array(
		'alpha' => true,
		'size'   => 20,
	),
	'output' => array(
		array(
			'element'  => 'header',
			'property' => 'background-color',
		),
	),
) );


Kirki::add_panel( 'hero', array(
    'priority'    => 15,
    'title'       => __( 'Hero', 'telesphorus' ),
    'description' => __( 'This panel will provide all the options of the Hero.', 'telesphorus' ),
) );



/* adding hero section*/
Kirki::add_section( 'hero_background', array(
    'title'          => __( 'Image or Video background' ),
    'description'    => __( 'Customize Hero.', 'telesphorus' ),
    'panel'          => 'hero', // Not typically needed.
    'priority'       => 10,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

Kirki::add_field( 'telesphorus_sage', array(
	'type'        => 'image',
	'settings'    => 'hero_image_url',
	'label'       => esc_attr__( 'Image Control (URL)', 'telesphorus' ),
	'description' => esc_attr__( 'Description Here.', 'telesphorus' ),
	'section'     => 'hero_background',
	'priority'    => 10,
	'default'     => '',
) );

Kirki::add_field( 'telesphorus_sage', array(
	'type'        => 'upload',
	'settings'    => 'hero_video_url',
	'label'       => esc_attr__( 'Video Control (URL)', 'telesphorus' ),
	'description' => esc_attr__( 'Description Here.', 'telesphorus' ),
	'section'     => 'hero_background',
	'priority'    => 20,
	'default'     => '',
) );

/* adding hero section*/
Kirki::add_section( 'hero', array(
    'title'          => __( 'Hero gradient overlay' ),
    'description'    => __( 'Customize Hero.', 'telesphorus' ),
    'panel'          => 'hero', // Not typically needed.
    'priority'       => 20,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

// Add Fields.
Kirki::add_field( 'telesphorus_sage', array(
	'type'      => 'color',
	'settings'  => 'color_top',
	'label'     => esc_attr__( 'Top Color', 'telesphorus_sage' ),
	'section'   => 'hero',
	'default'   => '#FFFFFF',
	'priority'  => 10,
	'output'    => array(
		array(
			'element'         => '.hero-section::before',
			'property'        => 'background',
			'value_pattern'   => 'linear-gradient(angledeg, $ topPos%,bottomCol bottomPos%)',
			'pattern_replace' => array(
				'topPos'    => 'color_bottom',
				'bottomCol' => 'color_top_position',
				'bottomPos' => 'color_bottom_position',
				'angle' => 'direction_angle',
			),
		),
	),
) );

Kirki::add_field( 'telesphorus_sage', array(
	'type'      => 'color',
	'settings'  => 'color_bottom',
	'label'     => esc_attr__( 'Bottom Color', 'telesphorus_sage' ),
	'section'   => 'hero',
	'default'   => '#F2F2F2',
	'priority'  => 11,
	'output'    => array(
		array(
			'element'         => '.hero-section::before',
			'property'        => 'background',
			'value_pattern'   => 'linear-gradient(angledeg, topCol topPos%,$ bottomPos%)',
			'pattern_replace' => array(
				'topCol'    => 'color_top',
				'topPos'    => 'color_top_position',
				'bottomPos' => 'color_bottom_position',
				'angle' => 'direction_angle',
			),
		),
	),
) );

Kirki::add_field( 'telesphorus_sage', array(
	'type'      => 'slider',
	'settings'  => 'color_top_position',
	'label'     => esc_attr__( 'Top Color Position', 'telesphorus_sage' ),
	'section'   => 'hero',
	'default'   => 0,
	'priority'  => 12,
	'choices'   => array(
		'min'  => 0,
		'max'  => 100,
		'step' => 1,
	),
    'output'    => array(
		array(
			'element'         => '.hero-section::before',
			'property'        => 'background',
			'value_pattern'   => 'linear-gradient(angledeg, topCol $%,bottomCol bottomPos%)',
			'pattern_replace' => array(
				'topCol'    => 'color_top',
				'bottomCol' => 'color_bottom',
				'bottomPos' => 'color_bottom_position',
				'angle' => 'direction_angle',
			),
		),
	),
) );

Kirki::add_field( 'telesphorus_sage', array(
	'type'      => 'slider',
	'settings'  => 'color_bottom_position',
	'label'     => esc_attr__( 'Bottom Color Position', 'telesphorus_sage' ),
	'section'   => 'hero',
	'default'   => 0,
	'priority'  => 13,
	'choices'   => array(
		'min'  => 0,
		'max'  => 100,
		'step' => 1,
	),
    'output'    => array(
		array(
			'element'         => '.hero-section::before',
			'property'        => 'background',
			'value_pattern'   => 'linear-gradient(angledeg, topCol topPos%,bottomCol $%)',
			'pattern_replace' => array(
				'topCol'    => 'color_top',
				'topPos'    => 'color_top_position',
				'bottomCol' => 'color_bottom',
				'angle' => 'direction_angle',
			),
		),
	),
) );

Kirki::add_field( 'telesphorus_sage', array(
	'type'      => 'slider',
	'settings'  => 'direction_angle',
	'label'     => esc_attr__( 'Gradient angle', 'telesphorus_sage' ),
	'section'   => 'hero',
	'default'   => 90,
	'priority'  => 14,
	'choices'   => array(
		'min'  => 0,
		'max'  => 180,
		'step' => 1,
	),
    'output'    => array(
		array(
			'element'         => '.hero-section::before',
			'property'        => 'background',
			'value_pattern'   => 'linear-gradient($deg, topCol topPos%,bottomCol bottomPos%)',
			'pattern_replace' => array(
				'topCol'    => 'color_top',
				'topPos'    => 'color_top_position',
				'bottomCol' => 'color_bottom',
				'bottomPos' => 'color_bottom_position',
			),
		),
	),
) );


Kirki::add_field( 'telesphorus_sage', array(
	'type'      => 'slider',
	'settings'  => 'gradient_opacity',
	'label'     => esc_attr__( 'Gradient opacity', 'telesphorus_sage' ),
	'section'   => 'hero',
	'default'   => 0.5,
	'priority'  => 15,
	'choices'   => array(
		'min'  => 0,
		'max'  => 1,
		'step' => 0.1,
	),
    'output'    => array(
		array(
			'element'         => '.hero-section::before',
			'property'        => 'opacity',
		),
	),
) );

/* adding hero section*/
Kirki::add_section( 'hero_content', array(
    'title'          => __( 'Hero Content' ),
    'description'    => __( 'Customize Hero.', 'telesphorus' ),
    'panel'          => 'hero', // Not typically needed.
    'priority'       => 30,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

Kirki::add_field( 'telesphorus_sage', array(
	'type'        => 'radio',
	'settings'    => 'hero-static-text-option',
	'label'       => __( 'Radio Control', 'textdomain' ),
	'section'     => 'hero_content',
	'default'     => 'static',
	'priority'    => 10,
	'choices'     => array(
		'static'   => array(
			esc_attr__( 'Static Text', 'my_textdomain' ),
			esc_attr__( 'These are some extra details about Red', 'textdomain' ),
		),
		'carousel' => array(
			esc_attr__( 'Carousel', 'kirki' ),
			esc_attr__( 'These are some extra details about Green', 'textdomain' ),
		),
	),
) );

Kirki::add_field( 'telesphorus_sage', array(
	'type'     => 'textarea',
	'settings' => 'hero-static-text',
	'label'    => __( 'Textarea Control', 'textdomain' ),
	'section'  => 'hero_content',
	'default'  => esc_attr__( 'This is a defualt value', 'textdomain' ),
	'priority' => 10,
	'required'    => array(
		array(
				'setting'  => 'hero-static-text-option',
				'value'    => 'static',
				'operator' => '==',
		),
),
) );
	}
}
add_action( 'after_setup_theme', 'my_plugin_include_kirki' );
