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
      // Grid mode (<=3 cards): destroy Swiper and display as CSS grid
      once('solutions-grid-no-swiper', '.solutions-grid--grid .swiper-container', context).forEach(function (el) {
        var check = setInterval(function () {
          if (el.swiper) {
            clearInterval(check);
            el.swiper.destroy(false, true);
            var wrapper = el.querySelector('.swiper-wrapper');
            if (wrapper) {
              wrapper.style.display = 'grid';
              wrapper.style.gridTemplateColumns = window.innerWidth <= 767
                ? '1fr'
                : window.innerWidth <= 1023
                  ? 'repeat(2, 1fr)'
                  : 'repeat(3, 1fr)';
              wrapper.style.gap = 'var(--spacing-lg)';
              wrapper.style.transform = 'none';

              window.addEventListener('resize', function () {
                wrapper.style.gridTemplateColumns = window.innerWidth <= 767
                  ? '1fr'
                  : window.innerWidth <= 1023
                    ? 'repeat(2, 1fr)'
                    : 'repeat(3, 1fr)';
              });
            }
          }
        }, 100);
        setTimeout(function () { clearInterval(check); }, 3000);
      });

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
