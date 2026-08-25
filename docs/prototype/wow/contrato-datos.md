# Contrato de Datos — Slice "WOW"

**Versión:** v1 · **Estado:** DRAFT (a congelar en Fase 0)
**Base:** `PLAN_MVP_IBC.md §3.1`
**Responsable:** David (define) + Alex (valida que puede rellenarlo)

## Convenciones

- **Timecode**: Alex escribe en formato SRT `HH:MM:SS,mmm` (también vale `HH:MM:SS.mmm`). David lo convierte a **milisegundos** (`start_ms`, `end_ms`, rango `[start, end)`).
- **Coordenadas**: posición aproximada en % de la pantalla (0–100). Origen `(0,0)` = esquina superior izquierda. `pos_x`/`pos_y` = centro del objeto; `w`/`h` = ancho/alto del objeto en %.
- **IDs**: Alex usa referencias cortas (`S01`, `E01`, `A01`, `OP01`) en las plantillas; David las resuelve a IDs reales en BD.

## Enums cerrados

| Campo | Valores |
|---|---|
| `inventory_items.type` | `product, brand, person, character, location, object, identifier, artwork, screen, document, text, audio_work` |
| `appearances.source` | `manual, datision, ocr, asr, object_detection, logo_detection, vlm, llm, other` |
| `appearance_relevances.vertical` | `advertising, interactive, clearance` |
| `advertising_opportunities.value_level` | `high, medium, low` |
| `taxons.taxonomy` | `key_context, inventory_type, family, clearance_family` |
| `validations.status` | `unvalidated, validated, rejected` |
| `detection_candidates.status` | `pending, accepted, rejected` |

## Key Contexts (lista cerrada)

`Automotive, Travel, Luxury, Fashion, Food & Beverage, Technology, Sports, Family, Beauty, Home & Living`

## Tablas y qué se siembra en el slice

| Tabla | Campos clave | ¿Se siembra en wow? |
|---|---|---|
| `scenes` | `project_id, position, start_time, end_time, name` | ✅ Sí |
| `inventory_items` | `project_id, name, type, brand_id?, canonical_id?, created_by` | ✅ Sí |
| `appearances` | `inventory_item_id, scene_id, start_time, end_time, pos_x, pos_y, w, h, source, provenance, created_by` | ✅ Sí |
| `evidence` | `evidenceable_type/id, type, file_path, timecode, note, source, provider, model, generated_at, validation_status, created_by` | ✅ Pocos frames/crops |
| `validations` | `appearance_id, status, actor, reason, created_at` | ⚠️ Mínimo |
| `contextual_relationships` | `project_id, source_item_id, target_item_id, relationship_type, scene_id, evidence_id?, created_by` | ⚠️ Mínimo |
| `taxons` | `taxonomy, name, parent_id` | ✅ Key Contexts |
| `taxon_assignments` | `taxon_id, assignable_type, assignable_id, created_by` | ✅ (scenes) |
| `appearance_relevances` | `appearance_id, vertical, created_by` | ✅ ("Relevant For") |
| `analysis_runs` | `project_id, provider, status, config, started_at, finished_at` | ❌ Vacío (proveedor posterior) |
| `detection_candidates` | `analysis_run_id, class, start/end, pos, confidence, status, inventory_item_id?` | ❌ Vacío (proveedor posterior) |
| `advertising_opportunities` | `project_id, appearance_id?, scene_id?, value_level, rationale, created_by` | ✅ Sí |

> Las tablas se crean TODAS (11) en migraciones, aunque solo se siembren las marcadas. Workspace/Inspector/Clearance se apoyarán en ellas después sin refactor.

## Reglas de contenido

- Nada inventado: los datos vienen del episodio real (SRT + vídeo).
- Todo Appearance tiene origen (`source`) y, en lo posible, Evidence asociada.
- Advertising H/M/L es **criterio humano explicable** (rationale), nunca score automático.
- Key Context ≠ narrativa; se asignan a Scene (una scene → varios contextos).
