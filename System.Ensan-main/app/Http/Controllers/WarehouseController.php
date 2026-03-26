<?php
namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index() { return Warehouse::paginate(20); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'location' => 'nullable|string'
        ]);
        return Warehouse::create($data);
    }
    public function show(Warehouse $warehouse) { return $warehouse; }
    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'location' => 'nullable|string'
        ]);
        $warehouse->update($data);
        return $warehouse;
    }
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return response()->noContent();
    }
}
