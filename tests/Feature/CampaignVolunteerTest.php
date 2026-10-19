<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\DatabaseTestCase;

class CampaignVolunteerTest extends DatabaseTestCase
{
    public function test_campaign_relationships()
    {
        $user = User::create(['name' => 'Test User', 'email' => \Illuminate\Support\Str::uuid().'@example.test', 'password' => bcrypt('test-password')]);
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
        $volunteer = User::create(['name' => 'Test User', 'email' => \Illuminate\Support\Str::uuid().'@example.test', 'password' => bcrypt('test-password')]);
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
