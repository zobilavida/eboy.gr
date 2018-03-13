<?php
if ( !defined('WILOKE_TURN_OFF_REDIS') ){
	define('WILOKE_TURN_OFF_REDIS', false);
}

require_once  ( get_template_directory() . '/admin/run.php' );

/*
 |--------------------------------------------------------------------------
 | After theme setup
 |--------------------------------------------------------------------------
 |
 | Run needed functions after the theme is setup
 |
 */
add_action('after_setup_theme', 'wiloke_listgo_after_setup_theme');
function wiloke_listgo_after_setup_theme(){
	add_theme_support('html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ));
	add_theme_support('title-tag');
	add_theme_support('widgets');
	add_theme_support('woocommerce');
	add_theme_support('automatic-feed-links');
	add_theme_support('menus');
	add_theme_support('post-thumbnails');
	add_theme_support('title-tag');
	add_theme_support('post-formats', array( 'gallery', 'quote', 'video', 'audio' ));
	add_theme_support( 'editor-style' );
	// Woocommerce
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	add_image_size('wiloke_listgo_370x370', 370, 370, true);
	add_image_size('wiloke_listgo_740x370', 740, 370, true);
	add_image_size('wiloke_listgo_740x740', 740, 740, true);
	add_image_size('wiloke_listgo_455x340', 455, 340, true);
	$GLOBALS['content_width'] = apply_filters('wiloke_filter_content_width', 1200);
	load_theme_textdomain( 'listgo', get_template_directory() . '/languages' );
}

add_filter('wp_list_categories', 'wiloke_listgo_count_span');
function wiloke_listgo_count_span($links) {
	$links = str_replace('</a> (', ' (', $links);
	$links = str_replace(')', ')</a>', $links);
	return $links;
}

add_filter('get_archives_link', 'wiloke_listgo_archive_count_span');
function wiloke_listgo_archive_count_span($links) {
	$links = str_replace('</a>&nbsp;(', ' (', $links);
	$links = str_replace(')', ')</a>', $links);
	return $links;
}

add_filter('wiloke_menu_style_filter', 'wiloke_listgo_mega_menu_style');
function wiloke_listgo_mega_menu_style($args) {
	return array(
		'wiloke-menu-horizontal' => esc_html__('Menu Horizontal', 'listgo'),
	);
}

add_filter('wiloke_menu_theme_filter', 'wiloke_listgo_mega_menu_themes');
function wiloke_listgo_mega_menu_themes($args) {
	return array(
		'' => esc_html__('Default', 'listgo'),
	);
}

// Footer Style
add_filter('body_class', 'wiloke_listgo_body_class');
function wiloke_listgo_body_class($classes) {
	global $wiloke;

	$style = 'footer-style1';

	if ( isset($wiloke->aThemeOptions['footer_style']) ) {
		$style = $wiloke->aThemeOptions['footer_style'];
	}

	if ( is_singular('listing') && is_page_template('default') ){
	    $layout = $wiloke->aThemeOptions['listing_layout'];
	    $fClass = str_replace('/', '', $layout);
	    $aParseListingTemplate = explode('/', $layout);
	    $classes[] = 'listing-template-' . $fClass . ' listing-template-'.$aParseListingTemplate[1];
    }

	$classes[] = $style;

	return $classes;
}

add_filter( 'user_can_richedit', 'patrick_user_can_richedit');

function patrick_user_can_richedit($c) {
	return true;
}

add_action('wiloke/listgo/wiloke-submission/addlisting/before_listing_information', 'listgoAddCustomFieldsToListingPage', 10, 2);

if ( !function_exists('listgoAddCustomFieldsToListingPage') ){
	function listgoAddCustomFieldsToListingPage($postID, $packageID){
		if ( !function_exists('acf_form') ){
			return '';
		}

		$aPackageSettings = Wiloke::getPostMetaCaching($packageID, 'pricing_settings');

		if ( !isset($aPackageSettings['afc_custom_field']) || empty($aPackageSettings['afc_custom_field']) ){
			return '';
		}

		if ( isset($aPackageSettings['toggle_custom_field']) && ($aPackageSettings['toggle_custom_field'] == 'disable') ){
			return '';
		}

		$aSettings = array(
			'group_title' => get_the_title($aPackageSettings['afc_custom_field']),
			'group_desc'  => '',
			/* (string) Unique identifier for the form. Defaults to 'acf-form' */
			'id' => 'acf-form',
			/* (int|string) The post ID to load data from and save data to. Defaults to the current post ID.
			Can also be set to 'new_post' to create a new post on submit */
			'post_id' => $postID,
			/* (array) An array of post data used to create a post. See wp_insert_post for available parameters.
			The above 'post_id' setting must contain a value of 'new_post' */
			'new_post' => false,

			/* (array) An array of field group IDs/keys to override the fields displayed in this form */
			'field_groups' => array($aPackageSettings['afc_custom_field']),

			/* (array) An array of field IDs/keys to override the fields displayed in this form */
			'fields' => true,
			/* (boolean) Whether or not to create a form element. Useful when a adding to an existing form. Defaults to true */
			'form' => false,
			'return' => '',
			/* (string) Determines element used to wrap a field. Defaults to 'div'
			Choices of 'div', 'tr', 'td', 'ul', 'ol', 'dl' */
			'field_el' => 'div',

			/* (string) Whether to use the WP uploader or a basic input for image and file fields. Defaults to 'wp'
			Choices of 'wp' or 'basic'. Added in v5.2.4 */
			'uploader' => 'wp',

			/* (boolean) Whether to include a hidden input field to capture non human form submission. Defaults to true. Added in v5.3.4 */
			'honeypot' => false
		);

		?>
		<div class="add-listing-group">
			<?php if ( WilokePublic::addLocationBy() === 'default' ): ?>
			<div class="col-sm-12">
			<?php endif; ?>
			
			<?php if ( !empty($aSettings['group_title']) ) : ?>
				<h4 class="add-listing-title profile-title"><?php echo esc_html($aSettings['group_title']); ?></h4>
			<?php endif; ?>
			<?php if ( !empty($aSettings['group_title']) ) : ?>
				<p class="add-listing-description"><?php echo esc_html($aSettings['group_desc']); ?></p>
			<?php endif; ?>
			<div class="row">
				<?php acf_form( $aSettings ); ?>
			</div>
			<?php if ( WilokePublic::addLocationBy() === 'default' ): ?>
			</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
