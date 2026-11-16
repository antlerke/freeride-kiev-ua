/* FreeRide — поява блоків при скролі.
   Одноразово: блок, який уже зʼявився, при зворотному скролі не ховається.

   Після завершення появи знімаємо data-rev і .in. Це не косметика:
   правило `.js [data-rev].in { transform: none }` специфічніше за
   `.lvl:hover`, тож поки атрибут висить, ховер-зсув не спрацьовує.
   Заразом прибираємо will-change, щоб не тримати шар на кожному блоці. */
(function () {
  'use strict';

  var STAGGER = 90;   /* мс між сусідніми картками в групі */
  var DURATION = 620; /* має збігатися з transition у base.css */
  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');

  function all(sel) { return Array.prototype.slice.call(document.querySelectorAll(sel)); }

  function cleanup(el) {
    el.removeAttribute('data-rev');
    el.classList.remove('in');
    el.style.transitionDelay = '';
    el.style.willChange = '';
  }

  function show(el, animated) {
    if (!animated) { cleanup(el); return; }
    el.classList.add('in');
    var delay = parseFloat(el.style.transitionDelay) || 0;
    setTimeout(function () { cleanup(el); }, DURATION + delay + 120);
  }

  /* Групи: дітям проставляємо напрямок і затримку */
  all('[data-rev-group]').forEach(function (group) {
    var dir = group.getAttribute('data-rev-group') || 'up';
    Array.prototype.forEach.call(group.children, function (child, i) {
      if (!child.hasAttribute('data-rev')) child.setAttribute('data-rev', dir);
      child.style.transitionDelay = (i * STAGGER) + 'ms';
    });
  });

  var items = all('[data-rev]');

  /* Без підтримки спостерігача або з вимкненим рухом — показуємо одразу */
  if (!('IntersectionObserver' in window) || reduced.matches) {
    items.forEach(function (el) { show(el, false); });
    return;
  }

  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (!e.isIntersecting) return;
      show(e.target, true);
      io.unobserve(e.target);
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

  items.forEach(function (el) {
    /* Те, що вже видно при завантаженні, показуємо без анімації */
    var r = el.getBoundingClientRect();
    if (r.top < window.innerHeight * 0.9) show(el, false);
    else io.observe(el);
  });

  /* Якщо користувач увімкнув економію руху вже після завантаження */
  if (reduced.addEventListener) {
    reduced.addEventListener('change', function (ev) {
      if (ev.matches) all('[data-rev]').forEach(function (el) { show(el, false); });
    });
  }
})();
