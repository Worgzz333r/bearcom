(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.searchExpand = {
    attach: function (context) {
      var toggles = once('search-expand', '[data-search-toggle]', context);

      toggles.forEach(function (toggle) {
        var field = document.querySelector('[data-search-field]');
        if (!field) return;

        toggle.addEventListener('click', function (e) {
          e.preventDefault();
          field.classList.toggle('is-open');
          if (field.classList.contains('is-open')) {
            field.querySelector('input').focus();
          }
        });

        // Close on Escape.
        field.querySelector('input').addEventListener('keydown', function (e) {
          if (e.key === 'Escape') {
            field.classList.remove('is-open');
          }
        });
      });
    }
  };
})(Drupal, once);
