@auth
<div id="ensanChatWidget" dir="rtl">
    <!-- Floating Welcome Speech Bubble from Anis on System Open -->
    <div id="ensanWelcomeBubble" class="ensan-welcome-bubble d-none shadow-lg">
        <button type="button" class="welcome-bubble-close" id="closeWelcomeBubbleBtn" title="إغلاق الترحيب" aria-label="إغلاق">
            <i class="bi bi-x-lg"></i>
        </button>
        <div class="d-flex align-items-center gap-2.5">
            <div class="welcome-avatar-wrap flex-shrink-0">
                <div class="welcome-avatar-circle">
                    <img src="{{ asset('images/heart-icon.png') }}" alt="Ensan" class="welcome-ensan-logo">
                </div>
            </div>
            <div class="welcome-bubble-content flex-grow-1">
                <div class="welcome-bubble-title">أهلاً بك يا {{ auth()->user()->name }} 🌿</div>
                <div class="welcome-bubble-text">أنا <strong>أنيس</strong> رفيق العمل في إنسان، جاهز لمساعدتك وتنسيق مهامك!</div>
            </div>
        </div>
        <div class="d-flex align-items-center justify-content-end gap-2 mt-2 pt-1 border-top border-light">
            <button type="button" id="welcomeStartChatBtn" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold" style="font-size: 0.74rem;">
                <i class="bi bi-chat-heart me-1"></i> فتح المحادثة
            </button>
            <button type="button" id="welcomeDismissBtn" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-muted" style="font-size: 0.72rem;">
                لاحقاً
            </button>
        </div>
    </div>

    <!-- Floating Launcher Button with Pure Ensan Logo Only -->
    <button type="button" id="ensanChatLauncher" class="ensan-chat-launcher" title="أنيس - رفيق العمل ومحادثات الفريق" aria-label="فتح محادثات إنسان">
        <span class="ensan-chat-pulse"></span>
        <div class="ensan-chat-launcher-icon">
            <!-- Pure Ensan Logo Only -->
            <img src="{{ asset('images/heart-icon.png') }}" alt="Ensan" class="ensan-launcher-heart">
            <svg class="close-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </div>
        <span id="ensanChatTotalBadge" class="ensan-chat-badge d-none">0</span>
    </button>

    <!-- Main Chat Window Card -->
    <div id="ensanChatBox" class="ensan-chat-box shadow-lg">
        <!-- Top App Header -->
        <div class="ensan-chat-header">
            <div class="d-flex align-items-center gap-2 overflow-hidden">
                <button type="button" id="backToContactsBtn" class="ensan-back-btn d-none" title="الرجوع لقائمة الزملاء">
                    <i class="bi bi-arrow-right"></i>
                </button>
                <div class="ensan-bot-avatar position-relative flex-shrink-0">
                    <div id="currentChatAvatar" class="avatar-circle ensan-avatar-heart-wrap">
                        <img src="{{ asset('images/heart-icon.png') }}" alt="Ensan" class="ensan-header-logo">
                    </div>
                    <span id="chatOnlineIndicator" class="online-indicator"></span>
                </div>
                <div class="chat-header-info overflow-hidden">
                    <h6 id="currentChatTitle" class="m-0 fw-bold text-white text-truncate">أنيس - رفيق العمل</h6>
                    <small id="currentChatSubtitle" class="text-white-50 text-truncate d-block" style="font-size: 0.74rem;">القناة العامة وتواصل فريق العمل</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                <button type="button" id="headerAudienceBadgeBtn" class="ensan-header-pill d-none" title="تعديل نطاق ظهور الرسائل">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span id="headerAudienceBadgeText">مخصصة</span>
                </button>
                <button type="button" id="minimizeChatBtn" class="ensan-btn-icon" title="تصغير الشات">
                    <i class="bi bi-dash-lg"></i>
                </button>
            </div>
        </div>

        <!-- Segmented Navigation Tabs -->
        <div id="ensanNavTabs" class="ensan-nav-tabs">
            <button type="button" class="ensan-tab-btn active" data-tab="channel">
                <i class="bi bi-megaphone-fill me-1"></i>
                <span>القناة العامة</span>
            </button>
            <button type="button" class="ensan-tab-btn position-relative" data-tab="contacts">
                <i class="bi bi-people-fill me-1"></i>
                <span>المحادثات الخاصة</span>
                <span id="contactsTabBadge" class="tab-unread-dot d-none"></span>
            </button>
        </div>

        <!-- View 1: Public Channel Stream -->
        <div id="channelView" class="ensan-chat-view active">
            <div id="channelMessagesFeed" class="ensan-messages-feed">
                <!-- Welcome Bot Intro Banner -->
                <div class="ensan-bot-welcome">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="bot-badge">
                            <i class="bi bi-person-heart text-success me-1"></i> أنيس | رفيق العمل
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                            <i class="bi bi-broadcast me-1"></i> بث مباشر للفريق
                        </span>
                    </div>
                    <p class="welcome-text">
                        مرحباً بك يا <strong>{{ auth()->user()->name }}</strong> 👋 في مساحة التواصل الداخلي لفريق إنسان. يمكنك التحدث للجميع أو تخصيص ظهور رسالتك ومشاركة الصور والمستندات والاستيكرات.
                    </p>

                    <!-- Quick Prompt Chips -->
                    <div class="prompt-chips">
                        <button type="button" class="prompt-chip" data-text="السلام عليكم ورحمة الله وبركاته، تحياتي للجميع 👋">
                            👋 السلام عليكم
                        </button>
                        <button type="button" class="prompt-chip" data-text="هل توجد أي مستجدات أو مهام اليوم؟ 📋">
                            📋 مستجدات اليوم
                        </button>
                        <button type="button" class="prompt-chip" data-text="تمت مراجعة الحالات والشكاوى وجاري المتابعة ✅">
                            ✅ متابعة الحالات
                        </button>
                        <button type="button" class="prompt-chip" data-text="برجاء التنسيق بخصوص حالات الزيارة الميدانية 📍">
                            📍 زيارة ميدانية
                        </button>
                    </div>
                </div>

                <div id="channelMessagesStream"></div>
                <div id="channelScrollAnchor"></div>
            </div>
        </div>

        <!-- View 2: Colleagues List / Direct Contacts Selector -->
        <div id="contactsView" class="ensan-chat-view">
            <div class="p-2.5 border-bottom bg-light bg-opacity-75">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="contactSearchInput" class="form-control border-start-0 ps-1" placeholder="ابحث باسم الموظف أو الوظيفة...">
                </div>
            </div>

            <div id="contactsListContainer" class="contacts-list-container">
                <div class="contacts-divider text-muted px-3 py-1.5 small fw-bold d-flex justify-content-between align-items-center">
                    <span>الزملاء في المؤسسة (محادثات مباشرة 1:1)</span>
                    <span id="contactsCountBadge" class="badge bg-secondary rounded-pill">0</span>
                </div>
                <div id="contactsListItems">
                    <div class="text-center py-5 text-muted small">
                        <span class="spinner-border spinner-border-sm text-success me-1"></span> جاري تحميل الزملاء...
                    </div>
                </div>
            </div>
        </div>

        <!-- View 3: 1-on-1 Direct Chat Stream -->
        <div id="directChatView" class="ensan-chat-view">
            <div id="directMessagesFeed" class="ensan-messages-feed">
                <!-- Direct Security Notice -->
                <div class="direct-security-banner">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-success fs-5"></i>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.78rem;">محادثة خاصة ومباشرة ومشفرة</div>
                            <div class="text-muted" style="font-size: 0.72rem;">هذه الرسائل تظهر فقط بينك وبين هذا الموظف مباشرة.</div>
                        </div>
                    </div>
                </div>

                <div id="directMessagesStream"></div>
                <div id="directScrollAnchor"></div>
            </div>
        </div>

        <!-- Audience Targeting Bar (Visible in Channel View) -->
        <div id="targetingBar" class="ensan-targeting-bar">
            <div class="d-flex align-items-center justify-content-between gap-2 w-100">
                <button type="button" id="openAudienceModalBtn" class="ensan-audience-pill" title="تحديد من يمكنه قراءة هذه الرسالة في القناة">
                    <i class="bi bi-sliders text-success"></i>
                    <span id="currentAudienceLabel">نطاق الظهور: للجميع 🌐</span>
                    <i class="bi bi-chevron-up ms-1" style="font-size: 0.65rem;"></i>
                </button>
                <span id="targetingActiveTag" class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle d-none" style="font-size: 0.7rem;">
                    مخصصة
                </span>
            </div>
        </div>

        <!-- Attachment Staging Preview (Appears when user selects a file before sending) -->
        <div id="attachmentPreviewBar" class="attachment-preview-bar d-none">
            <div class="d-flex align-items-center justify-content-between gap-2 w-100">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <div id="attachThumbBox" class="attach-thumb-box">
                        <i class="bi bi-file-earmark-text text-primary"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div id="attachFileName" class="text-truncate fw-bold text-dark" style="font-size: 0.76rem;">اسم الملف</div>
                        <div id="attachFileSize" class="text-muted" style="font-size: 0.68rem;">0 KB</div>
                    </div>
                </div>
                <button type="button" id="removeAttachmentBtn" class="btn btn-sm btn-light border rounded-circle py-0 px-1.5" title="إلغاء المرفق">
                    <i class="bi bi-x-lg" style="font-size: 0.72rem;"></i>
                </button>
            </div>
        </div>

        <!-- Emoji & Stickers Drawer Panel -->
        <div id="emojiStickersDrawer" class="emoji-stickers-drawer">
            <div class="p-2 border-bottom d-flex align-items-center justify-content-between bg-light">
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm emoji-drawer-tab active" data-drawer-tab="emojis">
                        😊 إيموجي
                    </button>
                    <button type="button" class="btn btn-sm emoji-drawer-tab" data-drawer-tab="stickers">
                        ✨ استيكرات إنسان
                    </button>
                </div>
                <button type="button" id="closeEmojiDrawerBtn" class="btn btn-sm btn-light rounded-circle py-0 px-2 fs-6">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Emojis Tab Content -->
            <div id="emojisTabContent" class="emoji-drawer-content p-2.5">
                <div class="emoji-category-title">التعبيرات السريعة والمشاعر</div>
                <div class="emoji-grid mb-3">
                    <button type="button" class="emoji-item">👍</button>
                    <button type="button" class="emoji-item">❤️</button>
                    <button type="button" class="emoji-item">🙏</button>
                    <button type="button" class="emoji-item">😂</button>
                    <button type="button" class="emoji-item">😊</button>
                    <button type="button" class="emoji-item">👏</button>
                    <button type="button" class="emoji-item">🔥</button>
                    <button type="button" class="emoji-item">💐</button>
                    <button type="button" class="emoji-item">🤝</button>
                    <button type="button" class="emoji-item">🎉</button>
                    <button type="button" class="emoji-item">✨</button>
                    <button type="button" class="emoji-item">💯</button>
                    <button type="button" class="emoji-item">🤲</button>
                    <button type="button" class="emoji-item">🌟</button>
                    <button type="button" class="emoji-item">💡</button>
                    <button type="button" class="emoji-item">☕</button>
                    <button type="button" class="emoji-item">💪</button>
                    <button type="button" class="emoji-item">😍</button>
                    <button type="button" class="emoji-item">👌</button>
                    <button type="button" class="emoji-item">🌸</button>
                </div>

                <div class="emoji-category-title">العمل والمهام الميدانية</div>
                <div class="emoji-grid">
                    <button type="button" class="emoji-item">📋</button>
                    <button type="button" class="emoji-item">📍</button>
                    <button type="button" class="emoji-item">✅</button>
                    <button type="button" class="emoji-item">⏳</button>
                    <button type="button" class="emoji-item">📞</button>
                    <button type="button" class="emoji-item">🏢</button>
                    <button type="button" class="emoji-item">📂</button>
                    <button type="button" class="emoji-item">📊</button>
                    <button type="button" class="emoji-item">📌</button>
                    <button type="button" class="emoji-item">🔔</button>
                    <button type="button" class="emoji-item">📝</button>
                    <button type="button" class="emoji-item">🗓️</button>
                    <button type="button" class="emoji-item">💼</button>
                    <button type="button" class="emoji-item">🎯</button>
                    <button type="button" class="emoji-item">🚀</button>
                    <button type="button" class="emoji-item">📦</button>
                </div>
            </div>

            <!-- Stickers Tab Content (WhatsApp-style rich Ensan stickers) -->
            <div id="stickersTabContent" class="emoji-drawer-content p-2.5 d-none">
                <div class="small text-muted mb-2">اضغط على أي استيكر لإرساله فوراً في المحادثة:</div>
                <div class="stickers-grid">
                    <div class="ensan-sticker-card" data-code="sticker_jazak" data-title="جزاكم الله خيراً 💚">
                        <div class="sticker-icon-circle bg-emerald-soft">💚</div>
                        <div class="sticker-title">جزاكم الله خيراً</div>
                        <div class="sticker-sub">تقبل الله طاعاتكم</div>
                    </div>
                    <div class="ensan-sticker-card" data-code="sticker_heroes" data-title="بوركت جهودكم يا أبطال 🌟">
                        <div class="sticker-icon-circle bg-amber-soft">🌟</div>
                        <div class="sticker-title">بوركت جهودكم يا أبطال</div>
                        <div class="sticker-sub">فريق إنسان المتميز</div>
                    </div>
                    <div class="ensan-sticker-card" data-code="sticker_approved" data-title="تم الاعتماد والإنجاز بنجاح ✅">
                        <div class="sticker-icon-circle bg-blue-soft">✅</div>
                        <div class="sticker-title">تم الاعتماد والإنجاز</div>
                        <div class="sticker-sub">جاهز للمرحلة التالية</div>
                    </div>
                    <div class="ensan-sticker-card" data-code="sticker_field" data-title="في الطريق للميدان / زيارة حالة 📍">
                        <div class="sticker-icon-circle bg-rose-soft">📍</div>
                        <div class="sticker-title">في الطريق للميدان</div>
                        <div class="sticker-sub">بحث وزيارة ميدانية</div>
                    </div>
                    <div class="ensan-sticker-card" data-code="sticker_mizan" data-title="في ميزان حسناتكم جميعاً 🤲">
                        <div class="sticker-icon-circle bg-emerald-soft">🤲</div>
                        <div class="sticker-title">في ميزان حسناتكم</div>
                        <div class="sticker-sub">أجر وعطاء مستمر</div>
                    </div>
                    <div class="ensan-sticker-card" data-code="sticker_morning" data-title="صباح الخير والبركة والتوفيق ☕">
                        <div class="sticker-icon-circle bg-amber-soft">☕</div>
                        <div class="sticker-title">صباح الخير والبركة</div>
                        <div class="sticker-sub">يوم موفق ومليء بالإنجاز</div>
                    </div>
                    <div class="ensan-sticker-card" data-code="sticker_following" data-title="جاري المتابعة والتدقيق فوراً ⏳">
                        <div class="sticker-icon-circle bg-indigo-soft">⏳</div>
                        <div class="sticker-title">جاري المتابعة فوراً</div>
                        <div class="sticker-sub">قيد الإجراء والمراجعة</div>
                    </div>
                    <div class="ensan-sticker-card" data-code="sticker_teamwork" data-title="فريق إنسان دائماً في الموعد 🤝">
                        <div class="sticker-icon-circle bg-teal-soft">🤝</div>
                        <div class="sticker-title">فريق إنسان في الموعد</div>
                        <div class="sticker-sub">معاً نصنع الأثر</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Input Footer -->
        <div class="ensan-chat-footer">
            <form id="ensanChatForm" class="d-flex align-items-center gap-1.5 m-0 w-100">
                <!-- Emoji Button -->
                <button type="button" id="toggleEmojiBtn" class="ensan-input-action-btn" title="إيموجي واستيكرات واتساب">
                    <i class="bi bi-emoji-smile"></i>
                </button>

                <!-- Attach File Button -->
                <button type="button" id="triggerAttachBtn" class="ensan-input-action-btn" title="إرفاق صورة أو مستند">
                    <i class="bi bi-paperclip"></i>
                </button>
                <input type="file" id="chatFileInput" class="d-none" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip">

                <!-- Text Input -->
                <input type="text" id="ensanChatInput" class="form-control ensan-chat-input" placeholder="اكتب رسالتك هنا... (Enter للإرسال)" autocomplete="off" maxlength="2500">

                <!-- Send Button -->
                <button type="submit" id="ensanSendBtn" class="ensan-send-btn" title="إرسال الرسالة">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>

        <!-- Audience Targeting Modal (Slide-up Panel inside ChatBox) -->
        <div id="audienceModal" class="audience-modal-panel">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-1 rounded-circle bg-success bg-opacity-15 text-success d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                        <i class="bi bi-eye-fill"></i>
                    </div>
                    <div>
                        <h6 class="m-0 fw-bold text-dark fs-6">نطاق ظهور الرسالة</h6>
                        <small class="text-muted" style="font-size: 0.72rem;">حدد لمن تظهر رسالتك أو من يتم حجبها عنه</small>
                    </div>
                </div>
                <button type="button" id="closeAudienceModalBtn" class="btn btn-sm btn-light rounded-circle py-0 px-2 fs-6">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="audience-modal-body p-3">
                <!-- Option 1: All -->
                <div class="audience-option-card mb-2 active" data-target="all">
                    <div class="d-flex align-items-start gap-2">
                        <input class="form-check-input mt-1" type="radio" name="targetTypeRadio" id="radioTargetAll" value="all" checked>
                        <label class="form-check-label flex-grow-1 cursor-pointer" for="radioTargetAll">
                            <div class="fw-bold text-dark" style="font-size: 0.84rem;">🌐 للجميع (افتراضي)</div>
                            <div class="text-muted" style="font-size: 0.74rem;">تظهر الرسالة لجميع أعضاء وموظفي الفريق في القناة العامة.</div>
                        </label>
                    </div>
                </div>

                <!-- Option 2: Only Specific Users -->
                <div class="audience-option-card mb-2" data-target="only">
                    <div class="d-flex align-items-start gap-2">
                        <input class="form-check-input mt-1" type="radio" name="targetTypeRadio" id="radioTargetOnly" value="only">
                        <label class="form-check-label flex-grow-1 cursor-pointer" for="radioTargetOnly">
                            <div class="fw-bold text-success" style="font-size: 0.84rem;">🎯 مخصصة (تظهر فقط لأشخاص محددين)</div>
                            <div class="text-muted" style="font-size: 0.74rem;">حدد موظفاً أو أكثر ليتمكنوا فقط من قراءة هذه الرسالة.</div>
                        </label>
                    </div>
                </div>

                <!-- Option 3: Exclude Specific Users -->
                <div class="audience-option-card mb-3" data-target="exclude">
                    <div class="d-flex align-items-start gap-2">
                        <input class="form-check-input mt-1" type="radio" name="targetTypeRadio" id="radioTargetExclude" value="exclude">
                        <label class="form-check-label flex-grow-1 cursor-pointer" for="radioTargetExclude">
                            <div class="fw-bold text-danger" style="font-size: 0.84rem;">🚫 استثناء (إخفاء عن أشخاص محددين)</div>
                            <div class="text-muted" style="font-size: 0.74rem;">تظهر للجميع مع حجبها عن الموظفين الذين تحددهم.</div>
                        </label>
                    </div>
                </div>

                <!-- Dynamic User Selector List -->
                <div id="audienceUsersListWrap" class="audience-users-wrap d-none">
                    <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                        <span id="audienceListTitle" class="fw-bold text-dark small">اختر الموظفين:</span>
                        <div class="d-flex gap-1.5">
                            <button type="button" id="selectAllAudienceUsersBtn" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill" style="font-size: 0.7rem;">تحديد الكل</button>
                            <button type="button" id="clearAllAudienceUsersBtn" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill" style="font-size: 0.7rem;">إلغاء التحديد</button>
                        </div>
                    </div>
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text bg-light border-end-0 py-0"><i class="bi bi-search text-muted" style="font-size: 0.75rem;"></i></span>
                        <input type="text" id="audienceSearchInput" class="form-control form-control-sm border-start-0 ps-1" placeholder="بحث في الموظفين..." style="font-size: 0.78rem;">
                    </div>
                    <div id="audienceUsersCheckboxes" class="audience-checkbox-scroll"></div>
                </div>
            </div>

            <div class="p-2.5 border-top bg-light d-flex align-items-center justify-content-end gap-2">
                <button type="button" id="resetAudienceModalBtn" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size: 0.78rem;">استعادة للجميع</button>
                <button type="button" id="applyAudienceModalBtn" class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-xs" style="font-size: 0.78rem;">تأكيد وحفظ</button>
            </div>
        </div>
    </div>
