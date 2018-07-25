jQuery(document).ready(function($) {
$(document).on('facetwp-loaded', function() {
"use strict";

       $('.related-product-preview').on('click',function(){
           var therelatedId = $(this).attr('data-project-id');
           var relateddiv = $('.right-product');

           $.ajax({
               type: "POST",
               url: singleprojectajax.ajaxurl,
               data : {action : 'load_single_product_content', post_id: therelatedId },
               success: function(data){
                   relateddiv.html(data);
               },
               complete: function(){
                   loadKeepStampScript();
                   loadsingleproductScript();
                   loadVariationScript();
                   loadFlexSliderScript();
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
function loadKeepStampScript() {
   $.getScript("https://eboy.gr/app/themes/tshirtakias/dist/scripts/keepstamp_ajax-7cea83afa4.js?ver=1");
}

});
});
