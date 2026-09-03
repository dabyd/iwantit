<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AnalysisEndpointTest extends TestCase
{
    use DatabaseTransactions;

    private function makeProject(): Project
    {
        return Project::create(['name' => 'WOW test', 'demo_code' => 'wow-'.uniqid()]);
    }

    private function ensurePermission(): void
    {
        Permission::findOrCreate('analysis-screen', 'web');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $project = $this->makeProject();

        $this->get("/projects/{$project->id}/analysis/overview")->assertRedirect('/login');
    }

    public function test_forbidden_without_capability(): void
    {
        $this->ensurePermission();
        $project = $this->makeProject();
        $user = User::factory()->create();
        $project->update(['users_id' => $user->id]);

        $this->actingAs($user)
            ->get("/projects/{$project->id}/analysis/overview")
            ->assertForbidden();
    }

    public function test_forbidden_without_project_access(): void
    {
        $this->ensurePermission();
        $project = $this->makeProject();
        $user = User::factory()->create();
        $user->givePermissionTo('analysis-screen');

        $this->actingAs($user)
            ->get("/projects/{$project->id}/analysis/overview")
            ->assertForbidden();
    }

    public function test_overview_returns_json_for_authorized_user(): void
    {
        $this->ensurePermission();
        $project = $this->makeProject();
        $user = User::factory()->create();
        $user->givePermissionTo('analysis-screen');
        $project->update(['users_id' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson("/projects/{$project->id}/analysis/overview");

        $response->assertOk()
            ->assertJsonStructure([
                'content_intelligence',
                'business_opportunities',
                'key_contexts',
            ]);
    }

    public function test_advertising_opportunities_rejects_invalid_level(): void
    {
        $this->ensurePermission();
        $project = $this->makeProject();
        $user = User::factory()->create();
        $user->givePermissionTo('analysis-screen');
        $project->update(['users_id' => $user->id]);

        $this->actingAs($user)
            ->getJson("/projects/{$project->id}/advertising-opportunities?level=bogus")
            ->assertStatus(422);
    }
}
