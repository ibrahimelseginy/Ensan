<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() { return User::paginate(50); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string',
            'is_employee' => 'boolean',
            'is_volunteer' => 'boolean',
            'active' => 'boolean'
        ]);
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }
    public function show(User $user) { return $user; }
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string',
            'is_employee' => 'boolean',
            'is_volunteer' => 'boolean',
            'active' => 'boolean'
        ]);
        if (isset($data['password'])) { $data['password'] = Hash::make($data['password']); }
        $user->update($data);
        return $user;
    }
    public function destroy(User $user)
    {
        $user->delete();
        return response()->noContent();
    }

    public function attachRole(User $user, \App\Models\Role $role)
    {
        $user->roles()->syncWithoutDetaching([$role->id]);
        return $user->load('roles');
    }

    public function detachRole(User $user, \App\Models\Role $role)
    {
        $user->roles()->detach($role->id);
        return $user->load('roles');
    }
}
