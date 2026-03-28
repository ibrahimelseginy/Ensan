<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class AuditWebController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user && $user->roles()->where('key', 'admin')->exists();
        if (!$isAdmin) {
            abort(403);
        }

        $days = (int) ($request->input('days') ?? 14);
        $method = $request->input('method');
        $uid = $request->input('user_id');
        $q = trim((string) $request->input('q'));

        $from = now()->subDays(max($days, 1))->startOfDay();
        $to = now()->endOfDay();

        $base = Audit::whereBetween('created_at', [$from, $to])
            ->when($method, function ($q2, $m) {
                $q2->where('method', $m); })
            ->when($uid, function ($q2, $u) {
                $q2->where('user_id', $u); })
            ->when($q !== '', function ($q2) use ($q) {
                $q2->where('path', 'like', "%$q%"); });

        if ($request->get('export') === 'csv') {
            $cols = ['id', 'created_at', 'user_id', 'method', 'path', 'payload'];
            if (Schema::hasColumn('audits', 'status_code')) {
                $cols = array_merge(['id', 'created_at', 'user_id', 'method', 'path', 'status_code', 'ip', 'user_agent'], ['payload']);
            }
            $rows = $base->orderByDesc('id')->limit(1000)->get($cols);
            $filename = 'audits_' . now()->format('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ];
            $content = implode(',', $cols) . "\n";
            foreach ($rows as $r) {
                $payload = isset($r->payload) && $r->payload ? json_encode($r->payload, JSON_UNESCAPED_UNICODE) : '';
                $ua = isset($r->user_agent) ? (string) $r->user_agent : '';
                $map = [
                    'id' => $r->id,
                    'created_at' => optional($r->created_at)->format('Y-m-d H:i:s'),
                    'user_id' => $r->user_id,
                    'method' => $r->method,
                    'path' => $r->path,
                    'status_code' => $r->status_code ?? '',
                    'ip' => $r->ip ?? '',
                    'user_agent' => str_replace(["\n", "\r", "\t", "\""], [" ", " ", " ", "'"], $ua),
                    'payload' => str_replace(["\n", "\r", "\t", "\""], [" ", " ", " ", "'"], $payload)
                ];
                $line = [];
                foreach ($cols as $c) {
                    $line[] = $map[$c] ?? '';
                }
                $content .= implode(',', array_map(function ($v) {
                    return '"' . (string) $v . '"'; }, $line)) . "\n";
            }
            return response($content, 200, $headers);
        }

        $audits = (clone $base)->orderByDesc('id')->paginate(20);

        $userIds = $audits->pluck('user_id')->filter()->unique();
        $usersMap = User::whereIn('id', $userIds)->get()->keyBy('id');

        $stats = [
            'total' => (int) $base->count(),
            'GET' => (int) (clone $base)->where('method', 'GET')->count(),
            'POST' => (int) (clone $base)->where('method', 'POST')->count(),
            'PUT' => (int) (clone $base)->where('method', 'PUT')->count(),
            'PATCH' => (int) (clone $base)->where('method', 'PATCH')->count(),
            'DELETE' => (int) (clone $base)->where('method', 'DELETE')->count(),
        ];
        $topPaths = $base->select('path', DB::raw('COUNT(*) as c'))->groupBy('path')->orderByDesc('c')->limit(5)->get();
        $topUsers = $base->select('user_id', DB::raw('COUNT(*) as c'))->whereNotNull('user_id')->groupBy('user_id')->orderByDesc('c')->limit(5)->get();
        $permissionsMap = \App\Models\Permission::pluck('name', 'id')->toArray();
        return view('audits.index', compact('audits', 'usersMap', 'days', 'method', 'uid', 'q', 'stats', 'topPaths', 'topUsers', 'permissionsMap'));
    }
}

