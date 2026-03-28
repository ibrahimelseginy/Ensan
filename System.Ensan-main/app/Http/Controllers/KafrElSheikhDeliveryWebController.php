<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\KafrElSheikhDelivery;
use Illuminate\Http\Request;

final class KafrElSheikhDeliveryWebController extends Controller
{
    public function index()
    {
        $deliveries = KafrElSheikhDelivery::latest()->paginate(50);
        return view('kafr_el_sheikh_deliveries.index', compact('deliveries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'vehicle_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        KafrElSheikhDelivery::create($validated);

        return redirect()->route('kafr-el-sheikh-deliveries.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function update(Request $request, KafrElSheikhDelivery $kafr_el_sheikh_delivery)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'vehicle_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $kafr_el_sheikh_delivery->update($validated);

        return redirect()->route('kafr-el-sheikh-deliveries.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy(KafrElSheikhDelivery $kafr_el_sheikh_delivery)
    {
        $kafr_el_sheikh_delivery->delete();

        return redirect()->route('kafr-el-sheikh-deliveries.index')->with('success', 'تم الحذف بنجاح');
    }
}
