<?php

namespace App\Features\WebsiteContent\Interfaces;

use Illuminate\Http\Request;

interface DonationPageSettingsInterface
{
    /**
     * Get all donation page settings
     */
    public function getSettings(): array;

    /**
     * Update donation page settings
     */
    public function updateSettings(array $data): void;
}
