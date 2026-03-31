(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.pagerArrows = {
    attach: function (context) {
      var pagers = once('pager-arrows', '.pager__items', context);
      pagers.forEach(function (list) {
        // Add disabled previous arrow if missing
        if (!list.querySelector('.pager__item--previous')) {
          var prev = document.createElement('li');
          prev.className = 'pager__item pager__item--previous is-disabled';
          list.insertBefore(prev, list.firstChild);
        }

        // Add disabled next arrow if missing
        if (!list.querySelector('.pager__item--next')) {
          var next = document.createElement('li');
          next.className = 'pager__item pager__item--next is-disabled';
          list.appendChild(next);
        }
      });
    }
  };
})(Drupal, once);
