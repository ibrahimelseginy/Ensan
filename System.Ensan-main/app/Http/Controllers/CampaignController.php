<?php
namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index() { return Campaign::paginate(20); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'season_year' => 'required|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'in:active,archived'
        ]);
        return Campaign::create($data);
    }
    public function show(Campaign $campaign) { return $campaign; }
    public function update(Request $request, Campaign $campaign)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'season_year' => 'sometimes|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'in:active,archived'
        ]);
        $campaign->update($data);
        return $campaign;
    }
    public function destroy(Campaign $campaign)
    {
        $campaign->delete();
        return response()->noContent();
    }
}
