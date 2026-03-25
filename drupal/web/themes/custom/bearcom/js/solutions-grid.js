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

  function shouldBeGrid(section) {
    var cardCount = parseInt(section.getAttribute('data-card-count')) || 0;
    var perView = getPerView();
    return cardCount <= perView;
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

  function destroyAndGrid(container) {
    if (container.swiper) {
      container.swiper.destroy(true, true);
    }
    container.removeAttribute('style');
    var wrapper = container.querySelector('.swiper-wrapper');
    if (wrapper) {
      wrapper.removeAttribute('style');
      wrapper.style.display = 'grid';
      wrapper.style.gridTemplateColumns = 'repeat(' + getPerView() + ', 1fr)';
      wrapper.style.gap = getSpaceBetween() + 'px';
      wrapper.style.transform = 'none';
    }
    container.querySelectorAll('.swiper-slide').forEach(function (slide) {
      slide.removeAttribute('style');
    });
  }

  function initSwiper(container, section) {
    // Re-init requires page reload, so just update params if swiper exists
    if (container.swiper) {
      var swiper = container.swiper;
      swiper.params.slidesPerView = getPerView();
      swiper.params.spaceBetween = getSpaceBetween();
      swiper.update();
      highlightCenter(swiper);
    }
  }

  Drupal.behaviors.solutionsGrid = {
    attach: function (context) {
      once('solutions-grid-init', '.solutions-grid .swiper-container', context).forEach(function (el) {
        var section = el.closest('.solutions-grid');
        var isGridMode = false;

        function update() {
          var wantGrid = shouldBeGrid(section);

          if (wantGrid && !isGridMode) {
            // Switch to grid
            section.classList.add('solutions-grid--grid');
            section.classList.remove('solutions-grid--slider');
            destroyAndGrid(el);
            isGridMode = true;
          } else if (!wantGrid && isGridMode) {
            // Need slider but Swiper was destroyed — reload needed
            // For now just update grid columns
            section.classList.remove('solutions-grid--grid');
            section.classList.add('solutions-grid--slider');
            location.reload();
          } else if (!wantGrid && !isGridMode) {
            // Slider mode — update params
            section.classList.remove('solutions-grid--grid');
            section.classList.add('solutions-grid--slider');
            if (el.swiper) {
              el.swiper.params.slidesPerView = getPerView();
              el.swiper.params.spaceBetween = getSpaceBetween();
              el.swiper.update();
              highlightCenter(el.swiper);
            }
          } else if (wantGrid && isGridMode) {
            // Stay grid, update columns
            var wrapper = el.querySelector('.swiper-wrapper');
            if (wrapper) {
              wrapper.style.gridTemplateColumns = 'repeat(' + getPerView() + ', 1fr)';
              wrapper.style.gap = getSpaceBetween() + 'px';
            }
          }
        }

        // Wait for Swiper init
        var check = setInterval(function () {
          if (el.swiper) {
            clearInterval(check);

            // Initial check
            if (shouldBeGrid(section)) {
              section.classList.add('solutions-grid--grid');
              destroyAndGrid(el);
              isGridMode = true;
            } else {
              section.classList.add('solutions-grid--slider');
              el.swiper.params.slidesPerView = getPerView();
              el.swiper.params.spaceBetween = getSpaceBetween();
              el.swiper.update();
              highlightCenter(el.swiper);

              el.swiper.on('slideChange', function () {
                highlightCenter(el.swiper);
              });
            }

            window.addEventListener('resize', update);
          }
        }, 100);
        setTimeout(function () { clearInterval(check); }, 3000);
      });
    }
  };
})(Drupal, once);
