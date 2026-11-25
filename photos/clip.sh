#!/usr/bin/env bash
# Записує зображення з буфера обміну в photos/out/<id>.png
# (кнопка «Копіювати зображення» в Gemini кладе його в буфер macOS).
set -euo pipefail
[ $# -eq 1 ] || { echo "вжиток: ./clip.sh <shot-id>" >&2; exit 1; }
HERE="$(cd "$(dirname "$0")" && pwd)"
mkdir -p "$HERE/out"
DEST="$HERE/out/$1.png"
osascript <<EOF
set p to (POSIX file "$DEST")
try
    set d to the clipboard as «class PNGf»
on error
    error "у буфері немає зображення"
end try
set f to open for access p with write permission
set eof f to 0
write d to f
close access f
EOF
printf '%s  %s КБ\n' "$1.png" "$(( $(stat -f%z "$DEST") / 1024 ))"
