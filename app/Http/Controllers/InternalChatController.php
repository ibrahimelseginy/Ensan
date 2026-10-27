<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\ChangeRequest;
use App\Models\Complaint;
use App\Models\InternalMessage;
use App\Models\MobileNotification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class InternalChatController extends Controller
{
    /**
     * Get active contacts and current user info with short TTL caching for maximum performance.
     */
    public function getContacts(): JsonResponse
    {
        $currentUserId = Auth::id();

        // Cache the raw contacts directory for 45 seconds to avoid repeated heavy queries
        $cachedUsers = Cache::remember('internal_chat_user_directory', 45, function () {
            return User::query()
                ->where(function ($q) {
                    $q->where('active', 1)
                      ->orWhereNull('active')
                      ->orWhere('is_employee', 1);
                })
                ->orderBy('name')
                ->get(['id', 'name', 'job_title', 'department', 'role', 'profile_photo_path', 'updated_at']);
        });

        // Fast indexed query for unread counts specifically for current user
        $unreadCounts = InternalMessage::query()
            ->where('receiver_id', $currentUserId)
            ->whereNull('read_at')
            ->selectRaw('sender_id, count(*) as unread_count')
            ->groupBy('sender_id')
            ->pluck('unread_count', 'sender_id')
            ->toArray();

        $contacts = $cachedUsers->filter(fn($u) => $u->id !== $currentUserId)->map(function ($u) use ($unreadCounts) {
            $avatar = null;
            if (!empty($u->profile_photo_path)) {
                $avatar = method_exists($u, 'getImageUrlAttribute') ? $u->image_url : asset('storage/' . $u->profile_photo_path);
            }

            return [
                'id' => $u->id,
                'name' => $u->name,
                'role' => $u->job_title ?: ($u->department ?: $u->role),
                'avatar' => $avatar,
                'initial' => mb_strtoupper(mb_substr($u->name, 0, 1)),
                'unread_count' => (int) ($unreadCounts[$u->id] ?? 0),
            ];
        })->values();

        $currentUser = Auth::user();
        $myAvatar = null;
        if ($currentUser && !empty($currentUser->profile_photo_path)) {
            $myAvatar = method_exists($currentUser, 'getImageUrlAttribute') ? $currentUser->image_url : asset('storage/' . $currentUser->profile_photo_path);
        }

        return response()->json([
            'success' => true,
            'current_user' => [
                'id' => $currentUser?->id,
                'name' => $currentUser?->name,
                'avatar' => $myAvatar,
                'initial' => $currentUser ? mb_strtoupper(mb_substr((string) ($currentUser->name ?? 'U'), 0, 1)) : 'U',
            ],
            'public_channel' => [
                'id' => 'public',
                'name' => 'القناة العامة للموظفين',
                'description' => 'مساحة تواصل مشتركة لجميع موظفي وفريق عمل إنسان',
                'unread_count' => 0,
            ],
            'contacts' => $contacts,
        ]);
    }

    /**
     * Retrieve messages incrementally using since_id for ultra-fast polling.
     */
    public function getMessages(Request $request): JsonResponse
    {
        $currentUserId = Auth::id();
        $contactId = $request->query('contact_id');
        $sinceId = (int) $request->query('since_id', 0);

        if (!empty($contactId) && $contactId !== 'public') {
            $contactId = (int) $contactId;

            $query = InternalMessage::query()
                ->betweenUsers($currentUserId, $contactId);

            if ($sinceId > 0) {
                // Incremental fetch: only messages newer than since_id
                $messages = $query
                    ->where('id', '>', $sinceId)
                    ->with(['sender:id,name,profile_photo_path'])
                    ->orderBy('id', 'asc')
                    ->limit(100)
                    ->get();
            } else {
                // Initial load: get latest 60 messages in ascending order
                $messages = $query
                    ->with(['sender:id,name,profile_photo_path'])
                    ->orderBy('id', 'desc')
                    ->limit(60)
                    ->get()
                    ->reverse()
                    ->values();
            }

            // Only mark newly fetched incoming messages as read if any exist
            if ($messages->isNotEmpty()) {
                $incomingIds = $messages
                    ->where('sender_id', $contactId)
                    ->where('receiver_id', $currentUserId)
                    ->whereNull('read_at')
                    ->pluck('id');

                if ($incomingIds->isNotEmpty()) {
                    InternalMessage::whereIn('id', $incomingIds)->update(['read_at' => now()]);
                }
            }
        } else {
            // Public channel with privacy targeting filter
            $query = InternalMessage::query()
                ->publicRoomForUser($currentUserId);

            if ($sinceId > 0) {
                $messages = $query
                    ->where('id', '>', $sinceId)
                    ->with(['sender:id,name,profile_photo_path'])
                    ->orderBy('id', 'asc')
                    ->limit(100)
                    ->get();
            } else {
                $messages = $query
                    ->with(['sender:id,name,profile_photo_path'])
                    ->orderBy('id', 'desc')
                    ->limit(60)
                    ->get()
                    ->reverse()
                    ->values();
            }
        }

        $formatted = $messages->map(fn($m) => $this->formatMessagePayload($m, $currentUserId));

        return response()->json([
            'success' => true,
            'messages' => $formatted,
            'last_id' => $messages->isNotEmpty() ? $messages->max('id') : $sinceId,
        ]);
    }

    /**
     * Send message with instant validation, attachments, audience targeting, and intelligent AI bot reply.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'nullable|string|max:3000',
            'attachment' => 'nullable|file|max:25600', // up to 25MB
            'sticker_code' => 'nullable|string|max:100',
            'sticker_title' => 'nullable|string|max:150',
            'contact_id' => 'nullable',
            'target_type' => 'nullable|string|in:all,only,exclude,direct',
            'visible_to_users' => 'nullable|array',
            'visible_to_users.*' => 'integer',
            'hidden_from_users' => 'nullable|array',
            'hidden_from_users.*' => 'integer',
        ]);

        $currentUserId = Auth::id();
        $currentUser = Auth::user();
        $rawContactId = $validated['contact_id'] ?? null;
        $receiverId = null;
        $targetType = 'all';
        $visibleToUsers = null;
        $hiddenFromUsers = null;

        // Attachment handling
        $attachmentPath = null;
        $attachmentType = null;
        $attachmentName = null;
        $attachmentSize = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $bytes = $file->getSize();
            $units = ['B', 'KB', 'MB', 'GB'];
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $attachmentSize = round($bytes / pow(1024, $pow), 1) . ' ' . ($units[$pow] ?? 'B');

            $mime = (string) $file->getMimeType();
            if (str_starts_with($mime, 'image/')) {
                $attachmentType = 'image';
                $attachmentPath = $file->store('internal_chat/images', 'public');
            } else {
                $attachmentType = 'file';
                $attachmentPath = $file->store('internal_chat/files', 'public');
            }
        } elseif (!empty($validated['sticker_code'])) {
            $attachmentType = 'sticker';
            $attachmentName = $validated['sticker_title'] ?? 'استيكر إنسان';
            $attachmentPath = $validated['sticker_code'];
        }

        $msgText = trim((string) ($validated['message'] ?? ''));
        if (empty($msgText) && empty($attachmentPath)) {
            return response()->json(['success' => false, 'message' => 'يرجى كتابة رسالة أو إرفاق ملف أو اختيار استيكر'], 422);
        }

        if (!empty($rawContactId) && $rawContactId !== 'public') {
            $receiverId = (int) $rawContactId;
            if ($receiverId === $currentUserId) {
                return response()->json(['success' => false, 'message' => 'لا يمكن إرسال رسالة لنفسك'], 422);
            }
            $targetType = 'direct';
        } else {
            $reqTarget = $validated['target_type'] ?? 'all';
            if ($reqTarget === 'only') {
                $targetType = 'only';
                $visibleToUsers = !empty($validated['visible_to_users']) ? array_values(array_unique(array_map('intval', $validated['visible_to_users']))) : [];
            } elseif ($reqTarget === 'exclude') {
                $targetType = 'exclude';
                $hiddenFromUsers = !empty($validated['hidden_from_users']) ? array_values(array_unique(array_map('intval', $validated['hidden_from_users']))) : [];
            } else {
                $targetType = 'all';
            }
        }

        $msg = InternalMessage::create([
            'sender_id' => $currentUserId,
            'receiver_id' => $receiverId,
            'is_bot' => false,
            'target_type' => $targetType,
            'visible_to_users' => $visibleToUsers,
            'hidden_from_users' => $hiddenFromUsers,
            'message' => $msgText,
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'attachment_name' => $attachmentName,
            'attachment_size' => $attachmentSize,
            'read_at' => null,
        ]);

        // Dispatch notifications in background
        $this->dispatchNotifications($msg, $currentUser, $receiverId, $targetType, $visibleToUsers, $attachmentType);

        // Check if an automated intelligent reply from Anis AI bot should be generated
        $botMessage = null;
        if (empty($receiverId) && !empty($msgText)) {
            $botReplyText = $this->generateBotReply($msgText, $currentUser);
            if ($botReplyText) {
                $botUser = $this->resolveBotUser();
                if ($botUser) {
                    $botMessage = InternalMessage::create([
                        'sender_id' => $botUser->id,
                        'receiver_id' => null,
                        'is_bot' => true,
                        'target_type' => 'all',
                        'message' => $botReplyText,
                        'read_at' => null,
                    ]);
                }
            }
        }

        $res = [
            'success' => true,
            'message' => $this->formatMessagePayload($msg, $currentUserId),
        ];

        if ($botMessage) {
            $res['bot_reply'] = $this->formatMessagePayload($botMessage, $currentUserId);
        }

        return response()->json($res);
    }

    /**
     * Get instant unread badge count using indexed lookup.
     */
    public function unreadCount(): JsonResponse
    {
        $currentUserId = Auth::id();

        $unreadCount = InternalMessage::query()
            ->where('receiver_id', $currentUserId)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Format message payload consistently for client consumption.
     */
    protected function formatMessagePayload(InternalMessage $m, int $currentUserId): array
    {
        $isBot = (bool) $m->is_bot;
        $isMe = ($m->sender_id === $currentUserId && !$isBot);
        $sender = $m->sender;

        $avatar = null;
        if ($isBot) {
            $senderName = 'أنيس | المساعد الذكي 🤖';
            $senderInitial = '🤖';
            $avatar = asset('images/heart-icon.png');
        } else {
            $senderName = $sender ? $sender->name : 'موظف';
            $senderInitial = $sender ? mb_strtoupper(mb_substr($sender->name, 0, 1)) : 'M';
            if ($sender && !empty($sender->profile_photo_path)) {
                $avatar = method_exists($sender, 'getImageUrlAttribute') ? $sender->image_url : asset('storage/' . $sender->profile_photo_path);
            }
        }

        $targetingBadge = null;
        if ($isBot) {
            $targetingBadge = [
                'type' => 'bot',
                'label' => 'رد ذكي آلي 🌟',
                'color' => 'success'
            ];
        } elseif ($m->isPublic()) {
            if ($m->target_type === 'only') {
                $count = count($m->visible_to_users ?? []);
                $targetingBadge = [
                    'type' => 'only',
                    'label' => 'مخصصة لأشخاص محددين 🔒' . ($isMe ? " ({$count})" : ''),
                    'color' => 'warning'
                ];
            } elseif ($m->target_type === 'exclude') {
                $count = count($m->hidden_from_users ?? []);
                $targetingBadge = [
                    'type' => 'exclude',
                    'label' => 'مخفية عن بعض الزملاء 👁️‍🗨️' . ($isMe ? " ({$count})" : ''),
                    'color' => 'secondary'
                ];
            }
        } else {
            $targetingBadge = [
                'type' => 'direct',
                'label' => 'رسالة خاصة ومباشرة 🔒',
                'color' => 'info'
            ];
        }

        return [
            'id' => $m->id,
            'is_bot' => $isBot,
            'sender_id' => $m->sender_id,
            'sender_name' => $senderName,
            'sender_avatar' => $avatar,
            'sender_initial' => $senderInitial,
            'is_me' => $isMe,
            'message' => $m->message,
            'attachment_path' => $m->attachment_path,
            'attachment_type' => $m->attachment_type,
            'attachment_name' => $m->attachment_name,
            'attachment_size' => $m->attachment_size,
            'attachment_url' => $m->attachment_url,
            'target_type' => $m->target_type,
            'targeting_badge' => $targetingBadge,
            'time' => $m->created_at->format('H:i'),
            'date' => $m->created_at->translatedFormat('d M'),
            'read_at' => $m->read_at ? $m->read_at->format('H:i') : null,
        ];
    }

    /**
     * Resolve a dedicated bot user entity.
     */
    protected function resolveBotUser(): ?User
    {
        return Cache::remember('ensan_bot_user_instance', 3600, function () {
            $bot = User::where('email', 'anis@ensaneg.com')->first();
            if ($bot) {
                return $bot;
            }
            return User::where('role', 'admin')->orWhere('is_admin', 1)->first() ?: User::first();
        });
    }

    /**
     * Intelligent context-aware AI bot reply generation.
     */
    protected function generateBotReply(string $text, $currentUser): ?string
    {
        $raw = trim($text);
        if (empty($raw)) {
            return null;
        }

        $clean = mb_strtolower($raw);
        $norm = str_replace(
            ['أ', 'إ', 'آ', 'ة', 'ى'],
            ['ا', 'ا', 'ا', 'ه', 'ي'],
            $clean
        );

        $isExplicitlyAddressed = str_contains($clean, 'أنيس')
            || str_contains($norm, 'انيس')
            || str_contains($norm, 'مساعد')
            || str_contains($clean, 'ai')
            || str_contains($norm, 'شات')
            || str_contains($norm, 'بوت')
            || str_starts_with($raw, '/')
            || str_starts_with($raw, '؟')
            || str_ends_with($raw, '؟')
            || str_contains($raw, '?');

        $isQuickPrompt = str_contains($norm, 'السلام عليكم')
            || str_contains($norm, 'صباح الخير')
            || str_contains($norm, 'مستجدات')
            || str_contains($norm, 'متابعه')
            || str_contains($norm, 'ميداني')
            || str_contains($norm, 'ضيافه')
            || str_contains($norm, 'حجز')
            || str_contains($norm, 'احصائي')
            || str_contains($norm, 'ارقام')
            || str_contains($norm, 'شكاو')
            || str_contains($norm, 'حالات')
            || str_contains($norm, 'مستفيد')
            || str_contains($norm, 'بعثاء الامل');

        if (!$isExplicitlyAddressed && !$isQuickPrompt) {
            return null;
        }

        $userName = $currentUser?->name ?? 'زميلي العزيز';

        // 1. Greetings
        if (str_contains($norm, 'السلام عليكم') || str_contains($norm, 'مرحبا') || str_contains($norm, 'اهلا')) {
            return "وعليكم السلام ورحمة الله وبركاته، أهلاً بك يا {$userName} 🌿 أتمنى لك يوماً موفقاً ومثمراً في خدمة ورعاية أهالينا. كيف يمكنني مساندتك اليوم؟";
        }
        if (str_contains($norm, 'صباح الخير') || str_contains($norm, 'صباح النور')) {
            return "صباح النور والبركة يا {$userName} ☀️ يوم عمل جديد مليء بالخير والعطاء إن شاء الله! جاهز لمساعدتك في أي استفسار أو إحصائيات.";
        }

        // 2. Daily updates and system stats
        if (str_contains($norm, 'مستجدات') || str_contains($norm, 'مهام') || str_contains($norm, 'احصائي') || str_contains($norm, 'ارقام') || str_contains($norm, 'تقرير')) {
            $beneficiariesCount = Beneficiary::count();
            $pendingComplaints = Complaint::whereIn('status', ['pending', 'in_progress', 'open'])->count();
            $pendingChanges = ChangeRequest::where('status', 'pending')->count();

            return "📋 **مستجدات نظام إنسان اليوم:**\n" .
                   "• إجمالي المستفيدين المسجلين: " . number_format($beneficiariesCount) . " حالة.\n" .
                   "• الشكاوى الجاري متابعتها: " . number_format($pendingComplaints) . " شكوى.\n" .
                   "• طلبات تعديل البيانات المعلقة: " . number_format($pendingChanges) . " طلب.\n" .
                   "فريق العمل جاهز لمساندة الحالات على مدار الساعة! 💪 هل تود تفاصيل عن قسم معين؟";
        }

        // 3. Guest House (Dar Al Diyafa)
        if (str_contains($norm, 'ضيافه') || str_contains($norm, 'حجز') || str_contains($norm, 'فندق') || str_contains($norm, 'سكن')) {
            return "🏨 **معلومات دار ضيافة إنسان لمرضى الأورام:**\n" .
                   "• فرع كفر الشيخ (بجوار معهد الأورام): 0473131335 / 01016649593\n" .
                   "• فرع الغربية (طنطا - شارع المديرية): 0403299166 / 01026017236\n" .
                   "• الخط الساخن الموحد: 01006090616\n" .
                   "• الإقامة والوجبات المجانية متاحة لجميع المرضى القادمين من خارج المحافظة، ورابط الحجز الإلكتروني: ensaneg.com/diyafa";
        }

        // 4. Beneficiaries & Cases
        if (str_contains($norm, 'حالات') || str_contains($norm, 'مستفيد') || str_contains($norm, 'بعثاء الامل')) {
            $count = Beneficiary::count();
            return "👥 **شؤون الحالات والمستفيدين:**\n" .
                   "• إجمالي الحالات المسجلة بقاعدة البيانات: " . number_format($count) . " حالة.\n" .
                   "• يمكنك مراجعة وتحديث بيانات أي حالة، إضافة أفراد الأسرة، أو توجيه زيارة بحث اجتماعي ميدانية عبر قسم المستفيدين من القائمة الجانبية.";
        }

        // 5. Complaints
        if (str_contains($norm, 'شكاو') || str_contains($norm, 'شكوي') || str_contains($norm, 'اقتراح')) {
            $count = Complaint::whereIn('status', ['pending', 'in_progress', 'open'])->count();
            return "⚠️ **متابعة الشكاوى والاقتراحات:**\n" .
                   "• يوجد حالياً " . number_format($count) . " شكوى جاري العمل على حلها والتحقق من تفاصيلها.\n" .
                   "• يمكن للزملاء الاطلاع على الشكاوى المسندة إليهم عبر بوابة الشكاوى، ويتم إرسال إشعار فوري لأي تحديث يطرأ على الحالة.";
        }

        // Default intelligent assistant reply
        return "أهلاً بك يا {$userName} 🌟 أنا **أنيس**، رفيق العمل الذكي في مؤسسة إنسان. أنا متواجد دائماً لمساعدتك في استخراج الإحصائيات، متابعة الشكاوى وطلبات التعديل، أو توجيهك في نظام العمل. كيف يمكنني خدمتك تحديداً؟";
    }

    /**
     * Dispatch in-system push notifications safely.
     */
    protected function dispatchNotifications($msg, $currentUser, $receiverId, $targetType, $visibleToUsers, $attachmentType): void
    {
        try {
            $previewText = !empty($msg->message)
                ? mb_substr($msg->message, 0, 100)
                : ($attachmentType === 'image' ? '📷 صورة مرفقة' : ($attachmentType === 'sticker' ? '✨ استيكر' : '📎 ملف مرفق'));

            $senderName = $currentUser?->name ?? 'موظف';

            if ($receiverId) {
                MobileNotification::create([
                    'title' => 'رسالة خاصة جديدة من ' . $senderName,
                    'body' => $previewText,
                    'category' => 'محادثات',
                    'target_audience' => 'user',
                    'target_type' => 'user',
                    'target_user_id' => $receiverId,
                    'is_sent' => true,
                    'sent_at' => now(),
                ]);
            } elseif ($targetType === 'only' && !empty($visibleToUsers)) {
                $currentUserId = $currentUser?->id;
                foreach ($visibleToUsers as $vUserId) {
                    if ($vUserId !== $currentUserId) {
                        MobileNotification::create([
                            'title' => 'رسالة موجهة لك من ' . $senderName,
                            'body' => $previewText,
                            'category' => 'محادثات',
                            'target_audience' => 'user',
                            'target_type' => 'user',
                            'target_user_id' => $vUserId,
                            'is_sent' => true,
                            'sent_at' => now(),
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silently ignore notification dispatch errors to never block message delivery
        }
    }
}
