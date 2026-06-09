<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OpcacheReset extends Command
{
    protected $signature = 'opcache:reset';

    protected $description = 'Reset PHP OPcache via web process';

    public function handle()
    {
        $file = public_path('opcache-reset-tmp.php');
        file_put_contents($file, '<?php opcache_reset(); unlink(__FILE__); echo "OK";');

        $url = config('app.url').'/opcache-reset-tmp.php';
        $this->info("Triggering OPcache reset via {$url}...");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $result === 'OK') {
            $this->info('OPcache reset successfully.');

            return self::SUCCESS;
        }

        $this->warn("HTTP {$httpCode} — response: {$result}");
        $this->warn('If OPcache is not reset, restart PHP-FPM manually or access /admin/opcache-reset as Admin.');

        return self::SUCCESS;
    }
}
