(function (Drupal, once) {
  'use strict';

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

})(Drupal, once);
