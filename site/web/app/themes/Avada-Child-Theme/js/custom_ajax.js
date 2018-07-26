jQuery(document).ready(function($) {
  jQuery('.col-3').find('.product-preview.mens').trigger('click');
  $(document).on('facetwp-loaded', function() {


"use strict";

$('.product-preview').on('click',function(){
    var theId = $(this).attr('data-project-id');
    var div = $('.right-product');

    $.ajax({
        type: "POST",
        url: singleprojectajax.ajaxurl,
        data : {action : 'load_single_product_content', post_id: theId },
        success: function(data){
            div.html(data);
        },
        complete: function(){
            loadsingleproductScript();
            loadVariationScript();
            loadFlexSliderScript();
            loadRelatedScript();
            loadKeepStampScript();
        },
        error : function() {
        }
    });
});



function loadsingleproductScript() {
   $.getScript("https://eboy.gr/app/plugins/woocommerce/assets/js/frontend/single-product.min.js?ver=3.3.5");
}
function loadVariationScript() {
   $.getScript("https://eboy.gr/app/plugins/woocommerce/assets/js/frontend/add-to-cart-variation.min.js?ver=3.3.5");
}
function loadFlexSliderScript() {
   $.getScript("https://eboy.gr/app/plugins/woocommerce/assets/js/flexslider/jquery.flexslider.min.js?ver=2.6.1");
}
function loadRelatedScript() {
   $.getScript("https://eboy.gr/app/themes/tshirtakias/dist/scripts/related_ajax-192f1b5387.js?ver=1");
}
function loadKeepStampScript() {
   $.getScript("https://eboy.gr/app/themes/tshirtakias/dist/scripts/keepstamp_ajax-7cea83afa4.js?ver=1");
}

});
});
