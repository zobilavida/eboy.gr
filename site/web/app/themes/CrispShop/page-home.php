<?php
/**
 * The template for displaying home page.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package crispshop
 */

get_header(); ?>

	<div class="inner">
		<div class="banner-1 banner-wrap">
			<?php $banner = get_template_directory_uri() . '/images/banner-head-1.jpg'; ?>
			<a href="<?php echo get_theme_mod('crispshop_banner_link', '/shop/'); ?>"><img src="<?php echo get_theme_mod('crispshop_home_banner', $banner); ?>" /></a>
		</div>

		<div class="product-block">
			<h3><span>Featured Products</span></h3>

			<ul class="products">
				<?php $featured = new WP_Query([
					'post_type'   =>  'product',
					'stock'       =>  1,
					'showposts'   =>  9,
					'orderby'     =>  'date',
					'meta_query'  =>  [ 
						['key' => '_featured', 'value' => 'yes' ]
					]
				]);
				if ( $featured->have_posts() ) :
					while ( $featured->have_posts() ) : $featured->the_post();
						wc_get_template_part( 'content', 'product' );
					endwhile;
				endif;
				wp_reset_query(); ?>
			</ul>
		</div>

		<div class="product-block">
			<h3><span>New Arrivals</span></h3>

			<ul class="products">
				<?php $featured = new WP_Query([
					'post_type'   =>  'product',
					'stock'       =>  1,
					'showposts'   =>  9,
					'orderby'     =>  'date'
				]);
				if ( $featured->have_posts() ) :
					while ( $featured->have_posts() ) : $featured->the_post();
						wc_get_template_part( 'content', 'product' );
					endwhile;
				endif;
				wp_reset_query(); ?>
			</ul>
		</div>
	</div>

<?php get_footer();
