<?php

namespace App\Http\Controllers;

use App\Models\KafrElSheikhService;
use Illuminate\Http\Request;

class KafrElSheikhServiceWebController extends Controller
{
    public function index()
    {
        $services = KafrElSheikhService::latest()->paginate(50);
        return view('kafr_el_sheikh_services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_type' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        KafrElSheikhService::create($validated);

        return redirect()->route('kafr-el-sheikh-services.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function update(Request $request, KafrElSheikhService $kafr_el_sheikh_service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_type' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $kafr_el_sheikh_service->update($validated);

        return redirect()->route('kafr-el-sheikh-services.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy(KafrElSheikhService $kafr_el_sheikh_service)
    {
        $kafr_el_sheikh_service->delete();

        return redirect()->route('kafr-el-sheikh-services.index')->with('success', 'تم الحذف بنجاح');
    }
}
