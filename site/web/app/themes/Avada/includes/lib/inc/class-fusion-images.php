<?php
/**
 * Images handler.
 *
 * @package Fusion-Library
 * @since 1.0.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Handle images.
 * Includes responsive-images tweaks.
 *
 * @since 1.0.0
 */
class Fusion_Images {

	/**
	 * The grid image meta.
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	public static $grid_image_meta;

	/**
	 * An array of the accepted widths.
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	public static $grid_accepted_widths;

	/**
	 * An array of supported layouts.
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	public static $supported_grid_layouts;

	/**
	 * Ratio used for masonry calculations.
	 *
	 * @static
	 * @access public
	 * @var float
	 */
	public static $masonry_grid_ratio;

	/**
	 * Width used for masonry 2x2 calculations.
	 *
	 * @static
	 * @access public
	 * @var int
	 */
	public static $masonry_width_double;

	/**
	 * Constructor.
	 *
	 * @access  public
	 */
	public function __construct() {
		global $fusion_settings;

		if ( ! $fusion_settings ) {
			$fusion_settings = Fusion_Settings::get_instance();
		}

		self::$grid_image_meta        = array();
		self::$grid_accepted_widths   = array( '200', '400', '600', '800', '1200' );
		self::$supported_grid_layouts = array( 'masonry', 'grid', 'timeline', 'large', 'portfolio_full', 'related-posts' );
		self::$masonry_grid_ratio     = $fusion_settings->get( 'masonry_grid_ratio' );
		self::$masonry_width_double   = $fusion_settings->get( 'masonry_width_double' );

		add_filter( 'max_srcset_image_width', array( $this, 'set_max_srcset_image_width' ) );
		add_filter( 'wp_calculate_image_srcset', array( $this, 'set_largest_image_size' ), '10', '5' );
		add_filter( 'wp_calculate_image_srcset', array( $this, 'edit_grid_image_srcset' ), '15', '5' );
		add_filter( 'wp_calculate_image_sizes', array( $this, 'edit_grid_image_sizes' ), '10', '5' );
		add_filter( 'post_thumbnail_html', array( $this, 'edit_grid_image_src' ), '10', '5' );
		add_action( 'delete_attachment', array( $this, 'delete_resized_images' ) );
		add_filter( 'wpseo_sitemap_urlimages', array( $this, 'extract_img_src_for_yoast' ), '10', '2' );
		add_filter( 'fusion_library_image_base_size_width', array( $this, 'fb_adjust_grid_image_base_size' ), 20, 4 );
		add_filter( 'fusion_masonry_element_class', array( $this, 'adjust_masonry_element_class' ), 10, 2 );
		add_filter( 'attachment_fields_to_edit', array( $this, 'add_image_meta_fields' ), 10, 2 );
		add_filter( 'attachment_fields_to_save', array( $this, 'save_image_meta_fields' ), 10, 2 );
		add_action( 'admin_head', array( $this, 'style_image_meta_fields' ) );
		add_filter( 'wp_update_attachment_metadata', array( $this, 'remove_dynamically_generated_images' ), 10, 2 );
	}

	/**
	 * Adds lightbox attributes to links.
	 *
	 * @param  string $content The content.
	 */
	public function prepare_lightbox_links( $content ) {

		preg_match_all( '/<a[^>]+href=([\'"])(.+?)\1[^>]*>/i', $content, $matches );
		$attachment_id = self::get_attachment_id_from_url( $matches[2][0] );
		$attachment_id = apply_filters( 'wpml_object_id', $attachment_id, 'attachment' );
		$title = get_post_field( 'post_title', $attachment_id );
		$caption = get_post_field( 'post_excerpt', $attachment_id );

		$content = preg_replace( '/<a/', '<a data-rel="iLightbox[postimages]" data-title="' . $title . '" data-caption="' . $caption . '"', $content, 1 );

		return $content;
	}

