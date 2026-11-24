#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Генерує фото за shotlist.json через Gemini Image API. Лише стандартна бібліотека.

    export GEMINI_API_KEY=...        (або поклади рядок GEMINI_API_KEY=... у photos/.env)
    python3 generate.py --list
    python3 generate.py                      # усе, чого ще немає в out/
    python3 generate.py --only hero-home,band-serpentine --force
    python3 generate.py --flash              # дешева модель для чернеток

Ключ ніде не друкується і нікуди не записується.
"""
import argparse, base64, json, os, pathlib, sys, time, urllib.error, urllib.request

HERE = pathlib.Path(__file__).resolve().parent
OUT = HERE / 'out'
PRO = 'gemini-3-pro-image-preview'
FLASH = 'gemini-2.5-flash-image'
ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent'


def api_key():
    k = os.environ.get('GEMINI_API_KEY', '').strip()
    if not k:
        env = HERE / '.env'
        if env.exists():
            for line in env.read_text(encoding='utf-8').splitlines():
                line = line.strip()
                if line.startswith('GEMINI_API_KEY='):
                    k = line.split('=', 1)[1].strip().strip('"\'')
    if not k:
        sys.exit('Немає GEMINI_API_KEY. Постав змінну оточення або поклади її в photos/.env '
                 '(файл поза git). Ключ у виводі скрипта не зʼявляється.')
    return k


def build_prompt(shot, data):
    return '%s\n\n%s\n\nAvoid: %s' % (data['preamble'], shot['prompt'], data['negative'])


def call(model, prompt, ar, size, key, tries=4):
    body = json.dumps({
        'contents': [{'parts': [{'text': prompt}]}],
        'generationConfig': {
            'responseModalities': ['IMAGE'],
            'imageConfig': {'aspectRatio': ar, 'imageSize': size},
        },
    }).encode('utf-8')
    req = urllib.request.Request(
        ENDPOINT % model, data=body,
        headers={'Content-Type': 'application/json', 'x-goog-api-key': key})
    for n in range(tries):
        try:
            with urllib.request.urlopen(req, timeout=300) as r:
                payload = json.load(r)
            break
        except urllib.error.HTTPError as e:
            detail = e.read().decode('utf-8', 'replace')[:400]
            if e.code in (429, 500, 503) and n < tries - 1:
                wait = 8 * (n + 1)
                print('    %s, повтор через %ds' % (e.code, wait))
                time.sleep(wait)
                continue
            raise SystemExit('    HTTP %s: %s' % (e.code, detail))
    for cand in payload.get('candidates', []):
        for part in cand.get('content', {}).get('parts', []):
            inline = part.get('inlineData') or part.get('inline_data')
            if inline and inline.get('data'):
                mime = inline.get('mimeType') or inline.get('mime_type') or 'image/png'
                return base64.b64decode(inline['data']), mime
    raise SystemExit('    Модель не повернула зображення: %s' % json.dumps(payload)[:400])


def main():
    p = argparse.ArgumentParser()
    p.add_argument('--only', default='', help='через кому id кадрів')
    p.add_argument('--force', action='store_true', help='перегенерувати наявні')
    p.add_argument('--flash', action='store_true', help='швидка дешева модель')
    p.add_argument('--list', action='store_true', help='показати шотліст і вийти')
    a = p.parse_args()

    data = json.loads((HERE / 'shotlist.json').read_text(encoding='utf-8'))
    shots = data['shots']

    if a.list:
        for s in shots:
            mark = ' [потрібне справжнє фото]' if s.get('real') else ''
            print('%-22s %-5s %-3s %s%s' % (s['id'], s['ar'], s['size'],
                                            s['prompt'][:70], mark))
        return

    want = [x.strip() for x in a.only.split(',') if x.strip()]
    if want:
        unknown = set(want) - {s['id'] for s in shots}
        if unknown:
            sys.exit('Немає таких кадрів: %s' % ', '.join(sorted(unknown)))
        shots = [s for s in shots if s['id'] in want]

    OUT.mkdir(exist_ok=True)
    key = api_key()
    model = FLASH if a.flash else PRO
    made = skipped = 0
    for s in shots:
        dest = OUT / ('%s.png' % s['id'])
        if dest.exists() and not a.force:
            skipped += 1
            continue
        print('%s  (%s, %s, %s)' % (s['id'], s['ar'], s['size'], model))
        blob, mime = call(model, build_prompt(s, data), s['ar'], s['size'], key)
        if mime == 'image/jpeg':
            dest = dest.with_suffix('.jpg')
        dest.write_bytes(blob)
        print('    → %s  %d КБ' % (dest.name, len(blob) // 1024))
        made += 1
    print('\nЗгенеровано %d, пропущено наявних %d. Далі: python3 apply.py' % (made, skipped))


if __name__ == '__main__':
    main()
