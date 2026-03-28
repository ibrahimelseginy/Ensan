<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'season_year' => 'required|integer|min:2000|max:2100',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
            'status'      => 'required|in:active,archived',
            'project_id'  => 'nullable|exists:projects,id',
        ];
    }
}
