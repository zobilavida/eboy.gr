/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 * ======================================================================== */

(function($) {

  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var Sage = {
    // All pages
    'common': {
      init: function() {
        // JavaScript to be fired on all pages
        /*
        	*************************
        	SVG Convert
        	*************************

        	---
        	Developer(s)
        	---

        	Jason Mayo
        	http://bymayo.co.uk
        	http://twitter.com/madebymayo

        */

        	// ------------------------
        	// Build
        	// ------------------------

        	(function ($) {

        		$.fn.svgConvert = function(options) {

        			//
        			// Settings
        			//

        				var pluginSettings = $.extend(
        						{
        							svgCleanupAttr: ['width','height','id','x','y','xmlns','xmlns:a','xmlns:xlink','xml:space','enable-background','version','style'],
        							imgIncludeAttr: true,
        							imgCleanupAttr: [],
        							removeClass: true,
        							addClass: 'svg-converted',
        							onComplete: function() {}
        						},
        						options
        					);

        			//
        			// Variables
        			//

        				var pluginObj = this.selector,
        					pluginObjName = pluginObj.substring(1),
        					pluginObjLength = $(pluginObj).length;

        				pluginSettings.imgCleanupAttr.push('alt', 'src');

        			//
        			// Build
        			//

        				$(pluginObj).each(
        					function(index) {

        						var imageObj = $(this),
        							imagePath = imageObj.attr('src'),
        							imageAttributes = {};

        						// Image - Get Attributes

        						if (pluginSettings.imgIncludeAttr) {

        							$.each(
        								this.attributes,
        								function() {
        									if(
        										this.specified &&
        										pluginSettings.imgCleanupAttr.indexOf(this.name) !== 0
        									) {
        										if (this.name === 'class' && pluginSettings.removeClass) {
        											this.value = this.value.replace(pluginObjName + ' ', '');
        										}
        										if (this.name === 'class' && pluginSettings.addClass) {
        											this.value = this.value += ' ' + pluginSettings.addClass;
        										}
        										imageAttributes[this.name] = this.value;
        									}
        								}
        							);

        						}

        						$.get(
        							imagePath,
        							function(data) {

        								var svgData = $(data).find('svg'),
        									svgOutput;

        								// SVG - Cleanup Attributes

        								$.each(
        									pluginSettings.svgCleanupAttr,
        									function(i, item) {
        										svgData.removeAttr(item);
        									}
        								);

        								// Image - Include Attributes

        								if (pluginSettings.imgIncludeAttr) {

        									$.each(
        										imageAttributes,
        										function(key, value) {
        											svgData.attr(key, value);
        										}
        									);

        								}

        								// Output

        								imageObj.replaceWith($(data).find('svg'));

        								// Callback - Complete

        								if (index + 1 === pluginObjLength) {
        									pluginSettings.onComplete.call(this);
        								}

        							}
        						);

        					}
        				);

        		};

        	}( jQuery ));

      },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired

        $('.svg-convert').svgConvert();

        $('.btn').hover(
        function(){$(this).toggleClass('test');}
        );

        // click event way
        // adds sliding underline HTML
        jQuery('#menu2').append('<li class="slide-line"></li>');

        // animate slide-line on click
        jQuery(document).on('click', '#menu2 li a', function () {

                var $this = jQuery(this),
                    offset = $this.offset(),
                    //find the offset of the wrapping div
                    offsetBody = jQuery('#box2').offset();

                // GSAP animate to clicked menu item
                TweenMax.to(jQuery('#menu2 .slide-line'), 0.85, {
                  css:{
                    width: $this.outerWidth()+'px',
                    left: (offset.left-offsetBody.left)+'px',
                    top: (offset.top-offsetBody.top+jQuery(this).height())+'px'
                  },
                  ease: Elastic.easeOut.config(2, 1)
                });

                return false;
        });

        jQuery('#menu2 > li a').first().trigger("click");

        // ScrollMagic

        var scale_tween = TweenMax.to('.logo', 0.5, {
          scale:0.75,
          rotation:360,
          transformOrigin:"50% 50%",
          ease: Linear.easeNone
        });

        var background_white_tween = TweenMax.to('.navbar-custom ', 0.25, {backgroundColor:"#fff", opacity: 0.95, ease:Linear.easeNone});

        var filters_move = TweenMax.to('.filter_index ', 0.25, {opacity: 0, ease:Linear.easeNone});

        // init ScrollMagic Controller
        var controller = new ScrollMagic.Controller();
        // Scale Scene
        var scale_scene = new ScrollMagic.Scene({
          triggerElement: '.filters'
        })
        .setTween(scale_tween);

        // background Scene
        var background_scene = new ScrollMagic.Scene({
          triggerElement: '.filters', triggerHook: 0.042
        })
        .setTween(background_white_tween);

        var scene_filters = new ScrollMagic.Scene({triggerElement: ".filters", triggerHook: 0.012})
        .setPin(".filters", {pushFollowers: false})
        //.addIndicators({name: "1 (duration: 2000)"}) // add indicators (requires plugin)
        .setTween(filters_move);

        controller.addScene([
        scale_scene,
        background_scene,
        scene_filters
        ]);

        // Button Hover
        var div1 = $(".btn1"),
            tn1 = TweenMax.to(div1, 0.25, {scale:1.25, repeat:0, yoyo:false, ease:Linear.easeNone, paused:true});

        div1.mouseenter(function()
        {
          tn1.play();
        });

        div1.mouseleave(function()
        {
          var currentTime = tn1.time();
          tn1.reverse(currentTime);
        });

        // isotope
        var $container = $('.grid');
        $container.isotope({
          itemSelector: '.product',
        });

        // layout Isotope again after all images have loaded
$container.imagesLoaded().progress( function() {
   $container.isotope('layout');
});

        // filter items on button click
        $('.filter-button-group').on( 'click', 'li', function() {
          var filterValue = $(this).attr('data-filter');
          $('.grid').isotope({ filter: filterValue });
          $('.filter-button-group li').removeClass('active');
          $(this).addClass('active');
        });

        // carousel
        $('.carousel').carousel();

        // Progress bar 2
        var delay = 500;
$(".progress-bar").each(function(i){
    $(this).delay( delay*i ).animate( { width: $(this).attr('aria-valuenow') + '%' }, delay );

    $(this).prop('Counter',0).animate({
        Counter: $(this).text()
    }, {
        duration: delay,
        easing: 'swing',
        step: function (now) {
            $(this).text(Math.ceil(now)+'%');
        }
    });
});

        // Progress bar
        var $alert = $('.alert');
var $progressBar = $('.progress');

var progress = 0;      // initial value of your progress bar
var timeout = 10;      // number of milliseconds between each frame
var increment = 0.5;    // increment for each frame
var maxprogress = 110; // when to leave stop running the animation

function animate() {
    setTimeout(function () {
        progress += increment;
        if(progress < maxprogress) {
            $progressBar.attr('value', progress);
            animate();
        } else {
          //  $progressBar.css('display', 'inline');

        }
    }, timeout);
}
animate();



      }
    },
    // Home page
    'home': {
      init: function() {
        // JavaScript to be fired on the home page
      },
      finalize: function() {
        // JavaScript to be fired on the home page, after the init JS




(function($) {
    window.fwp_is_paging = false;

    $(document).on('facetwp-refresh', function() {
        if (! window.fwp_is_paging) {
            window.fwp_page = 1;
            FWP.extras.per_page = 'default';
        }

        window.fwp_is_paging = false;
    });

    $(document).on('facetwp-loaded', function() {
        window.fwp_total_rows = FWP.settings.pager.total_rows;

        if (! FWP.loaded) {
            window.fwp_default_per_page = FWP.settings.pager.per_page;

            $(window).scroll(function() {
                if ($(window).scrollTop() === $(document).height() - $(window).height()) {
                    var rows_loaded = (window.fwp_page * window.fwp_default_per_page);
                    if (rows_loaded < window.fwp_total_rows) {
                        //console.log(rows_loaded + ' of ' + window.fwp_total_rows + ' rows');
                        window.fwp_page++;
                        window.fwp_is_paging = true;
                        FWP.extras.per_page = (window.fwp_page * window.fwp_default_per_page);
                        FWP.soft_refresh = true;
                        FWP.refresh();
                    }
                }
            });
        }
    });
})(jQuery);



      }
    },
    // About us page, note the change from about-us to about_us.
    'about_us': {
      init: function() {
        // JavaScript to be fired on the about us page
      }
    }
  };

  // The routing fires all common scripts, followed by the page specific scripts.
  // Add additional events for more control over timing e.g. a finalize event
  var UTIL = {
    fire: function(func, funcname, args) {
      var fire;
      var namespace = Sage;
      funcname = (funcname === undefined) ? 'init' : funcname;
      fire = func !== '';
      fire = fire && namespace[func];
      fire = fire && typeof namespace[func][funcname] === 'function';

      if (fire) {
        namespace[func][funcname](args);
      }
    },
    loadEvents: function() {
      // Fire common init JS
      UTIL.fire('common');

      // Fire page-specific init JS, and then finalize JS
      $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function(i, classnm) {
        UTIL.fire(classnm);
        UTIL.fire(classnm, 'finalize');
      });

      // Fire common finalize JS
      UTIL.fire('common', 'finalize');
    }
  };

  // Load Events
  $(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.
