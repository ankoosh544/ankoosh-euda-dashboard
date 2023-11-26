<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

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
    public function boot(): void
    {
        Filament::registerRenderHook(
			'styles.end',
			fn (): string => Blade::render("@vite(['resources/css/app.css'])")
		);
		Filament::registerRenderHook(
			'scripts.end',
			fn (): string => Blade::render("@vite(['resources/js/app.js'])")
		);
        
    }
}
