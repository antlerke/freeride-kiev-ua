#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Дістає кадр із дампа javascript_tool у photos/out/<id>.jpg.

Коли результат javascript_tool завеликий, інструмент сам зберігає його
у файл — базова64 туди потрапляє повз контекст моделі. Цей скрипт її декодує.

    python3 pull.py <shot-id>                 — бере найсвіжіший дамп
    python3 pull.py <shot-id> <шлях-до-дампа> — конкретний файл
"""
import base64, glob, json, os, pathlib, re, sys

TOOLS = pathlib.Path.home() / '.claude/projects/-Users-saisai--gemini-antigravity-scratch-tg-survey-bot/d22dc22f-d962-42c3-8792-0c7ee3cb8185/tool-results'

shot = sys.argv[1]
if len(sys.argv) > 2:
    dump = pathlib.Path(sys.argv[2])
else:
    cands = sorted(TOOLS.glob('mcp-Claude_Browser-javascript_tool-*.txt'), key=os.path.getmtime)
    if not cands:
        sys.exit('Дампів javascript_tool не знайдено')
    dump = cands[-1]
raw = dump.read_text('utf-8')
try:
    data = json.loads(raw)
    text = ''.join(p.get('text', '') for p in data) if isinstance(data, list) else str(data)
except json.JSONDecodeError:
    text = raw
b64 = re.sub(r'[^A-Za-z0-9+/=]', '', text.strip().strip('"'))
blob = base64.b64decode(b64)
if blob[:2] != b'\xff\xd8':
    sys.exit('Це не JPEG (перші байти %r)' % blob[:8])
out = pathlib.Path(__file__).resolve().parent / 'out'
out.mkdir(exist_ok=True)
for old in out.glob(shot + '.*'):
    old.unlink()
dest = out / (shot + '.jpg')
dest.write_bytes(blob)
print('%s  %d КБ' % (dest.name, len(blob) // 1024))
