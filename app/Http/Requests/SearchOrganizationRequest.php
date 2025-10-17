<?php

namespace App\Http\Requests;

use App\DTO\SearchOrganizationDTO;
use App\Http\Resources\ErrorResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SearchOrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->wantsJson() || $this->ajax();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has(key: 'should_take_descendants')) {
            $this->merge([
                'should_take_descendants' => filter_var(
                    $this->input(key: 'should_take_descendants'),
                    filter: FILTER_VALIDATE_BOOLEAN,
                    options: FILTER_NULL_ON_FAILURE
                ),
            ]);
        } else {
            $this->merge(['should_take_descendants' => false]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'address_building' => ['nullable','string', 'max:255','exists:buildings,address'],
            'name_organization' => ['nullable', 'string', 'max:255','exists:organizations,name'],

            'name_activity' => ['nullable','string', 'max:255','exists:activities,name'],
            'should_take_descendants' => ['boolean'],

            'polygon'          => ['nullable', Rule::closedPolygon()],
            'polygon.*'        => ['required_with:polygon', 'string', 'regex:/^\s*-?\d{1,3}(?:\.\d{1,2})?\s+-?\d{1,2}(?:\.\d{1,2})?\s*$/'],

            'lat'    => ['nullable','numeric','between:-90,90','required_with:lon,radius'],
            'lon'    => ['nullable','numeric','between:-180,180','required_with:lat,radius'],
            'radius' => ['nullable','numeric','min:0','required_with:lat,lon'],
        ];
    }

    public function messages(): array
    {
        return [
            'polygon.*.regex' => 'Пара координат должны совпадать с шаблоном, например: "130.15 34.10" ',
        ];
    }

    public function dto(): SearchOrganizationDTO {
        return SearchOrganizationDTO::fromArray(data: $this->validated());
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
