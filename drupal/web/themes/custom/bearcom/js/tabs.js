(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.bearcomTabs = {
    attach: function (context) {
      once('bearcom-tabs', '[data-tabs]', context).forEach(function (wrapper) {
        var triggers = wrapper.querySelectorAll('[data-tab-trigger]');
        var panels = wrapper.querySelectorAll('[data-tab-panel]');
        var nav = wrapper.querySelector('.guided-journey__nav');
        var tabCount = triggers.length;

        function moveSlider(index) {
          if (!nav || !tabCount) return;
          var tabWidth = 100 / tabCount;
          nav.style.setProperty('--slider-left', (tabWidth * index) + '%');
          nav.style.setProperty('--slider-width', tabWidth + '%');
        }

        triggers.forEach(function (trigger) {
          trigger.addEventListener('click', function () {
            var index = parseInt(this.getAttribute('data-tab-trigger'));

            triggers.forEach(function (t) { t.classList.remove('is-active'); });
            panels.forEach(function (p) { p.classList.remove('is-active'); });

            this.classList.add('is-active');
            var target = wrapper.querySelector('[data-tab-panel="' + index + '"]');
            if (target) {
              target.classList.add('is-active');
            }

            moveSlider(index);
          });
        });

        // Init slider on first active tab
        moveSlider(0);
      });
    }
  };

})(Drupal, once);
