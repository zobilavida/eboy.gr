jQuery(document).ready(function() {
    jQuery("#mobile-slide a").click(function(e) {
        e.preventDefault();
        jQuery('body').addClass('mobile-nav-opened');
    });

    jQuery(".mobile-nav-close").click(function(e) {
        e.preventDefault();
        jQuery('body').removeClass('mobile-nav-opened');
    });

    jQuery(".mobile-overlay").click(function() {
        jQuery('body').removeClass('mobile-nav-opened');
    });

    jQuery('.secondary-categories > a').click(function(e) {
        e.preventDefault();
        var winWidth = jQuery(window).width();

        if (winWidth < 1071) {
            if (jQuery(this).parent().hasClass('opened')) {
                jQuery(this).parent().removeClass('opened');
            } else {
                jQuery(this).parent().addClass('opened');
            }
        }
    });

    jQuery('.all-cats-menu li.has-sub a span').click(function(e) {
        e.preventDefault();
        var winWidth = jQuery(window).width();

        if (winWidth < 1071) {
            if (jQuery(this).parent().parent().hasClass('cat-opened')) {
                jQuery(this).parent().parent().removeClass('cat-opened');
                jQuery(this).parent().parent().find("ul").slideUp();
            } else {
                jQuery(this).parent().parent().addClass('cat-opened');
                jQuery(this).parent().parent().find("ul").slideDown();
            }
        }
    });

    jQuery('.main-navigation .mobile-nav-wrap ul li.menu-item-has-children > a').each(function() {
        jQuery(this).append('<span />');
    });

    jQuery(".categories-menu h5").live( "click", function(e) {
        e.preventDefault();
        if(!jQuery(this).parent().hasClass('opened')) {
            jQuery(this).parent().addClass('opened');
            jQuery(this).parent().find(".categories-dropdown").slideDown();
        } else {
            jQuery(this).parent().removeClass('opened');
            jQuery(this).parent().find(".categories-dropdown").slideUp();
        }
    });

    jQuery(".categories-dropdown ul li a span").live( "click", function(e) {
        e.preventDefault();
        if(!jQuery(this).parent().parent().hasClass('opened')) {
            jQuery(this).parent().parent().addClass('opened');
            jQuery(this).parent().parent().find("ul").first().slideDown();
        } else {
            jQuery(this).parent().parent().removeClass('opened');
            jQuery(this).parent().parent().find("ul").first().slideUp();
        }
    });

    jQuery(window).on('resize', function() {
        var winWidth = jQuery(window).width();
        if (winWidth > 899) {
            if (jQuery('body').hasClass('mobile-nav-opened')) {
                jQuery('body').removeClass('mobile-nav-opened');
            }
        }
    });
});

equalheight = function(container){

var currentTallest = 0,
     currentRowStart = 0,
     rowDivs = new Array(),
     jQueryel,
     topPosition = 0;
 jQuery(container).each(function() {

   jQueryel = jQuery(this);
   jQuery(jQueryel).height('auto')
   topPostion = jQueryel.position().top;

   if (currentRowStart != topPostion) {
     for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
       rowDivs[currentDiv].height(currentTallest);
     }
     rowDivs.length = 0; // empty the array
     currentRowStart = topPostion;
     currentTallest = jQueryel.height();
     rowDivs.push(jQueryel);
   } else {
     rowDivs.push(jQueryel);
     currentTallest = (currentTallest < jQueryel.height()) ? (jQueryel.height()) : (currentTallest);
  }
   for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
     rowDivs[currentDiv].height(currentTallest);
   }
 });
}

jQuery(window).load(function() {
    equalheight('ul.products li.product');
});


jQuery(window).resize(function(){
    equalheight('ul.products li.product');
});