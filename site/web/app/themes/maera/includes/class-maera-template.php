<?php

class Maera_Template {

	function __construct() {
	}

	/**
	 * Test if all required plugins are installed.
	 * If they are not then then do not proceed with the template loading.
	 * Instead display a custom template file that urges users to visit their dashboard to install them.
	 */
	public function dependencies() {

		if ( 'bad' == Maera()->plugins->test_missing() ) {
			get_template_part( 'lib/required-error' );
			return;
		}

	}

	/**
	 * Get the header.
	 */
	public function header() {
		get_header();
	}

	/**
	 * Old, naming, this is just a fallback to the render() method.
	 */
	public function main( $templates = null, $context = null ) {
		$this->render( $templates, $context );
	}

	/**
	 * Render a template
	 * This will render the necessary twig template
	 */
	public function render( $templates = null, $context = null ) {

		if ( is_null( $templates ) ) {
			$templates = apply_filters( 'maera/templates', array() );
		}

		if ( is_null( $context ) ) {
			$context = $this->context();
		}

		Timber::render(
			$templates,
			$context,
			Maera()->cache->cache_duration(),
			Maera()->cache->cache_mode()
		);

	}

	/**
	 * Get the footer
	 */
	public function footer() {
		get_footer();
	}

	/**
	 * Determine the context that will be used by the content() method
	 */
	public function context() {

		global $wp_query;

		$context = Maera()->cache->get_context();
		$post = new TimberPost();
		$context['post'] = $post;
		$context['posts'] = Timber::get_posts();

		// Compatibility hack or plugins that change the content.
		if ( $this->plugins_compatibility() ) {
			$context['content'] = maera_get_echo( 'the_content' );
		}

		if ( is_singular() ) {
			$context['wp_title'] .= ' - ' . $post->title();
		}

		if ( is_search() ) {
			$context['title'] = esc_html__( 'Search results for ', 'maera' ) . get_search_query();
		}

		if ( is_archive() || is_home() ) {
			$context['posts'] = Timber::query_posts( false, 'TimberPost' );
			$context['title'] = get_the_archive_title();

			if ( is_author() ) {
				$author = new TimberUser( $wp_query->query_vars['author'] );
				$context['author'] = $author;
			}

		}

		if ( class_exists( 'WooCommerce' ) ) {
			global $product;
			$context['product'] = $product;
		}

		return $context;
	}

	/**
	 * Add compatibility for some plugins.
	 */
	public function plugins_compatibility() {

		$compatibility = false;

		// bbPress
		$compatibility = function_exists( 'is_bbpress' ) && is_bbpress() ? true : $compatibility;
		// BuddyPress
		$compatibility = function_exists( 'is_buddypress' ) && is_buddypress() ? true : $compatibility;

		return apply_filters( 'maera/template/plugin_compatibility', $compatibility );

	}

}
