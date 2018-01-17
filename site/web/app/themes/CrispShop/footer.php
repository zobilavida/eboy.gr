<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package crispshop
 */

?>

	</div><!-- #content -->

	<div id="subscribe">
		<div class="inner">
			<div class="subscribe-left">
				<h3>Sign up for news &amp; special offers</h3>
				<p>New arrivals and exclusive offers delivered straight to your inbox.</p>
			</div>

			<div class="subscribe-right">
				<form action="#">
					<?php $widgetNL = new WYSIJA_NL_Widget(true);
					echo $widgetNL->widget(array('form' => 1, 'form_type' => 'php')); ?>
				</form>
			</div>

			<div class="clear"></div>
		</div>
	</div>

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="inner">
			<div class="footer-top">
				<div class="footer-widget-1 footer-widget">
					<img src="<?php echo get_theme_mod('crispshop_footer_logo', ''); ?>" alt="" />
					<?php echo get_theme_mod('crispshop_footer_intro', ''); ?>
				</div>

				<div class="footer-widget-2 footer-widget">
					<?php dynamic_sidebar('footer-widget-2'); ?>
				</div>

				<div class="footer-widget-3 footer-widget">
					<?php dynamic_sidebar('footer-widget-3'); ?>
				</div>

				<div class="footer-widget-4 footer-widget">
					<div class="footer-address">
						<p><?php echo get_theme_mod('crispshop_contact_address', ''); ?></p>
					</div>

					<div class="footer-phone">
						<p><?php echo get_theme_mod('crispshop_phone_number', ''); ?></p>
					</div>

					<div class="footer-email">
						<p><a href="mailto:<?php echo get_theme_mod('crispshop_contact_email', ''); ?>"><?php echo get_theme_mod('crispshop_contact_email', ''); ?></a></p>
					</div>
				</div>

				<div class="clear"></div>
			</div>

			<div class="footer-bottom">
				<div class="footer-left">
					<p>&copy; Copyright <?php echo date('Y'); ?>. All Rights Reserved.</p>
				</div>

				<div class="footer-right">
					<img src="<?php echo get_template_directory_uri(); ?>/images/cards.png" />
				</div>

				<div class="clear"></div>
			</div>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
