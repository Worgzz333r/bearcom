(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.locationDetailMap = {
    attach: function (context) {
      once('location-detail-map', '.location-detail-map', context).forEach(function (el) {
        if (!window.L) return;

        var lat = parseFloat(el.dataset.lat);
        var lon = parseFloat(el.dataset.lon);
        if (isNaN(lat) || isNaN(lon)) return;

        var map = L.map(el).setView([lat, lon], 14);

        L.tileLayer('https://mt1.google.com/vt/lyrs=r&x={x}&y={y}&z={z}', {
          attribution: '&copy; Google Maps',
          maxZoom: 18,
        }).addTo(map);

        var icon = L.divIcon({
          className: 'location-marker',
          html: '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="9" r="9" fill="#FC5000"/></svg>',
          iconSize: [18, 18],
          iconAnchor: [9, 9],
        });

        L.marker([lat, lon], { icon: icon }).addTo(map);
      });
    }
  };
})(Drupal, once);
