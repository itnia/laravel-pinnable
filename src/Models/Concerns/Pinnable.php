<?php

namespace Itnia\Pinnable\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Itnia\Pinnable\Models\Pin;

/** @mixin Model */
trait Pinnable
{
    public function pins(): MorphMany
    {
        return $this->morphMany(Pin::class, 'pinnable');
    }

    public function isPinnedBy(Model $pinner): bool
    {
        return $this->pins()->by($pinner)->exists();
    }

    public function isPinnedIn(Model $owner): bool
    {
        return $this->pins()->forOwner($owner)->exists();
    }
}
