<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ClickStatistic extends Model
{
    use HasFactory;

    protected $table = 'click_statistics';

    protected $fillable = [
        'type',
        'versions_id',
        'products_id',
        'brands_id',
        'video_time',
        'ip_address',
        'user_agent',
        'browser',
        'browser_version',
        'os',
        'os_version',
        'device',
        'referer',
        'license_key',
    ];

    protected $casts = [
        'video_time' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'products_id');
    }

    /**
     * Relación con Brand
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brands_id');
    }

    /**
     * Registra una visualización de la API (cuando se llama a get_hotpoints)
     */
    public static function logView(Request $request): self
    {
        $clientInfo = self::parseClientInfo($request);
        
        return self::create([
            'type' => 'view',
            'versions_id' => $request->vid ?? null,
            'video_time' => $request->time ?? null,
            'ip_address' => $clientInfo['ip'],
            'user_agent' => $clientInfo['user_agent'],
            'browser' => $clientInfo['browser'],
            'browser_version' => $clientInfo['browser_version'],
            'os' => $clientInfo['os'],
            'os_version' => $clientInfo['os_version'],
            'device' => $clientInfo['device'],
            'referer' => $request->header('referer'),
            'license_key' => $request->key ?? null,
        ]);
    }

    /**
     * Registra un clic en producto o marca
     */
    public static function logClick(Request $request, string $type, int $id, ?int $versionsId = null): self
    {
        $clientInfo = self::parseClientInfo($request);
        
        $data = [
            'type' => 'click',
            'versions_id' => $versionsId ?? $request->vid ?? null,
            'video_time' => $request->time ?? null,
            'ip_address' => $clientInfo['ip'],
            'user_agent' => $clientInfo['user_agent'],
            'browser' => $clientInfo['browser'],
            'browser_version' => $clientInfo['browser_version'],
            'os' => $clientInfo['os'],
            'os_version' => $clientInfo['os_version'],
            'device' => $clientInfo['device'],
            'referer' => $request->header('referer'),
        ];

        if ($type === 'product') {
            $data['products_id'] = $id;
        } elseif ($type === 'brand') {
            $data['brands_id'] = $id;
        }

        return self::create($data);
    }

    /**
     * Parsea la información del cliente desde el Request
     */
    public static function parseClientInfo(Request $request): array
    {
        $userAgent = $request->header('User-Agent', '');
        $ip = $request->ip();

        // Parsear User-Agent para extraer browser, OS y device
        $browser = 'Unknown';
        $browserVersion = '';
        $os = 'Unknown';
        $osVersion = '';
        $device = 'desktop';

        // Detectar navegador
        if (preg_match('/Firefox\/([0-9.]+)/i', $userAgent, $matches)) {
            $browser = 'Firefox';
            $browserVersion = $matches[1];
        } elseif (preg_match('/Edg\/([0-9.]+)/i', $userAgent, $matches)) {
            $browser = 'Edge';
            $browserVersion = $matches[1];
        } elseif (preg_match('/Chrome\/([0-9.]+)/i', $userAgent, $matches)) {
            $browser = 'Chrome';
            $browserVersion = $matches[1];
        } elseif (preg_match('/Safari\/([0-9.]+)/i', $userAgent, $matches) && !preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Safari';
            if (preg_match('/Version\/([0-9.]+)/i', $userAgent, $versionMatches)) {
                $browserVersion = $versionMatches[1];
            }
        } elseif (preg_match('/MSIE ([0-9.]+)/i', $userAgent, $matches)) {
            $browser = 'Internet Explorer';
            $browserVersion = $matches[1];
        } elseif (preg_match('/Trident\/.*rv:([0-9.]+)/i', $userAgent, $matches)) {
            $browser = 'Internet Explorer';
            $browserVersion = $matches[1];
        } elseif (preg_match('/Opera|OPR\/([0-9.]+)/i', $userAgent, $matches)) {
            $browser = 'Opera';
            $browserVersion = $matches[1] ?? '';
        }

        // Detectar sistema operativo
        if (preg_match('/Windows NT ([0-9.]+)/i', $userAgent, $matches)) {
            $os = 'Windows';
            $osVersionMap = [
                '10.0' => '10/11',
                '6.3' => '8.1',
                '6.2' => '8',
                '6.1' => '7',
                '6.0' => 'Vista',
                '5.1' => 'XP',
            ];
            $osVersion = $osVersionMap[$matches[1]] ?? $matches[1];
        } elseif (preg_match('/Mac OS X ([0-9_.]+)/i', $userAgent, $matches)) {
            $os = 'macOS';
            $osVersion = str_replace('_', '.', $matches[1]);
        } elseif (preg_match('/iPhone OS ([0-9_]+)/i', $userAgent, $matches)) {
            $os = 'iOS';
            $osVersion = str_replace('_', '.', $matches[1]);
            $device = 'mobile';
        } elseif (preg_match('/iPad.*OS ([0-9_]+)/i', $userAgent, $matches)) {
            $os = 'iPadOS';
            $osVersion = str_replace('_', '.', $matches[1]);
            $device = 'tablet';
        } elseif (preg_match('/Android ([0-9.]+)/i', $userAgent, $matches)) {
            $os = 'Android';
            $osVersion = $matches[1];
            $device = preg_match('/Mobile/i', $userAgent) ? 'mobile' : 'tablet';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $os = 'Linux';
        }

        // Detectar dispositivo móvil si no se detectó antes
        if ($device === 'desktop') {
            if (preg_match('/Mobile|Android|iPhone|iPod/i', $userAgent)) {
                $device = 'mobile';
            } elseif (preg_match('/Tablet|iPad/i', $userAgent)) {
                $device = 'tablet';
            }
        }

        return [
            'ip' => $ip,
            'user_agent' => $userAgent,
            'browser' => $browser,
            'browser_version' => $browserVersion,
            'os' => $os,
            'os_version' => $osVersion,
            'device' => $device,
        ];
    }
}
