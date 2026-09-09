# IwantIt — Google Cloud Maximum Analysis: informe del experimento (0-300s)

**Estado:** fases 0-3 ejecutadas sobre clip 0-300s · **Fecha:** 2026-09-09 ·
**Rama:** `feat/demo-ibc` · **Proyecto GCP:** `iwantit-max-analysis` (europe-west1)

## 1. Objetivo (reformulado en sesión)

No es "hay una camiseta" — eso ya lo sabe el cliente. El objetivo es:

- **Nivel 1 (obligatorio): familia de producto** — camiseta, botella, coche, vaso, botas...
- **Nivel 2 (bonus): marca/modelo** — Nike, Coca-Cola, Galaxy S23... Si llega, perfecto; si no, el experimento sigue siendo éxito.
- **Clearance:** contar entidades distintas que pueden requerir clearance (ej. cuántos puentes hay en el capítulo).
- Un hotpoint con solo L1 sin marca **ya es útil** (entra como `draft`).

## 2. Activo y ground truth

- **Episodio:** Emily in Paris S03E02, `1749565299.mp4` → `demo-vision/mp4/S03E02.mp4` (743 MB, 1986,9 s, 1080p25).
- **Clip smoke:** `demo-vision/mp4/clip_0_300.mp4` (139 MB, 300,1 s). Ambos subidos a `gs://iwantit-exp-emily-s03e02/video/`.
- **Manual:** Proyecto 12 en DB `demo2` — 77 objetos, 57.389 hotpoints, ~120 h de análisis.
- **Detalle técnico DB:** no existe tabla `versions`; `hotpoints.versions_id = projects.id` directamente (verificado: `versions_id=12` → 57.389 filas).
- **GT 0-300s:** `data/ground_truth_0_300.csv` (13.943 hotpoints detalle) + `data/ground_truth_0_300_agregado.csv` (34 productos) + `data/ground_truth_0_300_familias.csv` (34 productos con `family_L1`: **29 directos, 5 NEEDS_FAMILY_REVIEW**).
- **Tolerancias:** ±1 s temporal, ±10 px espacial (0,0052 x / 0,0093 y en 1080p). Evaluación espacial pendiente de cajas.

## 3. Vocabulario L1 congelado v1 (`docs/L1_FAMILIAS.md`)

Moda (18) · Food & Beverage (9) · Tech (6) · Hogar (7) · Vehículo (5) ·
Lugar/geo (9) · Clearance detectable Guía §21.12 (12) · Persona (3) · Texto (4).
Regla: fuera de lista → `NEEDS_FAMILY_REVIEW`, nunca se inventa familia.

## 4. Método por fases

| Fase | Qué | Salida |
|---|---|---|
| 0 | Proyecto GCP + billing + 5 APIs + bucket + subida (todo por CLI, usuario novel guiado) | bucket con episodio + clip |
| 1 | Prompt 1 `family_inventory` (Gemini 2.5-flash, clip 0-300s, 1 pasada) | `01_RAW/gemini_family_0_300.txt` → 93 obs JSONL |
| 1b | Normalización a esquema común (familia, tiempos, conf, evidencia, provenance) | `02_NORMALIZED/observations.jsonl` |
| 1c | Comparativa vs GT con ±1 s (`scripts/compare.py`) | recall L1 16/30 = 53,3 % |
| 3a | Prompt 3 `clearance_detectable` (1 pasada) | 371 obs + 12 summaries |
| 2 | Extracción 1 FPS (300 frames) + Vision (LOGO+TEXT+OBJECT) | `02_NORMALIZED/vision.jsonl` (3.029 detecciones con cajas 0-1) |
| 2b | Caza de los 14 MISS (`scripts/vision_compare.py`) | 4 rescatados → recall combinado 20/30 = 66,7 % |
| 4 | **(pendiente de ejecutar)** crops 1080p de los 10 MISS + Prompt 2 marca | `scripts/make_crops.sh`, `scripts/run_prompt_image.py` |
| 5 | (pendiente) informe final + mapa de capacidades | este documento §8 |

## 5. Resultados Prompt 1 — recall L1 53,3 %

Encontrados (16/30): blazer, bolso 4/4, joya, lámpara, mesa, vestido, coche, gafas 2/3, móvil 2/3, accesorio_moda 2/2.
MISS (14): plaza, restaurante, abrigo, taza, jarra, lata Coca-Cola, Galaxy rosa, silla, chaqueta fucsia, MacBook, Rolex, gafas[AI], escaparate Tiffany, artwork[AI].
Patrón: falla en **objeto pequeño o de fondo**; acierta en familia grande y visible.
Observación de granularidad: los segmentos AI son a nivel de escena (ej. `blazer t[15→135]`), el GT es denso por producto — la métrica usada es solape con ±1 s, no igualdad de segmentación.

