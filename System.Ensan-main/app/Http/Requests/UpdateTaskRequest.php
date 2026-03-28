<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'                   => 'sometimes|string',
            'volunteer_activity_name' => 'nullable|string',
            'description'             => 'nullable|string',
            'assigned_to'             => 'nullable|exists:users,id',
            'assigned_by'             => 'nullable|exists:users,id',
            'due_date'                => 'nullable|date',
            'status'                  => 'in:pending,in_progress,done',
            'project_id'              => 'nullable|exists:projects,id',
            'campaign_id'             => 'nullable|exists:campaigns,id',
            'guest_house_id'          => 'nullable|exists:guest_houses,id',
            'rating'                  => 'nullable|integer|min:1|max:5',
            'evaluation_notes'        => 'nullable|string'
        ];
    }
}
