<?php
function crispshop_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widget 2', 'crispshop' ),
		'id'            => 'footer-widget-2',
		'description'   => esc_html__( 'Add widgets here.', 'crispshop' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widget 3', 'crispshop' ),
		'id'            => 'footer-widget-3',
		'description'   => esc_html__( 'Add widgets here.', 'crispshop' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}

add_action( 'widgets_init', 'crispshop_widgets_init' );
?>