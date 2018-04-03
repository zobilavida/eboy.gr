(function($) {

    var facets_in_use = function() {
        var in_use = false;

        $.each(EWP.facets, function(name, val) {
            if (0 < val.length && 'paged' !== name) {
                in_use = true;
                return false; // exit loop
            }
        });

        return in_use;
    }

    var evaluate_condition = function(cond) {
        var is_valid = false;
        var compare_field;

        if ('facets-empty' == cond.object) {
            return false === facets_in_use();
        }
        else if ('facets-not-empty' == cond.object) {
            return true === facets_in_use();
        }
        else if ('uri' == cond.object) {
            compare_field = EWP_HTTP.uri;
        }
        else if ('total-rows' == cond.object) {
            if ('undefined' === typeof EWP.settings.pager) {
                return false;
            }
            compare_field = EWP.settings.pager.total_rows;
        }
        else if ('facet-' == cond.object.substr(0, 6)) {
            var facet_name = cond.object.substr(6);
            if ('undefined' === typeof EWP.facets[facet_name]) {
                return false;
            }
            compare_field = EWP.facets[facet_name];
        }
        else if ('template-' == cond.object.substr(0, 9)) {
            compare_field = EWP.template;
            cond.value = cond.object.substr(9);
        }

        // operators
        if ('is' == cond.compare) {
            if (is_intersect(cond.value, compare_field)) {
                is_valid = true;
            }
        }
        else if ('not' == cond.compare) {
            if (! is_intersect(cond.value, compare_field)) {
                is_valid = true;
            }
        }

        return is_valid;
    }

    var is_intersect = function(arr1, arr2) {

        // force arrays
        arr1 = [].concat(arr1);
        arr2 = [].concat(arr2);

        // exact match
        if (arr1.toString() === arr2.toString()) {
            return true;
        }

        var result = arr1.filter(function(n) {
            return arr2.indexOf(n) != -1;
        });

        return result.length > 0;
    }

    var do_action = function(action, is_valid) {
        var item;
        var is_custom = false;
        var animation = 'hide';

        if ('template' == action.object) {
            item = $('.eboywp-template');
        }
        else if ('facets' == action.object) {
            item = $('.eboywp-facet');
        }
        else if ('facet-' == action.object.substr(0, 6)) {
            item = $('.eboywp-facet-' + action.object.substr(6));
        }
        else if ('custom' == action.object) {
            is_custom = true;
            var lines = action.selector.split("\n");
            var selectors = [];
            for (var i = 0; i < lines.length; i++){
                var selector = lines[i].replace(/^\s+|\s+$/gm, '');
                if (selector.length) {
                    selectors.push(selector);
                }
            }
            item = selectors;
        }

        if (item.length < 1) {
            return;
        }

        if (('show' == action.toggle && is_valid) || ('hide' == action.toggle && ! is_valid)) {
            animation = 'show';
        }

        // toggle
        if (is_custom) {
            $.each(item, function(idx, selector) {
                if ('$EMPTY' == selector.substr(0, 6)) {
                    var tmp = { 'empty': [], 'nonempty': [] };
                    $.each(EWP.settings.num_choices, function(key, val) {
                        (0 === val) ?
                            tmp['empty'].push('.eboywp-facet-' + key) :
                            tmp['nonempty'].push('.eboywp-facet-' + key);
                    });

                    var $EMPTY = $(tmp['empty'].join(', '));
                    var $NONEMPTY = $(tmp['nonempty'].join(', '));
                    var opposite = selector.replace('$EMPTY', '$NONEMPTY');

                    if ('show' == animation) {
                        eval(selector + ".removeClass('is-hidden')");
                        eval(opposite + ".addClass('is-hidden')");
                    }
                    else {
                        eval(selector + ".addClass('is-hidden')");
                        eval(opposite + ".removeClass('is-hidden')");
                    }
                }
                else {
                    var which = ('show' == animation) ? '.removeClass' : '.addClass';
                    eval(selector + which + "('is-hidden')");
                }
            });
        }
        else {
            ('show' == animation) ? item.removeClass('is-hidden') : item.addClass('is-hidden');
        }
    }

    $(document).on('eboywp-refresh eboywp-loaded', function(e) {

        // each ruleset
        $.each(EWPCL.rulesets, function(idx, ruleset) {
            if ('refresh-loaded' != ruleset.on && e.type != 'eboywp-' + ruleset.on) {
                return; // skip iteration
            }

            // if no conditions, set to TRUE
            var this_result = (ruleset.conditions.length < 1);
            var result = [];

            // foreach condition group
            $.each(ruleset.conditions, function(idx_1, cond_group) {
                this_result = false;

                // foreach "OR" condition
                $.each(cond_group, function(idx_2, cond_or) {
                    if (evaluate_condition(cond_or)) {
                        this_result = true;
                        return false; // exit loop
                    }
                });

                result.push(this_result);
            });

            // make sure no conditions are false
            var is_valid = (result.indexOf(false) < 0);

            // apply actions
            $.each(ruleset.actions, function(idx_1, action) {
                do_action(action, is_valid);
            });
        });
    });
})(jQuery);