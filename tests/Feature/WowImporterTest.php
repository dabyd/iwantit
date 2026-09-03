<?php

namespace Tests\Feature;

use App\Models\AdvertisingOpportunity;
use App\Models\Appearance;
use App\Models\InventoryItem;
use App\Models\Project;
use App\Models\Scene;
use App\Services\WowDatasetImporter;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WowImporterTest extends TestCase
{
    use DatabaseTransactions;

    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = sys_get_temp_dir().'/wow-import-'.uniqid();
        mkdir($this->dir, 0777, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir.'/*') ?: [] as $file) {
            unlink($file);
        }
        @rmdir($this->dir);
        parent::tearDown();
    }

    private function writeCsv(string $name, string $content): void
    {
        file_put_contents($this->dir.'/'.$name, $content);
    }

    private function files(): array
    {
        return [
            'scenes' => $this->dir.'/01_scenes.csv',
            'elements' => $this->dir.'/02_elements.csv',
            'appearances' => $this->dir.'/03_appearances.csv',
            'opportunities' => $this->dir.'/04_advertising_opportunities.csv',
        ];
    }

    public function test_imports_full_dataset_with_timecodes_brands_and_taxons(): void
    {
        $project = Project::create(['name' => 'WOW test', 'demo_code' => 'wow-'.uniqid()]);

        $this->writeCsv('01_scenes.csv', "scene_id_ref,position,name,start_time,end_time,key_contexts,notes\nS01,1,Oficina,00:00:14.180,00:01:30.000,\"Fashion, Luxury\",\n");
        $this->writeCsv('02_elements.csv', "element_id_ref,name,type,brand,notes\nE01,Taza,product,CafeBrand,\n");
        $this->writeCsv('03_appearances.csv', "appearance_id_ref,element_id_ref,scene_id_ref,start_time,end_time,pos_x,pos_y,w,h,source,relevant_for,notes\nA01,E01,S01,00:00:20.500,00:00:28.000,50,60,10,8,manual,\"Advertising\",\n");
        $this->writeCsv('04_advertising_opportunities.csv', "opportunity_id_ref,scene_id_ref,appearance_id_ref,value_level,elements_involved,contexts,rationale\nOP01,S01,A01,high,\"E01\",\"Food & Beverage\",producto protagonista\n");

        $counts = (new WowDatasetImporter)->import($project->id, $this->files());

        $this->assertSame(['scenes' => 1, 'elements' => 1, 'appearances' => 1, 'opportunities' => 1], $counts);

        $scene = Scene::where('project_id', $project->id)->firstOrFail();
        $this->assertSame(14180, (int) $scene->start_time);
        $this->assertSame(90000, (int) $scene->end_time);
        $this->assertSame(['Fashion', 'Luxury'], $scene->taxons->pluck('name')->sort()->values()->all());

        $item = InventoryItem::where('project_id', $project->id)->firstOrFail();
        $this->assertSame('product', $item->type->value);
        $this->assertSame('CafeBrand', $item->brand->name);

        $appearance = Appearance::where('inventory_item_id', $item->id)->firstOrFail();
        $this->assertSame('manual', $appearance->source->value);
        $this->assertSame('advertising', $appearance->relevances->first()->vertical->value);

        $opportunity = AdvertisingOpportunity::where('project_id', $project->id)->firstOrFail();
        $this->assertSame('high', $opportunity->value_level->value);
        $this->assertSame(['Taza'], $opportunity->elements->pluck('name')->all());
    }

    public function test_skips_example_rows(): void
    {
        $project = Project::create(['name' => 'WOW test', 'demo_code' => 'wow-'.uniqid()]);

        $this->writeCsv('01_scenes.csv', "scene_id_ref,position,name,start_time,end_time,key_contexts,notes\nS01,1,Real,00:00:10.000,00:00:20.000,\"\",\nS02,2,\"EJEMPLO - borrar\",00:00:20.000,00:00:30.000,\"\",\"EJEMPLO - fila de ejemplo\"\n");
        $this->writeCsv('02_elements.csv', "element_id_ref,name,type,brand,notes\n");
        $this->writeCsv('03_appearances.csv', "appearance_id_ref,element_id_ref,scene_id_ref,start_time,end_time,pos_x,pos_y,w,h,source,relevant_for,notes\n");
        $this->writeCsv('04_advertising_opportunities.csv', "opportunity_id_ref,scene_id_ref,appearance_id_ref,value_level,elements_involved,contexts,rationale\n");

        $counts = (new WowDatasetImporter)->import($project->id, $this->files());

        $this->assertSame(1, $counts['scenes']);
        $this->assertSame(1, Scene::where('project_id', $project->id)->count());
    }

    public function test_reset_removes_analysis_data(): void
    {
        $project = Project::create(['name' => 'WOW test', 'demo_code' => 'wow-'.uniqid()]);

        $this->writeCsv('01_scenes.csv', "scene_id_ref,position,name,start_time,end_time,key_contexts,notes\nS01,1,Oficina,00:00:14.180,00:01:30.000,\"Fashion\",\n");
        $this->writeCsv('02_elements.csv', "element_id_ref,name,type,brand,notes\nE01,Taza,product,,\n");
        $this->writeCsv('03_appearances.csv', "appearance_id_ref,element_id_ref,scene_id_ref,start_time,end_time,pos_x,pos_y,w,h,source,relevant_for,notes\nA01,E01,S01,00:00:20.000,00:00:28.000,50,60,10,8,manual,\"Advertising\",\n");
        $this->writeCsv('04_advertising_opportunities.csv', "opportunity_id_ref,scene_id_ref,appearance_id_ref,value_level,elements_involved,contexts,rationale\nOP01,S01,A01,high,\"E01\",\"Fashion\",x\n");

        $importer = new WowDatasetImporter;
        $importer->import($project->id, $this->files());

        $this->assertSame(1, Scene::where('project_id', $project->id)->count());

        $importer->reset($project->id);

        $this->assertSame(0, Scene::where('project_id', $project->id)->count());
        $this->assertSame(0, InventoryItem::where('project_id', $project->id)->count());
        $this->assertSame(0, AdvertisingOpportunity::where('project_id', $project->id)->count());
        $this->assertSame(0, Appearance::whereHas('scene', fn ($q) => $q->where('project_id', $project->id))->count());
    }
}
