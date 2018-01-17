jQuery(document).ready(function() {
	jQuery('.single_add_to_cart_button').click(function(e) {
		e.preventDefault();
		var product_id = jQuery(this).val();
		var variation_id = jQuery('input[name="variation_id"]').val();
		var quantity = jQuery('input[name="quantity"]').val();
		console.log(quantity);
		jQuery('.cart-dropdown-inner').empty();

		if (variation_id != '') {
			jQuery.ajax ({
				url: crispshop_ajax_object.ajax_url,
				type:'POST',
				data:'action=crispshop_add_cart_single&product_id=' + product_id + '&variation_id=' + variation_id + '&quantity=' + quantity,

				success:function(results) {
					jQuery('.cart-dropdown-inner').append(results);
					var cartcount = jQuery('.item-count').html();
					jQuery('.cart-totals span').html(cartcount);
				}
			});
		} else {
			jQuery.ajax ({
				url: crispshop_ajax_object.ajax_url,  
				type:'POST',
				data:'action=crispshop_add_cart_single&product_id=' + product_id + '&quantity=' + quantity,

				success:function(data) {
					console.log(results);
					jQuery('.cart-dropdown-inner').append(results);
					var cartcount = jQuery('.item-count').html();
					jQuery('.cart-totals span').html(cartcount);
				}
			});
		}
	});
});