<?php

namespace Itnia\Pinnable\Exceptions;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class InvalidPinModel extends InvalidArgumentException
{
    public static function notPersisted(Model $model): self
    {
        return new self(sprintf(
            'The %s model must be persisted before it can be used in a pin.',
            $model::class,
        ));
    }
}
