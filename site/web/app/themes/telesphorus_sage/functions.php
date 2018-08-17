<?php
/**
 * telesphorus includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/telesphorus/pull/1042
 */
$sage_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/wrapper.php',   // Theme wrapper class
  'lib/tgm-plugin-activation.php',   // TGM plugin activation
  'lib/customizer.php',
  'lib/bs4navwalker.php',
  'lib/theme-settings.php'
  //'theme-customizations.php' // Theme menu
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'telesphorus'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);


/* Include Redux
if ( is_admin() ) {
    include 'admin/admin-init.php';
}
*/

// Add svg & swf support
function cc_mime_types( $mimes ){
    $mimes['svg'] = 'image/svg+xml';
    $mimes['swf']  = 'application/x-shockwave-flash';

    return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );


function get_hero_content(){

  if( get_theme_mod('hero_image_url') ){?>

    <video poster="<?php echo esc_url( get_theme_mod( 'hero_image_url' ) ); ?>" class="video-fluid" playsinline autoplay muted loop>
      <source src="<?php echo esc_url( get_theme_mod( 'hero_video_url' ) ); ?>" type="video/mp4">

    </video>
    <div class="hero-content-static container-fluid">
      <div class="row">
        <div class="col-12">

          <?php if ( get_theme_mod( 'hero-static-text-option' ) == 'static') { ?>
            <div class="container">
              <div class="row">
                <div class="col-12">
      <h2 class=""><?php echo esc_html( get_theme_mod( 'hero-static-text' ) ); ?></h2><br>
     <a class="btn btn-primary btn-lg" href="#" role="button">Enquire Now</a>
               </div>
               </div>
               </div>
        <?php  } ?>
        <?php if ( get_theme_mod( 'hero-static-text-option' ) == 'carousel') { ?>
          <div class="container">
            <div class="row">
              <div class="col-12">
    <h2 class="">Carousel</h2><br>
   <a class="btn btn-primary btn-lg" href="#" role="button">Enquire Now</a>
             </div>
             </div>
             </div>
      <?php  } ?>
 </div>
 </div>
 </div>
<?php }else{
  //your code

}
}
add_action ('telesphorus_hero', 'get_hero_content');


/**
* Adds Bootstrap Carousel widget
*/
class Bootstrapcarousel_Widget extends WP_Widget {

	/**
	* Register widget with WordPress
	*/
	function __construct() {
		parent::__construct(
			'bootstrapcarousel_widget', // Base ID
			esc_html__( 'Bootstrap Carousel', 'telesphorus' ), // Name
			array( 'description' => esc_html__( 'Creates Bootstrap carousel', 'telesphorus' ), ) // Args
		);
	}

	/**
	* Widget Fields
	*/
	private $widget_fields = array(
		array(
			'label' => 'Title',
			'id' => 'title_51949',
			'default' => 'Default title',
			'type' => 'textarea',
		),
	);

	/**
	* Front-end display of widget
	*/
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		// Output widget title
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		// Output generated fields
		echo '<p>'.$instance['title_51949'].'</p>';

		echo $args['after_widget'];
	}

	/**
	* Back-end widget fields
	*/
	public function field_generator( $instance ) {
		$output = '';
		foreach ( $this->widget_fields as $widget_field ) {
			$widget_value = ! empty( $instance[$widget_field['id']] ) ? $instance[$widget_field['id']] : esc_html__( $widget_field['default'], 'telesphorus' );
			switch ( $widget_field['type'] ) {
				case 'textarea':
					$output .= '<p>';
					$output .= '<label for="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'">'.esc_attr( $widget_field['label'], 'telesphorus' ).':</label> ';
					$output .= '<textarea class="widefat" id="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'" name="'.esc_attr( $this->get_field_name( $widget_field['id'] ) ).'" rows="6" cols="6" value="'.esc_attr( $widget_value ).'">'.$widget_value.'</textarea>';
					$output .= '</p>';
					break;
				default:
					$output .= '<p>';
					$output .= '<label for="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'">'.esc_attr( $widget_field['label'], 'telesphorus' ).':</label> ';
					$output .= '<input class="widefat" id="'.esc_attr( $this->get_field_id( $widget_field['id'] ) ).'" name="'.esc_attr( $this->get_field_name( $widget_field['id'] ) ).'" type="'.$widget_field['type'].'" value="'.esc_attr( $widget_value ).'">';
					$output .= '</p>';
			}
		}
		echo $output;
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'telesphorus' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'telesphorus' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
		$this->field_generator( $instance );
	}

	/**
	* Sanitize widget form values as they are saved
	*/
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		foreach ( $this->widget_fields as $widget_field ) {
			switch ( $widget_field['type'] ) {
				case 'checkbox':
					$instance[$widget_field['id']] = $_POST[$this->get_field_id( $widget_field['id'] )];
					break;
				default:
					$instance[$widget_field['id']] = ( ! empty( $new_instance[$widget_field['id']] ) ) ? strip_tags( $new_instance[$widget_field['id']] ) : '';
			}
		}
		return $instance;
	}
} // class Bootstrapcarousel_Widget

// register Bootstrap Carousel widget
function register_bootstrapcarousel_widget() {
	register_widget( 'Bootstrapcarousel_Widget' );
}
add_action( 'widgets_init', 'register_bootstrapcarousel_widget' );
