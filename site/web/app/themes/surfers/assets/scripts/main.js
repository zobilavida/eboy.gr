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
var $wind = $('.wind');
var $bubble_all = $('.bubble_all');
var $boy = $('.boy');
var $col_boy = $('.col_boy');
var $girl = $('.girl');
var $col_girl = $('.col_girl');

TweenLite.set($wind, {x: '+=200px', scale: 1});
TweenLite.set($bubble_all, {x: '+=208px', scale: 1.1});
TweenLite.set($boy, {x: '+=110px', y:'+=60px', scale: 1});
TweenLite.set($girl, {x: '-=20px', y:'+=80px', scale: 1});

TweenMax.fromTo($wind, 1,
		{scale: 0}, {delay: 1, ease: Elastic.easeOut.config(2.5, 1), scale: 1, repeat: 0, yoyo: false});
TweenMax.fromTo($bubble_all, 1,
		{scale: 0}, {delay: 1.5, ease: Elastic.easeOut.config(2.5, 1), scale: 1, repeat: 0, yoyo: false});
TweenMax.fromTo($col_boy, 2,
		{x: 1224}, {delay: 1.75, ease: Elastic.easeOut.config(1, 1), x: 0, repeat: 0, yoyo: false});
TweenMax.fromTo($boy, 4,
		{y: 60}, {ease: Bounce.easeOut, y: 80, repeat: -1, yoyo: true});
TweenMax.fromTo($col_girl, 2,
		{x: 1224}, {delay: 2, ease: Elastic.easeOut.config(1, 1), x: 0, repeat: 0, yoyo: false});
TweenMax.fromTo($girl, 4,
		{y: 60, }, {delay:0.5, ease: Bounce.easeOut, y: 80, repeat: -1, yoyo: true});



   },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired


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
