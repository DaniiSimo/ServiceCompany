<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class,  'organizations_and_activities');
    }

}
