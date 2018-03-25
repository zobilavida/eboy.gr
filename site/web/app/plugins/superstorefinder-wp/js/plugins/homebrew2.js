/*!
 * 
 * Homebrewed plugin functions!
 *
 * Basically it's a bunch of common functions used in the websites we
 * build. They're built in the format of jQuery plugins for reusability.
 *
 * You may remove those that you don't need.
 *
 * - HC
 *
 * @TODO: Make these more extensible for easier future usage.
 */
var homebrew = {};
(function($) {
    /* Avoid `console` errors in browsers that lack a console. */
    var method,
        noop = function () {},
        methods = [
            'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
            'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
            'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
            'timeStamp', 'trace', 'warn'
        ],
        length = methods.length,
        console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }

    /* Setup homebrew object */
    var root = $('html');
    $.extend(homebrew, {
        browser : {
            ie      : root.hasClass('ie'),
            ie9     : root.hasClass('ie9'),
            lt9     : root.hasClass('lt9'),
            ie8     : root.hasClass('ie8'),
            lt8     : root.hasClass('lt8'),
            ie7     : root.hasClass('ie7'),
            firefox : (window.mozIndexedDB !== undefined)
        },

        events : {
            transitionEnd : 'oTransitionEnd otransitionend webkitTransitionEnd transitionend'
        },

        classes : {
            transitionable: 'is-transitionable'
        },

        screenSize : {
            small : false,
            medium : false,
            large : true
        },

        mediaQueries : {
            small : 'only screen and (max-width: 40em)',
            medium : 'only screen and (min-width: 40.063em)',
            large : 'only screen and (min-width: 64.063em)'
        },

        mediaQueriesIE9 : {
            small : { size : 640, method : 'max-width' },
            medium : { size : 641, method : 'min-width' },
            large : { size : 1025, method : 'min-width' }
        }
    });



    $.extend(homebrew, {
        utils : {
            /*
             * Executes a function a max of once every n milliseconds 
             * 
             * Arguments:
             *    Func (Function): Function to be throttled.
             * 
             *    Delay (Integer): Function execution threshold in milliseconds.
             * 
             * Returns:
             *    Lazy_function (Function): Function with throttling applied.
             */
            throttle : function(func, delay) {
                var timer = null;

                return function () {
                    var context = this,
                        args = arguments;

                    clearTimeout(timer);

                    timer = setTimeout(function () {
                        func.apply(context, args);
                    }, delay);
                };
            },
            /* End throttle */

            /*
             * Executes a function when it stops being invoked for n seconds
             * Modified version of _.debounce() http://underscorejs.org
             *
             * Arguments:
             *    Func (Function): Function to be debounced.
             *
             *    Delay (Integer): Function execution threshold in milliseconds.
             * 
             *    Immediate (Bool): Whether the function should be called at the beginning 
             *    of the delay instead of the end. Default is false.
             *
             * Returns:
             *    Lazy_function (Function): Function with debouncing applied.
             */
            debounce : function(func, delay, immediate) {
                var timeout, result;

                return function() {
                    var context = this,
                        args = arguments,
                        later = function() {
                          timeout = null;
                          if (!immediate) result = func.apply(context, args);
                        };

                    var callNow = immediate && !timeout;

                    clearTimeout(timeout);
                    timeout = setTimeout(later, delay);
                    if (callNow) result = func.apply(context, args);

                    return result;
                };
            }
            /* End debounce */
        },
        /* End utils */

        makePlugin : function(plugin) {
            var pluginName = plugin.prototype.name;

            $.fn[pluginName] = function(options) {
                var args = $.makeArray(arguments),
                    after = args.slice(1);

                return this.each(function() {
                    var instance = $.data(this, pluginName);

                    if(instance) {
                        if(typeof options === 'string') {
                            instance[options].apply(instance, after);
                        } else if(instance.update) {
                            instance.update.apply(instance, args);
                        }
                    } else {
                        new plugin(this, options);
                    }
                });
            };
        },
        /* End makePlugin */

        generateUniqueID : function() {
            return String(new Date().getTime()) + String(Math.round(Math.random()*100000, 10));
        },
        getSelectorsFromHTMLString : function(str) {
            if(typeof str !== 'string') {
                console.error('homebrew.getSelectorsFromHTMLString(): Expecting a string as the first argument. Please check:');
                console.log(str);
                return {};
            }

            var tagMatch = str.match(/<(.*?) /g),
                attributesMatch = str.match(/ (.*?)=("|')(.*?)("|')/g),
                selectorsObj = {};

            if(tagMatch) {
                selectorsObj.tag = tagMatch[0].replace(/(<| )/g, '');
            }

            if(attributesMatch) {
                var attributeSplitArray,
                    classesArray;

                while(attributesMatch.length) {
                    attributeSplitArray = $.trim(attributesMatch.shift()).split('=');

                    switch(attributeSplitArray[0]) {
                        case 'class' :
                            classesArray = attributeSplitArray[1].replace(/("|')/g, '').split(' ');

                            for(var i = classesArray.length-1; i > -1; i--) {
                                if(classesArray[i] === '') {
                                    classesArray.splice(i, 1);
                                }
                            }

                            selectorsObj.classes = classesArray;
                        break;

                        default :
                            selectorsObj[attributeSplitArray[0]] = attributeSplitArray[1].replace(/("|')/g, '');
                        break;
                    }
                }
            }

            return selectorsObj;
        },
        /* End getSelectorsFromHTMLString */

        getClassFromHTMLString : function(str) {
            var selectorsObj = this.getSelectorsFromHTMLString(str);

            if(selectorsObj.classes && selectorsObj.classes.length) {
                return selectorsObj.classes;
            } else {
                return [];
            }
        },

        getKeyValuePairsFromString : function(str, pairSeparator, keyValueSeparator) {
            pairSeparator = pairSeparator || ';';
            keyValueSeparator = keyValueSeparator || ':';
            var splitArray = str.split(pairSeparator),
                keyValuePairs = {},
                currentPair;
            while(splitArray.length) {
                currentPair = splitArray.shift();
                if(!currentPair) continue;
                currentPair = currentPair.split(keyValueSeparator);
                currentPair = currentPair.map(function(str) {
                    return $.trim(str);
                });
                if(currentPair[1] === 'true') {
                    currentPair[1] = true;
                } else if(currentPair[1] === 'false') {
                    currentPair[1] = false;
                }
                keyValuePairs[currentPair[0]] = currentPair[1];
            }
            return keyValuePairs;
        },
        watchSize : function(mediaQuery, callback) {
            var self = this,
                _mediaQuery = self.mediaQueries[mediaQuery];

            if(typeof _mediaQuery === 'undefined') {
                throw new Error('homebrew.watchSize(): No match media query. mediaQuery provided is ' + mediaQuery);
            }

            /* For modern browsers, use native matchMedia.addListener */
            if(typeof matchMedia === 'function' && matchMedia.addListener) {
                var matchMediaObj = matchMedia(_mediaQuery);

                callback(matchMediaObj.matches);

                matchMediaObj.addListener(function(mq) {
                    callback(mq.matches);
                });
            /* For IE9, simulate the matchMedia.addListener behaviour using
             * a resize handler. */
            } else if(!self.browser.lt9) {
                var mediaQueryProps = self.mediaQueriesIE9[mediaQuery];
                if(typeof mediaQueryProps === 'undefined') return;

                var currentScreen,
                    getCurrentScreen;

                if(mediaQueryProps.method === 'min-width') {
                    getCurrentScreen = function() {
                        return ($(window).width() >= mediaQueryProps.size);
                    };
                } else if(mediaQueryProps.method === 'max-width') {
                    getCurrentScreen = function() {
                        return ($(window).width() <= mediaQueryProps.size);
                    };
                }

                currentScreen = getCurrentScreen();

                callback(currentScreen);

                $(window).on('resize.watchMedia', self.utils.throttle(function() {
                    if(currentScreen !== getCurrentScreen()) {
                        currentScreen = !currentScreen;
                        callback(currentScreen);
                    }
                }, 100));
            /* For legacy browsers, only run the functions for medium
             * screens and above. */
            } else {
                if(mediaQuery === 'small' || mediaQuery === 'xsmall') {
                    callback(false);
                } else {
                    callback(true);
                }
            }
        }
        /* End watchSize */
    });

    homebrew.watchSize('small', function(isMediumScreen) {
        homebrew.screenSize.small = isMediumScreen;
        homebrew.screenSize.medium = !isMediumScreen;
    });

    homebrew.watchSize('large', function(isLargeScreen) {
        homebrew.screenSize.medium = !isLargeScreen;
        homebrew.screenSize.large = isLargeScreen;
    });




    /**---- Carouselify ---**\
     *  Turn a list of elements into a rotating carousel.
     *
     *  Arguments:
     *      $('.carousel').carouselify({
     *          items : '.carousel-item',
     *          activeItem : null,
     *          classes : {
     *              active : 'is-active',
     *              hidden : 'is-hidden'
     *          },
     *          loop : true,
     *          transitions : {
     *              enable : true,
     *              classes : {
     *                  transitionIn : 'is-transitioning-in',
     *                  transitionOut : 'is-transitioning-out',
     *                  reverse : 'is-reverse'
     *              },
     *              onStart : null,
     *              onEnd : null
     *          },
     *          switchers : {
     *              enable : true,
     *              markups : {
     *                  nextSwitcher : '<div class="carousel__switcher carousel__switcher--next" />',
     *                  prevSwitcher : '<div class="carousel__switcher carousel__switcher--prev" />'
     *              }
     *          },
     *          pagers : {
     *              enable : true,
     *              markup : '<a href="#/" class="carousel__pager" />',
     *              holder : '<div class="carousel__pagers" />'
     *          },
     *          timer : {
     *              enable : true,
     *              duration : 10000,
     *              showBar : true,
     *              barMarkup : '<div class="carousel__timer-bar" />'
     *          },
     *          onSwitch : null
     *      });
     *
     *  - items
     *      |-- Type: String
     *      |-- Default: '.carousel-item'
     *      |-- Pass in the selector of the carousel items.
     *  - activeItem
     *      |-- Type: Number
     *      |-- Default: null
     *      |-- Pass in the index of the carousel item that should be active
     *          when the plugin is initialised.
     *  - classes
     *      |-- Type: Object
     *      |-- Pass in an object containing strings of the classes to use.
     *          Properties in this object:
     *          - active
     *              |-- Type: String
     *              |-- Default: 'is-active'
     *              |-- The class that will be added onto the active item.
     *          - hidden
     *              |-- Type: String
     *              |-- Default: 'is-hidden'
     *              |-- The class that will be added onto the switcher
     *                  buttons when an unloopable carousel is at the first
     *                  item and the last item. (At the first time, the
     *                  prevSwitcher button will receive this class, while
     *                  at the last item, the nextSwitcher button will
     *                  receive it.)
     *  - loop
     *      |-- Type: Boolean
     *      |-- Default: true
     *      |-- Allows the carousel to loop back to the first item after it
     *          reaches the last item and vice versa.
     *  - transitions
     *      |-- Type: Object
     *      |-- Pass in an arguments object. Arguments in this object are:
     *          - enable
     *              |-- Type: Boolean
     *              |-- Default: true
     *              |-- Pass in `true` to have the plugin attempt to use
     *                  CSS transitions by toggling the respective classes
     *                  on the items and leveraging the `transitionEnd`
     *                  event.
     *          - classes
     *              |-- Type: Object
     *              |-- Pass in an object containing strings of the classes
     *                  to use. Properties in this object:
     *                  - transitionIn:
     *                      |-- Type: String
     *                      |-- Default: 'is-transitioning-in'
     *                      |-- The class that will be added onto the next
     *                          active item to trigger the transition.
     *                  - transitionOut:
     *                      |-- Type: String
     *                      |-- Default: 'is-transitioning-out'
     *                      |-- The class that will be added onto the current
     *                          active item to trigger the transition.
     *                  - reverse:
     *                      |-- Type: String
     *                      |-- Default: 'is-reverse'
     *                      |-- The class that will be added onto both the
     *                          current and the next active item when the
     *                          carousel is triggered in the opposite direction.
     *                          This is useful to trigger a reverse transition.
     *          - onStart
     *              |-- Type: Function
     *              |-- Default: null
     *              |-- Pass in a callback function to be executed after the
     *                  plugin adds the general homebrew transition class but
     *                  before the plugin adds the transitionIn class to the
     *                  target item. The function will receive two arguments:
     *                  (1) the jQuery object of the item that the carousel
     *                      is switching to, and
     *                  (2) the jQuery object of the item that the carousel
     *                      is switching away from.
     *          - onEnd
     *              |-- Type: Function
     *              |-- Default: null
     *              |-- Pass in a callback function to be executed after the
     *                  transition completes and the plugin removes all
     *                  transition-related classes from the items. The
     *                  function will receive two arguments:
     *                  (1) the jQuery object of the item that the carousel
     *                      is switching to, and
     *                  (2) the jQuery object of the item that the carousel
     *                      is switching away from.
     *  - switchers
     *      |-- Type: Object
     *      |-- Pass in an arguments object. Arguments in this object are:
     *          - enable
     *              |-- Type: Boolean
     *              |-- Default: true
     *              |-- Option to have the plugin use switcher buttons.
     *                  Switcher buttons make the carousel rotate between the
     *                  next and previous items.
     *          - markups
     *              |-- Type: Object
     *              |-- Pass in an object containing strings of the markups
     *                  to use. These markup strings have TWO uses:
     *                  (1) the plugin will extract the classes in the markup
     *                      string and use it to find any pre-existing switcher
     *                      buttons in the carousel; if no pre-existing
     *                      switcher buttons are found, then...
     *                  (2) the plugin will create its own switcher buttons
     *                      using the provided markup.
     *              |-- Properties in this object:
     *                  - nextSwitcher:
     *                      |-- Type: String
     *                      |-- Default: ''<div class="carousel-switcher carousel-switcher--next" />'
     *                      |-- The markup to be used for the switcher that
     *                          rotates the carousel to the next item.
     *                  - prevSwitcher:
     *                      |-- Type: String
     *                      |-- Default: ''<div class="carousel-switcher carousel-switcher--prev" />'
     *                      |-- The markup to be used for the switcher that
     *                          rotates the carousel to the previous item.
     *  - pagers
     *      |-- Type: Object
     *      |-- Pass in an arguments object. Arguments in this object are:
     *          - enable
     *              |-- Type: Boolean
     *              |-- Default: true
     *              |-- Option to have the plugin use pager buttons.
     *                  Pager buttons make the carousel rotate directly to the
     *                  corresponding item.
     *          - markup
     *              |-- Type: String
     *              |-- Default: '<div class="carousel__pager" />'
     *              |-- Pass in the markup string to be used for pagers.
     *              |-- Markup strings have TWO uses:
     *                  (1) the plugin will extract the classes in the markup
     *                      string and use it to find any pre-existing pager
     *                      buttons in the carousel; if no pre-existing
     *                      pager buttons are found, then...
     *                  (2) the plugin will create its own pager buttons
     *                      using the provided markup.
     *          - holder
     *              |-- Type: String
     *              |-- Default: '<div class="carousel__pagers" />'
     *              |-- Pass in the markup string to use for the holder of the
     *                  holders. If a holder is used, then the plugin will look
     *                  for pagers within the provided holders. 
     *              |-- Pass in `null` or an empty string to refrain from using
     *                  a holder. If a holder isn't used, then the plugin will
     *                  search for pagers within the carousel element.
     *              |-- Markup strings have TWO uses:
     *                  (1) the plugin will extract the classes in the markup
     *                      string and use it to find any pre-existing pager
     *                      buttons in the carousel; if no pre-existing
     *                      pager buttons are found, then...
     *                  (2) the plugin will create its own pager buttons
     *                      using the provided markup.
     *  - timer
     *      |-- Type: Object
     *      |-- Pass in an arguments object. Arguments in this object are:
     *          - enable
     *              |-- Type: Boolean
     *              |-- Default: true
     *              |-- Option to have the plugin automatically rotate through
     *                  the items based on a timer.
     *          - duration
     *              |-- Type: Number
     *              |-- Default: 10000
     *              |-- Determines the delay between each rotation. A larger
     *                  number would cause the carousel to rotate less often.
     *  - onSwitch
     *      |-- Type: Function
     *      |-- Default: null
     *      |-- Pass in a callback function to be executed right before the
     *          carousel switches between items. The function will receive two
     *          arguments:
     *          (1) the jQuery object of the item that the carousel is
     *              switching to, and
     *          (2) the jQuery object of the item that the carousel is
     *              switching away from.
     */
    homebrew.Carousel = function(el, args) {
        if(!el) return;
        this.init(el, args);
    };

    $.extend(homebrew.Carousel.prototype, {
        name : 'carouselify',

        options : {
            items : '.carousel-item',

            activeItem : null,

            classes : {
                active : 'is-active',
                hidden : 'is-hidden'
            },

            loop : true,

            transitions : {
                enable : true,
                classes : {
                    transitionIn : 'is-transitioning-in',
                    transitionOut : 'is-transitioning-out',
                    reverse : 'is-reverse'
                },
                onStart : null,
                onEnd : null
            },

            switchers : {
                enable : true,
                markups : {
                    nextSwitcher : '<div class="carousel__switcher carousel__switcher--next" />',
                    prevSwitcher : '<div class="carousel__switcher carousel__switcher--prev" />'
                }
            },

            pagers : {
                enable : true,
                markup : '<a href="#/" class="carousel__pager" />',
                holder : '<div class="carousel__pagers" />'
            },

            timer : {
                enable : true,
                duration : 10000,
                showBar : true,
                barMarkup : '<div class="carousel__timer-bar" />'
            },

            onSwitch : null
        },

        init : function(el, args) {
            var instance = this,
                options = $.extend(true, {}, instance.options, args);

            if(typeof options.items !== 'string') {
                console.error('$.fn.carouselify: Expecting String type from `items` argument. Please check:');
                console.log(options.items);
                return;
            }

            var $el = $(el),
                $items = $el.find(options.items);

            if(!$items.length) return;

            instance.$el = $el;
            instance.$items = $items;
            instance.totalItems = $items.length;
            instance.options = options;

            if(typeof options.activeItem === 'number') {
                $items.eq(instance.activeItem).addClass(options.classes.active);
                instance.activeItem = options.activeItem;
            } else if(instance.$items.filter(options.classes.active).length) {
                instance.activeItem = instance.$items.filter(options.classes.active).index();
            } else {
                instance.activeItem = 0;
            }

            if(options.switchers.enable) {
                var $nextSwitcher,
                    nextSwitcherClasses = homebrew.getClassFromHTMLString(options.switchers.markups.nextSwitcher);

                if(nextSwitcherClasses.length) {
                    $nextSwitcher = $el.find('.' + nextSwitcherClasses.join('.'));
                }
                if(!$nextSwitcher || !$nextSwitcher.length) {
                    $nextSwitcher = $(options.switchers.markups.nextSwitcher);
                    $el.append($prevSwitcher);
                    instance.addDestroyable($nextSwitcher);
                }

                instance.$nextSwitcher = $nextSwitcher;

                var $prevSwitcher,
                    prevSwitcherClasses = homebrew.getClassFromHTMLString(options.switchers.markups.prevSwitcher);

                if(prevSwitcherClasses.length) {
                    $prevSwitcher = $el.find('.' + prevSwitcherClasses.join('.'));
                }
                if(!$prevSwitcher || !$prevSwitcher.length) {
                    $prevSwitcher = $(options.switchers.markups.prevSwitcher);
                    $el.prepend($prevSwitcher);
                    instance.addDestroyable($prevSwitcher);
                }

                instance.$prevSwitcher = $prevSwitcher;
            }

            if(options.pagers.enable) {
                var $holders,
                    $pagers,
                    pagerClasses = homebrew.getClassFromHTMLString(options.pagers.markup);

                if(typeof options.pagers.holder === 'string'
                && options.pagers.holder !== '') {
                    var holderClasses = homebrew.getClassFromHTMLString(options.pagers.holder);

                    if(holderClasses.length) {
                        $holders = $el.find('.' + holderClasses.join('.'));
                    }
                    if(!$holders || !$holders.length) {
                        $holders = $(options.pagers.holder).appendTo($el);
                    }
                }

                if(!$holders || !$holders.length) {
                    $holders = $el;
                }

                if(pagerClasses.length) {
                    $pagers = $holders.find('.' + pagerClasses.join('.'));
                }

                if(!$pagers || !$pagers.length) {
                    var finalPagersStr = [];
                    for(var i = instance.totalItems-1; i > -1; i--) {
                        finalPagersStr.push(options.pagers.markup);
                    }
                    $pagers = $(finalPagersStr.join('')).appendTo($holders);
                    instance.addDestroyable($pagers);
                }

                $pagers.eq(instance.activeItem).addClass(options.classes.active);
                instance.$pagers = $pagers;
            }

            if(options.timer.enable && options.showBar) {
                var $timerBar,
                    timerBarClasses = homebrew.getClassFromHTMLString(options.timer.barMarkup);

                if(timerBarClasses.length) {
                    $timerBar = $el.find('.' + timerBarClasses.join('.'));
                }
                if(!$timerBar || !$timerBar.length) {
                    $timerBar = $(options.timer.barMarkup);
                    instance.addDestroyable($timerBar);
                }

                instance.$timerBar = $timerBar;

                if(typeof options.timer.duration !== 'number') {
                    console.error('$.fn.carouselify(): Expecting a number for the timer duration. Please check:');
                    console.log(options.timer.duration);
                    console.error('Reverting back to default.');

                    options.timer.duration = homebrew.Carousel.prototype.options.timer.duration;
                }
            }

            instance.enable();

            $.data(el, instance.name, instance);

            return instance;
        },

        enable : function() {
            var instance = this;

            if(instance.$nextSwitcher && instance.$prevSwitcher) {
                instance.$nextSwitcher.on('click.' + instance.name, function() {
                    instance.switchTo('next');
                });

                instance.$prevSwitcher.on('click.' + instance.name, function() {
                    instance.switchTo('prev');
                });
            }

            if(instance.$pagers) {
                instance.$pagers.each(function(index) {
                    $(this).on('click.' + instance.name, function(e) {
                        e.preventDefault();
                        instance.switchTo(index);
                    });
                });
            }

            instance.runTimer();

            if(instance.options.timer.enable) {
                $(window)
                    .on('focus.' + instance.name, function() {
                        instance.runTimer();
                    })
                    .on('blur.' + instance.name, function() {
                        clearTimeout(instance.timer);
                    });
            }

            return instance;
        },

        disable : function() {
            var instance = this;

            clearTimeout(instance.timer);

            if(instance.$nextSwitcher && instance.$prevSwitcher) {
                instance.$nextSwitcher.add(instance.$prevSwitcher).off('click.'  + instance.name);
            }

            if(instance.$pagers) {
                instance.$pagers.off('click.' + instance.name);
            }

            return instance;
        },

        switchTo : function(itemIndex) {
            var instance = this;
            if(instance.isSwitching === true) return instance;
            instance.isSwitching = true;
            var activeItem = instance.activeItem,
                options = instance.options;

            if(typeof itemIndex === 'string') {
                switch(itemIndex) {
                    case 'next' :
                        activeItem++;

                        if(activeItem >= instance.totalItems) {
                            activeItem = 0;
                        }
                    break;

                    case 'prev' :
                        activeItem--;

                        if(activeItem < 0) {
                            activeItem = instance.totalItems - 1;
                        }
                    break;

                    default:
                        console.error('Homebrew.Carousel.switchTo(): Unrecognised string method `' + itemIndex + '`.');
                        return;
                    break;
                }
            } else if(typeof itemIndex === 'number') {
                activeItem = itemIndex;
            } else {
                console.error('Homebrew.Carousel.switchTo(): Unsupported argument type: `' + typeof itemIndex + '`.');
                console.log(itemIndex);
                return;
            }

            if(activeItem === instance.activeItem) return instance;

            var $currentItem = instance.$items.eq(activeItem),
                $prevItem = instance.$items.eq(instance.activeItem),
                activeClass = options.classes.active;

            if(typeof options.onSwitch === 'function') {
                options.onSwitch.call(instance.$el[0], $currentItem, $prevItem);
            }

            instance.runTimer();

            if(options.transitions.enable) {
                var transitionEvent = homebrew.events.transitionEnd,
                    transitionClass = homebrew.classes.transitionable,
                    transitionInClass = options.transitions.classes.transitionIn,
                    transitionOutClass = options.transitions.classes.transitionOut,
                    reverseClass = options.transitions.classes.reverse;

                instance.$items.removeClass([
                    reverseClass,
                    transitionClass
                ].join(' '));

                instance.$items.not($prevItem).removeClass(activeClass);

                $currentItem
                    .one(transitionEvent, function() {
                        $currentItem
                            .off(transitionEvent)
                            .add($prevItem)
                                .removeClass([
                                    transitionInClass,
                                    transitionOutClass,
                                    reverseClass,
                                    transitionClass,
                                    activeClass
                                ].join(' '))
                            .end()
                            .addClass(activeClass);

                        instance.isSwitching = false;
                        if(typeof options.transitions.onEnd === 'function') {
                            options.transitions.onEnd.call(instance.$el[0], $currentItem, $prevItem);
                        }
                    });

                if(itemIndex === 'prev' && instance.activeItem === 0 && activeItem === instance.totalItems-1
                || itemIndex !== 'next' && instance.activeItem > activeItem) {
                    $prevItem.add($currentItem).addClass(reverseClass);
                }

                setTimeout(function() {
                    $prevItem.add($currentItem).addClass(transitionClass);
                    $prevItem.addClass(transitionOutClass);
                    $currentItem.addClass(transitionInClass);

                    if(typeof options.transitions.onStart === 'function') {
                        options.transitions.onStart.call(instance.$el[0], $currentItem, $prevItem);
                    }
                }, 10);
            } else {
                instance.$items.removeClass(activeClass);
                $currentItem.addClass(activeClass);
                instance.isSwitching = false;
            }

            instance.$pagers.removeClass(activeClass)
                            .eq(activeItem)
                                .addClass(activeClass);

            instance.activeItem = activeItem;

            return instance;
        },

        runTimer : function() {
            var instance = this,
                options = instance.options;

            if(!options.timer.enable) return instance;

            clearTimeout(instance.timer);

            if(options.loop || activeItem < instance.totalItems-1) {
                instance.timer = setTimeout(function() {
                    instance.switchTo('next');
                }, options.timer.duration);
            }

            return instance;
        },

        addDestroyable : function($obj) {
            var instance = this;
            if(!instance.destroyables) instance.destroyables = [];
            instance.destroyables.push($obj);
            return instance;
        },

        destroy : function() {
            var instance = this;

            instance.disable();

            if(instance.destroyable) {
                while(instance.destroyable.length) {
                    instance.destoryable.shift().remove();
                }
            }

            $.removeData(instance.$el[0], instance.name);
        }
    });

    homebrew.makePlugin(homebrew.Carousel);


    /**---- Height Syncer ----**\
     *  Sync the height of a collection of items.
     *
     *  Arguments:
     *      $('.items-holder').heightSyncify({
     *          items : [
     *              $('.items-holder').find('.item')
     *          ]
     *      });
     *
     *  - items
     *      |-- Type: Array
     *      |-- Pass in an Array of the items to sync. The Array can consist
     *          of either selector strings or jQuery Objects. If selector
     *          strings are provided, the plugin will try to select the targets
     *          within the currently iterated element. The sequence determines
     *          which set of items' height get synced first. This is important
     *          if you need to sync two items that are of parent-child relation
     *          (you would most likely want to sync the children's height first
     *          so that they also count towards the parents' height)
     *
     *  Methods:
     *  - init
     *      |-- $('.items-holder').heightSyncify();
     *      |-- Initialise the plugin. Once the plugin is initialised, you can
     *          pass in a string to trigger a specific method on it.
     *      |-- Accepts an arguments object. Refer to the above for the list
     *          of arguments available.
     *  - update
     *      |-- $('.items-holder').heightSyncify();
     *      |-- Init the plugin again to update its options.
     *      |-- Accepts an arguments object. Refer to the above for the list
     *          of arguments available.
     *  - enable
     *      |-- $('.items-holder').heightSyncify('enable');
     *      |-- Automatically re-sync the height when the window is resized.
     *  - disable
     *      |-- $('.items-holder').heightSyncify('disable');
     *      |-- Stop re-syncing the height when the window is resized.
     *  - sync
     *      |-- $('.items-holder').heightSyncify('sync');
     *      |-- Sync the items height.
     *  - destroy
     *      |-- $('.items-holder').heightSyncify('destroy');
     *      |-- Removes the plugin.
     */
    homebrew.HeightSyncer = function(el, args) {
        if(!el) return;
        this.init(el, args);
    };
    $.extend(homebrew.HeightSyncer.prototype, {
        name : 'heightSyncify',

        init : function(el, args) {
            args = args || {};
            var instance = this;
            instance.$el = $(el);
            instance.uniqueID = homebrew.generateUniqueID();
            instance.update(args)
                    .enable();
            $.data(el, instance.name, instance);
            return instance;
        },
        update : function(args) {
            args = args || {};
            var instance = this;
            instance.items = args.items.slice(0);
            for(var i = instance.items.length-1; i > -1; i--) {
                if(typeof instance.items[i] === 'string') {
                    instance.items[i] = instance.$el.find(instance.items[i]);
                }
                instance.items[i].find('img').each(function() {
                    if(this.complete) return;
                    $(this).one('load', function() {
                        clearTimeout(instance.timer);
                        instance.timer = setTimeout(function() {
                            instance.sync();
                        }, 100);
                    });
                });
            }
            if(args.options) {
                $.extend(instance, args.options);
            }
            instance.sync();
            return instance;
        },
        enable : function() {
            var instance = this;
            if(instance.enabled) return instance;
            instance.enabled = true;
            $(window).on('resize.' + instance.uniqueID, homebrew.utils.throttle(function() {
                instance.sync();
            }, 30));
            return instance;
        },
        disable : function() {
            this.enabled = false;
            $(window).off('.' + this.uniqueID);
            return this;
        },
        sync : function() {
            var instance = this,
                $currentCollection,
                $items = $(),
                leftOffset,
                currentLeftThreshold,
                heights,
                tallestHeight;

            if(!instance.items || !instance.items.length) return instance;

                for(var i = 0, ii = instance.items.length; i < ii; i++) {
                    leftOffset = currentLeftThreshold = -9999;
                $currentCollection = instance.items[i];

                $currentCollection.each(function(index) {
                    $items = $items.add($(this));
                        leftOffset = $(this).offset().left;
                    if(!$currentCollection.eq(index+1).length 
                    || $currentCollection.eq(index+1).offset().left <= leftOffset) {
                        heights = [];
                        $items.css('height', '');

                        $items.each(function() {
                            heights.push($(this).outerHeight());
                    });

                        tallestHeight = Math.max.apply(null, heights);

                        $items.outerHeight(tallestHeight);
                        $items = $();
                        }
                    });
                }
                
            instance.$el.trigger('afterSync');

            return instance;
        },
        destroy : function() {
            var instance = this;
            while(instance.items.length) {
                instance.items.shift().css('height', '');
            }
            instance.disable();
            $.removeData(instance.$el[0], instance.name);
        }
    });
    homebrew.makePlugin(homebrew.HeightSyncer);
    /**---- Tooltipify ---**\
     *  Initialise a custom tooltip on the element.
     *
     *  Arguments:
     *      $('.my-element').tooltipify({
     *          appendTo : 'body',
     *          classes : {
     *              active : 'is-active'
     *          },
     *          markups : {
     *              tooltip : '<div class="tooltip" />'
     *          },
     *          contents : function() {
     *              var instance = this,
     *                  title = instance.$el.attr('title');
     *
     *              instance.$el.data(instance.name + '-title', title);
     *              instance.$el.removeAttr('title');
     *
     *              return title;
     *          },
     *          transitions : {
     *              enable : true,
     *              classes : {
     *                  transitionIn : 'is-transitioning-in',
     *                  transitionOut : 'is-transitioning-out'
     *              }
     *          },
     *          hoverDuration : 400
     *      });
     *
     *  - appendTo
     *      |-- Type: Object | String
     *      |-- Default: 'body'
     *      |-- Determines where the tooltip will be appended to when it is
     *          is created to be shown.
     *      |-- You can pass in either a string, a Node element or a jQuery
     *          object. If what you passed in results in nothing being selected,
     *          the plugin will fallback to appending to the <body> element.
     *  - classes
     *      |-- An Object that contains strings of general classes to be used
     *          in the plugin. The classes are:
     *          - active
     *              |-- Default: 'is-active'
     *              |-- The class that will be added to the tooltip after it is
     *                  created and shown.
     *  - markups
     *      |-- An Object that contains strings of markups to be used in
     *          the plugin. The markups are:
     *          - tooltip
     *              |-- Default: '<div class="tooltip" />'
     *              |-- The markup used to contain the tooltip.
     *  - contents
     *      |-- Type: Function | String
     *      |-- Default: function() {
     *              var instance = this,
     *                  title = instance.$el.attr('title');
     *
     *              instance.$el.data(instance.name + '-title', title);
     *              instance.$el.removeAttr('title');
     *
     *              return title;
     *          }
     *      |-- Determines the content of the tooltip. There are two ways
     *          to set the content:
     *          (1) Use a function that returns the content string when it is
     *              run. This is useful if the element itself has a title
     *              attribute, as you can use this function to save the value
     *              and then proceed to remove the attribute (to prevent the
     *              default tooltip),
     *              OR
     *          (2) Directly pass in the content string itself.
     *      |-- The content string is inserted using the `.html()` method.
     *  - hoverDuration
     *      |-- Type: Number
     *      |-- Default: 400
     *      |-- Determines how long the mouse needs to hover over the element
     *          in order to trigger the tooltip. Lower number means a shorter
     *          duration to trigger.
     *  - transitions
     *      |-- An Object that contains the various properties to be used
     *          in the plugin. The properties are:
     *          - enable
     *              |-- Type: Boolean
     *              |-- Default: true
     *              |-- Determines whether or not the plugin should attempt
     *                  to leverage CSS transitions. 
     *          - classes
     *              |-- An Object that contains strings of transition classes
     *                  to be used in the plugin. The classes are:
     *                  - transition
     *                      |-- Default: homebrew.classes.transition
     *                      |-- This class is used to enable the transition
     *                          effect on the element.
     *                  - transitionIn
     *                      |-- Default: 'is-transitioning-in'
     *                      |-- This class is used to make the tooltip
     *                          transition in.
     *                  - transitionOut
     *                      |-- Default: 'is-transitioning-out'
     *                      |-- This class is used to make the tooltip
     *                          transition out.
     */
    homebrew.Tooltip = function(el, args) {
        if(!el) return;
        this.init(el, args);
    };
    $.extend(homebrew.Tooltip.prototype, {
        name : 'tooltipify',
        options : {
            appendTo : 'body',
            markups : {
                tooltip : '<div class="tooltip" />',
                closer : '<a href="#/" class="tooltip__closer" />'
            },
            classes : {
                active : 'is-active'
            },
            contents : function() {
                var instance = this,
                    title = instance.$el.attr('title');
                instance.$el.data(instance.name + '-title', title);
                instance.$el.removeAttr('title');
                return title;
            },
            hoverDuration : 400,
            transitions : {
                enable : true,
                classes : {
                    transition : homebrew.classes.transitionable,
                    transitionIn : 'is-transitioning-in',
                    transitionOut : 'is-transitioning-out'
                }
            }
        },
        init : function(el, args) {
            var instance = this,
                options = $.extend({}, instance.options, args);
            instance.$el = $(el).addClass(instance.name);
            instance.uniqueID = homebrew.generateUniqueID();
            instance.options = options;
            instance.$appendTo = $(options.appendTo);
            if(!instance.$appendTo.length) {
                instance.$appendTo = 'body';
            }
            if(typeof options.contents === 'function') {
                options.contents = options.contents.call(instance);
            }
            instance.enable();
            $.data(el, instance.name, instance);
        },
        enable : function() {
            var instance = this,
                $el = instance.$el,
                options = instance.options;
            $el.on('click.' + instance.uniqueID, function(e) {
                e.preventDefault();
                clearTimeout(instance.timer);
                instance.open();
            });
            $el.on('mouseenter.' + instance.uniqueID, function(e) {
                clearTimeout(instance.timer);
                instance.timer = setTimeout(function() {
                    instance.open();
                }, options.hoverDuration);
            });
            $el.on('mouseleave.' + instance.uniqueID, function(e) {
                clearTimeout(instance.timer);
                instance.timer = setTimeout(function() {
                    instance.close();
                }, options.hoverDuration);
            });
            return instance;
        },
        disable : function() {
            this.$el.off('.' + instance.uniqueID);
            return this;
        },
        getAltRender : function() {
            return Modernizr.touch && homebrew.screenSize.small;
        },
        open : function() {
            var instance = this;
            if(instance.$tooltip) return;
            var $el = instance.$el,
                options = instance.options,
                $tooltip = $(options.markups.tooltip).appendTo(instance.$appendTo).html(options.contents),
                activeClass = options.classes.active,
                altRender = instance.getAltRender();
            instance.$tooltip = $tooltip;
            if(altRender) {
                var $closer = $('.' + homebrew.getClassFromHTMLString(options.markups.closer).join('.'));
                if(!$closer.length) {
                    $closer = $(options.markups.closer).prependTo($tooltip);
                }
                $closer.on('click', function(e) {
                    e.preventDefault();
                    instance.close();
                });
            } else {
                if(homebrew.screenSize.small) {
                    if($tooltip.outerWidth() + parseInt($tooltip.css('margin-left'), 10) === $(window).width()) {
                        $tooltip.css({
                            left : '0px',
                            marginLeft : '0px'
                        });
                    } else if($el.offset().left + $tooltip.outerWidth() > $(window).width()) {
                        $tooltip.css('right', '0px');
                    } else {
                        $tooltip
                            .css('left', $el.offset().left + 'px');
                    }
                } else {
                    if($el.offset().left + $tooltip.outerWidth() > $(window).width()) {
                        $tooltip
                            .addClass('is-opposite')
                            .css('right', '0px');
                    } else {
                        $tooltip
                            .css('left', $el.offset().left + 'px');
                    }
                }
                $tooltip.css('top', $el.offset().top - $('#mainContent').offset().top - $tooltip.outerHeight() + 'px')
            }
            if(options.transitions.enable) {
                var transitionEvent = homebrew.events.transitionEnd,
                    transitionClass = homebrew.classes.transitionable,
                    transitionInClass = options.transitions.classes.transitionIn;
                $tooltip
                    .one(transitionEvent, function() {
                        $tooltip.removeClass([transitionClass, transitionInClass].join(' '))
                                .addClass(activeClass);
                        $(document).on([
                            'click.fauxBlur.', instance.uniqueID,
                            ' touchstart.fauxBlur.', instance.uniqueID
                        ].join(''), function(e) {
                            if(!$tooltip.is(e.target) && $tooltip.has(e.target).length === 0) {
                                instance.close();
                            }
                        });
                    })
                    .on({
                        mouseenter : function() {
                            clearTimeout(instance.timer);
                        },
                        mouseleave : function() {
                            clearTimeout(instance.timer);
                            instance.timer = setTimeout(function() {
                                instance.close();
                            }, options.hoverDuration);
                        }
                    });
                setTimeout(function() {
                    $tooltip.addClass(transitionClass);
                    if(altRender) {
                        $tooltip.css('margin-top', -$tooltip.outerHeight() + 'px');
                    } else {
                        $tooltip.addClass(transitionInClass);
                    }
                }, 10);
            } else {
                $tooltip.addClass(activeClass);
            }
            return instance;
        },
        close : function(args) {
            args = args || {};
            var instance = this,
                $tooltip = instance.$tooltip;
            if(!$tooltip) return;
            var options = instance.options,
                activeClass = options.classes.active,
                altRender = instance.getAltRender();
            $(document).off('.fauxBlur.' + instance.uniqueID);
            if(options.transitions.enable) {
                var transitionEvent = homebrew.events.transitionEnd,
                    transitionClass = homebrew.classes.transitionable,
                    transitionOutClass = options.transitions.classes.transitionOut;
                $tooltip
                    .trigger(transitionEvent)
                    .off('mouseenter mouseleave')
                    .one(transitionEvent, function() {
                        $tooltip.removeClass([transitionClass, transitionOutClass].join(' '))
                                .removeClass(activeClass)
                                .remove();
                        instance.$tooltip = null;
                        if(typeof args.onCloseEnd === 'function') {
                            args.onCloseEnd();
                        }
                    });
                setTimeout(function() {
                    $tooltip.addClass(transitionClass);
                    if(altRender) {
                        $tooltip.css('margin-top', '');
                    } else {
                        $tooltip.addClass(transitionOutClass);
                    }
                }, 10);
            } else {
                $tooltip.removeClass(activeClass);
                instance.$tooltip = null;
                if(typeof args.onClose === 'function') {
                    args.onClose();
                }
            }
            return instance;
        },
        destroy : function() {
            var instance = this,
                el = instance.$el[0],
                options = instance.options;
            instance.disable().close();
            $.removeData(el, instance.name);
            if($.data(el, instance.name + '-title')) {
                el.title = $.data(el, instance.name + '-title');
                $.removeData(el, instance.name + '-title');
            }
        }
    });
    homebrew.makePlugin(homebrew.Tooltip);
    /* Extend jQuery with our custom functions built in plugins format. */
    $.fn.extend({
        /**---- Dropdownify ----**\
         * Call this `$('select').dropdownify()` function on the <select> nodes that
         * you want to have custom visuals on.
         
         * Sample <select> node:
         
            <select id="foobar">
                <!-- All your options -->
            </select>
            
         * This will result in:
         
            <div class="dropdown">
                <select id="foobar">
                    <!-- All your options -->
                </select>
                
                <div class="dropdown-btn">
                    <div class="dropdown-arrow" />
                    <span>Label</span>
                </div>
            </div>
         
         * If there are any attributes that you need to set onto the
         * resulting dropdown node, you can do it through
         * `data-dropdown-attributes`. The value has to be a valid
         * JSON format, as shown below:
         
            <select data-dropdown-attributes='{ "class" : "sample-class", "style" : "width: 300px;", "id" : "sampleID" }' id="foobar">
                <!-- All your options -->
            </select>

         * Alternatively, you can wrap the <select> with a parent node
         * (preferably a <span> node) and add your desired attributes on
         * that node. Then, instead of running dropdownify on the <select>
         * node, you run it on the parent node instead.
     
         * Example of wrapping with a parent node:

            <span class="sample-class" style="width: 300px;" id="sampleID">
                <select id="foobar">
                    <!-- All your options -->
                </select>
            </span>

            $('#sampleID').dropdownify();
            
         * Both of the above methods will result in this:
         
            <div class="sample-class dropdown" style="width: 300px;" id="sampleID">
                <select id="foobar">
                    <!-- All your options -->
                </select>
                
                <div class="dropdown-btn">
                    <div class="dropdown-arrow" />
                    <span>Label</span>
                </div>
            </div>
         */
        dropdownify : function() {
            var pluginName = 'dropdownify',
                options = {
                    markups : {
                        holder : '<div />',
                        button : '<div />',
                        arrow : '<div><i /></div>',
                        label : '<span />'
                    },
                    classes : {
                        holder : 'dropdown',
                        button : 'dropdown-btn',
                        arrow : 'dropdown-arrow',
                        label : 'dropdown-label'
                    }
                },
                methods = {
                    create: function() {
                        /**
                         * If these dropdowns are in <form> nodes, then we need to
                         * attach a handler to adjust our input visuals whenever
                         * the form is reset.
                         */
                        var forms = this.parents('form');
                        
                        if(forms.length) {
                            forms.off('.' + pluginName).on('reset.' + pluginName, function() {
                                var _this = $(this);
                                
                                setTimeout(function() {
                                    _this.find('select').trigger('change');
                                }, 25);
                            });
                        }
                        
                        this.each(function() {
                            var $this = $(this),
                                dropdown = ($this.is('select')) ? $this : $this.children('select');
                            if(!dropdown.length || typeof dropdown.data(pluginName) !== 'undefined') return;

                            var dropdownAttr;
                            if($this.is('select')) {
                                dropdownAttr = $this.data('dropdown-attributes');
                            } else {
                                dropdownAttr = $this.getAttributes();
                                dropdown.unwrap();
                            }
                            
                            var holder = dropdown.closest('.' + options.classes.holder);
                            
                            if(holder.length < 1) {
                                holder = $(options.markups.holder).addClass(options.classes.holder);
                                dropdown.after(holder).prependTo(holder);
                            }

                            if(typeof dropdownAttr === 'object') {
                                $.each(dropdownAttr, function(key, value) {
                                    if(key === 'class') {
                                        holder.addClass(value);
                                    } else {
                                        holder.attr(key, value);
                                    }
                                });
                            }

                            var btn = holder.find('.' + options.classes.button);
                            if(btn.length < 1) btn = $(options.markups.button).addClass(options.classes.button).appendTo(holder);

                            var arrow = btn.find('.' + options.classes.arrow);
                            if(arrow.length < 1) arrow = $(options.markups.arrow).addClass(options.classes.arrow).appendTo(btn);

                            var label = btn.find('.' + options.classes.label);
                            if(label.length < 1) label = $(options.markups.label).addClass(options.classes.label).text(dropdown.find('option:checked').text()).appendTo(btn);
                            
                            dropdown.data(pluginName, {
                                activate: function() {
                                    var _this = this;

                                    dropdown.on('change.' + pluginName, function() {
                                        _this.refresh();
                                    });

                                    return _this;
                                },

                                deactivate: function() {
                                    dropdown.off('.' + pluginName);
                                    return this;
                                },

                                refresh: function() {
                                    var checkedText = dropdown.find('option:checked').text();
                                    if(!dropdown.data('ignore-asterisk') && checkedText.lastIndexOf('*') === checkedText.length-1) {
                                        checkedText = [
                                            checkedText.substr(0, checkedText.length-1),
                                            '<span class="text-red">*</span>'
                                        ].join('');
                                    }
                                    label.html(checkedText);
                                    btn.toggleClass('is-placeholder', Boolean(dropdown.val() === ''));
                                    return this;
                                },

                                destroy: function() {
                                    dropdown
                                        .insertBefore(holder)
                                        .off('.' + pluginName)
                                        .removeData(pluginName)
                                        .closest('form')
                                            .off('.' + pluginName);

                                    holder.remove();
                                },

                                $elems: {
                                    arrow: arrow,
                                    button: btn,
                                    holder: holder,
                                    label: label
                                }
                            });

                            dropdown.data(pluginName).activate().refresh();
                        });
                    },

                    activate: function() {
                        runElemMethod.call(this, pluginName, 'activate');
                    },

                    deactivate: function() {
                        runElemMethod.call(this, pluginName, 'deactivate');
                    },

                    refresh: function() {
                        runElemMethod.call(this, pluginName, 'refresh');
                    },
                    
                    destroy: function() {
                        runElemMethod.call(this, pluginName, 'destroy');
                    }
                },
                _arguments = arguments;

            for(var i = _arguments.length-1; i > -1; i--) {
                if(typeof _arguments[i] === 'object') $.extend(true, options, _arguments[i]);
            }
            
            if(typeof _arguments[0] === 'string' && typeof methods[_arguments[0]] === 'function') {
                methods[_arguments[0]].call(this);
            } else {
                methods.create.call(this);
            }
            
            return this;
        /* End dropdownify */
        },


        /**---- getAttributes ----**\
        
         * Call `$('.foo').getAttributes()` to get ALL attributes of the
         * element, which is returned as an Object Literal.
         
         * This is added in to be used by the `$.dropdownify()` function,
         * but feel free to use it whenever you need it.
         **/
        getAttributes : function() {
            var elem = this, 
                attr = {};

            if(elem.length) {
                $.each(elem.get(0).attributes, function(v,n) { 
                    n = n.nodeName||n.name;
                    v = elem.attr(n); // relay on $.fn.attr, it makes some filtering and checks
                    if(v !== undefined && v !== false) attr[n] = v;
                });
            }

            return attr;
        },


        /**---- Inputify ----**\
        
         * Call this `$('.foo').inputify()` function on the radio/checkboxes that
         * you want to have custom visuals on.
         
         * Sample input node:
         
            <label for="radio1">
                <input type="radio" name="radioGroup" id="radio1" />
            </label>
         
         * Supported input types are `radio` and `checkbox`, as
         * only those two require custom visuals so far.
         
         * `name` attribute is used for radio buttons to group
         * them up together, so that only one radio button in that
         * group can be checked at a time.

         * Ideally, the input node should either (1) be wrapped inside
         * a <label> node, or (2) have a <label> that corresponds to the
         * ID of the input. If there is no <label> node to use, the script
         * will create its own.
         */
        inputify : function(_options) {
            var pluginName = 'inputify',
                options = {
                    inputIconClass : 'icon--input',
                    radioClass : 'icon--radio-btn',
                    checkboxClass : 'icon--checkbox',
                    checkedClass : 'is-checked',
                    hiddenClass : 'hidden-accessible',
                    inputVisualMarkupArray : ['<div class="icon ', 'inputIconClass', ' ', 'inputTypeClass', '"><i /></div>'],
                    labelMarkupArray : ['<label for="', 'inputID', '">', 'inputVisualMarkupArray', '</label>']
                },
                methods = {
                    create: function() {
                        var labels = $('label');
                                    
                        /**
                         * Setup the input visual DOM nodes
                         */
                        this.each(function(index) {
                            var input = $(this);
                            if(typeof input.data(pluginName) !== 'undefined') return;

                            var inputType = input.attr('type'),
                                inputTypeClass = getInputTypeClass(input.attr('type')),
                                inputID = input.attr('id'),
                                label = labels.filter('[for="' + inputID + '"]');
                            
                            if(!label.length) label = input.closest('label');

                            if(!label.length) {
                                if(typeof inputID !== 'string' || inputID === '') {
                                    input.attr('id', index + new Date().getTime());
                                    inputID = input.attr('id');
                                }

                                options.labelMarkupArray[1] = inputID;
                                options.labelMarkupArray[3] = options.inputVisualMarkupArray.join('');
                                label = $(options.labelMarkupArray.join(''));

                                label.data(pluginName, {
                                    destroyable: true
                                });

                                input.after(label);
                            }

                            var inputVisual = label.find('.' + inputTypeClass);

                            if(!inputVisual.length) {
                                options.inputVisualMarkupArray[1] = options.inputIconClass;
                                options.inputVisualMarkupArray[3] = inputTypeClass;
                                if(input.is(':checked')) options.inputVisualMarkupArray[3] += ' is-checked';

                                if(input.parents('label').length) {
                                    $(options.inputVisualMarkupArray.join('')).insertAfter(input);
                                } else {
                                    $(options.inputVisualMarkupArray.join('')).prependTo(label);
                                }
                            }

                            input
                                .addClass(options.hiddenClass)
                                .data(pluginName, {
                                    '$els' : {
                                        'label' : label
                                    },

                                    refresh : function() {
                                        var $inputVisual = this.$els.label.find('.' + options.inputIconClass);

                                        if(input.is(':checked')) {
                                            $inputVisual.addClass(options.checkedClass);
                                        } else {
                                            $inputVisual.removeClass(options.checkedClass);
                                        }

                                        return this;
                                    }
                                });
                        });
                        
                        /**
                         * Setup the change handler for radio buttons.
                         *
                         * It is more complicated for radios, because if the clicked
                         * radio is in a group, only the clicked radio will trigger
                         * the change event, the rest of the radios in the group won't.
                         * We need to work around this in order to update the custom
                         * radio visuals accordingly.
                         */
                        var radioInputs = this.filter('[type="radio"]');
                        
                        if(radioInputs.length) {
                            /**
                             * First, split the radio inputs into groups. This is done by
                             * pushing each group's name into an array, making sure
                             * that there are no duplicates.
                             */
                            var radioGroupNames = [];
                            
                            radioInputs.each(function() {
                                var radioInput = $(this),
                                    radioInputName = radioInput.attr('name');
                            
                                if(typeof radioInputName !== 'string') return;

                                if(radioGroupNames.length < 1 && radioInputName !== '') {
                                    radioGroupNames.push(radioInputName);
                                } else {
                                    for(var i = radioGroupNames.length-1; i > -1; i--) {
                                        if(radioGroupNames[i] == radioInputName) {
                                            break;
                                        } else if(i === 0) {
                                            radioGroupNames.push(radioInputName);
                                        }
                                    }
                                }
                            });
                            
                            /**
                             * Now we iterate through each group.
                             *
                             * If at anytime, a radio input in the group triggers a
                             * change, we will loop through all the other radio inputs
                             * in the same group to update their visuals accordingly.
                             *
                             * An IIFE (Immediately Invoked Function Expression) is used
                             * so that the variable can be preserved to be used with the
                             * event handler.
                             */
                            for(var i=radioGroupNames.length; i > -1; i--) {
                                (function() {
                                    var $radioGroupInputs = radioInputs.filter('[name="' + radioGroupNames[i] + '"]');

                                    $radioGroupInputs
                                        .off('change.' + pluginName)
                                        .on('change.' + pluginName, function() {
                                            $radioGroupInputs.each(function() {
                                                $(this).data(pluginName).refresh();
                                            });
                                        });
                                })();
                            }
                        }
                        
                        /**
                         * Setup the change handler for checkboxes.
                         */
                        var checkboxInputs = this.filter('[type="checkbox"]');
                        
                        if(checkboxInputs.length) {
                            checkboxInputs
                                .off('change.' + pluginName)
                                .on('change.' + pluginName, function() {
                                    $(this).data(pluginName).refresh();
                                });
                        }
                        
                        /**
                         * If these inputs are in <form> nodes, then we need to attach
                         * a handler to adjust our input visuals whenever the form
                         * is reset.
                         */
                        var forms = this.closest('form'),
                            inputs = this;
                        
                        if(forms.length) {
                            forms
                                .off('reset.inputify')
                                .on('reset.inputify', function() {
                                    setTimeout(function() {
                                        inputs.trigger('change');
                                    }, 25);
                                });
                        }
                    },
                    
                    destroy: function() {
                        var labels = $('label');
                        
                        this.each(function() {
                            var $this = $(this),
                                inputId = $this.attr('id');

                            if(typeof label.data(pluginName) !== 'object'
                            && label.data(pluginName).destroyable) {
                                label.remove();
                            }
                        });
                        
                        var forms = this.closest('form');
                        
                        /* Remove all plugin event handlers from the input elements
                         * and their corresponding forms. */
                        this.off('.' + pluginName);
                        forms.off('.' + pluginName);
                    },

                    refresh: function() {
                        runElemMethod.call(this, pluginName, 'refresh');
                    }
                },
                _arguments = arguments;
            
            function getInputTypeClass(type) {
                switch(type) {
                    case 'radio':      return options.radioClass;
                    case 'checkbox':   return options.checkboxClass;
                    default:           return undefined;
                }
            }
            
            for(var i = _arguments.length-1; i > -1; i--) {
                if(typeof _arguments[i] === 'object') $.extend(options, _arguments[i]);
            }
            
            if(typeof _arguments[0] === 'string' && typeof methods[_arguments[0]] === 'function') {
                methods[_arguments[0]].call(this);
            } else {
                methods.create.call(this);
            }
            
            return this;
        /* End inputify */
        },


        /**---- Popupify ----**\
        
         * Call this `$('.foo').popupify()` function on your desired popups and
         * popup togglers.
         
         * Sample popup toggler node:
         
            <a href="#corporateProfile" class="foo">Toggle popup</a>
            
            <div id="corporateProfile" />
         
         * `href` should be a hash, followed by the ID of the target popup.
         
         * Alternatively, you may pass a function into the `content` parameter to
         * programmatically select the element whose contents will be used as the
         * popup contents. For example:
        
            <button type="button" id="popupToggler">Click me to show popup</button>
            
            <div class="foobar-con">
                <div class="hello-world"> ... </div>
                <div class="popup-content"> ... </div>
            </div>
            
            <script>
                $(function() {
                    $('#popupToggler').popupify({
                        content : function() {
                            // `this` refers to the element currently being iterated over
                            return $(this).next().find('.popup-content');
                        }
                    });
                });
            </script>
         
         * While invoking this function, it is possible to specify a `height`
         * argument to create popups that have a set height:
         
            $('.foobar').popupify({ height : 500 });
            
         * Alternatively, you may also specify the height through the
         * `data-popup-height` attribute:
         
            <a href="#corporateProfile" data-popup-height="500">Toggle popup</a>
            
         * The height value MUST be a NUMBER.

         * If the popup contents is taller than its set height, default
         * scrollbars will appear on the popup.
         
         * If the popup itself is taller than the page, then scrollbars
         * will appear on the popup holder.
         **/
        popupify : function(_options) {
            var pluginName = 'popupify',
                $body = $('body'),
                options = {
                    classes : {
                        mainPopupHolder : 'main-popup-holder',
                        shown : 'is-shown'
                    },
                    ids : {
                        mainPopupHolder : 'mainPopupHolder'
                    },
                    $elems : {
                        mainPopupHolder : null
                    },
                    markups : {
                        closeBtn : '<a href="#close" data-hide-popup="true" class="popup-closer">Back</a>'
                    },
                    content : undefined,
                    addCloseBtns : true,
                    closeOnOverlay : true
                },
                methods = {
                    create : function() {
                        var $mainPopupHolder = options.$elems.mainPopupHolder;
                    
                        if(!thisObjectExists($mainPopupHolder)) $mainPopupHolder = $('#' + options.ids.mainPopupHolder);
                        
                        if($mainPopupHolder.length < 1) {
                            $mainPopupHolder = $([
                                '<div class="', options.classes.mainPopupHolder, '" id="', options.ids.mainPopupHolder, '" ></div>'
                            ].join('')).appendTo($body);

                            options.$elems.mainPopupHolder = $mainPopupHolder;
                        } else {
                            $mainPopupHolder.appendTo('body');
                        }
                        
                        /**
                         * If the main popup holder doesn't have any of its popup
                         * methods, add them in now.
                         **/
                        if(!thisDataIsValidObject($mainPopupHolder, pluginName)) {
                            $mainPopupHolder.data(pluginName, {
                                reveal : function() {
                                    $body.addClass('popup-is-shown');
                                    setTimeout(function() {
                                        $mainPopupHolder.addClass(options.classes.shown);
                                    }, 10);
                                    return this;
                                },
                                
                                conceal : function() {
                                    $body.removeClass('popup-is-shown');
                                    setTimeout(function() {
                                        $mainPopupHolder.removeClass(options.classes.shown);
                                    }, 10);
                                    return this;
                                },

                                activate : function() {
                                    var _dataMethods = this;

                                    _dataMethods.deactivate();

                                        $mainPopupHolder.on("click." + pluginName, function() {
                                        if($.fn.popupify.closeOnOverlay === true) {
                                            _dataMethods.conceal();
                                        }
                                        });

                                    $mainPopupHolder.on('click.' + pluginName, '.popup', function(e) {
                                        e.stopPropagation();
                                    });
                                        
                                    $mainPopupHolder.on('click.' + pluginName, '[data-hide-popup]', function(e) {
                                        e.preventDefault();
                                        _dataMethods.conceal();
                                    });

                                    return _dataMethods;
                                },

                                deactivate : function() {
                                    $mainPopupHolder.off('.' + pluginName);
                                    return this;
                                },
                                
                                destroy : function() {
                                    this.deactivate();
                                    $mainPopupHolder.removeData(pluginName);
                                }
                            });
                            
                            /* Attach the event handlers to the main popup holder */
                            $mainPopupHolder.data(pluginName).activate();
                            
                            /* Apply pseudo-elements polyfill for IE7. */
                            if(homebrew.browser.ie7) $mainPopupHolder.pseudofy({ method : 'before' });
                        }
                        
                        /** 
                         * If the `this` reference has nothing for us to iterate
                         * through, exit the function.
                         **/
                        if(this.length < 1) return;
                        
                        this.each(function() {
                            var $this = $(this),
                                dataTargetPopup = $this.data('target-popup'),
                                target;
                            
                            /* See if a target content is available */
                            if(typeof options.content === 'function') {
                                target = options.content.call(this);
                            } else if(typeof dataTargetPopup === 'string') {
                                target = $($this.data('target-popup'));
                            } else if(typeof dataTargetPopup === 'object' && dataTargetPopup instanceof jQuery) {
                                target = dataTargetPopup;
                            } else if(typeof $this.attr('href') !== 'undefined') {
                                target = $($this.attr('href'));
                            }
                            
                            /**
                             * If a target content available, then what we're currently
                             * iterating through should be the popup toggler. Proceed
                             * to attach event handlers on the toggler.
                             **/
                            if(thisObjectExists(target)) {
                                if(!thisDataIsValidObject($this, pluginName)) {
                                    $this.data(pluginName, {
                                        reveal : function() {
                                            target.data(pluginName).reveal();
                                            return this;
                                        },
                                        
                                        conceal : function() {
                                            $mainPopupHolder.data(pluginName).conceal();
                                            return this;
                                        },
                                        
                                        activate : function() {
                                            var _dataMethods = this;
                                            
                                            _dataMethods.deactivate();

                                            $this.on('click.' + pluginName, function(e) {
                                                e.preventDefault();
                                                _dataMethods.reveal();
                                            });
                                            
                                            return $this;
                                        },
                                        
                                        deactivate : function() {
                                            $this.off('.' + pluginName);
                                            return $this;
                                        },
                                        
                                        destroy : function() {
                                            this.deactivate();
                                            target.data(pluginName).destroy();
                                            $this.removeData(pluginName);
                                        }
                                    });
                                
                                    $this.data(pluginName).activate();
                                }
                            } else {
                            /**
                             * Otherwise, assume that what we're iterating through
                             * is the target content itself.
                             **/
                                target = $(this);
                            }
                            
                            if(!target.parent().is($mainPopupHolder)) {
                                target.appendTo($mainPopupHolder);
                            }

                            if(options.addCloseBtns && target.find('.popup-closer').length < 1) {
                                target.prepend(options.markups.closeBtn);
                            }

                            if(typeof target.data(pluginName) === 'undefined') {
                                target.data(pluginName, {
                                    reveal : function() {
                                        $mainPopupHolder.find('.popup').removeClass(options.classes.shown);
                                        target.addClass(options.classes.shown);
                                        $mainPopupHolder.data(pluginName).reveal();
                                        $.fn.popupify.closeOnOverlay = options.closeOnOverlay;
                                        
                                        if(homebrew.browser.ie) {
                                            target.trigger('revealed');
                                        } else {
                                            $mainPopupHolder.on(homebrew.events.transitionEnd, function() {
                                                $mainPopupHolder.off(homebrew.events.transitionEnd);
                                                target.trigger('revealed');
                                            });
                                        }
                                        
                                        return this;
                                    },
                                    
                                    conceal : function() {
                                        $mainPopupHolder.data(pluginName).conceal();
                                        return this;
                                    },
                                    
                                    destroy : function() {
                                        target.removeData(pluginName);
                                    }
                                });
                            }
                        });
                    },
                    
                    activate : function() {
                        runElemMethod.call(this, pluginName, 'activate');
                    },
                    
                    deactivate : function() {
                        runElemMethod.call(this, pluginName, 'deactivate');
                    },
                    
                    destroy : function() {
                        var $mainPopupHolder = options.$elems.mainPopupHolder;
                    
                        if(!thisObjectExists($mainPopupHolder)) $mainPopupHolder = $('#' + options.ids.mainPopupHolder);
                        
                        if($mainPopupHolder.length && typeof $mainPopupHolder.data(pluginName) !== 'undefined') {
                            $mainPopupHolder.data(pluginName).destroy();
                        }
                        
                        runElemMethod.call(this, pluginName, 'destroy');
                    }
                },
                _arguments = arguments;
            
            /**
             * Run through the arguments. If an object is found, assume it's an
             * Object Literal with customised options for the function. 
             **/
            for(var i = _arguments.length-1; i > -1; i--) {
                if(typeof _arguments[i] === 'object') $.extend(options, _arguments[i]);
            }
            
            if(typeof _arguments[0] === 'string' && typeof methods[_arguments[0]] === 'function') {
                methods[_arguments[0]].call(this);
            } else {
                methods.create.call(this);
            }
            
            return this;
        /* End popupify */
        },


        /**---- Placeholderify ----**\
        
         * Call this `$('.foo').placeholderify()` function on any nodes that
         * require fallback for the `placeholder` HTML5 attribute.
         
         * You are required to perform your own browser/feature
         * checking before calling this function.
         */
        placeholderify : function(_options) {
            var options = $.extend({}, _options, {
                    placeholderClass : 'is-placeholder'
                });
            
            return this.each(function() {
                var input = $(this),
                    placeholderText = input.attr('placeholder');
                
                if(input.val() === '') {
                    if(input.attr('type') === 'password') {
                        input.attr('type', 'text');
                        input.data('was-password', true);
                    }
                    input.val(placeholderText).addClass(options.placeholderClass);
                }
                
                input.on({
                    'focus.placeholder' : function() {
                        if(input.val() === placeholderText) {
                            if(input.data('was-password')) {
                                input.attr('type', 'password');
                            }
                            input.val('').removeClass(options.placeholderClass);
                        }
                    },
                    
                    'blur.placeholder' : function() {
                        if(input.val() === '') {
                            if(input.data('was-password')) {
                                input.attr('type', 'text');
                            }
                            input.val(placeholderText).addClass(options.placeholderClass);
                        }
                    }
                });
            });
        },


        /**---- queryString ----**\
        
         * Function to get the query string. Let's say your URL is like this:

            aaa.com?title=hello&desc=goodbye

         * Calling `$.fn.queryStr()` would return you an Object Literal with all
         * the key-value pairs, as shown:

             {
                title : 'hello',
                desc : 'goodbye'
             }
         
         * If no query string is available, the function will return `false`.
         
         * The function accepts a single argument, which should be the key
         * that you're searching for. If a matching key is found, its value is
         * returned.

         * Using the above URL again, if you were to run `$.fn.queryStr('title')`,
         * then the function will return 'hello' to you. If the query string isn't
         * found, then the function will return `false`.
         **/
        queryStr : function(key) {
            var searchStr = window.location.search;
            
            if(searchStr === '') return false;
            
            var queryArr = window.location.search.substr(1).split('&'),
                tempArr;
            
            if(typeof key === 'string') {
                for(var i = 0, ii = queryArr.length; i < ii; i++) {
                    tempArr = queryArr[i].split('=');
                    if(tempArr[0] === key) return tempArr[1];
                }
                return false;
            } else {
                var resultValue = {};
                
                for(var i = 0, ii = queryArr.length; i < ii; i++) {
                    tempArr = queryArr[i].split('=');
                    resultValue[tempArr[0]] = tempArr[1];
                }
                
                return resultValue;
            }
        },


        /**---- Togglerify ----**\
        
         * `$('.togglers').togglerify()` turns its target nodes into
         * togglers; by default, it is a simple class toggler that
         * adds/removes a class on its corresponding target node.
         
         * An example:
         
            <a class="togglers" data-togglerify-target="togglerContentID">Toggler</a>
            
            <div id="togglerContentID" />
         
         * `data-togglerify-target` determines which element the function
         * will toggle the class on. The value should be the ID of the
         * desired node. Alternatively, you can pass in a function to the
         * `content` argument, which will be used to select the desired
         * element. For example:

            <a class="togglers">Toggler</a>
            <div class="contents" />

            $('.togglers').togglerify({
                content: function(index) {
                    return $(this).next('.contents);
                }
            });

         * The `this` keyword refers to the toggler element that's being
         * iterated over. You're also given the toggler's index to make use.
         
         * By default, the class toggled is `is-toggled`. You may pass
         * in your own class to toggle, as follows:
         
            $('.foo').togglerify({ toggledClass : 'is-triggered' });
         
         * The parameters that let you adjust the behaviour:
         *   - `singleActive`
         *          = Default value: false
         *          = Setting this to `true` ensures that there is only
         *            one active toggler at any given time in each
         *            iterated group
         *   - `selfToggleable`
         *          = Default value: true
         *          = Setting this to `false` removes the toggler's
         *            ability to toggle itself off.
         *   - `slide`
         *          = Default value: false
         *          = Setting this to `true` causes the function to
         *            add a sliding transition effect, using CSS Transitions
         *            if available, and falling back to jQuery's slide
         *            methods otherwise.
         *   - `pretoggle`
         *          = Default value: undefined
         *          = Assigning an index to this parameter causes the
         *            function to immediately activate the toggler in
         *            question. Index should be zero-based.
         *   - `content`
         *          = Default value: undefined
         *          = Assigning a function to this parameter causes the
         *            function to run this in order to find its target
         *            content. If this parameter is provided, the function
         *            will ignore the `data-togglerify-target` attribute.
         **/
        togglerify : function() {
            var pluginName = 'togglerify',
                options = {
                    toggledClass : 'is-toggled',
                    singleActive : false,
                    selfToggleable : true,
                    
                    slide : false,
                    slideDuration : 300,
                    
                    pretoggle : undefined,
                    content : undefined,

                    useCSSTransitions : !homebrew.browser.ie && root.hasClass('csstransitions'),

                    classes : {
                        transitionable: homebrew.classes.transitionable
                    }
                },
                methods = {
                    create: function() {
                        var togglers = this;

                        togglers.each(function(index) {
                            var thisToggler = $(this),
                                targetData = thisToggler.data('togglerify-target'),
                                target;
                            
                            if(typeof options.content === 'function') {
                                target = options.content.call(this, index);
                            } else if(typeof targetData === 'string') {
                                target = $('#' + targetData);
                            } else {
                                target = $(thisToggler.attr('href'));
                            }
                            
                            if(!target.length) return;
                            
                            var togglerAndTarget = thisToggler.add(target);

                            togglerAndTarget.data(pluginName, {
                                activate : function() {
                                    this.deactivate();
                                    
                                    thisToggler.on('click.' + pluginName, function(e) {
                                        e.preventDefault();
                                        
                                        if(thisToggler.hasClass(options.toggledClass)) {
                                            /**
                                             * If the selfToggleable boolean is false, it means that the
                                             * toggler is not able to toggle itself off.
                                             **/
                                            if(options.selfToggleable === false) return;

                                            /* Make this toggler inactive */
                                            thisToggler.data(pluginName).toggleOff();
                                            return;
                                        }
                                        
                                        /* Make this toggler active */
                                        thisToggler.data(pluginName).toggleOn();
                                    });

                                    return this;
                                },

                                deactivate : function() {
                                    thisToggler.off('click.' + pluginName);
                                    return this;
                                },

                                toggleOn : function(methodSettings) {
                                    methodSettings = methodSettings || {};

                                    togglerAndTarget
                                        .trigger('toggleOn', [thisToggler, target])
                                        .trigger('toggle', [thisToggler, target]);
                                    
                                    thisToggler.addClass(options.toggledClass);
                                    target.addClass(options.toggledClass);

                                    /**
                                     * If the singleActive boolean is true, it means that only
                                     * one toggler in the group can be active at any given time. 
                                     **/
                                    if(options.singleActive) {
                                        togglers.not(thisToggler).togglerify('toggleOff');
                                    }

                                    if(options.slide && !methodSettings.noSlide) {
                                        var targetHeight = methodSettings.contentHeight;

                                        if(options.useCSSTransitions) {
                                            if(typeof targetHeight !== 'number') {
                                                target.css('height', 'auto');
                                                targetHeight = target.get(0).clientHeight;
                                            }

                                            target.css('height', '0px').addClass(options.classes.transitionable);
                                            
                                            setTimeout(function() {
                                                target
                                                    .off(homebrew.events.transitionEnd)
                                                    .on(homebrew.events.transitionEnd, function() {
                                                        target
                                                            .off(homebrew.events.transitionEnd)
                                                            .removeClass(options.classes.transitionable);

                                                        if(typeof methodSettings.contentHeight !== 'number') {
                                                            target.css('height', '');
                                                        }

                                                        togglerAndTarget
                                                            .trigger('afterToggleOn', [thisToggler, target])
                                                            .trigger('afterToggle', [thisToggler, target]);
                                                    })
                                                    .css('height', targetHeight + 'px');
                                            }, 5);
                                        } else {
                                            if(typeof targetHeight !== 'number') {
                                                target.show();
                                                targetHeight = target.get(0).clientHeight;
                                            }
                                            target.hide();
                                            target.slideDown(options.slideDuration, function() {
                                                togglerAndTarget
                                                    .trigger('afterToggleOn', [thisToggler, target])
                                                    .trigger('afterToggle', [thisToggler, target]);
                                            });
                                        }
                                    } else {
                                        togglerAndTarget
                                            .trigger('afterToggleOn', [thisToggler, target])
                                            .trigger('afterToggle', [thisToggler, target]);
                                    }

                                    return this;
                                },
                                
                                toggleOff : function(methodSettings) {
                                    if(!thisToggler.hasClass(options.toggledClass)) return;
                                    methodSettings = methodSettings || {};

                                    togglerAndTarget
                                        .trigger('toggleOff', [thisToggler, target])
                                        .trigger('toggle', [thisToggler, target]);

                                    thisToggler.removeClass(options.toggledClass);
                                
                                    if(options.slide && !methodSettings.noSlide) {
                                        if(options.useCSSTransitions) {
                                            var targetHeight = target.get(0).clientHeight;

                                            target
                                                .css('height', targetHeight + 'px')
                                                .removeClass(options.toggledClass);

                                            setTimeout(function() {
                                                target
                                                    .addClass(options.classes.transitionable)
                                                    .off(homebrew.events.transitionEnd)
                                                    .on(homebrew.events.transitionEnd, function() {
                                                        target
                                                            .off(homebrew.events.transitionEnd)
                                                            .removeClass(options.classes.transitionable)
                                                            .css('height', '');

                                                        togglerAndTarget
                                                            .trigger('afterToggleOff', [thisToggler, target])
                                                            .trigger('afterToggle', [thisToggler, target]);
                                                    })
                                                    .css('height', '0px');
                                            }, 5);
                                        } else {
                                            target
                                                .slideUp(options.slideDuration, function() {
                                                    togglerAndTarget
                                                        .trigger('afterToggleOff', [thisToggler, target])
                                                        .trigger('afterToggle', [thisToggler, target]);
                                                })
                                                .removeClass(options.toggledClass);
                                        }
                                    } else {
                                        target.removeClass(options.toggledClass);

                                        togglerAndTarget
                                            .trigger('afterToggleOff', [thisToggler, target])
                                            .trigger('afterToggle', [thisToggler, target]);
                                    }

                                    return this;
                                },

                                set : function(key, value) {
                                    if(typeof arguments[0] === 'object') {
                                        $.extend(true, options, arguments[0]);
                                    } else {
                                        options[key] = value;
                                    }
                                    return this;
                                },

                                destroy : function() {
                                    this.deactivate();
                                    togglerAndTarget.removeData(pluginName);
                                }
                            });

                            thisToggler.data(pluginName).activate();
                            
                            /* If need to pretoggle a set, this is the time to do it. */
                            if(typeof options.pretoggle === 'number' && index === options.pretoggle) {
                                thisToggler.addClass(options.toggledClass);
                                if(options.slide) target.addClass(options.toggledClass);
                                if(!options.useCSSTransitions) target.show();
                            }
                        });
                    },

                    activate: function() {
                        runElemMethod.call(this, pluginName, 'activate', arguments);
                    },

                    deactivate: function() {
                        runElemMethod.call(this, pluginName, 'deactivate', arguments);
                    },

                    toggleOn: function() {
                        runElemMethod.call(this, pluginName, 'toggleOn', arguments);
                    },
                    
                    toggleOff: function() {
                        runElemMethod.call(this, pluginName, 'toggleOff', arguments);
                    },

                    set: function() {
                        runElemMethod.call(this, pluginName, 'set', arguments);
                    },

                    destroy : function() {
                        runElemMethod.call(this, pluginName, 'destroy', arguments);
                    }
                },
                _arguments = arguments;
            
            if(typeof _arguments[0] === 'string' && _arguments[0] !== 'create' && typeof methods[_arguments[0]] === 'function') {
                methods[_arguments[0]].apply(this, $.makeArray(_arguments).slice(1));
            } else {
                for(var i = _arguments.length-1; i > -1; i--) {
                    if(typeof _arguments[i] === 'object') $.extend(options, _arguments[i]);
                }

                methods.create.call(this);
            }
            
            return this;
        /* End togglerify */
        },


        /**---- Validify ----**\
        
         * Call this `$('form').validify()` on form nodes that require
         * validation.
         
         * The reason we are using JS to do validation instead of the
         * built-in HTML5 validation attributes is because javascript
         * validation is capable of providing real-time feedback,
         * whereas HTML5 validation only provides feedback when the user
         * tries to submit their form. Additionally, JS validation will
         * work in older browsers that do not support HTML5 validation.

         * To initialise the field with validation, you must explicitly
         * set the `data-validify` attribute on the element, as
         * shown below:

            (A) <input type="text" data-validify="required: true, pattern: numbers, minlength: 6" />
            (B) <input type="text" data-validify />

         * The field will NOT be initialised by the validation function if
         * it doesn't have the custom data attribute.

         * On line (A), a comma-separated list of key-value pairs containing
         * validation criteria is set as the attribute's value. This
         * determines how the field should be validated. If omitted,
         * the field is simply initialised with the minimum validation method,
         * which checks whether or not the field is empty.

         * List of criteria available properties in the JSON Object so far is:
         
            required : true
            pattern : numbers
            valid : true
            minlength : 6
            compareWith : #yourOtherInput
            noSuccess : true
            response : #yourResponseElem
            floater : #yourFloaterElem
            
         * It is possible to group fields together using the `data-validify-group`
         * attribute.

            <fieldset data-validify-group-data="required: true, pattern: numbers">
                (A) <input data-validify />
                (B) <input data-validify="pattern: alphanumeric" />
            </fieldset>

         * Any criteria set on the group element will be applied onto the fields
         * in the group. However, the criteria set on the fields themselves will
         * always take precedence. For example, for (A) above, its criteria
         * will be following the set declared on the group, which is
         * "required: true, pattern: numbers", while for (B), it will be
         * "required: true, pattern: alphanumeric" instead.

         * Being in a group also results in slightly different response
         * behaviour, whereby the success/failed messages are only shown once
         * the entire group of fields have been validated to be successful or
         * failed.

         * Majority of the useful functions and properties are exposed through
         * `$('input').data('validify')`, on both the form element and the field
         * elements. You will need to make use of these to determine whether or
         * not the form is valid, and then allow users to proceed based on that.
         
         * To add your own regex pattern, scroll down to the function options below
         * for more information.
         */
        validify : function(_options) {
            var pluginName = 'validify',
                options = {
                    selectors : {
                        all : 'input[data-validify], textarea[data-validify], select[data-validify]',
                        fields : 'input[data-validify], textarea[data-validify], select[data-validify]',
                        groups : '[data-validify-group]',
                        textFields : 'input[type="text"], input[type="tel"], input[type="number"], input[type="email"], input[type="password"], textarea',
                        checkButtons : 'input[type="radio"], input[type="checkbox"]',
                        dropdowns : 'select'
                    },
                    
                    classes : [
                        { key : 'success',      className : 'is-success' },
                        { key : 'hovered',      className : 'is-hovered' },
                        { key : 'focused',      className : 'is-focused' },
                        { key : 'failed',       className : 'is-failed' },
                        { key : 'pattern',      className : 'is-failed-pattern' },
                        { key : 'minlength',    className : 'is-failed-minlength' },
                        { key : 'required',     className : 'is-failed-required' },
                        { key : 'compare-with', className : 'is-failed-compare-with' },
                        { key : 'minchecked',   className : 'is-failed-minchecked' },
                        { key : 'custom',       className : 'is-failed-custom' }
                    ],
                    
                    regexes : [
                        /**
                         * To add your own reusable regex pattern, just duplicate
                         * and modify as needed.
                         
                            { key : 'your-keyword', pattern : /your-pattern/ }
                         
                         * Replace `your-keyword` with the desired String value to
                         * use in the JSON data object, and `/your-pattern/` with
                         * your desired regex pattern.

                         * Once added in, you can immediately start using it like below:
                         
                            <input type="text" data-validify='pattern: your-keyword' />

                         **/
                        { key : 'name', pattern:  /^[^\d<>!@#$%^&*()=+|{}:;"',.`~_\/\\\]]+$/i },
                        { key : 'alphabet', pattern:  /^[a-z\s]+$/i },
                        { key : 'alphanumeric', pattern : /^[a-z0-9\s]+$/i },
                        { key : 'email', pattern : /^[^\s@]+@[^\s@]+\.[^\s@]+$/i },
						{ key : 'dob', pattern : /^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/ },
                        { key : 'numbers', pattern : /^\s*\d+\s*$/ },
                        { key : 'phone', pattern : /^[\+\-0-9\s]+$/ },
                        { key : 'mobile-number', pattern : /^01(|[\d]+)$/ },
                        { key : 'full-mobile-number', pattern : /^601(|[\d]+)$/ },
                        { key : 'basic', pattern : /^[^<>]+$/i }
                    ],
                
                    validationTimerDuration : 250,
                    debug: false
                },
                allClasses = (function() {
                    var tempArr = [];
                    
                    for(var i = options.classes.length-1; i > -1; i--) {
                        if(options.classes[i].key === 'focused') continue;
                        tempArr.push(options.classes[i].className);
                    }
                    
                    return tempArr.join(' ');
                })(),
                methods = {
                    create: function() {
                        this.each(function() {
                            var thisForm = $(this),
                                allInputs = thisForm.find(options.selectors.all);
                            
                            thisForm.data(pluginName, {
                                valid: false,
                                strict: false,
                                debug: options.debug,
                                allInputs: allInputs,

                                /**
                                 * Force the relevant input nodes in this form to update their
                                 * own validation status, then make the form refresh itself
                                 * to see if it becomes valid based on the latest results.
                                 **/
                                validate: function() {
                                    var _this = this;
                                        
                                    debugMsg.call(thisForm, [
                                        ' /*===================================================*\\ ',
                                        '            Updating form validation status              ',
                                        ' ------------------------------------------------------- '
                                    ], 'start');

                                    debugMsg.call(thisForm, [
                                        'Form: ',
                                        thisForm,
                                        ' '
                                    ]);
                                    
                                    /*----*/

                                    allInputs.each(function() {
                                        $(this).data(pluginName).validate();
                                    });
                                    
                                    _this.refresh();
                                    
                                    /*----*/

                                    debugMsg.call(thisForm, 'Form validation: ' + _this.valid);

                                    debugMsg.call(thisForm, [
                                        ' ------------------------------------------------------- ',
                                        '         Form validation status update complete          ',
                                        ' \\*===================================================*/ ',
                                        ''
                                    ], 'end');
                                },
                                
                                /**
                                 * Check the form's validation status without forcing each
                                 * of its input children to update itself.
                                 *
                                 * This is used after an input element updates its own 
                                 * status, so that we can see whether or not the form
                                 * becomes valid.
                                 **/
                                refresh: function() {
                                    var _this = this;
                                    
                                    _this.valid = true;
                                    
                                    allInputs.each(function() {
                                        if($(this).data(pluginName).valid === false) {
                                            _this.valid = false;
                                            return false;
                                        }
                                    });
                                },

                                set: function(key, value) {
                                    this[key] = value;
                                    return this;
                                },

                                destroy: function() {
                                    runElemMethod.call(this.allInputs, pluginName, 'destroy');
                                    thisForm.removeData(pluginName);
                                }
                            });
                            
                            if(thisForm.is('form')) {
                                thisForm.off('.' + pluginName).on('reset.' + pluginName, function() {
                                    setTimeout(thisForm.data(pluginName).validate, 100);
                                });
                            }
                            
                            allInputs.each(function(index) {
                                var thisInput = $(this),
                                    validifyData = typeCast(convertCSVtoObject(thisInput.data(pluginName))),
                                    inputPluginObj = {
                                        group : thisInput.closest('[data-validify-group]')
                                    },
                                    triggerEvents;

                                inputPluginObj.isInHolder = Boolean(thisInput.parent('.field-holder').length);

                                /* If the input is in a group, get the entire group's data first */
                                if(thisObjectExists(inputPluginObj.group)) {
                                    var nestedGroupInputs = inputPluginObj.group.find(options.selectors.groups).find(options.selectors.all),
                                        groupValidifyData = typeCast(convertCSVtoObject(inputPluginObj.group.data(pluginName + '-group')));

                                    if(thisIsJSON(groupValidifyData)) {
                                        if(typeof groupValidifyData === 'string') {
                                            $.extend(inputPluginObj, JSON.parse(groupValidifyData));
                                        } else {
                                            $.extend(inputPluginObj, groupValidifyData);
                                        }
                                    }
                                    
                                    inputPluginObj.groupInputs = inputPluginObj.group.find(options.selectors.all).not(nestedGroupInputs);
                                }

                                /* If the input itself has data, then get it and overwrite the data
                                 * inferred from the group */
                                if(thisIsJSON(validifyData)) {
                                    if(typeof validifyData === 'string') {
                                        $.extend(true, inputPluginObj, JSON.parse(validifyData));
                                    } else {
                                        $.extend(true, inputPluginObj, validifyData);
                                    }
                                }

                                if(thisInput.data('validify-pattern')) {
                                    inputPluginObj.pattern = thisInput.data('validify-pattern');
                                }

                                if(typeof inputPluginObj.pattern === 'undefined') {
                                    if(thisInput.attr('type') === 'email') {
                                        inputPluginObj.pattern = 'email';
                                    } else {
                                        inputPluginObj.pattern = 'basic';
                                    }
                                }
                                
                                /* If an ID to compare with is provided, then select the element and
                                 * store it in the data object. */
                                if(typeof inputPluginObj.compareWith === 'string') {
                                    inputPluginObj.$compareWith = $(inputPluginObj.compareWith);
                                }
                                
                                /* Similar to above: if an ID to respond with is provided, then
                                 * select the element and store it in the data object. */
                                if(typeof inputPluginObj.response === 'string') {
                                    inputPluginObj.$response = $(inputPluginObj.response);
                                }
                                
                                /* Similar to above. */
                                if(typeof inputPluginObj.groupResponse === 'string') {
                                    inputPluginObj.$groupResponse = $(inputPluginObj.groupResponse);
                                }
                                
                                /* Similar to above, but with additional methods for the floater. */
                                if(typeof inputPluginObj.floater === 'string') {
                                    inputPluginObj.$floater = $(inputPluginObj.floater);

                                    if(inputPluginObj.$floater.length) {
                                        inputPluginObj.$floater.data('floater', {
                                            $popup : inputPluginObj.$floater.closest('.popup')
                                        });

                                        if(inputPluginObj.$floater.data('floater').$popup.length) {
                                            inputPluginObj.$floater.appendTo($('#mainPopupHolder'));
                                        } else {
                                            inputPluginObj.$floater.appendTo('body');
                                        }

                                        $.extend(inputPluginObj.$floater.data('floater'), {
                                            refresh : function() {
                                                    var topPos = thisInput.offset().top - inputPluginObj.$floater.outerHeight();
                                                    if(this.$popup.length) topPos += $('#mainPopupHolder').scrollTop() - $(window).scrollTop();

                                                    inputPluginObj.$floater
                                                        .off(FE.events.transitionEnd)
                                                        .css({
                                                            width : thisInput.outerWidth() + 'px',
                                                            marginTop : '',
                                                            top :  topPos + 'px',
                                                            left : thisInput.offset().left + 'px'
                                                        })
                                                        .addClass('is-shown');

                                                return this;
                                            },

                                            reveal : function() {
                                                var self = this;

                                                self.refresh();
                                                inputPluginObj.$floater.addClass('is-shown');

                                                $(window)
                                                    .off('.' + inputPluginObj.eventID)
                                                    //.off(FE.events.transitionEnd)
                                                    .on('resize.' + pluginName + '.' + inputPluginObj.eventID, homebrew.utils.throttle(function() {
                                                        self.refresh();
                                                    }, 100));
                                                    
                                                return self;
                                            },

                                            conceal : function() {
                                                /*inputPluginObj.$floater
                                                    .on(FE.events.transitionEnd, function() {
                                                        inputPluginObj.$floater
                                                            .off(FE.events.transitionEnd)
                                                            .css({
                                                                top : '',
                                                                left : ''
                                                            });
                                                    })
                                                    .removeClass('is-shown')
                                                    .css('margin-top', '');*/

                                                inputPluginObj.$floater
                                                    .removeClass('is-shown')
                                                    .css({
                                                        marginTop : '',
                                                        top : '',
                                                        left : ''
                                                    });

                                                $(window).off('resize.' + inputPluginObj.eventID);

                                                return this;
                                            }
                                        });
                                    } else {
                                        debugMsg.call(thisForm, 'A floater ID `' + inputPluginObj.floater + '` has been provided, but selection returns nothing.', 'error');
                                        inputPluginObj.$floater = undefined;
                                    }
                                }
                                
                                /**
                                 * Adjust what event to attach the handler to, depending on
                                 * what kind of element we're currently iterating over.
                                 **/
                                if(thisInput.is(options.selectors.checkButtons)) {
                                    triggerEvents = 'change.' + pluginName;
                                } else if(thisInput.is(options.selectors.textFields)) {
                                    triggerEvents = ['input.' + pluginName, 'blur.' + pluginName].join(' ');
                                } else if(thisInput.is(options.selectors.dropdowns)) {
                                    triggerEvents = ['change.' + pluginName, 'blur.' + pluginName].join(' ');
                                }
                                
                                thisInput.data(pluginName, $.extend(validifyData, {
                                    eventID : new Date().getTime() + index,
                                    parentForm: thisForm,

                                    validate: function() {
                                        var _this = this;
                                        debugMsg.call(thisForm, thisInput);
                                        
                                        $.extend(_this, validateInput(thisInput, thisForm));
                                        reflectValidity(thisInput, thisForm);

                                        debugMsg.call(thisForm, ' ');
                                        
                                        thisInput.trigger('validate');
                                        thisForm.data(pluginName).refresh();
                                        
                                        return _this;
                                    },
                                    
                                    activate: function() {
                                        var _this = this;

                                        _this.deactivate();
                                        
                                        thisInput.on(triggerEvents, function() {
                                            clearTimeout(_this.timer);
                                            
                                            /**
                                             * validate() is run in an anonymous function, so that the
                                             * scope of the `this` keyword in the validate() function
                                             * will be preserved as the input plugin object.
                                             *
                                             * If you were to assign the function straight to the timeout
                                             * by using setTimeout(_this.validate, 250), the `this`
                                             * keyword becomes the Window object, which is NOT what we want.
                                             */
                                            _this.timer = setTimeout(function() {
                                                if(thisInput.attr('type') === 'radio' || thisInput.attr('type') === 'checkbox') {
                                                    allInputs.filter('[name="' + thisInput.attr('name') + '"]').each(function() {
                                                        $(this).data('validify').validate();
                                                    });
                                                } else {
                                                    _this.validate();
                                                }
                                            }, options.validationTimerDuration);
                                        });
                                        
                                        if(thisObjectExists(_this.$response)) {
                                            /* Trigger focus state on the response container. */
                                            thisInput.on('focus.' + pluginName, function() {
                                                _this.$response.addClass(getClassFromString('focused'));
                                            });
                                            
                                            thisInput.on('blur.' + pluginName, function() {
                                                _this.$response.removeClass(getClassFromString('focused'));
                                            });
                                        }
                                        
                                        if(thisObjectExists(_this.$groupResponse)) {
                                            /* Trigger focus state on the response container. */
                                            thisInput.on('focus.' + pluginName, function() {
                                                _this.$groupResponse.addClass(getClassFromString('focused'));
                                            });
                                            
                                            thisInput.on('blur.' + pluginName, function() {
                                                _this.$groupResponse.removeClass(getClassFromString('focused'));
                                            });
                                        }
                                        
                                        if(thisObjectExists(_this.$compareWith)) {
                                            /* Make sure that when the target node has
                                             * changed, we revalidate this node again. */
                                            _this.$compareWith.on('validate.' + pluginName, function() {
                                                _this.validate();
                                            });
                                        }
                                        
                                        if(thisObjectExists(_this.$floater)) {
                                            thisInput
                                                .on('focus.' + pluginName, function() {
                                                    _this.$floater.addClass(getClassFromString('focused'));
                                                    toggleFloater(_this);
                                                })
                                                .on('blur.' + pluginName, function() {
                                                    _this.$floater
                                                        .removeClass(getClassFromString('focused'))
                                                        .data('floater').conceal();
                                                });

                                            var mouseTarget = (_this.isInHolder) ? thisInput.parent('.field-holder') : thisInput;

                                            mouseTarget
                                                .on('mouseenter.' + pluginName, function() {
                                                    _this.$floater.addClass(getClassFromString('hovered'));
                                                    toggleFloater(_this);
                                                })
                                                .on('mouseleave.' + pluginName, function() {
                                                    _this.$floater.removeClass(getClassFromString('hovered'));
                                                    if(_this.$floater.hasClass(getClassFromString('focused'))) return;
                                                    _this.$floater.data('floater').conceal();
                                                });
                                        }
                                        
                                        return _this;
                                    },
                                    
                                    deactivate: function() {
                                        var _this = this;
                                    
                                        thisInput.off('.' + pluginName);
                                        
                                        if(thisObjectExists(_this.$compareWith)) {
                                            _this.$compareWith.off('validate.' + pluginName);
                                        }
                                        
                                        return _this;
                                    },
                                    
                                    forceValidate: function() {
                                        this.set('valid', true).set('error', false);
                                        clearTimeout(this.timer);
                                        reflectValidity(thisInput, thisForm);
                                        return this;
                                    },
                                    
                                    forceInvalidate: function(errorCode) {
                                        this.set('valid', false).set('error', errorCode);
                                        clearTimeout(this.timer);
                                        reflectValidity(thisInput, thisForm);
                                        return this;
                                    },

                                    forceNeutral: function() {
                                        this.set('valid', 'neutral').set('error', false);
                                        clearTimeout(this.timer);
                                        reflectValidity(thisInput, thisForm);
                                        return this;
                                    },
                                    
                                    excludeFromValidation: function() {
                                        return this.set('required', false);
                                    },
                                    
                                    includeInValidation: function() {
                                        return this.set('required', true);
                                    },

                                    destroy: function() {
                                        thisInput.removeData(pluginName);
                                    },

                                    set: function(key, value) {
                                        this[key] = value;
                                        return this;
                                    }
                                }, inputPluginObj));
                            });
                            
                            allInputs.each(function() {
                                var thisInput = $(this),
                                    dataObj = thisInput.data(pluginName);
                                
                                if(typeof dataObj.valid === 'undefined' || dataObj.valid === null || dataObj.valid === 'neutral') {
                                    dataObj.validate();
                                } else {
                                    dataObj.originalState = {
                                        val : thisInput.val(),
                                        valid : dataObj.valid,
                                        error : dataObj.error
                                    };
                                }
                                
                                reflectValidity(thisInput, thisForm);
                                
                                dataObj.activate();
                            });
                        });
                    },
                    
                    activate: function() {
                        runElemMethod.call(this, pluginName, 'activate');
                    },
                    
                    deactivate: function() {
                        runElemMethod.call(this, pluginName, 'deactivate');
                    },
                    
                    forceValidate: function() {
                        runElemMethod.call(this, pluginName, 'forceValidate');
                    },
                    
                    forceInvalidate: function() {
                        runElemMethod.call(this, pluginName, 'forceInvalidate');
                    },
                    
                    forceNeutral: function() {
                        runElemMethod.call(this, pluginName, 'forceNeutral');
                    },
                    
                    excludeFromValidation: function() {
                        runElemMethod.call(this, pluginName, 'excludeFromValidation');
                    },
                    
                    includeInValidation: function() {
                        runElemMethod.call(this, pluginName, 'includeInValidation');
                    },
                    
                    destroy: function() {
                        runElemMethod.call(this, pluginName, 'destroy');
                    },

                    set: function() {
                        runElemMethod.call(this, pluginName, 'set', arguments);
                    }
                },
                _arguments = arguments;
            
            for(var i = _arguments.length-1; i > -1; i--) {
                if(typeof _arguments[i] === 'object') $.extend(options, _arguments[i]);
            }
            
            if(typeof _arguments[0] === 'string' && typeof methods[_arguments[0]] === 'function') {
                methods[_arguments[0]].call(this);
            } else {
                methods.create.call(this);
            }
            
            function validateInput(targetInput, targetForm) {
                var dataObj = targetInput.data(pluginName),
                    formDataObj = targetForm.data(pluginName),
                    inputValue = targetInput.val();
                
                if(targetInput.is(options.selectors.checkButtons)) {
                    debugMsg.call(targetForm, 'Input is a :checked element (radio button / checkbox.)');
                    
                    /**
                     * If the node is in a group, then see if we need to
                     * validate it any differently.
                     */
                    if(thisObjectExists(dataObj.group)) {
                        debugMsg.call(targetForm, ':checked element is in a group.');
                    
                        var inputName = targetInput.attr('name'),
                            inputGroup = dataObj.groupInputs.filter('[name="' + inputName + '"]'),
                            minChecked = dataObj.minChecked,
                            maxChecked = dataObj.maxChecked;
                        
                        /**
                         * If a maximum amount of checked nodes has been
                         * specified, then disable the unchecked boxes when
                         * the limit is reached.
                         */
                        var maxChecked = getDataAttr(dataObj.group, 'validify-max-checked');
                            
                        if(typeof maxChecked === 'number') {
                            targetInput.on('change.' + pluginName, function() {
                                var isMaxed = (inputGroup.filter(':checked').length >= maxChecked);
                            
                                inputGroup.not(':checked').prop('disabled', isMaxed);
                            });
                        }
                        
                        /**
                         * If a minimum amount is specified, then see how
                         * many checked nodes are there. If it doesn't meet
                         * the requirements, then immediately fail this input.
                         */
                        if(typeof minChecked === 'number') {
                            debugMsg.call(targetForm, [
                                indentStr(0) + 'The group requires at least ' + minChecked + ':checked elements.',
                                indentStr(1) + 'Number of :checked elements in the group: ' + inputGroup.filter(':checked').length
                            ]);
                        
                            if(inputGroup.filter(':checked').length < minChecked) {
                                debugMsg.call(targetForm, indentStr(2) + 'Group does not meet the minimum number of :checked elements.');
                            
                                if(formDataObj.strict) {
                                    debugMsg.call(targetForm, indentStr(3) + 'Strict mode on. Input fails validation.', 'error');
                                    return { valid: false, error: 'min-checked' };
                                } else {
                                    debugMsg.call(targetForm, indentStr(3) + 'Strict mode off. Input is in neutral validation.', 'neutral');
                                    return { valid: 'neutral', error: false };
                                }
                            } else {
                                debugMsg.call(targetForm, indentStr(2) + 'Group meets the minimm number of :checked elements.');
                            }
                        }
                    }

                    if(!targetInput.is(':checked')) {
                        debugMsg.call(targetForm, [
                            ":checked element isn't checked.",
                            indentStr(0) + 'Validation type: required.'
                        ]);

                        if(typeof dataObj.required === 'boolean' && dataObj.required === true) {
                            debugMsg.call(targetForm, indentStr(1) + ":checked element is required.");
                            
                            debugMsg.call(targetForm, indentStr(2) + 'Check to see if any other buttons in its group has been checked.');

                            if(targetForm.data('validify').allInputs.filter('[name="' + targetInput.attr('name') + '"]').is(':checked')) {
                                debugMsg.call(targetForm, indentStr(3) + 'There is a checked input in the group. Validation passes.', 'success');
                                return { valid: true, error: false };
                            } else {
                                debugMsg.call(targetForm, indentStr(3) + 'There are no checked inputs in the group.');

                                if(formDataObj.strict) {
                                    debugMsg.call(targetForm, indentStr(4) + 'Strict mode on; Validation fails.', 'error');
                                    return { valid: false, error: 'required' };
                                } else {
                                    debugMsg.call(targetForm, indentStr(4) + 'Strict mode off; node is in neutral validation.', 'neutral');
                                    return { valid: 'neutral', error: false };
                                }
                            }
                        } else {
                            debugMsg.call(targetForm, indentStr(1) + ":checked element isn't required.");
                            debugMsg.call(targetForm, indentStr(2) + "Neutral validation.", 'neutral');

                            return { valid: 'neutral', error: false };
                        }
                    }

                    debugMsg.call(targetForm, ":checked element is checked.");
                } else {
                    if(typeof dataObj.originalState !== 'undefined') {
                        debugMsg.call(targetForm, "Input has an original state cached, checking to see if its value matches its original state.");
                        if(inputValue === dataObj.originalState.val) {
                            debugMsg.call(targetForm, indentStr(1) + "Value matches its original state. Returning to how it was.");
                            return { valid: dataObj.originalState.valid, error: dataObj.originalState.error };
                        }
                    }

                    if(inputValue === '') {
                        debugMsg.call(targetForm, [
                            "Input is empty.",
                            indentStr(0) + 'Validation type: required.'
                        ]);
                    
                        /**
                         * If required, and field is blank, it should only fail validation
                         * when the form is about to be submitted. Otherwise, it should
                         * just pass off as neutral. This is because when the user is still
                         * filling the form, it is likely that they are aware of the required
                         * field being empty.
                         *
                         * `Strict Mode` is enabled when you call `.set('strict', true)` on the form
                         * data object.
                         */
                        if(typeof dataObj.required === 'boolean' && dataObj.required === true) {
                            if(formDataObj.strict) {
                                debugMsg.call(targetForm, indentStr(1) + 'Input is required. Strict mode on; input has failed validation', 'error');
                                return { valid: false, error: 'required' };
                            } else {
                                debugMsg.call(targetForm, indentStr(1) + 'Input is required. Strict mode off; input is in neutral validation', 'neutral');
                                return { valid: 'neutral', error: false };
                            }
                        } else {
                        /* If not required, then the blank field should pass as neutral validation. */
                            debugMsg.call(targetForm, indentStr(1) + "Input isn't required. Neutral validation.", 'neutral');
                            return { valid: 'neutral', error: false };
                        }
                    } else if(thisObjectExists(dataObj.$compareWith)) {
                        debugMsg.call(targetForm, "Input needs to be compared to: " + dataObj.compareWith);
                        
                        var targetComparison = dataObj.$compareWith;
                        /**
                         * I believe it is beneficial to be very strict with
                         * comparison nodes: if the script can't find the
                         * desired node to compare to, then throw an error
                         * so that we will know that this needs to be fixed.
                         */
                        if(targetComparison.length < 1) {
                            throw new Error('$.validify(): A target for comparison has been provided, but selection has returned with nothing. Please check your spelling, or make sure both this input and the target comparison input exist on the same page at the same time.');
                        }
                        
                        if(targetComparison.data(pluginName).valid !== true) {
                            debugMsg.call(targetForm, indentStr(0) + "The input to compare with hasn't passed validation yet. Neutral validation.", 'neutral');
                            return { valid: 'neutral', error: false };
                        } else if(targetInput.val() !== targetComparison.val()) {
                            debugMsg.call(targetForm, indentStr(0) + "Comparison failed. Input has failed validation.", 'error');
                            return { valid: false, error: 'compare-with' };
                        }
                    /* If the input is NOT validated through a comparison,
                     * then we proceed to attach the regular validation. */
                    } else {
                        debugMsg.call(targetForm, "Input isn't empty.");
                    
                        /**
                         * If a pattern has been provided, then we will need to
                         * test it against the corresponding regex pattern.
                         *
                         * If the pattern value is a string, then we will obtain
                         * the regex pattern from the function's default options.
                         * Otherwise, if the pattern value is an object, then
                         * we simply test with it.
                         */
                        if(typeof dataObj.pattern === 'string') {
                            debugMsg.call(targetForm, [
                                indentStr(0) + 'Validation type: pattern.',
                                indentStr(1) + 'Pattern provided: ' + dataObj.pattern
                            ]);
                            
                            var regExp;
                        
                            regExp = getRegexFromString(dataObj.pattern);

                            if(typeof regExp === 'string') {
                                var patternArray = dataObj.pattern.split('/');

                                if(patternArray.length > 2) {
                                    regExp = new RegExp(patternArray[1], patternArray[2]);
                                } else {
                                    regExp = new RegExp(dataObj.pattern);
                                }
                            }

                            if(typeof regExp === 'object') {
                                if(regExp.test(inputValue)) {
                                    debugMsg.call(targetForm, indentStr(1) + "Input value matches the requierd pattern.", 'success');
                                } else {
                                    debugMsg.call(targetForm, indentStr(1) + "Input value doesn't match the required pattern. Input has failed validation.", 'error');
                                    return { valid: false, error: 'pattern' };
                                }
                            } else {
                                debugMsg.call(targetForm, indentStr(1) + "A pattern has been provided, but it doesn't seem to result in a valid RegExp object. Please check again.", 'error');
                            }
                        }
                        
                        /**
                         * Similar to above, this should only fail validation
                         * when the form is about to be submitted, hence the
                         * use of `Strict Mode`.
                         */
                        if(typeof dataObj.minlength === 'number') {
                            debugMsg.call(targetForm, indentStr(0) + 'Validation type: minlength.');
                        
                            var minlengthNum = parseInt(dataObj.minlength, 10);
                        
                            if(inputValue.length < minlengthNum) {
                                if(formDataObj.strict) {
                                    debugMsg.call(targetForm, indentStr(1) + 'Input value hasn\'t reached the minimum length. Strict mode on; input has failed validation.', 'error');
                                    return { valid: false, error: 'minlength' };
                                } else {
                                    debugMsg.call(targetForm, indentStr(1) + 'Input value hasn\'t reached the minimum length. Strict mode off; input is in neutral validation.', 'neutral');
                                    return { valid: 'neutral', error: false };
                                }
                            } else {
                                debugMsg.call(targetForm, indentStr(1) + "Input value has reached the minimum length.");
                            }
                        }
                    }
                }
                
                debugMsg.call(targetForm, 'Input has passed validation.', 'success');
                return { valid: true, error: false };
            }
                                    
            function reflectValidity(targetInput, targetForm) {
                var dataObj = targetInput.data(pluginName),
                    isInGroup = thisObjectExists(dataObj.group),
                    $response = $(dataObj.$response),
                    $floater = $(dataObj.$floater),
                    $groupResponse = $(dataObj.$groupResponse);

                targetInput.add($response).add($floater).add($groupResponse).removeClass(allClasses);

                if(isInGroup) {
                    var groupStatus = true;
                
                    debugMsg.call(targetForm, 'Input is in a group. Checking through all inputs in the group.');
                    
                    dataObj.groupInputs.each(function() {
                        var thisInput = $(this),
                            thisDataObj = thisInput.data(pluginName),
                            $validifyResponses = thisInput.add($(thisDataObj.$response)).add($(thisDataObj.$floater)),
                            thisInputStatus = thisDataObj.valid,
                            thisIsCheckInput = (targetInput.is(options.selectors.checkButtons));
                        
                        if(thisInputStatus === true) {
                            if(thisIsCheckInput && !thisInput.is(':checked')) {
                                $validifyResponses.removeClass(getClassFromString('success'));
                                return;
                            }
                            $validifyResponses.addClass(getClassFromString('success'));
                        } else if(thisInputStatus === false) {
                            $validifyResponses.addClass(getClassFromString('failed'));
                            
                            if(typeof thisDataObj.error === 'string') {
                                $validifyResponses.add($groupResponse).addClass(getClassFromString(thisDataObj.error));
                            }
                            
                            groupStatus = false;
                        } else if(typeof thisInputStatus === 'undefined' || thisInputStatus === 'neutral') {
                            if(groupStatus !== false) groupStatus = 'neutral';
                        }

                        toggleFloater.call(thisInput, thisDataObj);
                    });
                    
                    if(groupStatus === true) {
                        debugMsg.call(targetForm, indentStr(0) + 'Input group has passed validation. Applying success classes onto the group response element.', 'success');
                        $groupResponse.addClass(getClassFromString('success'));
                    } else if(groupStatus === false) {
                        debugMsg.call(targetForm, indentStr(0) + 'Input group has failed validation. Applying failed classes onto the group response element.', 'error');
                        $groupResponse.addClass(getClassFromString('failed'));
                    } else if(groupStatus === 'neutral') {
                        debugMsg.call(targetForm, indentStr(0) + 'Input group has neutral validation. Group response element will not be modified.', 'neutral');
                    }
                } else {
                    var $validifyResponses = targetInput.add($response).add($floater);

                    if(dataObj.valid === false) {
                        $validifyResponses.addClass(getClassFromString('failed'));

                        if(typeof dataObj.error === 'string') {
                            $validifyResponses.addClass(getClassFromString(dataObj.error));
                        }
                    } else if(dataObj.valid === true) {
                        if(dataObj.noSuccess) {
                            debugMsg.call(targetForm, 'Input has `noSuccess` set to true. Leaving response in its neutral state.', 'neutral');
                        } else {
                        $validifyResponses.addClass(getClassFromString('success'));
                        }
                    }

                    toggleFloater.call(targetInput, dataObj);
                }
            }
            
            function getRegexFromString(string) {
                /* Helper function to help us translate the pattern
                 * attribute's string value into a regex object. */
                for(var i = options.regexes.length-1; i > -1; i--) {
                    if(string === options.regexes[i].key) return options.regexes[i].pattern;
                }
                return '';
            }
            
            function getClassFromString(string) {
                /* Helper function to help us translate the status
                 * string value into its corresponding class. */
                var returnClass;
                for(var i = options.classes.length-1; i > -1; i--) {
                    if(string === options.classes[i].key) return options.classes[i].className;
                }
                return '';
            }
            
            function getDataAttr(target, key) {
                if(typeof target === 'undefined' || target === null) return;
                
                return target.data(key, null).data(key);
            }

            function toggleFloater(dataObj) {
                var $theFloater = dataObj.$floater;
                if(typeof $theFloater === 'undefined') return;

                if($theFloater.hasClass(getClassFromString('failed'))) {
                    if($theFloater.hasClass(getClassFromString('focused')) || $theFloater.hasClass(getClassFromString('hovered'))) {
                        $theFloater.data('floater').reveal();
                    }
                } else {
                    $theFloater.data('floater').conceal();
                }
            }
            
            function indentStr(levels) {
                var indentationStrArr = [];
                
                if(typeof levels === 'number' && levels > 0) {
                    for(var i = 0, ii = levels; i < ii; i++) {
                        indentationStrArr.push('   ');
                    }
                }
                
                indentationStrArr.push('`- ');
            
                return indentationStrArr.join('');
            }
            
            function thisIsJSON(value) {
                if(typeof value === 'string') {
                    return (value.substr(0,1) === '{' && value.substr(value.length-1, value.length) === '}');
                } else {
                    return (typeof value === 'object' && value !== null && value !== undefined);
                }
            }
            
            /* Helper function to show console logs only when
             * debug mode is set to true. */
            function debugMsg(message, styles) {
                if(!this.data(pluginName).debug) return;

                switch(styles) {
                    case 'error':
                        styles = 'color: #ff8201;';
                    break;

                    case 'neutral':
                        styles = 'color: #999;';
                    break;

                    case 'success':
                        styles = 'color: #5cb21d;';
                    break;

                    case 'start':
                        styles = 'background-color: #6b3269; color: #fff;';
                    break;

                    case 'end':
                        styles = 'background-color: #444; color: #fff;';
                    break;

                    default:
                        styles = '';
                    break;
                }

                if(message instanceof Array) {
                    while(message.length) {
                        if(typeof message[0] === 'string') {
                            console.log('%c' + message[0], styles);
                        } else {
                            console.log(message[0]);
                        }
                        message.shift();
                    }
                } else {
                    if(typeof message[0] === 'string') {
                        console.log('%c' + message, styles);
                    } else {
                        console.log(message);
                    }
                }
            }

            function typeCast(keyValueObj) {
                $.each(keyValueObj, function(key, value) {
                    switch(key) {
                        case 'valid':
                        case 'required':
                            keyValueObj[key] = (value === 'true') ? true : false;
                        break;

                        case 'minlength':
                        case 'maxlength':
                        case 'minchecked':
                        case 'maxchecked':
                            keyValueObj[key] = parseInt(value, 10);
                        break;
                    }
                });

                return keyValueObj;
            }
            
            return this;
            
            /* Removes all data and events related to validify. */
            /* function destroyValidify() {
                forms.each(function() {
                    var form = $(this),
                        allInputs = thisForm.find(options.selectors.all);
                    
                    allInputs.each(function() {
                        var currentInput = $(this);
                        
                        currentInput
                            .off('.validify')
                            .removeData('validify-status')
                            .removeClass(allClasses)
                            .prop('disabled', false); // For disabled checkboxes
                    
                        $('#' + currentInput.data('validify-response-id')).removeClass(allClasses);
                    });
                    
                    thisForm.find('fieldset').each(function() {
                        $('#' + $(this).data('validify-response-id')).removeClass(allClasses);
                    });
                    
                    thisForm.off('.validify');
                });
            } */
        /* End validify */
        }
    });
    
    function runElemMethod(pluginName, methodName, _args) {
        this.each(function() {
            var $this = $(this),
                pluginData = $this.data(pluginName);
            
            if(typeof pluginData === 'object'
            && typeof pluginData[methodName] === 'function') {
                pluginData[methodName].apply(pluginData, _args);
            }
        });
    }

    function thisObjectExists(target) {
        return (typeof target === 'object' && target !== null && target.length);
    }

    function thisDataIsValidObject($elem, dataName) {
        var elemData = $elem.data(dataName);
        return (typeof elemData === 'object' && elemData !== null && elemData !== undefined);
    }

    function convertCSVtoObject(csv) {
        if(csv === '') return {};

        var splitArray = csv.split(','),
            keyValuesArray = [],
            keyValuesObj = {};

        while(splitArray.length) {
            keyValuesArray.push($.trim(splitArray[0]).split(':'));
            splitArray.shift();
        }

        while(keyValuesArray.length) {
            keyValuesObj[$.trim(keyValuesArray[0][0])] = $.trim(keyValuesArray[0][1]);
            keyValuesArray.shift();
        }

        return keyValuesObj;
    }
})(jQuery);