<?php
namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index() { return Item::paginate(20); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'sku' => 'nullable|string',
            'name' => 'required|string',
            'unit' => 'nullable|string',
            'estimated_value' => 'nullable|numeric'
        ]);
        return Item::create($data);
    }
    public function show(Item $item) { return $item; }
    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'sku' => 'nullable|string',
            'name' => 'sometimes|string',
            'unit' => 'nullable|string',
            'estimated_value' => 'nullable|numeric'
        ]);
        $item->update($data);
        return $item;
    }
    public function destroy(Item $item)
    {
        $item->delete();
        return response()->noContent();
    }
}
