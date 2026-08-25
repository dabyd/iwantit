# Traspaso Automático AI → Hotpoints

## Objetivo

Modificar la pestaña "AI Objects Detection" en Projects para que tenga un botón que traspase **todos** los objetos detectados por AI a hotpoints de forma automática, con parámetros de proximidad configurables, vinculación para evitar duplicados, estado draft/published, y deshacer.

---

## Lista de pasos (checkpoints)

### Paso 1 — Migración 1: Añadir columnas a hotpoints
**Archivo:** `database/migrations/xxxx_add_status_and_batch_to_hotpoints.php`
- `status` (string 20, default 'draft')
- `datision_result_id` (unsignedBigInt, nullable) → FK a `datision_results`
- `ai_import_batch_id` (unsignedBigInt, nullable) → FK a `ai_import_batches`

### Paso 2 — Migración 2: Crear tabla ai_import_batches
**Archivo:** `database/migrations/xxxx_create_ai_import_batches_table.php`
- `id`, `project_id`, `previous_editor_json` (json, nullable), `created_product_ids` (json, nullable), `created_brand_ids` (json, nullable), `status` (string 20, default 'active'), timestamps

### Paso 3 — Ejecutar migraciones
```bash
php artisan migrate
```

### Paso 4 — Modelo Hotpoint
**Archivo:** `app/Models/Hotpoint.php`
- Añadir a `$fillable`: `status`, `datision_result_id`, `ai_import_batch_id`
- Añadir scope `published()` → `where('status', 'published')`

### Paso 5 — Modelo AiImportBatch
**Archivo:** `app/Models/AiImportBatch.php`
- Modelo simple con `$guarded = []`
- Casts para `previous_editor_json`, `created_product_ids`, `created_brand_ids` → 'array'

### Paso 6 — Helper: groupDetectionsByProximity()
**Archivo:** `app/Http/Controllers/DatisionController.php`
- Nuevo método que toma detecciones (Collection), frameGap, xRange, yRange
- Agrupa por proximidad de frame + coordenadas
- Devuelve grupos con detection_ids y frames

### Paso 7 — Método: autoExportToHotpoints()
**Archivo:** `app/Http/Controllers/DatisionController.php`
- Recibe `project_id`, `proximity_frames`, `proximity_x`, `proximity_y`
- Crea batch con snapshot del editor JSON
- Itera todas las clases → productos → agrupa detecciones → crea hotpoints → genera JSON editor
- Transacción + batch tracking

### Paso 8 — Método: undoAutoExport()
**Archivo:** `app/Http/Controllers/DatisionController.php`
- Busca último batch `active` del proyecto
- Elimina hotpoints, productos, marcas del batch
- Restaura snapshot del editor JSON
- Marca batch como `undone`

### Paso 9 — Rutas
**Archivo:** `routes/web.php`
- `POST /datision-auto-export`
- `POST /datision-undo-export`

### Paso 10 — API pública (IwantitController)
**Archivo:** `app/Http/Controllers/IwantitController.php`
- `get_hotpoints()`: añadir `->where('hotpoints.status', 'published')`
- `save_hotpoints()`: setear `status = 'published'` en hotpoints creados

### Paso 11 — Export manual existente
**Archivo:** `app/Http/Controllers/DatisionController.php` (método `exportToHotpoints`)
- Añadir `'status' => 'draft'` en la creación de hotpoints

### Paso 12 — UI: HTML inputs y botones
**Archivo:** `resources/views/components/layouts/tab-aiobjects.blade.php`
- 3 inputs: Frame gap (default 2), X range (default 10), Y range (default 10)
- Botón "Transfer ALL objects to hotpoints"
- Botón "Undo last auto-transfer"
- Span de estado

### Paso 13 — UI: JavaScript
**Archivo:** `resources/views/components/layouts/tab-aiobjects.blade.php`
- Fetch a `/datision-auto-export` con project_id + 3 parámetros
- Fetch a `/datision-undo-export` con project_id
- Manejo de errores y feedback visual

### Paso 14 — Tests / Verificación
```bash
php artisan test
# Prueba manual: abrir proyecto con detecciones AI, probar flujo completo
```

---

## Notas técnicas

- **Undo scope completo**: hotpoints + JSON editor + productos creados + marcas creadas
- **Estado draft/published**: AI imports → draft, editor manual → published, API pública → solo published
- **Parámetros UI-only**: no se persisten, valores por defecto 2/10/10 al cargar página
- **Vinculación**: `datision_detection_id` (existente) + `datision_result_id` (nuevo) para evitar doble import
- **Transacción**: todo el auto-export va en DB::transaction() para atomicidad
