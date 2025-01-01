<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Sử dụng Bootstrap cho phân trang
        Paginator::useBootstrap();

        // Chia sẻ danh mục cho tất cả các view
        View::composer('*', function ($view) {
            $view->with('categories', Category::orderBy('name')->get());
        });
    }
}
