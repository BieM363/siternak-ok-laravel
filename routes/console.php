<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cronjob utama: Pembatasan waktu login operator maksimal 8 jam & sinkronisasi notifikasi peramban ponsel lapangan
Schedule::command('ternak:operator-session-cron')->everyMinute();

// Cronjob siklus kebuntingan sapi setiap 8 jam (selaras dengan shift 8 jam operator lapangan) & tengah malam
Schedule::command('ternak:update-pregnancy-status')->cron('0 */8 * * *');
Schedule::command('ternak:update-pregnancy-status')->dailyAt('00:00');
