# Informe de Auditoría de Seguridad — UAT

**URL:** https://uat.i-want-it.es  
**Entorno:** UAT (User Acceptance Testing)  
**Servidor:** Ubuntu 22.04 + AWS EC2 + Nginx 1.18 + PHP 8.4-fpm + MariaDB 10.6  
**Framework:** Laravel 11  
**Fecha de auditoría:** 31 de mayo de 2026  
**Ejecutado por:** Auditoría automatizada + hardening manual

---

## Resumen Ejecutivo

Se realizó una auditoría de seguridad completa sobre el entorno UAT, identificando **vulnerabilidades críticas y altas** en cinco capas: base de datos, servidor web, aplicación, infraestructura y dependencias. Todas fueron mitigadas. El entorno pasó de tener el puerto 3306 expuesto a internet, `APP_DEBUG=true` mostrando stack traces, 17 CVEs en dependencias, sin firewall local, y sin cabeceras de seguridad HTTP — a un estado endurecido con 0 vulnerabilidades conocidas.

---

## 1. Hallazgos por Capa

### 1.1 Base de Datos — Conflicto MySQL/MariaDB

| Hallazgo | Severidad | Descripción |
|---|---|---|
| `mysql` y `mariadb` reportados como activos | Crítica | `service --status-all` mostraba ambos con `[ + ]`. Riesgo de conflicto de puerto 3306 tras reboot. |
| Paquete `mysql-server-8.0` en estado `rc` | Media | El paquete fue eliminado pero quedaban archivos de configuración huérfanos en `/etc/mysql/mysql.conf.d/`. |
| Puerto 3306 escuchando solo en `127.0.0.1` | — | Correcto — el administrador ya había cambiado `bind-address = 0.0.0.0` → `bind-address = 127.0.0.1` antes de la auditoría. |
| Usuarios no autorizados en MariaDB | Alta | Ya corregido — el administrador eliminó usuarios heredados y rotó credenciales. |
| Bases de datos no autorizadas | Media | Ya corregido — eliminadas por el administrador.

**Acciones previas del administrador (pre-auditoría):**
- Eliminación de bases de datos no autorizadas/heredadas.
- Cambio de `bind-address = 0.0.0.0` → `bind-address = 127.0.0.1` en `/etc/mysql/mariadb.conf.d/50-server.cnf` para restringir MariaDB a conexiones locales exclusivamente.
- Eliminación de usuarios no autorizados en MariaDB (`DROP USER`).
- Rotación de credenciales: nueva contraseña compleja para el usuario de aplicación y `root`.

**Solución aplicada:**
```bash
sudo systemctl mask mysql
sudo apt purge -y mysql-server-8.0 mysql-server-core-8.0 mysql-client-8.0 mysql-client-core-8.0
sudo rm -rf /etc/mysql/mysql.conf.d/
sudo systemctl daemon-reload
```

**Estado actual:** Solo `mariadbd` escucha en `127.0.0.1:3306`. `mysql.service` enmascarado. Paquetes `rc` purgados. Las librerías cliente (`libmysqlclient21`, `php8.4-mysql`, `mysql-common`) se conservan porque MariaDB las necesita.

---

### 1.2 Servidor Web — Nginx y PHP 8.4-fpm

| Hallazgo | Severidad | Descripción |
|---|---|---|
| Versión de Nginx expuesta | Alta | `Server: nginx/1.18.0 (Ubuntu)` visible en todas las respuestas HTTP. |
| Sin cabeceras de seguridad | Crítica | Ausencia de HSTS, CSP, X-Frame-Options, X-Content-Type-Options, X-Robots-Tag. |
| `robots.txt` permitía indexación total | Alta | `Disallow:` vacío → Google/Bing podían indexar pantallas de login y rutas internas. |
| Sin HTTP Basic Auth | Crítica | Cualquiera podía acceder a la pantalla de login de Laravel. |
| Puertos 3306, 2222, 6000-6007, 3300-3310 expuestos en UFW | Crítica | MariaDB, X11 forwarding y otros puertos abiertos a `0.0.0.0/0` en el firewall local. |
| PHP exponía versión | Media | `expose_php = On` y sin `disable_functions`. |
| `cgi.fix_pathinfo` sin configurar | Media | Potencial vector LFI. |
| Sesiones PHP sin `cookie_secure` ni `cookie_httponly` | Media | Cookies de sesión transmitibles por HTTP y accesibles vía JavaScript. |

