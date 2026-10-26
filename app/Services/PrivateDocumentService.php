<?php

namespace App\Services;

use App\Models\User;
use App\Models\WebDonor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

final class PrivateDocumentService
{
    public const DIRECTORIES = ['user-docs', 'pending/user-docs', 'attachments', 'donation_proofs', 'website/donations/proofs', 'anasen/donations/proofs'];

    public function isDocumentDirectory(string $directory): bool
    {
        foreach (self::DIRECTORIES as $prefix) {
            if ($directory === $prefix || str_starts_with($directory, $prefix.'/')) return true;
        }
        return false;
    }

    public function store(UploadedFile $file, string $directory): string
    {
        if (!$this->isDocumentDirectory($directory) || str_contains($directory, '..')) throw new \InvalidArgumentException('Invalid document directory.');
        $path = $file->store('private/'.$directory, 'local');
        if (!$path) throw new \RuntimeException('تعذر حفظ المستند.');
        return $path;
    }

    public function url(string $path): ?string
    {
        if (!str_starts_with($path, 'private/') || !$this->canRead($path)) return null;
        return URL::temporarySignedRoute('documents.download', now()->addHours(24), ['path' => $path]);
    }

    private function canRead(string $path): bool
    {
        $user = auth()->user() ?? (request() ? request()->user() : null) ?? auth('web_donor')->user();
        if ($user instanceof User) {
            if ($user->hasRole(['admin', 'super_admin', 'manager', 'مدير المؤسسة بالكامل', 'إدارة'])) return true;
            if (str_contains($path, 'user-docs/')) {
                if ($user->hasPermission('users.view') || $user->hasPermission('users.edit') || \Illuminate\Support\Facades\Gate::allows('users.view') || \Illuminate\Support\Facades\Gate::allows('users.edit')) return true;
                return in_array($path, [$user->contract_image, $user->criminal_record_image, $user->id_card_image], true)
                    || User::where('contract_image', $path)->orWhere('criminal_record_image', $path)->orWhere('id_card_image', $path)->exists();
            }
            if (str_starts_with($path, 'private/attachments/')) return $user->hasPermission('attachments.view');
            if ($user->hasPermission('donations.view') || $user->hasPermission('website.donation_accounts.view')) return true;
            return \App\Models\DonationProof::where('image_path', $path)
                ->whereHas('donation', fn ($q) => $q->where('user_id', $user->id))->exists();
        }
        if ($user instanceof WebDonor) {
            return \App\Models\DonationProof::where('image_path', $path)->where(function ($q) use ($user) {
                $q->whereHas('webDonation', fn ($d) => $d->where('web_donor_id', $user->id))
                    ->orWhereHas('donation', fn ($d) => $d->where('web_donor_id', $user->id));
            })->exists();
        }
        return false;
    }
}
