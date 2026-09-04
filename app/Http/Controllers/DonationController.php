<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Services\DonationService;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\UpdateDonationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

final class DonationController extends Controller
{
    public function __construct(
        private DonationService $donationService
    ) {}

    public function index(Request $request): LengthAwarePaginator
    {
        $filters = $request->only(['q', 'project_id', 'campaign_id', 'guest_house_id', 'channel', 'type']);
        return $this->donationService->searchDonations($filters, 20);
    }

    public function store(StoreDonationRequest $request): mixed
    {
        return $this->donationService->createDonation($request->validated());
    }

    public function show(Donation $donation): Donation
    {
        return $donation->load(['donor', 'project', 'campaign', 'warehouse', 'delegate', 'route']);
    }

    public function update(UpdateDonationRequest $request, Donation $donation): mixed
    {
        return $this->donationService->updateDonation($donation, $request->validated());
    }

    public function destroy(Donation $donation, Request $request): mixed
    {
        $reason = (string) $request->input('cancellation_reason', 'لم يتم تحديد سبب');
        return $this->donationService->cancelDonation($donation, $reason);
    }
}
