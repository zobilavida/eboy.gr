<?php

include_once( dirname( __FILE__ ) . '/includes/kirki/kirki.php' );

function mytheme_kirki_configuration() {
    return array( 'url_path'     => get_stylesheet_directory_uri() . '/includes/kirki/' );
}
add_filter( 'kirki/config', 'mytheme_kirki_configuration' );

function up_kirki_section( $wp_customize ) {
 /**
 * Add sections
 */
$wp_customize->add_section( 'body_options', array(
 'title'       => __( 'Body Options', 'telesphorus' ),
 'priority'    => 10,
// 'panel'       => 'telesphorus_options',
 ) );
$wp_customize->add_section( 'header_options', array(
'title'       => __( 'Header Options', 'telesphorus' ),
'priority'    => 20,
 // 'panel'       => 'telesphorus_options',
) );
$wp_customize->add_section( 'hero_options', array(
'title'       => __( 'Hero Options', 'telesphorus' ),
'priority'    => 30,
 // 'panel'       => 'telesphorus_options',
) );
}
add_action( 'customize_register', 'up_kirki_section' );

function up_kirki_fields( $wp_customize ) {
 /*Body Options*/
 $fields[] = array(
   'type'        => 'color',
 	'settings'    => 'color_setting_hex',
 	'label'       => __( 'Body Background Color', 'telesphorus' ),
 	'description' => esc_attr__( 'This is a color control - without alpha channel.', 'textdomain' ),
 	'section'     => 'body_options',
 	'default'     => '#0088CC',
);

$fields[] = array(
  'type'        => 'typography',
	'settings'    => 'my_setting',
	'label'       => esc_attr__( 'Control Label', 'telesphorus' ),
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

 $fields[] = array(
   'type'      => 'color',
 	'settings'  => 'content_color',
 	'label'     => __( 'Header Color', 'telesphorus' ),
 	'section'   => 'header_options',
 	'default'   => '#FFFFFF',
 	'priority'  => 10,
 	'transport' => 'auto',
 	'output'    => array(
 		array(
 			'element'  => 'header',
 			'property' => 'background-color'
 		),
 	),
  );

  $fields[] = array(
    'type'      => 'color',
  	'settings'  => 'color_top',
  	'label'     => esc_attr__( 'Left Color', 'mytheme' ),
  	'section'   => 'hero_options',
  	'default'   => '#FFFFFF',
  	'priority'  => 10,
  	'output'    => array(
  		array(
  			'element'         => '.hero-section',
  			'property'        => 'background',
  			'value_pattern'   => 'linear-gradient(90deg, $ topPos%,bottomCol bottomPos%)',
  			'pattern_replace' => array(
  				'topPos'    => 'color_bottom',
  				'bottomCol' => 'color_top_position',
  				'bottomPos' => 'color_bottom_position',
  			),
  		),
  	),
);

$fields[] = array(
  'type'      => 'color',
	'settings'  => 'color_bottom',
	'label'     => esc_attr__( 'Right Color', 'mytheme' ),
	'section'   => 'hero_options',
	'default'   => '#F2F2F2',
	'priority'  => 11,
	'output'    => array(
		array(
			'element'         => '.hero-section',
			'property'        => 'background',
			'value_pattern'   => 'linear-gradient(90deg, topCol topPos%,$ bottomPos%)',
			'pattern_replace' => array(
				'topCol'    => 'color_top',
				'topPos'    => 'color_top_position',
				'bottomPos' => 'color_bottom_position',
			),
		),
	),
 );

 $fields[] = array(
   'type'      => 'slider',
 	'settings'  => 'color_top_position',
 	'label'     => esc_attr__( 'Left Color Position', 'mytheme' ),
 	'section'   => 'hero_options',
 	'default'   => 0,
 	'priority'  => 12,
 	'choices'   => array(
 		'min'  => 0,
 		'max'  => 100,
 		'step' => 1,
 	),
     'output'    => array(
 		array(
 			'element'         => '.hero-section',
 			'property'        => 'background',
 			'value_pattern'   => 'linear-gradient(90deg, topCol $%,bottomCol bottomPos%)',
 			'pattern_replace' => array(
 				'topCol'    => 'color_top',
 				'bottomCol' => 'color_bottom',
 				'bottomPos' => 'color_bottom_position',
 			),
 		),
 	),
 );

 $fields[] = array(
   'type'      => 'slider',
 	'settings'  => 'color_bottom_position',
 	'label'     => esc_attr__( 'Right Color Position', 'mytheme' ),
 	'section'   => 'hero_options',
 	'default'   => 0,
 	'priority'  => 13,
 	'choices'   => array(
 		'min'  => 0,
 		'max'  => 100,
 		'step' => 1,
 	),
     'output'    => array(
 		array(
 			'element'         => '.hero-section',
 			'property'        => 'background',
 			'value_pattern'   => 'linear-gradient(90deg, topCol topPos%,bottomCol $%)',
 			'pattern_replace' => array(
 				'topCol'    => 'color_top',
 				'topPos'    => 'color_top_position',
 				'bottomCol' => 'color_bottom',
 			),
 		),
 	),
);




    return $fields;
}

add_filter( 'kirki/fields', 'up_kirki_fields' );