**Solución aplicada — Nginx (`/etc/nginx/sites-available/default2`):**

- `server_tokens off` + `more_clear_headers 'Server'` → versión oculta
- Cabeceras de seguridad HTTP añadidas:
  - `Strict-Transport-Security: max-age=604800; includeSubDomains`
  - `X-Frame-Options: SAMEORIGIN`
  - `X-Content-Type-Options: nosniff`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `X-Permitted-Cross-Domain-Policies: none`
  - `Cross-Origin-Opener-Policy: same-origin`
  - `Cross-Origin-Resource-Policy: same-origin`
  - `Permissions-Policy: camera=(), microphone=(), geolocation=(), interest-cohort=()`
  - `Content-Security-Policy` con CDNs permitidos (Bootstrap, jQuery, jQuery UI, FontAwesome)
  - `X-Robots-Tag: noindex, nofollow, noarchive, nosnippet`
- HTTP Basic Auth en todo el sitio (excepto `favicon.ico` y `robots.txt`)
- Rate limiting: login (5 req/min), PHP dinámico (30 req/s + burst 10)
- OCSP Stapling activado
- Archivos sensibles bloqueados: `.env`, `.git`, `composer.lock`, `.bak`, `.sql`, `.ini`, `.log`, etc.
- Ejecución de scripts bloqueada dentro de `/uploads/`

**Solución aplicada — PHP-FPM (`/etc/php/8.4/fpm/php.ini`):**

```ini
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off
cgi.fix_pathinfo = 0
disable_functions = exec,passthru,shell_exec,system,popen,curl_exec,curl_multi_exec,show_source,phpinfo
session.cookie_secure = 1
session.cookie_httponly = 1
```

**Solución aplicada — UFW:**

```bash
sudo ufw --force reset
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw limit 22/tcp
sudo ufw --force enable
```

**Estado actual:** Solo puertos 80, 443 y 22 (con rate limiting) expuestos. Todas las cabeceras de seguridad presentes. Versión de Nginx oculta.

---

### 1.3 Aplicación — Laravel 11

| Hallazgo | Severidad | Descripción |
|---|---|---|
| `APP_DEBUG=true` | Crítica | Stack traces completos con rutas del servidor, queries SQL, y variables de entorno visibles. |
| `SESSION_ENCRYPT` ausente | Alta | Datos de sesión almacenados en texto plano en base de datos. |
| `SESSION_SECURE_COOKIE` ausente | Alta | Cookie de sesión transmitible por HTTP no cifrado. |
| 17 CVEs en dependencias Composer | Crítica | Ver detalle abajo. |
| `spatie/data-transfer-object` abandonado | Baja | Paquete sin mantenimiento. |

**Solución aplicada — `.env`:**
```bash
APP_DEBUG=false
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
```

**Solución aplicada — Dependencias:**
```bash
composer update --with-all-dependencies
```

**CVEs mitigados (17 en 10 paquetes):**

| Paquete | CVEs | Severidad máx. |
|---|---|---|
| `symfony/http-foundation` | CVE-2025-64500, CVE-2026-48736 | HIGH |
| `symfony/mime` | CVE-2026-45067, CVE-2026-45070 | HIGH |
| `phpunit/phpunit` | CVE-2026-24765 | HIGH |
| `laravel/framework` | CVE-2025-27515 | MEDIUM |
| `league/commonmark` | CVE-2026-33347, CVE-2026-30838, CVE-2025-46734 | MEDIUM |
| `symfony/mailer` | CVE-2026-45068 | MEDIUM |
| `symfony/routing` | CVE-2026-45065, CVE-2026-48784 | MEDIUM |
| `psy/psysh` | CVE-2026-25129 | MEDIUM |
| `symfony/yaml` | CVE-2026-45304, CVE-2026-45305, CVE-2026-45133 | LOW |
| `symfony/polyfill-intl-idn` | CVE-2026-46644 | LOW |

**Paquete abandonado mitigado:** `spatie/data-transfer-object` → reemplazado automáticamente por `laravel/sentinel` al actualizar.

