#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Друкує готовий однорядковий промпт для вставки в чат Gemini.
    python3 prompts.py            — список id
    python3 prompts.py hero-calc  — промпт цього кадру
"""
import json, pathlib, sys
d = json.loads((pathlib.Path(__file__).resolve().parent / 'shotlist.json').read_text('utf-8'))
out = pathlib.Path(__file__).resolve().parent / 'out'
if len(sys.argv) < 2:
    for s in d['shots']:
        done = '✓' if list(out.glob(s['id'] + '.*')) else ' '
        print('%s %-22s %s' % (done, s['id'], s['ar']))
    sys.exit()
s = next(x for x in d['shots'] if x['id'] == sys.argv[1])
print('Generate a %s image. %s SCENE: %s AVOID: %s'
      % (s['ar'], d['preamble'], s['prompt'], d['negative']))
