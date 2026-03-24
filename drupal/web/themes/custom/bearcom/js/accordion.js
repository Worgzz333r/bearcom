(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.accordion = {
    attach: function (context) {
      once('accordion', '.accordion__trigger', context).forEach(function (trigger) {
        trigger.addEventListener('click', function () {
          var item = trigger.closest('.accordion__item');
          var isOpen = item.classList.contains('is-open');

          item.classList.toggle('is-open');
          trigger.setAttribute('aria-expanded', !isOpen);
        });
      });
    }
  };
})(Drupal, once);
