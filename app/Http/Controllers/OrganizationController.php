<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrganizationResource;
use App\Services\SearchOrganizationService;
use App\Http\Requests\SearchOrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Контроллер Организации
 */
class OrganizationController extends Controller
{
    public function __construct(private SearchOrganizationService $searchOrganizationService){}

	#[OA\Get(
		path: '/api/organizations/{organization}',
		tags: ['Organization'],
		security: [['bearerAuth' => []]],
		summary: 'Получение организации по идентификатору',
		parameters: [
			new OA\PathParameter(
				name: 'organization',
				description: 'id организации',
				required: true,
				schema: new OA\Schema(type: 'integer', format: 'int64')
			),
		],
		responses: [
            new OA\Response(
                response: 200, description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Organization'
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 401, description: 'Ошибка аутентификации',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'description', type: 'string', example: 'Ошибка аутентификации'),
                        new OA\Property(property: 'errors', type: 'object', example: ["Доступ запрещён, необходим токен авторизации"]),
                    ],
                    type: 'object'
                )
            ),
			new OA\Response(
				response: 404, description: 'Ресурс не найден',
				content: new OA\JsonContent(
					properties: [
						new OA\Property(property: 'message', type: 'string', example: 'Organization not found'),
					],
					type: 'object'
				)
			),
		]
	)]
    public function show(Request $request,Organization $organization): JsonResponse
    {
        return $organization->load(relations:['building','phones','activities'])
            ->toResource()
            ->response(request: $request)
            ->setStatusCode(code: 200);
    }
    #[OA\Get(
        path: '/api/organizations',
        tags: ['Organization'],
        security: [['bearerAuth' => []]],
        summary: 'Получение списка организаций',
        parameters: [
            new OA\QueryParameter(
                name: 'address_building',
                description: 'Адрес здания',
                schema: new OA\Schema(ref: '#/components/schemas/Building/properties/address')
            ),
            new OA\QueryParameter(
                name: 'name_organization',
                description: 'Название организации',
                schema: new OA\Schema(ref: '#/components/schemas/Organization/properties/name')
            ),
            new OA\QueryParameter(
                name: 'name_activity',
                description: 'Название деятельности',
                schema: new OA\Schema(ref: '#/components/schemas/Activity/properties/name')
            ),
            new OA\QueryParameter(
                name: 'should_take_descendants',
                schema: new OA\Schema(
                    type: 'boolean',
                    default: false
                ),
                description: "Необходимо ли искать среди потомков деятельности ?(Работает в связке с name_activity)"
            ),
            new OA\QueryParameter(
                name: 'polygon[]',
                description: 'Координаты области (первая и последняя пара координат должны совпадать)',
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    minItems: 4,
                    items: new OA\Items(
                        type: 'string',
                        pattern: '^-?\d{1,3}\.\d{2}\s+-?\d{1,2}\.\d{2}$',
                        example: '130.15 34.10'
                    )
                ),
                example: [
                    '130.15 34.10',
                    '130.17 34.10',
                    '130.17 34.11',
                    '130.15 34.11',
                    '130.15 34.10'
                ]
            ),
            new OA\QueryParameter(
                name: 'lat',
                description: 'Широта, относительно которой будет производиться поиск по радиусу (Работает в связке с lon и radius)',
                required: false,
                schema: new OA\Schema(
                    type: 'number',
                    example: "34.10",
                    format: 'double',
                    minimum: -90,
                    maximum: 90
                ),
            ),
            new OA\QueryParameter(
                name: 'lon',
                description: 'Долгота, относительно которой будет производиться поиск по радиусу (Работает в связке с lat и radius)',
                required: false,
                schema: new OA\Schema(
                    type: 'number',
                    example: "130.15",
                    format: 'double',
                    minimum: -180,
                    maximum: 180,
                ),
            ),
            new OA\QueryParameter(
                name: 'radius',
                description: 'Радиус значение в метрах (Работает в связке с lon и lat)',
                required: false,
                schema: new OA\Schema(type: 'number', example: "100", minimum: 0),
            )
        ],
        responses: [
            new OA\Response(
                response: 200, description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Organization')
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 401, description: 'Ошибка аутентификации',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'description', type: 'string', example: 'Ошибка аутентификации'),
                        new OA\Property(property: 'errors', type: 'object', example: ["Доступ запрещён, необходим токен авторизации"]),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 422, description: 'Ошибка валидации',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'description', type: 'string', example: 'Ошибка валидации'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            example: [
                                "address_building" => "Ошибка валидации",
                                'name_organization' => "Ошибка валидации",
                                'polygon' => "Ошибка валидации",
                                'lat' => "Ошибка валидации",
                                'lon' => "Ошибка валидации",
                                'radius' => "Ошибка валидации",
                            ]
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function index(SearchOrganizationRequest $request): JsonResponse
    {
        $dto = $request->dto();
        $data = $this->searchOrganizationService->search(dto: $dto);

        return OrganizationResource::collection($data)->response(request: $request)->setStatusCode(code: 200);
    }
}
