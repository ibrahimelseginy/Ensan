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
            'code' => 'sometimes|string|unique:accounts,code,' . ($accountId ?? ''),
            'name' => 'sometimes|string',
            'type' => 'sometimes|in:asset,liability,equity,revenue,expense'
        ];
    }
}
