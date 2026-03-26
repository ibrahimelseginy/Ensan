<?php

namespace App\Http\Controllers;

use App\Models\RamadanIftar;
use App\Models\Campaign;
use App\Models\Project;
use Illuminate\Http\Request;

class RamadanIftarWebController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');

        $iftars = RamadanIftar::with(['project', 'campaign'])
            ->when($q, function ($query) use ($q) {
            $query->where('beneficiary_name', 'like', "%$q%")
                ->orWhere('national_id', 'like', "%$q%")
                ->orWhere('guide_name', 'like', "%$q%")
                ->orWhere('guide_phone', 'like', "%$q%");
        })
            ->latest()
            ->paginate(50);

        // Calculate statistics for the summary table
        $rawStats = RamadanIftar::with('project')
            ->select('project_id', 'region', \Illuminate\Support\Facades\DB::raw('COUNT(*) as families_count'), \Illuminate\Support\Facades\DB::raw('SUM(meals_count) as meals_sum'))
            ->groupBy('project_id', 'region')
            ->get();

        $statistics = [];
        foreach ($rawStats as $stat) {
            $projectName = $stat->project ? $stat->project->name : 'بدون مشروع';
            $regionName = $stat->region ?: 'أخرى';
            $key = $projectName . '_' . $regionName;

            if (!isset($statistics[$key])) {
                $statistics[$key] = [
                    'project' => $projectName,
                    'region' => $regionName,
                    'families_count' => 0,
                    'items_count' => 0,
                ];
            }
            $statistics[$key]['families_count'] += $stat->families_count;
            $statistics[$key]['items_count'] += $stat->meals_sum;
        }

        return view('ramadan_iftars.index', compact('iftars', 'statistics'));
    }

    public function create()
    {
        $campaigns = Campaign::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        return view('ramadan_iftars.create', compact('campaigns', 'projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'beneficiary_name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'national_id' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'meals_count' => 'required|integer|min:1',
            'guide_name' => 'nullable|string|max:255',
            'guide_phone' => 'nullable|string|max:255',
            'guide_phone_2' => 'nullable|string|max:255',
            'delivery_method' => 'nullable|string|max:255',
            'delivery_cost' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
        ]);

        RamadanIftar::create($data);
        return redirect()->route('ramadan-iftars.index')->with('success', 'تم تسجيل بيانات الإفطار بنجاح.');
    }

    public function edit(RamadanIftar $ramadan_iftar)
    {
        $campaigns = Campaign::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        return view('ramadan_iftars.edit', compact('ramadan_iftar', 'campaigns', 'projects'));
    }

    public function update(Request $request, RamadanIftar $ramadan_iftar)
    {
        $data = $request->validate([
            'beneficiary_name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'national_id' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'meals_count' => 'required|integer|min:1',
            'guide_name' => 'nullable|string|max:255',
            'guide_phone' => 'nullable|string|max:255',
            'guide_phone_2' => 'nullable|string|max:255',
            'delivery_method' => 'nullable|string|max:255',
            'delivery_cost' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
        ]);

        $ramadan_iftar->update($data);
        return redirect()->route('ramadan-iftars.index')->with('success', 'تم التعديل بنجاح.');
    }

    public function destroy(RamadanIftar $ramadan_iftar)
    {
        $ramadan_iftar->delete();
        return back()->with('success', 'تم الحذف بنجاح.');
    }
}
