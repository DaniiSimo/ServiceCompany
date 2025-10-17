<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    use HasFactory;

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(related: Organization::class,  table: 'organizations_and_activities');
    }
}
