<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\GuestHouse;
use App\Models\TaskAttachment;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class EmployeeTaskWebController extends Controller
{
    /**
     * Display Kanban Board & List of employee tasks.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user && $user->roles()->whereIn('key', ['admin', 'manager'])->exists();

        $selectedEmployee = $request->query('assigned_to');
        $selectedPriority = $request->query('priority');
        $selectedProject = $request->query('project_id');
        $search = $request->query('search');
        $viewMode = $request->query('view', 'board'); // 'board' or 'table'

        $query = Task::with([
            'assignee',
            'assignees',
            'assigner',
            'reviewer',
            'attachments.user',
            'logs.user',
            'project',
            'campaign',
            'guestHouse',
        ]);

        // Visibility Rule:
        // Admins can see all, or filter.
        // Regular employees can ONLY see their assigned tasks OR shared tasks they are part of!
        if (!$isAdmin) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('assigned_by', $user->id)
                  ->orWhereHas('assignees', fn($sq) => $sq->where('users.id', $user->id));
            });
        } elseif ($selectedEmployee) {
            $query->where(function ($q) use ($selectedEmployee) {
                $q->where('assigned_to', $selectedEmployee)
                  ->orWhereHas('assignees', fn($sq) => $sq->where('users.id', $selectedEmployee));
            });
        }

        if ($selectedPriority) {
            $query->where('priority', $selectedPriority);
        }

        if ($selectedProject) {
            $query->where('project_id', $selectedProject);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $allTasks = $query->orderBy('order')->orderByDesc('id')->get();

        // 4 Standard Stages for Trello Kanban Board:
        $stages = [
            'pending' => [
                'key' => 'pending',
                'title' => 'قائمة المهام',
                'subtitle' => 'مهام جديدة بانتظار البدء',
                'color' => '#64748b',
                'bg' => '#f1f5f9',
                'icon' => 'bi-clipboard2',
                'badge_class' => 'bg-secondary-subtle text-secondary',
                'tasks' => $allTasks->where('status', 'pending')->values(),
            ],
            'in_progress' => [
                'key' => 'in_progress',
                'title' => 'قيد التنفيذ',
                'subtitle' => 'مهام جاري العمل عليها الآن',
                'color' => '#3b82f6',
                'bg' => '#eff6ff',
                'icon' => 'bi-hourglass-split',
                'badge_class' => 'bg-primary-subtle text-primary',
                'tasks' => $allTasks->where('status', 'in_progress')->values(),
            ],
            'under_review' => [
                'key' => 'under_review',
                'title' => 'قيد المراجعة',
                'subtitle' => 'تم تسليمها وبانتظار التدقيق',
                'color' => '#f59e0b',
                'bg' => '#fffbeb',
                'icon' => 'bi-search',
                'badge_class' => 'bg-warning-subtle text-warning',
                'tasks' => $allTasks->where('status', 'under_review')->values(),
            ],
            'done' => [
                'key' => 'done',
                'title' => 'مكتملة ومراجعة',
                'subtitle' => 'تم إنجازها واعتمادها بنجاح',
                'color' => '#10b981',
                'bg' => '#ecfdf5',
                'icon' => 'bi-check2-circle',
                'badge_class' => 'bg-success-subtle text-success',
                'tasks' => $allTasks->where('status', 'done')->values(),
            ],
        ];

        // Overall stats
        $stats = [
            'total' => $allTasks->count(),
            'pending' => $stages['pending']['tasks']->count(),
            'in_progress' => $stages['in_progress']['tasks']->count(),
            'under_review' => $stages['under_review']['tasks']->count(),
            'done' => $stages['done']['tasks']->count(),
        ];

        $employees = User::where('is_employee', true)->orderBy('name')->get(['id', 'name', 'email']);
        $projects = Project::orderBy('name')->get(['id', 'name']);
        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get(['id', 'name', 'season_year']);
        $guestHouses = GuestHouse::orderBy('name')->get(['id', 'name']);

        return view('employee_tasks.index', compact(
            'stages',
            'allTasks',
            'stats',
            'isAdmin',
            'employees',
            'projects',
            'campaigns',
            'guestHouses',
            'viewMode',
            'selectedEmployee',
            'selectedPriority',
            'selectedProject',
            'search'
        ));
    }

    /**
     * Show create task page.
     */
    public function create(): View
    {
        $users = User::where('is_employee', true)->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = GuestHouse::orderBy('name')->get();

        return view('employee_tasks.create', compact('users', 'projects', 'campaigns', 'guestHouses'));
    }

    /**
     * Store a newly created task with assignees, attachments, and change log.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $data['assigned_by'] = $data['assigned_by'] ?? $user?->id;
        $data['status'] = $data['status'] ?? 'pending';
        $data['priority'] = $data['priority'] ?? 'medium';

        $sharedWith = $data['shared_with'] ?? [];
        $attachments = $request->file('attachments') ?? [];
        unset($data['shared_with'], $data['attachments']);

        $task = Task::create($data);

        // Attach shared team members
        if (!empty($sharedWith)) {
            $task->assignees()->sync($sharedWith);
        }

        // Handle uploaded attachments
        if (!empty($attachments)) {
            foreach ($attachments as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store("tasks/{$task->id}", 'public');
                    $task->attachments()->create([
                        'user_id' => $user?->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getClientMimeType(),
                    ]);
                }
            }
        }

        // Log creation activity
        $task->logActivity(
            'created',
            null,
            $task->status,
            'تم إنشاء المهمة وتعيينها بواسطة ' . ($user?->name ?? 'المسؤول')
        );

        return redirect()->route('employee-tasks.show', $task)->with('success', 'تم إنشاء المهمة بنجاح وإضافتها للوحة المهام');
    }

    /**
     * Show single task details, attachments, and full audit trail timeline.
     */
    public function show(Task $employee_task, Request $request): View
    {
        $user = $request->user();

        if (!$employee_task->canAccess($user)) {
            abort(403, 'عفواً، لا تملك صلاحية الوصول إلى هذه المهمة.');
        }

        $task = $employee_task->load([
            'assignee',
            'assignees',
            'assigner',
            'reviewer',
            'attachments.user',
            'logs.user',
            'project',
            'campaign',
            'guestHouse',
        ]);

        $isAdmin = $user && $user->roles()->whereIn('key', ['admin', 'manager'])->exists();
        $allEmployees = User::where('is_employee', true)->orderBy('name')->get();

        return view('employee_tasks.show', compact('task', 'isAdmin', 'allEmployees'));
    }

    /**
     * Show edit form.
     */
    public function edit(Task $employee_task, Request $request): View
    {
        $user = $request->user();
        if (!$employee_task->canAccess($user)) {
            abort(403, 'غير مصرح بتعديل هذه المهمة.');
        }

        $users = User::where('is_employee', true)->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = GuestHouse::orderBy('name')->get();

        return view('employee_tasks.edit', [
            'task' => $employee_task->load(['assignee', 'assignees', 'project', 'campaign', 'guestHouse']),
            'users' => $users,
            'projects' => $projects,
            'campaigns' => $campaigns,
            'guestHouses' => $guestHouses,
        ]);
    }

    /**
     * Update task details, status, or assignees.
     */
    public function update(UpdateTaskRequest $request, Task $employee_task): RedirectResponse
    {
        $user = $request->user();
        if (!$employee_task->canAccess($user)) {
            abort(403, 'غير مصرح بتعديل هذه المهمة.');
        }

        $data = $request->validated();
        $oldStatus = $employee_task->status;
        $newStatus = $data['status'] ?? $oldStatus;

        $sharedWith = $data['shared_with'] ?? null;
        $attachments = $request->file('attachments') ?? [];
        $statusNote = $data['status_note'] ?? null;
        unset($data['shared_with'], $data['attachments'], $data['status_note']);

        // Check completion / review metadata
        $isAdmin = $user && $user->roles()->whereIn('key', ['admin', 'manager'])->exists();
        if ($newStatus === 'done' && $oldStatus !== 'done') {
            $data['completed_at'] = now();
            if ($isAdmin) {
                $data['reviewed_by'] = $user->id;
                $data['reviewed_at'] = now();
            }
        }

        $employee_task->update($data);

        // Sync shared assignees if provided
        if (is_array($sharedWith)) {
            $employee_task->assignees()->sync($sharedWith);
        }

        // Upload new attachments if any
        if (!empty($attachments)) {
            foreach ($attachments as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store("tasks/{$employee_task->id}", 'public');
                    $attachment = $employee_task->attachments()->create([
                        'user_id' => $user?->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getClientMimeType(),
                    ]);

                    $employee_task->logActivity(
                        'attachment_added',
                        null,
                        null,
                        "تم إرفاق ملف: {$attachment->file_name} بواسطة {$user->name}"
                    );
                }
            }
        }

        // Log status change if status changed
        if ($newStatus !== $oldStatus) {
            $details = $statusNote ?? ("تم تغيير حالة المهمة بواسطة " . $user->name);
            if ($newStatus === 'done') {
                $details = $isAdmin
                    ? "تمت مراجعة واعتماد إنجاز المهمة (Done) بنجاح بواسطة " . $user->name
                    : "أكد الموظف {$user->name} اكتمال المهمة وجاهزيتها للمراجعة";
            } elseif ($newStatus === 'under_review') {
                $details = "قام {$user->name} بتسليم المهمة ونقلها إلى مرحلة المراجعة والتدقيق";
            }

            $employee_task->logActivity('status_changed', $oldStatus, $newStatus, $details);
        }

        return redirect()->route('employee-tasks.show', $employee_task)->with('success', 'تم تحديث المهمة بنجاح.');
    }

    /**
     * Quick status update endpoint (via Dropdown option or Kanban Drag-and-Drop).
     * Accessible by Assignee, Shared Collaborators, and Admins.
     */
    public function updateStatus(Request $request, Task $employee_task): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        if (!$employee_task->canAccess($user)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
            }
            abort(403, 'غير مصرح بتعديل حالة هذه المهمة.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,under_review,done',
            'status_note' => 'nullable|string|max:1000',
            'order' => 'nullable|integer',
        ]);

        $oldStatus = $employee_task->status;
        $newStatus = $validated['status'];
        $isAdmin = $user && $user->roles()->whereIn('key', ['admin', 'manager'])->exists();

        $updateData = ['status' => $newStatus];
        if (isset($validated['order'])) {
            $updateData['order'] = $validated['order'];
        }

        if ($newStatus === 'done' && $oldStatus !== 'done') {
            $updateData['completed_at'] = now();
            if ($isAdmin) {
                $updateData['reviewed_by'] = $user->id;
                $updateData['reviewed_at'] = now();
            }
        } elseif ($newStatus !== 'done') {
            $updateData['completed_at'] = null;
        }

        $employee_task->update($updateData);

        // Generate expressive audit trail message
        $note = $validated['status_note'] ?? null;
        $fromLabel = $employee_task->getStageLabelAttribute();

        if ($newStatus === 'done') {
            $details = $isAdmin
                ? ("تمت مراجعة واعتماد إنجاز المهمة (Done) بواسطة " . $user->name . ($note ? " — ملاحظات: {$note}" : ''))
                : ("قام الموظف {$user->name} بإنهاء المهمة ووضعها كمكتملة" . ($note ? " — ملاحظات: {$note}" : ''));
            $action = 'completed';
        } elseif ($newStatus === 'under_review') {
            $details = "قام {$user->name} بتقديم المهمة للمراجعة والتدقيق" . ($note ? " — ملاحظات: {$note}" : '');
            $action = 'reviewed';
        } else {
            $details = "قام {$user->name} بتحديث مرحلة المهمة إلى " . match($newStatus) {
                'pending' => 'قائمة المهام',
                'in_progress' => 'قيد التنفيذ',
                default => $newStatus
            } . ($note ? " — ملاحظات: {$note}" : '');
            $action = 'status_changed';
        }

        $employee_task->logActivity($action, $oldStatus, $newStatus, $details);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث مرحلة المهمة وتسجيل التغيير بنجاح.',
                'new_status' => $newStatus,
                'stage_label' => $employee_task->fresh()->stage_label,
            ]);
        }

        return redirect()->back()->with('success', 'تم تحديث مرحلة المهمة وتسجيل الإجراء في سجل التغييرات.');
    }

    /**
     * Upload an attachment to the task.
     */
    public function uploadAttachment(Request $request, Task $employee_task): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        if (!$employee_task->canAccess($user)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
            }
            abort(403);
        }

        $request->validate([
            'file' => 'required|file|max:25600', // up to 25MB
            'note' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $path = $file->store("tasks/{$employee_task->id}", 'public');

        $attachment = $employee_task->attachments()->create([
            'user_id' => $user->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
        ]);

        $employee_task->logActivity(
            'attachment_added',
            null,
            null,
            "قام {$user->name} بإرفاق ملف: {$attachment->file_name}" . ($request->note ? " ({$request->note})" : '')
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم رفع الملف بنجاح وتسجيله في سجل المهمة.',
                'attachment' => [
                    'id' => $attachment->id,
                    'name' => $attachment->file_name,
                    'size' => $attachment->formatted_size,
                    'url' => $attachment->url,
                    'is_image' => $attachment->is_image,
                    'uploader' => $user->name,
                    'created_at' => $attachment->created_at?->timezone('Africa/Cairo')->format('Y-m-d h:i A'),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'تم رفع الملف بنجاح وتسجيله في سجل المهمة.');
    }

    /**
     * Download task attachment.
     */
    public function downloadAttachment(Task $employee_task, TaskAttachment $attachment, Request $request): BinaryFileResponse
    {
        if (!$employee_task->canAccess($request->user())) {
            abort(403);
        }

        if ($attachment->task_id !== $employee_task->id) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'الملف غير موجود على الخادم.');
        }

        return response()->download(
            Storage::disk('public')->path($attachment->file_path),
            $attachment->file_name
        );
    }

    /**
     * Deletion is strictly prohibited to maintain permanent audit trail and accountability.
     * ("اهم حاجه ان المهام دي ما تكونش قابله للمسح من قبل حد")
     */
    public function destroy(Task $employee_task): RedirectResponse
    {
        return redirect()->back()->with('error', '🔒 عفواً، المهام وسجلاتها غير قابلة للحذف نهائياً في النظام لحفظ السجلات والشفافية التامة.');
    }
}
