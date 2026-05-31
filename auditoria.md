# Auditoría de Seguridad — HARDENING GUIDE

**Entorno:** https://uat.i-want-it.es | Ubuntu 22.04 + AWS EC2 + Nginx + PHP 8.4-fpm + Laravel 11  
**Fecha:** Mayo 2026  
**Propósito:** UAT (User Acceptance Testing)

---

## Diagnóstico Inicial del Servidor

### Servicios activos (`service --status-all`)
```
[ + ] acpid, apparmor, apport, chrony, cron, dbus, irqbalance, kmod,
      mariadb, multipath-tools, mysql, nginx, php8.4-fpm, plymouth,
      plymouth-log, procps, ssh, udev, ufw, unattended-upgrades
```

### Puerto 3306
```
LISTEN 0  80  127.0.0.1:3306  0.0.0.0:*  users:(("mariadbd",pid=1589134,fd=22))
```
→ Solo MariaDB está escuchando. MySQL no tiene proceso real.

### Paquetes instalados
```
ii  mariadb-server              1:10.6.22-0ubuntu0.22.04.1
ii  mariadb-server-10.6         1:10.6.22-0ubuntu0.22.04.1
ii  mariadb-server-core-10.6    1:10.6.22-0ubuntu0.22.04.1
rc  mysql-server-8.0            8.0.34-0ubuntu0.22.04.1           ← config residual
```

### Cabeceras HTTP actuales
```
Server: nginx/1.18.0 (Ubuntu)
```
→ Versión expuesta. Sin HSTS, CSP, X-Frame-Options, ni X-Robots-Tag.

### robots.txt actual
```
User-agent: *
Disallow:
```
→ Vacío = permite indexación total. Google/Bing pueden estar indexando el UAT.

### Base de datos activa
```
/var/lib/mysql/demo2 → 66 MB
```
→ MariaDB 10.6 con archivos Aria e InnoDB. Base `demo2` activa.

---

## PRIORIDAD 1 — Resolución del Conflicto de Base de Datos

### Peligro

`service --status-all` reportó tanto `mysql` como `mariadb` con `[ + ]`. El diagnóstico confirmó que solo `mariadbd` tiene proceso en el puerto 3306. Sin embargo:

- El servicio `mysql` aparece porque el paquete `mysql-server-8.0` dejó un script SysV residual al ser eliminado (`rc` en dpkg).
- Si systemd intenta arrancar `mysql.service`, este podría reclamar el puerto 3306 antes que MariaDB tras un reboot, causando caída de la aplicación.

### Acciones ejecutadas

```bash
# 1. Detener y enmascarar el servicio mysql residual
sudo systemctl stop mysql 2>/dev/null || true
sudo systemctl disable mysql 2>/dev/null || true
sudo systemctl mask mysql

# 2. Purgar paquetes MySQL de Oracle
sudo apt purge -y mysql-server-8.0 mysql-server-core-8.0 \
                 mysql-client-8.0 mysql-client-core-8.0 2>/dev/null

# 3. Eliminar archivos de configuración huérfanos
sudo rm -rf /etc/mysql/mysql.conf.d/

# 4. Recargar systemd
sudo systemctl daemon-reload

# 5. Verificar estado final
dpkg -l | grep mysql
```

### Estado final (paquetes restantes — todos necesarios)

| Paquete | Rol | Se conserva |
|---|---|---|
| `mysql-common` | Archivos base compartidos MySQL/MariaDB | Sí |
| `libmysqlclient21` | Librería cliente C (PHP PDO) | Sí |
| `php8.4-mysql` | Extensión PHP MySQL/MariaDB | Sí |
| `libdbd-mysql-perl` | Driver Perl DBI | Sí (inofensivo) |

---

## PRIORIDAD 2 — Seguridad Específica para Entornos UAT

### 2.1 Bloquear indexación en motores de búsqueda

**Problema:** `robots.txt` tiene `Disallow:` vacío. Crawlers pueden indexar pantallas de login, rutas internas, y contenido de pruebas.

**Solución combinada — `robots.txt` + `X-Robots-Tag` en Nginx (defensa en profundidad):**

```bash
# En el servidor, dentro del release actual:
echo 'User-agent: *
Disallow: /' | sudo tee /var/www2/iwantit/current/public/robots.txt
sudo chown ubuntu:www-data /var/www2/iwantit/current/public/robots.txt
sudo chmod 644 /var/www2/iwantit/current/public/robots.txt
```

