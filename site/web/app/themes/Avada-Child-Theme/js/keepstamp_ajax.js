jQuery(document).ready(function($) {
         var selectedurl = $( "input[name='stamp_url']" ).attr('value');

         $('.selected-stamp').attr('src', selectedurl);
 });
