<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\ChangeRequest;
use App\Services\BeneficiaryService;
use App\Http\Requests\StoreBeneficiaryRequest;
use App\Http\Requests\UpdateBeneficiaryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class BeneficiaryController extends Controller
{
    public function __construct(
        private BeneficiaryService $beneficiaryService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $beneficiaries = $this->beneficiaryService->getFilteredBeneficiaries($request->all(), 20);
        return response()->json($beneficiaries);
    }

    public function store(StoreBeneficiaryRequest $request): JsonResponse
    {
        $result = $this->beneficiaryService->createBeneficiary($request->validated());

        if ($result instanceof ChangeRequest) {
            return response()->json(['message' => 'تم إرسال طلب إضافة المستفيد للموافقة', 'change_request_id' => $result->id], 202);
        }

        return response()->json(['message' => 'تم إضافة المستفيد بنجاح', 'data' => $result], 201);
    }

    public function show(Beneficiary $beneficiary): JsonResponse
    {
        return response()->json($beneficiary->load(['project', 'campaign', 'attachments']));
    }

    public function update(UpdateBeneficiaryRequest $request, Beneficiary $beneficiary): JsonResponse
    {
        try {
            $result = $this->beneficiaryService->updateBeneficiary($beneficiary, $request->validated());

            if ($result instanceof ChangeRequest) {
                return response()->json(['message' => 'تم إرسال طلب تعديل المستفيد للمراجعة', 'change_request_id' => $result->id], 202);
            }

            return response()->json(['message' => 'تم تعديل المستفيد بنجاح', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy(Beneficiary $beneficiary): JsonResponse
    {
        $result = $this->beneficiaryService->deleteBeneficiary($beneficiary);

        if ($result instanceof ChangeRequest) {
            return response()->json(['message' => 'تم إرسال طلب حذف المستفيد للمراجعة', 'change_request_id' => $result->id], 202);
        }

        return response()->json(['message' => 'تم حذف المستفيد بنجاح']);
    }
}
