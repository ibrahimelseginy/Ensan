<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\TantaWorker;
use Illuminate\Http\Request;

final class TantaWorkerWebController extends Controller
{
    public function index()
    {
        $workers = TantaWorker::latest()->paginate(50);
        return view('tanta_workers.index', compact('workers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'profession' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        TantaWorker::create($validated);

        return redirect()->route('tanta-workers.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function update(Request $request, TantaWorker $tanta_worker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'profession' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $tanta_worker->update($validated);

        return redirect()->route('tanta-workers.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy(TantaWorker $tanta_worker)
    {
        $tanta_worker->delete();

        return redirect()->route('tanta-workers.index')->with('success', 'تم الحذف بنجاح');
    }
}
