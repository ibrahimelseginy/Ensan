<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Finance\CashTransfer;
use App\Models\Finance\CashAccount;
use App\Models\Finance\PaymentInstrument;
use App\Models\Treasury;
use App\Models\TreasuryTransaction;
use App\Models\User;
use App\Models\Project;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CashTransferWebController extends Controller
{
    public function index(Request $request): View
    {
        $treasuries = Treasury::where('is_active', true)->orderBy('name')->get();

        $transfers = CashTransfer::with(['fromAccount', 'toAccount', 'requester', 'receiver', 'approver'])
            ->orderByDesc('id')
            ->paginate(15);

        // Cheques and Bank Disbursals
        $paymentInstruments = PaymentInstrument::with(['sourceAccount', 'project', 'creator'])
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $projects = Project::orderBy('name')->get(['id', 'name']);
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('transfers.index', compact('transfers', 'treasuries', 'paymentInstruments', 'projects', 'users'));
    }

    public function storeSupply(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'from_treasury_id' => 'required|exists:treasuries,id|different:to_treasury_id',
            'to_treasury_id' => 'required|exists:treasuries,id',
            'amount' => 'required|numeric|min:1',
            'transfer_type' => 'required|in:supply,internal_transfer,bank_deposit',
            'reference' => 'nullable|string|max:191',
            'note' => 'nullable|string|max:1000',
        ]);

        $fromTreasury = Treasury::findOrFail($data['from_treasury_id']);
        $toTreasury = Treasury::findOrFail($data['to_treasury_id']);

        if ($fromTreasury->current_balance < (float) $data['amount']) {
            return back()->withInput()->with('error', "رصيد الخزينة المصدر ({$fromTreasury->name}) لا يكفي للتحويل. المتاح: " . number_format($fromTreasury->current_balance, 2) . ' ج.م');
        }

        DB::transaction(function () use ($data, $fromTreasury, $toTreasury) {
            $prefix = $data['transfer_type'] === 'supply' ? 'SUP' : ($data['transfer_type'] === 'bank_deposit' ? 'DEP' : 'TRF');
            $transferNumber = $prefix . '-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $fromCashAccount = CashAccount::firstOrCreate(
                ['legacy_treasury_id' => $fromTreasury->id],
                ['code' => 'ACC-' . $fromTreasury->id, 'name' => $fromTreasury->name, 'kind' => 'cash', 'ledger_account_id' => 1, 'currency' => 'EGP', 'is_active' => true]
            );

            $toCashAccount = CashAccount::firstOrCreate(
                ['legacy_treasury_id' => $toTreasury->id],
                ['code' => 'ACC-' . $toTreasury->id, 'name' => $toTreasury->name, 'kind' => $toTreasury->type === 'bank' ? 'bank' : 'cash', 'ledger_account_id' => 1, 'currency' => 'EGP', 'is_active' => true]
            );

            // Deduct immediately from source
            $fromTreasury->current_balance -= (float) $data['amount'];
            $fromTreasury->save();

            $fromTreasury->transactions()->create([
                'type' => 'out',
                'amount' => $data['amount'],
                'transaction_date' => now()->toDateString(),
                'description' => "تحويل صادر/توريد إلى ({$toTreasury->name}) - سند: {$transferNumber}" . ($data['note'] ? ' - ' . $data['note'] : ''),
                'created_by' => auth()->id(),
            ]);

            // Create transfer record pending receipt
            CashTransfer::create([
                'transfer_number' => $transferNumber,
                'from_cash_account_id' => $fromCashAccount->id,
                'to_cash_account_id' => $toCashAccount->id,
                'amount' => $data['amount'],
                'currency' => 'EGP',
                'requested_on' => now()->toDateString(),
                'status' => 'submitted', // pending confirmation by receiver
                'requested_by' => auth()->id(),
                'reference' => $data['reference'] ?? null,
                'note' => $data['note'] ?? null,
            ]);
        });

        return back()->with('success', 'تم تسجيل طلب التوريد/التحويل بنجاح، وخصم المبلغ من الخزينة الموردة بانتظار تأكيد الاستلام الفعلي من الخزينة المستلمة.');
    }

    public function confirmReceipt(Request $request, CashTransfer $transfer): RedirectResponse
    {
        if ($transfer->status !== 'submitted') {
            return back()->with('error', 'التحويل تم تأكيده أو إلغاؤه مسبقاً.');
        }

        DB::transaction(function () use ($transfer) {
            $toAccount = CashAccount::findOrFail($transfer->to_cash_account_id);
            $toTreasury = Treasury::where('id', $toAccount->legacy_treasury_id)->first();

            if ($toTreasury) {
                $toTreasury->current_balance += (float) $transfer->amount;
                $toTreasury->save();

                $fromName = $transfer->fromAccount ? $transfer->fromAccount->name : 'خزينة أخرى';
                $toTreasury->transactions()->create([
                    'type' => 'in',
                    'amount' => $transfer->amount,
                    'transaction_date' => now()->toDateString(),
                    'description' => "استلام وتأكيد توريد/تحويل من ({$fromName}) - سند: {$transfer->transfer_number}",
                    'created_by' => auth()->id(),
                ]);
            }

            $transfer->status = 'received';
            $transfer->received_at = now();
            $transfer->received_by = auth()->id();
            $transfer->save();
        });

        return back()->with('success', 'تم تأكيد استلام التوريد بنجاح وإضافة المبلغ إلى الخزينة المستلمة.');
    }

    public function disburseCheque(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'cheque_number' => 'required|string|max:64',
            'bank_treasury_id' => 'required|exists:treasuries,id',
            'amount' => 'required|numeric|min:1',
            'issued_on' => 'required|date',
            'destination_type' => 'required|in:internal_treasury,external_party',
            'target_treasury_id' => 'nullable|required_if:destination_type,internal_treasury|exists:treasuries,id',
            'beneficiary_name' => 'nullable|required_if:destination_type,external_party|string|max:191',
            'project_id' => 'nullable|exists:projects,id',
            'note' => 'nullable|string|max:1000',
        ]);

        $bankTreasury = Treasury::findOrFail($data['bank_treasury_id']);

        if ($bankTreasury->current_balance < (float) $data['amount']) {
            return back()->withInput()->with('error', "رصيد حساب البنك ({$bankTreasury->name}) لا يكفي لصرف الشيك. المتاح: " . number_format($bankTreasury->current_balance, 2) . ' ج.م');
        }

        DB::transaction(function () use ($data, $bankTreasury) {
            $bankAccount = CashAccount::firstOrCreate(
                ['legacy_treasury_id' => $bankTreasury->id],
                ['code' => 'BNK-' . $bankTreasury->id, 'name' => $bankTreasury->name, 'kind' => 'bank', 'ledger_account_id' => 1, 'currency' => 'EGP', 'is_active' => true]
            );

            // Deduct from bank
            $bankTreasury->current_balance -= (float) $data['amount'];
            $bankTreasury->save();

            $bankTreasury->transactions()->create([
                'type' => 'out',
                'amount' => $data['amount'],
                'transaction_date' => $data['issued_on'],
                'description' => "صرف شيك رقم ({$data['cheque_number']}) - المستفيد: " . ($data['beneficiary_name'] ?? 'خزينة داخلية') . ($data['note'] ? ' - ' . $data['note'] : ''),
                'created_by' => auth()->id(),
            ]);

            // If internal treasury, add to target treasury
            if ($data['destination_type'] === 'internal_treasury' && !empty($data['target_treasury_id'])) {
                $targetTreasury = Treasury::findOrFail($data['target_treasury_id']);
                $targetTreasury->current_balance += (float) $data['amount'];
                $targetTreasury->save();

                $targetTreasury->transactions()->create([
                    'type' => 'in',
                    'amount' => $data['amount'],
                    'transaction_date' => $data['issued_on'],
                    'description' => "استلام سيولة شيك بنكي رقم ({$data['cheque_number']}) مسحوب من ({$bankTreasury->name})",
                    'created_by' => auth()->id(),
                ]);
            }

            PaymentInstrument::create([
                'instrument_number' => $data['cheque_number'],
                'instrument_type' => PaymentInstrument::TYPE_CHEQUE,
                'source_cash_account_id' => $bankAccount->id,
                'amount' => $data['amount'],
                'currency' => 'EGP',
                'beneficiary_type' => $data['destination_type'],
                'beneficiary_name' => $data['beneficiary_name'] ?? ($targetTreasury->name ?? null),
                'status' => 'cleared',
                'issued_on' => $data['issued_on'],
                'cleared_on' => $data['issued_on'],
                'project_id' => $data['project_id'] ?? null,
                'created_by' => auth()->id(),
                'note' => $data['note'] ?? null,
            ]);
        });

        return back()->with('success', 'تم صرف الشيك البنكي بنجاح وتحديث الأرصدة فوراً.');
    }

    public function directBankTransfer(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bank_treasury_id' => 'required|exists:treasuries,id',
            'beneficiary_name' => 'required|string|max:191',
            'bank_account_iban' => 'nullable|string|max:64',
            'bank_name' => 'nullable|string|max:191',
            'amount' => 'required|numeric|min:1',
            'transfer_date' => 'required|date',
            'disbursal_type' => 'required|in:orphan_sponsorship,medical_aid,family_support,general',
            'project_id' => 'nullable|exists:projects,id',
            'note' => 'nullable|string|max:1000',
        ]);

        $bankTreasury = Treasury::findOrFail($data['bank_treasury_id']);

        if ($bankTreasury->current_balance < (float) $data['amount']) {
            return back()->withInput()->with('error', "رصيد حساب البنك ({$bankTreasury->name}) لا يكفي للتحويل. المتاح: " . number_format($bankTreasury->current_balance, 2) . ' ج.م');
        }

        DB::transaction(function () use ($data, $bankTreasury) {
            $bankAccount = CashAccount::firstOrCreate(
                ['legacy_treasury_id' => $bankTreasury->id],
                ['code' => 'BNK-' . $bankTreasury->id, 'name' => $bankTreasury->name, 'kind' => 'bank', 'ledger_account_id' => 1, 'currency' => 'EGP', 'is_active' => true]
            );

            // Deduct from bank
            $bankTreasury->current_balance -= (float) $data['amount'];
            $bankTreasury->save();

            $types = [
                'orphan_sponsorship' => 'كفالة أيتام شهرية',
                'medical_aid' => 'مساعدة علاجية',
                'family_support' => 'دعم مالي لأسرة',
                'general' => 'مساعدة عامة',
            ];
            $typeLabel = $types[$data['disbursal_type']] ?? 'مساعدة مباشرة';

            $bankTreasury->transactions()->create([
                'type' => 'out',
                'amount' => $data['amount'],
                'transaction_date' => $data['transfer_date'],
                'description' => "تحويل بنكي مباشر ({$typeLabel}) للمستفيد: {$data['beneficiary_name']}" . ($data['bank_account_iban'] ? " (حساب/آيبان: {$data['bank_account_iban']})" : ''),
                'created_by' => auth()->id(),
            ]);

            PaymentInstrument::create([
                'instrument_number' => 'BNK-TRF-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'instrument_type' => PaymentInstrument::TYPE_BANK_TRANSFER,
                'source_cash_account_id' => $bankAccount->id,
                'amount' => $data['amount'],
                'currency' => 'EGP',
                'beneficiary_type' => $data['disbursal_type'],
                'beneficiary_name' => $data['beneficiary_name'],
                'status' => 'cleared',
                'issued_on' => $data['transfer_date'],
                'cleared_on' => $data['transfer_date'],
                'external_reference' => $data['bank_account_iban'] ?? null,
                'project_id' => $data['project_id'] ?? null,
                'created_by' => auth()->id(),
                'note' => $data['note'] ?? null,
            ]);
        });

        return back()->with('success', 'تم تنفيذ التحويل البنكي المباشر للمستفيد بنجاح وخصم المبلغ من رصيد البنك دون المرور على الخزائن النقدية.');
    }

    public function printSupplyVoucher(CashTransfer $transfer): View
    {
        $transfer->load(['fromAccount', 'toAccount', 'requester', 'receiver']);
        return view('transfers.print_voucher', compact('transfer'));
    }
}
