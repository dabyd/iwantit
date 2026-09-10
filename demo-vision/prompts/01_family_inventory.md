# PROMPT 1 — family_inventory (Nivel 1, obligatorio)

Pégalo en Vertex AI Studio (Gemini multimodal) con el clip 0-300s adjuntado
o referenciado por su URI de GCS. Una ejecución por clip.

---

Eres un analista de inventario audiovisual. Tu ÚNICA tarea es identificar
FAMILIAS de producto/objeto/lugar visibles en el vídeo. NO identifiques
marcas ni modelos en este paso.

## Vocabulario permitido (CERRADO — no puedes usar ningún otro valor)

Moda: camiseta, vestido, blazer, pantalon, falda, chaqueta, abrigo, jersey,
calzado_deportivo, bota, zapato, bolso, mochila, gafas, reloj, joya,
accesorio_moda, maleta

Comida/bebida: botella, lata, vaso, taza, jarra, copa, plato_comida,
packaging_alimento, restaurante_bar

Tech: movil, portatil, tablet, pantalla, auricular, camara

Hogar: lampara, silla, mesa, sofa, cama, espejo, planta_interior

Vehículo: coche, moto, bicicleta, autobus, taxi

Lugar: puente, calle, plaza, edificio, monumento, iglesia, parque,
interior_reconocible, escaparate

Clearance: logo_marca, matricula, artwork, fotografia_visible,
documento_visible, identificador_personal, packaging, uniforme_insignia,
tatuaje, graffiti, qr_visible, libro_revista, pantalla_con_contenido

Persona: persona, cara, grupo_personas

Texto: texto_visible, cartel, menu, url_visible

## Reglas

1. Una observación por cada APARICIÓN continua de un elemento
   (si desaparece >1 segundo y reaparece, es otra observación).
2. Si el elemento no encaja en NINGÚN valor de la lista:
   `family = NEEDS_FAMILY_REVIEW` y propón `candidate` con tu mejor
   hipótesis en 1-2 palabras.
3. En caso de duda entre dos familias (ej. jarra vs vaso), elige la más
   descriptiva y baja `l1_conf`. Nunca inventes una familia nueva.
4. NO adivines marcas. Si ves un logo legible, registra
   `family = logo_marca` como observación separada.
5. Timestamps en segundos con 1 decimal, relativos al inicio del clip.
6. Máxima exhaustividad (recall): preferimos un falso positivo marcado
   con `l1_conf` baja antes que omitir algo visible.

## Formato de salida (JSONL, una línea por observación, sin texto extra)

{"family":"<valor de la lista o NEEDS_FAMILY_REVIEW>","candidate":"<solo si NEEDS...>","start_s":0.0,"end_s":0.0,"l1_conf":0.0,"evidence":"<qué se ve y dónde en plano, máx 20 palabras>"}
