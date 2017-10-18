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
  'lib/customizer.php',
  'lib/contactformhandler.php' // Contact Form
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

// Register Custom Navigation Walker (Soil)
require_once('wp_bootstrap_navwalker.php');

//declare your new menu
register_nav_menus( array(
    'primary' => __( 'Primary Menu', 'sage' ),
) );

// Add svg & swf support
function cc_mime_types( $mimes ){
    $mimes['svg'] = 'image/svg+xml';
    $mimes['swf']  = 'application/x-shockwave-flash';

    return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );



//enable logo uploading via the customize theme page

function themeslug_theme_customizer( $wp_customize ) {
    $wp_customize->add_section( 'themeslug_logo_section' , array(
    'title'       => __( 'Logo', 'themeslug' ),
    'priority'    => 30,
    'description' => 'Upload a logo to replace the default site name and description     in the header',
) );
$wp_customize->add_setting( 'themeslug_logo' );
$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize,     'themeslug_logo', array(
    'label'    => __( 'Logo', 'themeslug' ),
    'section'  => 'themeslug_logo_section',
    'settings' => 'themeslug_logo',
    'extensions' => array( 'jpg', 'jpeg', 'gif', 'png', 'svg' ),
) ) );
}
add_action('customize_register', 'themeslug_theme_customizer');


/* Portfolio */
function cpt_ntp_portfolio() {
$labels = array(
 'name' => _x( 'Portfolio', 'Post Type General Name', 'ntp_framework' ),
 'singular_name' => _x( 'Portfolio', 'Post Type Singular Name', 'ntp_framework' ),
 'menu_name' => __( 'Portfolio', 'ntp_framework' ),
 'parent_item_colon' => __( 'Portfolio parent :', 'ntp_framework' ),
 'all_items' => __( 'Tous les portfolios', 'ntp_framework' ),
 'view_item' => __( 'Voir le portfolio', 'ntp_framework' ),
 'add_new_item' => __( 'Ajouter un portfolio', 'ntp_framework' ),
 'add_new' => __( 'Nouveau portfolio', 'ntp_framework' ),
 'edit_item' => __( 'Editer un portfolio', 'ntp_framework' ),
 'update_item' => __( 'Mettre à jour le portfolio', 'ntp_framework' ),
 'search_items' => __( 'Rechercher des produits', 'ntp_framework' ),
 'not_found' => __( 'Aucun portfolio trouvé', 'ntp_framework' ),
 'not_found_in_trash' => __( 'Aucun portfolio trouvé dans la corbeille', 'ntp_framework' ),
 );
 $rewrite = array(
 'slug' => 'portfolio',
 'with_front' => true,
 'pages' => false,
 'feeds' => false,
 );
 $args = array(
 'label' => __( 'portfolio', 'ntp_framework' ),
 'description' => __( 'Les créations de votre entreprise', 'ntp_framework' ),
 'labels' => $labels,
 'supports' => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields','excerpt' ),
 'taxonomies' => array( 'type' ),
 'hierarchical' => false,
 'public' => true,
 'show_ui' => true,
 'show_in_menu' => true,
 'show_in_nav_menus' => true,
 'show_in_admin_bar' => true,
 'menu_position' => 20,
 'menu_icon' => '',
 'can_export' => true,
 'has_archive' => true,
 'exclude_from_search' => true,
 'publicly_queryable' => true,
 'query_var' => 'portfolio',
 'rewrite' => $rewrite,
 'capability_type' => 'page',
 );
 register_post_type( 'portfolio', $args );
}
add_action( 'init', 'cpt_ntp_portfolio', 0 );


