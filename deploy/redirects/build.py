# -*- coding: utf-8 -*-
"""Будує карту 301/410 зі старого сайту на новий.

Джерело істини — `_inventory.txt`: усі 158 адрес із сайтмапів старого сайту,
вивантажені 2026-11-24. Скрипт падає, якщо бодай одна адреса не отримала
рішення, тож мовчки загубити сторінку не вийде.

Перезібрати:  python3 build.py
"""
import io, os, re, csv

HERE = os.path.dirname(os.path.abspath(__file__))
HOST = 'https://freeride.kiev.ua'

# ── Рішення ───────────────────────────────────────────────────────────
# Українська переїхала в корінь і забрала теперішні кореневі слаги разом
# з їхньою історією, тож для них редірект не потрібен — міняється лише
# мова вмісту. Редіректи потрібні для /uk/, для чищених слагів і для того,
# що не переноситься.

KEEP = [                      # лишаються за своєю адресою, редіректу немає
    '/', '/en/',
    '/kontakty/', '/komanda/', '/podbor-motoczikla/', '/otzyvy/',
    '/en/contacts/', '/en/team/', '/en/motorcycle-selection/',
    '/en/reviews/', '/en/gallery/', '/en/gift-certificate/',
    '/motocourses/', '/en/motocourses/',
    '/motocourses/bazovyj/', '/motocourses/intensiv/',
    '/motocourses/polnyj-kurs/', '/motocourses/gorod/', '/motocourses/probnyj/',
    '/en/motocourses/basic-course/', '/en/motocourses/intensive/',
    '/en/motocourses/full-course/', '/en/motocourses/city/',
    '/en/motocourses/trial/',
]

MOVE = {                      # 301 на конкретну адресу
    # чищення слагів: суфікси -2/-3 — сліди подвійного створення сторінки,
    # «czialnosti» — баг транслітерації Polylang
    '/galereya-2/':                  '/galereya/',
    '/podarochnyj-sertifikat-3/':    '/sertifikat/',
    '/politika-konfidenczialnosti/': '/polityka-konfidencijnosti/',

    # українська гілка переїхала в корінь
    '/uk/':                          '/',
    '/uk/golovna-storinka/':         '/',
    '/uk/kontakti/':                 '/kontakty/',
    '/uk/komanda/':                  '/komanda/',
    '/uk/vidguki/':                  '/otzyvy/',
    '/uk/galereya/':                 '/galereya/',
    '/uk/pidbir-motoczikla/':        '/podbor-motoczikla/',
    '/uk/podarunkovij-sertifikat/':  '/sertifikat/',
    '/uk/motokursi/':                '/motocourses/',
    '/uk/motocourses/':              '/motocourses/',
    '/uk/motocourses/bazovij/':      '/motocourses/bazovyj/',
    '/uk/motocourses/intensiv/':     '/motocourses/intensiv/',
    '/uk/motocourses/povnij-kurs/':  '/motocourses/polnyj-kurs/',
    '/uk/motocourses/misto/':        '/motocourses/gorod/',
    '/uk/motocourses/probnij/':      '/motocourses/probnyj/',

    # дублі головної
    '/glavnaya-stranicza/':          '/',
    '/en/front-page/':               '/en/',

    # лендинг курсів дублював архів
    '/motokursy/':                   '/motocourses/',
    '/en/moto/':                     '/en/motocourses/',

    # мотопарк став блоком у «Про нас»
    '/motopark/':                    '/pro-nas/#motopark',
    '/uk/motopark/':                 '/pro-nas/#motopark',
    '/en/moto-park/':                '/en/about/#motopark',

    # старі курси без пари в новій лінійці
    '/motocourses/kruizer/':                 '/motocourses/',
    '/motocourses/scooter/':                 '/motocourses/',
    '/motocourses/povyshenie-kvalifikacii/': '/motocourses/polnyj-kurs/',
    '/uk/motocourses/kruizer/':              '/motocourses/',
    '/uk/motocourses/scooter/':              '/motocourses/',
    '/uk/motocourses/pidvyshchennya-kvalifikacii/': '/motocourses/polnyj-kurs/',
    '/en/motocourses/cruiser/':              '/en/motocourses/',
    '/en/motocourses/scooter/':              '/en/motocourses/',
    '/en/motocourses/advanced-training/':    '/en/motocourses/full-course/',
}

