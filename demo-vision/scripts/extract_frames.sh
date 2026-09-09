#!/bin/bash
# Extrae 1 FPS del clip para el barrido Vision.
# Uso: ./extract_frames.sh [clip] [salida]
set -e
CLIP="${1:-demo-vision/mp4/clip_0_300.mp4}"
OUT="${2:-demo-vision/frames}"
mkdir -p "$OUT"
/opt/homebrew/bin/ffmpeg -y -i "$CLIP" -vf fps=1 "$OUT/frame_%04d.jpg"
echo "Frames en $OUT: $(ls "$OUT"/frame_*.jpg | wc -l)"