</div>

<style>
/* ── Modern Floating AI Chat Widget Styles ── */
#ensanChatWidget {
    position: fixed;
    bottom: 24px;
    left: 24px;
    z-index: 99990;
    font-family: inherit;
}

/* Floating Launcher Button with Ensan Logo + AI */
.ensan-chat-launcher {
    width: 62px;
    height: 62px;
    border-radius: 50%;
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    border: 2px solid rgba(255, 255, 255, 0.4);
    box-shadow: 0 8px 26px rgba(16, 185, 129, 0.45), 0 2px 8px rgba(0,0,0,0.12);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    position: relative;
    transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.25s ease;
    outline: none !important;
}

.ensan-chat-launcher:hover {
    transform: scale(1.08) translateY(-2px);
    box-shadow: 0 12px 32px rgba(16, 185, 129, 0.6), 0 4px 12px rgba(0,0,0,0.15);
}

.ensan-chat-launcher:active {
    transform: scale(0.96);
}

.ensan-chat-pulse {
    position: absolute;
    inset: -5px;
    border-radius: 50%;
    border: 2px solid rgba(16, 185, 129, 0.65);
    animation: ensanPulseRing 2.4s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
    pointer-events: none;
}

@keyframes ensanPulseRing {
    0% { transform: scale(0.95); opacity: 0.85; }
    50% { transform: scale(1.2); opacity: 0; }
    100% { transform: scale(1.2); opacity: 0; }
}

