/* FreeRide — поява блоків при скролі.
   Одноразово: блок, який уже зʼявився, при зворотному скролі не ховається. */
(function () {
  'use strict';

  var STAGGER = 90;   /* мс між сусідніми картками в групі */
  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');

  function all(sel) { return Array.prototype.slice.call(document.querySelectorAll(sel)); }

  /* Групи: дітям проставляємо напрямок і затримку */
  all('[data-rev-group]').forEach(function (group) {
    var dir = group.getAttribute('data-rev-group') || 'up';
    Array.prototype.forEach.call(group.children, function (child, i) {
      if (!child.hasAttribute('data-rev')) child.setAttribute('data-rev', dir);
      child.style.transitionDelay = (i * STAGGER) + 'ms';
    });
  });

  var items = all('[data-rev]');

  function show(el) { el.classList.add('in'); }

  /* Без підтримки спостерігача або з вимкненим рухом — показуємо одразу */
  if (!('IntersectionObserver' in window) || reduced.matches) {
    items.forEach(function (el) { el.style.transitionDelay = '0ms'; show(el); });
    return;
  }

  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (!e.isIntersecting) return;
      show(e.target);
      io.unobserve(e.target);
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

  items.forEach(function (el) {
    /* Те, що вже видно при завантаженні, показуємо без анімації */
    var r = el.getBoundingClientRect();
    if (r.top < window.innerHeight * 0.9) {
      el.style.transitionDelay = el.style.transitionDelay || '0ms';
      show(el);
    } else {
      io.observe(el);
    }
  });

  /* Якщо користувач увімкнув економію руху вже після завантаження */
  if (reduced.addEventListener) {
    reduced.addEventListener('change', function (ev) {
      if (ev.matches) items.forEach(show);
    });
  }
})();
