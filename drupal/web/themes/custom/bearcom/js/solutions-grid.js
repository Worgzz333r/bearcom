(function (Drupal, once) {
  'use strict';

  function getPerView() {
    var w = window.innerWidth;
    if (w < 650) return 1;
    if (w < 1200) return 2;
    return 3;
  }

  function getSpaceBetween() {
    var w = window.innerWidth;
    if (w < 650) return 16;
    if (w < 1200) return 24;
    return 40;
  }

  function highlightCenter(swiper) {
    var slides = swiper.slides;
    for (var i = 0; i < slides.length; i++) {
      slides[i].classList.remove('is-center');
    }
    var perView = Math.floor(swiper.params.slidesPerView);
    if (perView < 3) return;
    var centerIndex = swiper.activeIndex + Math.floor(perView / 2);
    if (slides[centerIndex]) {
      slides[centerIndex].classList.add('is-center');
    }
  }

  function applyBreakpoints(swiper) {
    swiper.params.slidesPerView = getPerView();
    swiper.params.spaceBetween = getSpaceBetween();
    swiper.update();
    highlightCenter(swiper);
  }

  Drupal.behaviors.solutionsGrid = {
    attach: function (context) {
      // Slider mode (>3 cards): apply breakpoints + highlight center card
      once('solutions-grid-slider', '.solutions-grid--slider .swiper-container', context).forEach(function (el) {
        var check = setInterval(function () {
          if (el.swiper) {
            clearInterval(check);
            var swiper = el.swiper;

            applyBreakpoints(swiper);

            swiper.on('slideChange', function () {
              highlightCenter(swiper);
            });

            window.addEventListener('resize', function () {
              applyBreakpoints(swiper);
            });
          }
        }, 100);
        setTimeout(function () { clearInterval(check); }, 3000);
      });
    }
  };
})(Drupal, once);
