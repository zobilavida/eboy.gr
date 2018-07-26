jQuery(document).ready(function($) {

  $(document).on('facetwp-loaded', function() {

  $( ".stamp" ).click(function() {
    var text = $( this ).attr('stamp-name');
    var url = $( this ).attr('stamp-url');

    $( "input[name='stamp_url']" ).attr('value', url);
    $( "input[name='_custom_option']" ).val( text );
    $('.selected-stamp').attr('src', url);
  });


});
  });
