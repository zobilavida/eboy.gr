<?php
/**
 * The template for displaying the author pages.
 *
 * Learn more: https://codex.wordpress.org/Author_Templates
 *
 * @package telesphorus
 */

get_header();
$container   = get_theme_mod( 'telesphorus_container_type' );
?>


<div class="wrapper" id="author-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<!-- Do the left sidebar check -->
			<?php get_template_part( 'global-templates/left-sidebar-check' ); ?>

			<main class="site-main" id="main">

				<header class="page-header author-header">

					<?php
					$curauth = ( isset( $_GET['author_name'] ) ) ? get_user_by( 'slug',
						$author_name ) : get_userdata( intval( $author ) );
					?>

					<h1><?php esc_html_e( 'About:', 'telesphorus' ); ?><?php echo esc_html( $curauth->nickname ); ?></h1>

					<?php if ( ! empty( $curauth->ID ) ) : ?>
						<?php echo get_avatar( $curauth->ID ); ?>
					<?php endif; ?>

					<dl>
						<?php if ( ! empty( $curauth->user_url ) ) : ?>
							<dt><?php esc_html_e( 'Website', 'telesphorus' ); ?></dt>
							<dd>
								<a href="<?php echo esc_url( $curauth->user_url ); ?>"><?php echo esc_html( $curauth->user_url ); ?></a>
							</dd>
						<?php endif; ?>

						<?php if ( ! empty( $curauth->user_description ) ) : ?>
							<dt><?php esc_html_e( 'Profile', 'telesphorus' ); ?></dt>
							<dd><?php echo esc_html( $curauth->user_description ); ?></dd>
						<?php endif; ?>
					</dl>

					<h2><?php esc_html_e( 'Posts by', 'telesphorus' ); ?> <?php echo esc_html( $curauth->nickname ); ?>
						:</h2>

				</header><!-- .page-header -->

				<ul>

					<!-- The Loop -->
					<?php if ( have_posts() ) : ?>
						<?php while ( have_posts() ) : the_post(); ?>
							<li>
								<a rel="bookmark" href="<?php the_permalink() ?>"
								   title="<?php esc_html_e( 'Permanent Link:', 'telesphorus' ); ?> <?php the_title(); ?>">
									<?php the_title(); ?></a>,
								<?php telesphorus_posted_on(); ?> <?php esc_html_e( 'in',
								'telesphorus' ); ?> <?php the_category( '&' ); ?>
							</li>
						<?php endwhile; ?>

					<?php else : ?>

						<?php get_template_part( 'loop-templates/content', 'none' ); ?>

					<?php endif; ?>

					<!-- End Loop -->

				</ul>

			</main><!-- #main -->

			<!-- The pagination component -->
			<?php telesphorus_pagination(); ?>

		</div><!-- #primary -->

		<!-- Do the right sidebar check -->
		<?php get_template_part( 'global-templates/right-sidebar-check' ); ?>

	</div> <!-- .row -->

</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