**Añadir al server block de Nginx** (ver sección PRIORIDAD 3 para el bloque completo):

```nginx
add_header X-Robots-Tag "noindex, nofollow, noarchive, nosnippet" always;
```

**Verificación:**
```bash
curl -I https://uat.i-want-it.es 2>/dev/null | grep -i x-robots
# Debe devolver: X-Robots-Tag: noindex, nofollow, noarchive, nosnippet

curl https://uat.i-want-it.es/robots.txt
# Debe devolver: User-agent: * \n Disallow: /
```

### 2.2 HTTP Basic Auth como barrera previa a Laravel

Nadie externo al equipo debe ver la pantalla de login. Basic Auth en Nginx bloquea todo el tráfico no autenticado.

```bash
# 1. Instalar dependencia
sudo apt install -y apache2-utils

# 2. Crear archivo de passwords
sudo htpasswd -c /etc/nginx/.htpasswd-uat uat-viewer
# Introduce la contraseña cuando pregunte (mínimo 12 caracteres)

# 3. Permisos restrictivos
sudo chown root:www-data /etc/nginx/.htpasswd-uat
sudo chmod 640 /etc/nginx/.htpasswd-uat
```

**Configuración Nginx** dentro del bloque `location /`:

```nginx
location / {
    auth_basic           "UAT - Acceso Restringido";
    auth_basic_user_file /etc/nginx/.htpasswd-uat;

    # Si hay webhooks externos que necesitan bypass:
    # satisfy any;
    # allow IP_DEL_WEBHOOK;

    try_files $uri $uri/ /index.php?$query_string;
}
```

```bash
sudo nginx -t && sudo systemctl reload nginx
```

**Verificación:**
```bash
curl -o /dev/null -w '%{http_code}' https://uat.i-want-it.es/
# Debe devolver: 401
```

### 2.3 Política de contraseñas para cuentas de prueba

El riesgo principal son cuentas creadas para QA con contraseñas débiles (`admin@test.com` / `test1234`) que den acceso al panel de administración con datos potencialmente reales.

**A. Validación en Laravel 11 — `config/auth.php`:**
```php
'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => 'password_reset_tokens',
        'expire' => 60,
        'throttle' => 60,
    ],
],
```

**B. Reglas de contraseña en creación/edición de usuarios:**
```php
use Illuminate\Validation\Rules\Password;

'password' => [
    'required',
    'confirmed',
    Password::min(12)
        ->mixedCase()
        ->letters()
        ->numbers()
        ->symbols()
        ->uncompromised(),
],
```

**C. Rate limiting en login:**
```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('login', function ($request) {
    return Limit::perMinute(5)->by($request->input('email').'|'.$request->ip());
});
```

### 2.4 Restricción de acceso por IP (AWS Security Groups)

Si el equipo de QA tiene IPs fijas, restringir a nivel de AWS **antes** que UFW:

```
AWS Console → EC2 → Security Groups → Inbound Rules:

Type    Protocol  Port  Source
HTTPS   TCP       443   <IP_OFICINA>/32
HTTP    TCP       80    <IP_OFICINA>/32
SSH     TCP       22    <IP_OFICINA>/32
```

Si las IPs son dinámicas, usar AWS Client VPN o Tailscale en lugar de abrir `0.0.0.0/0`.

---

## PRIORIDAD 3 — Endurecimiento de Nginx y PHP 8.4-fpm

### 3.1 Server block completo de Nginx

Fichero: `/etc/nginx/sites-available/uat.i-want-it.es`

