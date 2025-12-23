<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Paginator::defaultView('pagination::bootstrap-4');
        
        // Mempertahankan parameter URL saat pagination
        Paginator::currentPathResolver(function () {
            return url()->current();
        });
        
        Paginator::currentPageResolver(function ($pageName = 'page') {
            $page = Request::get($pageName);
            if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int) $page >= 1) {
                return (int) $page;
            }
            return 1;
        });
    }
}
