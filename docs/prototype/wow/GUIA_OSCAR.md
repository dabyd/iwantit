# Guía de integración frontend — Slice "WOW" (para Oscar)

> **Audiencia:** Oscar (Frontend + IA).
> **Propósito:** entender qué está hecho en el backend, qué tienes que desarrollar tú y cómo conectar tu pantalla con los endpoints ya existentes.
> **Este documento es autocontenido:** sirve tanto para leerlo tú como para pasárselo a una IA y que desarrolle tu parte sin ambigüedades.

---

## 1. Objetivo del slice

Mostrar, con datos reales, la historia central del producto:

> *"IwantIt entiende qué ocurre dentro del contenido, lo estructura una vez y lo convierte en inteligencia reutilizable para Advertising."*

Dos pantallas dentro de la pestaña **Analysis** del proyecto:

1. **Analysis Overview** (pantalla héroe) — datos agregados del proyecto.
2. **Advertising MVP** — listado de oportunidades de publicidad H/M/L.

**Activo de demo:** Emily in Paris S3E2 (uso interno/privado). Proyecto **12** en BD.

---

## 2. Estado actual: qué está hecho y qué falta

### ✅ Hecho (backend, David) — NO tienes que tocarlo

| Área | Detalle |
|---|---|
| Modelo de datos | 11 tablas + 2 pivots (`scenes`, `inventory_items`, `appearances`, `evidence`, `validations`, `contextual_relationships`, `taxons`, `taxon_assignments`, `appearance_relevances`, `analysis_runs`, `detection_candidates`, `advertising_opportunities`) |
| Modelos Eloquent | `app/Models/` (Scene, InventoryItem, Appearance, …, AdvertisingOpportunity) |
| Enums | `app/Enums/` (InventoryItemType, AppearanceSource, Vertical, ValueLevel, Taxonomy, ValidationStatus, DetectionCandidateStatus) |
| Importador | `php artisan wow:import {project} [--dir=…] [--reset]` |
| Servicio de consultas | `app/Services/WowAnalysisService.php` (Overview + Advertising) |
| **Endpoints** | `GET /projects/{id}/analysis/overview` y `GET /projects/{id}/advertising-opportunities` |
| Autorización | Capability `analysis-screen` + acceso al proyecto |
| Métricas | Conteos + time-on-screen (unión de intervalos) |
| **Navegación** | Pestaña "Analysis" **ya añadida** al proyecto (funcional, básica) |
| Frames | `php artisan frames:extract {project} [--crop]` |
| SRT | `storage/app/srt/{project_id}.srt` |

### 🟡 Pendiente (tú, Oscar)

| ID | Tarea | Estado actual |
|---|---|---|
| O-01 | Mock JSON estático | ❌ Ya no hace falta: los endpoints reales están listos. |
| O-02 | Navegación tab "Analysis" | ✅ Ya existe (backend + tab básico). Puedes pulirla. |
| O-03 | **Analysis Overview (héroe)** | 🟡 Tú: diseñar y montar el contenido. |
| O-04 | **Advertising MVP (H/M/L)** | 🟡 Tú: diseñar y montar la tabla/cards. |
| O-05 | Conectar a endpoints reales | ✅ Ya conectado con `fetch`. Puedes refinar. |
| O-06 | Polish visual | 🟡 Tú. |

**En resumen: el backend y la conexión ya están hechos. Tu trabajo es el diseño/HTML/CSS/JS de las dos pantallas dentro de un componente Blade que ya existe.**

---

## 3. Cómo verlo funcionando ya (antes de tocar nada)

```bash
# 1. Importar el dataset demo en el proyecto 12
php artisan wow:import 12 --dir=database/seeders/data/wow-demo --reset

# 2. Arrancar el entorno
composer dev
```

3. Abre el navegador, entra como **Admin** y ve a `/projects/12/edit`.
4. Verás la pestaña **Analysis** (la última). Ya carga datos reales vía `fetch`, con un render básico.

> Si no ves la pestaña, comprueba que tu usuario Admin tenga el permiso `analysis-screen` (se siembra con `php artisan db:seed --class=MenuPermissionsSeeder`).

---

## 4. Contrato de API (lo que consume tu frontend)

Base URL: `http://<dominio>` (mismas rutas que el backoffice). Todas las rutas requieren **sesión iniciada** (cookie de Laravel) y el permiso `analysis-screen`.

### 4.1 Autenticación y errores

| Situación | Respuesta |
|---|---|
| No autenticado | `302` → redirect a `/login` |
| Sin permiso `analysis-screen` | `403` |
| Sin acceso al proyecto (permiso `read`) | `403` |
| `?level=` con valor inválido | `422` con `{"message": "Nivel inválido. Valores permitidos: high, medium, low."}` |

### 4.2 `GET /projects/{project_id}/analysis/overview`

Devuelve los agregados para la pantalla héroe.

