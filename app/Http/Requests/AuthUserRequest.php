<?php

namespace App\Http\Requests;

use App\DTO\AuthUserDTO;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthUserRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function dto(): AuthUserDTO {
        return AuthUserDTO::fromArray(
            data: array_merge($this->validated(),['ip' => $this->ip()])
        );
    }

    public function failedValidation(Validator $validator):void
    {
        $payload = (object)[
            'description' => 'Ошибка валидации',
            'errors'      => $validator->errors(),
        ];

        $response = ErrorResource::make($payload)
            ->response(request())
            ->setStatusCode(code: 422);

        throw new HttpResponseException(response: $response);
    }
}
