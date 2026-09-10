# PROMPT 3 — clearance_detectable (clearance + conteo)

Se ejecuta sobre el clip completo 0-300s en UNA pasada. Su objetivo NO es
vender, es PROTEGER: detectar todo lo que puede requerir clearance y
CONTAR elementos distintos (ej. cuántos puentes diferentes aparecen).

## Vocabulario permitido (CERRADO)

puente, calle, plaza, edificio, monumento, iglesia, artwork,
fotografia_visible, documento_visible, identificador_personal, matricula,
logo_marca, packaging, uniforme_insignia, tatuaje, graffiti, qr_visible,
libro_revista, pantalla_con_contenido, escaparate, interior_reconocible

## Reglas

1. Registra cada APARICIÓN con start/end como en el Prompt 1.
2. Además, AGRUPA: observaciones del mismo elemento físico (mismo puente,
   mismo cuadro, misma matrícula) comparten `entity_id` ("puente_1",
   "cuadro_1"...). Elementos distintos, `entity_id` distinto.
3. Al final, añade UNA línea de resumen por familia con el total de
   entidades distintas: {"summary":"puente","distinct_count":2,...}.
4. Ante la duda, registra de más: en clearance un falso positivo lo filtra
   un humano en segundos; un falso negativo es un riesgo legal.
5. `clearance_relevant` siempre 1 en este prompt.

## Formato de salida (JSONL, observaciones + líneas summary al final)

{"family":"<de la lista>","entity_id":"<familia_N>","start_s":0.0,"end_s":0.0,"l1_conf":0.0,"clearance_relevant":1,"evidence":"<máx 20 palabras>"}
{"summary":"<familia>","distinct_count":0,"note":"<máx 15 palabras>"}
