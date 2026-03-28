(function (Drupal, once) {
  'use strict';

  // Clean category params from URL after page load so Views AJAX
  // doesn't re-apply them when user unchecks filters
  if (window.location.search.indexOf('category') !== -1) {
    window.history.replaceState({}, '', window.location.pathname);
  }

  Drupal.behaviors.sortDropdown = {
    attach: function (context) {
      once('sort-dropdown', '.form-item-sort-by', context).forEach(function (wrapper) {
        var select = wrapper.querySelector('select');
        var label = wrapper.querySelector('label');
        if (!select || !label) return;

        // Hide native select
        select.style.display = 'none';
        label.style.display = 'none';

        // Build custom dropdown
        var dropdown = document.createElement('div');
        dropdown.className = 'sort-dropdown';

        var trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'sort-dropdown__trigger';
        trigger.innerHTML = 'Sort by <svg class="sort-dropdown__chevron" width="12" height="8" viewBox="0 0 12 8" fill="none"><path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

        var panel = document.createElement('div');
        panel.className = 'sort-dropdown__panel';

        Array.from(select.options).forEach(function (opt) {
          var item = document.createElement('button');
          item.type = 'button';
          item.className = 'sort-dropdown__item';
          if (opt.selected) item.classList.add('is-active');
          item.textContent = opt.textContent;
          item.dataset.value = opt.value;

          item.addEventListener('click', function () {
            select.value = this.dataset.value;
            select.dispatchEvent(new Event('change', { bubbles: true }));

            panel.querySelectorAll('.sort-dropdown__item').forEach(function (i) {
              i.classList.remove('is-active');
            });
            this.classList.add('is-active');
            dropdown.classList.remove('is-open');
          });

          panel.appendChild(item);
        });

        trigger.addEventListener('click', function (e) {
          e.stopPropagation();
          dropdown.classList.toggle('is-open');
        });

        document.addEventListener('click', function (e) {
          if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('is-open');
          }
        });

        dropdown.appendChild(trigger);
        dropdown.appendChild(panel);
        wrapper.appendChild(dropdown);
      });
    }
  };

  Drupal.behaviors.pagerArrows = {
    attach: function (context) {
      once('pager-arrows', '.pager__items', context).forEach(function (list) {
        if (!list.querySelector('.pager__item--previous')) {
          var li = document.createElement('li');
          li.className = 'pager__item pager__item--previous is-disabled';
          list.prepend(li);
        }
        if (!list.querySelector('.pager__item--next')) {
          var li = document.createElement('li');
          li.className = 'pager__item pager__item--next is-disabled';
          list.append(li);
        }
      });
    }
  };

  Drupal.behaviors.filterKeepOpen = {
    attach: function (context) {
      if (window.innerWidth > 767) return;
      var sidebar = document.querySelector('.filter-sidebar');
      if (!sidebar) return;
      if (window._filterSidebarOpen) {
        sidebar.style.transition = 'none';
        sidebar.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
        sidebar.offsetHeight;
        sidebar.style.transition = '';
      }
    }
  };

  Drupal.behaviors.sortMobileMove = {
    attach: function (context) {
      if (window.innerWidth > 767) return;
      once('sort-mobile-move', '.form-item-sort-by', context).forEach(function (sortEl) {
        var topBar = document.querySelector('.category-page__top');
        if (topBar) {
          topBar.appendChild(sortEl);
          sortEl.style.position = 'static';
          sortEl.style.margin = '0';
          sortEl.style.padding = '0';
          sortEl.style.border = 'none';
        }
      });
    }
  };

  Drupal.behaviors.filterToggle = {
    attach: function (context) {
      once('filter-toggle', '.filter-toggle', context).forEach(function (btn) {
        btn.addEventListener('click', function () {
          var sidebar = document.querySelector('.filter-sidebar');
          if (!sidebar) return;
          sidebar.classList.add('is-open');
          btn.setAttribute('aria-expanded', 'true');
          document.body.style.overflow = 'hidden';
          document.documentElement.style.overflow = 'hidden';
          window._filterSidebarOpen = true;
        });
      });

      once('filter-clear', '.filter-sidebar__clear', context).forEach(function (btn) {
        btn.addEventListener('click', function () {
          var sidebar = btn.closest('.filter-sidebar');
          if (!sidebar) return;
          sidebar.querySelectorAll('input[type="checkbox"]:checked').forEach(function (cb) {
            cb.checked = false;
          });
          var submit = sidebar.querySelector('.form-submit');
          if (submit) submit.click();
        });
      });

      once('filter-close', '.filter-sidebar__close', context).forEach(function (btn) {
        btn.addEventListener('click', function () {
          var sidebar = btn.closest('.filter-sidebar');
          if (!sidebar) return;
          sidebar.classList.remove('is-open');
          document.body.style.overflow = '';
          document.documentElement.style.overflow = '';
          window._filterSidebarOpen = false;
          var toggle = document.querySelector('.filter-toggle');
          if (toggle) toggle.setAttribute('aria-expanded', 'false');
        });
      });
    }
  };

})(Drupal, once);
