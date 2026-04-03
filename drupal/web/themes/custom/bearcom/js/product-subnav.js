(function (Drupal, once) {
  'use strict';

  // Default order for subnav items
  var defaultOrder = [
    'overview',
    'guided-journey',
    'features',
    'specifications',
    'related-products',
    'faq'
  ];

  Drupal.behaviors.productSubnav = {
    attach: function (context) {
      var navs = once('product-subnav', '[data-product-subnav]', context);
      navs.forEach(function (nav) {
        var list = nav.querySelector('[data-product-subnav-list]');
        if (!list) return;

        var page = document.querySelector('.product-page');
        if (!page) return;

        // If no items from Twig (field_subnav), auto-generate from sections
        if (list.children.length === 0) {
          var sections = page.querySelectorAll('[data-section-label]');
          if (sections.length === 0) {
            nav.style.display = 'none';
            return;
          }

          // Collect sections into a map by id
          var sectionMap = {};
          sections.forEach(function (section) {
            var label = section.getAttribute('data-section-label');
            var id = section.id;
            if (label && id) {
              sectionMap[id] = label;
            }
          });

          // Build nav in defined order, then append any remaining
          var added = {};
          defaultOrder.forEach(function (id) {
            if (sectionMap[id]) {
              var li = document.createElement('li');
              li.className = 'product-subnav__item';
              var a = document.createElement('a');
              a.href = '#' + id;
              a.className = 'product-subnav__link';
              a.textContent = sectionMap[id];
              li.appendChild(a);
              list.appendChild(li);
              added[id] = true;
            }
          });

          // Append sections not in default order
          Object.keys(sectionMap).forEach(function (id) {
            if (!added[id]) {
              var li = document.createElement('li');
              li.className = 'product-subnav__item';
              var a = document.createElement('a');
              a.href = '#' + id;
              a.className = 'product-subnav__link';
              a.textContent = sectionMap[id];
              li.appendChild(a);
              list.appendChild(li);
            }
          });
        }

        // Hide if still empty
        if (list.children.length === 0) {
          nav.style.display = 'none';
          return;
        }

        // Smooth scroll on click
        var links = list.querySelectorAll('.product-subnav__link');
        links.forEach(function (link) {
          link.addEventListener('click', function (e) {
            var href = link.getAttribute('href');
            if (href && href.charAt(0) === '#') {
              e.preventDefault();
              var target = document.getElementById(href.substring(1));
              if (target) {
                if (href === '#overview') {
                  window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                  var navBottom = nav.getBoundingClientRect().bottom;
                  var y = target.getBoundingClientRect().top + window.pageYOffset - navBottom;
                  window.scrollTo({ top: y, behavior: 'smooth' });
                }
              }
            }
          });
        });

        // Active state on scroll — sort by DOM position, not link order
        function updateActive() {
          var threshold = 200;
          var pairs = [];

          links.forEach(function (link, i) {
            var href = link.getAttribute('href');
            if (href && href.charAt(0) === '#') {
              var target = document.getElementById(href.substring(1));
              if (target) {
                pairs.push({ index: i, top: target.getBoundingClientRect().top });
              }
            }
          });

          // Sort by actual position on page
          pairs.sort(function (a, b) { return a.top - b.top; });

          var activeIndex = pairs.length > 0 ? pairs[0].index : 0;
          for (var j = 0; j < pairs.length; j++) {
            if (pairs[j].top <= threshold) {
              activeIndex = pairs[j].index;
            }
          }

          links.forEach(function (link, i) {
            var wasActive = link.classList.contains('is-active');
            link.classList.toggle('is-active', i === activeIndex);
            if (!wasActive && i === activeIndex) {
              var li = link.closest('.product-subnav__item');
              if (li) {
                li.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
              }
            }
          });
        }

        window.addEventListener('scroll', updateActive, { passive: true });
        updateActive();
      });
    }
  };
})(Drupal, once);
