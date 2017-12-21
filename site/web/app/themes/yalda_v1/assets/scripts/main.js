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

      },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired

                   var $slider = $('.slideshow .slider'),
                 maxItems = $('.item', $slider).length,
                 dragging = false,
                 tracking,
                 rightTracking;

               $sliderRight = $('.slideshow').clone().addClass('slideshow-right').appendTo($('.split-slideshow'));

               rightItems = $('.item', $sliderRight).toArray();
               reverseItems = rightItems.reverse();
               $('.slider', $sliderRight).html('');
               for (i = 0; i < maxItems; i++) {
                 $(reverseItems[i]).appendTo($('.slider', $sliderRight));
               }

               $slider.addClass('slideshow-left');
               $('.slideshow-left').slick({
                 vertical: true,
                 verticalSwiping: true,
                 arrows: false,
                 infinite: true,
                 dots: true,
                 speed: 1000,
                 cssEase: 'cubic-bezier(0.7, 0, 0.3, 1)'
               }).on('beforeChange', function(event, slick, currentSlide, nextSlide) {

                 if (currentSlide > nextSlide && nextSlide === 0 && currentSlide === maxItems - 1) {
                   $('.slideshow-right .slider').slick('slickGoTo', -1);
                   $('.slideshow-text').slick('slickGoTo', maxItems);
                 } else if (currentSlide < nextSlide && currentSlide === 0 && nextSlide === maxItems - 1) {
                   $('.slideshow-right .slider').slick('slickGoTo', maxItems);
                   $('.slideshow-text').slick('slickGoTo', -1);
                 } else {
                   $('.slideshow-right .slider').slick('slickGoTo', maxItems - 1 - nextSlide);
                   $('.slideshow-text').slick('slickGoTo', nextSlide);
                 }
               }).on("mousewheel", function(event) {
                 event.preventDefault();
                 if (event.deltaX > 0 || event.deltaY < 0) {
                   $(this).slick('slickNext');
                 } else if (event.deltaX < 0 || event.deltaY > 0) {
                   $(this).slick('slickPrev');
                 }
               }).on('mousedown touchstart', function(){
                 dragging = true;
                 tracking = $('.slick-track', $slider).css('transform');
                 tracking = parseInt(tracking.split(',')[5]);
                 rightTracking = $('.slideshow-right .slick-track').css('transform');
                 rightTracking = parseInt(rightTracking.split(',')[5]);
               }).on('mousemove touchmove', function(){
                 if (dragging) {
                   newTracking = $('.slideshow-left .slick-track').css('transform');
                   newTracking = parseInt(newTracking.split(',')[5]);
                   diffTracking = newTracking - tracking;
                   $('.slideshow-right .slick-track').css({'transform': 'matrix(1, 0, 0, 1, 0, ' + (rightTracking - diffTracking) + ')'});
                 }
               }).on('mouseleave touchend mouseup', function(){
                 dragging = false;
               });

               $('.slideshow-right .slider').slick({
                 swipe: false,
                 vertical: true,
                 arrows: false,
                 infinite: true,
                 speed: 950,
                 cssEase: 'cubic-bezier(0.7, 0, 0.3, 1)',
                 initialSlide: maxItems - 1
               });
  


      }
    },
    // Home page
    'home': {
      init: function() {
        // JavaScript to be fired on the home page
      },
      finalize: function() {
        // JavaScript to be fired on the home page, after the init JS
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
