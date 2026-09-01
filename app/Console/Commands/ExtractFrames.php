<?php

namespace App\Console\Commands;

use App\Models\Appearance;
use App\Models\Project;
use Illuminate\Console\Command;

class ExtractFrames extends Command
{
    protected $signature = 'frames:extract
                            {project : ID o demo_code del proyecto}
                            {--appearance=* : IDs de apariciones concretas (por defecto, todas las del proyecto)}
                            {--crop : Recorta la imagen al bounding box de la aparición}
                            {--out=uploads/evidence : Directorio de salida (relativo a public/)}';

    protected $description = 'Extrae frames (y opcionalmente crops) de las apariciones de un proyecto usando ffmpeg';

    public function handle(): int
    {
        $project = $this->resolveProject((string) $this->argument('project'));

        $videoPath = public_path('uploads/'.$project->filename);
        if (! $project->filename || ! is_file($videoPath)) {
            $this->error("Vídeo no encontrado para el proyecto: {$videoPath}");

            return self::FAILURE;
        }

        $ffmpeg = $this->resolveFfmpeg();
        if ($ffmpeg === null) {
            $this->error('ffmpeg no encontrado. Configura FFMPEG_PATH en el .env o instala ffmpeg.');

            return self::FAILURE;
        }

        $query = Appearance::whereHas('inventoryItem', fn ($q) => $q->where('project_id', $project->id));

        $appearanceIds = array_values(array_filter(array_map('intval', $this->option('appearance') ?: [])));
        if ($appearanceIds !== []) {
            $query->whereIn('id', $appearanceIds);
        }

        $appearances = $query->get();
        if ($appearances->isEmpty()) {
            $this->warn('No hay apariciones para extraer.');

            return self::SUCCESS;
        }

        $info = getVideoInfo($videoPath);
        $width = (int) ($info['width'] ?? 0);
        $height = (int) ($info['height'] ?? 0);

        $outRoot = public_path(rtrim((string) $this->option('out'), '/'));
        $crop = (bool) $this->option('crop');

        $bar = $this->output->createProgressBar($appearances->count());
        $bar->start();

        $extracted = 0;
        foreach ($appearances as $appearance) {
            $seconds = ($appearance->start_time + $appearance->end_time) / 2000;
            $dir = "{$outRoot}/{$project->id}";
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = "appearance_{$appearance->id}.jpg";
            $path = "{$dir}/{$filename}";

            $cmd = $this->ffmpegCommand($ffmpeg, $videoPath, $seconds, $crop, $appearance, $width, $height, $path);
            shell_exec($cmd.' 2>&1');

            if (is_file($path) && filesize($path) > 0) {
                $this->registerEvidence($appearance, $project->id, $filename, $crop);
                $extracted++;
            } else {
                $this->newLine();
                $this->warn("Fallo al extraer la aparición #{$appearance->id}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("{$extracted} de {$appearances->count()} frames extraídos en {$outRoot}/{$project->id}");

        return self::SUCCESS;
    }

    private function ffmpegCommand(string $ffmpeg, string $videoPath, float $seconds, bool $crop, Appearance $appearance, int $width, int $height, string $outPath): string
    {
        $cmd = $ffmpeg.' -y -i '.escapeshellarg($videoPath).' -ss '.$seconds;

        if ($crop && $width > 0 && $height > 0) {
            $cmd .= ' -vf '.escapeshellarg($this->cropFilter($appearance, $width, $height));
        }

        return $cmd.' -frames:v 1 -q:v 2 '.escapeshellarg($outPath);
    }

    private function cropFilter(Appearance $appearance, int $width, int $height): string
    {
        $cx = ((float) $appearance->pos_x / 100) * $width;
        $cy = ((float) $appearance->pos_y / 100) * $height;
        $bw = ((float) $appearance->w / 100) * $width;
        $bh = ((float) $appearance->h / 100) * $height;

        $x = max(0, (int) round($cx - $bw / 2));
        $y = max(0, (int) round($cy - $bh / 2));
        $w = min((int) round($bw), $width - $x);
        $h = min((int) round($bh), $height - $y);

        return "crop={$w}:{$h}:{$x}:{$y}";
    }

    private function registerEvidence(Appearance $appearance, int $projectId, string $filename, bool $crop): void
    {
        $type = $crop ? 'crop' : 'frame';

        if ($appearance->evidence()->where('type', $type)->exists()) {
            return;
        }

        $appearance->evidence()->create([
            'type' => $type,
            'file_path' => "uploads/evidence/{$projectId}/{$filename}",
            'timecode' => (int) (($appearance->start_time + $appearance->end_time) / 2),
            'source' => 'manual',
            'provider' => 'ffmpeg',
            'generated_at' => now(),
        ]);
    }

    private function resolveFfmpeg(): ?string
    {
        $configured = config('app.ffmpeg_path');

        if (is_executable($configured)) {
            return $configured;
        }

        $fromPath = trim((string) shell_exec('command -v ffmpeg 2>/dev/null'));

        return $fromPath !== '' ? $fromPath : null;
    }

    private function resolveProject(string $value): Project
    {
        $project = is_numeric($value)
            ? Project::find($value)
            : Project::where('demo_code', $value)->orWhere('name', $value)->first();

        if (! $project) {
            throw new \InvalidArgumentException("Proyecto no encontrado: {$value}");
        }

        return $project;
    }
}
