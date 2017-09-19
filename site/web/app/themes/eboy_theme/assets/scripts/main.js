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
          var scrollTop = 0;
$(window).scroll(function(){
  scrollTop = $(window).scrollTop();
   $('.counter').html(scrollTop);

  if (scrollTop >= 100) {
    $('.navbar').addClass('scrolled-nav');
  } else if (scrollTop < 100) {
    $('.navbar').removeClass('scrolled-nav');
  }

});







      },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired
      }
    },
    // Home page
    'home': {
      init: function() {
        // JavaScript to be fired on the home page
        // Init controller
        var controller = new ScrollMagic.Controller();

        // Change behavior of controller
        // to animate scroll instead of jump
        controller.scrollTo(function(target) {

          TweenMax.to(window, 1, {
            scrollTo : {
              y : target, // scroll position of the target along y axis
              autoKill : true // allows user to kill scroll action smoothly
            },
            ease : Cubic.easeInOut
          });

        });

        //  Bind scroll to anchor links
        $(document).on("click", "a[href^=#]", function(e) {
          var id = $(this).attr("href");

          if($(id).length > 0) {
            e.preventDefault();

            // trigger scroll
            controller.scrollTo(id);

            // If supported by the browser we can also update the URL
            if (window.history && window.history.pushState) {
              history.pushState("", document.title, id);
            }
          }

        });

         new ScrollMagic.Scene({
             triggerElement: "#home",
             duration: $('#home').height()
           })
           .setClassToggle(".home", "active")
           .addTo(controller);

         new ScrollMagic.Scene({
             triggerElement: "#portfolio",
             duration: $('#portfolio').height()
           })
           .setClassToggle(".portfolio", "active")
           .addTo(controller);

         new ScrollMagic.Scene({
             triggerElement: "#about",
             duration: $('#about').height()
           })
           .setClassToggle(".about", "active")
           .addTo(controller);


      },
      finalize: function() {
        // JavaScript to be fired on the home page, after the init JS



        //Isotope
        var $container = $('.grid'),
        $items = $('.grid_item');
         $checkboxes = $('#filters input');

        $container.isotope({
        itemSelector: '.grid_item',
        masonry: {
        columnWidth: '.grid-sizer',
        gutter: '.gutter-sizer'
        },
        getSortData : {
          selected : function( $grid_item ){
            // sort by selected first, then by original order
            return ($($grid_item).hasClass('selected') ? -1000 : 0 ) + $($grid_item).index();
          }
        },
        sortBy : 'selected'
        });

        $checkboxes.change(function(){
    var filters = [];
    // get checked checkboxes values
    $checkboxes.filter(':checked').each(function(){
      filters.push( this.value );
    });
    // ['.red', '.blue'] -> '.red, .blue'
    filters = filters.join(', ');
    $container.isotope({ filter: filters });
  });

        $.ajaxSetup({cache:false});
        $items.click(function(){

        var $this = $(this);
        var post_url = $this.attr('data-href');

        // don't proceed if already selected
        var $previousSelected = $('.selected');
  TweenMax.to(window, 2, {scrollTo:{y:"#filters", offsetY:120}});
        $(".content_text").html('<div class="loading">loading...</div>');
        $(".content_slider").html('<div class="loading">loading...</div>');
        $(".carousel-inner").load(post_url + " .carousel-item");
        $('.carousel').carousel({
        interval: 2000
      });
        $(".content_text").load(post_url + " .portfolio_info", function() {
          if ( !$this.hasClass('selected') ) {
            $this.addClass('selected big');
          }

       $previousSelected.removeClass('selected big');

            // update sortData for new items size
            $container
              .isotope( 'updateSortData', $this )
              .isotope( 'updateSortData', $previousSelected )
              .isotope();

          });
          $('.carousel-control-next').click(function(event){
  event.stopPropagation();
});
          });
          var tl = new TimelineMax({repeat:0,yoyo:false});
          tl.staggerFromTo(".grid_item", 0.5,
              {delay:2.75, x:170, autoAlpha:0,ease: Elastic.easeOut.config(2, 0.75)},
              {delay:2.75, x:0, autoAlpha:1,ease: Elastic.easeOut.config(2, 0.75)}  , 0.4);
            // layout Isotope after each image loads
                $container.imagesLoaded().progress( function() {
                $container.isotope('layout');
                });


                // Make the two sliders have the same height
                $(function() {
                  var imgHeight = '';

                  // Define a resize function
                  function setImgHeight() {
                    imgHeight = $('.carousel .carousel-item.active img').height();
                    $('.carousel-img').height(imgHeight);
                    console.log(imgHeight);
                  }

                  // Initialize the height
                  // setTimeout to wait until the image is loaded
                  setTimeout( function(){
                    setImgHeight();
                  }, 1000 );

                  // Recalculate the height if the screen is resized
                  $( window ).resize(function() {
                    setImgHeight();
                  });
                });


                function animateProgressBar(){

                    $(".meter > span").each(function() {
                        $(this)
                            .data("origWidth", $(this).width())
                            .width(0)
                            .animate({
                                width: $(this).data("origWidth")
                            }, 1200);
                    });
                }

var waypoint = new Waypoint({
  element: document.getElementById('about'),
  handler: function() {
    animateProgressBar();
  },
  offset: 580
});
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