```nginx
# Redirección HTTP → HTTPS
server {
    listen 80;
    server_name uat.i-want-it.es;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name uat.i-want-it.es;

    # ---- SSL ----
    ssl_certificate     /etc/letsencrypt/live/uat.i-want-it.es/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/uat.i-want-it.es/privkey.pem;
    ssl_trusted_certificate /etc/letsencrypt/live/uat.i-want-it.es/chain.pem;

    # ---- Cifrado fuerte ----
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers 'ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305';
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 1d;
    ssl_session_tickets off;

    # ---- OCSP Stapling ----
    ssl_stapling on;
    ssl_stapling_verify on;
    resolver 8.8.8.8 1.1.1.1 valid=300s;
    resolver_timeout 5s;

    # ---- Ocultar versión de Nginx ----
    server_tokens off;
    # Requiere: sudo apt install nginx-extras
    more_clear_headers 'Server';

    # ---- HSTS (1 semana en UAT, permite revertir sin esperar meses) ----
    add_header Strict-Transport-Security "max-age=604800; includeSubDomains" always;

    # ---- Cabeceras de seguridad HTTP ----
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header X-Permitted-Cross-Domain-Policies "none" always;
    add_header Cross-Origin-Opener-Policy "same-origin" always;
    add_header Cross-Origin-Resource-Policy "same-origin" always;
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=(), interest-cohort=()" always;

    # ---- Content-Security-Policy ----
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: blob:; font-src 'self'; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'" always;

    # ---- Bloquear indexación ----
    add_header X-Robots-Tag "noindex, nofollow, noarchive, nosnippet" always;

    # ---- Bloquear archivos ocultos ----
    location ~ /\.(?!well-known) {
        deny all;
        access_log off;
        log_not_found off;
    }

    # ---- Bloquear archivos de configuración y backup ----
    location ~* \.(bak|config|sql|fla|psd|ini|log|sh|inc|swp|dist|md)$ {
        deny all;
        access_log off;
        log_not_found off;
    }

    location ~* composer\.(json|lock)$ {
        deny all;
        access_log off;
        log_not_found off;
    }

    location ~* (package\.json|webpack\.config\.js|vite\.config\.js)$ {
        deny all;
        access_log off;
        log_not_found off;
    }

    # ---- Rate limiting ----
    limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;
    limit_req_zone $binary_remote_addr zone=general:10m rate=30r/s;

    location /login {
        limit_req zone=login burst=3 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    root /var/www2/iwantit/current/public;
    index index.php index.html;
    charset utf-8;

    location / {
        # ---- HTTP Basic Auth ----
        auth_basic           "UAT - Acceso Restringido";
        auth_basic_user_file /etc/nginx/.htpasswd-uat;

        limit_req zone=general burst=10 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    # ---- PHP-FPM ----
    location ~ \.php$ {
        limit_req zone=general burst=10 nodelay;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_param PATH_TRANSLATED "";
        fastcgi_intercept_errors on;
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
    }

    # Bloquear ejecución de scripts en uploads
    location ~* /uploads/.*\.(php|pl|py|cgi|asp|js)$ {
        deny all;
    }

    # Cache de assets estáticos (Vite/Laravel)
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot|webp|avif)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
}
```

```bash
# Aplicar
sudo nginx -t && sudo systemctl reload nginx
```

### 3.2 PHP 8.4-fpm — `php.ini`

Fichero: `/etc/php/8.4/fpm/php.ini`

```ini
; ---- Ocultar presencia de PHP ----
expose_php = Off

; ---- Deshabilitar funciones peligrosas ----
; ATENCIÓN: no incluyas proc_open, proc_close ni proc_get_status
; si usas Symfony Process (colas, ffprobe, etc.)
disable_functions = exec,passthru,shell_exec,system,popen,curl_exec,curl_multi_exec,show_source,phpinfo,pcntl_exec,parse_ini_file

; ---- Restricción de acceso a archivos remotos ----
allow_url_fopen = Off
allow_url_include = Off

; ---- Path info Fix (previene LFI) ----
cgi.fix_pathinfo = 0

; ---- Límites de recursos ----
memory_limit = 256M
max_execution_time = 60
max_input_time = 60
max_input_vars = 3000
post_max_size = 100M
upload_max_filesize = 100M

; ---- Sesiones seguras ----
session.cookie_httponly = 1
session.cookie_samesite = "Lax"
session.cookie_secure = 1
session.use_strict_mode = 1
session.use_only_cookies = 1
session.sid_length = 48
session.sid_bits_per_character = 6

; ---- Logging sin exponer datos ----
log_errors = On
display_errors = Off
display_startup_errors = Off
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
```

### 3.3 PHP 8.4-fpm — `www.conf`

Fichero: `/etc/php/8.4/fpm/pool.d/www.conf`

