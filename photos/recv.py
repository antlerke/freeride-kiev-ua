#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Локальний приймач кадрів зі сторінки Gemini у браузері.

    python3 recv.py          # слухає 127.0.0.1:8778, Ctrl+C щоб спинити

Сторінка в браузері робить fetch(url).then(r=>r.blob()) і шле блоб сюди,
файл лягає прямо в photos/out/. Через чат картинка не проходить.

Приймає тільки імена з shotlist.json і слухає тільки петлю — назовні
порт не виставлений.
"""
import http.server, json, os, pathlib, socketserver, urllib.parse

HERE = pathlib.Path(__file__).resolve().parent
OUT = HERE / 'out'
PORT = 8778
EXT = {'image/jpeg': '.jpg', 'image/png': '.png', 'image/webp': '.webp'}
ALLOWED = {s['id'] for s in json.loads((HERE / 'shotlist.json').read_text('utf-8'))['shots']}


class H(http.server.BaseHTTPRequestHandler):
    def _cors(self):
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'POST, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', '*')
        # Chrome: запит із публічного сайту в локальну мережу вимагає цього
        self.send_header('Access-Control-Allow-Private-Network', 'true')

    def do_OPTIONS(self):
        self.send_response(204)
        self._cors()
        self.end_headers()

    def do_GET(self):
        self.send_response(200)
        self._cors()
        self.send_header('Content-Type', 'text/plain; charset=utf-8')
        self.end_headers()
        self.wfile.write(('приймач живий, уже прийнято: %d\n'
                          % len(list(OUT.glob('*')))).encode())

    def do_POST(self):
        q = urllib.parse.parse_qs(urllib.parse.urlparse(self.path).query)
        name = (q.get('name') or [''])[0]
        if name not in ALLOWED:
            self.send_response(400)
            self._cors()
            self.end_headers()
            self.wfile.write(b'unknown shot id')
            print('  відмова: невідомий кадр %r' % name[:40])
            return
        n = int(self.headers.get('Content-Length') or 0)
        blob = self.rfile.read(n)
        ext = EXT.get((self.headers.get('Content-Type') or '').split(';')[0], '.png')
        OUT.mkdir(exist_ok=True)
        for old in OUT.glob(name + '.*'):
            old.unlink()
        (OUT / (name + ext)).write_bytes(blob)
        print('  ← %s%s  %d КБ' % (name, ext, len(blob) // 1024))
        self.send_response(200)
        self._cors()
        self.end_headers()
        self.wfile.write(b'ok')

    def log_message(self, *a):
        pass


if __name__ == '__main__':
    os.chdir(HERE)
    socketserver.TCPServer.allow_reuse_address = True
    with socketserver.TCPServer(('127.0.0.1', PORT), H) as srv:
        print('Приймач на http://127.0.0.1:%d — чекаю кадри в %s' % (PORT, OUT))
        try:
            srv.serve_forever()
        except KeyboardInterrupt:
            print('\nспинив')
