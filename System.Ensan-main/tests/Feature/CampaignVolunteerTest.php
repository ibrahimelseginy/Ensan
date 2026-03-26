<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignVolunteerTest extends TestCase
{
    // We don't use RefreshDatabase here because we want to test against the actual dev DB structure 
    // but we will wrap in transaction if possible, or just cleanup.
    // Actually, using RefreshDatabase is safer but might wipe data if not configured to use sqlite in memory.
    // Let's assume we can just create and delete.

    public function test_campaign_relationships()
    {
        $user = User::factory()->create();
        $project = Project::create(['name' => 'Test Project', 'status' => 'active']);
        $campaign = Campaign::create([
            'name' => 'Test Campaign',
            'season_year' => 2025,
            'status' => 'active',
            'project_id' => $project->id,
            'manager_user_id' => $user->id
        ]);

        // Test manager relationship
        $this->assertEquals($user->id, $campaign->manager->id);

        // Test volunteer relationship
        $volunteer = User::factory()->create();
        $campaign->volunteers()->attach($volunteer, ['role' => 'helper', 'hours' => 10]);

        $this->assertTrue($campaign->volunteers->contains($volunteer));
        $this->assertEquals('helper', $campaign->volunteers->first()->pivot->role);

        // Test monthly volunteer relationship
        $campaign->monthlyVolunteers()->create([
            'user_id' => $volunteer->id,
            'month' => 1,
            'year' => 2025,
            'notes' => 'Great work'
        ]);

        $this->assertEquals(1, $campaign->monthlyVolunteers()->count());
        $this->assertEquals('Great work', $campaign->monthlyVolunteers->first()->notes);

        // Cleanup
        $campaign->monthlyVolunteers()->delete();
        $campaign->volunteers()->detach();
        $campaign->delete();
        $project->delete();
        $user->delete();
        $volunteer->delete();
    }
}
