(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.industryCarouselFix = {
    attach: function (context) {
      // Destroy swiper on grids marked as non-slider
      once('grid-no-swiper', '.card-grid--industry.card-grid--grid .swiper-container', context).forEach(function (el) {
        var check = setInterval(function () {
          if (el.swiper) {
            clearInterval(check);
            el.swiper.destroy(false, true);
            var wrapper = el.querySelector('.swiper-wrapper');
            if (wrapper) {
              wrapper.style.display = 'grid';
              wrapper.style.gridTemplateColumns = window.innerWidth <= 767 ? '1fr' : 'repeat(3, 1fr)';
              wrapper.style.gap = '24px';
              wrapper.style.transform = 'none';

              window.addEventListener('resize', function () {
                wrapper.style.gridTemplateColumns = window.innerWidth <= 767 ? '1fr' : 'repeat(3, 1fr)';
              });
            }
          }
        }, 100);
        setTimeout(function () { clearInterval(check); }, 3000);
      });

      // Set slidesPerView and slidesPerGroup for slider card grids
      once('mobile-slider', '[class*="card-grid--industry"].card-grid--slider .swiper-container', context).forEach(function (el) {
        var check = setInterval(function () {
          if (el.swiper) {
            clearInterval(check);
            var swiper = el.swiper;

            // Fixed scrollbar drag size
            swiper.params.scrollbar.dragSize = 377;

            var isMobile = window.innerWidth <= 767;

            if (isMobile) {
              swiper.params.slidesPerView = 1;
              swiper.params.spaceBetween = 16;
            } else {
              swiper.params.slidesPerView = 3;
              swiper.params.spaceBetween = 40;
            }
            swiper.params.slidesPerGroup = 1;
            swiper.update();

            window.addEventListener('resize', function () {
              var mobile = window.innerWidth <= 767;
              if (mobile) {
                swiper.params.slidesPerView = 1;
                swiper.params.spaceBetween = 16;
              } else {
                swiper.params.slidesPerView = 3;
                swiper.params.spaceBetween = 40;
              }
              swiper.params.slidesPerGroup = 1;
              swiper.params.scrollbar.dragSize = 377;
              swiper.update();
            });
          }
        }, 100);
        setTimeout(function () { clearInterval(check); }, 3000);
      });
    }
  };
})(Drupal, once);
