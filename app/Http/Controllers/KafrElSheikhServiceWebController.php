<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\KafrElSheikhService;
use Illuminate\Http\Request;

final class KafrElSheikhServiceWebController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $serviceType = trim((string) $request->query('service_type', ''));
        $perPage = (int) $request->query('per_page', 12);

        if (! in_array($perPage, [12, 24, 48], true)) {
            $perPage = 12;
        }

        $services = KafrElSheikhService::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('service_type', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->when($serviceType !== '', fn ($query) => $query->where('service_type', $serviceType))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $serviceTypes = KafrElSheikhService::query()
            ->whereNotNull('service_type')
            ->where('service_type', '!=', '')
            ->distinct()
            ->orderBy('service_type')
            ->pluck('service_type');

        $stats = [
            'total' => KafrElSheikhService::count(),
            'with_phone' => KafrElSheikhService::whereNotNull('phone')->where('phone', '!=', '')->count(),
            'types' => $serviceTypes->count(),
        ];

        return view('kafr_el_sheikh_services.index', compact(
            'services',
            'serviceTypes',
            'stats',
            'search',
            'serviceType',
            'perPage',
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_type' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:2000',
        ], $this->validationMessages());

        KafrElSheikhService::create($this->normalize($validated));

        return redirect()->route('kafr-el-sheikh-services.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function update(Request $request, KafrElSheikhService $kafr_el_sheikh_service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_type' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:2000',
        ], $this->validationMessages());

        $kafr_el_sheikh_service->update($this->normalize($validated));

        return redirect()->route('kafr-el-sheikh-services.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy(KafrElSheikhService $kafr_el_sheikh_service)
    {
        $kafr_el_sheikh_service->delete();

        return redirect()->route('kafr-el-sheikh-services.index')->with('success', 'تم الحذف بنجاح');
    }

    /**
     * @param  array{name: string, service_type?: string|null, phone?: string|null, notes?: string|null}  $data
     * @return array{name: string, service_type: string|null, phone: string|null, notes: string|null}
     */
    private function normalize(array $data): array
    {
        return [
            'name' => trim($data['name']),
            'service_type' => filled($data['service_type'] ?? null) ? trim($data['service_type']) : null,
            'phone' => filled($data['phone'] ?? null) ? trim($data['phone']) : null,
            'notes' => filled($data['notes'] ?? null) ? trim($data['notes']) : null,
        ];
    }

    /** @return array<string, string> */
    private function validationMessages(): array
    {
        return [
            'name.required' => 'يرجى إدخال اسم مقدم الخدمة.',
            'name.max' => 'اسم مقدم الخدمة طويل أكثر من اللازم.',
            'service_type.max' => 'نوع الخدمة يجب ألا يتجاوز 100 حرف.',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرفاً.',
            'notes.max' => 'الملاحظات يجب ألا تتجاوز 2000 حرف.',
        ];
    }
}
