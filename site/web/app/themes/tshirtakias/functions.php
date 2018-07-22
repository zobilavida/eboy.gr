<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/wrapper.php',   // Theme wrapper class
  'lib/customizer.php', // Theme customizer
  'plugins/facetwp/index.php' // Theme extends
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'tshirtakias'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

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


function load_stamps () {

echo do_shortcode('[facetwp facet="stamps"]');
    // WP_Query arguments
    $args = array(
      "post_type" => "stamps",
      "post_status" => "publish",
    //  'meta_key'			=> 'rating',
    //	'orderby'			=> 'post__in',
      "order" => "DESC",
      "posts_per_page" => 15
    );

    $query = new WP_Query( $args );
    if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
    $thumb_id = get_post_thumbnail_id();
    $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'thumbnail-size', true);
    $thumb_url = $thumb_url_array[0];


?>
  <div class="w-25">
      <img src="<?php echo $thumb_url ?>" class="img-fluid stamp" alt="Responsive image">
    </div>
<?php
endwhile;
endif;
}
add_action ( 'tshirtakias_stamps', 'load_stamps', 10 );

function load_product () {
?>

<?php
$args = array(
'post_type' => 'product',
'stock' => 1,
'posts_per_page' => 4,
'orderby' =>'date',
'order' => 'DESC' );
$loop = new WP_Query( $args );
while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>
<div class="span3">
<a id="id-<?php the_id(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
<?php if (has_post_thumbnail( $loop->post->ID )) echo get_the_post_thumbnail($loop->post->ID, 'shop_catalog'); else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="My Image Placeholder" width="65px" height="115px" />'; ?>
<h3><?php the_title(); ?></h3>
<span class="price"><?php echo $product->get_price_html(); ?></span>
</a>
<?php  $product->get_attributes(); ?>
</div><!-- /span3 -->
<?php endwhile; ?>
<?php wp_reset_query(); ?>
<?php
}
add_action ( 'tshirtakias_product', 'load_product', 10 );

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
  echo $mens_product_id->ID; # You will get different product ids here
}

  $get_featured_mens_cat = array(
	'taxonomy'     => 'product_cat',
  'slug'          => 'mens',
	'orderby'      => 'name',
	'hide_empty'   => '0',
	'include'      => $cat_array
);
$mens_category = get_categories( $get_featured_mens_cat );
$j = 1;
foreach ($mens_category as $mens_cat) {
	$mens_thumbnail_id = get_woocommerce_term_meta( $mens_cat->term_id, 'thumbnail_id', true ); // Get Category Thumbnail
	$mens_image = wp_get_attachment_url( $mens_thumbnail_id );
	if ( $mens_image ) {
    echo '<div class="p-2 w-100">';
    echo '<a href="#" class="project-preview" data-project-id="' . $mens_product_id->ID . '">';
    echo $mens_cat->name;
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
  echo $womens_product_id->ID; # You will get different product ids here
}

  $get_featured_womens_cat = array(
	'taxonomy'     => 'product_cat',
  'slug'          => 'womens',
	'orderby'      => 'name',
	'hide_empty'   => '0',
	'include'      => $cat_array
);
$womens_category = get_categories( $get_featured_womens_cat );
$w = 1;
foreach ($womens_category as $womens_cat) {
	$womens_thumbnail_id = get_woocommerce_term_meta( $womens_cat->term_id, 'thumbnail_id', true ); // Get Category Thumbnail
	$womens_image = wp_get_attachment_url( $womens_thumbnail_id );
	if ( $womens_image ) {
    echo '<div class="p-2 w-100">';
    echo '<a href="#" class="project-preview" data-project-id="' . $womens_product_id->ID . '">';
    echo $womens_cat->name;
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
  echo $hoodies_product_id->ID; # You will get different product ids here
}

  $get_featured_hoodies_cat = array(
	'taxonomy'     => 'product_cat',
  'slug'          => 'hoodies',
	'orderby'      => 'name',
	'hide_empty'   => '0',
	'include'      => $cat_array
);
$hoodies_category = get_categories( $get_featured_hoodies_cat );
$w = 1;
foreach ($hoodies_category as $hoodies_cat) {
	$hoodies_thumbnail_id = get_woocommerce_term_meta( $hoodies_cat->term_id, 'thumbnail_id', true ); // Get Category Thumbnail
	$hoodies_image = wp_get_attachment_url( $hoodies_thumbnail_id );
	if ( $hoodies_image ) {
    echo '<div class="p-2 w-100">';
    echo '<a href="#" class="project-preview" data-project-id="' . $hoodies_product_id->ID . '">';
    echo $hoodies_cat->name;
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
  echo $kids_product_id->ID; # You will get different product ids here
}

  $get_featured_kids_cat = array(
	'taxonomy'     => 'product_cat',
  'slug'          => 'kids',
	'orderby'      => 'name',
	'hide_empty'   => '0',
	'include'      => $cat_array
);
$kids_category = get_categories( $get_featured_kids_cat );
$w = 1;
foreach ($kids_category as $kids_cat) {
	$kids_thumbnail_id = get_woocommerce_term_meta( $kids_cat->term_id, 'thumbnail_id', true ); // Get Category Thumbnail
	$kids_image = wp_get_attachment_url( $kids_thumbnail_id );
	if ( $kids_image ) {
    echo '<div class="p-2 w-100">';
    echo '<a href="#" class="project-preview" data-project-id="' . $kids_product_id->ID . '">';
    echo $kids_cat->name;
		echo '<img src="' . $kids_image . '" alt="" />';
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
  echo $babies_product_id->ID; # You will get different product ids here
}

  $get_featured_babies_cat = array(
	'taxonomy'     => 'product_cat',
  'slug'          => 'babies',
	'orderby'      => 'name',
	'hide_empty'   => '0',
	'include'      => $cat_array
);
$babies_category = get_categories( $get_featured_babies_cat );
$w = 1;
foreach ($babies_category as $babies_cat) {
	$babies_thumbnail_id = get_woocommerce_term_meta( $babies_cat->term_id, 'thumbnail_id', true ); // Get Category Thumbnail
	$babies_image = wp_get_attachment_url( $babies_thumbnail_id );
	if ( $babies_image ) {
    echo '<div class="p-2 w-100">';
    echo '<a href="#" class="project-preview" data-project-id="' . $babies_product_id->ID . '">';
    echo $babies_cat->name;
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

remove_action ('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );



function load_single_product_content () {
     $post_id = intval(isset($_POST['post_id']) ? $_POST['post_id'] : 0);

     if ($post_id > 0) {
         $the_query = new WP_query(array('p' => $post_id, 'post_type' => 'product'));
         if ($the_query->have_posts()) {
             while ($the_query->have_posts()) : $the_query->the_post();
             wc_get_template_part( 'content', 'single-product' );
         endwhile;
         } else {
             echo "There were no products found";
         }
     }
     wp_die();
}

add_action('wp_ajax_load_single_product_content', 'load_single_product_content');
add_action('wp_ajax_nopriv_load_single_product_content', 'load_single_product_content');
