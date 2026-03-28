<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class AdminWebsiteAccountApiController extends Controller
{
    /**
     * List website donor accounts
     */
    public function index()
    {
        $accounts = User::where('role', 'donor')
            ->latest()
            ->paginate(20);
            
        return response()->json($accounts);
    }

    /**
     * Create a new website donor account (Admin registration)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'role' => 'donor',
            'active' => true,
            'email' => $data['email'] ?? ($data['phone'] . '@anasen.charity'),
            'password' => Hash::make(Str::random(16))
        ]);

        return response()->json([
            'message' => 'Account created successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Remove a website donor account
     */
    public function destroy(User $user)
    {
        if ($user->role !== 'donor') {
            return response()->json(['message' => 'Cannot delete non-donor accounts from here'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    }
}
