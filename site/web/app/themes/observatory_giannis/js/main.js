jQuery(document).ready(function($) {

  var hamburger = document.querySelector(".hamburger");
  hamburger.addEventListener("click", function() {
    hamburger.classList.toggle("is-active");
    $(".navbar-collapse").toggleClass( "show" );
  });

  /* ---------------------------------------------- /*
   * Hero full window height
   /* ---------------------------------------------- */
  $('.hero_image').css("height", $(window).height());

  $( window ).resize(function() {
  $('.hero_image').css("height", $(window).height());
  });

  /* ---------------------------------------------- /*
   * skyline full document height
   /* ---------------------------------------------- */
  $('.skyline_fixed').css("height", $(document).height() -319);

  $( window ).resize(function() {
  $('.skyline_fixed').css("height", $(document).height() -319);
  });

  /* ---------------------------------------------- /*
   * Set backgrounds
   /* ---------------------------------------------- */

  var module = $('.module, .module-small, .skyline_fixed');
  module.each(function(i) {
      if ($(this).attr('data-background')) {
          $(this).css('background-image', 'url(' + $(this).attr('data-background') + ')');
      }
  });



});
