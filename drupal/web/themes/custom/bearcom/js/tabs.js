(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.bearcomTabs = {
    attach: function (context) {
      once('bearcom-tabs', '[data-tabs]', context).forEach(function (wrapper) {
        var triggers = wrapper.querySelectorAll('[data-tab-trigger]');
        var panels = wrapper.querySelectorAll('[data-tab-panel]');
        var panelsContainer = wrapper.querySelector('.guided-journey__panels');
        var nav = wrapper.querySelector('.guided-journey__nav');
        var tabCount = triggers.length;
        var currentIndex = 0;

        function moveSlider(index) {
          if (!nav || !tabCount) return;
          var tabWidth = 100 / tabCount;
          nav.style.setProperty('--slider-left', (tabWidth * index) + '%');
          nav.style.setProperty('--slider-width', tabWidth + '%');
        }

        function activateTab(index) {
          var direction = index < currentIndex ? 'left' : 'right';
          currentIndex = index;
          triggers.forEach(function (t) { t.classList.remove('is-active'); });
          panels.forEach(function (p) {
            p.classList.remove('is-active');
            p.classList.remove('gj-animate-left');
            p.classList.remove('gj-animate-right');
          });

          if (triggers[index]) triggers[index].classList.add('is-active');
          var target = wrapper.querySelector('[data-tab-panel="' + index + '"]');
          if (target) {
            target.classList.add('is-active');
            // Trigger animation after display:block takes effect
            requestAnimationFrame(function () {
              requestAnimationFrame(function () {
                target.classList.add(direction === 'left' ? 'gj-animate-left' : 'gj-animate-right');
              });
            });
          }

          moveSlider(index);
          updateDots();
        }

        // Desktop tab triggers
        triggers.forEach(function (trigger) {
          trigger.addEventListener('click', function () {
            var index = parseInt(this.getAttribute('data-tab-trigger'));
            activateTab(index);
          });
        });

        moveSlider(0);

        // Mobile: dots + touch swipe
        var mobileNav = null;
        var dots = [];

        function createMobileNav() {
          if (mobileNav || tabCount <= 1) return;

          mobileNav = document.createElement('div');
          mobileNav.className = 'guided-journey__mobile-nav';

          var dotsWrap = document.createElement('div');
          dotsWrap.className = 'guided-journey__mobile-dots';

          for (var i = 0; i < tabCount; i++) {
            var dot = document.createElement('button');
            dot.className = 'guided-journey__mobile-dot';
            if (i === 0) dot.classList.add('is-active');
            dot.setAttribute('aria-label', 'Tab ' + (i + 1));
            dot.dataset.index = i;
            dot.addEventListener('click', function () {
              activateTab(parseInt(this.dataset.index));
            });
            dots.push(dot);
            dotsWrap.appendChild(dot);
          }

          mobileNav.appendChild(dotsWrap);

          if (panelsContainer) {
            panelsContainer.after(mobileNav);
          }

          // Touch swipe detection
          var touchStartX = 0;
          var touchEndX = 0;
          var threshold = 50;

          panelsContainer.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].screenX;
          }, { passive: true });

          panelsContainer.addEventListener('touchend', function (e) {
            touchEndX = e.changedTouches[0].screenX;
            var diff = touchStartX - touchEndX;

            if (Math.abs(diff) > threshold) {
              if (diff > 0 && currentIndex < tabCount - 1) {
                activateTab(currentIndex + 1);
              } else if (diff < 0 && currentIndex > 0) {
                activateTab(currentIndex - 1);
              }
            }
          }, { passive: true });
        }

        function updateDots() {
          dots.forEach(function (dot, i) {
            dot.classList.toggle('is-active', i === currentIndex);
          });
        }

        createMobileNav();
      });
    }
  };

})(Drupal, once);
