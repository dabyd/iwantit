<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Índices para optimizar las queries más frecuentes en la carga de proyectos.
     */
    public function up(): void
    {
        // hotpoints: consultados por versions_id + products_id + time
        if (!$this->indexExists('hotpoints', 'hotpoints_versions_products_time_index')) {
            Schema::table('hotpoints', function (Blueprint $table) {
                $table->index(['versions_id', 'products_id', 'time'], 'hotpoints_versions_products_time_index');
            });
        }

        // hotpoints_dates: consultados por project_id + product_id
        if (!$this->indexExists('hotpoints_dates', 'hotpoints_dates_project_product_index')) {
            Schema::table('hotpoints_dates', function (Blueprint $table) {
                $table->index(['project_id', 'product_id'], 'hotpoints_dates_project_product_index');
            });
        }

        // datision_results: consultados por datision_id + class
        if (!$this->indexExists('datision_results', 'datision_results_datision_class_index')) {
            Schema::table('datision_results', function (Blueprint $table) {
                $table->index(['datision_id', 'class'], 'datision_results_datision_class_index');
            });
        }

        // datision_detections: consultados por datision_result_id
        if (!$this->indexExists('datision_detections', 'datision_detections_result_id_index')) {
            Schema::table('datision_detections', function (Blueprint $table) {
                $table->index('datision_result_id', 'datision_detections_result_id_index');
            });
        }

        // licenses: consultados por versions_id
        if (!$this->indexExists('licenses', 'licenses_versions_id_index')) {
            Schema::table('licenses', function (Blueprint $table) {
                $table->index('versions_id', 'licenses_versions_id_index');
            });
        }

        // products_datision_objects_ia_classes: consultados por products_id
        if (!$this->indexExists('products_datision_objects_ia_classes', 'pdoiac_products_id_index')) {
            Schema::table('products_datision_objects_ia_classes', function (Blueprint $table) {
                $table->index('products_id', 'pdoiac_products_id_index');
            });
        }
    }

    public function down(): void
    {
        Schema::table('hotpoints', function (Blueprint $table) {
            $table->dropIndex('hotpoints_versions_products_time_index');
        });
        Schema::table('hotpoints_dates', function (Blueprint $table) {
            $table->dropIndex('hotpoints_dates_project_product_index');
        });
        Schema::table('datision_results', function (Blueprint $table) {
            $table->dropIndex('datision_results_datision_class_index');
        });
        Schema::table('datision_detections', function (Blueprint $table) {
            $table->dropIndex('datision_detections_result_id_index');
        });
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropIndex('licenses_versions_id_index');
        });
        Schema::table('products_datision_objects_ia_classes', function (Blueprint $table) {
            $table->dropIndex('pdoiac_products_id_index');
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }
};
