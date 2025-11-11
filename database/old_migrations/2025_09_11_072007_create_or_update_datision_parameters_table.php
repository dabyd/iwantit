<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        if (!Schema::hasTable('datision_parameters')) {
            // Crear la tabla si no existe
            Schema::create('datision_parameters', function (Blueprint $table) {
                $table->id(); // bigint unsigned NOT NULL AUTO_INCREMENT
                $table->string('machine_url', 2048);
                $table->unsignedInteger('threshold_sec');
                $table->integer('frames')->nullable();
                $table->integer('x1')->nullable();
                $table->integer('y1')->nullable();
                $table->float('low_price')->nullable();
                $table->float('medium_price')->nullable();
                $table->float('high_price')->nullable();
                $table->float('extra_price')->nullable();
                $table->timestamps();
                
                // Solo índice para threshold_sec (machine_url es demasiado largo)
                $table->index('threshold_sec');
            });
            
            // Añadir índice parcial para machine_url después de crear la tabla
            DB::statement('ALTER TABLE `datision_parameters` ADD INDEX `datision_parameters_machine_url_index` (`machine_url`(255))');
            
        } else {
            // Modificar la tabla si ya existe - añadir columnas faltantes
            $columns = Schema::getColumnListing('datision_parameters');
            
            Schema::table('datision_parameters', function (Blueprint $table) use ($columns) {
                if (!in_array('machine_url', $columns)) {
                    $table->string('machine_url', 2048)->after('id');
                }
                if (!in_array('threshold_sec', $columns)) {
                    $table->unsignedInteger('threshold_sec')->after('machine_url');
                }
                if (!in_array('frames', $columns)) {
                    $table->integer('frames')->nullable()->after('threshold_sec');
                }
                if (!in_array('x1', $columns)) {
                    $table->integer('x1')->nullable()->after('frames');
                }
                if (!in_array('y1', $columns)) {
                    $table->integer('y1')->nullable()->after('x1');
                }
                if (!in_array('low_price', $columns)) {
                    $table->float('low_price')->nullable()->after('y1');
                }
                if (!in_array('medium_price', $columns)) {
                    $table->float('medium_price')->nullable()->after('low_price');
                }
                if (!in_array('high_price', $columns)) {
                    $table->float('high_price')->nullable()->after('medium_price');
                }
                if (!in_array('extra_price', $columns)) {
                    $table->float('extra_price')->nullable()->after('high_price');
                }
                if (!in_array('created_at', $columns)) {
                    $table->timestamps();
                }
            });
            
            // Verificar y añadir índices
            $this->addMachineUrlIndexIfNotExists();
            $this->addIndexIfNotExists('datision_parameters', 'threshold_sec');
        }
    }

    /**
     * Reversar las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('datision_parameters');
    }

    /**
     * Añadir índice parcial para machine_url si no existe
     */
    private function addMachineUrlIndexIfNotExists()
    {
        $indexName = 'datision_parameters_machine_url_index';
        
        // Verificar si el índice existe
        $indexExists = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.statistics 
            WHERE table_schema = DATABASE() 
            AND table_name = 'datision_parameters' 
            AND index_name = ?
        ", [$indexName]);
        
        if ($indexExists[0]->count == 0) {
            // Crear índice parcial para machine_url (primeros 255 caracteres)
            DB::statement("ALTER TABLE `datision_parameters` ADD INDEX `{$indexName}` (`machine_url`(255))");
        }
    }

    /**
     * Añadir índice si no existe (para campos normales)
     */
    private function addIndexIfNotExists($table, $column)
    {
        $indexName = $table . '_' . $column . '_index';
        
        // Verificar si el índice existe
        $indexExists = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.statistics 
            WHERE table_schema = DATABASE() 
            AND table_name = ? 
            AND index_name = ?
        ", [$table, $indexName]);
        
        if ($indexExists[0]->count == 0) {
            DB::statement("ALTER TABLE `{$table}` ADD INDEX `{$indexName}` (`{$column}`)");
        }
    }
};