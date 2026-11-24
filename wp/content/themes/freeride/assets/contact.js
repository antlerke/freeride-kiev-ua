/* FreeRide — віджет звʼязку. На вузькому екрані рейка згортається
   в одну кнопку, щоб не перекривати контент. Без залежностей. */
(function () {
  'use strict';

  var box = document.getElementById('callme');
  if (!box) return;
  var btn = box.querySelector('.callme-toggle');
  var list = document.getElementById('callme-list');
  if (!btn || !list) return;

  var mq = window.matchMedia('(max-width: 1379px)');

  function open() {
    box.classList.add('open');
    btn.setAttribute('aria-expanded', 'true');
    document.addEventListener('keydown', onKey);
    document.addEventListener('click', onOutside, true);
  }

  function close(returnFocus) {
    box.classList.remove('open');
    btn.setAttribute('aria-expanded', 'false');
    document.removeEventListener('keydown', onKey);
    document.removeEventListener('click', onOutside, true);
    if (returnFocus) btn.focus();
  }

  function onKey(e) {
    if (e.key === 'Escape' || e.key === 'Esc') close(true);
  }

  function onOutside(e) {
    if (!box.contains(e.target)) close(false);
  }

  btn.addEventListener('click', function () {
    box.classList.contains('open') ? close(false) : open();
  });

  /* Перехід у месенджер має згорнути рейку: інакше, повернувшись
     у вкладку, користувач застане її розкритою поверх сторінки */
  list.addEventListener('click', function (e) {
    if (e.target.closest('a')) close(false);
  });

  /* На широкому екрані рейка розкрита завжди — знімаємо стан */
  function sync() { if (!mq.matches) close(false); }
  if (mq.addEventListener) mq.addEventListener('change', sync);
  else if (mq.addListener) mq.addListener(sync);
})();
