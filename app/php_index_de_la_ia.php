<?php
declare(strict_types=1);

/**
 * Endpoint HTTP for Nginx/PHP-FPM.
 * POST JSON:
 * {
 *   "classes": [],
 *   "id_project": 333,
 *   "path": "https://demo.i-want-it.es/uploads/1749551993.mp4",
 *   "threshold_sec": 2
 * }
 *
 * Success: { "task_id": "..." }
 * Error:   { "error": { "code": "...", "message": "..." } }
 */

//// ===== CONFIG =====
const STORAGE_DIR       = __DIR__ . '/downloads';
const DOWN_TIMEOUT_S    = 300;
const DOWN_CONNECT_S    = 15;
const POST_URL          = 'http://localhost:5018/v1/process_media';
const POST_CONNECT_S    = 10;
const POST_TIMEOUT_S    = 60;
const ACCEPT_INSECURE_LOCAL_TLS = true;

//// ===== UTILS =====
function send_json(array $payload, int $httpCode = 200): void {
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        http_response_code($httpCode);
    }
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

set_exception_handler(function(Throwable $e) {
    send_json(['error' => ['code' => 'UNCAUGHT_EXCEPTION', 'message' => $e->getMessage()]], 500);
});

set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

function ensure_storage_dir(): void {
    if (!is_dir(STORAGE_DIR)) {
        if (!@mkdir(STORAGE_DIR, 0775, true)) {
            send_json(['error' => ['code' => 'STORAGE_INIT_ERROR', 'message' => 'Could not create storage directory']], 500);
        }
    }
    if (!is_writable(STORAGE_DIR)) {
        send_json(['error' => ['code' => 'STORAGE_NOT_WRITABLE', 'message' => 'Storage directory is not writable']], 500);
    }
}

function read_input_json(): array {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        send_json(['ok' => true, 'msg' => 'Send me POST JSON'], 200);
    }
    $ct = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($ct, 'application/json') === false) {
        send_json(['error' => ['code' => 'UNSUPPORTED_MEDIA_TYPE', 'message' => 'Content-Type must be application/json']], 415);
    }
    $raw = file_get_contents('php://input');
    $data = json_decode((string)$raw, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        send_json(['error' => ['code' => 'INVALID_JSON', 'message' => 'Invalid JSON: ' . json_last_error_msg()]], 400);
    }
    return $data;
}

function validate_url(string $url): void {
    $parts = parse_url($url);
    if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
        send_json(['error' => ['code' => 'INVALID_URL', 'message' => 'Field "path" is not a valid URL']], 400);
    }
    if (!in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
        send_json(['error' => ['code' => 'UNSUPPORTED_SCHEME', 'message' => 'Only http/https schemes are supported']], 400);
    }
}

function file_ext_from_url(string $url): ?string {
    $p = parse_url($url, PHP_URL_PATH);
    if (!$p) return null;
    $ext = pathinfo($p, PATHINFO_EXTENSION);
    return $ext !== '' ? $ext : null;
}

function random_filename(?string $ext): string {
    $name = bin2hex(random_bytes(16));
    $ext  = $ext ? ltrim($ext, '.') : 'bin';
    return $name . '.' . $ext;
}

function ensure_space(?int $bytesNeeded): void {
    if ($bytesNeeded === null) return;
    $free = @disk_free_space(STORAGE_DIR);
    if ($free !== false && $free < $bytesNeeded) {
        send_json(['error' => ['code' => 'INSUFFICIENT_SPACE', 'message' => 'Not enough disk space']], 507);
    }
}

function curl_errno_label(int $errno): string {
    return 'CURL_' . $errno;
}

function estimate_remote_size(string $url): ?int {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_NOBODY         => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_CONNECTTIMEOUT => DOWN_CONNECT_S,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_FAILONERROR    => true,
        CURLOPT_USERAGENT      => 'fetch-and-notify/1.0',
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_2TLS,
    ]);
    $ok = curl_exec($ch);
    $len = null;
    if ($ok) {
        $v = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        if (is_numeric($v) && (int)$v >= 0) $len = (int)$v;
    }
    curl_close($ch);
    return $len;
}

