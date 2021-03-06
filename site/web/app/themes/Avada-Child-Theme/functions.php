<?php
//include get_template_directory() . '/../Avada-Child-Theme/plugins/facetwp/index.php';
function avada_child_styles() {
    if ( is_page_template('store-app.php') ) {
        wp_enqueue_style( 'avada-child-stylesheet', get_stylesheet_uri()  );
}
}
add_action('wp_enqueue_scripts', 'avada_child_styles');

function avada_child_scripts() {
  if ( is_page_template('store-app.php') ) {
    wp_enqueue_script( 'wc-single-product' );
    wp_enqueue_script( 'wc-add-to-cart-variation' );
   wp_enqueue_script( 'flexslider' );
  //  wp_enqueue_script( 'photoswipe-ui-default' );
  // wp_enqueue_script( 'photoswipe' );
  // wp_enqueue_script( 'zoom' );
  }
        wp_enqueue_script('child_script', get_stylesheet_directory_uri() . '/js/main.js', array('jquery'), false, true);
        wp_enqueue_script('ifbreakpoint', get_stylesheet_directory_uri() . '/js/IfBreakpoint.js', array('jquery'), false, true);
          wp_register_script('pa_script', get_stylesheet_directory_uri() . '/js/custom_ajax.js', array('jquery'), false, true);
          wp_localize_script( 'pa_script', 'singleprojectajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
          wp_enqueue_script('pa_script');
          wp_register_script('pb_script', get_stylesheet_directory_uri() . '/js/related_ajax.js', array('jquery'), false, true);
          wp_localize_script( 'pb_script', 'singleprojectajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
          wp_enqueue_script('pb_script');
}

add_action('wp_enqueue_scripts', 'avada_child_scripts', 20);

function add_admin_scripts( $hook ) {

global $post;

if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
if ( 'stamps' === $post->post_type ) {
wp_enqueue_script( 'myscript', get_stylesheet_directory_uri().'/js/second_featured.js' );
}
}
}
add_action( 'admin_enqueue_scripts', 'add_admin_scripts', 10, 1 );

// Add svg & swf support
function cc_mime_types( $mimes ){
    $mimes['svg'] = 'image/svg+xml';
    $mimes['swf']  = 'application/x-shockwave-flash';

    return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

if ( ! function_exists('custom_stamps_post_type') ) {

// Register Custom Post Type
function custom_stamps_post_type() {

	$labels = array(
		'name'                  => _x( 'Stamps', 'Post Type General Name', 'tshirtakias' ),
		'singular_name'         => _x( 'Stamp', 'Post Type Singular Name', 'tshirtakias' ),
		'menu_name'             => __( 'Stamps', 'tshirtakias' ),
		'name_admin_bar'        => __( 'Stamp', 'tshirtakias' ),
		'archives'              => __( 'Stamps Archives', 'tshirtakias' ),
		'attributes'            => __( 'Stamps Attributes', 'tshirtakias' ),
		'parent_item_colon'     => __( 'Parent Stamp:', 'tshirtakias' ),
		'all_items'             => __( 'All Stamps', 'tshirtakias' ),
		'add_new_item'          => __( 'Add New Stamp', 'tshirtakias' ),
		'add_new'               => __( 'Add New', 'tshirtakias' ),
		'new_item'              => __( 'New Stamp', 'tshirtakias' ),
		'edit_item'             => __( 'Edit Stamp', 'tshirtakias' ),
		'update_item'           => __( 'Update Stamp', 'tshirtakias' ),
		'view_item'             => __( 'View Stamp', 'tshirtakias' ),
		'view_items'            => __( 'View Stamps', 'tshirtakias' ),
		'search_items'          => __( 'Search Stamp', 'tshirtakias' ),
		'not_found'             => __( 'Not found', 'tshirtakias' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'tshirtakias' ),
		'featured_image'        => __( 'Featured Image', 'tshirtakias' ),
		'set_featured_image'    => __( 'Set featured image', 'tshirtakias' ),
		'remove_featured_image' => __( 'Remove featured image', 'tshirtakias' ),
		'use_featured_image'    => __( 'Use as featured image', 'tshirtakias' ),
		'insert_into_item'      => __( 'Insert into Stamp', 'tshirtakias' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Stamp', 'tshirtakias' ),
		'items_list'            => __( 'Stamps list', 'tshirtakias' ),
		'items_list_navigation' => __( 'Stamps list navigation', 'tshirtakias' ),
		'filter_items_list'     => __( 'Filter Stamps list', 'tshirtakias' ),
	);
	$args = array(
		'label'                 => __( 'Stamp', 'tshirtakias' ),
		'description'           => __( 'Stamps Directory Listing', 'tshirtakias' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'taxonomies'            => array( 'stamp_category' ),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
	);
	register_post_type( 'stamps', $args );

}
add_action( 'init', 'custom_stamps_post_type', 0 );

}

// Register Custom Taxonomy
function custom_stamp_cats() {

	$labels = array(
		'name'                       => _x( 'Stamp Category', 'Taxonomy General Name', 'tshirtakias' ),
		'singular_name'              => _x( 'Stamps Category', 'Taxonomy Singular Name', 'tshirtakias' ),
		'menu_name'                  => __( 'Stamps Categories', 'tshirtakias' ),
		'all_items'                  => __( 'All Stamps Categories', 'tshirtakias' ),
		'parent_item'                => __( 'Parent Stamps Categories', 'tshirtakias' ),
		'parent_item_colon'          => __( 'Parent Item:', 'tshirtakias' ),
		'new_item_name'              => __( 'New Stamps Categories Name', 'tshirtakias' ),
		'add_new_item'               => __( 'Add New Stamp Category', 'tshirtakias' ),
		'edit_item'                  => __( 'Edit Stamp Category', 'tshirtakias' ),
		'update_item'                => __( 'Update Stamp Category', 'tshirtakias' ),
		'view_item'                  => __( 'View Stamp Category', 'tshirtakias' ),
		'separate_items_with_commas' => __( 'Separate Stamps Categories with commas', 'tshirtakias' ),
		'add_or_remove_items'        => __( 'Add or remove Stamps Categories', 'tshirtakias' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'tshirtakias' ),
		'popular_items'              => __( 'Popular Stamps Categories', 'tshirtakias' ),
		'search_items'               => __( 'Search Stamps Categories', 'tshirtakias' ),
		'not_found'                  => __( 'Not Found', 'tshirtakias' ),
		'no_terms'                   => __( 'No Stamps Categories', 'tshirtakias' ),
		'items_list'                 => __( 'Stamps Categories list', 'tshirtakias' ),
		'items_list_navigation'      => __( 'Stamps Categories list navigation', 'tshirtakias' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'stamp_cat', array( 'stamps' ), $args );

}
add_action( 'init', 'custom_stamp_cats', 0 );


add_action( 'add_meta_boxes', 'listing_image_add_metabox' );
function listing_image_add_metabox () {
	add_meta_box( 'listingimagediv', __( 'PNG image', 'tshirtakias' ), 'listing_image_metabox', 'stamps', 'side', 'low');
}

function listing_image_metabox ( $post ) {
	global $content_width, $_wp_additional_image_sizes;

	$image_id = get_post_meta( $post->ID, '_listing_image_id', true );

	$old_content_width = $content_width;
	$content_width = 254;

	if ( $image_id && get_post( $image_id ) ) {

		if ( ! isset( $_wp_additional_image_sizes['post-thumbnail'] ) ) {
			$thumbnail_html = wp_get_attachment_image( $image_id, array( $content_width, $content_width ) );
		} else {
			$thumbnail_html = wp_get_attachment_image( $image_id, 'post-thumbnail' );
		}

		if ( ! empty( $thumbnail_html ) ) {
			$content = $thumbnail_html;
			$content .= '<p class="hide-if-no-js"><a href="javascript:;" id="remove_listing_image_button" >' . esc_html__( 'Remove PNG image', 'tshirtakias' ) . '</a></p>';
			$content .= '<input type="hidden" id="upload_listing_image" name="_listing_cover_image" value="' . esc_attr( $image_id ) . '" />';
		}

		$content_width = $old_content_width;
	} else {

		$content = '<img src="" style="width:' . esc_attr( $content_width ) . 'px;height:auto;border:0;display:none;" />';
		$content .= '<p class="hide-if-no-js"><a title="' . esc_attr__( 'Set PNG image', 'tshirtakias' ) . '" href="javascript:;" id="upload_listing_image_button" id="set-listing-image" data-uploader_title="' . esc_attr__( 'Choose an image', 'tshirtakias' ) . '" data-uploader_button_text="' . esc_attr__( 'Set listing image', 'tshirtakias' ) . '">' . esc_html__( 'Set listing image', 'tshirtakias' ) . '</a></p>';
		$content .= '<input type="hidden" id="upload_listing_image" name="_listing_cover_image" value="" />';

	}

	echo $content;
}

add_action( 'save_post', 'listing_image_save', 10, 1 );
function listing_image_save ( $post_id ) {
	if( isset( $_POST['_listing_cover_image'] ) ) {
		$image_id = (int) $_POST['_listing_cover_image'];
		update_post_meta( $post_id, '_listing_image_id', $image_id );
	}
}

function listing_image_get_meta ($value) {
global $post;

$image_id = get_post_meta ($post->ID, $value, true);

if (!empty ($image_id)) {
return is_array ($image_id) ? stripslashes_deep ($image_id) : stripslashes (wp_kses_decode_entities ($image_id));
} else {
return false;
}
}
function load_stamps () {

    // WP_Query arguments
    $args = array(
      "post_type" => "stamps",
      "post_status" => "publish",
    //  'meta_key'			=> 'rating',
    //	'orderby'			=> 'post__in',
      "order" => "DESC",
      "posts_per_page" => 32
    );

    $query = new WP_Query( $args );
    if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
    $thumb_id = get_post_thumbnail_id();
    $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'thumbnail-size', true);
    $thumb_url = $thumb_url_array[0];
    $imagething = wp_get_attachment_image_src( listing_image_get_meta('_listing_image_id'), 'full');

?>
  <div class="w-25">
    <a href="#" stamp-name="<?php the_title(); ?>" stamp-url="<?php echo $imagething[0] ?>" class="stamp">
      <img src="<?php echo $thumb_url ?>" class="img-fluid stamp" alt="Responsive image">
    </a>
    </div>
<?php
endwhile;
endif;
}
add_action ( 'tshirtakias_stamps', 'load_stamps', 10 );


//remove_action ('woocommerce_single_custom_product_summary', 'woocommerce_template_single_meta', 40 );
//remove_action ('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
remove_action ('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
remove_action ('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

//remove_action ('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
//remove_action ('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
//remove_action ('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );




add_action( 'woocommerce_after_single_custom_product_summary', 'woocommerce_output_custom_related_products', 10 );
function woocommerce_output_custom_related_products($args = array())
{
    global $product, $woocommerce_loop;
    $defaults = array('posts_per_page' => 4, 'columns' => 4, 'orderby' => 'rand', 'order' => 'desc');
    $args = wp_parse_args($args, $defaults);
    // Get visble related products then sort them at random.
    $args['related_products'] = array_filter(array_map('wc_get_product', wc_get_related_products($product->get_id(), $args['posts_per_page'], $product->get_upsell_ids())), 'wc_products_array_filter_visible');
    // Handle orderby.
    $args['related_products'] = wc_products_array_orderby($args['related_products'], $args['orderby'], $args['order']);
    // Set global loop values.
    $woocommerce_loop['name'] = 'related';
    $woocommerce_loop['columns'] = apply_filters('woocommerce_related_products_columns', $args['columns']);
    wc_get_template('single-product/custom-related.php', $args);
}


add_action( 'woocommerce_before_single_product_custom_summary', 'woocommerce_show_product_custom_images', 20 );

function woocommerce_template_custom_loop_product_link_open() {
  global $product;
  $id = $product->get_id();

  echo '<a href="#" data-project-id="' . $id . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link product-preview">';
}
add_action( 'woocommerce_before_custom_shop_loop_item', 'woocommerce_template_custom_loop_product_link_open', 10 );

function woocommerce_template_loop_custom_product_link_close() {
  global $product;
$thumbnail = get_the_post_thumbnail_url($product->ID);

  echo '<img src="'. $thumbnail .'" class="img-fluid" alt="Responsive image">';
  echo '</a>';
}
add_action( 'woocommerce_after_shop_custom_loop_item', 'woocommerce_template_loop_custom_product_link_close', 5 );



	function woocommerce_show_product_custom_images() {
		wc_get_template( 'single-product/product-custom-image.php' );
	}

  function woocommerce_template_single_custom_title() {
    wc_get_template( 'single-product/custom-title.php' );
  }

add_action( 'woocommerce_single_custom_product_summary', 'woocommerce_custom_variable_add_to_cart', 30 );

function woocommerce_custom_variable_add_to_cart() {
  global $product;

  // Enqueue variation scripts.
  wp_enqueue_script( 'wc-add-to-cart-variation' );

  // Get Available variations?
  $get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

  // Load the template.
  wc_get_template( 'single-product/add-to-cart/custom-variable.php', array(
    'available_variations' => $get_variations ? $product->get_available_variations() : false,
    'attributes'           => $product->get_variation_attributes(),
    'selected_attributes'  => $product->get_default_attributes(),
  ) );
}

function get_product_subcategories_list( $category_slug ){
    $terms_html = array();
    $taxonomy = 'product_cat';
    // Get the product category (parent) WP_Term object
    $parent = get_term_by( 'slug', $category_slug, $taxonomy );
    // Get an array of the subcategories IDs (children IDs)
    $children_ids = get_term_children( $parent->term_id, $taxonomy );

    // Loop through each children IDs
    foreach($children_ids as $children_id){
        $term = get_term( $children_id, $taxonomy ); // WP_Term object
        $term_link = get_term_link( $term, $taxonomy ); // The term link
        if ( is_wp_error( $term_link ) ) $term_link = '';
        // Set in an array the html formated subcategory name/link
        $terms_html[] = '<a href="' . esc_url( $term_link ) . '" rel="tag" class="' . $term->slug . '">' . $term->name . '</a>';
    }
    return '<span class="subcategories-' . $category_slug . '">' . implode( ', ', $terms_html ) . '</span>';
}

function get_product_category_mens_images () {

  $mens_args = array(
    'post_type' => 'product',
    'product_cat' => 'mens',
    'post_status' => 'publish',
    'posts_per_page' => '1',
    'orderby' => 'ID',
    'order' => 'ASC', # Keep ASC for First 3 products or keep DESC for Latest 3 products as required
);
$mens_product_id = get_posts($mens_args);

foreach($mens_product_id AS $mens_product_id){
//  echo $mens_product_id->ID; # You will get different product ids here
}

  $get_featured_mens_cat = array(
	'taxonomy'     => 'product_cat',
  'slug'          => 'mens',
	'orderby'      => 'name',
	'hide_empty'   => '0',
	//'include'      => $cat_array
);
$mens_category = get_categories( $get_featured_mens_cat );
$j = 1;
foreach ($mens_category as $mens_cat) {
	$mens_thumbnail_id = get_woocommerce_term_meta( $mens_cat->term_id, 'thumbnail_id', true ); // Get Category Thumbnail
	$mens_image = wp_get_attachment_url( $mens_thumbnail_id );
	if ( $mens_image ) {
    echo '<div class="col-2 col-lg-12 text-center p-2 d-none d-md-block">';
    echo $mens_cat->name ;
    echo '</div>';
    echo '<div class="col-2 col-lg-12 text-center p-2">';
    echo '<a href="#" class="product-preview mens" data-project-id="' . $mens_product_id->ID . '" data-href="https://eboy.gr/tshirtakias/mens/">';
		echo '<img src="' . $mens_image . '" alt="" />';
    echo '</a>';
    echo '</div>';
	}
//echo $cat->name; // Get Category Name
//	echo $cat->description; // Get Category Description
	$j++;
}
// Reset Post Data
wp_reset_query();
}
add_action ( 'tshirtakias_product_category_images', 'get_product_category_mens_images', 10 );


function get_product_category_womens_images () {

  $womens_args = array(
    'post_type' => 'product',
    'product_cat' => 'womens',
    'post_status' => 'publish',
    'posts_per_page' => '1',
    'orderby' => 'ID',
    'order' => 'ASC', # Keep ASC for First 3 products or keep DESC for Latest 3 products as required
);
$womens_product_id = get_posts($womens_args);

foreach($womens_product_id AS $womens_product_id){
//  echo $womens_product_id->ID; # You will get different product ids here
}

  $get_featured_womens_cat = array(
	'taxonomy'     => 'product_cat',
  'slug'          => 'womens',
	'orderby'      => 'name',
	'hide_empty'   => '0',
	//'include'      => $cat_array
);
$womens_category = get_categories( $get_featured_womens_cat );
$w = 1;
foreach ($womens_category as $womens_cat) {
	$womens_thumbnail_id = get_woocommerce_term_meta( $womens_cat->term_id, 'thumbnail_id', true ); // Get Category Thumbnail
	$womens_image = wp_get_attachment_url( $womens_thumbnail_id );
	if ( $womens_image ) {
    echo '<div class="col-2 col-lg-12 text-center p-2 d-none d-md-block">';
    echo $womens_cat->name ;
    echo '</div>';
    echo '<div class="col-2 col-lg-12 text-center p-2">';
    echo '<a href="#" class="product-preview mens" data-project-id="' . $womens_product_id->ID . '" data-href="https://eboy.gr/tshirtakias/womens/">';
		echo '<img src="' . $womens_image . '" alt="" />';
    echo '</a>';
    echo '</div>';
	}
//echo $cat->name; // Get Category Name
//	echo $cat->description; // Get Category Description
	$w++;
}
// Reset Post Data
wp_reset_query();
}
add_action ( 'tshirtakias_product_category_images', 'get_product_category_womens_images', 20 );

function get_product_category_hoodies_images () {

  $hoodies_args = array(
    'post_type' => 'product',
    'product_cat' => 'hoodies',
    'post_status' => 'publish',
    'posts_per_page' => '1',
    'orderby' => 'ID',
    'order' => 'ASC', # Keep ASC for First 3 products or keep DESC for Latest 3 products as required
);
$hoodies_product_id = get_posts($hoodies_args);

foreach($hoodies_product_id AS $hoodies_product_id){
//  echo $hoodies_product_id->ID; # You will get different product ids here
}

  $get_featured_hoodies_cat = array(
	'taxonomy'     => 'product_cat',
  'slug'          => 'hoodies',
	'orderby'      => 'name',
	'hide_empty'   => '0',
	//'include'      => $cat_array
);
$hoodies_category = get_categories( $get_featured_hoodies_cat );
$w = 1;
foreach ($hoodies_category as $hoodies_cat) {
	$hoodies_thumbnail_id = get_woocommerce_term_meta( $hoodies_cat->term_id, 'thumbnail_id', true ); // Get Category Thumbnail
	$hoodies_image = wp_get_attachment_url( $hoodies_thumbnail_id );
	if ( $hoodies_image ) {
    echo '<div class="col-2 col-lg-12 text-center p-2 d-none d-md-block">';
    echo $hoodies_cat->name ;
    echo '</div>';
    echo '<div class="col-2 col-lg-12 text-center p-2">';
    echo '<a href="#" class="product-preview mens" data-project-id="' . $hoodies_product_id->ID . '" data-href="https://eboy.gr/tshirtakias/hoodies/">';
		echo '<img src="' . $hoodies_image . '" alt="" />';
    echo '</a>';
    echo '</div>';
	}
//echo $cat->name; // Get Category Name
//	echo $cat->description; // Get Category Description
	$w++;
}
// Reset Post Data
wp_reset_query();
}
add_action ( 'tshirtakias_product_category_images', 'get_product_category_hoodies_images', 30 );

function get_product_category_kids_images () {

  $kids_args = array(
    'post_type' => 'product',
    'product_cat' => 'kids',
    'post_status' => 'publish',
    'posts_per_page' => '1',
    'orderby' => 'ID',
    'order' => 'ASC', # Keep ASC for First 3 products or keep DESC for Latest 3 products as required
);
$kids_product_id = get_posts($kids_args);

foreach($kids_product_id AS $kids_product_id){
  //echo $kids_product_id->ID; # You will get different product ids here
}

  $get_featured_kids_cat = array(
	'taxonomy'     => 'product_cat',
  'slug'          => 'kids',
	'orderby'      => 'name',
	'hide_empty'   => '0',
	//'include'      => $cat_array
);
$kids_category = get_categories( $get_featured_kids_cat );
$w = 1;
foreach ($kids_category as $kids_cat) {
	$kids_thumbnail_id = get_woocommerce_term_meta( $kids_cat->term_id, 'thumbnail_id', true ); // Get Category Thumbnail
	$kids_image = wp_get_attachment_url( $kids_thumbnail_id );
	if ( $kids_image ) {
    echo '<div class="col-2 col-lg-12 text-center p-2 d-none d-md-block">';
    echo $kids_cat->name ;
    echo '</div>';	}
    echo '<div class="col-2 col-lg-12 text-center p-2">';
    echo '<a href="#" class="product-preview mens" data-project-id="' . $kids_product_id->ID . '" data-href="https://eboy.gr/tshirtakias/kids/">';
		echo '<img src="' . $kids_image . '" alt="" />';
    echo '</a>';
    echo '</div>';
//echo $cat->name; // Get Category Name
//	echo $cat->description; // Get Category Description
	$w++;
}
// Reset Post Data
wp_reset_query();
}
add_action ( 'tshirtakias_product_category_images', 'get_product_category_kids_images', 40 );

function get_product_category_babies_images () {

  $babies_args = array(
    'post_type' => 'product',
    'product_cat' => 'babies',
    'post_status' => 'publish',
    'posts_per_page' => '1',
    'orderby' => 'ID',
    'order' => 'ASC', # Keep ASC for First 3 products or keep DESC for Latest 3 products as required
);
$babies_product_id = get_posts($babies_args);

foreach($babies_product_id AS $babies_product_id){
//  echo $babies_product_id->ID; # You will get different product ids here
}

  $get_featured_babies_cat = array(
	'taxonomy'     => 'product_cat',
  'slug'          => 'babies',
	'orderby'      => 'name',
	'hide_empty'   => '0',
	//'include'      => $cat_array
);
$babies_category = get_categories( $get_featured_babies_cat );
$w = 1;
foreach ($babies_category as $babies_cat) {
	$babies_thumbnail_id = get_woocommerce_term_meta( $babies_cat->term_id, 'thumbnail_id', true ); // Get Category Thumbnail
	$babies_image = wp_get_attachment_url( $babies_thumbnail_id );
	if ( $babies_image ) {
    echo '<div class="col-2 col-lg-12 text-center p-2 d-none d-md-block">';
    echo $babies_cat->name ;
    echo '</div>';
    echo '<div class="col-2 col-lg-12 text-center p-2">';
    echo '<a href="#" class="product-preview babies" data-project-id="' . $babies_product_id->ID . '" data-href="https://eboy.gr/tshirtakias/babies/">';
		echo '<img src="' . $babies_image . '" alt="" />';
    echo '</a>';
    echo '</div>';
	}
//echo $cat->name; // Get Category Name
//	echo $cat->description; // Get Category Description
	$w++;
}
// Reset Post Data
wp_reset_query();
}
add_action ( 'tshirtakias_product_category_images', 'get_product_category_babies_images', 50 );

function load_single_product_content () {
     $post_id = intval(isset($_POST['post_id']) ? $_POST['post_id'] : 0);

     if ($post_id > 0) {
         $the_query = new WP_query(array('p' => $post_id, 'post_type' => 'product'));
         if ($the_query->have_posts()) {
             while ($the_query->have_posts()) : $the_query->the_post();
             wc_get_template_part( 'content', 'single-custom-product' );
         endwhile;
         } else {
             echo "There were no products found";
         }
     }
     wp_die();
}

add_action('wp_ajax_load_single_product_content', 'load_single_product_content');
add_action('wp_ajax_nopriv_load_single_product_content', 'load_single_product_content');





function kia_custom_option(){
    $value = isset( $_POST['_custom_option'] ) ? sanitize_text_field( $_POST['_custom_option'] ) : '';
    printf( '<input name="_custom_option" value="%s" class="stamp_code" />', __( 'Stamp code', 'kia-plugin-textdomain' ), esc_attr( $value ) );
}
add_action( 'woocommerce_before_add_to_cart_button', 'kia_custom_option', 9 );

function kia_add_to_cart_validation($passed, $product_id, $qty){

    if( isset( $_POST['_custom_option'] ) && sanitize_text_field( $_POST['_custom_option'] ) == '' ){
        $product = wc_get_product( $product_id );
        wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter some Stamp code.', 'kia-plugin-textdomain' ), $product->get_title() ), 'error' );
        return false;
    }

    return $passed;

}
add_filter( 'woocommerce_add_to_cart_validation', 'kia_add_to_cart_validation', 10, 3 );

function kia_add_cart_item_data( $cart_item, $product_id ){

    if( isset( $_POST['_custom_option'] ) ) {
        $cart_item['custom_option'] = sanitize_text_field( $_POST['_custom_option'] );
    }

    return $cart_item;

}
add_filter( 'woocommerce_add_cart_item_data', 'kia_add_cart_item_data', 10, 2 );

function kia_get_cart_item_from_session( $cart_item, $values ) {

    if ( isset( $values['custom_option'] ) ){
        $cart_item['custom_option'] = $values['custom_option'];
    }

    return $cart_item;

}
add_filter( 'woocommerce_get_cart_item_from_session', 'kia_get_cart_item_from_session', 20, 2 );

function kia_add_order_item_meta( $item_id, $values ) {

    if ( ! empty( $values['custom_option'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'custom_option', $values['custom_option'] );
    }
}
add_action( 'woocommerce_add_order_item_meta', 'kia_add_order_item_meta', 10, 2 );

function kia_get_item_data( $other_data, $cart_item ) {

    if ( isset( $cart_item['custom_option'] ) ){

        $other_data[] = array(
            'name' => __( 'Stamp Code', 'kia-plugin-textdomain' ),
            'value' => sanitize_text_field( $cart_item['custom_option'] )
        );

    }

    return $other_data;

}
add_filter( 'woocommerce_get_item_data', 'kia_get_item_data', 10, 2 );

function kia_order_item_product( $cart_item, $order_item ){

    if( isset( $order_item['custom_option'] ) ){
        $cart_item_meta['custom_option'] = $order_item['custom_option'];
    }

    return $cart_item;

}
add_filter( 'woocommerce_order_item_product', 'kia_order_item_product', 10, 2 );

function kia_email_order_meta_fields( $fields ) {
    $fields['custom_field'] = __( 'Stamp Code', 'kia-plugin-textdomain' );
    return $fields;
}
add_filter('woocommerce_email_order_meta_fields', 'kia_email_order_meta_fields');


function wc_custom_get_gallery_image_html( $attachment_id, $main_image = false ) {
	$flexslider        = (bool) apply_filters( 'woocommerce_single_product_flexslider_enabled', get_theme_support( 'wc-product-gallery-slider' ) );
	$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
	$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
	$image_size        = apply_filters( 'woocommerce_gallery_image_size', $flexslider || $main_image ? 'woocommerce_single': $thumbnail_size );
	$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'small' ) );
	$thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
	$full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
	$image             = wp_get_attachment_image( $attachment_id, $image_size, false, array(
		'title'                   => get_post_field( 'post_title', $attachment_id ),
		'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
		'data-src'                => $full_src[0],
		'data-large_image'        => $full_src[0],
		'data-large_image_width'  => $full_src[1],
		'data-large_image_height' => $full_src[2],
		'class'                   => $main_image ? 'wp-post-image' : '',
	) );

	return '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" class="woocommerce-product-gallery__image 2">' . $image . '</div>';
}

?>
