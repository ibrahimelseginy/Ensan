<?php
namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index() { return Account::paginate(50); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:accounts,code',
            'name' => 'required|string',
            'type' => 'required|in:asset,liability,equity,revenue,expense'
        ]);
        return Account::create($data);
    }
    public function show(Account $account) { return $account; }
    public function update(Request $request, Account $account)
    {
        $data = $request->validate([
            'code' => 'sometimes|string|unique:accounts,code,' . $account->id,
            'name' => 'sometimes|string',
            'type' => 'sometimes|in:asset,liability,equity,revenue,expense'
        ]);
        $account->update($data);
        return $account;
    }
    public function destroy(Account $account)
    {
        $account->delete();
        return response()->noContent();
    }
}