```json
{
  "content_intelligence": {
    "scenes": 4,
    "elements": 5,
    "appearances": 6,
    "relationships": 0
  },
  "business_opportunities": {
    "advertising": { "high": 2, "medium": 2, "low": 1 },
    "clearance_relevant": 0
  },
  "key_contexts": [
    { "name": "Fashion", "scenes": 2 },
    { "name": "Food & Beverage", "scenes": 2 },
    { "name": "Luxury", "scenes": 2 },
    { "name": "Beauty", "scenes": 1 },
    { "name": "Travel", "scenes": 1 }
  ]
}
```

| Campo | Tipo | Descripción |
|---|---|---|
| `content_intelligence.scenes` | int | nº de escenas |
| `content_intelligence.elements` | int | nº de elementos identificados |
| `content_intelligence.appearances` | int | nº de apariciones |
| `content_intelligence.relationships` | int | nº de relaciones contextuales |
| `business_opportunities.advertising.high/medium/low` | int | oportunidades por nivel |
| `business_opportunities.clearance_relevant` | int | apariciones relevantes para clearance |
| `key_contexts[].name` | string | nombre del Key Context |
| `key_contexts[].scenes` | int | nº de escenas con ese contexto |

### 4.3 `GET /projects/{project_id}/advertising-opportunities[?level=high|medium|low]`

Devuelve el listado de oportunidades. `level` es un filtro opcional.

```json
{
  "items": [
    {
      "id": 7,
      "value_level": "high",
      "scene": { "id": 15, "name": "Savoir Office" },
      "elements": [
        { "id": 17, "name": "Leather handbag", "type": "product", "time_on_screen_ms": 30000 }
      ],
      "contexts": ["Fashion", "Luxury"],
      "rationale": "Handbag protagonista en plano central durante 30 segundos…",
      "start_ms": 40000,
      "end_ms": 70000,
      "duration_ms": 30000
    }
  ]
}
```

| Campo | Tipo | Descripción |
|---|---|---|
| `items[].id` | int | id de la oportunidad |
| `items[].value_level` | string | `high` \| `medium` \| `low` |
| `items[].scene` | object\|null | `{ id, name }` de la escena asociada |
| `items[].elements[]` | array | elementos involucrados |
| `items[].elements[].id/name/type` | — | identidad del elemento |
| `items[].elements[].time_on_screen_ms` | int | tiempo total en pantalla del elemento (ms, unión de apariciones) |
| `items[].contexts[]` | string[] | nombres de Key Contexts |
| `items[].rationale` | string\|null | justificación humana de la oportunidad |
| `items[].start_ms` / `end_ms` | int\|null | rango temporal de la oportunidad (ms) |
| `items[].duration_ms` | int\|null | `end_ms - start_ms` |

> **Convención de tiempo:** TODO en milisegundos (`start_ms`, `end_ms`, `duration_ms`, `time_on_screen_ms`). Para mostrar `s`, divide entre 1000.

---

## 5. Valores cerrados (enums) que verás en los JSON

| Campo | Valores posibles |
|---|---|
| `value_level` | `high`, `medium`, `low` |
| `elements[].type` | `product`, `brand`, `person`, `character`, `location`, `object`, `identifier`, `artwork`, `screen`, `document`, `text`, `audio_work` |
| `contexts` / Key Contexts (lista cerrada) | `Automotive`, `Travel`, `Luxury`, `Fashion`, `Food & Beverage`, `Technology`, `Sports`, `Family`, `Beauty`, `Home & Living` |

---

## 6. Dónde trabajas (archivos y convenciones)

### 6.1 El tab ya existe

- **`resources/views/components/layouts/tab-analysis.blade.php`** — el componente de la pestaña "Analysis". **Aquí vive tu trabajo principal.**
- Se incluye en **`resources/views/components/layouts/edit.blade.php`**, gated por:
  ```blade
  @can('analysis-screen')
      <x-layouts.tab-analysis :data="$data" />
  @endcan
  ```
  Donde `$data` es el modelo `Project` (tiene `->id`).

### 6.2 Cómo funciona el sistema de pestañas

- Cada tab es un componente Blade que:
  1. llama a `\App\Helpers\TabCounter::incrementAndGet()` → obtiene su número (`$currentCount`).
  2. envuelve su contenido en `<div class="tab-{{ $currentCount }}">` con un `<h2>Título</h2>`.
- Un JS (`public/js/app.js`, compilado desde `resources/js/app.js`) lee los `.tab-N` y construye la navegación. **El `<h2>` es el título del botón.**
- **Importante:** el bucle JS solo recorre hasta `.tab-9`. No añadas más tabs de los que ya hay (Analysis es el 8º).

### 6.3 Stack frontend

- **Blade** + **Tailwind CSS** + **Vite** (`npm run dev` / `composer dev`).
- JS y CSS en `resources/js/` y `resources/css/` (entran por Vite).
- Para el tab puedes usar JS inline (como ya hace `tab-analysis.blade.php`) o moverlo a `resources/js/`.

