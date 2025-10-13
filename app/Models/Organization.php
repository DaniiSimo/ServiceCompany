<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApi\Attributes as OA;
#[OA\Schema(
	schema: 'Organization',
	type: 'object',
	required: ['name','building_id'],
	properties: [
		new OA\Property(property: 'id', type: 'integer', format: 'int64', example: 1),
		new OA\Property(property: 'name', type: 'string', example: 'ПАО ГаражХозТелеком', maxLength: 255),
		new OA\Property(property: 'building', ref: '#/components/schemas/Building'),
		new OA\Property(property: 'phones', type: 'array',
			items: new OA\Items(
				ref: '#/components/schemas/Phone'
			)
		),
		new OA\Property(property: 'activites', type: 'array',
			items: new OA\Items(
				ref: '#/components/schemas/Activity'
			)
		)
	]
)]
class Organization extends Model
{
    use HasFactory;
	protected $hidden  = ['building_id', 'created_at', 'updated_at'];
	protected $with = ['building','phones','activities'];
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function phones(): HasMany
    {
        return $this->hasMany(Phone::class);
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class,  'organizations_and_activities');
    }
}
