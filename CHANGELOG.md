# Changelog

Todos los cambios notables de este proyecto se documentan en este fichero. Formato basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/).

## [v. 0.3] - 2026-09-03

### Añadido
- Seeder completo del dataset curado de Alex para el proyecto #12 Emily in Paris (34 scenes, 36 elements, 243 appearances, 19 advertising contexts).

### Cambiado
- **WowDatasetImporter (`app/Services/WowDatasetImporter.php`)**: compatibilidad con el nuevo formato de CSVs de Alex.
  - `01_scenes.csv`: soporte para 3 filas de título + cabecera desplazada, `key_context_1/2/3` desagregados (antes `key_contexts` único), timecode `HH:MM:SS:FF` (frames con `:`) además de `HH:MM:SS,mmm`/`HH:MM:SS.mmm`.
  - `02_elements.csv`: columnas `family`, `source`, `provenance`; mapeo `PLACE` → `location`; tipo case-insensitive; `BRAND`/`PRODUCT`/`OBJECT` soportados.
  - `03_appearances.csv`: columnas `modality_1/2`, `provenance`, `relevant_for_1/2/3` (antes `relevant_for` único), `w/h` y `pos_x/pos_y` vacíos → `0`, `source=IMPORTED` → `manual`, timecode con `.` y frames.
  - `04_advertising_opportunities.csv`: nuevo esquema `AdvertisingContext` (`advertising_context_id_ref,context,context_quality,scene_ref_1/2/3,elements_involved;` con nombres, `rationale`); creación de 1 oportunidad por `scene_ref` representativo (compatibilidad legacy `opportunity_id_ref/scene_id_ref/appearance_id_ref/value_level/contexts` mantenida).
  - Parsing robusto `;` vs `,` y extracción `E\d+` de tokens tipo `E009 Restaurant Terra Nera`.

### Corregido
- **Diagnóstico de importación**: errores de CSV ahora incluyen `fichero`, `línea`, `columna`, `valor` y fila JSON (ej. `01_scenes.csv línea 6 columna start_time: Timecode vacío...`). Clases `refOrFail`, `requireValue`, `parseTimecode` relanzan `RuntimeException` con contexto para `wow:import`.

### Infra
- Versión `IWI_VERSION` `v. 0.2` → `v. 0.3`, `IWI_YEAR` `2025` → `2026`.

## [v. 0.2] - 2025
- Backend slice WOW: migraciones P0 (11 tablas), modelos/enums, importador inicial, `WowAnalysisService` y endpoints `/analysis/overview` y `/advertising-opportunities` con tab Analysis.
