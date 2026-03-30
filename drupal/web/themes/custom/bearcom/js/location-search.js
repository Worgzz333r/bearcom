(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.locationSearch = {
    attach: function (context) {
      once('location-search', '.location-search', context).forEach(function (el) {
        var input = document.getElementById('location-query');
        var cardsContainer = document.getElementById('location-cards');
        var mapContainer = document.getElementById('location-map');
        var directoryGrid = document.getElementById('state-directory-grid');
        var geolocBtn = document.getElementById('use-my-location');
        var map = null;
        var markers = [];

        var mapSettings = drupalSettings.bearcom || {};
        var tileUrl = mapSettings.tileUrl || 'https://mt1.google.com/vt/lyrs=r&x={x}&y={y}&z={z}';
        var markerSvgUrl = mapSettings.markerIcon || '/' + drupalSettings.bearcom.themePath + '/images/marker-orange.svg';

        function initMap() {
          if (!mapContainer || !window.L) return;
          map = L.map(mapContainer).setView([39.8283, -98.5795], 4);
          L.tileLayer(tileUrl, {
            attribution: '&copy; Google Maps',
            maxZoom: 18,
          }).addTo(map);
        }

        function orangeIcon() {
          return L.icon({
            iconUrl: markerSvgUrl,
            iconSize: [18, 18],
            iconAnchor: [9, 9],
            popupAnchor: [0, -9],
          });
        }

        function clearMarkers() {
          if (!map) return;
          markers.forEach(function (m) { map.removeLayer(m); });
          markers = [];
        }

        function addMarkers(locations) {
          if (!map) {
            console.warn('locationSearch: map not initialized, skipping markers');
            return;
          }
          clearMarkers();
          var bounds = [];
          locations.forEach(function (loc) {
            if (!loc.lat || !loc.lon) return;
            var lat = parseFloat(loc.lat);
            var lon = parseFloat(loc.lon);
            var addressHtml = (loc.address || '').replace(/\n/g, '<br>');
            var marker = L.marker([lat, lon], { icon: orangeIcon() })
              .addTo(map)
              .bindPopup(
                '<div class="location-popup">' +
                '<strong>' + loc.title + '</strong><br>' +
                addressHtml + '<br>' +
                '<a href="/node/' + loc.nid + '">More Info &rarr;</a>' +
                '</div>'
              );
            markers.push(marker);
            bounds.push([lat, lon]);
          });
          if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50], maxZoom: 12 });
          }
        }

        function renderCards(locations) {
          if (!cardsContainer) return;
          if (locations.length === 0) {
            cardsContainer.innerHTML = '<p class="location-search__empty">No locations found.</p>';
            return;
          }
          var html = '';
          locations.forEach(function (loc) {
            var addressHtml = (loc.address || '').replace(/\n/g, '<br>');
            html += '<div class="location-card">' +
              '<div class="location-card__body">' +
              '<h3 class="location-card__title">' + loc.title + '</h3>' +
              '<div class="location-card__address">' + addressHtml + '</div>' +
              '<a href="/node/' + loc.nid + '" class="location-card__link">More Info</a>' +
              '</div>' +
              '</div>';
          });
          cardsContainer.innerHTML = html;
        }

        var allLocations = [];
        var dataReady = false;

        function loadAllLocations() {
          return fetch('/api/locations?_format=json')
            .then(function (res) {
              if (!res.ok) throw new Error('API returned ' + res.status);
              return res.json();
            })
            .then(function (data) {
              allLocations = data;
              dataReady = true;
              return data;
            })
            .catch(function (err) {
              console.warn('locationSearch: failed to load locations', err);
              allLocations = [];
              dataReady = true;
              return [];
            });
        }

        function searchLocations(query) {
          if (!dataReady) return;
          var filtered;
          if (query) {
            var q = query.toLowerCase();
            filtered = allLocations.filter(function (loc) {
              return (loc.title && loc.title.toLowerCase().indexOf(q) !== -1) ||
                     (loc.address && loc.address.toLowerCase().indexOf(q) !== -1) ||
                     (loc.phone && loc.phone.indexOf(q) !== -1);
            });
          } else {
            // Default: show only first location
            filtered = allLocations.slice(0, 1);
          }
          renderCards(filtered);
          addMarkers(query ? filtered : allLocations);
        }

        function buildDirectory() {
              var data = allLocations;
              var byState = {};
              data.forEach(function (loc) {
                var parts = loc.title.split(', ');
                var state = parts.length > 1 ? parts[parts.length - 1] : 'Other';
                if (!byState[state]) byState[state] = [];
                byState[state].push(loc);
              });
              var states = Object.keys(byState).sort();
              var html = '';
              states.forEach(function (state) {
                html += '<div class="state-directory__state">';
                html += '<h3 class="state-directory__state-name">' + state + '</h3>';
                html += '<ul class="state-directory__locations">';
                byState[state].forEach(function (loc) {
                  html += '<li><a href="/node/' + loc.nid + '">' + loc.title + '</a></li>';
                });
                html += '</ul></div>';
              });
          if (directoryGrid) directoryGrid.innerHTML = html;
        }

        if (input) {
          input.addEventListener('input', function () {
            searchLocations(input.value.trim());
          });
        }
        if (geolocBtn) {
          geolocBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (navigator.geolocation) {
              navigator.geolocation.getCurrentPosition(function (pos) {
                if (map) map.setView([pos.coords.latitude, pos.coords.longitude], 8);
                searchLocations('');
              });
            }
          });
        }

        initMap();
        loadAllLocations().then(function () {
          searchLocations('');
          buildDirectory();
        });
      });
    }
  };
})(Drupal, once);
