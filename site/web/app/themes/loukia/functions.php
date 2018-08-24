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
  'lib/post-types.php'
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

//enable logo uploading via the customize theme page

function themeslug_theme_customizer( $wp_customize ) {
    $wp_customize->add_section( 'themeslug_logo_section' , array(
    'title'       => __( 'Logo', 'themeslug' ),
    'priority'    => 30,
    'description' => 'Upload a logo to replace the default site name and description     in the header',
) );
$wp_customize->add_setting( 'themeslug_logo' );
$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'themeslug_logo', array(
    'label'    => __( 'Logo', 'themeslug' ),
    'section'  => 'themeslug_logo_section',
    'settings' => 'themeslug_logo',
    'extensions' => array( 'jpg', 'jpeg', 'gif', 'png', 'svg' ),
) ) );
}

add_action ('customize_register', 'themeslug_theme_customizer');


function collections_menu(){

    query_posts(array(
        'post_type' => 'collection',
        'showposts' => -1
    ) );


?>
<div class="col-2">
<nav class="navbar navbar-full navbar-light navbar-left">
  <ul class="nav navbar-nav">
<?php while (have_posts()) : the_post();

?>
  <li class="nav-item pull-sm-right">
    <a class="nav-link" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
  </li>
<?php endwhile;?>
</ul>
</nav>
</div>
<div class="col-10">
  <?php
  $images = get_field('photos');
  $size = 'medium'; // (thumbnail, medium, large, full or custom size)
  global $post;
  while (have_posts()) : the_post();


if( $images ): ?>
<section class="<?php echo $post->post_name; ?>">
   <ul>
     <li class="nav-item pull-sm-right">
       <a class="nav-link" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
     </li>
       <?php foreach( $images as $image ): ?>
           <li>
             <?php echo wp_get_attachment_image( $image['ID'], $size ); ?>
           </li>
       <?php endforeach; ?>
   </ul>
<?php endif; ?>
<?php previous_post_link(); ?> &bull; <?php next_post_link(); ?>
</section>
  		    <?php endwhile; ?>
</div>
<?php }

add_action ('collection', 'collections_menu', 10 );
