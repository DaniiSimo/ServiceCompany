<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OpenApi\Attributes as OA;
#[OA\Schema(
	schema: 'Activity',
	type: 'object',
	required: ['name','path'],
	properties: [
		new OA\Property(property: 'id', type: 'integer', format: 'int64', example: 1),
		new OA\Property(property: 'name', type: 'string', example: 'quo dolor', maxLength: 255),
		new OA\Property(
			property: 'path',
			type: 'string',
			example: 'quo_dolor',
			description: 'Путь по которому происходит поиск активности строится через trim(string: Str::slug(title: Str::ascii(value: $value),separator: \'_\'))'
		),
		new OA\Property(
			property: 'level',
			type: 'smallint',
			example: '1',
			minimum: 1,
			maximum: 3,
			readOnly: true,
			description: 'Поле выражения, которое описывает глубину деятельности'
		),
	]
)]
class Activity extends Model
{
    use HasFactory;

	protected $hidden  = ['pivot','created_at', 'updated_at'];

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class,  'organizations_and_activities');
    }

}
