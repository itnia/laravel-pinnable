<?php

namespace Itnia\Pinnable\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Itnia\Pinnable\Exceptions\InvalidPinModel;
use Itnia\Pinnable\Models\Pin;

/** @mixin Model */
trait HasPins
{
    /** @return MorphMany<Pin, $this> */
    public function pins(): MorphMany
    {
        return $this->morphMany(Pin::class, 'owner');
    }

    public function pin(Model $pinnable, ?Model $pinner = null): Pin
    {
        $this->ensurePinModelIsPersisted($this);
        $this->ensurePinModelIsPersisted($pinnable);

        if ($pinner) {
            $this->ensurePinModelIsPersisted($pinner);
        }

        return $this->pins()->firstOrCreate([
            'pinnable_type' => $pinnable->getMorphClass(),
            'pinnable_id' => $pinnable->getKey(),
        ], [
            'pinner_type' => $pinner?->getMorphClass(),
            'pinner_id' => $pinner?->getKey(),
        ]);
    }

    public function unpin(Model $pinnable): bool
    {
        $this->ensurePinModelIsPersisted($this);
        $this->ensurePinModelIsPersisted($pinnable);

        return $this->pins()
            ->where('pinnable_type', $pinnable->getMorphClass())
            ->where('pinnable_id', $pinnable->getKey())
            ->delete() > 0;
    }

    protected function ensurePinModelIsPersisted(Model $model): void
    {
        if (! $model->exists || $model->getKey() === null) {
            throw InvalidPinModel::notPersisted($model);
        }
    }
}
