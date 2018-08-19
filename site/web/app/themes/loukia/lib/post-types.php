<?php
// Register Custom Post Type collection
// Post Type Key: collection
function create_collection_cpt() {

	$labels = array(
		'name' => __( 'collections', 'Post Type General Name', 'sage' ),
		'singular_name' => __( 'collection', 'Post Type Singular Name', 'sage' ),
		'menu_name' => __( 'collections', 'sage' ),
		'name_admin_bar' => __( 'collection', 'sage' ),
		'archives' => __( 'collection Archives', 'sage' ),
		'attributes' => __( 'collection Attributes', 'sage' ),
		'parent_item_colon' => __( 'Parent collection:', 'sage' ),
		'all_items' => __( 'All collections', 'sage' ),
		'add_new_item' => __( 'Add New collection', 'sage' ),
		'add_new' => __( 'Add New', 'sage' ),
		'new_item' => __( 'New collection', 'sage' ),
		'edit_item' => __( 'Edit collection', 'sage' ),
		'update_item' => __( 'Update collection', 'sage' ),
		'view_item' => __( 'View collection', 'sage' ),
		'view_items' => __( 'View collections', 'sage' ),
		'search_items' => __( 'Search collection', 'sage' ),
		'not_found' => __( 'Not found', 'sage' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'sage' ),
		'featured_image' => __( 'Featured Image', 'sage' ),
		'set_featured_image' => __( 'Set featured image', 'sage' ),
		'remove_featured_image' => __( 'Remove featured image', 'sage' ),
		'use_featured_image' => __( 'Use as featured image', 'sage' ),
		'insert_into_item' => __( 'Insert into collection', 'sage' ),
		'uploaded_to_this_item' => __( 'Uploaded to this collection', 'sage' ),
		'items_list' => __( 'collections list', 'sage' ),
		'items_list_navigation' => __( 'collections list navigation', 'sage' ),
		'filter_items_list' => __( 'Filter collections list', 'sage' ),
	);
	$args = array(
		'label' => __( 'collection', 'sage' ),
		'description' => __( 'Collections', 'sage' ),
		'labels' => $labels,
		'menu_icon' => 'dashicons-book-alt',
		'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields', ),
		'taxonomies' => array(),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => true,
		'can_export' => true,
		'has_archive' => true,
		'hierarchical' => true,
		'exclude_from_search' => false,
		'show_in_rest' => true,
		'publicly_queryable' => true,
		'capability_type' => 'post',
	);
	register_post_type( 'collection', $args );

}
add_action( 'init', 'create_collection_cpt', 0 );


add_action( 'add_meta_boxes', 'mytheme_add_meta_box' );