	/**
	 * Modify the maximum image width to be included in srcset attribute.
	 *
	 * @since 1.0.0
	 * @param int $max_width  The maximum image width to be included in the 'srcset'. Default '1600'.
	 * @return int  The new max width.
	 */
	public function set_max_srcset_image_width( $max_width ) {
		return 1920;
	}

	/**
	 * Add the fullsize image to the scrset attribute.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $sources {
	 *     One or more arrays of source data to include in the 'srcset'.
	 *
	 *     @type array $width {
	 *         @type string $url        The URL of an image source.
	 *         @type string $descriptor The descriptor type used in the image candidate string,
	 *                                  either 'w' or 'x'.
	 *         @type int    $value      The source width if paired with a 'w' descriptor, or a
	 *                                  pixel density value if paired with an 'x' descriptor.
	 *     }
	 * }
	 * @param array  $size_array    Array of width and height values in pixels (in that order).
	 * @param string $image_src     The 'src' of the image.
	 * @param array  $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.
	 * @param int    $attachment_id Image attachment ID or 0.
	 *
	 * @return array $sources       One or more arrays of source data to include in the 'srcset'.
	 */
	public function set_largest_image_size( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
		$cropped_image = false;

		foreach ( $sources as $source => $details ) {
			if ( $details['url'] === $image_src ) {
				$cropped_image = true;
			}
		}

		if ( ! $cropped_image ) {
			$full_image_src = wp_get_attachment_image_src( $attachment_id, 'full' );

			$full_size = array(
				'url'        => $full_image_src[0],
				'descriptor' => 'w',
				'value'      => $image_meta['width'],
			);

			$sources[ $image_meta['width'] ] = $full_size;
		}

		return $sources;
	}

