<?php

/**
* The Shell
*/
class Maera_Shell_Core {

	private static $instance;

	private function __construct() {
		do_action( 'maera/shell/include_modules' );

		Maera_Helper::define( 'MAERA_SHELL_PATH', dirname( __FILE__ ) );

		// Enqueue the scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 110 );

		// Add the shell Timber modifications
		add_filter( 'timber_context', array( $this, 'timber_extras' ) );

		$header_args = array( 'default-image' => get_template_directory_uri() . '/core-shell/assets/images/grid-back.png' );
		add_theme_support( 'custom-header', $header_args );

		add_theme_support( 'tonesque' );
		add_theme_support( 'site-logo' );
		add_theme_support( 'infinite-scroll', array(
			'type'      => 'click',
		    'container' => 'content',
		    'footer'    => false,
		) );

		add_filter( 'maera/styles', array( $this, 'custom_header' ) );
		add_filter( 'maera/styles', array( $this, 'colorposts_build_css' ) );
		add_filter( 'maera/image/display', '__return_true' );
		add_filter( 'maera/image/width', array( $this, 'image_width' ) );
		add_filter( 'maera/image/height', array( $this, 'image_height' ) );

		global $content_width;
		$content_width = 774;

	}

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	function image_width() {
		return 912;
	}

	function image_height() {
		return 368;
	}

	/**
	 * Register all scripts and additional stylesheets (if necessary)
	 */
	function scripts() {

		wp_enqueue_script( 'html5shiv', get_template_directory_uri() . '/assets/js/html5shiv.js', array( '' ), false, false );
		wp_script_add_data( 'html5shiv', 'conditional', 'lt IE 9' );
		wp_enqueue_script( 'respondjs', get_template_directory_uri() . '/assets/js/respond.min.js', array( '' ), false, false );
		wp_script_add_data( 'respondjs', 'conditional', 'lt IE 9' );
		wp_enqueue_style( 'maera_theme_main', get_template_directory_uri() . '/core-shell/assets/css/main.css' );

		wp_enqueue_script( 'maera_theme_main_menu', get_template_directory_uri() . '/core-shell/assets/js/vendor/menu.js', false, null, true );

	}

	/**
	 * Timber extras.
	 */
	function timber_extras( $data ) {

		$data['singular']['image']['switch'] = true;
		$data['singular']['image']['width']  = 736;
		$data['singular']['image']['height'] = 300;

		$data['archives']['image']['switch'] = true;
		$data['archives']['image']['width']  = 736;
		$data['archives']['image']['height'] = 300;

		return $data;
	}

	function custom_header( $styles ) {

		$url = $this->custom_header_url();

		if ( empty( $url ) ) {
			return;
		} else {
			return $styles . '.page-header:before{ background: url("' . $url . '") no-repeat center center; }';
		}

	}

	function custom_header_url() {

		$image_url = get_header_image();
		if ( is_singular() && has_post_thumbnail() ) {
			$image_array = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
			$image_url = $image_array[0];
		}

		if ( empty( $image_url ) ) {
			return false;
		} else {
			return $image_url;
		}

	}

	function pn_get_attachment_id_from_url( $attachment_url = '' ) {

		global $wpdb;
		$attachment_id = false;

		// If there is no url, return.
		if ( '' == $attachment_url )
			return;

		// Get the upload directory paths
		$upload_dir_paths = wp_upload_dir();

		// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
		if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

			// If this is the URL of an auto-generated thumbnail, get the URL of the original image
			$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

			// Remove the upload path base directory from the attachment URL
			$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

			// Finally, run a custom database query to get the attachment ID from the modified attachment URL
			$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );

		}

		return $attachment_id;
	}

	/**
	* Build CSS from Tonesque
	*
	* @uses get_the_ID(), is_single(), get_post_meta(), colorposts_get_post_image(), update_post_meta(), apply_filters()
	*
	* @since Color Posts 1.0
	*/
	function colorposts_build_css( $styles ) {

		$src = $this->custom_header_url();

		if ( $src && get_template_directory_uri() . '/core-shell/assets/images/grid-back.png' != $src ) {

			$attachment_id = $this->pn_get_attachment_id_from_url( $src );
			// Grab color from post meta
			$tonesque = get_post_meta( $attachment_id, '_post_colors', true );
			// No color? Let's get one
			if ( empty( $tonesque ) ) {
				$tonesque = new Tonesque( $src );
				$tonesque = array(
					'color'    => $tonesque->color(),
					'contrast' => $tonesque->contrast(),
				);
				if ( $tonesque['color'] ) {
					update_post_meta( get_the_ID(), '_post_colors', $tonesque );
				}

			}

			// Add the CSS to our page
			extract( $tonesque );

			if ( ! empty( $color ) ) {

				$white = new Jetpack_Color( '#FFFFFF' );
				$color = new Jetpack_Color( '#' . $color );

				$luminosity = $color->toLuminosity();
				$fontcolor  = ( $luminosity < 0.5 ) ? '#FFFFFF' : '#222222';
				$background = $fontcolor == '#FFFFFF' ? 'rgba(0,0,0,0.3)' : 'rgba(255,255,255,0.3)';

				$styles .= 'a{color:#' . $color->getReadableContrastingColor( $white, 6 )->toHex() . ';}';
				$styles .= '#menu.menu-wrap, .menu-button {background-color:#' . $color->getReadableContrastingColor( $white )->toHex() . ';}';
				$styles .= '.page-header{color:' . $fontcolor . ' !important; background: ' . $background . ';box-shadow:0px 0px 5px ' . $color . ';}';

			}

		} else {

			$styles .= '.page-header:before{background-color:#0C6890;background-image:url("' . get_template_directory_uri() . '/core-shell/assets/images/grid-back.png' . '");background-size:auto !important;background-repeat:repeat;}';

		}

		return $styles;

	}

}

/**
 * Include the shell
 */
function maera_shell_core_include( $shells ) {

	// Add our shell to the array of available shells
	$shells[] = array(
		'value' => 'core',
		'label' => 'Core',
		'class' => 'Maera_Shell_Core',
	);

	return $shells;

}
add_filter( 'maera/shells/available', 'maera_shell_core_include' );
