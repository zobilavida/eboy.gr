jQuery(document).ready(function(t){"use strict";function e(){t.getScript("https://eboy.gr/app/plugins/woocommerce/assets/js/frontend/single-product.min.js?ver=3.3.5")}function o(){t.getScript("https://eboy.gr/app/plugins/woocommerce/assets/js/frontend/add-to-cart-variation.min.js?ver=3.3.5")}function r(){t.getScript("https://eboy.gr/app/plugins/woocommerce/assets/js/flexslider/jquery.flexslider.min.js?ver=2.6.1")}t(".related-product-preview").on("click",function(){var n=t(this).attr("data-project-id"),c=t(".right-product");t.ajax({type:"POST",url:singleprojectajax.ajaxurl,data:{action:"load_single_product_content",post_id:n},success:function(t){c.html(t)},complete:function(){e(),o(),r()},error:function(){}})})});