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
          className: 'location-detail-marker',
          html: '<svg width="27" height="43" viewBox="0 0 27 43" xmlns="http://www.w3.org/2000/svg"><path d="M13.5 0C6.044 0 0 6.044 0 13.5 0 23.625 13.5 43 13.5 43S27 23.625 27 13.5C27 6.044 20.956 0 13.5 0zm0 18.333a4.833 4.833 0 110-9.666 4.833 4.833 0 010 9.666z" fill="#EA4335"/></svg>',
          iconSize: [27, 43],
          iconAnchor: [13.5, 43],
        });

        L.marker([lat, lon], { icon: icon }).addTo(map);
      });
    }
  };
})(Drupal, once);
