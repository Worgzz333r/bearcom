(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.industryCarousel = {
    attach: function (context) {
      // Find all card-grids inside industry-page field_solutions
      var grids = once('industry-carousel', '.industry-page .field--name-field-solutions .card-grid', context);

      grids.forEach(function (grid, index) {
        // Skip first grid (index 0) — it stays as regular grid
        if (index === 0) return;

        var itemsContainer = grid.querySelector('.card-grid__items');
        if (!itemsContainer) return;

        // Get all cards
        var cards = itemsContainer.querySelectorAll('.card');
        if (cards.length === 0) return;

        // Create Splide structure
        var splideEl = document.createElement('div');
        splideEl.className = 'splide industry-carousel';

        var track = document.createElement('div');
        track.className = 'splide__track';

        var list = document.createElement('ul');
        list.className = 'splide__list';

        cards.forEach(function (card) {
          var slide = document.createElement('li');
          slide.className = 'splide__slide';
          slide.appendChild(card.cloneNode(true));
          list.appendChild(slide);
        });

        track.appendChild(list);
        splideEl.appendChild(track);

        // Replace grid with splide
        itemsContainer.innerHTML = '';
        itemsContainer.classList.remove('grid', 'grid--3');
        itemsContainer.appendChild(splideEl);

        // Init Splide
        new Splide(splideEl, {
          type: 'slide',
          perPage: 3,
          gap: '24px',
          pagination: false,
          arrows: false,
          drag: true,
          breakpoints: {
            1023: { perPage: 2 },
            767: { perPage: 1 }
          }
        }).mount();
      });
    }
  };

})(Drupal, once);