```ini
[www]

; Unix socket (más seguro que TCP 9000)
listen = /var/run/php/php8.4-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

user = www-data
group = www-data

; Process Manager: ondemand para ahorrar RAM en UAT
pm = ondemand
pm.max_children = 20
pm.process_idle_timeout = 30s
pm.max_requests = 500

; Seguridad
security.limit_extensions = .php .phtml

; Status page (solo local)
pm.status_path = /fpm-status
ping.path = /fpm-ping

; Environment
env[HOSTNAME] = $HOSTNAME
env[PATH] = /usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
env[TMP] = /tmp
env[TMPDIR] = /tmp
env[TEMP] = /tmp

; Refuerzo de funciones deshabilitadas (capa extra sobre php.ini)
php_admin_value[disable_functions] = exec,passthru,shell_exec,system,popen,curl_exec,curl_multi_exec,show_source,phpinfo
php_admin_value[expose_php] = Off
php_admin_value[allow_url_fopen] = Off
php_admin_value[allow_url_include] = Off
```

```bash
# Aplicar
sudo php-fpm8.4 -t 2>/dev/null || true
sudo systemctl restart php8.4-fpm
```

---

## PRIORIDAD 4 — Seguridad en Laravel 11

### 4.1 El dilema de `APP_DEBUG`

**Riesgo con `APP_DEBUG=true`:**
- Quien atraviese la Basic Auth verá stack traces completos con rutas del servidor, queries SQL, y variables de entorno (`AWS_ACCESS_KEY_ID`, `DB_PASSWORD`, `APP_KEY`).
- Si Ignition (dependencia de dev) queda accesible, expone un panel web interactivo para ejecutar código.

**Recomendación:** `APP_DEBUG=false` siempre. Usar logs para depurar:

```bash
# En el .env de UAT:
APP_DEBUG=false
LOG_CHANNEL=daily
LOG_LEVEL=debug
```

```bash
# Seguir logs en tiempo real (más seguro que debug en pantalla):
sudo tail -f /var/www2/iwantit/shared/storage/logs/laravel-$(date +%Y-%m-%d).log
```

**Refuerzo en Laravel 11 — `bootstrap/app.php`:**
```php
->withExceptions(function (Exceptions $exceptions) {
    // No exponer detalles en producción/UAT
    $exceptions->dontReport([]);
    $exceptions->dontFlash([
        'current_password',
        'password',
        'password_confirmation',
    ]);
})
```

### 4.2 Configuraciones `.env` recomendadas para UAT

```bash
APP_NAME="IWantIt-UAT"
APP_ENV=uat
APP_KEY=base64:...                # php artisan key:generate
APP_DEBUG=false
APP_URL=https://uat.i-want-it.es

LOG_CHANNEL=daily
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=demo2                 # base SEPARADA de producción
DB_USERNAME=uat_user              # usuario con privilegios MÍNIMOS
DB_PASSWORD=<password-fuerte>

SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=uat.i-want-it.es
SESSION_SAME_SITE=lax

SANCTUM_STATEFUL_DOMAINS=uat.i-want-it.es

AWS_ACCESS_KEY_ID=<KEY-UAT>      # NUNCA credenciales de producción
AWS_SECRET_ACCESS_KEY=<SECRET-UAT>
AWS_DEFAULT_REGION=eu-west-1
AWS_BUCKET=uat-i-want-it-uploads  # bucket SEPARADO de producción

MAIL_MAILER=log                   # UAT no debe enviar emails reales

CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

### 4.3 Caché y optimización en despliegue

El archivo `deploy.php` omite `route:cache` y `view:cache` por errores de rutas duplicadas. Esto deja las rutas cargándose desde archivos en cada request. Corregir los errores de raíz:

```bash
# Diagnosticar rutas duplicadas
php artisan route:list --json | jq '.[].uri' | sort | uniq -d

# Diagnosticar errores de vistas
php artisan view:cache 2>&1

# Una vez corregido, cachear todo
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 4.4 Auditoría de dependencias

```bash
# Auditoría de vulnerabilidades conocidas
composer audit --locked

# Formato JSON para CI/CD
composer audit --locked --format=json
```

**Hook para `deploy.php`:**
```php
desc('Audit Composer dependencies for vulnerabilities');
task('composer:audit', function () {
    $output = run('{{bin/php}} {{release_path}}/composer.phar audit --locked --format=json', [
        'timeout' => 120,
    ]);
    $result = json_decode($output, true);
    if (!empty($result['advisories'])) {
        writeln('<error>Vulnerabilidades encontradas en dependencias</error>');
        foreach ($result['advisories'] as $package => $advisories) {
            writeln("<error>  - $package: " . count($advisories) . " advisory(s)</error>");
        }
    } else {
        writeln('<info>Auditoria de dependencias: limpia</info>');
    }
});
```