.ensan-chat-launcher-icon {
    width: 38px;
    height: 38px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ── Welcome Greeting Bubble on System Open ── */
.ensan-welcome-bubble {
    position: absolute;
    bottom: 82px;
    left: 0;
    width: 330px;
    max-width: calc(100vw - 36px);
    background: #ffffff;
    border-radius: 20px;
    padding: 14px 14px 10px 14px;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.2), 0 3px 12px rgba(16, 185, 129, 0.15);
    border: 1px solid rgba(16, 185, 129, 0.35);
    z-index: 99991;
    animation: welcomeSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    transition: opacity 0.3s ease, transform 0.3s ease;
}

@keyframes welcomeSlideUp {
    0% { opacity: 0; transform: translateY(16px) scale(0.94); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}

.welcome-bubble-close {
    position: absolute;
    top: 8px;
    left: 8px;
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 0.75rem;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
}
.welcome-bubble-close:hover {
    background: #f1f5f9;
    color: #475569;
}

.welcome-avatar-circle {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border: 2px solid #10b981;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}

.welcome-ensan-logo {
    width: 22px;
    height: 22px;
    object-fit: contain;
}

.welcome-person-emoji {
    position: absolute;
    bottom: -4px;
    right: -4px;
    font-size: 1.1rem;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
    animation: waveHand 2.2s infinite ease-in-out;
}

.welcome-bubble-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #065f46;
    margin-bottom: 2px;
}

.welcome-bubble-text {
    font-size: 0.78rem;
    color: #475569;
    line-height: 1.45;
}

/* Pure Ensan Heart Logo */
.ensan-launcher-heart {
    width: 33px;
    height: 33px;
    object-fit: contain;
    filter: brightness(0) invert(1) drop-shadow(0 2px 4px rgba(0,0,0,0.25));
    transition: transform 0.25s ease;
    display: block;
}

.ensan-chat-launcher:hover .ensan-launcher-heart {
    transform: scale(1.1);
}

.ensan-chat-launcher-icon .close-icon-svg {
    width: 26px;
    height: 26px;
    display: none;
    position: absolute;
}

.ensan-chat-widget-open .ensan-launcher-heart {
    display: none !important;
}

.ensan-chat-widget-open .ensan-chat-launcher-icon .close-icon-svg {
    display: block !important;
}

.ensan-chat-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background: #ef4444;
    color: #ffffff;
    font-size: 0.72rem;
    font-weight: 700;
    min-width: 20px;
    height: 20px;
    padding: 0 5px;
    border-radius: 10px;
    border: 2px solid #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(239, 68, 68, 0.5);
    animation: bounceIn 0.3s ease;
}

/* Chat Card Drawer */
.ensan-chat-box {
    position: absolute;
    bottom: 76px;
    left: 0;
    width: 400px;
    max-width: calc(100vw - 32px);
    height: 600px;
    max-height: calc(100vh - 100px);
    background: #ffffff;
    border-radius: 22px;
    box-shadow: 0 20px 50px rgba(15, 23, 42, 0.22), 0 4px 18px rgba(15, 23, 42, 0.08);
    border: 1px solid rgba(226, 232, 240, 0.9);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px) scale(0.96);
    transform-origin: bottom left;
    transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1), transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.25s;
    z-index: 99992;
}

.ensan-chat-widget-open .ensan-chat-box {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

/* Chat Header */
.ensan-chat-header {
    background: linear-gradient(135deg, #065f46 0%, #059669 60%, #10b981 100%);
    padding: 12px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
    position: relative;
    z-index: 5;
}

.ensan-back-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: #ffffff;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.15s ease;
    flex-shrink: 0;
}
.ensan-back-btn:hover {
    background: rgba(255, 255, 255, 0.35);
    transform: scale(1.06);
}

.ensan-bot-avatar .avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.22);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: #ffffff;
    font-size: 0.95rem;
    overflow: hidden;
    border: 2px solid rgba(255, 255, 255, 0.4);
    position: relative;
}

.ensan-header-logo {
    width: 26px;
    height: 26px;
    object-fit: contain;
    filter: brightness(0) invert(1);
}

.avatar-person-tag {
    position: absolute;
    bottom: -2px;
    right: -2px;
    background: #ffffff;
    font-size: 0.82rem;
    line-height: 1;
    border-radius: 50%;
    padding: 1px;
    border: 1.5px solid #10b981;
    box-shadow: 0 1px 4px rgba(0,0,0,0.2);
}

.avatar-circle img:not(.ensan-header-logo) {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.online-indicator {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #22c55e;
    border: 2px solid #ffffff;
}

.ensan-btn-icon {
    background: rgba(255, 255, 255, 0.15);
    border: none;
    color: #ffffff;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background 0.2s ease, transform 0.15s ease;
}

.ensan-btn-icon:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.05);
}

.ensan-header-pill {
    background: rgba(254, 240, 138, 0.25);
    border: 1px solid rgba(253, 224, 71, 0.5);
    color: #fef08a;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 20px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.2s ease;
}
.ensan-header-pill:hover {
    background: rgba(254, 240, 138, 0.4);
}

/* Nav Tabs */
.ensan-nav-tabs {
    display: flex;
    background: #f1f5f9;
    border-bottom: 1px solid #e2e8f0;
    padding: 4px 6px;
    gap: 4px;
}

