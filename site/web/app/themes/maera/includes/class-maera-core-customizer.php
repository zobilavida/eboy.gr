<?php

class Maera_Core_Customizer {

	function __construct() {

		// if ( defined( 'MAERA_SHOW_CORE_CUSTOMIZER' ) && MAERA_SHOW_CORE_CUSTOMIZER ) {
			add_action( 'customize_register', array( $this, 'add_section' ) );
			add_action( 'customize_register', array( $this, 'add_settings' ) );
			add_action( 'customize_register', array( $this, 'add_controls' ) );
		// }

	}

	/**
	 * Add the customizer section
	 */
	function add_section( $wp_customize ) {

		$wp_customize->add_section( 'maera_options', array(
			'title'    => esc_html__( 'Maera Options', 'maera' ),
			'priority' => 1,
		) );

	}

	/**
	 * Add the setting
	 */
	function add_settings( $wp_customize ) {

		$wp_customize->add_setting( 'maera_admin_options[shell]', array(
			'default'        => 'core',
			'type'           => 'option',
			'capability'     => 'edit_theme_options',
			'sanitize_callback' => array( $this, 'sanitize_text_value' ),

		) );

		$wp_customize->add_setting( 'maera_admin_options[dev_mode]', array(
			'default'        	=> 'core',
			'type'           	=> 'option',
			'capability'     	=> 'edit_theme_options',
			'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
		) );

		$wp_customize->add_setting( 'maera_admin_options[cache]', array(
			'default'        => 'core',
			'type'           => 'option',
			'capability'     => 'edit_theme_options',

		) );

		$wp_customize->add_setting( 'maera_admin_options[cache_mode]', array(
			'default'        => 'core',
			'type'           => 'option',
			'capability'     => 'edit_theme_options',
			'sanitize_callback' => array( $this, 'sanitize_text_value' ),
		) );

		$wp_customize->add_setting( 'maera_admin_options[reset]', array(
			'default'           => 'core',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => array( $this, 'reset_sanitize_callback' ),
		) );

	}

	/**
	 * Add the controls
	 */
	function add_controls( $wp_customize ) {

		// Get the available shells and format the array properly
		$available_shells = apply_filters( 'maera/shells/available', array() );
		$shells = array();
		foreach ( $available_shells as $available_shell ) {
			$shells[$available_shell['value']] = $available_shell['label'];
		}

		$wp_customize->add_control( 'maera_shell', array(
			'label'       => esc_html__( 'Shell', 'maera' ),
			'section'     => 'maera_options',
			'settings'    => 'maera_admin_options[shell]',
			'description' => esc_html__( 'You can change the active shell here. Please note that the changes will not take effect immediately. You will have to save and your selection and then refresh this page. All current options will be lost, so we advise you to first export them from the "Theme Options" page on your dashboard.', 'maera' ),
			'type'        => 'radio',
			'choices'     => $shells,
		) );

		$wp_customize->add_control( 'maera_dev_mode', array(
			'label'       => esc_html__( 'Enable Development Mode', 'maera' ),
			'section'     => 'maera_options',
			'settings'    => 'maera_admin_options[dev_mode]',
			'type'        => 'checkbox',
		) );

		$wp_customize->add_control( 'maera_cache_mode', array(
			'label'       => esc_html__( 'Cache mode.', 'maera' ),
			'section'     => 'maera_options',
			'settings'    => 'maera_admin_options[cache_mode]',
			'type'        => 'select',
			'default'     => 'none',
			'choices'     => array(
				'none'      => esc_html__( 'No Caching', 'maera' ),
				'object'    => esc_html__( 'WP Object Caching', 'maera' ),
				'transient' => esc_html__( 'Transients', 'maera' ),
				'default'   => esc_html__( 'Default', 'maera' ),
			),
		) );

		$wp_customize->add_control( 'maera_reset', array(
			'label'       => esc_html__( 'Reset', 'maera' ),
			'section'     => 'maera_options',
			'description' => esc_html__( 'Please enter RESET to reset the theme mods.', 'maera' ),
			'settings'    => 'maera_admin_options[reset]',
			'type'        => 'text',
			'default'     => '',
		) );

	}

	public function reset_sanitize_callback( $input ) {

		if ( 'reset' == strtolower( $input ) ) {
			remove_theme_mods();
		}

		return '';

	}

	function sanitize_checkbox( $checked ) {
		return ( ( isset( $checked ) && true == $checked ) ? true : false );
	}

	function sanitize_text_field( $input ) {
		return sanitize_text_field( $input );
	}
}
