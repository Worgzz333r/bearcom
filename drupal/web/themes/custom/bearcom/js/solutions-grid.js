(function (Drupal, once) {
  'use strict';

  function highlightCenter(swiper) {
    var slides = swiper.slides;
    for (var i = 0; i < slides.length; i++) {
      slides[i].classList.remove('is-center');
    }
    var perView = Math.floor(swiper.params.slidesPerView);
    var centerIndex = swiper.activeIndex + Math.floor(perView / 2);
    if (slides[centerIndex]) {
      slides[centerIndex].classList.add('is-center');
    }
  }

  Drupal.behaviors.solutionsGrid = {
    attach: function (context) {
      // Slider mode (>3 cards): highlight center card
      once('solutions-grid-slider', '.solutions-grid--slider .swiper-container', context).forEach(function (el) {
        var check = setInterval(function () {
          if (el.swiper) {
            clearInterval(check);
            var swiper = el.swiper;
            highlightCenter(swiper);
            swiper.on('slideChange', function () {
              highlightCenter(swiper);
            });
            swiper.on('resize', function () {
              highlightCenter(swiper);
            });
          }
        }, 100);
        setTimeout(function () { clearInterval(check); }, 3000);
      });
    }
  };
})(Drupal, once);
