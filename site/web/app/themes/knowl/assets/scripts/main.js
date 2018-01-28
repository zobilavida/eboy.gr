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




        $('.sticky').Stickyfill();
      $('#login_modal').appendTo("body");


      $('#open-image').click(function (e) {
          e.preventDefault();
          $(this).ekkoLightbox();
      });

      $(document).on('click', '[data-toggle="lightbox"]', function(event) {
    event.preventDefault();
    $(this).ekkoLightbox();
});


        $(document).on( 'click', '.delete-post', function() {
          $(this).closest(".internship").addClass("active");
            var id = $(this).data('id');
            var nonce = $(this).data('nonce');
            var post = $(this).parents('.post:first');
            $.ajax({
                type: 'post',
                url: MyAjax.ajaxurl,
                data: {
                    action: 'my_delete_post',
                    nonce: nonce,
                    id: id
                },
                success: function( result ) {
                    if( result === 'success' ) {



                    }
                }
            });
            return false;
        });

              },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired


        $(document).on('facetwp-refresh', function() {
            $('.facetwp-template').animate({ opacity: 0 }, 1000);
        });
        $(document).on('facetwp-loaded', function() {
            $('.facetwp-template').animate({ opacity: 1 }, 1000);
        });




$.ajaxSetup({cache:false});
        $(document).on('facetwp-loaded', function() {
          $(".button-edit").click(function(){
            var post_url = $(this).attr('data-href');
            $(".editform").html("loading...");
              $(".editform").load(post_url + " .entry-form");
          });
        });



      $(document).on('facetwp-loaded', function() {
        $(".button-application").click(function(){
          var post_url = $(this).attr('data-href');
          $(".newapplication").html("loading...");
            $(".newapplication").load(post_url + " .apply-form");
        });
      });

      (function($){
          window.gwdc = function( options ) {
              this.options = options;
              this.startDateInput = $( '#input_' + this.options.formId + '_' + this.options.startFieldId );
              this.endDateInput = $( '#input_' + this.options.formId + '_' + this.options.endFieldId );
              this.countInput = $( '#input_' + this.options.formId + '_' + this.options.countFieldId );
              this.init = function() {
                  var gwdc = this;
                  // add data for "format" for parsing date
                  gwdc.startDateInput.data( 'format', this.options.startDateFormat );
                  gwdc.endDateInput.data( 'format', this.options.endDateFormat );
                  gwdc.populateDayCount();
                  gwdc.startDateInput.change( function() {
                      gwdc.populateDayCount();
                  } );
                  gwdc.endDateInput.change( function() {
                      gwdc.populateDayCount();
                  } );
                  $( '#ui-datepicker-div' ).hide();
              };
              this.getDayCount = function() {
                  var startDate = this.parseDate( this.startDateInput.val(), this.startDateInput.data('format') );
                  var endDate = this.parseDate( this.endDateInput.val(), this.endDateInput.data('format') );
                  var dayCount = 0;
                  if( !this.isValidDate( startDate ) || !this.isValidDate( endDate ) )
                      return '';
                  if( startDate > endDate ) {
                      return 0;
                  } else {
                      var diff = endDate - startDate;
                      dayCount = diff / ( 60 * 60 * 24 * 1000 ); // secs * mins * hours * milliseconds
                      dayCount = Math.round( dayCount ) + this.options.countAdjust;
                      return dayCount;
                  }
              };
              this.parseDate = function( value, format ) {
                  if( !value )
                      return false;
                  format = format.split('_');
                  var dateFormat = format[0];
                  var separators = { slash: '/', dash: '-', dot: '.' };
                  var separator = format.length > 1 ? separators[format[1]] : separators.slash;
                  var dateArr = value.split(separator);
                  switch( dateFormat ) {
                  case 'mdy':
                      return new Date( dateArr[2], dateArr[0] - 1, dateArr[1] );
                  case 'dmy':
                      return new Date( dateArr[2], dateArr[1] - 1, dateArr[0] );
                  case 'ymd':
                      return new Date( dateArr[0], dateArr[1] - 1, dateArr[2] );
                  }
                  return false;
              };
              this.populateDayCount = function() {
                  this.countInput.val( this.getDayCount() ).change();
              };
              this.isValidDate = function( date ) {
                  return !isNaN( Date.parse( date ) );
              };
              this.init();
          };
      })(jQuery);
    }
    },
    // Home page
    'home': {
      init: function() {
        // JavaScript to be fired on the home page

        $('.Count').each(function () {
          var $this = $(this);
          jQuery({ Counter: 0 }).animate({ Counter: $this.text() }, {
            duration: 3000,
            easing: 'swing',
            step: function () {
              $this.text(Math.ceil(this.Counter));
            }
          });
        });


        function scaleToFill() {
            $('video.video-background').each(function(index, videoTag) {
               var $video = $(videoTag),
                   videoRatio = videoTag.videoWidth / videoTag.videoHeight,
                   tagRatio = $video.width() / $video.height(),
                   val;

               if (videoRatio < tagRatio) {
                   val = tagRatio / videoRatio * 1.00;
               } else if (tagRatio < videoRatio) {
                   val = videoRatio / tagRatio * 1.00;
               }

               $video.css('transform','scale(' + val  + ',' + val + ')');

            });
        }

        $(function () {
            scaleToFill();

            $('.video-background').on('loadeddata', scaleToFill);

            $(window).resize(function() {
                scaleToFill();
            });
        });
      },
      finalize: function() {
        // JavaScript to be fired on the home page, after the init JS
        (function ($) {

          // Init ScrollMagic
            var controller = new ScrollMagic.Controller();

            // get all slides
          var slides = ["#slide01", "#slide02", "#slide03"];

          // get all headers in slides that trigger animation
          var headers = ["#slide01 header", "#slide02 header", "#slide03 header"];

          // get all break up sections
          var breakSections = ["#cb01", "#cb02", "#cb03"];

          // Enable ScrollMagic only for desktop, disable on touch and mobile devices
          if (!Modernizr.touch) {

              // SCENE 4 - parallax effect on each of the slides with bcg
              // move bcg container when slide gets into the view
            slides.forEach(function (slide, index) {

              var $bcg = $(slide).find('.bcg');

              var slideParallaxScene = new ScrollMagic.Scene({
                    triggerElement: slide,
                    triggerHook: 1,
                    duration: "100%"
                })
                .setTween(TweenMax.from($bcg, 1, {y: '-40%', autoAlpha: 0.3, ease:Power0.easeNone}))
                .addTo(controller);
              });

              // SCENE 5 - parallax effect on the intro slide
              // move bcg container when intro gets out of the the view

              var introTl = new TimelineMax();

              introTl

                .to($('#intro .video-background'), 1.4, {y: '40%', ease:Power1.easeOut}, '-=0.6');


            var introScene = new ScrollMagic.Scene({
                  triggerElement: '#intro',
                  triggerHook: 0,
                  duration: "100%"
              })
              .setTween(introTl)
              .addTo(controller);



              // SCENE 6 - sidebar sticky

              var stick = new TimelineMax();

              stick
                .to($('#sticky_item'), 0, {css:{className:'+=sticky'}});

            var stickScene = new ScrollMagic.Scene({
                  triggerElement: '#sticky_trigger',
                  triggerHook: 1
              })
              .setTween(stick)
              .addTo(controller);



              // change behaviour of controller to animate scroll instead of jump
            controller.scrollTo(function (newpos) {
              TweenMax.to(window, 1, {scrollTo: {y: newpos}, ease:Power1.easeInOut});
            });

            //  bind scroll to anchor links
            $(document).on("click", "a[href^='#']", function (e) {
              var id = $(this).attr("href");
              if ($(id).length > 0) {
                e.preventDefault();

                // trigger scroll
                controller.scrollTo(id);

                  // if supported by the browser we can even update the URL.
                if (window.history && window.history.pushState) {
                  history.pushState("", document.title, id);
                }
              }
            });

          }

        }(jQuery));

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
