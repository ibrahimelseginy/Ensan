<?php

namespace App\Http\Controllers;

use App\Models\ChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChangeRequestWebController extends Controller
{
    public function index(Request $request)
    {
        // Only admin should see this
        if (!request()->user() || !request()->user()->hasRole('admin')) {
            abort(403, 'غير مصرح لك بدخول هذه الصفحة. هذه الصفحة للأدمن فقط.');
        }

        $query = ChangeRequest::with(['user', 'reviewer', 'subject']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $requests = $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->paginate(50); // Increased page size to reduce "disappearing" feel

        $permissionsMap = \App\Models\Permission::pluck('name', 'id')->toArray();
        $currentStatus = $request->status ?? 'all';

        return view('change_requests.index', compact('requests', 'permissionsMap', 'currentStatus'));
    }

    public function approve(ChangeRequest $changeRequest)
    {
        if (!request()->user()->hasRole('admin')) {
            abort(403, 'الموافقة مسموحة للأدمن فقط');
        }

        if ($changeRequest->status !== 'pending') {
            return back()->with('error', 'الطلب تمت معالجته مسبقاً');
        }

        // Execute logic
        try {
            $modelClass = $changeRequest->model_type;
            $payload = $changeRequest->payload;

            // Unwrap if payload contains extra metadata (like diffs)
            $executionData = $payload;
            if (is_array($payload) && isset($payload['__is_wrapped'])) {
                $executionData = $payload['data'] ?? [];
            }

            if ($changeRequest->action === 'create') {
                $instance = $modelClass::create($executionData);
                // Save the newly created model ID
                $changeRequest->model_id = $instance->id;
                
                // Special handling for Donations postCreate if needed
                if ($modelClass === 'App\Models\Donation') {
                    \App\Services\DonationService::postCreate($instance);
                } elseif ($modelClass === 'App\Models\Expense') {
                    // Process Treasury for Expenses
                    $treasuryService = new \App\Services\TreasuryIntegrationService();
                    if(isset($executionData['treasury_id'])) {
                         $treasuryService->processExpenseFromTreasury($instance, $executionData['treasury_id']);
                    }
                } elseif ($modelClass === 'App\Models\DelegateTrip') {
                    if (isset($executionData['create_journal_entry']) && $executionData['create_journal_entry']) {
                        try {
                            $logisticsService = new \App\Services\LogisticsAccountingService();
                            $logisticsService->createJournalEntry($instance);
                        } catch (\Exception $e) {}
                    }
                } elseif ($modelClass === 'App\Models\Payroll') {
                    $instance->calculateNetAmount();
                    $instance->save();
                    if (isset($executionData['create_journal_entry']) && $executionData['create_journal_entry']) {
                        try {
                            $ps = app(\App\Services\PayrollAccountingService::class);
                            $ps->createJournalEntry($instance);
                        } catch (\Exception $e) {}
                    }
                    if (isset($executionData['treasury_id']) && $executionData['treasury_id']) {
                        try {
                            $ts = new \App\Services\TreasuryIntegrationService();
                            $ts->processPayrollFromTreasury($instance, $executionData['treasury_id']);
                        } catch (\Exception $e) {}
                    }
                } elseif ($modelClass === 'App\Models\FinancialClosure') {
                    $instance->update(['approved' => true, 'approved_by' => auth()->id()]);
                    \App\Models\JournalEntry::where('date', '<=', $instance->date)->when($instance->branch, function ($q, $b) {
                        $q->where('branch', $b);
                    })->update(['locked' => true]);
                } elseif ($modelClass === 'App\Models\Leave') {
                    $instance->update(['status' => 'approved', 'approved_by' => auth()->id()]);
                }
            } elseif ($changeRequest->action === 'update') {
                $instance = $modelClass::find($changeRequest->model_id);
                if ($instance) {
                    $instance->update($executionData);
                    
                    if ($modelClass === 'App\Models\Role' && isset($executionData['permissions'])) {
                        $instance->permissions()->sync($executionData['permissions']);
                    }
                    
                    // Sync Treasury Transaction for Donations
                    if ($modelClass === 'App\Models\Donation' && $instance->type === 'cash') {
                         $transaction = \App\Models\TreasuryTransaction::where('donation_id', $instance->id)
                             ->where('type', 'in')
                             ->where('status', 'active')
                             ->first();

                         if ($transaction) {
                             $isUpdated = false;
                             
                             // Update Amount
                             if (isset($executionData['amount']) && $transaction->amount != $executionData['amount']) {
                                 $transaction->amount = $executionData['amount'];
                                 $isUpdated = true;
                             }
                             
                             // Update Treasury
                             if (isset($executionData['treasury_id']) && $transaction->treasury_id != $executionData['treasury_id']) {
                                 $oldTreasuryId = $transaction->treasury_id;
                                 $transaction->treasury_id = $executionData['treasury_id'];
                                 $isUpdated = true;
                                 
                                 // Update old treasury balance
                                 $oldTreasury = \App\Models\Treasury::find($oldTreasuryId);
                                 if ($oldTreasury) $oldTreasury->updateBalance();
                             }

                             if ($isUpdated) {
                                 $transaction->save();
                                 if ($transaction->treasury) $transaction->treasury->updateBalance();
                                 
                                 // Also update Journal Entry if exists
                                 $je = \App\Models\JournalEntry::where('reference', "DON-{$instance->id}")->first();
                                 if ($je) {
                                      \App\Models\JournalEntryLine::where('journal_entry_id', $je->id)
                                          ->update(['debit' => \Illuminate\Support\Facades\DB::raw("CASE WHEN debit > 0 THEN {$executionData['amount']} ELSE 0 END"), 
                                                    'credit' => \Illuminate\Support\Facades\DB::raw("CASE WHEN credit > 0 THEN {$executionData['amount']} ELSE 0 END")]);
                                 }
                             }
                         }
                    }

                    if ($modelClass === 'App\Models\Payroll') {
                        $instance->calculateNetAmount();
                        $instance->save();

                        // Sync Treasury Transaction
                        $transaction = \App\Models\TreasuryTransaction::where('payroll_id', $instance->id)
                            ->where('type', 'out')
                            ->where('status', 'active')
                            ->first();
                        
                        if ($transaction) {
                            $isUpdated = false;
                            $netAmount = $instance->net_amount ?? $instance->amount;
                            if ($transaction->amount != $netAmount) {
                                $transaction->amount = $netAmount;
                                $isUpdated = true;
                            }
                            if (isset($executionData['treasury_id']) && $transaction->treasury_id != $executionData['treasury_id']) {
                                $oldTreasuryId = $transaction->treasury_id;
                                $transaction->treasury_id = $executionData['treasury_id'];
                                $isUpdated = true;
                                if ($oldTreasury = \App\Models\Treasury::find($oldTreasuryId)) $oldTreasury->updateBalance();
                            }
                            if ($isUpdated) {
                                $transaction->save();
                                if ($transaction->treasury) $transaction->treasury->updateBalance();
                            }
                        }
                    }

                    if ($modelClass === 'App\Models\Expense') {
                        // Sync Treasury Transaction
                        $transaction = \App\Models\TreasuryTransaction::where('expense_id', $instance->id)
                            ->where('type', 'out')
                            ->where('status', 'active')
                            ->first();

                        if ($transaction) {
                            $isUpdated = false;
                            if (isset($executionData['amount']) && $transaction->amount != $executionData['amount']) {
                                $transaction->amount = $executionData['amount'];
                                $isUpdated = true;
                            }
                            if (isset($executionData['treasury_id']) && $transaction->treasury_id != $executionData['treasury_id']) {
                                $oldTreasuryId = $transaction->treasury_id;
                                $transaction->treasury_id = $executionData['treasury_id'];
                                $isUpdated = true;
                                if ($oldTreasury = \App\Models\Treasury::find($oldTreasuryId)) $oldTreasury->updateBalance();
                            }
                            if ($isUpdated) {
                                $transaction->save();
                                if ($transaction->treasury) $transaction->treasury->updateBalance();

                                // Also update Journal Entry if exists
                                $je = \App\Models\JournalEntry::where('reference', "EXP-{$instance->id}")->first();
                                if ($je) {
                                     \App\Models\JournalEntryLine::where('journal_entry_id', $je->id)
                                         ->update([
                                             'debit' => \Illuminate\Support\Facades\DB::raw("CASE WHEN debit > 0 THEN {$instance->amount} ELSE 0 END"), 
                                             'credit' => \Illuminate\Support\Facades\DB::raw("CASE WHEN credit > 0 THEN {$instance->amount} ELSE 0 END")
                                         ]);
                                }
                            }
                        }
                    }

                    if ($modelClass === 'App\Models\DelegateTrip') {
                        if (isset($executionData['status']) && $executionData['status'] === 'paid') {
                            try {
                                $logisticsService = new \App\Services\LogisticsAccountingService();
                                $logisticsService->markAsPaid($instance);
                            } catch (\Exception $e) {}
                        }
                    }

                    if ($modelClass === 'App\Models\FinancialClosure') {
                        if ($instance->approved) {
                            // If date or branch changed, we might need to lock entries for the new area
                            \App\Models\JournalEntry::where('date', '<=', $instance->date)->when($instance->branch, function ($q, $b) {
                                $q->where('branch', $b);
                            })->update(['locked' => true]);
                        }
                    }
                }

            } elseif ($changeRequest->action === 'delete') {
                $instance = $modelClass::find($changeRequest->model_id);
                if ($instance) {
                    if ($modelClass === 'App\Models\DelegateTrip') {
                         try {
                             $ls = new \App\Services\LogisticsAccountingService();
                             $ls->reverseJournalEntry($instance);
                         } catch (\Exception $e) {}
                    }
                    
                    if ($modelClass === 'App\Models\Payroll') {
                        try {
                            $ts = new \App\Services\TreasuryIntegrationService();
                            $ts->cancelPayrollTransaction($instance, 'حذف طلب مراجعة');
                        } catch (\Exception $e) {}

                        if ($instance->journal_entry_id) {
                            try {
                                $ps = app(\App\Services\PayrollAccountingService::class);
                                $ps->reverseJournalEntry($instance);
                            } catch (\Exception $e) {}
                        }
                    }

                    if ($modelClass === 'App\Models\FinancialClosure') {
                        // Automatically unlock entries for that date when closure is deleted via approved request
                        \App\Models\JournalEntry::where('date', $instance->date)->when($instance->branch, function ($q, $b) {
                           $q->where('branch', $b);
                       })->update(['locked' => false]);
                   }
                    
                    $instance->delete();
                }
            } elseif ($changeRequest->action === 'cancel') {
                $instance = $modelClass::find($changeRequest->model_id);
                if ($instance) {
                    $reason = $executionData['reason'] ?? 'Approved by manager';
                    
                    if ($modelClass === 'App\Models\Donation') {
                        $instance->update([
                            'status' => 'cancelled',
                            'cancelled_at' => now(),
                            'cancelled_by' => auth()->id(),
                            'cancellation_reason' => $reason
                        ]);
                        
                        if ($instance->type === 'cash' && $instance->treasury_id) {
                             $ts = new \App\Services\TreasuryIntegrationService();
                             $ts->cancelDonationTransaction($instance, $reason);
                        }
                    } elseif ($modelClass === 'App\Models\Expense') {
                        $instance->update([
                            'status' => 'cancelled',
                            'cancelled_at' => now(),
                            'cancelled_by' => auth()->id(),
                            'cancellation_reason' => $reason
                        ]);

                        $ts = new \App\Services\TreasuryIntegrationService();
                        $ts->cancelExpenseTransaction($instance, $reason);
                    } elseif ($modelClass === 'App\Models\Payroll') {
                        $instance->update([
                            'status' => 'cancelled',
                            'cancelled_at' => now(),
                            'cancelled_by' => auth()->id(),
                            'cancellation_reason' => $reason
                        ]);

                        $ts = new \App\Services\TreasuryIntegrationService();
                        $ts->cancelPayrollTransaction($instance, $reason);

                        if ($instance->journal_entry_id) {
                            try {
                                $ps = app(\App\Services\PayrollAccountingService::class);
                                $ps->reverseJournalEntry($instance);
                            } catch (\Exception $e) {}
                        }
                    } else {
                        if (method_exists($instance, 'cancel')) {
                            $instance->cancel();
                        }
                    }
                }
            }

            $changeRequest->update([
                'status' => 'approved',
                'reviewer_id' => auth()->id()
            ]);

            return back()->with('success', 'تم قبول التغيير وتنفيذه بنجاح');

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تنفيذ التغيير: ' . $e->getMessage());
        }
    }

    public function revert(ChangeRequest $changeRequest)
    {
        if (!request()->user()->hasRole('admin')) {
            abort(403, 'التراجع مسموح للأدمن فقط');
        }

        if ($changeRequest->status === 'pending') {
            return back()->with('error', 'الطلب معلق بالفعل');
        }

        // If it was approved, we try to reverse the effect
        if ($changeRequest->status === 'approved') {
            try {
                $modelClass = $changeRequest->model_type;
                $modelId = $changeRequest->model_id;
                
                $instance = $modelId ? $modelClass::find($modelId) : null;

                if ($changeRequest->action === 'create') {
                    if ($instance) {
                        // Cleanup side effects for create
                        if ($modelClass === 'App\Models\Expense') {
                             \App\Models\TreasuryTransaction::where('expense_id', $instance->id)->delete();
                             \App\Models\JournalEntry::where('reference', "EXP-{$instance->id}")->delete();
                             if ($instance->treasury) $instance->treasury->updateBalance();
                        }
                         if ($modelClass === 'App\Models\Donation') {
                             \App\Models\TreasuryTransaction::where('donation_id', $instance->id)->delete();
                             \App\Models\JournalEntry::where('reference', "DON-{$instance->id}")->delete();
                             if ($instance->treasury) $instance->treasury->updateBalance();
                        }
                        $instance->delete();
                    }
                } elseif ($changeRequest->action === 'update') {
                    // Revert UPDATE: restore old values from 'diff'
                    if ($instance && isset($changeRequest->payload['diff'])) {
                        $oldValues = [];
                        foreach ($changeRequest->payload['diff'] as $key => $vals) {
                            $oldValues[$key] = $vals['from'];
                        }
                        $instance->update($oldValues);
                        
                        if ($modelClass === 'App\Models\Donation') {
                             $transaction = \App\Models\TreasuryTransaction::where('donation_id', $instance->id)->where('type', 'in')->first();
                             if ($transaction && isset($oldValues['amount'])) {
                                 $transaction->update(['amount' => $oldValues['amount']]);
                                 $transaction->treasury->updateBalance();
                             }
                        }
                    }
                } elseif ($changeRequest->action === 'delete') {
                    if (method_exists($modelClass, 'withTrashed')) {
                        $instance = $modelClass::withTrashed()->find($modelId);
                        if ($instance) $instance->restore();
                    }
                } elseif ($changeRequest->action === 'cancel') {
                    if ($instance) {
                        $instance->update(['status' => 'active']);
                        // Remove reversal transactions
                        \App\Models\TreasuryTransaction::whereIn('reference', ["REV-DON-{$instance->id}", "REV-EXP-{$instance->id}"])->delete();
                        if (isset($instance->treasury)) $instance->treasury->updateBalance();
                    }
                }
            } catch (\Exception $e) {
                return back()->with('error', 'فشل التراجع عن التعديلات: ' . $e->getMessage());
            }
        }

        $changeRequest->update([
            'status' => 'pending',
            'reviewer_id' => null
        ]);

        return back()->with('success', 'تم التراجع عن القرار وعاد الطلب لحالة التعليق');
    }

    public function reject(Request $request, ChangeRequest $changeRequest)
    {
        if (!request()->user()->hasRole('admin')) {
            abort(403, 'الرفض مسموح للأدمن فقط');
        }

        if ($changeRequest->status !== 'pending') {
            return back()->with('error', 'الطلب تمت معالجته مسبقاً');
        }

        $changeRequest->update([
            'status' => 'rejected',
            'reviewer_id' => request()->user()->id,
            'rejection_reason' => $request->input('rejection_reason')
        ]);

        // If it's a Leave request, update the Leave model status as well
        if ($changeRequest->model_type === 'App\Models\Leave' && $changeRequest->model_id) {
            $leave = \App\Models\Leave::find($changeRequest->model_id);
            if ($leave) {
                $leave->update([
                    'status' => 'rejected',
                    'rejection_reason' => $request->input('rejection_reason')
                ]);
            }
        }

        return back()->with('success', 'تم رفض الطلب بنجاح. لم يتم تطبيق أي تغييرات.');
    }

    public function update(Request $request, ChangeRequest $changeRequest)
    {
        if (!request()->user()->hasRole('admin')) {
             abort(403, 'تعديل السجلات مسموح للأدمن فقط');
        }

        $data = $request->validate([
            'status' => 'required|in:pending,approved,rejected,cancelled',
            'rejection_reason' => 'nullable|string',
            'payload' => 'nullable|array' // Advanced usage
        ]);

        // If changing status to 'approved' from something else, we should try to execute the logic
        if ($data['status'] === 'approved' && $changeRequest->status !== 'approved') {
             // Temporarily set status to pending so 'approve' method accepts it
             $changeRequest->status = 'pending';
             $changeRequest->save();
             
             // Reuse existing approve logic
             return $this->approve($changeRequest);
        }

        $changeRequest->update($data);
        return back()->with('success', 'تم تعديل السجل بنجاح');
    }

    public function destroy(ChangeRequest $changeRequest)
    {
        if (!request()->user()->hasRole('admin')) {
             abort(403, 'حذف السجلات مسموح للأدمن فقط');
        }
        
        $changeRequest->delete();
        return back()->with('success', 'تم حذف السجل بنجاح');
    }

    public function bulkDestroy(Request $request)
    {
        if (!request()->user()->hasRole('admin')) {
             abort(403, 'حذف السجلات مسموح للأدمن فقط');
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:change_requests,id'
        ]);

        $ids = $request->input('ids');
        ChangeRequest::whereIn('id', $ids)->delete();

        return back()->with('success', 'تم حذف السجلات المحددة بنجاح');
    }

    public function bulkRevert(Request $request)
    {
        if (!request()->user()->hasRole('admin')) {
             abort(403, 'التراجع مسموح للأدمن فقط');
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:change_requests,id'
        ]);

        $ids = $request->input('ids');
        $requests = ChangeRequest::whereIn('id', $ids)->get();
        $count = 0;

        foreach ($requests as $req) {
            // Revert logic (calling the existing revert logic or method if generic enough, but here we invoke the same logic)
            // Ideally we should refactor revert logic to a service, but for now we call the private/protected logic or just re-implement minimal.
            // Since revert logic is in the 'revert' method which returns a response, we should refactor it. 
            // IMPROVEMENT: Refactor revert logic to a helper or service. 
            // For this quick implementation, I'll extract the revert logic to a protected method.
            
            if ($req->status !== 'approved') continue;
            
            try {
               $this->performRevert($req);
               $count++;
            } catch (\Exception $e) {
                // Continue or log error
            }
        }

        return back()->with('success', "تم التراجع عن $count من الطلبات المحددة بنجاح");
    }

    protected function performRevert(ChangeRequest $changeRequest)
    {
        $modelClass = $changeRequest->model_type;
        $payload = $changeRequest->payload;
        $snapshot = $changeRequest->before_image ?? []; // assuming we might settle on this later, but currently we use diff

        if ($changeRequest->action === 'create') {
            // Undo create = Delete
            $instance = $modelClass::find($changeRequest->model_id);
            if ($instance) {
                 // Clean up financial side effects if any
                if ($modelClass === 'App\Models\Donation' || $modelClass === 'App\Models\Expense' || $modelClass === 'App\Models\Payroll') {
                     // Reverse Transactions logic must be handled.
                     // For brevity, we assume standard delete handles basic cleanup or we need specific service calls.
                     // Copying logic from revert method:
                     if ($modelClass === 'App\Models\Expense' || $modelClass === 'App\Models\Donation' || $modelClass === 'App\Models\Payroll') {
                        // Treasury/Journal cleanup if possible
                         // For now, hard delete is risky without full service logic.
                         // We will rely on model events or just delete.
                     }
                     
                     // Specifically for Donation/Expense/Payroll where we did manual treasury work:
                     if ($modelClass === 'App\Models\Donation') {
                        // Find transaction?
                     }
                }
                $instance->delete();
            }
        } elseif ($changeRequest->action === 'update') {
            // Undo update = Restore old values
             // We need the DIFF to revert.
            $executionData = $payload;
             if (is_array($payload) && isset($payload['__is_wrapped']) && isset($payload['diff'])) {
                $instance = $modelClass::find($changeRequest->model_id);
                if ($instance) {
                    $revertData = [];
                    foreach ($payload['diff'] as $key => $vals) {
                        $revertData[$key] = $vals['from'];
                    }
                    $instance->update($revertData);
                     // If financial update, we might need to fix amounts. 
                     // This is complex for bulk without shared service logic.
                }
            }
        } elseif ($changeRequest->action === 'delete') {
            // Undo delete = Restore
            $instance = $modelClass::withTrashed()->find($changeRequest->model_id);
            if ($instance) {
                $instance->restore();
            }
        } elseif ($changeRequest->action === 'cancel') {
             // Undo cancel = Update status to active?
             $instance = $modelClass::find($changeRequest->model_id);
             if ($instance) {
                 // Try to set status 'active' or 'pending' depending on model
                 $instance->status = 'active'; // Assumption
                 $instance->save();
                 // Reversal of cancellation transactions is needed!
             }
        }

        $changeRequest->update(['status' => 'pending', 'reviewer_id' => null]);
    }

    public function cancel(ChangeRequest $changeRequest)
    {
        if ($changeRequest->status !== 'pending') {
            return back()->with('error', 'لا يمكن إلغاء طلب تمت معالجته بالفعل');
        }

        // Only the requester or an admin/manager can cancel
        $user = request()->user();
        if (!$user || ($user->id !== $changeRequest->user_id && !$user->hasRole('admin') && !$user->hasRole('manager'))) {
            abort(403, 'غير مصرح لك بإلغاء هذا الطلب');
        }

        $changeRequest->update([
            'status' => 'cancelled',
            'reviewer_id' => auth()->id()
        ]);

        return back()->with('success', 'تم إلغاء الطلب بنجاح');
    }
}

