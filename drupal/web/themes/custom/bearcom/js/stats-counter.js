(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.statsCounter = {
    attach: function (context) {
      once('stats-counter', '[data-stats]', context).forEach(function (section) {
        var numbers = section.querySelectorAll('[data-stat-number]');

        var observer = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              numbers.forEach(function (el) {
                animateNumber(el);
              });
              observer.disconnect();
            }
          });
        }, { threshold: 0.3 });

        observer.observe(section);
      });

      function animateNumber(el) {
        var text = el.textContent.trim();
        var prefix = '';
        var suffix = '';
        var match = text.match(/^([^\d]*)(\d[\d,.]*)(.*)$/);

        if (!match) return;

        prefix = match[1];
        var numStr = match[2];
        suffix = match[3];

        var hasComma = numStr.indexOf(',') !== -1;
        var cleanNum = numStr.replace(/,/g, '');
        var target = parseFloat(cleanNum);
        var isFloat = cleanNum.indexOf('.') !== -1;
        var decimals = isFloat ? cleanNum.split('.')[1].length : 0;

        var duration = 2000;
        var startTime = null;

        function step(timestamp) {
          if (!startTime) startTime = timestamp;
          var progress = Math.min((timestamp - startTime) / duration, 1);
          var eased = 1 - Math.pow(1 - progress, 3);
          var current = eased * target;

          if (isFloat) {
            current = current.toFixed(decimals);
          } else {
            current = Math.floor(current);
          }

          var formatted = String(current);
          if (hasComma) {
            var parts = formatted.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            formatted = parts.join('.');
          }

          el.textContent = prefix + formatted + suffix;

          if (progress < 1) {
            requestAnimationFrame(step);
          }
        }

        el.textContent = prefix + (isFloat ? (0).toFixed(decimals) : '0') + suffix;
        setTimeout(function () {
          requestAnimationFrame(step);
        }, 400);
      }
    }
  };

})(Drupal, once);
