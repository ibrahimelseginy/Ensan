<?php

namespace App\Http\Controllers\Api\Anasen;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * GET /api/users
     * List accounts with Name, Phone, and Registration Date
     */
    public function index()
    {
        try {
            $users = User::select('id', 'name', 'phone', 'created_at')
                        ->withCount('donations')
                        ->withSum('donations as total_donations', 'amount')
                        ->latest()
                        ->get();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to list users', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user list'
            ], 500);
        }
    }

    /**
     * GET /api/admin/users/{id}
     * Get donor file (details + donation history)
     */
    public function show($id)
    {
        try {
            $user = User::select('id', 'name', 'phone', 'email', 'created_at')
                        ->with(['donations' => function($q) {
                            $q->select([
                                'id', 'user_id', 'amount', 'category', 
                                'target_id', 'payment_method', 'status', 
                                'type', 'created_at'
                            ])->with(['proof', 'project:id,name', 'campaign:id,name'])->latest();
                        }])
                        ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve user details', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'User not found or error occurred'
            ], 404);
        }
    }

    /**
     * DELETE /api/admin/users/{id}
     * Delete user account
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Log the action before deletion
            Log::info('Deleting user account', [
                'user_id' => $id,
                'admin_id' => auth()->id()
            ]);

            // Note: This only deletes the User record. 
            // Donations linked to this user_id will have a null user_id if defined in migrations, 
            // or we might need to handle them here if data integrity is a concern.
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User account deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete user', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user'
            ], 500);
        }
    }
}
