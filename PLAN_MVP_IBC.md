# PLAN — MVP IBC (11–12 septiembre 2026) — v1.1 FINAL

**Base:** Guía Maestra v2.3 RC1 + `IwantIt_MVP_Respuestas_Alcance.md` + `IwantIt_MVP_IBC_Respuestas_Plan.md`
**Equipo:** 1 Backend + 1 Frontend + Product (Alejandro, PO y aprobador final)
**Objetivo:** ampliar el MVP real para demostrar que *"IwantIt entiende qué ocurre dentro del contenido, lo estructura una vez y lo convierte en inteligencia reutilizable para Advertising, Interactive y Clearance"*.

---

## 1. Decisiones adoptadas (definitivas)

| # | Decisión | Detalle |
|---|----------|---------|
| D1 | Demo = **producto real**, no prototipo | Todo entra en el MVP Laravel |
| D2 | **Híbrido** v2.3 | Semántica v2.3 para lo nuevo; Object/Hotpoint/Key File se mantienen vía adapters |
| D3 | **Object NO es base de la nueva IA**; **Hotpoint NO mezcla aparición con activación** | Modelo nuevo sobre InventoryItem + Appearance |
| D4 | No romper player, demo pública, APIs, Key Files, Projects | Compatibilidad obligatoria |
| D5 | Datision = `PrototypeAnalysisProvider` (experimental) | Se normaliza a `detection_candidates` |
| D6 | Datos demo **sembrados manualmente**, reales y verificables | Sin cifras económicas inventadas |
| D7 | UI **inglés**; naming aprobado | Ver §7 |
| D8 | Sin biometría | IA detecta **Person** → resolución humana a **Character** |
| D9 | **ContentVersion = 1:1 con `projects`** | No se crea la separación completa ahora |
| D10 | Frontend **Blade + Vite + JS ligero** | Sin React/Inertia |
| D11 | **Clearance y Person→Character comprometidos** | No se descopan aunque lleven más tiempo |
| D12 | **IBC = gate duro para P0**; fecha inamovible | P1 se entrega justo después si no está estable |
| D13 | Caso estrella interno: **Emily in Paris S3E2** | Uso **interno/privado** (ver DEMO_RIGHTS_BLOCKER §9) |

---

## 2. Estado actual (mapeo legacy → v2.3)

| Legacy (MySQL `demo2`) | Rol actual | Destino MVP ampliado |
|---|---|---|
| `projects` | Project + vídeo + owner | Ancla `project_id`; actúa como ContentVersion de facto |
| `products` | "Object" | `inventory_items` (identidad local). Product queda como catálogo/destino |
| `brands` | Catálogo marcas | Referenciado desde `inventory_items.brand_id` |
| `hotpoints` | Aparición + interacción | `appearances` (verdad audiovisual). `hotpoints` sigue alimentando el player |
| `hotpoints_dates` | Config comercial | Fuera de InventoryItem (config Interactive, P2) |
| `licenses` | Key File (SRT) | Se mantiene para serving/player |
| `datision_*` | Piloto IA | `analysis_runs` + `detection_candidates` |
| `click_statistics` | views/clicks/view_p | Se complementan con métricas derivadas del modelo nuevo |

**Convención FK:** tablas nuevas usan `project_id` → `projects.id` (el legado usa `versions_id` para lo mismo; se documenta, no se renombra).

---

## 3. Modelo de datos final

### 3.1 Tablas P0 (gate IBC)

1. **`scenes`** — cobertura temporal ordenada
   - `id, project_id, position, start_time, end_time, name`
   - Invariante: ordenadas, sin huecos ni solapamientos (validado en backend).

2. **`inventory_items`** — "Identified Elements"
   - `id, project_id, name, type, brand_id nullable, canonical_id nullable, created_by`
   - `type` enum cerrado: `product, brand, person, character, location, object, identifier, artwork, screen, document, text, audio_work`.