	/**
	 * Filter out all srcset attributes, that do not fit current grid layout.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $sources {
	 *     One or more arrays of source data to include in the 'srcset'.
	 *
	 *     @type array $width {
	 *         @type string $url        The URL of an image source.
	 *         @type string $descriptor The descriptor type used in the image candidate string,
	 *                                  either 'w' or 'x'.
	 *         @type int    $value      The source width if paired with a 'w' descriptor, or a
	 *                                  pixel density value if paired with an 'x' descriptor.
	 *     }
	 * }
	 * @param array  $size_array    Array of width and height values in pixels (in that order).
	 * @param string $image_src     The 'src' of the image.
	 * @param array  $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.
	 * @param int    $attachment_id Image attachment ID or 0.
	 *
	 * @return array $sources       One or more arrays of source data to include in the 'srcset'.
	 */
	public function edit_grid_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
		// Only do manipulation for blog images.
		if ( ! empty( self::$grid_image_meta ) ) {
			// Only include the uncropped sizes in srcset.
			foreach ( $sources as $width => $source ) {

				// Make sure the original image isn't deleted.
				preg_match( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif|tiff|svg)$)/i', $source['url'], $matches );

				if ( ! in_array( $width, self::$grid_accepted_widths ) && isset( $matches[0] ) ) {
					unset( $sources[ $width ] );
				}
			}
		}

		ksort( $sources );

		return $sources;
	}

	/**
	 * Edits the'sizes' attribute for grid images.
	 *
	 * @since 1.0.0
	 *
	 * @param string       $sizes         A source size value for use in a 'sizes' attribute.
	 * @param array|string $size          Image size to retrieve. Accepts any valid image size, or an array
	 *                                    of width and height values in pixels (in that order). Default 'medium'.
	 * @param string       $image_src     Optional. The URL to the image file. Default null.
	 * @param array        $image_meta    Optional. The image meta data as returned by 'wp_get_attachment_metadata()'.
	 *                                    Default null.
	 * @param int          $attachment_id Optional. Image attachment ID. Either `$image_meta` or `$attachment_id`
	 *                                    is needed when using the image size name as argument for `$size`. Default 0.
	 * @return string|bool A valid source size value for use in a 'sizes' attribute or false.
	 */
	public function edit_grid_image_sizes( $sizes, $size, $image_src, $image_meta, $attachment_id ) {
		if ( isset( self::$grid_image_meta['layout'] ) ) {
			$content_break_point = apply_filters( 'fusion_library_content_break_point', 1100 );
			$content_width       = apply_filters( 'fusion_library_content_width', 1170 );

			if ( isset( self::$grid_image_meta['gutter_width'] ) ) {
				$content_width -= (int) self::$grid_image_meta['gutter_width'] * ( (int) self::$grid_image_meta['columns'] - 1 );
			}

			// Grid.
			if ( in_array( self::$grid_image_meta['layout'], array( 'masonry', 'grid', 'portfolio_full', 'related-posts' ), true ) ) {

				$main_break_point = (int) apply_filters( 'fusion_library_grid_main_break_point', 800 );
				if ( 640 < $main_break_point ) {
					$breakpoint_range = $main_break_point - 640;
				} else {
					$breakpoint_range = 360;
				}

				$breakpoint_interval = $breakpoint_range / 5;

				$main_image_break_point = apply_filters( 'fusion_library_main_image_breakpoint', $main_break_point );
				$break_points = apply_filters(
					'fusion_library_image_breakpoints', array(
						6 => $main_image_break_point,
						5 => $main_image_break_point - $breakpoint_interval,
						4 => $main_image_break_point - 2 * $breakpoint_interval,
						3 => $main_image_break_point - 3 * $breakpoint_interval,
						2 => $main_image_break_point - 4 * $breakpoint_interval,
						1 => $main_image_break_point - 5 * $breakpoint_interval,
					)
				);

				$sizes = apply_filters( 'fusion_library_image_grid_initial_sizes', '', $main_break_point, (int) self::$grid_image_meta['columns'] );

				$sizes .= '(min-width: 2200px) 100vw, ';

				foreach ( $break_points as $columns => $breakpoint ) {
					if ( $columns <= (int) self::$grid_image_meta['columns'] ) {
						$width = $content_width / $columns;

						// For one column layouts where the content width is larger than column breakpoint width, don't reset the width.
						if ( $breakpoint < $width && ! ( 1 === (int) self::$grid_image_meta['columns'] && $content_width > $breakpoint + $breakpoint_interval ) ) {
							$width = $breakpoint + $breakpoint_interval;
						}

						$sizes .= '(min-width: ' . round( $breakpoint ) . 'px) ' . round( $width ) . 'px, ';
					}
				}
			} elseif ( 'timeline' === self::$grid_image_meta['layout'] ) { // Timeline.
				$width = 40;
				$sizes = '(max-width: ' . $content_break_point . 'px) 100vw, ' . $width . 'vw';

				// Large Layouts (e.g. person or image element).
			} elseif ( false !== strpos( self::$grid_image_meta['layout'], 'large' ) ) {

				// If possible, set the correct size for the content width, depending on columns.
				if ( $attachment_id ) {
					$base_image_size = $this->get_grid_image_base_size( $attachment_id, self::$grid_image_meta['layout'], self::$grid_image_meta['columns'], 'get_closest_ceil' );

					if ( is_integer( $base_image_size ) ) {
						$content_width = $base_image_size;
					} else {
						$content_width = $size[0];
					}
				}

				$sizes = '(max-width: ' . $content_break_point . 'px) 100vw, ' . $content_width . 'px';
			}// End if().
		}// End if().

		return $sizes;
	}

	/**
	 * Change the src attribute for grid images.
	 *
	 * @since 1.0.0
	 *
	 * @param string       $html              The post thumbnail HTML.
	 * @param int          $post_id           The post ID.
	 * @param string       $post_thumbnail_id The post thumbnail ID.
	 * @param string|array $size              The post thumbnail size. Image size or array of width and height
	 *                                        values (in that order). Default 'post-thumbnail'.
	 * @param string       $attr              Query string of attributes.
	 * @return string The html markup of the image.
	 */
	public function edit_grid_image_src( $html, $post_id = null, $post_thumbnail_id = null, $size = null, $attr = null ) {
		if ( isset( self::$grid_image_meta['layout'] ) && in_array( self::$grid_image_meta['layout'], self::$supported_grid_layouts ) && 'full' === $size ) {
			$image_size = $this->get_grid_image_base_size( $post_thumbnail_id, self::$grid_image_meta['layout'], self::$grid_image_meta['columns'] );

			$full_image_src = wp_get_attachment_image_src( $post_thumbnail_id, $image_size );

			$html = preg_replace( '@src="([^"]+)"@', 'src="' . $full_image_src[0] . '"', $html );

		}

		return $html;
	}

	/**
	 * Get image size based on column size.
	 *
	 * @since 1.0.0
	 *
	 * @param null|int    $post_thumbnail_id Attachment ID.
	 * @param null|string $layout            The layout.
	 * @param null|int    $columns           Number of columns.
	 * @param string      $match_basis       Use 'get_closest' or 'get_closest_ceil'.
	 * @return string Image size name.
	 */
	public function get_grid_image_base_size( $post_thumbnail_id = null, $layout = null, $columns = null, $match_basis = 'get_closest' ) {
		global $is_IE;
		$sizes = array();

		// Get image metadata.
		$image_meta = wp_get_attachment_metadata( $post_thumbnail_id );

		if ( $image_meta ) {
			$image_sizes = array();
			if ( isset( $image_meta['sizes'] ) && ! empty( $image_meta['sizes'] ) ) {
				$image_sizes = $image_meta['sizes'];
			}

			if ( $image_sizes && is_array( $image_sizes ) ) {

				foreach ( $image_sizes as $name => $image ) {
					if ( in_array( strval( $name ), self::$grid_accepted_widths, true ) ) {
						// Create accepted sizes array.
						if ( $image['width'] ) {
							$sizes[ $image['width'] ] = $name;
						}
					}
				}
			}
			$sizes[ $image_meta['width'] ] = 'full';
		}
		$gutter = isset( self::$grid_image_meta['gutter_width'] ) ? self::$grid_image_meta['gutter_width'] : '';
		$width = apply_filters( 'fusion_library_image_base_size_width', 1000, $layout, $columns, $gutter );

		ksort( $sizes );

		$image_size = null;
		$size_name = null;

		// Find the best match.
		foreach ( $sizes as $size => $name ) {

			// Find closest size match.
			$match_condition = null === $image_size || abs( $width - $image_size ) > abs( $size - $width );

			// Find closest match greater than available width.
			if ( 'get_closest_ceil' === $match_basis ) {
				$match_condition = $size > $width && abs( $width - $image_size ) > abs( $size - $width );
			}

			if ( $match_condition ) {
				$image_size = $size;
				$size_name = $name;
			}
		}

		// Fallback to 'full' image size if no match was found or Internet Explorer is used.
		if ( null == $size_name || '' == $size_name || $is_IE ) {
			$size_name = 'full';
		}

		return $size_name;
	}

	/**
	 * Returns adjusted width of an image container.
	 * Adjustment is made based on Fusion Builder column image container is currently in.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param  int    $width           The container width in pixels.
	 * @param  string $layout          The layout name.
	 * @param  int    $columns         The number of columns used as a divider.
	 * @param  int    $gutter_width    The gutter width - in pixels.
	 * @global array  $fusion_col_type The current column padding and type.
	 * @return int
	 */
	public function fb_adjust_grid_image_base_size( $width, $layout, $columns = 1, $gutter_width = 30 ) {
		global $fusion_col_type;

		if ( ! empty( $fusion_col_type['type'] ) ) {
			$coeff = explode( '_', $fusion_col_type['type'] );
			$width = absint( $width * $coeff[0] / $coeff[1] );
		}

		return $width;
	}

	/**
	 * Setter function for the $grid_image_meta variable.
	 *
	 * @since 1.0.0
	 *
	 * @param array $grid_image_meta    Array containing layout and number of columns.
	 *
	 * @return void
	 */
	public function set_grid_image_meta( $grid_image_meta ) {
		self::$grid_image_meta = $grid_image_meta;
	}

	/**
	 * Gets the attachment ID from the URL.
	 *
	 * @param string $attachment_url The URL of the attachment.
	 * @return string The attachment ID
	 */
	public static function get_attachment_id_from_url( $attachment_url = '' ) {
		global $wpdb;
		$attachment_id = false;

		if ( '' === $attachment_url || ! is_string( $attachment_url ) ) {
			return '';
		}

		$upload_dir_paths = wp_upload_dir();
		$upload_dir_paths_baseurl = set_url_scheme( $upload_dir_paths['baseurl'] );

		// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image.
		if ( false !== strpos( $attachment_url, $upload_dir_paths_baseurl ) ) {

			// If this is the URL of an auto-generated thumbnail, get the URL of the original image.
			$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif|tiff|svg)$)/i', '', $attachment_url );

			// Remove the upload path base directory from the attachment URL.
			$attachment_url = str_replace( $upload_dir_paths_baseurl . '/', '', $attachment_url );

			// Get the actual attachment ID.
			$attachment_id = attachment_url_to_postid( $attachment_url );
			$wpml_object_id = apply_filters( 'wpml_object_id', $attachment_id, 'attachment' );
			$attachment_id = $wpml_object_id ? $wpml_object_id : $attachment_id;
		}

		return $attachment_id;
	}

	/**
	 * Gets the most important attachment data from the url.
	 *
	 * @since 1.0.0
	 * @param string $attachment_url The url of the used attachment.
	 * @return array/bool The attachment data of the image, false if the url is empty or attachment not found.
	 */
	public function get_attachment_data_from_url( $attachment_url = '' ) {

		if ( '' === $attachment_url ) {
			return false;
		}

		$attachment_url = set_url_scheme( $attachment_url );
		$attachment_base_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif|tiff|svg)$)/i', '', $attachment_url );
		$attachment_base_url = apply_filters( 'fusion_get_attachment_data_from_url_attachment_base_url', $attachment_base_url );

		$attachment_data['id'] = self::get_attachment_id_from_url( $attachment_base_url );

		if ( ! $attachment_data['id'] ) {
			return false;
		}

		$attachment_data = $this->get_attachment_data( $attachment_data['id'] );

		if ( $attachment_base_url !== $attachment_url ) {
			preg_match( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif|tiff|svg)$)/i', $attachment_url, $matches );
			$dimensions = explode( 'x', $matches[0] );
			$attachment_data['width'] = absint( $dimensions[0] );
			$attachment_data['height'] = absint( $dimensions[1] );
		}

		return $attachment_data;
	}

	/**
	 * Gets the most important attachment data.
	 *
	 * @since 1.2
	 * @access public
	 * @param int    $attachment_id The ID of the used attachment.
	 * @param string $size          The image size to be returned.
	 * @return array/bool           The attachment data of the image,
	 *                              false if the url is empty or attachment not found.
	 */
	public function get_attachment_data( $attachment_id = 0, $size = 'full' ) {

		if ( ! $attachment_id ) {
			return false;
		}

		$attachment_data['id'] = $attachment_id;

		if ( 'none' !== $size ) {
			$attachment_src = wp_get_attachment_image_src( $attachment_id, $size );
			$attachment_data['url'] = esc_url( $attachment_src[0] );
			$attachment_data['width'] = esc_attr( $attachment_src[1] );
			$attachment_data['height'] = esc_attr( $attachment_src[2] );
		}

		$attachment_data['alt'] = esc_attr( get_post_field( '_wp_attachment_image_alt', $attachment_id ) );

		// Check for WP versions prior to 4.6.
		if ( function_exists( 'wp_get_attachment_caption' ) ) {
			$attachment_data['caption'] = wp_get_attachment_caption( $attachment_id );
		} else {
			$post = get_post( $attachment_id );

			$attachment_data['caption'] = ( $post ) ? $post->post_excerpt : '';
		}
		$attachment_data['caption_attribute'] = esc_attr( strip_tags( $attachment_data['caption'] ) );
		$attachment_data['title']             = get_the_title( $attachment_id );
		$attachment_data['title_attribute']   = esc_attr( strip_tags( $attachment_data['title'] ) );

		return $attachment_data;
	}

	/**
	 * Deletes the resized images when the original image is deleted from the Wordpress Media Library.
	 * This is necessary in order to handle custom image sizes created from the Fusion_Image_Resizer class.
	 *
	 * @access public
	 * @param int   $post_id The post ID.
	 * @param array $delete_image_sizes Array of images sizes to be deleted. All are deleted if empty.
	 * @return void
	 */
	public function delete_resized_images( $post_id, $delete_image_sizes = array() ) {
		// Get attachment image metadata.
		$metadata = wp_get_attachment_metadata( $post_id );
		if ( ! $metadata ) {
			return;
		}
		// Do some bailing if we cannot continue.
		if ( ! isset( $metadata['file'] ) || ! isset( $metadata['image_meta']['resized_images'] ) ) {
			return;
		}
		$pathinfo = pathinfo( $metadata['file'] );
		$resized_images = isset( $metadata['image_meta']['resized_images'] ) ? $metadata['image_meta']['resized_images'] : array();
		// Get WordPress uploads directory (and bail if it doesn't exist).
		$wp_upload_dir = wp_upload_dir();
		$upload_dir    = $wp_upload_dir['basedir'];
		if ( ! is_dir( $upload_dir ) ) {
			return;
		}
		// Delete the resized images.
		foreach ( $resized_images as $handle => $dims ) {
			if ( ! empty( $delete_image_sizes ) && ! in_array( $handle, $delete_image_sizes ) ) {
				continue;
			}

			// Get the resized images filename.
			$file = $upload_dir . '/' . $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $dims . '.' . $pathinfo['extension'];
			// Delete the resized image.
			@unlink( $file );

			// Get the retina resized images filename.
			$retina_file = $upload_dir . '/' . $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $dims . '@2x.' . $pathinfo['extension'];
			// Delete the resized retina image.
			@unlink( $retina_file );
		}
	}


	/**
	 * Adds [fusion_imageframe], [fusion_gallery] and [fusion_image_before_after] images to Yoast SEO XML sitemap.
	 *
	 * @since 1.0.0
	 * @param array $images Current post images.
	 * @param int   $post_id The post ID.
	 */
	public function extract_img_src_for_yoast( $images, $post_id ) {
		$post    = get_post( $post_id );
		$content = $post->post_content;

		// For images from fusion_imageframe shortcode.
		if ( preg_match_all( '/\[fusion_imageframe(.+?)?\](?:(.+?)?\[\/fusion_imageframe\])?/', $content, $matches ) ) {

			foreach ( $matches[0] as $image_frame ) {
				$src = '';

				if ( false === strpos( $image_frame, '<img' ) && $image_frame ) {

					$pattern = get_shortcode_regex();
					$matches = array();
					preg_match( "/$pattern/s", $image_frame, $matches );
					$src = $matches[5];
				} else {
					preg_match( '/(src=["\'](.*?)["\'])/', $image_frame, $src );

					if ( array_key_exists( '2', $src ) ) {
						$src = $src[2];
					}
				}

				if ( ! in_array( $src, $images ) ) {
					$images[] = array(
						'src' => $src,
					);
				}
			}
		}

		// For images from fusion_gallery shortcode.
		if ( preg_match_all( '/\[fusion_gallery(.+?)?\](?:(.+?)?\[\/fusion_gallery\])?/', $content, $matches ) ) {
			foreach ( $matches[0] as $image_gallery ) {
				$atts = shortcode_parse_atts( $image_gallery );
				if ( isset( $atts['image_ids'] ) && ! empty( $atts['image_ids'] ) ) {
					$image_ids = explode( ',', $atts['image_ids'] );
					foreach ( $image_ids as $image_id ) {
						$images[] = array(
							'src' => wp_get_attachment_url( $image_id ),
						);
					}
				}
			}
		}

		// For images from fusion_image_before_after shortcode.
		if ( preg_match_all( '/\[fusion_image_before_after(.+?)?\](?:(.+?)?\[\/fusion_image_before_after\])?/', $content, $matches ) ) {
			foreach ( $matches[0] as $item ) {
				$atts = shortcode_parse_atts( $item );
				if ( isset( $atts['before_image'] ) && ! empty( $atts['before_image'] ) ) {
					$images[] = array(
						'src' => $atts['before_image'],
					);
				}
				if ( isset( $atts['after_image'] ) && ! empty( $atts['after_image'] ) ) {
					$images[] = array(
						'src' => $atts['after_image'],
					);
				}
			}
		}

		return $images;
	}

	/**
	 * Returns element orientation class, based on width and height ratio of an attachment image.
	 *
	 * @since 1.1
	 * @param int   $attachment_id ID of attachment image.
	 * @param array $attachment    An image attachment array.
	 * @param float $ratio         The aspect ratio threshold.
	 * @param int   $width_double  Width above which 2x2 content should be displayed.
	 * @return string              Orientation class.
	 */
	public function get_element_orientation_class( $attachment_id = '', $attachment = array(), $ratio = false, $width_double = false ) {
		$element_class = 'fusion-element-grid';
		$ratio         = $ratio ? $ratio : self::$masonry_grid_ratio;
		$width_double  = $width_double ? $width_double : self::$masonry_width_double;

		if ( empty( $attachment ) && '' !== $attachment_id ) {
			$attachment = wp_get_attachment_image_src( $attachment_id, 'full' );
		}

		if ( isset( $attachment[1] ) && isset( $attachment[2] ) ) {

			// Fallback to legacy calcs of Avada 5.4.2 or earlier.
			if ( '1.0' === $ratio ) {
				$fallback_ratio = 0.8;
				$lower_limit = ( $fallback_ratio / 2 ) + ( $fallback_ratio / 4 );
				$upper_limit = ( $fallback_ratio * 2 ) - ( $fallback_ratio / 2 );

				if ( $lower_limit > $attachment[2] / $attachment[1] ) {
					// Landscape image.
					$element_class = 'fusion-element-landscape';
				} elseif ( $upper_limit < $attachment[2] / $attachment[1] ) {
					// Portrait image.
					$element_class = 'fusion-element-portrait';
				} elseif ( $attachment[1] > $width_double ) {
					// 2x2 image.
					$element_class = 'fusion-element-landscape fusion-element-portrait';
				}
			} else {
				if ( $ratio < $attachment[1] / $attachment[2] ) {
					// Landscape image.
					$element_class = 'fusion-element-landscape';

				} elseif ( $ratio < $attachment[2] / $attachment[1] ) {
					// Portrait image.
					$element_class = 'fusion-element-portrait';
				} elseif ( $attachment[1] > $width_double ) {
					// 2x2 image.
					$element_class = 'fusion-element-landscape fusion-element-portrait';
				}
			}
		}

		return apply_filters( 'fusion_masonry_element_class', $element_class, $attachment_id );
	}

	/**
	 * Returns element orientation class, based on width and height ratio of an attachment image.
	 *
	 * @since 1.1
	 * @param string $element_orientation_class The orientation class.
	 * @return int|float.
	 */
	public function get_element_base_padding( $element_orientation_class = '' ) {
		$fusion_element_grid_padding = 0.8;

		$masonry_element_padding = array(
			'fusion-element-grid'      => $fusion_element_grid_padding,
			'fusion-element-landscape' => $fusion_element_grid_padding / 2,
			'fusion-element-portrait'  => $fusion_element_grid_padding * 2,
		);

		if ( isset( $masonry_element_padding[ $element_orientation_class ] ) ) {
			$fusion_element_grid_padding = $masonry_element_padding[ $element_orientation_class ];
		}

		return $fusion_element_grid_padding;
	}

	/**
	 * Filters element orientation class, based on image meta.
	 *
	 * @since 1.5
	 * @param string $element_class Orientation class.
	 * @param int    $attachment_id ID of attachment image.
	 * @return string               Orientation class.
	 */
	public function adjust_masonry_element_class( $element_class, $attachment_id = '' ) {

		if ( '' !== $attachment_id && '' !== get_post_meta( $attachment_id, 'fusion_masonry_element_layout', true ) ) {
			$element_class = get_post_meta( $attachment_id, 'fusion_masonry_element_layout', true );
		}

		return $element_class;
	}

	/**
	 * Add Image meta fields
	 *
	 * @param  array  $form_fields Fields to include in attachment form.
	 * @param  object $post        Attachment record in database.
	 * @return array  $form_fields Modified form fields.
	 */
	public function add_image_meta_fields( $form_fields, $post ) {

		if ( wp_attachment_is_image( $post->ID ) ) {
			$image_layout = '' !== get_post_meta( $post->ID, 'fusion_masonry_element_layout', true ) ? sanitize_text_field( get_post_meta( $post->ID, 'fusion_masonry_element_layout', true ) ) : '';

			$form_fields['fusion_masonry_element_layout'] = array(
				'label' => __( 'Masonry Image Layout', 'Avada' ),
				'input' => 'html',
				'html'  => '<select name="attachments[' . $post->ID . '][fusion_masonry_element_layout]" id="attachments[' . $post->ID . '][fusion_masonry_element_layout]"">
					    <option value="">' . esc_html__( 'Default', 'Avada' ) . '</option>
						<option value="fusion-element-grid" ' . selected( 'fusion-element-grid', $image_layout, false ) . '>' . esc_html__( '1x1', 'Avada' ) . '</option>
						<option value="fusion-element-landscape" ' . selected( 'fusion-element-landscape', $image_layout, false ) . '>' . esc_html__( 'Landscape', 'Avada' ) . '</option>
						<option value="fusion-element-portrait" ' . selected( 'fusion-element-portrait', $image_layout, false ) . '>' . esc_html__( 'Portrait', 'Avada' ) . '</option>
						<option value="fusion-element-landscape fusion-element-portrait" ' . selected( 'fusion-element-landscape fusion-element-portrait', $image_layout, false ) . '>' . esc_html__( '2x2', 'Avada' ) . '</option>
					</select>',
				'helps' => __( 'Set layout which will be used when image is displayed in masonry.', 'Avada' ),
			);
		}

		return $form_fields;
	}

	/**
	 * Save values of Photographer Name and URL in media uploader
	 *
	 * @param  array $post       The post data for database.
	 * @param  array $attachment Attachment fields from $_POST form.
	 * @return array $post       Modified post data.
	 */
	public function save_image_meta_fields( $post, $attachment ) {

		if ( wp_attachment_is_image( $post['ID'] ) ) {
			if ( isset( $attachment['fusion_masonry_element_layout'] ) ) {
				if ( '' !== $attachment['fusion_masonry_element_layout'] ) {
					update_post_meta( $post['ID'], 'fusion_masonry_element_layout', $attachment['fusion_masonry_element_layout'] );
				} else {
					delete_post_meta( $post['ID'], 'fusion_masonry_element_layout' );
				}
			}
		}

		return $post;
	}

	/**
	 * Style image meta fields.
	 */
	public function style_image_meta_fields() {
		global $pagenow;

		if ( 'post.php' === $pagenow && wp_attachment_is_image( get_the_ID() ) ) {
			echo '<style type="text/css">.compat-field-fusion_masonry_element_layout th, .compat-field-fusion_masonry_element_layout td{display: block;}.compat-field-fusion_masonry_element_layout th{padding-bottom: 10px;}</style>';
		}

	}

	/**
	 * Removes dynamically created thumbnails.
	 *
	 * @since 1.6
	 *
	 * @param array $data          Array of updated attachment meta data.
	 * @param int   $attachment_id Attachment post ID.
	 *
	 * @return array $data         Array of updated attachment meta data.
	 */
	public function remove_dynamically_generated_images( $data, $attachment_id ) {

		if ( ! isset( $data['image_meta']['resized_images']['fusion-500'] ) ) {
			$this->delete_resized_images( $attachment_id, array( 'fusion-500' ) );
		}

		return $data;
	}

}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
