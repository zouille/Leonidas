(function ($) {
  Drupal.behaviors.cultureboxMenuJs = {
    attach: function (context) {
      var primaryActive = $('#wrapper > header > div > ul > li.active', context).data('mlid');
      var path = document.location.pathname;

      if (path !== "/" && primaryActive !== undefined) {
        $.getJSON("/culturebox-menu/submain/" + primaryActive, function (data) {
         if (data.html !== '') {
           $('#wrapper header nav div ul li', context).each(function () {
             var items = $("li[data-parent-mlid=" + $(this).data('mlid') + "]", data.html).parents('div.json').html();
             $(this).append(items);
           });
         }
        });
        
        $('body').trigger("onCbMenuLoaded");
      }
    }
  };
})(jQuery);
