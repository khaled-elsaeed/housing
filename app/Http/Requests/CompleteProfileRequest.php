<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CompleteProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'full_name_ar' => [
                'required', 
                'string', 
                'max:255', 
                'regex:/^[\x{0600}-\x{06FF}\s\-]+$/u'  // Only Arabic characters, spaces, and hyphens
            ],
            'full_name_en' => [
                'required', 
                'string', 
                'max:255', 
                'regex:/^[a-zA-Z\s\-]+$/'  // Only English characters, spaces, and hyphens
            ],
            'national_id' => [
                'required',
                'string',
                'size:14',
                'regex:/^[2-3]\d{13}$/',  // Egyptian National ID format (14 digits starting with 2 or 3)
                Rule::unique('users', 'national_id')->ignore($this->user()->id)
            ],
            'birthDate' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'governorate' => [
                'required',
                'integer',
                Rule::exists('governorates', 'id')->whereNull('deleted_at')
            ],
            'city' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')
                    ->where('governorate_id', $this->input('governorate'))
                    ->whereNull('deleted_at')
            ],
            'street' => [
                'nullable', 
                'string', 
                'max:255'
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^01[0125][0-9]{8}$/'  // Egyptian phone number format
            ],
            'faculty' => [
                'required',
                'integer',
                Rule::exists('faculties', 'id')->whereNull('deleted_at')
            ],
            'program' => [
                'required',
                'integer',
                Rule::exists('programs', 'id')
                    ->where('faculty_id', $this->input('faculty'))
                    ->whereNull('deleted_at')
            ],
            'universityId' => [
                'nullable',
                'string',
                'size:9',
                Rule::unique('users', 'university_id')->ignore($this->user()->id)
            ],
            'universityEmail' => [
                'nullable',
                'email',
                'max:255',
                'regex:/^[a-zA-Z]+@nmu\.edu\.eg$/',  // NMU email format
                Rule::unique('users', 'university_email')->ignore($this->user()->id)
            ],
            'gpa' => ['nullable', 'numeric', 'min:0', 'max:4', 'regex:/^\d*\.?\d{0,2}$/'],
            'parentRelationship' => ['required', 'string', 'max:10'],
            'parentName' => ['required', 'string', 'max:255'],
            'parentPhone' => [
                'nullable',
                'string',
                'regex:/^01[0125][0-9]{8}$/'  // Egyptian phone number format
            ],
            
            'parentEmail' => ['nullable', 'email', 'max:255'],
            'isParentAbroad' => ['required', Rule::in(['yes', 'no'])],
            'abroadCountry' => [
                'nullable',
                'required_if:isParentAbroad,yes',
                'integer',
                Rule::exists('countries', 'id')->whereNull('deleted_at')
            ],
            'parentPhoneAbroad' =>[
                'nullable',
                'string',
                'regex:/^\+?[1-9]\d{1,14}$/'  // International phone number format
            ],
            'livingWithParent' => [
                'nullable',
                'required_if:isParentAbroad,no',
                Rule::in(['yes', 'no'])
            ],
            'parentGovernorate' => [
                'nullable',
                'required_if:isParentAbroad,no',
                'integer',
                Rule::exists('governorates', 'id')->whereNull('deleted_at')
            ],
            'parentCity' => [
                'nullable',
                'required_if:isParentAbroad,no',
                'integer',
                Rule::exists('cities', 'id')
                    ->where('governorate_id', $this->input('parentGovernorate'))
                    ->whereNull('deleted_at')
            ],
            'emergencyContactRelationship' => ['required', 'string', 'max:50'],
            'emergencyContactName' => ['required', 'string', 'max:255'],
            'emergencyContactPhone' => [
                'required',
                'string',
                'regex:/^01[0125][0-9]{8}$/'  // Egyptian phone number format
            ],
            'termsCheckbox' => ['accepted'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'full_name_ar.regex' => 'The Arabic name must contain only Arabic characters.',
            'full_name_en.regex' => 'The English name must contain only English characters.',
            'national_id.size' => 'The national ID must be exactly 14 digits.',
            'national_id.regex' => 'The national ID format is invalid. It must start with 2 or 3 followed by 13 digits.',
            'phone.regex' => 'The phone number must be a valid Egyptian mobile number.',
            'parentPhone.regex' => 'The parent phone number must be a valid Egyptian mobile number.',
            'emergencyContactPhone.regex' => 'The emergency contact phone number must be a valid Egyptian mobile number.',
            'gpa.regex' => 'The GPA must have no more than 2 decimal places.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'full_name_ar' => strip_tags($this->full_name_ar),
            'full_name_en' => strip_tags($this->full_name_en),
            'street' => $this->street ? strip_tags($this->street) : null,
            'parentName' => strip_tags($this->parentName),
            'emergencyContactName' => strip_tags($this->emergencyContactName),
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'full_name_ar' => 'Arabic name',
            'full_name_en' => 'English name',
            'national_id' => 'national ID',
            'birthDate' => 'birth date',
            'universityId' => 'university ID',
            'universityEmail' => 'university email',
            'parentRelationship' => 'parent relationship',
            'parentName' => 'parent name',
            'parentPhone' => 'parent phone',
            'parentEmail' => 'parent email',
            'isParentAbroad' => 'parent abroad status',
            'abroadCountry' => 'abroad country',
            'livingWithParent' => 'living with parent status',
            'parentGovernorate' => 'parent governorate',
            'parentCity' => 'parent city',
            'emergencyContactRelationship' => 'emergency contact relationship',
            'emergencyContactName' => 'emergency contact name',
            'emergencyContactPhone' => 'emergency contact phone',
        ];
    }
}