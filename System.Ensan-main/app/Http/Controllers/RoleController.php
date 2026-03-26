<?php
namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index() { return Role::paginate(50); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'key' => 'required|string|unique:roles,key'
        ]);
        return Role::create($data);
    }
    public function show(Role $role) { return $role; }
    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'key' => 'sometimes|string|unique:roles,key,' . $role->id
        ]);
        $role->update($data);
        return $role;
    }
    public function destroy(Role $role)
    {
        $role->delete();
        return response()->noContent();
    }
}
