<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'department_id' => ['required','exists:departments,id'],
            'first_name' => ['required','string','max:100'],
            'last_name' => ['nullable','string','max:100'],
            //'email' => ['required','email','unique:employees,email'],
            'email' => ['required','email',"unique:employees,email,$id"],
            'dob' => ['nullable','date'],
            'designation' => ['nullable','string'],
            'phones' => ['nullable','array'],
            'phones.*.phone' => ['required_with:phones','string'],
            'phones.*.type' => ['in:mobile,home,work'],
            'phones.*.primary' => ['boolean'],
            'addresses' => ['nullable','array'],
            'addresses.*.address_line1' => ['required_with:addresses','string'],
            'addresses.*.city' => ['nullable','string'],
            'addresses.*.state' => ['nullable','string'], //isko simple kro
            'addresses.*.country' => ['nullable','string'],
            'addresses.*.postal_code' => ['nullable','string'],
            'addresses.*.type' => ['in:present,permanent,other'],
            'addresses.*.primary' => ['boolean'],
        ];
    }
}
