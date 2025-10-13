<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class GetByAreaRequest extends FormRequest
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
            'polygon' =>  ['nullable', Rule::closedPolygon()],
            'polygon.*' => ['required', 'string', 'regex:/^\s*-?\d{1,3}(\.\d{1,2})?\s+-?\d{1,3}(\.\d{1,2})?\s*$/'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lon' => ['nullable', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'polygon.*.regex' => ['Пара координат должны совпадать с шаблоном, например: "130.15 34.10" '],
        ];
    }

    public function failedValidation(Validator $validator):void
    {
        throw new HttpResponseException(response: response()->json(data: [
            'status' => 422,
            'description' => 'Ошибка валидации',
            'data' => $validator->errors(),
        ], status: 422));
    }
}
