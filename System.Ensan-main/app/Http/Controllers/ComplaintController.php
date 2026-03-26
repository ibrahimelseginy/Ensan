<?php
namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index() { return Complaint::paginate(20); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'source_type' => 'required|in:donor,beneficiary,employee',
            'source_id' => 'required|integer',
            'against_user_id' => 'nullable|exists:users,id',
            'status' => 'in:open,in_progress,closed',
            'subject' => 'required|string',
            'message' => 'required|string',
            'attachment_path' => 'nullable|string'
        ]);
        return Complaint::create($data);
    }
    public function show(Complaint $complaint) { return $complaint; }
    public function update(Request $request, Complaint $complaint)
    {
        $data = $request->validate([
            'against_user_id' => 'nullable|exists:users,id',
            'status' => 'in:open,in_progress,closed'
        ]);
        $complaint->update($data);
        return $complaint;
    }
    public function destroy(Complaint $complaint)
    {
        $complaint->delete();
        return response()->noContent();
    }
}
