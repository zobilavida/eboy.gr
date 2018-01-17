jQuery(document).ready(function() {
  var sliders = new Array();

  var winWidth = jQuery(window).width();
	if (winWidth < 850 && winWidth > 662) {
    jQuery('.products').each(function(i, slider) {
      sliders[i] = jQuery(slider).bxSlider({
        minSlides: 3,
        maxSlides: 3,
        slideWidth: 300,
        slideMargin: 20,
        pager: false,
        moveSlides: 1
      });
    });
  } else if (winWidth < 663 && winWidth > 345) {
    jQuery('.products').each(function(i, slider) {
      sliders[i] = jQuery(slider).bxSlider({
        minSlides: 2,
        maxSlides: 2,
        slideWidth: 300,
        slideMargin: 20,
        pager: false,
        moveSlides: 1
      });
    });
  } else if (winWidth < 346) {
    jQuery('.products').each(function(i, slider) {
      sliders[i] = jQuery(slider).bxSlider({
        pager: false
      });
    });
  } else {
    jQuery('.products').each(function(i, slider) {
      sliders[i] = jQuery(slider).bxSlider({
        minSlides: 4,
        maxSlides: 4,
        slideWidth: 300,
        slideMargin: 20,
        pager: false,
        moveSlides: 1
      });
    });
  }

  jQuery(window).on('resize', function() {
    var winWidth = jQuery(window).width();
    if (winWidth < 850 && winWidth > 662) {
      jQuery.each(sliders, function(i, slider) {
          slider.reloadSlider({
          minSlides: 3,
          maxSlides: 3,
          slideWidth: 300,
          slideMargin: 20,
          pager: false,
          moveSlides: 1
        });
      });
    } else if (winWidth < 663 && winWidth > 345) {
      jQuery.each(sliders, function(i, slider) {
        slider.reloadSlider({
          minSlides: 2,
          maxSlides: 2,
          slideWidth: 300,
          slideMargin: 20,
          pager: false,
          moveSlides: 1
        });
      });
    } else if (winWidth < 346) {
      jQuery.each(sliders, function(i, slider) {
        slider.reloadSlider({
          pager: false
        });
      });
    } else {
      jQuery.each(sliders, function(i, slider) {
        slider.reloadSlider({
          minSlides: 4,
          maxSlides: 4,
          slideWidth: 300,
          slideMargin: 20,
          pager: false,
          moveSlides: 1
        });
      });
    }
  });

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