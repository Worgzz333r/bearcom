(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.locationDetailMap = {
    attach: function (context) {
      once('location-detail-map', '.location-detail-map', context).forEach(function (el) {
        if (!window.L) return;

        var lat = parseFloat(el.dataset.lat);
        var lon = parseFloat(el.dataset.lon);
        if (isNaN(lat) || isNaN(lon)) return;

        var settings = drupalSettings.bearcom || {};
        var tileUrl = settings.tileUrl || 'https://mt1.google.com/vt/lyrs=r&x={x}&y={y}&z={z}';
        var markerUrl = settings.markerPin || '/' + settings.themePath + '/images/marker-pin.svg';

        var map = L.map(el).setView([lat, lon], 14);

        L.tileLayer(tileUrl, {
          attribution: '&copy; Google Maps',
          maxZoom: 18,
        }).addTo(map);

        var icon = L.icon({
          iconUrl: markerUrl,
          iconSize: [27, 43],
          iconAnchor: [13.5, 43],
        });

        L.marker([lat, lon], { icon: icon }).addTo(map);
      });
    }
  };
})(Drupal, once);