# 410 Gone, а не 301 на головну: масовий редірект видаленого вмісту на
# головну Google читає як мʼяку 404 і все одно викидає з індексу, зате
# втрачається сигнал «сторінки більше немає».
GONE_PREFIX = [
    '/blog/', '/uk/blog/',
    '/novosti/', '/uk/novini/', '/en/news/',
    '/category/', '/uk/category/', '/en/category/',
    '/tag/', '/uk/tag/', '/en/tag/',
    '/author/',
    '/jet-popup/', '/uk/jet-popup/', '/en/jet-popup/',
]
GONE_EXACT = [
    '/test-mail/',     # службова сторінка, потрапила в індекс помилково
    '/cto/',           # послуга не переноситься на новий сайт
]

# ── Збірка ────────────────────────────────────────────────────────────
inv = [l.strip().replace(HOST, '') or '/'
       for l in io.open(os.path.join(HERE, '_inventory.txt'), encoding='utf-8')
       if l.strip()]

rows, undecided = [], []
for u in sorted(set(inv)):
    if u in KEEP:
        rows.append((u, '', 'keep', 'лишається за своєю адресою'))
    elif u in MOVE:
        rows.append((u, MOVE[u], '301', ''))
    elif u in GONE_EXACT or any(u.startswith(p) for p in GONE_PREFIX):
        rows.append((u, '', '410', 'не переноситься'))
    else:
        undecided.append(u)

if undecided:
    raise SystemExit('Без рішення лишилися адреси:\n  ' + '\n  '.join(undecided))

# Адреси з MOVE, яких немає в сайтмапі (напр. старі слаги поза індексом),
# теж мають потрапити в правила.
extra = [u for u in MOVE if u not in set(inv)]
for u in sorted(extra):
    rows.append((u, MOVE[u], '301', 'немає в сайтмапі, але адреса жива'))

moves = [r for r in rows if r[2] == '301']
gones = [r for r in rows if r[2] == '410']
keeps = [r for r in rows if r[2] == 'keep']

# 1. CSV для плагіна Redirection (Tools → Redirection → Import)
with io.open(os.path.join(HERE, 'redirects.csv'), 'w', encoding='utf-8', newline='') as f:
    w = csv.writer(f)
    w.writerow(['source', 'target', 'regex', 'code'])
    for src, dst, kind, _ in moves:
        w.writerow([src, dst, 0, 301])
    for src, _, kind, _ in gones:
        w.writerow([src, '', 0, 410])

# 2. Правила для nginx — швидше за плагін, бо не піднімає PHP
with io.open(os.path.join(HERE, 'redirects.nginx.conf'), 'w', encoding='utf-8') as f:
    f.write('# Карта 301/410 зі старого freeride.kiev.ua на новий.\n'
            '# Згенеровано build.py, руками не правити.\n'
            '# Підключення у server {}:\n'
            '#   include /path/to/redirects.nginx.conf;\n\n')
    f.write('location = /favicon.ico { log_not_found off; access_log off; }\n\n')
    for src, dst, _, _ in moves:
        f.write('location = %s { return 301 %s; }\n' % (src, dst))
    f.write('\n# Видалений вміст\n')
    for p in GONE_PREFIX:
        f.write('location ^~ %s { return 410; }\n' % p)
    for u in GONE_EXACT:
        f.write('location = %s { return 410; }\n' % u)

# 3. Таблиця для людини
with io.open(os.path.join(HERE, 'redirects.md'), 'w', encoding='utf-8') as f:
    f.write('# Карта переадресації\n\n')
    f.write('Згенеровано `build.py` з `_inventory.txt` — усі адреси з сайтмапів '
            'старого сайту станом на 24.11.2026.\n\n')
    f.write('| | кількість |\n|---|---|\n')
    f.write('| 301 на нову адресу | %d |\n' % len(moves))
    f.write('| 410 видалено | %d |\n' % len(gones))
    f.write('| лишається як є | %d |\n' % len(keeps))
    f.write('| **разом** | **%d** |\n\n' % len(rows))
    f.write('## 301\n\n| Було | Стало |\n|---|---|\n')
    for src, dst, _, note in moves:
        f.write('| `%s` | `%s`%s |\n' % (src, dst, ' — ' + note if note else ''))
    f.write('\n## 410 Gone\n\nЦілі розділи, не окремі адреси:\n\n')
    for p in GONE_PREFIX:
        n = len([r for r in gones if r[0].startswith(p)])
        f.write('- `%s*` — %d адрес\n' % (p, n))
    for u in GONE_EXACT:
        f.write('- `%s`\n' % u)
    f.write('\n## Лишається за своєю адресою\n\n')
    f.write('Українська версія переїхала в корінь і забрала ці слаги разом '
            'з їхньою історією — редірект не потрібен.\n\n')
    for src, _, _, _ in keeps:
        f.write('- `%s`\n' % src)

print('301: %d   410: %d   без змін: %d   разом правил: %d'
      % (len(moves), len(gones), len(keeps), len(rows)))
