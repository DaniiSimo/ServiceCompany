<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Building extends Model
{
    use HasFactory;
	protected $casts  = ['lon'=>'float','lat'=>'float'];

    public function organization(): HasMany
    {
        return $this->hasMany(related: Organization::class);
    }
}
