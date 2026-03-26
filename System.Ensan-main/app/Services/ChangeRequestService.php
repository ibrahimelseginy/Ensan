<?php
namespace App\Services;

use App\Models\ChangeRequest;
use Illuminate\Support\Facades\Auth;

class ChangeRequestService
{
    /**
     * Handle a create/update/delete request.
     * If user is admin (or forced), executes immediately.
     * Otherwise, stores a pending ChangeRequest.
     *
     * @param string $modelType Class name (e.g. App\Models\Donation)
     * @param int|null $modelId Null for create, ID for update/delete
     * @param string $action 'create', 'update', 'delete'
     * @param array $payload Data for create/update
     * @param callable $executor Logic to execute if approved immediately
     * @return mixed Result of execution or the ChangeRequest instance
     */
    public static function handleRequest(string $modelType, ?int $modelId, string $action, array $payload, callable $executor, bool $forceRequest = false)
    {
        $user = request()->user();

        // If forceRequest is true, we MUST create a request even for admins
        // Otherwise, if user has enough roles (ADMIN ONLY), we can auto-approve
        $canAutoApprove = !$forceRequest && ($user && $user->hasRole('admin'));

        if ($canAutoApprove) {
            // Execute the action
            $result = $executor();

            // LOG IT by creating an approved ChangeRequest
            if ($user) {
                \App\Models\ChangeRequest::create([
                    'user_id' => $user->id,
                    'model_type' => $modelType,
                    'model_id' => $modelId ?? ($result->id ?? null),
                    'action' => $action,
                    'payload' => $payload,
                    'status' => 'approved',
                    'reviewer_id' => $user->id
                ]);
            }

            return $result;
        }

        // If action is update, try to compute diff
        if ($action === 'update' && $modelId) {
            try {
                $instance = $modelType::find($modelId);
                if ($instance) {
                    $diff = [];
                    foreach ($payload as $key => $newVal) {
                        // Skip if key is not a model attribute or is an array (complex json not diffed deeply for now)
                        if (is_array($newVal)) continue;

                        $oldVal = $instance->getAttribute($key);
                        // Loose comparison
                        if ($oldVal != $newVal) {
                             $diff[$key] = [
                                 'from' => $oldVal,
                                 'to' => $newVal
                             ];
                        }
                    }
                    // Wrap payload
                    if (!empty($diff)) {
                        $payload = [
                            'data' => $payload,
                            'diff' => $diff,
                            '__is_wrapped' => true
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Fail silently on diff generation
            }
        }

        // Otherwise create a change request
        $cr = ChangeRequest::create([
            'user_id' => $user ? $user->id : null,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'action' => $action,
            'payload' => $payload,
            'status' => 'pending'
        ]);

        return $cr;
    }
}
