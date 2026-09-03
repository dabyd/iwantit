<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\WowDatasetImporter;
use Illuminate\Console\Command;

class ImportWowDataset extends Command
{
    protected $signature = 'wow:import
                            {project : ID o demo_code del proyecto}
                            {--dir=database/seeders/data/wow : Directorio con los 4 CSVs de Alex}
                            {--reset : Elimina los datos de análisis del proyecto antes de importar}';

    protected $description = 'Importa el dataset curado de Alex (scenes, elements, appearances, advertising_opportunities) en un proyecto';

    public function handle(WowDatasetImporter $importer): int
    {
        $project = $this->resolveProject((string) $this->argument('project'));
        $dir = (string) $this->option('dir');

        $files = [
            'scenes' => "{$dir}/01_scenes.csv",
            'elements' => "{$dir}/02_elements.csv",
            'appearances' => "{$dir}/03_appearances.csv",
            'opportunities' => "{$dir}/04_advertising_opportunities.csv",
        ];

        foreach ($files as $label => $path) {
            if (! is_file($path)) {
                $this->error("Falta el CSV ({$label}): {$path}");

                return self::FAILURE;
            }
        }

        $this->info("Importando dataset en proyecto #{$project->id} ({$project->name})…");

        try {
            if ($this->option('reset')) {
                $importer->reset($project->id);
                $this->warn('Datos de análisis previos del proyecto eliminados.');
            }

            $counts = $importer->import($project->id, $files, (int) ($this->laravel['auth']?->id() ?? 0) ?: null);
        } catch (\Throwable $e) {
            $this->error('Importación fallida: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->table(
            ['Tabla', 'Importados'],
            collect($counts)->map(fn ($count, $label) => [$label, $count])->values()->toArray()
        );

        $this->info('Importación completada.');

        return self::SUCCESS;
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
