# PHASE 0 — Checklist Google Cloud (paso a paso para David)

Objetivo: proyecto + bucket + APIs + clip subido, con control de gasto
de los 2000 créditos. Tiempo estimado: 30-45 min la primera vez.

## Lo que HACES TÚ (necesita tu login de Google)

### Paso 1 — Proyecto y facturación (console.cloud.google.com)
- [ ] Crea proyecto `iwantit-max-analysis`.
- [ ] Activa Billing con los 2000 de crédito en ese proyecto.
- [ ] Ve a Billing → Budgets & alerts → crea alerta 25% / 50% / 80%.
- [ ] Apunta el PROJECT_ID real (a veces añade sufijo).

### Paso 2 — APIs (una sola vez)
En Cloud Shell (botón `>_` arriba en la consola) pega:
```bash
gcloud config set project iwantit-max-analysis
gcloud services enable \
  videointelligence.googleapis.com \
  vision.googleapis.com \
  speech.googleapis.com \
  aiplatform.googleapis.com \
  storage.googleapis.com
```

### Paso 3 — Bucket y subida del vídeo
```bash
gcloud storage buckets create gs://iwantit-exp-emily-s03e02 --location=europe-west1
# desde donde tengas el MP4 (32 min, 1080p):
gcloud storage cp S03E02.mp4 gs://iwantit-exp-emily-s03e02/video/emily_s03e02.mp4
# clip 0-5 min para el smoke (lo generas en local con ffmpeg):
ffmpeg -ss 0 -t 300 -i S03E02.mp4 -c copy clip_0_300.mp4
gcloud storage cp clip_0_300.mp4 gs://iwantit-exp-emily-s03e02/video/clip_0_300.mp4
```

### Paso 4 — Primera ejecución (Vertex AI Studio, sin código)
1. Consola → Vertex AI → AI Studio → "Create prompt" → modelo Gemini
   multimodal (el más reciente disponible).
2. Adjunta `gs://iwantit-exp-emily-s03e02/video/clip_0_300.mp4`
   (botón de adjuntar → Cloud Storage URI).
3. Pega el contenido de `prompts/01_family_inventory.md` y ejecuta.
4. Guarda la respuesta tal cual en `01_RAW/gemini_family_0_300.txt`.
5. Repite con `prompts/03_clearance_detectable.md`.
6. Mira en Billing cuánto costó: ese es tu coste unitario x5 min.

## Lo que HAGO YO (cuando me confirmes cada punto)
- [x] Vocabulario L1 congelado (`L1_FAMILIAS.md`).
- [x] Prompts 01/02/03 listos para pegar.
- [x] Ground truth 0-300s exportado + mapeado a familias.
- [ ] Script `normalize.py`: convierte respuestas Gemini → `02_NORMALIZED/observations.jsonl`
      → `05_IMPORT/hotpoints_draft.json` (formato de tu `Hotpoint`).
- [ ] Script `extract_frames.sh`: 1 FPS + crops para Vision/VideoIntel.
- [ ] Comparativa AI vs manual con tolerancias ±1s / ±10px y métricas L1/L2.
