jQuery(document).ready(function(t){"use strict";t(".project-preview").on("click",function(){var c=t(this).attr("data-project-id"),e=t(".right");t.ajax({type:"POST",url:singleprojectajax.ajaxurl,data:{action:"load_single_product_content",post_id:c},success:function(t){e.html(t)},complete:function(){},error:function(){}})})});