### 4.5 Middleware de seguridad adicional

Crear `app/Http/Middleware/SecureHeaders.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
```

Registrar en `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\SecureHeaders::class,
    ]);
})
```

---

## PRIORIDAD 5 — Seguridad en el Servidor (Ubuntu + AWS EC2)

### 5.1 Endurecimiento de SSH

Fichero: `/etc/ssh/sshd_config`

```bash
sudo cp /etc/ssh/sshd_config /etc/ssh/sshd_config.bak.$(date +%Y%m%d)
```

```ini
# ---- Autenticación ----
PermitRootLogin no
PubkeyAuthentication yes
PasswordAuthentication no
ChallengeResponseAuthentication no
UsePAM yes

# ---- Forwarding ----
X11Forwarding no
AllowTcpForwarding no
AllowAgentForwarding no

# ---- Usuarios permitidos ----
AllowUsers ubuntu deployer

# ---- Protocolo ----
Protocol 2

# ---- Tiempos y límites ----
ClientAliveInterval 300
ClientAliveCountMax 2
MaxAuthTries 3
MaxSessions 5
LoginGraceTime 30

# ---- Algoritmos fuertes (sin CBC, MD5, SHA1) ----
Ciphers chacha20-poly1305@openssh.com,aes256-gcm@openssh.com,aes128-gcm@openssh.com
MACs hmac-sha2-512-etm@openssh.com,hmac-sha2-256-etm@openssh.com
KexAlgorithms curve25519-sha256,curve25519-sha256@libssh.org,diffie-hellman-group16-sha512,diffie-hellman-group18-sha512,diffie-hellman-group-exchange-sha256
HostKeyAlgorithms ssh-ed25519,ssh-ed25519-cert-v01@openssh.com,rsa-sha2-512,rsa-sha2-256

# ---- Logging ----
SyslogFacility AUTH
LogLevel VERBOSE
```

```bash
# Verificar sintaxis y aplicar (NO cierres la sesión actual hasta comprobar)
sudo sshd -t && sudo systemctl restart sshd
# ABRE OTRA TERMINAL y verifica conexión ANTES de cerrar la actual
```

### 5.2 Configuración de UFW

```bash
# Resetear reglas actuales
sudo ufw --force reset

# Políticas por defecto
sudo ufw default deny incoming
sudo ufw default allow outgoing

# Permitir servicios necesarios
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw limit 22/tcp      # rate limiting en SSH

# Si usas VPN/Tailscale:
# sudo ufw allow from 100.64.0.0/10 to any port 22

# Activar
sudo ufw --force enable

# Verificar
sudo ufw status verbose
```

Resultado esperado:

```
Status: active
Logging: on (low)
Default: deny (incoming), allow (outgoing)

To          Action      From
--          ------      ----
80/tcp      ALLOW IN    Anywhere
443/tcp     ALLOW IN    Anywhere
22/tcp      LIMIT IN    Anywhere
```

### 5.3 Defensa en profundidad: 3 capas de filtrado

| Capa | Herramienta | Función |
|---|---|---|
| Perímetro | AWS Security Group | Filtra en el hypervisor, no consume CPU de la instancia |
| Host | UFW | Segunda barrera si el SG falla o se modifica |
| Aplicación | Nginx `allow/deny` + Basic Auth | Control granular por ruta |

### 5.4 Actualizaciones automáticas de seguridad

```bash
sudo dpkg-reconfigure --priority=low unattended-upgrades
# Seleccionar "Yes"

sudo vim /etc/apt/apt.conf.d/50unattended-upgrades
```

```
Unattended-Upgrade::Allowed-Origins {
    "${distro_id}:${distro_codename}-security";
    "${distro_id}ESMApps:${distro_codename}-apps-security";
    "${distro_id}ESM:${distro_codename}-infra-security";
};

Unattended-Upgrade::DevRelease "false";
Unattended-Upgrade::AutoFixInterruptedDpkg "true";
Unattended-Upgrade::Remove-Unused-Kernel-Packages "true";
Unattended-Upgrade::Remove-New-Unused-Dependencies "true";
Unattended-Upgrade::Remove-Unused-Dependencies "true";
Unattended-Upgrade::Automatic-Reboot "true";
Unattended-Upgrade::Automatic-Reboot-Time "04:00";
```

