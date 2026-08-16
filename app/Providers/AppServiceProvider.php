<?php

namespace App\Providers;

use App\Models\Guru;
use App\Models\Murid;
use App\Models\Spp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
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
        Carbon::setLocale('id');

        // Automatically invalidate cached dashboard metrics on data mutations
        $clearStats = function () {
            $bulanIni = now()->format('Y-m');
            Cache::forget('dash_total_murid');
            Cache::forget('dash_total_guru');
            Cache::forget("dash_belum_bayar_{$bulanIni}");
            Cache::forget("dash_pemasukan_{$bulanIni}");
        };

        Murid::saved($clearStats);
        Murid::deleted($clearStats);
        Guru::saved($clearStats);
        Guru::deleted($clearStats);
        Spp::saved($clearStats);
        Spp::deleted($clearStats);
    }
}
