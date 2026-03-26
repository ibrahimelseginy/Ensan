<?php
namespace App\Http\Controllers;

use App\Models\Delegate;
use Illuminate\Http\Request;

class DelegateController extends Controller
{
    public function index() { return Delegate::with('route')->paginate(20); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'route_id' => 'nullable|exists:travel_routes,id',
            'active' => 'boolean'
        ]);
        return Delegate::create($data);
    }
    public function show(Delegate $delegate) { return $delegate->load('route'); }
    public function update(Request $request, Delegate $delegate)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'route_id' => 'nullable|exists:travel_routes,id',
            'active' => 'boolean'
        ]);
        $delegate->update($data);
        return $delegate->load('route');
    }
    public function destroy(Delegate $delegate)
    {
        $delegate->delete();
        return response()->noContent();
    }
}
