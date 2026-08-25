# Preguntas para definir el alcance del MVP ampliado de IwantIt

**Contexto previo (para quienes no han leído la guía v2.3):**
IwantIt es una *infraestructura de inteligencia de contenido*: convierte vídeo en conocimiento estructurado y gobernado (qué aparece, cuándo, dónde, con qué prueba, y qué usos están autorizados) que alimenta tres productos — **Interactive** (compra/interacción), **Clearance** (derechos) y **Advertising** (señales publicitarias) — sin duplicar la "verdad" del contenido. El MVP actual (Laravel) ya tiene: Projects, Objects, Hotpoints, Key Files, un player interactivo, un piloto de detección por IA (Datision) y métricas básicas (views/clicks/conversion).

Queremos ampliar el MVP para poder **demostrar, con cosas concretas y pequeñas, la potencia de monetización** de lo que ya tenemos. Para hacer un plan correcto necesitamos cerrar estas decisiones.

---

## Bloque 1 — Audiencia y mensaje

**1.1. ¿A quién va dirigida la explicación de "la potencia"?**
- Inversores (levantar capital: tesis, tamaño de mercado, tracción, barrera de entrada).
- Clientes productoras / broadcasters (convencer de que su contenido vale más de lo que creen).
- Plataformas OTT / streaming (vender integración).
- Marcas / agencias de publicidad.
- Mixto / todos con un único argumento.

**1.2. ¿Cuál es el argumento central que queremos que se entienda?**
- *"El cliente monetiza mejor su contenido"*: el mismo vídeo genera más vías de ingreso (commerce, publicidad, derechos).
- *"IwantIt monetiza como negocio"*: el modelo de licencia por capas (VIEW / QUERY / SERVE / EXPORT / DOWNLOAD / RETAIN / REUSE).
- Ambos, enlazados.

**1.3. ¿Cuáles son las "pequeñas cosas clave" que hoy NO conseguimos comunicar?**
(Concretar: ¿es la prueba/Evidence de cada aparición?, ¿el valor por vertical de negocio?, ¿el pasaporte exportable?, ¿el estado de derechos?, ¿la medición de intención del espectador?, ¿otra cosa?)

---

## Bloque 2 — Forma de la demostración

**2.1. ¿Cómo materializamos la explicación?**
- Demo guiada *dentro* del producto (tour clicable sobre un proyecto real).
- Ampliar el producto real para que las nuevas pantallas sean la demo por sí mismas.
- Prototipo navegable con datos sembrados (sin tocar el backend).
- Pieza de venta derivada (one-pager / vídeo / deck con capturas y números).

**2.2. ¿Dónde se consume?**
- Dentro de la plataforma (con login).
- Público, sin login (como el player actual embebido en la landing).
- Ambos.

**2.3. ¿Qué recorrido debe hacer la persona en la demo?**
(Por ejemplo: ver una escena → ver qué objetos aparecen y su prueba → ver el valor por vertical → ver el pasaporte → ver el player interactivo → ver el reporte de derechos.)

---

## Bloque 3 — Prioridad de producto (según la guía v2.3)

**3.1. ¿Qué bloques de valor priorizamos ahora?** *(elegir los que importan)*
- **Project Passport**: snapshot versionado y exportable del estado del proyecto (impacto inmediato, fácil de enseñar).
- **Analysis Overview + Workspace**: inteligencia del contenido, valor por vertical, tabla de apariciones, Inspector.
- **Evidence + Validation**: la prueba y la decisión de calidad sobre cada aparición (la "verdad" del contenido).
- **Clearance (derechos)**: casos, evaluaciones, decisiones, avisos/bloqueos, reportes.
- **Advertising (señales)**: clasificación, suitability, availability.
- **Publication / serving**: revisiones publicadas, activación, rollback, entrega gobernada.

**3.2. ¿Qué vertical de contenido demostramos?** *(cine, series, TV, FAST, branded content, publicidad, factual…)*

---

## Bloque 4 — Arquitectura y migración

**4.1. ¿Seguimos el modelo de dominio v2.3 (migración) o evolucionamos ligero para llegar antes al valor?**
- Migración al modelo v2.3 (Object→InventoryItem/Appearance, Organization, ContentVersion…): más lento, más robusto.
- Evolución ligera sobre las entidades actuales (Object/Hotpoint/Key File), añadiendo capas encima sin refactor estructural.
- Híbrido: migrar solo lo imprescindible para la demo y diferir el resto.
- Que lo recomendemos nosotros en el plan.

**4.2. ¿Hay que preservar compatibilidad con los consumidores actuales** (player demo embebido en la landing, APIs existentes) **o podemos romper cosas temporalmente?**

**4.3. El piloto de IA (Datision): ¿lo mostramos como parte de la demo o lo dejamos fuera?** (La guía lo clasifica como *experimental*, no como fuente de verdad.)

---

## Bloque 5 — Datos y contenido para la demo

**5.1. ¿Qué contenido usaremos?**
- Un proyecto real ya existente que sirva de "caso estrella".
- Sembrar un proyecto ficticio con escenas, productos y Evidence bien elegidos.
- Contenido real de clientes (implica permisos y privacidad).

**5.2. ¿El contenido disponible tiene** timecodes/precisión temporal, subtítulos/SRT, frames/crops, y datos de marca/producto **suficientes para mostrar Evidence y Validation?** ¿O hay que generarlo?

**5.3. ¿Hay restricciones de derechos de imagen o de datos (biometría, reconocimiento facial, GDPR) que limiten qué podemos mostrar?**

---

## Bloque 6 — Métricas y "monetización" demostrable

**6.1. ¿Qué métricas queremos poder mostrar?** *(hoy existen views, clicks y conversion sin definición gobernada)*
- ¿Basta con las actuales o necesitamos: tiempo en pantalla por producto, intención, valor por vertical, disponibilidad publicitaria, ahorro de tiempo en revisión?

**6.2. ¿Tenemos números reales que respaldar, o usaremos cifras ilustrativas/estimadas?** (La guía exige no presentar métricas sin fuente y definición.)

---

## Bloque 7 — Recursos y plazos

**7.1. ¿Quién y con qué capacidad?**
- Solo una persona, semanas.
- Solo una persona, sin prisa (meses).
- Equipo pequeño (2-3 personas).
- A estimar en el plan antes de comprometer recursos.

**7.2. ¿Hay una fecha límite o evento** (ronda de inversión, reunión de venta, demo a un cliente)?

**7.3. ¿Quién decide las prioridades?** (¿Hay un product owner / responsable final de aprobar el plan?)

---

## Bloque 8 — Presentación y narrativa

**8.1. ¿Idioma de la narrativa y la interfaz?** (Español / Inglés / Catalán / mixto según cliente)

**8.2. ¿Debe la demo ser autosuficiente** (que un socio o cliente la recorra solo) **o siempre con alguien de IwantIt explicando?**

**8.3. ¿Hay material de marca o narrativo ya decidido** (nombres de los bloques como "Business Value by Vertical" / "Relevant For") **o queda por definir el copy?**

---

Cuando los socios respondan (aunque sea de forma parcial o con "lo que tú recomiendes"), se cruzará con la guía v2.3 y se devolverá el plan priorizado.
