/*
* jQuery stickyBar Plugin
* Copyright (c) 2010 Brandon S. <antizeph@gmail.com>
* Version: 1.1.2 (09/14/2010)
* http://plugins.jquery.com/project/stickyBar
* 
* Usage (simple):      $.stickyBar(div);
* Usage (advanced):    $.stickyBar(divTarget, {'showClose' : true, 'divBase' : divBase});
* 
* Notes:    divTarget is the div you want to be stickied (and by default is also divBase).
*           divBase is the target to scroll past to invoke stickyBar.
*           showClose displays a small 'x' that closes stickyBar
*/
(function($){
    $.fn.stickyBar = function(o){
        $.stickyBar(o);
    }

    $.stickyBar = function(divTarget, options){
        var defaults = {
            'divBase'   : '',
            'showClose' : false
        };
        settings = $.extend(defaults, options);

        var wrapped = 0; //initial value
        
        //if divBase is a defined option, set the stickyBarTop value to it, otherwise, use divTarget
        divTargetBase = (settings.divBase) ? divTargetBase = settings.divBase : divTargetBase = divTarget;

        var stickyBarTop = $(divTargetBase).offset().top;
        $(window).scroll(function(){
            var scrollPos = $(window).scrollTop();

            if (scrollPos > stickyBarTop){
                if (wrapped == 0){                
                    $(divTarget).wrap('<div class="sticky">');
                    $(".sticky").css({
                                'position'    : "fixed",
                                'top'         : "0",
                                'left'        : "0",
                                'width'       : "100%",
                                'z-index'     : "9999"
                            });
                    wrapped = 1;

                    if (settings.showClose){
                        $(".sticky").append('<div class="stickyClose" style="left:95%;position:absolute;color:#fff;top:0;left:98%;cursor:pointer">x</div>');
                        $(".stickyClose").click(function(){
                            $(".sticky").slideUp();
                            setTimeout(function(){
                                $(divTarget).unwrap();
                                $(".stickyClose").remove();
                            },400);
                            wrapped = 2; //won't happen again on the page until a refresh
                        });
                    }

                }
            } else {
                if (wrapped == 1){
                    $(divTarget).unwrap();
                    $(".stickyClose").remove();
                    wrapped = 0;
                }
            }
        });
    };
}) (jQuery);

/*
 * Viewport - jQuery selectors for finding elements in viewport
 *
 * Copyright (c) 2008-2009 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *  http://www.appelsiini.net/projects/viewport
 *
 */
(function($) {
    $.belowthefold = function(element, settings) {
        var fold = $(window).height() + $(window).scrollTop();
        return fold <= $(element).offset().top - settings.threshold;
    };

    $.abovethetop = function(element, settings) {
        var top = $(window).scrollTop();
        return top >= $(element).offset().top + $(element).height() - settings.threshold;
    };
    
    $.rightofscreen = function(element, settings) {
        var fold = $(window).width() + $(window).scrollLeft();
        return fold <= $(element).offset().left - settings.threshold;
    };
    
    $.leftofscreen = function(element, settings) {
        var left = $(window).scrollLeft();
        return left >= $(element).offset().left + $(element).width() - settings.threshold;
    };
    
    $.inviewport = function(element, settings) {
        return !$.rightofscreen(element, settings) && !$.leftofscreen(element, settings) && !$.belowthefold(element, settings) && !$.abovethetop(element, settings);
    };
    
    $.extend($.expr[':'], {
        "below-the-fold": function(a, i, m) {
            return $.belowthefold(a, {threshold : 0});
        },
        "above-the-top": function(a, i, m) {
            return $.abovethetop(a, {threshold : 0});
        },
        "left-of-screen": function(a, i, m) {
            return $.leftofscreen(a, {threshold : 0});
        },
        "right-of-screen": function(a, i, m) {
            return $.rightofscreen(a, {threshold : 0});
        },
        "in-viewport": function(a, i, m) {
            return $.inviewport(a, {threshold : 0});
        }
    });
})(jQuery);
