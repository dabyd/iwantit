# Guía de importación del dataset de Alex (slice WOW)

Cómo pasar los datos curados de Alex (Excel → CSV) a la base de datos del proyecto.

**Comando:** `php artisan wow:import {project} [--dir=…] [--reset]`

---

## 1. Exportar cada hoja de Excel a CSV

Alex entrega un Excel con 4 hojas. Para cada hoja:

- *Archivo → Guardar como → CSV UTF-8 (delimitado por comas)*.
- Nómbralas exactamente así (el impresor las busca por nombre):

| Hoja | Nombre de fichero |
|---|---|
| Scenes | `01_scenes.csv` |
| Elements | `02_elements.csv` |
| Appearances | `03_appearances.csv` |
| Advertising opportunities | `04_advertising_opportunities.csv` |

## 2. Verificar formato y valores

La primera fila de cada CSV debe ser la cabecera exacta. Tienes plantillas de referencia en
`database/seeders/data/wow/` (y en `docs/prototype/wow/plantillas-alex/`).

Reglas:

- **Tiempos**: `HH:MM:SS,mmm` (ej. `00:03:45,180`). Con punto también vale (`00:03:45.180`).
- **Referencias cruzadas**: códigos cortos para relacionar hojas — escena `S01`, elemento `E01`, aparición `A01`, oportunidad `OP01`.
- **Valores cerrados** (ver `contrato-datos.md`):
  - `type`: `product, brand, person, character, location, object, identifier, artwork, screen, document, text, audio_work`
  - `key_contexts`: `Automotive, Travel, Luxury, Fashion, Food & Beverage, Technology, Sports, Family, Beauty, Home & Living`
  - `relevant_for`: `Advertising, Interactive, Clearance` (separados por coma)
  - `value_level`: `high, medium, low`
- Las filas de ejemplo que contengan `EJEMPLO` se **ignoran automáticamente**.

## 3. Colocar los CSVs en la carpeta de datos

```bash
cp 01_scenes.csv 02_elements.csv 03_appearances.csv 04_advertising_opportunities.csv database/seeders/data/wow/
```

Si los dejas en otra carpeta, pásala con `--dir` (ver paso 4).

## 4. Ejecutar la importación

```bash
php artisan wow:import <id-del-proyecto>
```

- `<id-del-proyecto>` es el `id` numérico del proyecto (lo ves al abrir el proyecto en el backoffice o en la URL). También puedes usar el `demo_code`.
- Para CSVs en otra carpeta: `php artisan wow:import 12 --dir=/ruta/al/directorio`.

### Reimportar desde cero (opción recomendada tras corregir datos)

```bash
php artisan wow:import <id-del-proyecto> --reset
```

El flag `--reset` **borra primero todos los datos de análisis del proyecto** (scenes, elements,
appearances, oportunidades, taxon_assignments, evidence…) y luego importa limpio. Sin él, cada
importación **suma** registros y duplica datos.

> Usa `--reset` siempre que quieras reimportar una versión corregida del dataset.

## 5. Verificar el resultado

El comando imprime una tabla con los conteos importados por tipo:

```
+---------------+------------+
| Tabla         | Importados |
+---------------+------------+
| scenes        | 2          |
| elements      | 2          |
| appearances   | 2          |
| opportunities | 1          |
+---------------+------------+
```

Si algo falla, el comando **aborta con el mensaje exacto** (ej. `Referencia de escena no encontrada: S05`
o `Timecode inválido: …`) y **no deja datos a medias**: la importación es transaccional.

## Notas

- La importación es **transaccional**: si falla a mitad, no queda nada insertado.
- Las **marcas** (`brand`) se crean automáticamente si el nombre no existe en la tabla `brands`.
- Los **key contexts** se crean/reutilizan como taxonomía global (no se borran con `--reset`).
- Los datos se consultan después vía los endpoints de análisis:
  - `GET /projects/{project}/analysis/overview`
  - `GET /projects/{project}/advertising-opportunities`
