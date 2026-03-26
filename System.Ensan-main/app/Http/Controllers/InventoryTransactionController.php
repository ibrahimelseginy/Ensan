<?php
namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use Illuminate\Http\Request;

class InventoryTransactionController extends Controller
{
    public function index()
    {
        return InventoryTransaction::with(['item','warehouse','sourceDonation','beneficiary','project','campaign'])->paginate(20);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'item_id' => 'required|exists:items,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'type' => 'required|in:in,transfer,out',
            'quantity' => 'required|numeric',
            'source_donation_id' => 'nullable|exists:donations,id',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'reference' => 'nullable|string'
        ]);
        return InventoryTransaction::create($data);
    }
    public function show(InventoryTransaction $inventoryTransaction)
    {
        return $inventoryTransaction->load(['item','warehouse','sourceDonation','beneficiary','project','campaign']);
    }
    public function update(Request $request, InventoryTransaction $inventoryTransaction)
    {
        $data = $request->validate([
            'type' => 'sometimes|in:in,transfer,out',
            'quantity' => 'nullable|numeric',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'reference' => 'nullable|string'
        ]);
        $inventoryTransaction->update($data);
        return $inventoryTransaction->load(['item','warehouse','sourceDonation','beneficiary','project','campaign']);
    }
    public function destroy(InventoryTransaction $inventoryTransaction)
    {
        $inventoryTransaction->delete();
        return response()->noContent();
    }
}
