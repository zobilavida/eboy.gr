<?php
$aConfigureMailchimp = array();

return array(
    'menu_name' => esc_html__('Theme Options', 'listgo'),
    'menu_slug' => 'wiloke',
    'redux'     => array(
        'args'      => array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name'             => 'wiloke_options',
                // This is where your data is stored in the database and also becomes your global variable name.
                'display_name'         => 'wiloke',
                // Name that appears at the top of your panel
                'display_version'      => WILOKE_THEMEVERSION,
                // Version that appears at the top of your panel
                'menu_type'            => 'submenu',
                //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu'       => false,
                // Show the sections below the admin menu item or not
                'menu_title'           => esc_html__( 'Theme Options', 'listgo' ),
                'page_title'           => esc_html__( 'Theme Options', 'listgo' ),
                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key'       => '',
                // Set it you want google fonts to update weekly. A google_api_key value is required.
                'google_update_weekly' => false,
                // Must be defined to add google fonts to the typography module
                'async_typography'     => true,
                // Use a asynchronous font on the front end or font string
                //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
                'admin_bar'            => true,
                // Show the panel pages on the admin bar
                'admin_bar_icon'     => 'dashicons-portfolio',
                // Choose an icon for the admin bar menu
                'admin_bar_priority' => 50,
                // Choose an priority for the admin bar menu
                'global_variable'      => '',
                // Set a different name for your global variable other than the opt_name
                'dev_mode'             => WP_DEBUG ? true : false,
                // Show the time the page took to load, etc
                'update_notice'        => true,
                // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
                'customizer'           => false,
                // Enable basic customizer support
                //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
                //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

                // OPTIONAL -> Give you extra features
                'page_priority'        => null,
                // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent'          => 'themes.php',
                // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions'     => 'manage_options',
                // Permissions needed to access the options panel.
                'menu_icon'            => '',
                // Specify a custom URL to an icon
                'last_tab'             => '',
                // Force your panel to always open to a specific tab (by id)
                'page_icon'            => 'icon-themes',
                // Icon displayed in the admin panel next to your menu_title
                'page_slug'            => '',
                // Page slug used to denote the panel, will be based off page title then menu title then opt_name if not provided
                'save_defaults'        => true,
                // On load save the defaults to DB before user clicks save or not
                'default_show'         => false,
                // If true, shows the default value next to each field that is not the default value.
                'default_mark'         => '',
                // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export'   => true,
                // Shows the Import/Export panel when not used as a field.

                // CAREFUL -> These options are for advanced use only
                'transient_time'       => 60 * MINUTE_IN_SECONDS,
                'output'               => true,
                // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag'           => true,
                // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database'             => '',
                // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'system_info'          => false,
                // REMOVE

                // HINTS
                'hints'                => array(
                    'icon'          => 'el el-question-sign',
                    'icon_position' => 'right',
                    'icon_color'    => 'lightgray',
                    'icon_size'     => 'normal',
                    'tip_style'     => array(
                        'color'   => 'light',
                        'shadow'  => true,
                        'rounded' => false,
                        'style'   => '',
                    ),
                    'tip_position'  => array(
                        'my' => 'top left',
                        'at' => 'bottom right',
                    ),
                    'tip_effect'    => array(
                        'show' => array(
                            'effect'   => 'slide',
                            'duration' => '500',
                            'event'    => 'mouseover',
                        ),
                        'hide' => array(
                            'effect'   => 'slide',
                            'duration' => '500',
                            'event'    => 'click mouseleave',
                        ),
                    ),
                )
        ),
        'sections'  => array(
            // General Settings
            array(
                'title'            => esc_html__('General', 'listgo'),
                'id'               => 'general_settings',
                'subsection'       => false,
                'customizer_width' => '500px',
                'pages'            => array('page'),
                'fields'           => array(
                    array(
                        'id'       => 'general_logo',
                        'type'     => 'media',
                        'title'    => esc_html__( 'Logo', 'listgo'),
                        'subtitle' => esc_html__( 'Upload a logo for this site. This logo will be displayed at the top right of header.', 'listgo'),
                        'default'  => array(
                            'url'  => WILOKE_THEME_URI . 'img/logo.png'
                        )
                    ),
                    array(
                        'id'       => 'general_retina_logo',
                        'type'     => 'media',
                        'title'    => esc_html__( 'Retina Logo', 'listgo'),
                        'subtitle' => esc_html__( 'Upload a logo for retina-display devices.', 'listgo'),
                        'default'  => ''
                    ),
                    array(
                        'id'       => 'general_favicon',
                        'type'     => 'media',
                        'title'    => esc_html__( 'Upload favicon', 'listgo'),
                        'default'  => ''
                    ),
	                array(
		                'id'       => 'general_menu_mobile_at',
		                'type'     => 'text',
		                'title'    => esc_html__( 'Mobile Menu', 'listgo'),
		                'subtitle' => esc_html__( 'The menu will automatically switch to Mobile style if the screen is smaller  than or enqual to x px', 'listgo'),
		                'default'  => 1024
	                ),
	                array(
                        'id'       => 'is_preloader',
                        'type'     => 'select',
                        'title'    => esc_html__( 'Enable Pre-loader', 'listgo'),
                        'default'  => 'no',
                        'options'  => array(
                            'yes' => esc_html__('Yes', 'listgo'),
                            'no'  => esc_html__('Thanks, but no thanks', 'listgo')
                        )
                    ),
	                array(
		                'type'         => 'text',
		                'id'           => 'general_content_limit',
		                'title'        => esc_html__('Excerpt Length', 'listgo'),
		                'default'      => 115
	                ),
	                array(
		                'type'         => 'media',
		                'id'           => 'general_pagenotfound_bg',
		                'title'        => esc_html__('404 Background Image', 'listgo'),
		                'default'      => ''
	                ),
                    array(
                        'id'    => 'open_map_settings_section',
                        'type'  => 'section',
                        'indent'=> true,
                        'title' => esc_html__('Map General Settings', 'listgo'),
	                    'subtitle' => esc_html__('If you are using any cache plugin, You need to flush cache after the settings is changed.', 'listgo')
                    ),
                    array(
                        'type'         => 'text',
                        'id'           => 'general_map_api',
                        'title'        => esc_html__('Google Map API (*)', 'listgo'),
                        'subtitle'     => Wiloke::wiloke_kses_simple_html( __('It is required if you wanna use Google MAP. Please go to this link to generate your <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">Google API key</a>. Once the Google Map is activated, make sure that the following things are enabled <a href="https://landing.wiloke.com/listgo/wiloke-guide/enable-google-api.png"></a>', 'listgo'), true),
                        'default'      => ''
                    ),
	                array(
		                'type'         => 'text',
		                'id'           => 'general_mapbox_api',
		                'title'        => esc_html__('MapBox Token (*)', 'listgo'),
		                'subtitle'     => Wiloke::wiloke_kses_simple_html( __('It is required if you wanna use Map Shortcode. Please go to this link to generate your <a href="https://www.mapbox.com/studio/account/tokens" target="_blank">Mapbox Token</a>', 'listgo'), true),
		                'default'      => ''
	                ),
	                array(
		                'type'         => 'text',
		                'id'           => 'general_map_theme',
		                'title'        => esc_html__('MapBox Theme (*)', 'listgo'),
		                'subtitle'     => Wiloke::wiloke_kses_simple_html(__('This setting is required. Watch this video <a href="https://www.youtube.com/watch?v=-3tdKCxpWIY">How to create a Mapbox Theme?</a> to know more.', 'listgo'), true),
		                'default'      => ''
	                ),
	                array(
		                'id'    => 'close_map_general_settings_section',
		                'type'  => 'section',
		                'indent'=> false
	                ),
	                array(
		                'id'    => 'open_map_template_section',
		                'type'  => 'section',
		                'indent'=> true,
		                'title' => esc_html__('Map Template Settings and Map Shortcode Settings', 'listgo')
	                ),
                    array(
                        'type'         => 'text',
                        'id'           => 'general_map_max_zoom',
                        'title'        => esc_html__('Map Maximum Zoom (Desktop)', 'listgo'),
                        'default'      => 4
                    ),
                    array(
                        'type'         => 'text',
                        'id'           => 'general_map_min_zoom',
                        'title'        => esc_html__('Map Minimum Zoom (Desktop)', 'listgo'),
                        'desc'         => esc_html__('A negative number is allowable', 'listgo'),
                        'default'      => -1
                    ),
	                array(
		                'type'         => 'text',
		                'id'           => 'general_map_center_zoom',
		                'title'        => esc_html__('Map Center Zoom', 'listgo'),
		                'default'      => 10
	                ),
                    array(
                        'type'         => 'text',
                        'id'           => 'general_map_cluster_radius',
                        'title'        => esc_html__('Map Cluster Radius', 'listgo'),
                        'subtitle'     => Wiloke::wiloke_kses_simple_html(__('The maximum radius that a cluster will cover from the central marker', 'listgo'), true),
                        'default'      => 60
                    ),
                    array(
                        'id'    => 'close_map_template_section',
                        'type'  => 'section',
                        'indent'=> false
                    ),
	                array(
		                'id'    => 'open_map_single_settings',
		                'type'  => 'section',
		                'indent'=> true,
		                'title' => esc_html__('Map on Single Listing settings', 'listgo'),
	                ),
	                array(
		                'type'         => 'text',
		                'id'           => 'general_map_single_max_zoom',
		                'title'        => esc_html__('Map Maximum Zoom (Desktop)', 'listgo'),
		                'default'      => 10
	                ),
	                array(
		                'type'         => 'text',
		                'id'           => 'general_map_single_min_zoom',
		                'title'        => esc_html__('Map Minimum Zoom (Desktop)', 'listgo'),
		                'desc'         => esc_html__('A negative number is allowable', 'listgo'),
		                'default'      => -1
	                ),
	                array(
		                'type'         => 'text',
		                'id'           => 'general_map_single_center_zoom',
		                'title'        => esc_html__('Map Center Zoom', 'listgo'),
		                'default'      => 5
	                ),
	                array(
		                'id'    => 'close_map_single_settings',
		                'type'  => 'section',
		                'indent'=> false
	                ),
                )
            ),

            array(
	            'title'            => esc_html__('Header', 'listgo'),
	            'id'               => 'header_settings',
	            'subsection'       => false,
	            'customizer_width' => '500px',
	            'icon'             => 'dashicons dashicons-align-right',
	            'fields'           => array(
		            array(
			            'id'    => 'open_header_image_section',
			            'type'  => 'section',
			            'indent'=> true,
			            'title' => esc_html__('Header Image', 'listgo'),
		            ),
		            array(
			            'id'        => 'blog_header_image',
			            'type'      => 'media',
			            'title'     => esc_html__('Blog Page', 'listgo'),
			            'description'  => esc_html__('This image will be used on blog page, post page, category page and archive page. But if a post / a category / a tag is EXISTING a Featured Image, that image will be used instead. In other word, You can override this image.', 'listgo'),
			            'default'   => ''
		            ),
		            array(
			            'id'        => 'blog_header_overlay',
			            'type'      => 'color_rgba',
			            'title'     => esc_html__('Blog Overlay Color', 'listgo'),
			            'default'   => ''
		            ),
		            array(
			            'id'        => 'listing_header_image',
			            'type'      => 'media',
			            'title'     => esc_html__('Listing Page', 'listgo'),
			            'description'  => esc_html__('If the featured image is empty, this image will be used.', 'listgo'),
			            'default'   => ''
		            ),
		            array(
			            'id'        => 'listing_header_overlay',
			            'type'      => 'color_rgba',
			            'title'     => esc_html__('Listing Overlay Color', 'listgo'),
			            'default'   => ''
		            ),
		            array(
			            'id'        => 'woocommerce_header_image',
			            'type'      => 'media',
			            'title'     => esc_html__('WooCommerce Page', 'listgo'),
			            'description'  => esc_html__('This image will be used on wooocommerce pages', 'listgo'),
			            'default'   => ''
		            ),
		            array(
			            'id'        => 'woocommerce_header_overlay',
			            'type'      => 'color_rgba',
			            'title'     => esc_html__('WooCommerce Overlay Color', 'listgo'),
			            'default'   => ''
		            ),
		            array(
			            'id'    => 'close_header_image_section',
			            'type'  => 'section',
			            'indent'=> false
		            ),
		            array(
			            'id'        => 'header_nav_style',
			            'type'      => 'select',
			            'title'     => esc_html__('Navigation Background Color', 'listgo'),
			            'description' => esc_html__('Note that some special pages such as Listing Creative Layout always keep Transparent Background.', 'listgo'),
			            'options'     => array(
			            	'header--transparent' => esc_html__('Transparent', 'listgo'),
			            	'header--background'  => esc_html__('Black', 'listgo'),
			            	'header--custombg'    => esc_html__('Custom Background', 'listgo'),
			            ),
			            'default'   => 'header--background'
		            ),
		            array(
			            'id'        => 'header_custom_nav_bg',
			            'type'      => 'color_rgba',
			            'required'   => array('header_nav_style', '=', 'header--custombg'),
			            'title'     => esc_html__('Custom Background Color', 'listgo'),
			            'default'   => ''
		            ),
		            array(
			            'id'        => 'header_custom_nav_color',
			            'type'      => 'color_rgba',
			            'required'   => array('header_nav_style', '=', 'header--custombg'),
			            'title'     => esc_html__('Custom Menu Item Color', 'listgo'),
			            'default'   => ''
		            ),
	            )
            ),

            // Login Register Review
	        array(
		        'title'            => esc_html__('Sign Up, Sign in, Review', 'listgo'),
		        'id'               => 'sign_up_sign_in_review',
		        'subsection'       => false,
		        'customizer_width' => '500px',
		        'icon'             => 'dashicons dashicons-smiley',
		        'fields'           => array(
			        array(
				        'id'        => 'sign_in_desc',
				        'type'      => 'textarea',
				        'title'     => esc_html__('Sign Up Description', 'listgo'),
				        'default'   => __('By creating an account you agree to our <a href="#">Terms and Conditions</a> and our <a href="#">Privacy Policy</a>', 'listgo')
			        ),
			        array(
				        'id'        => 'toggle_google_recaptcha',
				        'type'      => 'select',
				        'title'     => esc_html__('Toggle Google reCAPTCHA', 'listgo'),
				        'options'   => array(
					        'disable'  => esc_html__('Disable', 'listgo'),
					        'enable'   => esc_html__('Enable', 'listgo')
				        ),
				        'default'   => 'disable'
			        ),
			        array(
				        'id'        => 'google_recaptcha_site_key',
				        'type'      => 'text',
				        'required'  => array('toggle_google_recaptcha', '=', 'enable'),
				        'title'     => esc_html__('Google reCAPTCHA - Site Key', 'listgo'),
				        'description'=>__('<a href="https://blog.wiloke.com/get-google-recaptcha-keys/" target="_blank">How to get Google reCAPTCHA keys?</a>', 'listgo'),
				        'default'   => ''
			        ),
			        array(
				        'id'        => 'google_recaptcha_site_secret',
				        'type'      => 'text',
				        'required'  => array('toggle_google_recaptcha', '=', 'enable'),
				        'title'     => esc_html__('Google reCAPTCHA - Secret Key', 'listgo'),
				        'description'=>__('<a href="https://blog.wiloke.com/get-google-recaptcha-keys/" target="_blank">How to get Google reCAPTCHA keys?</a>', 'listgo'),
				        'default'   => ''
			        ),
		        )
	        ),
            // Sidebar
            array(
                'title'            => esc_html__('Sidebar', 'listgo'),
                'id'               => 'sidebar_settings',
                'subsection'       => false,
                'customizer_width' => '500px',
                'icon'             => 'dashicons dashicons-align-right',
                'fields'           => array(
                    array(
                        'id'        => 'blog_sidebar',
                        'type'      => 'select',
                        'options'   => array(
                            'left'  => esc_html__('Left Sidebar', 'listgo'),
                            'right' => esc_html__('Right Sidebar', 'listgo'),
                            'no'    => esc_html__('No Sidebar', 'listgo'),
                        ),
                        'default'   => 'right',
                        'title'     => esc_html__('Blog Sidebar', 'listgo')
                    ),
	                array(
		                'id'        => 'blog_sidebar_style',
		                'type'      => 'select',
		                'options'   => array(
			                'sidebar'            => esc_html__('All widgets in a box', 'listgo'),
			                'sidebar-background' => esc_html__('Each item separated by a box', 'listgo')
		                ),
		                'default'   => 'sidebar',
		                'title'     => esc_html__('Blog Sidebar style ', 'listgo'),
		                'description'=> esc_html__('This setting will be used on the category page, tag page, post page', 'listgo')
	                ),
                    array(
                        'id'        => 'page_sidebar',
                        'type'      => 'select',
                        'options'   => array(
                            'left'  => esc_html__('Left Sidebar', 'listgo'),
                            'right' => esc_html__('Right Sidebar', 'listgo'),
                            'no'    => esc_html__('No Sidebar', 'listgo'),
                        ),
                        'default'   => 'no',
                        'title'     => esc_html__('Page Sidebar', 'listgo')
                    ),
                    array(
                        'id'        => 'archive_search_sidebar',
                        'type'      => 'select',
                        'options'   => array(
                            'left'  => esc_html__('Left Sidebar', 'listgo'),
                            'right' => esc_html__('Right Sidebar', 'listgo'),
                            'no'    => esc_html__('No Sidebar', 'listgo'),
                        ),
                        'default'   => 'right',
                        'title'     => esc_html__('Archive, Home, Search ', 'listgo')
                    ),
	                array(
		                'id'        => 'listing_location_category_sidebar',
		                'type'      => 'select',
		                'options'   => array(
			                'left'  => esc_html__('Left Sidebar', 'listgo'),
			                'right' => esc_html__('Right Sidebar', 'listgo'),
			                'no'    => esc_html__('No Sidebar', 'listgo'),
		                ),
		                'default'   => 'right',
		                'title'     => esc_html__('Listing Location Sidebar & Listing Category Sidebar ', 'listgo')
	                ),
	                array(
		                'id'        => 'listing_sidebar_position',
		                'type'      => 'select',
		                'options'   => array(
			                'left'  => esc_html__('Left Sidebar', 'listgo'),
			                'right' => esc_html__('Right Sidebar', 'listgo'),
			                'no'    => esc_html__('No Sidebar', 'listgo'),
		                ),
		                'default'   => 'leff',
		                'title'     => esc_html__('Listing Sidebar Position ', 'listgo'),
		                'description'=> esc_html__('To set the listing sidebar, please go to Appearance -> Widgets -> Dragging widget items into Listing Sidebar area.', 'listgo')
	                ),
	                array(
		                'id'        => 'events_sidebar_position',
		                'type'      => 'select',
		                'options'   => array(
			                'left'  => esc_html__('Left Sidebar', 'listgo'),
			                'right' => esc_html__('Right Sidebar', 'listgo'),
			                'no'    => esc_html__('No Sidebar', 'listgo'),
		                ),
		                'default'   => 'right',
		                'title'     => esc_html__('Event Sidebar', 'listgo')
	                ),
	                array(
                        'id'        => 'woocommerce_sidebar',
                        'type'      => 'select',
                        'options'   => array(
                            'left'  => esc_html__('Left Sidebar', 'listgo'),
                            'right' => esc_html__('Right Sidebar', 'listgo'),
                            'no'    => esc_html__('No Sidebar', 'listgo'),
                        ),
                        'default'   => 'no',
                        'title'     => esc_html__('WooCommerce Sidebar ', 'listgo')
                    ),
	                array(
		                'id'        => 'woocommerce_sidebar_style',
		                'type'      => 'select',
		                'options'   => array(
			                'sidebar'            => esc_html__('All widgets in a box', 'listgo'),
			                'sidebar-background' => esc_html__('Each item separated by a box', 'listgo')
		                ),
		                'default'   => 'sidebar-background',
		                'title'     => esc_html__('WooCommerce Sidebar style ', 'listgo')
	                ),
                )
            ),

            // Blog Single
            array(
                'title'            => esc_html__('Blog', 'listgo'),
                'id'               => 'blog_single',
                'icon'             => 'dashicons dashicons-media-spreadsheet',
                'subsection'       => false,
                'customizer_width' => '500px',
                'fields'           => array(
                    array(
                        'type'        => 'section',
                        'id'          => 'section_blog_section',
                        'title'       => esc_html__('General Settings', 'listgo'),
                        'indent'      => true
                    ),
                    array(
                        'type'        => 'select',
                        'id'          => 'blog_layout',
                        'title'       => esc_html__('Blog Layout', 'listgo'),
                        'options'     => array(
                            'post__grid'      => esc_html__('Grid', 'listgo'),
                            'post__standard'  => esc_html__('Standard', 'listgo')
                        ),
                        'default'     => 'post__standard'
                    ),
	                array(
		                'type'        => 'select',
		                'id'          => 'blog_layout_grid_on_desktops',
		                'title'       => esc_html__('Articles per row on Desktops', 'listgo'),
		                'options'     => array(
			                'col-md-4'      => esc_html__('3 articles / row', 'listgo'),
			                'col-md-3'      => esc_html__('4 articles / row', 'listgo'),
			                'col-md-6'      => esc_html__('2 articles / row', 'listgo')
		                ),
		                'required'    => array('blog_layout', '=', 'post__grid'),
		                'default'     => 'col-md-4'
	                ),
	                array(
		                'type'        => 'select',
		                'id'          => 'blog_layout_grid_on_smalls',
		                'title'       => esc_html__('Articles per row on Tablets', 'listgo'),
		                'options'     => array(
			                'col-sm-6'      => esc_html__('2 articles / row', 'listgo'),
			                'col-sm-4'      => esc_html__('3 articles / row', 'listgo'),
			                'col-sm-3'      => esc_html__('4 articles / row', 'listgo')
		                ),
		                'required'    => array('blog_layout', '=', 'post__grid'),
		                'default'     => 'col-sm-6'
	                ),
                    array(
                        'type'        => 'section',
                        'id'          => 'section_blog_section_close',
                        'title'       => '',
                        'indent'      => false
                    ),
                    array(
                        'type'        => 'section',
                        'id'          => 'section_single_post',
                        'title'       => esc_html__('Article Settings', 'listgo'),
                        'indent'      => true
                    ),
                    array(
                        'type'        => 'select',
                        'id'          => 'single_post_toggle_related_posts',
                        'title'       => esc_html__('Related Posts', 'listgo'),
                        'options'     => array(
                            'enable'  => esc_html__('Enable', 'listgo'),
                            'disable' => esc_html__('Disable', 'listgo')
                        ),
                        'default'     => 'enable'
                    ),
                    array(
                        'type'        => 'text',
                        'id'          => 'single_post_related_posts_title',
                        'title'       => esc_html__('Title', 'listgo'),
                        'default'     => 'You may also like'
                    ),
                    array(
                        'type'        => 'select',
                        'id'          => 'single_post_related_number_of_articles',
                        'title'       => esc_html__('Number of articles', 'listgo'),
                        'options'     => array(
                            'col-md-4'  => esc_html__('3 Articles', 'listgo'),
                            'col-md-6' => esc_html__('2 Articles', 'listgo')
                        ),
                        'default'     => 'col-md-6'
                    )
                ),
            ),

            // Listing Settings
	        array(
	            'title' => esc_html__('Listing Settings', 'listgo'),
	            'icon'             => 'dashicons dashicons-lightbulb',
	            'subsection'       => false,
	            'customizer_width' => '500px',
	            'fields'           => array(
		            array(
			            'title'     => esc_html__('Listing Slug settings', 'listgo'),
			            'id'       => 'open_listing_slug_section',
			            'type'     => 'section',
			            'indent'   => true
		            ),
		            array(
			            'id'      => 'custom_listing_location_slug',
			            'type'    => 'text',
			            'title'   => esc_html__('Listing Location Slug', 'listgo'),
			            'description' => Wiloke::wiloke_kses_simple_html( __('Leave empty to use the default setting. Warning: Please click on Settings -> Permalinks -> Select Post Name and click Save Changes button', 'listgo'), true ),
			            'default' => ''
		            ),
		            array(
			            'id'      => 'custom_listing_cat_slug',
			            'type'    => 'text',
			            'title'   => esc_html__('Listing Category Slug', 'listgo'),
			            'description' => Wiloke::wiloke_kses_simple_html( __('Leave empty to use the default setting. Warning: Please click on Settings -> Reading -> Select Post Name and click Save Changes button', 'listgo'), true ),
			            'default' => ''
		            ),
		            array(
			            'id'      => 'custom_listing_tag_slug',
			            'type'    => 'text',
			            'title'   => esc_html__('Listing Tag Slug', 'listgo'),
			            'description' => Wiloke::wiloke_kses_simple_html( __('Leave empty to use the default setting. Warning: Please click on Settings -> Permalinks -> Select Post Name and click Save Changes button', 'listgo'), true ),
			            'default' => ''
		            ),
		            array(
			            'id'      => 'custom_listing_single_slug',
			            'type'    => 'text',
			            'title'   => esc_html__('Single Listing Slug', 'listgo'),
			            'description' => Wiloke::wiloke_kses_simple_html( __('Leave empty to use the default setting. Warning: Please click on Settings -> Permalinks -> Select Post Name and click Save Changes button', 'listgo'), true ),
			            'default' => ''
		            ),
		            array(
			            'id'       => 'close_listing_slug_section',
			            'type'     => 'section',
			            'indent'   => false
		            ),
		            array(
			            'title'     => esc_html__('Add Listing', 'listgo'),
			            'id'       => 'open_add_listing_page_section',
			            'type'     => 'section',
			            'indent'   => true
		            ),
		            array(
			            'id'        => 'toggle_add_listing_btn_on_mobile',
			            'type'      => 'select',
			            'title'     => esc_html__('Toggle Add Listing Button on the mobile', 'listgo'),
			            'default'   => 'disable',
			            'options'   => array(
				            'disable' => esc_html__('Disable', 'listgo'),
				            'enable'  => esc_html__('Enable', 'listgo')
			            )
		            ),
		            array(
			            'id'        => 'add_listing_select_location_type',
			            'type'      => 'select',
			            'title'     => esc_html__('Select Location Type', 'listgo'),
			            'default'   => 'default',
			            'options'   => array(
				            'default' => esc_html__('Add Locations by admin only', 'listgo'),
				            'google'  => esc_html__('Automatically Add Location By Google', 'listgo')
			            )
		            ),
		            array(
			            'id'       => 'close_add_listing_page_section',
			            'type'     => 'section',
			            'indent'   => false
		            ),
		            array(
			            'title' => esc_html__('Listing Category and Listing Location Settings', 'listgo'),
			            'id'    => 'open_listings_category_and_listing_location_section',
			            'type'  => 'section',
			            'indent'=> true
		            ),
		            array(
			            'id'        => 'listing_taxonomy_layout',
			            'type'      => 'select',
			            'title'     => esc_html__('Listing Category & Listing Location Layout', 'listgo'),
			            'subtitle'  => esc_html__('Set a layout for listing category page & listing location page.', 'listgo'),
			            'default'   => 'listing--list',
			            'options'   => array(
				            'listing--grid'     => esc_html__('Grid', 'listgo'),
				            'listing--grid1'    => esc_html__('Grid 2', 'listgo'),
				            'listing--list'     => esc_html__('List', 'listgo'),
				            'listing--list1'    => esc_html__('List 2', 'listgo'),
				            'circle-thumbnail'  => esc_html__('List Circle Thumbnail (New)', 'listgo'),
				            'creative-rectangle'=> esc_html__('List Creative Rectangle (New)', 'listgo')
			            )
		            ),
		            array(
			            'title' => '',
			            'id'    => 'close_listings_category_and_listing_location_section',
			            'type'  => 'section',
			            'indent'=> false
		            ),
		            array(
			            'title' => esc_html__('Search Form Settings', 'listgo'),
			            'id'    => 'open_search_form_section',
			            'type'  => 'section',
			            'indent'=> true
		            ),
		            array(
			            'id'    => 'listing_search_page',
			            'type'  => 'select',
			            'title' => esc_html__('Search Form Action', 'listgo'),
			            'default' => 'self',
			            'options' => array(
				            'map'    => esc_html__('Redirect To Search page', 'listgo'),
				            'self'   => esc_html__('Show the search results on the self page.', 'listgo'),
			            )
		            ),
		            array(
			            'id'    => 'header_search_map_page',
			            'type'  => 'select',
			            'data'  => 'pages',
			            'default'=> '',
			            'title' => esc_html__('Search Page', 'listgo'),
			            'required' => array('listing_search_page', '=', 'map'),
			            'description' => esc_html__('When user click on Search button, it will redirect to this page. To create a search page, please go to Pages -> Add New -> Set that page as Listing template or Map page Template (This setting should under Page Attributes box)', 'listgo'),
		            ),
		            array(
			            'id'        => 'header_search_keyword_label',
			            'type'      => 'text',
			            'title'     => esc_html__('Label for the keyword field', 'listgo'),
			            'default'   => esc_html__('Address, city or select suggestion category', 'listgo')
		            ),
		            array(
			            'id'        => 'header_search_location_label',
			            'type'      => 'text',
			            'title'     => esc_html__('Label for the Location field', 'listgo'),
			            'default'   => esc_html__('Location', 'listgo')
		            ),
		            array(
			            'id'        => 'header_search_all_cost_label',
			            'type'      => 'text',
			            'title'     => esc_html__('Label for the default cost', 'listgo'),
			            'default'   => esc_html__('Cost - It does\'t matter', 'listgo')
		            ),
		            array(
			            'id'        => 'header_search_cheap_cost_label',
			            'type'      => 'text',
			            'title'     => esc_html__('Label for Cheap Segment', 'listgo'),
			            'default'   => esc_html__('$ - Cheap', 'listgo')
		            ),
		            array(
			            'id'        => 'header_search_moderate_cost_label',
			            'type'      => 'text',
			            'title'     => esc_html__('Label for Moderate Segment', 'listgo'),
			            'default'   => esc_html__('$$ - Moderate', 'listgo')
		            ),
		            array(
			            'id'        => 'header_search_expensive_cost_label',
			            'type'      => 'text',
			            'title'     => esc_html__('Label for Expensive Segment', 'listgo'),
			            'default'   => esc_html__('$$$ - Expensive', 'listgo')
		            ),
		            array(
			            'id'        => 'header_search_ultra_high_cost_label',
			            'type'      => 'text',
			            'title'     => esc_html__('Label for  Ultra high', 'listgo'),
			            'default'   => esc_html__('$$$$ - Ultra high', 'listgo')
		            ),
		            array(
			            'id'    => 'listing_toggle_search_by_tag',
			            'type'  => 'select',
			            'title' => esc_html__('Toggle Search By Tags', 'listgo'),
			            'default' => 'enable',
			            'options' => array(
				            'disable'  => esc_html__('Disable', 'listgo'),
				            'enable'   => esc_html__('Enable', 'listgo'),
			            )
		            ),
		            array(
			            'id'    => 'listgo_search_max_radius',
			            'type'  => 'text',
			            'title' => esc_html__('Maximum Radius (*)', 'listgo'),
			            'default' => 20
		            ),
		            array(
			            'id'    => 'listgo_search_min_radius',
			            'type'  => 'text',
			            'title' => esc_html__('Minimum Radius (*)', 'listgo'),
			            'default' => 1
		            ),
		            array(
			            'id'    => 'listgo_search_default_radius',
			            'type'  => 'text',
			            'title' => esc_html__('Set Default Radius (*)', 'listgo'),
			            'default' => 10
		            ),
		            array(
			            'title' => '',
			            'id'    => 'close_search_form_settings',
			            'type'  => 'section',
			            'indent'=> false
		            ),
	                array(
	                	'id'        => 'listing_layout',
		                'type'      => 'select',
		                'title'     => esc_html__('Single Listing Layout', 'listgo'),
		                'subtitle'  => esc_html__('Set a layout for listing page. Note that you can override  this setting for each individual page by using Listing Template', 'listgo'),
		                'default'   => 'templates/single-listing-traditional.php',
		                'options'   => array(
			                'templates/single-listing-traditional.php'      => esc_html__('Traditional', 'listgo'),
		                	'templates/single-listing-creative.php'         => esc_html__('Creative', 'listgo'),
		                	'templates/single-listing-creative-sidebar.php' => esc_html__('Creative Sidebar', 'listgo'),
		                	'templates/single-listing-lively.php'           => esc_html__('Lively', 'listgo'),
		                	'templates/single-listing-blurbehind.php'       => esc_html__('Blur Behind', 'listgo'),
		                	'templates/single-listing-lisa.php'             => esc_html__('Lisa', 'listgo'),
		                	'templates/single-listing-howard-roark.php'     => esc_html__('Roark', 'listgo'),
		                )
	                ),
		            array(
			            'id'        => 'listing_contactform7',
			            'type'      => 'select',
			            'data'      => 'posts',
			            'args'      => array(
				            'post_type'         => 'wpcf7_contact_form',
				            'posts_per_page'    => -1,
				            'orderby'           => 'post_date',
				            'post_status'       => 'publish'
			            ),
			            'title'       => esc_html__('Set Contact Form 7', 'listgo'),
			            'description' => esc_html__('This contact form will be used on the Contact & Map tab and on the Contact Widget', 'listgo')
		            ),
		            array(
			            'title' => esc_html__('Claim Settings', 'listgo'),
			            'id'    => 'open_claim_section',
			            'type'  => 'section',
			            'indent'=> true
		            ),
		            array(
			            'id'    => 'listing_toggle_claim_listings',
			            'type'  => 'select',
			            'title' => esc_html__('Toggle Claim Listings', 'listgo'),
			            'default' => 'enable',
			            'options' => array(
				            'enable'    => esc_html__('Enable', 'listgo'),
				            'disable'   => esc_html__('Disable', 'listgo'),
			            )
		            ),
		            array(
			            'id'        => 'listing_claim_title',
			            'type'      => 'text',
			            'title'     => esc_html__('Claim Title', 'listgo'),
			            'default'   => esc_html__('Is this your business?', 'listgo'),
			            'required'  => array('listing_toggle_claim_listings', '=', 'enable'),
		            ),
		            array(
			            'id'        => 'listing_claim_description',
			            'type'      => 'textarea',
			            'title'     => esc_html__('Claim Description', 'listgo'),
			            'default'   => esc_html__('Claim listing is the best way to manage and protect your business', 'listgo'),
			            'required'  => array('listing_toggle_claim_listings', '=', 'enable'),
		            ),
		            array(
			            'id'        => 'listing_claim_popup_description',
			            'type'      => 'textarea',
			            'title'     => esc_html__('Claim Popup Description', 'listgo'),
			            'default'   => esc_html__('Claim your listing in order to manage the listing page. You will get access to the listing dashboard, where you can upload photos, change the listing content and much more.', 'listgo'),
			            'required'  => array('listing_toggle_claim_listings', '=', 'enable'),
		            ),
		            array(
			            'id'    => 'listing_toggle_claim_required_phone',
			            'type'  => 'select',
			            'title' => esc_html__('Toggle Required Supply Business Phone', 'listgo'),
			            'default' => 'enable',
			            'options' => array(
				            'enable'    => esc_html__('Enable', 'listgo'),
				            'disable'   => esc_html__('Disable', 'listgo'),
			            )
		            ),
		            array(
			            'id'    => 'listing_claimed_template',
			            'type'  => 'select',
			            'data'  => 'posts',
			            'args'      => array(
				            'post_type'         => 'page',
				            'posts_per_page'    => -1,
				            'post_status'       => 'publish',
				            'meta_key'          => '_wp_page_template',
				            'meta_value'        => 'templates/edit-claimed.php'
			            ),
			            'required'      => array('listing_toggle_claim_listings', '=', 'enable'),
			            'title'         => esc_html__('Edit Claimed Page', 'listgo'),
			            'subtitle'      => esc_html__('Where claimer guy could be edit his Listing', 'listgo'),
			            'description'   => Wiloke::wiloke_kses_simple_html('<strong>Please go to Pages -> Add New -> Create a new page then assign the page to Edit Claimed template </strong>', 'listgo'),
			            'default'       => ''
		            ),
		            array(
			            'id'    => 'close_claim_section',
			            'type'  => 'section',
			            'indent'=> false
		            ),
		            array(
			            'title' => esc_html__('Listings Near By You', 'listgo'),
			            'id'    => 'open_geocode_section',
			            'type'  => 'section',
			            'indent'=> true
		            ),
		            array(
			            'id'    => 'listing_toggle_ask_for_geocode',
			            'type'  => 'select',
			            'title' => esc_html__('Ask For Current User Position', 'listgo'),
			            'default' => 'enable',
			            'options' => array(
				            'enable'    => esc_html__('Enable', 'listgo'),
				            'disable'   => esc_html__('Disable', 'listgo'),
			            )
		            ),
		            array(
			            'id'    => 'close_geocode_section',
			            'type'  => 'section',
			            'indent'=> false
		            ),
		            array(
		            	'id'        => 'open_listing_tab_section',
			            'type'      => 'section',
			            'indent'    => true,
			            'title'     => esc_html__('Tab Settings', 'listgo'),
		            ),
		            array(
			            'id'        => 'listing_toggle_tab_desc',
			            'type'      => 'select',
			            'title'     => esc_html__('Toggle Description Tab', 'listgo'),
			            'default'   => 'enable',
			            'options'   => array(
			            	'enable'    => esc_html__('Enable', 'listgo'),
			            	'disable'   => esc_html__('Disable', 'listgo')
			            )
		            ),
		            array(
		                'id'        => 'listing_tab_desc',
			            'type'      => 'text',
			            'title'     => esc_html__('Description Tab', 'listgo'),
			            'required'  => array('listing_toggle_tab_desc', '=', 'enable'),
			            'default'   => 'Description'
		            ),
		            array(
			            'id'        => 'listing_toggle_tab_contact_and_map',
			            'type'      => 'select',
			            'title'     => esc_html__('Toggle Contact And Map Tab', 'listgo'),
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => esc_html__('Enable', 'listgo'),
				            'disable'   => esc_html__('Disable', 'listgo')
			            )
		            ),
		            array(
			            'id'        => 'listing_tab_contact_and_map',
			            'type'      => 'text',
			            'title'     => esc_html__('Contact And Map Tab', 'listgo'),
			            'required'  => array('listing_toggle_tab_contact_and_map', '=', 'enable'),
			            'default'   => 'Contact & Map'
		            ),
		            array(
			            'id'        => 'listing_toggle_tab_review_and_rating',
			            'type'      => 'select',
			            'title'     => esc_html__('Toggle Review and rating', 'listgo'),
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => esc_html__('Enable', 'listgo'),
				            'disable'   => esc_html__('Disable', 'listgo')
			            )
		            ),
		            array(
			            'id'        => 'listing_tab_review_and_rating',
			            'type'      => 'text',
			            'title'     => esc_html__('Review And Rating Tab', 'listgo'),
			            'required'  => array('listing_toggle_tab_review_and_rating', '=', 'enable'),
			            'default'   => 'Review & Rating'
		            ),
		            array(
			            'id'        => 'listing_toggle_add_photo_in_review_tab',
			            'type'      => 'select',
			            'title'     => esc_html__('Allow user to add photos in their review', 'listgo'),
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => esc_html__('Enable', 'listgo'),
				            'disable'   => esc_html__('Disable', 'listgo')
			            )
		            ),
		            array(
			            'id'        => 'close_listing_tab_section',
			            'type'      => 'section',
			            'indent'    => false
		            ),
		            array(
			            'id'        => 'open_listing_meta_data_section',
			            'type'      => 'section',
			            'indent'    => true,
			            'title'     => esc_html__('Toggle Meta Data', 'listgo'),
		            ),
		            array(
			            'id'        => 'listing_toggle_posted_on',
			            'type'      => 'select',
			            'title'     => esc_html__('Posted On', 'listgo'),
			            'subtitle'  => esc_html__('Showing when the article was created', 'listgo'),
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => esc_html__('Enable', 'listgo'),
				            'disable'   => esc_html__('Disable', 'listgo'),
			            )
		            ),
		            array(
			            'id'        => 'listing_toggle_categories',
			            'type'      => 'select',
			            'title'     => esc_html__('Toggle Categories', 'listgo'),
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => esc_html__('Enable', 'listgo'),
				            'disable'   => esc_html__('Disable', 'listgo'),
			            )
		            ),
		            array(
			            'id'        => 'listing_toggle_locations',
			            'type'      => 'select',
			            'title'     => esc_html__('Toggle Locations', 'listgo'),
			            'default'   => 'disable',
			            'options'   => array(
				            'enable'    => esc_html__('Enable', 'listgo'),
				            'disable'   => esc_html__('Disable', 'listgo'),
			            )
		            ),
		            array(
			            'id'        => 'listing_toggle_rating_result',
			            'type'      => 'select',
			            'title'     => esc_html__('Rating Result', 'listgo'),
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => esc_html__('Enable', 'listgo'),
				            'disable'   => esc_html__('Disable', 'listgo'),
			            )
		            ),
		            array(
			            'id'        => 'listing_toggle_following_author',
			            'type'      => 'select',
			            'title'     => esc_html__('Following Author\'s Article', 'listgo'),
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => esc_html__('Enable', 'listgo'),
				            'disable'   => esc_html__('Disable', 'listgo'),
			            )
		            ),
		            array(
			            'id'        => 'listing_toggle_report',
			            'type'      => 'select',
			            'title'     => esc_html__('Report', 'listgo'),
			            'subtitle'  => esc_html__('If this feature is enabled, A bad article could be report to admin by reader.', 'listgo'),
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => esc_html__('Enable', 'listgo'),
				            'disable'   => esc_html__('Disable', 'listgo'),
			            )
		            ),
		            array(
			            'id'        => 'listing_toggle_sharing_posts',
			            'type'      => 'select',
			            'title'     => esc_html__('Sharing Article', 'listgo'),
			            'description' => esc_html__('Important: Wiloke Sharing Posts plugin is required by this feature.', 'listgo'),
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => esc_html__('Enable', 'listgo'),
				            'disable'   => esc_html__('Disable', 'listgo'),
			            )
		            ),
		            array(
			            'id'            => 'listing_toggle_add_to_favorite',
			            'type'          => 'select',
			            'title'         => esc_html__('Add To My Favorite', 'listgo'),
			            'description'   => esc_html__('Important: Listgo Functionality plugin is required by this feature.', 'listgo'),
			            'default'       => 'enable',
			            'options'       => array(
				            'enable'    => esc_html__('Enable', 'listgo'),
				            'disable'   => esc_html__('Disable', 'listgo'),
			            )
		            ),
		            array(
			            'id'        => 'close_listing_meta_data_section',
			            'type'      => 'section',
			            'indent'    => false
		            ),
		            array(
		            	'title'     => esc_html__('Related Listings', 'listgo'),
			            'id'        => 'open_related_listing_section',
			            'type'      => 'section',
			            'indent'    => true
		            ),
		            array(
			            'id'        => 'listing_toggle_related_listings',
			            'type'      => 'select',
			            'title'     => esc_html__('Toggle Related Listings', 'listgo'),
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => esc_html__('Enable', 'listgo'),
				            'disable'   => esc_html__('Disable', 'listgo'),
			            )
		            ),
		            array(
			            'id'        => 'listing_related_listings_title',
			            'type'      => 'text',
			            'required'  => array('listing_toggle_related_listings', '=', 'enable'),
			            'title'     => esc_html__('Related Listing Title', 'listgo'),
			            'default'   => 'More Listings By %author%'
		            ),
		            array(
			            'id'        => 'listing_related_listings_by',
			            'type'      => 'select',
			            'required'  => array('listing_toggle_related_listings', '=', 'enable'),
			            'title'     => esc_html__('Get Related Listings By', 'listgo'),
			            'default'   => 'author',
			            'options'   => array(
				            'author'            => esc_html__('Author', 'listgo'),
				            'listing_cat'       => esc_html__('Listing Category', 'listgo'),
				            'listing_location'  => esc_html__('Listing Location', 'listgo'),
			            )
		            ),
		            array(
			            'id'        => 'close_related_listing_section',
			            'type'      => 'section',
			            'indent'    => false
		            ),
                )
	        ),

            // WooCommerce
	        array(
		        'title'            => esc_html__('WooCommerce', 'listgo'),
		        'id'               => 'woocommerce_settings',
		        'subsection'       => false,
		        'customizer_width' => '500px',
		        'icon'             => 'dashicons dashicons-cart',
		        'fields'           => array(
			        array(
				        'type'        => 'select',
				        'id'          => 'woo_products_per_row_on_desktops',
				        'title'       => esc_html__('Products per row on large Desktops', 'listgo'),
				        'options'     => array(
					        'col-md-4'      => esc_html__('3 products / row', 'listgo'),
					        'col-md-3'      => esc_html__('4 products / row', 'listgo'),
					        'col-md-6'      => esc_html__('2 products / row', 'listgo')
				        ),
				        'default'     => 'col-md-4'
			        ),
			        array(
				        'type'        => 'select',
				        'id'          => 'woo_products_per_row_on_tablets',
				        'title'       => esc_html__('Products per row on small Tablets', 'listgo'),
				        'options'     => array(
					        'col-sm-6'      => esc_html__('2 products / row', 'listgo'),
					        'col-sm-4'      => esc_html__('3 products / row', 'listgo'),
					        'col-sm-3'      => esc_html__('4 products / row', 'listgo')
				        ),
				        'default'     => 'col-sm-6'
			        ),
		        )
	        ),

            // Footer Settings
            array(
                'title'            => esc_html__('Footer', 'listgo'),
                'id'               => 'footer_settings',
                'subsection'       => false,
                'customizer_width' => '500px',
                'icon'             => 'dashicons dashicons-hammer',
                'fields'           => array(
	                array(
		                'id'        => 'footer_toggle_widgets',
		                'type'      => 'select',
		                'title'     => esc_html__('Footer Widgets', 'listgo'),
		                'subtitle'   => esc_html__('Select Enable if you want to use this feature. To set Footer Widgets, please go to Appearance -> Widgets -> Dragging your widgets into Footer 1 area and Footer 2 area.', 'listgo'),
		                'default'   => 'enable',
		                'options'   => array(
		                	'enable'  => esc_html__('Enable', 'listgo'),
		                	'disable' => esc_html__('Disable', 'listgo')
		                )
	                ),
	                array(
		                'id'        => 'footer_style',
		                'type'      => 'select',
		                'required'  => array('footer_toggle_widgets', '=', 'enable'),
		                'title'     => esc_html__('Footer Style', 'listgo'),
		                'default'   => 'footer-style1',
		                'options'   => array(
			                'footer-style1'  => esc_html__('Footer Style 1', 'listgo'),
			                'footer-style2'  => esc_html__('Footer Style 2', 'listgo')
		                )
	                ),
                	array(
                        'id'        => 'footer_bg',
                        'type'      => 'media',
                        'title'     => esc_html__('Footer Background', 'listgo'),
                        'default'   => ''
                    ),
	                array(
		                'id'        => 'footer_overlay',
		                'type'      => 'color_rgba',
		                'title'     => esc_html__('Overlay Color', 'listgo'),
		                'default'   => ''
	                ),
                    array(
                        'id'            => 'footer_logo',
                        'type'          => 'media',
                        'title'         => esc_html__('Footer Logo', 'listgo'),
                        'description'   => esc_html__('Leave empty to use the logo at General Section', 'listgo')
                    ),
                    array(
                        'type'        => 'textarea',
                        'id'          => 'footer_copyright',
                        'title'       => esc_html__('Copyright', 'listgo'),
                        'default'     => ''
                    )
                )
            ),

	        // Social networks
	        array(
		        'title'            => esc_html__('Social Networks', 'listgo'),
		        'id'               => 'social_network_settings',
		        'subsection'       => false,
		        'icon'             => 'dashicons dashicons-share',
		        'customizer_width' => '500px',
		        'fields'           => WilokeSocialNetworks::render_setting_field()
	        ),

	        // SEO
	        array(
		        'title'            => esc_html__('SEO', 'listgo'),
		        'id'               => 'seo_settings',
		        'subsection'       => false,
		        'customizer_width' => '500px',
		        'icon'             => 'dashicons dashicons-search',
		        'fields'           => array(
			        array(
				        'id'        => 'seo_open_graph_meta',
				        'type'      => 'select',
				        'options'   => array(
					        'enable'  => esc_html__('Enable', 'listgo'),
					        'disable' => esc_html__('Disable', 'listgo')
				        ),
				        'default'  => 'enable',
				        'title'    => esc_html__('Open Graph Meta', 'listgo'),
				        'subtitle' => esc_html__('Elements that describe the object in different ways and are represented by meta tags included on the object page', 'listgo')
			        ),
			        array(
				        'id'       => 'seo_og_image',
				        'type'     => 'media',
				        'title'    => esc_html__( 'Image', 'listgo'),
				        'subtitle' => esc_html__( 'This image represent your website within the social graph. It should use a 1200x1200px  or large square image.', 'listgo'),
				        'default'  => ''
			        ),
			        array(
				        'type'     => 'text',
				        'id'       => 'seo_home_custom_title',
				        'title'    => esc_html__('Homepage custom title', 'listgo'),
				        'subtitle' => esc_html__('The title will be displayed in homepage between &lt;title>&lt;/title> tags', 'listgo'),
				        'default'  => get_option('blogname')
			        ),
			        array(
				        'id'        => 'seo_home_title_format',
				        'type'      => 'select',
				        'options'   => array(
					        'blogname_blogdescription'  => esc_html__('Blog Name | Blog Description', 'listgo'),
					        'blogdescription_blogname'  => esc_html__('Blog Description | Blog Name', 'listgo'),
					        'blogname' => esc_html__('Blog Name Only', 'listgo')
				        ),
				        'default'  => 'blogname_blogdescription',
				        'title'    => esc_html__('Home Title Format', 'listgo'),
				        'subtitle' => esc_html__('If Homepage custom title not set', 'listgo')
			        ),
			        array(
				        'id'        => 'seo_archive_title_format',
				        'type'      => 'select',
				        'options'   => array(
					        'categoryname_blogname'  => esc_html__('Category Name | Blog Name', 'listgo'),
					        'blogname_categoryname'  => esc_html__('Blog Name | Category Name', 'listgo'),
					        'category' => esc_html__('Category Name Only', 'listgo')
				        ),
				        'default'     => 'categoryname_blogname',
				        'title'       => esc_html__('Category Title Format', 'listgo'),
				        'subtitle'    => esc_html__('If Homepage custom title not set', 'listgo')
			        ),
			        array(
				        'id'        => 'seo_single_post_page_title_format',
				        'type'      => 'select',
				        'options'   => array(
					        'posttitle_blogname'  => esc_html__('Post Title | Blog Name', 'listgo'),
					        'blogname_posttitle'  => esc_html__('Blog Name | Post Title', 'listgo'),
					        'posttitle' => esc_html__('Post Title Only', 'listgo')
				        ),
				        'default'     => 'posttitle_blogname',
				        'title'       => esc_html__('Single Post Page Title Format', 'listgo')
			        ),
			        array(
				        'id'       => 'seo_home_meta_keywords',
				        'type'     => 'textarea',
				        'default'  => '',
				        'title'    => esc_html__('Home Meta Keywords', 'listgo'),
				        'subtitle' => esc_html__('Add tags for the search engines and especially Google', 'listgo')
			        ),
			        array(
				        'id'    => 'seo_home_meta_description',
				        'type'  => 'textarea',
				        'title' => esc_html__('Home Meta Description', 'listgo'),
				        'default'  => get_option('blogdescription')
			        ),
			        array(
				        'id'     => 'seo_author_meta_description',
				        'type'   => 'textarea',
				        'title'  => esc_html__('Author Meta Description', 'listgo'),
				        'default'=>'wiloke.com'
			        ),
			        array(
				        'id'     => 'seo_contact_meta_description',
				        'type'   => 'textarea',
				        'title'  => esc_html__('Contact Meta Description', 'listgo'),
				        'default'=>'piratesmorefun@gmail.com'
			        ),
			        array(
				        'id'     => 'seo_other_meta_keywords',
				        'type'   => 'textarea',
				        'title'  => esc_html__('Other Meta Keywords', 'listgo'),
				        'default'=>''
			        ),
			        array(
				        'id'     => 'seo_other_meta_description',
				        'type'   => 'textarea',
				        'title'  => esc_html__('Other Meta Description', 'listgo'),
				        'default'=>''
			        )
		        )
	        ),

	        // Advanced Settings
            array(
                'title'            => esc_html__('Advanced Settings', 'listgo'),
                'id'               => 'advanced_settings',
                'icon'             => 'dashicons dashicons-lightbulb',
                'subsection'       => false,
                'customizer_width' => '500px',
                'fields'           => array(
                    array(
                        'id'        => 'advanced_google_fonts',
                        'type'      => 'select',
                        'title'     => esc_html__('Google Fonts', 'listgo'),
                        'options'   => array(
                            'default'   => esc_html__('Default', 'listgo'),
                            'general'   => esc_html__('Custom', 'listgo'),
                            // 'detail'    => esc_html__('Detail Custom', 'listgo')
                        ),
                        'default'   => 'default'
                    ),
                    array(
                        'id'            => 'advanced_general_google_fonts',
                        'type'          => 'text',
                        'title'         => esc_html__('Google Fonts', 'listgo'),
                        'required'      => array('advanced_google_fonts', '=', 'general'),
                        'description'   => esc_html__('The theme allows replace current Google Fonts with another Google Fonts. Go to https://fonts.google.com/specimen to get a the Font that you want. For example: https://fonts.googleapis.com/css?family=Prompt', 'listgo')
                    ),
                    array(
                        'id'            => 'advanced_general_google_fonts_css_rules',
                        'type'          => 'text',
                        'required'      => array('advanced_google_fonts', '=', 'general'),
                        'title'         => esc_html__('Css Rules', 'listgo'),
                        'description'   => esc_html__('This code shoule be under Google Font link. For example: font-family: \'Prompt\', sans-serif;', 'listgo')
                    ),
                    array(
                        'id'        => 'advanced_main_color',
                        'type'      => 'select',
                        'title'     => esc_html__('Theme Color', 'listgo'),
                        'options'   => array(
                            ''        => esc_html__('Default', 'listgo'),
                            'green'   => esc_html__('Green', 'listgo'),
                            'lime'    => esc_html__('Lime', 'listgo'),
                            'pink'    => esc_html__('Pink', 'listgo'),
                            'yellow'  => esc_html__('Yellow', 'listgo'),
                            'custom'  => esc_html__('Custom', 'listgo')
                        ),
                        'default'   => ''
                    ),
                    array(
                        'id'        => 'advanced_custom_main_color',
                        'type'      => 'color_rgba',
                        'title'     => esc_html__('Custom Color', 'listgo'),
                        'required'  => array('advanced_main_color', '=', 'custom')
                    ),
	                array(
		                'id'          => 'widget_caching',
		                'type'        => 'text',
		                'title'       => esc_html__('Widget Caching', 'listgo'),
		                'description' => esc_html__('Leave empty mean no caching. But We highly recommend using this feature. Unit is hour, it means if you enter in 1, the widget will be cached in 1 hour.', 'listgo'),
		                'default'     => ''
	                ),
	                array(
		                'id'          => 'sidebar_additional',
		                'type'        => 'text',
		                'title'       => esc_html__('Add More Sidebar', 'listgo'),
		                'description' => esc_html__('You can add more sidebar by entering in your sidebar id here. For example: my_custom_sidebar_1,my_custom_sidebar_2', 'listgo'),
		                'default'     => ''
	                ),
                    array(
                        'id'        => 'advanced_css_code',
                        'type'      => 'ace_editor',
                        'title'     => esc_html__('Custom CSS Code', 'listgo'),
                        'mode'      => 'css',
                        'theme'    => 'monokai'
                    ),
                    array(
                        'id'        => 'advanced_js_code',
                        'type'      => 'ace_editor',
                        'title'     => esc_html__('Custom Javascript Code', 'listgo'),
                        'mode'      => 'javascript',
                        'default'   => ''
                    ),
                )
            )
        )
    )
);