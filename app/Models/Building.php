<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApi\Attributes as OA;
#[OA\Schema(
	schema: 'Building',
	type: 'object',
	required: ['address','geom'],
	properties: [
		new OA\Property(property: 'id', type: 'integer', format: 'int64', example: 1),
		new OA\Property(property: 'address', type: 'string', example: 'ПАО ГаражХозТелеком'),
		new OA\Property(
			property: 'geom',
			type: 'string',
			writeOnly: true,
			description: "Служебное поле геометрии Point(SRID=4326). В ответах не возвращается" .
			"Заполняется на сервере из lon/lat, например: " .
			"DB::raw(\"ST_SetSRID(ST_MakePoint(:lon,:lat), 4326)\").",
			example: "0101000020E61000008D5E0D501A4847C026FE28EACC7655C0"
		),
		new OA\Property(property: 'lon', type: 'number', format: 'double', minimum: -180, maximum: 180, example: 130.158708),
		new OA\Property(property: 'lat', type: 'number', format: 'double', minimum:  -90, maximum:  90, example:  34.104894),
	]
)]
class Building extends Model
{
    use HasFactory;

	protected $hidden  = ['created_at', 'updated_at'];
	protected $casts  = ['lon'=>'float','lat'=>'float'];

    public function organization(): HasMany
    {
        return $this->hasMany(Organization::class);
    }
}
