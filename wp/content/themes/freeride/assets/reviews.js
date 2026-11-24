/* FreeRide — фільтр відгуків і прокрутка відеоряду. Без залежностей.
   Кнопки справді перемикають вміст: непрацюючий фільтр гірший за його
   відсутність, тож усе, що тут намальовано, має щось робити. */
(function () {
  'use strict';

  var filters = document.getElementById('filters');
  var secVideo = document.getElementById('sec-video');
  var secText = document.getElementById('sec-text');

  if (filters && secVideo && secText) {
    var pills = filters.querySelectorAll('.pill-btn[data-show]');

    filters.addEventListener('click', function (e) {
      var btn = e.target.closest('.pill-btn[data-show]');
      if (!btn) return;
      var show = btn.getAttribute('data-show');
      for (var i = 0; i < pills.length; i++) {
        pills[i].setAttribute('aria-pressed', String(pills[i] === btn));
      }
      secVideo.hidden = (show === 'text');
      secText.hidden = (show === 'video');
    });
  }

  /* Вибір курсу ховає картки інших курсів */
  var sel = document.getElementById('by-course');
  var revs = document.getElementById('revs');
  var more = document.getElementById('more');
  var moreBox = document.getElementById('revs-more');
  var empty = document.getElementById('revs-empty');

  if (sel && revs) {
    sel.addEventListener('change', function () {
      var v = sel.value, shown = 0;
      var cards = revs.querySelectorAll('.rev');
      for (var i = 0; i < cards.length; i++) {
        var hit = (v === 'all' || cards[i].getAttribute('data-course') === v);
        cards[i].hidden = !hit;
        if (hit) shown++;
      }
      if (empty) empty.hidden = shown !== 0;
      /* Добирати нічого, поки фільтр звужений */
      if (more) more.hidden = v !== 'all';
      if (moreBox && v !== 'all') {
        moreBox.hidden = true;
        if (more) more.setAttribute('aria-expanded', 'false');
      }
    });
  }

  if (more && moreBox) {
    more.addEventListener('click', function () {
      var open = moreBox.hidden;
      moreBox.hidden = !open;
      more.setAttribute('aria-expanded', String(open));
      more.firstChild.nodeValue = open ? '\n      Згорнути\n      ' : '\n      Показати ще відгуки\n      ';
    });
  }

  /* Стрілки прокручують рядок відео на одну картку */
  var vids = document.getElementById('vids');
  if (vids) {
    var navs = document.querySelectorAll('.vid-nav button[data-scroll]');
    for (var j = 0; j < navs.length; j++) {
      navs[j].addEventListener('click', function (e) {
        var dir = Number(e.currentTarget.getAttribute('data-scroll'));
        var card = vids.querySelector('.vid');
        var step = card ? card.getBoundingClientRect().width + 18 : 300;
        vids.scrollBy({ left: dir * step, behavior: 'smooth' });
      });
    }
  }
})();