.ensan-tab-btn {
    flex: 1;
    border: none;
    background: transparent;
    padding: 7px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.ensan-tab-btn:hover {
    color: #0f172a;
    background: rgba(255, 255, 255, 0.6);
}

.ensan-tab-btn.active {
    background: #ffffff;
    color: #059669;
    font-weight: 700;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
}

.tab-unread-dot {
    position: absolute;
    top: 5px;
    left: 8px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #ef4444;
    border: 1px solid #ffffff;
}

/* Chat Views Management */
.ensan-chat-view {
    display: none;
    flex: 1;
    flex-direction: column;
    overflow: hidden;
    min-height: 0;
}
.ensan-chat-view.active {
    display: flex;
}

/* Contacts View */
.contacts-list-container {
    flex: 1;
    overflow-y: auto;
    padding: 6px;
    background: #ffffff;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.18s ease;
    margin-bottom: 4px;
    border: 1px solid transparent;
}

.contact-item:hover {
    background: #f8fafc;
    border-color: #e2e8f0;
    transform: translateX(-2px);
}

.contact-item.active {
    background: #ecfdf5;
    border-color: #a7f3d0;
    border-right: 3px solid #10b981;
}

.contact-avatar-box .avatar-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: #475569;
    font-size: 0.88rem;
    overflow: hidden;
}

.contact-meta {
    flex: 1;
    min-width: 0;
}

.contact-name {
    font-size: 0.88rem;
    font-weight: 600;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.contact-role {
    font-size: 0.72rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    color: #64748b;
}

.contact-badge {
    background: #ef4444;
    color: #ffffff;
    font-size: 0.68rem;
    font-weight: 700;
    min-width: 18px;
    height: 18px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 5px;
}

/* Feed & Messages */
.ensan-messages-feed {
    flex: 1;
    overflow-y: auto;
    padding: 12px 14px;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-height: 0;
}

/* AI Bot Welcome Box */
.ensan-bot-welcome {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 12px 14px;
    margin-bottom: 4px;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
}

.bot-badge {
    display: inline-flex;
    align-items: center;
    background: #fef3c7;
    color: #92400e;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 20px;
}

.welcome-text {
    font-size: 0.8rem;
    color: #475569;
    line-height: 1.45;
    margin: 6px 0 10px 0;
}

.prompt-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.prompt-chip {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #334155;
    font-size: 0.72rem;
    padding: 4px 8px;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.prompt-chip:hover {
    background: #ecfdf5;
    border-color: #a7f3d0;
    color: #065f46;
}

/* Direct Chat Security Notice */
.direct-security-banner {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 12px;
    padding: 8px 12px;
    margin-bottom: 6px;
}

/* Message Rows */
.chat-msg-row {
    display: flex;
    gap: 8px;
    align-items: flex-end;
    animation: fadeIn 0.2s ease;
}

.chat-msg-row.outgoing {
    flex-direction: row-reverse;
}

.msg-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #cbd5e1;
    color: #334155;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.75rem;
    flex-shrink: 0;
    overflow: hidden;
}

.msg-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.msg-bubble-wrap {
    max-width: 82%;
    display: flex;
    flex-direction: column;
}

.msg-sender-name {
    font-size: 0.72rem;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 2px;
    padding: 0 4px;
}

.msg-bubble {
    padding: 9px 13px;
    border-radius: 16px;
    font-size: 0.85rem;
    line-height: 1.45;
    word-break: break-word;
    white-space: pre-wrap;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    position: relative;
}

.incoming .msg-bubble {
    background: #ffffff;
    color: #1e293b;
    border: 1px solid #e2e8f0;
    border-bottom-right-radius: 4px;
}

.outgoing .msg-bubble {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: #ffffff;
    border-bottom-left-radius: 4px;
}

.msg-target-tag {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 0.65rem;
    padding: 2px 6px;
    border-radius: 10px;
    margin-bottom: 4px;
    font-weight: 600;
}
.outgoing .msg-target-tag {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
}
.incoming .msg-target-tag {
    background: #fef3c7;
    color: #92400e;
}

/* AI Bot Message Bubble Special Styling */
.chat-msg-row.bot-msg {
    margin: 8px 0;
}
.chat-msg-row.bot-msg .msg-bubble {
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%) !important;
    border: 1.5px solid #10b981 !important;
    color: #064e3b !important;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.12) !important;
}
.chat-msg-row.bot-msg .msg-target-tag {
    background: #d1fae5 !important;
    color: #065f46 !important;
    font-weight: 700 !important;
}

/* Image Attachment inside Bubble */
.msg-attachment-image {
    max-width: 220px;
    max-height: 220px;
    border-radius: 12px;
    cursor: pointer;
    margin-top: 4px;
    display: block;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}
.msg-attachment-image:hover {
    transform: scale(1.02);
}

/* File Attachment Card */
.msg-attachment-file {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(0, 0, 0, 0.06);
    padding: 8px 12px;
    border-radius: 12px;
    margin-top: 5px;
    text-decoration: none !important;
    transition: background 0.15s ease;
}
.outgoing .msg-attachment-file {
    background: rgba(255, 255, 255, 0.18);
    color: #ffffff !important;
}
.incoming .msg-attachment-file {
    background: #f1f5f9;
    color: #1e293b !important;
}

.attach-file-icon {
    font-size: 1.4rem;
    line-height: 1;
}

/* Sticker Bubble */
.msg-sticker-bubble {
    background: #ffffff;
    border: 2px solid #a7f3d0;
    border-radius: 16px;
    padding: 10px 14px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.12);
    display: inline-block;
}
.outgoing .msg-sticker-bubble {
    border-color: rgba(255, 255, 255, 0.4);
    background: rgba(255, 255, 255, 0.95);
    color: #065f46;
}

.sticker-emoji-large {
    font-size: 2.2rem;
    line-height: 1;
    margin-bottom: 4px;
}
.sticker-caption {
    font-weight: 700;
    font-size: 0.84rem;
    color: #065f46;
}

.msg-time {
    font-size: 0.68rem;
    color: #94a3b8;
    margin-top: 3px;
    padding: 0 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.outgoing .msg-time {
    justify-content: flex-end;
}

/* Targeting Selector Bar */
.ensan-targeting-bar {
    background: #f8fafc;
    border-top: 1px solid #edf2f7;
    padding: 6px 12px;
    font-size: 0.74rem;
}

.ensan-audience-pill {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
    padding: 3px 10px;
    border-radius: 14px;
    font-size: 0.74rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s ease;
}
.ensan-audience-pill:hover {
    background: #f1f5f9;
    border-color: #10b981;
    color: #065f46;
}

/* Attachment Staging Preview */
.attachment-preview-bar {
    background: #eff6ff;
    border-top: 1px solid #bfdbfe;
    padding: 6px 12px;
}
.attach-thumb-box {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #ffffff;
    border: 1px solid #dbeafe;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    font-size: 1.1rem;
}
.attach-thumb-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Footer & Inputs */
.ensan-chat-footer {
    padding: 8px 10px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
}

.ensan-input-action-btn {
    background: transparent;
    border: none;
    color: #64748b;
    font-size: 1.25rem;
    line-height: 1;
    padding: 4px 6px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.18s ease;
    flex-shrink: 0;
}
.ensan-input-action-btn:hover {
    color: #059669;
    background: #f1f5f9;
    transform: scale(1.08);
}

.ensan-chat-input {
    border-radius: 24px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 8px 14px;
    font-size: 0.85rem;
    transition: all 0.2s ease;
}

.ensan-chat-input:focus {
    background: #ffffff;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
}

.ensan-send-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #059669, #10b981);
    color: #ffffff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: transform 0.15s ease, background 0.2s ease;
}

.ensan-send-btn:hover {
    transform: scale(1.08);
}

.ensan-send-btn:active {
    transform: scale(0.95);
}

/* Emoji & Stickers Drawer */
.emoji-stickers-drawer {
    position: absolute;
    bottom: 56px;
    left: 0;
    right: 0;
    height: 250px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    box-shadow: 0 -4px 18px rgba(0,0,0,0.08);
    z-index: 15;
    display: none;
    flex-direction: column;
}
.emoji-stickers-drawer.open {
    display: flex;
}

.emoji-drawer-tab {
    font-size: 0.76rem;
    font-weight: 600;
    color: #64748b;
    border-radius: 10px;
    padding: 4px 10px;
}
.emoji-drawer-tab.active {
    background: #e2e8f0;
    color: #0f172a;
    font-weight: 700;
}

.emoji-drawer-content {
    flex: 1;
    overflow-y: auto;
}

.emoji-category-title {
    font-size: 0.72rem;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 6px;
}

.emoji-grid {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 4px;
}

.emoji-item {
    background: transparent;
    border: none;
    font-size: 1.3rem;
    padding: 4px;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.12s ease, background 0.12s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}
.emoji-item:hover {
    background: #f1f5f9;
    transform: scale(1.2);
}

/* Stickers Grid */
.stickers-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
}

.ensan-sticker-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 8px;
    text-align: center;
    cursor: pointer;
    transition: all 0.18s ease;
}
.ensan-sticker-card:hover {
    background: #ecfdf5;
    border-color: #10b981;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.15);
}

