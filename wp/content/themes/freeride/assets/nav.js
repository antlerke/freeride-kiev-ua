/* FreeRide — бургер-меню. Без залежностей. */
(function () {
  'use strict';

  var bar = document.querySelector('.bar');
  var btn = document.querySelector('.burger');
  var drawer = document.getElementById('menu');
  if (!bar || !btn || !drawer) return;

  var mq = window.matchMedia('(max-width: 980px)');

  function open() {
    bar.classList.add('open');
    btn.setAttribute('aria-expanded', 'true');
    document.addEventListener('keydown', onKey);
    document.addEventListener('click', onOutside, true);
  }

  function close(returnFocus) {
    bar.classList.remove('open');
    btn.setAttribute('aria-expanded', 'false');
    document.removeEventListener('keydown', onKey);
    document.removeEventListener('click', onOutside, true);
    if (returnFocus) btn.focus();
  }

  function onKey(e) {
    if (e.key === 'Escape' || e.key === 'Esc') close(true);
  }

  /* Клік поза шапкою закриває меню */
  function onOutside(e) {
    if (!bar.contains(e.target)) close(false);
  }

  btn.addEventListener('click', function () {
    bar.classList.contains('open') ? close(false) : open();
  });

  /* Перехід за пунктом меню має його закрити — інакше панель
     лишиться висіти над секцією, до якої щойно проскролили */
  drawer.addEventListener('click', function (e) {
    if (e.target.closest('a')) close(false);
  });

  /* При переході на десктоп знімаємо стан, щоб меню не лишилося
     «відкритим» у розмітці, коли воно вже не панель */
  function sync() { if (!mq.matches) close(false); }
  if (mq.addEventListener) mq.addEventListener('change', sync);
  else if (mq.addListener) mq.addListener(sync);
})();
