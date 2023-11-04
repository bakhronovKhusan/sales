<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/home';
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group( function (){
                    require base_path('routes/api.php');
                    require base_path('app/Modules/Demo_keeper/Routes/api.php');
                    require base_path('app/Modules/Offline_Hunter/Routes/api.php');
                    require base_path('app/Modules/Online_Hunter/Routes/api.php');
                });

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
