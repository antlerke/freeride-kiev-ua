# -*- coding: utf-8 -*-
"""Збирає спрайт теми зі статичних макетів.

Запуск з кореня репозиторію:  python3 wp/tools/build-sprite.py

Разом з іконками збирає карту систем координат: вони намальовані
в різних сітках (18, 20, 22, 28 одиниць), і та сама картинка в чужому
viewBox виходить або крихітною, або обрізаною.
"""
import io, os, re, glob, collections

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
DOCS = os.path.join(ROOT, 'docs')
OUT  = os.path.join(ROOT, 'wp/content/themes/freeride/inc/sprite.php')

# Іконки, яких у макетах немає в <use> (їх малює JS калькулятора зі своїм
# розміром) — сітку задаємо за самим контуром.
OVERRIDES = {
    'i-check-sm': '0 0 16 16',
    'i-gauge':    '0 0 28 28',
    'i-doc':      '0 0 28 28',
}

icons, boxes = {}, collections.defaultdict(collections.Counter)
for f in sorted(glob.glob(os.path.join(DOCS, '*.html'))):
    s = io.open(f, encoding='utf-8').read()
    for m in re.finditer(r'<g id="([a-z0-9\-]+)">(.*?)</g>', s, re.S):
        icons.setdefault(m.group(1), m.group(2))
    for m in re.finditer(r'<svg[^>]*viewBox="([^"]+)"[^>]*>\s*<use href="#([a-z0-9\-]+)"', s):
        boxes[m.group(2)][m.group(1)] += 1

def box_of(name):
    if name in OVERRIDES:
        return OVERRIDES[name]
    if boxes.get(name):
        return boxes[name].most_common(1)[0][0]
    raise SystemExit('Іконка %s ніде не використана — додайте її в OVERRIDES' % name)

head = '''<?php
/**
 * Спрайт іконок.
 *
 * Один <svg> з усіма піктограмами виводиться раз на сторінку, далі
 * розмітка посилається на них через <use href="#i-...">. Так браузер
 * не вантажить десятки окремих файлів, а колір задається звичайним
 * currentColor.
 *
 * Разом з іконками тут лежить карта систем координат — див. tools/build-sprite.py.
 *
 * ЗГЕНЕРОВАНО, руками не правити: python3 wp/tools/build-sprite.py
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
\texit;
}

/** @return array<string,string> id іконки → viewBox */
function fr_icon_boxes(): array {
\treturn [
'''
body = ''.join("\t\t'%s' => '%s',\n" % (n, box_of(n)) for n in sorted(icons))
mid = '''\t];
}

function fr_sprite(): void {
\t?>
\t<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><defs>
'''
defs = ''.join('\t\t<g id="%s">%s</g>\n' % (n, icons[n].strip()) for n in sorted(icons))
tail = '''\t</defs></svg>
\t<?php
}
'''
io.open(OUT, 'w', encoding='utf-8').write(head + body + mid + defs + tail)
print('іконок у спрайті:', len(icons))
