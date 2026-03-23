(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.productGallery = {
    attach: function (context) {
      once('product-gallery', '[data-product-gallery]', context).forEach(function (gallery) {
        var slides = gallery.querySelectorAll('[data-slides] .product-gallery__slide');
        var thumbs = gallery.querySelectorAll('.product-gallery__thumb');
        var prevBtn = gallery.querySelector('[data-prev]');
        var nextBtn = gallery.querySelector('[data-next]');
        var thumbsContainer = gallery.querySelector('.product-gallery__thumbs');
        var scrollbar = gallery.querySelector('[data-scrollbar]');
        var scrollbarThumb = gallery.querySelector('[data-scrollbar-thumb]');
        var current = 0;

        function updateScrollbar() {
          if (!scrollbar || !thumbsContainer) return;
          var el = thumbsContainer;
          var ratio = el.clientHeight / el.scrollHeight;
          var thumbHeight = Math.max(ratio * scrollbar.clientHeight, 30);
          var maxScroll = el.scrollHeight - el.clientHeight;
          var scrollTop = el.scrollTop;
          var maxThumbTop = scrollbar.clientHeight - thumbHeight;
          var top = maxScroll > 0 ? (scrollTop / maxScroll) * maxThumbTop : 0;

          scrollbarThumb.style.height = thumbHeight + 'px';
          scrollbarThumb.style.top = top + 'px';
        }

        function goTo(index) {
          if (index < 0) index = slides.length - 1;
          if (index >= slides.length) index = 0;

          slides[current].classList.remove('is-active');
          thumbs[current].classList.remove('is-active');
          current = index;
          slides[current].classList.add('is-active');
          thumbs[current].classList.add('is-active');

          // Scroll thumb into view
          thumbs[current].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }

        thumbs.forEach(function (thumb) {
          thumb.addEventListener('click', function () {
            goTo(parseInt(this.getAttribute('data-slide'), 10));
          });
        });

        if (prevBtn) prevBtn.addEventListener('click', function () { goTo(current - 1); });
        if (nextBtn) nextBtn.addEventListener('click', function () { goTo(current + 1); });

        if (thumbsContainer && scrollbar) {
          thumbsContainer.addEventListener('scroll', updateScrollbar);
          updateScrollbar();
        }
      });
    }
  };
})(Drupal, once);
