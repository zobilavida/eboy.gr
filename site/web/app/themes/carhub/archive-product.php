<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 *
 * Updated by Elvtn, LLC to include FacetWP markup.
 * https://elvtn.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

	<?php
		/**
		 * woocommerce_before_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 * @hooked WC_Structured_Data::generate_website_data() - 30
		 */
	//	do_action( 'woocommerce_before_main_content' );
	?>

    <header class="woocommerce-products-header pb-3 mb-3">


	<?php  do_action( 'demetrios_woo_cat_thumb', 'woo_cat_thumb' ); ?>

    </header>



    <div class="container">
			<div class="row">
				<div class="col-12 text-center">
<?php do_action( 'woocommerce_archive_description' ); ?>
		</div>
			</div>
      <div class="row product-cat-if-desc">
				<div class="col-lg-1 col-4 p-0">

						<div class="cc-selector px-2 py-3">
							<div class="container p-0">
								<div class="row h-100">
									<div class="col-6">
				         <input id="view_2" type="radio" name="credit-card" value="view_2" />
				         <label class="drinkcard-cc view_2" for="view_2"></label>
							 </div>

							 <div class="col-6">
				         <input id="view_4" type="radio" name="credit-card" value="view_4" />
				         <label class="drinkcard-cc view_4"for="view_4"></label>
								 </div>
								 </div>
							 	</div>

				     </div>

					</div>
					<div class="col-8 d-lg-none text-right pt-3">
						<a class="" data-toggle="collapse" href="#collapsible" role="button" aria-expanded="false" aria-controls="collapsible">
				<img class="ico" src="<?= get_template_directory_uri(); ?>/dist/images/ico_settings.svg">
						</a>
					</div>

				<div class="col-lg-10 col-12 px-3 filters" id="collapsible">

					<div class="row">

							<div class="col-12  ">
								<div class="row">

															<div class="col-lg-3 col-12 px-3 filter-dropdown">
													<?php echo facetwp_display( 'facet', 'fabric' ); ?>
																</div>
																<div class="col-lg-3 col-12 px-3 filter-dropdown">
													<?php echo facetwp_display( 'facet', 'neckline' ); ?>
																	</div>
																	<div class="col-lg-3 col-12 px-3 filter-dropdown">
									        <?php echo facetwp_display( 'facet', 'silhouette' ); ?>
																		</div>
																		<div class="col-lg-3 col-12 px-3 filter-dropdown">
										      <?php echo facetwp_display( 'facet', 'style' ); ?>
																			</div>
									</div>
								</div>

						</div>
					</div>



				<div class="col-lg-1 d-none d-lg-block align-self-center text-center">
					<img class="ico" onclick="FWP.reset()" src="<?= get_template_directory_uri(); ?>/dist/images/reset.svg">

				</div>
      </div>
    </div>

		<?php if ( have_posts() ) : ?>

			<?php
				/**
				 * woocommerce_before_shop_loop hook.
				 *
				 * @hooked wc_print_notices - 10
				 * @hooked woocommerce_result_count - 20
				 * @hooked woocommerce_catalog_ordering - 30
				 */
				do_action( 'woocommerce_before_shop_loop' );
			?>

			<?php woocommerce_product_loop_start(); ?>

				<?php woocommerce_product_subcategories(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php
						/**
						 * woocommerce_shop_loop hook.
						 *
						 * @hooked WC_Structured_Data::generate_product_data() - 10
						 */
						do_action( 'woocommerce_shop_loop' );
					?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

			<?php
				/**
				 * woocommerce_after_shop_loop hook.
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );
			?>

		<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

			<?php
				/**
				 * woocommerce_no_products_found hook.
				 *
				 * @hooked wc_no_products_found - 10
				 */
				do_action( 'woocommerce_no_products_found' );
			?>

		<?php endif; ?>
    <!-- Added by Elvtn -->
    </div>

	<?php
		/**
		 * woocommerce_after_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>

	<?php
		/**
		 * woocommerce_sidebar hook.
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
	//	do_action( 'woocommerce_sidebar' );
	?>

<?php get_footer( 'shop' ); ?>