### 5.5 Auditorías programadas (cron semanal)

```bash
sudo crontab -e
```

```
# Domingo 03:00 — Lynis audit
0 3 * * 0 /usr/bin/lynis audit system --cronjob --quiet 2>&1 | mail -s "Lynis Audit UAT" admin@i-want-it.es

# Domingo 03:30 — ClamAV scan del storage
30 3 * * 0 /usr/bin/freshclam && /usr/bin/clamscan -r /var/www2/iwantit/shared/storage --log=/var/log/clamav/scan.log
```

---

## Checklist de Verificación

| # | Acción | Comando de verificación | Estado |
|---|---|---|---|
| 1 | MariaDB único en puerto 3306 | `sudo ss -tlnp \| grep 3306` (1 línea = mariadbd) | ☐ |
| 2 | MySQL huérfano purgado | `dpkg -l \| grep mysql-server-8.0` → sin `rc` | ☐ |
| 3 | `mysql.service` enmascarado | `systemctl status mysql` → `masked` o `not found` | ☐ |
| 4 | `robots.txt` → `Disallow: /` | `curl -s https://uat.i-want-it.es/robots.txt` | ☐ |
| 5 | `X-Robots-Tag` → `noindex` | `curl -I https://uat.i-want-it.es \| grep -i robots` | ☐ |
| 6 | HTTP Basic Auth activo | `curl -o /dev/null -w '%{http_code}' https://uat.i-want-it.es/` → `401` | ☐ |
| 7 | HSTS presente | `curl -I https://uat.i-want-it.es \| grep strict-transport` | ☐ |
| 8 | CSP presente | `curl -I https://uat.i-want-it.es \| grep content-security` | ☐ |
| 9 | `X-Frame-Options` presente | `curl -I https://uat.i-want-it.es \| grep x-frame` | ☐ |
| 10 | `server_tokens off` | `curl -I https://uat.i-want-it.es \| grep -i server` sin versión | ☐ |
| 11 | `expose_php = Off` | `curl -I https://uat.i-want-it.es \| grep -i x-powered-by` → vacío | ☐ |
| 12 | `APP_DEBUG=false` | `grep APP_DEBUG /var/www2/iwantit/shared/.env` | ☐ |
| 13 | `disable_functions` activo | `php -r "echo ini_get('disable_functions');"` | ☐ |
| 14 | `cgi.fix_pathinfo = 0` | `php -r "echo ini_get('cgi.fix_pathinfo');"` → `0` | ☐ |
| 15 | `SESSION_ENCRYPT=true` | `php artisan tinker --execute="echo config('session.encrypt');"` → `1` | ☐ |
| 16 | `SESSION_SECURE_COOKIE=true` | `php artisan tinker --execute="echo config('session.secure');"` → `1` | ☐ |
| 17 | `PermitRootLogin no` | `sudo sshd -T \| grep permitrootlogin` → `no` | ☐ |
| 18 | `PasswordAuthentication no` | `sudo sshd -T \| grep passwordauth` → `no` | ☐ |
| 19 | UFW activo | `sudo ufw status verbose` | ☐ |
| 20 | Security Groups AWS limitados | AWS Console → EC2 → Security Groups | ☐ |
| 21 | `composer audit` limpio | `composer audit --locked` en el release | ☐ |
| 22 | Caché optimizada | `php artisan config:cache` sin errores | ☐ |
| 23 | Base de datos UAT separada | Verificar `DB_DATABASE` en `.env` | ☐ |
| 24 | Unattended-upgrades activo | `systemctl status unattended-upgrades` | ☐ |

---

## Script de Auditoría Automática

Guardar como `~/audit-uat.sh` en el servidor:

