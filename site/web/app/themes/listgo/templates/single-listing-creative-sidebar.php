<?php
/*
 * Template Name: Creative Sidebar
 * Template Post Type: listing
 */
get_header();
global $wiloke, $post;
$aSettings = Wiloke::getPostMetaCaching($post->ID, 'listing_settings');
$aOtherSettings = Wiloke::getPostMetaCaching($post->ID, 'listing_other_settings');
$overlayColor = WilokePublic::getSetting('header_overlay', 'listing_header_overlay', $aOtherSettings);
$headerImg = get_the_post_thumbnail_url($post->ID, 'large');
if ( empty($headerImg) ){
	if ( isset($wiloke->aThemeOptions['listing_header_image']['id']) ){
		$headerImg = wp_get_attachment_image_url($wiloke->aThemeOptions['listing_header_image']['id'], 'large');
		$overlayColor = !empty($wiloke->aThemeOptions['listing_header_overlay']) ? $wiloke->aThemeOptions['listing_header_overlay']['rgba'] : '';
	}
}

while (have_posts()) : the_post(); ?>

	<div class="listing-single-wrap7">

		<!-- Single Hero 7 -->
        <div class="listing-single__hero7 lazy bg-scroll" data-src="<?php echo esc_url($headerImg); ?>"></div>
        <!-- End / Single Hero 7 -->

        <div class="listing-single__wrap-header7">
			<div class="container">
				<div class="row">
					<div class="col-md-4">
						<div class="listing-single__header">
                            <div class="listing-single__title">
                                <h1><?php the_title(); ?></h1>
								<?php do_action('wiloke/listgo/single/after_title', $post); ?>
                            </div>
							<?php WilokePublic::postMeta($post); ?>
							<?php WilokePublic::listingAction($post); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="container">
			<div class="row">
				<div class="col-md-8 col-md-push-4">
					<div class="listing-single">
						<div class="tab tab--2 listing-single__tab">

							<ul class="tab__nav">
								<?php do_action('wiloke/listgo/templates/single-listing/top_nav_tab'); ?>
								<?php WilokePublic::renderListingTab('description'); ?>
								<?php WilokePublic::renderListingTab('contact'); ?>
								<?php WilokePublic::renderListingTab('review'); ?>
								<?php do_action('wiloke/listgo/templates/single-listing/bottom_nav_tab'); ?>
							</ul>

							<div class="tab__content">
								<?php do_action('wiloke/listgo/templates/single-listing/after_open_content'); ?>
								<?php if ( WilokePublic::toggleTabStatus('listing_toggle_tab_desc') === 'enable' ) : ?>
                                    <div class="tab__panel active" id="tab-description">
										<?php do_action('wiloke/listgo/templates/single-listing/after_tab-description_open'); ?>
                                        <div class="listing-single__content">
		                                    <?php do_action('wiloke/listgo/templates/single-listing/after_listing_content_open', $post); ?>
		                                    <?php
		                                    the_content();
		                                    wp_link_pages();
		                                    do_action('wiloke/listgo/templates/single-listing/before_listing_content_close', $post); ?>
                                        </div>
	                                    <?php do_action('wiloke/listgo/templates/single-listing/before_tab-description_close'); ?>
                                    </div>
								<?php endif; ?>

								<?php include get_template_directory(). '/templates/single-listing-elements/contact-tab.php'; ?>

                                <?php if ( WilokePublic::toggleTabStatus('listing_toggle_tab_review_and_rating') === 'enable' ) : ?>
									<div class="tab__panel" id="tab-review">
										<?php
										comments_template();
										?>
									</div>
								<?php endif; ?>

								<?php do_action('wiloke/listgo/templates/single-listing/before_close_content'); ?>

							</div>

						</div>

						<?php if ( Wiloke::$mobile_detect->isMobile() ) : ?>
                            <div class="listing-single__sidebar">
								<?php get_sidebar('listing'); ?>
                            </div>
						<?php endif;?>

						<?php do_action('wiloke/listgo/single-listing/before_related_post', $post); ?>
						<!-- Related Posts -->
						<?php WilokePublic::renderRelatedPosts(); ?>
						<!-- End / Related Posts -->

						<?php
						/*
						 * hooked: renderPaymentEndEditButton
						 */
						do_action('wiloke/listgo/single-listing/after_related_post', $post);
						?>

						<div class="listing-single-bar">
							<div class="container">
                                <ul class="tab__nav">
									<?php do_action('wiloke/listgo/templates/single-listing/after_nav_tab_open', $post); ?>
									<?php WilokePublic::renderListingTab('description'); ?>
									<?php WilokePublic::renderListingTab('contact'); ?>
									<?php WilokePublic::renderListingTab('review'); ?>
									<?php do_action('wiloke/listgo/templates/single-listing/before_nav_tab_close', $post); ?>
                                </ul>
								<?php WilokePublic::listingAction($post); ?>
								<?php do_action('wiloke/listgo/templates/single-listing/render_custom_tab_content', $post); ?>
							</div>
						</div>

					</div>
				</div>

				<?php
				if ( !Wiloke::$mobile_detect->isMobile() ) {
					get_sidebar('listing');
				}else{
					?>
                    <div id="listgo-sidebar-placeholder"></div>
					<?php
				}
				?>
			</div>
		</div>

	</div>
	<?php
endwhile; wp_reset_postdata();
get_footer();