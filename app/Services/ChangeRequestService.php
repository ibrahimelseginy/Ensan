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

        // Admin and manager roles auto-approve and execute immediately (isolated from HR users.edit)
        $isAdminOrManager = $user && (
            $user->hasRole(['admin', 'super_admin', 'manager', 'مدير المؤسسة بالكامل', 'إدارة']) ||
            $user->hasPermission('change_requests.review')
        );

        $isFinancialModel = in_array($modelType, [
            \App\Models\FinancialClosure::class,
            \App\Models\JournalEntry::class,
            \App\Models\Expense::class,
            \App\Models\Account::class,
            \App\Models\TreasuryTransaction::class,
        ], true);

        $isAccountant = $user && (
            $user->hasRole(['fr', 'حسابات ماليه', 'حسابات', 'accountant', 'finance']) ||
            $user->hasPermission('manage_finance') ||
            $user->hasPermission('financial_closures.approve') ||
            $user->hasPermission('تعديل واعتماد الإغلاقات المالية')
        );

        $canAutoApprove = $isAdminOrManager || ($isAccountant && $isFinancialModel) || !$forceRequest;

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
                        'reviewer_id' => $user->id,
                        'reviewed_at' => now(),
                        'review_notes' => 'تم التنفيذ الفوري للمدير/المسؤول'
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
                            if ($key === 'family_members' && method_exists($instance, 'familyMembers')) {
                                $existingMembers = $instance->familyMembers->map(function ($m) {
                                    return [
                                        'id' => (string) $m->id,
                                        'full_name' => trim((string) $m->full_name),
                                        'relationship' => $m->relationship ?? 'child',
                                        'age' => $m->age !== null ? (string) $m->age : null,
                                        'birth_date' => optional($m->birth_date)->format('Y-m-d'),
                                        'code' => $m->code,
                                        'sponsorship_amount' => $m->sponsorship_amount ? (string) $m->sponsorship_amount : null,
                                        'case_details' => $m->case_details,
                                        'education_level' => $m->education_level,
                                    ];
                                })->values()->all();

                                $cleanSubmitted = collect($newVal)->filter(function ($m) {
                                    return is_array($m) && !empty(trim((string) ($m['full_name'] ?? '')));
                                })->map(function ($m) {
                                    return [
                                        'id' => !empty($m['id']) ? (string) $m['id'] : null,
                                        'full_name' => trim((string) ($m['full_name'] ?? '')),
                                        'relationship' => $m['relationship'] ?? 'child',
                                        'age' => isset($m['age']) && $m['age'] !== '' ? (string) $m['age'] : null,
                                        'birth_date' => !empty($m['birth_date']) ? (string) $m['birth_date'] : null,
                                        'code' => !empty($m['code']) ? (string) $m['code'] : null,
                                        'sponsorship_amount' => isset($m['sponsorship_amount']) && $m['sponsorship_amount'] !== '' ? (string) $m['sponsorship_amount'] : null,
                                        'case_details' => !empty($m['case_details']) ? (string) $m['case_details'] : null,
                                        'education_level' => !empty($m['education_level']) ? (string) $m['education_level'] : null,
                                    ];
                                })->values()->all();

                                if ($existingMembers != $cleanSubmitted) {
                                    $diff[$key] = [
                                        'from' => $existingMembers,
                                        'to' => $cleanSubmitted,
                                    ];
                                }
                                continue;
                            }

                            $oldVal = match ($key) {
                                'allocated_beneficiary_ids' => method_exists($instance, 'allocatedBeneficiaries')
                                    ? $instance->allocatedBeneficiaries()->pluck('beneficiaries.id')->map(fn ($id) => (int) $id)->all()
                                    : [],
                                'sponsored_beneficiary_ids', 'direct_sponsored_beneficiary_ids' => method_exists($instance, 'sponsoredBeneficiaries')
                                    ? $instance->sponsoredBeneficiaries()->pluck('beneficiaries.id')->map(fn ($id) => (int) $id)->all()
                                    : [],
                                'sponsored_family_member_ids' => method_exists($instance, 'sponsoredFamilyMembers')
                                    ? $instance->sponsoredFamilyMembers()->pluck('beneficiary_family_members.id')->map(fn ($id) => (int) $id)->all()
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

                        if (in_array($key, [
                            '_token', '_method', 'password', 'password_confirmation',
                            'sync_sponsored_beneficiaries', 'sync_sponsored_family_members',
                            'edit_action', 'form_context', 'updated_by', 'created_by'
                        ], true)) {
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
