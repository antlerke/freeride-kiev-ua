/* Фільтр розділів на сторінці галереї.
   Нічого не вантажить — просто ховає те, що не підходить. */
(function () {
  'use strict';

  var pills = document.querySelector('.gl-pills');
  if (!pills) return;

  var items = [].slice.call(document.querySelectorAll('[data-tag]'));

  pills.addEventListener('click', function (e) {
    var b = e.target.closest('button[data-tag]');
    if (!b) return;

    var tag = b.getAttribute('data-tag');
    [].forEach.call(pills.querySelectorAll('.pill-btn'), function (p) {
      p.classList.toggle('is-on', p === b);
    });

    items.forEach(function (el) {
      el.hidden = tag !== '' && el.getAttribute('data-tag') !== tag;
    });

    /* Перша картка добірки велика. Якщо вона сховалася, великою має
       стати перша з видимих — інакше в сітці лишається дірка. */
    var feat = [].slice.call(document.querySelectorAll('.gl-feat .gl-item'));
    var first = true;
    feat.forEach(function (el) {
      var lead = !el.hidden && first;
      el.classList.toggle('is-lead', lead);
      if (lead) first = false;
    });
  });
})();
