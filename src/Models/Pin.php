<?php

namespace Itnia\Pinnable\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Pin extends Model
{
    protected $fillable = [
        'owner_type',
        'owner_id',
        'pinnable_type',
        'pinnable_id',
        'pinner_type',
        'pinner_id',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function pinnable(): MorphTo
    {
        return $this->morphTo();
    }

    public function pinner(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForOwner(Builder $query, Model $owner): Builder
    {
        return $query
            ->where('owner_type', $owner->getMorphClass())
            ->where('owner_id', $owner->getKey());
    }

    public function scopeForPinnable(Builder $query, Model $pinnable): Builder
    {
        return $query
            ->where('pinnable_type', $pinnable->getMorphClass())
            ->where('pinnable_id', $pinnable->getKey());
    }

    public function scopeBy(Builder $query, Model $pinner): Builder
    {
        return $query
            ->where('pinner_type', $pinner->getMorphClass())
            ->where('pinner_id', $pinner->getKey());
    }
}
