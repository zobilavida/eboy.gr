var EWPCL = EWPCL || {
    is_loading: false,
    action_el: null
};


(function($) {


    $(function() {
        EWPCL.load();

        // Topnav
        $(document).on('click', '.eboywp-tab', function() {
            var tab = $(this).attr('rel');
            $('.eboywp-tab').removeClass('active');
            $(this).addClass('active');
            $('.eboywp-region').removeClass('active');
            $('.eboywp-region-' + tab).addClass('active');

            // Populate the export code
            if ('settings' == tab) {
                var code = JSON.stringify(EWPCL.parse_data());
                $('.export-code').val(code);
            }
        });

        $('.export-code').on('focus', function() {
            $(this).select();
        });

        $(document).on('click', '.ruleset-label', function() {
            e.preventDefault();
        });

        $(document).on('click', '.ruleset .title', function() {
            $(this).closest('.ruleset').toggleClass('collapsed');
        });

        // Trigger click
        $('.eboywp-header-nav a:first').click();
    });


    EWPCL.load = function() {
        EWPCL.is_loading = true;

        $.each(EWPCL.rulesets, function(index, ruleset) {
            $('.add-ruleset').click();

            // Set the ruleset props
            $('.eboywp-region-rulesets .ruleset:last .ruleset-label').text(ruleset.label);
            $('.eboywp-region-rulesets .ruleset:last .ruleset-on').val(ruleset.on);

            // Set the ations
            $.each(ruleset.actions, function(index, action) {
                $('.eboywp-region-rulesets .action-and:last').click();

                var $last = $('.eboywp-region-rulesets .action:last');

                // Add <option> if needed
                if ($last.find('.action-object option[value="' + action.object + '"]').length < 1) {
                    $last.find('.action-object').append('<option value="' + action.object + '">' + action.object + '</option>');
                }

                $last.find('.action-toggle').val(action.toggle);
                $last.find('.action-object').val(action.object).trigger('change');
                $last.find('.action-selector').val(action.selector);
            });

            // Set the conditions
            $.each(ruleset.conditions, function(index, cond_group) {
                $('.eboywp-region-rulesets .condition-and:last').click();

                $.each(cond_group, function(index, cond) {

                    // Skip first item ("AND")
                    if (0 < index) {
                        $('.eboywp-region-rulesets .condition-or:last').click();
                    }

                    var $last = $('.eboywp-region-rulesets .condition:last');

                    // Add <option> if needed
                    if ($last.find('.condition-object option[value="' + cond.object + '"]').length < 1) {
                        $last.find('.condition-object').append('<option value="' + cond.object + '">' + cond.object + '</option>');
                    }

                    $last.find('.condition-object').val(cond.object).trigger('change');
                    $last.find('.condition-compare').val(cond.compare);
                    $last.find('.condition-value').val(cond.value);
                });
            });
        });

        EWPCL.is_loading = false;
    }


    EWPCL.parse_data = function() {
        var rules = [];

        $('.eboywp-region-rulesets .ruleset').each(function(rule_num) {
            rules[rule_num] = {
                'label': $(this).find('.ruleset-label').text(),
                'on': $(this).find('.ruleset-on').val(),
                'conditions': [],
                'actions': []
            };

            // Get conditions (and preserve groups)
            $(this).find('.condition-group').each(function(group_num) {
                var conditions = [];

                $(this).find('.condition').each(function() {
                    var condition = {
                        'object': $(this).find('.condition-object').val(),
                        'compare': $(this).find('.condition-compare').val(),
                        'value': $(this).find('.condition-value').val()
                    };
                    conditions.push(condition);
                });

                rules[rule_num]['conditions'][group_num] = conditions;
            });

            // Get actions
            $(this).find('.action').each(function() {
                var action = {
                    'toggle': $(this).find('.action-toggle').val(),
                    'object': $(this).find('.action-object').val(),
                    'selector': $(this).find('.action-selector').val()
                };

                rules[rule_num]['actions'].push(action);
            });
        });

        return rules;
    }


    $(document).on('change', '.condition-object', function() {
        var $condition = $(this).closest('.condition');
        var val = $(this).val() || '';

        $condition.find('.condition-value').show();
        $condition.find('.condition-compare').show();
        var is_template = ( 'template-' == val.substr(0, 9));
        if ('eboys-empty' == val || 'eboys-not-empty' == val || is_template) {
            $condition.find('.condition-compare').hide();
            $condition.find('.condition-value').hide();
        }
    });


    $(document).on('change', '.action-object', function() {
        if ('custom' == $(this).val()) {
            $(this).closest('.action').find('.action-selector-btn').show();
        }
        else {
            $(this).closest('.action').find('.action-selector-btn').hide();
        }
    });


    $(document).on('click', '.eboywp-save', function() {
        $('.ewpcl-response').removeClass('dashicons-yes');
        $('.ewpcl-response').addClass('dashicons-image-rotate')
        $('.ewpcl-response').show();

        var data = EWPCL.parse_data();

        $.post(ajaxurl, {
            'action': 'ewpcl_save',
            'data': JSON.stringify(data)
        }, function(response) {
            $('.ewpcl-response').removeClass('dashicons-image-rotate');
            $('.ewpcl-response').addClass('dashicons-yes');
            setTimeout(function() {
                $('.ewpcl-response').stop().fadeOut();
            }, 4000);
        });
    });


    $(document).on('click', '.add-ruleset', function() {
        var $clone = $('.clone').clone();
        var $rule = $clone.find('.clone-ruleset');

        // Collapse ruleset on pageload
        if (EWPCL.is_loading) {
            $rule.find('.ruleset').addClass('collapsed');
        }

        $('.eboywp-region-rulesets .eboywp-content-wrap').append($rule.html());
        $('.eboywp-region-rulesets .eboywp-content-wrap').sortable({
            axis: 'y',
            items: '.ruleset',
            placeholder: 'sortable-placeholder',
            handle: '.toggle'
        });
    });


    $(document).on('click', '.condition-or', function() {
        var $clone = $('.clone-condition').clone();
        $clone.find('.condition').addClass('type-or');
        $clone.find('.condition .type').text('OR');
        $clone.find('.condition .btn').html('');
        $(this).closest('.condition-group').append($clone.html());
        $(this).closest('.condition-group').find('.condition:last .condition-object').trigger('change');
    });


    $(document).on('click', '.condition-and', function() {
        var $clone = $('.clone-condition').clone();
        var $ruleset = $(this).closest('.conditions-col');

        // Set the type label
        $clone.find('.condition .type').text('AND');

        // Create rule group
        $ruleset.find('.condition-wrap').append('<div class="condition-group" />');
        var $group = $ruleset.find('.condition-group:last');
        $group.append($clone.html());
        $group.find('.condition-object').trigger('change');

        // The first label should be "IF"
        $(this).closest('.conditions-col').find('.condition:first .type').text('IF');
    });


    $(document).on('click', '.condition-drop', function() {
        var $wrap = $(this).closest('.condition-wrap');
        var $cond = $(this).closest('.condition');
        var index = $(this).closest('.condition-group').find('.condition').index($cond);
        var siblings = $cond.siblings().length;

        // Remove group if it's the first or only item
        if (0 === siblings || 0 === index) {
            $(this).closest('.condition-group').remove(); // remove group
        }
        else {
            $(this).closest('.condition').remove(); // remove condition
        }

        // The first label should be "IF"
        $wrap.find('.condition:first .type').text('IF');
    });


    $(document).on('click', '.header-bar td.delete', function() {
        if (confirm('Delete this ruleset?')) {
            $(this).closest('.ruleset').remove();
        }
    });


    $(document).on('click', '.action-and', function() {
        var html = $('.clone-action').html();
        var $wrap = $(this).siblings('.action-wrap');

        $wrap.append(html);
        $wrap.find('.action:first .type').text('THEN');
        $wrap.find('.action:last .action-object').trigger('change');
    });


    $(document).on('click', '.action-drop', function() {
        var $wrap = $(this).closest('.action-wrap');
        $(this).closest('.action').remove();
        $wrap.find('.action:first .type').text('THEN');
    });


    $(document).on('click', '.action-selector-btn', function() {
        EWPCL.action_el = $(this).closest('.action');
        var val = EWPCL.action_el.find('.action-selector').val();
        $('.action-selector-input').val(val);
        $('.media-modal').show();
        $('.media-modal-backdrop').show();
    });


    $(document).on('click', '.selector-save', function() {
        var val = $('.action-selector-input').val();
        EWPCL.action_el.find('.action-selector').val(val);
        $('.media-modal-close').trigger('click');
    });


    $(document).on('click', '.media-modal-close', function() {
        $('.media-modal').hide();
        $('.media-modal-backdrop').hide();
    });


    $(document).on('click', '.ewpcl-import', function() {
        $('.ewpcl-import-response').html('Importing...');
        $.post(ajaxurl, {
            action: 'ewpcl_import',
            import_code: $('.import-code').val(),
        },
        function(response) {
            $('.ewpcl-import-response').html(response);
            setTimeout(function() {
                window.location.reload();
            }, 1500);
        });
    });

})(jQuery);
