(function ($) {
  Drupal.behaviors.CultureboxEmailPopup = {
    attach: function (context, settings) {
      $('span.email-popup', context).once(function () {
        var id, ids = [];

        if (window.location.href.match(/live\/export/)) {
          id = '&id=live/export';
        }
        else {
          if ($(this).data('nid') !== undefined) {
            id = '&id=' + $(this).data('nid');
          }
          else {
            id = (Drupal.settings.cultureboxSite != undefined && Drupal.settings.cultureboxSite.pageNodeNid != undefined) ? '&id=' + Drupal.settings.cultureboxSite.pageNodeNid : '';
            if (id == '') {
              id = $(this).closest('div[class*=node-]').attr('class');
              if (id != '' && id != undefined) {
                id = id.match('node\-[0-9]{1,20}');
                id = id[0].replace('node\-', '');
                id = (id != '' && id != undefined)
                        ? '&id=' + id : '';
              }
            }
          }
        }

        // Prevent build same html form id.
        $('[id]').each(function () {
          ids.push(this.id);
        });

        // Activate fancybox ajax form for element by click,
        $(this).fancybox({
          'type': 'ajax',
          'href': Drupal.settings.basePath + 'culturebox/ajax/send-mail?email_popup_form=1' + id,
          'overlayOpacity': 0.4,
          'overlayColor': '#000',
          'showCloseButton': true,
          'scrolling': 'none',
          'padding': 0,
          'centerOnScroll': true,
          'ajax': {
            'type': "POST",
            // Send data for form_builder to prevent generate same id via ajax.
            'data': {'ajax_html_ids': ids}
          },
          'onComplete': function () {
            window.scrollTo(window.scrollX + 1, window.scrollY + 1);
          }
        });
      });
      
      $('span.email-popup', context).once(function () {
        $(this).click(function () {
          $('#fancybox-wrap').addClass('email-popup');
        });
      });
    }
  }
})(jQuery);


