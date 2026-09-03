<?php

namespace Tests\Feature;

use App\Models\AdvertisingOpportunity;
use App\Models\Appearance;
use App\Models\InventoryItem;
use App\Models\Project;
use App\Models\Scene;
use App\Services\WowAnalysisService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WowAnalysisServiceTest extends TestCase
{
    use DatabaseTransactions;

    private function makeProject(): Project
    {
        return Project::create(['name' => 'WOW test', 'demo_code' => 'wow-'.uniqid()]);
    }

    public function test_overview_returns_derived_counts(): void
    {
        $project = $this->makeProject();
        $scene = Scene::create(['project_id' => $project->id, 'position' => 1, 'name' => 'S', 'start_time' => 0, 'end_time' => 40000]);
        $item = InventoryItem::create(['project_id' => $project->id, 'name' => 'Cafe', 'type' => 'product']);

        Appearance::create(['inventory_item_id' => $item->id, 'scene_id' => $scene->id, 'start_time' => 0, 'end_time' => 10000, 'source' => 'manual']);

        $opportunity = AdvertisingOpportunity::create(['project_id' => $project->id, 'scene_id' => $scene->id, 'value_level' => 'high', 'rationale' => 'x']);
        $opportunity->elements()->attach($item->id);

        $overview = (new WowAnalysisService)->overview($project);

        $this->assertSame(1, $overview['content_intelligence']['scenes']);
        $this->assertSame(1, $overview['content_intelligence']['elements']);
        $this->assertSame(1, $overview['content_intelligence']['appearances']);
        $this->assertSame(0, $overview['content_intelligence']['relationships']);
        $this->assertSame(1, $overview['business_opportunities']['advertising']['high']);
        $this->assertSame(0, $overview['business_opportunities']['advertising']['medium']);
        $this->assertSame(0, $overview['business_opportunities']['advertising']['low']);
    }

    public function test_time_on_screen_uses_interval_union_without_double_counting(): void
    {
        $project = $this->makeProject();
        $scene = Scene::create(['project_id' => $project->id, 'position' => 1, 'name' => 'S', 'start_time' => 0, 'end_time' => 40000]);
        $item = InventoryItem::create(['project_id' => $project->id, 'name' => 'Cafe', 'type' => 'product']);

        // [0,10s], [5,15s], [20,30s] -> unión = 15s + 10s = 25s = 25000ms
        foreach ([[0, 10000], [5000, 15000], [20000, 30000]] as [$start, $end]) {
            Appearance::create(['inventory_item_id' => $item->id, 'scene_id' => $scene->id, 'start_time' => $start, 'end_time' => $end, 'source' => 'manual']);
        }

        $opportunity = AdvertisingOpportunity::create(['project_id' => $project->id, 'scene_id' => $scene->id, 'value_level' => 'high', 'rationale' => 'x']);
        $opportunity->elements()->attach($item->id);

        $items = (new WowAnalysisService)->advertisingOpportunities($project)['items'];

        $this->assertSame(25000, $items[0]['elements'][0]['time_on_screen_ms']);
        $this->assertSame(0, $items[0]['start_ms']);
        $this->assertSame(40000, $items[0]['end_ms']);
        $this->assertSame(40000, $items[0]['duration_ms']);
    }

    public function test_advertising_opportunities_filters_by_level(): void
    {
        $project = $this->makeProject();
        $scene = Scene::create(['project_id' => $project->id, 'position' => 1, 'name' => 'S', 'start_time' => 0, 'end_time' => 40000]);

        AdvertisingOpportunity::create(['project_id' => $project->id, 'scene_id' => $scene->id, 'value_level' => 'high', 'rationale' => 'x']);
        AdvertisingOpportunity::create(['project_id' => $project->id, 'scene_id' => $scene->id, 'value_level' => 'low', 'rationale' => 'x']);

        $service = new WowAnalysisService;

        $this->assertCount(2, $service->advertisingOpportunities($project)['items']);
        $this->assertCount(1, $service->advertisingOpportunities($project, 'high')['items']);
        $this->assertSame('high', $service->advertisingOpportunities($project, 'high')['items'][0]['value_level']);
    }
}
