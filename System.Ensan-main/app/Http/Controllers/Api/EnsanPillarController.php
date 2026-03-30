<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EnsanPillar;
use Illuminate\Http\Request;

class EnsanPillarController extends Controller
{
    /**
     * Get all active integrated service pillars for home grid
     */
    public function index()
    {
        $pillars = EnsanPillar::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($pillar) {
                return [
                    'id' => $pillar->id,
                    'title' => $pillar->title,
                    'slug' => $pillar->slug,
                    'icon_url' => $pillar->icon_url_attribute,
                    'sort_order' => $pillar->sort_order,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $pillars
        ]);
    }

    /**
     * Get details for a specific pillar
     */
    public function show($slug)
    {
        $pillar = EnsanPillar::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$pillar) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service pillar not found'
            ], 404);
        }

        // Logic to link related projects (can be based on tags or category)
        $relatedProjects = \App\Models\Project::where('show_on_mobile', true)
            ->where('name', 'like', '%' . $pillar->title . '%') // Simple heuristic
            ->get()
            ->map(function ($project) {
                $project->image_url = $project->image_path ? $project->getFileUrl('image_path') : null;
                return $project;
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $pillar->id,
                'title' => $pillar->title,
                'description' => $pillar->description,
                'cover_url' => $pillar->cover_url_attribute,
                'projects' => $relatedProjects,
            ]
        ]);
    }
}
