<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ChangeRequest;
use App\Services\AccountService;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccountWebController extends Controller
{
    public function __construct(
        private AccountService $accountService
    ) {}

    public function index(): View
    {
        $accounts    = $this->accountService->getRootAccounts();
        $allAccounts = $this->accountService->getAllAccountsPaginated(50);

        return view('accounts.index', compact('accounts', 'allAccounts'));
    }

    public function dashboard(): View
    {
        $stats = $this->accountService->getDashboardData();
        return view('accounts.dashboard', $stats);
    }

    public function create(): View
    {
        $parents = $this->accountService->getSelectableParents();
        return view('accounts.create', compact('parents'));
    }

    public function store(StoreAccountRequest $request): RedirectResponse
    {
        $result = $this->accountService->createAccount($request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة الحساب للموافقة.');
        }

        return redirect()->route('accounts.index');
    }

    public function show(Account $account): View|RedirectResponse
    {
        if ($this->hasPendingRequest($account)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا الحساب لديه طلب مراجعة حالياً');
        }

        $account->load(['parent', 'children']);
        $lines = $account->lines()->with('journalEntry')->orderByDesc('id')->paginate(20);

        return view('accounts.show', compact('account', 'lines'));
    }

    public function edit(Account $account): View|RedirectResponse
    {
        if ($this->hasPendingRequest($account)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا الحساب لديه طلب مراجعة حالياً');
        }

        $parents = $this->accountService->getSelectableParents((int)$account->id);
        return view('accounts.edit', compact('account', 'parents'));
    }

    public function update(UpdateAccountRequest $request, Account $account): RedirectResponse
    {
        if ($this->hasPendingRequest($account)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا الحساب لديه طلب مراجعة حالياً');
        }

        $result = $this->accountService->updateAccount($account, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل الحساب للموافقة');
        }

        return redirect()->route('accounts.index')->with('success', 'تم تحديث الحساب بنجاح');
    }

    public function destroy(Account $account): RedirectResponse
    {
        if ($this->hasPendingRequest($account)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا الحساب لديه طلب مراجعة حالياً');
        }

        try {
            $result = $this->accountService->deleteAccount($account);

            if ($result instanceof ChangeRequest) {
                return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف الحساب للموافقة');
            }

            return redirect()->route('accounts.index')->with('success', 'تم حذف الحساب بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function hasPendingRequest(Account $account): bool
    {
        return ChangeRequest::where('model_type', Account::class)
            ->where('model_id', $account->id)
            ->where('status', 'pending')
            ->exists();
    }
}
