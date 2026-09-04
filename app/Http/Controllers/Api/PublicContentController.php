<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Project;
use Illuminate\Http\Request;

final class PublicContentController extends Controller
{
    public function campaigns()
    {
        return Campaign::where('status', 'active')
            ->get(['id', 'name', 'season_title', 'image_path', 'goal_amount', 'current_amount', 'status']);
    }

    public function campaignShow($id)
    {
        $campaign = Campaign::withCount('donations')->findOrFail($id);
        return response()->json([
            'id' => $campaign->id,
            'title' => $campaign->name,
            'description' => $campaign->website_content,
            'image' => $campaign->image_url,
            'target_amount' => $campaign->goal_amount,
            'raised_amount' => $campaign->current_amount,
            'donors_count' => $campaign->donations_count,
            'percentage_completed' => $campaign->progress_percentage,
            'status' => $campaign->status
        ]);
    }

    public function projects()
    {
        return Project::where('status', 'active')
            ->get(['id', 'name', 'image_path', 'status']);
    }

    public function projectShow($id)
    {
        $project = Project::withCount('donations')->findOrFail($id);
        return response()->json([
            'id' => $project->id,
            'title' => $project->name,
            'description' => $project->description,
            'image' => $project->image_url,
            'status' => $project->status,
            'donors_count' => $project->donations_count
        ]);
    }

    public function categories()
    {
        $categories = \App\Models\DonationCategory::with(['activeItems'])
            ->active()
            ->orderBy('sort_order')
            ->get()
            ->map(function ($cat) {
                return [
                    'id'    => $cat->id,
                    'name'  => $cat->name,
                    'slug'  => $cat->slug,
                    'items' => $cat->activeItems->map(fn($item) => [
                        'id'          => $item->id,
                        'title'       => $item->title,
                        'description' => $item->description,
                        'icon'        => $item->icon_url,
                        'image'       => $item->image_url,
                        'sort_order'  => $item->sort_order,
                        'bg_style'    => $item->bg_style,
                    ]),
                ];
            });

        return response()->json($categories);
    }
}
