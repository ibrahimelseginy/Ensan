<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserWebController extends Controller
{
    public function index()
    {
        // Statistics for Employees
        $totalEmployees = User::where('is_employee', true)->count();
        $activeEmployees = User::where('is_employee', true)->where('active', true)->count();

        $projectCounts = User::where('is_employee', true)
            ->whereNotNull('project_id')
            ->select('project_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('project_id')
            ->pluck('total', 'project_id');

        $projectsWithStats = \App\Models\Project::orderBy('name')->get()->map(function ($p) use ($projectCounts) {
            $p->employees_count = $projectCounts[$p->id] ?? 0;
            return $p;
        });

        // List all users, excluding volunteers (they are managed in the Volunteers page)
        $query = User::where('is_volunteer', false)->with('roles')->orderBy('name');

        if (request('active') === '1') {
            $query->where('active', true);
        }
        elseif (request('active') === '0') {
            $query->where('active', false);
        }

        if (request('project_id')) {
            $query->where('project_id', request('project_id'));
        }

        if (request('type') === 'employee') {
            $query->where('is_employee', true);
        }

        $users = $query->paginate(20);

        // Add pending check for each user
        $users->each(function ($u) {
            $u->pendingRequest = \App\Models\ChangeRequest::where('model_type', \App\Models\User::class)
                ->where('model_id', $u->id)
                ->where('status', 'pending')
                ->first();
        });

        return view('users.index', compact('users', 'totalEmployees', 'activeEmployees', 'projectsWithStats'));
    }
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('users.create', compact('roles'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string',
            'is_employee' => 'boolean',
            'is_volunteer' => 'boolean',
            'active' => 'boolean',
            'roles' => 'array',
            'department' => 'nullable|string',
            'job_title' => 'nullable|string',
            'salary' => 'nullable|numeric',
            'join_date' => 'nullable|date',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'profile_photo' => 'nullable|image|max:10240',
            'contract_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'criminal_record_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'id_card_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'annual_leave_quota' => 'nullable|integer',
            'leave_balance' => 'nullable|integer'
        ]);
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        if ($request->hasFile('profile_photo')) {
            $user->uploadImage($request->file('profile_photo'), 'profile-photos', 'profile_photo_path');
        }

        foreach (['contract_image', 'criminal_record_image', 'id_card_image'] as $field) {
            if ($request->hasFile($field)) {
                $user->uploadImage($request->file($field), 'user-docs', $field);
            }
        }

        if (!empty($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }
        return redirect()->route('users.show', $user);
    }
    public function show(User $user)
    {
        $pendingRequest = \App\Models\ChangeRequest::where('model_type', User::class)
            ->where('model_id', $user->id)
            ->where('status', 'pending')
            ->first();

        return view('users.show', compact('user', 'pendingRequest'));
    }
    public function edit(User $user)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\User::class)
            ->where('model_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المستخدم لديه طلب مراجعة حالياً');
        }

        $roles = Role::orderBy('name')->get();
        return view('users.edit', compact('user', 'roles'));
    }
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string',
            'is_employee' => 'boolean',
            'is_volunteer' => 'boolean',
            'active' => 'boolean',
            'roles' => 'array',
            'department' => 'nullable|string',
            'job_title' => 'nullable|string',
            'salary' => 'nullable|numeric',
            'join_date' => 'nullable|date',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'profile_photo' => 'nullable|image|max:10240',
            'contract_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'criminal_record_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'id_card_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240',
            'annual_leave_quota' => 'nullable|integer',
            'leave_balance' => 'nullable|integer'
        ]);
        // كلمة المرور: تُحفظ فوراً فقط إذا تم إدخال قيمة جديدة فعلاً
        $newPassword = trim($request->input('password', ''));
        if ($newPassword !== '' && strlen($newPassword) >= 6) {
            $user->password = Hash::make($newPassword);
            $user->saveQuietly();
        }
        unset($data['password']);

        unset($data['profile_photo']);

        foreach (['contract_image', 'criminal_record_image', 'id_card_image'] as $field) {
            unset($data[$field]);
        }

        // حفظ الأدوار
        if (isset($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }
        unset($data['roles']);

        // حفظ باقي البيانات مباشرة
        if (!empty($data)) {
            $user->update($data);
        }

        // الصورة الشخصية
        if ($request->hasFile('profile_photo')) {
            $user->uploadImage($request->file('profile_photo'), 'profile-photos', 'profile_photo_path');
        }

        // المستندات (عقد - سجل جنائي - بطاقة)
        foreach (['contract_image', 'criminal_record_image', 'id_card_image'] as $field) {
            if ($request->hasFile($field)) {
                $user->uploadImage($request->file($field), 'user-docs', $field);
            }
        }

        return redirect()->route('users.show', $user)->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }
    public function destroy(User $user)
    {
        $executor = function () use ($user) {
            $user->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\User::class ,
            $user->id,
            'delete',
        [
            'note' => 'حذف مستخدم',
            'name' => $user->name,
            'job_title' => $user->job_title,
            'phone' => $user->phone,
            'is_employee' => $user->is_employee,
            'is_volunteer' => $user->is_volunteer,
        ],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المستخدم للموافقة');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'تم حذف المستخدم بنجاح');
    }
    public function attachRole(User $user, Role $role)
    {
        $user->roles()->syncWithoutDetaching([$role->id]);
        return back();
    }
    public function detachRole(User $user, Role $role)
    {
        $user->roles()->detach($role->id);
        return back();
    }
}
