(function($){
  $(document).ready(function() {

    var $_GET = {};
    var __GET = window.location.search.substring(1).split("&");
    for(var i=0; i<__GET.length; i++) {
      var getVar = __GET[i].split("=");
      $_GET[getVar[0]] = typeof(getVar[1])=="undefined" ? "" : getVar[1];
    }
    createStoryJS({
      type: 'timeline',
      width: "100%",
      height: "100%",
      source: $_GET.base_url + '/culturebox/ajax/load-timeline/' + $_GET.id,
      css: 'css/timeline.css',
      js:  'js/timeline.js',
      lang: 'js/locale/fr.js',
      embed_id:   'timeline-embed'
    });

    setInterval(function(){
      jQuery('.content-slider').each(function(){
        var $this = jQuery(this);

        if (!$this.hasClass('enable')) {
          //if(jQuery('.content-slider').length){
          $.getScript('js/jquery.gallery.js', function () {
            jQuery('div.content-slider').gallery({
              duration: 500,
              listOfSlides: '.slider-holder > ul > li',
              circle: false,
              disableBtn: "disable",
              nextBtn: '.next',
              prevBtn: '.prev',
            });
            jQuery('div.content-slider').gallery({
              duration: 500,
              listOfSlides: '.content-slider-frame > li',
              switcher: '.slider-holder > ul > li',
              nextBtn: '.next',
              prevBtn: '.prev',
            });
            /*check slider amount*/
            $('div.content-slider').each(function(){
              var $this = $(this),
                $sliderHolder = $this.find('.slider-holder li').length,
                $sliderHolderWrap = $this.find('.slider-holder');

              if ($sliderHolder <= 4){
                $sliderHolderWrap.addClass('slider-holder-min');
                $sliderHolderWrap.parent().addClass('navHide');
              }

            });

            setTimeout(function(){
              $('.slider-item').each(function(){
                var $sliderItem = $(this);
                $sliderItem.niceScroll({
                  autohidemode: false,
                  cursorwidth: '10px',
                  cursorborder: 'none'
                });
              });
            }, 100);

            setTimeout(function(){ $this.addClass('enable'); }, 0);
          });
          //};
        }
      });
    }, 1000);
  });
})(jQuery);