/* Type */
function ct_ntp_type() {
$labels = array(
 'name' => _x( 'Types', 'Taxonomy General Name', 'ntp_framework' ),
 'singular_name' => _x( 'Type', 'Taxonomy Singular Name', 'ntp_framework' ),
 'menu_name' => __( 'Types', 'ntp_framework' ),
 'all_items' => __( 'Tous les types', 'ntp_framework' ),
 'parent_item' => __( 'Type parent', 'ntp_framework' ),
 'parent_item_colon' => __( 'Type parent :', 'ntp_framework' ),
 'new_item_name' => __( 'Nouveau type', 'ntp_framework' ),
 'add_new_item' => __( 'Ajouter un type', 'ntp_framework' ),
 'edit_item' => __( 'Editer un type', 'ntp_framework' ),
 'update_item' => __( 'Mettre à jour', 'ntp_framework' ),
 'separate_items_with_commas' => __( 'Séparer les types par des virgules', 'ntp_framework' ),
 'search_items' => __( 'Rechercher des types', 'ntp_framework' ),
 'add_or_remove_items' => __( 'Ajouter ou supprimer des types', 'ntp_framework' ),
 'choose_from_most_used' => __( 'Choisir parmi les types les plus utilisés', 'ntp_framework' ),
 );
 $rewrite = array(
 'slug' => 'type',
 'with_front' => true,
 'hierarchical' => true,
 );
 $args = array(
 'labels' => $labels,
 'hierarchical' => true,
 'public' => true,
 'show_ui' => true,
 'show_admin_column' => true,
 'show_in_nav_menus' => true,
 'show_tagcloud' => true,
 'query_var' => 'type',
 'rewrite' => $rewrite,
 'supports' => array('title','thumbnail','editor','page-attributes','excerpt'),
 );
 register_taxonomy( 'type', 'portfolio', $args );
}
add_action( 'init', 'ct_ntp_type', 0 );

add_action( 'custom_metadata_manager_init_metadata', function() {
    x_add_metadata_field( 'my-field-name', 'post' );
} );

function echo_first_image( $postID ) {
	$args = array(
		'numberposts' => 1,
		'order' => 'ASC',
		'post_mime_type' => 'image',
		'post_parent' => $postID,
		'post_status' => null,
		'post_type' => 'attachment',
	);

	$attachments = get_children( $args );

	if ( $attachments ) {
		foreach ( $attachments as $attachment ) {
			$image_attributes = wp_get_attachment_image_src( $attachment->ID, 'thumbnail' )  ? wp_get_attachment_image_src( $attachment->ID, 'thumbnail' ) : wp_get_attachment_image_src( $attachment->ID, 'full' );

			echo '<img src="' . wp_get_attachment_thumb_url( $attachment->ID ) . '" class="current">';
		}
	}
}


add_action( 'publish_post', 'itsg_create_sitemap' );
add_action( 'publish_page', 'itsg_create_sitemap' );

function itsg_create_sitemap() {

    $postsForSitemap = get_posts(array(
        'numberposts' => -1,
        'orderby' => 'modified',
        'post_type'  => array( 'post', 'portfolio' ),
        'order'    => 'DESC'
    ));

    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    foreach( $postsForSitemap as $post ) {
        setup_postdata( $post );

        $postdate = explode( " ", $post->post_modified );

        $sitemap .= '<url>'.
          '<loc>' . get_permalink( $post->ID ) . '</loc>' .
          '<lastmod>' . $postdate[0] . '</lastmod>' .
          '<changefreq>monthly</changefreq>' .
         '</url>';
      }

    $sitemap .= '</urlset>';

    $fp = fopen( ABSPATH . 'sitemap.xml', 'w' );

    fwrite( $fp, $sitemap );
    fclose( $fp );
}

function get_cats() {
  $terms = get_terms( 'type' );
  if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
   echo '<div id="filters">';
   foreach ( $terms as $term ) {
     echo '<input type="checkbox" name="' . $term->name . '" value=".type-' . $term->slug . '" id="' . $term->name . '"><label for="' . $term->name . '">' . $term->name . '</label>';
  //   echo '<li>' . $term->name . '</li>';

   }
   echo '</div>';
  }};

add_action ('custom_actions', 'get_cats', 0 );


function get_post_cats() {

};
add_action ('custom_actions', 'get_post_cats');

/**
 * Enables the Excerpt meta box in Page edit screen.
 */
function wpcodex_add_excerpt_support_for_pages() {
	add_post_type_support( 'portfolio', 'post-formats' );
}
add_action( 'init', 'wpcodex_add_excerpt_support_for_pages' );

/** Custom contact **/

function wptuts_get_the_ip() {
    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        return $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
        return $_SERVER["HTTP_CLIENT_IP"];
    }
    else {
        return $_SERVER["REMOTE_ADDR"];
    }
}

