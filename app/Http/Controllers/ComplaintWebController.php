<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use App\Models\Donor;
use App\Models\Beneficiary;
use Illuminate\Http\Request;

final class ComplaintWebController extends Controller
{
    public function index() { 
        $complaints = Complaint::orderByDesc('id')->paginate(20); 

        // Add pending check for each complaint
        $complaints->each(function($c) {
            $c->pendingRequest = \App\Models\ChangeRequest::where('model_type', Complaint::class)
                ->where('model_id', $c->id)
                ->where('status', 'pending')
                ->first();
        });

        return view('complaints.index', compact('complaints')); 
    }
    public function create() {
        $users = User::orderBy('name')->get();
        $donors = Donor::orderBy('name')->get();
        $beneficiaries = Beneficiary::orderBy('full_name')->get();
        $trackingCode = Complaint::generateUniqueTrackingCode();
        return view('complaints.create', compact('users','donors','beneficiaries','trackingCode'));
    }
    public function store(Request $request)
    {
        $messages = [
            'source_type.required' => 'نوع المصدر مطلوب.',
            'source_type.in' => 'نوع المصدر غير صحيح.',
            'source_id.integer' => 'رقم المصدر يجب أن يكون رقماً صحيحاً.',
            'complainant_name.max' => 'اسم صاحب الشكوى يجب ألا يتجاوز 255 حرفاً.',
            'against_user_id.exists' => 'الموظف المشكو ضده غير موجود.',
            'subject.required' => 'عنوان الشكوى مطلوب.',
            'message.required' => 'نص الشكوى مطلوب.',
        ];
        $data = $request->validate([
            'tracking_code' => 'nullable|string|max:30',
            'source_type' => 'required|in:donor,beneficiary,employee',
            'source_id' => 'nullable|integer',
            'complainant_name' => 'nullable|string|max:255',
            'complainant_phone' => 'nullable|string|max:50',
            'against_user_id' => 'nullable|exists:users,id',
            'status' => 'in:open,in_progress,closed',
            'subject' => 'required|string',
            'message' => 'required|string'
        ], $messages);

        // Ensure we have a person identified or named
        if (empty($data['source_id']) && empty($data['complainant_name'])) {
            return back()->withInput()->withErrors(['source_id' => 'يرجى اختيار الشخص صاحب الشكوى من القائمة أو كتابة اسمه يدوياً.']);
        }

        // Auto-fill complainant_name if selected from list
        if (!empty($data['source_id']) && empty($data['complainant_name'])) {
            if ($data['source_type'] === 'donor') {
                $donor = Donor::find($data['source_id']);
                $data['complainant_name'] = $donor?->name;
                $data['complainant_phone'] = $data['complainant_phone'] ?? $donor?->phone;
            } elseif ($data['source_type'] === 'beneficiary') {
                $ben = Beneficiary::find($data['source_id']);
                $data['complainant_name'] = $ben?->full_name;
                $data['complainant_phone'] = $data['complainant_phone'] ?? $ben?->phone;
            } elseif ($data['source_type'] === 'employee') {
                $emp = User::find($data['source_id']);
                $data['complainant_name'] = $emp?->name;
                $data['complainant_phone'] = $data['complainant_phone'] ?? $emp?->phone;
            }
        }

        if (empty($data['source_id'])) {
            $data['source_id'] = 0;
        }
        
        $executor = function () use ($data) {
             return Complaint::create($data);
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Complaint::class,
            null,
            'create',
            $data,
            $executor
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال الشكوى للمراجعة.');
        }

        return redirect()->route('complaints.show', $result)
            ->with('success', 'تم حفظ وتسجيل الشكوى بنجاح. كود التتبع المعتمد هو: ' . $result->tracking_code);
    }
    public function show(Complaint $complaint) { 
        $pending = \App\Models\ChangeRequest::where('model_type', Complaint::class)
            ->where('model_id', $complaint->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الشكوى لديها طلب مراجعة حالياً');
        }

        return view('complaints.show', compact('complaint')); 
    }
    public function edit(Complaint $complaint) { 
        $pending = \App\Models\ChangeRequest::where('model_type', Complaint::class)
            ->where('model_id', $complaint->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الشكوى لديها طلب مراجعة حالياً');
        }

        $users = User::orderBy('name')->get(); 
        return view('complaints.edit', compact('complaint','users')); 
    }
    public function update(Request $request, Complaint $complaint) {
        $pending = \App\Models\ChangeRequest::where('model_type', Complaint::class)
            ->where('model_id', $complaint->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الشكوى لديها طلب مراجعة حالياً');
        }

        $data = $request->validate([
            'against_user_id' => 'nullable|exists:users,id',
            'status' => 'in:open,in_progress,closed',
            'subject' => 'required|string',
            'message' => 'required|string',
            'resolution' => 'nullable|string',
        ]);

        // تحديد وقت الحل عند إغلاق الشكوى
        if (($data['status'] ?? null) === 'closed' && $complaint->status !== 'closed') {
            $data['resolved_at'] = now();
        } elseif (($data['status'] ?? null) !== 'closed') {
            $data['resolved_at'] = null;
        }

        $executor = function () use ($complaint, $data) {
            $complaint->update($data);
            return $complaint;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Complaint::class,
            $complaint->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل الشكوى للموافقة');
        }

        return redirect()->route('complaints.show', $complaint); 
    }
    public function destroy(Complaint $complaint) { 
        $pending = \App\Models\ChangeRequest::where('model_type', Complaint::class)
            ->where('model_id', $complaint->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الشكوى لديها طلب مراجعة حالياً');
        }

        $executor = function () use ($complaint) {
            $complaint->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Complaint::class,
            $complaint->id,
            'delete',
            ['note' => 'حذف شكوى'],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف الشكوى للموافقة');
        }

        return redirect()->route('complaints.index'); 
    }
}
