<?php
function crispshop_customizer( $wp_customize ) {
	$wp_customize->add_section( 'crispshop_settings', array(
	    'title' => __( 'General Settings', 'crispshop' ),
	    'priority' => 30,
	));

	$wp_customize->add_setting( 'crispshop_logo', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_img_sanitize_fallback',
	));

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'crispshop_logo', array(
	    'label'    => __( 'Logo', 'crispshop' ),
	    'section'  => 'crispshop_settings',
	    'settings' => 'crispshop_logo',
	)));

	$wp_customize->add_setting( 'crispshop_logo_max_width', array(
		'capability' => 'edit_theme_options',
		'default' => '250px',
		'sanitize_callback' => 'crispshop_sanitize_input',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_logo_max_width', array(
        'label'     => __('Logo Max Width', 'crispshop'),
        'description'     => __('In Pixels, for example 250px', 'crispshop'),
        'section'   => 'crispshop_settings',
        'settings'  => 'crispshop_logo_max_width',
        'type'      => 'text',
    )));

	$wp_customize->add_setting( 'crispshop_favicon', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_img_sanitize_fallback',
	));

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'crispshop_favicon', array(
	    'label'    => __( 'Favicon', 'crispshop' ),
	    'section'  => 'crispshop_settings',
	    'settings' => 'crispshop_favicon',
	)));

	$wp_customize->add_setting( 'crispshop_base_color', array(
		'default' => '#ea3a3c',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color',
	));

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'crispshop_base_color', array(
		'label' => __( 'Base Color', 'crispshop' ),
		'settings' => 'crispshop_base_color',
		'section' => 'crispshop_settings',
	)));

	$wp_customize->add_setting( 'crispshop_link_color', array(
		'default' => '#ea3a3c',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color',
	));

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'crispshop_link_color', array(
		'label' => __( 'Default Link Color', 'crispshop' ),
		'settings' => 'crispshop_link_color',
		'section' => 'crispshop_settings',
	)));

	$wp_customize->add_section( 'crispshop_top_bar', array(
	    'title' => __( 'Top Bar Settings', 'crispshop' ),
	    'priority' => 31,
	));

	$wp_customize->add_setting( 'crispshop_top_bar_display', array(
		'capability' => 'edit_theme_options',
		'default' => false,
		'sanitize_callback' => 'crispshop_sanitize_checkbox',
	));

	$wp_customize->add_control( 'crispshop_top_bar_display', array(
		'type' => 'checkbox',
		'settings' => 'crispshop_top_bar_display',
		'section' => 'crispshop_top_bar',
		'label' => __( 'Hide Top Bar' ),
	));

	$wp_customize->add_setting( 'crispshop_top_bg', array(
		'default' => '#f1f1f1',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color',
	));

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'crispshop_top_bg', array(
		'label' => __( 'Background', 'crispshop' ),
		'settings' => 'crispshop_top_bg',
		'section' => 'crispshop_top_bar',
	)));

	$wp_customize->add_setting( 'crispshop_top_font_color', array(
		'default' => '#666',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color',
	));

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'crispshop_top_font_color', array(
		'label' => __( 'Font Color', 'crispshop' ),
		'settings' => 'crispshop_top_font_color',
		'section' => 'crispshop_top_bar',
	)));

	$wp_customize->add_setting( 'crispshop_phone_number', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_input',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_phone_number', array(
        'label'     => __('Phone Number', 'crispshop'),
        'section'   => 'crispshop_top_bar',
        'settings'  => 'crispshop_phone_number',
        'type'      => 'text',
    )));

    $wp_customize->add_section( 'crispshop_menu_settings', array(
	    'title' => __( 'Menu Color Settings', 'crispshop' ),
	    'priority' => 31,
	));

	$wp_customize->add_setting( 'crispshop_menu_color', array(
		'default' => '#444',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color',
	));

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'crispshop_menu_color', array(
		'label' => __( 'Menu Color', 'crispshop' ),
		'settings' => 'crispshop_menu_color',
		'section' => 'crispshop_menu_settings',
	)));

	$wp_customize->add_setting( 'crispshop_menu_hover_color', array(
		'default' => '#ea3a3c',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color',
	));

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'crispshop_menu_hover_color', array(
		'label' => __( 'Menu Hover Color', 'crispshop' ),
		'settings' => 'crispshop_menu_hover_color',
		'section' => 'crispshop_menu_settings',
	)));

	$wp_customize->add_section( 'crispshop_font', array(
	    'title' => __( 'Font Settings', 'crispshop' ),
	    'priority' => 31,
	));

	$wp_customize->add_setting( 'crispshop_site_font', array(
		'default' => 'open_sans',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_fallback',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_site_font', array(
	    'label' => __( 'Site Font', 'crispshop' ),
	    'section' => 'crispshop_font',
	    'settings' => 'crispshop_site_font',
	    'type' => 'select',
	    'choices' => array(
	        'droid_sans' => __( 'Droid Sans', 'crispshop' ),
	        'open_sans' => __( 'Open Sans', 'crispshop' ),
	        'oswald' => __( 'Oswald', 'crispshop' ),
	        'pt_sans' => __( 'PT Sans', 'crispshop' ),
	        'lato' => __( 'Lato', 'crispshop' ),
	        'raleway' => __( 'Raleway', 'crispshop' ),
	        'ubuntu' => __( 'Ubuntu', 'crispshop' )
	    )
	)));

	$wp_customize->add_setting( 'crispshop_site_font_size', array(
		'default' => '14px',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_fallback',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_site_font_size', array(
	    'label' => __( 'Site Font Size', 'crispshop' ),
	    'section' => 'crispshop_font',
	    'settings' => 'crispshop_site_font_size',
	    'type' => 'select',
	    'choices' => array(
	    	'12px' => __( '12px', 'crispshop' ),
	        '13px' => __( '13px', 'crispshop' ),
			'14px' => __( '14px', 'crispshop' ),
			'15px' => __( '15px', 'crispshop' ),
			'16px' => __( '16px', 'crispshop' ),
			'17px' => __( '17px', 'crispshop' ),
			'18px' => __( '18px', 'crispshop' ),
			'19px' => __( '19px', 'crispshop' ),
			'20px' => __( '20px', 'crispshop' )
	    )
	)));

	$wp_customize->add_setting( 'crispshop_site_font_color', array(
		'default' => '#111',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color',
	));

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'crispshop_site_font_color', array(
		'label' => __( 'Site Font Color', 'crispshop' ),
		'settings' => 'crispshop_site_font_color',
		'section' => 'crispshop_font',
	)));

	$wp_customize->add_setting( 'crispshop_site_font_style', array(
		'default' => '400',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_fallback',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_site_font_style', array(
	    'label' => __( 'Site Font Style', 'crispshop' ),
	    'section' => 'crispshop_font',
	    'settings' => 'crispshop_site_font_style',
	    'type' => 'select',
	    'choices' => array(
	    	'300' => __( 'Light', 'crispshop' ),
	        '400' => __( 'Normal', 'crispshop' ),
			'700' => __( 'Bold', 'crispshop' )
	    )
	)));

	$wp_customize->add_section( 'crispshop_hfont', array(
	    'title' => __( 'Header Font Settings', 'crispshop' ),
	    'priority' => 31,
	));

	$wp_customize->add_setting( 'crispshop_site_hfont', array(
		'default' => 'open_sans',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_fallback',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_site_hfont', array(
	    'label' => __( 'Heading Font', 'crispshop' ),
	    'section' => 'crispshop_hfont',
	    'settings' => 'crispshop_site_hfont',
	    'type' => 'select',
	    'choices' => array(
	        'droid_sans' => __( 'Droid Sans', 'crispshop' ),
	        'open_sans' => __( 'Open Sans', 'crispshop' ),
	        'oswald' => __( 'Oswald', 'crispshop' ),
	        'pt_sans' => __( 'PT Sans', 'crispshop' ),
	        'lato' => __( 'Lato', 'crispshop' ),
	        'raleway' => __( 'Raleway', 'crispshop' ),
	        'ubuntu' => __( 'Ubuntu', 'crispshop' )
	    )
	)));

	$wp_customize->add_setting( 'crispshop_site_hfont_color', array(
		'default' => '#111',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color',
	));

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'crispshop_site_hfont_color', array(
		'label' => __( 'Heading Font Color', 'crispshop' ),
		'settings' => 'crispshop_site_hfont_color',
		'section' => 'crispshop_hfont',
	)));

	$wp_customize->add_setting( 'crispshop_site_hfont_style', array(
		'default' => '700',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_fallback',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_site_hfont_style', array(
	    'label' => __( 'Heading Font Style', 'crispshop' ),
	    'section' => 'crispshop_hfont',
	    'settings' => 'crispshop_site_hfont_style',
	    'type' => 'select',
	    'choices' => array(
	    	'300' => __( 'Light', 'crispshop' ),
	        '400' => __( 'Normal', 'crispshop' ),
			'700' => __( 'Bold', 'crispshop' )
	    )
	)));

	$wp_customize->add_setting( 'crispshop_site_hfont1_size', array(
		'default' => '28px',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_fallback',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_site_hfont1_size', array(
	    'label' => __( 'H1 Font Size', 'crispshop' ),
	    'section' => 'crispshop_hfont',
	    'settings' => 'crispshop_site_hfont1_size',
	    'type' => 'select',
	    'choices' => array(
	    	'16px' => __( '16px', 'crispshop' ),
			'18px' => __( '18px', 'crispshop' ),
			'20px' => __( '20px', 'crispshop' ),
			'22px' => __( '22px', 'crispshop' ),
			'24px' => __( '24px', 'crispshop' ),
			'26px' => __( '26px', 'crispshop' ),
			'28px' => __( '28px', 'crispshop' ),
			'30px' => __( '30px', 'crispshop' ),
			'32px' => __( '32px', 'crispshop' ),
			'34px' => __( '34px', 'crispshop' ),
			'36px' => __( '36px', 'crispshop' )
	    )
	)));

	$wp_customize->add_setting( 'crispshop_site_hfont2_size', array(
		'default' => '24px',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_fallback',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_site_hfont2_size', array(
	    'label' => __( 'H2 Font Size', 'crispshop' ),
	    'section' => 'crispshop_hfont',
	    'settings' => 'crispshop_site_hfont2_size',
	    'type' => 'select',
	    'choices' => array(
	    	'16px' => __( '16px', 'crispshop' ),
			'18px' => __( '18px', 'crispshop' ),
			'20px' => __( '20px', 'crispshop' ),
			'22px' => __( '22px', 'crispshop' ),
			'24px' => __( '24px', 'crispshop' ),
			'26px' => __( '26px', 'crispshop' ),
			'28px' => __( '28px', 'crispshop' ),
			'30px' => __( '30px', 'crispshop' ),
			'32px' => __( '32px', 'crispshop' ),
			'34px' => __( '34px', 'crispshop' ),
			'36px' => __( '36px', 'crispshop' )
	    )
	)));

	$wp_customize->add_setting( 'crispshop_site_hfont3_size', array(
		'default' => '22px',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_fallback',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_site_hfont3_size', array(
	    'label' => __( 'H3 Font Size', 'crispshop' ),
	    'section' => 'crispshop_hfont',
	    'settings' => 'crispshop_site_hfont3_size',
	    'type' => 'select',
	    'choices' => array(
	    	'16px' => __( '16px', 'crispshop' ),
			'18px' => __( '18px', 'crispshop' ),
			'20px' => __( '20px', 'crispshop' ),
			'22px' => __( '22px', 'crispshop' ),
			'24px' => __( '24px', 'crispshop' ),
			'26px' => __( '26px', 'crispshop' ),
			'28px' => __( '28px', 'crispshop' ),
			'30px' => __( '30px', 'crispshop' ),
			'32px' => __( '32px', 'crispshop' ),
			'34px' => __( '34px', 'crispshop' ),
			'36px' => __( '36px', 'crispshop' )
	    )
	)));

	$wp_customize->add_setting( 'crispshop_site_hfont4_size', array(
		'default' => '18px',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_fallback',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_site_hfont4_size', array(
	    'label' => __( 'H4 Font Size', 'crispshop' ),
	    'section' => 'crispshop_hfont',
	    'settings' => 'crispshop_site_hfont4_size',
	    'type' => 'select',
	    'choices' => array(
	    	'12px' => __( '12px', 'crispshop' ),
			'14px' => __( '14px', 'crispshop' ),
	    	'16px' => __( '16px', 'crispshop' ),
			'18px' => __( '18px', 'crispshop' ),
			'20px' => __( '20px', 'crispshop' ),
			'22px' => __( '22px', 'crispshop' ),
			'24px' => __( '24px', 'crispshop' ),
			'26px' => __( '26px', 'crispshop' ),
			'28px' => __( '28px', 'crispshop' ),
			'30px' => __( '30px', 'crispshop' ),
			'32px' => __( '32px', 'crispshop' )
	    )
	)));

	$wp_customize->add_setting( 'crispshop_site_hfont5_size', array(
		'default' => '14px',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_fallback',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_site_hfont5_size', array(
	    'label' => __( 'H5 Font Size', 'crispshop' ),
	    'section' => 'crispshop_hfont',
	    'settings' => 'crispshop_site_hfont5_size',
	    'type' => 'select',
	    'choices' => array(
	    	'12px' => __( '12px', 'crispshop' ),
			'14px' => __( '14px', 'crispshop' ),
	    	'16px' => __( '16px', 'crispshop' ),
			'18px' => __( '18px', 'crispshop' ),
			'20px' => __( '20px', 'crispshop' ),
			'22px' => __( '22px', 'crispshop' ),
			'24px' => __( '24px', 'crispshop' ),
			'26px' => __( '26px', 'crispshop' ),
			'28px' => __( '28px', 'crispshop' ),
			'30px' => __( '30px', 'crispshop' ),
			'32px' => __( '32px', 'crispshop' )
	    )
	)));

	$wp_customize->add_setting( 'crispshop_site_hfont6_size', array(
		'default' => '12px',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_fallback',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_site_hfont6_size', array(
	    'label' => __( 'H6 Font Size', 'crispshop' ),
	    'section' => 'crispshop_hfont',
	    'settings' => 'crispshop_site_hfont6_size',
	    'type' => 'select',
	    'choices' => array(
	    	'12px' => __( '12px', 'crispshop' ),
			'14px' => __( '14px', 'crispshop' ),
	    	'16px' => __( '16px', 'crispshop' ),
			'18px' => __( '18px', 'crispshop' ),
			'20px' => __( '20px', 'crispshop' ),
			'22px' => __( '22px', 'crispshop' ),
			'24px' => __( '24px', 'crispshop' ),
			'26px' => __( '26px', 'crispshop' ),
			'28px' => __( '28px', 'crispshop' ),
			'30px' => __( '30px', 'crispshop' ),
			'32px' => __( '32px', 'crispshop' )
	    )
	)));

	$wp_customize->add_section( 'crispshop_social_media', array(
	    'title' => __( 'Social Media Settings', 'crispshop' ),
	    'priority' => 33,
	));

	$wp_customize->add_setting( 'crispshop_sharing', array(
		'default' => '1',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_checkbox',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_sharing', array(
        'label'     => __('Enable Sharing', 'crispshop'),
        'section'   => 'crispshop_social_media',
        'settings'  => 'crispshop_sharing',
        'type'      => 'checkbox',
    )));

    $wp_customize->add_setting( 'crispshop_facebook', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_url',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_facebook', array(
        'label'     => __('Facebook URL', 'crispshop'),
        'section'   => 'crispshop_social_media',
        'settings'  => 'crispshop_facebook',
        'type'      => 'text',
    )));

    $wp_customize->add_setting( 'crispshop_twitter', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_url',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_twitter', array(
        'label'     => __('Twitter URL', 'crispshop'),
        'section'   => 'crispshop_social_media',
        'settings'  => 'crispshop_twitter',
        'type'      => 'text',
    )));

    $wp_customize->add_setting( 'crispshop_gplus', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_url',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_gplus', array(
        'label'     => __('Google+ URL', 'crispshop'),
        'section'   => 'crispshop_social_media',
        'settings'  => 'crispshop_gplus',
        'type'      => 'text',
    )));

    $wp_customize->add_setting( 'crispshop_instagram', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_url',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_instagram', array(
        'label'     => __('Instragram URL', 'crispshop'),
        'section'   => 'crispshop_social_media',
        'settings'  => 'crispshop_instagram',
        'type'      => 'text',
    )));

    $wp_customize->add_setting( 'crispshop_pinterest', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_url',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_pinterest', array(
        'label'     => __('Pinterest URL', 'crispshop'),
        'section'   => 'crispshop_social_media',
        'settings'  => 'crispshop_pinterest',
        'type'      => 'text',
    )));

    $wp_customize->add_setting( 'crispshop_youtube', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_url',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_youtube', array(
        'label'     => __('YouTube URL', 'crispshop'),
        'section'   => 'crispshop_social_media',
        'settings'  => 'crispshop_youtube',
        'type'      => 'text',
    )));

    $wp_customize->add_section( 'crispshop_home_page', array(
	    'title' => __( 'Home Page', 'crispshop' ),
	    'priority' => 33,
	));

	$wp_customize->add_setting( 'crispshop_home_banner', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_img_sanitize_fallback',
	));

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'crispshop_home_banner', array(
	    'label'    => __( 'Banner Image', 'crispshop' ),
	    'section'  => 'crispshop_home_page',
	    'settings' => 'crispshop_home_banner',
	)));

	$wp_customize->add_setting( 'crispshop_banner_link', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_url',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_banner_link', array(
        'label'     => __('Banner Link', 'crispshop'),
        'section'   => 'crispshop_home_page',
        'settings'  => 'crispshop_banner_link',
        'type'      => 'text',
    )));

    $wp_customize->add_setting( 'crispshop_home_banner_mobile', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_img_sanitize_fallback',
	));

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'crispshop_home_banner_mobile', array(
	    'label'    => __( 'Banner Image (Mobile)', 'crispshop' ),
	    'section'  => 'crispshop_home_page',
	    'settings' => 'crispshop_home_banner_mobile',
	)));

	$wp_customize->add_setting( 'crispshop_banner_link_mobile', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_url',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_banner_link_mobile', array(
        'label'     => __('Banner Link (Mobile)', 'crispshop'),
        'section'   => 'crispshop_home_page',
        'settings'  => 'crispshop_banner_link_mobile',
        'type'      => 'text',
    )));

    $wp_customize->add_section( 'crispshop_contact_page', array(
	    'title' => __( 'Contact Page', 'crispshop' ),
	    'priority' => 33,
	));

	$wp_customize->add_setting( 'crispshop_contact_email', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_input',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_contact_email', array(
        'label'     => __('Email Address', 'crispshop'),
        'description' => __('Contact form emails will be sent to this email.', 'crispshop'),
        'section'   => 'crispshop_contact_page',
        'settings'  => 'crispshop_contact_email',
        'type'      => 'text',
    )));

    $wp_customize->add_setting( 'crispshop_contact_fax', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_input',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_contact_fax', array(
        'label'     => __('FAX', 'crispshop'),
        'section'   => 'crispshop_contact_page',
        'settings'  => 'crispshop_contact_fax',
        'type'      => 'text',
    )));

    $wp_customize->add_setting( 'crispshop_contact_address', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_input',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_contact_address', array(
        'label'     => __('Address', 'crispshop'),
        'section'   => 'crispshop_contact_page',
        'settings'  => 'crispshop_contact_address',
        'type'      => 'text',
    )));

    $wp_customize->add_setting( 'crispshop_contact_map', array(
		'capability' => 'edit_theme_options',
		'default' => false,
		'sanitize_callback' => 'crispshop_sanitize_checkbox',
	));

	$wp_customize->add_control( 'crispshop_contact_map', array(
		'type' => 'checkbox',
		'settings' => 'crispshop_contact_map',
		'section' => 'crispshop_contact_page',
		'label' => __( 'Hide Map' ),
	));

    $wp_customize->add_section( 'crispshop_footer_settings', array(
	    'title' => __( 'Footer Settings', 'crispshop' ),
	    'priority' => 33,
	));

	$wp_customize->add_setting( 'crispshop_footer_bg', array(
		'default' => '#222',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color',
	));

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'crispshop_footer_bg', array(
		'label' => __( 'Footer Background', 'crispshop' ),
		'settings' => 'crispshop_footer_bg',
		'section' => 'crispshop_footer_settings',
	)));

	$wp_customize->add_setting( 'crispshop_footer_text', array(
		'default' => '#fff',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color',
	));

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'crispshop_footer_text', array(
		'label' => __( 'Footer Font Color', 'crispshop' ),
		'settings' => 'crispshop_footer_text',
		'section' => 'crispshop_footer_settings',
	)));

	$wp_customize->add_setting( 'crispshop_footer_logo', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_img_sanitize_fallback',
	));

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'crispshop_footer_logo', array(
	    'label'    => __( 'Footer Logo', 'crispshop' ),
	    'settings' => 'crispshop_footer_logo',
		'section' => 'crispshop_footer_settings',
	)));

	$wp_customize->add_setting( 'crispshop_footer_logo_max_width', array(
		'capability' => 'edit_theme_options',
		'default' => '150px',
		'sanitize_callback' => 'crispshop_sanitize_input',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_footer_logo_max_width', array(
        'label'     => __('Logo Max Width', 'crispshop'),
        'description'     => __('In Pixels, for example 150px', 'crispshop'),
        'section'   => 'crispshop_footer_settings',
        'settings'  => 'crispshop_footer_logo_max_width',
        'type'      => 'text',
    )));

	$wp_customize->add_setting( 'crispshop_footer_intro', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'crispshop_sanitize_input',
	));

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'crispshop_footer_intro', array(
        'label'     => __('Footer Intro', 'crispshop'),
        'settings' => 'crispshop_footer_intro',
		'section' => 'crispshop_footer_settings',
        'type'      => 'textarea',
    )));
}

add_action('customize_register','crispshop_customizer');

function crispshop_sanitize_input( $input ) {
    return esc_html( $input );
}

function crispshop_sanitize_checkbox( $input ) {
	return ( $input === true ) ? true : false;
}

function crispshop_sanitize_url( $input ) {
	return esc_url_raw( $input );
}

function crispshop_img_sanitize_fallback( $image, $setting ) {
	$mimes = array(
        'jpg|jpeg|jpe' => 'image/jpeg',
        'gif'          => 'image/gif',
        'png'          => 'image/png',
        'bmp'          => 'image/bmp',
        'tif|tiff'     => 'image/tiff',
        'ico'          => 'image/x-icon'
    );
	
	$file = wp_check_filetype( $image, $mimes );
	return ( $file['ext'] ? $image : $setting->default );
}
?>