<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(
            __DIR__.'/../../resources/views/vendor/pagination',
            'pagination'
        );

        Paginator::defaultView('pagination::default');
        Paginator::defaultSimpleView('pagination::default');
    }
}
