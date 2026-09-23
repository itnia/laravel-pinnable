# Laravel Pinnable

Attach and manage pins on Eloquent models via polymorphic relationships.

The package creates one pin per `owner` and `pinnable` pair. The optional
`pinner` is the model that created the pin and is kept as attribution data.
Calling `pin()` again for the same pair returns the existing pin.

## Installation

```bash
composer require itnia/laravel-pinnable
php artisan vendor:publish --tag=pinnable-migrations
php artisan migrate
```

## Usage

Use `HasPins` on the model that owns pins and `Pinnable` on models that can be pinned.

The `owner` can be any Eloquent model — a `Board`, a `User`, a `Team`, or
anything else. The same applies to `pinnable` and `pinner`.

### Example 1: Board pins articles

```php
use Itnia\Pinnable\Models\Concerns\HasPins;
use Itnia\Pinnable\Models\Concerns\Pinnable;
use Itnia\Pinnable\Models\Pin;

class Board extends Model
{
    use HasPins;
}

class Article extends Model
{
    use Pinnable;
}

$board->pin($article, auth()->user());
$board->unpin($article);
$article->isPinnedBy(auth()->user());
$article->isPinnedIn($board);

# with pins
$articles = Article::query()
    ->with(['pins' => fn($q) => $q->forOwner($board)])
    ->get();
```

### Example 2: User pins articles

```php
use Itnia\Pinnable\Models\Concerns\HasPins;
use Itnia\Pinnable\Models\Concerns\Pinnable;
use Itnia\Pinnable\Models\Pin;

class User extends Model
{
    use HasPins;
}

class Article extends Model
{
    use Pinnable;
}

$user->pin($article);
$user->unpin($article);
$article->isPinnedIn($user);

# with attribute $article->is_pinned === true|false
$articles = Article::query()
    ->withExists(['pins as is_pinned' => fn($q) => $q->forOwner($board)])
    ->get();
```

`unpin()` returns `true` when a pin was deleted and `false` when no matching
pin existed. The owner, pinnable, and pinner models must already be saved.

Pins can also be queried through scopes:

```php
Pin::forOwner($board)->forPinnable($article)->by($user)->first();
```

The package stores `owner_type`, `owner_id`, `pinnable_type`, `pinnable_id`,
and the optional polymorphic `pinner_type` and `pinner_id` in the `pins` table.
The default migration uses Laravel's standard integer polymorphic identifiers.
For UUID or ULID models, customize the published migration to use the matching
`uuidMorphs()` or `ulidMorphs()` definitions.

## Testing

```bash
composer install
composer test
```

The test suite runs against Laravel 12 and 13 in GitHub Actions.