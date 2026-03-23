(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.specsToggle = {
    attach: function (context) {
      once('specs-toggle', '[data-specs]', context).forEach(function (wrapper) {
        var items = wrapper.querySelectorAll(':scope > div > div');
        if (items.length <= 6) return;

        var btn = document.createElement('button');
        btn.className = 'spec-table__toggle';
        btn.type = 'button';
        btn.innerHTML = 'Additional specs <span class="toggle-icon">+</span>';
        btn.style.display = 'block';
        wrapper.parentNode.appendChild(btn);

        btn.addEventListener('click', function () {
          var expanded = wrapper.classList.toggle('is-expanded');
          btn.innerHTML = expanded
            ? 'Additional specs <span class="toggle-icon">&times;</span>'
            : 'Additional specs <span class="toggle-icon">+</span>';
        });
      });
    }
  };
})(Drupal, once);