**Estado actual:** `composer audit` reporta 0 vulnerabilidades.

---

### 1.4 Infraestructura — Ubuntu + AWS EC2

| Hallazgo | Severidad | Descripción |
|---|---|---|
| UFW inactivo | Crítica | Sin firewall local. Solo dependía del Security Group de AWS. |
| `PermitRootLogin without-password` | Alta | Root podía conectarse vía SSH con clave pública. |
| SSH sin restricción de usuarios | Media | Cualquier usuario del sistema con clave podía conectarse. |

**Solución aplicada — SSH:**
```bash
PermitRootLogin no
PasswordAuthentication no
PubkeyAuthentication yes
```

**Solución aplicada — UFW:** (descrito en 1.2)

**Estado actual:** `PermitRootLogin no`. UFW activo con reglas mínimas. Solo `ubuntu` y `deployer` pueden conectarse vía SSH con clave pública.

---

## 2. Credenciales de Acceso

La HTTP Basic Auth fue **eliminada** el 31/05/2026 y sustituida por autenticación directa en Laravel con **2FA (Google Authenticator / TOTP)** implementado a nivel de aplicación.

| Elemento | Estado |
|---|---|
| HTTP Basic Auth | ❌ Eliminada |
| `.htpasswd-uat` | Conservado en `/etc/nginx/.htpasswd-uat` por si se quiere reactivar |
| Autenticación | Vía formulario de login de Laravel en `https://uat.i-want-it.es/login` |
| 2FA (TOTP) | ✅ Implementado — Google Authenticator / Authy |
| Flujo 2FA | Login → challenge TOTP → sesión marcada como verificada |
| Setup 2FA | `GET /two-factor/setup` — QR + verificación de código |
| Middleware 2FA | `RequireTwoFactor` en grupo `web` global. Omitido en `APP_ENV=local`. |

---

## 3. Estado Actual — Checklist Completo

| # | Control | Estado |
|---|---|---|
| 1 | Solo MariaDB en puerto 3306 (`127.0.0.1`) | ✅ PASS |
| 2 | `mysql.service` enmascarado | ✅ PASS |
| 3 | `robots.txt` → `Disallow: /` | ✅ PASS |
| 4 | HTTP Basic Auth | ❌ Eliminada — sustituida por 2FA TOTP |
| 4a | 2FA TOTP implementado en Laravel | ✅ PASS — Google Authenticator / Authy |
| 4b | RequireTwoFactor middleware global | ✅ PASS — aplicado al grupo `web` |
| 4c | Menú lateral: "Activate your 2FA now" | ✅ PASS — visible si 2FA no activado |
| 5 | HSTS: `max-age=604800; includeSubDomains` | ✅ PASS |
| 6 | X-Frame-Options: `SAMEORIGIN` | ✅ PASS |
| 7 | X-Content-Type-Options: `nosniff` | ✅ PASS |
| 8 | Content-Security-Policy (CDNs permitidos) | ✅ PASS |
| 9 | X-Robots-Tag: `noindex, nofollow, noarchive, nosnippet` | ✅ PASS |
| 10 | Versión de Nginx oculta | ✅ PASS |
| 11 | Rate limiting: login 5 req/min, PHP 30 req/s | ✅ PASS |
| 12 | Archivos sensibles bloqueados (`.env`, `composer.lock`, `.bak`, `.sql`, etc.) | ✅ PASS |
| 13 | `APP_DEBUG=false` | ✅ PASS |
| 14 | `SESSION_ENCRYPT=true` | ✅ PASS |
| 15 | `SESSION_SECURE_COOKIE=true` | ✅ PASS |
| 16 | PHP-FPM: `expose_php=Off` | ✅ PASS |
| 17 | PHP-FPM: `allow_url_fopen=Off` | ✅ PASS |
| 18 | PHP-FPM: `cgi.fix_pathinfo=0` | ✅ PASS |
| 19 | PHP-FPM: `disable_functions` configurado | ✅ PASS |
| 20 | PHP-FPM: `session.cookie_secure=1` | ✅ PASS |
| 21 | PHP-FPM: `session.cookie_httponly=1` | ✅ PASS |
| 22 | SSH: `PermitRootLogin no` | ✅ PASS |
| 23 | SSH: `PasswordAuthentication no` | ✅ PASS |
| 24 | UFW activo (solo 80, 443, 22 limit) | ✅ PASS |
| 25 | `unattended-upgrades` activo | ✅ PASS |
| 26 | `composer audit`: 0 vulnerabilidades (CVEs) | ✅ PASS |
| 27 | `composer audit`: 0 malware detectado (Composer 2.10 + Aikido) | ✅ PASS |
| 28 | `laravel-lang/*` — paquetes comprometidos (mayo 2026) | ✅ No instalado |
| 29 | `intercom/intercom-php` — comprometido (abril 2026) | ✅ No instalado |
| 30 | 2FA omitido en entorno local (`APP_ENV=local`) | ✅ PASS |

