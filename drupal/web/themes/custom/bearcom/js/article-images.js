/**
 * Wrap bare <img> tags in article body with a container for wave overlay.
 */
(function (Drupal) {
  'use strict';
  Drupal.behaviors.articleImages = {
    attach: function (context) {
      var container = context.querySelector('.article-body__content');
      if (!container) return;
      container.querySelectorAll('img').forEach(function (img) {
        if (img.closest('.article-body__img-wrap')) return;
        var wrap = document.createElement('div');
        wrap.className = 'article-body__img-wrap';
        img.parentNode.insertBefore(wrap, img);
        wrap.appendChild(img);
      });
    }
  };
})(Drupal);
