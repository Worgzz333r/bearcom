(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.relatedCarousel = {
    attach(context) {
      once('related-carousel', '[data-related-carousel]', context).forEach(function (carousel) {
        var slides = carousel.querySelector('[data-slides]');
        var prev = carousel.querySelector('[data-prev]');
        var next = carousel.querySelector('[data-next]');
        if (!slides) return;

        var items = slides.children;
        var offset = 0;

        function getVisible() {
          var w = window.innerWidth;
          if (w <= 519) return 1;
          if (w <= 767) return 2;
          return 3;
        }

        function update() {
          var item = items[0];
          if (!item) return;
          var gap = parseFloat(getComputedStyle(slides).gap) || 0;
          var itemWidth = item.offsetWidth + gap;
          slides.style.transform = 'translateX(-' + (offset * itemWidth) + 'px)';
        }

        if (prev) {
          prev.addEventListener('click', function () {
            if (offset > 0) { offset--; update(); }
          });
        }

        if (next) {
          next.addEventListener('click', function () {
            var maxOffset = Math.max(0, items.length - getVisible());
            if (offset < maxOffset) { offset++; update(); }
          });
        }
      });
    }
  };
})(Drupal, once);
