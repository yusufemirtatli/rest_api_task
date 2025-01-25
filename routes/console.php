<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

\Illuminate\Support\Facades\Schedule::call(function () {
    app(\App\Http\Controllers\ReservationController::class)->expireReservations();
})->everyMinute(); // Rezervasyon sürelerini her dakika kontrol eder;

\Illuminate\Support\Facades\Schedule::call(function () {
    app(\App\Http\Controllers\TicketController::class)->expireTickets();
})->everyFourHours();
