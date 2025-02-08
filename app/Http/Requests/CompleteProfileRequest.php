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
            'nameAr' => [
                'required', 
                'string', 
                'max:255', 
                'regex:/^[\x{0600}-\x{06FF}\s\-]+$/u'  // Only Arabic characters, spaces, and hyphens
            ],
            'nameEn' => [
                'required', 
                'string', 
                'max:255', 
                'regex:/^[a-zA-Z\s\-]+$/'  // Only English characters, spaces, and hyphens
            ],
           'nationalId' => [
    'required',
    'string',
    'size:14',
    'regex:/^[2-3]\d{13}$/',  // Egyptian National ID format (14 digits starting with 2 or 3)
    Rule::unique('students', 'national_id')  // 'students' instead of 'student'
        ->ignore($this->user()->student?->id ?? null, 'id')  // Properly handle the ignore case
],
            'birthDate' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'governorate' => [
                'required',
                'integer',
                Rule::exists('governorates', 'id')
            ],
            'city' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')
                    ->where('governorate_id', $this->input('governorate'))
                    
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
                Rule::exists('faculties', 'id')
            ],
            'program' => [
                'required',
                'integer',
                Rule::exists('programs', 'id')
                    ->where('faculty_id', $this->input('faculty'))
                    
            ],
            'academicId' => [
                'nullable',
                'string',
                'size:9',

                    ],
            'academicEmail' => [
    'nullable',
    'email',
    'max:255',
    'regex:/^[a-zA-Z]+\d{9}@nmu\.edu\.eg$/',
],

            'score' => ['nullable', 'numeric', 'min:0', 'max:4', 'regex:/^\d*\.?\d{0,2}$/'],
            'parentRelationship' => ['required', 'string'],
            'parentName' => ['required', 'string', 'max:255'],
            'parentPhone' => [
                'nullable',
                'string',
                'regex:/^01[0125][0-9]{8}$/'  // Egyptian phone number format
            ],
            
            'parentEmail' => ['nullable', 'email', 'max:255'],
            'isParentAbroad' => ['required', Rule::in(['1', '0'])],
            'abroadCountry' => [
                'nullable',
                'required_if:isParentAbroad,1',
                'integer',
                Rule::exists('countries', 'id')
            ],
            'parentPhoneAbroad' =>[
                'nullable',
                'string',
                'regex:/^\+?[1-9]\d{1,14}$/'  // International phone number format
            ],
            'livingWithParent' => [
                'nullable',
                'required_if:isParentAbroad,0',
                Rule::in(['1', '0'])
            ],
            'parentGovernorate' => [
                'nullable',
                'required_if:livingWithParent,0',
                'integer',
                Rule::exists('governorates', 'id')
            ],
            'parentCity' => [
                'nullable',
                'required_if:livingWithParent,0',
                'integer',
                Rule::exists('cities', 'id')
                    ->where('governorate_id', $this->input('parentGovernorate'))
                    
            ],
            'hasSiblingInDorm' => ['required', Rule::in(['1', '0'])],
            'siblingGender' => [ 'nullable',
            'required_if:hasSiblingInDorm,1', 'string', 'max:50'],
            'siblingName' => ['nullable',
            'required_if:hasSiblingInDorm,1'
            , 'string', 'max:255'],
            'siblingNationalId' => [
                'nullable',
            'required_if:hasSiblingInDorm,1',
                'string',
                'size:14',
                'regex:/^[2-3]\d{13}$/'
            ],
            'siblingFaculty' => [
'nullable',
            'required_if:hasSiblingInDorm,1',                'integer',
                Rule::exists('faculties', 'id')
            ],
            'emergencyContactRelationship' => ['nullable',
                'required_if:isParentAbroad,1'
            ,
             'string', 'max:50'],
            'emergencyContactName' => ['nullable',
                'required_if:isParentAbroad,1'
            , 'string', 'max:255'],
            'emergencyContactPhone' => [
                'nullable',
                'required_if:isParentAbroad,1'
            ,
                'string',
                'regex:/^01[0125][0-9]{8}$/'  // Egyptian phone number format
            ],
'termsCheckbox' => ['required', 'in:accepted'],
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
            'nameAr.regex' => 'The Arabic name must contain only Arabic characters.',
            'nameEn.regex' => 'The English name must contain only English characters.',
            'nationalId.size' => 'The national ID must be exactly 14 digits.',
            'nationalId.regex' => 'The national ID format is invalid. It must start with 2 or 3 followed by 13 digits.',
            'phone.regex' => 'The phone number must be a valid Egyptian phone number.',
            'parentPhone.regex' => 'The parent phone number must be a valid Egyptian phone number.',
            'emergencyContactPhone.regex' => 'The emergency contact phone number must be a valid Egyptian phone number.',
            'score.regex' => 'The score must have 0 more than 2 decimal places.',
            'siblingNationalId.regex' => 'The sibling national ID format is invalid. It must start with 2 or 3 followed by 13 digits.',
            'termsCheckbox.required' => 'You must accept the Terms and Conditions to proceed.',

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
            'nameAr' => strip_tags($this->nameAr),
            'nameEn' => strip_tags($this->nameEn),
            'street' => $this->street ? strip_tags($this->street) : null,
            'parentName' => strip_tags($this->parentName),
            'siblingName' => $this->siblingName ? strip_tags($this->siblingName) : null,
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
            'nameAr' => 'Arabic name',
            'nameEn' => 'English name',
            'nationalId' => 'national ID',
            'birthDate' => 'birth date',
            'universityId' => 'university ID',
            'academicEmail' => 'university email',
            'parentRelationship' => 'parent relationship',
            'parentName' => 'parent name',
            'parentPhone' => 'parent phone',
            'parentEmail' => 'parent email',
            'isParentAbroad' => 'parent abroad status',
            'abroadCountry' => 'abroad country',
            'livingWithParent' => 'living with parent status',
            'parentGovernorate' => 'parent governorate',
            'parentCity' => 'parent city',
            'hasSiblingInDorm' => 'sibling in dorm status',
            'siblingGender' => 'sibling relationship',
            'siblingName' => 'sibling name',
            'siblingNationalId' => 'sibling national ID',
            'siblingFaculty' => 'sibling faculty',
            'emergencyContactRelationship' => 'emergency contact relationship',
            'emergencyContactName' => 'emergency contact name',
            'emergencyContactPhone' => 'emergency contact phone',
        ];
    }
}