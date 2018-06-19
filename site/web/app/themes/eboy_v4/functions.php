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
  'lib/customizer.php' // Theme customizer
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
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

// Register Custom Post Type
function custom_post_type() {

	$labels = array(
		'name'                  => _x( 'Portfolios', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Portfolio', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Portfolio', 'text_domain' ),
		'name_admin_bar'        => __( 'Portfolio', 'text_domain' ),
		'archives'              => __( 'Portfolio Archives', 'text_domain' ),
		'attributes'            => __( 'Portfolio Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Portfolio:', 'text_domain' ),
		'all_items'             => __( 'All Portfolios', 'text_domain' ),
		'add_new_item'          => __( 'Add Portfolio', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Portfolio', 'text_domain' ),
		'edit_item'             => __( 'Edit Portfolio', 'text_domain' ),
		'update_item'           => __( 'Update Portfolio', 'text_domain' ),
		'view_item'             => __( 'View Portfolio', 'text_domain' ),
		'view_items'            => __( 'View Portfolio', 'text_domain' ),
		'search_items'          => __( 'Search Portfolios', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Portfolio', 'text_domain' ),
		'description'           => __( 'Post Type Description', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes', 'post-formats', 'excerpt' ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
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
		'capability_type'       => 'page',
	);
	register_post_type( 'Portfolio', $args );

}
add_action( 'init', 'custom_post_type', 0 );

function lb_editor_remove_meta_box() {
	global $post_type;

	// Check to see if the global $post_type variable exists
	// and then check to see if the current post_type supports
	// excerpts. If so, remove the default excerpt meta box
	// provided by the WordPress core. If you would like to only
	// change the excerpt meta box for certain post types replace
	// $post_type with the post_type identifier.
	if (isset($post_type) && post_type_supports($post_type, 'excerpt')) remove_meta_box('postexcerpt', $post_type, 'normal');
}
add_action('admin_menu', 'lb_editor_remove_meta_box');

function lb_editor_add_custom_meta_box() {
	global $post_type;

	// Again, check to see if the global $post_type variable
	// exists and then if the current post_type supports excerpts.
	// If so, add the new custom excerpt meta box. If you would
	// like to only change the excerpt meta box for certain post
	// types replace $post_type with the post_type identifier.
	if (isset($post_type) && post_type_supports($post_type, 'excerpt')) add_meta_box('postexcerpt', __('Excerpt'), 'lb_editor_custom_post_excerpt_meta_box', $post_type, 'normal', 'high');
}
add_action( 'add_meta_boxes', 'lb_editor_add_custom_meta_box' );

function lb_editor_custom_post_excerpt_meta_box( $post ) {
	// Adjust the settings for the new wp_editor. For all
	// available settings view the wp_editor reference
	// http://codex.wordpress.org/Function_Reference/wp_editor
	$settings = array( 'textarea_rows' => '12', 'quicktags' => false, 'tinymce' => true);

	// Create the new meta box editor and decode the current
	// post_excerpt value so the TinyMCE editor can display
	// the content as it is styled.
	wp_editor(html_entity_decode(stripcslashes($post->post_excerpt)), 'excerpt', $settings);

	// The meta box description - adjust as necessary
	echo '<p><em>Excerpts are optional, hand-crafted, summaries of your content.</em></p>';
}


function eboy_woocommerce_current_tags_links() {
  $output = array();

// get an array of the WP_Term objects for a defined product ID
$terms = wp_get_post_terms( get_the_id(), 'post_tag' );

// Loop through each product tag for the current product
if( count($terms) > 0 ){
    foreach($terms as $term){
        $term_id = $term->term_id; // Product tag Id
        $term_name = $term->name; // Product tag Name
        $term_slug = $term->slug; // Product tag slug
        $term_link = get_term_link( $term, 'post_tag' ); // Product tag link

        // Set the product tag names in an array
        $output[] = '<a href="'.$term_link.'">'.$term_name.'</a>';
    }
    // Set the array in a coma separated string of product tags for example
    $output = implode( ', ', $output );

    // Display the coma separated string of the product tags
    echo $output;
}
}
add_action ('eboy_woocommerce_current_tags', 'eboy_woocommerce_current_tags_links');


function eboy_woocommerce_current_tags_sketo() {
  $output = array();

// get an array of the WP_Term objects for a defined product ID
$terms = wp_get_post_terms( get_the_id(), 'post_tag' );

// Loop through each product tag for the current product
if( count($terms) > 0 ){
    foreach($terms as $term){
        $term_id = $term->term_id; // Product tag Id
        $term_name = $term->name; // Product tag Name
        $term_slug = $term->slug; // Product tag slug
        $term_link = get_term_link( $term, 'post_tag' ); // Product tag link

        // Set the product tag names in an array
        $output[] = $term_slug;
    }
    // Set the array in a coma separated string of product tags for example
    $output = implode( ' ', $output );

    // Display the coma separated string of the product tags
    echo $output;
}
}
add_action ('eboy_woocommerce_current_tags_thumb', 'eboy_woocommerce_current_tags_sketo');

function eboy_portfolio_demo() { ?>
<?php if( get_field('demo') ): ?>
  <a href="<?php the_field('demo'); ?>" class="btn-info btn-sm active" role="button" aria-pressed="true">Demo</a>


<?php endif; ?>
<?php }

add_action ('eboy_portfolio', 'eboy_portfolio_demo');

function the_breadcrumb() {
		echo '<ul id="crumbs">';
	if (!is_home()) {
		echo '<li><a href="';
		echo get_option('home');
		echo '">';
		echo 'Home';
		echo "</a></li>";
		if (is_category() || is_single()) {
			echo '<li>';
			the_category(' </li><li> ');
			if (is_single()) {
				echo "</li><li>";
				the_title();
				echo '</li>';
			}
		} elseif (is_page()) {
			echo '<li>';
			echo the_title();
			echo '</li>';
		}
	}
	elseif (is_tag()) {single_tag_title();}
	elseif (is_day()) {echo"<li>Archive for "; the_time('F jS, Y'); echo'</li>';}
	elseif (is_month()) {echo"<li>Archive for "; the_time('F, Y'); echo'</li>';}
	elseif (is_year()) {echo"<li>Archive for "; the_time('Y'); echo'</li>';}
	elseif (is_author()) {echo"<li>Author Archive"; echo'</li>';}
	elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<li>Blog Archives"; echo'</li>';}
	elseif (is_search()) {echo"<li>Search Results"; echo'</li>';}
	echo '</ul>';
}

// filter the Gravity Forms button type
add_filter("gform_submit_button_1", "form_submit_button", 10, 2);
function form_submit_button($button, $form){
return "<button class='btn btn-primary custom-btn btn-lg btn-block' id='gform_submit_button_{$form["id"]}'><span><i class='fa fa-share fa-2x'></i> Send </span></button>";
}

add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );


add_filter( 'wp_mail_from_name', 'my_mail_from_name' );
function my_mail_from_name( $name ) {
    return "Site name";
}

add_filter( 'wp_mail_from', 'my_mail_from' );
function my_mail_from( $email ) {
    return "your@email.com";
}

// AJAX send contact form
function contacts_form()
{
    $headers  = 'Content-type: text/html; charset=utf-8';

    $name = trim(htmlspecialchars($_POST['name']));
    $mail = trim(htmlspecialchars($_POST['email']));
    $phone = trim(htmlspecialchars($_POST['phone']));
    $comment = trim(htmlspecialchars($_POST['comment']));

    $mailTo = 'youremail@mail.com';
    //$mailTo = get_field('email', 'option');

    $textMessage = "<table>
                        <tr>
                            <td style='padding: 5px 0px;'><b>Name:</b></td>
                            <td style='padding: 5px 0px; padding-left: 20px;'>" . $name . "</td>
                        </tr>";
    if(!empty($mail)) {
        $textMessage .= "<tr>
                            <td style='padding: 5px 0px;'><b>E-mail:</b></td>
                            <td style='padding: 5px 0px; padding-left: 20px;'>" . $mail . "</td>
                        </tr>";
    }
    if(!empty($phone)) {
        $textMessage .= "<tr>
                            <td style='padding: 5px 0px;'><b>Phone:</b></td>
                            <td style='padding: 5px 0px; padding-left: 20px;'>" . $phone . "</td>
                        </tr>";
    }
    if(!empty($comment)) {
        $textMessage .= "<tr>
                            <td style='padding: 5px 0px;'><b>Comment:</b></td>
                            <td style='padding: 5px 0px; padding-left: 20px;'>" . $comment ."</td>
                        </tr>
                    </table>";
    }
    if(!empty($name) || !empty($mail) || !empty($phone)) {
        wp_mail($mailTo, '|Your Site', $textMessage, $headers);
    }
    wp_die();
}

add_action('wp_ajax_contacts_form', 'contacts_form');
add_action('wp_ajax_nopriv_contacts_form', 'contacts_form');
