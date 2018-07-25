jQuery(document).ready(function($) {
    $(document).on('facetwp-loaded', function() {
         var selectedurl = $( "input[name='stamp_url']" ).attr('value');

         $('.selected-stamp').attr('src', selectedurl);
 });
 });
