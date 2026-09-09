#!/bin/bash
# Recorta frames a resolución original en los momentos MISS (3 por item:
# inicio/medio/fin). Sin cajas previas: frame completo 1080p.
# Uso: ./make_crops.sh [video] [salida]
set -e
VIDEO="${1:-demo-vision/mp4/S03E02.mp4}"
OUT="${2:-demo-vision/crops}"
FF="/opt/homebrew/bin/ffmpeg"
mkdir -p "$OUT"

# formato: "id t1 t2 t3"
CROPS="
plaza_12-65 12 38 65
restaurante_12-69 12 40 69
taza_111-275 111 190 274
jarra_148-298 148 220 297
lata_coca_158-163 158 160 162
movil_rosa_166-207 166 186 207
chaqueta_fucsia_168-208 168 188 207
portatil_180-234 180 207 234
reloj_229-258 230 244 258
gafas_262-270 262 266 270
"

echo "$CROPS" | grep -v '^$' | while read -r id t1 t2 t3; do
  for t in $t1 $t2 $t3; do
    f="$OUT/${id}_t${t}.jpg"
    [ -f "$f" ] && { echo "skip $f"; continue; }
    $FF -y -v error -ss "$t" -i "$VIDEO" -frames:v 1 -q:v 2 "$f"
    echo "ok $f"
  done
done
echo "Crops en $OUT: $(ls "$OUT"/*.jpg 2>/dev/null | wc -l)"
