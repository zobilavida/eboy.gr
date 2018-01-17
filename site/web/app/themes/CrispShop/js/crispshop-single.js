jQuery(document).ready(function() {
	jQuery('.product-images').bxSlider({
		pagerCustom: '.product-thumbnails'
	});

	jQuery('.product-thumbnails').bxSlider({
		minSlides: 4,
  		maxSlides: 4,
  		slideWidth: 150,
  		slideMargin: 2,
  		pager: false,
  		moveSlides: 1,
  		infiniteLoop: false
	});

	jQuery('.single_add_to_cart_button').click(function(e) {
		e.preventDefault();
		jQuery(this).addClass('adding-cart');
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
					jQuery('.single_add_to_cart_button').removeClass('adding-cart');
					jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
					jQuery('.cart-dropdown').addClass('show-dropdown');
	                setTimeout(function () { 
	                    jQuery('.cart-dropdown').removeClass('show-dropdown');
	                }, 3000);
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
					jQuery('.single_add_to_cart_button').removeClass('adding-cart');
					jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
					jQuery('.cart-dropdown').addClass('show-dropdown');
	                setTimeout(function () { 
	                    jQuery('.cart-dropdown').removeClass('show-dropdown');
	                }, 3000);
				}
			});
		}
	});
});