.sticker-icon-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    margin: 0 auto 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
}
.bg-emerald-soft { background: #d1fae5; }
.bg-amber-soft { background: #fef3c7; }
.bg-blue-soft { background: #dbeafe; }
.bg-rose-soft { background: #ffe4e6; }
.bg-indigo-soft { background: #e0e7ff; }
.bg-teal-soft { background: #ccfbf1; }

.sticker-title {
    font-weight: 700;
    font-size: 0.76rem;
    color: #0f172a;
    line-height: 1.25;
}
.sticker-sub {
    font-size: 0.65rem;
    color: #64748b;
    margin-top: 2px;
}

/* Slide-up Audience Modal Panel */
.audience-modal-panel {
    position: absolute;
    inset: 0;
    background: #ffffff;
    z-index: 20;
    display: flex;
    flex-direction: column;
    transform: translateY(100%);
    transition: transform 0.26s cubic-bezier(0.16, 1, 0.3, 1);
}

.audience-modal-panel.open {
    transform: translateY(0);
}

.audience-modal-body {
    flex: 1;
    overflow-y: auto;
}

.audience-option-card {
    padding: 10px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #ffffff;
    transition: all 0.15s ease;
    cursor: pointer;
}
.audience-option-card:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}
.audience-option-card.active {
    border-color: #10b981;
    background: #ecfdf5;
}

.audience-users-wrap {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px;
}

.audience-checkbox-scroll {
    max-height: 160px;
    overflow-y: auto;
}

.audience-user-check-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 8px;
    border-radius: 8px;
    transition: background 0.12s ease;
    cursor: pointer;
}
.audience-user-check-item:hover {
    background: #f1f5f9;
}

.cursor-pointer {
    cursor: pointer;
}

@keyframes bounceIn {
    0% { transform: scale(0.3); opacity: 0; }
    50% { transform: scale(1.1); opacity: 1; }
    100% { transform: scale(1); }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes pulseGlow {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.2); opacity: 0.7; }
}

/* Responsive adjustment */
@media (max-width: 480px) {
    #ensanChatWidget {
        left: 14px;
        bottom: 16px;
    }
    .ensan-chat-box {
        width: calc(100vw - 28px);
        height: calc(100vh - 90px);
        bottom: 68px;
    }
}
</style>

<script>
(function() {
    // Current Chat State
    var currentView = 'channel'; // 'channel', 'contacts', 'direct'
    var currentContactId = 'public'; // 'public' or user_id integer
    var currentContactName = 'القناة العامة للموظفين';
    var currentContactRole = 'فريق عمل مؤسسة إنسان';
    var currentContactAvatar = '';
    var contactsData = [];
    var pollInterval = null;
    var badgePollInterval = null;
    var isOpen = false;
    var lastMessageCount = 0;

    // Staged Attachment State
    var stagedFile = null;

    // Audience Targeting State for Public Channel
    var currentAudienceTarget = 'all'; // 'all', 'only', 'exclude'
    var currentSelectedUserIds = []; // array of user IDs

    // Elements
    var widget = document.getElementById('ensanChatWidget');
    var launcher = document.getElementById('ensanChatLauncher');
    var chatBox = document.getElementById('ensanChatBox');
    var totalBadge = document.getElementById('ensanChatTotalBadge');
    var minimizeChatBtn = document.getElementById('minimizeChatBtn');
    var backToContactsBtn = document.getElementById('backToContactsBtn');
    var navTabs = document.getElementById('ensanNavTabs');
    var tabButtons = navTabs.querySelectorAll('.ensan-tab-btn');
    var contactsTabBadge = document.getElementById('contactsTabBadge');

    var channelView = document.getElementById('channelView');
    var contactsView = document.getElementById('contactsView');
    var directChatView = document.getElementById('directChatView');

    var currentChatTitle = document.getElementById('currentChatTitle');
    var currentChatSubtitle = document.getElementById('currentChatSubtitle');
    var currentChatAvatar = document.getElementById('currentChatAvatar');
    var headerAudienceBadgeBtn = document.getElementById('headerAudienceBadgeBtn');
    var headerAudienceBadgeText = document.getElementById('headerAudienceBadgeText');

    var channelMessagesFeed = document.getElementById('channelMessagesFeed');
    var channelMessagesStream = document.getElementById('channelMessagesStream');
    var directMessagesFeed = document.getElementById('directMessagesFeed');
    var directMessagesStream = document.getElementById('directMessagesStream');

    var contactSearchInput = document.getElementById('contactSearchInput');
    var contactsListItems = document.getElementById('contactsListItems');
    var contactsCountBadge = document.getElementById('contactsCountBadge');

    var targetingBar = document.getElementById('targetingBar');
    var openAudienceModalBtn = document.getElementById('openAudienceModalBtn');
    var currentAudienceLabel = document.getElementById('currentAudienceLabel');
    var targetingActiveTag = document.getElementById('targetingActiveTag');

    var attachmentPreviewBar = document.getElementById('attachmentPreviewBar');
    var attachThumbBox = document.getElementById('attachThumbBox');
    var attachFileName = document.getElementById('attachFileName');
    var attachFileSize = document.getElementById('attachFileSize');
    var removeAttachmentBtn = document.getElementById('removeAttachmentBtn');

    var triggerAttachBtn = document.getElementById('triggerAttachBtn');
    var chatFileInput = document.getElementById('chatFileInput');

    var toggleEmojiBtn = document.getElementById('toggleEmojiBtn');
    var emojiStickersDrawer = document.getElementById('emojiStickersDrawer');
    var closeEmojiDrawerBtn = document.getElementById('closeEmojiDrawerBtn');
    var emojiDrawerTabs = document.querySelectorAll('.emoji-drawer-tab');
    var emojisTabContent = document.getElementById('emojisTabContent');
    var stickersTabContent = document.getElementById('stickersTabContent');

    var audienceModal = document.getElementById('audienceModal');
    var closeAudienceModalBtn = document.getElementById('closeAudienceModalBtn');
    var audienceOptionCards = audienceModal.querySelectorAll('.audience-option-card');
    var audienceUsersListWrap = document.getElementById('audienceUsersListWrap');
    var audienceUsersCheckboxes = document.getElementById('audienceUsersCheckboxes');
    var audienceSearchInput = document.getElementById('audienceSearchInput');
    var selectAllAudienceUsersBtn = document.getElementById('selectAllAudienceUsersBtn');
    var clearAllAudienceUsersBtn = document.getElementById('clearAllAudienceUsersBtn');
    var applyAudienceModalBtn = document.getElementById('applyAudienceModalBtn');
    var resetAudienceModalBtn = document.getElementById('resetAudienceModalBtn');

    var chatForm = document.getElementById('ensanChatForm');
    var chatInput = document.getElementById('ensanChatInput');
    var sendBtn = document.getElementById('ensanSendBtn');

    // Subtle audio chime
    function playNotificationSound() {
        try {
            var audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            var osc = audioCtx.createOscillator();
            var gain = audioCtx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
            osc.frequency.exponentialRampToValueAtTime(880, audioCtx.currentTime + 0.15); // A5
            gain.gain.setValueAtTime(0.12, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.3);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.3);
        } catch (e) {}
    }

    // Toggle Chat
    launcher.addEventListener('click', function() {
        toggleChat();
    });

    minimizeChatBtn.addEventListener('click', function() {
        closeChat();
    });

    function toggleChat() {
        if (isOpen) {
            closeChat();
        } else {
            openChat();
        }
    }

    function openChat() {
        if (typeof hideWelcomeGreeting === 'function') hideWelcomeGreeting();
        isOpen = true;
        widget.classList.add('ensan-chat-widget-open');
        loadContacts();
        switchView(currentView || 'channel');
        startPolling();
        setTimeout(function() { chatInput.focus(); }, 200);
    }

    function closeChat() {
        isOpen = false;
        widget.classList.remove('ensan-chat-widget-open');
        audienceModal.classList.remove('open');
        emojiStickersDrawer.classList.remove('open');
        stopPolling();
    }

    // Tab Navigation
    tabButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var tab = this.getAttribute('data-tab');
            if (tab === 'channel') {
                switchToChannel();
            } else if (tab === 'contacts') {
                switchToContacts();
            }
        });
    });

    backToContactsBtn.addEventListener('click', function() {
        switchToContacts();
    });

    function switchView(viewName) {
        currentView = viewName;
        emojiStickersDrawer.classList.remove('open');

        channelView.classList.remove('active');
        contactsView.classList.remove('active');
        directChatView.classList.remove('active');

        if (viewName === 'channel') {
            channelView.classList.add('active');
            navTabs.classList.remove('d-none');
            backToContactsBtn.classList.add('d-none');
            targetingBar.classList.remove('d-none');
            updateNavTabButtons('channel');

            currentContactId = 'public';
            currentChatTitle.textContent = 'أنيس - رفيق العمل';
            currentChatSubtitle.textContent = 'القناة العامة وتواصل فريق العمل';
            currentChatAvatar.innerHTML = '<img src="{{ asset('images/heart-icon.png') }}" alt="Ensan" class="ensan-header-logo">';
            headerAudienceBadgeBtn.classList.toggle('d-none', currentAudienceTarget === 'all');

            loadMessages();
        } else if (viewName === 'contacts') {
            contactsView.classList.add('active');
            navTabs.classList.remove('d-none');
            backToContactsBtn.classList.add('d-none');
            targetingBar.classList.add('d-none');
            updateNavTabButtons('contacts');

            currentChatTitle.textContent = 'محادثات الزملاء المباشرة';
            currentChatSubtitle.textContent = 'اختر زميلاً لبدء محادثة خاصة (1:1)';
            currentChatAvatar.innerHTML = '<i class="bi bi-people-fill text-white fs-6"></i>';
            headerAudienceBadgeBtn.classList.add('d-none');
        } else if (viewName === 'direct') {
            directChatView.classList.add('active');
            navTabs.classList.add('d-none');
            backToContactsBtn.classList.remove('d-none');
            targetingBar.classList.add('d-none');

            currentChatTitle.textContent = currentContactName;
            currentChatSubtitle.textContent = currentContactRole || 'محادثة خاصة ومباشرة 🔒';
            if (currentContactAvatar) {
                currentChatAvatar.innerHTML = '<img src="' + currentContactAvatar + '" alt="">';
            } else {
                currentChatAvatar.textContent = mbInitial(currentContactName);
            }
            headerAudienceBadgeBtn.classList.add('d-none');

            loadMessages();
        }
    }

    function switchToChannel() {
        switchView('channel');
    }

    function switchToContacts() {
        switchView('contacts');
        renderContacts(contactSearchInput.value.trim().toLowerCase());
    }

    function updateNavTabButtons(activeTab) {
        tabButtons.forEach(function(btn) {
            btn.classList.toggle('active', btn.getAttribute('data-tab') === activeTab);
        });
    }

    // Quick prompt chips
    document.querySelectorAll('.prompt-chip').forEach(function(chip) {
        chip.addEventListener('click', function() {
            var text = this.getAttribute('data-text');
            chatInput.value = text;
            chatInput.focus();
        });
    });

    function debounce(fn, delay) {
        var timer = null;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function() {
                fn.apply(context, args);
            }, delay);
        };
    }

    // Search contacts filter with debouncing
    contactSearchInput.addEventListener('input', debounce(function() {
        var query = this.value.trim().toLowerCase();
        renderContacts(query);
    }, 150));

    // Load Contacts from server
    function loadContacts() {
        fetch('{{ route("internal-chat.contacts") }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                contactsData = data.contacts || [];
                contactsCountBadge.textContent = contactsData.length;
                renderContacts();
                updateTotalBadge();
                renderAudienceCheckboxes();
            }
        })
        .catch(function(err) {
            console.error('Error fetching contacts', err);
        });
    }

    // Render Contacts list
    function renderContacts(filterQuery) {
        var filtered = contactsData;
        if (filterQuery) {
            filtered = contactsData.filter(function(c) {
                return (c.name && c.name.toLowerCase().includes(filterQuery)) ||
                       (c.role && c.role.toLowerCase().includes(filterQuery));
            });
        }

        if (filtered.length === 0) {
            contactsListItems.innerHTML = '<div class="text-center py-5 text-muted small"><i class="bi bi-person-x fs-2 d-block mb-1 opacity-50"></i> لم يتم العثور على زملاء مطابقين</div>';
            return;
        }

        var html = '';
        filtered.forEach(function(c) {
            var activeClass = (String(currentContactId) === String(c.id) && currentView === 'direct') ? ' active' : '';
            var badgeHtml = c.unread_count > 0 ? '<span class="contact-badge">' + c.unread_count + '</span>' : '';
            var avatarHtml = c.avatar 
                ? '<img src="' + c.avatar + '" alt="" onerror="this.style.display=\'none\';">' + c.initial
                : c.initial;

            html += '<div class="contact-item' + activeClass + '" data-id="' + c.id + '" data-name="' + escapeHtml(c.name) + '" data-role="' + escapeHtml(c.role || '') + '" data-avatar="' + (c.avatar || '') + '">';
            html += '  <div class="contact-avatar-box position-relative">';
            html += '    <div class="avatar-circle">' + avatarHtml + '</div>';
            html += '  </div>';
            html += '  <div class="contact-meta">';
            html += '    <div class="d-flex justify-content-between align-items-center">';
            html += '      <span class="contact-name">' + escapeHtml(c.name) + '</span>';
            html +=        badgeHtml;
            html += '    </div>';
            html += '    <small class="contact-role text-muted">' + escapeHtml(c.role || 'موظف بمؤسسة إنسان') + '</small>';
            html += '  </div>';
            html += '</div>';
        });

        contactsListItems.innerHTML = html;

        contactsListItems.querySelectorAll('.contact-item').forEach(function(item) {
            item.addEventListener('click', function() {
                var cId = this.getAttribute('data-id');
                var cName = this.getAttribute('data-name');
                var cRole = this.getAttribute('data-role');
                var cAvatar = this.getAttribute('data-avatar');
                startDirectChat(cId, cName, cRole, cAvatar);
            });
        });
    }

    function startDirectChat(id, name, role, avatar) {
        currentContactId = id;
        currentContactName = name;
        currentContactRole = role;
        currentContactAvatar = avatar;

        var found = contactsData.find(function(c) { return String(c.id) === String(id); });
        if (found) {
            found.unread_count = 0;
            updateTotalBadge();
        }

        switchView('direct');
        chatInput.placeholder = 'رسالة خاصة إلى ' + name + '...';
        chatInput.focus();
    }

    // In-Memory Conversation Cache for 0ms instantaneous switching
    var conversationStore = {}; // { [contactId]: { messages: [], lastId: 0, loaded: false } }

    function getStore(id) {
        var key = String(id || 'public');
        if (!conversationStore[key]) {
            conversationStore[key] = { messages: [], lastId: 0, loaded: false };
        }
        return conversationStore[key];
    }

    // Load Messages with incremental polling (since_id)
    function loadMessages(isPolling) {
        var cid = String(currentContactId || 'public');
        var store = getStore(cid);
        var targetStream = (currentView === 'direct') ? directMessagesStream : channelMessagesStream;

        // If not polling and we already have cached messages, display them immediately with zero delay
        if (!isPolling && store.loaded && store.messages.length > 0) {
            renderMessagesList(store.messages, targetStream);
            scrollToBottom();
        }

        var url = '{{ route("internal-chat.messages") }}?contact_id=' + encodeURIComponent(cid);
        if (isPolling && store.lastId > 0) {
            url += '&since_id=' + store.lastId;
        }

        fetch(url, { headers: { 'Accept': 'application/json' } })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (!data.success) return;

            var newItems = data.messages || [];

            if (!isPolling || !store.loaded) {
                // Initial load: render complete set
                store.messages = newItems;
                store.lastId = data.last_id || (newItems.length ? newItems[newItems.length - 1].id : 0);
                store.loaded = true;
                renderMessagesList(store.messages, targetStream);
                scrollToBottom();
            } else if (newItems.length > 0) {
                // Incremental poll: append ONLY newly arrived messages without wiping DOM!
                var hadIncoming = false;
                newItems.forEach(function(m) {
                    if (!store.messages.some(function(ex) { return ex.id === m.id; })) {
                        store.messages.push(m);
                        appendMessageToStream(m, targetStream);
                        if (!m.is_me) hadIncoming = true;
                    }
                });
                store.lastId = Math.max(store.lastId, data.last_id || 0);

                var feed = (currentView === 'direct') ? directMessagesFeed : channelMessagesFeed;
                var isNearBottom = (feed.scrollHeight - feed.scrollTop - feed.clientHeight) < 160;
                if (isNearBottom) {
                    scrollToBottom();
                }

                if (hadIncoming) {
                    playNotificationSound();
                }
            }
        })
        .catch(function(err) {
            console.error('Error fetching messages', err);
        });
    }

    function renderMessagesList(messages, container) {
        if (!messages || messages.length === 0) {
            var emptyMsg = (currentView === 'direct') 
                ? 'لا توجد رسائل خاصة سابقة مع ' + escapeHtml(currentContactName) + '. ابدأ المحادثة المباشرة الآن! 🔒'
                : 'لا توجد رسائل سابقة في القناة العامة. شارك فريقك الآن! 📢';
            container.innerHTML = '<div class="text-center py-5 text-muted small"><i class="bi bi-chat-heart fs-1 d-block mb-2 text-success opacity-40"></i> ' + emptyMsg + '</div>';
            return;
        }

        var html = '';
        messages.forEach(function(m) {
            html += buildMessageHtml(m);
        });
        container.innerHTML = html;
    }

    function appendMessageToStream(m, targetStream) {
        var container = targetStream || ((currentView === 'direct') ? directMessagesStream : channelMessagesStream);
        var emptyNotice = container.querySelector('.text-muted');
        if (emptyNotice) {
            emptyNotice.remove();
        }
        var div = document.createElement('div');
        div.innerHTML = buildMessageHtml(m);
        while (div.firstChild) {
            container.appendChild(div.firstChild);
        }
    }

    function appendMessage(m) {
        appendMessageToStream(m);
    }

    function buildMessageHtml(m) {
        var isBot = !!m.is_bot;
        var directionClass = isBot ? 'incoming bot-msg' : (m.is_me ? 'outgoing' : 'incoming');

        var avatarContent;
        if (isBot) {
            avatarContent = '<div class="avatar-circle ensan-avatar-heart-wrap" style="width:28px;height:28px;background:#ecfdf5;border:1px solid #10b981;"><span style="font-size:1rem;">🙋‍♂️</span></div>';
        } else {
            avatarContent = m.sender_avatar
                ? '<img src="' + m.sender_avatar + '" alt="" onerror="this.parentElement.textContent=\'' + (m.sender_initial || 'M') + '\';">'
                : (m.sender_initial || 'M');
        }

        var senderNameHtml;
        if (isBot) {
            senderNameHtml = '<span class="msg-sender-name text-success fw-bold"><i class="bi bi-person-heart text-success me-1"></i> ' + escapeHtml(m.sender_name) + '</span>';
        } else if (!m.is_me) {
            senderNameHtml = '<span class="msg-sender-name">' + escapeHtml(m.sender_name) + '</span>';
        } else {
            senderNameHtml = '';
        }

        var checksHtml = m.is_me ? '<i class="bi bi-check2-all text-white-50 ms-1" style="font-size:0.75rem;"></i>' : '';

        var targetingTagHtml = '';
        if (m.targeting_badge) {
            targetingTagHtml = '<div class="msg-target-tag"><i class="bi bi-shield-lock-fill"></i> ' + escapeHtml(m.targeting_badge.label) + '</div>';
        }

        // Attachment rendering
        var attachmentHtml = '';
        if (m.attachment_type === 'image' && m.attachment_url) {
            attachmentHtml = '<a href="' + m.attachment_url + '" target="_blank" rel="noopener"><img src="' + m.attachment_url + '" alt="صورة مرفقة" class="msg-attachment-image"></a>';
        } else if (m.attachment_type === 'file' && m.attachment_url) {
            var iconClass = 'bi-file-earmark-text';
            if (m.attachment_name && m.attachment_name.endsWith('.pdf')) iconClass = 'bi-file-earmark-pdf text-danger';
            else if (m.attachment_name && (m.attachment_name.endsWith('.xls') || m.attachment_name.endsWith('.xlsx'))) iconClass = 'bi-file-earmark-excel text-success';
            else if (m.attachment_name && (m.attachment_name.endsWith('.doc') || m.attachment_name.endsWith('.docx'))) iconClass = 'bi-file-earmark-word text-primary';

            attachmentHtml = '<a href="' + m.attachment_url + '" target="_blank" download class="msg-attachment-file">' +
                '<i class="bi ' + iconClass + ' attach-file-icon"></i>' +
                '<div class="flex-grow-1 overflow-hidden">' +
                '  <div class="text-truncate fw-bold" style="font-size:0.78rem;">' + escapeHtml(m.attachment_name || 'ملف مرفق') + '</div>' +
                '  <small style="font-size:0.65rem;opacity:0.8;">' + (m.attachment_size || 'تحميل') + '</small>' +
                '</div>' +
                '<i class="bi bi-arrow-down-circle fs-6"></i>' +
                '</a>';
        } else if (m.attachment_type === 'sticker') {
            attachmentHtml = '<div class="msg-sticker-bubble">' +
                '<div class="sticker-emoji-large">' + getStickerEmoji(m.attachment_path) + '</div>' +
                '<div class="sticker-caption">' + escapeHtml(m.attachment_name || 'استيكر') + '</div>' +
                '</div>';
        }

        var textContent = m.message ? '<div>' + escapeHtml(m.message) + '</div>' : '';

        var html = '<div class="chat-msg-row ' + directionClass + '">';
        if (!m.is_me) {
            html += '<div class="msg-avatar">' + avatarContent + '</div>';
        }
        html += '  <div class="msg-bubble-wrap">';
        html +=      senderNameHtml;
        html += '    <div class="msg-bubble">';
        html +=        targetingTagHtml;
        html +=        attachmentHtml;
        html +=        textContent;
        html += '    </div>';
        html += '    <div class="msg-time">';
        html += '      <span>' + m.time + '</span>';
        html +=        checksHtml;
        html += '    </div>';
        html += '  </div>';
        html += '</div>';

        return html;
    }

    function getStickerEmoji(code) {
        var map = {
            'sticker_jazak': '💚',
            'sticker_heroes': '🌟',
            'sticker_approved': '✅',
            'sticker_field': '📍',
            'sticker_mizan': '🤲',
            'sticker_morning': '☕',
            'sticker_following': '⏳',
            'sticker_teamwork': '🤝'
        };
        return map[code] || '✨';
    }

    function scrollToBottom() {
        setTimeout(function() {
            if (currentView === 'direct') {
                directMessagesFeed.scrollTop = directMessagesFeed.scrollHeight;
            } else {
                channelMessagesFeed.scrollTop = channelMessagesFeed.scrollHeight;
            }
        }, 50);
    }

    // File Attachment Upload Handlers
    triggerAttachBtn.addEventListener('click', function() {
        chatFileInput.click();
    });

    chatFileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            stagedFile = this.files[0];
            showAttachmentPreview(stagedFile);
        }
    });

    removeAttachmentBtn.addEventListener('click', function() {
        clearStagedAttachment();
    });

    function showAttachmentPreview(file) {
        attachmentPreviewBar.classList.remove('d-none');
        attachFileName.textContent = file.name;
        var bytes = file.size;
        var units = ['B', 'KB', 'MB', 'GB'];
        var pow = floorSafe((bytes ? Math.log(bytes) : 0) / Math.log(1024));
        attachFileSize.textContent = (bytes / Math.pow(1024, pow)).toFixed(1) + ' ' + (units[pow] || 'B');

        if (file.type.startsWith('image/')) {
            var reader = new FileReader();
            reader.onload = function(e) {
                attachThumbBox.innerHTML = '<img src="' + e.target.result + '" alt="">';
            };
            reader.readAsDataURL(file);
        } else {
            attachThumbBox.innerHTML = '<i class="bi bi-file-earmark-text text-primary"></i>';
        }
        chatInput.focus();
    }

    function clearStagedAttachment() {
        stagedFile = null;
        chatFileInput.value = '';
        attachmentPreviewBar.classList.add('d-none');
    }

    function floorSafe(val) {
        return Math.floor(Math.max(0, val));
    }

    // Emoji & Stickers Drawer Handlers
    toggleEmojiBtn.addEventListener('click', function() {
        emojiStickersDrawer.classList.toggle('open');
    });

    closeEmojiDrawerBtn.addEventListener('click', function() {
        emojiStickersDrawer.classList.remove('open');
    });

    emojiDrawerTabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var target = this.getAttribute('data-drawer-tab');
            emojiDrawerTabs.forEach(function(t) { t.classList.toggle('active', t === tab); });
            if (target === 'emojis') {
                emojisTabContent.classList.remove('d-none');
                stickersTabContent.classList.add('d-none');
            } else {
                emojisTabContent.classList.add('d-none');
                stickersTabContent.classList.remove('d-none');
            }
        });
    });

    // Clicking an emoji appends it to input
    document.querySelectorAll('.emoji-item').forEach(function(item) {
        item.addEventListener('click', function() {
            var emoji = this.textContent.trim();
            chatInput.value += emoji;
            chatInput.focus();
        });
    });

    // Clicking a sticker sends it immediately
    document.querySelectorAll('.ensan-sticker-card').forEach(function(card) {
        card.addEventListener('click', function() {
            var code = this.getAttribute('data-code');
            var title = this.getAttribute('data-title');
            sendStickerMessage(code, title);
            emojiStickersDrawer.classList.remove('open');
        });
    });

    function sendStickerMessage(code, title) {
        var formData = new FormData();
        formData.append('sticker_code', code);
        formData.append('sticker_title', title);
        formData.append('contact_id', currentContactId);

        if (currentContactId === 'public') {
            formData.append('target_type', currentAudienceTarget);
            if (currentAudienceTarget === 'only') {
                currentSelectedUserIds.forEach(function(id) { formData.append('visible_to_users[]', id); });
            } else if (currentAudienceTarget === 'exclude') {
                currentSelectedUserIds.forEach(function(id) { formData.append('hidden_from_users[]', id); });
            }
        }

        dispatchMessagePost(formData);
    }

    // Audience Targeting UI Logic
    openAudienceModalBtn.addEventListener('click', function() {
        audienceModal.classList.add('open');
    });
    headerAudienceBadgeBtn.addEventListener('click', function() {
        audienceModal.classList.add('open');
    });
    closeAudienceModalBtn.addEventListener('click', function() {
        audienceModal.classList.remove('open');
    });

    audienceOptionCards.forEach(function(card) {
        card.addEventListener('click', function() {
            var target = this.getAttribute('data-target');
            var radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
            updateAudienceCardSelection(target);
        });
    });

    function updateAudienceCardSelection(target) {
        audienceOptionCards.forEach(function(c) {
            c.classList.toggle('active', c.getAttribute('data-target') === target);
        });

        if (target === 'only' || target === 'exclude') {
            audienceUsersListWrap.classList.remove('d-none');
            document.getElementById('audienceListTitle').textContent = (target === 'only') 
                ? 'اختر الموظفين المسموح لهم برؤية الرسالة:' 
                : 'اختر الموظفين المطلوب حجب الرسالة عنهم:';
        } else {
            audienceUsersListWrap.classList.add('d-none');
        }
    }

    function renderAudienceCheckboxes(filterQuery) {
        var filtered = contactsData;
        if (filterQuery) {
            filtered = contactsData.filter(function(c) {
                return (c.name && c.name.toLowerCase().includes(filterQuery)) ||
                       (c.role && c.role.toLowerCase().includes(filterQuery));
            });
        }

        var html = '';
        filtered.forEach(function(c) {
            var isChecked = currentSelectedUserIds.includes(Number(c.id)) ? 'checked' : '';
            var avatarHtml = c.avatar ? '<img src="' + c.avatar + '" style="width:20px;height:20px;border-radius:50%;object-fit:cover;">' : '<span class="badge bg-secondary rounded-circle" style="width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;font-size:0.65rem;">' + c.initial + '</span>';

            html += '<label class="audience-user-check-item w-100">';
            html += '  <input type="checkbox" class="form-check-input audience-user-chk m-0" value="' + c.id + '" ' + isChecked + '>';
            html +=    avatarHtml;
            html += '  <span class="small fw-semibold text-dark flex-grow-1 text-truncate">' + escapeHtml(c.name) + '</span>';
            html += '  <small class="text-muted text-truncate" style="font-size:0.68rem;max-width:120px;">' + escapeHtml(c.role || '') + '</small>';
            html += '</label>';
        });

        audienceUsersCheckboxes.innerHTML = html;

        audienceUsersCheckboxes.querySelectorAll('.audience-user-chk').forEach(function(chk) {
            chk.addEventListener('change', function() {
                var uid = Number(this.value);
                if (this.checked) {
                    if (!currentSelectedUserIds.includes(uid)) currentSelectedUserIds.push(uid);
                } else {
                    currentSelectedUserIds = currentSelectedUserIds.filter(function(id) { return id !== uid; });
                }
            });
        });
    }

    audienceSearchInput.addEventListener('input', debounce(function() {
        renderAudienceCheckboxes(this.value.trim().toLowerCase());
    }, 150));

    selectAllAudienceUsersBtn.addEventListener('click', function() {
        currentSelectedUserIds = contactsData.map(function(c) { return Number(c.id); });
        renderAudienceCheckboxes(audienceSearchInput.value.trim().toLowerCase());
    });

    clearAllAudienceUsersBtn.addEventListener('click', function() {
        currentSelectedUserIds = [];
        renderAudienceCheckboxes(audienceSearchInput.value.trim().toLowerCase());
    });

    resetAudienceModalBtn.addEventListener('click', function() {
        currentAudienceTarget = 'all';
        currentSelectedUserIds = [];
        var radioAll = document.getElementById('radioTargetAll');
        if (radioAll) radioAll.checked = true;
        updateAudienceCardSelection('all');
        applyAudienceSettings();
        audienceModal.classList.remove('open');
    });

    applyAudienceModalBtn.addEventListener('click', function() {
        var selectedRadio = audienceModal.querySelector('input[name="targetTypeRadio"]:checked');
        var targetType = selectedRadio ? selectedRadio.value : 'all';

        if ((targetType === 'only' || targetType === 'exclude') && currentSelectedUserIds.length === 0) {
            alert('يرجى تحديد موظف واحد على الأقل، أو اختيار "للجميع".');
            return;
        }

        currentAudienceTarget = targetType;
        applyAudienceSettings();
        audienceModal.classList.remove('open');
    });

    function applyAudienceSettings() {
        if (currentAudienceTarget === 'only') {
            currentAudienceLabel.textContent = 'مخصصة: لـ (' + currentSelectedUserIds.length + ') موظفين 🔒';
            targetingActiveTag.classList.remove('d-none');
            targetingActiveTag.textContent = 'مخصصة (' + currentSelectedUserIds.length + ')';
            headerAudienceBadgeBtn.classList.remove('d-none');
            headerAudienceBadgeText.textContent = 'مخصصة (' + currentSelectedUserIds.length + ')';
        } else if (currentAudienceTarget === 'exclude') {
            currentAudienceLabel.textContent = 'استثناء: مخفية عن (' + currentSelectedUserIds.length + ') موظفين 🚫';
            targetingActiveTag.classList.remove('d-none');
            targetingActiveTag.textContent = 'استثناء (' + currentSelectedUserIds.length + ')';
            headerAudienceBadgeBtn.classList.remove('d-none');
            headerAudienceBadgeText.textContent = 'استثناء (' + currentSelectedUserIds.length + ')';
        } else {
            currentAudienceLabel.textContent = 'نطاق الظهور: للجميع 🌐';
            targetingActiveTag.classList.add('d-none');
            headerAudienceBadgeBtn.classList.add('d-none');
        }
    }

    // Form Submission
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var msg = chatInput.value.trim();
        if (!msg && !stagedFile) return;

        var formData = new FormData();
        formData.append('message', msg);
        formData.append('contact_id', currentContactId);

        if (stagedFile) {
            formData.append('attachment', stagedFile);
        }

        if (currentContactId === 'public') {
            formData.append('target_type', currentAudienceTarget);
            if (currentAudienceTarget === 'only') {
                currentSelectedUserIds.forEach(function(id) { formData.append('visible_to_users[]', id); });
            } else if (currentAudienceTarget === 'exclude') {
                currentSelectedUserIds.forEach(function(id) { formData.append('hidden_from_users[]', id); });
            }
        }

        chatInput.value = '';
        clearStagedAttachment();
        emojiStickersDrawer.classList.remove('open');
        sendBtn.disabled = true;

        dispatchMessagePost(formData);
    });

    function dispatchMessagePost(formData) {
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch('{{ route("internal-chat.send") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            sendBtn.disabled = false;
            if (data.success && data.message) {
                var cid = String(currentContactId || 'public');
                var store = getStore(cid);
                store.messages.push(data.message);
                store.lastId = Math.max(store.lastId, data.message.id);
                appendMessage(data.message);
                scrollToBottom();

                // If smart bot responded
                if (data.bot_reply) {
                    setTimeout(function() {
                        store.messages.push(data.bot_reply);
                        store.lastId = Math.max(store.lastId, data.bot_reply.id);
                        appendMessage(data.bot_reply);
                        scrollToBottom();
                        playNotificationSound();
                    }, 400);
                }
            } else if (data.message) {
                alert(data.message);
            }
        })
        .catch(function(err) {
            sendBtn.disabled = false;
            console.error('Failed to send message', err);
        });
    }

    // Polling logic with Page Visibility API and adaptive intervals
    function startPolling() {
        stopPolling();
        if (!isOpen) return; // Only poll messages when the widget is actively open

        pollInterval = setInterval(function() {
            if (!document.hidden && isOpen && (currentView === 'channel' || currentView === 'direct')) {
                loadMessages(true);
            }
        }, 3500);
    }

    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }

    // Page Visibility listener: reduce CPU & network usage when tab is inactive
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopPolling();
        } else {
            if (isOpen && (currentView === 'channel' || currentView === 'direct')) {
                loadMessages(true);
                startPolling();
            }
            checkUnreadBadge();
        }
    });

    // Background unread badge poll (every 25 seconds when tab is active)
    function checkUnreadBadge() {
        if (document.hidden) return; // Skip badge check when tab is hidden

        fetch('{{ route("internal-chat.unread-count") }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                var count = data.unread_count || 0;
                if (count > 0) {
                    totalBadge.textContent = count > 99 ? '99+' : count;
                    totalBadge.classList.remove('d-none');
                    contactsTabBadge.classList.remove('d-none');
                } else {
                    totalBadge.classList.add('d-none');
                    contactsTabBadge.classList.add('d-none');
                }
            }
        })
        .catch(function(e) {});
    }

    function updateTotalBadge() {
        var total = 0;
        contactsData.forEach(function(c) {
            total += (c.unread_count || 0);
        });
        if (total > 0) {
            totalBadge.textContent = total > 99 ? '99+' : total;
            totalBadge.classList.remove('d-none');
            contactsTabBadge.classList.remove('d-none');
        } else {
            totalBadge.classList.add('d-none');
            contactsTabBadge.classList.add('d-none');
        }
    }

    function mbInitial(name) {
        if (!name) return 'U';
        return name.trim().charAt(0).toUpperCase();
    }

    function escapeHtml(text) {
        if (!text) return '';
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Welcome Greeting on System Open
    var welcomeBubble = document.getElementById('ensanWelcomeBubble');
    var closeWelcomeBubbleBtn = document.getElementById('closeWelcomeBubbleBtn');
    var welcomeStartChatBtn = document.getElementById('welcomeStartChatBtn');
    var welcomeDismissBtn = document.getElementById('welcomeDismissBtn');

    function showWelcomeGreeting() {
        if (!welcomeBubble) return;
        var welcomed = sessionStorage.getItem('ensan_anis_welcomed');
        if (welcomed) return;

        setTimeout(function() {
            if (!isOpen) {
                welcomeBubble.classList.remove('d-none');
                playNotificationSound();
                setTimeout(function() {
                    hideWelcomeGreeting();
                }, 14000);
            }
        }, 1200);
    }

    function hideWelcomeGreeting() {
        if (!welcomeBubble) return;
        welcomeBubble.classList.add('d-none');
        sessionStorage.setItem('ensan_anis_welcomed', '1');
    }

    if (closeWelcomeBubbleBtn) closeWelcomeBubbleBtn.addEventListener('click', hideWelcomeGreeting);
    if (welcomeDismissBtn) welcomeDismissBtn.addEventListener('click', hideWelcomeGreeting);
    if (welcomeStartChatBtn) {
        welcomeStartChatBtn.addEventListener('click', function() {
            hideWelcomeGreeting();
            openChat();
        });
    }

    // Trigger welcoming popup on system open
    showWelcomeGreeting();

    // Initial check
    checkUnreadBadge();
    badgePollInterval = setInterval(checkUnreadBadge, 25000);
})();
</script>
@endauth
