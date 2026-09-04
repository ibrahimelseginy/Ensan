<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Donor;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class DonorRepository
{
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return Donor::paginate($perPage);
    }

    public function all(): Collection
    {
        return Donor::orderBy('name')->get();
    }

    public function findById(int $id): ?Donor
    {
        return Donor::find($id);
    }

    public function findByPhoneOrEmail(?string $phone, ?string $email): ?Donor
    {
        if (!$phone && !$email) {
            return null;
        }

        return Donor::query()
            ->when($email, fn($q) => $q->where('email', $email))
            ->when($phone, fn($q) => $q->orWhere('phone', $phone))
            ->first();
    }

    public function create(array $data): Donor
    {
        return Donor::create($data);
    }

    public function update(Donor $donor, array $data): bool
    {
        return $donor->update($data);
    }

    public function delete(Donor $donor): bool
    {
        return $donor->delete();
    }
}
