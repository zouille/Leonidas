var menu_start = 0;
var mainWidgetLive = 0;

/**
 * Return get parameter by name.
 */
function getURLParameter(sParam) {
  var sPageURL = window.location.search.substring(1);
  var sURLVariables = sPageURL.split('&');
  for (var i = 0; i < sURLVariables.length; i++)
  {
    var sParameterName = sURLVariables[i].split('=');
    if (sParameterName[0] == sParam)
    {
      return sParameterName[1];
    }
  }
}
;

(function($) {
  Drupal.behaviors.CultureboxPlayer = {
    attach: function(context) {
      var encart = $("#encart-binaural", context);
      var expander = encart.find('#expander');

      if (expander.length) {
        expander.click(function() {
          encart.find('.expendable').slideToggle(450);
          encart.toggleClass("expanded");
        });
      }
    }
  }

  Drupal.behaviors.CultureboxSearchFormWidget = {
    attach: function(context) {
      // Redirect search form.
      $('.bottomactions #culturebox-search-header-search-form .btn-search').once(
        function() {
          $(this).click(function(event) {
            event.preventDefault();
            var searchUrl = Drupal.settings.basePath;
            if (Drupal.settings.cultureboxSite != undefined && Drupal.settings.cultureboxSite.baseUrl != undefined) {
              searchUrl = Drupal.settings.cultureboxSite.baseUrl + '/';
            }
            searchUrl += 'recherche/?keywords=' + $('#edit-search').attr('value');
            window.open(searchUrl, '_blank');
          });
        }
      );
      
      // Handle hidden sort elements.
      $('.search-sorting-visible').once(function () {
        var $sortingSelect = $(this);
        var $realSortingSelect = $('.exposed-hidden-sort-by');
        if ($sortingSelect.size() && $realSortingSelect.size()) {
          var $realSortingOrder = $('.exposed-hidden-sort-order');
          if ($realSortingSelect.val()) {
            // We handle select and radios sort.
            if ($sortingSelect.is('input')) {
              $sortingSelect.filter('input[value="' + $realSortingSelect.val() + '"]').attr('checked', 'checked');
            }
            else {
              $sortingSelect.val($realSortingSelect.val());
            }
          }
          $sortingSelect.change(function () {
            var $this = $(this);
            var $direction = 'ASC';
            switch ($this.val()) {
              case 'views':
              case 'field_live_published_date':
                $direction = 'DESC';
                break;
            }
            if ($realSortingOrder.size()) {
              $realSortingOrder.val($direction);
            }
            $realSortingSelect.val($this.val());

            // Trigger form submit to apply sorting.
            var $button = $realSortingOrder.parents('form').find('input[type="submit"]');
            if ($button.size()) {
              $button.click();
            }
          });
        }
      });

      // Handle lives search form. We redirect to aliased path on festival/genre change.
      if (Drupal.settings.cultureboxSearch && Drupal.settings.cultureboxSearch.livesSearchBlockSelects) {
        $('#views-exposed-form-live-search-panel-pane-live-full', context).once(function () {
          for (var selectId in Drupal.settings.cultureboxSearch.livesSearchBlockSelects) {
            $('#' + selectId, $(this)).change(function () {
              var selectedValue = $(this).val(), currentSelectId = $(this).attr('id');
              if (Drupal.settings.cultureboxSearch.livesSearchBlockSelects[currentSelectId] && Drupal.settings.cultureboxSearch.livesSearchBlockSelects[currentSelectId][selectedValue]) {
                window.location.href = Drupal.settings.cultureboxSearch.livesSearchBlockSelects[currentSelectId][selectedValue];
              }
            });
          }
        });
      }
    }
  }

  Drupal.behaviors.culturebox = {
    attach: function(context) {

      $('.min-refresh').click(function () {
        window.location.reload(true);
      });

      var selector = 'ajax-share-links';
      var $selectorBlock = $('#' + selector, context);

      $selectorBlock.once(selector, function () {
        var nid = $selectorBlock.data('nid');

        if (nid !== undefined) {
          var do_not_call_on_node_page_if_not_published = false;
          var $body = $('body', context);

          if ($body.hasClass('page-node-not-published') && $body.hasClass('page-node-' + nid)) {
            // Si on est sur la page d'un noeud qui n'est pas publié, il est inutile de charger le bloc correspondant à ce noeud.
            do_not_call_on_node_page_if_not_published = true;
          }

          if (!do_not_call_on_node_page_if_not_published) {
            $.get('/culturebox/ajax/sharing/' + nid, function (data) {
              var now = Math.floor(Date.now() / 1000);

              // On n'affiche pas le bloc si le noeud a été modifié il y a moins de 500 secondes pour éviter les problèmes entre le cache Varnish et les crawlers des réseaux sociaux.
              if (data.html !== '' && data.node_changed !== undefined && now - data.node_changed > 500) {
                // On ajoute le code HTML récupéré que l'on cache par défaut et qui sera affiché ensuite lorsqu'on aura fini tous les traitements nécessaires.
                // Afin d'éviter notamment que le nombre de partages change après avoir été affiché.
                $selectorBlock.hide();
                $selectorBlock.html(data.html);

                // On applique les fonctionnalités nécessaires sur les boutons que l'on vient de charger (ouverture en popup, formulaire e-mail...).
                Drupal.behaviors.CultureboxSocialShareButton.attach($selectorBlock);
                Drupal.behaviors.CultureboxEmailPopup.attach($selectorBlock);

                // On déclenche l'événement "onCbButtonSharing" utilisé pour XTClick.
                $('body').trigger("onCbButtonSharing");

                // On essaye de récupérer directement depuis facebook le nombre de partages.
                var url = $selectorBlock.data('url');

                if (url !== undefined) {
                  $.get('https://api.facebook.com/method/links.getStats?urls=' + url + '&format=json', function (fb) {
                    if (fb[0].total_count !== undefined && fb[0].total_count !== 0) {
                      // Comme on a pu récupérer le nombre de partages depuis facebook uniquement en JS, on ajoute aussi les comptes qu'on a en BDD pour twitter et google.
                      var shares = fb[0].total_count;

                      if (data.shares !== undefined && data.shares.shares_twitter !== undefined) {
                        shares += data.shares.shares_twitter;
                      }

                      if (data.shares !== undefined && data.shares.shares_google !== undefined) {
                        shares += data.shares.shares_google;
                      }

                      var $selectorBlockCnt = $selectorBlock.find('.share-cnt');
                      var $selectorBlockCntValue = $selectorBlockCnt.find('.share-cnt-value', data.html);
                      var $selectorBlockCntText = $selectorBlockCnt.find('.share-cnt-text', data.html);

                      if (shares > parseInt($selectorBlockCntValue.data('share-cnt-value'))) {
                        // On transforme le nombre de partages en un chiffre réduit.
                        $.getScript('https://cdnjs.cloudflare.com/ajax/libs/numeral.js/1.5.3/numeral.min.js').done(function () {
                          numeral.language('fr', {
                            delimiters: {
                              thousands: ' ',
                              decimal: ','
                            },
                            abbreviations: {
                              thousand: 'k',
                              million: 'm',
                              billion: 'b',
                              trillion: 't'
                            },
                            ordinal: function (number) {
                              return number === 1 ? 'er' : 'ème';
                            },
                            currency: {
                              symbol: '€'
                            }
                          });
                          numeral.language('fr');
                          shares = numeral(shares).format('0.[0]a');

                          if (shares != $selectorBlockCntValue.text()) {
                            $selectorBlockCntValue.text(shares);
                            $selectorBlockCnt.show();

                            if (shares > 1) {
                              $selectorBlockCntText.text('Partages');
                            }
                          }

                          $selectorBlock.show();
                        });
                      }
                      else {
                        $selectorBlock.show();
                      }
                    }
                    else {
                      $selectorBlock.show();
                    }
                  });
                }
                else {
                  $selectorBlock.show();
                }
              }
            });
          }
        }
        else {
          // Si on a forcé une URL à partir de laquelle récupérer le nombre de partages.
          var url = $selectorBlock.data('url');

          if (url !== undefined) {
            $.get('https://api.facebook.com/method/links.getStats?urls=' + url + '&format=json', function (fb) {
              if (fb[0].total_count !== undefined && fb[0].total_count !== 0) {
                // Comme on a pu récupérer le nombre de partages depuis facebook uniquement en JS, on ajoute aussi les comptes qu'on a en BDD pour twitter et google.
                var shares = fb[0].total_count;

                var $selectorBlockCnt = $selectorBlock.find('.share-cnt');
                var $selectorBlockCntValue = $selectorBlockCnt.find('.share-cnt-value');
                var $selectorBlockCntText = $selectorBlockCnt.find('.share-cnt-text');

                // On transforme le nombre de partages en un chiffre réduit.
                $.getScript('https://cdnjs.cloudflare.com/ajax/libs/numeral.js/1.5.3/numeral.min.js').done(function () {
                  numeral.language('fr', {
                    delimiters: {
                      thousands: ' ',
                      decimal: ','
                    },
                    abbreviations: {
                      thousand: 'k',
                      million: 'm',
                      billion: 'b',
                      trillion: 't'
                    },
                    ordinal: function (number) {
                      return number === 1 ? 'er' : 'ème';
                    },
                    currency: {
                      symbol: '€'
                    }
                  });
                  numeral.language('fr');
                  shares = numeral(shares).format('0.[0]a');

                  $selectorBlockCntValue.text(shares);
                  $selectorBlockCnt.show();

                  if (shares > 1) {
                    $selectorBlockCntText.text('Partages');
                  }

                  $selectorBlock.show();
                });
              }
            });
          }
        }
      });

      //Init Custom Forms
      if (typeof(initCastomForms) != 'undefined') {
        initCastomForms();
      }
      
      //Init Same Height
      if (typeof(jQuery().sameHeight) != 'undefined') {
        jQuery('ul.live-article-list').sameHeight({
          elements: '>li',
          flexible: true,
          multiLine: true
        });
      }
      
      //init gallery
      $('div.slider', context).gallery({
        duration: 500,
        listOfSlides: '.holder > .slider-list',
        circle: true,
        disableBtn: "hide",
        nextBtn: '.next',
        prevBtn: '.prev',
      });
      $('div.content-slider', context).gallery({
        duration: 500,
        listOfSlides: '.slider-holder > ul > li',
        circle: false,
        disableBtn: "disable",
        nextBtn: '.next',
        prevBtn: '.prev',
      });
      $('div.content-slider', context).gallery({
        duration: 500,
        listOfSlides: '.content-slider-frame > li',
        switcher: '.slider-holder > ul > li',
        nextBtn: '.next',
        prevBtn: '.prev',
      });

      $('#emission-endirect-slider', context).gallery({
        listOfSlides: 'ul > .endirect-slider-item',
        nextBtn: '.controls .next',
        prevBtn: '.controls .prev'
      });

      $('div.event-block', context).gallery({
        duration: 500,
        listOfSlides: '.event-slider-list > li',
        circle: false,
        disableBtn: "disable",
        nextBtn: '.next',
        prevBtn: '.prev',
      });
      $('div.event-post', context).gallery({
        duration: 500,
        listOfSlides: '.event-slider-list > li',
        circle: false,
        disableBtn: "disable",
        nextBtn: '.next',
        prevBtn: '.prev',
      });

      // lightbox pour les images grand format
      $(".seo-optim-img").click(function() {
        //au clic on retire la class déclencheuse pour éviter les multiple affichage
        $(this).removeClass('seo-optim-img');
        var that = $(this);
        var legend = $(this).find('.description').clone();
        var contentSeo = '<div id="overlay"></div><div id="modale-seo-img"><div class="close-popin">Fermer</div>';
        contentSeo += '<img src="' + $(this).attr('href') + '" title="' + $(this).find('img').attr('title') + '" /><div class="description">' + legend.html() + '</div></div>';
        $('body').prepend(contentSeo);
        $('#overlay').css('height', $('body').height());

        var modal = $('#modale-seo-img');
        var img = $('#modale-seo-img').find('img');

        if (img.width() > document.body.clientWidth) {
          img.css('width', document.body.clientWidth - 100);
        }

        modal.css({
          'left': '50%',
          'top': that.offset().top - 50,
          'margin-left': (modal.innerWidth() / 2) * -1
        });

        // Si l'image à l'intérieur de la popup n'a pas fini de charger on recentrera la popup quand elle aura fini de charger.
        if (img.width() === 0) {
          img.one('load', function() {
            modal.css('margin-left', (modal.innerWidth() / 2) * -1);
          });
        }

        $('#overlay, .close-popin').click(function() {
          modal.remove();
          $('#overlay').remove();
          // à la fermeture on remet la classe déclencheuse.
          $(that).addClass('seo-optim-img');
        });

        return false;
      });

      // Playlists.
      var pl = $('#node-playlist-full', context);
      pl.find('.ar-player:first').addClass('active');
      pl.find('.active .image').append('<div class="play-bleu"></div>');


      var playlist = pl.find('.ar-player');

      var prev = pl.find('.side-block.nav .prev');
      var next = pl.find('.side-block.nav .next');

      prev.click(function() {
        pl.find('.ar-player.active').prev().click();
      });

      next.click(function() {
        pl.find('.ar-player.active').next().click();
      });

      playlist.click(function() {
        var that = $(this);
        var nid = that.attr('data-nid');

        playlist.removeClass('active');
        playlist.find('.play-bleu').remove();
        that.addClass('active');
        that.find(' .image').append('<div class="play-bleu"></div>');

        if (nid !== undefined) {
          $.get('/ajax/reload/player/' + nid, function(response) {
            if (typeof(response.data) != 'undefined') {
              $('#article-full-main-media').html(response.data);
            }
          }, 'json');
        }
      });

      //popup add my calendar
      $('.add-my-calendar', context).css('display', 'none');
      $('.btn-plus', context).click(function() {
        $('.add-my-calendar').fadeOut();
        $('.img').removeClass("active");
        $(this).parent('.img').children('.add-my-calendar').fadeIn();
        $(this).parent('.img').addClass("active");
        return false;
      });
      $('html').not('.calendar-processed').click(function() {
        $('.add-my-calendar').fadeOut();
        $('.img').removeClass("active");
      }).addClass('calendar-processed');

      $('.btn-plus', context).click(function(event) {
        event.stopPropagation();
      });
      //end popup add my calendar
      
      // onAdLoaded est déclenché par publicite.min.js
      $('body').on('onAdLoaded', function () {
        if ($('.habillagepub').length) {
          $('body').addClass('site-habillage').addClass('site-habillage-menu');
        }
      });

      //-------
      $('.popup').once(function() {
        $('.popup').css('display', 'none');
        $('.share-link > span').once('share-link-1', function() {
          var $this = $(this);
          $this.click(function() {
            $('#averir-popup-response').html('');
            $('#culturebox-live-newsletter-form').show();
            $('.popup').fadeOut();
            $this.addClass("active").parents('.share-link').children('.popup').fadeIn();
            return false;
          });
        });
      });


      $(document).bind('mousedown', function(e) {
        e = e || event;
        if (e.button != undefined && e.button != 2) {
          var t = e.target || e.srcElement;
          t = $(t);
          if (t.parents('.popup').length == 0) {
            $('.popup').fadeOut();
            $('.share-link > .share').removeClass("active");
          }
        }
      });

      //footer position with video and twitter blocks
      $(document).ready(function() {
        if ($(".twitter-line").length) {
          $('body').addClass("with-footer-twitter");
        }
      });


      //init custom scroll
      if (typeof($().jScrollPane) != 'undefined') {
        $('.scroll-pane', context).jScrollPane();
      }

      //top nav
      $('.top-nav').hover(function() {
        $('.top-nav').addClass('no-active');
      },
        function() {
          $('.top-nav').removeClass('no-active');
        });

      // Add event onclick for article diaporama images.
      $('.article-diaporama-next').each(function() {
        $(this).click(function() {
          rightArrowPressed();
        });
      });

      $('.article-diaporama-prev').each(function() {
        $(this).click(function() {
          leftArrowPressed();
        });
      });

      if (!menu_start) {
        $('#wrapper > header > nav > ul > li').one('mouseover', function(event) {
          $this = $(this);
          setTimeout(function() {
            var submenu = $('.drop', $this);
            var offset = $this.position().left;
            var itemsWidth = 0;
            var lastRowFirstIndex = 0;
            $('li', submenu).each(function(index, elem) {
              // 9 - left and right margins. They are not counted in .width().
              itemsWidth += $(this).width() + 11;
              // If items summury width in current row is more then page width we should align this row and remember index of first elem in next row.
              if (itemsWidth / 1002 >= 1) {
                if (offset > 200) {
                  var currentRowFirstElem = $('li', submenu).eq(lastRowFirstIndex);
                  currentRowFirstElem.css("margin-left", (1002 - (itemsWidth - $(this).width() - 11)) / 2);
                  currentRowFirstElem.addClass("no-line-before");
                }
                itemsWidth = $(this).width() + 11;
                lastRowFirstIndex = index;
              }
            });

            // Alignment last row.
            var lastRowFirstElem = $('li', submenu).eq(lastRowFirstIndex);
            if (offset - (itemsWidth / 2) > 0) {
              if ((offset - (itemsWidth / 2) + itemsWidth) > 1002) {
                lastRowFirstElem.css("margin-left", 1002 - itemsWidth);
              }
              else {
                lastRowFirstElem.css("margin-left", offset - (itemsWidth / 2));
              }
            }
            else {
              if (lastRowFirstIndex != 0) {
                lastRowFirstElem.css("margin-left", (1002 - itemsWidth) / 2);
              }
            }
            lastRowFirstElem.addClass("no-line-before");
          }, 10);

        }, null);
        menu_start = 1;
      }

      // live - widget form page
      $('.codebox textarea').once(function () {
        $('input#width').focusin(function () {
          $('#myRadio5, #myRadio6, #myRadio7').removeClass('radioAreaChecked').addClass('radioArea');
          $('#w300, #w550, #w900').removeClass('radioAreaCheckedLabel');
          $('#myRadio8').addClass('radioAreaChecked');
          $('#wcustom').addClass('radioAreaCheckedLabel');
        });
        $('.settings li label, .settings li div:first-child').click(function () {
          $(this).parent('li').siblings().children('div:first-child').removeClass('radioAreaChecked').addClass('radioArea');
          $(this).parent('li').siblings().children('label').removeClass('radioAreaCheckedLabel');
        });
        $('#w300, #w550, #w900, #myRadio5, #myRadio6, #myRadio7').click(function () {
          $('#myRadio8').removeClass('radioAreaChecked').addClass('radioArea');
          $('#wcustom').removeClass('radioAreaCheckedLabel');
        });
        $('#widgetform .source select').focusin(function () {
          $('#myRadio0, #myRadio1, #myRadio2').removeClass('radioAreaChecked').addClass('radioArea');
          $('#source1, #source2, #source3').removeClass('radioAreaCheckedLabel');
          $(this).parent('div').parent('.w-sel').siblings('.radioArea').addClass('radioAreaChecked');
          $(this).parent('div').parent('.w-sel').siblings('label').addClass('radioAreaCheckedLabel');
        });

        var baseUrl = 'http://' + window.location.hostname;
        if (Drupal.settings.cultureboxSite != undefined && Drupal.settings.cultureboxSite.baseUrl != undefined) {
          baseUrl = Drupal.settings.cultureboxSite.baseUrl;
        }
        var url_widget = baseUrl + '/resultats/widgets/external.html';
        var tmpselectedid = $('#livesource option:selected').attr('value');
        var tmpfirst = '<iframe src="' + url_widget + '?source_type=live&id=' + tmpselectedid + '&player=simple&width=530&height=300&size=auto" width="550" height="520" onload="this.style.height = this.contentWindow.document.body.scrollHeight + \'px\';" frameborder="0" scrolling="no"></iframe>';
        $('#textcopybox').attr('readonly', 'readonly').val(tmpfirst);
        $('.widgetpreview').html(tmpfirst);
        $('.sbmtwidget').bind('click', function () {
          $('#textcopybox').select();
        });
        
        $('#widgetform .settings.widths > li').click(function () {
          // Quand on clique n'importe où sur la case d'une dimension, il faut que cette dimension soit sélectionnée.
          $(this).find('.radioArea').click();
        });
        
        $('#widgetform input, #widgetform select').change(function () {
          var width = $('#width').val() ? $('#width').val() : 550;
          var height = 540;
          var bonusHeight = false;
          var code_html = '';
          code_html += '<iframe src="' + url_widget + '?';
          
          // On force la sélection du radio bouton correspondant si on sélectionne directement un élément des select lists dans la partie "choix du contenu".
          $this_id = $(this).attr('id');
          
          if ($this_id === 'livesource' || $this_id === 'catalogsource' || $this_id === 'festivalsource') {
            $(this).parents('li').find('input[type="radio"]').click().change();
          }

          $('#livesource').focusin(function () {
            $('#lblunv')[0].checked = true;
          });
          $('#lblunv:checked').each(function () {
            if ($('#livesource option:selected')) {
              code_html += 'source_type=live&id=' + $('#livesource option:selected').attr('value');
            }
            else {
              code_html += 'source_type=live';
            }
          });
          $('#catalogsource').focusin(function () {
            $('#lblunv2')[0].checked = true;
          });

          $('#lblunv2:checked').each(function () {
            if ($('#catalogsource option:selected')) {
              code_html += 'source_type=catalog&id=' + $('#catalogsource option:selected').attr('value');
            }
            else {
              code_html += 'source_type=catalog';
            }
          });
          $('#festivalsource').focusin(function () {
            $('#lblunv3')[0].checked = true;
          });
          $('#lblunv3:checked').each(function () {
            if ($('#festivalsource option:selected')) {
              code_html += 'source_type=festival&id=' + $('#festivalsource option:selected').attr('value');
            }
            else {
              code_html += 'source_type=festival';
            }
          });
          
          $('#lblplayer:checked').each(function () {
            code_html += '&player=simple';
          });
          $('#lblplayer2:checked').each(function () {
            code_html += '&player=advanced';
            bonusHeight = true;
          });
          
          $('.lbloptautoplay .checkboxArea').each(function () {
            code_html += '&autoplay=false';
          });
          
          if ($('#lbloptlang option:selected').attr('value') == 'en') {
            code_html += '&language=en';
          }
          
          if ($('#lbloptcolor option:selected').attr('value') == 'black') {
            code_html += '&color=black';
          }

          $('#lblsize:checked').each(function () {
            code_html += '&width=280';
            code_html += '&height=160';
            width = 300;
            height = bonusHeight ? 615 : 375;
          });
          $('#lblsize2:checked').each(function () {
            code_html += '&width=530';
            code_html += '&height=300';
            width = 550;
            height = bonusHeight ? 780 : 540;
          });
          $('#lblsize3:checked').each(function () {
            code_html += '&width=880';
            code_html += '&height=490';
            width = 900;
            height = bonusHeight ? 920 : 680;
          });
          $('#lblsize4:checked').each(function () {
            if (width < 300) {
              width = 300;
            }
            if (width > 1000) {
              width = 1000;
            }
            height = Math.floor(width / 1.783);
            
            code_html += '&width=' + (width - 20);
            code_html += '&height=' + height;
            
            height = bonusHeight ? height + 480 : height + 240;
            
            if (width < 400) {
              // Pour les widget < 400px, on n'affiche pas certains boutons, donc on réduit la hauteur.
              height -= 33;
            }
          });
          $('input#width').change(function () {
            $('#lblsize4')[0].checked = true;
          });
          $('input#width').keypress(function (e) {
            if (e.which == 13) {
              return false;
            }
          });
          code_html += '&size=auto" width="' + width + '" height="' + height + '" onload="this.style.height = this.contentWindow.document.body.scrollHeight + \'px\';" frameborder="0" scrolling="no"></iframe>';
          $('#textcopybox').attr('readonly', 'readonly').val(code_html);
          $('.widgetpreview').html(code_html);
          $('.btn-refresh').click(function () {
            $('.widgetpreview').html(code_html).trigger('refresh');
          });
          return false;
        });

        $('#widgetform .settings.sameheight li:first-child').prepend('<span class="overlaymask"></span>');
        $('#widgetform select').styler({
          singleSelectzIndex: 99
        });
        $('li.optgroup').click(function() {
          return false;
        });
      });

      // end
      $('.iframewidget #tab2').once(function() {
        $('.tabs-holder #tab2').addClass('seeyou');
      });
      
      // Plus/moins sur les textes à trou.
      $('body').once(function () {
        $('#ltextlive').append('<span id="mbuttonlive">.. <span class="dotted-link">plus</span></span>');
        $('#mtextlive').append('<span id="lbuttonlive"> <span class="dotted-link">moins</span></span>');

        $('#mbuttonlive').click(function () {
          $('#mtextlive').show();
          $('#mbuttonlive').hide();
        });

        $('#lbuttonlive').click(function () {
          $('#mtextlive').hide();
          $('#mbuttonlive').show();
        });
      });
      //bouton m'avertir pop-up agenda-live
      $("#festival-agenda .inform").click(function(){
    	  $(this).children(".popup").show();
      });
      $('.minisite-page-v2 #festival-agenda .slider .other .title').mouseenter(function(){
    	 $(this).hide();
    	 $(this).siblings('.slider li .date').hide();
    	 $(this).siblings('.inform').show();
      });
      $('#festival-agenda .inform').mouseleave(function(){
     	 $(this).hide();
     	 $(this).siblings('.slider li .title').show();
     	 $(this).siblings('.slider li .date').show();
       });
      $('.minisite-page-v2 #festival-agenda .slider .rpl .title').mouseenter(function(){
     	 $(this).hide();
     	 $(this).siblings('.slider li .date').hide();
     	 $(this).siblings('.btn-revoir').show();
       });
      $('#festival-agenda .btn-revoir').mouseleave(function(){
      	 $(this).hide();
      	 $(this).siblings('.slider li .title').show();
      	 $(this).siblings('.slider li .date').show();
        });
    }
  };
})(jQuery);