## 6. Resultados Vision 1 FPS — 4/14 rescatados (recall 66,7 %)

- Abrigo amarillo: 100 detecciones `Coat` con cajas → evaluable en ±10px.
- Silla: 1 frame (`Chair` 0,515).
- Tiffany: logo conf 0,989, caja estable en 2 frames.
- Marco/artwork: `Picture frame` 2 frames.
- **10 MISS reales** (verificado que no es gap de keywords: en esos rangos Vision solo ve Person/Clothing/Top/Coat): Coca-Cola, Galaxy rosa, MacBook, Rolex, gafas, taza, jarra, chaqueta fucsia, plaza, restaurante.
- `logo_detection` solo disparó en Tiffany; ni Coca-Cola ni Apple ni Samsung ni Rolex a frame completo 1080p. OCR sí lee textos grandes (créditos del opening).

## 7. Resultados Prompt 3 (clearance) — 371 obs, 12 entidades

Puente: **2 distintos** (Pont de Bir-Hakeim + puentes del Sena en vista aérea) · logo_marca: 10 (116 apariciones) · interior_reconocible: 5 · edificio: 4 (incl. Galerie Patrick Fourtin) · packaging: 4 · monumento: 2 (Eiffel + Panthéon) · calle: 2 · escaparate: 2 · artwork: 2 · pantallas: 2 · iglesia: 1 (Sacré-Cœur) · identificador_personal: 1 (bebé).
Todo fuera del GT de producto = **descubrimiento puro**.
⚠️ **Hallazgo de fiabilidad:** timestamps t[384-390] exceden los 300 s del clip — el modelo mezcla conocimiento del episodio o alucina tiempos. En clearance, conteo OK pero tiempos a validar.

## 8. Mapa de capacidades provisional

- **AUTOMATIC/ASSISTED:** familia grande y visible (ropa, bolsos, mobiliario, lugar genérico), OCR de texto grande, transcripción (pendiente de probar STT), conteo de entidades clearance.
- **ASSISTED (humano valida):** marca sobre logo/texto legible (Tiffany 0,989), identidad moda sin logo.
- **MANUAL (hoy):** producto pequeño/fondo (lata, móvil, portátil, reloj, gafas, taza, jarra), lugares específicos sin cartel (plaza, restaurante).
- **NO FIABLE:** timestamps finos de Gemini en pasadas largas (ver §7), person→personaje automático (no probado, queda en candidatos).

## 9. Cómo ejecutar la fase 4 (crops + marca)

```bash
./demo-vision/scripts/make_crops.sh            # 30 crops 1080p en demo-vision/crops/
# ejemplo (repetir por MISS cambiando obs/imágenes/salida):
python demo-vision/scripts/run_prompt_image.py \
  --prompt demo-vision/prompts/02_brand_enrichment.md \
  --obs '{"family":"lata","start_s":157.7,"end_s":162.5}' \
  --images demo-vision/crops/lata_coca_158-163_t158.jpg \
           demo-vision/crops/lata_coca_158-163_t160.jpg \
           demo-vision/crops/lata_coca_158-163_t162.jpg \
  --out demo-vision/01_RAW/brand_lata_coca.txt
```

Ventanas MISS (id_crop): plaza_12-65 · restaurante_12-69 · taza_111-275 ·
jarra_148-298 · lata_coca_158-163 · movil_rosa_166-207 · chaqueta_fucsia_168-208 ·
portatil_180-234 · reloj_229-258 · gafas_262-270.

## 10. Coste y próximos pasos

- Revisar gasto real en Billing → Budgets & alerts (alertas 25/50/80 % creadas en Fase 0).
- Pendiente: STT + diarización (menciones audibles de marca), embeddings (movido a Fase 3), episodio completo 32 min, mapa final AUTOMATIC/ASSISTED/MANUAL.
- Índice de ficheros: `prompts/` (3), `docs/` (checklist, L1, contexto v0.1, este informe), `data/` (3 CSV GT), `scripts/` (run_prompt, normalize, compare, extract_frames, vision_scan, vision_compare, make_crops, run_prompt_image), `01_RAW/`, `02_NORMALIZED/`. `mp4/`, `frames/`, `01_RAW/vision`, `crops/` ignorados por git por tamaño.