function download_to(string $url, string $destPath): void {
    $fp = @fopen($destPath, 'wb');
    if (!$fp) {
        send_json(['error' => ['code' => 'WRITE_ERROR', 'message' => 'Could not create destination file']], 500);
    }

	echo $url . PHP_EOL;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_FILE           => $fp,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_CONNECTTIMEOUT => DOWN_CONNECT_S,
        CURLOPT_TIMEOUT        => DOWN_TIMEOUT_S,
        CURLOPT_FAILONERROR    => true,
        CURLOPT_USERAGENT      => 'fetch-and-notify/1.0',
        CURLOPT_NOSIGNAL       => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_2TLS,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $ok   = curl_exec($ch);
    $errno= curl_errno($ch);
    $err  = curl_error($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);

    if (!$ok) {
        @unlink($destPath);
        $code = $errno ? curl_errno_label($errno) : 'DOWNLOAD_ERROR';
        $msg  = $errno ? ("cURL error ($errno): $err") : ("HTTP $http while downloading resource");
        $status = ($errno || $http >= 500) ? 502 : 404;
        send_json(['error' => ['code' => $code, 'message' => $msg]], $status);
    }

    if (!is_file($destPath) || filesize($destPath) === 0) {
        @unlink($destPath);
        send_json(['error' => ['code' => 'EMPTY_FILE', 'message' => 'Downloaded file is empty']], 500);
    }
}

// Reemplaza tu post_json() por esta versión con fallback
function post_json_with_fallback(string $baseUrl, array $payload): array {
    $try = function (string $url) use ($payload): array {
        $ch = curl_init($url);
        $opts = [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_USERAGENT      => 'fetch-and-notify/1.0',
            // Fuerza HTTP/1.1 para evitar rarezas con ALPN
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        ];

        // Si es https, puedes (opcional) relajar verificación local
        if (str_starts_with($url, 'https://')) {
            $opts[CURLOPT_SSL_VERIFYPEER] = false;
            $opts[CURLOPT_SSL_VERIFYHOST] = 0;
        }

        curl_setopt_array($ch, $opts);
        $resp  = curl_exec($ch);
        $errno = curl_errno($ch);
        $err   = curl_error($ch);
        $http  = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        return [$resp, $errno, $err, $http];
    };

    $urlHttps = preg_replace('#^http://#i', 'https://', $baseUrl);
    $urlHttp  = preg_replace('#^https://#i', 'http://', $baseUrl);

    // 1) Intento HTTPS
    [$resp, $errno, $err, $http] = $try($urlHttps);

    // Si es el típico fallo TLS (35 / "wrong version number"), probamos HTTP
    $looksTlsMismatch = $errno === 35 || str_contains(strtolower($err), 'wrong version number');

    if ($errno && $looksTlsMismatch) {
        // 2) Fallback a HTTP
        [$resp, $errno, $err, $http] = $try($urlHttp);
    }
x
    if ($errno) {
        send_json(['error' => ['code' => 'CURL_' . $errno, 'message' => "cURL error posting: $err"]], 502);
    }
    if ($http < 200 || $http >= 300) {
        send_json(['error' => ['code' => 'POST_HTTP_ERROR', 'message' => "HTTP $http from {$baseUrl}"]], 502);
    }
    $decoded = json_decode((string)$resp, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
        send_json(['error' => ['code' => 'POST_INVALID_JSON', 'message' => 'Response is not valid JSON: ' . json_last_error_msg()]], 502);
    }
    return $decoded;
}

//// ===== MAIN =====
ensure_storage_dir();
$input = read_input_json();

// Minimal validation
if (!isset($input['path']) || !is_string($input['path']) || $input['path'] === '') {
    send_json(['error' => ['code' => 'INVALID_INPUT', 'message' => '"path" field is required and must be a string']], 400);
}
validate_url($input['path']);

$remoteUrl = $input['path'];
$ext       = file_ext_from_url($remoteUrl);
$filename  = random_filename($ext);
$destPath  = STORAGE_DIR . DIRECTORY_SEPARATOR . $filename;

// Estimate size & check disk space
$estimated = estimate_remote_size($remoteUrl);
ensure_space($estimated);

// Download
download_to($remoteUrl, $destPath);

// Prepare payload for POST
$payload = $input;
$payload['path'] = $destPath;

print_r( $payload );

// Notify
$response = post_json_with_fallback(POST_URL, $payload);

// Validate task_id
if (!isset($response['task_id']) || !is_string($response['task_id']) || $response['task_id'] === '') {
    send_json(['error' => ['code' => 'MISSING_TASK_ID', 'message' => 'Response did not contain a valid task_id']], 502);
}

// Success
send_json(['task_id' => $response['task_id']], 200);