function initTabs() {
  jQuery('ul.tabs-nav, ul#tab-team-index').contentTabs({
    animSpeed: 500,
    effect: 'none'
  });

  jQuery('#tab-team-index a').css('height', jQuery('#tab-team-index').innerHeight());
}

jQuery(function () {
  // init tooltip
  jQuery('area[title]').each(function () {
    var link = jQuery(this);
    var positionData = link.attr('rel').split('-');
    var posY = positionData[0];
    var posX = positionData[1];
    link.hoverTooltip({
      positionTypeX: posX,
      positionTypeY: posY,
      attribute: 'title'
    });
  });

  //enter city
  jQuery('.city-frame').css('display', 'none');
  jQuery('.change-city').click(function() {
    jQuery('.city-frame').css('display', 'block');
    return false;
  });
  jQuery('.btn-push').click(function() {
    if (jQuery('input.text-city').val()) {
      jQuery('strong.city-place').text(jQuery('input.text-city').val());
    }
    jQuery('.city-frame').css('display', 'none');
    return false;
  });

});

jQuery(function() {
  initCarousel();
});

(function($) {
  Drupal.behaviors.cultureboxSiteEmission = {
    attach: function() {
      var ancreSummaryExtrait = $('.action-link.summary-extrait');
      var summaryExtrait = $('#summary-extrait');
      if (ancreSummaryExtrait.length && !summaryExtrait.length) {
        ancreSummaryExtrait.hide();
      }
      
      if ($(".emission-page #ar-node-fiche-emission-full + .side-block").length > 0) {
        var hh = $("#ar-node-fiche-emission-full h1").innerHeight();
        hh += $("#ar-node-fiche-emission-full h2").innerHeight();
        $(".emission-page #header-bottom .side-block").css('top', hh);
      }

      if ($(".emission-page .node-extrait-full + .side-block").length > 0) {
        var hh = $(".node-extrait-full h1").innerHeight();
        hh += $(".node-extrait-full h2").innerHeight();
        hh += $(".breadcrumb").innerHeight();
        hh -= 8;
        $(".emission-page #header-bottom .side-block:first").css('margin-top', hh);
        //$(".emission-page #header-bottom .node-extrait-full").css('margin-top', -1 * hh);
      }

      $('.expander.hidden').click(function() {
        $(this).toggleClass('hidden').toggleClass('shown');
        var expanderPrev = $(this).prev();
        expanderPrev.toggleClass('expanded');
      });

      $('.read-more').click(function() {
        var parentDiv = $(this).parent('.presentation-emission');
        parentDiv.find('.show').hide();
        parentDiv.find('.hide').fadeIn(400);

        parentDiv.find('.desc').toggleClass('hide');
        parentDiv.find('.desc').toggleClass('show');

        $(this).hasClass('exp') ? $(this).find('.txt').html('Lire la suite') : $(this).find('.txt').html('Réduire');
        $(this).toggleClass('exp');
        return false;
      });
      $('.container-left .plus-info').click(function() {
        var parentDiv = $(this).parent('.container-left');
        parentDiv.find('.content-text.show').hide();
        parentDiv.find('.content-text.hide').fadeIn(400);

        parentDiv.find('.content-text').toggleClass('hide');
        parentDiv.find('.content-text').toggleClass('show');

        $(this).hasClass('exp') ? $(this).find('.txt').html('Lire la suite') : $(this).find('.txt').html('Réduire');
        $(this).toggleClass('exp');
        return false;
      });
    }
  };

  Drupal.behaviors.cultureboxSiteLivesRadioFilter = {
    attach: function() {
      $('#views-exposed-form-lives-list-live-list-tous-les-lives input[type="radio"], #views-exposed-form-lives-list-lives-by-channel input[type="radio"], #views-exposed-form-diffusions-list-diffusions-list-toutes-les-diffusions input[type="radio"], #views-exposed-form-diffusions-list-diffusions-list-all-extraits input[type="radio"], #views-exposed-form-diffusions-list-diffusions-list-all-news input[type="radio"], #views-exposed-form-votes-list-votes-list-complete input[type="radio"]').once(function() {
        $(this).bind('change', function() {
          $('input.form-submit', $(this).closest('form')).click();
        });
      });
    }
  };

  Drupal.behaviors.cultureboxSiteLivesThematicFilter = {
    attach: function() {
      $('#culturebox-live-filter-thematic-form .sub-thematic select, #culturebox-live-filter-festival-form select, #culturebox-emission-filter-emission-form select').once(function() {
        $(this).bind('change', function() {
          var tid = $('option:selected', $(this)).val();
          if (tid | 0 !== 0) {
            $('.filter-links .term-' + tid)[0].click();
          }
          else {
            tid = tid.replace('festivals\-', '');
            if (tid | 0 !== 0) {
              $('#views-exposed-form-event-terms-list-festivals-list select').val(tid);
              $('#views-exposed-form-event-terms-list-festivals-list .form-submit').click();
            }
            else {
              if ($('#views-exposed-form-event-terms-list-festivals-list select').length) {
                $('#views-exposed-form-event-terms-list-festivals-list select').val('All');
                $('#views-exposed-form-event-terms-list-festivals-list .form-submit').click();
              }
              else {
                if (tid.indexOf('communaute') !== -1) {
                  $('.filter-links .term-' + tid)[0].click();
                }
                else {
                  $('.filter-links .term-' + tid)[0].click();
                  var parent = $(this).closest('.sub-thematic').find('.selectArea').attr('class');
                  if (parent != '' && parent != undefined) {
                    parent = parent.match('parent\-[0-9]{1,20}');
                    parent = parent[0].replace('parent\-', '');
                  }
                  if (parent) {
                    $('.filter-links .term-' + parent)[0].click();
                  }
                }
              }
            }
          }
        });
      });

      $('#culturebox-live-filter-thematic-form .thematic select').once(function() {
        $(this).bind('change', function() {
          var tid = $('option:selected', $(this)).val();
          if (tid | 0 != 0) {
            $('.filter-links .term-' + tid)[0].click();
          }
          else {
            window.location.href = Drupal.settings.basePath + 'live/tous-les-lives';
          }
        });
      });
    }
  };

  Drupal.behaviors.cultureboxLiveNewsletterForm = {
    attach: function() {
      $('#culturebox-live-newsletter-form .error').once(function() {
        $(this).closest('.popup').show();
      });
    }
  };

  Drupal.behaviors.cultureboxCounter = {
    attach: function(context, settings) {
      if (settings.CultureboxLive != undefined && settings.CultureboxLive.CountdownTime != undefined) {
        $('#countdown').once(function() {
          var countdown = new Date(settings.CultureboxLive.CountdownTime);
          $('#countdown').countdown({
            until: countdown,
            timezone: settings.CultureboxLive.offset / 60,
            format: 'dHM',
            layout: '<span class="txt">bientôt</span><strong>{dn} <span>J</span></strong>' +
              '<strong>{hn} <span>H</span></strong><strong>{mn} <span>m</span></strong>'
          });
        });
      }
      else if ($('.countdown', context).length) {
        $('#countdown').once(function() {
          var countdown = $('.countdown', context).html();
          var offset = $('#countdown .offset', context).html();
          countdown = new Date(countdown);
          $('#countdown').countdown({
            until: countdown,
            timezone: offset / 60,
            format: 'dHM',
            layout: '<span class="txt">bientôt</span><strong>{dn} <span>J</span></strong>' +
              '<strong>{hn} <span>H</span></strong><strong>{mn} <span>m</span></strong>'
          });
        });
      }
    }
  };

  Drupal.behaviors.CultureboxVideoBonusPopup = {
    attach: function(context, settings) {
      $('a.video-bonus', context).once('video-bonus', function() {
        var $this = $(this);
        $this.click(function(event) {
          if (event.preventDefault) {
            event.preventDefault();
          }
          else {
            return false;
          }
        });
        $this.fancybox({
          'type': 'ajax',
          'href': $(this).attr('href'),
          'overlayOpacity': 0.4,
          'overlayColor': '#000',
          'showCloseButton': true,
          'scrolling': 'none',
          'padding': 0,
          'centerOnScroll': true,
          'onComplete': function() {
            window.scrollTo(window.scrollX + 1, window.scrollY + 1);
          }
        });
      });
    }
  }

  Drupal.behaviors.CultureboxVideoBonusShowPopup = {
    attach: function(context, settings) {
      var aid = getURLParameter('bonus');
      if (aid) {
        var $bonus = $('.video-bonus-processed.video-' + aid);
        setTimeout(function() {
          $('#fancybox-loading').once(function() {
            $bonus.click();
          });
        }, 0);
      }
    }
  }

  Drupal.behaviors.CultureboxLiveTrailer = {
    attach: function(context) {
      var button = $('.video-container', context).find('.video-button');

      button.find('.btn-a-wrapper').once(function() {
        button.find('.btn-a-wrapper').find('a.btn-a').click(function() {
          button.find('.live-image').hide();
          button.find('.btn-a-wrapper').hide();
          button.find('.counter').hide();
          button.find('.delay-message').hide();
          button.find('.trailer').show();
        });
      });

      var iframe = $('.iframewidget, .minisite-page-v2 .stream', context);
      iframe.find('.btn-a-wrapper').once(function() {
        iframe.find('.btn-a-wrapper').find('a.btn-a').click(function() {
          iframe.find('.live-image').hide();
          iframe.find('.btn-a-wrapper').hide();
          iframe.find('.counter').hide();
          iframe.find('.delay-message').hide();
          iframe.find('.trailer').show();
        });
      });
    }
  };

  Drupal.behaviors.CultureboxLiveWidget = {
    attach: function() {
      $('.gmask a.widget-player-link').once(function() {
        $(this).click(function(event) {
          if (event.preventDefault) {
            event.preventDefault();
          }
          else {
            return false;
          }
          var nid = $(this).closest('div.node-live').attr('class');
          var $nodeWrapper = $('.widget-main-live');
          var $reagir = $('a.widget-reagir');
          var width = 550;

          nid = nid.match(/node-\d+/);
          nid = nid[0].replace(/node-/, '');

          if (mainWidgetLive == 0) {
            mainWidgetLive = $('.node-live', $nodeWrapper).attr('id');
            mainWidgetLive = mainWidgetLive.replace(/node-/, '');
          }
          if ($('.invalid_media', $nodeWrapper).length) {
            width = $('.invalid_media', $nodeWrapper).width();
          }
          else if ($('iframe', $nodeWrapper).length) {
            width = $('iframe', $nodeWrapper).width();
          }
          else if ($('img', $nodeWrapper).length) {
            width = $('img', $nodeWrapper).width();
          }
          else {
            width = $nodeWrapper.closest('html').width();
          }
          if (nid) {
            $.ajax({
              url: Drupal.settings.basePath + 'ajax/culturebox/live/widget/' + nid + '/' + width,
              success: function(data) {
                if (data.node !== 'undefined') {
                  $nodeWrapper.html(data.node);
                  $reagir.attr('href', Drupal.settings.basePath + 'node/' + nid + '#disqus_thread');
                  Drupal.attachBehaviors($nodeWrapper, Drupal.settings);
                }

                if (data.articles !== 'undefined') {
                  if ($('#tab2').length) {
                    $('#tab2').remove();
                  }
                  if (data.articles) {
                    $('.tabs-holder').append(data.articles);
                    $('.tabs-nav li:last').show();
                  }
                  else {
                    $('.tabs-nav li:last').hide();
                  }
                  Drupal.attachBehaviors($('#tab2'), Drupal.settings);
                  initDansActuCarousel();
                }

                if ($('#countdown').length && data.countdown !== 'undefined') {
                  var countdown = new Date(data.countdown);
                  $('#countdown').countdown('option', 'until', countdown);
                }
              }
            });
          }
        });
      });
      $('.gmask a.asset-player-link').once(function() {
        $(this).click(function(event) {
          if (event.preventDefault) {
            event.preventDefault();
          }
          else {
            return false;
          }
          var aid = $(this).closest('div.widget-asset').attr('class');
          aid = aid.match(/asset-\d+/);
          aid = aid[0].replace(/asset-/, '');

          if (mainWidgetLive == 0) {
            mainWidgetLive = $('.node-live', $('.widget-main-live')).attr('id');
            mainWidgetLive = mainWidgetLive.replace(/node-/, '');
          }
          window.open(Drupal.settings.basePath + "node/" + mainWidgetLive + "?bonus=" + aid, '_blank');
        });
      });
    }
  };

  Drupal.behaviors.CultureboxLiveWidgetExportForm = {
    attach: function() {
      $('#lblunv2, #lblunv3').once(function() {
        $(this).bind('change', function() {
          $('#lblplayer2').prev('.radioArea').click();
          $('#lblplayer').closest('li').addClass('radioAreaDisabled');
        });
      });
      $('#lblunv').once(function() {
        $(this).bind('change', function() {
          $('#lblplayer').closest('li').removeClass('radioAreaDisabled');
        });
      });
    }
  };

  Drupal.behaviors.cultureboxEmissionDiffusionsDateFilter = {
    attach: function() {
      $('#culturebox-emission-filter-date-form select').once(function() {
        $(this).bind('change', function() {
          var date = $('option:selected', $(this)).val();
          $('.filter-links .date-' + date)[0].click();
        });
      });
    }
  };

  Drupal.behaviors.CultureboxDiffusionTrailer = {
    attach: function(context) {
      var videoButton = $('#ar-node-fiche-emission-full', context).find('.video-button');
      videoButton.find('.btn-a-wrapper').once(function() {
        videoButton.find('.btn-a-wrapper a.btn-a').click(function() {
          videoButton.find('.live-image').hide();
          videoButton.find('.btn-a-wrapper').hide();
          videoButton.find('.counter').hide();
          videoButton.find('.delay-message').hide();
          videoButton.find('.mask.bientot').hide();
          videoButton.find('.play').hide();
          videoButton.find('.trailer').removeClass('hide');
        });
      });
    }
  };

  Drupal.behaviors.popupculturebox = {
    attach: function (context) {
      if ($('#dialog-tabs', context).length && window.location.pathname.search('/resultats/widgets/external.html') === -1) {
        var tabletName = (function () {
          var isIpad = function () {
            var regExp = /Mozilla.*iPad.*Safari.*/i;
            return regExp.test(navigator.userAgent);
          },
                  isWindows8 = function () {
                    var regExp = /Windows NT 6\.(2|3)/i;
                    return regExp.test(navigator.userAgent);
                  },
                  isAndroidTablett = function () {
                    if (!navigator.userAgent.match(/android/i)) {
                      return false;
                    }
                    if (navigator.userAgent.match(/mobile/i)) {
                      return false;
                    }
                    return true;
                  };

          if (isIpad()) {
            return 'ipad';
          }
          if (isWindows8()) {
            return 'windows8';
          }
          if (isAndroidTablett()) {
            return 'android-tablette';
          }

          return '';
        }()),
                cultureboxRedirectTo = function (url) {
                  var location = 'http://' + window.location.hostname + window.location.pathname;
                  var params = '?referer=' + encodeURIComponent(location);
                  window.location.href = url + params;
                };

        var showPopup = $.trim(cbGetCookie('show_popup')),
                date = new Date;

        if (tabletName === 'ipad' || tabletName === 'android-tablette') {
          if (showPopup === undefined || showPopup === "") {
            $("#dialog-tabs").dialog({
              autoOpen: false,
              draggable: false,
              resizable: false,
              dialogClass: 'dialog-tablet',
              width: 944,
              height: 507,
              modal: true,
              buttons: [
                {
                  text: "CONTINUER SUR LE SITE",
                  class: 'btn-close-mobi-popup',
                  click: function () {
                    ga('send', 'event', 'button', 'click', 'close-appli');
                    $(this).dialog('close');
                  }
                },
                {
                  text: "TÉLÉCHARGER L'APPLICATION",
                  class: 'btn-appli-mobi',
                  click: function () {
                    ga('send', 'event', 'button', 'click', 'download-appli');

                    if (tabletName === 'ipad') {
                      cultureboxRedirectTo('https://itunes.apple.com/fr/app/culturebox/id648273701?mt=8');
                    }
                    else if (tabletName === 'android-tablette') {
                      cultureboxRedirectTo('https://play.google.com/store/apps/details?id=fr.francetv.culturebox&hl=fr');
                    }

                    $(this).dialog('close');
                  }
                }
              ]
            });

            var image_src = '/sites/all/themes/culturebox/images/images_popin/popin_android_v3.jpg';

            if (tabletName === 'ipad') {
              image_src = '/sites/all/themes/culturebox/images/images_popin/popin_ipad_v3.jpg';
            }

            // On rajoute l'image à afficher dans la popup.
            $('#dialog-tabs').html('<img src="' + image_src + '" width="944" height="374" />');

            $('#dialog-tabs img').load(function () {
              // On n'ouvre la popup que lorsque l'image principale a fini d'être chargée.
              $('#dialog-tabs').dialog('open');
              date.setTime(date.getTime() + 1 * 24 * 3600 * 1000);
              cbSetCookie('show_popup', 'true', date.toUTCString(), '/');

              $('body').on('click', '#dialog-tabs', function () {
                ga('send', 'event', 'button', 'click', 'download-appli');

                if (tabletName === 'ipad') {
                  cultureboxRedirectTo('https://itunes.apple.com/fr/app/culturebox/id648273701?mt=8');
                }
                else if (tabletName === 'android-tablette') {
                  cultureboxRedirectTo('https://play.google.com/store/apps/details?id=fr.francetv.culturebox&hl=fr');
                }

                $('#dialog-tabs').dialog('close');
              });
            });
          }
        }
      }
    }
  };
})(jQuery);

// scroll gallery init
function initCarousel() {
  jQuery('div#videosliees, div#danslactu').scrollGallery({
    mask: 'div.gmask',
    slider: '>ul',
    slides: '>li',
    disableWhileAnimating: true,
    btnPrev: '.video-prev',
    btnNext: '.video-next',
    circularRotation: true,
    pauseOnHover: false,
    autoRotation: false,
    maskAutoSize: false,
    switchTime: 2000,
    animSpeed: 600,
    step: 1
  });
}

// scroll gallery init
function initDansActuCarousel() {
  jQuery('div#danslactu').scrollGallery({
    mask: 'div.gmask',
    slider: '>ul',
    slides: '>li',
    disableWhileAnimating: true,
    btnPrev: '.video-prev',
    btnNext: '.video-next',
    circularRotation: true,
    pauseOnHover: false,
    autoRotation: false,
    maskAutoSize: false,
    switchTime: 2000,
    animSpeed: 600,
    step: 1
  });
}
