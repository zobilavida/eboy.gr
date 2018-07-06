<?php
/**
 * Template Name: Front Page Template
 */
get_header(); ?>

			<?php
			while ( have_posts() ) :
				the_post();
?>
        <?php get_template_part( 'template-parts/unit', 'skyline' ); ?>
				<?php get_template_part( 'template-parts/unit', 'hero' ); ?>
        <?php get_template_part( 'template-parts/unit', 'main' ); ?>

			<?php endwhile; // end of the loop. ?>


<?php get_footer(); ?>