```bash
#!/usr/bin/env bash
set -euo pipefail

RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m'

check() {
    if [ $? -eq 0 ]; then
        echo -e "  ${GREEN}[PASS]${NC} $1"
    else
        echo -e "  ${RED}[FAIL]${NC} $1"
    fi
}

echo "=== AUDITORÍA DE SEGURIDAD UAT ==="
echo

echo "[1] Conflicto MySQL/MariaDB:"
ss -tlnp | grep 3306
check "Un solo proceso en 3306"
dpkg -l | grep -E 'mysql-server|mariadb-server' | grep '^ii'
check "Un solo paquete instalado"
systemctl is-active mysql 2>&1 | grep -qE 'inactive|masked|unknown'
check "mysql.service enmascarado o inactivo"

echo "[2] robots.txt:"
curl -sf https://uat.i-want-it.es/robots.txt 2>/dev/null || true

echo "[3] Cabeceras HTTP:"
curl -sI https://uat.i-want-it.es 2>/dev/null | grep -iE 'x-frame|x-content|strict-transport|x-robots|content-security|server|x-powered-by' || true

echo "[4] HTTP Basic Auth:"
HTTP_CODE=$(curl -so /dev/null -w '%{http_code}' https://uat.i-want-it.es/ 2>/dev/null)
echo "  HTTP Code: $HTTP_CODE"
[ "$HTTP_CODE" = "401" ]
check "Basic Auth activa (HTTP 401)"

echo "[5] APP_DEBUG:"
grep 'APP_DEBUG=' /var/www2/iwantit/shared/.env 2>/dev/null || echo "  No se pudo leer .env"

echo "[6] PHP Security:"
php -r "
echo 'expose_php='.ini_get('expose_php').PHP_EOL;
echo 'allow_url_fopen='.ini_get('allow_url_fopen').PHP_EOL;
echo 'fix_pathinfo='.ini_get('cgi.fix_pathinfo').PHP_EOL;
echo 'disable_functions='.ini_get('disable_functions').PHP_EOL;
echo 'session.cookie_secure='.ini_get('session.cookie_secure').PHP_EOL;
echo 'session.cookie_httponly='.ini_get('session.cookie_httponly').PHP_EOL;
" 2>/dev/null || true

echo "[7] SSH:"
sudo sshd -T 2>/dev/null | grep -iE 'permitrootlogin|passwordauth|pubkeyauth|allowusers|protocol' || true

echo "[8] UFW:"
sudo ufw status verbose 2>/dev/null || true

echo "[9] unattended-upgrades:"
systemctl is-active unattended-upgrades 2>/dev/null || true

echo "[10] Dependencias:"
if [ -f /var/www2/iwantit/current/composer.lock ]; then
    php /var/www2/iwantit/current/composer.phar audit --locked --format=json 2>/dev/null | python3 -m json.tool 2>/dev/null | head -20 || true
else
    echo "  composer.lock no encontrado en el release actual"
fi

echo
echo "=== FIN ==="
```

```bash
chmod +x ~/audit-uat.sh
sudo ~/audit-uat.sh
```

---

## Resumen de Riesgos por Prioridad

| Prioridad | Riesgo | Impacto | Mitigación |
|---|---|---|---|
| 1 | MaríaDB/MySQL en conflicto | Caída total de la aplicación tras reboot | Enmascarar mysql.service, purgar paquetes `rc` |
| 2a | Indexación en Google/Bing | Fuga de URLs internas, pantallas de login | `robots.txt` + `X-Robots-Tag` |
| 2b | Sin barrera de acceso | Cualquiera puede ver la pantalla de login y atacar | HTTP Basic Auth en Nginx |
| 2c | Contraseñas débiles en QA | Acceso no autorizado al panel de administración | `Password::min(12)->uncompromised()` + rate limiting |
| 3a | Versión de Nginx expuesta | Facilita ataques dirigidos a CVE conocidos | `server_tokens off`, `more_clear_headers` |
| 3b | Sin cabeceras de seguridad | Clickjacking, MIME-sniffing, XSS | HSTS + CSP + X-Frame + X-Content-Type |
| 3c | PHP expuesto | Revela stack de tecnología | `expose_php = Off`, `disable_functions` |
| 4a | `APP_DEBUG=true` | Stack traces con credenciales AWS/BD visibles | `APP_DEBUG=false`, usar logs |
| 4b | Dependencias sin auditar | Vulnerabilidades conocidas en paquetes Composer | `composer audit` en cada deploy |
| 5a | SSH con password | Fuerza bruta contra el servidor | Solo clave pública, `AllowUsers` restrictivo |
| 5b | Firewall sin rate limiting | Ataques de fuerza bruta sin freno | `ufw limit 22/tcp` |
| 5c | Sin actualizaciones automáticas | CVEs sin parchear | `unattended-upgrades` + reboot programado |
