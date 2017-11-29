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

$( "#contactForm" ).validate({
   rules: {
       contactname: {
       required: true
     },
     contactemail: {
       required: true,
       email: true
     },
     contactcontent: {
       required: true
     }
   }
 });

 $(function () {
   $('[data-toggle="popover"]').popover();
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
      //  gutter: '.gutter-sizer'
        }
        });

        $checkboxes.change(function(){
    var filters = [];

    // get checked checkboxes values
    $checkboxes.filter(':checked').each(function(){
      filters.push( this.value );
      TweenMax.fromTo( $(".ajax_content"), 1.2, {css: {display: "block"}}, {css:{display: "none" }, ease: Elastic.easeOut.config(1, 0.3) }) ;
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
        $(this).addClass( "selected" );
        $(".carousel-inner").html('<div class="loading">loading...</div>');
        $(".content_slider").html('<div class="loading">loading...</div>');
        $(".portfolio_left").load(post_url + " .card");

        $(".portfolio_right").load(post_url + " .portfolio_info", function() {
          TweenMax.to(window, 2, {scrollTo:{y:"#filters", offsetY:40}});
          TweenMax.to($(".ajax_content"), 1, {css: {display: "block"}});
        //  TweenMax.from($(".portfolio_left"), 1, {css: {display: "block"}});
          TweenMax.fromTo( $(".portfolio_left"), 1.2, {css:{left:400, autoAlpha: 0}}, {css:{left:0 , autoAlpha : 1 }, ease: Elastic.easeOut.config(1, 0.3) }) ;
          $('.carousel').carousel({
              interval: 3100,
              pause:false
          });

          $('.carousel').carousel('cycle');

       $previousSelected.removeClass('selected big');


            // update sortData for new items size
            $container
              .isotope( 'updateSortData', $this )
              .isotope( 'updateSortData', $previousSelected )
              .isotope();
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

                $("button#submit").click(function(){
                $.ajax({
                type: "POST",
                url: "https://eboy.gr/app/themes/eboy_theme/feedback.php",
                data: $('form.feedback').serialize(),
                success: function(message){
                $("#feedback").html(message);
                $("#feedback-modal").modal('hide');
                },
                error: function(){
                alert("Error");
                }
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
    'isotope': {
      init: function() {
        // JavaScript to be fired on the about us page

var $grid = $('.grid').isotope({
  itemSelector: '.grid-item',
  percentPosition: true,
  masonry: {
    columnWidth: '.grid-sizer'
  }
});

$grid.on( 'click', '.grid-item-content', function() {

  var itemContent = this;
  setItemContentPixelSize( itemContent );

  var itemElem = itemContent.parentNode;
  $( itemElem ).toggleClass('is-expanded');

  // force redraw
  var redraw = itemContent.offsetWidth;
  // renable default transition
  itemContent.style[ transitionProp ] = '';

  addTransitionListener( itemContent );
  setItemContentTransitionSize( itemContent, itemElem );

  $grid.isotope('layout');
});


var docElemStyle = document.documentElement.style;
var transitionProp = typeof docElemStyle.transition == 'string' ?
  'transition' : 'WebkitTransition';
var transitionEndEvent = {
  WebkitTransition: 'webkitTransitionEnd',
  transition: 'transitionend'
}[ transitionProp ];

function setItemContentPixelSize( itemContent ) {
  var previousContentSize = getSize( itemContent );
  // disable transition
  itemContent.style[ transitionProp ] = 'none';
  // set current size in pixels
  itemContent.style.width = previousContentSize.width + 'px';
  itemContent.style.height = previousContentSize.height + 'px';
}

function addTransitionListener( itemContent ) {
  if ( !transitionProp ) {
    return;
  }
  // reset 100%/100% sizing after transition end
  var onTransitionEnd = function() {
    itemContent.style.width = '';
    itemContent.style.height = '';
    itemContent.removeEventListener( transitionEndEvent, onTransitionEnd );
  };
  itemContent.addEventListener( transitionEndEvent, onTransitionEnd );
}

function setItemContentTransitionSize( itemContent, itemElem ) {
  // set new size
  var size = getSize( itemElem );
  itemContent.style.width = size.width + 'px';
  itemContent.style.height = size.height + 'px';
}


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
