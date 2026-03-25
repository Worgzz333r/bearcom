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

      // Open item by default based on data-open-index
      once('accordion-default', '.accordion[data-open-index]', context).forEach(function (accordion) {
        var index = parseInt(accordion.getAttribute('data-open-index'), 10);
        var items = accordion.querySelectorAll('.accordion__item');
        if (items[index]) {
          items[index].classList.add('is-open');
          var trigger = items[index].querySelector('.accordion__trigger');
          if (trigger) {
            trigger.setAttribute('aria-expanded', 'true');
          }
        }
      });
    }
  };
})(Drupal, once);
