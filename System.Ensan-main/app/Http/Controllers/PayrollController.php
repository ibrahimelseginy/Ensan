<?php
namespace App\Http\Controllers;

use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index() { return Payroll::with('user')->paginate(50); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|string',
            'amount' => 'required|numeric',
            'currency' => 'nullable|string',
            'paid_at' => 'nullable|date'
        ]);
        return Payroll::create($data);
    }
    public function show(Payroll $payroll) { return $payroll->load('user'); }
    public function update(Request $request, Payroll $payroll)
    {
        $data = $request->validate([
            'month' => 'nullable|string',
            'amount' => 'nullable|numeric',
            'currency' => 'nullable|string',
            'paid_at' => 'nullable|date'
        ]);
        $payroll->update($data);
        return $payroll->load('user');
    }
    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return response()->noContent();
    }
}
