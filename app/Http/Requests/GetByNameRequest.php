<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetByNameRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255','exists:organizations,name'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response: response()->json(data: [
            'status' => 422,
            'description' => 'Ошибка валидации',
            'data' => $validator->errors(),
        ], status: 422));
    }
}
