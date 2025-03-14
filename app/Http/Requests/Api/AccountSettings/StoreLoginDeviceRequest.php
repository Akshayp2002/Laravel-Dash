<?php

namespace App\Http\Requests\Api\AccountSettings;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoginDeviceRequest extends FormRequest
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
            'app_name'   => 'required|string',
            'os'         => 'required|string',
            'ip_address' => 'required|ip',
            'latitude'   => 'nullable|string',
            'longitude'  => 'nullable|string',
        ];
    }
}
