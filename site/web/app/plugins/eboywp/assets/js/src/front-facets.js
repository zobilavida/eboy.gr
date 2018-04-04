(function($) {

    EWP.logic = EWP.logic || {};

    /* ======== IE11 .val() fix ======== */

    $.fn.pVal = function() {
        var val = $(this).eq(0).val();
        return val === $(this).attr('placeholder') ? '' : val;
    }

    /* ======== Autocomplete ======== */

    wp.hooks.addAction('eboywp/refresh/autocomplete', function($this, facet_name) {
        var val = $this.find('.eboywp-autocomplete').val() || '';
        EWP.facets[facet_name] = val;
    });

    $(document).on('eboywp-loaded', function() {
        $('.eboywp-autocomplete:not(.ready)').each(function() {
            var $this = $(this);
            var $parent = $this.closest('.eboywp-facet');
            var facet_name = $parent.attr('data-name');
            var opts = wp.hooks.applyFilters('eboywp/set_options/autocomplete', {
                serviceUrl: ('wp' === EWP.template) ? document.URL : EWP_JSON.ajaxurl,
                type: 'POST',
                minChars: 3,
                deferRequestBy: 200,
                showNoSuggestionNotice: true,
                noSuggestionNotice: EWP_JSON['no_results'],
                params: {
                    action: 'eboywp_autocomplete_load',
                    facet_name: facet_name,
                    data: EWP.build_post_data()
                }
            }, { 'facet_name': facet_name });
            $this.autocomplete(opts);
            $this.addClass('ready');
        });
    });

    $(document).on('keyup', '.eboywp-autocomplete', function(e) {
        if (13 === e.which) {
            EWP.autoload();
        }
    });

    $(document).on('click', '.eboywp-autocomplete-update', function() {
        EWP.autoload();
    });

    /* ======== Checkboxes ======== */

    wp.hooks.addAction('eboywp/refresh/checkboxes', function($this, facet_name) {
        var selected_values = [];
        $this.find('.eboywp-checkbox.checked').each(function() {
            selected_values.push($(this).attr('data-value'));
        });
        EWP.facets[facet_name] = selected_values;
    });

    wp.hooks.addFilter('eboywp/selections/checkboxes', function(output, params) {
        var choices = [];
        $.each(params.selected_values, function(idx, val) {
            var choice = params.el.find('.eboywp-checkbox[data-value="' + val + '"]').clone();
            choice.find('.eboywp-counter').remove();
            choice.find('.eboywp-expand').remove();
            choices.push({
                value: val,
                label: choice.text()
            });
        });
        return choices;
    });

    $(document).on('click', '.eboywp-type-checkboxes .eboywp-expand', function(e) {
        var $wrap = $(this).parent('.eboywp-checkbox').next('.eboywp-depth');
        $wrap.toggleClass('visible');
        var content = $wrap.hasClass('visible') ? EWP_JSON['collapse'] : EWP_JSON['expand'];
        $(this).text(content);
        e.stopPropagation();
    });

    $(document).on('click', '.eboywp-type-checkboxes .eboywp-checkbox:not(.disabled)', function() {
        $(this).toggleClass('checked');
        EWP.autoload();
    });

    $(document).on('click', '.eboywp-type-checkboxes .eboywp-toggle', function() {
        var $parent = $(this).closest('.eboywp-facet');
        $parent.find('.eboywp-toggle').toggleClass('eboywp-hidden');
        $parent.find('.eboywp-overflow').toggleClass('eboywp-hidden');
    });

    $(document).on('eboywp-loaded', function() {
        $('.eboywp-type-checkboxes .eboywp-overflow').each(function() {
            var num = $(this).find('.eboywp-checkbox').length;
            var $el = $(this).siblings('.eboywp-toggle:first');
            $el.text($el.text().replace('{num}', num));
        });

        // are children visible?
        $('.eboywp-type-checkboxes').each(function() {
            var $facet = $(this);
            var name = $facet.attr('data-name');

            // error handling
            if (Object.keys(EWP.settings).length < 1) {
                return;
            }

            // hierarchy toggles
            if ('yes' === EWP.settings[name]['show_expanded']) {
                $facet.find('.eboywp-depth').addClass('visible');
            }

            if (1 > $facet.find('.eboywp-expand').length) {
                $facet.find('.eboywp-depth').each(function() {
                    var which = $(this).hasClass('visible') ? 'collapse' : 'expand';
                    $(this).prev('.eboywp-checkbox').append(' <span class="eboywp-expand">' + EWP_JSON[which] + '</span>');
                });

                // un-hide groups with selected items
                $facet.find('.eboywp-checkbox.checked').each(function() {
                    $(this).parents('.eboywp-depth').each(function() {
                        $(this).prev('.eboywp-checkbox').find('.eboywp-expand').text(EWP_JSON['collapse']);
                        $(this).addClass('visible');
                    });

                    // show children of selected items
                    $(this).find('.eboywp-expand').trigger('click');
                });
            }
        });
    });

    /* ======== Radio ======== */

    wp.hooks.addAction('eboywp/refresh/radio', function($this, facet_name) {
        var selected_values = [];
        $this.find('.eboywp-radio.checked').each(function() {
            selected_values.push($(this).attr('data-value'));
        });
        EWP.facets[facet_name] = selected_values;
    });

    wp.hooks.addFilter('eboywp/selections/radio', function(output, params) {
        var choices = [];
        $.each(params.selected_values, function(idx, val) {
            var choice = params.el.find('.eboywp-radio[data-value="' + val + '"]').clone();
            choice.find('.eboywp-counter').remove();
            choices.push({
                value: val,
                label: choice.text()
            });
        });
        return choices;
    });

    $(document).on('click', '.eboywp-type-radio .eboywp-radio:not(.disabled)', function() {
        var is_checked = $(this).hasClass('checked');
        $(this).closest('.eboywp-facet').find('.eboywp-radio').removeClass('checked');
        if (! is_checked) {
            $(this).addClass('checked');
        }
        EWP.autoload();
    });

    /* ======== Date Range ======== */

    wp.hooks.addAction('eboywp/refresh/date_range', function($this, facet_name) {
        var min = $this.find('.eboywp-date-min').pVal() || '';
        var max = $this.find('.eboywp-date-max').pVal() || '';
        EWP.facets[facet_name] = ('' !== min || '' !== max) ? [min, max] : [];
    });

    wp.hooks.addFilter('eboywp/selections/date_range', function(output, params) {
        var vals = params.selected_values;
        var $el = params.el;
        var out = '';

        if ('' !== vals[0]) {
            out += ' from ' + $el.find('.eboywp-date-min').next().val();
        }
        if ('' !== vals[1]) {
            out += ' to ' + $el.find('.eboywp-date-max').next().val();
        }
        return out;
    });

    $(document).on('eboywp-loaded', function() {
        var $dates = $('.eboywp-type-date_range .eboywp-date:not(".ready, .flatpickr-alt")');

        if (0 === $dates.length) {
            return;
        }

        var flatpickr_opts = {
            altInput: true,
            altInputClass: 'flatpickr-alt',
            altFormat: 'Y-m-d',
            disableMobile: true,
            locale: EWP_JSON.datepicker.locale,
            onChange: function() {
                EWP.autoload();
            },
            onReady: function(dateObj, dateStr, instance) {
                var clearBtn = '<div class="flatpickr-clear">' + EWP_JSON.datepicker.clearText + '</div>';
                $(clearBtn).on('click', function() {
                        instance.clear();
                        instance.close();
                })
                .appendTo($(instance.calendarContainer));
            }
        };

        $dates.each(function() {
            var $this = $(this);
            var facet_name = $this.closest('.eboywp-facet').attr('data-name');
            flatpickr_opts.altFormat = EWP.settings[facet_name].format;

            var opts = wp.hooks.applyFilters('eboywp/set_options/date_range', flatpickr_opts, {
                'facet_name': facet_name,
                'element': $this
            });
            new flatpickr(this, opts);
            $this.addClass('ready');
        });
    });

    /* ======== Dropdown ======== */

    wp.hooks.addAction('eboywp/refresh/dropdown', function($this, facet_name) {
        var val = $this.find('.eboywp-dropdown').val();
        EWP.facets[facet_name] = val ? [val] : [];
    });

    wp.hooks.addFilter('eboywp/selections/dropdown', function(output, params) {
        return params.el.find('.eboywp-dropdown option:selected').text();
    });

    $(document).on('change', '.eboywp-type-dropdown select', function() {
        var $facet = $(this).closest('.eboywp-facet');
        var facet_name = $facet.attr('data-name');

        if ('' !== $facet.find(':selected').val()) {
            EWP.frozen_facets[facet_name] = 'soft';
        }
        EWP.autoload();
    });

    /* ======== fSelect ======== */

    wp.hooks.addAction('eboywp/refresh/fselect', function($this, facet_name) {
        var val = $this.find('select').val();
        if (null === val || '' === val) {
            val = [];
        }
        else if (false === $.isArray(val)) {
            val = [val];
        }
        EWP.facets[facet_name] = val;
    });

    wp.hooks.addFilter('eboywp/selections/fselect', function(output, params) {
        var choices = [];
        $.each(params.selected_values, function(idx, val) {
            var choice = params.el.find('.eboywp-dropdown option[value="' + val + '"]').text();
            choices.push({
                value: val,
                label: choice.replace(/{{(.*?)}}/, '')
            });
        });
        return choices;
    });

    $(document).on('eboywp-loaded', function() {
        $('.eboywp-type-fselect select:not(.ready)').each(function() {
            var facet_name = $(this).closest('.eboywp-facet').attr('data-name');
            var settings = EWP.settings[facet_name];
            var opts = wp.hooks.applyFilters('eboywp/set_options/fselect', {
                placeholder: settings.placeholder,
                overflowText: settings.overflowText,
                searchText: settings.searchText,
                optionFormatter: function(row) {
                    row = row.replace(/{{/g, '<span class="eboywp-counter">');
                    row = row.replace(/}}/g, '<span>');
                    return row;
                }
            }, { 'facet_name': facet_name });

            $(this).fSelect(opts);
            $(this).addClass('ready');
        });

        // unfreeze choices
        $('.fs-wrap.fs-disabled').removeClass('fs-disabled');
    });

    $(document).on('fs:changed', function(e, wrap) {
        if (wrap.classList.contains('multiple')) {
            var facet_name = $(wrap).closest('.eboywp-facet').attr('data-name');

            if ('or' === EWP.settings[facet_name]['operator']) {
                EWP.frozen_facets[facet_name] = 'soft';

                // freeze choices
                if (EWP.auto_refresh) {
                    $(wrap).addClass('fs-disabled');
                }
            }

            EWP.autoload();
        }
    });

    $(document).on('fs:closed', function(e, wrap) {
        if (! wrap.classList.contains('multiple')) {
            EWP.autoload();
        }
    });

    /* ======== Hierarchy ======== */

    wp.hooks.addAction('eboywp/refresh/hierarchy', function($this, facet_name) {
        var selected_values = [];
        $this.find('.eboywp-link.checked').each(function() {
            selected_values.push($(this).attr('data-value'));
        });
        EWP.facets[facet_name] = selected_values;
    });

    wp.hooks.addFilter('eboywp/selections/hierarchy', function(output, params) {
        return params.el.find('.eboywp-link.checked').text();
    });

    $(document).on('click', '.eboywp-facet .eboywp-link', function() {
        $(this).closest('.eboywp-facet').find('.eboywp-link').removeClass('checked');
        if ('' !== $(this).attr('data-value')) {
            $(this).addClass('checked');
        }
        EWP.autoload();
    });

    $(document).on('click', '.eboywp-type-hierarchy .eboywp-toggle', function() {
        var $parent = $(this).closest('.eboywp-facet');
        $parent.find('.eboywp-toggle').toggleClass('eboywp-hidden');
        $parent.find('.eboywp-overflow').toggleClass('eboywp-hidden');
    });

    /* ======== Number Range ======== */

    wp.hooks.addAction('eboywp/refresh/number_range', function($this, facet_name) {
        var min = $this.find('.eboywp-number-min').val() || '';
        var max = $this.find('.eboywp-number-max').val() || '';
        EWP.facets[facet_name] = ('' !== min || '' !== max) ? [min, max] : [];
    });

    wp.hooks.addFilter('eboywp/selections/number_range', function(output, params) {
        return params.selected_values[0] + ' - ' + params.selected_values[1];
    });

    $(document).on('click', '.eboywp-type-number_range .eboywp-submit', function() {
        EWP.refresh();
    });

    /* ======== Proximity ======== */

    $(document).on('eboywp-loaded', function() {
        var $locations = $('.eboywp-location');

        if ($locations.length < 1) {
            return;
        }

        $locations.each(function(idx, el) {
            var $input = $(this);

            if ($input.closest('.input-group').length < 1) {

                // Select the first choice
                (function pacSelectFirst(input) {
                    var _addEventListener = input.addEventListener;

                    function addEventListenerWrapper(type, listener) {
                        if ('keydown' === type) {
                            var orig_listener = listener;
                            listener = function(event) {
                                if (13 === event.which && 0 === $('.pac-container .pac-item-selected').length) {
                                    var simulated_downarrow = $.Event('keydown', {keyCode: 40, which: 40});
                                    orig_listener.apply(input, [simulated_downarrow]); // down arrow
                                }
                                orig_listener.apply(input, [event]); // original event
                            }
                        }
                        _addEventListener.apply(input, [type, listener]);
                    }
                    input.addEventListener = addEventListenerWrapper;

                    var options = EWP_JSON['proximity']['autocomplete_options'];
                    var autocomplete = new google.maps.places.Autocomplete(input, options);

                    google.maps.event.addListener(autocomplete, 'place_changed', function() {
                        var place = autocomplete.getPlace();
                        if ('undefined' !== typeof place.geometry) {
                            var $facet = $(input).closest('.eboywp-facet');
                            $facet.find('.eboywp-lat').val(place.geometry.location.lat());
                            $facet.find('.eboywp-lng').val(place.geometry.location.lng());
                            EWP.autoload();
                        }
                    });
                })($input[0]);

                // Preserve CSS IDs
                if (0 === idx) {
                    $input.attr('id', 'eboywp-location');
                    $input.closest('.eboywp-facet').find('.eboywp-radius').attr('id', 'eboywp-radius');
                }

                // Add the "Locate me" icon
                $input.wrap(' <div class="eboywp-location input-group"></div>');
                $input.after('<i class="locate-me"></i>');
            }

            $input.trigger('keyup');
        });
    });

    $(document).on('click', '.eboywp-type-proximity .locate-me', function(e) {
        var $this = $(this);
        var $facet = $this.closest('.eboywp-facet');
        var $input = $facet.find('.eboywp-location');
        var $lat = $facet.find('.eboywp-lat');
        var $lng = $facet.find('.eboywp-lng');

        // reset
        if ($this.hasClass('f-reset')) {
            $lat.val('');
            $lat.val('');
            $input.val('');
            EWP.autoload();
            return;
        }

        // loading icon
        $this.addClass('f-loading');

        // HTML5 geolocation
        navigator.geolocation.getCurrentPosition(function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;

            $lat.val(lat);
            $lng.val(lng);

            var geocoder = new google.maps.Geocoder();
            var latlng = {lat: parseFloat(lat), lng: parseFloat(lng)};
            geocoder.geocode({'location': latlng}, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    $input.val(results[0].formatted_address);
                }
                else {
                    $input.val('Your location');
                }
                $this.addClass('f-reset');
                EWP.autoload();
            });

            $this.removeClass('f-loading');
        },
        function() {
            $this.removeClass('f-loading');
        });
    });

    $(document).on('keyup', '.eboywp-location', function() {
        var $facet = $(this).closest('.eboywp-facet');
        $facet.find('.locate-me').toggleClass('f-reset', ('' !== $(this).val()));
    });

    $(document).on('change', '.eboywp-radius', function() {
        var $facet = $(this).closest('.eboywp-facet');
        if ('' !== $facet.find('.eboywp-location').val()) {
            EWP.autoload();
        }
    });

    $(document).on('input', '.eboywp-radius-slider', function(e) {
        var $facet = $(this).closest('.eboywp-facet');
        $facet.find('.eboywp-radius-dist').text(e.target.value);
    });

    wp.hooks.addAction('eboywp/refresh/proximity', function($this, facet_name) {
        var lat = $this.find('.eboywp-lat').val();
        var lng = $this.find('.eboywp-lng').val();
        var radius = $this.find('.eboywp-radius').val();
        var location = encodeURIComponent($this.find('.eboywp-location').val());
        EWP.frozen_facets[facet_name] = 'hard';
        EWP.facets[facet_name] = ('' !== lat && 'undefined' !== typeof lat) ?
            [lat, lng, radius, location] : [];
    });

    wp.hooks.addFilter('eboywp/selections/proximity', function(label, params) {
        return EWP_JSON['proximity']['clearText'];
    });

    /* ======== Search ======== */

    EWP.logic.search = {
        set_button: function($facet) {
            var val = $facet.find('.eboywp-search').val();
            $facet.find('.eboywp-btn').toggleClass('f-reset', ('' !== val));
        },
        delay_refresh: EWP.helper.debounce(function(facet_name) {
            EWP.frozen_facets[facet_name] = 'soft';
            EWP.autoload();
        }, 250)
    };

    wp.hooks.addAction('eboywp/refresh/search', function($this, facet_name) {
        var val = $this.find('.eboywp-search').val() || '';
        EWP.facets[facet_name] = val;
    });

    $(document).on('eboywp-loaded', function() {
        $('.eboywp-facet .eboywp-search').each(function() {
            var $facet = $(this).closest('.eboywp-facet');
            EWP.logic.search['set_button']($facet);
        });
    });

    $(document).on('keyup', '.eboywp-facet .eboywp-search', function(e) {
        var $facet = $(this).closest('.eboywp-facet');
        var facet_name = $facet.attr('data-name');

        EWP.logic.search['set_button']($facet);

        if ('undefined' !== typeof EWP.settings[facet_name]) {
            if ('yes' === EWP.settings[facet_name]['auto_refresh']) {
                EWP.logic.search['delay_refresh'](facet_name);
            }
            else if (13 === e.keyCode) {
                if ('' === $facet.find('.eboywp-search').val()) {
                    $facet.find('.eboywp-btn').click();
                }
                else {
                    EWP.autoload();
                }
            }
        }
    });

    $(document).on('click', '.eboywp-type-search .eboywp-btn', function(e) {
        var $this = $(this);
        var $facet = $this.closest('.eboywp-facet');
        var facet_name = $facet.attr('data-name');

        if ($this.hasClass('f-reset') || '' === $facet.find('.eboywp-search').val()) {
            $facet.find('.eboywp-search').val('');
            EWP.facets[facet_name] = [];
            EWP.set_hash();
            EWP.fetch_data();
        }
    });

    /* ======== Slider ======== */

    wp.hooks.addAction('eboywp/refresh/slider', function($this, facet_name) {
        EWP.facets[facet_name] = [];

        // settings have already been loaded
        if ('undefined' !== typeof EWP.frozen_facets[facet_name]) {
            if ('undefined' !== typeof $this.find('.eboywp-slider')[0].noUiSlider) {
                EWP.facets[facet_name] = $this.find('.eboywp-slider')[0].noUiSlider.get();
            }
        }
    });

    wp.hooks.addAction('eboywp/set_label/slider', function($this) {
        var facet_name = $this.attr('data-name');
        var min = EWP.settings[facet_name]['lower'];
        var max = EWP.settings[facet_name]['upper'];
        var format = EWP.settings[facet_name]['format'];
        var opts = {
            decimal_separator: EWP.settings[facet_name]['decimal_separator'],
            thousands_separator: EWP.settings[facet_name]['thousands_separator']
        };

        if ( min === max ) {
            var label = EWP.settings[facet_name]['prefix']
                + nummy(min).format(format, opts)
                + EWP.settings[facet_name]['suffix'];
        }
        else {
            var label = EWP.settings[facet_name]['prefix']
                + nummy(min).format(format, opts)
                + EWP.settings[facet_name]['suffix']
                + ' &mdash; '
                + EWP.settings[facet_name]['prefix']
                + nummy(max).format(format, opts)
                + EWP.settings[facet_name]['suffix'];
        }
        $this.find('.eboywp-slider-label').html(label);
    });

    wp.hooks.addFilter('eboywp/selections/slider', function(output, params) {
        return params.el.find('.eboywp-slider-label').text();
    });

    $(document).on('eboywp-loaded', function() {
        $('.eboywp-slider:not(.ready)').each(function() {
            var $parent = $(this).closest('.eboywp-facet');
            var facet_name = $parent.attr('data-name');
            var opts = EWP.settings[facet_name];

            // on first load, check for slider URL variable
            if (false !== EWP.helper.get_url_var(facet_name)) {
                EWP.frozen_facets[facet_name] = 'hard';
            }

            // fail on slider already initialized
            if ('undefined' !== typeof $(this).data('options')) {
                return;
            }

            // fail if start values are null
            if (null === EWP.settings[facet_name].start[0]) {
                return;
            }

            // fail on invalid ranges
            if (parseFloat(opts.range.min) >= parseFloat(opts.range.max)) {
                EWP.settings[facet_name]['lower'] = opts.range.min;
                EWP.settings[facet_name]['upper'] = opts.range.max;
                wp.hooks.doAction('eboywp/set_label/slider', $parent);
                return;
            }

            // custom slider options
            var slider_opts = wp.hooks.applyFilters('eboywp/set_options/slider', {
                range: opts.range,
                start: opts.start,
                step: parseFloat(opts.step),
                connect: true
            }, { 'facet_name': facet_name });


            var slider = $(this)[0];
            noUiSlider.create(slider, slider_opts);
            slider.noUiSlider.on('update', function(values, handle) {
                EWP.settings[facet_name]['lower'] = values[0];
                EWP.settings[facet_name]['upper'] = values[1];
                wp.hooks.doAction('eboywp/set_label/slider', $parent);
            });
            slider.noUiSlider.on('set', function() {
                EWP.frozen_facets[facet_name] = 'hard';
                EWP.autoload();
            });

            $(this).addClass('ready');
        });

        // hide reset buttons
        $('.eboywp-type-slider').each(function() {
            var name = $(this).attr('data-name');
            var $button = $(this).find('.eboywp-slider-reset');
            $.isEmptyObject(EWP.facets[name]) ? $button.hide() : $button.show();
        });
    });

    $(document).on('click', '.eboywp-slider-reset', function() {
        var facet_name = $(this).closest('.eboywp-facet').attr('data-name');
        EWP.reset(facet_name);
    });

})(jQuery);
