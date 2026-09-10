# demo-vision — Experimento Google Cloud Maximum Analysis (Emily in Paris S03E02)

Objetivo: medir hasta dónde llega la IA de Google Cloud reconociendo
**familias de producto** (Nivel 1 obligatorio) y **marca/modelo** (Nivel 2 bonus),
contra el análisis manual del Proyecto 12 (120h, 57.389 hotpoints).

## Estructura

- `prompts/` — los 3 prompts listos para pegar en Vertex AI Studio:
  - `01_family_inventory.md` — Nivel 1: familia (vocabulario cerrado).
  - `02_brand_enrichment.md` — Nivel 2: marca con evidencia o UNKNOWN.
  - `03_clearance_detectable.md` — clearance + conteo de entidades distintas.
- `docs/` — `L1_FAMILIAS.md` (vocabulario congelado v1) y
  `00_PHASE0_CHECKLIST.md` (guía paso a paso GCP).
- `data/` — ground truth 0-300s desde `demo2` (`versions_id = 12`):
  - `ground_truth_0_300.csv` — 13.943 hotpoints detalle.
  - `ground_truth_0_300_agregado.csv` — 34 productos agregados.
  - `ground_truth_0_300_familias.csv` — 34 productos con `family_L1`
    (29 directos, 5 NEEDS_FAMILY_REVIEW).

## Estado

Tolerancias de evaluación: ±1s temporal, ±10px espacial.
Clip smoke test: primeros 5 min del episodio. Pendiente: crear proyecto GCP
y subir el clip (ver `docs/00_PHASE0_CHECKLIST.md`, pasos de David).
