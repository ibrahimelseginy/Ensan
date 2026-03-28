<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Services\DonorService;
use App\Http\Requests\StoreDonorRequest;
use App\Http\Requests\UpdateDonorRequest;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class DonorController extends Controller
{
    public function __construct(
        private DonorService $donorService
    ) {}

    public function index(): LengthAwarePaginator
    {
        return $this->donorService->searchDonors([], 20);
    }

    public function store(StoreDonorRequest $request): mixed
    {
        return $this->donorService->createDonor($request->validated());
    }

    public function show(Donor $donor): Donor
    {
        return $donor;
    }

    public function update(UpdateDonorRequest $request, Donor $donor): mixed
    {
        return $this->donorService->updateDonor($donor, $request->validated());
    }

    public function destroy(Donor $donor): Response
    {
        $this->donorService->deleteDonor($donor);
        return response()->noContent();
    }
}
