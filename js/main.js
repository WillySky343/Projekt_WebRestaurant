/* ===================================================
   La Bella Cucina – Main JavaScript
   =================================================== */

(function () {
  'use strict';

  /* ---------- Helpers ---------- */
  function $(selector, context) {
    return (context || document).querySelector(selector);
  }

  function $$(selector, context) {
    return Array.from((context || document).querySelectorAll(selector));
  }

  /* ===================================================
     NAVIGATION – sticky scroll + mobile toggle
     =================================================== */
  var header = $('.site-header');
  var navToggle = $('#navToggle');
  var navMenu = $('#navMenu');

  // Sticky header on scroll
  function onScroll() {
    if (window.scrollY > 60) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }

    // Back-to-top visibility
    var btt = $('#backToTop');
    if (btt) {
      if (window.scrollY > 400) {
        btt.hidden = false;
      } else {
        btt.hidden = true;
      }
    }
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll(); // run once on load

  // Mobile menu toggle
  if (navToggle && navMenu) {
    navToggle.addEventListener('click', function () {
      var expanded = navToggle.getAttribute('aria-expanded') === 'true';
      navToggle.setAttribute('aria-expanded', String(!expanded));
      navMenu.classList.toggle('open', !expanded);
    });

    // Close menu when a nav link is clicked
    $$('.nav-link', navMenu).forEach(function (link) {
      link.addEventListener('click', function () {
        navMenu.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
      });
    });

    // Close menu on outside click
    document.addEventListener('click', function (e) {
      if (!header.contains(e.target)) {
        navMenu.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  /* ===================================================
     ACTIVE NAV LINK – highlight current section
     =================================================== */
  var sections = $$('section[id]');
  var navLinks = $$('.nav-link');

  function setActiveLink() {
    var scrollY = window.scrollY + 120;
    var currentId = '';

    sections.forEach(function (section) {
      if (section.offsetTop <= scrollY) {
        currentId = section.getAttribute('id');
      }
    });

    navLinks.forEach(function (link) {
      var href = link.getAttribute('href');
      link.classList.toggle('active-link', href === '#' + currentId);
    });
  }

  window.addEventListener('scroll', setActiveLink, { passive: true });

  /* ===================================================
     MENU TABS – filter dishes by category
     =================================================== */
  var menuTabs = $$('.menu-tab');
  var menuCards = $$('.menu-card');

  function showCategory(category) {
    menuCards.forEach(function (card) {
      var match = card.getAttribute('data-category') === category;
      card.classList.toggle('visible', match);
    });

    menuTabs.forEach(function (tab) {
      var isActive = tab.getAttribute('data-category') === category;
      tab.classList.toggle('active', isActive);
      tab.setAttribute('aria-selected', String(isActive));
    });
  }

  // Show first category on load
  if (menuTabs.length > 0) {
    var firstCategory = menuTabs[0].getAttribute('data-category');
    showCategory(firstCategory);

    menuTabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        showCategory(tab.getAttribute('data-category'));
      });

      // Keyboard: left/right arrow navigation between tabs
      tab.addEventListener('keydown', function (e) {
        var idx = menuTabs.indexOf(tab);
        if (e.key === 'ArrowRight') {
          menuTabs[(idx + 1) % menuTabs.length].focus();
        } else if (e.key === 'ArrowLeft') {
          menuTabs[(idx - 1 + menuTabs.length) % menuTabs.length].focus();
        }
      });
    });
  }

  /* ===================================================
     RESERVATION FORM – client-side validation
     =================================================== */
  var reservationForm = $('#reservationForm');

  if (reservationForm) {
    // Set minimum date to today
    var dateInput = $('#reservationDate');
    if (dateInput) {
      var today = new Date();
      var yyyy = today.getFullYear();
      var mm = String(today.getMonth() + 1).padStart(2, '0');
      var dd = String(today.getDate()).padStart(2, '0');
      dateInput.setAttribute('min', yyyy + '-' + mm + '-' + dd);
    }

    function showError(inputId, message) {
      var input = $('#' + inputId);
      var errorEl = $('#' + inputId + 'Error');
      if (input) input.classList.add('invalid');
      if (errorEl) errorEl.textContent = message;
    }

    function clearError(inputId) {
      var input = $('#' + inputId);
      var errorEl = $('#' + inputId + 'Error');
      if (input) input.classList.remove('invalid');
      if (errorEl) errorEl.textContent = '';
    }

    // Real-time validation on blur
    ['guestName', 'guestEmail', 'guestCount', 'reservationDate', 'reservationTime'].forEach(function (id) {
      var el = $('#' + id);
      if (el) {
        el.addEventListener('blur', function () {
          validateField(id);
        });
        el.addEventListener('input', function () {
          clearError(id);
        });
      }
    });

    function validateField(id) {
      var el = $('#' + id);
      if (!el) return true;
      var val = el.value.trim();

      if (id === 'guestName') {
        if (!val) { showError(id, 'Please enter your full name.'); return false; }
        if (val.length < 2) { showError(id, 'Name must be at least 2 characters.'); return false; }
      }

      if (id === 'guestEmail') {
        if (!val) { showError(id, 'Please enter your email address.'); return false; }
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(val)) { showError(id, 'Please enter a valid email address.'); return false; }
      }

      if (id === 'guestCount') {
        if (!val) { showError(id, 'Please select the number of guests.'); return false; }
      }

      if (id === 'reservationDate') {
        if (!val) { showError(id, 'Please choose a date.'); return false; }
        var chosen = new Date(val + 'T00:00:00');
        var todayDate = new Date();
        todayDate.setHours(0, 0, 0, 0);
        if (chosen < todayDate) { showError(id, 'Date cannot be in the past.'); return false; }
      }

      if (id === 'reservationTime') {
        if (!val) { showError(id, 'Please choose a time.'); return false; }
      }

      clearError(id);
      return true;
    }

    reservationForm.addEventListener('submit', function (e) {
      e.preventDefault();

      var fieldsToValidate = ['guestName', 'guestEmail', 'guestCount', 'reservationDate', 'reservationTime'];
      var valid = fieldsToValidate.every(function (id) {
        return validateField(id);
      });

      if (!valid) return;

      // Simulate a successful submission
      var submitBtn = reservationForm.querySelector('button[type="submit"]');
      submitBtn.textContent = 'Sending…';
      submitBtn.disabled = true;

      setTimeout(function () {
        reservationForm.reset();
        submitBtn.textContent = 'Confirm Reservation';
        submitBtn.disabled = false;
        var successEl = $('#formSuccess');
        if (successEl) {
          successEl.hidden = false;
          successEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
          setTimeout(function () { successEl.hidden = true; }, 6000);
        }
      }, 1000);
    });
  }

  /* ===================================================
     NEWSLETTER FORM
     =================================================== */
  var nlForm = $('#newsletterForm');

  if (nlForm) {
    nlForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var emailInput = $('#nlEmail');
      var successEl = $('#nlSuccess');
      if (!emailInput || !emailInput.value.trim()) return;
      var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(emailInput.value.trim())) return;

      if (successEl) {
        successEl.hidden = false;
        emailInput.value = '';
      }
    });
  }

  /* ===================================================
     INTERSECTION OBSERVER – fade-in animations
     =================================================== */
  var animatedEls = $$('.menu-card, .highlight-item, .contact-card, .gallery-item');
  animatedEls.forEach(function (el) {
    el.classList.add('fade-in');
  });

  if ('IntersectionObserver' in window) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });

    animatedEls.forEach(function (el) {
      observer.observe(el);
    });
  } else {
    // Fallback: show everything immediately
    animatedEls.forEach(function (el) { el.classList.add('visible'); });
  }

  /* ===================================================
     BACK TO TOP BUTTON
     =================================================== */
  var backToTopBtn = $('#backToTop');
  if (backToTopBtn) {
    backToTopBtn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ===================================================
     FOOTER – current year
     =================================================== */
  var yearEl = $('#currentYear');
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }

})();