function wptuts_contact_form_sc( $atts ) {

  extract( shortcode_atts( array(
      // if you don't provide an e-mail address, the shortcode will pick the e-mail address of the admin:
      "email" => get_bloginfo( 'admin_email' ),
      "subject" => "",
      "label_name" => "Your Name",
      "label_email" => "Your E-mail Address",
      "label_subject" => "Subject",
      "label_message" => "Your Message",
      "label_submit" => "Submit",
      // the error message when at least one of the required fields are empty:
      "error_empty" => "Please fill in all the required fields.",
      // the error message when the e-mail address is not valid:
      "error_noemail" => "Please enter a valid e-mail address.",
      // and the success message when the e-mail is sent:
      "success" => "Thanks for your e-mail! We'll get back to you as soon as we can."
  ), $atts ) );

  // if the <form> element is POSTed, run the following code
if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    $error = false;
    // set the "required fields" to check
    $required_fields = array( "your_name", "email", "message", "subject" );

    // this part fetches everything that has been POSTed, sanitizes them and lets us use them as $form_data['subject']
    foreach ( $_POST as $field => $value ) {
        if ( get_magic_quotes_gpc() ) {
            $value = stripslashes( $value );
        }
        $form_data[$field] = strip_tags( $value );
    }

    // if the required fields are empty, switch $error to TRUE and set the result text to the shortcode attribute named 'error_empty'
    foreach ( $required_fields as $required_field ) {
        $value = trim( $form_data[$required_field] );
        if ( empty( $value ) ) {
            $error = true;
            $result = $error_empty;
        }
    }

    // and if the e-mail is not valid, switch $error to TRUE and set the result text to the shortcode attribute named 'error_noemail'
    if ( ! is_email( $form_data['email'] ) ) {
        $error = true;
        $result = $error_noemail;
    }

    if ( $error == false ) {
        $email_subject = "[" . get_bloginfo( 'name' ) . "] " . $form_data['subject'];
        $email_message = $form_data['message'] . "\n\nIP: " . wptuts_get_the_ip();
        $headers  = "From: " . $form_data['name'] . " <" . $form_data['email'] . ">\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\n";
        $headers .= "Content-Transfer-Encoding: 8bit\n";
        wp_mail( $email, $email_subject, $email_message, $headers );
        $result = $success;
        $sent = true;
    }
    // but if $error is still FALSE, put together the POSTed variables and send the e-mail!
    if ( $error == false ) {
        // get the website's name and puts it in front of the subject
        $email_subject = "[" . get_bloginfo( 'name' ) . "] " . $form_data['subject'];
        // get the message from the form and add the IP address of the user below it
        $email_message = $form_data['message'] . "\n\nIP: " . wptuts_get_the_ip();
        // set the e-mail headers with the user's name, e-mail address and character encoding
        $headers  = "From: " . $form_data['your_name'] . " <" . $form_data['email'] . ">\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\n";
        $headers .= "Content-Transfer-Encoding: 8bit\n";
        // send the e-mail with the shortcode attribute named 'email' and the POSTed data
        wp_mail( $email, $email_subject, $email_message, $headers );
        // and set the result text to the shortcode attribute named 'success'
        $result = $success;
        // ...and switch the $sent variable to TRUE
        $sent = true;
    }
}

// if there's no $result text (meaning there's no error or success, meaning the user just opened the page and did nothing) there's no need to show the $info variable
if ( $result != "" ) {
    $info = '<div class="info">' . $result . '</div>';
}
// anyways, let's build the form! (remember that we're using shortcode attributes as variables with their names)
$email_form = '<form class="contact-form" method="post" action="' . get_permalink() . '">
    <div>
        <label for="cf_name">' . $label_name . ':</label>
        <input type="text" name="your_name" id="cf_name" size="50" maxlength="50" value="' . $form_data['your_name'] . '" />
    </div>
    <div>
        <label for="cf_email">' . $label_email . ':</label>
        <input type="text" name="email" id="cf_email" size="50" maxlength="50" value="' . $form_data['email'] . '" />
    </div>
    <div>
        <label for="cf_subject">' . $label_subject . ':</label>
        <input type="text" name="subject" id="cf_subject" size="50" maxlength="50" value="' . $subject . $form_data['subject'] . '" />
    </div>
    <div>
        <label for="cf_message">' . $label_message . ':</label>
        <textarea name="message" id="cf_message" cols="50" rows="15">' . $form_data['message'] . '</textarea>
    </div>
    <div>
        <input type="submit" value="' . $label_submit . '" name="send" id="cf_send" />
    </div>
</form>';

if ( $sent == true ) {
    return $info;
} else {
    return $info . $email_form;
}

}
add_shortcode( 'contact', 'wptuts_contact_form_sc' );
