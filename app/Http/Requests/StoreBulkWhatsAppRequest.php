<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBulkWhatsAppRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'request_id' => ['required', 'uuid'],
            'booking_ids' => ['required', 'array', 'min:1', 'max:50'],
            'booking_ids.*' => ['required', 'integer', 'distinct', 'exists:zoom_bookings,id'],
            'recipients' => ['required', 'string', 'max:5000'],
            'interval_seconds' => ['required', 'integer', 'between:7,15'],
            'include_credentials' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_ids.required' => 'Pilih sedikitnya satu meeting.',
            'booking_ids.max' => 'Maksimal 50 meeting dalam satu antrean.',
            'booking_ids.*.exists' => 'Salah satu meeting sudah tidak tersedia. Muat ulang daftar booking.',
            'booking_ids.*.distinct' => 'Meeting yang sama tidak boleh dipilih dua kali.',
            'recipients.required' => 'Masukkan sedikitnya satu nomor WhatsApp tujuan.',
            'interval_seconds.between' => 'Jeda pengiriman harus antara 7 sampai 15 detik.',
        ];
    }
}
