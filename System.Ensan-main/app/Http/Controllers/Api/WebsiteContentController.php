<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\WebPage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WebsiteContentController extends Controller
{
    // --- Projects ---
    public function indexProjects()
    {
        return response()->json(Project::latest()->get());
    }

    public function showProject(Project $project)
    {
        return response()->json($project);
    }

    public function storeProject(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
            'category' => 'nullable|string',
            'website_content' => 'nullable|string',
            'sponsorship_details' => 'nullable|string',
        ]);

        $project = Project::create($data);
        if ($request->hasFile('image')) {
            $project->uploadImage($request->file('image'), 'website/projects', 'image_path');
        }
        return response()->json($project, 201);
    }

    public function updateProject(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
            'category' => 'nullable|string',
            'website_content' => 'nullable|string',
            'sponsorship_details' => 'nullable|string',
        ]);

        $project->update($data);
        if ($request->hasFile('image')) {
            $project->uploadImage($request->file('image'), 'website/projects', 'image_path');
        }
        return response()->json($project);
    }

    public function destroyProject(Project $project)
    {
        $project->delete();
        return response()->json(['message' => 'Project deleted']);
    }

    // --- Campaigns ---
    public function indexCampaigns()
    {
        return response()->json(Campaign::latest()->get());
    }

    public function showCampaign(Campaign $campaign)
    {
        return response()->json($campaign);
    }

    public function storeCampaign(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
            'category' => 'nullable|string',
            'website_content' => 'nullable|string',
            'goal_amount' => 'nullable|numeric',
            'current_amount' => 'nullable|numeric',
        ]);

        $campaign = Campaign::create($data);
        if ($request->hasFile('image')) {
            $campaign->uploadImage($request->file('image'), 'website/campaigns', 'image_path');
        }
        return response()->json($campaign, 201);
    }

    public function updateCampaign(Request $request, Campaign $campaign)
    {
        $data = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
            'category' => 'nullable|string',
            'website_content' => 'nullable|string',
            'goal_amount' => 'nullable|numeric',
            'current_amount' => 'nullable|numeric',
        ]);

        $campaign->update($data);
        if ($request->hasFile('image')) {
            $campaign->uploadImage($request->file('image'), 'website/campaigns', 'image_path');
        }
        return response()->json($campaign);
    }

    public function destroyCampaign(Campaign $campaign)
    {
        $campaign->delete();
        return response()->json(['message' => 'Campaign deleted']);
    }

    // --- Web Pages ---
    public function indexPages()
    {
        return response()->json(WebPage::latest()->get());
    }

    public function showPage(WebPage $page)
    {
        return response()->json($page);
    }

    public function storePage(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
            'is_published' => 'boolean',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $page = WebPage::create($data);
        if ($request->hasFile('image')) {
            $page->uploadImage($request->file('image'), 'website/pages', 'image_path');
        }
        return response()->json($page, 201);
    }

    public function updatePage(Request $request, WebPage $page)
    {
        $data = $request->validate([
            'title' => 'sometimes|string',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
            'is_published' => 'boolean',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $page->update($data);
        if ($request->hasFile('image')) {
            $page->uploadImage($request->file('image'), 'website/pages', 'image_path');
        }
        return response()->json($page);
    }

    public function destroyPage(WebPage $page)
    {
        $page->delete();
        return response()->json(['message' => 'Page deleted']);
    }

    // --- Volunteer Wall ---
    public function getVolunteerWall()
    {
        $wall = \App\Models\WebVolunteerWall::orderBy('rank', 'desc')->get()->map(function ($v) {
            return [
            'id' => $v->id,
            'name' => $v->name,
            'hours' => $v->hours,
            'rank' => $v->rank,
            'image' => $v->image_path ? $v->getFileUrl('image_path') : null,
            ];
        });

        $boardMembers = \App\Models\WebBoardMember::orderBy('sort_order')->get()->map(function ($m) {
            return [
            'id' => $m->id,
            'name' => $m->name,
            'role' => $m->role,
            'description' => $m->description,
            'image' => $m->image_path ? $m->getFileUrl('image_path') : null,
            ];
        });

        $partners = \App\Models\WebPartner::all()->map(function ($p) {
            return [
            'id' => $p->id,
            'name' => $p->name,
            'type' => $p->type,
            'logo' => $p->logo_path ? $p->getFileUrl('logo_path') : null,
            'website' => $p->website_url,
            ];
        });

        return response()->json([
            'wall' => $wall,
            'board_members' => $boardMembers,
            'partners' => $partners
        ]);
    }

    // --- Partners ---
    public function getPartners()
    {
        return response()->json(\App\Models\WebPartner::all());
    }
}