if ( ! function_exists( 'mytheme_add_meta_box' ) ) {
	/**
	 * Add meta box to page screen
	 *
	 * This function handles the addition of variuos meta boxes to your page or post screens.
	 * You can add as many meta boxes as you want, but as a rule of thumb it's better to add
	 * only what you need. If you can logically fit everything in a single metabox then add
	 * it in a single meta box, rather than putting each control in a separate meta box.
	 *
	 * @since 1.0.0
	 */
	 function mytheme_add_meta_box() {
	 		add_meta_box( 'additional-page-metabox-options', esc_html__( 'Metabox Controls', 'mytheme' ), 'mytheme_metabox_controls', 'collection', 'normal', 'low' );
	 	}
	 }

	 if ( ! function_exists( 'mytheme_metabox_controls' ) ) {
	 	/**
	 	 * Meta box render function
	 	 *
	 	 * @param  object $post Post object.
	 	 * @since  1.0.0
	 	 */
	 	function mytheme_metabox_controls( $post ) {
	 		$meta = get_post_meta( $post->ID );
			$mytheme_featured_image = ( isset( $meta['mytheme_featured_image'][0] ) ) ? $meta['mytheme_featured_image'][0] : '';
			$mytheme_gallery = ( isset( $meta['mytheme_gallery'][0] ) ) ? $meta['mytheme_gallery'][0] : '';
	 		wp_nonce_field( 'mytheme_control_meta_box', 'mytheme_control_meta_box_nonce' ); // Always add nonce to your meta boxes!

	 		?>
	 		<style type="text/css">
	 			.post_meta_extras p{margin: 20px;}
	 			.post_meta_extras label{display:block; margin-bottom: 10px;}
	 		</style>
	 		<div class="post_meta_extras">
				<p>
	<label for="mytheme_featured_image"><?php esc_html_e( 'Featured Image', 'mytheme' ); ?></label>
	<span class="uploaded_image">
	<?php if ( '' !== $mytheme_featured_image ) : ?>
		<img src="<?php echo esc_url( $mytheme_featured_image ); ?>" />
	<?php endif; ?>
	</span>
	<input type="text" name="mytheme_featured_image" value="<?php echo esc_url( $mytheme_featured_image ); ?>" class="featured_image_upload">
	<input type="button" name="image_upload" value="<?php esc_html_e( 'Upload Image', 'mytheme' ); ?>" class="button upload_image_button">
	<input type="button" name="remove_image_upload" value="<?php esc_html_e( 'Remove Image', 'mytheme' ); ?>" class="button remove_image_button">
</p>

<p>
	<label for="mytheme_gallery"><?php esc_html_e( 'Project Gallery', 'mytheme' ); ?></label>
	<div class="separator gallery_images">
		<?php
		$img_array = ( isset( $mytheme_gallery ) && '' !== $mytheme_gallery ) ? explode( ',', $mytheme_gallery ) : '';
		if ( '' !== $img_array ) {
			foreach ( $img_array as $img ) {
				echo '<div class="gallery-item" data-id="' . esc_attr( $img ) . '"><div class="remove">x</div>' . wp_get_attachment_image( $img ) . '</div>';
			}
		}
		?>
	</div>
	<p class="separator gallery_buttons">
		<input id="mytheme_gallery_input" type="hidden" name="mytheme_gallery" value="<?php echo esc_attr( $mytheme_gallery ); ?>" />
		<input id="manage_gallery" title="<?php esc_html_e( 'Manage gallery', 'mytheme' ); ?>" type="button" class="button" value="<?php esc_html_e( 'Manage gallery', 'mytheme' ); ?>" />
		<input id="empty_gallery" title="<?php esc_html_e( 'Empty gallery', 'mytheme' ); ?>" type="button" class="button" value="<?php esc_html_e( 'Empty gallery', 'mytheme' ); ?>" />
	</p>
</p>
			</div>
	 		<?php
	 	}
	 }

	 add_action( 'save_post', 'mytheme_save_metaboxes' );

	 if ( ! function_exists( 'mytheme_save_metaboxes' ) ) {
	 	/**
	 	 * Save controls from the meta boxes
	 	 *
	 	 * @param  int $post_id Current post id.
	 	 * @since 1.0.0
	 	 */
	 	function mytheme_save_metaboxes( $post_id ) {
	 		/*
	 		 * We need to verify this came from the our screen and with proper authorization,
	 		 * because save_post can be triggered at other times. Add as many nonces, as you
	 		 * have metaboxes.
	 		 */
	 		if ( ! isset( $_POST['mytheme_control_meta_box_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['mytheme_control_meta_box_nonce'] ), 'mytheme_control_meta_box' ) ) { // Input var okay.
	 			return $post_id;
	 		}

	 		// Check the user's permissions.
	 		if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) { // Input var okay.
	 			if ( ! current_user_can( 'edit_page', $post_id ) ) {
	 				return $post_id;
	 			}
	 		} else {
	 			if ( ! current_user_can( 'edit_post', $post_id ) ) {
	 				return $post_id;
	 			}
	 		}

	 		/*
	 		 * If this is an autosave, our form has not been submitted,
	 		 * so we don't want to do anything.
	 		 */
	 		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	 			return $post_id;
	 		}

	 		/* Ok to save */
			if ( isset( $_POST['mytheme_featured_image'] ) ) { // Input var okay.
				update_post_meta( $post_id, 'mytheme_featured_image', sanitize_text_field( wp_unslash( $_POST['mytheme_featured_image'] ) ) ); // Input var okay.
			}
			if ( isset( $_POST['mytheme_gallery'] ) ) { // Input var okay.
				update_post_meta( $post_id, 'mytheme_gallery', sanitize_text_field( wp_unslash( $_POST['mytheme_gallery'] ) ) ); // Input var okay.
			}


	 	}
	 }
