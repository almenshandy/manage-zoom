<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'zoom_account_id'  => ['nullable', 'exists:zoom_accounts,id'],
            'topic'            => ['required', 'string', 'max:255'],
            'project_name'     => ['nullable', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'start_date'       => ['required', 'date'],
            'end_date'         => ['required', 'date', 'after_or_equal:start_date'],
            'start_time'       => ['required'],
            'end_time'         => ['required'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:1440'],
            'user_note'        => ['nullable', 'string', 'max:500'],
            'admin_note'       => ['nullable', 'string', 'max:500'],
        ];
    }
}
