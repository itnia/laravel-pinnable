<?php

use Itnia\Pinnable\Exceptions\InvalidPinModel;
use Itnia\Pinnable\Models\Pin;
use Itnia\Pinnable\Tests\TestOwner;
use Itnia\Pinnable\Tests\TestPinnable;
use Itnia\Pinnable\Tests\TestPinner;

it('creates and retrieves a pin through model concerns', function (): void {
    $owner = TestOwner::create();
    $pinnable = TestPinnable::create();
    $pinner = TestPinner::create();

    $pin = $owner->pin($pinnable, $pinner);

    expect($pin->owner->is($owner))->toBeTrue()
        ->and($pin->pinnable->is($pinnable))->toBeTrue()
        ->and($pin->pinner->is($pinner))->toBeTrue()
        ->and($owner->pins)->toHaveCount(1)
        ->and($pinnable->pins)->toHaveCount(1)
        ->and($pinnable->isPinnedBy($pinner))->toBeTrue()
        ->and($pinnable->isPinnedIn($owner))->toBeTrue();
});

it('does not create duplicate pins for the same owner and pinnable', function (): void {
    $owner = TestOwner::create();
    $pinnable = TestPinnable::create();
    $firstPinner = TestPinner::create();
    $secondPinner = TestPinner::create();

    $firstPin = $owner->pin($pinnable, $firstPinner);
    $secondPin = $owner->pin($pinnable, $secondPinner);

    expect($secondPin->is($firstPin))->toBeTrue()
        ->and($secondPin->pinner->is($firstPinner))->toBeTrue()
        ->and(Pin::forOwner($owner)->forPinnable($pinnable)->count())->toBe(1);
});

it('deletes a pin', function (): void {
    $owner = TestOwner::create();
    $pinnable = TestPinnable::create();

    $owner->pin($pinnable);

    expect($owner->unpin($pinnable))->toBeTrue()
        ->and($owner->unpin($pinnable))->toBeFalse()
        ->and(Pin::forOwner($owner)->forPinnable($pinnable)->exists())->toBeFalse();
});

it('rejects unsaved models', function (): void {
    $owner = TestOwner::create();

    expect(fn (): mixed => $owner->pin(new TestPinnable()))
        ->toThrow(InvalidPinModel::class);
});
