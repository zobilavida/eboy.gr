(function($) {
    $(document).on('eboywp-loaded', function() {
        $('.edd-no-js').hide();
        $('a.edd-add-to-cart').addClass('edd-has-js');
    });
})(jQuery);