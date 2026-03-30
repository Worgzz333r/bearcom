(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.mobileMenu = {
    attach: function (context) {
      var menu = document.querySelector('[data-mobile-menu]');
      if (!menu) return;

      // Toggle — hamburger button (opens/closes).
      var toggleBtns = once('mobile-menu-toggle', '[data-mobile-menu-toggle]', context);
      toggleBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
          var isOpen = menu.classList.toggle('is-open');
          btn.classList.toggle('hamburger--active', isOpen);
          document.body.style.overflow = isOpen ? 'hidden' : '';
          document.documentElement.style.overflow = isOpen ? 'hidden' : '';
        });
      });

      // Accordion — toggle panels on top-level items.
      var items = once('mobile-acc', '.mobile-menu__nav .mega-menu__item--has-children', context);
      items.forEach(function (item) {
        var link = item.querySelector(':scope > .mega-menu__link');
        if (!link) return;

        link.addEventListener('click', function (e) {
          e.preventDefault();

          // Close siblings.
          var siblings = item.parentElement.querySelectorAll(':scope > .is-expanded');
          siblings.forEach(function (sib) {
            if (sib !== item) sib.classList.remove('is-expanded');
          });

          item.classList.toggle('is-expanded');
        });
      });

      // Second-level accordion — column titles (Voice, Security, Data, etc.)
      var columns = once('mobile-acc-col', '.mobile-menu__nav .mega-menu__column-title', context);
      columns.forEach(function (title) {
        var column = title.parentElement;

        title.addEventListener('click', function () {
          // Close siblings.
          var siblings = column.parentElement.querySelectorAll(':scope > .mega-menu__column.is-expanded');
          siblings.forEach(function (sib) {
            if (sib !== column) sib.classList.remove('is-expanded');
          });

          column.classList.toggle('is-expanded');
        });
      });

      // Solutions accordion — sol-column titles
      var solTitles = once('mobile-acc-sol', '.mobile-menu__nav .mega-menu__sol-title', context);
      solTitles.forEach(function (title) {
        var column = title.closest('.mega-menu__sol-column');
        if (!column) return;

        title.addEventListener('click', function () {
          var siblings = column.parentElement.querySelectorAll(':scope > .mega-menu__sol-column.is-expanded');
          siblings.forEach(function (sib) {
            if (sib !== column) sib.classList.remove('is-expanded');
          });

          column.classList.toggle('is-expanded');
        });
      });
    }
  };
})(Drupal, once);
