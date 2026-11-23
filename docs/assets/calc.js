/* FreeRide — калькулятор курсу.
   Жодної ціни в коді: усе з data/pricing.json, який потім віддаватиме
   адмінка за тією самою схемою. Без залежностей. */
(function () {
  'use strict';

  var root = document.getElementById('calc');
  if (!root) return;

  var nf = new Intl.NumberFormat('uk-UA');
  var D = null;                 // дані
  var state = { course: null, extras: {}, docs: {} };

  function money(n) { return nf.format(n) + ' ' + D.currency; }
  function hours(n) { return n + ' ' + D.unit; }
  function byId(list, id) { for (var i = 0; i < list.length; i++) if (list[i].id === id) return list[i]; return null; }
  function el(sel) { return root.querySelector(sel); }
  function svg(icon, size) {
    return '<svg width="' + size + '" height="' + size + '" viewBox="0 0 28 28" aria-hidden="true"><use href="#' + icon + '"/></svg>';
  }
  function courseHours(c) {
    var t = 0; for (var k in c.mix) t += c.mix[k]; return t;
  }

  /* ── Розрахунок ────────────────────────────────────────────
     Одна чиста функція: вхід — стан, вихід — усе, що показує
     правий стовпець. Жодного читання DOM. */
  function calc(st) {
    var c = byId(D.courses, st.course);
    var mix = {}, k;
    for (k in c.mix) mix[k] = c.mix[k];

    var lines = [{ label: c.name + ' (' + hours(courseHours(c)) + ')', value: money(c.price) }];
    var total = c.price;

    D.extras.forEach(function (x) {
      var h = st.extras[x.id] || 0;
      if (!h) return;
      mix[x.type] = (mix[x.type] || 0) + h;
      var sum = h * x.rate;
      total += sum;
      lines.push({ label: x.label + ' — ' + hours(h) + ' × ' + money(x.rate), value: money(sum) });
    });

    var docs = c.docs.slice();
    D.documents.forEach(function (d) {
      if (c.docs.indexOf(d.id) >= 0) return;          // вже в курсі
      if (!st.docs[d.id]) return;
      docs.push(d.id);
      total += d.price;
      lines.push({ label: d.label, value: money(d.price) });
    });

    lines.push({
      label: 'Документи ' + docs.map(function (id) { return D.docLabels[id] || id; }).join(' та '),
      value: 'включено', ok: true
    });

    return { course: c, mix: mix, lines: lines, total: total, compare: compare(st, c, mix) };
  }

  /* Порівняння: ліворуч — обраний курс, добудований щонайменше
     до складу повного; праворуч — повний курс цілком.
     Години, які вже є в базовому курсі, другий раз не рахуємо —
     інакше економія виходить завищеною. */
  function compare(st, c, mix) {
    if (!D.compare || !D.compare.enabled) return null;
    var target = byId(D.courses, D.compare.target);
    if (!target) return null;

    var base = c;
    if (c.id === target.id) {
      // Обрано повний курс — порівнюємо з найближчим нижчим
      base = D.courses.reduce(function (acc, x) {
        if (x.id === target.id || x.price >= target.price) return acc;
        return (!acc || x.price > acc.price) ? x : acc;
      }, null);
      if (!base) return null;
    }

    /* Добудувати можна лише тими заняттями, які є в прайсі додаткових.
       Якщо базовому курсу бракує годин типу, якого в додаткових немає
       (STARTER має 5 малокубатурних проти 13), то це вже інша програма —
       порівнювати нема чого. Додасться такий тип у прайс — порівняння
       зʼявиться саме, без правок коду. */
    var sellable = {};
    D.extras.forEach(function (x) { sellable[x.type] = true; });
    var comparable = true;
    D.lessonTypes.forEach(function (t) {
      if (sellable[t.id]) return;
      if ((base.mix[t.id] || 0) < (target.mix[t.id] || 0)) comparable = false;
    });
    if (!comparable) return null;

    var sum = base.price, added = [];
    D.extras.forEach(function (x) {
      var inBase = base.mix[x.type] || 0;
      var chosen = (c.id === base.id) ? (st.extras[x.id] || 0) : 0;
      var need = Math.max(inBase + chosen, target.mix[x.type] || 0) - inBase;
      if (need <= 0) return;
      sum += need * x.rate;
      added.push(hours(need) + ' ' + x.label.toLowerCase());
    });
    D.documents.forEach(function (d) {
      if (base.docs.indexOf(d.id) >= 0) return;
      if (target.docs.indexOf(d.id) < 0) return;
      sum += d.price;
      added.push(d.label);   /* назву документа не чіпаємо: «категорія а1» виглядає як помилка */
    });

    return {
      base: base, target: target, altTotal: sum, added: added,
      save: sum - target.price
    };
  }

  /* ── Малювання ─────────────────────────────────────────── */
  function renderInputs() {
    el('#courses').innerHTML = D.courses.map(function (c) {
      return '<label class="course-opt">' +
        '<input class="sr" type="radio" name="course" value="' + c.id + '"' +
        (c.id === state.course ? ' checked' : '') + '>' +
        '<span class="course-in">' +
          '<span class="course-ic">' + svg(c.icon, 28) + '</span>' +
          '<b>' + c.name + '</b>' +
          (c.legacy && c.legacy !== c.name ? '<s>' + c.legacy + '</s>' : '') +
          '<em>' + hours(courseHours(c)) + '</em>' +
          '<strong>' + money(c.price) + '</strong>' +
          '<span class="course-tick" aria-hidden="true">' + svg('i-check-sm', 16) + '</span>' +
        '</span></label>';
    }).join('');

    el('#extras').innerHTML = D.extras.map(function (x) {
      var t = byId(D.lessonTypes, x.type);
      return '<div class="xrow">' +
        '<span class="xrow-ic">' + svg(t ? t.icon : 'i-moto', 24) + '</span>' +
        '<span class="xrow-txt"><b>' + x.label + '</b><span>' + money(x.rate) + '/' + D.unit + '</span></span>' +
        '<span class="stepper">' +
          '<button type="button" data-extra="' + x.id + '" data-d="-1" aria-label="Менше: ' + x.label + '">−</button>' +
          '<output id="out-' + x.id + '">' + hours(0) + '</output>' +
          '<button type="button" data-extra="' + x.id + '" data-d="1" aria-label="Більше: ' + x.label + '">+</button>' +
        '</span></div>';
    }).join('');

    el('#docs').innerHTML = D.documents.map(function (d) {
      return '<label class="doc-row" id="doc-' + d.id + '">' +
        '<input type="checkbox" data-doc="' + d.id + '">' +
        '<span><b>' + d.label + ' — ' + money(d.price) + '</b>' +
        '<span>' + d.note + '</span>' +
        '<span class="doc-note" hidden></span></span></label>';
    }).join('');

    el('#mix-hint').innerHTML = '<b>Розподіл годин у курсах:</b><br>' + D.courses.map(function (c) {
      var parts = D.lessonTypes.filter(function (t) { return c.mix[t.id]; }).map(function (t) {
        return hours(c.mix[t.id]) + ' (' + t.label.toLowerCase() + ')';
      });
      return c.name + ' — ' + parts.join(' + ');
    }).join('<br>');
  }

  function render() {
    var r = calc(state);

    el('#sum-badge').textContent = r.course.name;

    el('#mix').innerHTML = D.lessonTypes.filter(function (t) { return r.mix[t.id]; }).map(function (t) {
      return '<div class="mix-row"><span>' + svg(t.icon, 22) + t.label + '</span><b>' + hours(r.mix[t.id]) + '</b></div>';
    }).join('');

    el('#cost').innerHTML = r.lines.map(function (l) {
      return '<div class="cost-row"><span>' + l.label + '</span><b' + (l.ok ? ' class="ok"' : '') + '>' + l.value + '</b></div>';
    }).join('');

    el('#total').textContent = money(r.total);

    var box = el('#vs');
    if (!r.compare) { box.hidden = true; } else {
      var cp = r.compare;
      box.hidden = false;
      el('#vs-a-name').textContent = cp.base.name;
      el('#vs-a-note').textContent = cp.added.length
        ? '+ ' + cp.added.join(', ')
        : 'без доповнень';
      el('#vs-a-sum').textContent = money(cp.altTotal);
      el('#vs-b-name').textContent = cp.target.name;
      el('#vs-b-sum').textContent = money(cp.target.price);
      /* Економія буває і відʼємною — якщо прайс так складеться.
         Малювати «економію» на мінусі було б брехнею. */
      var save = el('#save'), eq = el('#vs-equal');
      save.hidden = cp.save <= 0;
      eq.hidden = cp.save > 0;
      if (cp.save > 0) el('#save-sum').textContent = money(cp.save);
      else if (cp.save === 0) eq.textContent = 'Різниці у вартості немає';
      else eq.textContent = 'Окремими заняттями вийде дешевше на ' + money(-cp.save);
    }

    el('#cta-name').textContent = r.course.name;

    /* А1 всередині обраного курсу — чекбокс вимикаємо, а не ігноруємо мовчки */
    D.documents.forEach(function (d) {
      var row = el('#doc-' + d.id);
      if (!row) return;
      var inCourse = r.course.docs.indexOf(d.id) >= 0;
      var input = row.querySelector('input');
      input.disabled = inCourse;
      row.classList.toggle('is-off', inCourse);
      var note = row.querySelector('.doc-note');
      note.hidden = !inCourse;
      note.textContent = 'У курсі ' + r.course.name + ' — вже включено';
    });

    D.extras.forEach(function (x) {
      el('#out-' + x.id).textContent = hours(state.extras[x.id] || 0);
    });
  }

  /* ── Події ─────────────────────────────────────────────── */
  function bind() {
    el('#courses').addEventListener('change', function (e) {
      if (e.target.name !== 'course') return;
      state.course = e.target.value;
      render();
    });

    el('#extras').addEventListener('click', function (e) {
      var b = e.target.closest('button[data-extra]');
      if (!b) return;
      var id = b.getAttribute('data-extra');
      var x = byId(D.extras, id);
      var v = (state.extras[id] || 0) + Number(b.getAttribute('data-d'));
      state.extras[id] = Math.max(0, Math.min(x.max || 20, v));
      render();
    });

    el('#docs').addEventListener('change', function (e) {
      var i = e.target.closest('input[data-doc]');
      if (!i) return;
      state.docs[i.getAttribute('data-doc')] = i.checked;
      render();
    });
  }

  fetch('data/pricing.json', { cache: 'no-cache' })
    .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
    .then(function (data) {
      D = data;
      state.course = (D.compare && D.compare.target) || D.courses[D.courses.length - 1].id;
      renderInputs();
      bind();
      render();
      root.classList.add('ready');
    })
    .catch(function (err) {
      root.classList.add('failed');
      var box = el('#calc-error');
      if (box) {
        box.hidden = false;
        box.textContent = 'Не вдалося завантажити прайс (' + err.message + '). Ціни — на сторінці курсів.';
      }
    });
})();
