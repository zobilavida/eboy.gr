(function($) {
    $(document).on('eboywp-loaded', function() {
        $('.eboywp-checkbox').each(function() {
            $(this).attr('role', 'checkbox');
            $(this).attr('aria-checked', $(this).hasClass('checked') ? 'true' : 'false');
            $(this).attr('tabindex', 0);
        });
    });
})(jQuery);