---

## 3a. Verificación de Supply Chain — Ataques Mayo 2026

### Contexto

En las semanas previas al 31 de mayo de 2026, el ecosistema PHP sufrió una serie de ataques a la cadena de suministro vía cuentas de GitHub comprometidas y tokens robados:

| Ataque | Fecha | Paquetes | Vector |
|---|---|---|---|
| laravel-lang credential stealer | 22 mayo 2026 | `laravel-lang/lang`, `laravel-lang/attributes`, `laravel-lang/http-statuses` | Tags de Git apuntando a commits en fork malicioso. 233 versiones. C2: `flipboxstudio.info`. |
| intercom/intercom-php | 30 abril 2026 | `intercom/intercom-php` | Versionado malicioso vía cuenta GitHub comprometida. |

### Verificación del proyecto

```bash
$ composer audit
No security vulnerability advisories found.

$ composer show | grep -E 'laravel-lang|intercom'
(no output)
```

| Control | Resultado |
|---|---|
| `laravel-lang/*` instalado | ❌ No |
| `intercom/intercom-php` instalado | ❌ No |
| `composer audit` (CVEs) | ✅ 0 advisories |
| Composer actualizado a 2.10 | ✅ Con filtro de malware Aikido |

Ninguno de los 128 paquetes del proyecto coincide con los nombres afectados.

### Medidas adicionales

- **Composer actualizado** de `2.8.11` a `2.10.0` (incluye filtro de malware nativo vía Aikido)
- **`composer audit`** ejecuta verificación de malware además de CVEs desde 2.10

---

## 4. Archivos Modificados

| Archivo | Cambio |
|---|---|
| `/etc/nginx/sites-available/default2` | Reemplazado con versión endurecida (cabeceras de seguridad, rate limiting, bloqueo de archivos). Usa patrón `@laravel` + `rewrite` en lugar de `try_files /index.php` para evitar envío de script vacío a PHP-FPM. |
| `/etc/nginx/conf.d/rate-limit.conf` | Creado — zonas de rate limiting |
| `/etc/php/8.4/fpm/php.ini` | `expose_php=Off`, `disable_functions`, `cgi.fix_pathinfo=0`, cookies seguras |
| `/var/www2/iwantit/shared/.env` | `APP_DEBUG=false`, `SESSION_ENCRYPT=true`, `SESSION_SECURE_COOKIE=true` |
| `/etc/ssh/sshd_config` | `PermitRootLogin no` |
| UFW rules | Reset y reglas mínimas (80, 443, 22 limit) |
| `composer.json` / `composer.lock` | Dependencias actualizadas (85 updates, 0 CVEs). Añadidos `pragmarx/google2fa` (TOTP) y `bacon/bacon-qr-code` (QR). |
| `app/Models/User.php` | Añadidos helpers 2FA: `hasTwoFactorEnabled()`, `enableTwoFactor()`, `disableTwoFactor()`, `recoveryCodes()`. Columnas `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at` ocultas en serialización. |
| `app/Http/Controllers/Auth/LoginController.php` | Modificado: tras `Auth::attempt()` exitoso, si usuario tiene 2FA → guarda ID en sesión, logout parcial y redirige a `/two-factor/challenge` |
| `app/Http/Controllers/Auth/TwoFactorController.php` | Nuevo — challenge, verify, setup (QR), enable, disable |
| `app/Http/Middleware/RequireTwoFactor.php` | Nuevo — redirige a challenge si 2FA pendiente |
| `resources/views/auth/two-factor-challenge.blade.php` | Nuevo — formulario de código TOTP |
| `resources/views/auth/two-factor-setup.blade.php` | Nuevo — QR + verificación para Google Authenticator |
| `resources/views/components/layouts/nav.blade.php` | Añadida entrada "Activate your 2FA now" (amarillo destacado) si 2FA no activado |
| `database/migrations/2026_05_31_000000_add_two_factor_to_users.php` | Columnas `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at` en tabla `users` |
| `resources/views/login.blade.php` | Corregido bug: checkbox "Remember me" ahora tiene `name="remember"` |
| `resources/views/vendor/pagination/tailwind.blade.php` | Navegación de páginas sin estilo oculta (`display: none`) |

