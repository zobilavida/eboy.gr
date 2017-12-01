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
        $('.carousel').carousel();
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
        $('.grid').isotope({
  itemSelector: '.grid-item',
  percentPosition: true,
  layoutMode: 'masonry'
});

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
