(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.searchAutocomplete = {
    attach: function (context) {
      var inputs = once('search-ac', '[data-search-field] input', context);

      inputs.forEach(function (input) {
        var dropdown = document.createElement('div');
        dropdown.className = 'search-autocomplete';
        input.closest('.top-bar__search') ?
          input.closest('.top-bar__search').appendChild(dropdown) :
          input.parentElement.appendChild(dropdown);

        var debounceTimer = null;

        input.addEventListener('input', function () {
          var query = input.value.trim();
          clearTimeout(debounceTimer);

          if (query.length < 2) {
            dropdown.innerHTML = '';
            dropdown.classList.remove('is-open');
            return;
          }

          debounceTimer = setTimeout(function () {
            fetch('/search?keys=' + encodeURIComponent(query), {
              headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
              .then(function (res) { return res.text(); })
              .then(function (html) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');
                var results = doc.querySelectorAll('.search-result');

                if (!results.length) {
                  dropdown.innerHTML = '<div class="search-autocomplete__empty">No results found</div>';
                  dropdown.classList.add('is-open');
                  return;
                }

                var items = '';
                var count = 0;
                results.forEach(function (row) {
                  if (count >= 6) return;
                  var link = row.querySelector('.views-field-title a');
                  if (link) {
                    items += '<a href="' + link.getAttribute('href') + '" class="search-autocomplete__item">' + link.textContent.trim() + '</a>';
                    count++;
                  }
                });

                if (items) {
                  items += '<a href="/search?keys=' + encodeURIComponent(query) + '" class="search-autocomplete__all">View all results</a>';
                }

                dropdown.innerHTML = items;
                dropdown.classList.add('is-open');
              })
              .catch(function () {
                dropdown.innerHTML = '';
                dropdown.classList.remove('is-open');
              });
          }, 300);
        });

        document.addEventListener('click', function (e) {
          if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('is-open');
          }
        });

        input.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') {
            dropdown.classList.remove('is-open');
          }
        });
      });
    }
  };
})(Drupal, once);
