<?php

namespace App\Http\Controllers;

use App\Http\Requests\{GetByActivityRequest, GetByAreaRequest, GetByBuildingRequest, GetByNameRequest};
use App\Models\{Activity, Building, Organization};
use Illuminate\Http\JsonResponse;

class OrganizationController extends Controller
{
    public function getByBuilding(GetByBuildingRequest $request): JsonResponse
	{
		$dataRequest = $request->safe()->only(keys:'address');
		return response()->json(data:
			Building::where(
				column: 'address',
				operator: '=',
				value: $dataRequest['address']
			)->first()->organization
		);
    }
    public function getByNameActivity(GetByActivityRequest $request) : JsonResponse{
		$dataRequest = $request->safe()->only(keys:'name');
		return response()->json(data:
			Activity::where(
				column: 'name',
				operator: '=',
				value: $dataRequest['name']
			)->first()->organizations
		);
    }
    public function get(int $id): JsonResponse
    {
        return response()->json(data: Organization::findOrFail(id: $id));
    }
    public function getByName(GetByNameRequest $request): JsonResponse
    {
        return response()->json(data:
			Organization::where(column: 'name',operator: '=',value: $request['name'])
				->first()
		);
    }

	public function getByArea(GetByAreaRequest $request): JsonResponse{
		$dataRequest = $request->safe()->only(keys:['polygon','lat','lon', 'radius']);

		return response()->json(data:
			Organization::query()
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
				->get()
		);
	}

	public function getByActivity(GetByActivityRequest $request): JsonResponse
	{
		$dataRequest = $request->safe()->only(keys:'name');
		$root = Activity::where(column: 'name', operator: '=', value: $dataRequest['name'])->first();
		return response()->json(data:
			Organization::query()
				->whereHas(
					relation: 'activities',
					callback: fn ($query) => $query->whereRaw('path <@ text2ltree(?)', [$root->path]))
				->get()
		);

	}
}
