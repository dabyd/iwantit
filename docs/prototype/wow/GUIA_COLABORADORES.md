# Guía de colaboración — Repo iwantit (rama feat/demo-ibc)

Esta guía explica cómo David da acceso al repo y cómo cada uno se conecta y trabaja en su parte.

## Datos del proyecto

| Dato | Valor |
|---|---|
| Repo (SSH) | `git@github.com:dabyd/iwantit.git` |
| Repo (HTTPS) | `https://github.com/dabyd/iwantit.git` |
| Rama de trabajo | `feat/demo-ibc` |
| Rama principal | `main` |

---

## 1. Dar acceso al repo (lo hace David)

1. En GitHub, abre **https://github.com/dabyd/iwantit**.
2. Ve a **Settings → Collaborators and teams → Add people**.
3. Escribe el usuario de GitHub de Oscar (y de Alex si va a tocar código, aunque normalmente no lo necesita).
4. Rol: **Write** (pueden empujar a ramas y abrir PRs).
5. Les llega una invitación por email; tienen que **aceptarla**.

> Si no tienes su usuario de GitHub, pídeselo antes.

---

## 2. Configurar clave SSH (lo hace cada desarrollador, una sola vez)

Si no tienen clave SSH, la generan en su Mac/Linux:

```bash
ssh-keygen -t ed25519 -C "tu-email@ejemplo.com"
# pulsar Enter en todas las preguntas (vale la configuración por defecto)
cat ~/.ssh/id_ed25519.pub   # copia la línea completa que imprime
```

Luego, en GitHub: **Settings → SSH and GPG keys → New SSH key** → pegan la clave.

Comprueban que funciona:

```bash
ssh -T git@github.com
# Debe responder: "Hi <usuario>! You've successfully authenticated..."
```

---

## 3. Clonar y preparar el entorno (Oscar y David)

```bash
git clone git@github.com:dabyd/iwantit.git
cd iwantit
git checkout feat/demo-ibc
composer install
cp .env.example .env
php artisan key:generate
npm install
```

### Base de datos

El proyecto usa **MySQL** (base `demo2`). El entorno de desarrollo vive en la máquina de David. Hay dos opciones para Oscar:

- **Opción A (recomendada):** David exporta un volcado y Oscar lo importa en su MySQL local:
  ```bash
  # David, en su máquina:
  mysqldump -u root -p demo2 > demo2.sql
  # Oscar, en su máquina (después de crear la base demo2):
  mysql -u root -p demo2 < demo2.sql
  ```
- **Opción B:** Oscar no toca BD y trabaja contra el **mock del contrato de API** (ver sección 5), conectando a los endpoints reales solo al final.

Después de tener la BD:

```bash
php artisan migrate
php artisan serve
```

---

## 4. Reparto de archivos (quién toca qué)

Para que dos personas + IA no pisen el mismo archivo:

| Persona | Archivos que puede tocar |
|---|---|
| **David** (backend) | `database/migrations/`, `app/Models/`, `app/Http/Controllers/`, `routes/`, `config/` |
| **Oscar** (frontend) | `resources/views/`, `public/js/`, `public/css/`, Vite |

**Reglas:**
- Nadie toca archivos del otro sin hablarlo antes.
- Los cambios a `routes/web.php` (que usa David) y a las vistas Blade (que usa Oscar) se coordinan por chat, no en paralelo a ciegas.

---

## 5. Cómo trabaja cada uno

### Oscar (frontend)

1. **Día 1:** lee `docs/prototype/wow/contrato-api.md` y crea un mock con JSON estático.
2. Trabaja en `feat/demo-ibc` sobre `resources/views/` y el JS.
3. Cada día, antes de empezar:
   ```bash
   git pull origin feat/demo-ibc
   ```
4. Commits pequeños y frecuentes:
   ```bash
   git add <sus-archivos>
   git commit -m "descripción corta"
   git push origin feat/demo-ibc
   ```
5. Cuando la pantalla esté conectada a los endpoints reales de David, borra el mock.

### David (backend)

1. Crea migraciones, modelos, controladores y endpoints según `contrato-datos.md` y `contrato-api.md`.
2. Es dueño de `routes/web.php`: avisa a Oscar cuando añada/renombre rutas.
3. Entrega a Alex el script de extracción de frames (tarea D-10) en la semana 1.
4. Al acabar una tarea, `git push origin feat/demo-ibc`.

### Alex (datos — no necesita git ni código)

Alex **no toca código ni git**. Su trabajo es:

1. Importar las plantillas de `docs/prototype/wow/plantillas-alex/` a Google Sheets.
2. Rellenar las 4 hojas viendo el episodio (instrucciones en `README-alex.md`).
3. Cuando termina, comparte la hoja con David, que la convierte en seeders.

> Si Alex igualmente quiere acceso al repo (solo lectura), David puede invitarlo con rol **Read**, pero no es necesario.

---

## 6. Flujo diario recomendado

1. `git pull origin feat/demo-ibc` (antes de tocar nada).
2. Trabajar solo en tus archivos.
3. Commit + push.
4. Si hay conflicto al hacer pull, parar y avisar a David (no resolver a ciegas).

## 7. Errores frecuentes y solución

| Problema | Solución |
|---|---|
| `Permission denied (publickey)` al clonar | Falta añadir la clave SSH en GitHub (sección 2) |
| `composer install` falla | Asegúrate de tener PHP 8.x y `composer` instalados |
| No conecta a la BD | Revisar credenciales en `.env` (pedir a David) |
| Conflicto en `routes/web.php` o vistas | Parar y coordinar por chat |

---

## 8. Contacto y dudas

- Cambios en rutas/API → preguntar a David.
- Dudas de diseño/vistas → coordinar con Oscar.
- Datos y contenido → Alex.
- Semántica del producto → ver `docs/prototype/wow/PLAN_WOW.md` y `PLAN_MVP_IBC.md`.
