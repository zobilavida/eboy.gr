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