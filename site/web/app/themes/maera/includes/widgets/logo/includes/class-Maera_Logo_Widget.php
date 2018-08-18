<?php

/**
 * @package     Maera/Widget/Class
 * @version     1.0.0
 * @author      Aristeides Stathopoulos, Brian Welch
 * @copyright   2014
 * @link        https://press.codes
 * @license     http://opensource.org/licenses/MIT
 */

class Maera_Logo_Widget extends WP_Widget {


	/**
	 * Add the widget to the back end.
	 * @todo TODO
	 * @since 1.0.0
	 */
	function __construct() {
		parent::__construct(
			'maera_logo_widget',
			esc_html__( 'Maera Logo', 'maera' ),
			array( 'description' => esc_html__( 'Maera logo widget.', 'maera' ) )
		);
	}


	/**
	 * Render the widget on the back end.
	 * @todo TODO
	 * @since 1.0.0
	 */
	function form( $instance ) {
		?>
		<p class="label"><?php _e( 'This widget has no options to set', 'maera' )?></p>
		<?php
	}


	/**
	 * Render the widget on the front end.
	 * @todo TODO
	 * @since 1.0.0
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		if ( ! empty($instance['title'] ) ) {
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}
		Maera()->views->render( 'site-logo.twig' );

		echo $args['after_widget'];

	}


	/**
	 * Widget instance update.
	 * @todo TODO
	 * @since 1.0.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}

}
