(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.contactForm = {
    attach: function (context) {
      once('contact-form', '.contact-page__form form', context).forEach(function (form) {
        form.addEventListener('submit', function (e) {
          e.preventDefault();

          var formData = new FormData(form);
          var submitBtn = form.querySelector('[type="submit"]');
          var originalBtnText = submitBtn ? submitBtn.textContent : '';
          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';
          }

          fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'text/html' }
          })
          .then(function (response) {
            if (response.ok) {
              showModal('success');
              form.reset();
            } else {
              showModal('error');
            }
          })
          .catch(function () {
            showModal('error');
          })
          .finally(function () {
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.textContent = originalBtnText;
            }
          });
        });
      });
    }
  };

  function showModal(type) {
    var existing = document.querySelector('.contact-modal');
    if (existing) existing.remove();

    var isSuccess = type === 'success';
    var modal = document.createElement('div');
    modal.className = 'contact-modal';
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('aria-label', isSuccess ? 'Message sent' : 'Error');

    modal.innerHTML =
      '<div class="contact-modal__overlay"></div>' +
      '<div class="contact-modal__content">' +
        '<button class="contact-modal__close" aria-label="Close">&times;</button>' +
        '<div class="contact-modal__icon contact-modal__icon--' + type + '"></div>' +
        '<h3 class="contact-modal__title">' + (isSuccess ? 'Thank You!' : 'Oops!') + '</h3>' +
        '<p class="contact-modal__text">' +
          (isSuccess
            ? 'Your message has been sent successfully. Our team will get back to you shortly.'
            : 'Something went wrong. Please try again later.') +
        '</p>' +
        '<button class="contact-modal__btn">' + (isSuccess ? 'OK' : 'Try Again') + '</button>' +
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

    document.addEventListener('keydown', function handler(e) {
      if (e.key === 'Escape') {
        closeModal(modal);
        document.removeEventListener('keydown', handler);
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
