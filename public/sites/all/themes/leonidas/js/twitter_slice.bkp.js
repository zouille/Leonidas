//slider for twitter block
$(window).load(function() {
  var $tempListA = $('.temp-twitter-feed .list-a');

  cultureboxFlexupdate = function(){

    $('.twitter-line .in-process').before('<div class="flexslider in-process-temp" style="visibility: hidden;"></div>');
    $('.twitter-line .in-process-temp').html($tempListA);

    $('.twitter-line .in-process-temp').flexslider({
      animation: "slide",
      direction: "vertical",
      directionNav: false,
      controlNav: false,
      slideshowSpeed: 5000,
      animationSpeed: 1000,
      pauseOnHover: true,
      slideshow: true,
      useCSS: false,
      start: function(slider){
        slider.slides.eq(slider.currentSlide).css('opacity', '1');
      },
      after: function(slider){

        if(slider.find('.flex-active-slide').length){
          $('.flex-active-slide').css('opacity', '1').prev().css('opacity', '0.5');
        }

      },
      before: function(slider){

        if(slider.find('.flex-active-slide').length){
          $('.flex-active-slide').css('opacity', '0.5');
        }

      }
    });
    $('.twitter-line .loading').show();
    $('.twitter-line .in-process').hide().remove();
    $('.twitter-line .in-process-temp').removeClass("in-process-temp").css("visibility", "visible").addClass("in-process");
    setTimeout(function(){
      $('.twitter-line .loading').hide();
    },1000);

  }

  $('.twitter-line .in-process').flexslider({
    animation: "slide",
    direction: "vertical",
    directionNav: false,
    controlNav: false,
    slideshowSpeed: 5000,
    animationSpeed: 1000,
    pauseOnHover: true,
    slideshow: true,
    useCSS: false,
    after: function(slider){

      $('.flex-active-slide').css('opacity', '1');

    },
    before: function(slider){

      $('.twitter-post').css('opacity', '0.5');

    },
    end: function(slider){

      cultureboxFlexupdate();

    }
  });

});
