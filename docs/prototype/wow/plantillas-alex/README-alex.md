# Plantillas para Alex — Cómo rellenar los datos

Hola Alex. Este documento es para ti, sin tecnicismos. Son 4 hojas de cálculo para rellenar viendo el episodio de Emily in Paris S3E2. No necesitas saber nada de código.

## Cómo empezar

1. Abre Google Sheets (hojas de cálculo de Google).
2. Importa cada fichero `.csv` como una pestaña nueva (Archivo → Importar → Subir → elige el .csv → "Reemplazar hoja actual").
3. Borra las filas de **EJEMPLO** (están marcadas) y rellena con los datos reales.
4. Guarda/comparte la hoja con David cuando termines.

## Las 4 hojas

| Hoja | Qué rellenas | Cuándo |
|---|---|---|
| `01_scenes` | Las escenas del episodio (nombre + inicio/fin) | Primero |
| `02_elements` | Los elementos que aparecen (productos, marcas, personas, lugares…) | Segundo |
| `03_appearances` | Dónde y cuándo aparece cada elemento | Tercero |
| `04_advertising_opportunities` | Oportunidades de publicidad H/M/L con tu justificación | Cuarto |

## Reglas simples

- **Tiempos**: cópialos directamente del SRT, en formato `00:03:45,180` (horas:minutos:segundos,milisegundos). Si usas punto en vez de coma (`00:03:45.180`) también vale.
- **Posición en pantalla**: escribe números del 0 al 100 (porcentaje).
  - `pos_x=0, pos_y=0` = esquina **superior izquierda**.
  - `pos_x=100, pos_y=100` = esquina **inferior derecha**.
  - `pos_x`/`pos_y` = el **centro** del objeto. `w`/`h` = lo ancho y alto que es (en % de la pantalla).
  - No hace falta precisión milimétrica: aproximado está bien.
- **IDs de referencia**: usa códigos cortos para relacionar hojas. Ej.: escena `S01`, elemento `E01`, aparición `A01`, oportunidad `OP01`. David los convierte luego.
- **Type (tipo de elemento)**: usa solo estas palabras exactas:
  `product, brand, person, character, location, object, identifier, artwork, screen, document, text, audio_work`
- **Key Contexts**: usa solo estas (separadas por coma si hay varias):
  `Automotive, Travel, Luxury, Fashion, Food & Beverage, Technology, Sports, Family, Beauty, Home & Living`
- **Relevant For**: `Advertising`, `Interactive` o `Clearance` (separadas por coma si hay varias).
- **value_level**: `high`, `medium` o `low`.
- Nada inventado: todo lo que apuntes debe verse en el vídeo o salir del SRT.

## Consejo

No intentes cubrir TODO el episodio. Céntrate en las **4–6 escenas "wow"** que David y Oscar van a destacar. Esas, impecables; el resto, con lo esencial.

Si algo no te encaja en las columnas, escríbelo en la columna `notes` y David lo resuelve.
