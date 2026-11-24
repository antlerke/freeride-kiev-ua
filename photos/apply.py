#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Ставить згенеровані кадри в макети docs/*.html замість текстових заглушок.

    python3 apply.py --check     # що є, чого бракує — нічого не змінює
    python3 apply.py             # зменшує з out/ у docs/assets/photos і править HTML

Ідемпотентний: заглушку, вже замінену на <img>, вдруге не чіпає.
Зменшення — системним sips (macOS). Якщо його немає, майстер копіюється як є.
"""
import argparse, json, pathlib, re, shutil, subprocess, sys

HERE = pathlib.Path(__file__).resolve().parent
ROOT = HERE.parent
OUT = HERE / 'out'
WEB = ROOT / 'docs' / 'assets' / 'photos'
BASE = ROOT / 'docs' / 'assets' / 'base.css'

CSS_MARK = '/* Фото замість заглушок — ставить photos/apply.py */'
CSS_BLOCK = CSS_MARK + '''
.lvl-media, .why-media, .extra-media, .mate-photo,
.founder-photo, .duo-photo, .duo-wide, .rev-shot { position: relative; overflow: hidden; }
.hero-media, .final-media, .chero-media, .mission-media, .prog-media, .vid-shot { overflow: hidden; }
.ph-img {
  position: absolute; inset: 0; width: 100%; height: 100%;
  object-fit: cover; display: block; border-radius: inherit;
}
'''


def master(shot_id):
    for ext in ('.png', '.jpg', '.jpeg', '.webp'):
        p = OUT / (shot_id + ext)
        if p.exists():
            return p
    return None


def resize(src, dst, width, quality):
    WEB.mkdir(parents=True, exist_ok=True)
    if shutil.which('sips'):
        subprocess.run(['sips', '-s', 'format', 'jpeg', '-s', 'formatOptions', str(quality),
                        '--resampleWidth', str(width), str(src), '--out', str(dst)],
                       check=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    else:
        shutil.copyfile(src, dst)
    return dst.stat().st_size


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--check', action='store_true')
    a = ap.parse_args()

    shots = {s['id']: s for s in json.loads((HERE / 'shotlist.json').read_text('utf-8'))['shots']}
    cfg = json.loads((HERE / 'mapping.json').read_text('utf-8'))

    needed = sorted({v['id'] for v in cfg['map'].values()})
    have = [i for i in needed if master(i)]
    missing = [i for i in needed if not master(i)]

    print('Кадрів для макетів: %d, згенеровано: %d, бракує: %d' % (len(needed), len(have), len(missing)))
    if missing:
        print('  бракує: ' + ', '.join(missing))
        print('  → python3 generate.py --only ' + ','.join(missing))
    if a.check or not have:
        if not have:
            print('\nНічого не застосовано: теку out/ порожня.')
        return

    # 1. зменшені копії для вебу
    for sid in have:
        shot = shots[sid]
        w = cfg['max_width'].get(shot['ar'], 1400)
        dst = WEB / (sid + '.jpg')
        kb = resize(master(sid), dst, w, cfg['quality']) // 1024
        print('  %-22s %5d px  %4d КБ' % (sid, w, kb))

    # 2. заглушки в HTML
    total = 0
    for html in sorted((ROOT / 'docs').glob('*.html')):
        src = html.read_text('utf-8')
        orig = src
        for text, rec in cfg['map'].items():
            if rec['id'] not in have or text not in src:
                continue
            # банер угорі сторінки вантажиться першим: lazy на ньому псує LCP
            eager = rec['id'].startswith('hero-')
            load = ('loading="eager" fetchpriority="high"' if eager
                    else 'loading="lazy"')
            img = ('<img class="ph-img" src="assets/photos/%s.jpg" alt="%s" '
                   '%s decoding="async">' % (rec['id'], rec['alt'], load))
            pat = re.compile(
                r'(<(?:div|span)\b[^>]*?class="([^"]*?)\bph\b([^"]*?)"[^>]*>)\s*'
                + re.escape(text))

            def sub(m):
                cls = (m.group(2) + m.group(3)).split()
                tag = m.group(1)
                tag = re.sub(r'class="[^"]*"', 'class="%s"' % ' '.join(cls), tag, count=1)
                if not cls:
                    tag = re.sub(r'\s*class=""', '', tag, count=1)
                return tag + img
            src, n = pat.subn(sub, src)
            total += n
        if src != orig:
            html.write_text(src, 'utf-8')
            print('  %s — оновлено' % html.name)
    print('Замінено заглушок: %d' % total)

    # 3. CSS один раз
    css = BASE.read_text('utf-8')
    if CSS_MARK not in css:
        BASE.write_text(css.rstrip() + '\n\n' + CSS_BLOCK, 'utf-8')
        print('base.css — додано правила .ph-img')

    left = re.findall(r'\[ФОТО[^\]]*\]', '\n'.join(
        p.read_text('utf-8') for p in (ROOT / 'docs').glob('*.html')))
    if left:
        print('\nЗалишилось заглушок: %d (зокрема ті, що чекають на справжні фото)' % len(left))


if __name__ == '__main__':
    main()
