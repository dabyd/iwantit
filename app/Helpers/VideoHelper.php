<?php

if (!function_exists('getVideoFPS')) {
    function getVideoFPS($videoPath) {
        $ffprobe = env('FFPROBE_PATH');
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
        $ffprobe = env('FFPROBE_PATH');
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
            $appUrl = env('APP_URL', 'http://localhost');
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