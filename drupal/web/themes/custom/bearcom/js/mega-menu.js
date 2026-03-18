(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.megaMenu = {
    attach: function (context) {
      var triggers = once('mega-menu', '[data-mega-menu-trigger]', context);

      triggers.forEach(function (trigger) {
        var panel = trigger.querySelector('[data-mega-menu-panel]');
        if (!panel) return;

        var openTimeout, closeTimeout;
        var OPEN_DELAY = 100;
        var CLOSE_DELAY = 250;

        trigger.addEventListener('mouseenter', function () {
          clearTimeout(closeTimeout);
          // Close other open panels.
          document.querySelectorAll('[data-mega-menu-panel].is-open').forEach(function (p) {
            if (p !== panel) p.classList.remove('is-open');
          });
          openTimeout = setTimeout(function () {
            panel.classList.add('is-open');
          }, OPEN_DELAY);
        });

        trigger.addEventListener('mouseleave', function () {
          clearTimeout(openTimeout);
          closeTimeout = setTimeout(function () {
            panel.classList.remove('is-open');
          }, CLOSE_DELAY);
        });
      });
    }
  };
})(Drupal, once);
