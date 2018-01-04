<?php
/**
 * Single product short description
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/short-description.php.
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
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

if ( ! $post->post_excerpt ) {
	return;
}

?>
<div class="container">
	<div class="row">
<div class="col-12 col-lg-8 product_description">
		<?php the_title( '<h1 class="product_title entry-title my-4">', '</h1>' ); ?>

    <?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?>
</div>

<div class="col-12 col-lg-4 pl-5">
	<div class="row no-gutters">
		<div class="col-12 no-gutters">
		<h1 class="product_title entry-title my-4">Tools</h1>
	</div>
	<div class="col-12 no-gutters">
		<?php
		wp_list_categories(
		array(
		'taxonomy' => 'product_cat',
		'child_of' => 15,
		'style'    => 'list',
		'title_li' => '',
		'walker' => new My_Walker_Category
		)
		);
?>
</div>
</div>
</div>
</div>
</div>
