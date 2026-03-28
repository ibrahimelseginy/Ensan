<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\RamadanBag;
use App\Models\Campaign;
use App\Models\Project;
use Illuminate\Http\Request;

final class RamadanBagWebController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $status = $request->input('status');

        $bags = RamadanBag::with(['project', 'campaign'])
            ->when($q, function ($query) use ($q) {
            $query->where('beneficiary_name', 'like', "%$q%")
                ->orWhere('national_id', 'like', "%$q%")
                ->orWhere('phone', 'like', "%$q%")
                ->orWhere('phone_2', 'like', "%$q%");
        })
            ->when($status, function ($query) use ($status) {
            $query->where('status', $status);
        })
            ->latest()
            ->paginate(50);

        // Calculate statistics for the summary table
        $rawStats = RamadanBag::with('project')
            ->select('project_id', 'region', \Illuminate\Support\Facades\DB::raw('COUNT(*) as families_count'), \Illuminate\Support\Facades\DB::raw('SUM(bags_count) as bags_sum'))
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
            $statistics[$key]['items_count'] += $stat->bags_sum ?: $stat->families_count;
        }

        return view('ramadan_bags.index', compact('bags', 'statistics'));
    }

    public function create()
    {
        $campaigns = Campaign::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        return view('ramadan_bags.create', compact('campaigns', 'projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'beneficiary_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'phone_2' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|max:255',
            'spouse_name' => 'nullable|string|max:255',
            'family_members' => 'nullable|integer',
            'case_conditions' => 'nullable|string',
            'region' => 'nullable|string|max:255',
            'bags_count' => 'nullable|integer',
            'address' => 'nullable|string',
            'bag_contents' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
        ]);

        RamadanBag::create($data);
        return redirect()->route('ramadan-bags.index')->with('success', 'تم تسجيل بيانات الشنطة بنجاح.');
    }

    public function edit(RamadanBag $ramadan_bag)
    {
        $campaigns = Campaign::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        return view('ramadan_bags.edit', compact('ramadan_bag', 'campaigns', 'projects'));
    }

    public function update(Request $request, RamadanBag $ramadan_bag)
    {
        $data = $request->validate([
            'beneficiary_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'phone_2' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|max:255',
            'spouse_name' => 'nullable|string|max:255',
            'family_members' => 'nullable|integer',
            'case_conditions' => 'nullable|string',
            'region' => 'nullable|string|max:255',
            'bags_count' => 'nullable|integer',
            'address' => 'nullable|string',
            'bag_contents' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
        ]);

        $ramadan_bag->update($data);
        return redirect()->route('ramadan-bags.index')->with('success', 'تم التعديل بنجاح.');
    }

    public function destroy(RamadanBag $ramadan_bag)
    {
        $ramadan_bag->delete();
        return back()->with('success', 'تم الحذف بنجاح.');
    }
}
