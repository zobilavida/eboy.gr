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
        $(window).scroll(function(){
          scrollTop = $(window).scrollTop();
           $('.counter').html(scrollTop);

          if (scrollTop >= 100) {
            $('.navbar').addClass('scrolled-nav');
          } else if (scrollTop < 100) {
            $('.navbar').removeClass('scrolled-nav');
          }

        });



        $('.input-daterange').datepicker({
            format: "MM dd, yyyy",
            autoclose: true,
            todayHighlight: true,
            startDate: "today"
        });

$('#timepicker1').timepicker({
  template: 'dropdown'
});
$('#timepicker2').timepicker();

var $openform = $('.openform');
$openform.click(function(){
var $this = $(this);
var post_name = $this.attr('data-href');

$(this).addClass( "selected" );

$('.choosen_car').html(post_name);
});

$("button#submit").click(function(){
$.ajax({
type: "POST",
url: "https://eboy.gr/app/themes/carhub/feedback.php",
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

      },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired
      }
    },
    // Home page
    'home': {
      init: function() {
        // JavaScript to be fired on the home page
        $(".openform").click(function(){
          //TweenMax.fromTo( $(".paralsec"), 1.2, {css: {backgroundSize: "100% 80%"}}, {css:{backgroundSize: "100% 41%" }, ease: Elastic.easeOut.config(1, 0.3) }) ;
        //  TweenMax.fromTo( $(".forma"), 1.2, {css: {display: "none"}}, {css:{display: "block" }, ease: Elastic.easeOut.config(1, 0.3) }) ;
            });
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
