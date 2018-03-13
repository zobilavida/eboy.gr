<?php

global $wiloke;

return array(
	'page_settings'                 => array(
		'id'         => 'page_settings',
		'title'      => esc_html__( 'Page Settings', 'listgo' ),
		'pages'      => array('page'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'      => 'select',
				'id'        => 'page_color',
				'name'      => esc_html__('Page color', 'listgo'),
				'description' => esc_html__('From ListGo 1.1.5 you can set the main color for each page. Note that you can also the theme color to whole site by clicking on Appearance -> Theme Options -> Advanced Settings.', 'listgo'),
				'default'   => 'inherit',
				'options'   => array(
					'default' => esc_html__('Inherit From ThemeOptions', 'listgo'),
					'green'   => esc_html__('Green', 'listgo'),
					'lime'    => esc_html__('Lime', 'listgo'),
					'pink'    => esc_html__('Pink', 'listgo'),
					'yellow'  => esc_html__('Yellow', 'listgo'),
					'custom'  => esc_html__('Custom', 'listgo')
				)
			),
			array(
				'type'      => 'colorpicker',
				'id'        => 'custom_page_color',
				'name'      => esc_html__('Custom Page Color', 'listgo'),
				'default'   => '',
				'dependency'  => array('page_color', '=', 'custom')
			),
			array(
				'type'      => 'select',
				'id'        => 'nav_style',
				'name'      => esc_html__('Menu Background', 'listgo'),
				'default'   => 'inherit',
				'options'   => array(
					'inherit'               => esc_html__('Inherit From ThemeOptions', 'listgo'),
					'header--transparent'   => esc_html__('Transparent', 'listgo'),
					'header--background'    => esc_html__('Black', 'listgo'),
					'header--custombg'      => esc_html__('Custom Color', 'listgo'),
				)
			),
			array(
				'type'      => 'colorpicker',
				'id'        => 'custom_nav_bg',
				'name'      => esc_html__('Custom Page Color', 'listgo'),
				'default'   => '',
				'dependency'  => array('nav_style', '=', 'header--custombg')
			),
			array(
				'type'      => 'colorpicker',
				'id'        => 'custom_nav_color',
				'name'      => esc_html__('Custom Menu Item Color', 'listgo'),
				'default'   => '',
				'dependency'  => array('nav_style', '=', 'header--custombg')
			),
			array(
				'type' => 'select',
				'id'   => 'toggle_header_image',
				'name' => esc_html__('Toggle Header Image', 'listgo'),
				'dependency_on_template' => array('contains', 'templates/homepage.php'),
				'description' => esc_html__('This feature is only available for Page Builder template. We recommend that you do not enable this feature if, in case, you want to build a Home Page. We recommend you do use Hero shortcode instead. But, in case, you want to build a page such as Pricing Table, Our team, this feature is recommended.', 'listgo'),
				'default' => 'disable',
				'options' => array(
					'disable' => esc_html__('Disable', 'listgo'),
					'enable' => esc_html__('Enbale', 'listgo')
				)
			),
			array(
				'type' => 'file',
				'id'   => 'header_image',
				'name' => esc_html__('Header Image', 'listgo'),
				'dependency_on_template' => array('not_contains', 'templates/listing-map.php'),
				'description' => esc_html__('We recommend an image of 1200px of the width', 'listgo')
			),
			array(
				'type' => 'colorpicker',
				'id'   => 'header_overlay',
				'name' => esc_html__('Header Overlay', 'listgo'),
				'dependency_on_template' => array('not_contains', 'templates/listing-map.php'),
				'description' => esc_html__('If you want to create a blur on the Header Image, this setting is useful for you.', 'listgo')
			)
		)
	),
    'template_settings'             => array(
	    'id'    => 'template_settings',
	    'title' => esc_html__('Template Settings', 'listgo'),
	    'pages' => array('page'), // Post type
	    'context'    => 'normal',
	    'dependency_on_template' => array('contains', 'templates/listing.php'),
	    'priority'   => 'low',
	    'show_names' => true, // Show field names on the left
	    'fields'     => array(
		    array(
			    'type'      => 'select',
			    'id'        => 'layout',
			    'name'      => esc_html__('Listing Layout', 'listgo'),
			    'default'   => 'listing--list',
			    'options'   => array(
				    'listing--grid' => esc_html__('Grid', 'listgo'),
				    'listing--grid1'=> esc_html__('Grid 2', 'listgo'),
				    'listing-grid2'=> esc_html__('Grid 3', 'listgo'),
				    'listing-grid3'=> esc_html__('Grid 4', 'listgo'),
				    'listing-grid4'=> esc_html__('Grid 5', 'listgo'),
				    'listing--list' => esc_html__('List', 'listgo'),
				    'listing--list1'=> esc_html__('List 2', 'listgo'),
				    'circle-thumbnail'  => esc_html__('List Circle Thumbnail (New)', 'listgo'),
				    'creative-rectangle'=> esc_html__('List Creative Rectangle (New)', 'listgo')
			    )
		    ),
		    array(
			    'type'      => 'select',
			    'id'        => 'order_by',
			    'name'      => esc_html__('Order by', 'listgo'),
			    'default'   => 'date',
			    'options'   => array(
				    'post_date'     => esc_html__('Date', 'listgo'),
				    'title'         => esc_html__('Title', 'listgo'),
				    'comment_count' => esc_html__('Comment Count', 'listgo'),
				    'author'        => esc_html__('Author', 'listgo'),
				    'rand'          => esc_html__('Random', 'listgo'),
				    'menu_order'    => esc_html__('Featured Listings First', 'listgo')
			    )
		    ),
		    array(
			    'type'      => 'select',
			    'id'        => 'show_terms',
			    'name'      => esc_html__('Show Terms', 'listgo'),
			    'description'   => esc_html__('Choosing kind of terms will be shown on each project item.', 'listgo'),
			    'default'   => 'both',
			    'options'   => array(
				    'both'              => esc_html__('Listing Locations and Listing Categories', 'listgo'),
				    'listing_location'  => esc_html__('Only Listing Locations', 'listgo'),
				    'listing_cat'       => esc_html__('Only Listing Categories', 'listgo')
			    )
		    ),
		    array(
			    'type'          => 'text',
			    'name'          => esc_html__('Image Size', 'listgo'),
			    'id'            => 'image_size',
			    'description'   => esc_html__('Set image size for the feature image. You can use one of the following keywords: large, medium, thumbnail or specify size by following this structure w,h, for example: 1000,400, it means you want to display a featured image of 1000 width x 4000 height.', 'listgo'),
			    'default'       => 'medium'
		    ),
		    array(
			    'type'      => 'select',
			    'id'        => 'sidebar_position',
			    'name'      => esc_html__('Sidebar Position', 'listgo'),
			    'default'   => 'inherit',
			    'options'   => array(
				    'inherit'   => esc_html__('Inherit Theme Options', 'listgo'),
				    'left'      => esc_html__('Left Sidebar', 'listgo'),
				    'right'     => esc_html__('Right Sidebar', 'listgo'),
				    'no'        => esc_html__('No Sidebar', 'listgo')
			    )
		    ),
		    array(
			    'type'              => 'select',
			    'name'              => esc_html__('Display Style', 'listgo'),
			    'id'                => 'display_style',
			    'default'           => 'all',
			    'options'           => array(
				    'all'           => esc_html__('Show all', 'listgo'),
				    'pagination'    => esc_html__('Pagination', 'listgo'),
				    'loadmore'      => esc_html__('Load more button', 'listgo')
			    )
		    ),
		    array(
			    'type'          => 'text',
			    'name'          => esc_html__('Posts per page', 'listgo'),
			    'id'            => 'posts_per_page',
			    'dependency'    => array('display_style', 'contains', 'pagination,loadmore'),
			    'description'   => esc_html__('Leave empty to use the general setting (General -> Settings -> Reading)', 'listgo'),
			    'default'       => ''
		    ),
	    )
    ),
	'events_template_settings'      => array(
		'id'    => 'events_template_settings',
		'title' => esc_html__('Events Template Settings', 'listgo'),
		'pages' => array('page'), // Post type
		'context'    => 'normal',
		'dependency_on_template' => array('contains', 'templates/events-template.php'),
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'          => 'text',
				'name'          => esc_html__('Posts per page', 'listgo'),
				'id'            => 'posts_per_page',
				'description'   => esc_html__('Leave empty to use the general setting (General -> Settings -> Reading)', 'listgo'),
				'default'       => 10
			),
			array(
				'type'          => 'text',
				'name'          => esc_html__('Image Size', 'listgo'),
				'id'            => 'image_size',
				'description'   => esc_html__('Set image size for the feature image. You can use one of the following keywords: large, medium, thumbnail or specify size by following this structure w,h, for example: 1000,400, it means you want to display a featured image of 1000 width x 4000 height.', 'listgo'),
				'default'       => 'wiloke_listgo_740x370'
			),
			array(
				'type'          => 'text',
				'name'          => esc_html__('Excerpt Length', 'listgo'),
				'id'            => 'limit_character',
				'default'       => 100
			),
		)
	),
	'map_template_settings'         => array(
		'id'    => 'map_template_settings',
		'title' => esc_html__('Map Settings', 'listgo'),
		'pages' => array('page'), // Post type
		'context'    => 'normal',
		'dependency_on_template' => array('contains', 'templates/listing-map.php'),
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'      => 'text',
				'id'        => 'map_theme',
				'name'      => esc_html__('Map Theme', 'listgo'),
				'description'      => Wiloke::wiloke_kses_simple_html(__('Leave empty to use the Theme Options setting. Please refer to Wiloke Guide -> FAQs -> How to create a Map page to know more.', 'listgo'), true),
				'default'   => ''
			),
			array(
				'type'      => 'text',
				'id'        => 'map_center',
				'name'      => esc_html__('Map Center', 'listgo'),
				'description'=> esc_html__('Leave empty means select the first article', 'listgo'),
				'default'   => ''
			)
		)
	),
    'listing_settings'              => array(
        'id'         => 'listing_settings',
        'title'      => esc_html__( 'Listing Information', 'listgo' ),
        'pages'      => array('listing'), // Post type
        'context'    => 'normal',
        'priority'   => 'low',
        'show_names' => true, // Show field names on the left
        'fields'     => array(
            array(
                'type'         => 'latlong',
                'id'           => 'map',
                'name'         => esc_html__('Map Settings', 'listgo'),
                'description'  => esc_html__('Enter in post\'s Lat&Long. If the map does not work, please go to Appearance -> Theme Options -> General -> Google Map API key.', 'listgo'),
                'placeholder'  => '21.027764,105.834160',
                'default'      => '',
            ),
            array(
                'type'         => 'text',
                'id'           => 'phone_number',
                'name'         => esc_html__('Phone', 'listgo'),
                'description'  => esc_html__('Leave empty inherit Your profile settings', 'listgo'),
                'default'      => '',
            ),
            array(
                'type'         => 'text',
                'id'           => 'website',
                'name'         => esc_html__('Website', 'listgo'),
                'description'  => esc_html__('Leave empty inherit Your profile settings', 'listgo'),
                'default'      => ''
	        )
        )
    ),
	'listing_open_table_settings'   => array(
		'id'         => 'listing_open_table_settings',
		'title'      => esc_html__( 'Open Table Settings', 'listgo' ),
		'description'=> Wiloke::wiloke_kses_simple_html( __('You can find your restaurant id here <a href="https://www.otrestaurant.com/marketing/reservationwidget" target="_blank">https://www.otrestaurant.com</a>', 'listgo'), true ),
		'pages'      => array('listing'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'text',
				'id'           => 'restaurant_name',
				'name'         => esc_html__('Restaurant Name', 'listgo'),
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'restaurant_id',
				'name'         => esc_html__('Restaurant ID', 'listgo'),
				'default'      => ''
			)
		)
	),
	'listing_claim'                 => array(
		'id'         => 'listing_claim',
		'title'      => esc_html__( 'Listing Claim', 'listgo' ),
		'pages'      => array('listing'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'select',
				'id'           => 'status',
				'name'         => esc_html__('Claim Status', 'listgo'),
				'description'  => esc_html__('Note that you need to enable Claim System in Appearance -> Theme Options -> Listing Settings first.', 'listgo'),
				'default'      => 'not_claimed',
				'options'      => array(
					'not_claimed' => esc_html__('Not Claimed', 'listgo'),
					'claimed'     => esc_html__('Claimed', 'listgo')
				)
			)
		)
	),
	'listing_price'                 => array(
		'id'         => 'listing_price',
		'title'      => esc_html__( 'Price Segment', 'listgo' ),
		'pages'      => array('listing'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'select',
				'id'           => 'price_segment',
				'name'         => esc_html__('Range Segment', 'listgo'),
				'default'      => '',
				'options'      => array(
					''              => esc_html__('Thanks, but no thanks', 'listgo'),
					'cheap'         => esc_html__('$ - Cheap', 'listgo'),
					'moderate'      => esc_html__('$$ - Moderate', 'listgo'),
					'expensive'     => esc_html__('$$$ - Expensive', 'listgo'),
					'ultra_high'    => esc_html__('$$$$ - Ultra high', 'listgo')
				)
			),
			array(
				'type'         => 'text_small',
				'id'           => 'price_from',
				'name'         => esc_html__('Price From', 'listgo'),
				'default'      => '',
				'options'      => ''
			),
			array(
				'type'         => 'text_small',
				'id'           => 'price_to',
				'name'         => esc_html__('Price To', 'listgo'),
				'default'      => '',
				'options'      => ''
			)
		)
	),
	'listing_social_media'          => array(
		'id'         => 'listing_social_media',
		'title'      => esc_html__( 'Social Media', 'listgo' ),
		'pages'      => array('listing'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'text',
				'id'           => 'facebook',
				'name'         => esc_html__('Facebook', 'listgo'),
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'twitter',
				'name'         => esc_html__('Twitter', 'listgo'),
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'google-plus',
				'name'         => esc_html__('Google+', 'listgo'),
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'linkedin',
				'name'         => esc_html__('Linkedin', 'listgo'),
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'tumblr',
				'name'         => esc_html__('Tumblr', 'listgo'),
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'instagram',
				'name'         => esc_html__('Instagram', 'listgo'),
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'pinterest',
				'name'         => esc_html__('Pinterest', 'listgo'),
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'vimeo',
				'name'         => esc_html__('Vimeo', 'listgo'),
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'youtube',
				'name'         => esc_html__('Youtube', 'listgo'),
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'whatsapp',
				'name'         => esc_html__('Whatsapp', 'listgo'),
				'default'      => '',
			)
		)
	),
    'gallery_settings'              => array(
        'id'         => 'gallery_settings',
        'title'      => esc_html__( 'Gallery', 'listgo' ),
        'pages'      => array('listing'), // Post type
        'context'    => 'side',
        'priority'   => 'low',
        'show_names' => true, // Show field names on the left
        'fields'     => array(
            array(
                'type'         => 'file_list',
                'id'           => 'gallery',
                'query_args'   => array('type' => 'image' ),
                'preview_size' => array(50, 50),
                'name'         => esc_html__('Upload Images', 'listgo'),
                'default'      => '',
            )
        )
    ),
	'listing_other_settings'        => array(
		'id'         => 'listing_other_settings',
		'title'      => esc_html__( 'Other Settings', 'listgo' ),
		'pages'      => array('listing'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'colorpicker',
				'id'           => 'header_overlay',
				'name'         => esc_html__('Overlay Color', 'listgo'),
				'description'  => esc_html__('Leave empty to use the default setting in the ThemeOptions', 'listgo'),
				'placeholder'  => '',
				'default'      => ''
			)
		)
	),
	'testimonial_settings'          => array(
		'id'         => 'testimonial_settings',
		'title'      => esc_html__( 'Testimonial Settings', 'listgo' ),
		'pages'      => array('testimonial'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'file',
				'id'           => 'profile_picture',
				'name'         => esc_html__('Profile picture', 'listgo'),
				'description'  => esc_html__('We recommend an image of 128x128px', 'listgo'),
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'position',
				'name'         => esc_html__('Job Position', 'listgo'),
				'default'      => '',
			),
		)
	),
	'event_settings'                => array(
		'id'         => 'event_settings',
		'title'      => esc_html__( 'Settings', 'listgo' ),
		'pages'      => array('event'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'text',
				'id'           => 'place_detail',
				'name'         => esc_html__('Place Detail', 'listgo'),
				'description'  => esc_html__('Enter the address where organize this event.', 'listgo'),
				'default'      => '',
				'date_format'  => ''
			),
			array(
				'type'         => 'text',
				'id'           => 'latitude',
				'name'         => esc_html__('Latitude', 'listgo'),
				'default'      => '',
				'required'     => true,
				'date_format'  => ''
			),
			array(
				'type'         => 'text',
				'id'           => 'longitude',
				'name'         => esc_html__('Longitude', 'listgo'),
				'default'      => '',
				'required'     => true,
				'date_format'  => ''
			),
			array(
				'type'         => 'text_date',
				'id'           => 'start_on',
				'name'         => esc_html__('Event start on', 'listgo'),
				'description'  => esc_html__('When does event start? Note that it will be show like July 16, 2017 on the front-end.', 'listgo'),
				'default'      => '',
				'date_format'  => 'M dd, yy'
			),
			array(
				'type'         => 'text',
				'id'           => 'start_at',
				'name'         => esc_html__('Event start at', 'listgo'),
				'description'  => esc_html__('Showing exactly the time event is opening', 'listgo'),
				'default'      => '8:30 AM',
			),
			array(
				'type'         => 'text',
				'id'           => 'end_at',
				'name'         => esc_html__('Event end at', 'listgo'),
				'description'  => esc_html__('Showing exactly the time event is closed', 'listgo'),
				'default'      => '8:30 PM',
			),
			array(
				'type'         => 'text_date',
				'id'           => 'end_on',
				'name'         => esc_html__('Event end on', 'listgo'),
				'default'      => '',
				'date_format'  => 'M dd, yy'
			),
			array(
				'type'         => 'post_type_select',
				'post_types'   => array('listing'),
				'id'           => 'belongs_to',
				'name'         => esc_html__('Event belongs to', 'listgo'),
				'description'  => esc_html__('Set the listing, which this event belongs to', 'listgo'),
				'default'      => ''
			),
			array(
				'type'         => 'text',
				'id'           => 'event_link',
				'name'         => esc_html__('Link to event', 'listgo'),
				'description'  => esc_html__('For example: An agency pay for you to promote their event. On their website has an article about this event, you can put that link here, when your user click on this event, it redirects to the agency page.', 'listgo'),
				'default'      => '',
			)
		)
	),
	'pricing_settings'              => array(
		'id'        => 'pricing_settings',
		'title'     => esc_html__('Settings', 'listgo'),
		'pages'     => array('pricing'),
		'context'   => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type' => 'desc',
				'id'   => 'description',
				'name' => esc_html__('Important', 'listgo'),
				'desc' => Wiloke::wiloke_kses_simple_html(__('If you are using 2Checkout Payment Gateway, Please read this tutorial <a href="https://blog.wiloke.com/setup-plan-for-2checkout-payment-gateway/" target="_blank">SETUP PLAN FOR 2CHECKOUT PAYMENT GATEWAY</a>', 'listgo'), true),
			),
			array(
				'type' => 'text',
				'id'   => 'description',
				'name' => esc_html__('Description', 'listgo'),
				'default' => 'Standard listing submission, active for 30 days.'
			),
			array(
				'type' => 'text_small',
				'id'   => 'duration',
				'name' => esc_html__('Duration (day)', 'listgo'),
				'description' => Wiloke::wiloke_kses_simple_html(__('Set duration of an article start from it is published. If this field is empty, it means unlimited time. <strong class="help red">This feature is only available for Non-recurring Payment</strong> or This is a <strong class="red help">Free</strong> package plan. Regarding <strong class="red help">Recurring Payment</strong>, the listing will be hidden after your user could not pay for their plan.', 'listgo'), true)
			),

			array(
				'type' => 'text_small',
				'id'   => 'price',
				'name' => esc_html__('Regular Price (number)', 'listgo'),
				'description' => esc_html__('Leave empty to set this pricing table as free. Notice: Only enter number.', 'listgo')
			),
			array(
				'type' => 'text_small',
				'id'   => 'regular_period',
				'name' => esc_html__('Regular Period (day)', 'listgo'),
				'description' => Wiloke::wiloke_kses_simple_html(__('Set a Billing Frequency. For example: If you set the value is 30, it means auto payment will be proceed each month. <strong class="help red">0 means Unlimited Availability package. Otherwise, Regular must be less than or equal to one year</strong>. <strong class="help red">This feature is available for Recurring Payment method</strong>', 'listgo'), true)
			),
			array(
				'type' => 'text_small',
				'id'   => 'trial_price',
				'name' => esc_html__('Trial Price (number)', 'listgo'),
				'description' => Wiloke::wiloke_kses_simple_html(__('<strong class="help red">A trial price cannot the same the regular price</strong>. Enter <strong class="red">0</strong> means Your customer can use this package, with <strong class="help red">X days</strong> free trial, for the package <strong class="help red">X is Trial Period setting below</strong>. After the free trial, the customer is billed X (X is regular price) dollar per <strong class="help red">Regular Period (day)</strong>. <strong class="help red">This feature is available for Recurring Payment method</strong>', 'listgo'), true)
			),
			array(
				'type' => 'text_small',
				'id'   => 'trial_period',
				'name' => esc_html__('Trial Period (day)', 'listgo'),
				'description' => Wiloke::wiloke_kses_simple_html(__('<strong class="help red">This feature is available for Recurring Payment method and You can not leave empty if you want to trial feature.</strong>', 'listgo'), true)
			),
			array(
				'type' => 'text_small',
				'id'   => 'number_of_posts',
				'name' => esc_html__('Number of Listings', 'listgo'),
				'description' => esc_html__('Set maximum listings will be created by this pricing. Leave empty to set unlimited listings.', 'listgo')
			),
			array(
				'type'          => 'select',
				'id'            => 'number_of_listing_per',
				'name'          => esc_html__('Number of listings Per', 'listgo'),
				'description'   => Wiloke::wiloke_kses_simple_html(__('This feature is only available for Recurring Payment. Eg: Regular Period: 30, Number of listings: 10, Number of Listings per: Regular Period, it means your user can add maximum 10 listings / month. <strong class="help red">This feature is available for Recurring Payment method</strong>. Regarding No-recurring package, it always is Lifetime.', 'listgo'), true),
				'default'       => 'regular_period',
				'options'       => array(
					'regular_period'    => esc_html__('Regular Period', 'listgo'),
					'lifetime'   => esc_html__('Lifetime', 'listgo'),
				)
			),
			array(
				'type' => 'post_type_select',
				'post_types'=> array('event-pricing'),
				'default' => '',
				'id'   => 'event_pricing_package',
				'name' => esc_html__('Event Package', 'listgo'),
				'description' => esc_html__('Once a client purchased this plan, they will be offered an event plan (on the first time billing only). The event plan allows creating events for their listing. You can create the event plan by clicking on Event Pricings -> Add New.', 'listgo')
			),
			array(
				'type'          => 'select',
				'id'            => 'publish_on_map',
				'name'          => esc_html__('Publish on map', 'listgo'),
				'description'   => esc_html__('When your customers purchase this pricing, their listings will be published/unpublish on Map', 'listgo'),
				'default'       => 'enable',
				'options'       => array(
					'enable'    => esc_html__('Enable', 'listgo'),
					'disable'   => esc_html__('Disable', 'listgo'),
				)
			),
			array(
				'type'          => 'select',
				'id'            => 'toggle_add_feature_listing',
				'name'          => esc_html__('Featured Listing', 'listgo'),
				'description'   => esc_html__('When your customers purchase this pricing, A ribbon will be added to their listings.', 'listgo'),
				'default'       => 'disable',
				'options'       => array(
					'enable'    => esc_html__('Enable', 'listgo'),
					'disable'   => esc_html__('Disable', 'listgo'),
				)
			),
			array(
				'type'          => 'select',
				'id'            => 'toggle_allow_add_gallery',
				'name'          => esc_html__('Toggle add gallery on the sidebar', 'listgo'),
				'description'   => esc_html__('When Allow / Not Allow ability to add a gallery onto the Listing sidebar', 'listgo'),
				'default'       => 'enable',
				'options'       => array(
					'enable'    => esc_html__('Enable', 'listgo'),
					'disable'   => esc_html__('Disable', 'listgo'),
				)
			),
			array(
				'type'          => 'select',
				'id'            => 'toggle_allow_embed_video',
				'name'          => esc_html__('Toggle Embed Video into Listing Content', 'listgo'),
				'description'   => esc_html__('Allow / Not Allow ability to embed Video into Listing', 'listgo'),
				'default'       => 'enable',
				'options'       => array(
					'enable'    => esc_html__('Enable', 'listgo'),
					'disable'   => esc_html__('Disable', 'listgo'),
				)
			),
			array(
				'type'          => 'select',
				'id'            => 'toggle_listing_template',
				'name'          => esc_html__('Toggle Listing Template', 'listgo'),
				'description'   => esc_html__('Allow using all listing template or just use the default template.', 'listgo'),
				'default'       => 'enable',
				'options'       => array(
					'enable'    => esc_html__('Enable', 'listgo'),
					'disable'   => esc_html__('Disable', 'listgo'),
				)
			),
			array(
				'type'          => 'select',
				'id'            => 'toggle_listing_shortcode',
				'name'          => esc_html__('Toggle Listing Shortcodes', 'listgo'),
				'description'   => esc_html__('Allow using Accordion, Listing Features, Menu Prices shortcodes or not.', 'listgo'),
				'default'       => 'enable',
				'options'       => array(
					'enable'    => esc_html__('Enable', 'listgo'),
					'disable'   => esc_html__('Disable', 'listgo'),
				)
			),
			array(
				'type' 			=> 'post_type_select',
				'post_types'	=> array('acf'),
				'default' 		=> '',
				'id'            => 'afc_custom_field',
				'name'          => esc_html__('Custom Fields', 'listgo'),
				'description'   => esc_html__('Select a Group Custom Field that will be used on this package. Note that this group must be set to "Post Type is equal to Listing" rule', 'listgo')
			),
			array(
				'type' 			=> 'select',
				'default' 		=> 'disable',
				'id'            => 'toggle_open_table',
				'name'          => esc_html__('Toggle Open Table', 'listgo'),
				'description'   => esc_html__('Allow your users to embed their Restaurant on www.opentable.com to their listing.', 'listgo'),
				'options'       => array(
					'enable'    => esc_html__('Enable', 'listgo'),
					'disable'   => esc_html__('Disable', 'listgo'),
				)
			),
			array(
				'type' => 'select',
				'id'   => 'highlight',
				'name' => esc_html__('High Light', 'listgo'),
				'description' => esc_html__('Set this pricing as highlight feature', 'listgo'),
				'options'       => array(
					'enable'    => esc_html__('Enable', 'listgo'),
					'disable'   => esc_html__('Disable', 'listgo'),
				),
				'default' => 'disable'
			)
		)
	),
	'event_pricing_settings'        => array(
		'id'        => 'event_pricing_settings',
		'title'     => esc_html__('Settings', 'listgo'),
		'pages'     => array('event-pricing'),
		'context'   => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type' => 'desc',
				'id'   => 'event_pricing_description',
				'name' => esc_html__('How Add Event Listing works?', 'listgo'),
				'desc' => Wiloke::wiloke_kses_simple_html(__('Please read this article to know more <a href="https://blog.wiloke.com/event-plan-works/" target="_blank">HOW “EVENT PLAN” WORKS?</a>', 'listgo'), true)
			),
			array(
				'type' => 'text_small',
				'id'   => 'price',
				'name' => esc_html__('Price', 'listgo'),
				'description' => esc_html__('This price must be bigger than 0. You can create a relationship between Event Plan and Pricing Plan by going to Pricing -> For: Package A -> Assigning this plan to the Package A.', 'listgo')
			),
			array(
				'type' => 'text_small',
				'id'   => 'number_of_posts',
				'name' => esc_html__('Number of Events', 'listgo'),
				'description' => esc_html__('Set maximum listings will be created by this pricing. The number must be bigger than 0.', 'listgo')
			)
		)
	),
	'review_settings'               => array(
		'id'        => 'review_settings',
		'title'     => esc_html__('Review Settings', 'listgo'),
		'pages'     => array('review'),
		'context'   => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'file_list',
				'id'           => 'gallery',
				'query_args'   => array('type' => 'image' ),
				'preview_size' => array(50, 50),
				'name'         => esc_html__('Gallery', 'listgo'),
				'default'      => '',
			)
		)
	),
	'half_map_settings'             => array(
		'id'                        => 'half_map_settings',
		'title'                     => esc_html__( 'Half Map Settings', 'listgo' ),
		'dependency_on_template'    => array('contains', 'templates/half-map.php'),
		'pages'                     => array('page'), // Post type
		'context'                   => 'normal',
		'priority'                  => 'low',
		'show_names'                => true, // Show field names on the left
		'fields'                    => array(
			array(
				'type'     => 'text',
				'id'       => 'maxZoom',
				'name'     => esc_html__('Map Maximum Zoom (Desktop)', 'listgo'),
				'default'  => 4
			),
			array(
				'type'     => 'text',
				'id'       => 'minZoom',
				'name'     => esc_html__('Map Minimum Zoom (Desktop)', 'listgo'),
				'desc'     => esc_html__('A negative number is allowable', 'listgo'),
				'default'  => -1
			),
			array(
				'type'     => 'text',
				'id'       => 'centerZoom',
				'name'     => esc_html__('Map Center Zoom', 'listgo'),
				'default'  => 10
			),
			array(
				'type'     => 'text',
				'id'       => 'center',
				'name'     => esc_html__('Map Center', 'listgo'),
				'desc'     => esc_html__('Eg: 38.8913,-77.02. Leave empty to set the first listing as the map center.', 'listgo'),
				'default'  => ''
			),
			array(
				'type'     => 'text',
				'id'       => 'maxClusterRadius',
				'name'     => esc_html__('Map Cluster Radius', 'listgo'),
				'subtitle' => Wiloke::wiloke_kses_simple_html(__('The maximum radius that a cluster will cover from the central marker', 'listgo'), true),
				'default'  => 60
			),
			array(
				'type'     => 'text',
				'id'       => 'posts_per_page',
				'name'     => esc_html__('Listings per page', 'listgo'),
				'default'  => 4
			),
			array(
				'type'      => 'select',
				'id'        => 'show_terms',
				'name'      => esc_html__('Show Terms', 'listgo'),
				'description'   => esc_html__('Choosing kind of terms will be shown on each project item.', 'listgo'),
				'default'   => 'both',
				'options'   => array(
					'both'              => esc_html__('Listing Locations and Listing Categories', 'listgo'),
					'listing_location'  => esc_html__('Only Listing Locations', 'listgo'),
					'listing_cat'       => esc_html__('Only Listing Categories', 'listgo')
				)
			)
		)
	)
);