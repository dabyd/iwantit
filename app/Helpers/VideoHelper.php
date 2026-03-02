<?php

if (!function_exists('getVideoFPS')) {
    function getVideoFPS($videoPath) {
        $ffprobe = config('app.ffprobe_path');
        $cmd = "$ffprobe -v 0 -select_streams v:0 -show_entries stream=r_frame_rate -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($videoPath) . " 2>&1";
        $output = shell_exec($cmd);

        if (!$output) {
            return 24;
        }

        if (str_contains($output, '/')) {
            [$num, $den] = explode('/', trim($output));
            return (int)$den !== 0 ? round((float)$num / (float)$den, 4) : 24;
        }

        if (is_null( $output ) || '' == $output ) {
            $output = 24;
        }

        return (float)trim($output);
    }

    function getVideoResolution($videoPath) {
        $ffprobe = config('app.ffprobe_path');
        $cmd = "$ffprobe -v error -select_streams v:0 -show_entries stream=width,height -of json " . escapeshellarg($videoPath) . " 2>&1";
        $output = shell_exec($cmd);

        $data = json_decode($output, true);

        $ret = array(
            'width' => null,
            'height' => null
        );
        if (isset($data['streams'][0]['width']) && isset($data['streams'][0]['height'])) {
            $ret['width']  = $data['streams'][0]['width'];
            $ret['height'] = $data['streams'][0]['height'];
        }
        return $ret;
    }
}

if (!function_exists('getVideoDuration')) {
    function getVideoDuration(string $videoPath): float {
        $ffprobe = config('app.ffprobe_path');
        $cmd = "$ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($videoPath) . " 2>&1";
        $output = shell_exec($cmd);
        $duration = (float)trim($output);
        return $duration > 0 ? $duration : 0.0;
    }

    function formatSrtTimestamp(float $seconds): string {
        $ms = (int)round(($seconds - floor($seconds)) * 1000);
        $s  = (int)floor($seconds);
        $h  = (int)floor($s / 3600);
        $m  = (int)floor(($s % 3600) / 60);
        $s  = $s % 60;
        return sprintf('%02d:%02d:%02d,%03d', $h, $m, $s, $ms);
    }

    /**
     * Genera contenido SRT en bloques de 10 segundos, repitiendo el mismo keyContent.
     *
     * @param float  $totalDuration  Duración total del vídeo en segundos.
     * @param string $keyContent     Contenido de la key (project_id + key hash).
     * @param int    $blockSeconds   Duración de cada bloque en segundos (por defecto 10).
     * @return string                Contenido SRT completo.
     */
    function generateSrtContent(float $totalDuration, string $keyContent, int $blockSeconds = 10): string {
        $srt = '';
        $index = 1;
        $currentTime = 0.0;

        while ($currentTime < $totalDuration) {
            $endTime = $currentTime + $blockSeconds;
            if ($endTime > $totalDuration) {
                $endTime = $totalDuration;
            }

            $startTs = formatSrtTimestamp($currentTime);
            $endTs   = formatSrtTimestamp($endTime);

            $srt .= $index . "\r\n";
            $srt .= $startTs . ' --> ' . $endTs . "\r\n";
            $srt .= $keyContent . "\r\n";
            $srt .= "\r\n";

            $index++;
            $currentTime += $blockSeconds;
        }

        return $srt;
    }
}

if (!function_exists('getAbsoluteFileUrl')) {
    /**
     * Convierte una URL parcial en una URL absoluta, verificando la existencia del archivo.
     * Si el archivo no existe, devuelve una URL a una imagen de "No Disponible".
     *
     * 🔥 FIX: CLI-safe - No falla cuando se ejecuta en modo CLI
     *
     * @param string $partialUrl La URL parcial (ej: "/uploads/1234.jpg").
     * @param string $noImageUrl La URL de la imagen por defecto si el archivo no existe.
     *                             Por defecto es "/img/No_Image_Available.jpg".
     * @return string La URL absoluta del archivo o la URL de "No Disponible".
     */
    function getAbsoluteFileUrl(string $partialUrl, string $noImageUrl = '/img/No_Image_Available.jpg'): string
    {
        // 🔥 FIX: Detectar si estamos en CLI
        $isCli = (php_sapi_name() === 'cli' || !isset($_SERVER['HTTP_HOST']));
        
        if ($isCli) {
            // En CLI, devolver la URL relativa o usar APP_URL del .env
            $appUrl = config('app.url', 'http://localhost');
            return rtrim($appUrl, '/') . '/' . ltrim($partialUrl, '/');
        }
        
        // Limpiar y normalizar la URL parcial para asegurar que empiece con '/'
        // y evitar dobles barras al concatenar
        $partialUrl = '/' . ltrim($partialUrl, '/');
        $noImageUrl = '/' . ltrim($noImageUrl, '/');

        // 1. Construir la ruta absoluta en el sistema de archivos
        //    $_SERVER['DOCUMENT_ROOT'] es la raíz del servidor web (ej: /var/www/html)
        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? public_path();
        $filePath = $documentRoot . $partialUrl;

        // 2. Verificar si el archivo existe en el sistema de archivos
        if (file_exists($filePath) && is_file($filePath)) {
            // Si el archivo existe, construir la URL absoluta.
            // $_SERVER['REQUEST_SCHEME'] (http/https) y $_SERVER['HTTP_HOST'] (dominio.com)
            $protocol = $_SERVER['REQUEST_SCHEME'] ?? 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

            return $protocol . '://' . $host . $partialUrl;
        } else {
            // Si el archivo no existe, devolver la URL de la imagen de "No Disponible".
            $protocol = $_SERVER['REQUEST_SCHEME'] ?? 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

            return $protocol . '://' . $host . $noImageUrl;
        }
    }
}