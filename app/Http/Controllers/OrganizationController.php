<?php

namespace App\Http\Controllers;

use App\Http\Requests\{GetByActivityRequest, GetByAreaRequest, GetByBuildingRequest, GetByNameRequest};
use App\Models\{Activity, Building, Organization};
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
/**
 * Контроллер Организации
 */
class OrganizationController extends Controller
{
	#[OA\Get(
		path: '/api/get/building',
		tags: ['Organization'],
		security: [['bearerAuth' => []]],
		summary: 'Получение организации по адресу здания',
		parameters: [
			new OA\QueryParameter(
				name: 'address',
				description: 'Адрес здания',
				required: true,
				schema: new OA\Schema(ref: '#/components/schemas/Building/properties/address')
			),
		],
		responses: [
			new OA\Response(
				response: 200, description: 'OK',
				content: new OA\JsonContent(
					type: 'object',
					ref: '#/components/schemas/Organization'
				)
			),
			new OA\Response(
				response: 401, description: 'Ошибка аутентификации',
				content: new OA\JsonContent(
					properties: [
						new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
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
			new OA\Response(
				response: 422, description: 'Ошибка валидации',
				content: new OA\JsonContent(
					properties: [
						new OA\Property(property: 'status', type: 'integer', example: 422),
						new OA\Property(property: 'description', type: 'string', example: 'Ошибка валидации'),
						new OA\Property(property: 'errors', type: 'object', example: ["address" => "Ошибка валидации"]),
					],
					type: 'object'
				)
			),
		]
	)]
    public function getByBuilding(GetByBuildingRequest $request): JsonResponse
	{
		$dataRequest = $request->safe()->only(keys:'address');

		$organization = Building::where(
			column: 'address',
			operator: '=',
			value: $dataRequest['address']
		)->first()->organization?->first();

		return !is_null($organization)
			?response()->json(data: $organization)
			:response()->json(data: ['message' => 'Organization not found',], status: 404);
    }
	#[OA\Get(
		path: '/api/get/name-activity',
		tags: ['Organization'],
		security: [['bearerAuth' => []]],
		summary: 'Получение организаций по названию деятельности',
		parameters: [
			new OA\QueryParameter(
				name: 'name',
				description: 'Название деятельности',
				required: true,
				schema: new OA\Schema(ref: '#/components/schemas/Activity/properties/name')
			),
		],
		responses: [
			new OA\Response(
				response: 200, description: 'OK',
				content: new OA\JsonContent(
					type: 'array',
					items: new OA\Items(ref: '#/components/schemas/Organization')
				)
			),
			new OA\Response(
				response: 401, description: 'Ошибка аутентификации',
				content: new OA\JsonContent(
					properties: [
						new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
					],
					type: 'object'
				)
			),
			new OA\Response(
				response: 404, description: 'Ресурс не найден',
				content: new OA\JsonContent(
					properties: [
						new OA\Property(property: 'message', type: 'string', example: 'Organizations not found'),
					],
					type: 'object'
				)
			),
			new OA\Response(
				response: 422, description: 'Ошибка валидации',
				content: new OA\JsonContent(
					properties: [
						new OA\Property(property: 'status', type: 'integer', example: 422),
						new OA\Property(property: 'description', type: 'string', example: 'Ошибка валидации'),
						new OA\Property(property: 'errors', type: 'object', example: ["name" => "Ошибка валидации"]),
					],
					type: 'object'
				)
			),
		]
	)]
    public function getByNameActivity(GetByActivityRequest $request) : JsonResponse{
		$dataRequest = $request->safe()->only(keys:'name');
		$organizations = Activity::where(
			column: 'name',
			operator: '=',
			value: $dataRequest['name']
		)->first()->organizations;
		return !$organizations->isEmpty()
			?response()->json(data:  $organizations)
			:response()->json(data: ['message' => 'Organizations not found',], status: 404);
    }
	#[OA\Get(
		path: '/api/get/{id}',
		tags: ['Organization'],
		security: [['bearerAuth' => []]],
		summary: 'Получение организации по идентификатору',
		parameters: [
			new OA\PathParameter(
				name: 'id',
				description: 'id организации',
				required: true,
				schema: new OA\Schema(type: 'integer', format: 'int64')
			),
		],
		responses: [
			new OA\Response(
				response: 200, description: 'OK',
				content: new OA\JsonContent(
					type: 'object',
					ref:  '#/components/schemas/Organization'
				)
			),
			new OA\Response(
				response: 401, description: 'Ошибка аутентификации',
				content: new OA\JsonContent(
					properties: [
						new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
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
    public function get(int $id): JsonResponse
    {
		$organization = Organization::find(id: $id);

        return !is_null($organization)
			? response()->json(data: Organization::findOrFail(id: $id))
			: response()->json(data: ['message' => 'Organization not found',], status: 404);
    }
	#[OA\Get(
		path: '/api/get/name',
		tags: ['Organization'],
		security: [['bearerAuth' => []]],
		summary: 'Получение организации по названию',
		parameters: [
			new OA\QueryParameter(
				name: 'name',
				description: 'Название организации',
				required: true,
				schema: new OA\Schema(ref: '#/components/schemas/Organization/properties/name')
			),
		],
		responses: [
			new OA\Response(
				response: 200, description: 'OK',
				content: new OA\JsonContent(
					type: 'object',
					ref:  '#/components/schemas/Organization'
				)
			),
			new OA\Response(
				response: 401, description: 'Ошибка аутентификации',
				content: new OA\JsonContent(
					properties: [
						new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
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
			new OA\Response(
				response: 422, description: 'Ошибка валидации',
				content: new OA\JsonContent(
					properties: [
						new OA\Property(property: 'status', type: 'integer', example: 422),
						new OA\Property(property: 'description', type: 'string', example: 'Ошибка валидации'),
						new OA\Property(property: 'errors', type: 'object', example: ["name" => "Ошибка валидации"]),
					],
					type: 'object'
				)
			),
		]
	)]
    public function getByName(GetByNameRequest $request): JsonResponse
    {
		$dataRequest = $request->safe()->only(keys:'name');
		$organization = Organization::where(column: 'name',operator: '=',value: $dataRequest['name'])
			->first();
        return !is_null($organization)
			?response()->json(data:$organization)
			:response()->json(data: ['message' => 'Organization not found',], status: 404);
    }
	#[OA\Get(
		path: '/api/get/area',
		tags: ['Organization'],
		security: [['bearerAuth' => []]],
		summary: 'Получение организаций по области',
		description: 'Поиск организаций, которые находятся в заданном радиусе/прямоугольной области относительно указанной точки на карте.',
		parameters: [
			new OA\QueryParameter(
				name: 'polygon',
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
				description: 'Широта координаты, относительно которой будет производиться поиск по радиусу',
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
				description: 'Долгота координаты, относительно которой будет производиться поиск по радиусу',
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
				description: 'Значение в метрах',
				required: false,
				schema: new OA\Schema(type: 'number', example: "100", minimum: 0),
			),
		],
		responses: [
			new OA\Response(
				response: 200, description: 'OK',
				content: new OA\JsonContent(
					type: 'array',
					items: new OA\Items(ref: '#/components/schemas/Organization')
				)
			),
			new OA\Response(
				response: 401, description: 'Ошибка аутентификации',
				content: new OA\JsonContent(
					properties: [
						new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
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
			new OA\Response(
				response: 422, description: 'Ошибка валидации',
				content: new OA\JsonContent(
					properties: [
						new OA\Property(property: 'status', type: 'integer', example: 422),
						new OA\Property(property: 'description', type: 'string', example: 'Ошибка валидации'),
						new OA\Property(property: 'errors', type: 'object', example: [
							"polygon" => "Ошибка валидации",
							"polygon.*" => "Ошибка валидации координаты",
							"lat" => "Ошибка валидации",
							"lon" => "Ошибка валидации",
							"radius" => "Ошибка валидации"
						]),
					],
					type: 'object'
				)
			),
		]
	)]
	public function getByArea(GetByAreaRequest $request): JsonResponse{
		$dataRequest = $request->safe()->only(keys:['polygon','lat','lon', 'radius']);
		$organizations = Organization::query()
							->whereHas(
								relation: 'building',
								callback: fn($query) =>
								$query->when(
									value: isset($dataRequest['polygon']),
									callback: function ($subQuery) use ($dataRequest) {
										$strPolygon = 'POLYGON(('.implode(separator: ',', array: $dataRequest['polygon']).'))';
										$subQuery->whereRaw(
											sql: 'ST_Intersects(geom::geometry, ST_SetSRID(ST_GeomFromText(?), 4326))',
											bindings: [$strPolygon]
										);
									}
								)->when(
									value: isset($dataRequest['lat'],$dataRequest['lon']),
									callback: fn ($subQuery) =>
									$subQuery->whereRaw(
										sql: 'ST_DWithin(geom, ST_SetSRID(ST_MakePoint(?, ?),4326)::geography, ?)',
										bindings: [(float) $dataRequest['lon'], (float) $dataRequest['lat'], $dataRequest['radius'] ?? 1]
									)
								)
							)
							->get();
		return !$organizations->isEmpty()
			?response()->json(data: $organizations)
			:response()->json(data: ['message' => 'Organizations not found',], status: 404);
	}
	#[OA\Get(
		path: '/api/get/activity',
		tags: ['Organization'],
		security: [['bearerAuth' => []]],
		summary: 'Получение организаций по дереву деятельностей',
		description: 'Например, поиск по виду деятельности «Еда», которая находится на первом уровне дерева, и чтобы нашлись все организации, которые относятся к видам деятельности, лежащим внутри',
		parameters: [
			new OA\QueryParameter(
				name: 'name',
				description: 'Название деятельности',
				required: true,
				schema: new OA\Schema(ref: '#/components/schemas/Activity/properties/name')
			),
		],
		responses: [
			new OA\Response(
				response: 200, description: 'OK',
				content: new OA\JsonContent(
					type: 'array',
					items: new OA\Items(ref: '#/components/schemas/Organization')
				)
			),
			new OA\Response(
				response: 401, description: 'Ошибка аутентификации',
				content: new OA\JsonContent(
					properties: [
						new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
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
			new OA\Response(
				response: 422, description: 'Ошибка валидации',
				content: new OA\JsonContent(
					properties: [
						new OA\Property(property: 'status', type: 'integer', example: 422),
						new OA\Property(property: 'description', type: 'string', example: 'Ошибка валидации'),
						new OA\Property(property: 'errors', type: 'object', example: ["name" => "Ошибка валидации"]),
					],
					type: 'object'
				)
			),
		]
	)]
	public function getByActivity(GetByActivityRequest $request): JsonResponse
	{
		$dataRequest = $request->safe()->only(keys:'name');
		$root = Activity::where(column: 'name', operator: '=', value: $dataRequest['name'])->first();
		$organizations = Organization::query()
			->whereHas(
				relation: 'activities',
				callback: fn ($query) => $query->whereRaw('path <@ text2ltree(?)', [$root->path]))
			->get();
		return !$organizations->isEmpty()
			?response()->json(data: $organizations)
			:response()->json(data: ['message' => 'Organizations not found',], status: 404);

	}
}
