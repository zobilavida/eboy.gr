jQuery(document).ready(function() {
  jQuery('.add_to_cart_button').click(function(e) {
		e.preventDefault();
		var prodID = jQuery(this).attr('data-product_id');
        jQuery(this).addClass('adding-cart');
		jQuery('.cart-dropdown-inner').empty();

		jQuery.ajax ({
            url: crispshop_ajax_object.ajax_url,
            type:'POST',
            data:'action=crispshop_add_cart&prodID=' + prodID,

            success:function(results) {
                jQuery('.cart-dropdown-inner').html(results);
                var cartcount = jQuery('.item-count').html();
                jQuery('.cart-totals span').html(cartcount);
                jQuery('.products .add_to_cart_button').removeClass('adding-cart');
                jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
                jQuery('.cart-dropdown').addClass('show-dropdown');
                setTimeout(function () { 
                    jQuery('.cart-dropdown').removeClass('show-dropdown');
                }, 3000);
            }
       });
	});
});