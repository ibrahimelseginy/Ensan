<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $accountId = $this->route('account')?->id;

        return [
            'code'        => 'required|string|max:20|unique:accounts,code,' . ($accountId ?? ''),
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:asset,liability,equity,revenue,expense',
            'parent_id'   => 'nullable|exists:accounts,id',
            'description' => 'nullable|string'
        ];
    }
}
