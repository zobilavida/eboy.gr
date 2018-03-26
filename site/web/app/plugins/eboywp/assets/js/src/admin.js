window.EWP = {
    is_indexing: false,
    is_name_editable: false
};

(function($) {
    $(function() {

        var row_count = 0;

        EWP.load_settings = function() {

            // Settings load hook
            EWP.settings = wp.hooks.applyFilters('eboywp/load_settings', EWP.settings);

            $.each(EWP.settings.facets, function(idx, obj) {
                var $row = $('.clone-facet .eboywp-row').clone();
                $row.attr('data-id', row_count);
                $row.attr('data-type', obj.type);
                $row.find('.facet-fields').html(EWP.clone[obj.type]);
                $row.find('.facet-label').val(obj.label);
                $row.find('.facet-name').text(obj.name);
                $row.find('.facet-type').val(obj.type);

                // Facet load hook
                wp.hooks.doAction('eboywp/load/' + obj.type, $row, obj);

                // UI for code-based facets
                if ('undefined' !== typeof obj['_code']) {
                    $row.addClass('in-code');
                }

                $('.eboywp-content').append($row);
                $('.content-facets .eboywp-cards').append(EWP.build_card({
                    card: 'facet',
                    id: row_count,
                    label: obj.label,
                    name: obj.name,
                    type: obj.type
                }));
                row_count++;
            });

            $.each(EWP.settings.templates, function(idx, obj) {
                var $row = $('.clone-template .eboywp-row').clone();
                $row.attr('data-id', row_count);
                $row.find('.template-label').val(obj.label);
                $row.find('.template-name').text(obj.name);
                $row.find('.template-query').val(obj.query);
                $row.find('.template-template').val(obj.template);

                // UI for code-based templates
                if ('undefined' !== typeof obj['_code']) {
                    $row.addClass('in-code');
                }

                $('.eboywp-content').append($row);
                $('.content-templates .eboywp-cards').append(EWP.build_card({
                    card: 'template',
                    id: row_count,
                    label: obj.label,
                    name: obj.name
                }));
                row_count++;
            });

            $.each(EWP.settings.settings, function(key, val) {
                var $this = $('.eboywp-setting[data-name=' + key + ']');
                $this.val(val);
            });

            // Initialize the Query Builder
            $('.qb-area').queryBuilder({
                post_types: EWP.builder.post_types,
                taxonomies: EWP.builder.taxonomies,
                refresh: function(el) {
                    var json = JSON.stringify(el.data('query_args'), null, 2);
                    json = "<?php\nreturn " + json + ';';
                    json = json.replace(/[\{\(\[]/g, 'array(');
                    json = json.replace(/[\}\]]/g, ')');
                    json = json.replace(/:/g, ' =>');
                    $('.qb-results').val(json);
                }
            });

            // Initialize fSelect
            $('.qb-post-type').fSelect({
                placeholder: EWP.i18n['All post types']
            });

            $('.export-items').fSelect({
                placeholder: EWP.i18n['Select some items']
            });

            // Sortable
            $('.eboywp-cards').sortable({
                handle: '.card-label'
            });

            // Hide the preloader
            $('.eboywp-loading').addClass('hidden');
            $('.eboywp-header-nav a:first').click();
            $('.eboywp-region-settings .eboywp-subnav a:first').click();
        }


        EWP.build_card = function(params) {
            var output = '<li data-id="' + params.id + '">';
            output += '<div class="eboywp-card">';
            output += '<div class="card-delete"></div>';
            output += '<div class="card-label">' + params.label + '</div>';
            if ('facet' === params.card) {
                output += '<div class="card-type">' + params.type + '</div>';
            }
            output += '</div>';
            output += '</li>';
            return output;
        }


        // Is the indexer running?
        EWP.get_progress = function() {
            $.post(ajaxurl, {
                action: 'eboywp_heartbeat',
                nonce: EWP.nonce
            }, function(response) {

                // Remove extra spaces added by some themes
                var response = response.trim();

                if ('-1' == response) {
                    $('.eboywp-response').html(EWP.i18n['Indexing complete']);
                    EWP.is_indexing = false;
                }
                else if ($.isNumeric(response)) {
                    $('.eboywp-response').html(EWP.i18n['Indexing'] + '... ' + response + '%');
                    $('.eboywp-response').addClass('visible');
                    setTimeout(function() {
                        EWP.get_progress();
                    }, 5000);
                }
                else {
                    $('.eboywp-response').html(response);
                    EWP.is_indexing = false;
                }
            });
        }
        EWP.get_progress();


        // Topnav
        $(document).on('click', '.eboywp-tab', function() {
            var tab = $(this).attr('rel');
            $('.eboywp-tab').removeClass('active');
            $(this).addClass('active');
            $('.eboywp-region').removeClass('active');
            $('.eboywp-region-' + tab).addClass('active');
        });


        // Conditionals based on facet type
        $(document).on('change', '.facet-type', function() {
            var val = $(this).val();
            var $facet = $(this).closest('.eboywp-row');
            $facet.find('.eboywp-show').show();

            if (val !== $facet.attr('data-type')) {
                $facet.find('.facet-fields').html(EWP.clone[val]);
                $facet.attr('data-type', val);
            }

            wp.hooks.doAction('eboywp/change/' + val, $(this));

            // Update the card
            var id = $facet.attr('data-id');
            $('.eboywp-cards li[data-id="'+ id +'"] .card-type').text(val);

            // Trigger .facet-source
            $facet.find('.facet-source').trigger('change');
        });


        // Conditionals based on facet source
        $(document).on('change', '.facet-source', function() {
            var val = $(this).val();
            var $facet = $(this).closest('.eboywp-row');
            var facet_type = $facet.find('.facet-type').val();
            var display = ('string' === typeof val && -1 < val.indexOf('tax/')) ? 'table-row' : 'none';

            if ('checkboxes' === facet_type || 'dropdown' === facet_type) {
                $facet.find('.facet-parent-term').closest('tr').css({ 'display' : display });
                $facet.find('.facet-hierarchical').closest('tr').css({ 'display' : display });
            }
            else if ('fselect' === facet_type || 'radio' === facet_type) {
                $facet.find('.facet-parent-term').closest('tr').css({ 'display' : display });
            }
        });


        // Conditionals based on facet source_other
        $(document).on('change', '.facet-source-other', function() {
            var $facet = $(this).closest('.eboywp-row');
            var display = ('' !== $(this).val()) ? 'table-row' : 'none';
            $facet.find('.facet-compare-type').closest('tr').css({ 'display' : display });
        });


        // Add item
        $(document).on('click', '.eboywp-add', function() {
            var $parent = $(this).closest('.eboywp-col');
            var type = $parent.hasClass('content-facets') ? 'facet' : 'template';
            var label = ('facet' === type) ? 'New facet' : 'New template';
            var name = ('facet' === type) ? 'new_facet' : 'new_template';

            var $row = $('.clone-' + type + ' .eboywp-row').clone();
            $row.attr('data-id', row_count);

            $('.eboywp-content').append($row);
            $parent.find('.eboywp-cards').append(EWP.build_card({
                card: type,
                id: row_count,
                label: label,
                name: name,
                type: 'checkboxes'
            }));

            // Simulate a click
            $parent.find('.eboywp-cards li:last .eboywp-card').trigger('click');

            row_count++;
        });


        // Remove item
        $(document).on('click', '.card-delete', function(e) {
            if (confirm(EWP.i18n['Are you sure?'])) {
                var id = $(this).closest('li').attr('data-id');
                $(this).closest('.eboywp-region').find('.eboywp-content .eboywp-row[data-id="' + id + '"]').remove();
                $(this).closest('li').remove();
            }
            e.stopPropagation();
        });


        // Edit item
        $(document).on('click', '.eboywp-card', function(e) {
            var $this = $(this);
            var id = $this.closest('li').attr('data-id');
            var $el = $('.eboywp-row[data-id="' + id + '"]');

            $('.eboywp-grid').addClass('hidden');
            $('.eboywp-region-basics .eboywp-subnav .btn-wrap').removeClass('hidden');
            $('.eboywp-region-basics .eboywp-subnav .search-wrap').addClass('hidden');
            $el.addClass('visible');

            // Trigger facet conditionals
            if ($this.closest('.eboywp-col').hasClass('content-facets')) {
                $el.find('.facet-type').trigger('change');
                $el.find('.facet-source').fSelect();
            }

            // Scroll to top
            $('html, body').animate({ scrollTop: 0 }, 'fast');

            // Set the active row
            EWP.active_row = id;
        });


        // Back button
        $(document).on('click', '.eboywp-back', function() {
            $('.eboywp-grid').removeClass('hidden');
            $('.eboywp-row.visible').removeClass('visible');
            $(this).closest('.btn-wrap').addClass('hidden');
            $(this).closest('.eboywp-subnav').find('.search-wrap').removeClass('hidden');
        });


        // Focus on the label
        $(document).on('focus', '.facet-label, .template-label', function() {
            var type = $(this).hasClass('facet-label') ? 'facet' : 'template';
            var name_val = $(this).siblings('.' + type + '-name').text();
            EWP.is_name_editable = ('' === name_val || ('new_' + type) === name_val);
        });


        // Change the label
        $(document).on('keyup', '.facet-label, .template-label', function() {
            var label = $(this).val();
            var type = $(this).hasClass('facet-label') ? 'facet' : 'template';
            var $row = $(this).closest('.eboywp-row');
            var id = $row.attr('data-id');

            if (EWP.is_name_editable) {
                var val = $.trim(label).toLowerCase();
                val = val.replace(/[^\w- ]/g, ''); // strip invalid characters
                val = val.replace(/[- ]/g, '_'); // replace space and hyphen with underscore
                val = val.replace(/[_]{2,}/g, '_'); // strip consecutive underscores

                // Update the input field
                $(this).siblings('.' + type + '-name').text(val);
            }

            // Edit the card
            $('.eboywp-cards li[data-id="'+ id +'"] .card-label').text(label);
        });


        // Open modal window
        $(document).on('click', '.open-builder', function() {
            $('.media-modal').show();
            $('.media-modal-backdrop').show();
        });


        // Send Query Builder arguments to the active editor
        $(document).on('click', '.qb-send', function() {
            var args = $('.modal-content-wrap').find('.qb-results').val();
            $('.eboywp-row[data-id="' + EWP.active_row + '"] .template-query').val(args);
            $('.media-modal-close').trigger('click');
        });


        // Close modal window
        $(document).on('click', '.media-modal-close', function() {
            $('.media-modal').hide();
            $('.media-modal-backdrop').hide();
        });


        // Copy to clipboard
        $(document).on('click', '.copy-shortcode', function() {
            var $this = $(this);
            var orig_text = $this.text();
            var $el = $('.eboywp-clipboard');
            var name = $(this).closest('.eboywp-row').find('.facet-name').text();

            try {
                $el.removeClass('hidden');
                $el.val('[eboywp facet="' + name + '"]');
                $el.select();
                document.execCommand('copy');
                $el.addClass('hidden');
                $this.text('Copied!');
            }
            catch(err) {
                $this.text('Press CTRL+C to copy');
            }

            window.setTimeout(function() {
                $this.text(orig_text);
            }, 2000);
        });


        // Code unlock
        $(document).on('click', '.code-unlock .unlock', function() {
            $(this).closest('.eboywp-row').removeClass('in-code');
        });


        // Tab click
        $(document).on('click', '.eboywp-region-settings .eboywp-subnav a', function() {
            var tab = $(this).attr('data-tab');
            $('.eboywp-region-settings .eboywp-subnav a').removeClass('active');
            $('.eboywp-settings-section').removeClass('active');
            $('.eboywp-region-settings .eboywp-subnav a[data-tab=' + tab + ']').addClass('active');
            $('.eboywp-settings-section[data-tab=' + tab + ']').addClass('active');
        });


        // Save
        $(document).on('click', '.eboywp-save', function() {
            $('.eboywp-response').html(EWP.i18n['Saving'] + '...');
            $('.eboywp-response').addClass('visible');

            var data = {
                'facets': [],
                'templates': [],
                'settings': {}
            };

            // Loop through cards, looking up the content
            $('.eboywp-cards li').each(function() {
                var $this = $('.eboywp-row[data-id="' + $(this).data('id') + '"]');

                if ($this.hasClass('in-code')) {
                    return;
                }

                // Facet
                if ($this.is('[data-type]')) {
                    var obj = {
                        'label': $this.find('.facet-label').val(),
                        'name': $this.find('.facet-name').text(),
                        'type': $this.find('.facet-type').val()
                    };

                    // Argument order changed in 3.0.0
                    try {
                        obj = wp.hooks.applyFilters('eboywp/save/' + obj.type, obj, $this);
                    }
                    catch(err) {
                        obj = wp.hooks.applyFilters('eboywp/save/' + obj.type, $this, obj);
                    }

                    data.facets.push(obj);
                }
                // Template
                else {
                    data.templates.push({
                        'label': $this.find('.template-label').val(),
                        'name': $this.find('.template-name').text(),
                        'query': $this.find('.template-query').val(),
                        'template': $this.find('.template-template').val()
                    });
                }
            });

            $('.eboywp-region-settings .eboywp-setting').each(function() {
                var name = $(this).attr('data-name');
                data.settings[name] = $(this).val();
            });

            // Settings save hook
            data = wp.hooks.applyFilters('eboywp/save_settings', data);

            $.post(ajaxurl, {
                action: 'eboywp_save',
                nonce: EWP.nonce,
                data: JSON.stringify(data)
            }, function(response) {
                $('.eboywp-response').html(response.message);
                $('.eboywp-rebuild').toggleClass('flux', response.reindex);
            }, 'json');
        });


        // Export
        $(document).on('click', '.export-submit', function() {
                $('.import-code').val(EWP.i18n['Loading'] + '...');
                $.post(ajaxurl, {
                    action: 'eboywp_backup',
                    nonce: EWP.nonce,
                    action_type: 'export',
                    items: $('.export-items').val()
                },
                function(response) {
                    $('.import-code').val(response);
                });
        });


        // Import
        $(document).on('click', '.import-submit', function() {
            $('.eboywp-response').addClass('visible');
            $('.eboywp-response').html(EWP.i18n['Importing'] + '...');
            $.post(ajaxurl, {
                action: 'eboywp_backup',
                nonce: EWP.nonce,
                action_type: 'import',
                import_code: $('.import-code').val(),
                overwrite: $('.import-overwrite').is(':checked') ? 1 : 0
            },
            function(response) {
                $('.eboywp-response').html(response);
                setTimeout(function() {
                    window.location.reload();
                }, 1500);
            });
        });


        // Rebuild index
        $(document).on('click', '.eboywp-rebuild', function() {
            $(this).removeClass('flux');

            if (EWP.is_indexing) {
                return;
            }

            EWP.is_indexing = true;

            $.post(ajaxurl, { action: 'eboywp_rebuild_index', nonce: EWP.nonce });
            $('.eboywp-response').html(EWP.i18n['Indexing'] + '...');
            $('.eboywp-response').addClass('visible');
            setTimeout(function() {
                EWP.get_progress();
            }, 5000);
        });


        // Activation
        $(document).on('click', '.eboywp-activate', function() {
            $('.eboywp-activation-status').html(EWP.i18n['Activating'] + '...');
            $.post(ajaxurl, {
                action: 'eboywp_license',
                nonce: EWP.nonce,
                license: $('.eboywp-license').val()
            }, function(response) {
                $('.eboywp-activation-status').html(response.message);
            }, 'json');
        });


        // Tooltips
        $(document).on('mouseover', '.eboywp-tooltip', function() {
            if ('undefined' === typeof $(this).data('powertip')) {
                var content = $(this).find('.eboywp-tooltip-content').html();
                $(this).data('powertip', content);
                $(this).powerTip({
                    placement: 'e',
                    mouseOnToPopup: true
                });
                $.powerTip.show(this);
            }
        });


        // Search
        $(document).on('keyup', '.eboywp-search', function() {
            var input = $(this).val().toLowerCase();

            if (input.length < 1) {
                $('.eboywp-cards li.hidden').removeClass('hidden');
            }
            else {
                $('.eboywp-card').each(function() {
                    var label = $(this).find('.card-label').text().toLowerCase();
                    var type = $(this).find('.card-type').text().toLowerCase();

                    if (-1 === label.indexOf(input) && -1 === type.indexOf(input)) {
                        $(this).closest('li').addClass('hidden');
                    }
                    else {
                        $(this).closest('li').removeClass('hidden');
                    }
                });
            }
        });


        // Initialize
        EWP.load_settings();
    });
})(jQuery);
