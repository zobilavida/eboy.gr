<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
$thePrice = $product->get_price();

?>

<?php
	/**
	 * woocommerce_before_single_product hook.
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div class="grid_item">
	                <div class="card" data-href="<?php echo get_permalink( $post->ID ); ?>" data-rel="<?php echo $post->ID ; ?>" data-price="<?php echo $thePrice ; ?>" >
					<?php $feat_image_url = wp_get_attachment_url( get_post_thumbnail_id() ); ?>
                   <img class="card-img-top" src="<?php echo $feat_image_url ?>" alt="<?php echo do_action( 'woocommerce_single_product_alt' ); ?> car rental">
                    <div class="card-block">

												<div class="row">
													<div class="col-lg-3">
                        <h3 class="card-price"><?php echo $product->get_price_html(); ?></h3>

												</div>
												<div class="col-lg-9">
											<h3 class="card-title"><?php the_title(); ?></h3>
											</div>
											</div>
                    </div>
										<div class="card-footer">
											<?php echo do_action( 'woocommerce_single_product_meta' ); ?>
                    </div>
                </div>
								<div class="extra">
									<div class="row">
										<div class="col-lg-6">
									  <img class="card-img-top" src="<?php echo $feat_image_url ?>" alt="<?php echo do_action( 'woocommerce_single_product_alt' ); ?> car rental">
									</div>
									<div class="col-lg-6 content_body">
								<?php echo do_action( 'woocommerce_single_product_summary' ); ?>
								</div>
								</div>
								</div>
</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>
