<?php

namespace Itnia\Pinnable\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Itnia\Pinnable\PinnableServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [PinnableServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('test_owners', function (Blueprint $table): void {
            $table->id();
        });

        Schema::create('test_pinnables', function (Blueprint $table): void {
            $table->id();
        });

        Schema::create('test_pinners', function (Blueprint $table): void {
            $table->id();
        });

        (require dirname(__DIR__).'/database/migrations/create_pins_table.php.stub')->up();
    }
}

class TestOwner extends Model
{
    use \Itnia\Pinnable\Models\Concerns\HasPins;

    public $timestamps = false;

    protected $table = 'test_owners';

    protected $guarded = [];
}

class TestPinnable extends Model
{
    use \Itnia\Pinnable\Models\Concerns\Pinnable;

    public $timestamps = false;

    protected $table = 'test_pinnables';

    protected $guarded = [];
}

class TestPinner extends Model
{
    public $timestamps = false;

    protected $table = 'test_pinners';

    protected $guarded = [];
}