3. **`appearances`** — aparición concreta (verdad audiovisual)
   - `id, inventory_item_id, scene_id, start_time, end_time, pos_x, pos_y, w, h, source, provenance, created_by`
   - `source` ∈ `manual, datision, ocr, asr, object_detection, logo_detection, vlm, llm, other`.
   - `time-on-screen` **derivado** (suma de intervalos), no almacenado.

4. **`evidence`** — prueba del conocimiento (polimórfico)
   - `id, evidenceable_type, evidenceable_id, type (frame, crop, text, dialog, audio, document, manual), file_path, timecode, note, source, provider, model, generated_at, validation_status, created_by`.

5. **`validations`** — decisión de calidad inmutable (append-only)
   - `id, appearance_id, status (unvalidated, validated, rejected), actor, reason, created_at`.

6. **`contextual_relationships`** — relaciones entre elementos
   - `id, project_id, source_item_id, target_item_id, relationship_type, scene_id, evidence_id nullable, created_by`.

7. **`taxons` + `taxon_assignments`** — Key Contexts, Family y registry de familias Clearance
   - `taxons: id, taxonomy (key_context | inventory_type | family | clearance_family), name, parent_id`.
   - `taxon_assignments: id, taxon_id, assignable_type, assignable_id (polimórfico: scene | appearance | inventory_item), created_by`.
   - Key Contexts se asignan **principalmente a Scene** (una Scene → varios contextos).

8. **`appearance_relevances`** — "Relevant For" (overlay a nivel Appearance)
   - `id, appearance_id, vertical (advertising, interactive, clearance), created_by`.
   - `Relevant For` ≠ Availability / Suitability / Activation / Decision / Publication.

9. **`analysis_runs`** — ejecución provider-neutral
   - `id, project_id, provider, status (pending, running, succeeded, failed, experiment_only), config json, started_at, finished_at`.

10. **`detection_candidates`** — salida normalizada de cualquier provider
    - `id, analysis_run_id, class, start_time, end_time, pos_x, pos_y, w, h, confidence, status (pending, accepted, rejected), inventory_item_id nullable, created_by`.

11. **`advertising_opportunities`** — Advertising MVP (manual)
    - `id, project_id, appearance_id nullable, scene_id nullable, value_level (high, medium, low), rationale, created_by`.
    - N:N con `taxons` (contextos) y con `inventory_items` (elementos) para el detalle.
    - H/M/L es interpretación humana y explicable; **no** es score automático.

### 3.2 Tablas P1 (comprometidas; post-IBC si no estables)

12. **`clearance_cases`** — ClearanceCase mínimo
    - `id, project_id, inventory_item_id, appearance_id nullable, detectable_family_id, status (open, in_review, resolved), decision (pending, approved, approved_with_conditions, rejected, escalated), notes nullable, created_by, reviewed_by nullable, created_at, updated_at`.
    - **No** usar `flagged`/`blocked` como `decision`. Warning/Blocker son dimensión separada (P2).
    - Registro de familias (taxonomy `clearance_family`): **Brand/Logo, Artwork, Screen, Document, Personal Identifier, Packaging, Photograph, Registration Plate** (+ opcionales: Uniform/Insignia, Tattoo, Graffiti, QR Code). **No**: Music, Location, Product, Person/Likeness.
    - `detectable_family` = "qué tipo de elemento observable" (no "qué riesgo jurídico").
    - Clearance signal ≠ Case ≠ Decision.

13. **`activation_opportunities`** — Interactive (conexión con player)
    - `id, project_id, inventory_item_id, activation_family, destination_url, cta, status, created_by`.

14. **`character_resolutions`** — Person → Character (resolución dedicada)
    - `id, project_id, person_item_id, character_id, status (proposed, resolved, rejected), resolved_by, resolved_at, created_at, updated_at`.
    - `person_item_id` y `character_id` referencian `inventory_items` (type `person` y `character` respectivamente). El **cast** = conjunto de `inventory_items` type `character` sembrado manualmente.
    - Cadena correcta: Detection → Person → Grouping of Appearances → Character Resolution → Resolved Character.
    - La resolución **no reescribe la provenance** (sigue `Source=AI, Type=Person`).
    - Crear Character es acción separada de resolver Person (no se auto-crea).

