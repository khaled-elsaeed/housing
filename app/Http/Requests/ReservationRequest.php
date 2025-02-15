<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check(); // Ensuring the user is authenticated
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reservation_period_type' => 'required|in:long,short',
            'reservation_academic_term_id' => 'required_if:reservation_period_type,long|exists:academic_terms,id',
            'stay_in_last_old_room' => 'nullable|in:on',
            'share_with_sibling' => 'nullable|in:on',
            'old_room_id' => 'nullable|exists:rooms,id',
            'sibling_id' => 'required_if:share_with_sibling,on|exists:users,id',
            'short_period_duration' => 'required_if:reservation_period_type,short|in:day,week,month',
            'start_date' => 'nullable|required_if:reservation_period_type,short|date',
            'end_date' => 'nullable|required_if:short_period_duration,week,month|date|after:start_date',
        ];
    }

    /**
     * Customize the error messages for the rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'end_date.after' => 'The end date must be after the start date.',
        ];
    }
}
