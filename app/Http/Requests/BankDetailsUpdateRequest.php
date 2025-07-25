<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankDetailsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:20'],
            'account_name' => ['required', 'string', 'max:255'],
            'bvn' => ['nullable', 'string', 'size:11'],
        ];
    }

    public function messages(): array
    {
        return [
            'bvn.size' => 'BVN must be exactly 11 digits.',
            'account_number.required' => 'Account number is required.',
        ];
    }
}