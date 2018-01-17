<?php
/**
 * The template for displaying all pages.
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

get_header();

	while ( have_posts() ) : the_post();

		if (!is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page()) { ?>
		<div id="page-header" class="inner">
			<div class="page-header-wrap">
				<div class="page-header-left">
					<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>
				</div>

				<div class="page-header-right">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
					<span>/</span>
					<?php the_title(); ?>
				</div>
			</div>
		</div>
		<?php } ?>
		
		<div id="primary" class="content-area page-content">
			<div class="inner">

				<?php the_content(); ?>

			</div><!-- #main -->
		</div><!-- #primary -->

	<?php endwhile; ?>

<?php get_footer();
