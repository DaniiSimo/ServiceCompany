<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OA;
#[OA\Schema(
	schema: 'Phone',
	type: 'object',
	required: ['phone','organization_id'],
	properties: [
		new OA\Property(property: 'id', type: 'integer', format: 'int64', example: 1),
		new OA\Property(
			property: 'phone',
			type: 'string',
			description: 'Формат: X-XXX-XXX(-XX-XX). Уникален внутри организации.',
			pattern: '^[1-9]-[0-9]{3}-[0-9]{3}(-[0-9]{2}-[0-9]{2})?$',
			example: '8-923-666-13-13',
			minLength: 9,
			maxLength: 15
		),
	]
)]
class Phone extends Model
{
    use HasFactory;
	protected $hidden = ['organization_id','created_at', 'updated_at'];
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