---

## 4. Dataset demo (seeding spec)

Se siembra manualmente un dataset de alta calidad sobre Emily in Paris S3E2:

- Scenes ordenadas con cobertura temporal completa.
- InventoryItems con Type correcto (Product, Brand, Person, Character, Location, Artwork, Screen, Document, …) y Family.
- Appearances precisas con timecodes, coordenadas y `source`/`provenance` correctos.
- Evidence: **frames/crops extraídos del vídeo** durante el sembrado (los thumbnails legacy no se convierten en Evidence autoritativa sin mapping).
- **SRT/transcript: hay que generarlo** (ASR + timestamps + revisión manual de los fragmentos usados en demo). Provenance: `source=AI/ASR, provider, model, generated_at, validation_status`.
- Key Contexts (solo los realmente asignados): Automotive, Travel, Luxury, Fashion, Food & Beverage, Technology, Sports, Family, Beauty, Home & Living. Métrica Overview = **nº Scenes por contexto** (sin temporal coverage, sin porcentajes).
- `vertical_relevance` (Relevant For) a nivel Appearance: Advertising / Interactive / Clearance.
- Advertising Opportunities H/M/L con rationale humano.
- Cast de personajes (Characters) sembrado manualmente.
- Clearance: señal agregada de Clearance-Relevant Elements + **4 Cases curados** (approved, approved_with_conditions, pending, escalated) — decisiones humanas, nunca automáticas.

**Regla:** el sistema debe permitir sustituir el Project demo sin tocar la arquitectura (ver §9).

---

## 5. Pantallas (frontend, Blade + Vite)

1. **Analysis Overview** (P0, héroe): Content Intelligence (nº Scenes/Elements/Appearances/Relationships), Business Opportunities (Advertising H/M/L, Activation, Clearance-Relevant), Key Contexts.
2. **Analysis Workspace** (P0): tabla Appearance → Element, Type/Family, Source, Validation, Relevant For, Scene; search + filtros; assignment/consolidation; validación inline; bulk actions (P1).
3. **Inspector** (P0): drill-down de elemento — Appearances, timecodes, Evidence, Scenes, Relationships, Key Contexts, on-screen time.
4. **Extended Element View** (P1): vista completa / galería.
5. **Advertising MVP** (P0): oportunidades H/M/L con Scene, Contexts, Elements, Relationships, Evidence, rationale.
6. **Clearance MVP** (P1): Clearance-Relevant Elements, distribución por familia, Evidence, creación de Case, Cases curados con status+decision.
7. **Conexión Interactive** (P1): Activation Opportunities → player/demo.
8. **Person → Character** (P1): resolver Person contra Character del cast; listar/crear Characters.

**Navegación:** nueva sección/tab "Analysis" en el proyecto (sin retirar tabs existentes).

---

## 6. Backlog priorizado

### P0 — Gate IBC (duro, prioridad absoluta)

| # | Tarea | Tipo |
|---|-------|------|
| P0-1 | Migraciones tablas P0 (11) + índices | BE |
| P0-2 | Modelos Eloquent + relaciones | BE |
| P0-3 | Seeders dataset Emily in Paris (scenes, items, appearances, evidence, contexts, relevances, opportunities) | BE/Data |
| P0-4 | Extracción de frames/crops Evidence desde el vídeo | Data |
| P0-5 | Generación SRT/transcript (ASR + revisión fragmentos demo) | Data/AI |
| P0-6 | Servicio de consulta (Overview agregados, Workspace filtrado, Inspector) | BE |
| P0-7 | Analysis Overview | FE |
| P0-8 | Analysis Workspace (tabla + filtros + validación) | FE/BE |
| P0-9 | Inspector | FE/BE |
| P0-10 | Evidence + Validation (subir frame/crop, marcar validated) | FE/BE |
| P0-11 | Advertising MVP (oportunidades H/M/L) | FE/BE |
| P0-12 | Métricas derivadas reales (conteos, timecodes, on-screen time) | BE |

