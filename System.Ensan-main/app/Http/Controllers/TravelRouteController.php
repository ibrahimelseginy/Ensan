<?php
namespace App\Http\Controllers;

use App\Models\TravelRoute;
use Illuminate\Http\Request;

class TravelRouteController extends Controller
{
    public function index() { return TravelRoute::paginate(50); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string'
        ]);
        return TravelRoute::create($data);
    }
    public function show(TravelRoute $travel_route) { return $travel_route; }
    public function update(Request $request, TravelRoute $travel_route)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'description' => 'nullable|string'
        ]);
        $travel_route->update($data);
        return $travel_route;
    }
    public function destroy(TravelRoute $travel_route)
    {
        $travel_route->delete();
        return response()->noContent();
    }
}
