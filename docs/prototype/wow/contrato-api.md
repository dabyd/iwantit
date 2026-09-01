# Contrato de API — Slice "WOW"

**Versión:** v1 · **Estado:** DRAFT (a congelar en Fase 0)
**Responsable:** David (define) + Oscar (aprueba y mockea)

## Convenciones

- Backend Laravel + Blade; Oscar consume JSON vía fetch desde las vistas.
- Toda ruta requiere **autorización por capability** en backend (sin bypass).
  - Capability: `analysis-screen` (vía `permission:analysis-screen`). Además se exige acceso al proyecto (`ProjectPermissionHelper::canView`).
- Timecodes devueltos en **milisegundos** (`start_ms`, `end_ms`).
- El frontend mockea contra estas formas hasta que existan los endpoints reales.

## Endpoints (slice wow)

### 1. Overview

`GET /projects/{project}/analysis/overview`

```json
{
  "content_intelligence": {
    "scenes": 12,
    "elements": 45,
    "appearances": 210,
    "relationships": 18
  },
  "business_opportunities": {
    "advertising": { "high": 3, "medium": 5, "low": 8 },
    "clearance_relevant": 7
  },
  "key_contexts": [
    { "name": "Fashion", "scenes": 4 },
    { "name": "Luxury", "scenes": 2 }
  ]
}
```

### 2. Advertising opportunities

`GET /projects/{project}/advertising-opportunities`

```json
{
  "items": [
    {
      "id": 1,
      "value_level": "high",
      "scene": { "id": 3, "name": "Oficina Savoir" },
      "elements": [ { "id": 12, "name": "Café", "type": "product", "time_on_screen_ms": 8000 } ],
      "contexts": [ "Food & Beverage" ],
      "rationale": "Producto protagonista en plano central durante 8s.",
      "start_ms": 842000,
      "end_ms": 850000,
      "duration_ms": 8000
    }
  ]
}
```

### 3. Filtro por nivel (opcional)

`GET /projects/{project}/advertising-opportunities?level=high`

Mismo shape, filtrado por `value_level`.

## Fuera del slice (no implementar ahora)

- `GET /projects/{project}/appearances` (Workspace)
- `GET /projects/{project}/elements/{element}` (Inspector)
- `POST /projects/{project}/appearances/{id}/validations` (Validation)
- `GET /projects/{project}/clearance` (Clearance MVP)
- `GET /projects/{project}/scenes` (listado completo)

## Reglas

- Los conteos del Overview son **derivados en backend** (COUNT sobre BD), nunca hardcodeados.
- `time-on-screen` se calcula (suma de intervalos), no se almacena.
- `time_on_screen_ms` por elemento = **unión temporal** de todas sus Appearances (los solapamientos se cuentan una sola vez).
- `duration_ms` de la oportunidad = `end_ms - start_ms`.
- Sin datos demo hardcodeados en frontend: Oscar usa el mock solo hasta conectar al endpoint real (O-05).
