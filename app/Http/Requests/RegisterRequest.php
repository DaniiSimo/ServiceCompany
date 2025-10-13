<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->wantsJson() || $this->ajax();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::defaults()],
        ];
    }

    public function failedValidation(Validator $validator):void
    {
        throw new HttpResponseException(response: response()->json(data:[
            'status' => 422,
            'description' => 'Ошибка валидации',
            'data' => $validator->errors()
        ], status: 422));
    }
}
