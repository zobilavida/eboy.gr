<?php
/**
 * @param Array(key=>val)
 * key: key of aConfigs
 * val: a part of file name: config.val.php
 */
return array(
	array(
		'name'               => esc_html__('Redux Framework', 'listgo'),
		'slug'               => 'redux-framework', // The plugin slug (typically the folder name).
		'required'           => true, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => esc_html__('Visual Composer', 'listgo'),
		'slug'               => 'js_composer', // The plugin slug (typically the folder name).
		'source'             => get_template_directory() . '/plugins/js_composer.zip', // The plugin source.
		'required'           => true, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => esc_html__('Wiloke Sharing Post', 'listgo'),
		'slug'               => 'wiloke-sharing-post', // The plugin slug (typically the folder name).
		'source'             => get_template_directory() . '/plugins/wiloke-sharing-post.zip', // The plugin source.
		'required'           => true, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => esc_html__('Wiloke Service', 'listgo'),
		'slug'               => 'wiloke-service', // The plugin slug (typically the folder name).
		'source'             => 'https://goo.gl/CFviQ4', // The plugin source.
		'required'           => true, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => esc_html__('Wiloke Listgo Widgets', 'listgo'),
		'slug'               => 'wiloke-listgo-widgets', // The plugin slug (typically the folder name).
		'source'             => get_template_directory() . '/plugins/wiloke-listgo-widgets.zip', // The plugin source.
		'required'           => true, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => esc_html__('Wiloke Mega Menu', 'listgo'),
		'slug'               => 'wiloke-mega-menu', // The plugin slug (typically the folder name).
		'source'             => 'https://goo.gl/cW1mdL', // The plugin source.
		'required'           => true, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => esc_html__('Wiloke Listgo Functionaliy', 'listgo'),
		'slug'               => 'wiloke-listgo-functionality', // The plugin slug (typically the folder name).
		'source'             => get_template_directory() . '/plugins/wiloke-listgo-functionality.zip', // The plugin source..zip', // The plugin source.
		'required'           => true, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => esc_html__('Login With Social', 'listgo'),
		'slug'               => 'login-with-social', // The plugin slug (typically the folder name).
		'source'             => 'https://goo.gl/cRvmoS', // The plugin source.
		'required'           => false, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => esc_html__('Contact Form 7', 'listgo'),
		'file_init'          => 'wp-contact-form-7.php',
		'slug'               => 'contact-form-7', // The plugin slug (typically the folder name).
		'required'           => false, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => esc_html__('WooCommcerce', 'listgo'),
		'slug'               => 'woocommerce', // The plugin slug (typically the folder name).
		'required'           => false, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	),
	array(
		'name'               => esc_html__('WP All Import - Listgo Addon', 'listgo'),
		'file_init'          => 'listgo-wp-all-import-addon.php',
		'slug'               => 'listgo-wp-all-import-addon', // The plugin slug (typically the folder name).
		'source'             => 'https://goo.gl/Aexpp9', // The plugin source.
		'required'           => false, // If false, the plugin is only 'recommended' instead of required.
		'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
	)
);