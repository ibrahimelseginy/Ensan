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
        $user = request()->user() ?? auth()->user();

        // If forceRequest is false, auto-approve for logged in users / admins
        $canAutoApprove = !$forceRequest;

        if ($canAutoApprove) {
            $result = $executor();

            if ($user) {
                try {
                    \App\Models\ChangeRequest::create([
                        'user_id' => $user->id,
                        'model_type' => $modelType,
                        'model_id' => $modelId ?? ($result->id ?? null),
                        'action' => $action,
                        'payload' => $payload,
                        'status' => 'approved',
                        'reviewer_id' => $user->id
                    ]);
                } catch (\Exception $e) {
                    // Ignore audit log error
                }
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
                        if (is_array($newVal)) {
                            $oldVal = match ($key) {
                                'allocated_beneficiary_ids' => method_exists($instance, 'allocatedBeneficiaries')
                                    ? $instance->allocatedBeneficiaries()->pluck('beneficiaries.id')->map(fn ($id) => (int) $id)->all()
                                    : [],
                                'sponsor_ids' => method_exists($instance, 'sponsors')
                                    ? $instance->sponsors()->pluck('donors.id')->map(fn ($id) => (int) $id)->all()
                                    : [],
                                'permissions' => method_exists($instance, 'permissions')
                                    ? $instance->permissions()->pluck('permissions.id')->map(fn ($id) => (int) $id)->all()
                                    : [],
                                default => null,
                            };

                            if ($oldVal !== null) {
                                $normaliseIds = function (array $ids): array {
                                    $ids = array_values(array_unique(array_map('intval', $ids)));
                                    sort($ids);
                                    return $ids;
                                };

                                if ($normaliseIds($oldVal) !== $normaliseIds($newVal)) {
                                    $diff[$key] = ['from' => $oldVal, 'to' => $newVal];
                                }
                            }

                            continue;
                        }

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

        if (request()->hasSession()) {
            request()->session()->flash('review_request_id', $cr->getKey());
        }

        return $cr;
    }
}
