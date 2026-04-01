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
                    'description' => $pillar->description,
                    'icon_url' => $pillar->icon_url,
                    'cover_url' => $pillar->cover_url,
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
        $pillar = EnsanPillar::with(['projects', 'services'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$pillar) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service pillar not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $pillar->id,
                'title' => $pillar->title,
                'slug' => $pillar->slug,
                'description' => $pillar->description,
                'icon_url' => $pillar->icon_url,
                'cover_url' => $pillar->cover_url,
                'projects' => $pillar->projects->map(function ($proj) {
                    return [
                        'id' => $proj->id,
                        'name' => $proj->name,
                        'description' => $proj->short_description ?? $proj->description,
                        'image_url' => $proj->image_url,
                        'goal_amount' => $proj->goal_amount,
                        'current_amount' => $proj->current_amount,
                        'progress_percentage' => $proj->progress_percentage,
                    ];
                }),
                'services' => $pillar->services->map(function ($serv) {
                    return [
                        'id' => $serv->id,
                        'title' => $serv->title,
                        'image_url' => $serv->image_url,
                        'share_price' => $serv->share_price,
                    ];
                }),
            ]
        ]);
    }
}
