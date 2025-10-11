<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use \Illuminate\Database\Eloquent\Relations\HasOne;
use \Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    public function building(): HasOne
    {
        return $this->hasOne(Building::class);
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
