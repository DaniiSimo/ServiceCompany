<?php

namespace App\Http\Requests;

use App\DTO\RegisterUserDTO;
use App\Http\Resources\ErrorResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
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

    public function dto(): RegisterUserDTO {
        return RegisterUserDTO::fromArray(data: $this->validated());
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
