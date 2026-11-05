<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        // Batas per fitur (penghitung terpisah agar pengaduan tidak menghabiskan jatah daftar, dst.)
        RateLimiter::for('pengaduan', fn (Request $r) => Limit::perMinutes(10, 5)->by('aduan|' . $r->ip()));
        RateLimiter::for('daftar', fn (Request $r) => Limit::perMinutes(10, 8)->by('daftar|' . $r->ip()));
        RateLimiter::for('ajukan', fn (Request $r) => Limit::perMinutes(10, 10)->by('ajukan|' . ($r->user()?->id ?? $r->ip())));
    }
}
