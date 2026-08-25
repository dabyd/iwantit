# PLAN — Slice "WOW" (Overview + Advertising)

**Rama:** `feat/demo-ibc` desde `main`
**Duración objetivo:** 2.5–3 semanas
**Asset:** Emily in Paris S3E2 (uso interno/privado hasta resolver derechos)
**SRT:** ya resuelto (`Emily.in.Paris.S03E02.NF.WEB-DL.DDP5.1.H.264-SMURF.en[cc].srt`)

## Equipo

| Persona | Rol | Responsabilidad |
|---|---|---|
| Alex | CEO / Datos | Cura dataset, valida SRT, extrae frames Evidence |
| Oscar | Frontend + IA | Overview, Advertising, navegación, polish visual |
| David | Backend + DevOps + IA | Migraciones, modelos, APIs, seeders, autorización, rama/CI |

## Objetivo

Mostrar, de forma creíble y con datos reales, la historia central del producto:
*"IwantIt entiende qué ocurre dentro del contenido, lo estructura una vez y lo convierte en inteligencia reutilizable para Advertising (y después Interactive/Clearance)."*

Dos pantallas: **Analysis Overview** (héroe) y **Advertising MVP**.

## Alcance

**IN (slice wow):**
- Modelo de datos P0 completo (11 tablas) — se construye entero desde el día 1.
- Seeders/importador del dataset curado de Alex.
- Servicio de consultas: agregados de Overview + listado de Advertising.
- Pantallas Overview y Advertising.
- Navegación "Analysis" en el proyecto (sin retirar tabs existentes).
- Autorización por capability en backend.
- Métricas derivadas reales (conteos, time-on-screen).
- Player/API/hotpoints existentes intactos.

**OUT (continuación sin refactor):**
- Workspace, Inspector, Evidence+Validation UI, Clearance MVP, Activation/Interactive, Person→Character.
- El modelo de datos los deja preparados, pero no se construyen ahora.

## Fase 0 — Día 1–2 (los tres, juntos)

1. **Congelar contrato de datos** (`contrato-datos.md`) — David + Alex.
2. **Congelar contrato de API** (`contrato-api.md`) — David define, Oscar aprueba.
3. **Entregar a Alex la plantilla** (`plantillas-alex/`) — David, día 1.
4. **Crear rama** `feat/demo-ibc` desde `main` — David.
5. **Decidir el traspaso AI→hotpoints sin commitear** (dentro o fuera de la rama) — equipo.

## Backlog por persona

### David — Backend + DevOps

| ID | Tarea | Est. |
|---|---|---|
| D-01 | Crear rama `feat/demo-ibc` + `.env` + ejecutar migraciones base | 0.5d |
| D-02 | Migraciones de las 11 tablas P0 + índices (FK `project_id`) | 2d |
| D-03 | Modelos Eloquent + relaciones + enums cerrados | 1.5d |
| D-04 | Importador/seeders que cargan el JSON/CSV curado de Alex | 1.5d |
| D-05 | Servicio de consultas: agregados Overview + listado Advertising | 2d |
| D-06 | APIs/endpoints de análisis (contrato `contrato-api.md`) | 1.5d |
| D-07 | Autorización por capability en backend | 1.5d |
| D-08 | Métricas derivadas reales (conteos + time-on-screen) | 1d |
| D-09 | Ruta/navegación "Analysis" en proyecto (backend) | 0.5d |
| D-10 | Script `ffmpeg` para extracción de frames (para Alex) | 0.5d |
| D-11 | Colocar SRT en repo + convención de rutas | 0.5d |
| D-12 | Tests (php artisan test) + integración + verificación | 2d |

### Oscar — Frontend

| ID | Tarea | Est. |
|---|---|---|
| O-01 | Mock del contrato de API (JSON estático) — arranca día 1 | 0.5d |
| O-02 | Navegación: tab "Analysis" en proyecto | 1d |
| O-03 | Analysis Overview (héroe): Content Intelligence + Business Opportunities + Key Contexts | 3d |
| O-04 | Advertising MVP: oportunidades H/M/L con scene, contextos, elementos, rationale | 3d |
| O-05 | Conectar pantallas a los endpoints reales | 1.5d |
| O-06 | Polish visual (lo que vende ante inversores) | 2.5d |

### Alex — Datos (sin conocimientos técnicos)

| ID | Tarea | Est. |
|---|---|---|
| A-01 | Ver el episodio y marcar escenas (nombre + inicio/fin desde el SRT) | 1d |
| A-02 | Listar elementos identificados (type + brand) | 1d |
| A-03 | Anotar apariciones (elemento + escena + tiempos + posición aproximada) | 1.5d |
| A-04 | Asignar Key Contexts a escenas | 0.5d |
| A-05 | Proponer oportunidades de Advertising H/M/L con rationale | 1d |
| A-06 | Seleccionar 4–6 escenas "wow" para destacar | 0.5d |
| A-07 | Validar SRT contra el vídeo (repaso de tiempos) | 0.5d |
| A-08 | Extraer frames/crops de apariciones clave (script de David) | 1d |

## Milestones

| Semana | David | Oscar | Alex |
|---|---|---|---|
| **1** | D-01..D-04 (rama, migraciones, modelos, importador) | O-01..O-02 (mock + navegación) | A-01..A-04 (mitad del dataset) |
| **2** | D-05..D-10 (consultas, APIs, auth, script frames) | O-03..O-04 (Overview + Advertising reales) | A-05..A-08 (resto + frames) |
| **2.5–3** | D-11..D-12 (integración + verificación) | O-05..O-06 (conexión + polish) | Repaso datos + validación final |

## Camino crítico

- El desarrollo (David y Oscar en paralelo) marca el ritmo: **~2.5–3 semanas**.
- Alex termina antes; su calidad de datos da el "wow", no su velocidad.

## Definition of Done (slice wow)

- Recorrido reproducible: Scenes → Appearances → Elements → Key Contexts → Overview → Advertising.
- Overview y Advertising muestran datos reales del dataset curado (nada inventado ni hardcodeado).
- Player, API pública y hotpoints existentes siguen funcionando.
- Autorización por capability activa en backend.
- Métricas derivadas calculadas, no almacenadas ni inventadas.
- `php artisan test` en verde.
- Demo ensayada en local antes de enseñar.

## Reglas de git

- Rama `feat/demo-ibc` desde `main`.
- **David** dueño de `database/migrations`, `app/Models`, `app/Http/Controllers`, rutas, `config`.
- **Oscar** dueño de `resources/views` y JS/Vite.
- Nadie toca archivos del otro sin PR. PRs pequeños y frecuentes.
- Sin secretos en el repo; `.env.example` solo placeholders.

## Riesgos

1. **Emily = solo interno** hasta resolver derechos (no bloquea desarrollo).
2. **Contratos del día 1** son el pegamento: sin ellos, dos IAs en paralelo chocarán.
3. **Desfase SRT↔vídeo** si el vídeo no es la versión SMURF (A-07).
4. **Doble fuente de verdad** `datision_objects_ia_classes` vs `taxons`: migrar vía adapter, no duplicar.
5. Workspace/Inspector/Clearance quedan preparados en el modelo pero no construidos.

## Siguientes pasos

1. David crea rama `feat/demo-ibc`.
2. Fase 0 (contratos + plantilla) en 48h.
3. David entrega `plantillas-alex/` a Alex el día 1.
4. Los tres arrancan sus backlogs en paralelo.
