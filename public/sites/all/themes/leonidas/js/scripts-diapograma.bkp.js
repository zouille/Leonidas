(function ($) {
  Drupal.behaviors.cultureboxDiaporama = {
    attach:function (context) {
      //stickyBar
      if ($('#toolbar-nav').size()) {
        $.stickyBar('#toolbar-nav');
      }
    }
  };
})(jQuery);

var current_pic_index = 0;
function leftArrowPressed() {
  if(current_pic_index > 0) {
    var target = '#navigation_prev'+current_pic_index;
    jQuery(target).click();
    current_pic_index -= 1;
  }
  else {
    var target = '#navigation_prev'+current_pic_index;
    jQuery(target).click();
    current_pic_index = (jQuery('a.navigation_next').size() -1);
  }
}

function rightArrowPressed() {
  if(current_pic_index < (jQuery('a.navigation_next').size() -1)){
    var target = '#navigation_next'+current_pic_index;
    jQuery(target).click();
    current_pic_index += 1;
  }
  else {
    var target = '#navigation_next'+current_pic_index;
    jQuery(target).click();
    current_pic_index = 0;
  }
}

(function ($) {
  $(document).ready(
    function () {
      var msie6 = $.browser == 'msie' && $.browser.version < 7;
      if (!msie6 && $('#toolbar-nav').size()) {
        //var top = $('#toolbar').offset().top - parseFloat($('#toolbar').css('margin-top').replace(/auto/, 0));
        var first_top = null;

        $('a.navigation_next').each(
          function() {
            var current = $(this).attr('id').replace('navigation_next','');
            if(current < ($('a.navigation_next').size()-1)) {
              next = parseInt(current) + 1;
              $(this).slideto({
                target : '#photo'+next,
                speed  : 1000,
                offset: -90
              });
            }
            else {
              $(this).slideto({
                target : '#photo0',
                speed  : 1000,
                offset: -90
              });
            }

          }
        );
        $('a.navigation_prev').each(
          function() {
            var current = $(this).attr('id').replace('navigation_prev','');
            if(current > 0) {
              next = parseInt(current) -1;
              $(this).slideto({
                target : '#photo'+next,
                speed  : 1000,
                offset: -90
              });
            }
            else {
              $(this).slideto({
                target : '#photo'+($('a.navigation_next').size()-1),
                speed  : 1000,
                offset: -90
              });
            }
          }
        );


        $(document.documentElement).keyup(
          function (event) {
            evt = event;
            switch (evt.keyCode) {
              case 37:
                leftArrowPressed();
                break;
              case 39:
                rightArrowPressed();
                break;
            }
          }
        );
      }
    }
  );
})(jQuery);
