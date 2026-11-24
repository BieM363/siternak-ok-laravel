<?php

namespace App\Providers;

use App\Models\PregnancyRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (
            request()->isSecure() ||
            request()->header('X-Forwarded-Proto') === 'https' ||
            str_contains((string) request()->getHost(), 'trycloudflare.com')
        ) {
            URL::forceScheme('https');
        }

        View::composer('layouts.app', function ($view) {
            $count = 0;
            try {
                if (Schema::hasTable('pregnancy_records')) {
                    $user = Auth::user();
                    $q = PregnancyRecord::whereHas('cow')->dueForReminder();
                    if ($user && $user->isOperator() && !$user->isAdmin()) {
                        $q->where('operator_id', $user->id);
                    } elseif ($user && $user->isAdminKabupaten()) {
                        $q->whereHas('cow', fn($cq) => $cq->where('regency_id', $user->regency_id));
                    }
                    $count = $q->count();
                }
            } catch (\Throwable $e) {
                $count = 0;
            }
            $view->with('navConfirmationCount', $count);
        });
    }
}
