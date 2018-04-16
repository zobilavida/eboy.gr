(function($) {
    $(function() {
        if ('object' !== typeof EWP) {
            return;
        }

        wp.hooks.addFilter('facetwp/template_html', function(resp, params) {
            if (EWP.is_load_more) {
                EWP.is_load_more = false;
                $('.facetwp-template').append(params.html);
                return true;
            }
            return resp;
        });
    });

    $(document).on('click', '.fwp-load-more', function() {
        EWP.is_load_more = true; // set the flag
        EWP.load_more_paged += 1; // next page
        EWP.facets['load_more'] = [EWP.load_more_paged]; // trick into adding URL var
        EWP.paged = EWP.load_more_paged; // grab the next page of results
        EWP.soft_refresh = true; // don't process facets
        EWP.is_reset = true; // don't parse facets
        EWP.refresh();
    });

    $(document).on('facetwp-loaded', function() {
        if (EWP.settings.pager.page < EWP.settings.pager.total_pages) {
            $('.fwp-load-more').show();
        }
        else {
            $('.fwp-load-more').hide();
        }
    });

    $(document).on('facetwp-refresh', function() {
        if (! EWP.loaded) {
            var uv = EWP_HTTP.url_vars;
            var paged = ('undefined' !== typeof uv.load_more) ? uv.load_more : 1;
            EWP.load_more_paged = parseInt(paged);
        }
        else {
            if (! EWP.is_load_more) {
                EWP.load_more_paged = 1;
            }
        }
    });
})(jQuery);
