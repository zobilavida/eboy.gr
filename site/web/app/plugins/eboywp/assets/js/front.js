var EWP = EWP || {};

(function($) {

    var defaults = {
        'facets': {},
        'template': null,
        'settings': {},
        'is_reset': false,
        'is_refresh': false,
        'is_bfcache': false,
        'auto_refresh': true,
        'soft_refresh': false,
        'static_facet': null,
        'used_facets': {},
        'facet_type': {},
        'loaded': false,
        'jqXHR': false,
        'extras': {},
        'helper': {},
        'paged': 1
    };

    for (var prop in defaults) {
        if ('undefined' === typeof EWP[prop]) {
            EWP[prop] = defaults[prop];
        }
    }

    // Safari popstate fix
    $(window).on('load', function() {
        setTimeout(function() {
            $(window).on('popstate', function() {

                // Detect browser "back-foward" cache
                if (EWP.is_bfcache) {
                    EWP.loaded = false;
                }

                if ((EWP.loaded || EWP.is_bfcache) && ! EWP.is_refresh) {
                    EWP.is_popstate = true;
                    EWP.refresh();
                    EWP.is_popstate = false;
                }
            });
        }, 0);
    });


    EWP.helper.get_url_var = function(name) {
        var name = EWP_JSON.prefix + name;
        var query_string = EWP.build_query_string();
        var url_vars = query_string.split('&');
        for (var i = 0; i < url_vars.length; i++) {
            var item = url_vars[i].split('=');
            if (item[0] === name) {
                return item[1];
            }
        }
        return false;
    }


    EWP.helper.serialize = function(obj, prefix) {
        var str = [];
        var prefix = ('undefined' !== typeof prefix) ? prefix : '';
        for (var p in obj) {
            if ('' != obj[p]) { // This must be "!=" instead of "!=="
                str.push(prefix + encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]));
            }
        }
        return str.join('&');
    }


    EWP.helper.escape_html = function(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; }).trim();
    }


    EWP.helper.detect_loop = function(node) {
        var iterator = document.createNodeIterator(node, NodeFilter.SHOW_COMMENT, EWP.helper.node_filter, false);
        while (curNode = iterator.nextNode()) {
            if (8 === curNode.nodeType && 'EWP-loop' === curNode.nodeValue) {
                return curNode.parentNode;
            }
        }

        return false;
    }


    EWP.helper.node_filter = function() {
        return NodeFilter.FILTER_ACCEPT;
    }


    // Refresh on each facet interaction?
    EWP.autoload = function() {
        if (EWP.auto_refresh && ! EWP.is_refresh) {
            EWP.refresh();
        }
    }


    EWP.refresh = function() {
        EWP.is_refresh = true;

        // Load facet DOM values
        if (! EWP.is_reset) {
            EWP.parse_facets();
        }

        // Check the URL on pageload
        if (! EWP.loaded) {
            EWP.load_from_hash();
        }

        // Fire a notification event
        $(document).trigger('eboywp-refresh');

        // Trigger window.onpopstate
        if (EWP.loaded && ! EWP.is_popstate) {
            EWP.set_hash();
        }

        // Preload?
        if (! EWP.loaded && ! EWP.is_bfcache && 'undefined' !== typeof EWP_JSON.preload_data) {
            EWP.render(EWP_JSON.preload_data);
        }
        else {
            EWP.fetch_data();
        }

        // Cleanup
        EWP.paged = 1;
        EWP.static_facet = null;
        EWP.soft_refresh = false;
        EWP.is_refresh = false;
        EWP.is_reset = false;
    }


    EWP.parse_facets = function() {
        EWP.facets = {};

        $('.eboywp-facet').each(function() {
            var $this = $(this);
            var facet_name = $this.attr('data-name');
            var facet_type = $this.attr('data-type');

            // Store the facet type
            EWP.facet_type[facet_name] = facet_type;

            // Plugin hook
            wp.hooks.doAction('eboywp/refresh/' + facet_type, $this, facet_name);

            // Support custom loader
            var do_loader = true;
            if (EWP.loaded) {
                if (EWP.soft_refresh || facet_name === EWP.static_facet || 'undefined' !== typeof EWP.used_facets[facet_name]) {
                    do_loader = false;
                }
            }

            if (do_loader) {
                EWP.loading_handler({
                    'element': $this,
                    'facet_name': facet_name,
                    'facet_type': facet_type
                });
            }
        });

        // Add pagination to the URL hash
        if (1 < EWP.paged) {
            EWP.facets['paged'] = EWP.paged;
        }

        // Add "per page" to the URL hash
        if (EWP.extras.per_page && 'default' !== EWP.extras.per_page) {
            EWP.facets['per_page'] = EWP.extras.per_page;
        }

        // Add sorting to the URL hash
        if (EWP.extras.sort && 'default' !== EWP.extras.sort) {
            EWP.facets['sort'] = EWP.extras.sort;
        }
    }


    EWP.loading_handler = function(args) {
        if ('fade' == EWP_JSON.loading_animation) {
            if (! EWP.loaded) {
                var $el = args.element;
                $(document).on('eboywp-refresh', function() {
                    $el.prepend('<div class="eboywp-overlay">');
                    $el.find('.eboywp-overlay').css({
                        width: $el.width(),
                        height: $el.height()
                    });
                });

                $(document).on('eboywp-loaded', function() {
                    $el.find('.eboywp-overlay').remove();
                });
            }
        }
        else if ('' == EWP_JSON.loading_animation) {
            args.element.html('<div class="eboywp-loading"></div>');
        }
    }


    EWP.build_query_string = function() {
        var query_string = '';

        // Non-eboywp URL variables
        var hash = [];
        var get_str = window.location.search.replace('?', '').split('&');
        $.each(get_str, function(idx, val) {
            var param_name = val.split('=')[0];
            if (0 !== param_name.indexOf(EWP_JSON.prefix)) {
                hash.push(val);
            }
        });
        hash = hash.join('&');

        // eboywp URL variables
        var EWP_vars = EWP.helper.serialize(EWP.facets, EWP_JSON.prefix);

        if ('' !== hash) {
            query_string += hash;
        }
        if ('' !== EWP_vars) {
            query_string += ('' !== hash ? '&' : '') + EWP_vars;
        }

        return query_string;
    }


    EWP.set_hash = function() {
        var query_string = EWP.build_query_string();

        if ('' !== query_string) {
            query_string = '?' + query_string;
        }

        if (history.pushState) {
            history.pushState(null, null, window.location.pathname + query_string);
        }

        // Update EWP_HTTP.get
        EWP_HTTP.get = {};
        window.location.search.replace('?', '').split('&').forEach(function(el) {
            var item = el.split('=');
            EWP_HTTP.get[item[0]] = item[1];
        });
    }


    EWP.load_from_hash = function() {
        var hash = [];
        var get_str = window.location.search.replace('?', '').split('&');
        $.each(get_str, function(idx, val) {
            var param_name = val.split('=')[0];
            if (0 === param_name.indexOf(EWP_JSON.prefix)) {
                hash.push(val.replace(EWP_JSON.prefix, ''));
            }
        });
        hash = hash.join('&');

        // Reset facet values
        $.each(EWP.facets, function(f) {
            EWP.facets[f] = [];
        });

        EWP.paged = 1;
        EWP.extras.sort = 'default';

        if ('' !== hash) {
            hash = hash.split('&');
            $.each(hash, function(idx, chunk) {
                var obj = chunk.split('=')[0];
                var val = chunk.split('=')[1];

                if ('paged' === obj) {
                    EWP.paged = val;
                }
                else if ('per_page' === obj) {
                    EWP.extras.per_page = val;
                }
                else if ('sort' === obj) {
                    EWP.extras.sort = val;
                }
                else if ('' !== val) {
                    var type = ('undefined' !== typeof EWP.facet_type[obj]) ? EWP.facet_type[obj] : '';
                    if ('search' === type || 'autocomplete' === type) {
                        EWP.facets[obj] = decodeURIComponent(val);
                    }
                    else {
                        EWP.facets[obj] = decodeURIComponent(val).split(',');
                    }
                }
            });
        }
    }


    EWP.fetch_data = function() {
        // Abort pending requests
        if (EWP.jqXHR && EWP.jqXHR.readyState !== 4) {
            EWP.jqXHR.abort();
        }

        var endpoint = ('wp' === EWP.template) ? document.URL : EWP_JSON.ajaxurl;

        // dataType is "text" for better JSON error handling
        EWP.jqXHR = $.ajax(endpoint, {
            type: 'POST',
            dataType: 'text',
            data: {
                action: 'eboywp_refresh',
                data: {
                    'facets': JSON.stringify(EWP.facets),
                    'static_facet': EWP.static_facet,
                    'used_facets': EWP.used_facets,
                    'http_params': EWP_HTTP,
                    'template': EWP.template,
                    'extras': EWP.extras,
                    'soft_refresh': EWP.soft_refresh ? 1 : 0,
                    'is_bfcache': EWP.is_bfcache ? 1 : 0,
                    'first_load': EWP.loaded ? 0 : 1,
                    'paged': EWP.paged
                }
            },
            success: function(response) {
                try {
                    var json_object = $.parseJSON(response);
                    EWP.render(json_object);
                }
                catch(e) {
                    var pos = response.indexOf('{"facets');
                    if (-1 < pos) {
                        var error = response.substr(0, pos);
                        var json_object = $.parseJSON(response.substr(pos));
                        EWP.render(json_object);

                        // Log the error
                        console.log(error);
                    }
                    else {
                        $('.eboywp-template').text('eboywp was unable to auto-detect the post listing');

                        // Log the error
                        console.log(response);
                    }
                }
            }
        });
    }


    EWP.render = function(response) {

        // Don't render CSS-based (or empty) templates on pageload
        // The template has already been pre-loaded
        if (('wp' === EWP.template || '' === response.template) && ! EWP.loaded && ! EWP.is_bfcache) {
            var inject = false;
        }
        else {
            var inject = response.template;

            if ('wp' === EWP.template) {
                var $tpl = $(response.template).find('.eboywp-template');

                if (1 > $tpl.length) {
                    var wrap = document.createElement('div');
                    wrap.innerHTML = response.template;
                    var loop = EWP.helper.detect_loop(wrap);

                    if (loop) {
                        $tpl = $(loop).addClass('eboywp-template');
                    }
                }

                if (0 < $tpl.length) {
                    var inject = $tpl.html();
                }
                else {
                    // Fallback until "loop_no_results" action is added to WP core
                    var inject = EWP_JSON['no_results_text'];
                }
            }
        }

        if (false !== inject) {
            if (! wp.hooks.applyFilters('eboywp/template_html', false, { 'response': response, 'html': inject })) {
                $('.eboywp-template').html(inject);
            }
        }

        // Populate each facet box
        $.each(response.facets, function(name, val) {
            $('.eboywp-facet-' + name).html(val);
        });

        // Populate the counts
        if ('undefined' !== typeof response.counts) {
            $('.eboywp-counts').html(response.counts);
        }

        // Populate the pager
        if ('undefined' !== typeof response.pager) {
            $('.eboywp-pager').html(response.pager);
        }

        // Populate the "per page" box
        if ('undefined' !== typeof response.per_page) {
            $('.eboywp-per-page').html(response.per_page);
            if ('default' !== EWP.extras.per_page) {
                $('.eboywp-per-page-select').val(EWP.extras.per_page);
            }
        }

        // Populate the sort box
        if ('undefined' !== typeof response.sort) {
            $('.eboywp-sort').html(response.sort);
            $('.eboywp-sort-select').val(EWP.extras.sort);
        }

        // Populate the settings object (iterate to preserve static facet settings)
        $.each(response.settings, function(key, val) {
            EWP.settings[key] = val;
        });

        // WP Playlist support
        if ('function' === typeof WPPlaylistView) {
            $('.eboywp-template .wp-playlist').each(function() {
                return new WPPlaylistView({ el: this });
            });
        }

        // Fire a notification event
        $(document).trigger('eboywp-loaded');

        // Allow final actions
        wp.hooks.doAction('eboywp/loaded');

        // Detect "back-forward" cache
        EWP.is_bfcache = true;

        // Done loading?
        EWP.loaded = true;
    }


    EWP.reset = function(facet_name) {
        EWP.parse_facets();

        if ('undefined' !== typeof facet_name) {
            EWP.facets[facet_name] = [];

            if ('undefined' !== typeof EWP.used_facets) {
                delete EWP.used_facets[facet_name];
            }
        }
        else {
            $.each(EWP.facets, function(f) {
                EWP.facets[f] = [];
            });

            EWP.extras.sort = 'default';
            EWP.used_facets = {};
        }

        EWP.is_reset = true;
        EWP.refresh();
    }


    EWP.init = function() {
        if (0 < $('.eboywp-sort').length) {
            EWP.extras.sort = 'default';
        }

        if (0 < $('.eboywp-pager').length) {
            EWP.extras.pager = true;
        }

        if (0 < $('.eboywp-per-page').length) {
            EWP.extras.per_page = 'default';
        }

        if (0 < $('.eboywp-counts').length) {
            EWP.extras.counts = true;
        }

        if (0 < $('.eboywp-selections').length) {
            EWP.extras.selections = true;
        }

        // Make sure there's a template
        var has_template = $('.eboywp-template').length > 0;

        if (! has_template) {
            var has_loop = EWP.helper.detect_loop(document.body);

            if (has_loop) {
                $(has_loop).addClass('eboywp-template');
            }
            else {
                return;
            }
        }

        var $div = $('.eboywp-template:first');
        EWP.template = $div.is('[data-name]') ? $div.attr('data-name') : 'wp';

        // Facets inside the template?
        if (0 < $div.find('.eboywp-facet').length) {
            console.error('Facets should not be inside the "eboywp-template" container');
        }

        wp.hooks.doAction('eboywp/ready');

        // Generate the user selections
        if (EWP.extras.selections) {
            wp.hooks.addAction('eboywp/loaded', function() {
                var selections = '';
                $.each(EWP.facets, function(key, val) {
                    if (val.length < 1 || 'undefined' === typeof EWP.settings.labels[key]) {
                        return true; // skip this facet
                    }

                    var choices = val;
                    var facet_type = $('.eboywp-facet-' + key).attr('data-type');
                    choices = wp.hooks.applyFilters('eboywp/selections/' + facet_type, choices, {
                        'el': $('.eboywp-facet-' + key),
                        'selected_values': choices
                    });

                    if ('string' === typeof choices) {
                        choices = [{ value: '', label: choices }];
                    }
                    else if ('undefined' === typeof choices[0].label) {
                        choices = [{ value: '', label: choices[0] }];
                    }

                    var values = '';
                    $.each(choices, function(idx, choice) {
                        values += '<span class="eboywp-selection-value" data-value="' + choice.value + '">' + EWP.helper.escape_html(choice.label) + '</span>';
                    });

                    selections += '<li data-facet="' + key + '"><span class="eboywp-selection-label">' + EWP.settings.labels[key] + ':</span> ' + values + '</li>';
                });

                if ('' !== selections) {
                    selections = '<ul>' + selections + '</ul>';
                }

                $('.eboywp-selections').html(selections);
            });
        }

        // Click on a user selection
        $(document).on('click', '.eboywp-selections .eboywp-selection-value', function() {
            if (EWP.is_refresh) {
                return;
            }

            var facet_name = $(this).closest('li').attr('data-facet');
            var facet_value = $(this).attr('data-value');

            EWP.parse_facets();
            EWP.is_reset = true;

            if ('' != facet_value) {
                var arr = EWP.facets[facet_name];
                var arr_idx = arr.indexOf(facet_value);
                if (-1 < arr_idx) {
                    arr.splice(arr_idx, 1);
                    EWP.facets[facet_name] = arr;
                }
            }
            else {
                EWP.facets[facet_name] = [];
            }

            if ('undefined' !== typeof EWP.used_facets) {
                delete EWP.used_facets[facet_name]; // slider support
            }

            delete EWP.facets['paged']; // remove "paged" from URL
            EWP.refresh();
        });

        // Pagination
        $(document).on('click', '.eboywp-page', function() {
            $('.eboywp-page').removeClass('active');
            $(this).addClass('active');

            EWP.paged = $(this).attr('data-page');
            EWP.soft_refresh = true;
            EWP.refresh();
        });

        // Per page
        $(document).on('change', '.eboywp-per-page-select', function() {
            EWP.extras.per_page = $(this).val();
            EWP.soft_refresh = true;
            EWP.autoload();
        });

        // Sorting
        $(document).on('change', '.eboywp-sort-select', function() {
            EWP.extras.sort = $(this).val();
            EWP.soft_refresh = true;
            EWP.autoload();
        });

        EWP.refresh();
    }


    $(function() {
        EWP.init();
    });
})(jQuery);