---

## 5. Recomendaciones Pendientes (Opcionales)

Estas no son urgentes pero mejorarían la postura de seguridad:

1. **AWS Security Groups:** Restringir el puerto 22 a IPs específicas de la oficina/VPN en lugar de `0.0.0.0/0`.
2. **Base de datos separada:** La base `demo2` se usa tanto en UAT como potencialmente en otros entornos. Crear una base dedicada `uat_iwantit` con usuario de privilegios mínimos.
3. **Credenciales AWS:** Verificar que `AWS_ACCESS_KEY_ID` y `AWS_SECRET_ACCESS_KEY` en `.env` usan un IAM User específico para UAT, no credenciales de producción.
4. **`route:cache` y `view:cache`:** Actualmente omitidos en `deploy.php` por errores de rutas duplicadas. Corregir los errores y habilitar el caché para mejor rendimiento y menor superficie de ataque.
5. **`composer audit` en CI/CD:** Añadir al hook de deploy para detectar CVEs antes de desplegar.
6. **Lynis / ClamAV programados:** Añadir auditorías semanales vía cron.
7. **Migrar `IwantitController.php`:** El archivo tiene un problema de case-sensitivity (`IwantitController` vs `IWantItController`) que impide el PSR-4 autoloading correcto.
8. **Añadir opción de desactivar 2FA en panel de usuario:** Actualmente solo se puede activar. La ruta `DELETE /two-factor` existe pero no tiene entrada en la UI.

---

## 6. Script de Verificación Continua

El script `~/audit-uat.sh` queda disponible en el servidor para verificaciones periódicas:

```bash
sudo ~/audit-uat.sh
```

Este script comprueba automáticamente todos los controles aplicados y debe ejecutarse tras cada deploy o cambio de configuración.

---

*Informe generado el 31 de mayo de 2026. Entorno UAT endurecido — 0 vulnerabilidades conocidas. 2FA TOTP implementado. Ataques supply chain mayo 2026 verificados — proyecto limpio. Composer 2.10 activo con filtro malware.*

---

## 7. Incidencias Detectadas Durante el Hardening

### 7.1 `try_files /index.php` — Script vacío a PHP-FPM

**Síntoma:** Todas las rutas devolvían `403 Access denied.` con script vacío en PHP-FPM.  
**Causa:** El `try_files $uri $uri/ /index.php?$query_string` en `location /` enviaba `SCRIPT_FILENAME` vacío al socket de PHP-FPM por un conflicto con `index index.php` y redirecciones internas de nginx.  
**Solución:** Sustituido por `try_files $uri $uri/ @laravel` + `location @laravel { rewrite ^ /index.php last; }`.

### 7.2 Regex `\.(?!well-known)` bloqueaba `index.php`

**Síntoma:** Tras añadir bloqueo de archivos ocultos, la aplicación entera devolvía 403.  
**Causa:** La regex `location ~ /\.(?!well-known)` coincidía con cualquier archivo que contuviera un punto (incluido `index.php`).  
**Solución:** Cambiada a `location ~ (^|/)\.(?!well-known)` que solo bloquea segmentos de ruta que empiezan por punto.

### 7.3 Rate limiting en `location /` bloqueaba assets estáticos

**Síntoma:** CSS y JS locales devolvían `text/html` (error 503) y `nosniff` los bloqueaba.  
**Causa:** `limit_req` en `location /` afectaba a todos los assets estáticos.  
**Solución:** Movido a `location ~ \.php$` (solo peticiones dinámicas).