### P1 — Comprometido (post-IBC si no está estable)

| # | Tarea | Tipo |
|---|-------|------|
| P1-1 | Extended Element View | FE |
| P1-2 | Activation Opportunities + conexión Interactive | BE/FE |
| P1-3 | Clearance MVP (casos + familias + Evidence) | BE/FE |
| P1-4 | Person → Character (cast + resolución + provenance) | BE/FE |
| P1-5 | Bulk actions en Workspace | FE |

### P2 — Después de IBC

Project Passport, Publication framework, Clearance workflow avanzado (warnings/blockers/overrides), scoring automático, saved queues, merge/split UI, AnalysisRun/Snapshot UI, provider/cost console, enterprise permissions, Organization migration.

---

## 7. Naming aprobado (UI en inglés)

- **Fijos:** Content Intelligence, Identified Elements, Appearances, Contextual Relationships, Business Opportunities, Key Contexts, Advertising Opportunities, Activation Opportunities, Clearance-Relevant Elements.
- **Advertising:** dominio `High/Medium/Low`; client-facing `High-Value/Medium-Value/Low-Value Opportunities`. Prohibido "AI Score / Opportunity Score / Monetization Score".
- **"Relevant For"** = label provisional (semántica interna `vertical_relevance` / `relevance_flags` con valores Advertising/Interactive/Clearance). Se revisa post-IBC sin migrar el modelo.
- **Type enum** (cerrado): Product, Brand, Person, Character, Location, Object, Identifier, Artwork, Screen, Document, Text, Audio/Work. No se añaden Types por cada categoría de negocio (eso es Family/Taxon).

---

## 8. Definition of Done / Gate funcional IBC

- Recorrido reproducible: `Scenes → Appearances → Identified Elements → Relationships + Context → Content Intelligence → Advertising (→ Interactive/Clearance)`.
- Evidence y Validation respaldan cada Appearance (nada sin origen).
- Player y API `api-iwi` siguen funcionando (hotpoints intactos).
- Métricas derivadas reales; cifras ilustrativas marcadas como ejemplo.
- Autorización por capability en backend.
- Semántica correcta: Clearance signal≠Case≠Decision; Warning/Blocker≠Decision; Person≠Character (provenance intacta); Key Context≠narrativa; H/M/L≠score.

---

## 9. Riesgos y blockers

| # | Tema | Estado |
|---|------|--------|
| R1 | **DEMO_RIGHTS_BLOCKER** — Emily in Paris solo autorizado para uso **interno/privado** | Blocker de derechos para la demo externa. Resolver antes de IBC: autorización del asset Emily **o** asset alternativo rights-cleared. No bloquea desarrollo |
| R2 | SRT/transcript inexistente | Hay que generarlo (P0-5) |
| R3 | Frames/crops Evidence | Extraer del vídeo (P0-4) |
| R4 | Plazo P0+P1 en 3,5 semanas | P0 gate duro; P1 comprometido post-IBC si no estable |
| R5 | Doble fuente de verdad: `datision_objects_ia_classes` vs `taxons` | Migrar clases IA a `taxons` vía adapter, no duplicar |
| R6 | "Relevant For" provisional | No bloquear desarrollo; copy se cierra post-IBC |

---

## 10. Gobernanza

- **Alejandro (PO)** = aprobador final de semántica, modelo de datos, scope, prioridades, workflow, IA, licensing, data exposure y UX funcional.
- Copy IBC: `Draft → Product review → Marketing/stakeholder → Product final approval → IBC copy freeze` (un único owner final).
- Desarrollo propone técnica/estimaciones/simplificaciones; Product aprueba lo de dominio.

---

## 11. Principio de ejecución

> **Construir primero una demo sólida de Content Intelligence → Advertising, preservando el modelo que permite conectar después Interactive y Clearance.**

No sacrificar semántica, estabilidad, trazabilidad, Evidence ni separación de responsabilidades para completar superficialmente todas las capacidades antes del evento.
