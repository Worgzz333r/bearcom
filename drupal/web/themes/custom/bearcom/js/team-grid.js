(function () {
  'use strict';

  function layoutTeamGrid(container) {
    var images = container.querySelectorAll('.team-grid__image');
    if (!images.length) return;

    var containerWidth = container.offsetWidth;
    // Scale everything relative to 375px design
    var scale = containerWidth / 375;
    var size = Math.round(100 * scale);
    var overflowX = Math.round(35 * scale);
    var centerOffset = Math.round(55 * scale);
    var edgeRowSpacing = Math.round(140 * scale);
    var groupGap = Math.round(-15 * scale);

    // Apply size to all images
    images.forEach(function (img) {
      img.style.width = size + 'px';
      img.style.height = size + 'px';
      var imgEl = img.querySelector('img');
      if (imgEl) {
        imgEl.style.width = size + 'px';
        imgEl.style.height = size + 'px';
      }
    });

    var totalHeight = 0;
    var i = 0;
    var groupIndex = 0;
    var y = 0;

    while (i < images.length) {
      var isFirstGroup = groupIndex === 0;

      // Separator: single centered image between groups (not for first group)
      if (!isFirstGroup && i < images.length) {
        images[i].style.left = (containerWidth - size) / 2 + 'px';
        images[i].style.top = y + 'px';
        totalHeight = Math.max(totalHeight, y + size);
        y += size + groupGap;
        i++;
      }

      // Row 1: two images on edges (always overflow)
      var row1Y = y;
      var offsetX = -overflowX;

      if (i < images.length) {
        images[i].style.left = offsetX + 'px';
        images[i].style.top = row1Y + 'px';
        totalHeight = Math.max(totalHeight, row1Y + size);
        i++;
      }
      if (i < images.length) {
        images[i].style.right = offsetX + 'px';
        images[i].style.left = 'auto';
        images[i].style.top = row1Y + 'px';
        i++;
      }

      // Row 2: one image centered
      if (i < images.length) {
        var centerY = row1Y + centerOffset;
        images[i].style.left = (containerWidth - size) / 2 + 'px';
        images[i].style.top = centerY + 'px';
        totalHeight = Math.max(totalHeight, centerY + size);
        i++;
      }

      // Row 3: two images on edges (inside container)
      var row3Y = row1Y + edgeRowSpacing;
      if (i < images.length) {
        images[i].style.left = '0px';
        images[i].style.top = row3Y + 'px';
        totalHeight = Math.max(totalHeight, row3Y + size);
        i++;
      }
      if (i < images.length) {
        images[i].style.right = '0px';
        images[i].style.left = 'auto';
        images[i].style.top = row3Y + 'px';
        totalHeight = Math.max(totalHeight, row3Y + size);
        i++;
      }

      y = row3Y + size + groupGap;
      groupIndex++;
    }

    container.style.height = totalHeight + 'px';
  }

  function initAll() {
    var grids = document.querySelectorAll('.team-grid__images');
    grids.forEach(layoutTeamGrid);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }

  window.addEventListener('resize', initAll);
})();
