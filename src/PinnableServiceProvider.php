<?php

namespace Itnia\Pinnable;

use Illuminate\Support\ServiceProvider;

class PinnableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../database/migrations/create_pins_table.php.stub' => database_path('migrations/'.date('Y_m_d_His').'_create_pins_table.php'),
        ], 'pinnable-migrations');
    }
}
