<?php

namespace App\Http\Controllers;

use App\Models\KafrElSheikhBroker;
use Illuminate\Http\Request;

class KafrElSheikhBrokerWebController extends Controller
{
    public function index()
    {
        $brokers = KafrElSheikhBroker::latest()->paginate(50);
        return view('kafr_el_sheikh_brokers.index', compact('brokers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        KafrElSheikhBroker::create($validated);

        return redirect()->route('kafr-el-sheikh-brokers.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function update(Request $request, KafrElSheikhBroker $kafr_el_sheikh_broker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $kafr_el_sheikh_broker->update($validated);

        return redirect()->route('kafr-el-sheikh-brokers.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy(KafrElSheikhBroker $kafr_el_sheikh_broker)
    {
        $kafr_el_sheikh_broker->delete();

        return redirect()->route('kafr-el-sheikh-brokers.index')->with('success', 'تم الحذف بنجاح');
    }
}
