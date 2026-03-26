(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.contactForm = {
    attach: function (context) {
      once('contact-form', '.contact-page__form form', context).forEach(function (form) {
        form.addEventListener('submit', function (e) {
          e.preventDefault();

          var formData = new FormData(form);
          var submitBtn = form.querySelector('[type="submit"]');
          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';
          }

          fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
              'Accept': 'text/html'
            }
          })
          .then(function (response) {
            if (response.ok) {
              showThankYouModal();
              form.reset();
            } else {
              alert('Something went wrong. Please try again.');
            }
          })
          .catch(function () {
            alert('Network error. Please try again.');
          })
          .finally(function () {
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.textContent = 'Submit';
            }
          });
        });
      });

      once('contact-modal-close', '.contact-modal', context).forEach(function (modal) {
        modal.addEventListener('click', function (e) {
          if (e.target === modal || e.target.closest('.contact-modal__close')) {
            closeModal(modal);
          }
        });
      });
    }
  };

  function showThankYouModal() {
    var existing = document.querySelector('.contact-modal');
    if (existing) {
      existing.remove();
    }

    var modal = document.createElement('div');
    modal.className = 'contact-modal';
    modal.innerHTML =
      '<div class="contact-modal__overlay"></div>' +
      '<div class="contact-modal__content">' +
        '<button class="contact-modal__close" aria-label="Close">&times;</button>' +
        '<div class="contact-modal__icon">' +
          '<svg width="56" height="56" viewBox="0 0 56 56" fill="none"><circle cx="28" cy="28" r="28" fill="#FC5000"/><path d="M18 28.5L25 35.5L38 22.5" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
        '</div>' +
        '<h3 class="contact-modal__title">Thank You!</h3>' +
        '<p class="contact-modal__text">Your message has been sent successfully. Our team will get back to you shortly.</p>' +
        '<button class="contact-modal__btn">OK</button>' +
      '</div>';

    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';

    requestAnimationFrame(function () {
      modal.classList.add('contact-modal--visible');
    });

    modal.querySelector('.contact-modal__btn').addEventListener('click', function () {
      closeModal(modal);
    });

    modal.addEventListener('click', function (e) {
      if (e.target === modal || e.target.classList.contains('contact-modal__overlay')) {
        closeModal(modal);
      }
    });
  }

  function closeModal(modal) {
    modal.classList.remove('contact-modal--visible');
    document.body.style.overflow = '';
    setTimeout(function () {
      modal.remove();
    }, 300);
  }

})(Drupal, once);
