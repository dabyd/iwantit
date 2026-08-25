# Preguntas para rematar el plan del MVP IBC

**Base:** `PLAN_MVP_IBC.md` + `IwantIt_MVP_Respuestas_Alcance.md`
**Fecha:** 18 de agosto de 2026
**Objetivo:** cerrar las decisiones que faltan antes de empezar a desarrollar.

---

## Decisiones ya cerradas (a título informativo)

| Decisión | Resolución |
|---|---|
| ContentVersion | Se trata como **1:1 con `projects`** (no se crea tabla ahora); la separación completa Project/Content/ContentVersion queda para después |
| Frontend | **Blade + Vite + JS ligero** (no se introduce React/Inertia) |
| Clearance y Person→Character | **Comprometidos** en el alcance; no se descopan aunque lleve más tiempo |

---

## Bloque A — Clearance

**A1. ¿Qué profundidad implementamos?**
- **Opción recomendada — Case mínimo con estado y decisión:** tabla `clearance_cases` con `status (open / in_review / resolved)` y `decision (pending / approved / flagged / blocked)`. Suficiente para el teaser y crece luego a workflow legal.
- Alternativa — Solo flags: marcar `clearance_relevant` + familia sobre el elemento, sin tabla de cases.

**A2. ¿Qué familias detectables sembramos inicialmente?** *(propuesta inicial, confirmar/ajustar)*
- Artwork
- Music
- Brand / Logo
- Person / Likeness
- Location
- Product

**A3. En la demo, ¿mostramos casos con "decision" real (approved/flagged/blocked) o solo elementos marcados como revisables?**

---

## Bloque B — Person → Character

**B1. ¿Cómo modelamos la resolución Person → Character?**
- **Opción recomendada — Tabla dedicada** `character_resolutions (person_item_id, character_item_id, status, actor)`: explícita, validable y auditable.
- Alternativa — Relación reutilizada: usar `contextual_relationships` con `relationship_type = portrays`, sin tabla nueva.

**B2. ¿De dónde sale el cast/lista de personajes de Emily in Paris S3E2?**
- Sembrarlo manualmente (recomendado para MVP).
- Importarlo de IMDb/TMDB (queda como proveedor futuro en la guía §83.15).
- Otro origen.

**B3. ¿La Person detectada por IA debe poder resolverse a un Character, o también crear Character desde cero si no existe en el cast?**

---

## Bloque C — Plazos y regla de entrega

**C1. ¿Qué regla de plazos fijamos?**
- **Opción recomendada — IBC = gate duro solo para P0** (Analysis Overview, Workspace, Inspector, Evidence/Validation, Advertising). El P1 comprometido (Extended Element View, conexión Interactive, Clearance MVP, Person→Character, bulk actions) se desarrolla sí o sí y se entrega inmediatamente después de IBC si no llega a la demo.
- Alternativa — Intentar meter todo (P0 + P1) antes de IBC, asumiendo riesgo de calidad.

**C2. ¿La fecha IBC (11–12 septiembre) es inamovible, o se puede deslizar unos días si el P1 comprometido lo requiere?**

---

## Bloque D — Datos del caso estrella

**D1. ¿Disponemos de SRT / transcript de Emily in Paris S3E2 con calidad suficiente para Evidence de diálogo, o hay que generarlo?**

**D2. ¿Disponemos de frames/crops para Evidence visual, o se extraerán del vídeo durante el sembrado?**

**D3. Confirmación de que el contenido demo (Emily in Paris S3E2) puede usarse dentro del alcance autorizado para demostración pública/comercial en IBC.**

---

## Bloque E — Naming y taxonomías

**E1. "Relevant For"** es provisional. ¿Lo dejamos como label provisional en la UI (no bloquea desarrollo) o cerramos ya un nombre definitivo?

**E2. Enum de Type cerrado** (candidato en guía §83.14). ¿Confirmamos esta lista para el MVP?:
`Product, Brand, Person, Character, Location, Object, Identifier, Artwork, Screen, Document, Text, Audio/Work`

**E3. Key Contexts iniciales** (propuesta). ¿Confirmamos?:
`Fashion, Luxury, Food & Beverage, Travel, Automotive, Technology`

**E4. Microcopy de Advertising** (labels internos de oportunidades H/M/L). ¿Aprobamos los nombres provisionales o hay que revisarlos con marketing antes de congelar copy de IBC?

---

## Bloque F — Gobernanza

**F1. ¿Confirmamos a Alejandro (Product) como único aprobador de semántica, scope y prioridades?**

**F2. ¿Quién revisa y aprueba el copy final de la demo antes de IBC (marketing / dirección / Alejandro)?**

---

Cuando se respondan (parcial o con "lo que recomiendes"), se actualiza `PLAN_MVP_IBC.md` y se arranca el desarrollo.
