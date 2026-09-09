#!/bin/bash
# Lote Prompt 2 (marca) sobre los 10 MISS. Copiar-pegar y listo:
#   ./demo-vision/scripts/run_brand_batch.sh
# Requiere: crops generados (make_crops.sh) y ADC login.
# Sigue aunque falle uno; al final lista qué salidas faltan.
cd "$(dirname "$0")/../.." || exit 1
PY="python demo-vision/scripts/run_prompt_image.py --prompt demo-vision/prompts/02_brand_enrichment.md"
OK=0; FAIL=0; FAILED=""
run() { # $1=id_obs $2=obs_json $3tesis... (imgs) $4=out
  local id="$1"; local obs="$2"; local out="$3"; shift 3
  echo "=== $id ==="
  # shellcheck disable=SC2086
  if $PY --obs "$obs" --images "$@" --out "$out"; then OK=$((OK+1));
  else FAIL=$((FAIL+1)); FAILED="$FAILED $id"; fi
}

run plaza '{"family":"plaza","start_s":12.0,"end_s":65.0}' \
  demo-vision/01_RAW/brand_plaza.txt \
  demo-vision/crops/plaza_12-65_t12.jpg demo-vision/crops/plaza_12-65_t38.jpg demo-vision/crops/plaza_12-65_t65.jpg

run restaurante '{"family":"restaurante_bar","start_s":12.0,"end_s":69.0}' \
  demo-vision/01_RAW/brand_restaurante.txt \
  demo-vision/crops/restaurante_12-69_t12.jpg demo-vision/crops/restaurante_12-69_t40.jpg demo-vision/crops/restaurante_12-69_t69.jpg

run taza '{"family":"taza","start_s":110.7,"end_s":274.6}' \
  demo-vision/01_RAW/brand_taza.txt \
  demo-vision/crops/taza_111-275_t111.jpg demo-vision/crops/taza_111-275_t190.jpg demo-vision/crops/taza_111-275_t274.jpg

run jarra '{"family":"jarra","start_s":148.0,"end_s":297.7}' \
  demo-vision/01_RAW/brand_jarra.txt \
  demo-vision/crops/jarra_148-298_t148.jpg demo-vision/crops/jarra_148-298_t220.jpg demo-vision/crops/jarra_148-298_t297.jpg

run lata '{"family":"lata","start_s":157.7,"end_s":162.5}' \
  demo-vision/01_RAW/brand_lata_coca.txt \
  demo-vision/crops/lata_coca_158-163_t158.jpg demo-vision/crops/lata_coca_158-163_t160.jpg demo-vision/crops/lata_coca_158-163_t162.jpg

run movil_rosa '{"family":"movil","start_s":165.6,"end_s":207.4}' \
  demo-vision/01_RAW/brand_movil_rosa.txt \
  demo-vision/crops/movil_rosa_166-207_t166.jpg demo-vision/crops/movil_rosa_166-207_t186.jpg demo-vision/crops/movil_rosa_166-207_t207.jpg

run chaqueta_fucsia '{"family":"chaqueta","start_s":167.9,"end_s":207.8}' \
  demo-vision/01_RAW/brand_chaqueta_fucsia.txt \
  demo-vision/crops/chaqueta_fucsia_168-208_t168.jpg demo-vision/crops/chaqueta_fucsia_168-208_t188.jpg demo-vision/crops/chaqueta_fucsia_168-208_t207.jpg

run portatil '{"family":"portatil","start_s":180.0,"end_s":234.4}' \
  demo-vision/01_RAW/brand_portatil.txt \
  demo-vision/crops/portatil_180-234_t180.jpg demo-vision/crops/portatil_180-234_t207.jpg demo-vision/crops/portatil_180-234_t234.jpg

run reloj '{"family":"reloj","start_s":229.3,"end_s":258.2}' \
  demo-vision/01_RAW/brand_reloj.txt \
  demo-vision/crops/reloj_229-258_t230.jpg demo-vision/crops/reloj_229-258_t244.jpg demo-vision/crops/reloj_229-258_t258.jpg

run gafas '{"family":"gafas","start_s":261.9,"end_s":270.0}' \
  demo-vision/01_RAW/brand_gafas.txt \
  demo-vision/crops/gafas_262-270_t262.jpg demo-vision/crops/gafas_262-270_t266.jpg demo-vision/crops/gafas_262-270_t270.jpg

echo "----------------------------------------"
echo "OK=$OK FAIL=$FAIL$FAILED"
