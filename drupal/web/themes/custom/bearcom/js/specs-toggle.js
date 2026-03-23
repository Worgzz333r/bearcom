(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.specsToggle = {
    attach: function (context) {
      once('specs-toggle', '[data-specs]', context).forEach(function (wrapper) {
        var container = wrapper.querySelector(':scope > div');
        if (!container) return;
        var items = container.querySelectorAll(':scope > div');
        if (items.length <= 6) return;

        // Create additional specs container
        var additional = document.createElement('div');
        additional.className = 'spec-table__additional';

        var btn = document.createElement('button');
        btn.className = 'spec-table__toggle';
        btn.type = 'button';
        btn.innerHTML = 'Additional specs <span class="toggle-icon"></span>';
        btn.style.display = 'block';

        var rowsWrap = document.createElement('div');
        rowsWrap.className = 'spec-table__additional-rows';

        // Move rows 7+ into additional container
        for (var i = items.length - 1; i >= 6; i--) {
          rowsWrap.insertBefore(items[i], rowsWrap.firstChild);
        }

        additional.appendChild(btn);
        additional.appendChild(rowsWrap);
        // Insert after .spec-table (grandparent of rows)
        var specTable = wrapper.closest('.spec-table');
        if (specTable) {
          specTable.parentNode.insertBefore(additional, specTable.nextSibling);
        } else {
          wrapper.parentNode.insertBefore(additional, wrapper.nextSibling);
        }

        btn.addEventListener('click', function () {
          additional.classList.toggle('is-expanded');
        });
      });
    }
  };
})(Drupal, once);
