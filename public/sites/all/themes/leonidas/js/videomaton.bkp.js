(function($) {
  Drupal.behaviors.CultureboxVideomaton = {
    attach: function(context, settings) {
      var $container = $('#container'),
        filters = {};

      $container.isotope({
        itemSelector: '.avignonoff',
        layoutMode: 'cellsByRow',
        cellsByRow: {
          columnWidth: 220,
          rowHeight: 315
        }
      });

      // filter buttons
      $('.filter a').click(function() {
        var $this = $(this);
        // don't proceed if already selected
        if ($this.hasClass('selected')) {
          return;
        }

        var $optionSet = $this.parents('.option-set');
        // change selected class
        $optionSet.find('.selected').removeClass('selected');
        $this.addClass('selected');

        // store filter value in object
        // i.e. filters.color = 'red'
        var group = $optionSet.attr('data-filter-group');
        filters[ group ] = $this.attr('data-filter-value');
        // convert object into array
        var isoFilters = [];
        for (var prop in filters) {
          isoFilters.push(filters[ prop ])
        }
        var selector = isoFilters.join('');
        $container.isotope({filter: selector});

        return false;
      });

      $('.video').magnificPopup({
        type: 'iframe',
        iframe: {
          markup: '<div class="mfp-iframe-scaler">' +
            '<div class="mfp-close"></div>' +
            '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
            '</div>', // HTML markup of popup, `mfp-close` will be replaced by the close button
        }
      });
    }
  }
})(jQuery);
