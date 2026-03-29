(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.searchExpand = {
    attach: function (context) {
      var toggles = once('search-expand', '[data-search-toggle]', context);

      toggles.forEach(function (toggle) {
        var wrapper = toggle.closest('.top-bar__search');
        var field = wrapper ? wrapper.querySelector('[data-search-field]') : null;
        if (!wrapper || !field) return;

        toggle.addEventListener('click', function (e) {
          e.preventDefault();
          wrapper.classList.toggle('is-open');
          if (wrapper.classList.contains('is-open')) {
            field.querySelector('input').focus();
          }
        });

        // Close on Escape.
        field.querySelector('input').addEventListener('keydown', function (e) {
          if (e.key === 'Escape') {
            wrapper.classList.remove('is-open');
          }
        });

        // Close on click outside.
        document.addEventListener('click', function (e) {
          if (!wrapper.contains(e.target)) {
            wrapper.classList.remove('is-open');
          }
        });
      });
    }
  };
})(Drupal, once);
