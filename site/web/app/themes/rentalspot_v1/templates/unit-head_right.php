<?php global $woocommerce; ?>
 <a class="your-class-name" href="<?php echo $woocommerce->cart->get_cart_url(); ?>"
title="<?php _e('Cart View', 'woothemes'); ?>">
<?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'woothemes'),
 $woocommerce->cart->cart_contents_count);?>  -
<?php echo $woocommerce->cart->get_cart_total(); ?>
</a>