---

## 7. Tus tareas, en detalle (O-03, O-04, O-06)

### O-03 — Analysis Overview (pantalla héroe)

Consume `GET /projects/{id}/analysis/overview`. Debe mostrar, como mínimo:

1. **Content Intelligence** → 4 tarjetas/contadores: `scenes`, `elements`, `appearances`, `relationships`.
2. **Business Opportunities** → conteo de `advertising` (`high/medium/low`) y `clearance_relevant`.
3. **Key Contexts** → lista de `{ name, scenes }` (ordena por `scenes` desc).

Sugerencia visual: usa el badge de color según nivel (`high` → verde/success, `medium` → ámbar/warning, `low` → gris/secondary).

### O-04 — Advertising MVP

Consume `GET /projects/{id}/advertising-opportunities`. Muestra un listado (tabla o cards) con:

- Nivel (`value_level`) → badge de color.
- Escena (`scene.name`).
- Elementos (`elements[]`) con `name`, `type` y `time_on_screen_ms` formateado (ej. "30s").
- Contextos (`contexts[]`).
- Tiempo (`start_ms` → `end_ms`, o `duration_ms`).
- `rationale`.

Añade el filtro por nivel con `?level=high|medium|low` (re-llama al endpoint).

### O-06 — Polish visual

- Consistencia con el design system del proyecto.
- Responsive (grid en desktop, apilado en móvil).
- Estados: loading, error, vacío ("No hay oportunidades todavía").

---

## 8. Pasos de integración (orden recomendado)

1. **Levanta el entorno** y abre el proyecto 12 con el dataset demo (sección 3).
2. **Trabaja dentro de `tab-analysis.blade.php`** (o mueve tu JS a `resources/js/` y tus estilos a Tailwind).
3. **Consume los endpoints** con `fetch` a `/projects/{id}/analysis/overview` y `/projects/{id}/advertising-opportunities`. El `id` ya está disponible en el componente como `$data->id` (lo tienes inyectado).
4. **No necesitas mock**: los endpoints ya devuelven datos reales del dataset demo.
5. **Verifica** en `/projects/12/edit` → tab Analysis.
6. **Commit por tu rama/PR** (ver reglas de git abajo).

### Ejemplo mínimo de `fetch` en el componente

```js
const id = {{ $data->id }};
const overview = await (await fetch(`/projects/${id}/analysis/overview`)).json();
const opps = await (await fetch(`/projects/${id}/advertising-opportunities`)).json();
```

> Si una petición devuelve `403`, es que el usuario no tiene `analysis-screen` o no puede ver ese proyecto. Si devuelve `302`, no hay sesión.

---

## 9. Reglas de git / ownership

- Rama: `feat/demo-ibc` desde `main`.
- **Oscar es dueño de:** `resources/views`, JS/Vite, `resources/css`.
- **David es dueño de:** `database/migrations`, `app/Models`, `app/Http/Controllers`, `routes`, `config`.
- Nadie toca archivos del otro sin PR. PRs pequeños y frecuentes.
- No subir secretos; `.env.example` solo con placeholders.

---

## 10. Anexo — checklist para que una IA desarrolle tu parte

Si le pasas este documento a una IA, dale además estas instrucciones explícitas:

> **Tarea:** implementar las pantallas Overview y Advertising dentro del componente Blade existente `resources/views/components/layouts/tab-analysis.blade.php`.
>
> **Contexto técnico:**
> - Proyecto Laravel + Blade + Tailwind + Vite.
> - El componente recibe la prop `$data` (modelo `Project`), de la que usas `$data->id`.
> - Endpoints (autenticación por cookie de sesión, no Bearer):
>   - `GET /projects/{id}/analysis/overview` → shape en sección 4.2.
>   - `GET /projects/{id}/advertising-opportunities?level=high|medium|low` → shape en sección 4.3.
> - Tiempos en milisegundos; formatea a segundos para mostrar.
> - `value_level` → `high` (verde), `medium` (ámbar), `low` (gris).
>
> **Requisitos:**
> 1. Overview: 4 contadores (Content Intelligence), bloque de Business Opportunities, lista de Key Contexts.
> 2. Advertising: listado con nivel, escena, elementos (con tiempo en pantalla), contextos, tiempo y rationale; filtro por nivel.
> 3. Estados de loading, error y vacío.
> 4. No alterar el `h2` "Analysis" ni la estructura de pestañas (`.tab-N` + `TabCounter`).
> 5. CSS con Tailwind, coherente con el resto del backoffice.
> 6. No tocar nada fuera de `resources/views/components/layouts/tab-analysis.blade.php` (y opcionalmente `resources/js/` y `resources/css/`).
>
> **Verificación:** `php artisan wow:import 12 --dir=database/seeders/data/wow-demo --reset`, abrir `/projects/12/edit` (Admin) y comprobar la pestaña Analysis.
