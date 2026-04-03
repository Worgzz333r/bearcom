(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.searchExpand = {
    attach: function (context) {
      // Desktop — top-bar search.
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

        field.querySelector('input').addEventListener('keydown', function (e) {
          if (e.key === 'Escape') {
            wrapper.classList.remove('is-open');
          }
        });

        document.addEventListener('click', function (e) {
          if (!wrapper.contains(e.target)) {
            wrapper.classList.remove('is-open');
          }
        });
      });

      // Mobile — header search.
      var mobileBtns = once('mobile-search', '[data-mobile-search-toggle]', context);
      mobileBtns.forEach(function (btn) {
        var wrap = btn.closest('[data-mobile-search]');
        if (!wrap) return;
        var input = wrap.querySelector('input');

        btn.addEventListener('click', function (e) {
          e.preventDefault();
          wrap.classList.toggle('is-open');
          if (wrap.classList.contains('is-open') && input) {
            input.focus();
          }
        });

        if (input) {
          input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
              wrap.classList.remove('is-open');
            }
          });
        }

        document.addEventListener('click', function (e) {
          if (!wrap.contains(e.target)) {
            wrap.classList.remove('is-open');
          }
        });
      });
    }
  };
})(Drupal, once);
