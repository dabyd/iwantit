<?php

// app/Helpers/TimeHelper.php

if (!function_exists('formatSecondsToTime')) {
    /**
     * Convierte segundos a formato hora:minuto:segundo
     *
     * @param float|int $seconds Número de segundos (puede tener decimales)
     * @return string Formato HH:MM:SS
     */
    function formatSecondsToTime($seconds)
    {
        // Redondear hacia arriba los segundos si tiene decimales
        $seconds = (int) ceil($seconds);
        
        // Calcular horas, minutos y segundos
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;
        
        // Formatear con ceros a la izquierda
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
    }
}

if (!function_exists('formatSecondsToTimeShort')) {
    /**
     * Convierte segundos a formato corto (omite las horas si son 0)
     *
     * @param float|int $seconds Número de segundos
     * @return string Formato MM:SS o HH:MM:SS
     */
    function formatSecondsToTimeShort($seconds)
    {
        $seconds = (int) ceil($seconds);
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
        } else {
            return sprintf('%02d:%02d', $minutes, $remainingSeconds);
        }
    }
}

if (!function_exists('formatSecondsToTimeHuman')) {
    /**
     * Convierte segundos a formato legible por humanos
     *
     * @param float|int $seconds Número de segundos
     * @return string Formato "X horas, Y minutos, Z segundos"
     */
    function formatSecondsToTimeHuman($seconds)
    {
        $seconds = (int) ceil($seconds);
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;
        
        $parts = [];
        
        if ($hours > 0) {
            $parts[] = $hours . ($hours === 1 ? ' hora' : ' horas');
        }
        
        if ($minutes > 0) {
            $parts[] = $minutes . ($minutes === 1 ? ' minuto' : ' minutos');
        }
        
        if ($remainingSeconds > 0 || empty($parts)) {
            $parts[] = $remainingSeconds . ($remainingSeconds === 1 ? ' segundo' : ' segundos');
        }
        
        return implode(', ', $parts);
    }
}

if (!function_exists('parseTimeToSeconds')) {
    /**
     * Convierte formato HH:MM:SS a segundos (función inversa)
     *
     * @param string $time Tiempo en formato HH:MM:SS o MM:SS
     * @return int Número de segundos
     */
    function parseTimeToSeconds($time)
    {
        $parts = explode(':', $time);
        $parts = array_reverse($parts);
        
        $seconds = 0;
        $multiplier = 1;
        
        foreach ($parts as $part) {
            $seconds += (int) $part * $multiplier;
            $multiplier *= 60;
        }
        
        return $seconds;
    }
}