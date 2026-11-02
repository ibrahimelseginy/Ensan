<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SystemUpdateNotificationService;

class BroadcastSystemUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:broadcast-update 
                            {module=all : The module ID (beneficiaries, finance, complaints, guest_house, hr, projects, logistics, all)} 
                            {--details= : Description of the update made by Eng. Ibrahim} 
                            {--title= : Custom notification title}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إرسال إشعار للموظفين بتحديث النظام في الجزء الخاص بهم فقط بتوقيع مهندس إبراهيم السجيني';

    /**
     * Execute the console command.
     */
    public function handle(SystemUpdateNotificationService $service): int
    {
        $module = $this->argument('module');
        $details = $this->option('details');
        $title = $this->option('title');

        $modules = SystemUpdateNotificationService::getModules();
        if (!isset($modules[$module]) && $module !== 'all') {
            $this->error("الموديول '{$module}' غير معرّف. الخيارات المتاحة هي: " . implode(', ', array_keys($modules)));
            return self::FAILURE;
        }

        $this->info("جاري إرسال إشعار التحديث الخاص بـ [{$module}] بتوقيع: مهندس إبراهيم السجيني 🛠️ ...");

        $res = $service->broadcastUpdate($module, $details, $title);

        $this->line("--------------------------------------------------");
        $this->info("تم إرسال الإشعار بنجاح!");
        $this->line("القسم المستهدف: " . $res['module_name']);
        $this->line("عدد الموظفين المستلمين: " . $res['notified_count']);
        if (!empty($res['employees'])) {
            $this->line("الموظفون: " . implode('، ', array_slice($res['employees'], 0, 8)) . (count($res['employees']) > 8 ? ' ...وآخرين' : ''));
        }
        $this->line("--------------------------------------------------");

        return self::SUCCESS;
    }
}
