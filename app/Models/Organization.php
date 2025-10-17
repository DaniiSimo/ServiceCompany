<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Builder, Factories\HasFactory, Model};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};

class Organization extends Model
{
    use HasFactory;
    public function building(): BelongsTo
    {
        return $this->belongsTo(related: Building::class);
    }

    public function phones(): HasMany
    {
        return $this->hasMany(related: Phone::class);
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(related: Activity::class,  table: 'organizations_and_activities');
    }

    public function scopeNamed(Builder $query, ?string $name):Builder
    {
        return $name
            ? $query->where(column: 'name', operator: '=',  value: $name)
            : $query;
    }

    public function scopeWithinRadius(Builder $query, ?float $lat, ?float $lon,?float $radius):Builder{
        return $lat && $lon && $radius
            ? $query->whereHas(
            relation: 'building',
            callback: fn($subQuery) => $subQuery->whereRaw(
                    sql: 'ST_DWithin(geom, ST_SetSRID(ST_MakePoint(?, ?),4326)::geography, ?)',
                    bindings: [$lon, $lat, $radius]
                )
            )
            : $query;
    }

    public function scopeWithinPolygon(Builder $query, ?array $polygon):Builder{
        return $polygon
            ? $query->whereHas(
                relation: 'building',
                callback: fn($subQuery) => $subQuery->whereRaw(
                    sql: 'ST_Intersects(geom::geometry, ST_SetSRID(ST_GeomFromText(?), 4326))',
                    bindings: ['POLYGON(('.implode(separator: ',', array: $polygon).'))']
                )
            )
            : $query;
    }

    public function scopeAddress(Builder $query, ?string $address):Builder{
        return $address
            ? $query->whereHas(
                relation: 'building',
                callback: fn($subQuery) => $subQuery->where(column: 'address', operator: '=', value: $address)
            )
            : $query;
    }

    public function scopeNamedActivity(Builder $query, ?string $name, ?bool $shouldTakeDescendants):Builder{
        return $name
            ? $query->whereHas(
                relation: 'activities',
                callback: fn($subQuery) => !$shouldTakeDescendants
                    ? $subQuery->where(column: 'name', operator: '=', value: $name)
                    : $subQuery->whereRaw(sql:'path <@ text2ltree(?)', bindings:[Activity::where('name', $name)->value('path')])
            )
            : $query;
    }
}
